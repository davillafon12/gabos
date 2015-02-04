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
	traerDocumento(t,d,n,s,i);	
}

function traerDocumento(t,d,n,s,i){
	$.ajax({
		url : protocolo+'//'+servidor+'/impresion',
		dataType : 'jsonp',
		data : "t="+t+"&d="+d+"&n="+n+"&s="+s+"&i="+i,
		success: function(data, textStatus, jqXHR)
		{
			try{				
				if(data.status==="error"){
					alert("Error: "+data.error);
				}else if(data.status==="success"){
					seleccionarTipoDocumento(d, data);				
				}
			}catch(e){
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
	}
	comenzarCierre();
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
	qz.append(" Nombre: "+factura.cliente_nom.substring(0, 31)+"\r\n");
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
	qz.append(" Vendedor: "+factura.vendedor.substring(0, 29)+"\r\n");
	qz.append("----------------------------------------\r\n");
	qz.append(" Articulo      Cant. Desc.      Precio  \r\n");
	qz.append("----------------------------------------\r\n");
	
	//PROCESADO DE PRODUCTOS
	for(i = 0; productos.length>i; i++){
		cantidad = productos[i].cantidad;
		descuento = productos[i].descuento;
		
		cant = parseInt(cantidad);
		des = parseInt(descuento);
		precio = parseFloat(productos[i].precio);
		precio = cantidad * ( precio - ( precio * ( descuento / 100 ) ) );
		
		qz.append(formatearCodigo(productos[i].codigo)+formatearCantidad(cantidad)+formatearDescuento(descuento)+acomodarPrecio(formatearNumero(precio))+"\r\n");
		qz.append(" ->"+productos[i].descripcion.substring(0, 36)+"\r\n");
	}
	
	qz.append("----------------------------------------\r\n");
	qz.append(enviarDerecha("Subtotal:"+formatearMontoTotal(formatearNumero(factura.subtotal)))+"\r\n");
	qz.append(enviarDerecha("IVA:"+formatearMontoTotal(formatearNumero(factura.total_iva)))+"\r\n");
	qz.append(enviarDerecha("Total:"+formatearMontoTotal(formatearNumero(factura.total)))+"\r\n");
	qz.append("----------------------------------------\r\n");
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






