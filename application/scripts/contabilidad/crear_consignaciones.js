var _SUCURSAL_ENTREGA_SELECCIONADA = "-1";
var _SUCURSAL_RECIBE_SELECCIONADA = "-1";
var _ARRAY_ARTICULOS = [];
var anchoImpresion = 1024;
var alturaImpresion = 768;

//Cargamos los eventos iniciales
$(window).on("load", function(){
		$("#sucursal_entrega").change(validarSucursalEntrega);
		$("#sucursal_recibe").change(validarSucursalRecibe);
		$(".input_codigo_articulo").keyup(cargarArticulo);
		$(".cantidad_articulo").keyup(validarCantidad);
		$("#crear_consignacion").on("click", realizarConsignacion);
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
			    		notyMsg('¡Debe seleccionar una sucursal que entrega consignación!', 'error');
			    		return false;
			    }
			    
			    if(sucursalRecibe === "-1"){
			    		notyMsg('¡Debe seleccionar una sucursal que recibe consignación!', 'error');
			    		return false;
			    }
			    
			    if(CodigoYaFueIngresado(contenido)){
			    		//notyMsg('¡Artículo ya fue ingresado!', 'error');
			    		return false;
			    }
			    ajaxArticulo(contenido, sucursal, sucursalRecibe, fila);
        	}
        	return false;
        }
	    
	    
    }
    
    if(contenido === ""){
		resetFila(fila, true);
		return false;
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
		$("#descuento_articulo_muestra_"+fila).html(parseFloat(articulo.descuento).format(_CANTIDAD_DECIMALES, 3, '.', ','));
		$("#descuento_articulo_"+fila).val(articulo.descuento);
		$("#articulo_precio_unidad_muestra_"+fila).html(parseFloat(articulo.precio_cliente).format(_CANTIDAD_DECIMALES, 3, '.', ','));
		$("#articulo_precio_unidad_"+fila).val(articulo.precio_cliente);
		$("#articulo_precio_unidad_final_"+fila).val(articulo.precio_no_afiliado);
		$("#exento_articulo_"+fila).val(articulo.exento);
		$("#retencion_articulo_"+fila).val(articulo.retencion);
		actualizarPrecioTotalFila(fila);
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
		
		actualizarPrecioTotalFila(fila);
		
		if(event.which == 13){
				moverseASiguienteFila(fila);
		}
}


/************************************  VALIDACION Y CREACION DE CONSIGNACION  ***************************************************/

function realizarConsignacion(){
		cargarArrayArticulos();
		if(validarExistenciaProductosEnTabla()){
				if(validarSucursales()){
						$.prompt("¡Esto dará en consignación los artículos ingresados!", {
							title: "¿Esta seguro que desea realizar la consignación?",
							buttons: { "Si, estoy seguro": true, "Cancelar": false },
							submit:function(e,v,m,f){
													if(v){													
															var parametros = {
																									sucursalRecibe :  $("#sucursal_recibe").val().trim(),
																									sucursalEntrega:  $("#sucursal_entrega").val().trim(),
																									articulos      :	JSON.stringify(_ARRAY_ARTICULOS),
																									costo 				 :  $("#costo").val(), 
																									iva 					 :  $("#iva").val(),
																									retencion			 :  $("#retencion").val(),
																									total          :  $("#costo_total").val(),
																									porcentaje_iva :	_PORCENTAJE_IVA																					
																							};
															doAjax("/contabilidad/consignaciones/consignarArticulos", "POST", false, parametros, "JSON", resultadoConsignacion, function(){
																	notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
															});
													}
											}
						});
				}
		}
}

function resultadoConsignacion(data){
		if(data.status === "success"){
				resetAllFields();
				notyMsg('¡Se creó la consignación con éxito!', 'success');
				
				//Impresion carta
				window.open(location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/impresion?t='+data.token+'&d=con&n='+data.consignacion+'&s='+data.sucursal+'&i=c','Impresion de Consignación','width='+anchoImpresion+',height='+alturaImpresion+',resizable=no,toolbar=no,location=no,menubar=no');
				
		}else{
			notyMsg(data.error, 'error');
		}
}

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
				notyMsg("Debe seleccionar una sucursal que consigna.", "error");
				return false;
		}
		
		if(sucursalRecibe === "-1"){
				notyMsg("Debe seleccionar una sucursal que recibe la consignación.", "error");
				return false;
		}
		
		if(sucursalEntrega === sucursalRecibe){
				notyMsg("La consignación no puede ser entre la misma sucursal.", "error");
				return false;
		}
		
		return true;
}

function cargarArrayArticulos(){
		var cantidadFilas = $("#tabla_productos tr").length - 1;
		for(var i=1; i <= cantidadFilas; i++){
				var codigo = $("#articulo_"+i).val().trim();
				var descripcion = $("#descripcion_articulo_"+i).html().trim();
				var cantidad = $("#cantidad_articulo_"+i).val().trim();
				var descuento = $("#descuento_articulo_"+i).val().trim();
				var precio_unidad = $("#articulo_precio_unidad_"+i).val().trim();
				var precio_total = $("#articulo_precio_total_"+i).val().trim();
				var precio_final = $("#articulo_precio_unidad_final_"+i).val().trim();
				var exento = $("#exento_articulo_"+i).val().trim();
				var retencion = $("#retencion_articulo_"+i).val().trim();
				
				if(codigo !== "" && descripcion !== ""){
						_ARRAY_ARTICULOS.push({codigo:codigo, descripcion:descripcion, cantidad:cantidad, descuento:descuento, exento:exento, retencion:retencion, precio_unidad:precio_unidad, precio_total:precio_total, precio_final:precio_final});
				}
				
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
		actualizarTotales();
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
		$("#descuento_articulo_muestra_"+fila).html("");
		$("#descuento_articulo_"+fila).val("");
		$("#articulo_precio_unidad_muestra_"+fila).html("");
		$("#articulo_precio_unidad_"+fila).val("");
		$("#articulo_precio_unidad_final_"+fila).val("");
		$("#articulo_precio_total_muestra_"+fila).html("");
		$("#articulo_precio_total_"+fila).val("");
		$("#articulo_precio_total_sin_descuento_"+fila).val("");
		$("#exento_articulo_"+fila).val("");
		$("#retencion_articulo_"+fila).val("");
		actualizarPrecioTotalFila(fila);
}

function actualizarPrecioTotalFila(fila){
		var cantidad = $("#cantidad_articulo_"+fila).val().trim() === "" ? 0 : $("#cantidad_articulo_"+fila).val().trim();
				cantidad = parseInt(cantidad);
		var precioUnidad = $("#articulo_precio_unidad_"+fila).val().trim() === "" ? 0 : $("#articulo_precio_unidad_"+fila).val().trim();
				precioUnidad = parseFloat(precioUnidad);
		var descuento = $("#descuento_articulo_"+fila).val().trim() === "" ? 0 : $("#descuento_articulo_"+fila).val().trim();
				descuento = parseFloat(descuento);
				descuento /= 100;
		
		var montoDeDescuento = precioUnidad * descuento;
		
		var precioTotal = cantidad * precioUnidad;
		var precioTotalSinDescuento = precioTotal;
				precioTotal -= montoDeDescuento;
		
		$("#articulo_precio_total_muestra_"+fila).html(precioTotal.format(_CANTIDAD_DECIMALES, 3, '.', ','));
		$("#articulo_precio_total_"+fila).val(precioTotal);
		$("#articulo_precio_total_sin_descuento_"+fila).val(precioTotalSinDescuento);
		actualizarTotales();
}

function actualizarTotales(){
		var montoSinIVA = 0.0;
		var iva = 0.0;
		var total = 0.0;
		var ivaSinDescuento = 0.0;
		var retencion = 0.0;
		
		var cantidadFilas = $("#tabla_productos tr").length - 1;
		var cantidadArticulos = 0;
		
		for(var i=1; i <= cantidadFilas; i++){
				var precioTotalArticulo = $("#articulo_precio_total_"+i).val().trim() === "" ? 0.0 : parseFloat($("#articulo_precio_total_"+i).val());
				var precioTotalArticuloSinDescuento = $("#articulo_precio_total_sin_descuento_"+i).val().trim() === "" ? 0.0 : parseFloat($("#articulo_precio_total_sin_descuento_"+i).val());
				total += precioTotalArticulo;
				cantidadArticulos += parseInt($("#cantidad_articulo_"+i).val().trim() === "" ? 0 : $("#cantidad_articulo_"+i).val().trim());
				
				//Valoramos exentos y retenciones
				var isExento = $("#exento_articulo_"+i).val() === '1' ? true : false;
				var aplicaRetencion = $("#retencion_articulo_"+i).val() === '0' ? true : false;
						
				if(!isExento){
						iva += precioTotalArticulo - (precioTotalArticulo / ( 1 + (_PORCENTAJE_IVA / 100)));
						ivaSinDescuento += precioTotalArticuloSinDescuento - (precioTotalArticuloSinDescuento / ( 1 + (_PORCENTAJE_IVA / 100)));
						//Si no es exento quiere decir que puede o no aplicar retencion
						if(aplicaRetencion){
								
								//Obtenemos el precio por unidad del cliente final
								var precioUnidadClienteFinal = $("#articulo_precio_unidad_final_"+i).val().trim() === "" ? 0.0 : parseFloat($("#articulo_precio_unidad_final_"+i).val().trim());
								//Obtenemos la cantidad de articulos
								var cantidad = $("#cantidad_articulo_"+i).val().trim() === "" ? 0 : $("#cantidad_articulo_"+i).val().trim();
										cantidad = parseInt(cantidad);
								//Obtenemos el costo final
								var precioTotalClienteFinalDeArticulo = precioUnidadClienteFinal * cantidad;
								var ivaArticuloClienteFinal = precioTotalClienteFinalDeArticulo - (precioTotalClienteFinalDeArticulo / ( 1 + (_PORCENTAJE_IVA / 100)));
								retencion += ivaArticuloClienteFinal - ivaSinDescuento;
						}
				}
		}
		
		if(!_APLICA_RETENCION){
				retencion = 0;
		}
		
		montoSinIVA = total / ( 1 + (_PORCENTAJE_IVA / 100)); 
		total +=  retencion;
		
		//console.log("Total: "+total+" IVA: "+iva+" Retencion: "+retencion+" "+" Costo: "+montoSinIVA);
		
		
		
		$("#costo").val(montoSinIVA.format(_CANTIDAD_DECIMALES, 3, '.', ','));
		$("#iva").val(iva.format(_CANTIDAD_DECIMALES, 3, '.', ','));
		$("#retencion").val(retencion.format(_CANTIDAD_DECIMALES, 3, '.', ','));
		$("#costo_total").val(total.format(_CANTIDAD_DECIMALES, 3, '.', ','));
		$("#cant_total_articulos").html(cantidadArticulos);
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
												+"<input type='hidden' id='exento_articulo_"+siguienteFila+"' />"
												+"<input type='hidden' id='retencion_articulo_"+siguienteFila+"' />"
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
											+"<td>"
												+"<div class='articulo_specs' id='descuento_articulo_muestra_"+siguienteFila+"'></div>"
												+"<input id='descuento_articulo_"+siguienteFila+"' type='hidden'/>"
											+"</td>"
											+"<td>"
												+"<div class='precio_articulo' id='articulo_precio_unidad_muestra_"+siguienteFila+"'></div>"
												+"<input id='articulo_precio_unidad_"+siguienteFila+"' type='hidden'/>"
												+"<input id='articulo_precio_unidad_final_"+siguienteFila+"' type='hidden'/>"
											+"</td>"
											+"<td>"
												+"<div class='precio_articulo' id='articulo_precio_total_muestra_"+siguienteFila+"'></div>"
												+"<input id='articulo_precio_total_"+siguienteFila+"' type='hidden'/>"
												+"<input id='articulo_precio_total_sin_descuento_"+siguienteFila+"' type='hidden'/>"
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