var _SUCURSAL_ENTREGA = -1;
var _SUCURSAL_RECIBE = -1;
var _ARRAY_ARTICULOS = [];

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
	}else{
		resetAllFields();
		notyMsg(data.error.trim(), "error");
	}
}

function getArticuloHTML(articulo, numero_fila){
	var checkbox = "<td style='width: 20px;'><input type='checkbox' value='"+numero_fila+"' class='articulos-seleccionados' meta-numero-fila='"+numero_fila+"'/></td>";
	var codigo = "<td><div class='articulo_specs' style='text-align:right;'>"+articulo.Codigo+"</div><input type='hidden' id='articulo_"+numero_fila+"' value='"+articulo.Id+"'/></td>";
	var descripcion = "<td><div class='articulo_specs'>"+articulo.Descripcion+"</div></td>";
	var cantidad = "<td><div class='articulo_specs' style='text-align:center;' id='articulo_cantidad_consignado_"+numero_fila+"'>"+articulo.Cantidad+"</div></td>";
	var cantidadFacturar = "<td><input type='text' class='articulo_specs cantidad-facturar' style='width: 60px; text-align:center;' id='articulo_cantidad_"+numero_fila+"' value='0'/></td>";
	var descuento = "<td><div class='articulo_specs' style='text-align:center;'>"+articulo.Descuento+"</div></td>";
	var exento = articulo.Exento.trim() === '0' ? '' : '&#10004;';
	var retencion = articulo.Retencion.trim() === '1' ? '&#10004;' : '';
		exento = "<td><div class='articulo_specs' style='text-align:center;'>"+exento+"</div></td>";
		retencion = "<td><div class='articulo_specs' style='text-align:center;'>"+retencion+"</div></td>";
	var precioUnidad = "<td><div class='articulo_specs' style='text-align:right;'>"+articulo.Precio_Unidad+"</div></td>";
	var precioTotal = "<td><div class='articulo_specs' style='text-align:right;'>"+articulo.Precio_Total+"</div></td>";
	
	return "<tr>"+checkbox+codigo+descripcion+cantidad+cantidadFacturar+descuento+exento+retencion+precioUnidad+precioTotal+"</tr>";
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
				$.prompt("¡Esto facturará los artículos con cantidad y devolverá a inventario, de quien consigna, las unidades restantes!", {
							title: "¿Esta seguro que desea crear la factura y devolver restantes?",
							buttons: { "Si, estoy seguro": true, "Cancelar": false },
							submit:function(e,v,m,f){
													if(v){													
															var parametros = {
																				sucursalRecibe :  $("#sucursal_recibe").val().trim(),
																				sucursalEntrega:  $("#sucursal_entrega").val().trim(),
																				articulos      :  JSON.stringify(_ARRAY_ARTICULOS),
																				devolver 	   :  debeDevolverArticulos ? 1 : 0																			
																		};
															doAjax("/contabilidad/consignaciones/crearFactura", "POST", false, parametros, "JSON", resultadoCreacion, function(){
																	notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
															});
													}
											}
						});
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
																				devolver 	   :  debeDevolverArticulos ? 1 : 0																			
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