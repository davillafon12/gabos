
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
	//traerDocumento("t=db426c6074f7eba4770e32c8e1928fe7&d=f&n=3&s=0&i=t");	
	traerDocumento(t,d,n,s,i);	
}

function traerDocumento(t,d,n,s,i){
	$.ajax({
		url : 'http://www.gabo.com/impresion',
		dataType : 'jsonp',
		data : "t="+t+"&d="+d+"&n="+n+"&s="+s+"&i="+i,
		success: function(data, textStatus, jqXHR)
		{
			//alert(data);
			//console.log(data);
			try{				
				if(data.status==="error"){
					alert("Error");
				}else if(data.status==="success"){
					seleccionarTipoDocumento(d, data);				
				}
			}catch(e){
				alert("Error e");				
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
	 
		}
	});
}

function seleccionarTipoDocumento(d, data){
	switch(d.trim()){
		case 'f':
			imprimirFactura(data);
		break;
	}
}

function imprimirFactura(data){
	empresa = data.empresa[0];
	factura = data.fHead[0];
	productos = data.fBody;
	
	qz = document.getElementById("qz");
	qz.append("\x1B\x40"); //Reset todo
	qz.append("\x1B\x74\x02"); //Code page PC850
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
	qz.append("----------------------------------------\r\n");			
	qz.append(" Factura: "+factura.consecutivo+"\r\n"); 
	qz.append(" Fecha: "+factura.fecha+"\r\n"); 
	qz.append("----------------------------------------\r\n");
	qz.append(" Cliente: "+factura.cliente_ced+"\r\n");
	qz.append(" Nombre: "+factura.cliente_nom+"\r\n");
	qz.append("----------------------------------------\r\n");
	qz.append(" Tipo de Pago: "+factura.tipo+"\r\n"); 
	if(factura.tipo==='credito'){
		qz.append(" Dias de Credito: "+factura.diasCredito+"\r\n");
		qz.append(" Fecha de Vencimiento: "+factura.fechaVencimiento+"\r\n"); 
	}else if(factura.tipo==='mixto'){
		qz.append(" Pagado con Tarjeta: "+formatearNumero(factura.cantidadTarjeta)+"\r\n");
		qz.append(" Pagado con Contado: "+formatearNumero(factura.cantidadContado)+"\r\n");
	}
	qz.append(" Moneda: "+factura.moneda+"\r\n"); 
	qz.append(" Vendedor: "+factura.vendedor+"\r\n");
	qz.append("----------------------------------------\r\n");
	qz.append(" Cant. Desc.     Articulo        Precio \r\n");
	qz.append("----------------------------------------\r\n");
	
	//PROCESADO DE PRODUCTOS
	for(i = 0; productos.length>i; i++){
		cantidad = productos[i].cantidad;
		descuento = productos[i].descuento;
		
		cant = parseInt(cantidad);
		des = parseInt(descuento);
		precio = parseFloat(productos[i].precio);
		precio = cantidad * ( precio - ( precio * ( descuento / 100 ) ) );
		
		qz.append(formatearCantidad(cantidad)+formatearDescuento(descuento)+formatearCodigo(productos[i].codigo)+acomodarPrecio(formatearNumero(precio))+"\r\n");
		qz.append(" ->"+productos[i].descripcion+"\r\n");
	}
	
	qz.append("----------------------------------------\r\n");
	//Tiramos a la izquierda
	qz.append("\x1B\x61\x02");
	qz.append("Total: "+formatearNumero(factura.total)+"\r\n");
	qz.append("----------------------------------------\r\n");
	//Centramos 
	qz.append("\x1B\x61\x01");
	qz.append("Recibido conforme: ___________\r\n");
	qz.append(" \r\n");
	qz.append("Los precios incluyen impuestos de venta\r\n");
	qz.append("Gracias por su visita\r\n");
	qz.append(" \r\n");
	qz.append(empresa.leyenda+"\r\n");
	qz.append("\r\n\r\n\r\n\r\n\r\n\r\n");
	//Cortar
	qz.append("\x1D\x56\x48");
	qz.print();
}

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

function acomodarPrecio(precio){
	//Largo 11
	n = precio.length;
	while(n<11){
		precio = " "+precio;
		n++;
	}
	return precio;
}

function formatearCodigo(codigo){	
	//Largo 17
	codigo = "     "+codigo;
	n = codigo.length;
	while(n<17){
		codigo = codigo+" ";
		n++;
	}
	return codigo;
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

function prueba(){
	var qz = document.getElementById("qz");
	qz.append("\x1B\x40"); //Reset todo
	qz.append(" Prueba Normal \r\n");
	qz.append("________________\r\n");
	qz.append("\x1B\x21\x02");
	qz.append(" Prueba 02 \r\n");
	qz.append("\x1B\x21\x08");
	qz.append(" Prueba 08 \r\n");
	qz.append("\x1B\x21\x01");
	qz.append(" Prueba 01 \r\n");
	qz.append("\x1B\x21\x10");
	qz.append(" Prueba 10 \r\n");
	qz.append("\x1B\x21\x20");
	qz.append(" Prueba 20 \r\n");
	qz.append("\x1B\x21\x80");
	qz.append(" Prueba 80 \r\n");
	qz.append("________________\r\n");
	qz.append("\x1B\x21\x01");
	qz.append("\x1B\x2D\x01");
	qz.append(" Prueba Underline 1d \r\n");
	qz.append("\x1B\x2D\x02");
	qz.append(" Prueba Underline 2d \r\n");
	qz.append("\x1B\x21\x01");
	qz.append("\x1B\x2D\x00");
	qz.append("________________\r\n");
	qz.append("\x1B\x4D\x00");
	qz.append(" Prueba Fuente A \r\n");
	qz.append("\x1B\x4D\x01");
	qz.append(" Prueba Fuente B \r\n");
	qz.append("\x1B\x4D\x02");
	qz.append(" Prueba Fuente C \r\n");
	qz.append("________________\r\n");
	qz.append("\x1B\x4D\x00");
	qz.append("\x1B\x21\x01");
	qz.append("\x1B\x61\x00");
	qz.append(" Prueba Jus L \r\n");
	qz.append("\x1B\x61\x01");
	qz.append(" Prueba Jus C \r\n");
	qz.append("\x1B\x61\x02");
	qz.append(" Prueba Jus R \r\n");
	qz.print();
}

function prueba2(){
	var qz = document.getElementById("qz");
	qz.append("\x1B\x40"); //Reset todo
	qz.append("\x1B\x21\x10");
	qz.append("\x1B\x61\x02");
	qz.append(" Inversiones Garotas Bonitas S.A. \r\n");
	qz.print();
}

function prueba3(){
	var qz = document.getElementById("qz");
	qz.append("\x1B\x40"); //Reset todo
	qz.append("\x1B\x21\x01");
	qz.append("\x1B\x2D\x01");
	qz.append("Estoesunapruebadelinepordebajoparaverhastadondeescapazdeimprimrlarayaabajo\r\n");
	qz.append("\x1B\x2D\x00");
	qz.append("--------------------------------------------------------------------------\r\n");
	qz.print();
}






