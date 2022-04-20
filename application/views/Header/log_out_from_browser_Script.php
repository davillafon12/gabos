<?php

	$Ruta_Base = base_url('');
	echo 
	"<script type='text/javascript'>
	var IDLE_TIMEOUT = ";
	
		echo $this->configuracion->getTiempoSesion();
	
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
		_idleSecondsCounter++;
		/*var oPanel = document.getElementById('timeout_show');
		if (oPanel){oPanel.innerHTML = _idleSecondsCounter;}*/
		if (_idleSecondsCounter >= IDLE_TIMEOUT) {
			LogOut();
		}
	}

	function LogOut(){
		document.location.href = '".$Ruta_Base."home/logout';
	}	
	
	</script>" ;
	
?>