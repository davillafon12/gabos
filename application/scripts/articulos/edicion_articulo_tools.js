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
	return true;
}

function verificarCodigoCabys(){
	if(!isNumber($("#codigo_cabys").val())){return false;}
	return true;
}