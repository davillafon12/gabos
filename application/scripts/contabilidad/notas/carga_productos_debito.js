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
			getArticulo(fila, value);		
		}
		
	
	}	
	
	//Validar si codigo es numerico
	/*if(isNumber(value)){
		getArticulo(fila, value);
	}else{
		resetRow(fila, true);
	}*/
	
	
}

function getArticulo(fila, articulo){
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/facturas/nueva/getArticuloJSON',
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
		agregarFila(-1);
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
	if(cantidad_validar<=0){
		$("#cantidad_articulo_"+id).val(1);
	}
	else if(cantidad_validar>cantidad_bodega){
		$("#cantidad_articulo_"+id).val(cantidad_bodega);
	}
	else if(!isNumber(cantidad_validar)){
		$("#cantidad_articulo_"+id).val(1);
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

function agregarFila(index){
	// Find a <table> element with id="myTable":
	var table = document.getElementById("tabla_productos");
	//alert(cantidadFilas(table));
	var siguienteFila = cantidadFilas(table)+1;
	var tabindex = siguienteFila+1;
	// Create an empty <tr> element and add it to the last position of the table:
	var row = table.insertRow(index);

	// Insert new cells (<td> elements) at the 1st and 2nd position of the "new" <tr> element:
	var cell1 = row.insertCell(0);
	var cell2 = row.insertCell(1);
	var cell3 = row.insertCell(2);
	var cell4 = row.insertCell(3);

	cell1.innerHTML = "<input id='articulo_"+siguienteFila+"' tabindex='"+tabindex+"'  class='input_codigo_articulo' autocomplete='off' name='codigo_articulo' type='text' onkeyup='buscarArticulo(event, this.value, this.id);'>";
	cell2.innerHTML = "<div class='articulo_specs' id='descripcion_articulo_"+siguienteFila+"'></div>"
                            +"<div class='tooltip_imagen_articulo' id='tooltip_imagen_articulo_"+siguienteFila+"'></div>";
        cell3.innerHTML = "<input id='cantidad_articulo_"+siguienteFila+"' class='cantidad_articulo' autocomplete='off' type='number' min='1' onchange='validarMaxMinCantidad(this.id)' onkeyup='validarEventoCantidad(this.id, event)' >	";
	cell4.innerHTML = "<div class='articulo_specs' id='bodega_articulo_"+siguienteFila+"'></div>";

	//Agrega la nueva fila al array de indices
	if(index==-1){array_pos_rows.push(siguienteFila);}
	else {array_pos_rows.splice(index-1, 0, siguienteFila);}
	
}

function cantidadFilas(tabla){
	return tabla.getElementsByTagName("tr").length-1;
}