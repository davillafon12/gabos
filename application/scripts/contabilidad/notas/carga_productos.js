var facturaSeleccionada = 0;

function seleccionarFactura(factura){
	//Asignamos la factura
	facturaSeleccionada = factura;
	//Cambiamos estilo
	marcarSeleccion();
	//Reset a los productos
	resetFieldsFromProductos();
	cargarProductos();
}

function marcarSeleccion(){
	$(".bordes_tabla_factura").css("background", "none"); //Limpiamos todo
	$("#factura_row_"+facturaSeleccionada).css("background", "#999999");
}

function resetFieldsFromProductos(){
	$("#tbody_productos").html('');
	$("#tbody_productos_seleccionados").html('');
}

function cargarProductos(){
	$.ajax({
		url : location.protocol+'//'+document.domain+'/contabilidad/notas/getProductosDeFactura',
		type: "POST",	
		data: {'factura':facturaSeleccionada},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					setProductosOnTable(informacion[0].productos);
				}
			}catch(e){
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

function setProductosOnTable(productos){	
	cuerpo = '';
	for(i=0; i<productos.length; i++){
		cuerpo += "<tr class='bordes_tabla' onclick='marcarArticulo("+i+")' id='producto_row_"+i+"'><td class='celdas_tabla'><p class='contact'>"+productos[i].codigo+"</p></td><td class='celdas_tabla'><p class='contact'>"+productos[i].descripcion+"</p></td><td class='celdas_tabla'><p class='contact' id='p_cantidad_original_"+i+"'>"+productos[i].cantidad+"</p></td></tr>"; 
	}
	$("#tbody_productos").html(cuerpo);
}