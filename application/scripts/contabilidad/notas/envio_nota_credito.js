function enviarNotaCredito(){
	if(validarEnvio()){
		jsonEnvio = obtenerJSON();
		enviarServer(jsonEnvio);
	}
}

function validarEnvio(){
	if(validarCedulaNombre()){
		cantFacturas = $('#tabla_facturas tr').length;
		if(cantFacturas>1){
			cantProductos = $('#tabla_productos tr').length;
			cantProductosSeleccionados = $('#tabla_productos_seleccionados tr').length;
			if(cantProductos>1||cantProductosSeleccionados>1){				
				if(cantProductosSeleccionados>1){
					if(validarCantidadDevolver(cantProductosSeleccionados-1)){
						if(validarCantidadDevolverConCantidadReal(cantProductosSeleccionados-1)){
							if(validarCantidadesMayoresCero(cantProductosSeleccionados-1)){
								if(existeFacturaAplicar){
									return true;
								}else{
									notyMsg('¡No se ha ingresado o no existe la factura a aplicar la nota!','error');
									return false;
								}
							}else{
								notyMsg('¡No ha ingresado alguna cantidad para devolver en algun producto!','error');
								return false;
							}							
						}else{
							notyMsg('¡Alguna cantidad de devolución no concuerda con la cantidad original!','error');
							return false;
						}					
					}else{
						notyMsg('¡Alguna cantidad de devolución es incorrecta!','error');
						return false;
					}
				}else{
					notyMsg('¡No se han seleccionado productos para la nota!','error');
					return false;
				}
			}else{
				notyMsg('¡No se han cargado productos!','error');
				return false;
			}
		}else{
			notyMsg('¡No se han cargado facturas!','error');
			return false;
		}
	}else{
		return false;
	}
	return true;
}

function validarCedulaNombre(){
	cedula = $("#cedula").val();
	nombre = $("#nombre").val();
	if(cedula.trim()===''){
		notyMsg('¡Cédula ingresada no es válida!','error');
		return false;
	}
	if(nombre.trim()==='No existe cliente!!!'||nombre.trim()===''){
		notyMsg('¡Nombre ingresado no es válido!','error');
		return false;
	}
	return true;
} 

function validarCantidadDevolver(cantidad){
	for(i=0; i<cantidad; i++){
		defectuoso = $("#celda_cantidad_defectuoso_"+productosCreditar[i]).html();
		bueno = $("#celda_cantidad_buena_"+productosCreditar[i]).html();
		//Verificamos que sean numeros
		if(!isNumber(defectuoso)||!isNumber(bueno)){return false;}
		//Si lo son revisamos que no sean menores a cero
		else if(parseFloat(defectuoso)<0||parseFloat(bueno)<0){return false;}
	}
	return true;
}

function validarCantidadDevolverConCantidadReal(cantidad){
	for(i=0; i<cantidad; i++){
		defectuoso = parseFloat($("#celda_cantidad_defectuoso_"+productosCreditar[i]).html());
		bueno = parseFloat($("#celda_cantidad_buena_"+productosCreditar[i]).html());
		original = parseFloat($("#p_cantidad_original_"+productosCreditar[i]).html());
		//Si la cantidad original es menor a la suma de defectuoso y bueno
		if(original<(defectuoso+bueno)){
			return false;
		}
	}
	return true;
}

function validarCantidadesMayoresCero(cantidad){	
	for(i=0; i<cantidad; i++){
		defectuoso = parseFloat($("#celda_cantidad_defectuoso_"+productosCreditar[i]).html());
		bueno = parseFloat($("#celda_cantidad_buena_"+productosCreditar[i]).html());
		if(defectuoso<1 && bueno<1){
			return false;
		}
	}
	return true;
}

function obtenerJSON(){
	cedula = $("#cedula").val();
	nombre = $("#nombre").val();
	factura = $("#factura_aplicar").val();
	
	productos = [];
	cantidad = $('#tabla_productos_seleccionados tr').length-1;
	for(i=0; i<cantidad; i++){
		codigo = $("#codigo_producto_"+productosCreditar[i]).html();
		defectuoso = parseFloat($("#celda_cantidad_defectuoso_"+productosCreditar[i]).html());
		bueno = parseFloat($("#celda_cantidad_buena_"+productosCreditar[i]).html());
		productos.push({c:codigo, d:defectuoso, b:bueno});
	}
	
	return {cedula:cedula, nombre:nombre, facturaAplicar:factura, facturaSeleccion:facturaSeleccionada, productos:JSON.stringify(productos)};
}

function enviarServer(json){
	$.ajax({
		url : location.protocol+'//'+document.domain+'/contabilidad/notas/generarNota',
		type: "POST",		
		async: false,
		data: json,				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');				
				if(informacion[0].status==="error"){
					manejarErroresEnvioNotas(informacion[0].error);
				}else if(informacion[0].status==="success"){
					consecutivo = informacion[0].consecutivo;
					//AQUI SE DEBE ENVIAR A IMPRESION
					notyMsg('¡Se creó la nota crédito con éxito!', 'success');
					
					//Cargamos una cedula vacia y la buscamos para resetear todo
					$("#cedula").val('');
					buscarCedula(null);
					
					if(tipoImpresion==='t'){
						//Impresion termica
						window.open(informacion[0].servidor_impresion+'/index.html?t='+informacion[0].token+'&d=nc&n='+informacion[0].nota+'&s='+informacion[0].sucursal+'&i='+tipoImpresion+'&server='+document.domain+'&protocol='+location.protocol,'Impresion de Notas Credito','width='+anchoImpresion+',height='+alturaImpresion+',resizable=no,toolbar=no,location=no,menubar=no');
					}else if(tipoImpresion==='c'){
						//Impresion carta
						window.open(location.protocol+'//'+document.domain+'/impresion?t='+informacion[0].token+'&d=nc&n='+informacion[0].nota+'&s='+informacion[0].sucursal+'&i='+tipoImpresion,'Impresion de Nota Crédito','width='+anchoImpresion+',height='+alturaImpresion+',resizable=no,toolbar=no,location=no,menubar=no');
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

function manejarErroresEnvioNotas(error){
	switch(error){
		case '1':
			notyMsg('¡No se pudo procesar la nota, contacte al administrador!', 'error');
		break;
		case '2':
			notyMsg('¡Mal formato de la URL, contacte al administrador!', 'error');
		break;
		case '3':
			notyMsg('¡Algun campo requerido esta vacío!', 'error');
		break;
		case '4':
			notyMsg('¡Cliente no existe!', 'error');
		break;
		case '5':
			notyMsg('¡Alguna de las facturas no existe!', 'error');
		break;
		case '6':
			notyMsg('¡Uno o varios de los productos seleccionados ya no existen!', 'error');
		break;
		case '7':
			notyMsg('¡La factura a aplicar, ya fue aplicada en otra nota crédito!', 'error');
		break;
		case '8':
			notyMsg('¡Error al crear la nota ERROR:8!', 'error');
		break;
		case '9':
			notyMsg('¡Error al crear la nota ERROR:9!', 'error');
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


