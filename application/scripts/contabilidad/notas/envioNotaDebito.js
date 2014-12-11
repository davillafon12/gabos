function generarNotaDebito(){
	if(validarNotaDebito()){
		enviarNota();
	}
}

function validarNotaDebito(){
	return tieneProductos();
}

function tieneProductos(){
	filas = $('#tabla_productos tr').length;
	for(i=1; i<filas; i++){
		descripcion = $("#descripcion_articulo_"+i).html();
		if(descripcion.trim()!=""){
			return true;
		}
	}
	notyMsg('Nota vacía, debe ingresar al menos un producto', 'error');
	return false;
}

function obtenerJSON(){
	productos = [];
	filas = $('#tabla_productos tr').length;
	for(i=1; i<filas; i++){
		codigo = $("#articulo_"+i).val();
		cantidad = $("#cantidad_articulo_"+fila).val();
		productos.push({co:codigo, ca:cantidad});
	}
	return {productos:JSON.stringify(productos)};
}

function enviarNota(){
	$.ajax({
		url : location.protocol+'//'+document.domain+'/contabilidad/notas/generarNotaDebito',
		type: "POST",		
		async: false,
		data: obtenerJSON(),				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');				
				if(informacion[0].status==="error"){
					//manejarErroresEnvioNotas(informacion[0].error);
				}else if(informacion[0].status==="success"){
					
				}
			}catch(e){
				//alert(e);
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}
