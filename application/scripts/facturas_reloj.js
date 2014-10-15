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
		document.getElementById("segundos").innerHTML='0'+segundos;
	}
	else
	{document.getElementById("segundos").innerHTML=segundos;}
	
}

function setMinutos(){
	if(minutos<10){
		document.getElementById("minutos").innerHTML='0'+minutos;
	}
	else
	{document.getElementById("minutos").innerHTML=minutos;}
}

function setHora(){
	if(hora<10){
		document.getElementById("hora").innerHTML='0'+hora;
	}
	else
	{document.getElementById("hora").innerHTML=hora;}
}