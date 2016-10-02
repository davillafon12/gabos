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
	
	function agregarNotaCreditoCabecera($consecutivo, $fecha, $nombre, $cliente, $sucursal, $facturaAcreditar, $facturaAplicar, $tipoPago, $moneda, $por_iva, $tipo_cambio){
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
	
	function getArticulosNotaCreditoParaImpresion($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
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
		$this->db->select('tb_27_notas_credito.Consecutivo, tb_27_notas_credito.Sucursal, tb_27_notas_credito.Por_IVA, tb_07_factura.Factura_Tipo_Pago as Tipo');
		$this->db->from('TB_27_Notas_Credito');
		$this->db->join('tb_07_factura', 'tb_07_factura.Factura_Consecutivo = tb_27_notas_credito.Factura_Aplicar');
		$this->db->where('TB_27_Notas_Credito.Sucursal', $sucursal);
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
			foreach($query->result() as $nota){
			
				if($notaCreditoBody = $this->contabilidad->getArticulosNotaCreditoParaImpresion($nota->Consecutivo, $nota->Sucursal)){
					
					$costo_total = 0;
					$iva = 0;
					$costo_sin_iva = 0;
					$retencion = 0;
					foreach($notaCreditoBody as $art){
						//$total = $total + ($art->precio * ($art->bueno + $art->defectuoso));		
						$cantidadArt = $art->bueno + $art->defectuoso;
						//Calculamos el precio total de los articulos
						$precio_total_articulo = (($art->precio)-(($art->precio)*(($art->descuento)/100)))*$cantidadArt;
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
	
	
	function crearCierreCaja($tipo_cambio, $conteo, $base, $fecha, $sucursal, $usuario, $bnServicios, $bnServiciosCredito){
		$datos = array(
										'Fecha'=>$fecha,
										'Base'=>$base,
										'Tipo_Cambio'=>$tipo_cambio,
										'Total_Conteo'=>$conteo,
										'Sucursal'=>$sucursal,
										'Usuario'=>$usuario,
										'BNServicios'=>$bnServicios,
										'BNServicios_Credito'=>$bnServiciosCredito
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
												tb_37_cierre_caja.BNServicios_Credito as bnserviciosc
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
	
}


?>