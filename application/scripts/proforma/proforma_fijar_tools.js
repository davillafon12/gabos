var consecutivoActual = 0; // Guarda el consecutivo de la factura con la que se esta trabajando
var tipo_moneda = 'colones'; //Guarda el tipo de moneda que se esta cargando en la factura
var iva_por = 0; // Guarda el porcentaje del iva
var tipo_cambio = 1; //Guarda el tipo de cambio
var tipo_factura = ''; //Guarda el tipo de factura, ya que si es factura pendiente no la imprime
var sucursal = 0;
var servidorImpresion = 0;
var token = '';
var clienteEsDeTipoSucursal = "0";
var clienteEsDeTipoExento = "0";
var clienteNoAplicaRetencion = "0";
var clienteCanBuy = true;
var infoClientePostAutorizacion = false;
var cedulaPostAuto = false;
var clienteEsExento = false;
var cliente_cedula = 0;

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
	
	$("#consecutivo").numeric();
	setMainValues();
});

function llamarFacturas(){
			
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/facturas/proforma/getFacturasSinProcesar',
		type: "POST",	
		data: {'cliente':$("#cedula").val()},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					montarFacturas(informacion[0].facturas);
				}
			}catch(e){
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
	
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
			notyMsg('No hay proformas con los filtros ingresados', 'warning');
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
		cuerpoTabla += "<tr class='bordes_tabla_factura' onclick='seleccionarFactura("+facturas[i].consecutivo+")'><td class='contact' style='text-align:center;'>"+facturas[i].consecutivo+"</td><td class='contact'>"+facturas[i].cliente+"</td><td class='contact' style='text-align:center;'>"+facturas[i].fecha+"</td><td class='contact' style='text-align:right;   padding-right: 20px;'>"+parseFloat(facturas[i].total).format(2, 3, '.', ',')+"</td></tr>";
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
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+"/facturas/caja/getProformaHeadersConsulta",
		type: "POST",
		data: {'consecutivo':consecutivo},		
		success: function(data, textStatus, jqXHR)
		{			
			try{
				facturaHEAD = $.parseJSON('[' + data.trim() + ']');
				if(facturaHEAD[0].status==="error"){
					notyMsg("Error al cargar la proforma, contacte al administrador. ERROR E"+facturaHEAD[0].error, "error");
					cleanTable();
				}else if(facturaHEAD[0].status==="success"){
					setEncabezadoFactura(facturaHEAD);
					cargarProductos(consecutivo);
					
					tipo_factura = facturaHEAD[0].tipo; 
					sucursal = facturaHEAD[0].sucursal;
					servidorImpresion = facturaHEAD[0].servidor_impresion;
					token = facturaHEAD[0].token;
					cliente_cedula = facturaHEAD[0].cedula;

				}
			}
			catch(e){
				notyMsg("Error al cargar la factura, contacte al administrador. ERROR E0", "error");
			}			
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

function cargarProductos(consecutivo){
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+"/facturas/caja/getArticulosProformaConsulta",
		type: "POST",
		data: {'consecutivo':consecutivo},		
		success: function(data, textStatus, jqXHR)
		{			
			//try{
				facturaBODY = $.parseJSON('[' + data.trim() + ']');
				if(facturaBODY[0].status==="error"){
					notyMsg("Error al cargar la factura, contacte al administrador. ERROR B"+facturaHEAD[0].error, "error");
					cleanTable();
					$("#boton_procesar").css("background","rgba(142, 68, 173, 0.54)");
					$("#boton_procesar").css("cursor","not-allowed");
					$("#boton_procesar").prop('disabled', true);
				}else if(facturaBODY[0].status==="success"){
					setProductosFactura(facturaBODY[0].productos);
					$("#boton_procesar").css("background","rgb(142, 68, 173)");
					$("#boton_procesar").css("cursor","pointer");
					$("#boton_procesar").prop('disabled', false);
				}
/*
			}
			catch(e){
				notyMsg("Error al cargar la factura, contacte al administrador. ERROR B0", "error");
				console.log(e);
			}	
*/		
		},
		error: function (jqXHR, textStatus, errorThrown)
		{notyError('¡Hubo un error al cargar la factura!');}
	});
}

function cleanTable(){
	elmtTable = document.getElementById('tabla_productos');
	tableRows = elmtTable.getElementsByTagName('tr');
	rowCount = tableRows.length;

	for (var x=rowCount-1; x>0; x--) {
	   elmtTable.removeChild(tableRows[x]);
	}
	clienteEsDeTipoSucursal = "0";
	clienteEsDeTipoExento = "0";
	clienteNoAplicaRetencion = "0";
}

function setEncabezadoFactura(facturaHEAD){	
	$("#observaciones").val(facturaHEAD[0].observaciones);
	
	//Traemos decimales
	decimales = parseInt(decimales); //Esta variable se inicializa en la vista!!
	
	iva_por = parseFloat(facturaHEAD[0].ivapor);
	tipo_cambio = parseFloat(facturaHEAD[0].cambio);
	tipo_moneda = facturaHEAD[0].moneda;
	
	//Costos totales
	total = parseFloat(facturaHEAD[0].total);
	iva = parseFloat(facturaHEAD[0].iva);
	costo = parseFloat(facturaHEAD[0].costo);
	retencion = parseFloat(facturaHEAD[0].retencion);
	
	if(tipo_moneda==='colones'){
		$(".tipo_moneda_display").html("₡");
	}else if(tipo_moneda==='dolares'){
		$(".tipo_moneda_display").html("$");
		total = total / tipo_cambio;
		iva = iva / tipo_cambio;
		costo = costo / tipo_cambio;
		retencion = retencion / tipo_cambio;
	}	
	
	$("#costo_total").val(total.format(decimales, 3, '.', ','));
	$("#iva").val(iva.format(decimales, 3, '.', ','));
	$("#costo").val(costo.format(decimales, 3, '.', ','));
	$("#retencion").val(retencion.format(decimales, 3, '.', ','));
	
	//Cargamos info del cliente
	clienteEsDeTipoSucursal = facturaHEAD[0].cliente_sucursal;
	clienteEsDeTipoExento = facturaHEAD[0].cliente_exento;
	clienteNoAplicaRetencion = facturaHEAD[0].cliente_retencion;
	
}

function setProductosFactura(productos){	
	$("#contenidoArticulos").html('');
	table = document.getElementById("tabla_productos").getElementsByTagName('tbody')[0];
	isCajaLoad=false;
	cantidad = productos.length;
	//artHTML = '';
	array_pos_rows = []; //Reiniciamos el index, para utilizar el metodo de crear JSON de factura de los articulos
	for (var i = 0; i < cantidad; i++) 
	{
		array_pos_rows[i] = i+1;
		row = table.insertRow(table.rows.length);	
		row.setAttribute("id","articulo_"+(i+1));
		decimales = $("#cantidad_decimales").val();
		decimales = parseInt(decimales);
		precio = parseFloat(productos[i].precio);
		precio = precio.toFixed(decimales);
		cell1 = row.insertCell(0);
		cell2 = row.insertCell(1);
		cell3 = row.insertCell(2);
		cell4 = row.insertCell(3);
		cell5 = row.insertCell(4);
		cell6 = row.insertCell(5);
		cell7 = row.insertCell(6);
		
		bodegaINT = productos[i].bodega;
		
		
		cell1.innerHTML = "<img class='imagen_arrow' title='Agregar Fila' src='/../application/scripts/Images/agregar_row.gif' width='14' height='7' onClick='agregarByCM("+(i+1)+")'/>"
				+"<input tabindex='"+(i+1)+"' id='codigo_articulo_"+(i+1)+"' class='input_codigo_articulo' autocomplete='off' name='codigo_articulo' type='text' onkeyup='buscarArticulo(event, this.value, this.id);' onkeydown='filtrarKeys(event, this.id);' value='"+productos[i].codigo+"' disabled>"
				+"<input id='codigo_articulo_anterior_"+(i+1)+"' type='hidden' value='"+productos[i].codigo+"'>";
		cell2.innerHTML = "<div class='articulo_specs' id='descripcion_articulo_"+(i+1)+"'>"+productos[i].descripcion+"</div>"
				+"<div class='tooltip_imagen_articulo' id='tooltip_imagen_articulo_"+(i+1)+"'><img src='"+location.protocol+"//"+document.domain+(location.port ? ':'+location.port: '')+"/application/images/articulos/"+productos[i].imagen+"' height='200' width='200'></div>";
		cell3.innerHTML = "<input id='cantidad_articulo_"+(i+1)+"' class='cantidad_articulo' autocomplete='off' name='cantidad_articulo' type='number' min='1' max='"+bodegaINT+"' onchange='cambiarCantidad(this.id, event, this.value);' onkeyup='cambiarCantidad(this.id, event, this.value);' value='"+productos[i].cantidad+"' disabled>"
				+"<input id='cantidad_articulo_anterior_"+(i+1)+"' type='hidden' value='"+productos[i].cantidad+"'>";
		cell4.innerHTML = "<div class='articulo_specs' id='bodega_articulo_"+(i+1)+"'>"+bodegaINT+"</div>";
		cell5.innerHTML = "<div class='articulo_specs' id='descuento_articulo_"+(i+1)+"' ondblclick='changeDiscount("+(i+1)+")'>"+productos[i].descuento+"</div>";
		
		precioUI = parseFloat(precio);
		precioUI = precioUI.format(2, 3, '.', ',');
		
		cell6.innerHTML = "<div class='articulo_specs' id='costo_unidad_articulo_"+(i+1)+"'>"+precioUI+"</div>"
				+"<input id='costo_unidad_articulo_ORIGINAL_"+(i+1)+"' type='hidden' value='"+productos[i].precio+"'>"
				+"<input id='costo_unidad_articulo_FINAL_"+(i+1)+"' type='hidden' value='"+productos[i].precioFinal+"'>"
				+"<input id='producto_exento_"+(i+1)+"' type='hidden' value='"+productos[i].exento+"'>"
				+"<input id='producto_retencion_"+(i+1)+"' type='hidden' value='"+productos[i].retencion+"'>";
		cell7.innerHTML = "<div class='articulo_specs' id='costo_total_articulo_"+(i+1)+"'></div>"
						  +"<input type='hidden' id='costo_total_articulo_sin_descuento_"+(i+1)+"'/>";
		
		
		//Agregamos las demas funciones de cada row
		agregarTooltip("#descripcion_articulo_"+(i+1));
		updateProductsTotal();
	}
	setCostos(cantidad);
	isCajaLoad=true;
}

function agregarTooltip(id_Row){
	$(id_Row).mouseover(function(){
		eleOffset = $(this).offset();

		$(this).next().fadeIn("fast").css({
			left: eleOffset.left + 100,
			top: eleOffset.top - 100
		});
		
	}).mouseout(function(){
		$(this).next().hide();
	});
}

function setCostos(cantidad){	
	for (var i = 0; i < cantidad; i++) 
	{	actualizaCostoTotalArticulo("cantidad_articulo_"+(i+1));
	}
}

function cantidadFilas(tabla)
{
	return tabla.getElementsByTagName("tr").length-1;
}

function updateProductsTotal(){
	var table = document.getElementById("tabla_productos");
	var rows = cantidadFilas(table);
	var cantidad_productos = 0;
	for (var i = 0; i < rows; i++) 
	{
		cantidad_productos = cantidad_productos + parseInt(getCantidadProducto(i+1));
	}
	
	//alert(cantidad_productos);
	document.getElementById("cant_total_articulos").innerHTML=cantidad_productos;
}

function getCantidadProducto(num_row){
	//alert(num_row);
	var cantidad_productos = document.getElementById("cantidad_articulo_"+num_row).value;
	
	//return cantidad_productos;
	if(cantidad_productos.trim()!='')
	{
		var cantidad_productos_float = parseInt(cantidad_productos);
		return cantidad_productos_float;
	}
	
	return 0;
}

function actualizaCostoTotalArticulo(id){
	
	var precioFinalSinDescuento = 0.0;
	
	if(isCajaLoad){
		validarMaxMinCantidad(id);
	}

	num_row = id.replace("cantidad_articulo_","");

	cantidad = document.getElementById("cantidad_articulo_"+num_row).value;
	updateProductsTotal();
	

	
	
	descuento = document.getElementById('descuento_articulo_'+num_row).innerHTML;
	precio_unidad = document.getElementById('costo_unidad_articulo_ORIGINAL_'+num_row).value;

	moneda = document.getElementById("tipo_moneda").value;
	
	if(moneda.indexOf('dolare') != -1){
			tipo_cambio_venta = document.getElementById("tipo_cambio_venta").value;
			factor_tipo_moneda_float = parseFloat(tipo_cambio_venta);
			precio_unidad = precio_unidad/factor_tipo_moneda_float;
	}
		
	//Conversion de tipos
	descuento_float = parseFloat(descuento);
	precio_unidad_float = parseFloat(precio_unidad);
	cantidad_float = parseFloat(cantidad);
	//Calculos matematicos
	descuento_float = descuento_float/100;
	descuento_float = descuento_float*precio_unidad_float;
	
	precioFinalSinDescuento = precio_unidad_float * cantidad_float;
	
	precio_final_por_unidad = precio_unidad_float - descuento_float;
	precio_total = precio_final_por_unidad * cantidad_float;
	//Cargamos la cantidad de decimales permitidos
	decimales = document.getElementById("cantidad_decimales").value;
	decimales_int = parseInt(decimales);
	//Cargamos el valor al div
	costo_total = document.getElementById("costo_total_articulo_"+num_row);
	$("#costo_total_articulo_sin_descuento_"+num_row).val(precioFinalSinDescuento);
	
	//Formateamos el valor
	precio_total = precio_total.toFixed(decimales_int);
	precio_total = parseFloat(precio_total);		
	precio_total = precio_total.format(2, 3, '.', ',');	
	costo_total.innerHTML = precio_total;

	//Actualizamos el costo total
	actualizaCostosTotales(decimales_int);
	codigo = document.getElementById("codigo_articulo_"+num_row).value;

	inventario = document.getElementById("cantidad_articulo_"+num_row).value;
	
	
}

function actualizaCostosTotales(decimales_int){

	costo_sin_IVA_factura = 0.0;
	costo_sin_IVA_factura_sin_descuento= 0.0;
	costo_total_factura = 0.0;
	IVA_Factura = 0.0;
	IVA_Factura_Sin_Descuento = 0.0;
	costo_cliente_final = 0.0;
	costo_retencion = 0.0;
	
	
	table = document.getElementById("tabla_productos");
	rows = cantidadFilas(table);
	
	//Recorremos la tabla
	for (i = 0; i < rows; i++) 
	{	
		if($("#descripcion_articulo_"+(i+1)).html().trim()===''){}
		else{
				//Obtenemos el precio total de la linea del producto
				precio_unitario_articulo = getPrecioTotalRow(i+1);
				var precioUnitarioSinDescuento = $.isNumeric($("#costo_total_articulo_sin_descuento_"+(i+1)).val()) ? parseFloat($("#costo_total_articulo_sin_descuento_"+(i+1)).val()) : 0.0;
				
				//Obtenemos el precio de cliente final de la linea del producto
				precio_cliente_final_articulo = getPrecioTotalRowFINAL(i+1);
				//Obtenemos si el producto es exento de impuestos	
				isExento = document.getElementById("producto_exento_"+(i+1)).value;
				//Obtenemos si el producto es exento de retencion	
				noRetencion = document.getElementById("producto_retencion_"+(i+1)).value;
				//Iniciamos la variable apra procesar el articulo sin IVA
				costo_unitario_articulo_sin_IVA = 0.0;
				var costoUnitarioSinIvaSinDescuento = 0.0;
				
				if(isExento==='0'){ //SI NO ES exento entonces saca el precio sin IVA
					costo_unitario_articulo_sin_IVA = precio_unitario_articulo/(1+porcentaje_iva);	
					costoUnitarioSinIvaSinDescuento = precioUnitarioSinDescuento - (precioUnitarioSinDescuento/(1+porcentaje_iva));	
					console.log("Re: "+noRetencion);	
					if(noRetencion==='0'){//Sacamos el valor de los impuestos por RETENCION
							//Si aplica la retencion sacamos la base imponible del producto final
							precio_cliente_final_articulo_sin_iva = precio_cliente_final_articulo/(1+porcentaje_iva);
							//Luego obtenemos la cantidad de IVA por pagar
							costo_retencion += precio_cliente_final_articulo-precio_cliente_final_articulo_sin_iva;
					}else if(noRetencion==='1'){
							//Si no aplica la retencion tons realizamos la sumatoria del impuesto del producto normal
							costo_retencion += costoUnitarioSinIvaSinDescuento;
					}					
				}
				else if(isExento==='1'){ //SI ES exento entonces el precio sera el mismo
					costo_unitario_articulo_sin_IVA = precio_unitario_articulo;
				}
				
				//Sumamos todos los costos SIN IVA
				costo_sin_IVA_factura += costo_unitario_articulo_sin_IVA;
				costo_sin_IVA_factura_sin_descuento += costoUnitarioSinIvaSinDescuento;
				//Sumamos el costo TOTAL de la factura
				costo_total_factura += precio_unitario_articulo;
				//Sacamos el total de un CLIENTE FINAL
				costo_cliente_final += precio_cliente_final_articulo;					
		}		
	}
	
	moneda = document.getElementById("tipo_moneda").value;
	if(moneda.indexOf('colone') != -1){
		costo_cliente_final = costo_cliente_final - costo_total_factura;
	}
	else{
		tipo_cambio_venta = document.getElementById("tipo_cambio_venta").value;
		factor_tipo_moneda_float = parseFloat(tipo_cambio_venta);
		costo_cliente_final = (costo_cliente_final/factor_tipo_moneda_float) - costo_total_factura;
		
		//Si es dolares, pasamos la retencion de colones a dolares
		costo_retencion = costo_retencion/factor_tipo_moneda_float;
	}
	console.log("Retencion: "+costo_retencion);
	
	//Formateo e impresion a UI
	//precio_unidad_FACTOR_float = precio_unidad_FACTOR_float.format(2, 3, '.', ',');
	
	IVA_Factura = costo_total_factura-costo_sin_IVA_factura;
	//A este punto el costo de la retencion va con todos los impuestos del cliente final
	//Ahora le quitamos los impuestos del cliente afiliado para onbtener la retencion real
	costo_retencion -= costo_sin_IVA_factura_sin_descuento;
	
	if(clienteEsDeTipoExento=="1"){
		costo_total_factura -= IVA_Factura;
		IVA_Factura = 0;
		costo_retencion = 0;
	}//Si el cliente es exento de impuestos
	
	if(!aplicarRetencionHacienda){
		//SI NO APLICA RETENCION
		costo_retencion = 0;
	}
	
	if(clienteNoAplicaRetencion=="1"){
		//Variable se inicializa en facturas_call
		//SI NO APLICA RETENCION
		costo_retencion = 0;
	}
	
	//Calculamos la ganancia y le quitamos la retencion
	costo_cliente_final -= costo_retencion;
	costo_cliente_final = costo_cliente_final.toFixed(decimales_int);
	
	costo_sin_IVA_factura = costo_sin_IVA_factura.toFixed(decimales_int);
	IVA_Factura = IVA_Factura.toFixed(decimales_int);
	
	//Al costo total le agregamos la retencion
	costo_total_factura += costo_retencion;
	costo_total_factura = costo_total_factura.toFixed(decimales_int);
	
	costo_retencion = costo_retencion.toFixed(decimales_int);
	
	//Como el toFixed devuelve un string debemos convertirlos de nuevo a float para formatear
	costo_cliente_final = parseFloat(costo_cliente_final);
	costo_sin_IVA_factura = parseFloat(costo_sin_IVA_factura);
	IVA_Factura = parseFloat(IVA_Factura);
	costo_total_factura = parseFloat(costo_total_factura);
	costo_retencion = parseFloat(costo_retencion);
	
	console.log(costo_cliente_final);
	console.log(costo_sin_IVA_factura);
	console.log(IVA_Factura);	
	console.log(costo_total_factura);
	console.log(costo_retencion);
	
	//Formateamos
	costo_cliente_final = costo_cliente_final.format(decimales_int, 3, '.', ',');
	costo_sin_IVA_factura = costo_sin_IVA_factura.format(decimales_int, 3, '.', ',');
	IVA_Factura = IVA_Factura.format(decimales_int, 3, '.', ',');
	costo_total_factura = costo_total_factura.format(decimales_int, 3, '.', ',');
	costo_retencion = costo_retencion.format(decimales_int, 3, '.', ',');
	
	
	
	
	$("#ganancia").val(costo_cliente_final);
	$("#costo").val(costo_sin_IVA_factura);
	$("#iva").val(IVA_Factura);
	$("#retencion").val(costo_retencion);
	$("#costo_total").val(costo_total_factura);
	
	/*alert(typeof costo_cliente_final);
	document.getElementById("ganancia").value = costo_cliente_final;
	
	document.getElementById("costo").value = costo_sin_IVA_factura;
	document.getElementById("iva").value = IVA_Factura;
	
	document.getElementById("costo_total").value = costo_total_factura;*/
}

function getPrecioTotalRow(num_row){

	
	precio_total = document.getElementById("costo_total_articulo_"+num_row).innerHTML;
	
	//Quitamos el formato de moneda para que se lea bien
	precio_total = precio_total.replace(/\./g,'').replace(/,/g,'.');
	console.log(precio_total);
	if(precio_total.trim()!='')
	{
		precio_total_float = parseFloat(precio_total);
				return precio_total_float;
	}
	
	return 0.0;
}

function getPrecioTotalRowFINAL(num_row){
	
	precio_total = document.getElementById("costo_unidad_articulo_FINAL_"+num_row).value;
	//alert(num_row);
	if(precio_total.trim()!='')
	{
		precio_total_float = parseFloat(precio_total);
		cantidad = document.getElementById("cantidad_articulo_"+num_row).value;
		precio_total_float = precio_total_float*cantidad;
		//alert(precio_total_float);
		return precio_total_float;
	}
	
	return 0.0;
}

var porcentaje_iva = 0.0;

function getIVAvalue_float(){	
	var impuesto_venta_float = parseFloat(document.getElementById("iva_porcentaje").value);
	return impuesto_venta_float/100; 
}

function setMainValues(){
	porcentaje_iva = getIVAvalue_float();
	//setFacturaTemporal();
}

function makeProformaEditable(){  // FALTA VALIDAR seCambioFactura en dos metodos
	if(consecutivoActual===0){return false;} //Si no se ha ingresado una factura
	else{
		seCambioFactura = true;
		
		enableArticulosInputs();
		enableArticulosCantidades();
		enableArticulosArrows();
		$("#observaciones").attr("disabled", false);
				//Agregar boton de guardar
		$("#boton_guardar_editar").css('display','inline-block');
		
	}	
}

function enableArticulosInputs()
{	
	var inputsCodigo = document.getElementsByClassName('input_codigo_articulo');
	for (var i = 0; i < inputsCodigo.length; i++) 
	{
		inputsCodigo[i].disabled=false;
	}
}

function enableArticulosCantidades()
{
	inputsCodigo = document.getElementsByClassName('cantidad_articulo');
	for (var i = 0; i < inputsCodigo.length; i++) 
	{
		inputsCodigo[i].disabled=false;
	}
}

function enableArticulosArrows()
{
	inputsCodigo = document.getElementsByClassName('imagen_arrow');
	for (var i = 0; i < inputsCodigo.length; i++) 
	{
		inputsCodigo[i].style.display='block';
	}
}

function filtrarKeys(e, id){

}

var isFromAgregarCantidad = true;

function buscarArticulo(e, value, id){
	id_row = id.replace("codigo_articulo_","");
	descripcion = $("#descripcion_articulo_"+id_row).html();
	codigo = value.trim();
	
	// 1) Revisar eventos
	
	if(e!=null)
	{
		//Cuando sea Up down left right, no haga nada
		if(e.keyCode == 37||e.keyCode == 38||e.keyCode == 39||e.keyCode == 40||e.keyCode == 107){return false;}
		
		//Cuando presiona enter
		if (e.keyCode == 13) 
		{				
			//Si es generico			
			if(codigo==='00'&&descripcion==='')
			{
				openGenericProductDialog(id);
				return false;
			}	
			//Si ya cargo producto pasarse a cantidad
			if(descripcion!="")
			{
				tabRowORAdd(id, false);
				return false;
			}else{
				if (articuloYaIngresado(codigo, id)&&codigo!='00'&&isFromAgregarCantidad) { //Si viene por primera vez
					
					if(!puedeRepetirProducto){
						isFromAgregarCantidad=false;
						agregarCantidadArticulosPopUp(id.replace("codigo_articulo_",""));
					}
				}
				else if(isFromAgregarCantidad==false){
					isFromAgregarCantidad=true;
					return false;
				}	
			}
			
			
			
			
			//Esto es para que no cargue el producto si ya esta ingresado
			codigo_anterior = $("#codigo_articulo_anterior_"+id_row).val();	
			if(codigo_anterior===codigo){return false;}
			
			// 3 Verificamos si el articulo esta repetido, si no lo buscamos normal
			
			if(articuloYaIngresado(codigo, id)&&codigo!='00'&&!puedeRepetirProducto)
			{
				resetRowFields(id_row, false);
				//Esto para que nos permita realizar cambios de articulo a la primera
				$("#codigo_articulo_anterior_"+num_row).val('');
			}
			else
			{
				num_row = id.replace("codigo_articulo_","");
				
				
				getArticulo(codigo, id, num_row, cliente_cedula);		
			}						
		}
		
		// 2 Si codigo es vacio no hace nada
			
		if(codigo===''){
			resetRowFields(id_row, true);
			return false;
		}					
	}	
	
}

function resetRowFields(id_row, flag_cod){
	descripcion = document.getElementById("descripcion_articulo_"+id_row).innerHTML;
	

	if(flag_cod){
		document.getElementById("codigo_articulo_"+id_row).value="";
		$("#codigo_articulo_anterior_"+id_row).val('');
	}
	document.getElementById("descripcion_articulo_"+id_row).innerHTML="";
	document.getElementById("bodega_articulo_"+id_row).innerHTML="";
	document.getElementById("cantidad_articulo_"+id_row).value="";
	document.getElementById("cantidad_articulo_"+id_row).disabled=true;
	document.getElementById("cantidad_articulo_anterior_"+id_row).value=-1;
	document.getElementById("descuento_articulo_"+id_row).innerHTML="";
	document.getElementById("costo_unidad_articulo_"+id_row).innerHTML="";
	document.getElementById("costo_total_articulo_"+id_row).innerHTML="";
	
	updateProductsTotal();
	//Actualizamos costos totales
	decimales = document.getElementById("cantidad_decimales").value;
	decimales_int = parseInt(decimales);
	actualizaCostosTotales(decimales_int);
}


function tabRowORAdd(row_id, isCantidadField){
    //alert(row_id);
	var id = row_id.replace("codigo_articulo_","");
	var table = document.getElementById("tabla_productos");
	
	var cantidadIndices = array_pos_rows.length;  //Obtenemos ultimo indice
	//alert(cantidadIndices);
	var ultimoID = array_pos_rows[cantidadIndices-1]; //Obtenemos la fila que esta de ultimo lugar
	//alert(ultimoID);
	if(ultimoID==id){ //Si la fila que estamos evaluendo es la ultima tons agrega fila
		agregarFila(-1);
	}
	
	var id_int = parseInt(id);
	//id_int = id_int+1;
	var currentIndex = array_pos_rows.indexOf(id_int); //Obtenemos la posicion donde se origino el evento
	var nextId = array_pos_rows[currentIndex+1]; //Obtenemos la siguiente posicion donde se hara el blur
	//alert(nextId);
	if(!isCantidadField){
	    //alert("Pasa a cantidad");
		descripcion = document.getElementById("descripcion_articulo_"+id_int).innerHTML;
		//alert(descripcion);
		if(descripcion!=""){
			doTabCodigoArticulo("cantidad_articulo_"+id_int);
		}		
	}
	else{
		//alert("Pasa a codigo");
		doTabCodigoArticulo("codigo_articulo_"+nextId); //Nos vamos a esa fila
	}	
}

function doTabCodigoArticulo(id){
	//cantidad = document.getElementById(id).value;
	document.getElementById(id).select();
	//document.getElementById(id).value=cantidad;
}

function agregarFila(index){
	// Find a <table> element with id="myTable":
	var table = document.getElementById("tabla_productos");
	//alert(cantidadFilas(table));
	var siguienteFila = cantidadFilas(table)+1;
	var tabindex = siguienteFila+1;
	// Create an empty <tr> element and add it to the last position of the table:
	var row = table.insertRow(index);
	row.setAttribute("id","articulo_"+siguienteFila);

	// Insert new cells (<td> elements) at the 1st and 2nd position of the "new" <tr> element:
	var cell1 = row.insertCell(0);
	var cell2 = row.insertCell(1);
	var cell3 = row.insertCell(2);
	var cell4 = row.insertCell(3);
	var cell5 = row.insertCell(4);
	var cell6 = row.insertCell(5);
	var cell7 = row.insertCell(6);

	// Add some text to the new cells:
	var cedula = document.getElementById('cedula').value;
	if(!clienteCanBuy)
	{
		cell1.innerHTML = "<img class='imagen_arrow' title='Agregar Fila' src='/../application/scripts/Images/agregar_row.gif' width='14' height='7' onClick='agregarByCM("+siguienteFila+")'/>"
		                  +"<input tabindex='"+tabindex+"' id='codigo_articulo_"+siguienteFila+"' class='input_codigo_articulo' autocomplete='off' name='codigo_articulo' type='text' onkeyup='buscarArticulo(event, this.value, this.id);' onkeydown='filtrarKeys(event, this.id);' disabled>"
						  +"<input id='codigo_articulo_anterior_"+siguienteFila+"' type='hidden' >";
	}
	else
	{
		cell1.innerHTML = "<img class='imagen_arrow' title='Agregar Fila' src='/../application/scripts/Images/agregar_row.gif' width='14' height='7' onClick='agregarByCM("+siguienteFila+")'/>"
						+"<input tabindex='"+tabindex+"' id='codigo_articulo_"+siguienteFila+"' class='input_codigo_articulo' autocomplete='off' name='codigo_articulo' type='text' onkeyup='buscarArticulo(event, this.value, this.id);' onkeydown='filtrarKeys(event, this.id);' >"
						+" <input id='codigo_articulo_anterior_"+siguienteFila+"' type='hidden' >";
	}					
	
	cell3.innerHTML = "<input id='cantidad_articulo_"+siguienteFila+"' class='cantidad_articulo' autocomplete='off' name='cantidad_articulo' type='number' min='1' onchange='cambiarCantidad(this.id, event, this.value);' onkeyup='cambiarCantidad(this.id, event, this.value);' disabled>"
					 +"<input id='cantidad_articulo_anterior_"+siguienteFila+"' type='hidden' value='-1'>";
	cell2.innerHTML = "<div class='articulo_specs' id='descripcion_articulo_"+siguienteFila+"'></div>"
					 +"<div class='tooltip_imagen_articulo' id='tooltip_imagen_articulo_"+siguienteFila+"'></div>";
	cell4.innerHTML = "<div class='articulo_specs' id='bodega_articulo_"+siguienteFila+"'></div>";
	cell5.innerHTML = "<div class='articulo_specs' id='descuento_articulo_"+siguienteFila+"' ondblclick='changeDiscount("+siguienteFila+")'></div>";
	cell6.innerHTML = "<div class='articulo_specs' id='costo_unidad_articulo_"+siguienteFila+"'>"
					 +"</div><input id='costo_unidad_articulo_ORIGINAL_"+siguienteFila+"' type='hidden' >"
					 +"<input id='costo_unidad_articulo_FINAL_"+siguienteFila+"' type='hidden' >"
					 +"<input id='producto_exento_"+siguienteFila+"' type='hidden' >"
					 +"<input id='producto_retencion_"+siguienteFila+"' type='hidden' >";
	cell7.innerHTML = "<div class='articulo_specs' id='costo_total_articulo_"+siguienteFila+"'></div>"
					 +"<input type='hidden' id='costo_total_articulo_sin_descuento_"+siguienteFila+"'/>";

	//Agrega la nueva fila al array de indices
	if(index==-1){array_pos_rows.push(siguienteFila);}
	else {array_pos_rows.splice(index-1, 0, siguienteFila);}
	
}

function cambiarCantidad(id, e, value){
	if(isCajaLoad){
		//No permitir valores en cero, si es asi ponerlos en uno
		if(value == 0){
			console.log(value+" "+id);
			value = 1;
			$("#"+id).val(value);
			
		}
		manejarEventoCantidadArticulo(e, value, id);
		//alert(id);
		actualizaCostoTotalArticulo(id);
		row = id.replace('cantidad_articulo_','');
		codigo_id = 'codigo_articulo_'+row;
		//alert(codigo);
		codigo = document.getElementById(codigo_id).value;
		cantidad_actual = document.getElementById("cantidad_articulo_"+row).value;
		document.getElementById("cantidad_articulo_anterior_"+row).value=cantidad_actual;
	}
}

function manejarEventoCantidadArticulo(e, value, row_id){
	if(e!=null){ 
		if (e.keyCode == 13) { //Si es enter
			//tabRowORAdd(id);
			id = row_id.replace("cantidad_articulo_","codigo_articulo_");
			//validarMaxMinCantidad(id);
			tabRowORAdd(id, true);			
		}
	}
}

function validarMaxMinCantidad(id){
	id = id.replace("codigo_articulo_","");
	id = id.replace("cantidad_articulo_","");
	//alert("bodega_articulo_"+id);
	cantidad_validar = document.getElementById("cantidad_articulo_"+id).value;
	cantidad_validar = parseInt(cantidad_validar);
	
	//alert(cantidad_validar);
	cantidad_bodega = document.getElementById("bodega_articulo_"+id).innerHTML;
	cantidad_bodega = parseInt(cantidad_bodega);
	if(cantidad_validar<0){
		document.getElementById("cantidad_articulo_"+id).value=0;
	}
	else if(cantidad_validar>cantidad_bodega){
		document.getElementById("cantidad_articulo_"+id).value=cantidad_bodega;
	}
	else if(!isNumber(cantidad_validar)){
		document.getElementById("cantidad_articulo_"+id).value=0;
	}
	else{
		document.getElementById("cantidad_articulo_"+id).value=cantidad_validar;
	}
}

function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

function articuloYaIngresado(value, id){
	if(value.trim()==''){return false;}
	var inputsCodigo = document.getElementsByClassName('input_codigo_articulo');
	for (var i = 0; i < inputsCodigo.length; i++) 
	{
		if(inputsCodigo[i].id==id){} //Si es el mismo input descartarlo
		else if(inputsCodigo[i].value.trim()==value) //Si ya esta en otro
		{
			numRowArticuloRepetido=i+1;
			return true;
		}
	}
	return false;
}

function getArticulo(codigo, id_fila, num_fila, cedula) {
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/facturas/nueva/getArticuloJSON',
		type: "POST",
		async: true,
		data: {'cedula':cedula, 'codigo':codigo},		
		success: function(data, textStatus, jqXHR)
		{
			//try{
				result = $.parseJSON('[' + data.trim() + ']');
				if(result[0].status==="error"){
					mostrarErroresCargarArticulo(result[0].error, num_fila);
				}else if(result[0].status==="success"){	
					resetRowFields(num_fila, false);
					setArticulo(result[0].articulo, num_fila);									
				}
/*
			}catch(e){
				notyConTipo('¡La respuesta tiene un formato indebido, contacte al administrador!','error');
			}	
*/	
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
	 
		}
	});
}

function notyConTipo(Mensaje, tipo){
	n = noty({
					   layout: 'topRight',
					   text: Mensaje,
					   type: tipo,
					   timeout: 4000
					});
}


function setArticulo(articulo, num_fila){
	//Seteamos la parte del codigo
	$("#codigo_articulo_anterior_"+num_fila).val(articulo.codigo);
	//Seteamos la descripcion
	$("#descripcion_articulo_"+num_fila).html(articulo.descripcion);
	//Seteamos el tootltip de la imagen del articulo
	$("#tooltip_imagen_articulo_"+num_fila).html("<img src='"+location.protocol+"//"+document.domain+(location.port ? ':'+location.port: '')+"/application/images/articulos/"+articulo.imagen+"' height='200' width='200'>");
	agregarTooltip("#descripcion_articulo_"+num_fila);
	//Seteamos la bodega
	$("#bodega_articulo_"+num_fila).html(articulo.inventario);
	//Seteamos la cantidad inicial
	$("#cantidad_articulo_"+num_fila).val(1);
	$("#cantidad_articulo_"+num_fila).prop( "disabled", false );
	$("#cantidad_articulo_"+num_fila).attr( "max", articulo.inventario );
	//Seteamos el descuento
	$("#descuento_articulo_"+num_fila).html(articulo.descuento);
		
	//Tipo de moneda y factor
	tipo_moneda = $("#tipo_moneda").val();
	factor_tipo_moneda = 1.00; //Cualquier cosa entre 1 es igual
	if(tipo_moneda.indexOf('dolare') != -1)
	{
		tipo_cambio_venta = $("#tipo_cambio_venta").val();
		factor_tipo_moneda = parseFloat(tipo_cambio_venta);
	}
	
	//Capturar decimales
	decimales = $("#cantidad_decimales").val();
	decimales = parseInt(decimales);
	
	//Precio del articulo	
	precio_articulo_unitario = parseFloat(articulo.precio_cliente);
	precio_articulo_unitario = precio_articulo_unitario/factor_tipo_moneda;
	
	//Seteamos precio sin formato en input oculto	
	$("#costo_unidad_articulo_ORIGINAL_"+num_fila).val(precio_articulo_unitario);
	
	//Seteamos precio con formato en UI
	precio_articulo_unitario = precio_articulo_unitario.toFixed(decimales);
	precio_articulo_unitario = parseFloat(precio_articulo_unitario);
	precio_articulo_unitario = precio_articulo_unitario.format(decimales, 3, '.', ',');
	$("#costo_unidad_articulo_"+num_fila).html(precio_articulo_unitario);
		
	//Seteamos el precio de cliente final para calcular ganancia
	$("#costo_unidad_articulo_FINAL_"+num_fila).val(parseFloat(articulo.precio_no_afiliado).toFixed(decimales));
	
	//Seteamos si es exento
	$("#producto_exento_"+num_fila).val(articulo.exento);
	
	//Seteamos si no se le aplica retencion
	$("#producto_retencion_"+num_fila).val(articulo.retencion);
	
	//Funciones Finales
	actualizaCostoTotalArticulo("cantidad_articulo_"+num_fila);
	updateProductsTotal();
	$("#cantidad_articulo_anterior_"+num_fila).val(1);
}




