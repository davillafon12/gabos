<?php 
Class contabilidad extends CI_Model
{
	
	 
	
	function existeFacturaPorId($id){
		$this->db->where('Credito_Id', mysql_real_escape_string($id));
		$this->db->from('tb_24_credito');
		$this->db-> limit(1);
		$query = $this->db->get();
		
		
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{			
			return true;
		}
	}
	
	function getCreditosOrdenadosPorConsecutivoMenorAMayor($facturas){
		$this->db->where_in('Credito_Id', $facturas);
		$this->db->from('tb_24_credito');
		$this->db->order_by('Credito_Factura_Consecutivo','ASC');
		$query = $this->db->get();
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{			
			return $query->result();
		}
	}	
	
	function saldarFactura($id, $saldoNuevo){
		$datos = array(
						'Credito_Saldo_Actual' => $saldoNuevo
						);
		$this->db->where('Credito_Id', $id);
		$this->db->update('tb_24_credito', $datos); 
	}
	
	function getConsecutivoUltimoRecibo($sucursal)
	{
		$this -> db -> select('Consecutivo');
		$this -> db -> from('TB_26_Recibos_Dinero');
		$this -> db -> join('TB_24_Credito', 'TB_26_Recibos_Dinero.Credito = TB_24_Credito.Credito_Id');
		$this -> db -> where('TB_24_Credito.Credito_Sucursal_Codigo', $sucursal);
		$this -> db -> order_by('Consecutivo', 'desc');
		$this -> db -> limit(1);
		$query = $this -> db -> get();
		if($query->num_rows()==0)
		{
			return 0;
		}
		else
		{			
			$result = $query->result();
			foreach($result as $row)
			{$consecutivo=$row->Consecutivo;}
			return $consecutivo;
		}
	}
	
	function agregarRecibo($sucursal, $codigoCredito, $saldo, $montoPagado, $tipoPago, $comentarios){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		date_default_timezone_set("America/Costa_Rica");
		$Current_datetime = date("y/m/d : H:i:s", now());
		$consecutivo = $this->getConsecutivoUltimoRecibo($sucursal)+1;
		$datos = array(
						'Consecutivo' => $consecutivo,
						'Recibo_Cantidad' => $montoPagado,
						'Recibo_Fecha' => $Current_datetime,
						'Recibo_Saldo' => $saldo,
						'Tipo_Pago' => $tipoPago,
						'Credito' => $codigoCredito,
						'Comentarios' => $comentarios
						);
		$this->db->insert('tb_26_recibos_dinero',$datos);
		//return $this->db->insert_id();
		return $consecutivo;
	} 
	
	function getConsecutivo($sucursal) //Traer el siguiente consecutivo de una empresa en particular
	{
		return $this->getConsecutivoUltimaNotaCredito($sucursal)+1;
	}
	
	function getConsecutivoUltimaNotaCredito($sucursal)
	{
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this -> db -> select('Consecutivo');
		$this -> db -> from('TB_27_Notas_Credito');
		$this -> db -> where('Sucursal', $sucursal);
		$this -> db -> order_by("Consecutivo", "desc");
		$this -> db -> limit(1);
		$query = $this -> db -> get();
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{			
			$result = $query->result();
			foreach($result as $row)
			{$consecutivo=$row->Consecutivo;}
			return $consecutivo;
		}
	}
	
	function getReciboParaImpresion($recibo, $sucursal){
		$queryLoco = "";
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->factura->getFacturasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$queryLoco = "AND TB_07_Factura.Factura_Consecutivo IN (".implode($facturas_trueque,',').")";
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->factura->getFacturasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$queryLoco = "AND TB_07_Factura.Factura_Consecutivo NOT IN (".implode($facturas_trueque,',').")";
				}
		}
		$query = $this->db->query("
			SELECT  tb_26_recibos_dinero.Consecutivo AS recibo,
					tb_26_recibos_dinero.Recibo_Cantidad AS monto,
					date_format(tb_26_recibos_dinero.Recibo_Fecha, '%d-%m-%Y %h:%i:%s %p') AS fecha_recibo, 
					tb_26_recibos_dinero.Recibo_Saldo AS saldo,
					tb_26_recibos_dinero.Tipo_Pago AS tipo_pago,
					tb_07_factura.Factura_Monto_Total AS Saldo_inicial,
					date_format(tb_24_credito.Credito_Fecha_Expedicion, '%d-%m-%Y %h:%i:%s %p') AS fecha_expedicion,
					tb_24_credito.Credito_Factura_Consecutivo AS factura,
					tb_07_factura.Factura_Moneda AS moneda,
					tb_07_factura.Factura_Nombre_Cliente AS cliente_nombre,
					tb_07_factura.TB_03_Cliente_Cliente_Cedula AS cliente_cedula,
					tb_26_recibos_dinero.Credito AS c,
					tb_26_recibos_dinero.Comentarios AS comentarios
			FROM tb_26_recibos_dinero
			JOIN tb_24_credito ON tb_24_credito.Credito_Id = tb_26_recibos_dinero.Credito
			JOIN tb_07_factura ON tb_07_factura.Factura_Consecutivo = tb_24_credito.Credito_Factura_Consecutivo
			WHERE  tb_24_credito.Credito_Sucursal_Codigo = $sucursal
			AND    tb_26_recibos_dinero.Consecutivo = $recibo
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
	
	function getSaldoAnteriorRecibo($recibo, $credito){
		/*
			SELECT Recibo_Saldo FROM tb_26_recibos_dinero
			WHERE  Credito = 1
			AND Consecutivo != 3
			ORDER BY Consecutivo DESC
			LIMIT 1
		*/
		$this->db->select("Recibo_Saldo");
		$this->db->where("Credito", $credito);
		$this->db->where("Consecutivo !=", $recibo);
		$this->db->from('tb_26_recibos_dinero');
		$this->db->order_by('Consecutivo', 'desc');
		$this->db->limit(1);
		$query = $this->db->get();
		if($query -> num_rows() != 0)
		{
		   $result = $query->result();
		   return $result[0]->Recibo_Saldo;
		}
		else
		{
		   return false;
		}
	}
	
	function agregarNotaCreditoCabecera($consecutivo, $fecha, $nombre, $cliente, $sucursal, $facturaAcreditar, $facturaAplicar, $tipoPago, $moneda, $por_iva, $tipo_cambio, $esAnulacion = false){
		$sucursalVendedor = $sucursal;
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es una sucursal con trueque
				$sucursal = $this->sucursales_trueque[$sucursal];
				$this->truequeAplicado = true;
		}
		$datos = array(
						'Consecutivo' => $consecutivo,
						'Nombre_Cliente' => $nombre,
						'Fecha_Creacion' => $fecha,
						'Factura_Acreditar' => $facturaAcreditar,
						'Factura_Aplicar' => $facturaAplicar,
						'Tipo_Pago' => $tipoPago,
						'Moneda' => $moneda,
						'Por_IVA' => $por_iva,
						'Tipo_Cambio' => $tipo_cambio,
                                                'Es_Anulacion' => $esAnulacion,
						'Sucursal' => $sucursal,
						'Cliente' => $cliente
						);
		$this->db->insert('TB_27_Notas_Credito', $datos);
		
		if($this->truequeHabilitado && $this->truequeAplicado){ //Si se aplico el trueque, se debe guardar el documento
			$datos = array("Consecutivo" => $consecutivo,
							"Documento" => 'nota_credito',
							"Sucursal" => $sucursalVendedor);
			$this->db->insert("tb_46_relacion_trueque", $datos);
			$this->truequeAplicado = false;
		}
		
		return $this->existeNotaCredito($consecutivo, $sucursal);
	}
	
	function existeNotaCredito($consecutivo, $sucursal){
		$this->db->where('Consecutivo', $consecutivo);
		$this->db->where('Sucursal', $sucursal);
		$this->db->from('TB_27_Notas_Credito');
		$query = $this->db->get();
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{			
			return true;
		}
	}
	
	function agregarProductosNotaCredito($consecutivo, $sucursal, $productos, $cliente, $facturaAcreditar){
		
/*
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
*/
		
		
		$datos = array();
		
		// Para valorar el precio real unitario debemos obtener el 
		// precio con el que se hizo la factura y no con el que este en la bd
		// esto porque puede que al cliente se le vendio con descuento 
		$this->load->model("factura","",true);
		foreach($productos as $producto){
			$descripcion = ""; 
			$precio = ""; 
			$descuento = 0;
			$exento = 0;
			$noRetencion = 0;
			$precioFinal = 0;
			if(trim($producto->c) == "00"){
				$descripcion = trim($producto->ds);
				$precio = trim($producto->p);
				$precioFinal = $precio;
			}else{
				$descripcion = $this->articulo->getArticuloDescripcion($producto->c, $sucursal);
				$precio = $this->precioArticuloEnFacturaDeterminada($facturaAcreditar, $sucursal, $producto->c);
				$articuloCompleto = $this->factura->getArticuloFactura($facturaAcreditar, $sucursal, $producto->c);
				$descuento = $articuloCompleto->Articulo_Factura_Descuento;
				$exento = $articuloCompleto->Articulo_Factura_Exento;
				$noRetencion = $articuloCompleto->Articulo_Factura_No_Retencion;
				$precioFinal = $articuloCompleto->Articulo_Factura_Precio_Final;
			}
			//Agregamos los datos a un array para ser agregado a la bd
			$pro = array(
						'Codigo' => $producto->c,
						'Descripcion' => $descripcion,
						'Cantidad_Bueno' => $producto->b,
						'Cantidad_Defectuoso' => $producto->d,
						'Precio_Unitario' => $precio,
						'Precio_Final' => $precioFinal,
						'Descuento' => $descuento,
						'Exento' => $exento,
						'No_Retencion' => $noRetencion,
						'Nota_Credito_Consecutivo' => $consecutivo,
						'Sucursal' => $sucursal
						);
			array_push($datos, $pro);
			
			//Ahora debemos aumentar el inventario segun sea el caso
			//Sumamos cantidades buenas
			$this->articulo->actualizarInventarioSUMA($producto->c, $producto->b, $sucursal);
			//Sumamos cantidades defectuosas
			$this->articulo->actualizarInventarioSUMADefectuoso($producto->c, $producto->d, $sucursal);
		}
		$this->db->insert_batch('TB_28_Productos_Notas_Credito', $datos);
	}
	
	function precioArticuloEnFacturaDeterminada($factura, $sucursal, $articulo){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this->db->select('Articulo_Factura_Descuento as descuento, Articulo_Factura_Precio_Unitario as precio');
		$this->db->from('tb_08_articulos_factura');
		$this->db->where('TB_07_Factura_Factura_Consecutivo',$factura);
		$this->db->where('TB_07_Factura_TB_02_Sucursal_Codigo',$sucursal);
		$this->db->where('Articulo_Factura_Codigo',$articulo);
		$query = $this->db->get();
		$art = $query->result()[0];
		//Calculamos el precio con el descuento
		return ($art->precio - ($art->precio * ($art->descuento/100)));
	}
	
	function getNotaCreditoHeaderParaImpresion($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$query = $this->db->query("
			SELECT 	Consecutivo AS nota, 
					Nombre_Cliente AS cliente_nombre, 
					Cliente AS cliente_cedula,
					date_format(Fecha_Creacion, '%d-%m-%Y %h:%i:%s %p') AS fecha,
					Factura_Aplicar AS factura_aplicar,
					Tipo_Pago AS tipo_pago,
					Moneda AS moneda,
					Por_IVA AS iva,
					Tipo_Cambio AS tipo_cambio
			FROM TB_27_Notas_Credito
			WHERE Consecutivo = $consecutivo
			AND Sucursal = $sucursal
		");
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{			
			return $query->result();
		}
	}
        
        function getNotaCredito($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
                $this->db->where("Consecutivo", $consecutivo);
                $this->db->where("Sucursal", $sucursal);
                $this->db->from("TB_27_Notas_Credito");
		$query = $this->db->get();
		if($query->num_rows()==0){
                    return false;
		}else{			
                    return $query->result()[0];
		}
	}
        
        function getArticulosNotaCredito($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
                $this->db->where("Nota_Credito_Consecutivo", $consecutivo);
                $this->db->where("Sucursal", $sucursal);
                $this->db->from("tb_28_productos_notas_credito");
		$query = $this->db->get();
		if($query->num_rows()==0){
                    return false;
		}else{			
                    return $query->result();
		}
	}
	
	function getArticulosNotaCreditoParaImpresion($consecutivo, $sucursal){
/*
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
*/
		$this->db->select("
		Codigo AS codigo, 
		Descripcion AS descripcion, 
		Cantidad_Bueno AS bueno, 
		Cantidad_Defectuoso AS defectuoso, 
		Precio_Unitario AS precio,
		Precio_Final AS precio_final,
		Descuento AS descuento,
		Exento AS exento,
		No_Retencion AS no_retencion");
		$this->db->from("tb_28_productos_notas_credito");
		$this->db->where("Nota_Credito_Consecutivo",$consecutivo);
		$this->db->where("Sucursal",$sucursal);
		$query = $this->db->get();
		
		
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{			
			return $query->result();
		}
	}
	
	function facturaAplciarYaFueAplicada($factura, $sucursal){
		$this->db->where('Factura_Aplicar', $factura);
		$this->db->where('Sucursal', $sucursal);
		$this->db->from('TB_27_Notas_Credito');
		$query = $this->db->get();
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{			
			return true;
		}
	}
	
	function getRecibos($cliente, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$this -> db -> where('Credito_Vendedor_Sucursal', $sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this -> db -> select('Consecutivo, Credito, Recibo_Cantidad, Recibo_Fecha, Recibo_Saldo, Credito_Factura_Consecutivo');
		$this -> db -> from('TB_26_recibos_dinero');
		$this -> db -> join('TB_24_credito','Credito_Id = Credito');
		$this -> db -> where('Credito_Sucursal_Codigo', $sucursal); 
		$this -> db -> where('Credito_Cliente_Cedula', $cliente);
		$this -> db -> where('Anulado', 0);
		$this -> db -> order_by("Consecutivo", "asc");
		$query = $this->db->get();
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{			
			return $query->result();
		}
	}
	
	function existeRecibo($recibo, $credito){
		$this->db->where('Credito', $credito);
		$this->db->where('Consecutivo', $recibo);
		$this->db->from('tb_26_recibos_dinero');
		$query = $this->db->get();
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{			
			return true;
		}
	}
	
	function existeReciboBySucursal($recibo, $sucursal){
		$this->db->where('Credito_Sucursal_Codigo', $sucursal);		
		$this->db->where('Consecutivo', $recibo);
		$this->db->join('tb_24_credito', 'tb_26_recibos_dinero.credito = tb_24_credito.Credito_Id');
		$this->db->from('tb_26_recibos_dinero');
		$query = $this->db->get();
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{			
			return $query->result();
		}
	}
	
	function getMontoRecibo($recibo, $credito){
		$this->db->where('Credito', $credito);
		$this->db->where('Consecutivo', $recibo);
		$this->db->from('tb_26_recibos_dinero');
		$query = $this->db->get();
		if($query->num_rows()==0)
		{
			return 0;
		}
		else
		{			
			$result = $query->result();
			foreach($result as $rec){
				return $rec->Recibo_Cantidad;
			}
		}
	}
	
	function getMontoCredito($credito){
		$this->db->where('Credito_Id', $credito);
		$this->db->from('tb_24_credito');
		$query = $this->db->get();
		if($query->num_rows()==0)
		{
			return 0;
		}
		else
		{			
			$result = $query->result();
			foreach($result as $cre){
				return $cre->Credito_Saldo_Actual;
			}
		}
	}
	
	function actualizarCredito($datos, $credito){
		$this->db->where('Credito_Id', $credito);
		$this->db->update('tb_24_credito', $datos);
	}
	
	function flagAnularRecibo($recibo, $credito){
		$this->db->where('Credito', $credito);
		$this->db->where('Consecutivo', $recibo);
		$datos = array('Anulado'=>1);
		$this->db->update('tb_26_recibos_dinero', $datos);
	}
	
	function guardarDepositoRecibo($recibo, $credito, $deposito, $id_banco, $banco_nombre){
		date_default_timezone_set("America/Costa_Rica");
		$fecha = date("y/m/d : H:i:s", now());
		$datos = array(
						'Banco_id' => $id_banco,
						'Banco_Nombre' => $banco_nombre,
						'Numero_Deposito' => $deposito,
						'Fecha' => $fecha,
						'Recibo' => $recibo,
						'Credito' => $credito
						);
		$this->db->insert('tb_29_deposito_recibo', $datos);
	}
	
	function guardarPagoTarjeta($autorizacion, $banco, $comision, $recibo, $credito){
		$datos = array(
						'Numero_Autorizacion' => $autorizacion,
						'Comision_Por' => $comision,
						'Banco' => $banco,
						'Recibo' => $recibo,
						'Credito' => $credito
						);
		$this->db->insert('tb_32_tarjeta_recibos', $datos);
	}
	
	function getConsecutivoUltimaNotaDebito($sucursal)
	{
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this -> db -> select('Consecutivo');
		$this -> db -> from('tb_30_notas_debito');
		$this -> db -> where('Sucursal', $sucursal);
		$this -> db -> order_by('Consecutivo', 'desc');
		$this -> db -> limit(1);
		$query = $this -> db -> get();
		if($query->num_rows()==0)
		{
			return 0;
		}
		else
		{			
			$result = $query->result();
			foreach($result as $row)
			{$consecutivo=$row->Consecutivo;}
			return $consecutivo;
		}
	}
	
	function crearNotaDebito($consecutivo, $fecha, $porcentaje_iva, $usuario, $sucursal, $sucursalRecibe, $sucursalEntrega){
		$sucursalVendedor = $sucursal;
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es una sucursal con trueque
				$sucursal = $this->sucursales_trueque[$sucursal];
				$this->truequeAplicado = true;
		}
		$datos = array(
						'Consecutivo' => $consecutivo,
						'Fecha' => $fecha,
						'Impuesto_Porcentaje' => $porcentaje_iva,
						'Usuario' => $usuario,
						'Sucursal' => $sucursal,
						'Sucursal_Recibe' => $sucursalRecibe,
						'Sucursal_Entrega' => $sucursalEntrega
						);
		$this->db->insert('tb_30_notas_debito', $datos);
		
		if($this->truequeHabilitado && $this->truequeAplicado){ //Si se aplico el trueque, se debe guardar el documento
			$datos = array("Consecutivo" => $consecutivo,
							"Documento" => 'nota_debito',
							"Sucursal" => $sucursalVendedor);
			$this->db->insert("tb_46_relacion_trueque", $datos);
			$this->truequeAplicado = false;
		}
	}
	
	function agregarArticuloNotaDebito($codigo, $descripcion, $cantidad, $costo, $notaConsecutivo, $sucursal, $usuario){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$datos = array(
						'Codigo' => $codigo,
						'Descripcion' => $descripcion,
						'Cantidad_Debitar' => $cantidad,
						'Precio_Unitario' => $costo,
						'Nota_Debito_Consecutivo' => $notaConsecutivo,
						'Sucursal' => $sucursal,
						'Usuario' => $usuario
						);
		$this->db->insert('tb_31_productos_notas_debito', $datos);
	}
	
	function getHeadNotaDebito($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$query = $this->db->query("
			SELECT 	Consecutivo AS nota, 
					date_format(Fecha, '%d-%m-%Y %h:%i:%s %p') AS fecha,
					Impuesto_Porcentaje AS iva,
					Observaciones AS observaciones,
					Sucursal_Recibe AS recibe,
					Sucursal_Entrega AS entrega        
				   FROM tb_30_notas_debito
			WHERE Consecutivo = $consecutivo
			AND Sucursal = $sucursal
		");
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{			
			return $query->result();
		}
	}
	
	function getProductosNotaDebito($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this->db->select("Codigo AS codigo, Descripcion AS descripcion, Cantidad_Debitar AS cantidad, Precio_Unitario AS precio");
		$this->db->from("tb_31_productos_notas_debito");
		$this->db->where("Sucursal", $sucursal);
		$this->db->where("Nota_Debito_Consecutivo", $consecutivo);
		$query = $this->db->get();
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{			
			return $query->result();
		}
	}
	
	function crearRetiroParcial($monto, $fecha, $tipo_cambio, $usuario, $sucursal){
		$datos = array(
						'Monto' => $monto,
						'Fecha_Hora' => $fecha,
						'Tipo_Cambio' => $tipo_cambio,
						'Sucursal' => $sucursal,
						'Usuario' => $usuario
						);
		$this->db->insert('TB_33_Retiros_Parciales', $datos);
		return $this->db->insert_id();
	}
	
	function getRetiroParcialHeadImpresion($retiro){
		$this->db->select("	TB_33_Retiros_Parciales.Id as consecutivo,
							TB_33_Retiros_Parciales.Monto as monto, 
							date_format(TB_33_Retiros_Parciales.Fecha_Hora, '%d-%m-%Y %h:%i:%s %p') as fecha,
							TB_33_Retiros_Parciales.Tipo_Cambio as tipo,
							TB_33_Retiros_Parciales.Sucursal as sucursal,
							CONCAT(tb_01_usuario.Usuario_Nombre, ' ', tb_01_usuario.Usuario_Apellidos) as usuario", false);
		$this->db->from('TB_33_Retiros_Parciales');
		$this->db->join('tb_01_usuario', 'tb_01_usuario.Usuario_Codigo = TB_33_Retiros_Parciales.Usuario');
		$this->db->where('Id', $retiro);
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result()[0];
		}
	}
	
	function agregarDenominacionRetiroParcial($denominacion, $cantidad, $tipo, $moneda, $retiro){
		$datos = array(
						'Denominacion' => $denominacion,
						'Cantidad' => $cantidad,
						'Tipo' => $tipo,
						'Moneda' => $moneda,
						'Retiro' => $retiro
						);
		$this->db->insert('tb_42_moneda_retiro_parcial', $datos);
	}
	
	function getDenominacionesRetiroParcialPorTipoYMoneda($retiro, $tipo, $moneda){
		$this->db->select('Denominacion as denominacion, Cantidad as cantidad');
		$this->db->from('tb_42_moneda_retiro_parcial');
		$this->db->where('Tipo',$tipo);
		$this->db->where('Moneda',$moneda);
		$this->db->where('Retiro',$retiro);
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result();
		}
	}
	
	function getFechaUltimoCierreCaja($sucursal){
		$this->db->where('Sucursal', $sucursal);
		$this->db->from('tb_37_cierre_caja');
		$query = $this->db->get();
		if($query->num_rows()==0)
		{
			//Si no hay cierres de caja devolvemos una fecha vieja para que agarre todas facturas
			return strtotime('01-01-2000 00:00:00');
		}
		else
		{			
			$result = $query->result();
			foreach($result as $row)
			{ $fecha = $row->Fecha; }
			return strtotime($fecha);
		}
	}
	
	function getFechaUltimoCierreCajaAntesDeCierreCaja($sucursal, $consecutivo){
		$this->db->where('Sucursal', $sucursal);
		$this->db->from('tb_37_cierre_caja');
		$query = $this->db->get();
		if($query->num_rows()==0||$query->num_rows()==1) //Si tiene un cieere quiere decir que no tiene fecha anterior
		{
			//Si no hay cierres de caja devolvemos una fecha vieja para que agarre todas facturas
			return strtotime('01-01-2000 00:00:00');
		}
		else
		{			
			$result = $query->result();
			$fecha = '';
			foreach($result as $row)
			{ 
					if($row->Id==$consecutivo){
						break; //Antes de que cargue la fecha de el, rompemos el ciclo para que quede con la fecha del ultimo cierre antes de el
					}
					$fecha = $row->Fecha; 
			}
			return strtotime($fecha);
		}
	}
	
	function getFacturasEntreRangoFechas($sucursal, $inicio, $final){
		$this->load->model("factura", "", true);
		
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->factura->getFacturasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("Factura_Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->factura->getFacturasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("Factura_Consecutivo", $facturas_trueque);
				}
		}
		$this->db->where('Factura_Fecha_Hora >', $inicio);
		$this->db->where('Factura_Fecha_Hora <', $final);
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->where('Factura_Estado', 'cobrada');
		$this->db->order_by('Factura_Consecutivo', 'asc'); 
		$this->db->from('tb_07_factura');		
		$query = $this->db->get();
		//echo $this->db->last_query();
		if($query->num_rows()==0)
		{			
			return false;
		}
		else
		{			
			return $query->result();
		}
	}
	
	function getRetirosParcialesRangoFechas($sucursal, $inicio, $final){
		$this->db->where('Fecha_Hora >', $inicio);
		$this->db->where('Fecha_Hora <', $final);
		$this->db->where('Sucursal', $sucursal);
		$this->db->order_by('Fecha_Hora', 'asc'); 
		$this->db->from('tb_33_retiros_parciales');		
		$query = $this->db->get();
		if($query->num_rows()==0)
		{			
			return false;
		}
		else
		{			
			return $query->result();
		}
	}
	
	function getFacturasPagasTarjetaRangoFechasYBanco($sucursal, $banco, $inicio, $final){
		/*
			SELECT * FROM TB_07_Factura
			JOIN TB_18_Tarjeta ON TB_07_Factura.Factura_Consecutivo = TB_18_Tarjeta.TB_07_Factura_Factura_Consecutivo
			WHERE TB_07_Factura.Factura_Tipo_Pago = 'tarjeta'
			OR TB_07_Factura.Factura_Tipo_Pago = 'mixto'
			AND TB_07_Factura.TB_02_Sucursal_Codigo = 0
			AND TB_18_Tarjeta.TB_22_Banco_Banco_Codigo
		*/
		/*$this->db->from('tb_07_factura');
		$this->db->join('TB_18_Tarjeta', 'TB_07_Factura.Factura_Consecutivo = TB_18_Tarjeta.TB_07_Factura_Factura_Consecutivo');
		$this->db->where('TB_07_Factura.Factura_Tipo_Pago', 'tarjeta');
		$this->db->or_where('TB_07_Factura.Factura_Tipo_Pago', 'mixto');
		$this->db->where('TB_07_Factura.TB_02_Sucursal_Codigo', $sucursal);
		$this->db->where('TB_18_Tarjeta.TB_22_Banco_Banco_Codigo', $banco);
		$this->db->where('TB_07_Factura.Factura_Fecha_Hora >', $inicio);
		$this->db->where('TB_07_Factura.Factura_Fecha_Hora <', $final);
		$query = $this->db->get();*/
		$this->load->model("factura","",true);
		$queryLoco = "";
		
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->factura->getFacturasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$queryLoco = "AND TB_07_Factura.Factura_Consecutivo IN (".implode($facturas_trueque,',').")";
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->factura->getFacturasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$queryLoco = "AND TB_07_Factura.Factura_Consecutivo NOT IN (".implode($facturas_trueque,',').")";
				}
		}
		
		$query = $this->db->query("
			SELECT * FROM TB_07_Factura
			JOIN TB_18_Tarjeta ON TB_07_Factura.Factura_Consecutivo = TB_18_Tarjeta.TB_07_Factura_Factura_Consecutivo
			WHERE (TB_07_Factura.Factura_Tipo_Pago = 'tarjeta'
			OR TB_07_Factura.Factura_Tipo_Pago = 'mixto')
			AND TB_07_Factura.TB_02_Sucursal_Codigo = $sucursal
			AND TB_18_Tarjeta.TB_07_Factura_TB_02_Sucursal_Codigo = $sucursal
			AND TB_18_Tarjeta.TB_22_Banco_Banco_Codigo = $banco
			AND TB_07_Factura.Factura_Fecha_Hora > '$inicio'
			AND TB_07_Factura.Factura_Fecha_Hora < '$final'
			AND TB_07_Factura.TB_03_Cliente_Cliente_Cedula != 2
			$queryLoco
		");
		if($query->num_rows()==0)
		{			
			return false;
		}
		else
		{			
			return $query->result();
		}
	}
	
	function getRecibosPagadosConTarjetaRangoFecha($sucursal, $banco, $inicio, $final){
		/*
			SELECT tb_32_tarjeta_recibos.Comision_Por,
					tb_26_recibos_dinero.Recibo_Cantidad,
					tb_26_recibos_dinero.Recibo_Fecha
			FROM tb_32_tarjeta_recibos
			JOIN tb_26_recibos_dinero ON tb_26_recibos_dinero.Consecutivo = tb_32_tarjeta_recibos.Recibo
			JOIN tb_24_credito ON tb_24_credito.Credito_Id = tb_32_tarjeta_recibos.Credito
			WHERE tb_24_credito.Credito_Sucursal_Codigo = 0
			AND  tb_32_tarjeta_recibos.Banco = 2
		*/
		$sucursalVendedor = $sucursal;
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this->db->select('tb_32_tarjeta_recibos.Comision_Por, tb_26_recibos_dinero.Recibo_Cantidad, tb_26_recibos_dinero.Recibo_Fecha');
		$this->db->from('tb_32_tarjeta_recibos');
		$this->db->join('tb_26_recibos_dinero', 'tb_26_recibos_dinero.Consecutivo = tb_32_tarjeta_recibos.Recibo');
		$this->db->join('tb_24_credito', 'tb_24_credito.Credito_Id = tb_32_tarjeta_recibos.Credito');
		$this->db->where('tb_24_credito.Credito_Sucursal_Codigo', $sucursal);
		$this->db->where('tb_32_tarjeta_recibos.Banco', $banco);
		$this->db->where('tb_26_recibos_dinero.Tipo_Pago', 'tarjeta');
		$this->db->where('tb_26_recibos_dinero.Recibo_Fecha >', $inicio);
		$this->db->where('tb_26_recibos_dinero.Recibo_Fecha <', $final);
		$this->db->where('tb_24_credito.Credito_Vendedor_Sucursal', $sucursalVendedor);
		$query = $this->db->get();
		if($query->num_rows()==0)
		{			
			return false;
		}
		else
		{			
			return $query->result();
		}
	}
	
	function guardarTipoDeposito($documento, $recibo){
		$datos = array(
						'Numero_Documento' => $documento,
						'Recibo' => $recibo
						);
		$this->db->insert('tb_43_deposito_recibo', $datos);
	}
	
	function getPagosMixtosPorRangoFecha($sucursal, $inicio, $final){
		/*
			SELECT tb_07_factura.Factura_Monto_Total AS monto, tb_07_factura.Factura_Fecha_Hora AS fecha, tb_23_mixto.Mixto_Cantidad_Paga AS pago_tarjeta 
			FROM tb_07_factura
			JOIN tb_23_mixto ON tb_23_mixto.TB_18_Tarjeta_TB_07_Factura_Factura_Consecutivo = tb_07_factura.Factura_Consecutivo
			WHERE tb_07_factura.Factura_Tipo_Pago = 'mixto'
			AND tb_07_factura.TB_02_Sucursal_Codigo = 0
		*/
		$this->load->model("factura", "", true);
		
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->factura->getFacturasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("tb_07_factura.Factura_Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->factura->getFacturasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("tb_07_factura.Factura_Consecutivo", $facturas_trueque);
				}
		}
		$this->db->select('tb_07_factura.Factura_Monto_Total AS monto, tb_07_factura.Factura_Fecha_Hora AS fecha, tb_23_mixto.Mixto_Cantidad_Paga AS pago_tarjeta');
		$this->db->from('tb_07_factura');
		$this->db->join('tb_23_mixto', 'tb_23_mixto.TB_18_Tarjeta_TB_07_Factura_Factura_Consecutivo = tb_07_factura.Factura_Consecutivo');
		$this->db->where('tb_07_factura.Factura_Tipo_Pago', 'mixto');
		$this->db->where('tb_07_factura.TB_02_Sucursal_Codigo', $sucursal);
		$this->db->where('tb_07_factura.Factura_Fecha_Hora >', $inicio);
		$this->db->where('tb_07_factura.Factura_Fecha_Hora <', $final);
		$this->db->where('TB_07_Factura.TB_03_Cliente_Cliente_Cedula !=', 2);
		return $this->db->get();
	}
	
	function getRecibosPorRangoFecha($sucursal, $inicio, $final){
		/*
			SELECT * 
			FROM tb_26_recibos_dinero
			JOIN tb_24_credito ON tb_24_credito.Credito_Id = tb_26_recibos_dinero.Credito
			WHERE Credito_Sucursal_Codigo = 0;
		*/
		$sucursalVendedor = $sucursal;
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this->db->from('tb_26_recibos_dinero');
		$this->db->where('Credito_Sucursal_Codigo', $sucursal);
		$this->db->join('tb_24_credito', 'tb_24_credito.Credito_Id = tb_26_recibos_dinero.Credito');
		$this->db->where('tb_26_recibos_dinero.Recibo_Fecha >', $inicio);
		$this->db->where('tb_26_recibos_dinero.Recibo_Fecha <', $final);
		$this->db->where('tb_24_credito.Credito_Vendedor_Sucursal', $sucursalVendedor);
		//QUE NO SEAN RECIBOS ANULADOS
		$this->db->where('tb_26_recibos_dinero.Anulado', 0);
		$query = $this->db->get();
		
		if($query->num_rows()==0)
		{			
			return false;
		}
		else
		{			
			return $query->result();
		}
	}
	
	function getNotaCreditoPorRangoFecha($sucursal, $inicio, $final){
		
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->getNotasCreditoTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->getNotasCreditoTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("Consecutivo", $facturas_trueque);
				}
		}
		$this->db->from('TB_27_Notas_Credito');
		$this->db->where('Sucursal', $sucursal);
                $this->db->where('Es_Anulacion', "0");
		$this->db->where('Fecha_Creacion >', $inicio);
		$this->db->where('Fecha_Creacion <', $final);
		$query = $this->db->get();		
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{			
			return $query->result();
		}
	}
	
	function getInfoGeneralNotaCreditoPorRangoFecha($sucursal, $inicio, $final){
		
		$respaldoSucursal = $sucursal;
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->getNotasCreditoTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("tb_27_notas_credito.Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->getNotasCreditoTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("tb_27_notas_credito.Consecutivo", $facturas_trueque);
				}
		}
		$this->db->select('tb_27_notas_credito.Consecutivo, tb_27_notas_credito.Sucursal, tb_27_notas_credito.Por_IVA, tb_07_factura.Factura_Tipo_Pago as Tipo, tb_07_factura.TB_03_Cliente_Cliente_Cedula as Cliente');
		$this->db->from('TB_27_Notas_Credito');
		$this->db->join('tb_07_factura', 'tb_07_factura.Factura_Consecutivo = tb_27_notas_credito.Factura_Aplicar');
		$this->db->where('TB_27_Notas_Credito.Sucursal', $sucursal);
                $this->db->where('TB_27_Notas_Credito.Es_Anulacion', "0");
		$this->db->where('tb_07_factura.TB_02_Sucursal_Codigo', $sucursal);
		$this->db->where('TB_27_Notas_Credito.Fecha_Creacion >', $inicio);
		$this->db->where('TB_27_Notas_Credito.Fecha_Creacion <', $final);
		$query = $this->db->get();	
		// SELECT tb_27_notas_credito.Consecutivo, tb_27_notas_credito.Sucursal, tb_07_factura.Factura_Tipo_Pago as Factura 
		// FROM tb_27_notas_credito 
		// JOIN tb_07_factura on tb_07_factura.Factura_Consecutivo = tb_27_notas_credito.Factura_Aplicar 
		// WHERE tb_07_factura.TB_02_Sucursal_Codigo = tb_27_notas_credito.Sucursal
		$contado = 0;
		$tarjeta = 0;
		$cheque = 0;
		$deposito = 0;
		$mixto = 0;
		$credito = 0;
		$apartado = 0;
		$totalNotas = 0;
				
		if($query->num_rows()!=0){
			$this->load->model("contabilidad", "", true);
			$this->load->model('cliente','',TRUE);
			foreach($query->result() as $nota){
				$cliente = $this->cliente->getClientes_Cedula($nota->Cliente);
				if($notaCreditoBody = $this->contabilidad->getArticulosNotaCreditoParaImpresion($nota->Consecutivo, $respaldoSucursal)){
					
					$costo_total = 0;
					$iva = 0;
					$costo_sin_iva = 0;
					$retencion = 0;
					foreach($notaCreditoBody as $art){
/*
						$total = $total + ($art->precio * ($art->bueno + $art->defectuoso));
						$total_iva = $total_iva + (($art->precio * ($art->bueno + $art->defectuoso)) * ($nota->Por_IVA/100));
						$subtotal = $subtotal + (($art->precio * ($art->bueno + $art->defectuoso)) - (($art->precio * ($art->bueno + $art->defectuoso)) * ($nota->Por_IVA/100)));
					
*/
					
						
						$cantidadArt = $art->bueno + $art->defectuoso;
						//Calculamos el precio total de los articulos
						//$precio_total_articulo = (($art->precio)-(($art->precio)*(($art->descuento)/100)))*$cantidadArt;
						$precio_total_articulo = $art->precio*$cantidadArt;
						$precio_total_articulo_sin_descuento = $art->precio*$cantidadArt;
						$precio_articulo_final = $art->precio_final;
						$precio_articulo_final = $precio_articulo_final * $cantidadArt;
						
						//Calculamos los impuestos
						
						$isExento = $art->exento;
						
						if($isExento=='0'){
							$costo_sin_iva += $precio_total_articulo/(1+(floatval($nota->Por_IVA)/100));
							
							
							$iva_precio_total_cliente = $precio_total_articulo - ($precio_total_articulo/(1+(floatval($nota->Por_IVA)/100)));
							$iva_precio_total_cliente_sin_descuento = $precio_total_articulo_sin_descuento - ($precio_total_articulo_sin_descuento/(1+(floatval($nota->Por_IVA)/100))); 
							
							$precio_final_sin_iva = $precio_articulo_final/(1+(floatval($nota->Por_IVA)/100));
							$iva_precio_final = $precio_articulo_final - $precio_final_sin_iva;
							
							if(!$art->no_retencion){
									$retencion += ($iva_precio_final - $iva_precio_total_cliente_sin_descuento);
							}
						}
						else if($isExento=='1'){
							$costo_sin_iva += $precio_total_articulo;
							//$retencion = 0;
						}
						$costo_total += $precio_total_articulo;

					
					
					}
					
					
					if($cliente[0]->Aplica_Retencion == "1")
						$retencion = 0;
					
					
					
					$iva = $costo_total-$costo_sin_iva;
					$costo_total += $retencion;

					
					
					
					$totalNotas += $costo_total;
				
					switch($nota->Tipo){
						case 'contado':
							$contado += $costo_total;
						break;
						case 'tarjeta':
							$tarjeta += $costo_total;
						break;
						case 'cheque':
							$cheque += $costo_total;
						break;
						case 'deposito':
							$deposito += $costo_total;
						break;
						case 'mixto':
							$mixto += $costo_total;
						break;
						case 'credito':
							$credito += $costo_total;
						break;
						case 'apartado':
							$apartado += $costo_total;
						break;
					}
				}
			}
		}
		return array("contado"=>$contado, "tarjeta"=>$tarjeta, "cheque"=>$cheque, "deposito"=>$deposito, "mixto"=>$mixto, "credito"=>$credito, "apartado"=>$apartado, "total"=>$totalNotas);
	
		
	}
	
	function getFacturasContadoPorRangoFecha($sucursal, $inicio, $final){
		$this->load->model("factura", "", true);
		
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->factura->getFacturasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("tb_07_factura.Factura_Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->factura->getFacturasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("tb_07_factura.Factura_Consecutivo", $facturas_trueque);
				}
		}
		$this->db->from('tb_07_factura');
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->where('Factura_Fecha_Hora >', $inicio);
		$this->db->where('Factura_Fecha_Hora <', $final);
		$this->db->where('Factura_Estado','cobrada');
		$this->db->where('Factura_Tipo_Pago','contado');
		$this->db->where('TB_03_Cliente_Cliente_Cedula !=', 2);
		
		$query = $this->db->get();		
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{			
			return $query->result();
		}
	}
	 
	
	function getFacturasDepositoPorRangoFecha($sucursal, $inicio, $final){
		$this->load->model("factura", "", true);
		
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->factura->getFacturasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("tb_07_factura.Factura_Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->factura->getFacturasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("tb_07_factura.Factura_Consecutivo", $facturas_trueque);
				}
		}
		$this->db->from('tb_07_factura');
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->where('Factura_Fecha_Hora >', $inicio);
		$this->db->where('Factura_Fecha_Hora <', $final);
		$this->db->where('Factura_Estado','cobrada');
		$this->db->where('Factura_Tipo_Pago','deposito');
		$this->db->where('TB_03_Cliente_Cliente_Cedula !=', 2);
		$query = $this->db->get();		
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{			
			return $query->result();
		}
	}
	
	function getFacturasPorRangoFecha($sucursal, $inicio, $final){
		$this->load->model("factura", "", true);
		
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->factura->getFacturasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("tb_07_factura.Factura_Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->factura->getFacturasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("tb_07_factura.Factura_Consecutivo", $facturas_trueque);
				}
		}
		$this->db->from('tb_07_factura');
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->where('Factura_Fecha_Hora >', $inicio);
		$this->db->where('Factura_Fecha_Hora <', $final);
		$this->db->where('Factura_Estado','cobrada');
		$query = $this->db->get();		
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{			
			return $query->result();
		}
	}
	
	function getFacturasCreditoYApartadoPorRangoFecha($sucursal, $inicio, $final){
		$this->load->model("factura", "", true);
		
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->factura->getFacturasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("tb_07_factura.Factura_Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->factura->getFacturasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("tb_07_factura.Factura_Consecutivo", $facturas_trueque);
				}
		}
		$this->db->from('tb_07_factura');
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->where('Factura_Fecha_Hora >', $inicio);
		$this->db->where('Factura_Fecha_Hora <', $final);
		$this->db->where('Factura_Estado','cobrada');
		$this->db->where("(Factura_Tipo_Pago = 'credito' OR Factura_Tipo_Pago = 'apartado')",'',false);
		$this->db->where('TB_03_Cliente_Cliente_Cedula !=', 2);
		$query = $this->db->get();		
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{			
			return $query->result();
		}
	}
	
	function getAbonoFacturasApartadoPorRangoFecha($sucursal, $inicio, $final){
		/*
			SELECT SUM(Abono) 
			FROM tb_40_apartado 
			JOIN tb_24_credito ON tb_40_apartado.Credito = tb_24_credito.Credito_Id 
			JOIN tb_07_factura ON tb_07_factura.Factura_Consecutivo = tb_24_credito.Credito_Factura_Consecutivo 
			WHERE tb_24_credito.Credito_Sucursal_Codigo = 0 
			AND tb_07_factura.TB_02_Sucursal_Codigo = 0 
			AND tb_07_factura.Factura_Estado = 'cobrada'
			;
		*/	
		$this->load->model("factura", "", true);
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->factura->getFacturasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("tb_07_factura.Factura_Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->factura->getFacturasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("tb_07_factura.Factura_Consecutivo", $facturas_trueque);
				}
		}
		$this->db->select('SUM(Abono) AS abono');
		$this->db->from('tb_40_apartado');
		$this->db->join('tb_24_credito','tb_24_credito.Credito_Id = tb_40_apartado.Credito');
		$this->db->join('tb_07_factura','tb_07_factura.Factura_Consecutivo = tb_24_credito.Credito_Factura_Consecutivo');
		$this->db->where('tb_24_credito.Credito_Sucursal_Codigo', $sucursal);
		$this->db->where('tb_07_factura.TB_02_Sucursal_Codigo', $sucursal);
		$this->db->where('tb_07_factura.Factura_Estado','cobrada');
		$this->db->where('tb_07_factura.Factura_Fecha_Hora >', $inicio);
		$this->db->where('tb_07_factura.Factura_Fecha_Hora <', $final);		
		$query = $this->db->get();		
		if($query->num_rows()==0)
		{
			return 0;
		}
		else
		{			
			return $query->result()[0]->abono;
		}
	}
	
	function getNotaDebitoPorRangoFecha($sucursal, $inicio, $final){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->getNotasDebitoTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->getNotasDebitoTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("Consecutivo", $facturas_trueque);
				}
		}
		$this->db->from('tb_30_notas_debito');
		$this->db->where('Sucursal', $sucursal);
		$this->db->where('Fecha >', $inicio);
		$this->db->where('Fecha <', $final);
		$query = $this->db->get();		
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{			
			return $query->result();
		}
	}
	
	function getVendidoPorVendedor($vendedor, $sucursal, $inicio, $final){
		/*
			SELECT 	SUM(tb_07_factura.Factura_Monto_Total) AS total_vendido, 
					CONCAT(tb_01_usuario.Usuario_Nombre, ' ', tb_01_usuario.Usuario_Apellidos) as usuario
			FROM tb_07_factura
			JOIN tb_01_usuario ON tb_01_usuario.Usuario_Codigo = tb_07_factura.Factura_Vendedor_Codigo
     JOIN tb_03_cliente ON tb_03_cliente.Cliente_Cedula = tb_07_factura.TB_03_Cliente_Cliente_Cedula
			WHERE tb_07_factura.TB_02_Sucursal_Codigo = 0
			AND Factura_Estado = 'cobrada'
			AND Factura_Vendedor_Codigo = 1
            AND tb_03_cliente.Cliente_EsSucursal = 0;
		*/
		$this->load->model("factura", "", true);
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->factura->getFacturasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("tb_07_factura.Factura_Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->factura->getFacturasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("tb_07_factura.Factura_Consecutivo", $facturas_trueque);
				}
		}
		$this->db->select("SUM(tb_07_factura.Factura_Monto_Total) AS total_vendido, 
					CONCAT(tb_01_usuario.Usuario_Nombre, ' ', tb_01_usuario.Usuario_Apellidos) as usuario", false);
		$this->db->from('tb_07_factura');
		$this->db->join('tb_01_usuario', 'tb_01_usuario.Usuario_Codigo = tb_07_factura.Factura_Vendedor_Codigo');
		$this->db->join('tb_03_cliente', 'tb_03_cliente.Cliente_Cedula = tb_07_factura.TB_03_Cliente_Cliente_Cedula');
		$this->db->where('tb_07_factura.TB_02_Sucursal_Codigo', $sucursal);
		$this->db->where('tb_07_factura.Factura_Estado', 'cobrada');
		$this->db->where('tb_07_factura.Factura_Vendedor_Codigo', $vendedor);
		$this->db->where('tb_03_cliente.Cliente_EsSucursal', 0);
		$this->db->where('tb_07_factura.Factura_Fecha_Hora >', $inicio);
		$this->db->where('tb_07_factura.Factura_Fecha_Hora <', $final);	
		$this->db->where('TB_07_Factura.TB_03_Cliente_Cliente_Cedula !=', 2);
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result();
		}
	}
	
	function facturaTraspasoHaSidoAplicada($factura, $sucursalSalida, $sucursalEntrada){
		$this->db->from('TB_44_Traspaso_Inventario');
		$this->db->where('Sucursal_Salida', $sucursalSalida);
		$this->db->where('Sucursal_Entrada', $sucursalEntrada);
		$this->db->where('Factura_Traspasada', $factura);
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return true;
		}
	}
	
	function crearTraspasoArticulos($sucursalSalida, $sucursalEntrada, $usuario, $fecha, $factura){
		$datos = array(
					'Sucursal_Salida' => $sucursalSalida,
					'Sucursal_Entrada' => $sucursalEntrada,
					'Fecha' => $fecha,
					'Usuario' => $usuario,
					'Factura_Traspasada' => $factura
					);
		$this->db->insert('TB_44_Traspaso_Inventario', $datos);
		return $this->db->insert_id();
	}
	
	function agregarArticuloTraspaso($codigo, $descripcion, $cantidad, $traspaso){
		$datos = array(
						'Codigo' => $codigo,
						'Descripcion' => $descripcion,
						'Cantidad' => $cantidad,
						'Traspaso' => $traspaso
						);
		$this->db->insert('TB_45_Articulos_Traspaso_Inventario', $datos);				
	}
	
	function getTraspasoArticulos($id){
		$this->db->select("	TB_44_Traspaso_Inventario.Id as consecutivo, 
							date_format(TB_44_Traspaso_Inventario.Fecha, '%d-%m-%Y %h:%i:%s %p') as fecha,
							TB_44_Traspaso_Inventario.Sucursal_Salida as salida,
							TB_44_Traspaso_Inventario.Sucursal_Entrada as entrada,
							TB_44_Traspaso_Inventario.Factura_Traspasada as factura,
							TB_44_Traspaso_Inventario.Usuario as usuario,
							CONCAT(tb_01_usuario.Usuario_Nombre, ' ', tb_01_usuario.Usuario_Apellidos) as usuario_nombre", false);		
		$this->db->from('TB_44_Traspaso_Inventario');
		$this->db->join('tb_01_usuario', 'tb_01_usuario.Usuario_Codigo = TB_44_Traspaso_Inventario.Usuario');
		$this->db->where('TB_44_Traspaso_Inventario.Id', $id);
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result()[0];
		}
	}
	
	function getArticulosTraspaso($id){
		$this->db->from('TB_45_Articulos_Traspaso_Inventario');
		$this->db->where('Traspaso', $id);
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result();
		}
	}
	
	function getRecibosFiltrados($cliente, $desde, $hasta, $tipo, $estado, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es una sucursal con trueque
				$this->db->where("Credito_Vendedor_Sucursal", $sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		/*
			SELECT  tb_26_recibos_dinero.Consecutivo as consecutivo,
					date_format(tb_26_recibos_dinero.Recibo_Fecha, '%d-%m-%Y %h:%i:%s %p') as fecha,        
					tb_26_recibos_dinero.Recibo_Cantidad as monto,
					CONCAT(tb_03_cliente.Cliente_Nombre, ' ', tb_03_cliente.Cliente_Apellidos) as cliente
			FROM tb_26_recibos_dinero
			JOIN tb_24_credito ON tb_24_credito.Credito_Id = tb_26_recibos_dinero.Credito
			JOIN tb_03_cliente ON tb_03_cliente.Cliente_Cedula = tb_24_credito.Credito_Cliente_Cedula
			WHERE tb_24_credito.Credito_Sucursal_Codigo = 0
			ORDER BY tb_26_recibos_dinero.Consecutivo ASC;
		*/
		$this->db->select(" tb_26_recibos_dinero.Consecutivo as consecutivo,
							date_format(tb_26_recibos_dinero.Recibo_Fecha, '%d-%m-%Y %h:%i:%s %p') as fecha,        
							tb_26_recibos_dinero.Recibo_Cantidad as total,
							CONCAT(tb_03_cliente.Cliente_Nombre, ' ', tb_03_cliente.Cliente_Apellidos) as cliente
							", false);
		$this->db->from("tb_26_recibos_dinero");
		$this->db->join('tb_24_credito', 'tb_24_credito.Credito_Id = tb_26_recibos_dinero.Credito');
		$this->db->join('tb_03_cliente','tb_03_cliente.Cliente_Cedula = tb_24_credito.Credito_Cliente_Cedula');
		$this->db->where('tb_24_credito.Credito_Sucursal_Codigo', $sucursal);
		$this->setFiltradoCliente($cliente, 'tb_24_credito.Credito_Cliente_Cedula');
		$this->setFiltradoFechaDesde($desde, 'tb_26_recibos_dinero.Recibo_Fecha');
		$this->setFiltradoFechaHasta($hasta, 'tb_26_recibos_dinero.Recibo_Fecha');
		$this->setFiltradoTipo($tipo, 'tb_26_recibos_dinero.Tipo_Pago');
		$this->setFiltradoEstado($estado, 'tb_26_recibos_dinero.Anulado');
		$this->db->order_by('tb_26_recibos_dinero.Consecutivo', 'ASC');
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
	
	function getNotasCreditoFiltrados($cliente, $desde, $hasta, $sucursal){
		
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->getNotasCreditoTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->getNotasCreditoTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("Consecutivo", $facturas_trueque);
				}
		}
		$this->db->select("Consecutivo as consecutivo, Nombre_Cliente as cliente, date_format(Fecha_Creacion, '%d-%m-%Y %h:%i:%s %p') as fecha", false);
		$this->db->from("tb_27_notas_credito");
		$this->db->where("Sucursal", $sucursal);
		$this->setFiltradoCliente($cliente, "Cliente");
		$this->setFiltradoFechaDesde($desde, "Fecha_Creacion");
		$this->setFiltradoFechaHasta($hasta, "Fecha_Creacion");
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
	
	function getNotasCreditoTrueque($sucursal){
			$this->db->select("Consecutivo");
			$this->db->from("tb_46_relacion_trueque");
			$this->db->where("Documento", "nota_credito");
			$this->db->where("Sucursal", $sucursal);
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
	
	function getNotasCreditoTruequeResponde($sucursales){
			$this->db->select("Consecutivo");
			$this->db->from("tb_46_relacion_trueque");
			$this->db->where("Documento", "nota_credito");
			$this->db->where_in("Sucursal", $sucursales);
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
	
	function getNotasDebitoTrueque($sucursal){
			$this->db->select("Consecutivo");
			$this->db->from("tb_46_relacion_trueque");
			$this->db->where("Documento", "nota_debito");
			$this->db->where("Sucursal", $sucursal);
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
	
	function getNotasDebitoTruequeResponde($sucursales){
			$this->db->select("Consecutivo");
			$this->db->from("tb_46_relacion_trueque");
			$this->db->where("Documento", "nota_debito");
			$this->db->where_in("Sucursal", $sucursales);
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
	
	function getNotasDebitoFiltrados($desde, $hasta, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->getNotasDebitoTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->getNotasDebitoTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("Consecutivo", $facturas_trueque);
				}
		}
		$this->db->select("tb_30_notas_debito.Consecutivo as consecutivo, 
												date_format(tb_30_notas_debito.Fecha, '%d-%m-%Y %h:%i:%s %p') as fecha,
												CONCAT(tb_01_usuario.Usuario_Nombre, ' ', tb_01_usuario.Usuario_Apellidos) as cliente", false);
		$this->db->from("tb_30_notas_debito");
		$this->db->join("tb_01_usuario","tb_01_usuario.Usuario_Codigo = tb_30_notas_debito.Usuario");
		$this->db->where("tb_30_notas_debito.Sucursal", $sucursal);
		$this->setFiltradoFechaDesde($desde, "tb_30_notas_debito.Fecha");
		$this->setFiltradoFechaHasta($hasta, "tb_30_notas_debito.Fecha");
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
	
	function getRetirosParcialesFiltrados($desde, $hasta, $sucursal){
		$this->db->select("tb_33_retiros_parciales.Id as consecutivo, 
												date_format(tb_33_retiros_parciales.Fecha_Hora, '%d-%m-%Y %h:%i:%s %p') as fecha,
												CONCAT(tb_01_usuario.Usuario_Nombre, ' ', tb_01_usuario.Usuario_Apellidos) as cliente,
												tb_33_retiros_parciales.Monto as total", false);
		$this->db->from("tb_33_retiros_parciales");
		$this->db->join("tb_01_usuario","tb_01_usuario.Usuario_Codigo = tb_33_retiros_parciales.Usuario");
		$this->db->where("tb_33_retiros_parciales.Sucursal", $sucursal);
		$this->setFiltradoFechaDesde($desde, "tb_33_retiros_parciales.Fecha_Hora");
		$this->setFiltradoFechaHasta($hasta, "tb_33_retiros_parciales.Fecha_Hora");
		$this->db->order_by("tb_33_retiros_parciales.Id", "desc"); 
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
	
	function setFiltradoCliente($cliente, $campo){
		if(trim($cliente)!=''){
			$this->db->where($campo, $cliente);
		}
	}
	
	function setFiltradoFechaDesde($fecha, $campo){
		if(trim($fecha)!=''){
			$fecha = $this->convertirFecha($fecha, " 00:00:00");
			$this->db->where("$campo >=", $fecha);
		}
	}
	
	function setFiltradoFechaHasta($fecha, $campo){
		if(trim($fecha)!=''){
			$fecha = $this->convertirFecha($fecha, " 23:59:59");
			$this->db->where("$campo <=", $fecha);
		}
	}
	
	function setFiltradoTipo($tipos, $campo){
		if(sizeOf($tipos)>0){
			$this->db->where_in($campo, $tipos);
		}
	}
	
	function setFiltradoEstado($estados, $campo){
		if(sizeOf($estados)>0){
			$this->db->where_in($campo, $estados);
		}
	}
	
	function convertirFecha($fecha, $horas){		
		if(trim($fecha)!=''){
			$fecha = explode("/",$fecha);
			$fecha = $fecha[0]."-".$fecha[1]."-".$fecha[2].$horas;
			//echo $fecha;
			date_default_timezone_set("America/Costa_Rica");
			return date("Y-m-d : H:i:s", strtotime($fecha));
		}		
		return $fecha;
	}
	
	
	function crearCierreCaja($tipo_cambio, $conteo, $base, $fecha, $sucursal, $usuario, $bnServicios, $bnServiciosCredito, $bcrServicios, $bcrServiciosCredito){
		$datos = array(
										'Fecha'=>$fecha,
										'Base'=>$base,
										'Tipo_Cambio'=>$tipo_cambio,
										'Total_Conteo'=>$conteo,
										'Sucursal'=>$sucursal,
										'Usuario'=>$usuario,
										'BNServicios'=>$bnServicios,
										'BNServicios_Credito'=>$bnServiciosCredito,
										'BCRServicios'=>$bcrServicios,
										'BCRServicios_Credito'=>$bcrServiciosCredito
										);
		$this->db->insert('tb_37_cierre_caja', $datos);
		return $this->db->insert_id(); 
	}
	
	function agregarDenominacionCierreCaja($denominacion, $cantidad, $tipo, $moneda, $cierre){
		$datos = array(
						'Denominacion' => $denominacion,
						'Cantidad' => $cantidad,
						'Tipo' => $tipo,
						'Moneda' => $moneda,
						'Cierre_Caja' => $cierre
						);
		$this->db->insert('tb_38_moneda_cierre_caja', $datos);
	}
	
	function getCierreCaja($consecutivo, $sucursal){
			$this->db->select("
												tb_37_cierre_caja.Id as consecutivo,
												date_format(tb_37_cierre_caja.Fecha, '%d-%m-%Y %h:%i:%s %p') as fecha,
												tb_37_cierre_caja.Fecha as fechaCruda,
												tb_37_cierre_caja.Base as base,
												tb_37_cierre_caja.Tipo_Cambio as tipo,
												tb_37_cierre_caja.Total_Conteo as conteo,
												CONCAT(tb_01_usuario.Usuario_Nombre, ' ', tb_01_usuario.Usuario_Apellidos) as usuario,
												tb_37_cierre_caja.BNServicios as bnservicios,
												tb_37_cierre_caja.BNServicios_Credito as bnserviciosc,
												tb_37_cierre_caja.BCRServicios as bcrservicios,
												tb_37_cierre_caja.BCRServicios_Credito as bcrserviciosc
												", false);
			$this->db->from("tb_37_cierre_caja");
			$this->db->join("tb_01_usuario","tb_01_usuario.Usuario_Codigo = tb_37_cierre_caja.Usuario");
			$this->db->where("Id", $consecutivo);
			$this->db->where("Sucursal", $sucursal);
			$query = $this->db->get();
			if($query->num_rows()==0){
				return false;
			}else{
				return $query->result()[0];
			}
	}
	
	function getDenominacionesCierreCajaPorTipoYMoneda($cierre, $tipo, $moneda){
		$this->db->select('Denominacion as denominacion, Cantidad as cantidad');
		$this->db->from('tb_38_moneda_cierre_caja');
		$this->db->where('Tipo',$tipo);
		$this->db->where('Moneda',$moneda);
		$this->db->where('Cierre_Caja',$cierre);
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result();
		}
	}
	
	
	function getCierresFiltrados($desde, $hasta, $sucursal){
		$this->db->select("tb_37_cierre_caja.Id as consecutivo, 
												date_format(tb_37_cierre_caja.Fecha, '%d-%m-%Y %h:%i:%s %p') as fecha,
												CONCAT(tb_01_usuario.Usuario_Nombre, ' ', tb_01_usuario.Usuario_Apellidos) as cliente,
												tb_37_cierre_caja.Total_Conteo as total,
												tb_37_cierre_caja.Tipo_Cambio as tipo,
												tb_37_cierre_caja.Base as base", false);
		$this->db->from("tb_37_cierre_caja");
		$this->db->join("tb_01_usuario","tb_01_usuario.Usuario_Codigo = tb_37_cierre_caja.Usuario");
		$this->db->where("tb_37_cierre_caja.Sucursal", $sucursal);
		$this->setFiltradoFechaDesde($desde, "tb_37_cierre_caja.Fecha");
		$this->setFiltradoFechaHasta($hasta, "tb_37_cierre_caja.Fecha");
		$this->db->order_by("tb_37_cierre_caja.Id", "desc"); 
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
	
	function crearConsignacion($fecha_hora, $porcentaje_iva, $iva, $retencion, $costo, $total, $sucursal_recibe_exenta, $sucursal_recibe_no_retencion, $usuario, $sucursal_entrega, $sucursal_recibe, $sucursal_recibe_cliente){
			$datos = array(
										"Fecha_Hora" => $fecha_hora,
										"Porcentaje_IVA" => $porcentaje_iva,
										"IVA" => $iva,
										"Retencion" => $retencion,
										"Costo" => $costo,
										"Total" => $total,
                                                                                "Estado" => "creada",
										"Sucursal_Recibe_Exenta" => $sucursal_recibe_exenta,
										"Sucursal_Recibe_No_retencion" => $sucursal_recibe_no_retencion,
										"Usuario" => $usuario,
										"Sucursal_Entrega" => $sucursal_entrega,
										"Sucursal_Recibe" => $sucursal_recibe,
										"Sucursal_Recibe_Cliente_Liga" => $sucursal_recibe_cliente
								);	
			$this->db->insert("tb_49_consignacion", $datos);
			return $this->db->insert_id();
	}
        
        function anularConsignacion($consignacion){
            $datos = array(
                "Estado" => "anulada");
            $this->db->where("Id", $consignacion);
            $this->db->update("tb_49_consignacion", $datos);
        }
        
        function eliminarArticulosDeConsignacion($consignacion){
            $this->db->where("Consignacion", $consignacion);
            $this->db->delete("tb_50_articulos_consignacion");
        }
	
	function registrarArticuloConsignacion($codigo, $descripcion, $cantidad, $descuento, $precio_unidad, $precio_total, $exento, $retencion, $imagen, $consignacion, $precio_final){
			$datos = array(
										"Codigo"=> $codigo,
										"Descripcion" => $descripcion,
										"Cantidad" => $cantidad,
										"Descuento" => $descuento,
										"Precio_Unidad" => $precio_unidad,
										"Precio_Total" => $precio_total,
										"Precio_Final" => $precio_final,
										"Exento" => $exento,
										"Retencion" => $retencion,
										"Imagen" => $imagen,
										"Consignacion" => $consignacion
										);
			$this->db->insert("tb_50_articulos_consignacion", $datos);
	}
	
	function getConsignacionParaImpresion($codigo){
			$this->db->select("
				Id as consecutivo,
				Costo as costo,
				IVA as iva,
				Retencion as retencion,
				Total as total,
				date_format(Fecha_Hora, '%d-%m-%Y %h:%i:%s %p') AS fecha,
				Usuario as usuario,
				Sucursal_Entrega as sucursal_entrega,
				Sucursal_Recibe as sucursal_recibe,
				Sucursal_Recibe_Cliente_Liga as cliente
			", false);
			$this->db->from("tb_49_consignacion");
			$this->db->where("Id", $codigo);
			$query = $this->db->get();
			if($query->num_rows()==0){
				return false;
			}else{
				return $query->result()[0];
			}
	}
        
        function getConsignacion($codigo){
			$this->db->from("tb_49_consignacion");
			$this->db->where("Id", $codigo);
			$query = $this->db->get();
			if($query->num_rows()==0){
				return false;
			}else{
				return $query->result()[0];
			}
	}
	
	function getArticulosDeConsignacionParaImpresion($consignacion){
			$this->db->select("
				Codigo as codigo,
				Descripcion as descripcion,
				Cantidad as cantidad,
				Descuento as descuento,
				Precio_Unidad as precio,
				Precio_Total as precio_total,
				Exento as exento
			");
			$this->db->from("tb_50_articulos_consignacion");
			$this->db->where("Consignacion", $consignacion);
			$query = $this->db->get();
			if($query->num_rows()==0){
				return false;
			}else{
				return $query->result();
			}
	}
        
        function getArticulosDeConsignacion($consignacion){
			$this->db->from("tb_50_articulos_consignacion");
			$this->db->where("Consignacion", $consignacion);
			$query = $this->db->get();
			if($query->num_rows()==0){
				return false;
			}else{
				return $query->result();
			}
	}
        
        function getArticulosDeConsignacionParaEditar($consignacion, $sucursalEnvia){
                        $this->db->select("tb_06_articulo.Articulo_Codigo as codigo,
                                           tb_06_articulo.Articulo_Cantidad_Inventario as inventario,
                                           tb_50_articulos_consignacion.Descripcion as descripcion,
                                           tb_50_articulos_consignacion.Descuento as descuento,
                                           tb_50_articulos_consignacion.Exento as exento,
                                           tb_50_articulos_consignacion.Cantidad as cantidad,
                                           tb_50_articulos_consignacion.Imagen as imagen,
                                           tb_50_articulos_consignacion.Retencion as retencion,
                                           tb_50_articulos_consignacion.Precio_Final as pFinal,
                                           tb_50_articulos_consignacion.Precio_Total as pTotal,
                                           tb_50_articulos_consignacion.Precio_Unidad as pUnidad");
			$this->db->from("tb_50_articulos_consignacion");
                        $this -> db -> join('tb_06_articulo', 'tb_06_articulo.Articulo_Codigo = tb_50_articulos_consignacion.Codigo');
                        $this->db->where("tb_50_articulos_consignacion.Consignacion", $consignacion);
			$this->db->where("tb_06_articulo.TB_02_Sucursal_Codigo", $sucursalEnvia);
			$query = $this->db->get();
			if($query->num_rows()==0){
				return false;
			}else{
				return $query->result();
			}
	}
	
	function getArticuloEnListaConsignacion($codigo, $sucursalEntrega, $sucursalRecibe, $precio_unitario, $descuento, $exento, $retencion, $precio_final){
			$this->db->where("Codigo", $codigo);
			$this->db->where("Sucursal_Entrega", $sucursalEntrega);
			$this->db->where("Sucursal_Recibe", $sucursalRecibe);
			$this->db->where("Precio_Unidad", $precio_unitario);
			$this->db->where("Precio_Final", $precio_final);
			$this->db->where("Descuento", $descuento);
			$this->db->where("Exento", $exento);
			$this->db->where("Retencion", $retencion);
			$this->db->from("tb_51_lista_consignacion");
			$query = $this->db->get();
			if($query->num_rows() == 0){
					return false;
			}else{
					return $query->result()[0];
			}
	}
	
	function getArticuloEnListaConsignacionById($id){
			$this->db->where("Id", $id);
			$this->db->from("tb_51_lista_consignacion");
			$query = $this->db->get();
			if($query->num_rows() == 0){
					return false;
			}else{
					return $query->result()[0];
			}
	}
	
	function registrarArticuloEnListaConsignacion($codigo, $descripcion, $cantidad, $descuento, $precio_unidad, $precio_total, $exento, $retencion, $imagen, $sucursalEntrega, $sucursalRecibe, $precio_final){
			$datos = array(
										"Codigo"=> $codigo,
										"Descripcion" => $descripcion,
										"Cantidad" => $cantidad,
										"Descuento" => $descuento,
										"Precio_Unidad" => $precio_unidad,
										"Precio_Total" => $precio_total,
										"Precio_Final" => $precio_final,
										"Exento" => $exento,
										"Retencion" => $retencion,
										"Imagen" => $imagen,
										"Sucursal_Entrega" => $sucursalEntrega,
										"Sucursal_Recibe" => $sucursalRecibe
										);
			$this->db->insert("tb_51_lista_consignacion", $datos);
	}
	
	function actualizarArticuloEnListaConsignacion($codigo, $nuevaCantidad, $precio_unidad, $sucursalEntrega, $sucursalRecibe){
			$datos = array(
										"Cantidad" => $nuevaCantidad
										);
			$this->db->where("Codigo", $codigo);
			$this->db->where("Precio_Unidad", $precio_unidad);
			$this->db->where("Sucursal_Entrega", $sucursalEntrega);
			$this->db->where("Sucursal_Recibe", $sucursalRecibe);
			$this->db->update("tb_51_lista_consignacion", $datos);
	}
	
	function getArticulosEnListaDeConsignacion($sucursalEntrega, $sucursalRecibe){
		$this->db->from('tb_51_lista_consignacion');
		$this->db->where('Sucursal_Entrega', $sucursalEntrega);
		$this->db->where('Sucursal_Recibe', $sucursalRecibe);
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result();
		}
	}
	
	function eliminarArticuloDeListaConsignacionById($id){
		$this->db->where("Id", $id);
		$this->db->delete("tb_51_lista_consignacion");
	}
	
	function actualizarCantidadArticuloListaConsignacion($id, $nuevaCantidad){
		$this->db->where("Id", $id);
		$this->db->update("tb_51_lista_consignacion", array("Cantidad"=>$nuevaCantidad));
	}
        
        function getConsignacionesFiltradas($consigna, $recibe, $desde, $hasta, $tipo = "creada"){
            $this->db->select(" distinct(c2.Id) as consecutivo,
                                date_format(c2.Fecha_Hora, '%d-%m-%Y %h:%i:%s %p') as fecha,
                                (select s.Sucursal_Nombre from tb_02_sucursal s, tb_49_consignacion c where s.Codigo = c.Sucursal_Entrega and c.Id = c2.Id) as entrega,
                                (select s.Sucursal_Nombre from tb_02_sucursal s, tb_49_consignacion c where s.Codigo = c.Sucursal_Recibe and c.Id = c2.Id) as recibe", false);
            $this->db->from("tb_49_consignacion c2, tb_02_sucursal s");
            if($consigna != ""){
                $this->db->where('c2.Sucursal_Entrega', $consigna);
            }
            if($recibe != ""){
                $this->db->where('c2.Sucursal_Recibe', $recibe);
            }
            if($desde != ""){
                $this->setFiltradoFechaDesde($desde, "c2.Fecha_Hora");
            }
            if($hasta != ""){
                $this->setFiltradoFechaHasta($hasta, "c2.Fecha_Hora");
            }
            if($tipo != ""){
                $this->db->where("c2.Estado", $tipo);
            }
            $query = $this->db->get();
            if($query->num_rows()==0){
                    return false;
            }else{
                    return $query->result();
            }
        }
        
	function aplicarConsignacion($costo, $total, $consignacion){
            $datos = array(
                        "Costo" => $costo,
                        "Total" => $total,
                        "Estado" => "aplicada"
                );
            $this->db->where("Id", $consignacion);
            $this->db->update("tb_49_consignacion", $datos);
	}
        
        function guardarConsignacion($costo, $total, $consignacion){
            $datos = array(
                        "Costo" => $costo,
                        "Total" => $total
                );
            $this->db->where("Id", $consignacion);
            $this->db->update("tb_49_consignacion", $datos);
	}
        
        function crearNotaCreditoElectronica($sucursal, $cliente, $nota, $costos, $articulos, $codigo, $razon, $numero, $tipoDoc, $fechaEmisionDoc){
            $feedback["status"] = false;
            
            // No vamos a aceptar receptores de pasaporte para FE
            if($cliente->NoReceptor || $cliente->Cliente_Tipo_Cedula == "pasaporte"){
                $cliente = null;
            }
            
            $responseData = $this->guardarDatosBasicosNotaCreditoElectronica($sucursal, $cliente, $costos, $articulos, $nota, $codigo, $razon, $numero, $tipoDoc, $fechaEmisionDoc);
        
            if($resClave = $this->generarClaveYConsecutivoParaNotaCreditoElectronica($nota->Consecutivo, $nota->Sucursal)){
                if($resXML = $this->generarXMLNotaCredito($nota->Consecutivo, $nota->Sucursal)){
                    if($resXMLFirmado = $this->firmarXMLNotaCredito($nota->Consecutivo, $nota->Sucursal)){
                        $feedback["data"] = $responseData;
                        $feedback["data"]["clave"] = $resClave["Clave"];
                        $feedback["status"] = true;
                        unset($feedback['error']);
                        log_message('error', "Se genero bien el XML firmado | NC | Consecutivo: $nota->Consecutivo | Sucursal: $nota->Sucursal");
                    }else{
                        // ERROR AL FIRMAR EL XML DE FE
                        $feedback['error']='54';
                        $feedback["error_msg"] = 'Error al firma el XML';
                        log_message('error', "Error al firmar el XML | NC | Consecutivo: $nota->Consecutivo | Sucursal: $nota->Sucursal");
                    }
                }else{
                    // ERROR AL GENERAR EL XML DE FE
                    $feedback['error']='53';
                    $feedback["error_msg"] = 'Error al generar el XML';
                    log_message('error', "Error al generar el XML | NC | Consecutivo: $nota->Consecutivo | Sucursal: $nota->Sucursal");
                }
            }else{
                // ERROR AL GENERAR LA CLAVE
                $feedback["error"] = '52';
                $feedback["error_msg"] = 'Error al generar la clave';
                log_message('error', "Error al generar la clave | NC | Consecutivo: $nota->Consecutivo | Sucursal: $nota->Sucursal");
            }
            return $feedback;
        }
        
        function guardarDatosBasicosNotaCreditoElectronica($emisor, $receptor, $costos, $articulos, $nota, $codigo, $razon, $numero, $tipoDoc, $fechaEmisionDoc){
            // Eliminamos informacion antigua de la misma factura
            $this->db->where("Consecutivo", $nota->Consecutivo);
            $this->db->where("Sucursal", $nota->Sucursal);
            $this->db->delete("tb_58_articulos_nota_credito_electronica");
            
            $this->db->where("Consecutivo", $nota->Consecutivo);
            $this->db->where("Sucursal", $nota->Sucursal);
            $this->db->delete("tb_57_nota_credito_electronica");
            
            $tipoPago['tipo'] = $nota->Tipo_Pago;
            
            // Guardamos el encabezado de la factura
            require_once PATH_API_HACIENDA;
            $api = new API_FE();
            date_default_timezone_set("America/Costa_Rica");
            $fechaFacturaActual = now();
            $situacion = $api->internetIsOnline() ? "normal" : "sininternet";
            $fechaEmision = date(DATE_ATOM, $fechaFacturaActual);
            $condicionVenta = $this->getCondicionVenta($tipoPago);
            $plazoCredito = "0";
            $medioPago = $this->getMedioPago($tipoPago);
            $codigoMoneda = $nota->Moneda == "colones" ? "CRC" : "USD";
            $tipoCambio = $nota->Tipo_Cambio;
            $otros = "";
            
            // Agregamos la info nueva
            $data = array(
                "Consecutivo" => $nota->Consecutivo,
                "Sucursal" => $nota->Sucursal,
                "FechaEmision" => $fechaEmision,
                "EmisorNombre" => $emisor->Sucursal_Nombre,
                "EmisorTipoIdentificacion" => $emisor->Tipo_Cedula,
                "EmisorIdentificacion" => $emisor->Sucursal_Cedula,
                "EmisorNombreComercial" => $emisor->Sucursal_Nombre,
                "EmisorProvincia" => $emisor->Provincia,
                "EmisorCanton" => str_pad($emisor->Canton,2,"0", STR_PAD_LEFT),
                "EmisorDistrito" => str_pad($emisor->Distrito,2,"0", STR_PAD_LEFT),
                "EmisorBarrio" => str_pad($emisor->Barrio,2,"0", STR_PAD_LEFT),
                "EmisorOtrasSennas" => $emisor->Sucursal_Direccion,
                "EmisorCodigoPaisTelefono" => $emisor->Codigo_Pais_Telefono,
                "EmisorTelefono" => str_replace("-", "", $emisor->Sucursal_Telefono),
                "EmisorCodigoPaisFax" => $emisor->Codigo_Pais_Fax,
                "EmisorFax" => str_replace("-", "", $emisor->Sucursal_Fax),
                "EmisorEmail" => $emisor->Sucursal_Email,
                "CondicionVenta" => $condicionVenta,
                "PlazoCredito" => $plazoCredito,
                "MedioPago" => $medioPago,
                "CodigoMoneda" => $codigoMoneda,
                "TipoCambio" => $tipoCambio,
                "TotalServiciosGravados" => $this->fn($costos['total_serv_gravados']),
                "TotalServiciosExentos" => $this->fn($costos['total_serv_exentos']),
                "TotalMercanciaGravada" => $this->fn($costos['total_merc_gravada']),
                "TotalMercanciaExenta" => $this->fn($costos['total_merc_exenta']),
                "TotalGravados" => $this->fn($costos['total_gravados']),
                "TotalExentos" => $this->fn($costos['total_exentos']),
                "TotalVentas" => $this->fn($costos['total_ventas']),
                "TotalDescuentos" => $this->fn($costos['total_descuentos']),
                "TotalVentasNeta" => $this->fn($costos['total_ventas_neta']),
                "TotalImpuestos" => $this->fn($costos['total_impuestos']),
                "TotalComprobante" => $this->fn($costos['total_comprobante']),
                "Otros" => trim($otros) == "" ? "-" : trim($otros),
                "TipoDocumento" => NOTA_CREDITO_ELECTRONICA,
                "CodigoPais" => CODIGO_PAIS,
                "ConsecutivoFormateado" => $this->formatearConsecutivo($nota->Consecutivo),
                "Situacion" => $situacion,
                "CodigoSeguridad" => rand(10000000,99999999),
                "RespuestaHaciendaEstado" => "sin_enviar",
                "CorreoEnviadoReceptor" => 0,
                "DocumentoReferenciaNumero" => $numero,
                "DocumentoReferenciaTipo" => $tipoDoc,
                "DocumentoReferenciaFechaEmision" => $fechaEmisionDoc,
                "DocumentoReferenciaCodigo" => $codigo,
                "DocumentoReferenciaRazon" => $razon
            );
           
            if($receptor != NULL){
                $data["ReceptorNombre"] = $receptor->Cliente_Nombre." ".$receptor->Cliente_Apellidos;
                $data["ReceptorTipoIdentificacion"] = $this->getTipoIdentificacionCliente($receptor->Cliente_Tipo_Cedula);
                $data["ReceptorIdentificacion"] = $receptor->Cliente_Cedula;
                $data["ReceptorProvincia"] = $receptor->Provincia;
                $data["ReceptorCanton"] = str_pad($receptor->Canton,2,"0", STR_PAD_LEFT);
                $data["ReceptorDistrito"] = str_pad($receptor->Distrito,2,"0", STR_PAD_LEFT);
                $data["ReceptorBarrio"] = str_pad($receptor->Barrio,2,"0", STR_PAD_LEFT);
                $data["ReceptorCodigoPaisTelefono"] = $receptor->Codigo_Pais_Telefono;
                $data["ReceptorTelefono"] = str_replace("-", "", $receptor->Cliente_Telefono);
                $data["ReceptorCodigoPaisFax"] = $receptor->Codigo_Pais_Fax;
                $data["ReceptorFax"] = str_replace("-", "", $receptor->Numero_Fax);
                $data["ReceptorEmail"] = $receptor->Cliente_Correo_Electronico;
            }
            
            $this->db->insert("tb_57_nota_credito_electronica", $data);
            
            foreach ($articulos as $art){
                $data = array(
                    "Cantidad" => $art["cantidad"],
                    "UnidadMedida" => $art["unidadMedida"],
                    "Detalle" => $art["detalle"],
                    "PrecioUnitario" => $art["precioUnitario"],
                    "MontoTotal" => $art["montoTotal"],
                    "MontoDescuento" => $art["montoDescuento"],
                    "NaturalezaDescuento" => $art["naturalezaDescuento"],
                    "Subtotal" => $art["subtotal"],
                    "ImpuestoObject" => json_encode($art["impuesto"]),
                    "MontoTotalLinea" => $art["montoTotalLinea"],
                    "Consecutivo" => $nota->Consecutivo,
                    "Sucursal" => $nota->Sucursal
                );
                
                $this->db->insert("tb_58_articulos_nota_credito_electronica", $data);
            }
            
            return array("situacion" => $situacion, "fecha" => $fechaFacturaActual);
        }
        
        function generarClaveYConsecutivoParaNotaCreditoElectronica($consecutivo, $sucursal, $api = NULL){
            $this->db->select("EmisorTipoIdentificacion, EmisorIdentificacion, CodigoPais, ConsecutivoFormateado, Situacion, CodigoSeguridad, TipoDocumento");
            $this->db->from("tb_57_nota_credito_electronica");
            $this->db->where("Consecutivo", $consecutivo);
            $this->db->where("Sucursal", $sucursal);
            
            $query = $this->db->get();
            if($query->num_rows()>0){
                if($api == NULL){
                    require_once PATH_API_HACIENDA;
                    $api = new API_FE();
                }
                $row = $query->result()[0];
                $tipoIdentificacion = "fisico";
		switch($row->EmisorTipoIdentificacion){
			case "01":
				$tipoIdentificacion = "fisico";
			break;
			case "02":
				$tipoIdentificacion = "juridico";
			break;
			case "03":
				$tipoIdentificacion = "dimex";
			break;
			case "04":
				$tipoIdentificacion = "nite";
			break;
		}
                if($claveRs = $api->createClave($tipoIdentificacion, $row->EmisorIdentificacion, $row->CodigoPais, $row->ConsecutivoFormateado, $row->Situacion, $row->CodigoSeguridad, $row->TipoDocumento)){
                    $data = array(
                        "Clave" => $claveRs["clave"],
                        "ConsecutivoHacienda" => $claveRs["consecutivo"]
                    );
                    $this->db->where("Consecutivo", $consecutivo);
                    $this->db->where("Sucursal", $sucursal);
                    $this->db->update("tb_57_nota_credito_electronica", $data);
                    return $data;
                }
            }
            return false;
        }
        
        function generarXMLNotaCredito($consecutivo, $sucursal, $api = NULL){
            $this->db->from("tb_57_nota_credito_electronica");
            $this->db->where("Consecutivo", $consecutivo);
            $this->db->where("Sucursal", $sucursal);
            $query = $this->db->get();
            if($query->num_rows()>0){
                if($api == NULL){
                    require_once PATH_API_HACIENDA;
                    $api = new API_FE();
                }
                $nota = $query->result()[0];
                $this->db->from("tb_58_articulos_nota_credito_electronica");
                $this->db->where("Consecutivo", $consecutivo);
                $this->db->where("Sucursal", $sucursal);
                $query = $this->db->get();
                if($query->num_rows()>0){
                    $articulos = $query->result();
                    $xmlRes = $api->crearXMLNotaCredito($nota->Clave, 
                                                    $nota->ConsecutivoHacienda, 
                                                    $nota->FechaEmision, 

                                                    $nota->EmisorNombre, 
                                                    $nota->EmisorTipoIdentificacion, 
                                                    $nota->EmisorIdentificacion, 
                                                    $nota->EmisorNombreComercial, 
                                                    $nota->EmisorProvincia, 
                                                    $nota->EmisorCanton, 
                                                    $nota->EmisorDistrito, 
                                                    $nota->EmisorBarrio, 
                                                    $nota->EmisorOtrasSennas, 
                                                    $nota->EmisorCodigoPaisTelefono, 
                                                    $nota->EmisorTelefono, 
                                                    $nota->EmisorCodigoPaisFax, 
                                                    $nota->EmisorFax, 
                                                    $nota->EmisorEmail, 

                                                    $nota->ReceptorNombre, 
                                                    $nota->ReceptorTipoIdentificacion, 
                                                    $nota->ReceptorIdentificacion, 
                                                    $nota->ReceptorProvincia, 
                                                    $nota->ReceptorCanton, 
                                                    $nota->ReceptorDistrito, 
                                                    $nota->ReceptorBarrio, 
                                                    $nota->ReceptorCodigoPaisTelefono, 
                                                    $nota->ReceptorTelefono, 
                                                    $nota->ReceptorCodigoPaisFax, 
                                                    $nota->ReceptorFax, 
                                                    $nota->ReceptorEmail,

                                                    $nota->CondicionVenta, 
                                                    $nota->PlazoCredito, 
                                                    $nota->MedioPago, 
                                                    $nota->CodigoMoneda, 
                                                    $nota->TipoCambio, 

                                                    $nota->TotalServiciosGravados, 
                                                    $nota->TotalServiciosExentos, 
                                                    $nota->TotalMercanciaGravada, 
                                                    $nota->TotalMercanciaExenta, 
                                                    $nota->TotalGravados, 
                                                    $nota->TotalExentos, 
                                                    $nota->TotalVentas, 
                                                    $nota->TotalDescuentos, 
                                                    $nota->TotalVentasNeta, 
                                                    $nota->TotalImpuestos, 
                                                    $nota->TotalComprobante,

                                                    $nota->Otros, 
                                                    $this->prepararArticulosParaXML($articulos),
                                                    $nota->DocumentoReferenciaTipo, 
                                                    $nota->DocumentoReferenciaNumero, 
                                                    $nota->DocumentoReferenciaRazon, 
                                                    $nota->DocumentoReferenciaCodigo, 
                                                    $nota->DocumentoReferenciaFechaEmision);
                    if($xmlRes){
                        $data = array(
                            "XMLSinFirmar" => $xmlRes["xml"]
                        );
                        $this->db->where("Consecutivo", $consecutivo);
                        $this->db->where("Sucursal", $sucursal);
                        $this->db->update("tb_57_nota_credito_electronica", $data);
                        return $data;
                    }
                }
            }
            return false;
        }
        
        function firmarXMLNotaCredito($consecutivo, $sucursal, $api = NULL){
            $this->db->from("tb_57_nota_credito_electronica");
            $this->db->where("Consecutivo", $consecutivo);
            $this->db->where("Sucursal", $sucursal);
            $query = $this->db->get();
            if($query->num_rows()>0){
                if($api == NULL){
                    require_once PATH_API_HACIENDA;
                    $api = new API_FE();
                }
                $nota = $query->result()[0];
                $this->db->from("tb_02_sucursal");
                $this->db->where("Codigo", $sucursal);
                $query = $this->db->get();
                if($query->num_rows()>0){
                    $empresa = $query->result()[0];
                    if($xmlFirmado = $api->firmarDocumento($empresa->Token_Certificado_Tributa, $nota->XMLSinFirmar, $empresa->Pass_Certificado_Tributa, $nota->TipoDocumento)){
                        $data = array(
                            "XMLFirmado" => $xmlFirmado
                        );
                        $this->db->where("Consecutivo", $consecutivo);
                        $this->db->where("Sucursal", $sucursal);
                        $this->db->update("tb_57_nota_credito_electronica", $data);
                        
                        // Guardarmos el XML firmado en un archivo
                        file_put_contents(PATH_DOCUMENTOS_ELECTRONICOS.$nota->Clave.".xml",  base64_decode($xmlFirmado));
                        
                        return $data;
                    }
                }
            }
            return false;
        }
        
        function generarNotaCreditoElectronica($consecutivo, $sucursal, $codigo, $razon, $numero, $tipoDoc, $fechaEmision){
            $responseFetch = $this->getDatosParaNotaCreditoElectronica($consecutivo, $sucursal);
            if($responseFetch["status"]){
//                    $r["notaCreditoHead"] = $notaCreditoHead;
//                    $r["facturaElectronica"] = $facturaElectronicaHead;
//                    $r["costos"] = $costos;
//                    $r["articulos"] = $artFinales;
//                    $r["cliente"] = $cliente;
//                    $r["empresa"] = $sucursal;
                $responseCreacion = $this->crearNotaCreditoElectronica($responseFetch["empresa"], $responseFetch["cliente"], $responseFetch["notaCreditoHead"], $responseFetch["costos"], $responseFetch["articulos"], $codigo, $razon, $numero, $tipoDoc, $fechaEmision);

                if($responseCreacion["status"]){
                    if($responseCreacion["data"]["situacion"] == "normal"){
                        if($resEnvio = $this->enviarNotaCreditoElectronicaAHacienda($consecutivo, $sucursal)){
                            if($resEnvio["estado_hacienda"] == "rechazado"){
                                log_message('error', "Nota credito fue RECHAZADA por Hacienda. | Consecutivo: $consecutivo | Sucursal: $sucursal");
                                $responseFetch["status"] = false;
                                $responseFetch['error'] = 903;
                                $responseFetch["error_msg"] = "Nota credito fue RECHAZADA por Hacienda, favor marcarla para su revisión";
                            }else if($resEnvio["estado_hacienda"] == "aceptado"){
                                $responseFetch["message"] = "Nota credito fue ACEPTADA por Hacienda";
                                $responseFetch["status"] = true;
                                $responseFetch["clave"] = $responseCreacion["data"]["clave"];
                                log_message('error', "Nota credito fue ACEPTADA por Hacienda | Consecutivo: $consecutivo | Sucursal: $sucursal");
                            }else{
                                $responseFetch["status"] = false;
                                $responseFetch['error'] = 903;
                                $responseFetch["error_msg"] = "Nota credito se envió a Hacienda pero no fue rechazada, ni aceptada";
                                log_message('error', "Hacienda envio otro estado {$resEnvio["estado_hacienda"]} | Consecutivo: $consecutivo | Sucursal: $sucursal");
                            }
                        }else{
                            log_message('error', "No se pudo enviar la nota credito a Hacienda, debemos marcarla como contingencia | Consecutivo: $consecutivo | Sucursal: $sucursal");
                            // Realizar documento de contingencia, porque al enviar a Hacienda algo fallo
                            // Pasos a seguir
                            //    1) Cambiar estado a contingencia
                            //    2) Regenerar y actualizar clave
                            //    3) Regenerar y actualizar XML
                            //    5) Regenerar y actualizar XML Firmado
                            //$this->factura->regenerarFacturaElectronicaPorContingencia($responseCheck["factura"]->Factura_Consecutivo, $responseCheck["factura"]->TB_02_Sucursal_Codigo);

                            $responseFetch["status"] = false;
                            $responseFetch['error'] = 902;
                            $responseFetch["error_msg"] = "Nota credito no se pudo enviar a Hacienda por fallo no reconocido";
                        }
                    }else{
                        $responseFetch["status"] = false;
                        $responseFetch['error'] = 901;
                        $responseFetch["error_msg"] = "Nota credito no se pudo enviar a Hacienda por falta de internet";
                    }
                }else{
                    $responseFetch["status"] = false;
                    $responseFetch['error'] = $responseCreacion["error"];
                    $responseFetch["error_msg"] = $responseCreacion["error_msg"];
                }
            }
            return $responseFetch;
        }
        
        function getDatosParaNotaCreditoElectronica($consecutivo, $sucursal){
            // Ocupamos:
            // Cabeza de la factura electronica a la que se le aplica la NC
            // Cabeza de la nota credito --- 
            // Articulos de la nota credito ---
            // Empresa
            // Cliente
            $r["status"] = false;
            $r["error"] = 1;
            $r["error_msg"] = "No se logro procesar la nota credito electronica";
            
            if($notaCreditoHead = $this->getNotaCredito($consecutivo, $sucursal)){
                if($notaCreditoArticulos = $this->getArticulosNotaCredito($consecutivo, $sucursal)){
                    if($cliente = $this->cliente->getClientes_Cedula($notaCreditoHead->Cliente)){
                        $cliente = $cliente[0];
                        if($empresaData = $this->empresa->getEmpresa($notaCreditoHead->Sucursal)){
                            $empresaData = $empresaData[0];
                            $costos = array(
                                "total_serv_gravados" => 0,
                                "total_serv_exentos" => 0,
                                "total_merc_gravada" => 0,
                                "total_merc_exenta" => 0,
                                "total_gravados" => 0,
                                "total_exentos" => 0,
                                "total_ventas" => 0,
                                "total_descuentos" => 0,
                                "total_ventas_neta" => 0,
                                "total_impuestos" => 0,
                                "total_comprobante" => 0,
                            );
                            $artFinales = array();
                            foreach($notaCreditoArticulos as $a){
                                $linea = $this->getDetalleLineaNotaCredito($a);
                                array_push($artFinales, $linea);

                                if($a->Exento == 0){
                                    $costos["total_merc_gravada"] += $linea["montoTotal"];
                                    $costos["total_gravados"] += $linea["montoTotal"];
                                }else{
                                    $costos["total_merc_exenta"] += $linea["montoTotal"];
                                    $costos["total_exentos"] += $linea["montoTotal"];
                                }
                                $costos["total_ventas"] += $linea["montoTotal"];

                                if(isset($linea["montoDescuento"])){
                                    $costos["total_descuentos"] += $linea["montoDescuento"];
                                }

                                $impuesto = $linea["impuesto"][0]["monto"];
                                $costos["total_impuestos"] += $impuesto;
                            }
                            $costos["total_ventas_neta"] = $costos["total_ventas"] - $costos["total_descuentos"];
                            $costos["total_comprobante"] = $costos["total_ventas_neta"] + $costos["total_impuestos"];


                            unset($r["error"]);
                            unset($r["error_msg"]);
                            $r["status"] = true;
                            $r["notaCreditoHead"] = $notaCreditoHead;
                            $r["costos"] = $costos;
                            $r["articulos"] = $artFinales;
                            $r["cliente"] = $cliente;
                            $r["empresa"] = $empresaData;
                        }else{
                            $r["error"] = 6;
                            $r["error_msg"] = "No existe empresa";
                        }
                    }else{
                        $r["error"] = 5;
                        $r["error_msg"] = "No existe cliente";
                    }
                }else{
                    $r["error"] = 3;
                    $r["error_msg"] = "La nota credito no tiene articulos";
                }
            }else{
                $r["error"] = 2;
                $r["error_msg"] = "No existe la nota credito ingresada";
            }
            return $r;
        }
        
        function enviarNotaCreditoElectronicaAHacienda($consecutivo, $sucursal, $api = NULL){
            $this->db->from("tb_57_nota_credito_electronica");
            $this->db->where("Consecutivo", $consecutivo);
            $this->db->where("Sucursal", $sucursal);
            $query = $this->db->get();
            if($query->num_rows()>0){
                if($api == NULL){
                    require_once PATH_API_HACIENDA;
                    $api = new API_FE();
                }
                $nota = $query->result()[0];
                $this->db->from("tb_02_sucursal");
                $this->db->where("Codigo", $sucursal);
                $query = $this->db->get();
                if($query->num_rows()>0){
                    $empresa = $query->result()[0];
                    if($tokenData = $api->solicitarToken($empresa->Ambiente_Tributa, $empresa->Usuario_Tributa, $empresa->Pass_Tributa)){
                        if($resEnvio = $api->enviarDocumento($empresa->Ambiente_Tributa, $nota->Clave, $nota->FechaEmision, $nota->EmisorTipoIdentificacion, $nota->EmisorIdentificacion, $nota->ReceptorTipoIdentificacion, $nota->ReceptorIdentificacion, $tokenData["access_token"], $nota->XMLFirmado)){
                            $data = array(
                                "RespuestaHaciendaEstado" => "procesando",
                                "FechaRecibidoPorHacienda" => date("y/m/d : H:i:s")
                            );
                            $this->db->where("Consecutivo", $consecutivo);
                            $this->db->where("Sucursal", $sucursal);
                            $this->db->update("tb_57_nota_credito_electronica", $data);
                            
                            return $this->getEstadoNotaCreditoHacienda($api, $nota, $empresa, $tokenData, $consecutivo, $sucursal);
                        }else{
                            $data = array(
                                "RespuestaHaciendaEstado" => "fallo_envio"
                            );
                            $this->db->where("Consecutivo", $consecutivo);
                            $this->db->where("Sucursal", $sucursal);
                            $this->db->update("tb_57_nota_credito_electronica", $data);
                            log_message('error', "Error al enviar la nota de credito a Hacienda | Consecutivo: $consecutivo | Sucursal: $sucursal");
                        }
                    }else{
                        $data = array(
                            "RespuestaHaciendaEstado" => "fallo_token"
                        );
                        $this->db->where("Consecutivo", $consecutivo);
                        $this->db->where("Sucursal", $sucursal);
                        $this->db->update("tb_57_nota_credito_electronica", $data);
                        log_message('error', "Error al generar el token para envio de la nota credito | Consecutivo: $consecutivo | Sucursal: $sucursal");
                    }
                }else{
                    log_message('error', "No existe empresa para su envio | Consecutivo: $consecutivo | Sucursal: $sucursal");
                }
            }else{
                log_message('error', "No existe nota credito para su envio | Consecutivo: $consecutivo | Sucursal: $sucursal");
            }
            return false;
        }
        
        public function getNotaCreditoElectronica($consecutivo, $sucursal){
            $this->db->from("tb_57_nota_credito_electronica");
            $this->db->where("Consecutivo", $consecutivo);
            $this->db->where("Sucursal", $sucursal);
            $query = $this->db->get();
            if($query->num_rows()>0){
                return $query->result()[0];
            }else{
                return false;
            }
        }
        
        public function generarPDFNotaCredito($consecutivo, $sucursal){
            if($empresa = $this->empresa->getEmpresaImpresion($sucursal)){
                if($notaCreditoHead = $this->getNotaCreditoHeaderParaImpresion($consecutivo, $sucursal)){
                    if($notaCreditoBody = $this->getArticulosNotaCreditoParaImpresion($consecutivo, $sucursal)){
                        if($notaElectronica = $this->getNotaCreditoElectronica($consecutivo, $sucursal)){
                            $cliente = $this->cliente->getClientes_Cedula($notaCreditoHead[0]->cliente_cedula);



    /*
                            $total = 0;
                            $subtotal = 0;
                            $total_iva = 0;

                            foreach($notaCreditoBody as $art){
                                    $total = $total + ($art->precio * ($art->bueno + $art->defectuoso));
                                    $total_iva = $total_iva + (($art->precio * ($art->bueno + $art->defectuoso)) * ($notaCreditoHead[0]->iva/100));
                                    $subtotal = $subtotal + (($art->precio * ($art->bueno + $art->defectuoso)) - (($art->precio * ($art->bueno + $art->defectuoso)) * ($notaCreditoHead[0]->iva/100)));
                            }

                            $notaCreditoHead[0]->total = $total;
                            $notaCreditoHead[0]->subtotal = $subtotal;
                            $notaCreditoHead[0]->total_iva = $total_iva;
    */

                            $costo_total = 0;
                            $iva = 0;
                            $costo_sin_iva = 0;
                            $retencion = 0;
                            foreach($notaCreditoBody as $art){
    /*
                                    $total = $total + ($art->precio * ($art->bueno + $art->defectuoso));
                                    $total_iva = $total_iva + (($art->precio * ($art->bueno + $art->defectuoso)) * ($nota->Por_IVA/100));
                                    $subtotal = $subtotal + (($art->precio * ($art->bueno + $art->defectuoso)) - (($art->precio * ($art->bueno + $art->defectuoso)) * ($nota->Por_IVA/100)));

    */


                                    $cantidadArt = $art->bueno + $art->defectuoso;
                                    //Calculamos el precio total de los articulos
                                    //$precio_total_articulo = (($art->precio)-(($art->precio)*(($art->descuento)/100)))*$cantidadArt;
                                    $precio_total_articulo = $art->precio*$cantidadArt;
                                    $precio_total_articulo_sin_descuento = ($art->precio/(1-($art->descuento/100)))*$cantidadArt;
                                    $precio_articulo_final = $art->precio_final;
                                    $precio_articulo_final = $precio_articulo_final * $cantidadArt;

                                    //Calculamos los impuestos

                                    $isExento = $art->exento;

                                    if($isExento=='0'){
                                            $costo_sin_iva += $precio_total_articulo/(1+(floatval($notaCreditoHead[0]->iva)/100));


                                            $iva_precio_total_cliente = $precio_total_articulo - ($precio_total_articulo/(1+(floatval($notaCreditoHead[0]->iva)/100)));
                                            $iva_precio_total_cliente_sin_descuento = $precio_total_articulo_sin_descuento - ($precio_total_articulo_sin_descuento/(1+(floatval($notaCreditoHead[0]->iva)/100))); 

                                            $precio_final_sin_iva = $precio_articulo_final/(1+(floatval($notaCreditoHead[0]->iva)/100));
                                            $iva_precio_final = $precio_articulo_final - $precio_final_sin_iva;

                                            if(!$art->no_retencion){
                                                            $retencion += ($iva_precio_final - $iva_precio_total_cliente_sin_descuento);
                                            }
                                    }
                                    else if($isExento=='1'){
                                            $costo_sin_iva += $precio_total_articulo;
                                            //$retencion = 0;
                                    }
                                    $costo_total += $precio_total_articulo;



                            }


                            if($cliente[0]->Aplica_Retencion == "1")
                                    $retencion = 0;



                            $iva = $costo_total-$costo_sin_iva;
                            $costo_total += $retencion;


                            $notaCreditoHead[0]->total = $costo_total;
                            $notaCreditoHead[0]->subtotal = $costo_sin_iva;
                            $notaCreditoHead[0]->total_iva = $iva;
                            $notaCreditoHead[0]->retencion = $retencion;
                            $notaCreditoHead[0]->consecutivoH = $notaElectronica->ConsecutivoHacienda;
                            $notaCreditoHead[0]->clave = $notaElectronica->Clave;


                            $this->impresion_m->notaCreditoPDF($empresa[0], $notaCreditoHead[0], $notaCreditoBody, true);
                            
                        }
                    }else{
                            //$this->retorno['error'] = '12';
                    }
                }else{
                        //$this->retorno['error'] = '11';
                }					
            }else{
                    //$this->retorno['error'] = '7';
            }
        }
        
        
        function marcarEnvioCorreoNotaCreditoElectronica($sucursal, $consecutivo){
            $this->db->where("Consecutivo", $consecutivo);
            $this->db->where("Sucursal", $sucursal);
            $data = array(
                "CorreoEnviadoReceptor" => 1
            );
            $this->db->update("tb_57_nota_credito_electronica", $data);
        }
        
        function crearNotaCreditoMacro(&$retorno, $cedula, $facturaAcreditar, $facturaAplicar, $sucursal, $productosAAcreditar, $usuarioCodigo, $razon, $justificacion, $esAnulacion = false){
            if($clienteObject = $this->cliente->getClientes_Cedula($cedula)){
                $clienteObject = $clienteObject[0];
                //Verificamos que existan las facturas
                if(is_numeric($facturaAplicar)&&$this->factura->existe_Factura($facturaAplicar, $sucursal)
                        &&is_numeric($facturaAcreditar)&&$this->factura->existe_Factura($facturaAcreditar, $sucursal)){
                        if($this->existeProductosAcreditar($productosAAcreditar, $sucursal)){
                                //Preguntamos si la factura a aplicar ya fue aplicada en otra nota
                                $facturaAcreditarHeader = $this->factura->getFacturasHeaders($facturaAcreditar, $sucursal)[0];
                                if(!$this->facturaAplciarYaFueAplicada($facturaAplicar, $sucursal) ||
                                        $facturaAcreditarHeader->Factura_Tipo_Pago == 'credito'){
                                        //Listo para realizar nota
                                        //Obtenemos el consecutivo
                                        if($consecutivo = $this->getConsecutivo($sucursal)){
                                                date_default_timezone_set("America/Costa_Rica");
                                                $fecha = date("y/m/d : H:i:s", now());

                                                $tipoPago = 'contado'; //Por defetco guarda este
                                                $moneda = 'colones'; //Por defecto guarda este
                                                
                                                

                                                if($this->agregarNotaCreditoCabecera($consecutivo, $fecha, $clienteObject->Cliente_Nombre." ".$clienteObject->Cliente_Apellidos, $cedula, $sucursal, $facturaAcreditar, $facturaAplicar, $tipoPago, $moneda, $this->configuracion->getPorcentajeIVA(), $this->configuracion->getTipoCambioCompraDolar(), $esAnulacion)){
                                                        $this->agregarProductosNotaCredito($consecutivo, $sucursal, $productosAAcreditar, $cedula, $facturaAcreditar);

                                                        if($facturaAcreditarHeader->Factura_Tipo_Pago == 'credito'){
                                                            if($credito = $this->cliente->getCredito($facturaAcreditar, $sucursal, $cedula)){
                                                                $costoNC = $this->getCostoTotalNotaCredito($consecutivo, $sucursal);
                                                                
                                                                if($costoNC > 0){
                                                                    $nuevoSaldo = ($credito->Credito_Saldo_Actual - $costoNC) < 0 ? 0 : ($credito->Credito_Saldo_Actual - $costoNC);
                                                                    $this->actualizarCredito(array("Credito_Saldo_Actual" => $nuevoSaldo), $credito->Credito_Id);
                                                                    $this->user->guardar_transaccion($usuarioCodigo, "El usuario realizando la nota credito: $consecutivo abono $costoNC al credito {$credito->Credito_Id} queda con saldo $nuevoSaldo",$sucursal,'nota');
                                                                }
                                                            }   
                                                        }
                                                        
                                                        
                                                        
                                                        $this->user->guardar_transaccion($usuarioCodigo, "El usuario realizo la nota credito: $consecutivo",$sucursal,'nota');
                                                        $retorno['status'] = 'success';
                                                        $retorno['nota'] = $consecutivo;
                                                        unset($retorno['error']);
                                                        $retorno['sucursal']= $sucursal;
                                                        $retorno['servidor_impresion']= $this->configuracion->getServidorImpresion();
                                                        $retorno['token'] =  md5($usuarioCodigo.$sucursal."GAimpresionBO");


                                                        // Realizar nota credito electronica
                                                        $respuestaHacienda["type"] = "error";
                                                        if($notaCreditoHead = $this->getNotaCredito($consecutivo, $sucursal)){
                                                            if($facturaElectronicaHead = $this->factura->getFacturaElectronica($notaCreditoHead->Factura_Acreditar, $sucursal)){
                                                                $response = $this->generarNotaCreditoElectronica($consecutivo, $sucursal, $razon, $justificacion, $facturaElectronicaHead->Clave, FACTURA_ELECTRONICA_CODIGO, $facturaElectronicaHead->FechaEmision);
                                                                if($response["status"]){
                                                                    $respuestaHacienda["type"] = "success";
                                                                    $respuestaHacienda["msg"] = $response["message"];

                                                                    $this->generarPDFNotaCredito($consecutivo, $sucursal);

                                                                    if(!$response["cliente"]->NoReceptor){
                                                                        require_once PATH_API_CORREO;
                                                                        $apiCorreo = new Correo();
                                                                        $attachs = array(
                                                                            PATH_DOCUMENTOS_ELECTRONICOS.$response["clave"].".xml",
                                                                            PATH_DOCUMENTOS_ELECTRONICOS.$response["clave"].".pdf");
                                                                        if($apiCorreo->enviarCorreo($response["cliente"]->Cliente_Correo_Electronico, "Nota Crédito #".$consecutivo." | ".$response["empresa"]->Sucursal_Nombre, "Este mensaje se envió automáticamente a su correo al generar una nota crédito bajo su nombre.", "Nota Crédito Electrónica - ".$response["empresa"]->Sucursal_Nombre, $attachs)){
                                                                            $this->marcarEnvioCorreoNotaCreditoElectronica($sucursal, $consecutivo);
                                                                        }
                                                                    }
                                                                }else{
                                                                    $respuestaHacienda["msg"] = $response["error_msg"]." | ERROR #".$response["error"];
                                                                }
                                                            }else{
                                                                $respuestaHacienda["msg"] = "No existe factura electrónica para generar la nota crédito electrónica.";
                                                            }
                                                        }else{
                                                            $respuestaHacienda["msg"] = "No se pudo obtener la nota crédito para generar la nota crédito electrónica.";
                                                        }

                                                        $retorno['hacienda'] = $respuestaHacienda;
                                                }else{
                                                        //No se pudo crear la nota
                                                        $retorno['error'] = '9';
                                                }
                                        }else{
                                                //No se pudo obtener el nuevo consecutivo
                                                $retorno['error'] = '8';
                                        }
                                }else{
                                        //La factura a aplicar ya fue aplicada
                                        $retorno['error'] = '7';
                                }
                        }else{
                                //Algun producto ya no existe
                                $retorno['error'] = '6';
                        }
                }else{
                        //Alguna factura no es valida o no existe
                        $retorno['error'] = '5';
                }
            }else{
                    //Cliente no valido
                    $retorno['error'] = '4'; 
            }
        }
        
        private function existeProductosAcreditar($productos, $sucursal){
		foreach($productos as $producto){
			if(!$this->articulo->existe_Articulo($producto->c,$sucursal) && trim($producto->c) != "00"){return false;}
		}
		return true;
	}
        
        function obtenerComprobantesParaTabla($columnaOrden, $tipoOrden, $busqueda, $inicio, $cantidad, $sucursal, $tipoDocumento){
            $tabla = $tipoDocumento == "FE" ? "tb_55_factura_electronica" : "";
            $tabla = $tipoDocumento == "NC" ? "tb_57_nota_credito_electronica" : $tabla;
		return $this->db->query("
			SELECT 	Clave AS clave,
                                ConsecutivoHacienda AS consecutivo,
				ReceptorIdentificacion AS cliente_identificacion,
                                ReceptorNombre AS cliente_nombre,
                                CorreoEnviadoReceptor as correo_enviado,
                                FechaEmision as fecha,
                                RespuestaHaciendaEstado as estado
			FROM $tabla
			WHERE (Clave LIKE '%$busqueda%' OR
                                ConsecutivoHacienda LIKE '%$busqueda%' OR
                                ReceptorIdentificacion LIKE '%$busqueda%' OR
                                ReceptorNombre LIKE '%$busqueda%')
			AND    Sucursal = $sucursal
			ORDER BY $columnaOrden $tipoOrden
			LIMIT $inicio,$cantidad		
		");		
	}
        
        function obtenerComprobantesParaTablaFiltrados($columnaOrden, $tipoOrden, $busqueda, $inicio, $cantidad, $sucursal, $tipoDocumento){
            $tabla = $tipoDocumento == "FE" ? "tb_55_factura_electronica" : "";
            $tabla = $tipoDocumento == "NC" ? "tb_57_nota_credito_electronica" : $tabla;
		return $this->db->query("
			SELECT 	Clave AS clave,
                                ConsecutivoHacienda AS consecutivo,
				ReceptorIdentificacion AS cliente_identificacion,
                                ReceptorNombre AS cliente_nombre,
                                CorreoEnviadoReceptor as correo_enviado,
                                FechaEmision as fecha,
                                RespuestaHaciendaEstado as estado
			FROM $tabla
			WHERE (Clave LIKE '%$busqueda%' OR
                                ConsecutivoHacienda LIKE '%$busqueda%' OR
                                ReceptorIdentificacion LIKE '%$busqueda%' OR
                                ReceptorNombre LIKE '%$busqueda%')
			AND    Sucursal = $sucursal
		");		
	}
        
        function getTotalComprobantesEnSucursal($sucursal, $tipoDocumento){
            $tabla = $tipoDocumento == "FE" ? "tb_55_factura_electronica" : "";
            $tabla = $tipoDocumento == "NC" ? "tb_57_nota_credito_electronica" : $tabla;
		$this->db->from($tabla);
		$this->db->where('Sucursal', $sucursal);
		$query = $this -> db -> get();
		return $query -> num_rows();
	}
        
        function getNotaCreditoElectronicaByClave($clave){
            $this->db->from("tb_57_nota_credito_electronica");
            $this->db->where("Clave", $clave);
            $query = $this->db->get();

            if($query->num_rows() == 0){
                return false;
            }else{
                return $query->result()[0];
            }
        }
        
        function getCostoTotalNotaCredito($consecutivo, $sucursal){
            if($notaCreditoHead = $this->getNotaCreditoHeaderParaImpresion($consecutivo, $sucursal)){
		if($notaCreditoBody = $this->getArticulosNotaCreditoParaImpresion($consecutivo, $sucursal)){
                    $cliente = $this->cliente->getClientes_Cedula($notaCreditoHead[0]->cliente_cedula);
                    $costo_total = 0;
                    $iva = 0;
                    $costo_sin_iva = 0;
                    $retencion = 0;
                    foreach($notaCreditoBody as $art){


                            $cantidadArt = $art->bueno + $art->defectuoso;
                            //Calculamos el precio total de los articulos
                            //$precio_total_articulo = (($art->precio)-(($art->precio)*(($art->descuento)/100)))*$cantidadArt;
                            $precio_total_articulo = $art->precio*$cantidadArt;
                            $precio_total_articulo_sin_descuento = ($art->precio/(1-($art->descuento/100)))*$cantidadArt;
                            $precio_articulo_final = $art->precio_final;
                            $precio_articulo_final = $precio_articulo_final * $cantidadArt;

                            //Calculamos los impuestos

                            $isExento = $art->exento;

                            if($isExento=='0'){
                                    $costo_sin_iva += $precio_total_articulo/(1+(floatval($notaCreditoHead[0]->iva)/100));


                                    $iva_precio_total_cliente = $precio_total_articulo - ($precio_total_articulo/(1+(floatval($notaCreditoHead[0]->iva)/100)));
                                    $iva_precio_total_cliente_sin_descuento = $precio_total_articulo_sin_descuento - ($precio_total_articulo_sin_descuento/(1+(floatval($notaCreditoHead[0]->iva)/100))); 

                                    $precio_final_sin_iva = $precio_articulo_final/(1+(floatval($notaCreditoHead[0]->iva)/100));
                                    $iva_precio_final = $precio_articulo_final - $precio_final_sin_iva;

                                    if(!$art->no_retencion){
                                                    $retencion += ($iva_precio_final - $iva_precio_total_cliente_sin_descuento);
                                    }
                            }
                            else if($isExento=='1'){
                                    $costo_sin_iva += $precio_total_articulo;
                                    //$retencion = 0;
                            }
                            $costo_total += $precio_total_articulo;



                    }


                    if($cliente[0]->Aplica_Retencion == "1")
                            $retencion = 0;



                    $iva = $costo_total-$costo_sin_iva;
                    $costo_total += $retencion;


                    return $costo_total;
                }
            }
            return 0;
        }
        
        
        function getNotasCreditoRecibidasHacienda(){
            $this->db->where_in("RespuestaHaciendaEstado", array("recibido", "procesando"));
            $this->db->from("tb_57_nota_credito_electronica");
            $query = $this->db->get();
            if($query->num_rows() == 0){
                return false;
            }else{
                return $query->result();
            }
        }
        
        function getEstadoNotaCreditoHacienda($api, $nota, $empresa, $tokenData, $consecutivo, $sucursal){
             // Obtener resultado de la factura
            $resCheck = array();
            $counter = 0;
            do {
                sleep(2);
                $counter++;
                $resCheck = $api->revisarEstadoAceptacion($empresa->Ambiente_Tributa, $nota->Clave, $tokenData["access_token"]);
                log_message('error', "Revisando estado de nota credito en Hacienda | Consecutivo: $consecutivo | Sucursal: $sucursal");
            } while (trim(strtolower($resCheck["data"]["ind-estado"])) == "procesando" && $counter < 5);

            if($resCheck["status"]){
                $estado = trim(strtolower($resCheck["data"]["ind-estado"]));
                $xmlRespuesta = isset($resCheck["data"]["respuesta-xml"]) ? trim($resCheck["data"]["respuesta-xml"]) : "NO XML FROM HACIENDA";
                $data = array(
                    "RespuestaHaciendaEstado" => $estado,
                    "RespuestaHaciendaFecha" => date("y/m/d : H:i:s"),
                    "RespuestaHaciendaXML" => $xmlRespuesta
                );
                $this->db->where("Consecutivo", $consecutivo);
                $this->db->where("Sucursal", $sucursal);
                $this->db->update("tb_57_nota_credito_electronica", $data);
                log_message('error', "Se obtuvo el estado de hacienda <$estado> | Consecutivo: $consecutivo | Sucursal: $sucursal");
                return array("status" => true, "estado_hacienda" => $estado);
            }else{
                log_message('error', "Error al revisar el estado de la nota credito en Hacienda | Consecutivo: $consecutivo | Sucursal: $sucursal");
            }
        }
}


?>
