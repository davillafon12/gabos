<?php
Class bodega_m extends CI_Model
{
	function existeArticuloEnBodega($codigo, $sucursal){
		$this -> db -> from('tb_09_bodega');
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
	
	function agregarArticulo($codigo, $descripcion, $costo, $cantidad, $fecha, $usuario, $sucursal){
		$datos = array(
						"Codigo" => $codigo,
						"Descripcion" => $descripcion,
						"Costo" => $costo,
						"Cantidad" => $cantidad,
						"Fecha_Ingreso" => $fecha,
						"Usuario" => $usuario,
						"Sucursal" => $sucursal
						);
		$this->db->insert("TB_09_Bodega", $datos);
	}
	
	
	
} //FIN DE LA CLASE


?>