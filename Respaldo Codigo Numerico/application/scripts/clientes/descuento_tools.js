///////////////////////////////// VARIABLES  //////////////////////////////////////
var cedula = '';
var flagReload = false; //Bandera utilizada para ver si se recarga al cliente o no segun el resultado del ajax

function buscarCedula (e) 
{
	cedula = $("#cedula").val();
	if(!isNumber(cedula))
	{ 
		$("#nombre").val('');
		resetFields();
	}
	else
	{	
		getNombreCliente(cedula);
	}
}

function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

function resetFields(){
	$("#descuento").val('00');
	$("#credito").val('0.0');
	
	cuerpoProductos  = "<tr><td colspan='4'><p class='tiny-font'>No tiene descuentos</p></td></tr><tr><td class='borde-arriba'><input class='input-codigo' type='text' id='codigo_producto' onkeyup='buscarArticulo();'/></td><td class='borde-arriba'><p class='tiny-font' id='descripcion_producto'></p></td><td class='borde-arriba'><input class='input-descuento' type='text' id='descuento_producto'/>%</td><td class='borde-arriba'><a href='javascript:;' onclick='agregarDescuentoProducto()' class='boton-cambiar'>Agregar</a></td></tr>";
	cuerpoFamilias = "<tr><td colspan='4'><p class='tiny-font'>No tiene descuentos</p></td></tr><tr><td class='borde-arriba'><input class='input-codigo' type='text' id='codigo_familia' onkeyup='buscarFamilia()'/></td><td class='borde-arriba'><p class='tiny-font' id='descripcion_familia'></p></td><td class='borde-arriba'><input class='input-descuento' type='text' id='descuento_familia'/>%</td><td class='borde-arriba'><a href='javascript:;' onclick='agregarDescuentoFamilia()' class='boton-cambiar'>Agregar</a></td></tr>";
	
	$("#cuerpo_productos").html(cuerpoProductos);
	$("#cuerpo_familia").html(cuerpoFamilias);
}

function getNombreCliente(cedula){
	$.ajax({
		url : location.protocol+'//'+document.domain+'/clientes/otros/getCliente',
		type: "POST",		
		//async: false,
		data: {'cedula':cedula},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					//alert(JSON.stringify(informacion[0].cliente, null, 4));
					setInformacion(informacion[0].cliente);
				}
			}catch(e){
				//alert(e);
				notyError('¡La respuesta tiene un formato indebido, contacte al administrador!');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

function manejarErrores(error){
	switch(error){
		case '1':
			notyError('¡No se pudo tramitar la información, contacte al administrador!');
			resetFields();
		break;
		case '2':
			notyError('¡Error en el envio de la URL, contacte al administrador!');
			resetFields();
		break;
		case '3':
			//Cliente no existe cuando se busca por cedula
			resetFields();
			$("#nombre").val('No existe cliente!!!');
		break;
		case '4':
			//Cliente 0 o 1
			resetFields();
			$("#nombre").val('');
		break;
		case '5':
			notyError('¡No existe cliente!');
		break;
		case '6':
			$("#descripcion_producto").html('');
		break;	
		case '7':
			notyError('¡Este cliente ya tiene un descuento con este producto!');
		break;
		case '8':
			notyError('¡No existe familia!');
		break;
		case '9':
			notyError('¡Este cliente ya tiene un descuento con esta familia!');
		break;
	}
}

function notyError(Mensaje){
	n = noty({
					   layout: 'topRight',
					   text: Mensaje,
					   type: 'error',
					   timeout: 4000
					});
}

function setInformacion(informacionArray){
	$("#nombre").val(informacionArray.nombre+" "+informacionArray.apellidos);
	$("#descuento").val(informacionArray.descuento);
	credito = parseFloat(informacionArray.maxCredito);
	credito = credito.format(2, 3, '.', ',');
	$("#credito").val(credito);
	setDescuentosProductos(informacionArray.desProductos);
	setDescuentosFamilias(informacionArray.desFamilias);
}

function setDescuentosProductos(productos){
	footerDescuentos = "<tr><td class='borde-arriba'><input class='input-codigo' type='text' id='codigo_producto' onkeyup='buscarArticulo();'/></td><td class='borde-arriba'><p class='tiny-font' id='descripcion_producto'></p></td><td class='borde-arriba'><input class='input-descuento' type='text' id='descuento_producto'/>%</td><td class='borde-arriba'><a href='javascript:;' onclick='agregarDescuentoProducto()' class='boton-cambiar'>Agregar</a></td></tr>";
	$("#cuerpo_productos").html('');
	for (i = 0; i < productos.length; i++) { 
		descripcion = productos[i].descripcion;
		descripcion = descripcion.substring(0, 42);
		$('#tabla_des_productos').append("<tr><td><p class='tiny-font'>"+productos[i].codigo+"</p></td><td><p class='tiny-font'>"+descripcion+"</p></td><td><p class='tiny-font'>"+productos[i].porcentaje+"</p></td><td><a href='javascript:;' onclick='eliminarDesProducto("+productos[i].id+")' class='boton-eliminar'>Eliminar</a></td></tr>");
	}
	if(productos.length==0){$('#tabla_des_productos').append("<tr><td colspan='4'><p class='tiny-font'>No tiene descuentos</p></td></tr>");}
	$('#tabla_des_productos').append(footerDescuentos);
}

function setDescuentosFamilias(familias){
	footerFamilias = "<tr><td class='borde-arriba'><input class='input-codigo' type='text' id='codigo_familia' onkeyup='buscarFamilia()'/></td><td class='borde-arriba'><p class='tiny-font' id='descripcion_familia'></p></td><td class='borde-arriba'><input class='input-descuento' type='text' id='descuento_familia'/>%</td><td class='borde-arriba'><a href='javascript:;' onclick='agregarDescuentoFamilia()' class='boton-cambiar'>Agregar</a></td></tr>";
	$("#cuerpo_familia").html('');
	for (i = 0; i < familias.length; i++) { 
		$('#tabla_des_familias').append("<tr><td><p class='tiny-font'>"+familias[i].codigo+"</p></td><td><p class='tiny-font'>"+familias[i].descripcion+"</p></td><td><p class='tiny-font'>"+familias[i].porcentaje+"</p></td><td><a href='javascript:;' onclick='eliminarDesFamilia("+familias[i].id+")' class='boton-eliminar'>Eliminar</a></td></tr>");
	}
	if(familias.length==0){$('#tabla_des_familias').append("<tr><td colspan='4'><p class='tiny-font'>No tiene descuentos</p></td></tr>");}
	$('#tabla_des_familias').append(footerFamilias);
}

/**
 * Number.prototype.format(n, x, s, c)
 * 
 * param integer n: length of decimal
 * param integer x: length of whole part
 * param mixed   s: sections delimiter
 * param mixed   c: decimal delimiter
	12345678.9.format(2, 3, '.', ',');  // "12.345.678,90"
	123456.789.format(4, 4, ' ', ':');  // "12 3456:7890"
	12345678.9.format(0, 3, '-');       // "12-345-679"
 */
 
Number.prototype.format = function(n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};

function setUpLiveSearch(){	
	$("#nombre").autocomplete({
		  source: location.protocol+'//'+document.domain+'/facturas/nueva/getNombresClientesBusqueda',
		  minLength: 1,
		  select: function( event, ui ) {
			$("#cedula").val(ui.item.id);
			evt = document.createEvent("KeyboardEvent");
			buscarCedula(evt); 		  
		  }
		});
}

function updateDescuento(){
	descuento = $("#descuento").val();
	if(isNumber(descuento)){
		descuento = parseInt(descuento);
		if(descuento>=0&&descuento<=100){
			cedula = $("#cedula").val();
			if(isNumber(cedula)){
				$.ajax({
						url : location.protocol+'//'+document.domain+'/clientes/otros/actualizarDescuento',
						type: "POST",		
						async: false,
						data: {'cedula':cedula, 'descuento':descuento},				
						success: function(data, textStatus, jqXHR)
						{
							try{
								informacion = $.parseJSON('[' + data.trim() + ']');
								if(informacion[0].status==="error"){
									manejarErrores(informacion[0].error);
								}else if(informacion[0].status==="success"){
									n = noty({
									   layout: 'topRight',
									   text: 'Se cambio el descuento con exito',
									   type: 'success',
									   timeout: 4000
									});										
								}
							}catch(e){
								//alert(e);
								notyError('¡La respuesta tiene un formato indebido, contacte al administrador!');
							}
						},
						error: function (jqXHR, textStatus, errorThrown)
						{}
					});				
			}else{
				notyError("¡Ingrese una cédula válida!");
			}
		}else{
			notyError("¡Ingrese un descuento válido!");
		}
	}else{
		notyError("¡Ingrese un descuento válido!");
	}
	evt = document.createEvent("KeyboardEvent");
	buscarCedula(evt);  
}

String.prototype.replaceAll = function(str1, str2, ignore) 
{
    return this.replace(new RegExp(str1.replace(/([\/\,\!\\\^\$\{\}\[\]\(\)\.\*\+\?\|\<\>\-\&])/g,"\\$&"),(ignore?"gi":"g")),(typeof(str2)=="string")?str2.replace(/\$/g,"$$$$"):str2);
} 


function updateCredito(){
	credito = $("#credito").val();
	credito = credito.replaceAll('.','', true);
	//alert(credito);
	if(credito.indexOf(',')!=-1){
	//alert();
		credito = credito.substring(0, credito.length - 3);
	}
		
	if(isNumber(credito)){
		credito = parseFloat(credito);
		if(credito>=0){
			cedula = $("#cedula").val();
			if(isNumber(cedula)){
				$.ajax({
						url : location.protocol+'//'+document.domain+'/clientes/otros/actualizarCredito',
						type: "POST",		
						async: false,
						data: {'cedula':cedula, 'credito':credito},				
						success: function(data, textStatus, jqXHR)
						{
							try{
								informacion = $.parseJSON('[' + data.trim() + ']');
								if(informacion[0].status==="error"){
									manejarErrores(informacion[0].error);
								}else if(informacion[0].status==="success"){
									n = noty({
									   layout: 'topRight',
									   text: 'Se cambio el credito con exito',
									   type: 'success',
									   timeout: 4000
									});	
								}
							}catch(e){
								//alert(e);
								notyError('¡La respuesta tiene un formato indebido, contacte al administrador!');
							}
						},
						error: function (jqXHR, textStatus, errorThrown)
						{}
					});				
			}else{
				notyError("¡Ingrese una cédula válida!");
			}
		}else{
			notyError("¡Ingrese un credito válido!");
		}
	}else{
		notyError("¡Ingrese un credito válido!");
	}
	evt = document.createEvent("KeyboardEvent");
	buscarCedula(evt);  
}

function formatCreditoField(){
	credito = $("#credito").val();
	
	if(isNumber(credito)){
		credito = parseFloat(credito);
		credito = credito.format(2, 3, '.', ',');
		$("#credito").val(credito);
	}
}

/*************************************************************************************************
*
*                              MANTENIMIENTO DE DESCUENTOS DE PRODUCTOS
*
*/

function eliminarDesProducto(idDescuento){
	$.ajax({
		url : location.protocol+'//'+document.domain+'/clientes/otros/eliminarDescuentoProducto',
		type: "POST",		
		async: false,
		data: {'id':idDescuento},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					n = noty({
					   layout: 'topRight',
					   text: 'Se elimino el descuento de producto con exito',
					   type: 'success',
					   timeout: 4000
					});
						
				}
			}catch(e){
				//alert(e);
				notyError('¡La respuesta tiene un formato indebido, contacte al administrador!');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
	evt = document.createEvent("KeyboardEvent");
	buscarCedula(evt);  
}

function buscarArticulo(value){
	//alert("");
	codigo = $("#codigo_producto").val();
	codigo = codigo.replace("&","");
	codigo = codigo.replace(";","");
	
	$.ajax({
		url : location.protocol+'//'+document.domain+'/clientes/otros/getArticulo',
		type: "POST",		
		//async: false,
		data: {'codigo':codigo},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					descripcion = informacion[0].descripcion;
					descripcion = descripcion.substring(0, 42); //Para que solo ponga una cantidad de la descripcion
					$("#descripcion_producto").html(descripcion);
				}
			}catch(e){
				notyError('¡La respuesta tiene un formato indebido, contacte al administrador!');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

function agregarDescuentoProducto(){
	descripcion = $("#descripcion_producto").html();
	codigo = $("#codigo_producto").val();
	codigo = codigo.replace("&","");
	codigo = codigo.replace(";","");
	
	
	
	if(descripcion.trim()===''||codigo.trim()===''){
		notyError('¡Debe ingresar un artículo válido!');
	}else{
		descuento = $("#descuento_producto").val();
		if(isNumber(descuento)){
			descuento = parseInt(descuento);
			if(descuento>=0&&descuento<=100){
				cedula = $("#cedula").val();
				if(isNumber(cedula)){
					envioDescuentoProducto(codigo, descuento);
				}else{
						notyError("¡Ingrese una cédula válida!");
					}
			}else{
				notyError("¡Ingrese un descuento válido!");
			}
		}else{
			notyError("¡Ingrese un descuento válido!");
		}		
	}
	//alert(flagReload);
	if(flagReload){
		//alert("Entro");
		flagReload = false; //Se vuelve a poner en falso
		evt = document.createEvent("KeyboardEvent");
		buscarCedula(evt);
	}
}

function envioDescuentoProducto(codigo, descuento){
	$.ajax({
		url : location.protocol+'//'+document.domain+'/clientes/otros/setDescuentoProducto',
		type: "POST",		
		async: false,
		data: {'codigo':codigo, 'descuento':descuento, 'cedula':cedula},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					//Todo salio bien
					flagReload = true; //Si se puede recargar la informacion
					n = noty({
					   layout: 'topRight',
					   text: 'Se agrego el descuento de producto con exito',
					   type: 'success',
					   timeout: 4000
					});
				}
			}catch(e){
				notyError('¡La respuesta tiene un formato indebido, contacte al administrador!');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
	
}




/*************************************************************************************************
*
*                              MANTENIMIENTO DE DESCUENTOS DE FAMILIAS
*
*/

function eliminarDesFamilia(idDescuento){
	$.ajax({
		url : location.protocol+'//'+document.domain+'/clientes/otros/eliminarDescuentoFamilia',
		type: "POST",		
		async: false,
		data: {'id':idDescuento},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					n = noty({
					   layout: 'topRight',
					   text: 'Se elimino el descuento de familia con exito',
					   type: 'success',
					   timeout: 4000
					});
						
				}
			}catch(e){
				//alert(e);
				notyError('¡La respuesta tiene un formato indebido, contacte al administrador!');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
	evt = document.createEvent("KeyboardEvent");
	buscarCedula(evt);  
}


function buscarFamilia(){
	//alert("");
	codigo = $("#codigo_familia").val();
	codigo = codigo.replace("&","");
	codigo = codigo.replace(";","");
	if(codigo.trim()===''){
		$("#descripcion_familia").html('');
		return false;
	}
	$.ajax({
		url : location.protocol+'//'+document.domain+'/clientes/otros/getFamilia',
		type: "POST",		
		//async: false,
		data: {'codigo':codigo},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					descripcion = informacion[0].descripcion;
					//alert(descripcion);
					descripcion = descripcion.substring(0, 42); //Para que solo ponga una cantidad de la descripcion
					$("#descripcion_familia").html(descripcion);
				}
			}catch(e){
				notyError('¡La respuesta tiene un formato indebido, contacte al administrador!');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

function agregarDescuentoFamilia(){
	descripcion = $("#descripcion_familia").html();
	codigo = $("#codigo_familia").val();
	codigo = codigo.replace("&","");
	codigo = codigo.replace(";","");
	
	
	
	if(descripcion.trim()===''||codigo.trim()===''){
		notyError('¡Debe ingresar una familia válida!');
	}else{
		descuento = $("#descuento_familia").val();
		if(isNumber(descuento)){
			descuento = parseInt(descuento);
			if(descuento>=0&&descuento<=100){
				cedula = $("#cedula").val();
				if(isNumber(cedula)){
					envioDescuentoFamilia(codigo, descuento);
				}else{
						notyError("¡Ingrese una cédula válida!");
					}
			}else{
				notyError("¡Ingrese un descuento válido!");
			}
		}else{
			notyError("¡Ingrese un descuento válido!");
		}		
	}
	//alert(flagReload);
	if(flagReload){
		//alert("Entro");
		flagReload = false; //Se vuelve a poner en falso
		evt = document.createEvent("KeyboardEvent");
		buscarCedula(evt);
	}
}

function envioDescuentoFamilia(codigo, descuento){
	$.ajax({
		url : location.protocol+'//'+document.domain+'/clientes/otros/setDescuentoFamilia',
		type: "POST",		
		async: false,
		data: {'codigo':codigo, 'descuento':descuento, 'cedula':cedula},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					//Todo salio bien
					flagReload = true; //Si se puede recargar la informacion
					n = noty({
					   layout: 'topRight',
					   text: 'Se agrego el descuento de producto con exito',
					   type: 'success',
					   timeout: 4000
					});
				}
			}catch(e){
				notyError('¡La respuesta tiene un formato indebido, contacte al administrador!');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
	
}

