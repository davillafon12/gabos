function generarNotaDebito(){
	if(validarNotaDebito()){
		enviarNota();
	}
}

function validarNotaDebito(){
	return tieneProductos();
}

function tieneProductos(){
	filas = $('#tabla_productos tr').length;
	for(i=1; i<filas; i++){
		descripcion = $("#descripcion_articulo_"+i).html();
		if(descripcion.trim()!=""){
			return true;
		}
	}
	notyMsg('Nota vacía, debe ingresar al menos un producto', 'error');
	return false;
}

function obtenerJSON(){
	productos = [];
	filas = $('#tabla_productos tr').length;
	for(i=1; i<filas; i++){		
		descripcion = $("#descripcion_articulo_"+i).html();
		codigo = $("#articulo_"+i).val();
		
		if(codigo.trim()!=''&&descripcion.trim()!=''){ //Si son filas con productos
			cantidad = $("#cantidad_articulo_"+fila).val();
			productos.push({co:codigo, ca:cantidad});
		}
	}
	return {productos:JSON.stringify(productos)};
}

function enviarNota(){
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')'/contabilidad/notas/generarNotaDebito',
		type: "POST",		
		async: false,
		data: obtenerJSON(),				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');				
				if(informacion[0].status==="error"){
					manejarErroresEnvioNotas(informacion[0].error);
				}else if(informacion[0].status==="success"){
					resetAllRows();
					notyMsg('¡Se creó la nota débito con éxito!', 'success');
					if(tipoImpresion==='t'){
						//Impresion termica
						window.open(informacion[0].servidor_impresion+'/index.html?t='+informacion[0].token+'&d=nd&n='+informacion[0].consecutivo+'&s='+informacion[0].sucursal+'&i='+tipoImpresion+'&server='+document.domain+'&protocol='+location.protocol,'Impresion de Notas Credito','width='+anchoImpresion+',height='+alturaImpresion+',resizable=no,toolbar=no,location=no,menubar=no');
					}else if(tipoImpresion==='c'){
						//Impresion carta
						window.open(location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')'/impresion?t='+informacion[0].token+'&d=nd&n='+informacion[0].consecutivo+'&s='+informacion[0].sucursal+'&i='+tipoImpresion,'Impresion de Nota Débito','width='+anchoImpresion+',height='+alturaImpresion+',resizable=no,toolbar=no,location=no,menubar=no');
					}
				}
			}catch(e){
				//alert(e);
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

function resetAllRows(){
	filas = $('#tabla_productos tr').length;
	for(i=1; i<filas; i++){		
		resetRow(i, true);
	}	
}

function manejarErroresEnvioNotas(error){
	switch(error){
		case '1':
					notyMsg('¡No se pudo crear la nota débito, contacte al administrador!', 'error');
					break;
		case '2':
					notyMsg('¡URL incompleta, contacte al administrador!', 'error');
					break;
		case '3':
					notyMsg('¡Nota sin productos!', 'error');
					break;
	}
}

// Para el tamaño del windows open
var anchoImpresion = 290;
var alturaImpresion = 400;
var tipoImpresion = 't';

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