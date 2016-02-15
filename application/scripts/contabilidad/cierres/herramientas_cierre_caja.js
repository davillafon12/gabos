var signo = '₡'; //Guarda el signo de la moneda para uso general
var denMonedas = [500, 100, 50, 25, 10, 5]; //Guarda las denominacions de monedas
var denBilletes = [50000, 20000, 10000, 5000, 2000, 1000]; //Guarda las denominacions de monedas
var denDolares = [50, 20, 10, 1]; //Guarda las denominacions de dolares

$(function(){
	//Cuando se cargue la pag ejecuta este codigo
	$("#cant_50000").numeric();
	$("#cant_20000").numeric();
	$("#cant_10000").numeric();
	$("#cant_5000").numeric();
	$("#cant_2000").numeric();
	$("#cant_1000").numeric();
	$("#cant_500").numeric();
	$("#cant_100").numeric();
	$("#cant_50").numeric();
	$("#cant_25").numeric();
	$("#cant_10").numeric();
	$("#cant_5").numeric();
	
	//Dolares
	$("#cant_do_50").numeric();
	$("#cant_do_20").numeric();
	$("#cant_do_10").numeric();
	$("#cant_do_1").numeric();
	
	//Tipo cambio
	$("#tipo_cambio_dolar").numeric();
	
	//Base caja 
	$("#base_caja").numeric();
	
	//Actualizar monto efectivo
	actualizarMontoTotalRetiro();
});

function replaceAll(find, replace, str) {
	return str.replace(new RegExp(find, 'g'), replace);
}

/**
 * Number.prototype.format(n, x, s, c)
 * 
 * param integer n: length of decimal
 * param integer x: length of whole part
 * param mixed   s: sections delimiter
 * param mixed   c: decimal delimiter
	12345678.9.format(2, 3, '.', ',');  // "12.345.678,90"
	123456.789.format(4, 4, ' ', ':');  // "12 3456:7890"
	12345678.9.format(0, 3, '-');       // "12-345-679"
 */
 
Number.prototype.format = function(n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};

String.prototype.aFlotante = function(){
	//Obtenemos la cadena
	cadena = this.toString();
	//Eliminamos el signo, variable externa CUIDADO!!!!
	cadena = cadena.replace(signo, '');
	//Quitamos los puntos
	cadena = replaceAll('\\.', '', cadena);
	//Reemplazamos la coma por un punto
	cadena = cadena.replace(',','.');
	
	return parseFloat(cadena);
};

function actualizarCantidad(cantidad, denominacion){
	signo = '₡';
	//Filtramos negativos
	cantidad = replaceAll('-', '', cantidad);
	$("#cant_"+denominacion).val(cantidad);
	
	if(cantidad.trim()===''){
		$("#total_"+denominacion).html(signo+"0,00");
		return false;
	}
	
	denominacion = parseInt(denominacion);
	cantidad = parseInt(cantidad);
	cantidad = cantidad * denominacion;
	cantidad = parseFloat(cantidad);
	cantidad = cantidad.format(2, 3, '.', ',');
	$("#total_"+denominacion).html(signo+cantidad);
	actualizarTotalesDenominaciones(cantidad, denominacion);
}

function actualizarCantidadDolar(cantidad, denominacion){
	signo = '$'
	//Filtramos negativos
	cantidad = replaceAll('-', '', cantidad);
	$("#cant_do_"+denominacion).val(cantidad);
	
	if(cantidad.trim()===''){
		$("#total_do_"+denominacion).html(signo+"0,00");
		return false;
	}
	
	denominacion = parseInt(denominacion);
	cantidad = parseInt(cantidad);
	cantidad = cantidad * denominacion;
	cantidad = parseFloat(cantidad);
	cantidad = cantidad.format(2, 3, '.', ',');
	$("#total_do_"+denominacion).html(signo+cantidad);
	actualizarTotalesDenominacionesDolares(cantidad, denominacion)
}

function actualizarTotalesDenominaciones(cantidad, denominacion){
	signo = '₡';
	cantidad = cantidad.aFlotante();
	denominacion = parseInt(denominacion);
	if(denominacion <=500){
		//Actualizamos monedas
		total = 0;
		for(i = 0; i<denMonedas.length; i++){
			x = $("#total_"+denMonedas[i]).html();
			x = x.aFlotante();
			total += x;
		}
		total = total.format(2, 3, '.', ',');
		$("#total_monedas").html(signo+total);
	}else{
		//actualizamos billetes
		total = 0;
		for(i = 0; i<denBilletes.length; i++){
			x = $("#total_"+denBilletes[i]).html();
			x = x.aFlotante();
			total += x;
		}
		total = total.format(2, 3, '.', ',');
		$("#total_billetes").html(signo+total);
	}
	actualizarMontoTotalRetiro();
}

function actualizarTotalesDenominacionesDolares(cantidad, denominacion){
	signo = '$';
	cantidad = cantidad.aFlotante();
	denominacion = parseInt(denominacion);	
	total = 0;
	for(i = 0; i<denDolares.length; i++){
		x = $("#total_do_"+denDolares[i]).html();
		x = x.aFlotante();
		total += x;
	}
	total = total.format(2, 3, '.', ',');
	$("#total_dolares").html(signo+total);
	actualizarMontoTotalRetiro();
}

function actualizarMontoTotalRetiro(){
	signo = '$';
	dolares = $("#total_dolares").html().aFlotante();	
	signo = '₡';
	billetes = $("#total_billetes").html().aFlotante();
	monedas = $("#total_monedas").html().aFlotante();
	tipo_cambio = $("#tipo_cambio_dolar").val();
	tipo_cambio = parseFloat(tipo_cambio);
	total = (dolares*tipo_cambio)+billetes+monedas;
	totalRetirosParciales = parseFloat($("#totalRetirosParciales").val());
	totalRetirosParciales += total;
	baseCaja = parseFloat($("#base_caja").val());
	//totalRetirosParciales -= baseCaja;
	total -= baseCaja;
	$("#input_retiro_parcial").html(total.format(2, 3, '.', ','));
	//$("#parrafoTotalRetirosParciales").html("₡"+totalRetirosParciales.format(2, 3, '.', ','));
	
}

function validarYFormatearCantidadEscritaTipoCambio(cantidad){
	if(isCantidadValida(cantidad)){
		cantidadValida = true;	

		//Cambiamos los puntos por nada
		//cantidad = cantidad.replace('.','');
		//Cambiamos las comas por un punto, cambiar a notacion del numeric
		cantidad = cantidad.replace(',','.');
		cantidad = parseFloat(cantidad);
		cantidad = cantidad.format(2, 3, '.', ',');
		$("#tipo_cambio_dolar").val(cantidad);
		actualizarMontoTotalRetiro();
	}else{
		cantidadValida = false;
		notyMsg('¡La cantidad ingresada no es válida!', 'error');
	}
}

function validarYFormatearCantidadEscrita(cantidad){
	if(isCantidadValida(cantidad)){
		cantidadValida = true;	

		//Cambiamos los puntos por nada
		cantidad = cantidad.replace('.','');
		//Cambiamos las comas por un punto, cambiar a notacion del numeric
		cantidad = cantidad.replace(',','.');
		cantidad = parseFloat(cantidad);
		cantidad = cantidad.format(2, 3, '.', ',');
		$("#input_retiro_parcial").val(cantidad);
	}else{
		cantidadValida = false;
		notyMsg('¡La cantidad ingresada no es válida!', 'error');
	}
}

function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

function isCantidadValida(valor){
	//Cambiamos los puntos por nada
	valor = valor.replace('.','');
	//Cambiamos las comas por un punto, cambiar a notacion del numeric
	valor = valor.replace(',','.');	
	return isNumber(valor);
}


function realizarCierreCaja(){
	cantidad = $("#input_retiro_parcial").html().replace("₡", "").trim();
	if(isCantidadValida(cantidad)){
			$.prompt("¡Esto realizará un cierre de caja y no puede deshacerse!", {
							title: "¿Esta seguro que desea realizar este cierre de caja?",
							buttons: { "Si, estoy seguro": true, "Cancelar": false },
							submit:function(e,v,m,f){
														if(v){
															procesarCierre(cantidad);
														}
													}
						});	
	}
}

function procesarCierre(cantidad){
	tipo_cambio = $("#tipo_cambio_dolar").val();
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/contabilidad/cierre/crearCierre',
		type: "POST",		
		data: {'cantidadEfectivo':cantidad, 'tipo_cambio':tipo_cambio, 'colones':getJSONColones(), 'dolares':getJSONDolares(), 'fechaCierre':fechaReal, 'base':$("#base_caja").val()},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					notyMsg('¡Se realizó el cierre de caja con éxito!', 'success');					
					window.open(location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/impresion?t='+informacion[0].token+'&d=cc&n='+informacion[0].cierre+'&s='+informacion[0].sucursal+'&i=c&server='+document.domain+'&protocol='+location.protocol,'Impresion de Cieere de Caja','width='+anchoImpresion+',height='+alturaImpresion+',resizable=no,toolbar=no,location=no,menubar=no');
					window.location.replace(location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+"/home");
				}
			}catch(e){				
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

function manejarErrores(tipo){
	switch(tipo){
		case '1' :
			notyMsg('¡No se pudo realizar el cierre de caja, contacte al administrador!', 'error');
		break;
		case '2' :
			notyMsg('¡La URL esta incompleta, contacte al administrador!', 'error');
		break;
		case '3' :
			notyMsg('¡La cantidad ingresada no es válida!', 'error');
		break;
		case '4' :
			notyMsg('¡No se puede realizar el cierre, hay facturas pendientes de cobro!', 'error');
		break;
	}
}

function notyMsg(Mensaje, tipo){
	n = noty({
			   layout: 'topRight',
			   text: Mensaje,
			   type: tipo,
			   timeout: 4000
			});
}

function getJSONColones(){
	monedas = [];
	//Procesamos billetes
	for(i = 0; i<denBilletes.length; i++){
		cantidad = $("#cant_"+denBilletes[i]).val();
		monedas.push({'denominacion':denBilletes[i],'cantidad':cantidad});
	}
	//Procesamos monedas
	for(i = 0; i<denMonedas.length; i++){
		cantidad = $("#cant_"+denMonedas[i]).val();
		monedas.push({'denominacion':denMonedas[i],'cantidad':cantidad});
	}
	return JSON.stringify(monedas);
}

function getJSONDolares(){
	dolares = [];
	//Procesamos billetes
	for(i = 0; i<denDolares.length; i++){
		cantidad = $("#cant_do_"+denDolares[i]).val();
		dolares.push({'denominacion':denDolares[i],'cantidad':cantidad});
	}
	return JSON.stringify(dolares);	
}


// Para el tamaño del windows open
var anchoImpresion = 1024;
var alturaImpresion = 768;
var tipoImpresion = 'c';

function cambiarTipoImpresion(tipo){
	tipoImpresion = tipo;
	switch(tipo){
		case 't':
			anchoImpresion = 290;
			alturaImpresion = 400;
		break;
		case 'c':
			anchoImpresion = 1024;
			alturaImpresion = 768;
		break;
	}
}