<?php 
Class ubicacion extends CI_Model{
    
    public function getProvincias(){
        $this -> db -> select('DISTINCT (ProvinciaNombre), ProvinciaID', false); 
        $this -> db -> from('tb_54_ubicaciones');
        $query = $this -> db -> get();
        if($query -> num_rows() != 0){
          return $query->result();
        }else{
          return array();
        }
    }
    
    public function getCantones($provincia){
        $this -> db -> select('DISTINCT (CantonNombre), CantonID', false); 
        $this -> db -> from('tb_54_ubicaciones');
        $this -> db -> where('ProvinciaID', $provincia);
        $query = $this -> db -> get();
        if($query -> num_rows() != 0){
          return $query->result();
        }else{
          return array();
        }
    }
    
    public function getDistritos($provincia, $canton){
        $this -> db -> select('DISTINCT (DistritoNombre), DistritoID', false); 
        $this -> db -> from('tb_54_ubicaciones');
        $this -> db -> where('ProvinciaID', $provincia);
        $this -> db -> where('CantonID', $canton);
        $query = $this -> db -> get();
        if($query -> num_rows() != 0){
          return $query->result();
        }else{
          return array();
        }
    }
    
    public function getBarrios($provincia, $canton, $distrito){
        $this -> db -> select('DISTINCT (BarrioNombre), BarrioID', false); 
        $this -> db -> from('tb_54_ubicaciones');
        $this -> db -> where('ProvinciaID', $provincia);
        $this -> db -> where('CantonID', $canton);
        $this -> db -> where('DistritoID', $distrito);
        $query = $this -> db -> get();
        if($query -> num_rows() != 0){
          return $query->result();
        }else{
          return array();
        }
    }

    function getUbicacion($provinciaId, $cantonId, $distritoId, $barrioId){
      $this -> db -> select('*');
      $this -> db -> from('tb_54_ubicaciones');
      $this -> db -> where('ProvinciaID', $provinciaId);
      $this -> db -> where('CantonID', $cantonId);
      $this -> db -> where('DistritoID', $distritoId);
      $this -> db -> where('BarrioID', $barrioId);
      $this -> db -> limit(1);

      $query = $this -> db -> get();

      if($query -> num_rows() != 0)
      {
        return $query->result()[0];
      }
      else
      {
        return false;
      }
    }
    
}