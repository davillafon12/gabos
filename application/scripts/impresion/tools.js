var totalEspacios = 40; //Cantidad de espacios soportados por la impresora mas pequeña TDMU200 en una sola fila
var servidor = '';
var protocolo = 'http:';

function parse(val) {
	result = "_nulo_",
		tmp = [];
	location.search
	//.replace ( "?", "" ) 
	// this is better, there might be a question mark inside
	.substr(1)
		.split("&")
		.forEach(function (item) {
		tmp = item.split("=");
		if (tmp[0] === val) result = decodeURIComponent(tmp[1]);
	});
	return result;
}

function imprimir(){
	d = parse('d');
	i = parse('i');
	n = parse('n');
	s = parse('s');
	t = parse('t');	
	servidor = parse('server'); 
	protocolo = parse('protocol');
	puerto = 80;
	traerDocumento(t,d,n,s,i);	
}

function traerDocumento(t,d,n,s,i){
	$.ajax({
		url : '/impresion',
		dataType : 'json',
		data : "t="+t+"&d="+d+"&n="+n+"&s="+s+"&i="+i,
		success: function(data, textStatus, jqXHR)
		{
			console.log(data);
			try{				
				if(data.status==="error"){
					alert("Error: "+data.error);
				}else if(data.status==="success"){
					seleccionarTipoDocumento(d, data);				
				}
			}catch(e){
				console.log(e);
				alert("No se pudo imprimir");
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

function seleccionarTipoDocumento(d, data){
	switch(d.trim()){
		case 'f':
			imprimirFactura(data);			
		break;
		case 'p':
			imprimirProforma(data);			
		break;
		case 'r':
			//Sacamos cuantos recibos se hicieron
			cant_recibos = data.recibos.length;
			for(i=0; i<cant_recibos; i++){
				recibo = data.recibos[i];
				recibo.push(data.empresa);
				imprimirRecibo(recibo);
			}
		break;
		case 'nc':
			imprimirNotaCredito(data);			
		break;
		case 'nd':
			imprimirNotaDebito(data);			
		break;
		case 'rp':
			imprimirRetiroParcial(data);			
		break;
	}
	comenzarCierre();
}

/**********************************************************************************************************************************
											FUNCIONES DE IMPRESION DE CADA DOCUMENTO
**********************************************************************************************************************************/

function imprimirFactura(data){
	empresa = data.empresa[0];
	factura = data.fHead[0];
	productos = data.fBody;
	
	//qz = document.getElementById("qz");
	qz.append("\x1B\x40"); //Reset todo
	qz.appendHex("x1Bx70x00x64x64"); //Abrir Gabeta
	qz.append("\x1B\x74\x16"); //Code page WPC1252
	//Seleccionamos el tipo de letra
	qz.append("\x1B\x21\x10");
	//Centramos
	qz.append("\x1B\x61\x01");
	qz.append(" "+empresa.nombre+" \r\n");
	//Seleccionamos tipo de letra
	qz.append("\x1B\x21\x01");
        qz.append(" "+empresa.administrador+" \r\n");
	qz.append(" Cedula: "+empresa.cedula+" \r\n");
	qz.append(" Tel.: "+empresa.telefono+" \r\n");
	qz.append(" Email: "+empresa.email+" \r\n");
        qz.append(" Direccion: "+empresa.direccion+" \r\n");
	qz.append(" \r\n");
	qz.append("\x1B\x40"); //Reset todo
	qz.append("\x1B\x74\x16"); //Code page WPC1252
	qz.append("----------------------------------------\r\n");
        qz.append(" FACTURA ELECTRONICA\r\n");
	qz.append(" Consecutivo: "+factura.consecutivoH+"\r\n"); 
        qz.append(" Clave: "+factura.clave+"\r\n"); 
	qz.append(" Fecha: "+factura.fecha+"\r\n"); 
	qz.append("----------------------------------------\r\n");
	qz.append(" Cliente: "+factura.cliente_ced+"\r\n");
	qz.append(" Nombre: \r\n");
	//Se tira en otro reglo para que queda todo el nombre, si es mayor a 40 lo corta
	qz.append(factura.cliente_nom.substring(0, 40)+"\r\n");
	qz.append("----------------------------------------\r\n");
	qz.append(" Tipo de Pago: "+factura.tipo+"\r\n"); 
	if(factura.tipo==='credito'){
		qz.append(" Dias de Credito: "+factura.diasCredito+"\r\n");
		qz.append(" Fecha de Vencimiento: "+factura.fechaVencimiento+"\r\n"); 
	}else if(factura.tipo==='mixto'){
		qz.append(" Pagado con Tarjeta: "+formatearNumero(factura.cantidadTarjeta)+"\r\n");
		qz.append(" Pagado con Contado: "+formatearNumero(factura.cantidadContado)+"\r\n");
	}else if(factura.tipo==='apartado'){
		qz.append(" Abono: "+formatearNumero(factura.abono)+"\r\n");
		qz.append(" Saldo: "+formatearNumero(factura.saldo)+"\r\n");
	}
	qz.append(" Moneda: "+factura.moneda+"\r\n"); 
	qz.append(" Vendedor: "+factura.vendedor.substring(0, 29)+"\r\n");
	qz.append(" Pago con: "+factura.recibido_vuelto+"\r\n"); 
	qz.append(" Vuelto: "+factura.entregado_vuelto+"\r\n");
	factura.estado = factura.estado == "cobrada" ? "facturada" : factura.estado;
	qz.append(" Estado: "+factura.estado+"\r\n");
	qz.append("----------------------------------------\r\n");
	qz.append(" Articulo      Cant. Desc.      Precio  \r\n");
	qz.append("----------------------------------------\r\n");
	
	var cantidadTotalArticulos = 0;
	
	//PROCESADO DE PRODUCTOS
	for(i = 0; productos.length>i; i++){
		cantidad = productos[i].cantidad;
		descuento = productos[i].descuento;
		
		cant = parseInt(cantidad);
		//des = parseInt(descuento);
		cantidadTotalArticulos += cant;
		precio = parseFloat(productos[i].precio);
		precio = cantidad * ( precio - ( precio * ( descuento / 100 ) ) );
		
		qz.append(formatearCodigo(productos[i].codigo)+formatearCantidad(cantidad)+formatearDescuento(descuento)+acomodarPrecio(formatearNumero(precio))+"\r\n");
		qz.append(" ->"+productos[i].descripcion.substring(0, 36)+"\r\n");
	}
	qz.append("----------------------------------------\r\n");
	qz.append("Cant. Articulos: "+cantidadTotalArticulos+"\r\n");
	qz.append("----------------------------------------\r\n");
	qz.append(enviarDerecha("Subtotal:"+formatearMontoTotal(formatearNumero(factura.subtotal)))+"\r\n");
	qz.append(enviarDerecha("IVA:"+formatearMontoTotal(formatearNumero(parseFloat(factura.total_iva)+parseFloat(factura.retencion))))+"\r\n");
	//qz.append(enviarDerecha("Retencion:"+formatearMontoTotal(formatearNumero(factura.retencion)))+"\r\n");
	qz.append(enviarDerecha("Total:"+formatearMontoTotal(formatearNumero(factura.total)))+"\r\n");
	qz.append("----------------------------------------\r\n");
	qz.append("Comentarios:\r\n");
	qz.append(factura.observaciones+"\r\n")
	//Centramos 
	qz.append("\x1B\x61\x01");
	qz.append("Recibido conforme: ___________\r\n");
	qz.append(" \r\n");
	qz.append("Los precios incluyen impuestos de venta\r\n");
	qz.append("Gracias por su visita\r\n");
	qz.append(" \r\n");
        qz.append(" Version 4.2\r\n");
	qz.append(empresa.leyenda+"\r\n");
	//Damos espacio al final
	qz.append("\r\n\r\n\r\n\r\n\r\n\r\n");
	qz.append("\x1B\x69"); //Cortar
	qz.print();
}

function imprimirProforma(data){
	empresa = data.empresa[0];
	factura = data.fHead[0];
	productos = data.fBody;
	
	//qz = document.getElementById("qz");
	qz.append("\x1B\x40"); //Reset todo
	qz.appendHex("x1Bx70x00x64x64"); //Abrir Gabeta
	qz.append("\x1B\x74\x16"); //Code page WPC1252
	//Seleccionamos el tipo de letra
	qz.append("\x1B\x21\x10");
	//Centramos
	qz.append("\x1B\x61\x01");
	qz.append(" "+empresa.nombre+" \r\n");
	//Seleccionamos tipo de letra
	qz.append("\x1B\x21\x01");
	qz.append(" Ced. Jur.: "+empresa.cedula+" \r\n");
	qz.append(" Tel.: "+empresa.telefono+" \r\n");
	//qz.append(" Direccion: "+empresa.Sucursal_Direccion+" \r\n");
	qz.append(" Email: "+empresa.email+" \r\n");
	qz.append(" \r\n");
	qz.append("\x1B\x40"); //Reset todo
	qz.append("\x1B\x74\x16"); //Code page WPC1252
	qz.append("----------------------------------------\r\n");			
	qz.append(" Proforma: "+factura.consecutivo+"\r\n"); 
	qz.append(" Fecha: "+factura.fecha+"\r\n"); 
	qz.append("----------------------------------------\r\n");
	qz.append(" Cliente: "+factura.cliente_ced+"\r\n");
	qz.append(" Nombre: \r\n");
	//Se tira en otro reglo para que queda todo el nombre, si es mayor a 40 lo corta
	qz.append(factura.cliente_nom.substring(0, 40)+"\r\n");
	qz.append("----------------------------------------\r\n");
	qz.append(" Moneda: "+factura.moneda+"\r\n"); 
	qz.append(" Vendedor: "+factura.vendedor.substring(0, 29)+"\r\n");
	qz.append(" Pago con: "+factura.recibido_vuelto+"\r\n"); 
	qz.append(" Vuelto: "+factura.entregado_vuelto+"\r\n");
	factura.estado = factura.estado == "sin_proces" ? "sin procesar" : factura.estado;
	qz.append(" Estado: "+factura.estado+"\r\n");
	qz.append("----------------------------------------\r\n");
	qz.append(" Articulo      Cant. Desc.      Precio  \r\n");
	qz.append("----------------------------------------\r\n");
	
	var cantidadTotalArticulos = 0;
	
	//PROCESADO DE PRODUCTOS
	for(i = 0; productos.length>i; i++){
		cantidad = productos[i].cantidad;
		descuento = productos[i].descuento;
		
		cant = parseInt(cantidad);
		des = parseInt(descuento);
		cantidadTotalArticulos += cant;
		precio = parseFloat(productos[i].precio);
		precio = cantidad * ( precio - ( precio * ( descuento / 100 ) ) );
		
		qz.append(formatearCodigo(productos[i].codigo)+formatearCantidad(cantidad)+formatearDescuento(descuento)+acomodarPrecio(formatearNumero(precio))+"\r\n");
		qz.append(" ->"+productos[i].descripcion.substring(0, 36)+"\r\n");
	}
	qz.append("----------------------------------------\r\n");
	qz.append("Cant. Articulos: "+cantidadTotalArticulos+"\r\n");
	qz.append("----------------------------------------\r\n");
	qz.append(enviarDerecha("Subtotal:"+formatearMontoTotal(formatearNumero(factura.subtotal)))+"\r\n");
	qz.append(enviarDerecha("IVA:"+formatearMontoTotal(formatearNumero(factura.total_iva)))+"\r\n");
	qz.append(enviarDerecha("Retencion:"+formatearMontoTotal(formatearNumero(factura.retencion)))+"\r\n");
	qz.append(enviarDerecha("Total:"+formatearMontoTotal(formatearNumero(factura.total)))+"\r\n");
	qz.append("----------------------------------------\r\n");
	qz.append("Comentarios:\r\n");
	qz.append(factura.observaciones+"\r\n")
	//Centramos 
	qz.append("\x1B\x61\x01");
	qz.append("Recibido conforme: ___________\r\n");
	qz.append(" \r\n");
	qz.append("Los precios incluyen impuestos de venta\r\n");
	qz.append("Gracias por su visita\r\n");
	qz.append(" \r\n");
	qz.append(empresa.leyenda+"\r\n");
	//Damos espacio al final
	qz.append("\r\n\r\n\r\n\r\n\r\n\r\n");
	qz.append("\x1B\x69"); //Cortar
	qz.print();
}


function imprimirRecibo(data){
	recibo = data[0];
	empresa = data[1][0];
	
	//qz = document.getElementById("qz");
	qz.append("\x1B\x40"); //Reset todo
	qz.appendHex("x1Bx70x00x64x64"); //Abrir Gabeta
	qz.append("\x1B\x74\x16"); //Code page WPC1252
	//Seleccionamos el tipo de letra
	qz.append("\x1B\x21\x10");
	//Centramos
	qz.append("\x1B\x61\x01");
	qz.append(" "+empresa.nombre+" \r\n");
	//Seleccionamos tipo de letra
	qz.append("\x1B\x21\x01");
	qz.append(" Ced. Jur.: "+empresa.cedula+" \r\n");
	qz.append(" Tel.: "+empresa.telefono+" \r\n");
	//qz.append(" Direccion: "+empresa.Sucursal_Direccion+" \r\n");
	qz.append(" Email: "+empresa.email+" \r\n");
	qz.append(" \r\n");
	qz.append("\x1B\x40"); //Reset todo
	qz.append("\x1B\x74\x16"); //Code page WPC1252
	qz.append("----------------------------------------\r\n");
	//Seleccionamos el tipo de letra
	qz.append("\x1B\x21\x08");	
	qz.append("Recibo de Dinero\r\n");
	qz.append("\x1B\x40"); //Reset todo
	qz.append("\x1B\x74\x16"); //Code page WPC1252
	qz.append("Numero: "+recibo.recibo+"\r\n");
	qz.append("Fecha: "+recibo.fecha_recibo+"\r\n");
	qz.append("Moneda: "+recibo.moneda+"\r\n");
	qz.append("Tipo de Pago: "+recibo.tipo_pago+"\r\n");
	qz.append("----------------------------------------\r\n");
	qz.append("Recibimos de: "+recibo.cliente_cedula+"\r\n");
	qz.append(recibo.cliente_nombre.substring(0, 40)+"\r\n");
	qz.append("La suma de: "+formatearNumero(recibo.monto)+"\r\n");
	qz.append("Por concepto de abono a la factura: \r\n");
	qz.append("Consecutivo: "+recibo.factura+"\r\n");
	qz.append("Emitida: "+recibo.fecha_expedicion+"\r\n");
	qz.append("Monto: "+formatearNumero(recibo.Saldo_inicial)+"\r\n");
	qz.append("----------------------------------------\r\n");
	qz.append("Saldo Anterior:"+formatearMontoTotal(formatearNumero(recibo.saldo_anterior))+"\r\n");
	//Underline
	qz.append("\x1B\x2D\x01");		
	qz.append("Este Abono:    "+formatearMontoTotal(formatearNumero(recibo.monto))+"\r\n");
	qz.append("\x1B\x40"); //Reset todo
	qz.append("\x1B\x74\x16"); //Code page WPC1252
	qz.append("Saldo Actual:  "+formatearMontoTotal(formatearNumero(recibo.saldo))+"\r\n");
	qz.append("----------------------------------------\r\n");
	qz.append("Comentarios:\r\n");
	qz.append(recibo.comentarios+"\r\n");
	qz.append("\r\n\r\n\r\n");
	//Centramos 
	qz.append("\x1B\x61\x01");
	//Underline
	qz.append("\x1B\x2D\x01");
	qz.append("                              \r\n");	
	qz.append("\x1B\x40"); //Reset todo
	qz.append("\x1B\x74\x16"); //Code page WPC1252
	//Centramos 
	qz.append("\x1B\x61\x01");
	qz.append("Firma Autoriza\r\n");
	qz.append("\r\n");
	qz.append("Gracias por su visita\r\n");
	//Damos espacio al final
	qz.append("\r\n\r\n\r\n\r\n\r\n\r\n");
	qz.append("\x1B\x69"); //Cortar
	qz.print();
}

function imprimirNotaCredito(data){
	empresa = data.empresa[0];
	nota = data.notaHead[0];
	productos = data.notaBody;

	//qz = document.getElementById("qz");
	qz.append("\x1B\x40"); //Reset todo
	qz.appendHex("x1Bx70x00x64x64"); //Abrir Gabeta
	qz.append("\x1B\x74\x16"); //Code page WPC1252
	//Seleccionamos el tipo de letra
	qz.append("\x1B\x21\x10");
	//Centramos
	qz.append("\x1B\x61\x01");
	qz.append(" "+empresa.nombre+" \r\n");
	//Seleccionamos tipo de letra
	qz.append("\x1B\x21\x01");
        qz.append(" "+empresa.administrador+" \r\n");
	qz.append(" Cedula: "+empresa.cedula+" \r\n");
	qz.append(" Tel.: "+empresa.telefono+" \r\n");
	qz.append(" Email: "+empresa.email+" \r\n");
        qz.append(" Direccion: "+empresa.direccion+" \r\n");
	qz.append(" \r\n");
	qz.append("\x1B\x40"); //Reset todo
	qz.append("\x1B\x74\x16"); //Code page WPC1252
	qz.append("----------------------------------------\r\n");
	//Seleccionamos el tipo de letra
	qz.append("\x1B\x21\x08");	
	qz.append("NOTA CREDITO ELECTRONICA\r\n");
	qz.append("\x1B\x40"); //Reset todo
	qz.append("\x1B\x74\x16"); //Code page WPC1252
	qz.append("Consecutivo: "+nota.consecutivoH+"\r\n"); 
        qz.append("Clave: "+nota.clave+"\r\n"); 
	qz.append("Fecha: "+nota.fecha+"\r\n");
	qz.append("Moneda: "+nota.moneda+"\r\n");
	qz.append("Tipo de Pago: "+nota.tipo_pago+"\r\n");
	qz.append("----------------------------------------\r\n");
	qz.append(" Cliente: "+nota.cliente_cedula+"\r\n");
	qz.append(" Nombre: \r\n");
	//Se tira en otro reglo para que queda todo el nombre, si es mayor a 40 lo corta
	qz.append(nota.cliente_nombre.substring(0, 40)+"\r\n");
	qz.append("----------------------------------------\r\n");
	qz.append(" Articulo      C.B.  C.D.       Precio  \r\n");
	qz.append("----------------------------------------\r\n");
	//PROCESADO DE PRODUCTOS
	for(i = 0; productos.length>i; i++){
		bueno = parseInt(productos[i].bueno);
		defectuoso = parseInt(productos[i].defectuoso);		
		precio = parseFloat(productos[i].precio);
				
		precio = precio * (bueno+defectuoso);
		precio = precio+"".trim();
		
		bueno = bueno+"";
		defectuoso = defectuoso+"";	
		
		qz.append(formatearCodigo(productos[i].codigo)+formatearCantidad(bueno)+formatearDescuento(defectuoso)+acomodarPrecio(formatearNumero(precio))+"\r\n");
		qz.append(" ->"+productos[i].descripcion.substring(0, 36)+"\r\n");
	}
	qz.append("----------------------------------------\r\n");
	qz.append(enviarDerecha("Subtotal:"+formatearMontoTotal(formatearNumero(nota.subtotal)))+"\r\n");
	qz.append(enviarDerecha("IVA:"+formatearMontoTotal(formatearNumero(nota.total_iva)))+"\r\n");
	qz.append(enviarDerecha("Total:"+formatearMontoTotal(formatearNumero(nota.total)))+"\r\n");
	qz.append("----------------------------------------\r\n");
	qz.append("\r\n");
	//Seleccionamos el tipo de letra
	qz.append("\x1B\x21\x10");
	//Centramos 
	qz.append("\x1B\x61\x01");
	qz.append("Se aplica esta nota credito a\r\n");
	qz.append("la factura #"+nota.factura_aplicar+"\r\n\r\n");

	qz.append("Recibido conforme: ___________\r\n");
	qz.append(" \r\n");
	qz.append("Los precios incluyen impuestos de venta\r\n");
	qz.append("Gracias por su visita\r\n");
	qz.append(" \r\n");
        qz.append(" Version 4.2\r\n");
	qz.append(empresa.leyenda+"\r\n");
	//Damos espacio al final
	qz.append("\r\n\r\n\r\n\r\n\r\n\r\n");
	qz.append("\x1B\x69"); //Cortar
	qz.print();
}

function imprimirNotaDebito(data){
	empresa = data.empresa[0];
	nota = data.notaHead[0];
	productos = data.notaBody;

	//qz = document.getElementById("qz");
	qz.append("\x1B\x40"); //Reset todo
	qz.appendHex("x1Bx70x00x64x64"); //Abrir Gabeta
	qz.append("\x1B\x74\x16"); //Code page WPC1252
	//Seleccionamos el tipo de letra
	qz.append("\x1B\x21\x10");
	//Centramos
	qz.append("\x1B\x61\x01");
	qz.append(" "+empresa.nombre+" \r\n");
	//Seleccionamos tipo de letra
	qz.append("\x1B\x21\x01");
	qz.append(" Ced. Jur.: "+empresa.cedula+" \r\n");
	qz.append(" Tel.: "+empresa.telefono+" \r\n");
	//qz.append(" Direccion: "+empresa.Sucursal_Direccion+" \r\n");
	qz.append(" Email: "+empresa.email+" \r\n");
	qz.append(" \r\n");
	qz.append("\x1B\x40"); //Reset todo
	qz.append("\x1B\x74\x16"); //Code page WPC1252
	qz.append("----------------------------------------\r\n");
	//Seleccionamos el tipo de letra
	qz.append("\x1B\x21\x08");	
	qz.append("Nota de Debito\r\n");
	qz.append("\x1B\x40"); //Reset todo
	qz.append("\x1B\x74\x16"); //Code page WPC1252
	qz.append("Numero: "+nota.nota+"\r\n");
	qz.append("Fecha: "+nota.fecha+"\r\n");
	qz.append("----------------------------------------\r\n");
	qz.append(" Articulo      Cantidad         Precio  \r\n");
	qz.append("----------------------------------------\r\n");
	//PROCESADO DE PRODUCTOS
	for(i = 0; productos.length>i; i++){
		cantidad = parseInt(productos[i].cantidad);		
		precio = parseFloat(productos[i].precio);
		precio = precio * cantidad;
		cantidad = cantidad+""; //Para pasarlo a string
		
		qz.append(formatearCodigo(productos[i].codigo)+"    "+formatearCantidad(cantidad)+"  "+acomodarPrecio(formatearNumero(precio))+"\r\n");
		qz.append(" ->"+productos[i].descripcion.substring(0, 36)+"\r\n");
	}
	qz.append("----------------------------------------\r\n");
	qz.append(enviarDerecha("Subtotal:"+formatearMontoTotal(formatearNumero(nota.subtotal)))+"\r\n");
	qz.append(enviarDerecha("IVA:"+formatearMontoTotal(formatearNumero(nota.total_iva)))+"\r\n");
	qz.append(enviarDerecha("Total:"+formatearMontoTotal(formatearNumero(nota.total)))+"\r\n");
	qz.append("----------------------------------------\r\n");
	//Damos espacio al final
	qz.append("\r\n\r\n\r\n\r\n\r\n\r\n");
	qz.append("\x1B\x69"); //Cortar
	qz.print();
}

function imprimirRetiroParcial(data){
	empresa = data.empresa[0];
	retiro = data.retiro;
	billetes = data.billetes;
	monedas = data.monedas;
	dolares = data.dolares;
	
	//qz = document.getElementById("qz");
	qz.append("\x1B\x40"); //Reset todo
	qz.appendHex("x1Bx70x00x64x64"); //Abrir Gabeta
	qz.append("\x1B\x74\x16"); //Code page WPC1252
	//Seleccionamos el tipo de letra
	qz.append("\x1B\x21\x10");
	//Centramos
	qz.append("\x1B\x61\x01");
	qz.append(" "+empresa.nombre+" \r\n");
	//Seleccionamos tipo de letra
	qz.append("\x1B\x21\x01");
	qz.append(" Ced. Jur.: "+empresa.cedula+" \r\n");
	qz.append(" Tel.: "+empresa.telefono+" \r\n");
	qz.append(" Email: "+empresa.email+" \r\n");
	qz.append(" \r\n");
	qz.append("\x1B\x40"); //Reset todo
	qz.append("\x1B\x74\x16"); //Code page WPC1252
	qz.append("----------------------------------------\r\n");
	//Seleccionamos el tipo de letra
	qz.append("\x1B\x21\x08");	
	qz.append("Retiro Parcial de Dinero\r\n");
	qz.append("\x1B\x40"); //Reset todo
	qz.append("\x1B\x74\x16"); //Code page WPC1252
	qz.append("Numero: "+retiro.consecutivo+"\r\n");
	qz.append("Fecha: "+retiro.fecha+"\r\n");
	qz.append("Cajero: "+retiro.usuario+"\r\n");
	qz.append("Tipo de Cambio: "+formatearCantidad(retiro.tipo)+"\r\n");
	qz.append("----------------------------------------\r\n");
	qz.append(" Denominacion      Cant.         Total  \r\n");
	qz.append("----------------------------------------\r\n");
	//Seleccionamos el tipo de letra
	qz.append("\x1B\x21\x08");	
	qz.append("Billetes\r\n");
	qz.append("\x1B\x40"); //Reset todo
	// PONEMOS LOS BILLETES EN COLONES   formatearDenominacion(cadena)  formatearCantidadDenominacion(cadena)   formatearTotalDenominacion(cadena)
	for(i = 0; billetes.length>i; i++){
		cantidad = parseInt(billetes[i].cantidad);		
		denominacion = parseInt(billetes[i].denominacion);
		total = cantidad * denominacion;
		total = total+""; //Para pasarlo a string
		
		qz.append(formatearDenominacion(billetes[i].denominacion)+formatearCantidadDenominacion(billetes[i].cantidad)+formatearTotalDenominacion(formatearCantidad(total))+"\r\n");
	}
	// -------------------------------
	//Seleccionamos el tipo de letra
	qz.append("\x1B\x21\x08");	
	qz.append("Monedas\r\n");
	qz.append("\x1B\x40"); //Reset todo
	// PONEMOS LAS MONEDAS EN COLONES
	for(i = 0; monedas.length>i; i++){
		cantidad = parseInt(monedas[i].cantidad);		
		denominacion = parseInt(monedas[i].denominacion);
		total = cantidad * denominacion;
		total = total+""; //Para pasarlo a string
		
		qz.append(formatearDenominacion(monedas[i].denominacion)+formatearCantidadDenominacion(monedas[i].cantidad)+formatearTotalDenominacion(formatearCantidad(total))+"\r\n");
	}
	// -------------------------------
	//Seleccionamos el tipo de letra
	qz.append("\x1B\x21\x08");	
	qz.append("Dolares\r\n");
	qz.append("\x1B\x40"); //Reset todo
	// PONEMOS LOS DOLARES
	for(i = 0; dolares.length>i; i++){
		cantidad = parseInt(dolares[i].cantidad);		
		denominacion = parseInt(dolares[i].denominacion);
		tipoCambio = parseFloat(retiro.tipo);
		total = (cantidad * denominacion) * tipoCambio;
		total = total+""; //Para pasarlo a string
		
		qz.append(formatearDenominacion(dolares[i].denominacion)+formatearCantidadDenominacion(dolares[i].cantidad)+formatearTotalDenominacion(formatearCantidad(total))+"\r\n");
	}
	// -------------------------------
	qz.append("----------------------------------------\r\n");
	qz.append(enviarDerecha("Total:"+formatearMontoTotal(formatearNumero(retiro.monto)))+"\r\n");
	qz.append("----------------------------------------\r\n");
	//Centramos 
	qz.append(" \r\n");
	qz.append(" \r\n");
	qz.append("\x1B\x61\x01");
	qz.append("Realizado Por: ___________\r\n");
	//Damos espacio al final
	qz.append("\r\n\r\n\r\n\r\n\r\n\r\n");
	qz.append("\x1B\x69"); //Cortar
	qz.print();
}

/**********************************************************************************************************************************
											         FUNCIONES DE FORMATEO
**********************************************************************************************************************************/




function formatearNumero(numero){
	numero = parseFloat(numero);
	numero = numero.format(2, 3, '.', ',');
	return numero;
}

function formatearCantidad(cantidad){
	//6 espacios
	switch(cantidad.length){
		case 1:
			return "   "+cantidad+"  ";
		break;
		case 2:
			return "  "+cantidad+"  ";
		break;
		case 3:
			return " "+cantidad+"  ";
		break;			
		case 4:
			return cantidad+"  ";
		break;
		default:
			return cantidad;
		break;
	}
}

function formatearDescuento(cantidad){
	cantidad = cantidad.substring(0, 4)
	//3 espacios
	switch(cantidad.length){
		case 1:
			return "   "+cantidad+"  ";
		break;
		case 2:
			return "  "+cantidad+"  ";
		break;
		case 3:
			return " "+cantidad+"  ";
		break;
		default:
			return cantidad;
		break;
	}
}

//Acomoda el precio del articulo apra que quede en 14 espacios
function acomodarPrecio(precio){
	//Largo 14
	n = precio.length;
	while(n<14){
		precio = " "+precio;
		n++;
	}
	return precio;
}

//Formatea el precio del monto total para que tenga siempre 16 espacios
function formatearMontoTotal(monto){
	//Largo 16
	n = monto.length;
	while(n<16){
		monto = " "+monto;
		n++;
	}
	return monto;
}

//Formatea el codigo del articulo para que tenga 14 espacios
function formatearCodigo(codigo){	
	//Largo 14
	codigo = " "+codigo; //Lo corremos un espacio a la derecha
	n = codigo.length;
	while(n<14){
		codigo = codigo+" ";
		n++;
	}
	return codigo;
}
function enviarDerecha(cadena){	
	n = cadena.length;
	while(n<totalEspacios){
		cadena = " "+cadena;
		n++;
	}
	return cadena;
}

function formatearDenominacion(cadena){
	cadena += "     ";
	n = cadena.length;
	while(n<16){
		cadena = " "+cadena;
		n++;
	}
	return cadena;
}

function formatearCantidadDenominacion(cadena){
	cadena += "   ";
	n = cadena.length;
	while(n<10){
		cadena = " "+cadena;
		n++;
	}
	return cadena;
}

function formatearTotalDenominacion(cadena){
	cadena += " ";
	n = cadena.length;
	while(n<14){
		cadena = " "+cadena;
		n++;
	}
	return cadena;
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

var segundos = 20;

function comenzarCierre(){
	//Despues de realizado el trabajo cierra la venta tras 20 segundos
	setInterval(function () {
		segundos--;
		$("#boton_reimprimir").val("Reimprimir ("+segundos+")");
		if(segundos===0){
			window.close();
		}		
	}, 1000);
}






