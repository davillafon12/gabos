var facturaCargada = false; //Variable guarda si la factura ya fue o no cargada

$(function(){
	$("#numero_factura").numeric();
});

function cargarFactura(){
	factura = $("#numero_factura").val();
	
	if(!isNumber(factura) || factura < 0){
		notyMsg('Número de factura no válido', 'error');
		return false;
	}
	
	resetFields();
	
	$.ajax({
		url : location.protocol+'//'+document.domain+'/contabilidad/agregarComprasSucursal/cargarFactura',
		type: "POST",		
		//async: false,
		data: {'factura': factura},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				
				informacion = $.parseJSON('[' + data.trim() + ']');
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					setProductos(informacion[0].productos);
					facturaCargada = true;
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

function manejarErrores(error){
	
	switch(error){
		case '1':
			notyMsg('¡No se puedo cargar la factura, por favor contacte al administrador!', 'error');
			resetFields();
		break;
		case '2':
			notyMsg('URL inválida, por favor contacte al administrador', 'error');
			resetFields();
		break;
		case '3':
			notyMsg('Número de factura inválido', 'error');
			resetFields();
		break;
		case '4':
			notyMsg('Factura ingresada no existe', 'error');
			resetFields();
		break;
		case '5':
			notyMsg('Sucursal seleccionada no existe', 'error');
		break;
		case '6':
			notyMsg('La sucursal de destino no puede ser a la misma sucursal que envía', 'error');
		break;
		case '7':
			notyMsg('Esta factura ya fue aplicada en un traspaso anterior', 'error');
		break;
	}
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

function setProductos(productos){
	for(i = 0; i<productos.length; i++){
		precioUnitario = parseFloat(productos[i].Articulo_Factura_Precio_Unitario);
		descuento = parseInt(productos[i].Articulo_Factura_Descuento);
		cantidad = parseInt(productos[i].Articulo_Factura_Cantidad);
		precio = cantidad * ( precioUnitario - ( precioUnitario * (descuento / 100) ));
		precio = precio.format(2, 3, '.', ',');
		precioUnitario = precioUnitario.format(2, 3, '.', ',');
		$("#productos_tabla").append("<tr><td class='productos-p txt-center'>"+productos[i].Articulo_Factura_Codigo+"</td><td class='productos-p'>"+productos[i].Articulo_Factura_Descripcion+"</td><td class='productos-p txt-center'>"+cantidad+"</td><td class='productos-p txt-center'>"+descuento+"</td><td class='productos-p txt-right'>"+precioUnitario+"</td><td class='productos-p txt-right'>"+precio+"</td></tr>");
	}	
}

function resetFields(){
	$("#productos_tabla").html('');
	facturaCargada = false;
}

function procesarSolicitud(){
	if(facturaCargada){
		$.prompt("¡Esto agregará todos los productos de esta factura como compras de la sucursal seleccionada!", {
						title: "¿Esta seguro que desea agregar esta factura como compras?",
						buttons: { "Si, estoy seguro": true, "Cancelar": false },
						submit:function(e,v,m,f){
													if(v){													
														agregarFacturaASucursal();
													}
												}
					});	
	}else{
		notyMsg('Primero cargue una factura a agregar como compra', 'error');
	}
}

function agregarFacturaASucursal(){
	factura = $("#numero_factura").val();
	sucursalAgregar = $("#sucursal").val();
	
	if(!isNumber(factura) || factura < 0){
		notyMsg('Número de factura no válido', 'error');
		return false;
	}
	
	$.ajax({
		url : location.protocol+'//'+document.domain+'/contabilidad/agregarComprasSucursal/agregarCompras',
		type: "POST",		
		async: false,
		data: {'factura': factura, 'sucursal':sucursalAgregar, 'prefijo':$("#prefijo").val()},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				
				informacion = $.parseJSON('[' + data.trim() + ']');
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					resetFields();
					$("#numero_factura").val('');
					window.open(location.protocol+'//'+document.domain+'/impresion?t='+informacion[0].token+'&d=t&n='+informacion[0].traspaso+'&s='+informacion[0].sucursal+'&i=c','Impresion de Traspaso','width=768,height=1024,resizable=no,toolbar=no,location=no,menubar=no');
					notyMsg('¡Se han agregado los artículos como compras con éxito!', 'success');					
				}
			}catch(e){
				
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}