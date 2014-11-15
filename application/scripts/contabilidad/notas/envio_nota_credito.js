function enviarNotaCredito(){
	if(validarEnvio()){
	
	}
}

function validarEnvio(){
	if(validarCedulaNombre()){
		cantFacturas = $('#tabla_facturas tr').length;
		if(cantFacturas>1){
			cantProductos = $('#tabla_productos tr').length;
			cantProductosSeleccionados = $('#tabla_productos_seleccionados tr').length;
			if(cantProductos>1||cantProductosSeleccionados>1){				
				if(cantProductosSeleccionados>1){
					if(validarCantidadDevolver(cantProductosSeleccionados-1)){
						if(validarCantidadDevolverConCantidadReal(cantProductosSeleccionados-1)){
							if(existeFacturaAplicar){
							
							}else{
								notyMsg('¡No se ha ingresado o no existe la factura a aplicar la nota!','error');
								return false;
							}
						}else{
							notyMsg('¡Alguna cantidad de devolución no concuerda con la cantidad original!','error');
							return false;
						}					
					}else{
						notyMsg('¡Alguna cantidad de devolución es incorrecta!','error');
						return false;
					}
				}else{
					notyMsg('¡No se han seleccionado productos para la nota!','error');
					return false;
				}
			}else{
				notyMsg('¡No se han cargado productos!','error');
				return false;
			}
		}else{
			notyMsg('¡No se han cargado facturas!','error');
			return false;
		}
	}else{
		return false;
	}
	return true;
}

function validarCedulaNombre(){
	cedula = $("#cedula").val();
	nombre = $("#nombre").val();
	if(cedula.trim()===''){
		notyMsg('¡Cédula ingresada no es válida!','error');
		return false;
	}
	if(nombre.trim()==='No existe cliente!!!'||nombre.trim()===''){
		notyMsg('¡Nombre ingresado no es válido!','error');
		return false;
	}
	return true;
} 

function validarCantidadDevolver(cantidad){
	for(i=0; i<cantidad; i++){
		defectuoso = $("#celda_cantidad_defectuoso_"+productosCreditar[i]).html();
		bueno = $("#celda_cantidad_buena_"+productosCreditar[i]).html();
		//Verificamos que sean numeros
		if(!isNumber(defectuoso)||!isNumber(bueno)){return false;}
		//Si lo son revisamos que no sean menores a cero
		else if(parseFloat(defectuoso)<0||parseFloat(bueno)<0){return false;}
	}
	return true;
}

function validarCantidadDevolverConCantidadReal(cantidad){
	for(i=0; i<cantidad; i++){
		defectuoso = parseFloat($("#celda_cantidad_defectuoso_"+productosCreditar[i]).html());
		bueno = parseFloat($("#celda_cantidad_buena_"+productosCreditar[i]).html());
		original = parseFloat($("#p_cantidad_original_"+productosCreditar[i]).html());
		//Si la cantidad original es menor a la suma de defectuoso y bueno
		if(original<(defectuoso+bueno)){
			return false;
		}
	}
	return true;
}



