$(function(){
	
	//Numeric mask
	$("#articulos_cantidad").numeric();
	$("#articulos_cantidad_defectuoso").numeric();
	$("#descuento").numeric();
	
	//Before submit
	$('#registrar_articulos_form').submit(function() {
		if(validarPrecios()){
			if(verificarCantidad()){
				if(verificarCantidadDefectuosa()){
					if(verificarDescuento()){
						return true;
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
});

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