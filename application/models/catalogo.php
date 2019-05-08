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
    
}

