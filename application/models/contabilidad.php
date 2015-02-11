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
	
	function getReciboParaImpresion($recibo, $sucursal){
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
					tb_26_recibos_dinero.Credito AS c
			FROM tb_26_recibos_dinero
			JOIN tb_24_credito ON tb_24_credito.Credito_Id = tb_26_recibos_dinero.Credito
			JOIN tb_07_factura ON tb_07_factura.Factura_Consecutivo = tb_24_credito.Credito_Factura_Consecutivo
			WHERE  tb_24_credito.Credito_Sucursal_Codigo = $sucursal
			AND    tb_26_recibos_dinero.Consecutivo = $recibo
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
	
	function agregarNotaCreditoCabecera($consecutivo, $fecha, $nombre, $cliente, $sucursal, $facturaAcreditar, $facturaAplicar, $tipoPago, $moneda, $por_iva, $tipo_cambio){
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
	
	function getNotaCreditoHeaderParaImpresion($consecutivo, $sucursal){
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
	
	function getArticulosNotaCreditoParaImpresion($consecutivo, $sucursal){
		$this->db->select("Codigo AS codigo, Descripcion AS descripcion, Cantidad_Bueno AS bueno, Cantidad_Defectuoso AS defectuoso, Precio_Unitario AS precio");
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
	
	function getHeadNotaDebito($consecutivo, $sucursal){
		$query = $this->db->query("
			SELECT 	Consecutivo AS nota, 
					date_format(Fecha, '%d-%m-%Y %h:%i:%s %p') AS fecha,
					Impuesto_Porcentaje AS iva,
					Observaciones AS observaciones        
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
		
		$query = $this->db->query("
			SELECT * FROM TB_07_Factura
			JOIN TB_18_Tarjeta ON TB_07_Factura.Factura_Consecutivo = TB_18_Tarjeta.TB_07_Factura_Factura_Consecutivo
			WHERE (TB_07_Factura.Factura_Tipo_Pago = 'tarjeta'
			OR TB_07_Factura.Factura_Tipo_Pago = 'mixto')
			AND TB_07_Factura.TB_02_Sucursal_Codigo = $sucursal
			AND TB_18_Tarjeta.TB_22_Banco_Banco_Codigo = $banco
			AND TB_07_Factura.Factura_Fecha_Hora > '$inicio'
			AND TB_07_Factura.Factura_Fecha_Hora < '$final'
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
		$this->db->select('tb_32_tarjeta_recibos.Comision_Por, tb_26_recibos_dinero.Recibo_Cantidad, tb_26_recibos_dinero.Recibo_Fecha');
		$this->db->from('tb_32_tarjeta_recibos');
		$this->db->join('tb_26_recibos_dinero', 'tb_26_recibos_dinero.Consecutivo = tb_32_tarjeta_recibos.Recibo');
		$this->db->join('tb_24_credito', 'tb_24_credito.Credito_Id = tb_32_tarjeta_recibos.Credito');
		$this->db->where('tb_24_credito.Credito_Sucursal_Codigo', $sucursal);
		$this->db->where('tb_32_tarjeta_recibos.Banco', $banco);
		$this->db->where('tb_26_recibos_dinero.Recibo_Fecha >', $inicio);
		$this->db->where('tb_26_recibos_dinero.Recibo_Fecha <', $final);
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
}


?>