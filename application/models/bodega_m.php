<?php
Class bodega_m extends CI_Model
{
	
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