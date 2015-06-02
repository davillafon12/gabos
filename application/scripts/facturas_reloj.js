$(document).ready(function() {
      document.onclick = function() { _idleSecondsCounter = 0;};
			document.onkeypress = function() {_idleSecondsCounter = 0;};
			window.setInterval(CheckIdleTime, 1000);
});

var hora = 0;
var minutos = 0;
var segundos = 0;

function reloj(){
	segundos++;
	if(segundos==60){
		segundos=0;
		minutos++;
		if(minutos==60){
			minutos=0;
			hora++;
		}
		setMinutos();
	}
	setSegundos();
}

function setSegundos(){
	if(segundos<10){
		$("#segundos").html('0'+segundos);
	}
	else
	{$("#segundos").html(segundos);}
	
}

function setMinutos(){
	if(minutos<10){
		$("#minutos").html('0'+minutos);
	}
	else
	{$("#minutos").html(minutos);}
}

function setHora(){
	if(hora<10){
		$("#hora").html('0'+hora);
	}
	else
	{$("#hora").html(hora);}
}


var _idleSecondsCounter = 0;

function CheckIdleTime() {
	reloj();
	_idleSecondsCounter++;
	if (_idleSecondsCounter >= IDLE_TIMEOUT) {
			LogOut();
	}
}

function LogOut(){
		window.onbeforeunload = null; //Eliminamos el evento de salida
		resetAll();
		document.location.href = location.protocol+'//'+document.domain+'/home/logout';
}	