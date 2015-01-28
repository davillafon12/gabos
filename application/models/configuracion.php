<?php
	class configuracion extends CI_Model
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
		
		function getEmpresaDefectoTraspasoCompras(){
			$this->db->from('tb_39_configuracion');
			$this->db->where('Parametro', 'codigo_empresa_traspaso_compras');
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
		
		function getTiempoSesion(){
			$this->db->from('tb_39_configuracion');
			$this->db->where('Parametro', 'tiempo_sesion');
			$this->db-> limit(1);
			$query = $this->db->get();		
			if($query->num_rows()==0)
			{
				return 600;
			}
			else
			{	
				$result = $query->result();
				return $result[0]->Valor;
			}
		}
		
		function getMontoMinimoCompra(){
			$this->db->from('tb_39_configuracion');
			$this->db->where('Parametro', 'monto_minimo_compra_cliente');
			$this->db-> limit(1);
			$query = $this->db->get();		
			if($query->num_rows()==0)
			{
				return 99999999999999; //Si no hay envia un numero alto
			}
			else
			{	
				$result = $query->result();
				return $result[0]->Valor;
			}
		}
		
		function getMontoIntermedioCompra(){
			$this->db->from('tb_39_configuracion');
			$this->db->where('Parametro', 'monto_intermedio_compra_cliente');
			$this->db-> limit(1);
			$query = $this->db->get();		
			if($query->num_rows()==0)
			{
				return 99999999999999; //Si no hay envia un numero alto
			}
			else
			{	
				$result = $query->result();
				return $result[0]->Valor;
			}
		}
		
		function getFechaUltimaActualizacion(){
			$this->db->from('tb_39_configuracion');
			$this->db->where('Parametro', 'ultima_actualizacion_estado_clientes');
			$this->db-> limit(1);
			$query = $this->db->get();		
			if($query->num_rows()==0)
			{
				return null; 
			}
			else
			{	
				$result = $query->result();
				return $result[0]->Valor;
			}
		}
		
		function getConfiguracionArray(){
			$this->db->from('tb_39_configuracion');
			$query = $this->db->get();		
			if($query->num_rows()==0)
			{
				return false;
			}
			else
			{	
				$result = $query->result();				
				foreach($result as $param){
					$config[$param->Parametro] = $param->Valor;
				}
				return $config;
			}
		}
				
				
		function actualizarCorreoAdmin($email){
			$this->db->where('Parametro', 'correo_administracion');
			$this->db->update('tb_39_configuracion', array('Valor'=>$email));
		}
		
		function actualizarDecimales($decimales){
			$this->db->where('Parametro', 'cantidad_decimales');
			$this->db->update('tb_39_configuracion', array('Valor'=>$decimales));
		}
		
		function actualizarCompraDolar($compra_dolar){
			$this->db->where('Parametro', 'dolar_compra');
			$this->db->update('tb_39_configuracion', array('Valor'=>$compra_dolar));
		}
		
		function actualizarVentaDolar($venta_dolar){
			$this->db->where('Parametro', 'dolar_venta');
			$this->db->update('tb_39_configuracion', array('Valor'=>$venta_dolar));
		}
		
		function actualizarCompraMinima($compra_minima){
			$this->db->where('Parametro', 'monto_minimo_compra_cliente');
			$this->db->update('tb_39_configuracion', array('Valor'=>$compra_minima));
		}
		
		function actualizarCompraIntermedia($compra_intermedia){
			$this->db->where('Parametro', 'monto_intermedio_compra_cliente');
			$this->db->update('tb_39_configuracion', array('Valor'=>$compra_intermedia));
		}
		
		function actualizarTiempoSesion($tiempo_sesion){
			$this->db->where('Parametro', 'tiempo_sesion');
			$this->db->update('tb_39_configuracion', array('Valor'=>$tiempo_sesion));
		}
		
		function actualizarPorcentajeIVA($por_iva){
			$this->db->where('Parametro', 'iva');
			$this->db->update('tb_39_configuracion', array('Valor'=>$por_iva));
		}
		
		function actualizarPorcentajeRetencion($por_retencion){
			$this->db->where('Parametro', 'porcentaje_retencion_tarjetas_hacienda');
			$this->db->update('tb_39_configuracion', array('Valor'=>$por_retencion));
		}
		
		function actualizarSucursalDefectoTraspasoCompras($sucursal_compras){
			$this->db->where('Parametro', 'codigo_empresa_traspaso_compras');
			$this->db->update('tb_39_configuracion', array('Valor'=>$sucursal_compras));
		}
		
		function actualizarUltimaActualizacionEstadoClientes($fecha){
			$this->db->where('Parametro', 'ultima_actualizacion_estado_clientes');
			$this->db->update('tb_39_configuracion', array('Valor'=>$fecha));
		}
		
	}
?>