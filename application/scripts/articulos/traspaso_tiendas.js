var _SUCURSAL_ENTREGA_SELECCIONADA = "-1";
var _SUCURSAL_RECIBE_SELECCIONADA = "-1";
var _ARRAY_ARTICULOS = [];
var anchoImpresion = 1024;
var alturaImpresion = 768;

$(document).ready(function(){
	$("#sucursal_entrega").change(validarSucursalEntrega);
	$("#sucursal_recibe").change(validarSucursalRecibe);
	$(".input_codigo_articulo").keyup(cargarArticulo);
	$(".cantidad_articulo").keyup(validarCantidad);
	$("#realizar_traspaso").click(realizarTraspaso);
});

/**************************  VALIDACION DE SUCURSAL QUE CONSIGNA  *************************************************/

function validarSucursalEntrega(){
		var sucursalEntrega = $("#sucursal_entrega").val();
		sucursalEntrega = sucursalEntrega.trim();
		if(sucursalEntrega === "-1"){
				confirmarBorradoDeArticulos();
		}else{
				//Si entra la misma sucursal no haga nada
				if(_SUCURSAL_ENTREGA_SELECCIONADA !== sucursalEntrega && _SUCURSAL_ENTREGA_SELECCIONADA !== "-1"){
						confirmarBorradoDeArticulos("entrega");
				}else if(_SUCURSAL_ENTREGA_SELECCIONADA === "-1"){
						habilitarCampos();
					 _SUCURSAL_ENTREGA_SELECCIONADA = $("#sucursal_entrega").val();
				}
		}
}

function validarSucursalRecibe(){
		var sucursalRecibe = $("#sucursal_recibe").val();
		sucursalRecibe = sucursalRecibe.trim();
		if(sucursalRecibe === "-1"){
				confirmarBorradoDeArticulos();
		}else{
				//Si entra la misma sucursal no haga nada
				if(_SUCURSAL_RECIBE_SELECCIONADA !== sucursalRecibe && _SUCURSAL_RECIBE_SELECCIONADA !== "-1"){
						confirmarBorradoDeArticulos("recibe");
				}else if(_SUCURSAL_RECIBE_SELECCIONADA === "-1"){
						habilitarCampos();
					 _SUCURSAL_RECIBE_SELECCIONADA = $("#sucursal_recibe").val();
				}
		}
}

function confirmarBorradoDeArticulos(tipoSucursal){
		$.prompt("¡Esto eliminará los artículos ingresados!", {
			title: "¿Esta seguro que desea cambiar de sucursal?",
			buttons: { "Si, estoy seguro": true, "Cancelar": false },
			submit:function(e,v,m,f){
										if(tipoSucursal === "entrega"){
											if(v){													
												resetAllFields();
												_SUCURSAL_ENTREGA_SELECCIONADA = $("#sucursal_entrega").val();
												if(_SUCURSAL_ENTREGA_SELECCIONADA === "-1"){
														deshabilitarCampos();
												}else{
														habilitarCampos();
												}
											}else{
												$("#sucursal_entrega").val(_SUCURSAL_ENTREGA_SELECCIONADA);
											}
										}else if(tipoSucursal === "recibe"){
											if(v){													
												resetAllFields();
												_SUCURSAL_RECIBE_SELECCIONADA = $("#sucursal_recibe").val();
												if(_SUCURSAL_RECIBE_SELECCIONADA === "-1"){
														deshabilitarCampos();
												}else{
														habilitarCampos();
												}
											}else{
												$("#sucursal_recibe").val(_SUCURSAL_RECIBE_SELECCIONADA);
											}
										}
									}
		});
}


/**********************************  CARGA DE ARTICULOS  ***********************************************************/

function cargarArticulo(event){
		var elementId = event.target.id;
		var fila = elementId.replace("articulo_","");
		var contenido = $("#"+elementId).val().trim();
		var descripcion = $("#descripcion_articulo_"+fila).html().trim();
		var sucursal = $("#sucursal_entrega").val().trim();
		var sucursalRecibe = $("#sucursal_recibe").val().trim();
		
	if(event.which == 13) {
        if(contenido !== ""){
        	if(descripcion !== ""){
        		//Nos movemos a cantidad de articulo
        		$("#cantidad_articulo_"+fila).select();
        	}else{
	        	if(sucursal === ""){
			    		notyMsg('¡Debe seleccionar una sucursal que entrega!', 'error');
			    		return false;
			    }
			    
			    if(sucursalRecibe === "-1"){
			    		notyMsg('¡Debe seleccionar una sucursal que recibe!', 'error');
			    		return false;
			    }
			    
/*
			    if(CodigoYaFueIngresado(contenido)){
			    		//notyMsg('¡Artículo ya fue ingresado!', 'error');
			    		return false;
			    }
*/
			    ajaxArticulo(contenido, sucursal, sucursalRecibe, fila);
        	}
        	return false;
        }
	    
	    
    }
    
    if(contenido === ""){
		resetFila(fila, true);
    }
    
}

function ajaxArticulo(codigo, sucursal, sucursalRecibe, fila){
	doAjax('/contabilidad/consignaciones/obtenerArticulo', 'POST', false, {codigo:codigo, sucursal:sucursal, sucursalRecibe:sucursalRecibe}, 'json', function(data){
			if(data.status === "success"){
				if(data.articulo === "no_existe"){
						resetFila(fila, false);
				}else{
						cargarInfoArticulo(data.articulo, fila);
				}
			}else{
				notyMsg(data.error, 'error');
			}
	}, function(){
			notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
	});
}


function cargarInfoArticulo(articulo, fila){
		$("#descripcion_articulo_"+fila).html(articulo.descripcion);
		//Seteamos el tootltip de la imagen del articulo
		$("#tooltip_imagen_articulo_"+fila).html("<img src='"+location.protocol+"//"+document.domain+(location.port ? ':'+location.port: '')+"/application/images/articulos/"+articulo.imagen+"' height='200' width='200'>");
		agregarTooltip("#descripcion_articulo_"+fila);
		$("#cantidad_articulo_"+fila).val(1);
		$("#bodega_articulo_"+fila).html(articulo.inventario);
		
}



/**************************************  VALIDACION DE CANTIDAD DE ARTICULOS  ***************************************************/

function validarCantidad(event){
		var elementId = event.target.id;
		var fila = elementId.replace("cantidad_articulo_","");
		var contenido = $("#"+elementId).val();
		var inventario = parseInt($("#bodega_articulo_"+fila).html());
		
		if(contenido.trim() === ""){
			$("#"+elementId).val(1);
		}
		
		try{ 
			contenido = parseInt(contenido);
		}catch(e){
			contenido = 1;
		}
		//Menor a uno
		if(contenido < 1){
				$("#"+elementId).val(1);
		}
		
		if(contenido > inventario){
				$("#"+elementId).val(inventario);
		}
		
		if(event.which == 13){
				moverseASiguienteFila(fila);
		}
}



/************************************  VALIDACION Y CREACION DE CONSIGNACION  ***************************************************/

function validarExistenciaProductosEnTabla(){
		if(_ARRAY_ARTICULOS.length == 0){
				notyMsg("No hay artículos ingresados.", "error");
				return false;
		}else{
				return true;
		}
}

function validarSucursales(){
		var sucursalEntrega = $("#sucursal_entrega").val().trim();
		var sucursalRecibe = $("#sucursal_recibe").val().trim();
		
		if(sucursalEntrega === "-1"){
				notyMsg("Debe seleccionar una sucursal de salida.", "error");
				return false;
		}
		
		if(sucursalRecibe === "-1"){
				notyMsg("Debe seleccionar una sucursal que recibe los artículos.", "error");
				return false;
		}
		
		if(sucursalEntrega === sucursalRecibe){
				notyMsg("El traspaso no puede ser entre la misma sucursal.", "error");
				return false;
		}
		
		return true;
}

function cargarArrayArticulos(){
		_ARRAY_ARTICULOS = [];
		var cantidadFilas = $("#tabla_productos tr").length - 1;
		for(var i=1; i <= cantidadFilas; i++){
				var codigo = $("#articulo_"+i).val().trim();
				var descripcion = $("#descripcion_articulo_"+i).html().trim();
				var cantidad = $("#cantidad_articulo_"+i).val().trim();
				
				if(codigo !== "" && descripcion !== ""){
						_ARRAY_ARTICULOS.push({codigo:codigo, descripcion:descripcion, cantidad:cantidad});
				}
				
		}
}

function realizarTraspaso(){
	cargarArrayArticulos();
	if(validarSucursales()){
		if(validarExistenciaProductosEnTabla()){
			$.prompt("¡Esto traspasará el inventario de los artículos ingresados!", {
				title: "¿Esta seguro que desea realizar el traspaso?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
										if(v){													
												var parametros = {
																sucursalRecibe :  $("#sucursal_recibe").val().trim(),
																sucursalEntrega:  $("#sucursal_entrega").val().trim(),
																articulos      :	JSON.stringify(_ARRAY_ARTICULOS)																				
														};
												doAjax("/articulos/traspaso/realizarTraspaso", "POST", false, parametros, "JSON", resultadoTraspaso, function(){
														notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
												});
										}
								}
			});
		}
	}
}

function resultadoTraspaso(data){
	if(data.status == 'success'){
		resetAllFields();
		notyMsg("Trasapaso realizado con éxito", "success");
		//Impresion carta
		window.open(location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/impresion?t='+data.token+'&d=ti&n='+data.traspaso+'&s='+data.sucursal+'&i=c','Impresión de Traspaso de Inventario','width='+anchoImpresion+',height='+alturaImpresion+',resizable=no,toolbar=no,location=no,menubar=no');

	}else{
		notyMsg(data.error, "error");
	}
}




/*********************************************************************************************************************************
*
*                                             UTILERIAS
*
*/

// n=decimales | x=centenares(Grupos de numeros) | s=divisor de centenares | c=divisor procentaje 
Number.prototype.format = function(n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};

function notyMsg(Mensaje, tipo){
	n = noty({
			   layout: 'topRight',
			   text: Mensaje,
			   type: tipo,
			   timeout: 4000
			});
}

function doAjax(url, method, async, parametros, datatype, successCallback, errorCallback){
		$.ajax({
			url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+url,
			type: method,		
			async: async,
			data: parametros,		
			dataType: datatype,		
			success: function(data, textStatus, jqXHR)
			{ if(successCallback!==null){successCallback(data);} },
			error: function (jqXHR, textStatus, errorThrown)
			{ if(errorCallback!==null){errorCallback(); } }
		});
}

function resetAllFields(){
		var cantidadFilas = $("#tabla_productos tr").length - 1;
		for(var i=1; i <= cantidadFilas; i++){
				resetFila(i, true);
		}
		_ARRAY_ARTICULOS = [];
}

function resetFila(fila, resetCodigo){
		if(resetCodigo){
				$("#articulo_"+fila).val("");
		}
		$("#descripcion_articulo_"+fila).html("");
		$("#tooltip_imagen_articulo_"+fila).html("");
		$("#cantidad_articulo_"+fila).val("");
		$("#bodega_articulo_"+fila).html("");
}

function habilitarCampos(){
		$(".input_codigo_articulo").prop('disabled', false);
		$(".cantidad_articulo").prop('disabled', false);
}

function deshabilitarCampos(){
		$(".input_codigo_articulo").prop('disabled', true);
		$(".cantidad_articulo").prop('disabled', true);
}

function moverseASiguienteFila(fila){
		var cantidadFilas = $("#tabla_productos tr").length - 1;
		fila = parseInt(fila);
		if(cantidadFilas === fila){
				//Agregar fila
				agregarFila(fila+1);
		}else{
				$("#articulo_"+(fila + 1)).select();
		}
}

function agregarFila(siguienteFila){
		var tabNumber = siguienteFila+1;
		var filaHTML = "<tr>"
											+"<td>"									
												+"<input id='articulo_"+siguienteFila+"' tabindex='"+tabNumber+"'  class='input_codigo_articulo' autocomplete='off' name='codigo_articulo' type='text'  />				"					
												
											+"</td>"
											+"<td>"
												+"<div class='articulo_specs' id='descripcion_articulo_"+siguienteFila+"'></div>"
												+"<div class='tooltip_imagen_articulo' id='tooltip_imagen_articulo_"+siguienteFila+"'></div>"
											+"</td>"
											+"<td>"
												+"<input id='cantidad_articulo_"+siguienteFila+"' class='cantidad_articulo' autocomplete='off' type='number' min='1'  />"									
											+"</td>"
											+"<td>"
												+"<div class='articulo_specs' id='bodega_articulo_"+siguienteFila+"'></div>"
											+"</td>"
										+"</tr>";
		$("#cuerpo_tabla_articulos").append(filaHTML);
		$("#articulo_"+siguienteFila).select();
		$("#articulo_"+siguienteFila).keyup(cargarArticulo);
		$("#cantidad_articulo_"+siguienteFila).keyup(validarCantidad);
}


function agregarTooltip(id_Row){
	$(id_Row).mouseover(function(){
		eleOffset = $(this).offset();

		$(this).next().fadeIn("fast").css({
			left: eleOffset.left + 100,
			top: eleOffset.top - 100
		});
		
	}).mouseout(function(){
		$(this).next().hide();
	});
} 


function CodigoYaFueIngresado(codigo){
		var cantidadFilas = $("#tabla_productos tr").length - 1;
		for(var i=1; i <= cantidadFilas; i++){
				var codigoArt = $("#articulo_"+i).val().trim();
				var descripcion = $("#descripcion_articulo_"+i).html().trim();
				if(codigoArt !== "" && descripcion !== ""){
						if(codigoArt === codigo){
								return true;
						}
				}				
		}
		return false;
}