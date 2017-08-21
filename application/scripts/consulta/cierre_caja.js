var consecutivoActual = 0; // Guarda el consecutivo de la factura con la que se esta trabajando
var tipo_moneda = 'colones'; //Guarda el tipo de moneda que se esta cargando en la factura
var iva_por = 0; // Guarda el porcentaje del iva
var tipo_cambio = 1; //Guarda el tipo de cambio
var tipo_factura = ''; //Guarda el tipo de factura, ya que si es factura pendiente no la imprime
var sucursal = 0;
var servidorImpresion = 0;
var token = '';

$(function() {
	 $.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        prevText: '<Ant',
        nextText: 'Sig>',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
	$( "#fecha_desde" ).datepicker();
	$( "#fecha_hasta" ).datepicker();
	
	
	$( "#nombre" ).autocomplete({
		  source: location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/facturas/nueva/getNombresClientesBusqueda',
		  minLength: 1,
		  select: function( event, ui ) {
			$("#cedula").val(ui.item.id);				  
		  }
		});
	
	$("#consecutivo").numeric()
});

function llamarFacturas(){
	if(validarFechas()){		
		$.ajax({
			url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/consulta/getCierresFiltrados',
			type: "POST",	
			data: {'desde':$("#fecha_desde").val(),'hasta':$("#fecha_hasta").val()},				
			success: function(data, textStatus, jqXHR)
			{
				try{
					informacion = $.parseJSON('[' + data.trim() + ']');
					if(informacion[0].status==="error"){
						manejarErrores(informacion[0].error);
					}else if(informacion[0].status==="success"){
						montarFacturas(informacion[0].retiros);
					}
				}catch(e){
					notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{}
		});
	}
}

function validarFechas(){
	desde = $("#fecha_desde").val();
	hasta = $("#fecha_hasta").val();
	patt = /^(0?[1-9]|[12][0-9]|3[01])[\/](0?[1-9]|1[012])[\/]\d{4}$/;
	if(desde.trim()!=''){
		//Validamos
		if(!patt.test(desde.trim())){
			notyMsg('La fecha -desde- no tiene un formato válido', 'error');
			return false;
		}
		
		
	}
	if(hasta.trim()!=''){
		//Validamos
		if(!patt.test(hasta.trim())){
			notyMsg('La fecha -hasta- no tiene un formato válido', 'error');
			return false;
		}
		
	}
	
	if(desde.trim()!=''&&hasta.trim()!=''){
		//Pasar de dd/mm/yyyy a yyyy-mm-dd
		desde = desde.split("/");	
		desde = desde[2]+"-"+desde[1]+"-"+desde[0];
		desde = new Date(desde);
		//Pasar de dd/mm/yyyy a yyyy-mm-dd
		hasta = hasta.split("/");	
		hasta = hasta[2]+"-"+hasta[1]+"-"+hasta[0];
		hasta = new Date(hasta);
		
		if(desde>hasta){
			notyMsg('La fecha -desde- debe ser menor a la fecha -hasta-', 'error');
			return false;
		}
		
	}	
	return true;
}

function notyMsg(Mensaje, tipo){
	n = noty({
			   layout: 'topRight',
			   text: Mensaje,
			   type: tipo,
			   timeout: 4000
			});
}

function tiposSeleccionados(){
	tipos = [];
	selec = $("input[name='tipo']:checked");
	for(i=0; i<selec.length; i++){
		tipos.push({dato:selec[i].value});
	}
	return JSON.stringify(tipos);
}

function estadosSeleccionados(){
	tipos = [];
	selec = $("input[name='estado']:checked");
	for(i=0; i<selec.length; i++){
		tipos.push({dato:selec[i].value});
	}
	return JSON.stringify(tipos);
}

function manejarErrores(error){
	switch(error){
		case '1':
			notyMsg('No se puedo realizar la búsqueda, por favor contacte al administrador', 'error');
		break;
		case '2':
			notyMsg('No URL tiene un formato inválido, por favor contacte al administrador', 'error');
		break;
		case '3':
			notyMsg('No hay cierres de caja con los filtros ingresados', 'warning');
			$("#facturas_filtradas").html('');
		break;
		case '4':
			notyMsg('Alguna de las fechas ingresadas no tiene un formato válido', 'error');
		break;
		case '5':
			notyMsg('La fecha desde debe ser menor a la fecha hasta', 'error');
		break;
	}
}

function montarFacturas(facturas){
	cuerpoTabla = '';
	for(i = 0; i<facturas.length; i++){
		cuerpoTabla += "<tr class='bordes_tabla_factura' onclick='seleccionarFactura("+facturas[i].consecutivo+")'><td class='contact' style='text-align:center;'>"+facturas[i].consecutivo+"</td><td class='contact'>"+facturas[i].cliente+"</td><td class='contact' style='text-align:center;'>"+facturas[i].fecha+"</td></tr>";
	}
	$("#facturas_filtradas").html(cuerpoTabla);
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

function seleccionarFactura(factura){
	$("#consecutivo").val(factura);
	cargarFactura();
}

function cargarFactura(){
	consecutivo = $("#consecutivo").val();
	if(consecutivo.trim()===''){
		notyMsg('Debe ingresar un consecutivo válido', 'error');
		return false;
	}
	cargaServerFactura(consecutivo);
}

function cargaServerFactura(consecutivo){
	cargarEncabezado(consecutivo);
	
	consecutivoActual = consecutivo; //Asignamos el consecutivo actual para realizar operaciones	
}

function cargarEncabezado(consecutivo){
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+"/consulta/getCierreCaja",
		type: "POST",
		data: {'cierre':consecutivo},		
		success: function(data, textStatus, jqXHR)
		{			
			try{
				facturaHEAD = $.parseJSON('[' + data.trim() + ']');
				if(facturaHEAD[0].status==="error"){
					notyMsg("Error al cargar el cierre de caja, contacte al administrador. ERROR E"+facturaHEAD[0].error, "error");
					cleanTable();
				}else if(facturaHEAD[0].status==="success"){
					setEncabezadoFactura(facturaHEAD[0]);
					
					sucursal = facturaHEAD[0].sucursal;
					servidorImpresion = facturaHEAD[0].servidor_impresion;
					token = facturaHEAD[0].token;
				}
			}
			catch(e){
				alert(e);
				notyMsg("Error al cargar el cierre de caja, contacte al administrador. ERROR E0", "error");
			}			
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
} 
 


function cleanTable(){
	
}

function setEncabezadoFactura(cab){	
	//Tipo cambio
	$("#tipo_cambio_dolar").val(parseFloat(cab.cierre.tipo).format(2, 3, '.', ','));
	//Total
	$("#input_retiro_parcial").val(parseFloat(cab.cierre.conteo).format(2, 3, '.', ','));
	
	$("#fecha_cierre").html("Fecha : "+cab.cierre.fecha);
	$("#base_caja").val(parseFloat(cab.cierre.base).format(2, 3, '.', ','));
	$("#primera_factura").html("Primera Factura: "+cab.datos.primeraFactura);
	$("#ultima_factura").html("Última Factura: : "+cab.datos.ultimaFactura);
	$("#input_bn_servicios").val(parseFloat(cab.cierre.bnservicios).format(2, 3, '.', ','));
	$("#input_bn_servicios_credito").val(parseFloat(cab.cierre.bnserviciosc).format(2, 3, '.', ','));
	$("#input_bcr_servicios").val(parseFloat(cab.cierre.bcrservicios).format(2, 3, '.', ','));
	$("#input_bcr_servicios_credito").val(parseFloat(cab.cierre.bcrserviciosc).format(2, 3, '.', ','));
	cab.datos.bnserviciosc = parseFloat(cab.cierre.bnserviciosc);
	cab.datos.bcrserviciosc = parseFloat(cab.cierre.bcrserviciosc);
	
	for(i = 0; i < cab.billetes.length; i++){
		$("#cant_"+cab.billetes[i].denominacion).val(cab.billetes[i].cantidad);
		total = cab.billetes[i].denominacion * cab.billetes[i].cantidad;
		$("#total_"+cab.billetes[i].denominacion).html("₡ "+parseFloat(total).format(2, 3, '.', ','));
	}
	
	for(i = 0; i < cab.monedas.length; i++){
		$("#cant_"+cab.monedas[i].denominacion).val(cab.monedas[i].cantidad);
		total = cab.monedas[i].denominacion * cab.monedas[i].cantidad;
		$("#total_"+cab.monedas[i].denominacion).html("₡ "+parseFloat(total).format(2, 3, '.', ','));
	}
	
	for(i = 0; i < cab.dolares.length; i++){
		$("#cant_do_"+cab.dolares[i].denominacion).val(cab.dolares[i].cantidad);
		total = cab.dolares[i].denominacion * cab.dolares[i].cantidad;
		$("#total_do_"+cab.dolares[i].denominacion).html("$ "+parseFloat(total).format(2, 3, '.', ','));
	}
	
	cargarRetiros(cab.datos);
	cargarDatafonos(cab.datos);
	cargarMixto(cab.datos.pagoMixto);
	cab.datos.recibos.efectivoBK = cab.datos.recibos.efectivo;
	cab.datos.recibos.efectivo = cab.datos.recibos.efectivo - cab.datos.detalleNotasCredito.credito;
	cab.datos.recibos.efectivoBK = cab.datos.recibos.efectivo; 
	cab.datos.recibos.detalleNotasCredito = cab.datos.detalleNotasCredito.credito;
	cargarRecibosDinero(cab.datos.recibos);
	cargarTotales(cab);
	cargarVendedores(cab.datos.vendedores);
	cargarDetalleNotasCredito(cab.datos.detalleNotasCredito);
}

function cargarDetalleNotasCredito(detalle){
	$("#nota_credito_contado").html("₡"+parseFloat(detalle.contado).format(2, 3, '.', ','));
	$("#nota_credito_tarjeta").html("₡"+parseFloat(detalle.tarjeta).format(2, 3, '.', ','));
	$("#nota_credito_cheque").html("₡"+parseFloat(detalle.cheque).format(2, 3, '.', ','));
	$("#nota_credito_credito").html("₡"+parseFloat(detalle.credito).format(2, 3, '.', ','));
	$("#nota_credito_deposito").html("₡"+parseFloat(detalle.deposito).format(2, 3, '.', ','));
	$("#nota_credito_mixto").html("₡"+parseFloat(detalle.mixto).format(2, 3, '.', ','));
	$("#nota_credito_apartado").html("₡"+parseFloat(detalle.apartado).format(2, 3, '.', ','));
}

function cargarRetiros(datos){
	retiros = datos.retirosParciales;
	//contenido_retiros_parciales
	//total_retiros
	filas = "<tr><td colspan='3'><p class='titulo-2'>Retiros Parciales</p></td></tr>"
							+	"<tr>"
							+	"	<td class='borde-abajo'><p class='parrafo'># Retiro</p></td>"
							+	"	<td class='borde-abajo'><p class='parrafo'>Fecha y Hora</p></td>"
							+	"	<td class='borde-abajo'><p class='parrafo'>Total</p></td>"
						+	"	</tr>";
	
	if(retiros.length==0){
		filas = "<tr><td colspan='3'><p class='parrafo'>No hay retiros parciales. . .</p></td></tr>";
	}else{
		for(i = 0; i<retiros.length; i++){
			filas = filas + "<tr>"
											+		"<td><p class='parrafo'>"+retiros[i].Id+"</p></td>"
											+		"<td><p class='parrafo'>"+retiros[i].Fecha_Hora+"</p></td>"
											+		"<td class='alg-right'><p class='parrafo'>₡"+parseFloat(retiros[i].Monto).format(2, 3, '.', ',')+"</p></td>"
											+ "</tr>";
		}
	}
	
	filas = filas + "<tr>"
								+	"<td colspan='2' class='alg-right borde-arriba'><p class='parrafo'>Total:</p></td>	"								
									+"<td class='alg-right borde-arriba'><p class='parrafo' id='total_retiros'>₡"+parseFloat(datos.totalRecibosParciales).format(2, 3, '.', ',')+"</p></td>"
								+"</tr>";
	
	
	
	$("#contenido_retiros_parciales").html(filas);
	/*
		<?php
									if(!$retirosParciales){
										echo "<tr><td colspan='3'><p class='parrafo'>No hay retiros parciales. . .</p></td></tr>";
									}else{
										$contador = 1;
										foreach($retirosParciales as $ret){
											echo "<tr>
													<td><p class='parrafo'>$contador</p></td>
													<td><p class='parrafo'>".date('d-m-Y H:i:s', strtotime($ret->Fecha_Hora))."</p></td>
													<td class='alg-right'><p class='parrafo'>₡".number_format($ret->Monto,2,",",".")."</p></td>
												  </tr>";
											$contador++;
										}
									}
								?>
	*/
}


function cargarDatafonos(datos){
	/*
		<?php
									if(sizeOf($pagoDatafonos['datafonos'])==0){
										echo "<tr><td colspan='3'><p class='parrafo'>No hay datáfonos registrados. . .</p></td></tr>";
									}else{
										foreach($pagoDatafonos['datafonos'] as $datafono){
											echo "
												<tr>
													<td><p class='parrafo' style='font-size: 11px;'>".$datafono->Banco_Codigo." - ".$datafono->Banco_Nombre."</p></td>
													<td class='alg-right'><p class='parrafo'>₡".number_format($datafono->Total_Comision,2,",",".")."</p></td>
													<td class='alg-right'><p class='parrafo'>₡".number_format($datafono->Total_Retencion,2,",",".")."</p></td>
													<td class='alg-right'><p class='parrafo'>₡".number_format($datafono->Total,2,",",".")."</p></td>
												</tr>
											";
										}
									}									
								?>
	*/
	
	bancos = datos.pagoDatafonos.datafonos;
	gen = datos.pagoDatafonos;
	//contenido_retiros_parciales
	//total_retiros
	filas = "<tr><td colspan='4'><p class='titulo-2'>Datáfonos</p></td></tr>"
							+"	<tr>"
							+"		<td class='borde-abajo'><p class='parrafo'>Banco</p></td>"
							+"		<td class='borde-abajo'><p class='parrafo'>Comisión</p></td>"
							+"		<td class='borde-abajo'><p class='parrafo'>Retención</p></td>"
							+"		<td class='borde-abajo'><p class='parrafo'>Total</p></td>"
							+"	</tr>";
	
	if(bancos.length==0){
		filas = filas + "<tr><td colspan='3'><p class='parrafo'>No hay pagos en datáfono. . .</p></td></tr>";
	}else{
		for(i = 0; i<bancos.length; i++){
			filas = filas + "<tr>"
												+"	<td><p class='parrafo' style='font-size: 11px;'>"+bancos[i].Banco_Codigo+" - "+bancos[i].Banco_Nombre+"</p></td>"
												+"	<td class='alg-right'><p class='parrafo'>₡"+parseFloat(bancos[i].Total_Comision).format(2, 3, '.', ',')+"</p></td>"
												+"	<td class='alg-right'><p class='parrafo'>₡"+parseFloat(bancos[i].Total_Retencion).format(2, 3, '.', ',')+"</p></td>"
												+"	<td class='alg-right'><p class='parrafo'>₡"+parseFloat(bancos[i].Total).format(2, 3, '.', ',')+"</p></td>"
												+"</tr> ";
		}
	}
	
	filas = filas + "<tr>"
									+"<td class='alg-right borde-arriba'><p class='parrafo'>Totales:</p></td>	"
									+"<td class='alg-right borde-arriba'><p class='parrafo'>₡"+parseFloat(gen.totalComision).format(2, 3, '.', ',')+"</p></td>"
									+"<td class='alg-right borde-arriba'><p class='parrafo'>₡"+parseFloat(gen.totalRetencion).format(2, 3, '.', ',')+"</p></td>"
									+"<td class='alg-right borde-arriba'><p class='parrafo'>₡"+parseFloat(gen.totalDatafonos).format(2, 3, '.', ',')+"</p></td>"
							+"	</tr>";
	filas = filas + "<tr>"
									+"<td class='alg-right borde-arriba' colspan='3'><p class='parrafo'>Total Con BN Servicios (Tarjetas):</p></td>	"
									+"<td class='alg-right borde-arriba'><p class='parrafo'>₡"+(datos.bnserviciosc+datos.bcrserviciosc+parseFloat(gen.totalDatafonos)).format(2, 3, '.', ',')+"</p></td>"
							+"	</tr>";
	$("#contenido_datafonos").html(filas);
}

function cargarMixto(mixto){
	$("#cant_facturas_mixto").html(mixto.cantidadFacturas);
	$("#total_efectivo_mixto").html("₡"+parseFloat(mixto.efectivo).format(2, 3, '.', ','));
	$("#total_tarjeta_mixto").html("₡"+parseFloat(mixto.tarjeta).format(2, 3, '.', ','));
	$("#total_mixto").html("₡"+parseFloat(mixto.total).format(2, 3, '.', ','));
}

function cargarRecibosDinero(recibos){
	$("#recibo_contado").html("₡"+parseFloat(recibos.efectivo).format(2, 3, '.', ','));
	$("#recibo_tarjeta").html("₡"+parseFloat(recibos.tarjeta).format(2, 3, '.', ','));
	$("#recibo_deposito").html("₡"+parseFloat(recibos.deposito).format(2, 3, '.', ','));
	var abonos = $.isNumeric(recibos.abonos) ? parseFloat(recibos.abonos).format(2, 3, '.', ',') : "0.00";
	$("#recibo_abono").html("₡"+abonos);
	$("#total_recibos_dinero").html("₡"+parseFloat(recibos.total-recibos.detalleNotasCredito).format(2, 3, '.', ','));
}

function cargarTotales(datos){
	baseCaja = parseFloat(datos.cierre.base);
	totalRetiroFinal = parseFloat(datos.cierre.conteo)
	bnservicios = parseFloat(datos.cierre.bnservicios);
	bcrservicios = parseFloat(datos.cierre.bcrservicios);
	datos = datos.datos;
	totalRetiros = parseFloat(datos.totalRecibosParciales);
	//totalEfectivo = (totalRetiros + totalRetiros) - baseCaja;
	//console.log(datos);
	var totalEfectivo = totalRetiros; 
	totalEfectivo -= datos.recibos.efectivo;
	totalEfectivo -= datos.recibos.abonos;
	totalEfectivo -= bnservicios - bcrservicios;
	totalEfectivo += datos.detalleNotasCredito.contado;
	//totalEfectivo -= datos.pagoMixto.efectivo;
	totalEfectivo -= datos.totalFacturasContado;
	
	
	
	$("#totales_factura_contado").html("₡"+parseFloat((datos.totalFacturasContado+datos.pagoMixto.efectivo)-datos.detalleNotasCredito.contado).format(2, 3, '.', ','));
	$("#totales_efectivo").html("₡"+totalEfectivo.format(2, 3, '.', ','));
	$("#totales_tarjetas").html("₡"+parseFloat(datos.pagoDatafonos.totalDatafonos+datos.bnserviciosc+datos.bcrserviciosc-datos.detalleNotasCredito.tarjeta).format(2, 3, '.', ','));
	$("#totales_creditos").html("₡"+parseFloat(datos.totalCreditos.totalCredito-datos.detalleNotasCredito.credito).format(2, 3, '.', ','));
	$("#totales_encomienda").html("₡"+parseFloat(datos.totalFacturasDeposito-datos.detalleNotasCredito.deposito).format(2, 3, '.', ','));
	$("#totales_apartados").html("₡"+parseFloat(datos.totalCreditos.totalApartado-datos.detalleNotasCredito.apartado).format(2, 3, '.', ','));
	$("#totales_notas_credito").html("₡"+parseFloat(datos.totalNotasCredito.total).format(2, 3, '.', ','));
	$("#totales_notas_debito").html("₡"+parseFloat(datos.totalNotasDebito.total).format(2, 3, '.', ','));
	
	$("#totalVendido").html("₡"+parseFloat(datos.valoresFinales.totalFacturas).format(2, 3, '.', ','));
	$("#totalIVA").html("₡"+parseFloat(datos.valoresFinales.totalIVA).format(2, 3, '.', ','));
	$("#totalRetencion").html("₡"+parseFloat(datos.valoresFinales.totalRetencion-datos.totalNotasCredito.retencion).format(2, 3, '.', ','));
}

function cargarVendedores(vendedores){
	totalVendedores = vendedores.totalVendido;
	vendedores = vendedores.vendidoVendedores;
	
	filas = "<tr><td colspan='7'><p class='titulo-2'>Vendedores</p></td></tr>"
								+"<tr>"
									+	"	<td class='borde-abajo'><p class='parrafo'>Vendedor</p></td>"
									+	"	<td class='borde-abajo'><p class='parrafo'>Vendido</p></td>"
									+	"</tr>	";
	
	for(i=0;i<vendedores.length;i++){
		if(vendedores[i][0].usuario != null){
				filas = filas + "<tr>"
											+	"	<td><p class='parrafo'>"+vendedores[i][0].usuario+"</p></td>"
											+	"	<td><p class='parrafo'>"+parseFloat(vendedores[i][0].total_vendido).format(2, 3, '.', ',')+"</p></td>"
											+	"</tr>	"
											;
		}
	}
	
	filas = filas + "<tr>"
									+ "<td colspan='7' class='alg-right borde-arriba'><p class='parrafo'>Total Vendedores: ₡"+parseFloat(totalVendedores).format(2, 3, '.', ',')+"</p></td>"
								  + "</tr>";
										
	
	$("#tabla_vendedores").html(filas);
}

// Para el tamaño del windows open
var anchoImpresion = 1024;
var alturaImpresion = 768;
var tipoImpresion = 'c';

function cambiarTipoImpresion(tipo){
	tipoImpresion = tipo;
	switch(tipo){
		case 't':
			anchoImpresion = 290;
			alturaImpresion = 400;
		break;
		case 'c':
			anchoImpresion = 1024;
			alturaImpresion = 768;
		break;
	}
}

function imprimir(){
	consecutivo = $("#consecutivo").val();
	if(consecutivo.trim()===''){
		notyMsg('Debe ingresar un consecutivo válido', 'error');
		return false;
	}else if(consecutivo!=consecutivoActual){
		notyMsg('El consecutivo ingresado no coincide con el cargado', 'error');
		return false;
	}else if(tipo_factura === 'pendiente'){
		notyMsg('No puede imprimir facturas pendientes', 'error');
		return false;
	}
	
	if(tipoImpresion==='t'){
		//Impresion termica
		//window.open(servidorImpresion+'/index.html?t='+token+'&d=nc&n='+consecutivoActual+'&s='+sucursal+'&i='+tipoImpresion+'&server='+document.domain+'&protocol='+location.protocol,'Impresion de Facturas','width='+anchoImpresion+',height='+alturaImpresion+',resizable=no,toolbar=no,location=no,menubar=no');
	}else if(tipoImpresion==='c'){
		//Impresion carta
		window.open(location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/impresion?t='+token+'&d=cc&n='+consecutivoActual+'&s='+sucursal+'&i='+tipoImpresion,'Impresion de Facturas','width='+anchoImpresion+',height='+alturaImpresion+',resizable=no,toolbar=no,location=no,menubar=no');
	}
}

