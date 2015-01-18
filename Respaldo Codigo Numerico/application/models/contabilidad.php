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
		$this -> db -> where('TB_24_Credito.Credito_Vendedor_Sucursal', $sucursal);
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
	
	function agregarRecibo($sucursal, $codigoCredito, $saldo, $montoPagado, $tipoPago){
		date_default_timezone_set("America/Costa_Rica");
		$Current_datetime = date("y/m/d : H:i:s", now());
		$consecutivo = $this->getConsecutivoUltimoRecibo($sucursal)+1;
		$datos = array(
						'Consecutivo' => $consecutivo,
						'Recibo_Cantidad' => $montoPagado,
						'Recibo_Fecha' => $Current_datetime,
						'Recibo_Saldo' => $saldo,
						'Tipo_Pago' => $tipoPago,
						'Credito' => $codigoCredito
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
	
	function agregarNotaCreditoCabecera($consecutivo, $fecha, $nombre, $cliente, $sucursal, $facturaAcreditar, $facturaAplicar){
		$datos = array(
						'Consecutivo' => $consecutivo,
						'Nombre_Cliente' => $nombre,
						'Fecha_Creacion' => $fecha,
						'Factura_Acreditar' => $facturaAcreditar,
						'Factura_Aplicar' => $facturaAplicar,
						'Sucursal' => $sucursal,
						'Cliente' => $cliente
						);
		$this->db->insert('TB_27_Notas_Credito', $datos);
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
	
	function agregarProductosNotaCredito($consecutivo, $sucursal, $productos, $cliente){
		$datos = array();
		
		foreach($productos as $producto){
			//Agregamos los datos a un arrya para ser agregado a la bd
			$pro = array(
						'Codigo' => $producto->c,
						'Descripcion' => $this->articulo->getArticuloDescripcion($producto->c, $sucursal),
						'Cantidad_Bueno' => $producto->b,
						'Cantidad_Defectuoso' => $producto->d,
						'Precio_Unitario' => $this->articulo->getPrecioProducto($producto->c, $this->articulo->getNumeroPrecio($cliente), $sucursal),
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
	
	function crearNotaDebito($consecutivo, $fecha, $porcentaje_iva, $usuario, $sucursal){
		$datos = array(
						'Consecutivo' => $consecutivo,
						'Fecha' => $fecha,
						'Impuesto_Porcentaje' => $porcentaje_iva,
						'Usuario' => $usuario,
						'Sucursal' => $sucursal
						);
		$this->db->insert('tb_30_notas_debito', $datos);
	}
	
	function agregarArticuloNotaDebito($codigo, $descripcion, $cantidad, $costo, $notaConsecutivo, $sucursal, $usuario){
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
	
	function crearRetiroParcial($monto, $fecha, $usuario, $sucursal){
		$datos = array(
						'Monto' => $monto,
						'Fecha_Hora' => $fecha,
						'Sucursal' => $sucursal,
						'Usuario' => $usuario
						);
		$this->db->insert('TB_33_Retiros_Parciales', $datos);
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
	
	function getFacturasEntreRangoFechas($sucursal, $inicio, $final){
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
}


?>