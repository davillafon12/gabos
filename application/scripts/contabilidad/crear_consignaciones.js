var _SUCURSAL_ENTREGA_SELECCIONADA = "-1";
var _SUCURSAL_RECIBE_SELECCIONADA = "-1";
var _ARRAY_ARTICULOS = [];
var anchoImpresion = 1024;
var alturaImpresion = 768;
var _CLIENTE_RETENCION = "1";
var _CLIENTE_EXENTO = "0";

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
		_CLIENTE_RETENCION = articulo.retencion_cliente;
		_CLIENTE_EXENTO = articulo.exento_cliente;
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
														$('#envio_consignacion').bPopup({
															modalClose: false
														});											
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
															doAjax("/contabilidad/consignaciones/consignarArticulos", "POST", true, parametros, "JSON", resultadoConsignacion, function(){
																	notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
															});
													}
											}
						});
				}
		}
}

function resultadoConsignacion(data){
	$('#envio_consignacion').bPopup().close();	
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
		_ARRAY_ARTICULOS = [];
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
		var costo_sin_IVA_factura = 0.0;
		var costo_total_factura = 0.0;
		var IVA_Factura = 0.0;
		var costo_cliente_final = 0.0;
		var costo_retencion = 0.0;
		
		var cantidadFilas = $("#tabla_productos tr").length - 1;
		var cantidadArticulos = 0;
		
		for(var i=1; i <= cantidadFilas; i++){

			if(!$.isNumeric($("#cantidad_articulo_"+(i)).val()))
				continue;

				var a = {cantidad:parseInt($("#cantidad_articulo_"+(i)).val()), 
					precio_unitario: parseFloat($("#articulo_precio_unidad_"+(i)).val()), 
					descuento: ($.isNumeric($("#descuento_articulo_"+(i)).val()) ? parseFloat($("#descuento_articulo_"+(i)).val()) : 0), 
					no_retencion: $("#retencion_articulo_"+(i)).val(), 
					precio_final: parseFloat($("#articulo_precio_unidad_final_"+(i)).val().replace(/,/g, "")), 
					exento: $("#exento_articulo_"+(i)).val()};
				
				var aplicaRetencion = true;
				if(_CLIENTE_EXENTO=="1" || _APLICA_RETENCION != 1 || _CLIENTE_RETENCION=="1"){
					aplicaRetencion = false;
				}
				
				var detalle = getDetalleLinea(a, aplicaRetencion);
				IVA_Factura += detalle.iva;
				costo_sin_IVA_factura += detalle.subtotal;
				costo_retencion += detalle.retencion;
				costo_cliente_final += detalle.costo_final;

				cantidadArticulos += a.cantidad;
		}

		costo_total_factura = IVA_Factura + costo_sin_IVA_factura + costo_retencion;

		$("#costo").val(costo_sin_IVA_factura.format(_CANTIDAD_DECIMALES, 3, '.', ','));
		$("#iva").val(IVA_Factura.format(_CANTIDAD_DECIMALES, 3, '.', ','));
		$("#retencion").val(costo_retencion.format(_CANTIDAD_DECIMALES, 3, '.', ','));
		$("#costo_total").val(costo_total_factura.format(_CANTIDAD_DECIMALES, 3, '.', ','));
		$("#cant_total_articulos").html(cantidadArticulos);
}

function getDetalleLinea(a, aplicaRetencion){
    if(typeof aplicaRetencion === "undefined")
        aplicaRetencion = true;
    
    var linea = {};

    // CANTIDAD
    var cantidad = parseFloat(a.cantidad);
    
    // PRECIO UNITARIO
    var precioUnitarioSinIVA = removeIVA(parseFloat(a.precio_unitario));

    // MONTO TOTAL
    var precioTotalSinIVA = cantidad*precioUnitarioSinIVA;

    // DESCUENTO
    var descuentoPrecioSinIva = 0;
    if(parseFloat(a.descuento) > 0){
        descuentoPrecioSinIva = (precioTotalSinIVA * (parseFloat(a.descuento) / 100));
    }
	
     // SUBTOTAL
    var subTotalSinIVA = (precioTotalSinIVA - descuentoPrecioSinIva);
    linea.subtotal = parseFloat(subTotalSinIVA);

    // IMPUESTOS
    var iva = getIVAvalue_float();
    linea.iva = parseFloat((subTotalSinIVA * iva));
	linea.retencion = 0;
	linea.costo_final = 0;
    if(a.no_retencion == "0" && aplicaRetencion){
        // Para le retencion NO SE TOMA EN CUENTA EL DESCUENTO
        var precioFinalUnitarioSinIVA = removeIVA(parseFloat(a.precio_final));
        var precioFinalTotalSinIVA = cantidad*precioFinalUnitarioSinIVA;
        var montoDeImpuesto = precioFinalTotalSinIVA * iva;
        linea.retencion = montoDeImpuesto - linea.iva;
        linea.costo_final = (parseFloat(a.precio_final) - (parseFloat(a.precio_final) * (parseFloat(a.descuento) / 100))) * cantidad;
    }else{
		linea.costo_final = cantidad * parseFloat(a.precio_unitario);
	}
    
    if(a.exento == 1){ // Es exento
        linea.iva = 0;
        linea.retencion = 0;
    }

    return linea;
}

function removeIVA(price){
    var iva = getIVAvalue_float();
    return (price/(1+iva)); 
}

function getIVAvalue_float(){	
	var impuesto_venta_float = parseFloat(_PORCENTAJE_IVA);
	return impuesto_venta_float/100; 
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
		$(".tooltip_imagen_articulo").css("display","none");
		$(this).next().fadeIn("fast").css({
			left: eleOffset.left + 100,
			top: eleOffset.top - 100
		});
		
	}).mouseout(function(){
		$(this).next().hide();
		$(".tooltip_imagen_articulo").css("display","none");

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