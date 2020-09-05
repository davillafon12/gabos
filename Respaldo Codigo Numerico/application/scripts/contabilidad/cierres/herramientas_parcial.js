var cantidadValida = false; //Variable guarda la condicion de si la cantidad ingresada es valida

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

function notyMsg(Mensaje, tipo){
	n = noty({
			   layout: 'topRight',
			   text: Mensaje,
			   type: tipo,
			   timeout: 4000
			});
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

function realizarRetiroParcial(){	
	cantidad = $("#input_retiro_parcial").val();
	if(cantidadValida&&isCantidadValida(cantidad)){ //Si la cantidad es valida prosigue
		//alert("Entro");
		
		$.prompt("¡Esto agregará un retiro parcial al cierre de caja!", {
					title: "¿Esta seguro que desea realizar este retiro parcial?",
					buttons: { "Si, estoy seguro": true, "Cancelar": false },
					submit:function(e,v,m,f){
												if(v){
													enviarRetiro(cantidad);
												}
											}
				});	
	}else{
		cantidadValida = false;
		notyMsg('¡La cantidad ingresada no es válida!', 'error');
	}
}

function enviarRetiro(cantidad){
	$.ajax({
		url : location.protocol+'//'+document.domain+'/contabilidad/retiro/crearRetiro',
		type: "POST",		
		//async: false,
		data: {'cantidad':cantidad},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					notyMsg('¡Se realizó el retiro parcial con éxito!', 'success');
					$("#input_retiro_parcial").val("");
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
			notyMsg('¡No se pudo realizar el retiro parcial, contacte al administrador!', 'error');
		break;
		case '2' :
			notyMsg('¡La URL esta incompleta, contacte al administrador!', 'error');
		break;
		case '3' :
			notyMsg('¡La cnatidad ingresada no es válida!', 'error');
		break;
	}
}