var _SUCURSAL_ENTREGA = -1;
var _SUCURSAL_RECIBE = -1;
var _ARRAY_ARTICULOS = [];
var _SUCURSAL_RECIBE_ES_EXENTA = false;

$(window).ready(function(){
	$("#sucursal_entrega").change(cargarArticulosConsignados);
	$("#sucursal_recibe").change(cargarArticulosConsignados);
	
	$("#crear_factura_consignacion").click(preguntaCrearFactura);

	$("#selector_general_articulos").click(seleccionarCheckboxes);
});

function cargarArticulosConsignados(e){
	switch(e.target.id.trim()){
		case 'sucursal_entrega':
			_SUCURSAL_ENTREGA = $("#sucursal_entrega").val();
		break;
		case 'sucursal_recibe':
			_SUCURSAL_RECIBE = $("#sucursal_recibe").val();
		break;
	}
	
	if(_SUCURSAL_ENTREGA === -1 || _SUCURSAL_RECIBE === -1){
		resetAllFields();
	}else{
		doAjax('/contabilidad/consignaciones/getArticulosEnListaConsignados', 'POST', false, {SE:_SUCURSAL_ENTREGA,SR:_SUCURSAL_RECIBE}, 'JSON', dibujarArticulosEnTabla, resetAllFields);
	}
	
}

function dibujarArticulosEnTabla(data){
	if(data.status.trim() === 'success'){
		var html = "";
		$.each(data.articulos, function(index, articulo){
			html += getArticuloHTML(articulo, index);
		});
		$("#cuerpo_tabla_articulos").html(html);
		$(".cantidad-facturar").numeric();
                
                _SUCURSAL_RECIBE_ES_EXENTA = data.isExento === "1";
	}else{
		resetAllFields();
		notyMsg(data.error.trim(), "error");
	}
}

function getArticuloHTML(articulo, numero_fila){
        var decimales = 2; //_CONFIG.cantidad_decimales
	var checkbox = "<td style='width: 20px;'><input type='checkbox' value='"+numero_fila+"' class='articulos-seleccionados' meta-numero-fila='"+numero_fila+"' onchange='actualizarTotal()'/></td>";
	var codigo = "<td><div class='articulo_specs' style='text-align:right;'>"+articulo.Codigo+"</div><input type='hidden' id='articulo_"+numero_fila+"' value='"+articulo.Id+"'/></td>";
	var descripcion = "<td><div class='articulo_specs'>"+articulo.Descripcion+"</div></td>";
	var cantidad = "<td><div class='articulo_specs' style='text-align:center;' id='articulo_cantidad_consignado_"+numero_fila+"'>"+articulo.Cantidad+"</div></td>";
        var bodega = "<td><div class='articulo_specs' style='text-align:center;' id='articulo_cantidad_bodega_"+numero_fila+"'>"+articulo.Bodega+"</div></td>";
	var cantidadFacturar = "<td><input type='text' class='articulo_specs cantidad-facturar' style='width: 60px; text-align:center;' id='articulo_cantidad_"+numero_fila+"' value='0' onchange='actualizarTotal()'/></td>";
	var descuento = "<td><div class='articulo_specs' style='text-align:center;' id='descuento_"+numero_fila+"'>"+parseFloat(articulo.Descuento).toFixed(decimales)+"</div></td>";
	var exento = articulo.Exento.trim() === '0' ? '' : '&#10004;';
	var retencion = articulo.Retencion.trim() === '1' ? '&#10004;' : '';
		exento = "<td><div class='articulo_specs' style='text-align:center;' id='exento_"+numero_fila+"' is-exento='"+articulo.Exento.trim()+"'>"+exento+"</div></td>";
		retencion = "<td><div class='articulo_specs' style='text-align:center;'  id='retencion_"+numero_fila+"' no-retencion='"+articulo.Retencion.trim()+"'>"+retencion+"</div></td>";
	var precioUnidad = "<td><div class='articulo_specs' style='text-align:right;' id='precio_unidad_"+numero_fila+"'>"+parseFloat(articulo.Precio_Unidad).toFixed(decimales)+"</div><span id='precio_final_"+numero_fila+"' style='display:none'>"+parseFloat(articulo.Precio_Final).toFixed(decimales)+"</span></td>";
	var precioTotal = "<td><div class='articulo_specs' style='text-align:right;' id='precio_total_"+numero_fila+"'>"+parseFloat(articulo.Precio_Total).toFixed(decimales)+"</div></td>";
	
	return "<tr>"+checkbox+codigo+descripcion+cantidad+bodega+cantidadFacturar+descuento+exento+retencion+precioUnidad+precioTotal+"</tr>";
}



function resetAllFields(){
	$("#cuerpo_tabla_articulos").html("");
}

// n=decimales | x=centenares(Grupos de numeros) | s=divisor de centenares | c=divisor procentaje 
Number.prototype.format = function(n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
}

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

function preguntaCrearFactura(){
	var debeDevolverArticulos = $('#devolver_consignados_check').is(':checked');
	if(validarSucursales()){
		if(validarArticulos()){
			if(debeDevolverArticulos){
				if(soloHayQueDevolver()){
					$.prompt("¡Esto devolverá a inventario todos los artículos seleccionados, NO generará factura!", {
						title: "¿Esta seguro que solo desea devolver artículos?",
						buttons: { "Si, estoy seguro": true, "Cancelar": false },
						submit:function(e,v,m,f){
												if(v){
														var parametros = {
																			sucursalRecibe :  $("#sucursal_recibe").val().trim(),
																			sucursalEntrega:  $("#sucursal_entrega").val().trim(),
																			articulos      :  JSON.stringify(_ARRAY_ARTICULOS),
																			devolver 	   :  debeDevolverArticulos ? 1 : 0,
																			soloDevolver   :  1
																	};
														doAjax("/contabilidad/consignaciones/crearFactura", "POST", false, parametros, "JSON", resultadoCreacion, function(){
																notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
														});
												}
										}
					});
				}else{
					$.prompt("¡Esto facturará los artículos con cantidad y devolverá a inventario, de quien consigna, las unidades restantes!", {
						title: "¿Esta seguro que desea crear la factura y devolver restantes?",
						buttons: { "Si, estoy seguro": true, "Cancelar": false },
						submit:function(e,v,m,f){
												if(v){
														var parametros = {
																			sucursalRecibe :  $("#sucursal_recibe").val().trim(),
																			sucursalEntrega:  $("#sucursal_entrega").val().trim(),
																			articulos      :  JSON.stringify(_ARRAY_ARTICULOS),
																			devolver 	   :  debeDevolverArticulos ? 1 : 0,
																			soloDevolver   :  0
																	};
														doAjax("/contabilidad/consignaciones/crearFactura", "POST", false, parametros, "JSON", resultadoCreacion, function(){
																notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
														});
												}
										}
					});
				}
			}else{
				$.prompt("¡Esto facturará los artículos con cantidad!", {
							title: "¿Esta seguro que desea crear la factura?",
							buttons: { "Si, estoy seguro": true, "Cancelar": false },
							submit:function(e,v,m,f){
													if(v){
															var parametros = {
																				sucursalRecibe :  $("#sucursal_recibe").val().trim(),
																				sucursalEntrega:  $("#sucursal_entrega").val().trim(),
																				articulos      :  JSON.stringify(_ARRAY_ARTICULOS),
																				devolver 	   :  debeDevolverArticulos ? 1 : 0,
																				soloDevolver   :  0
																		};
															doAjax("/contabilidad/consignaciones/crearFactura", "POST", false, parametros, "JSON", resultadoCreacion, function(){
																	notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
															});
													}
											}
						});
			}
		}
	}
}

function soloHayQueDevolver(){
	var articulosSeleccionados = $(".articulos-seleccionados:checked");
	for(var i = 0; articulosSeleccionados.length > i; i++){
		var numero_fila = $(articulosSeleccionados[i]).attr("meta-numero-fila");
		if($("#articulo_cantidad_"+numero_fila).val()>0){
			return false;
		}
	}
	return true;
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

function validarArticulos(){
	if($("#cuerpo_tabla_articulos").html().trim() === ''){
		notyMsg("No hay artículos cargados", "error");
		return false;
	}
	var articulosSeleccionados = $(".articulos-seleccionados:checked");
	console.log(articulosSeleccionados);
	if(articulosSeleccionados.length == 0){
		notyMsg("No hay artículos seleccionados a facturar", "error");
		return false;
	}
	if(!cargarArrayArticulos(articulosSeleccionados)){
		notyMsg("Alguna cantidad a facturar no tiene formato numérico", "error");
		return false;
	}
	return true;
}

function cargarArrayArticulos(articulosSeleccionados){
		_ARRAY_ARTICULOS = [];
		var cantidadFilas = articulosSeleccionados.length;
		for(var i=0; i < cantidadFilas; i++){
				var fila = $(articulosSeleccionados[i]).attr('meta-numero-fila');
				var codigo = $("#articulo_"+fila).val().trim();
				var cantidadConsignada = $("#articulo_cantidad_consignado_"+fila).html().trim();
				var cantidad = $("#articulo_cantidad_"+fila).val().trim();
				
				
				
				if($.isNumeric(cantidad) && parseInt(cantidad) >= 0 && parseInt(cantidadConsignada) >= parseInt(cantidad)){
					_ARRAY_ARTICULOS.push({codigo:codigo, cantidad:cantidad});
				}else{
					_ARRAY_ARTICULOS = [];
					return false;
				}
				
		}
		return true;
}

function resultadoCreacion(data){
	if(data.status.trim() === 'success'){
		doAjax('/contabilidad/consignaciones/getArticulosEnListaConsignados', 'POST', false, {SE:_SUCURSAL_ENTREGA,SR:_SUCURSAL_RECIBE}, 'JSON', dibujarArticulosEnTabla, resetAllFields);
		notyMsg('Se creó con éxito una factura pendiente', "success");
	}else{
		notyMsg(data.error, "error");
	}
}

function seleccionarCheckboxes(e){
	var seleccion = $(e.target).is(':checked');
	$(".articulos-seleccionados").each(function(){
		this.checked = seleccion;
	});
	
	
}

function actualizarTotal(){
    var total = 0;
    $.each($(".articulos-seleccionados:checked"), function(index, elem){
        var fila = $(elem).attr("meta-numero-fila");
        var cantidad = parseInt($("#articulo_cantidad_"+fila).val());
        var precioUnidad = parseFloat($("#precio_unidad_"+fila).text());
        var precioUnidadFinal = parseFloat($("#precio_final_"+fila).text());
        var descuento = parseFloat($("#descuento_"+fila).text());
        var isExento = $("#exento_"+fila).attr("is-exento") == 1;
        var retencion = $("#retencion_"+fila).attr("no-retencion") == 0;
        var precioConDescuento = precioUnidad - (precioUnidad * (descuento/100));
        var precioFinalConDescuento = precioUnidadFinal - (precioUnidadFinal * (descuento/100));
        var ivaFactor = parseFloat(_CONFIG.iva);
        var precioSinIva = precioConDescuento/(1+(ivaFactor/100)); 
        var precioFinalSinIva = precioFinalConDescuento/(1+(ivaFactor/100));
        var iva = precioConDescuento - precioSinIva;
        var ivaFinal = precioFinalConDescuento - precioFinalSinIva;
        
        if(retencion && _CONFIG.aplicar_retencion === "1"){
            iva = ivaFinal;
        }
        
        if(isExento || _SUCURSAL_RECIBE_ES_EXENTA){
            iva = 0;
        }
        
        total += (precioSinIva+iva)*cantidad;
    });
    $("#total_monto").text(total.toFixed(2));
}