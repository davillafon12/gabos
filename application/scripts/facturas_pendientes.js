function getFacturas(){
	$.ajax({
	  url: location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/facturas/caja/getFacturasPendientes'	  
	})
	.done(function( data ) {
		document.getElementById("cuerpoTablaPendientes").innerHTML = data;
	});
}

function cargaFactura(consecutivo){
	if(seCambioFactura){ //Deshacer factura si se edito
		deshacerFacturaCaja();
	}
	cargarEncabezado(consecutivo);
	cargarProductos(consecutivo);
	consecutivoActual = consecutivo; //Asignamos el consecutivo actual para realizar operaciones
	cerrarSelector(); //Cerramos el selector de facturas
	isProforma = false; //Decimos al flag de proforma que no es proforma
	seCambioFactura = false; //Decimos que no se cambio fatcura
	window.onbeforeunload=null; //Eliminamos los eventos de salida
	window.onunload=null; //Eliminamos los eventos de salida	 
	disableInputs(); //Deshabilitamos las entradas
	$("#boton_guardar_editar").css('display','none'); // Qutar el boton de guardar factura editada
}

function cargarEncabezado(consecutivo){
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+"/facturas/caja/getFacturaHeaders",
		type: "POST",
		async: false,
		data: {'consecutivo':consecutivo},		
		success: function(data, textStatus, jqXHR)
		{			
			try{
				facturaHEAD = $.parseJSON('[' + data.trim() + ']');
				if(facturaHEAD[0].status==="error"){
					displayErrors(facturaHEAD[0].error);
					cleanTable();
				}else if(facturaHEAD[0].status==="success"){
					setEncabezadoFactura(facturaHEAD);
				}
			}
			catch(e){
				if(seCambioFactura){deshacerFacturaCaja();}
				location.reload();
			}			
		},
		error: function (jqXHR, textStatus, errorThrown)
		{notyError('¡Hubo un error al cargar la factura!');}
	});
}

function displayErrors(NumError){
	switch(NumError) {
		case "10":
			notyError('Error al cargar el encabezado, por favor contacte al administrador \nERROR: '+NumError);
			break;
		case "11":
			notyError('Esta factura ya fue cobrada. \nERROR: '+NumError);
			getFacturas(); //Actualizamos el contenedor de facturas
			break;
		case "12":
			notyError('Esta factura fue anulada. \nERROR: '+NumError);
			getFacturas(); //Actualizamos el contenedor de facturas
			break;
		case "13":
			notyError('Error al leer el URL, por favor contacte al administrador \nERROR: '+NumError);
			break;
		case "14":
			notyError('Error al procesar factura en servidor, por favor contacte al administrador \nERROR: '+NumError);
			break;
		case "15":
			notyError('Error al cargar los productos, por favor contacte al administrador \nERROR: '+NumError);
			break;
		case "16":
			notyError('Error al leer el URL, por favor contacte al administrador \nERROR: '+NumError);
			break;
		case "17":
			notyError('Error al procesar factura en servidor, por favor contacte al administrador \nERROR: '+NumError);
			break;
		case "18":
			notyError('Error al leer el URL, por favor contacte al administrador \nERROR: '+NumError);
			break;
		case "19":
			notyError('¡No existe la factura!, por favor contacte al administrador \nERROR: '+NumError);
			break;
		case "20":
			notyError('¡No existe la proforma!, por favor contacte al administrador \nERROR: '+NumError);
			break;
		case "21":
			notyError('Esta proforma ya fue cobrada. \nERROR: '+NumError);
			break;
		case "22":
			notyError('El tiempo de esta proforma ya caducó. \nERROR: '+NumError);
			break;
		case "23":
			notyError('Algun producto no existe o no hay en inventario. \nERROR: '+NumError);
			break;
		case "24":
			notyError('El cliente ya no tiene credito disponible para esta factura. \nERROR: '+NumError);
			break;
                case "25":
			notyError('El cliente no existe. \nERROR: '+NumError);
			break;
                case "26":
			notyError('La sucursal no existe. \nERROR: '+NumError);
			break;
                case "27":
			notyError('La sucursal esta deshabilitada. \nERROR: '+NumError);
			break;
                case "28":
			notyError('Debe actualizar el domicilio de la sucursal para poder cobrar. \nERROR: '+NumError);
			break;
                case "29":
			notyError('Debe actualizar el domicilio de la sucursal para poder cobrar. \nERROR: '+NumError);
			break;
                case "30":
			notyError('La sucursal no cuenta con los datos necesarios para realizar Factura Electrónica. \nERROR: '+NumError);
			break;
                case "50":
			notyError('Error al generar la factura electrónica. \nERROR: '+NumError);
			break;
                case "51":
			notyError('Error al generar la factura electrónica. \nERROR: '+NumError);
			break;
                case "52":
			notyError('Error al generar la factura electrónica. \nERROR: '+NumError);
			break;
                case "53":
			notyError('Error al generar la factura electrónica. \nERROR: '+NumError);
			break;
                case "54":
			notyError('Error al generar la factura electrónica. \nERROR: '+NumError);
			break;
                case "55":
			notyError('La factura electrónica fue RECHAZADA por Hacienda, deberá generarla de nuevo. La factura ha sido ANULADA. \nERROR: '+NumError);
			break;
		case "cf_1":
			notyError('No se pudo actualizar la factura editada. \nERROR: '+NumError);
			break;
		case "cf_2":
			notyError('El formato de la URL es inebido para actualizar factura. \nERROR: '+NumError);
			break;
		case "cf_3":
			notyError('No se puede actualizar una factura sin productos. \nERROR: '+NumError);
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

function setEncabezadoFactura(facturaHEAD){
	$("#cedula").val(facturaHEAD[0].cedula);
	$("#tipo_moneda").val(facturaHEAD[0].moneda);	
	$("#nombre").val(facturaHEAD[0].nombre);
	$("#observaciones").val(facturaHEAD[0].observaciones);
	
	//Valores ocultos
	$("#iva_porcentaje").val(facturaHEAD[0].ivapor);
	$("#tipo_cambio_venta").val(facturaHEAD[0].cambio);
	
	//Cargamos info del cliente
	clienteEsDeTipoSucursal = facturaHEAD[0].cliente_sucursal;
	clienteEsDeTipoExento = facturaHEAD[0].cliente_exento;
	clienteNoAplicaRetencion = facturaHEAD[0].cliente_retencion;
}

function cargarProductos(consecutivo){
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+"/facturas/caja/getArticulosFactura",
		type: "POST",
		data: {'consecutivo':consecutivo},		
		success: function(data, textStatus, jqXHR)
		{			
			try{
				facturaBODY = $.parseJSON('[' + data.trim() + ']');
				if(facturaBODY[0].status==="error"){
					displayErrors(facturaBODY[0].error);
				}else if(facturaBODY[0].status==="success"){
					setProductosFactura(facturaBODY[0].productos);
				}
			}
			catch(e){
				if(seCambioFactura){deshacerFacturaCaja();}
				//alert(e);
				location.reload();
			}			
		},
		error: function (jqXHR, textStatus, errorThrown)
		{notyError('¡Hubo un error al cargar la factura!');}
	});
}

function setProductosFactura(productos){
	$("#contenidoArticulos").html('');
	table = document.getElementById("tabla_productos").getElementsByTagName('tbody')[0];
	isCajaLoad=false;
	cantidad = productos.length;
	//artHTML = '';
	//Capturar decimales
	decimales = $("#cantidad_decimales").val();
	decimales = parseInt(decimales);
	array_pos_rows = []; //Reiniciamos el index, para utilizar el metodo de crear JSON de factura de los articulos
	for (var i = 0; i < cantidad; i++) 
	{
		array_pos_rows[i] = i+1;
		row = table.insertRow(table.rows.length);	
		row.setAttribute("id","articulo_"+(i+1));
		precio = parseFloat(productos[i].precio);
		precio = precio.toFixed(decimales);
		cell1 = row.insertCell(0);
		cell2 = row.insertCell(1);
		cell2.setAttribute("class","imagen-margen-container");
		cell3 = row.insertCell(2);
		cell4 = row.insertCell(3);
		cell5 = row.insertCell(4);
		cell6 = row.insertCell(5);
		cell7 = row.insertCell(6);
		
		bodegaINT = productos[i].bodega;

		if(!isProforma){
			bodegaINT = parseInt(productos[i].cantidad)+parseInt(productos[i].bodega);
		}else{
			bodegaINT = productos[i].bodega;
		}

		
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
		precioUI = precioUI.format(decimales, 3, '.', ',');
		
		cell6.innerHTML = "<div class='articulo_specs unitario' id='costo_unidad_articulo_"+(i+1)+"'>"+precioUI+"</div>"
				+"<input id='costo_unidad_articulo_ORIGINAL_"+(i+1)+"' type='hidden' value='"+productos[i].precio+"'>"
				+"<input id='costo_unidad_articulo_FINAL_"+(i+1)+"' type='hidden' value='"+productos[i].precioFinal+"'>"
				+"<input id='producto_exento_"+(i+1)+"' type='hidden' value='"+productos[i].exento+"'>"
				+"<input id='producto_retencion_"+(i+1)+"' type='hidden' value='"+productos[i].retencion+"'>";
		cell7.innerHTML = "<div class='articulo_specs final' id='costo_total_articulo_"+(i+1)+"'></div>"
						  +"<input type='hidden' id='costo_total_articulo_sin_descuento_"+(i+1)+"'/>";
		
		
		//Agregamos las demas funciones de cada row
		//agregarTooltip("#descripcion_articulo_"+(i+1));
		updateProductsTotal();
	}
	setCostos(cantidad);
	isCajaLoad=true;
}

function setCostos(cantidad){
	//cantidad = productos.length;
	for (var i = 0; i < cantidad; i++) 
	{	actualizaCostoTotalArticulo("cantidad_articulo_"+(i+1));
	}
}

function cleanTable(){
	elmtTable = document.getElementById('tabla_productos');
	tableRows = elmtTable.getElementsByTagName('tr');
	rowCount = tableRows.length;

	for (var x=rowCount-1; x>0; x--) {
	   elmtTable.removeChild(tableRows[x]);
	}
	
	$("#costo").val(0);
	$("#iva").val(0);
	$("#retencion").val(0);
	$("#costo_total").val(0);
	var clienteEsDeTipoSucursal = "0";
	var clienteEsDeTipoExento = "0";
	var clienteNoAplicaRetencion = "0";
}

function mostrarFacturas(){
	html = $("#boton_mostrarFactura").html();
	if(html==='Mostrar Facturas'){
		$('#selector_facturas').delay(50).animate({
			'opacity': 1,
			'left': '0%'
		});
		$("#boton_mostrarFactura").html('Ocultar Facturas');
	}else{
		$('#selector_facturas').delay(200).animate({
			'opacity': 1,
			'left': '-50%'
		});
		$("#boton_mostrarFactura").html('Mostrar Facturas');	
	}
	
}

function cerrarSelector(){
	$('#selector_facturas').delay(200).animate({
		'opacity': 1,
		'left': '-50%'
	});
	$("#boton_mostrarFactura").html('Mostrar Facturas');	
}

//******************************************************************************************************
//*********************************TIPO DE PAGOS********************************************************
//******************************************************************************************************

function numTransaccion(tipo)
{
	inyectado = $("#numero_transaccion_container");
	vend = $("#vendedor");
	mxt = $("#cantidad_mixto_tarjeta");
	mxtLabel = $("#cantidad_mixto_label");
	banco_title = $("#banco");
	banco_sel = $("#banco_sel");
	mxtLabelContado = $("#monto_efectivo_mixto_label");
	mxtContado = $("#monto_efectivo_mixto");
	
	
	if(tipo.indexOf('contad') != -1)
	{
		vend.html('');
		inyectado.html('');
		mxt.html('');
		mxtLabel.html('');
		banco_title.html('');
		banco_sel.css('display', 'none');
		mxtLabelContado.html('');
		mxtContado.html('');
	}
	else if (tipo.indexOf('tarjet') != -1)
	{
		vend.html("Número de Autorización:");
		inyectado.html("<input id='numero_transaccion' class='input_uno' style='width: 100px; margin-left: 5px;' autocomplete='off' name='numero_transaccion' required='' type='text'>");		
		mxt.html('');
		mxtLabel.html('');
		banco_title.html('Datáfono:');
		banco_sel.css('display', 'block');
		mxtLabelContado.html('');
		mxtContado.html('');
	}
	else if (tipo.indexOf('cheq') != -1)
	{
		vend.html("Número de cheque:");
		inyectado.html("<input id='numero_cheque' class='input_uno' style='width: 100px; margin-left: 5px;' autocomplete='off' name='numero_cheque' required='' type='text'>");
		mxt.html('');
		mxtLabel.html('');
		banco_title.html('Datáfono:');
		banco_sel.css('display', 'block');
		mxtLabelContado.html('');
		mxtContado.html('');
	}
	else if (tipo.indexOf('deposi') != -1)
	{
		vend.html("Número de documento:");
		inyectado.html("<input id='numero_deposito' style='width: 100px; margin-left: 5px;' class='input_uno' autocomplete='off' name='numero_deposito' required='' type='text'>");
		mxt.html('');
		mxtLabel.html('');
		banco_title.html('Banco:');
		banco_sel.css('display', 'block');
		mxtLabelContado.html('');
		mxtContado.html('');
	}
	else if (tipo.indexOf('mixt') != -1)
	{
		vend.html("Número de Autorización:");
		inyectado.html("<input id='numero_transaccion' class='input_uno' style='width: 100px; margin-left: 5px;' autocomplete='off' name='numero_transaccion' required='' type='text'>");
		mxt.html("<input id='cantidad_mixto' class='input_uno' style='width: 100px; margin-left: 5px;' autocomplete='off' name='cantidad_mixto' required='' type='number' min='0' step='any'>");
		mxtLabel.html('Monto en tarjeta:');
		banco_title.html('Datáfono:');
		banco_sel.css('display', 'block');
		mxtLabelContado.html('Monto en efectivo:');
		mxtContado.html("<input id='monto_efectivo_mixto_input' class='input_uno' style='width: 100px; margin-left: 5px;' autocomplete='off' name='monto_efectivo_mixto' required='' type='number' min='0' step='any'>");
	}
	else if (tipo.indexOf('credit') != -1)
	{
		vend.html("Cantidad de días:");
		inyectado.html("<select id='cant_dias_credito' style='margin-left: 5px;'><option value='8' selected>8</option><option value='15'>15</option><option value='30'>30</option><option value='45'>45</option><option value='60'>60</option></select>");		
		mxt.html('');
		mxtLabel.html('');
		banco_title.html('');
		banco_sel.css('display', 'none');
		mxtLabelContado.html('');
		mxtContado.html('');
	}
	else if (tipo.indexOf('apartad') != -1)
	{
		vend.html("Abono:");
		inyectado.html("<input id='cantidad_abono' class='input_uno' style='width: 100px; margin-left: 5px;' autocomplete='off' required='' type='text'>");
		mxt.html('');
		mxtLabel.html('');
		banco_title.html('');
		banco_sel.css('display', 'none');
		mxtLabelContado.html('');
		mxtContado.html('');
	}
}

function disableInputs(){
	$("#nombre").prop( "disabled", true );
	$("#cedula").prop( "disabled", true );
	$("#observaciones").prop( "disabled", true );
}
