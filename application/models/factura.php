<?php 
Class factura extends CI_Model
{
	
	function getConsecutivo($id_empresa) //Traer el siguiente consecutivo de una empresa en particular
	{
		return $this->getConsecutivoUltimaFactura($id_empresa)+1;
	}
	
	
	function getConsecutivoUltimaFactura($sucursal)
	{
		$this -> db -> select('Factura_Consecutivo');
		$this -> db -> from('TB_07_Factura');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> order_by("Factura_Consecutivo", "desc");
		$this -> db -> limit(1);
		$query = $this -> db -> get();
		/*$query ="select Factura_Consecutivo from TB_07_Factura order by Factura_Consecutivo DESC limit 1";
        $res = $this->db->query($query);*/
	
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{
			$consecutivo;
			$result = $query->result();
			foreach($result as $row)
			{$consecutivo=$row->Factura_Consecutivo;}
			return $consecutivo;
		}
	}
	
	function crearfactura($cedula, $nombre, $currency, $observaciones, $sucursal, $vendedor, $isProforma){
		$c_array = $this->getConfgArray();
		if($isProforma){ //Si es proforma agarramos el iva y el cambio de la proforma, no el actual
			$proforma = $this->getProformasHeaders($isProforma, $sucursal);
			foreach($proforma as $head){
				$c_array['iva']=$head->Proforma_Porcentaje_IVA;
				$c_array['dolar_venta']=$head->Proforma_Tipo_Cambio;
			}
		}
		$sucursalVendedor =  $sucursal;
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
				$this->isDesampa = true;
		}
		if($consecutivo = $this->getConsecutivo($sucursal)){
			//return $consecutivo;
			$this->load->model('cliente','',TRUE);
			$clienteArray = $this->cliente->getNombreCliente($cedula);
			$dataFactura = array(
	                        'Factura_Consecutivo'=>mysql_real_escape_string($consecutivo),
	                        'Factura_Observaciones'=>mysql_real_escape_string($observaciones), 
													'Factura_Estado'=>'pendiente',
													'Factura_Moneda'=>mysql_real_escape_string($currency),
													'Factura_porcentaje_iva'=>$c_array['iva'],
													'Factura_tipo_cambio'=>$c_array['dolar_venta'],
													'Factura_Nombre_Cliente'=>mysql_real_escape_string($nombre),
													'TB_02_Sucursal_Codigo'=>$sucursal,
													'Factura_Vendedor_Codigo'=>$vendedor,	
													'Factura_Vendedor_Sucursal'=>$sucursalVendedor,	
													'TB_03_Cliente_Cliente_Cedula'=>mysql_real_escape_string($cedula),
													'Factura_Cliente_Exento'=>$clienteArray['exento'],
													'Factura_Cliente_No_Retencion'=>$clienteArray['retencion'],
													'Factura_Cliente_Sucursal'=>$clienteArray['sucursal']												
	                    );			
	        $this->db->insert('TB_07_Factura',$dataFactura); 
	        
	    if($this->trueque && $this->isDesampa){ //Si viene de desampa se guarda la factura
					$datos = array("Consecutivo" => $consecutivo,
													"Documento" => 'factura');
					$this->db->insert("TB_46_Relacion_Desampa", $datos);
					$this->isDesampa = false;
			}
	     
			return $this->existe_Factura($consecutivo, $sucursal);
		}else{
			return false;
		}
		
		
	}
	
	function getProformasHeaders($consecutivo, $sucursal){
		$this -> db -> select('*');
		$this -> db -> from('TB_10_Proforma');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Proforma_Consecutivo', $consecutivo);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   return $query->result();
		}
		else
		{
		   return false;
		}
	}
	
	function existe_Factura($consecutivo, $sucursal){
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
		}
		
		$this -> db -> select('Factura_Consecutivo');
		$this -> db -> from('TB_07_Factura');
		$this -> db -> where('Factura_Consecutivo', $consecutivo);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> limit(1);

		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		  //return false;
		  return $consecutivo; //Devolvemos consecutivo para valorar en controlador
		}
		else
		{
		  return false;
		}
	}
	
	function addItemtoInvoice($codigo, $descripcion, $cantidad, $descuento, $exento, $retencion, $precio, $precioFinal, $consecutivo, $sucursal, $vendedor, $cliente, $imagen){
		$sucursalVendedor = $sucursal;
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
		}
		$dataItem = array(
	                        'Articulo_Factura_Codigo'=>mysql_real_escape_string($codigo),
	                        'Articulo_Factura_Descripcion'=>mysql_real_escape_string($descripcion), 
							'Articulo_Factura_Cantidad'=>mysql_real_escape_string($cantidad),
							'Articulo_Factura_Descuento'=>mysql_real_escape_string($descuento),
							'Articulo_Factura_Exento'=>mysql_real_escape_string($exento),
							'Articulo_Factura_No_Retencion'=>mysql_real_escape_string($retencion),
							'Articulo_Factura_Precio_Unitario'=>mysql_real_escape_string($precio),
							'Articulo_Factura_Precio_Final' => 	$precioFinal,
							'Articulo_Factura_Imagen'=>mysql_real_escape_string($imagen),
							'TB_07_Factura_Factura_Consecutivo'=>mysql_real_escape_string($consecutivo),
							'TB_07_Factura_TB_02_Sucursal_Codigo'=>mysql_real_escape_string($sucursal),
							'TB_07_Factura_Factura_Vendedor_Codigo'=>mysql_real_escape_string($vendedor),
							'TB_07_Factura_Factura_Vendedor_Sucursal'=>mysql_real_escape_string($sucursalVendedor),
							'TB_07_Factura_TB_03_Cliente_Cliente_Cedula'=>mysql_real_escape_string($cliente)							
	                    );			
	        $this->db->insert('TB_08_Articulos_Factura',$dataItem);
	}
	
	function getCostosTotalesFactura($consecutivo, $sucursal){
		
		$costo_total = 0;
		$iva = 0;
		$costo_sin_iva = 0;
		$retencion = 0;
		$this->load->model('articulo','',TRUE);
		$this->load->model('cliente','',TRUE);
		//Traemos el array de configuracion para obtener el porcentaje
		$c_array = $this->getConfgArray();
		
		//Obtenemos la info del cliente para ver si es exento y/o aplica retencion
		$facturaEncabezado = $this->getFacturasHeaders($consecutivo, $sucursal)[0];
		$clienteEsExento = $this->cliente->clienteEsExentoDeIVA($facturaEncabezado->TB_03_Cliente_Cliente_Cedula);
		$clienteNoAplicaRetencion = $this->cliente->clienteEsExentoDeRetencion($facturaEncabezado->TB_03_Cliente_Cliente_Cedula);
		
		if($articulos = $this->getItemsFactura($consecutivo, $sucursal)){
			foreach($articulos as $articulo)
			{
				//Calculamos el precio total de los articulos
				$precio_total_articulo = (($articulo->Articulo_Factura_Precio_Unitario)-(($articulo->Articulo_Factura_Precio_Unitario)*(($articulo->Articulo_Factura_Descuento)/100)))*$articulo->Articulo_Factura_Cantidad;
				$precio_total_articulo_sin_descuento = $articulo->Articulo_Factura_Precio_Unitario*$articulo->Articulo_Factura_Cantidad;
				$precio_articulo_final = $articulo->Articulo_Factura_Precio_Final;
				$precio_articulo_final = $precio_articulo_final * $articulo->Articulo_Factura_Cantidad;
				
				//Calculamos los impuestos
				
				$isExento = $articulo->Articulo_Factura_Exento;
				
				if($isExento=='0'){
					$costo_sin_iva += $precio_total_articulo/(1+(floatval($c_array['iva'])/100));
					
					
					$iva_precio_total_cliente = $precio_total_articulo - ($precio_total_articulo/(1+(floatval($c_array['iva'])/100)));
					$iva_precio_total_cliente_sin_descuento = $precio_total_articulo_sin_descuento - ($precio_total_articulo_sin_descuento/(1+(floatval($c_array['iva'])/100))); 
					
					$precio_final_sin_iva = $precio_articulo_final/(1+(floatval($c_array['iva'])/100));
					$iva_precio_final = $precio_articulo_final - $precio_final_sin_iva;
					
					if(!$articulo->Articulo_Factura_No_Retencion){
							$retencion += ($iva_precio_final - $iva_precio_total_cliente_sin_descuento);
					}
				}
				else if($isExento=='1'){
					$costo_sin_iva += $precio_total_articulo;
					//$retencion = 0;
				}
				$costo_total += $precio_total_articulo;
				//$costo_sin_iva += (($articulo->Articulo_Factura_Precio_Unitario)-(($articulo->Articulo_Factura_Precio_Unitario)*(($articulo->Articulo_Factura_Descuento)/100)))*$articulo->Articulo_Factura_Cantidad;
			}
			$iva = $costo_total-$costo_sin_iva;
		}
		//$retencion -= $iva;
		//Si aplica la retencion entonces modificamos los costos
		if(!$c_array['aplicar_retencion']){
			$retencion = 0;
		}
		
		//Si el cliente es exento o no aplica retencion, lo valoramos
		if($clienteEsExento){
				$costo_total -= $iva;
				$iva = 0;
				$retencion = 0;
		}
		if($clienteNoAplicaRetencion){
				$retencion = 0;
		}
		
		$costo_total += $retencion;
		
		return array('Factura_Monto_Total'=>$costo_total, 'Factura_Monto_IVA'=>$iva, 'Factura_Monto_Sin_IVA'=>$costo_sin_iva, 'Factura_Retencion'=>$retencion);
	}
	
	function getItemsFactura($consecutivo, $sucursal){
		//$this -> db -> select('Articulo_Factura_Cantidad, Articulo_Factura_Descuento, Articulo_Factura_Precio_Unitario, Articulo_Factura_Exento');
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$this -> db -> where('TB_07_Factura_Factura_Vendedor_Sucursal', $sucursal);
				$sucursal = $this->cod_garotas;
		}
		$this -> db -> from('TB_08_Articulos_Factura');
		$this -> db -> where('TB_07_Factura_Factura_Consecutivo', $consecutivo);
		$this -> db -> where('TB_07_Factura_TB_02_Sucursal_Codigo', $sucursal);
		
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   return $query->result();
		}
		else
		{
		   return false;
		}
	}
	
	function getConfgArray()
	{
		/*$CI =& get_instance();
		$CI->load->model('XMLParser');
		return $CI->XMLParser->getConfigArray();*/
		return $this->configuracion->getConfiguracionArray();
	}
	
	function updateCostosTotales($data, $consecutivo, $sucursal){
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
		}
		$this->db->where('Factura_Consecutivo', $consecutivo);
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->update('TB_07_Factura' ,$data);
	}
	
	function getFacturasPendientes($sucursal){
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
				$facturas_desampa = $this->getFacturasDesampa();
				if(!empty($facturas_desampa)){
						$this->db->where_in("Factura_Consecutivo", $facturas_desampa);
				}
		}elseif($this->trueque && $sucursal == $this->cod_garotas){
				$facturas_desampa = $this->getFacturasDesampa();
				if(!empty($facturas_desampa)){
						$this->db->where_not_in("Factura_Consecutivo", $facturas_desampa);
				}
		}
		
		$this -> db -> select('*');
		$this -> db -> from('TB_07_Factura');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Factura_Estado', 'pendiente');
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   return $query->result();
		}
		else
		{
		   return false;
		}
	}
	
	function getFacturasHeaders($consecutivo, $sucursal){
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
				$facturas_desampa = $this->getFacturasDesampa();
				if(!empty($facturas_desampa)){
						$this->db->where_in("Factura_Consecutivo", $facturas_desampa);
				}
		}elseif($this->trueque && $sucursal == $this->cod_garotas){
				$facturas_desampa = $this->getFacturasDesampa();
				if(!empty($facturas_desampa)){
						$this->db->where_not_in("Factura_Consecutivo", $facturas_desampa);
				}
		}
		$this -> db -> select('*');
		$this -> db -> from('TB_07_Factura');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Factura_Consecutivo', $consecutivo);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   return $query->result();
		}
		else
		{
		   return false;
		}
	}
	
	function getFacturasHeadersImpresion($consecutivo, $sucursal){
		/*//JOIN tb_01_usuario ON tb_01_usuario.Usuario_Codigo = TB_07_Factura.Factura_Vendedor_Codigo
		$this -> db -> select("Factura_Consecutivo AS consecutivo, TB_03_Cliente_Cliente_Cedula AS cliente_ced, Factura_Nombre_Cliente AS cliente_nom, Factura_Monto_Total AS total, Factura_Tipo_Pago AS tipo, Factura_Moneda AS moneda, Factura_tipo_cambio AS cambio");
		$this -> db -> from('TB_07_Factura');
		$this -> db -> join('tb_01_usuario', 'tb_01_usuario.Usuario_Codigo = TB_07_Factura.Factura_Vendedor_Codigo');
		$this -> db -> where('TB_07_Factura.TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('TB_07_Factura.Factura_Consecutivo', $consecutivo);
		$this-> db ->set("date_format(Factura_Fecha_Hora, '%d-%m-%Y %h:%i:%s %p') AS fecha", FALSE);
		//$this -> db -> limit(1);
		$query = $this -> db -> get();*/
		$queryLoco = "";
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
				$facturas_desampa = $this->getFacturasDesampa();
				if(!empty($facturas_desampa)){
						$queryLoco = "AND TB_07_Factura.Factura_Consecutivo IN (".implode($facturas_desampa,',').")";
				}
		}elseif($this->trueque && $sucursal == $this->cod_garotas){
				$facturas_desampa = $this->getFacturasDesampa();
				if(!empty($facturas_desampa)){
						$queryLoco = "AND TB_07_Factura.Factura_Consecutivo NOT IN (".implode($facturas_desampa,',').")";
				}
		}
		
		$query = $this->db->query("
			SELECT Factura_Consecutivo AS consecutivo, 
				date_format(Factura_Fecha_Hora, '%d-%m-%Y %h:%i:%s %p') AS fecha, 
				TB_03_Cliente_Cliente_Cedula AS cliente_ced, 
				Factura_Nombre_Cliente AS cliente_nom, 
				Factura_Monto_Total AS total,
				Factura_Monto_Sin_IVA AS subtotal,
				Factura_Monto_IVA as total_iva,
				Factura_Retencion as retencion,
				Factura_Tipo_Pago AS tipo, 
				Factura_Moneda AS moneda, 
				Factura_tipo_cambio AS cambio, 
				CONCAT_WS(' ', Usuario_Nombre, Usuario_Apellidos) AS vendedor,
				Factura_Observaciones AS observaciones,
				Factura_Entregado_Vuelto AS entregado_vuelto,
				Factura_Recibido_Vuelto AS recibido_vuelto				
			FROM TB_07_Factura
			JOIN tb_01_usuario ON tb_01_usuario.Usuario_Codigo = TB_07_Factura.Factura_Vendedor_Codigo
			WHERE TB_07_Factura.TB_02_Sucursal_Codigo = $sucursal
			AND TB_07_Factura.Factura_Consecutivo = $consecutivo
			$queryLoco
		");

		if($query -> num_rows() != 0)
		{
		   return $query->result();
		}
		else
		{
		   return false;
		}
	}
	
	function getCliente($consecutivo, $sucursal){
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
		}
		$this -> db -> select('TB_03_Cliente_Cliente_Cedula');
		$this -> db -> from('TB_07_Factura');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Factura_Consecutivo', $consecutivo);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   $result = $query->result();
			foreach($result as $row)
			{	
				return $row->TB_03_Cliente_Cliente_Cedula;
			}
		}
		else
		{
		   return false;
		}
	}
	
	function getVendedor($consecutivo, $sucursal){
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				//Aunque el bretee en desampa, las facturas son de garotas y se guarda su id en gartas no en desampa
				$sucursal = $this->cod_garotas;
		}
		$this -> db -> select('Factura_Vendedor_Codigo');
		$this -> db -> from('TB_07_Factura');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Factura_Consecutivo', $consecutivo);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   $result = $query->result();
			foreach($result as $row)
			{	
				return $row->Factura_Vendedor_Codigo;
			}
		}
		else
		{
		   return false;
		}
	}
	
	function getTipoCambio($consecutivo, $sucursal){
		$this -> db -> select('Factura_tipo_cambio');
		$this -> db -> from('TB_07_Factura');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Factura_Consecutivo', $consecutivo);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   $result = $query->result();
			foreach($result as $row)
			{	
				return $row->Factura_tipo_cambio;
			}
		}
		else
		{
		   return false;
		}
	}
	
	function getMoneda($consecutivo, $sucursal){
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
		}
		$this -> db -> select('Factura_Moneda');
		$this -> db -> from('TB_07_Factura');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Factura_Consecutivo', $consecutivo);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   $result = $query->result();
			foreach($result as $row)
			{	
				return $row->Factura_Moneda;
			}
		}
		else
		{
		   return false;
		}
	}
	
	function getArticulosFactura($consecutivo, $sucursal){
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
		}
		$this -> db -> select('*');
		$this -> db -> from('TB_08_Articulos_Factura');
		$this -> db -> where('TB_07_Factura_TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('TB_07_Factura_Factura_Consecutivo', $consecutivo);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   return $query->result();
		}
		else
		{
		   return false;
		}
	}
	
	function getArticulosFacturaImpresion($consecutivo, $sucursal){
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
		}
		//echo "entro";
		$this -> db -> select('Articulo_Factura_Codigo AS codigo, Articulo_Factura_Descripcion AS descripcion, Articulo_Factura_Cantidad AS cantidad, Articulo_Factura_Descuento AS descuento, Articulo_Factura_Exento AS exento, Articulo_Factura_Precio_Unitario AS precio');
		$this -> db -> from('TB_08_Articulos_Factura');
		$this -> db -> where('TB_07_Factura_TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('TB_07_Factura_Factura_Consecutivo', $consecutivo);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   return $query->result();
		}
		else
		{
		   return false;
		}
	}
	
	
	
	function actualizarFacturaHead($datos, $consecutivo, $sucursal){
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
		}
		$this->db->where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this->db->where('Factura_Consecutivo', mysql_real_escape_string($consecutivo));
		$this->db->update('TB_07_Factura' ,$datos);
	}
	
	function guardarPagoTarjeta($consecutivo, $sucursal, $transaccion, $comision, $vendedor, $cliente, $banco){		
				$sucursalVendedor = $sucursal;
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
		}
		$dataFactura = array(
						'Tarjeta_Numero_Transaccion'=>mysql_real_escape_string($transaccion),
						'Tarjeta_Comision_Banco'=>mysql_real_escape_string($comision), 
						'TB_07_Factura_Factura_Consecutivo'=>$consecutivo,
						'TB_07_Factura_TB_02_Sucursal_Codigo'=>$sucursal,
						'TB_07_Factura_Factura_Vendedor_Codigo'=>$vendedor,
						'TB_07_Factura_Factura_Vendedor_Sucursal'=>$sucursalVendedor,
						'TB_07_Factura_TB_03_Cliente_Cliente_Cedula'=>$cliente,
						'TB_22_Banco_Banco_Codigo'=>$banco												
					);			
		$this->db->insert('TB_18_Tarjeta',$dataFactura); 	
		return $this->db->insert_id();
	}
	
	function guardarPagoCheque($consecutivo, $sucursal, $transaccion, $vendedor, $cliente, $banco){		
		$sucursalVendedor = $sucursal;
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
		}
		$dataFactura = array(
						'Cheque_Numero'=>mysql_real_escape_string($transaccion), 
						'Banco'=>mysql_real_escape_string($banco), 
						'TB_07_Factura_Factura_Consecutivo'=>$consecutivo,
						'TB_07_Factura_TB_02_Sucursal_Codigo'=>$sucursal,
						'TB_07_Factura_Factura_Vendedor_Codigo'=>$vendedor,
						'TB_07_Factura_Factura_Vendedor_Sucursal'=>$sucursalVendedor,
						'TB_07_Factura_TB_03_Cliente_Cliente_Cedula'=>$cliente												
					);			
		$this->db->insert('TB_13_Cheque',$dataFactura); 			
	}
	
	function guardarPagoDeposito($consecutivo, $sucursal, $transaccion, $vendedor, $cliente, $banco){	
		$sucursalVendedor = $sucursal;	
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
		}
		$dataFactura = array(
						'Deposito_Numero_Transaccion'=>mysql_real_escape_string($transaccion), 
						'TB_07_Factura_Factura_Consecutivo'=>$consecutivo,
						'TB_07_Factura_TB_02_Sucursal_Codigo'=>$sucursal,
						'TB_07_Factura_Factura_Vendedor_Codigo'=>$vendedor,
						'TB_07_Factura_Factura_Vendedor_Sucursal'=>$sucursalVendedor,
						'TB_07_Factura_TB_03_Cliente_Cliente_Cedula'=>$cliente,
						'TB_22_Banco_Banco_Codigo'=>$banco
					);			
		$this->db->insert('TB_19_Deposito',$dataFactura); 			
	}
	
	function guardarPagoMixto($consecutivo, $sucursal, $transaccion, $comision, $vendedor, $cliente, $banco, $cantidadPagoTarjeta){
		
		//Creamos el pago con tarjeta primero
		$tarjeta = $this->guardarPagoTarjeta($consecutivo, $sucursal, $transaccion, $comision, $vendedor, $cliente, $banco);
		$sucursalVendedor = $sucursal;		
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
		}
		//Creamos el pago mixto
		$dataFactura = array(
						'Mixto_Cantidad_Paga'=>mysql_real_escape_string($cantidadPagoTarjeta), 
						'TB_18_Tarjeta_Tarjeta_Id'=>$tarjeta,
						'TB_18_Tarjeta_TB_07_Factura_Factura_Consecutivo'=>$consecutivo,
						'TB_18_Tarjeta_TB_07_Factura_TB_02_Sucursal_Codigo'=>$sucursal,
						'TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Codigo'=>$vendedor,
						'TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Sucursal'=>$sucursalVendedor,
						'TB_18_Tarjeta_TB_07_Factura_TB_03_Cliente_Cliente_Cedula'=>$cliente,
						'TB_18_Tarjeta_TB_22_Banco_Banco_Codigo'=>$banco						
					);			
		$this->db->insert('TB_23_Mixto',$dataFactura);
	}
	
	function guardarPagoCredito($consecutivo, $sucursal, $vendedor, $cliente, $numeroDias, $fecha, $saldo){
		$sucursalVendedor = $sucursal;
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
		}
		$dataGuardar = array(
							'Credito_Numero_Dias' => $numeroDias,
							'Credito_Saldo_Actual' => $saldo,
							'Credito_Saldo_Inicial' => $saldo,
							'Credito_Fecha_Expedicion' => $fecha,
							'Credito_Factura_Consecutivo' => $consecutivo,
							'Credito_Sucursal_Codigo' => $sucursal,
							'Credito_Vendedor_Codigo' => $vendedor,
							'Credito_Vendedor_Sucursal' => $sucursalVendedor,
							'Credito_Cliente_Cedula' => $cliente
							);
		$this->db->insert('TB_24_Credito',$dataGuardar);
		return $this->db->insert_id();
	}
	
	function guardarPagoAbono($credito, $abono){
		$dataGuardar = array(
							'Abono' => $abono,
							'Credito' => $credito
							);
		$this->db->insert('tb_40_apartado',$dataGuardar);
	}
	
	function getAbonoApartado($sucursal, $consecutivo){
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
		}
		/*
			SELECT 	tb_40_apartado.Abono		
			FROM tb_40_apartado
			JOIN tb_24_credito ON tb_40_apartado.Credito = tb_24_credito.Credito_Id
			WHERE tb_24_credito.Credito_Factura_Consecutivo = 1
			AND tb_24_credito.Credito_Sucursal_Codigo = 0
		*/
		$this->db->select('tb_40_apartado.Abono');
		$this->db->from('tb_40_apartado');
		$this->db->join('tb_24_credito','tb_40_apartado.Credito = tb_24_credito.Credito_Id');
		$this->db->where('tb_24_credito.Credito_Factura_Consecutivo', $consecutivo);
		$this->db->where('tb_24_credito.Credito_Sucursal_Codigo', $sucursal);
		$query = $this->db->get();
		if($query -> num_rows() != 0)
		{
		   $result = $query->result();
		   return $result[0]->Abono;
		}
		else
		{
			return 0;
		}
	}
	
	function getCreditosClientePorSucursal($cedula, $sucursal){
		
		$this -> db -> select('*');
		$this -> db -> from('TB_24_Credito');
		$this -> db -> where('Credito_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Credito_Cliente_Cedula', $cedula);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   return $query->result();
		}
		else
		{
			//Si no tiene creditos anteriores devuelve una array con 0, no se envia el cero como tal proque se parsea como false
		   return array((object)array('Credito_Saldo_Actual'=>'0'));
		}
	}
	
	function getCreditoClienteDeFactura($consecutivo, $sucursal, $cliente){
		$this -> db -> select('Credito_Numero_Dias');
		$this -> db -> from('TB_24_Credito');
		$this -> db -> where('Credito_Factura_Consecutivo', $consecutivo);
		$this -> db -> where('Credito_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Credito_Cliente_Cedula', $cliente);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		    $query = $query->result();
			foreach($query as $row){
				return $row -> Credito_Numero_Dias;
			}
		}
		else
		{
			//Si no lo encuentra envia el minimo de dias
			return '8';
		}
	}
	
	function eliminarArticulosFactura($consecutivo, $sucursal){
		$this -> db -> where('TB_07_Factura_TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('TB_07_Factura_Factura_Consecutivo', $consecutivo);
		$this->db->delete('TB_08_Articulos_Factura'); 
	}
	
	function getMontoTotalPago($sucursal, $consecutivo){
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
		}
		$this->db->where('Factura_Consecutivo', $consecutivo);
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->from('tb_07_factura');
		$query = $this -> db -> get();
		if($query -> num_rows() != 0)
		{
		    $query = $query->result();
			foreach($query as $row){
				return $row -> Factura_Monto_Total;
			}
		}
		else
		{
			return 0;
		}
	}
	
	function getMontoPagoTarjetaMixto($sucursal, $consecutivo){
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
		}
		$this->db->where('TB_18_Tarjeta_TB_07_Factura_Factura_Consecutivo', $consecutivo);
		$this->db->where('TB_18_Tarjeta_TB_07_Factura_TB_02_Sucursal_Codigo', $sucursal);
		$this->db->from('tb_23_mixto');
		$query = $this -> db -> get();
		if($query -> num_rows() != 0)
		{
		    $query = $query->result();
			foreach($query as $row){
				return $row -> Mixto_Cantidad_Paga;
			}
		}
		else
		{
			return 0;
		}
	}
	
	function getFacturasFiltradas($cliente, $desde, $hasta, $tipo, $estado, $sucursal){
		if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
				$sucursal = $this->cod_garotas;
				$facturas_desampa = $this->getFacturasDesampa();
				if(!empty($facturas_desampa)){
						$this->db->where_in("Factura_Consecutivo", $facturas_desampa);
				}
		}elseif($this->trueque && $sucursal == $this->cod_garotas){
				$facturas_desampa = $this->getFacturasDesampa();
				if(!empty($facturas_desampa)){
						$this->db->where_not_in("Factura_Consecutivo", $facturas_desampa);
				}
		}		
		$this->db->select("Factura_Consecutivo as consecutivo,
		                   Factura_Monto_Total as total,
						   Factura_Nombre_Cliente as cliente,
						   date_format(Factura_Fecha_Hora, '%d-%m-%Y %h:%i:%s %p') as fecha", false);
		$this->db->from('tb_07_factura');
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->setFiltradoCliente($cliente);
		$this->setFiltradoFechaDesde($desde);
		$this->setFiltradoFechaHasta($hasta);
		$this->setFiltradoTipo($tipo);
		$this->setFiltradoEstado($estado);
		$this->db->order_by('Factura_Consecutivo','desc');
		$query = $this -> db -> get();
		if($query -> num_rows() != 0)
		{
		    return $query->result();			
		}
		else
		{
			return false;
		}
	}
	
	function setFiltradoCliente($cliente){
		if(trim($cliente)!=''){
			$this->db->where('TB_03_Cliente_Cliente_Cedula', $cliente);
		}
	}
	
	function setFiltradoFechaDesde($fecha){
		if(trim($fecha)!=''){
			$fecha = $this->convertirFecha($fecha);
			$this->db->where('Factura_Fecha_Hora >=', $fecha);
		}
	}
	
	function setFiltradoFechaHasta($fecha){
		if(trim($fecha)!=''){
			$fecha = $this->convertirFecha($fecha);
			$this->db->where('Factura_Fecha_Hora <=', $fecha);
		}
	}
	
	function setFiltradoTipo($tipos){
		if(sizeOf($tipos)>0){
			$this->db->where_in('Factura_Tipo_Pago', $tipos);
		}
	}
	
	function setFiltradoEstado($estados){
		if(sizeOf($estados)>0){
			$this->db->where_in('Factura_Estado', $estados);
		}
	}
	
	function convertirFecha($fecha){		
		if(trim($fecha)!=''){
			$fecha = explode("/",$fecha);
			$fecha = $fecha[0]."-".$fecha[1]."-".$fecha[2]." 00:00:00";
			//echo $fecha;
			date_default_timezone_set("America/Costa_Rica");
			return date("Y-m-d : H:i:s", strtotime($fecha));
		}		
		return $fecha;
	}
	
	function getFacturasDesampa(){
			$this->db->select("Consecutivo");
			$this->db->from("tb_46_relacion_desampa");
			$this->db->where("Documento", "factura");
			$query = $this->db->get();
			if($query->num_rows()==0){
					return array();
			}else{
					$facturas = array();
					foreach($query->result() as $f){
							array_push($facturas, $f->Consecutivo);
					}
					return $facturas;
			}
	}
	
}


?>