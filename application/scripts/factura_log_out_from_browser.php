<?php

	//Traemos informacion del XML
	
	libxml_use_internal_errors(true);
	$aux_array;
	$xml=simplexml_load_file("application/config/gabo-conf.xml");
	//$ruta_base_imagenes_script = base_url('application/images/scripts');
	if ($xml === false)
	{
		//Primero abrimos el xml de respaldo
		$xml_backup = simplexml_load_file("application/config/gabo-conf-backup.xml");
		//$aux_array['message'] = "<div class='status_2'><img src=".$ruta_base_imagenes_script."/warning.png /><p class='text_status'>¡Esta en archivo de respaldo!</div></p>";
		if ($xml_backup === false)
		{
			//$aux_array['message'] = "<div class='status_2'><img src=".$ruta_base_imagenes_script."/error.gif /><p class='text_status'>¡Esta en informacion estatica!</div></p>";
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
			</configuracion>";
			//$aux_array['flag']= "1";
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
	
	




    $Ruta_Base = base_url('');
	echo 
	"<script type='text/javascript'>
	var IDLE_TIMEOUT = ";
	if($aux_array['tiempo_sesion']!='0')
	{
		echo $aux_array['tiempo_sesion'];
	}
	echo "; //10 minutos
	var _idleSecondsCounter = 0;
	document.onclick = function() {
		_idleSecondsCounter = 0;
	};
	/*document.onmousemove = function() {
		_idleSecondsCounter = 0;
	};*/
	document.onkeypress = function() {
		_idleSecondsCounter = 0;
	};
	window.setInterval(CheckIdleTime, 1000);

	function CheckIdleTime() {
		reloj();
		_idleSecondsCounter++;
		/*var oPanel = document.getElementById('timeout_show');
		if (oPanel){oPanel.innerHTML = _idleSecondsCounter;}*/
		if (_idleSecondsCounter >= IDLE_TIMEOUT) {
			LogOut();
		}
	}

	function LogOut(){
		window.onbeforeunload = null; //Eliminamos el evento de salida
		resetAll();
		document.location.href = '".$Ruta_Base."home/logout';
	}	
	
	</script>" ;
	
?>