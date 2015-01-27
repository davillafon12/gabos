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
		if($consecutivo = $this->getConsecutivo($sucursal)){
			//return $consecutivo;
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
							'Factura_Vendedor_Sucursal'=>$sucursal,	
							'TB_03_Cliente_Cliente_Cedula'=>mysql_real_escape_string($cedula)													
	                    );			
	        $this->db->insert('TB_07_Factura',$dataFactura); 
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
	
	function addItemtoInvoice($codigo, $descripcion, $cantidad, $descuento, $exento, $precio, $consecutivo, $sucursal, $vendedor, $cliente, $imagen){
		$dataItem = array(
	                        'Articulo_Factura_Codigo'=>mysql_real_escape_string($codigo),
	                        'Articulo_Factura_Descripcion'=>mysql_real_escape_string($descripcion), 
							'Articulo_Factura_Cantidad'=>mysql_real_escape_string($cantidad),
							'Articulo_Factura_Descuento'=>mysql_real_escape_string($descuento),
							'Articulo_Factura_Exento'=>mysql_real_escape_string($exento),
							'Articulo_Factura_Precio_Unitario'=>mysql_real_escape_string($precio),	
							'Articulo_Factura_Imagen'=>mysql_real_escape_string($imagen),
							'TB_07_Factura_Factura_Consecutivo'=>mysql_real_escape_string($consecutivo),
							'TB_07_Factura_TB_02_Sucursal_Codigo'=>mysql_real_escape_string($sucursal),
							'TB_07_Factura_Factura_Vendedor_Codigo'=>mysql_real_escape_string($vendedor),
							'TB_07_Factura_Factura_Vendedor_Sucursal'=>mysql_real_escape_string($sucursal),
							'TB_07_Factura_TB_03_Cliente_Cliente_Cedula'=>mysql_real_escape_string($cliente)							
	                    );			
	        $this->db->insert('TB_08_Articulos_Factura',$dataItem);
	}
	
	function getCostosTotalesFactura($consecutivo, $sucursal){
		$costo_total = 0;
		$iva = 0;
		$costo_sin_iva = 0;
		
		if($articulos = $this->getItemsFactura($consecutivo, $sucursal)){
			foreach($articulos as $articulo)
			{
				//Calculamos el precio total de los articulos
				$precio_total_articulo = (($articulo->Articulo_Factura_Precio_Unitario)-(($articulo->Articulo_Factura_Precio_Unitario)*(($articulo->Articulo_Factura_Descuento)/100)))*$articulo->Articulo_Factura_Cantidad;
				
				//Calculamos los impuestos
				//Traemos el array de configuracion para obtener el porcentaje
				$c_array = $this->getConfgArray();
				$isExento = $articulo->Articulo_Factura_Exento;
				if($isExento=='0'){
					$costo_sin_iva += $precio_total_articulo/(1+(floatval($c_array['iva'])/100));
				}
				else if($isExento=='1'){
					$costo_sin_iva += $precio_total_articulo;
				}
				$costo_total += $precio_total_articulo;
				//$costo_sin_iva += (($articulo->Articulo_Factura_Precio_Unitario)-(($articulo->Articulo_Factura_Precio_Unitario)*(($articulo->Articulo_Factura_Descuento)/100)))*$articulo->Articulo_Factura_Cantidad;
			}
			$iva = $costo_total-$costo_sin_iva;
		}
		
		return array('Factura_Monto_Total'=>$costo_total, 'Factura_Monto_IVA'=>$iva, 'Factura_Monto_Sin_IVA'=>$costo_sin_iva);
	}
	
	function getItemsFactura($consecutivo, $sucursal){
		//$this -> db -> select('Articulo_Factura_Cantidad, Articulo_Factura_Descuento, Articulo_Factura_Precio_Unitario, Articulo_Factura_Exento');
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
		$this->db->where('Factura_Consecutivo', $consecutivo);
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->update('TB_07_Factura' ,$data);
	}
	
	function getFacturasPendientes($sucursal){
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
	
	function getCliente($consecutivo, $sucursal){
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
	
	function getArticulosFactura($consecutivo, $sucursal){
		//echo "entro";
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
	
	function actualizarFacturaHead($datos, $consecutivo, $sucursal){
		$this->db->where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this->db->where('Factura_Consecutivo', mysql_real_escape_string($consecutivo));
		$this->db->update('TB_07_Factura' ,$datos);
	}
	
	function guardarPagoTarjeta($consecutivo, $sucursal, $transaccion, $comision, $vendedor, $cliente, $banco){		
		$dataFactura = array(
						'Tarjeta_Numero_Transaccion'=>mysql_real_escape_string($transaccion),
						'Tarjeta_Comision_Banco'=>mysql_real_escape_string($comision), 
						'TB_07_Factura_Factura_Consecutivo'=>$consecutivo,
						'TB_07_Factura_TB_02_Sucursal_Codigo'=>$sucursal,
						'TB_07_Factura_Factura_Vendedor_Codigo'=>$vendedor,
						'TB_07_Factura_Factura_Vendedor_Sucursal'=>$sucursal,
						'TB_07_Factura_TB_03_Cliente_Cliente_Cedula'=>$cliente,
						'TB_22_Banco_Banco_Codigo'=>$banco												
					);			
		$this->db->insert('TB_18_Tarjeta',$dataFactura); 	
		return $this->db->insert_id();
	}
	
	function guardarPagoCheque($consecutivo, $sucursal, $transaccion, $vendedor, $cliente){		
		$dataFactura = array(
						'Cheque_Numero'=>mysql_real_escape_string($transaccion), 
						'TB_07_Factura_Factura_Consecutivo'=>$consecutivo,
						'TB_07_Factura_TB_02_Sucursal_Codigo'=>$sucursal,
						'TB_07_Factura_Factura_Vendedor_Codigo'=>$vendedor,
						'TB_07_Factura_Factura_Vendedor_Sucursal'=>$sucursal,
						'TB_07_Factura_TB_03_Cliente_Cliente_Cedula'=>$cliente												
					);			
		$this->db->insert('TB_13_Cheque',$dataFactura); 			
	}
	
	function guardarPagoDeposito($consecutivo, $sucursal, $transaccion, $vendedor, $cliente, $banco){		
		$dataFactura = array(
						'Deposito_Numero_Transaccion'=>mysql_real_escape_string($transaccion), 
						'TB_07_Factura_Factura_Consecutivo'=>$consecutivo,
						'TB_07_Factura_TB_02_Sucursal_Codigo'=>$sucursal,
						'TB_07_Factura_Factura_Vendedor_Codigo'=>$vendedor,
						'TB_07_Factura_Factura_Vendedor_Sucursal'=>$sucursal,
						'TB_07_Factura_TB_03_Cliente_Cliente_Cedula'=>$cliente,
						'TB_22_Banco_Banco_Codigo'=>$banco
					);			
		$this->db->insert('TB_19_Deposito',$dataFactura); 			
	}
	
	function guardarPagoMixto($consecutivo, $sucursal, $transaccion, $comision, $vendedor, $cliente, $banco, $cantidadPagoTarjeta){		
		//Creamos el pago con tarjeta primero
		$tarjeta = $this->guardarPagoTarjeta($consecutivo, $sucursal, $transaccion, $comision, $vendedor, $cliente, $banco);
		//Creamos el pago mixto
		$dataFactura = array(
						'Mixto_Cantidad_Paga'=>mysql_real_escape_string($cantidadPagoTarjeta), 
						'TB_18_Tarjeta_Tarjeta_Id'=>$tarjeta,
						'TB_18_Tarjeta_TB_07_Factura_Factura_Consecutivo'=>$consecutivo,
						'TB_18_Tarjeta_TB_07_Factura_TB_02_Sucursal_Codigo'=>$sucursal,
						'TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Codigo'=>$vendedor,
						'TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Sucursal'=>$sucursal,
						'TB_18_Tarjeta_TB_07_Factura_TB_03_Cliente_Cliente_Cedula'=>$cliente,
						'TB_18_Tarjeta_TB_22_Banco_Banco_Codigo'=>$banco						
					);			
		$this->db->insert('TB_23_Mixto',$dataFactura);
	}
	
	function guardarPagoCredito($consecutivo, $sucursal, $vendedor, $cliente, $numeroDias, $fecha, $saldo){
		$dataGuardar = array(
							'Credito_Numero_Dias' => $numeroDias,
							'Credito_Saldo_Actual' => $saldo,
							'Credito_Saldo_Inicial' => $saldo,
							'Credito_Fecha_Expedicion' => $fecha,
							'Credito_Factura_Consecutivo' => $consecutivo,
							'Credito_Sucursal_Codigo' => $sucursal,
							'Credito_Vendedor_Codigo' => $vendedor,
							'Credito_Vendedor_Sucursal' => $sucursal,
							'Credito_Cliente_Cedula' => $cliente
							);
		$this->db->insert('TB_24_Credito',$dataGuardar);
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
	
}


?>