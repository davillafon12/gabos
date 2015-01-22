<?php
	Class configuracion extends CI_Model
	{
		//Este modelo maneja todos los parametros de configuracion del sistema, sera el remplazo del XML a futuro
		function getPorRetencionHaciendaTarjeta(){
			$this->db->from('tb_39_configuracion');
			$this->db->where('Parametro', 'porcentaje_retencion_tarjetas_hacienda');
			$this->db-> limit(1);
			$query = $this->db->get();		
			if($query->num_rows()==0)
			{
				return 0;
			}
			else
			{	
				$result = $query->result();
				return $result[0]->Valor;
			}
		}
	}
?>