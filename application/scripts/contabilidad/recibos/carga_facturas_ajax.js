function buscarCedula (e) {
    cedula = $("#cedula").val();
    getNombreCliente(cedula);
}

function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

function getNombreCliente(cedula){
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/contabilidad/recibos/getFacturasCliente',
		type: "POST",		
		//async: false,
		data: {'cedula':cedula},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				//alert(data.trim());
				informacion = $.parseJSON('[' + data.trim() + ']');
				//alert(JSON.stringify(informacion[0], null, 4));
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					//alert(JSON.stringify(informacion, null, 4));
					setInformacion(informacion[0]);
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

function setInformacion(informacionArray){
	$("#nombre").val(informacionArray.cliente.nombre+" "+informacionArray.cliente.apellidos);
	setFacturasPendientes(informacionArray.creditos);
}

function manejarErrores(error){
	switch(error){
		case '1':
			notyMsg('¡No se pudo tramitar la información, contacte al administrador!','error');
			resetFields();
		break;
		case '2':
			notyMsg('¡Error en el envio de la URL, contacte al administrador!','error');
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
			//No hay facturas pendientes
			notyMsg('¡Este cliente no tiene facturas pendientes!','success');
			resetFields();
		break;			
	}
}

function notyMsg(Mensaje, tipo){
	n = noty({
			   layout: 'topRight',
			   text: Mensaje,
			   type: tipo,
			   timeout: 4000
			});
}

function resetFields(){
	$("#tbody_posibles_facturas").html('');
	$("#tbody_facturas_a_saldar").html('');
	$("#saldo_a_pagar_input").val('');
	facturasMarcadas = [];
    facturasSaldar = [];
    facturasMarcadasEliminar = [];
    $("#comentarios").val("");
}

function setUpLiveSearch(){	
	$("#nombre").autocomplete({
		  source: location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/facturas/nueva/getNombresClientesBusqueda',
		  minLength: 1,
		  select: function( event, ui ) {
			$("#cedula").val(ui.item.id);
			evt = document.createEvent("KeyboardEvent");
			buscarCedula(evt); 		  
		  }
		});
}

function setFacturasPendientes(creditos){
	//alert(JSON.stringify(creditos, null, 4));	
	cuerpo = '';
	for(i=0; i<creditos.length; i++){
		//alert(JSON.stringify(creditos[i], null, 4));
		saldo = parseFloat(creditos[i].saldo);
		saldo = saldo.format(2, 3, '.', ',');
		cuerpo += "<tr class='bordes_tabla' onclick='marcarFactura("+creditos[i].credito_id+")' id='credito_row_"+creditos[i].credito_id+"'><td class='celdas_tabla'><p class='contact'>"+creditos[i].factura_consecutivo+"</p></td><td class='celdas_tabla'><p class='contact'>"+creditos[i].fecha_expedicion+"</p></td><td class='celdas_tabla'><p class='contact'>"+creditos[i].fecha_vencimiento+"</p></td><td class='celdas_tabla'><p class='contact' id='saldo_"+creditos[i].credito_id+"'>"+saldo+"</p></td></tr>"; 
	}
	$("#tbody_posibles_facturas").html(cuerpo);
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
