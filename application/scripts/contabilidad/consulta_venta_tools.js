function cargarInfoSucursal(){
	sucursal = $("#sucursal").val();
	
	if(sucursal==='-1'){
		limpiarDatos();
		return false;
	}
	
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')'/contabilidad/consultaVenta/getDatosConsultaVenta',
		type: "POST",		
		async: false,
		data: {'sucursal':sucursal},				
		success: function(data, textStatus, jqXHR)
		{
			try{				
				informacion = $.parseJSON('[' + data.trim() + ']');				
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
					limpiarDatos();
				}else if(informacion[0].status==="success"){	
					cargarDatos(informacion[0]);
				}
			}catch(e){
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

function limpiarDatos(){
	$("#primera_factura").html('0');
	$("#ultima_factura").html('0');
	$("#hora").html('0');
	$("#total").html('0');
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
			notyMsg('¡No se puedo procesar los datos requeridos, contacte al administrador!', 'error');
			limpiarDatos();
		break
		case '2':
			notyMsg('¡URL con formato indebido, contacte al administrador!', 'error');
			limpiarDatos();
		break
		case '3':
			notyMsg('¡La sucursal escogida no existe!', 'error');
			limpiarDatos();
		break
	}
}

function cargarDatos(info){
	$("#primera_factura").html(info.primeraFactura);
	$("#ultima_factura").html(info.ultimaFactura);
	$("#hora").html(info.fecha);
	$("#hora_cierre").html(info.fecha_cierre);
	venta = parseFloat(info.venta);
	venta = venta.format(2, 3, '.', ',');
	$("#total").html(venta);
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