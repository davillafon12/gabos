var codigoCambiarValido = false; //Variable que controla si se cargo un producto bueno
var codigoAbonarValido = false; //Variable que controla si se cargo un producto valido a abonar

$(function() {
    $("#codigo_abonar").numeric();
	$("#codigo_cambiar").numeric();
	$("#cantidad").numeric();
});

function getArticuloCambiar(){
	codigo = $("#codigo_cambiar").val();
	sucursal = $("#sucursal").val();
	
	if(codigo.trim()===''){
		cleanFieldCambiar();
		$("#status").css('display', 'none');
		codigoCambiarValido = false;
		return false;
	}
	
	$.ajax({
		url : location.protocol+'//'+document.domain+'/articulos/cambio/getArticulo',
		type: "POST",		
		async: false,
		data: {'codigo':codigo, 'sucursal':sucursal},				
		success: function(data, textStatus, jqXHR)
		{
			try{				
				informacion = $.parseJSON('[' + data.trim() + ']');				
				if(informacion[0].status==="error"){
					//manejarErrores(informacion[0].error, fila);
					cleanFieldCambiar();
					$("#status").css('display', 'inline');
					codigoCambiarValido = false;
				}else if(informacion[0].status==="success"){	
					$("#status").css('display', 'none');
					setProducto(informacion[0].articulo);
					codigoCambiarValido = true;
				}
			}catch(e){
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
	
}

function cambioDeSucursal(){
	getArticuloCambiar();
	getArticuloAbonar();
}

function getArticuloAbonar(){
	codigo = $("#codigo_abonar").val();
	sucursal = $("#sucursal").val();
	
	if(codigo.trim()===''){
		cleanFieldAbonar();
		$("#status2").css('display', 'none');
		codigoAbonarValido = false;
		return false;
	}
	
	$.ajax({
		url : location.protocol+'//'+document.domain+'/articulos/cambio/getArticulo',
		type: "POST",		
		async: false,
		data: {'codigo':codigo, 'sucursal':sucursal},				
		success: function(data, textStatus, jqXHR)
		{
			try{				
				informacion = $.parseJSON('[' + data.trim() + ']');				
				if(informacion[0].status==="error"){
					//manejarErrores(informacion[0].error, fila);
					cleanFieldAbonar();
					$("#status2").css('display', 'inline');
					codigoAbonarValido = false;
				}else if(informacion[0].status==="success"){	
					$("#status2").css('display', 'none');
					setProductoAbonar(informacion[0].articulo);
					codigoAbonarValido = true;
				}
			}catch(e){
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
	
}

function cleanFieldCambiar(){
	$("#descripcion_cambiar").html('');
	$("#inventario").html('');
	$("#inventarioh").val('');
}

function cleanFieldAbonar(){
	$("#descripcion_abonar").html('');
	$("#cantidad").val('');
}

function setProducto(articulo){
	$("#descripcion_cambiar").html(articulo.descripcion);
	$("#inventario").html(articulo.inventario);
	$("#inventarioh").val(articulo.inventario);
}

function setProductoAbonar(articulo){
	$("#descripcion_abonar").html(articulo.descripcion);
}

function notyMsg(Mensaje, tipo){
	n = noty({
			   layout: 'topRight',
			   text: Mensaje,
			   type: tipo,
			   timeout: 4000
			});
}

function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

function validarCantidad(){
	inventario = $("#inventarioh").val();
	cantidad = $("#cantidad").val();
	
	if(!isNumber(cantidad)){
		notyMsg('La cantidad ingresada no es válida', 'error');
		return false;
	}
	
	cantidad = parseInt(cantidad);
	inventario = parseInt(inventario);
	
	if(cantidad<1){
		notyMsg('La cantidad ingresada debe ser mayor a cero', 'error');
		return false;
	}
	
	if(cantidad>inventario){
		notyMsg('La cantidad ingresada debe ser menor al disponible en inventario', 'error');
		return false;
	}
	
	return true;
}

function validarCodigos(){
	cambiar = $("#codigo_cambiar").val();
	abonar = $("#codigo_abonar").val();
	
	if(cambiar.trim() === abonar.trim()){
		notyMsg('Los artículos deben ser diferentes', 'error');
		return false;
	}
	return true;
}

function realizarCambioCodigo(){
	if(codigoCambiarValido){
		if(codigoAbonarValido){
			if(validarCantidad()){
				if(validarCodigos()){
					$.prompt("¡Esto cambiará el inventario!", {
					title: "¿Esta seguro que desea cambiar este código?",
					buttons: { "Si, estoy seguro": true, "Cancelar": false },
					submit:function(e,v,m,f){
												if(v){													
													sendCambio($("#codigo_cambiar").val(), $("#codigo_abonar").val(), $("#sucursal").val(), $("#cantidad").val());
												}
											}
					});	
				}
			}
		}else{
			notyMsg('Articulo a Abonar no es válido', 'error');
		}
	}else{
		notyMsg('Articulo a Cambiar no es válido', 'error');
	}	
}

function limpiarTodo(){
	$("#descripcion_cambiar").html('');
	$("#inventario").html('');
	$("#inventarioh").val('');
	$("#descripcion_abonar").html('');
	$("#cantidad").val('');
	$("#codigo_cambiar").val('');
	$("#codigo_abonar").val('');
	$("#sucursal")[0].selectedIndex = 0;
	codigoCambiarValido = false; 
    codigoAbonarValido = false;
}

function sendCambio(cod_cambiar, cod_abonar, sucursal, cantidad){
	$.ajax({
		url : location.protocol+'//'+document.domain+'/articulos/cambio/realizarCambio',
		type: "POST",		
		async: false,
		data: {'cod_cambiar':cod_cambiar, 'cod_abonar':cod_abonar, 'sucursal':sucursal, 'cantidad':cantidad},				
		success: function(data, textStatus, jqXHR)
		{
			try{				
				informacion = $.parseJSON('[' + data.trim() + ']');				
				if(informacion[0].status==="error"){
					manejoErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){	
					notyMsg('¡Se realizó el cambio de código con éxito!', 'success');
					limpiarTodo();
				}
			}catch(e){
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

function manejoErrores(error){
	switch(error){
		case '1':
			notyMsg('¡No se puedo realizar el cambio, contacte al administrador!', 'error');
		break;
		case '2':
			notyMsg('¡URL en formato incorrecto, contacte al administrador!', 'error');
		break;
		case '3':
			notyMsg('¡Algún dato ingresado no es numérico!', 'error');
		break;
		case '4':
			notyMsg('¡Sucursal ingresada no existe!', 'error');
		break;
		case '5':
			notyMsg('¡Artículo a cambiar no existe!', 'error');
		break;
		case '6':
			notyMsg('¡Artículo a abonar no existe!', 'error');
		break;
		case '7':
			notyMsg('¡La cantidad ingresada no es válida!', 'error');
		break;
	}
}