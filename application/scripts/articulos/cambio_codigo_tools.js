var codigoCambiarValido = false; //Variable que controla si se cargo un producto bueno
var codigoAbonarValido = false; //Variable que controla si se cargo un producto valido a abonar
var sucursalYaIngresada = false; //Variable que controla si ya se cargo una sucursal

$(function() {
    //$("#codigo_abonar").numeric();
	//$("#codigo_cambiar").numeric();
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

function ingresarSucursal(){ //Bloquea la seleccion de sucursal una vez se haya ingresado el primer articulo, para no enviar diferentes articulos en diferentes sucursales
	sucursalYaIngresada = true;
	$("#sucursal").prop('disabled', true);
}

function desIngresarSucursal(){ //Bloquea la seleccion de sucursal una vez se haya ingresado el primer articulo, para no enviar diferentes articulos en diferentes sucursales
	sucursalYaIngresada = false;
	$("#sucursal").prop('disabled', false);
}

function getFilasTabla(){
	return $("#tbody_articulos tr");
}

function verificarCodigoNoRepetido(){
	filas = getFilasTabla();
	codigo = $("#codigo_cambiar").val();
	if(filas.length>0){
		for(i = 0; i<filas.length; i++){
			codigoFila = filas[i].children[0].innerHTML;
			if(codigo.trim() === codigoFila.trim()){
				notyMsg('El artículo ya fue ingresado para cambio', 'error');
				return false;
			}
		}
	}
	return true;
}

function agregarFila(){
	if(codigoCambiarValido){
		if(codigoAbonarValido){
			if(validarCantidad()){
				if(validarCodigos()){
					if(verificarCodigoNoRepetido()){
						ingresarSucursal(); //Bloquemos la sucursal
						
						ingresarFila($("#codigo_cambiar").val(), $("#codigo_abonar").val(), $("#cantidad").val(), $("#descripcion_cambiar").html(), $("#descripcion_abonar").html());
						//Verificar que sea de la misma sucursal, para esto bloquear el combo cuando ya se agrego la primera fila, y verificar que cuando se eliminen todas volver a habilitarlo
					}
				}
			}
		}else{
			notyMsg('Articulo a Abonar no es válido', 'error');
		}
	}else{
		notyMsg('Articulo a Cambiar no es válido', 'error');
	}
}

function ingresarFila(cod_cambiar, cod_abonar, cantidad, des_cambiar, des_abonar){

	fila = "<tr class='fila_articulo'>"
	+	"<td class='contactSmall'>"+cod_cambiar+"</td>"
	+	"<td class='contactSmall'>"+des_cambiar+"</td>"
	+	"<td class='contactSmall'><img src="+location.protocol+"//"+document.domain+"/application/images/recibos/flecha_derecha.png></td>"
	+	"<td class='contactSmall'>"+cod_abonar+"</td>"
	+	"<td class='contactSmall'>"+des_abonar+"</td>"
	+	"<td class='contactSmall'>"+cantidad+"</td>"
	+	"<td class='contactSmall'><a href='javascript:;' onclick='eliminarFila(this)'><img class='eliminar_cruz' title='Eliminar Artículo' src="+location.protocol+"//"+document.domain+"/application/images/Icons/eliminar.png></a></td>"
	+ "</tr>";
	$("#tbody_articulos").append(fila);
}


function realizarCambioCodigo(){
	filas = getFilasTabla();
	
	if(filas.length>0){
		$.prompt("¡Esto cambiará el inventario!", {
					title: "¿Esta seguro que desea cambiar este código?",
					buttons: { "Si, estoy seguro": true, "Cancelar": false },
					submit:function(e,v,m,f){
							if(v){													
								sendCambio($("#sucursal").val(), articulosToJSON());
							}
						}
					});	
	}else{
		notyMsg('Debe agregar al menos un artículo para realizar el cambio', 'error');
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
	$("#tbody_articulos").html('');
	sucursalYaIngresada = false;
	desIngresarSucursal();
}

function sendCambio(sucursal, articulos){
	$.ajax({
		url : location.protocol+'//'+document.domain+'/articulos/cambio/realizarCambio',
		type: "POST",		
		async: false,
		data: {'sucursal':sucursal, 'articulos':articulos},				
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

function eliminarFila(fila){
	padre = fila.parentNode.parentNode.parentNode;
	hijo = fila.parentNode.parentNode;
	padre.removeChild(hijo);
	verificarTablaVacia();
}

function verificarTablaVacia(){
	filas = getFilasTabla();
	if(filas.length<1){
		desIngresarSucursal();
	}
}

function articulosToJSON(){
	filas = getFilasTabla();
	articulos = [];
	
	for(i=0; i<filas.length; i++){
				
		codigo_cambiar = filas[i].children[0].innerHTML;
		codigo_abonar = filas[i].children[3].innerHTML;
		cantidad = filas[i].children[5].innerHTML;
		
		articulos.push({'cambiar':codigo_cambiar, 'abonar':codigo_abonar, 'cantidad':cantidad});
	}
	return JSON.stringify(articulos);
}