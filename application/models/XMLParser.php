<?php 
Class XMLParser extends CI_Model
{	
	function getConfigArray()
	{
	    libxml_use_internal_errors(true);
		$aux_array;
		$xml=simplexml_load_file("application/config/gabo-conf.xml");
		$ruta_base_imagenes_script = base_url('application/images/scripts');
		if ($xml === false)
		{
			//Primero abrimos el xml de respaldo
			$xml_backup = simplexml_load_file("application/config/gabo-conf-backup.xml");
			$aux_array['message'] = "<div class='status_2'><img src=".$ruta_base_imagenes_script."/warning.png /><p class='text_status'>¡Esta en archivo de respaldo!</div></p>";
			if ($xml_backup === false)
		    {
				$aux_array['message'] = "<div class='status_2'><img src=".$ruta_base_imagenes_script."/error.gif /><p class='text_status'>¡Esta en informacion estatica!</div></p>";
				//Ultimo caso cargamos configuracion alambrada
				$configuracionXML = "<?xml version='1.0' encoding='UTF-8'?>
				<configuracion>
					<correo_administracion>davillafon12@gmail.com</correo_administracion>
					<dolar_venta>550</dolar_venta>
					<dolar_compra>547</dolar_compra>
					<cantidad_decimales>2</cantidad_decimales>
					<monto_minimo_compra>5000</monto_minimo_compra>
					<monto_minimo_venta>12000</monto_minimo_venta>
					<tiempo_sesion>600</tiempo_sesion>
					<iva>13</iva>
				</configuracion>";
				$aux_array['flag']= "1";
				$xml_alam = simplexml_load_string($configuracionXML);
				foreach($xml_alam->children() as $child)
				{
					$aux_array[$child->getName()] = (string) $child;
				}
			}
			else
			{
				foreach($xml_backup->children() as $child)
				{
					$aux_array[$child->getName()] = (string) $child;
				}
			}
		}
		else
		{		
			foreach($xml->children() as $child)
			{
				$aux_array[$child->getName()] = (string) $child;
			}
		}
        return $aux_array;
	}
	
	function getXMLPath()
	{
		//Nos aseguramos de tener la ruta del archivo que esta bien
		libxml_use_internal_errors(true);
		$ruta = "application/config/gabo-conf.xml";
		$xml=simplexml_load_file($ruta);
		if ($xml === false)
		{
			$ruta = "application/config/gabo-conf-backup.xml";
			//Primero abrimos el xml de respaldo
			$xml_backup = simplexml_load_file($ruta);
			if ($xml_backup === false)
		    {
				return false;
			}
			else
			{
				return $ruta;
			}
		}
		else
		{
			return $ruta;
		}		
	}
	
	function saveXML($venta_min, $compra_min, $venta_dolar, $compra_dolar, $cant_dec, $email, $cant_ses, $cant_iva)
	{
		//Obtenemos ruta
		$ruta_XML = $this->getXMLPath();
		if($ruta_XML)
		{
			$config = simplexml_load_file($ruta_XML);
			
			if($email!=''){$config->correo_administracion = $email;}
			if($venta_dolar!=''){$config->dolar_venta = $venta_dolar;}
			if($compra_dolar!=''){$config->dolar_compra = $compra_dolar;}
			if($cant_dec!=''){$config->cantidad_decimales = $cant_dec;}
			if($compra_min!=''){$config->monto_minimo_compra = $compra_min;}
			if($venta_min!=''){$config->monto_minimo_venta = $venta_min;}
			if($cant_ses!=''){$config->tiempo_sesion = $cant_ses;}
			if($cant_iva!=''){$config->iva = $cant_iva;}
            
			$config->asXML($ruta_XML);
			
			return true;
		}
		else
		{return false;}
	}
	
	
}//FIN DE LA CLASE
?>