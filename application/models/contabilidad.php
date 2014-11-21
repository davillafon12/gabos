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
	
	function agregarRecibo($factura, $cliente, $sucursal, $vendedor, $saldo, $montoPagado){
		date_default_timezone_set("America/Costa_Rica");
		$Current_datetime = date("y/m/d : H:i:s", now());
		
		$datos = array(
						'Recibo_Cantidad' => $montoPagado,
						'Recibo_Fecha' => $Current_datetime,
						'Recibo_Saldo' => $saldo,
						'Factura_Consecutivo' => $factura,
						'Sucursal_Codigo' => $sucursal,
						'Vendedor_Codigo' => $vendedor,
						'Vendedor_Sucursal' => $sucursal,
						'Cliente_Cedula' => $cliente
						);
		$this->db->insert('tb_26_recibos_dinero',$datos);
		return $this->db->insert_id();
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
}


?>