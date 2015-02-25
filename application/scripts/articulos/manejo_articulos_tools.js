
$(function(){
	$("#descuento").numeric();
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
		url : location.protocol+'//'+document.domain+'/articulos/editar/agregarDescuentoMasivo',
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
	}	
}

function cargarArticulosSucursal(){
	$("#tabla_editar").dataTable().fnDraw();	
}

	
	
	
	