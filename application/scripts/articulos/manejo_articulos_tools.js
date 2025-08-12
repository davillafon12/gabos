var hayArticulosTemporales = false; //Se utilzia para valorar que realmente se hayan cargado productos


$(function(){
	$("#descuento").numeric();
	actualizarProductosTemporales();
});

function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

function notyMsg(Mensaje, tipo){
	n = noty({
			   layout: 'topRight',
			   text: Mensaje,
			   type: tipo,
			   timeout: 4000
			});
}

//Reset todo los checkboxes
function resetCheckBox(){
	$('tbody tr td input[type="checkbox"]').each(function(){
		$(this).prop('checked', false);
		});
}

function selectAllCheckBox(){
	$('tbody tr td input[type="checkbox"]').each(function(){
		$(this).prop('checked', true);
		});
}

function agregarDescuentoMasivo(){
	descuento = $("#descuento").val();
	if(isNumber(descuento)){
		if(descuento>=0&&descuento<=100){
			seleccionados = getSelectedCheckboxes();
			if(seleccionados.length>0){
				$.prompt("¡Esto agregará el descuento ingresado a todos los artículos seleccionados!", {
						title: "¿Esta seguro que desea realizar esta operación?",
						buttons: { "Si, estoy seguro": true, "Cancelar": false },
						submit:function(e,v,m,f){
													if(v){													
														ingresarDescuento(convertirArray(seleccionados), descuento);
													}
												}
					});	
			}else{
				notyMsg('Debe seleccionar al menos un artículo', 'error');					
			}
		}else{
			notyMsg('Debe ingresar un descuento válido', 'error');	
		}		
	}else{
		notyMsg('Debe ingresar un descuento válido', 'error');		
	}
}

function getSelectedCheckboxes(){
	return $('tbody tr td input[type="checkbox"]:checked');	
}

function convertirArray(array){
	newArray = [];
	for(i=0; i<array.length; i++){
		newArray.push(array[i].value);		
	}
	return newArray;
}
	
function ingresarDescuento(articulos, descuento){
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/articulos/editar/agregarDescuentoMasivo',
		type: "POST",		
		async: false,
		data: {'descuento':descuento, 'articulos':JSON.stringify(articulos), 'sucursal':$("#sucursalListaArticulos").val()},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');				
				if(informacion[0].status==="error"){					
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					$("#tabla_editar").dataTable().fnDraw();
					notyMsg('¡Se agrego el descuento con éxito a los artículos seleccionados!', 'success');
					$("#descuento").val('');
				}
			}catch(e){
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});	
}
	
function manejarErrores(error){
	switch(error){
		case '1':
				notyMsg('¡No se pudo realizar la operación, contacte al administrador!', 'error');
				break;
		case '2':
				notyMsg('¡URL inválida, contacte al administrador!', 'error');
				break;
		case '3':
				notyMsg('¡No se seleccionaron actículos!', 'error');
				break;
		case '3':
				notyMsg('¡Descuento inválido!', 'error');
				break;
		case '4':
				notyMsg('¡Sucursal no existe!', 'error');
				break;
		case '5':
				notyMsg('¡No habían artículos que devolver!', 'error');
				break;
		case '6':
				notyMsg('¡Estado de retención inválido!', 'error');
				break;			
	}	
}

function cargarArticulosSucursal(){
	$("#tabla_editar").dataTable().fnDraw();	
}

function cargarArticulosBodegaSucursal(){
	$("#tabla_editar_bodega").dataTable().fnDraw();	
}

function actualizarProductosTemporales(){
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/articulos/editar/obtenerArticulosTemporales',
		type: "POST",		
		async: false,
		data: {'sucursal':$("#sucursalTemporalesArticulos").val()},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');				
				if(informacion[0].status==="error"){					
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					cargarArticulosTemporales(informacion[0].articulos);
				}
			}catch(e){
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});	
}

function cargarArticulosTemporales(articulos){
	$("#cuerpo_articulos_temporales").html('');
	for(i=0; i<articulos.length; i++){
		$("#cuerpo_articulos_temporales").append("<tr><td>"+articulos[0].Codigo_Articulo+"</td><td>"+articulos[0].Cantidad+"</td></tr>");
	}
	//Si cargo productos
	if(articulos.length>0){
		hayArticulosTemporales = true;
	}else{
		hayArticulosTemporales = false;
	}
}

function devolverProductosTemporales(){
	if(hayArticulosTemporales){
		$.prompt("¡Esto devolverá todos los productos temporales a inventario, esto afectará el inventario! \n Solo ejecutarse cuando sea estrictamente necesario.", {
						title: "¿Esta seguro que desea realizar esta operación?",
						buttons: { "Si, estoy seguro": true, "Cancelar": false },
						submit:function(e,v,m,f){
													if(v){													
														retornarArticulos();
													}
												}
					});
	}else{
		notyMsg('¡No hay artículos que devolver al inventario!', 'error');
	}	
}
	
function retornarArticulos(){
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/articulos/editar/retornarArticulosTemporales',
		type: "POST",		
		async: false,
		data: {'sucursal':$("#sucursalTemporalesArticulos").val()},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');				
				if(informacion[0].status==="error"){					
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					notyMsg('¡Se retornaron los artículos con éxito!', 'success');
					actualizarProductosTemporales();
				}
			}catch(e){
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});	
}
	

function habilitarRetencion(){
		seleccionados = getSelectedCheckboxes();
		if(seleccionados.length>0){
			$.prompt("¡Esto habilitará la retención a todos los artículos seleccionados!", {
					title: "¿Esta seguro que desea realizar esta operación?",
					buttons: { "Si, estoy seguro": true, "Cancelar": false },
					submit:function(e,v,m,f){
											if(v){													
													actualizarRetencion(convertirArray(seleccionados), 0);
											}
									}
				});	
		}else{
			notyMsg('Debe seleccionar al menos un artículo', 'error');					
		}
}

function deshabilitarRetencion(){
		seleccionados = getSelectedCheckboxes();
		if(seleccionados.length>0){
			$.prompt("¡Esto deshabilitará la retención a todos los artículos seleccionados!", {
					title: "¿Esta seguro que desea realizar esta operación?",
					buttons: { "Si, estoy seguro": true, "Cancelar": false },
					submit:function(e,v,m,f){
											if(v){													
													actualizarRetencion(convertirArray(seleccionados), 1);
											}
									}
				});	
		}else{
			notyMsg('Debe seleccionar al menos un artículo', 'error');					
		}
}

function actualizarRetencion(articulos, estado){
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/articulos/editar/actualizarRetencionMasivo',
		type: "POST",		
		async: false,
		data: {'estado':estado, 'articulos':JSON.stringify(articulos), 'sucursal':$("#sucursalListaArticulos").val()},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');				
				if(informacion[0].status==="error"){					
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					$("#tabla_editar").dataTable().fnDraw();
					notyMsg('¡Se actualizó la retención con éxito a los artículos seleccionados!', 'success');
				}
			}catch(e){
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});	
}