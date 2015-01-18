//VARIABLES

	var array_pos_rows = [1,2,3,4,5,6,7,8,9,10];

////////////////////////////////////////////////////////////////////////////////////////////////////////


function buscarArticulo(e, value, id){
	fila = id.replace("articulo_","");
	descripcion = $("#descripcion_articulo_"+fila).html();
	
	//Validar eventos
	if(e!=null){
		//Cuando sea Up down left right, no haga nada
		if(e.keyCode == 37||e.keyCode == 38||e.keyCode == 39||e.keyCode == 40||e.keyCode == 107){return false;}
		
		if (e.keyCode == 13) //Enter
		{
			if(descripcion.trim()!="")
			{
				pasarSiguienteFila(fila, false);
				return false;
			}			
		}
	
	
	}	
	
	//Validar si codigo es numerico
	/*if(isNumber(value)){
		getArticulo(fila, value);
	}else{
		resetRow(fila, true);
	}*/
	
	getArticulo(fila, value);
}

function getArticulo(fila, articulo){
	$.ajax({
		url : location.protocol+'//'+document.domain+'/facturas/nueva/getArticuloJSON',
		type: "POST",		
		async: false,
		data: {'cedula':0, 'codigo':articulo},				
		success: function(data, textStatus, jqXHR)
		{
			try{				
				informacion = $.parseJSON('[' + data.trim() + ']');				
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error, fila);
				}else if(informacion[0].status==="success"){					
					setProducto(informacion[0].articulo, fila);
				}
			}catch(e){
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

function resetRow(fila, todaFila){	
	if(todaFila){
		$("#articulo_"+fila).val("");
	}
	$("#descripcion_articulo_"+fila).html("");
	$("#bodega_articulo_"+fila).html("");
	$("#cantidad_articulo_"+fila).val("");	
}

function notyMsg(Mensaje, tipo){
	n = noty({
			   layout: 'topRight',
			   text: Mensaje,
			   type: tipo,
			   timeout: 4000
			});
}

function manejarErrores(error, fila){
	switch(error){
		case '1':
			notyMsg('¡No se pudo tramitar la información, contacte al administrador!','error');
			resetRow(fila, true);
		break;
		case '2':
			notyMsg('¡Error en el envio de la URL, contacte al administrador!','error');
			resetRow(fila, true);
		break;
		case '3':
			notyMsg('¡Código vacío o no es válido!','error');
			resetRow(fila, true);
		break;
		case '4':
			notyMsg('¡Cliente no existe o cédula no es válida!','error');
			resetRow(fila, true);
		break;
		case '5':
			resetRow(fila, false);
		break;
		case '6':
			notyMsg('¡El producto no tiene mas unidades en inventario!','error');
			resetRow(fila, false);
		break;
	}
}

function setProducto(articulo, fila){	
	if(!articuloYaIngresado(articulo.codigo, "articulo_"+fila)){
		$("#descripcion_articulo_"+fila).html(articulo.descripcion);
		$("#bodega_articulo_"+fila).html(articulo.inventario);
		$("#cantidad_articulo_"+fila).val(1);
	}else{
		notyMsg('¡El producto ya fue ingresado en otra fila!','warning');
	}
}

function pasarSiguienteFila(fila, isCantidadField){
	tabla_productos = $("#tabla_productos");	
	cantidadIndices = array_pos_rows.length;  //Obtenemos ultimo indice	
	ultimoID = array_pos_rows[cantidadIndices-1]; //Obtenemos la fila que esta de ultimo lugar	
	if(ultimoID==fila){ //Si la fila que estamos evaluendo es la ultima tons agrega fila
		//agregarFila(-1);
	}	
	
	filaInt = parseInt(fila);
	currentIndex = array_pos_rows.indexOf(filaInt); //Obtenemos la posicion donde se origino el evento
	nextId = array_pos_rows[currentIndex+1]; //Obtenemos la siguiente posicion donde se hara el blur
	if(!isCantidadField){
		doTabCodigoArticulo("cantidad_articulo_"+filaInt);			
	}
	else{
		doTabCodigoArticulo("articulo_"+nextId); //Nos vamos a esa fila
	}	
}

function doTabCodigoArticulo(id){	
	$("#"+id).select();
}

function validarMaxMinCantidad(id){	
	id = id.replace("cantidad_articulo_","");		
	cantidad_validar = $("#cantidad_articulo_"+id).val();
	cantidad_validar = parseInt(cantidad_validar);	
	cantidad_bodega = $("#bodega_articulo_"+id).html();
	cantidad_bodega = parseInt(cantidad_bodega);
	if(cantidad_validar<0){
		$("#cantidad_articulo_"+id).val(0);
	}
	else if(cantidad_validar>cantidad_bodega){
		$("#cantidad_articulo_"+id).val(cantidad_bodega);
	}
	else if(!isNumber(cantidad_validar)){
		$("#cantidad_articulo_"+id).val(0);
	}
	else{
		$("#cantidad_articulo_"+id).val(cantidad_validar);
	}
	
}

function validarEventoCantidad(id, e){
	validarMaxMinCantidad(id);
	//Validar eventos
	if(e!=null){			
		if (e.keyCode == 13) //Enter
		{
			if(descripcion.trim()!="")
			{
				fila = id.replace("cantidad_articulo_","");				
				pasarSiguienteFila(fila, true);
				return false;
			}			
		}
	}	
}

function articuloYaIngresado(value, id){
	if(value.trim()==''){return false;}
	inputsCodigo = $('.input_codigo_articulo');
	for (i = 0; i < inputsCodigo.length; i++) 
	{
		if(inputsCodigo[i].id==id){} //Si es el mismo input descartarlo
		else if(inputsCodigo[i].value.trim()==value) //Si ya esta en otro
		{
			return true;
		}
	}
	return false;
}