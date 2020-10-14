<?php 

Class catalogo extends CI_Model
{
    
    public function getTipoCodigoProductoServicio(){
        $this->db->from("catalogo_tipo_codigo_articulo");
        $query = $this -> db -> get();

        if($query -> num_rows() != 0)
        {
          return $query->result();
        }
        else
        {
          return array();
        }
    }
    
    public function getUnidadesDeMedida(){
        $this->db->from("catalogo_unidad_medida");
        $this->db->order_by("Descripcion", "asc"); 
        $query = $this -> db -> get();

        if($query -> num_rows() != 0)
        {
          return $query->result();
        }
        else
        {
          return array();
        }
    }
    
    public function getUnidadDeMedidaById($id){
        $this->db->from("catalogo_unidad_medida");
        $this->db->where("Id", $id);
        $query = $this -> db -> get();

        if($query -> num_rows() != 0)
        {
          return $query->result()[0];
        }
        else
        {
          return array();
        }
    }
    
    public function getTipoImpuestos(){
        $this->db->from("catalogo_tipo_impuesto");
        $query = $this -> db -> get();

        if($query -> num_rows() != 0)
        {
          return $query->result();
        }
        else
        {
          return array();
        }
    }
    
    public function getTipoTarifas(){
        $this->db->from("catalogo_tipo_tarifa");
        $query = $this -> db -> get();

        if($query -> num_rows() != 0)
        {
          return $query->result();
        }
        else
        {
          return array();
        }
    }
}

