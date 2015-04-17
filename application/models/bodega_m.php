<?php
Class bodega_m extends CI_Model
{
	function existeArticuloEnBodega($codigo, $sucursal){
		$this -> db -> from('TB_34_Bodega');
		$this -> db -> where('Codigo', $codigo);
		$this -> db -> where('Sucursal', $sucursal);
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
	
	function getCantidadArticulo($codigo, $sucursal){
		$this -> db -> from('TB_34_Bodega');
		$this -> db -> where('Codigo', $codigo);
		$this -> db -> where('Sucursal', $sucursal);
		$this -> db -> limit(1);

		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
			$result = $query->result();
			foreach($result as $row){
				return $row->Cantidad;
			}
		}
		else
		{
			return 0;
		}
	}
	
	function restarCantidadBodega($cantidad, $codigo, $sucursal){
		$cantidad = $this->getCantidadArticulo($codigo, $sucursal) - $cantidad;
		$datos = array(
						"Cantidad" => $cantidad
						);
						
		$this->db->where("Sucursal", $sucursal);
		$this->db->where("Codigo", $codigo);
		$this->db->update("TB_34_Bodega", $datos);
	}
	
	function agregarArticulo($codigo, $descripcion, $costo, $cantidad, $usuario, $sucursal){
		$datos = array(
						"Codigo" => $codigo,
						"Descripcion" => $descripcion,
						"Costo" => $costo,
						"Cantidad" => $cantidad,
						"Usuario" => $usuario,
						"Sucursal" => $sucursal
						);
		$this->db->insert("TB_34_Bodega", $datos);
	}
	
	function actualizarArticulo($codigo, $descripcion, $costo, $cantidad, $sucursal){
		$cantidad = $cantidad + $this->getCantidadArticulo($codigo, $sucursal);
		
		$datos = array(
						"Descripcion" => $descripcion,
						"Costo" => $costo,
						"Cantidad" => $cantidad
						);
						
		$this->db->where("Sucursal", $sucursal);
		$this->db->where("Codigo", $codigo);
		$this->db->update("TB_34_Bodega", $datos);
	}
	
	function agregarCompra($codigo, $descripcion, $costo, $cantidad, $fecha, $usuario, $sucursal){
		$datos = array(
						"Codigo" => $codigo,
						"Descripcion" => $descripcion,
						"Costo" => $costo,
						"Cantidad" => $cantidad,
						"Fecha_Ingreso" => $fecha,
						"Usuario" => $usuario,
						"Sucursal" => $sucursal
						);
		$this->db->insert("TB_09_Compras", $datos);
	}
	
	function obtenerArticulosBodegaParaTabla($columnaOrden, $tipoOrden, $busqueda, $inicio, $cantidad, $sucursal){
		/*
			SELECT 	Codigo AS codigo,
					Descripcion AS descripcion,
					Cantidad AS inventario
			FROM tb_34_bodega
			WHERE (Codigo LIKE '%%' OR
				   Descripcion LIKE '%%' OR
				   Cantidad LIKE '%%' )
			AND    Sucursal = $sucursal
			ORDER BY Codigo DESC
			LIMIT 40,60
		*/
		return $this->db->query("
			SELECT 	Codigo AS codigo,
					Descripcion AS descripcion,
					Cantidad AS inventario
			FROM tb_34_bodega
			WHERE (Codigo LIKE '%%' OR
				   Descripcion LIKE '%%' OR
				   Cantidad LIKE '%%' )
			AND    Sucursal = $sucursal
			ORDER BY $columnaOrden $tipoOrden
			LIMIT $inicio,$cantidad		
		");		
	}
	
	function obtenerArticulosBodegaParaTablaFiltrados($columnaOrden, $tipoOrden, $busqueda, $inicio, $cantidad, $sucursal){
		/*
			SELECT 	Codigo AS codigo,
					Descripcion AS descripcion,
					Cantidad AS inventario
			FROM tb_34_bodega
			WHERE (Codigo LIKE '%%' OR
				   Descripcion LIKE '%%' OR
				   Cantidad LIKE '%%' )
			AND    Sucursal = $sucursal
		*/
		return $this->db->query("
			SELECT 	Codigo AS codigo,
					Descripcion AS descripcion,
					Cantidad AS inventario
			FROM tb_34_bodega
			WHERE (Codigo LIKE '%%' OR
				   Descripcion LIKE '%%' OR
				   Cantidad LIKE '%%' )
			AND    Sucursal = $sucursal	
		");	
	}
	
	function getTotalArticulosBodegaEnSucursal($sucursal){
		$this->db->from('tb_34_bodega');
		$this->db->where('Sucursal', $sucursal);
		$query = $this -> db -> get();
		return $query -> num_rows();
	}
	
	
	
} //FIN DE LA CLASE


?>