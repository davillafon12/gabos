var _CODIGO_ARTICULO = -1; 

$(function(){
	//Tooltip foto
	agregarTooltip("#foto_thumb");

	//Numeric mask
	$("#articulos_cantidad").numeric();
	$("#articulos_cantidad_defectuoso").numeric();
	$("#descuento").numeric();

	//Before submit
	$('#actualizar_articulos_form').submit(function() {
		if(validarPrecios()){
			if(verificarCantidad()){
				if(verificarCantidadDefectuosa()){
					if(verificarDescuento()){
						if(verificarCodigoCabys()){
							return true;
						}else{
							notyMsg('¡Debe ingresar un código Cabys válido!', 'error');
							return false;
						}
					}else{
						notyMsg('¡Descuento ingresado no es válido!', 'error');
						return false;
					}
				}else{
					notyMsg('¡Cantidad defectuosa ingresada no es válida!', 'error');
					return false;
				}
			}else{
				notyMsg('¡Cantidad ingresada no es válida!', 'error');
				return false;
			}
		}else{
			notyMsg('¡Alguno de los precios tiene un mal formato!', 'error');
			return false;
		}
	});

	$( "#busqueda_codigo_cabys" ).autocomplete({
		source: location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/articulos/editar/getCabysForName',
		minLength: 3,
		select: function( event, ui ) {
			console.log(ui);
			$("#codigo_cabys,#codigo_cabys_display").val(ui.item.id);
			$("#impuesto_cabys").val(ui.item.impuesto);
		}
	});

	$("#esCombo").change(function(e){
		if($("#esCombo").is(":checked")){
			habilitarCampos();
		}else{
			deshabilitarCampos();
		}
	});

	$(".input_codigo_articulo").keyup(cargarArticulo);
	$(".cantidad_articulo").keyup(validarCantidad);

	_CODIGO_ARTICULO = $("#articulo_codigo").val();

	if($("#esCombo").is(":checked")){
		habilitarCampos();
	}
});

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

function validarPrecios(){
	if(!isNumber($("#costo").val())){return false;}
	if(!isNumber($("#precio1").val())){return false;}
	if(!isNumber($("#precio2").val())){return false;}
	if(!isNumber($("#precio3").val())){return false;}
	if(!isNumber($("#precio4").val())){return false;}
	if(!isNumber($("#precio5").val())){return false;}
	return true;
}

function verificarCantidad(){
	if(!isNumber($("#articulos_cantidad").val())){return false;}
	if($("#articulos_cantidad").val()<0){return false;}
	return true;
}

function verificarCantidadDefectuosa(){
	if(!isNumber($("#articulos_cantidad_defectuoso").val())){return false;}
	if($("#articulos_cantidad_defectuoso").val()<0){return false;}
	return true;
}

function verificarDescuento(){
	if(!isNumber($("#descuento").val())){return false;}
	if($("#descuento").val()<0){return false;}
	if($("#descuento").val()>100){return false;}

	if(!isNumber($("#costo_d").val())){return false;}
	if($("#costo_d").val()<0){return false;}
	if($("#costo_d").val()>100){return false;}

	if(!isNumber($("#precio1_d").val())){return false;}
	if($("#precio1_d").val()<0){return false;}
	if($("#precio1_d").val()>100){return false;}

	if(!isNumber($("#precio2_d").val())){return false;}
	if($("#precio2_d").val()<0){return false;}
	if($("#precio2_d").val()>100){return false;}

	if(!isNumber($("#precio3_d").val())){return false;}
	if($("#precio3_d").val()<0){return false;}
	if($("#precio3_d").val()>100){return false;}

	if(!isNumber($("#precio4_d").val())){return false;}
	if($("#precio4_d").val()<0){return false;}
	if($("#precio4_d").val()>100){return false;}

	if(!isNumber($("#precio5_d").val())){return false;}
	if($("#precio5_d").val()<0){return false;}
	if($("#precio5_d").val()>100){return false;}

	return true;
}

function verificarCodigoCabys(){
	if(!isNumber($("#codigo_cabys").val())){return false;}
	return true;
}

function habilitarCampos(){
	$(".input_codigo_articulo").prop('disabled', false);
	$(".cantidad_articulo").prop('disabled', false);
}

function deshabilitarCampos(){
	$(".input_codigo_articulo").prop('disabled', true);
	$(".cantidad_articulo").prop('disabled', true);
}

function cargarArticulo(event){
	var elementId = event.target.id;
	var fila = elementId.replace("articulo_","");
	var contenido = $("#"+elementId).val().trim();
	var descripcion = $("#descripcion_articulo_"+fila).html().trim();		
		
	if(event.which == 13) {
        if(contenido !== ""){
			if(_CODIGO_ARTICULO == contenido){
				notyMsg("Artículo es el mismo al combo que desea agregar", 'warning');
				return;
			}

			if(descripcion !== ""){
        		//Nos movemos a cantidad de articulo
        		$("#cantidad_articulo_"+fila).select();
        	}else{
				if(verificarSiCodigoYaFueIngresado(contenido)){
					notyMsg("Artículo ya fue ingresado", 'warning');
					return;
				}			
				
			    ajaxArticulo(contenido, fila);
        	}
        	return false;
        }    
    }
    
    if(contenido === ""){
		resetFila(fila, true);
		return false;
    }
}

function cargarInfoArticulo(articulo, fila){
	$("#descripcion_articulo_"+fila).html(articulo.descripcion);
	$("#cantidad_articulo_"+fila).val(1);
}

function verificarFormulario(event){
	if($("#esCombo").is(":checked") && getCantidadArticulosCombo() <= 0){
		notyMsg("No hay artículos agregados al combo, por favor agregar algunos antes de actualizar", 'error');
		return;
	}


	$("#actualizar_articulos_form").submit();
}

function getCantidadArticulosCombo(){
	var cantidad = 0;
	$(".input_codigo_articulo").each(function(index, value){
		var fila = $(value).attr("id").replace("articulo_","");
		var codigo = $(value).val().trim();
		var descripcion = $("#descripcion_articulo_" + fila).html().trim();
		
		if(codigo !== "" && descripcion !== ""){
			cantidad++;
		}else if(codigo !== "" && descripcion == ""){
			$(value).val("");
		}

	});
	return cantidad;
}

function ajaxArticulo(codigo, fila){
	doAjax('/clientes/otros/getArticulo', 'POST', false, {codigo:codigo}, 'json', function(data){
			if(data.status === "success"){
				if(data.error === "6"){
					resetFila(fila, false);
				}else{
					cargarInfoArticulo(data, fila);
				}
			}else{
				if(data.error === "6"){
					notyMsg("Producto no existe", 'error');
					resetFila(fila, false);
				}else{
					notyMsg(data.error, 'error');
				}				
			}
	}, function(e){
		console.error(e);
			notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
	});
}


function validarCantidad(event){
	var elementId = event.target.id;
	var fila = elementId.replace("cantidad_articulo_","");
	var contenido = $("#"+elementId).val();
	
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
	
	
	if(event.which == 13){
			moverseASiguienteFila(fila);
	}
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
									+"<input id='articulo_"+siguienteFila+"' tabindex='"+tabNumber+"'  class='input_codigo_articulo' autocomplete='off' name='codigo_articulo_"+siguienteFila+"[]' type='text'  />"
							+"</td>"
							+"<td>"
									+"<div class='articulo_specs' id='descripcion_articulo_"+siguienteFila+"'></div>"
									+"<div class='tooltip_imagen_articulo' id='tooltip_imagen_articulo_"+siguienteFila+"'></div>"
							+"</td>"
							+"<td>"
									+"<input id='cantidad_articulo_"+siguienteFila+"' class='cantidad_articulo_"+siguienteFila+"[]' autocomplete='off' type='number' min='1'  />"									
							+"</td>"
						+"</tr>";
		$("#cuerpo_tabla_articulos").append(filaHTML);
		$("#articulo_"+siguienteFila).select();
		$("#articulo_"+siguienteFila).keyup(cargarArticulo);
		$("#cantidad_articulo_"+siguienteFila).keyup(validarCantidad);
}

function verificarSiCodigoYaFueIngresado(codigo){
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
		{ if(errorCallback!==null){errorCallback(errorThrown); } }
	});
}

function resetFila(fila, resetCodigo){
	if(resetCodigo){
		$("#articulo_"+fila).val("");
	}
	$("#descripcion_articulo_"+fila).html("");
	$("#cantidad_articulo_"+fila).val("");
}