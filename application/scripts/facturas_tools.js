/////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////// EVENTO DE ALT+A AGREGAR FILA ////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////

window.onkeydown = keydown;




function keydown(e) {
	var evtobj = window.event? event : e
    if (evtobj.keyCode == 65 && evtobj.altKey){ agregarFila(-1);}
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
	cell2.setAttribute("class","imagen-margen-container");
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
	cell6.innerHTML = "<div class='articulo_specs unitario' id='costo_unidad_articulo_"+siguienteFila+"'>"
					 +"</div><input id='costo_unidad_articulo_ORIGINAL_"+siguienteFila+"' type='hidden' >"
					 +"<input id='costo_unidad_articulo_FINAL_"+siguienteFila+"' type='hidden' >"
					 +"<input id='producto_exento_"+siguienteFila+"' type='hidden' >"
					 +"<input id='producto_retencion_"+siguienteFila+"' type='hidden' >";
	cell7.innerHTML = "<div class='articulo_specs final' id='costo_total_articulo_"+siguienteFila+"'></div>"
					 +"<input type='hidden' id='costo_total_articulo_sin_descuento_"+siguienteFila+"'/>";

	//Agrega la nueva fila al array de indices
	if(index==-1){array_pos_rows.push(siguienteFila);}
	else {array_pos_rows.splice(index-1, 0, siguienteFila);}
	
}

function cantidadFilas(tabla)
{
	return tabla.getElementsByTagName("tr").length-1;
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

/////////////////////////// VARIABLE GLOBAL
var array_pos_rows = [];
array_pos_rows[0] = 1;
array_pos_rows[1] = 2;
array_pos_rows[2] = 3;
array_pos_rows[3] = 4;
array_pos_rows[4] = 5;
array_pos_rows[5] = 6;
array_pos_rows[6] = 7;
array_pos_rows[7] = 8;
array_pos_rows[8] = 9;
array_pos_rows[9] = 10;
///////////////////////////////////////////

function agregarByCM(id){
	var id_int = parseInt(id);
	var indice_actual = array_pos_rows.indexOf(id_int);
	
	var index = obtenerIndex(1, indice_actual);
	agregarFila(index);
}

function obtenerIndex(where, row_num){
	
	var table = document.getElementById("tabla_productos");
	var cantidad_rows = cantidadFilas(table);
	
	//Valoramos si se agrega arriba o abajo
	if(where==0){ //Es arriba
		if(row_num==0){return 1;} //Si es arriba de la posicion inicial devuelve 0
		else{return row_num}; //Devuelve el index de arriba
	}else if(where==1){
		if(row_num==cantidad_rows){agregarFila(-1);} //Si esta en la ultima posicion que agregue una fila para despues devolver index
		return row_num+1;
	}
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////// BUSQUEDA DE CLIENTE /////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////

function buscarCedula (e) 
{
	var cedula = document.getElementById('cedula').value;
	if(cedula.trim() === "")
	{ 
		document.getElementById('nombre').value="";
		//document.getElementById('nombre').disabled=true;
		disableArticulosInputs();
	}
	else
	{getNombreCliente(cedula);}
	
	var nombre = document.getElementById('nombre').value;
	
	if (e.keyCode == 13 && nombre.indexOf('No existe client')== -1 && nombre!='') {
		doTabCodigoArticulo("codigo_articulo_1");
	}
    
}

function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

function enableArticulosInputs()
{	
	var inputsCodigo = document.getElementsByClassName('input_codigo_articulo');
	for (var i = 0; i < inputsCodigo.length; i++) 
	{
		inputsCodigo[i].disabled=false;
	}

	// Fix para cuando sea factura sin productos
	if(inputsCodigo.length == 0){
		agregarFila(1);
	}
}

function disableArticulosInputs()
{
	var inputsCodigo = document.getElementsByClassName('input_codigo_articulo');
	for (var i = 0; i < inputsCodigo.length; i++) 
	{
		inputsCodigo[i].disabled=true;
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
				cedula = $("#cedula").val();
				
				getArticulo(codigo, id, num_row, cedula);		
			}						
		}
		
		// 2 Si codigo es vacio no hace nada
			
		if(codigo===''){
			resetRowFields(id_row, true);
			return false;
		}					
	}	
	
}

var numRowArticuloRepetido = '';

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







var restringe = 0;
var valor_real_inventario = '';




var isActualizarCliente = false;

function setArticulo(articulo, num_fila){
	//Seteamos la parte del codigo
	$("#codigo_articulo_anterior_"+num_fila).val(articulo.codigo);
	//Seteamos la descripcion
	$("#descripcion_articulo_"+num_fila).html(articulo.descripcion);
	//Seteamos el tootltip de la imagen del articulo
	$("#tooltip_imagen_articulo_"+num_fila).html("<img src='"+location.protocol+"//"+document.domain+(location.port ? ':'+location.port: '')+"/application/images/articulos/"+articulo.imagen+"' height='200' width='200'>");
	
	//agregarTooltip("#descripcion_articulo_"+num_fila);
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
	//actualizarCantidadProductoInventario('', articulo.codigo, num_fila);
	$("#cantidad_articulo_anterior_"+num_fila).val(1);
}

function setDatosArticulo(articuloARRAY, id_input, num_row, cantidadArticulos){
	descripcion = document.getElementById('descripcion_articulo_'+num_row).innerHTML;
	if(articuloARRAY[0]==1&&descripcion.trim()!==''){
		//alert("Reiteratio");
		if(isActualizarCliente){ //Si el llamado es por cambio de cliente sigue
			//alert("Es cambio de cliente");
		}
		else{ //Si el llamado es por redundancia lo cancela
			return false;
		}		
	}
	//alert(isActualizarCliente);
	isActualizarCliente=false;
	/*
	ESTRUCTURA DEL ARRAY
	0 => flag de existencia
	1 => codigo
	2 => descripcion
	3 => inventario/bodega
	4 => descuento
	5 => Familia donde esta contenida el articulo
	6 => costo del producto para este cliente
	7 => costo del producto para cliente final
	8 => nombre de la imagen del producto
	9 => si esta o no exento
	*/
	if(articuloARRAY[0]==0)
	{		
		if(descripcion.trim()!==''){
			//alert(descripcion);
			//codigo = document.getElementById('codigo_articulo_'+num_row).value;
			//cantidad = document.getElementById('cantidad_articulo_'+num_row).value;
			//actualizarCantidadProductoInventario('0', codigo, num_row);
		}
			
			//actualizarCantidadProductoInventario(parseInt(cantidad), codigo);
			//alert("¡No hay articulo con ese codigo!");
			resetRowFields(num_row, false);	
			/*updateProductsTotal();
			//Actualizamos costos totales
			var decimales = document.getElementById("cantidad_decimales").value;
			var decimales_int = parseInt(decimales);
			actualizaCostosTotales(decimales_int);	*/	
	}
	else
	{
		if(articuloARRAY[3]==='0'){
			n = noty({
						   layout: 'topRight',
						   text: 'No hay mas unidades existentes en inventario',
						   type: 'warning',
						   timeout: 4000
						});
			return false;
		}
			
		//restringe++;
		tipo_moneda = document.getElementById("tipo_moneda").value;
		factor_tipo_moneda_float = 1.00; //Cualquier cosa entre 1 es igual
		if(tipo_moneda.indexOf('colone') != -1)
		{//No pasa nada, el factor de tipo de moneda sigue igual
		}
		else if(tipo_moneda.indexOf('dolare') != -1)
		{
			tipo_cambio_venta = document.getElementById("tipo_cambio_venta").value;
			factor_tipo_moneda_float = parseFloat(tipo_cambio_venta);
			//alert(tipo_cambio_venta);
		}
		//alert(tipo_moneda);
		
		//Seteamos el codigo para valoracion en resetRowFields()
		document.getElementById("codigo_articulo_anterior_"+num_row).value=articuloARRAY[1];
		
		
		document.getElementById("descripcion_articulo_"+num_row).innerHTML=articuloARRAY[2];
		
		document.getElementById("tooltip_imagen_articulo_"+num_row).innerHTML = "<img src='"+location.protocol+"//"+document.domain+(location.port ? ':'+location.port: '')+"/application/images/articulos/"+articuloARRAY[8]+".jpg' height='200' width='200'>";
		agregarTooltip("#descripcion_articulo_"+num_row);
		
		document.getElementById("bodega_articulo_"+num_row).innerHTML=articuloARRAY[3];
		
		/*restringe++;
		if(restringe<2){
			document.getElementById("bodega_articulo_"+num_row).innerHTML=articuloARRAY[3];
			//restringe++;
			//valor_real_inventario = articuloARRAY[3];
		}
		else{restringe=0;}
		
		if(document.getElementById("bodega_articulo_"+num_row).innerHTML==''){
			document.getElementById("bodega_articulo_"+num_row).innerHTML=articuloARRAY[3];
		}*/
		//Cargamos la cantidad de decimales permitidos
		decimales = document.getElementById("cantidad_decimales").value;
		decimales_int = parseInt(decimales);
		//Input number de cantidad
		document.getElementById("cantidad_articulo_"+num_row).value=cantidadArticulos;
		//document.getElementById("cantidad_articulo_anterior_"+num_row).value=cantidadArticulos;
		document.getElementById("cantidad_articulo_"+num_row).disabled=false;
		document.getElementById("cantidad_articulo_"+num_row).setAttribute("max",articuloARRAY[3]);
		
		
		//Calculamos el descuento para rebajarlo al costo de unidad
		descuento = parseInt(articuloARRAY[4]);
		document.getElementById("descuento_articulo_"+num_row).innerHTML=descuento;
		//Calculamos el precio con descuento
		precio_unidad_float = parseFloat(articuloARRAY[6]);		
		
		//precio_unidad_float -= precio_unidad_float*(descuento/100);
		precio_unidad_FACTOR_float = precio_unidad_float/factor_tipo_moneda_float;
		document.getElementById("costo_unidad_articulo_ORIGINAL_"+num_row).value=precio_unidad_float;
		
		
		//PRECIO QUE SE VE REFLEJADO EN UI
		precio_unidad_FACTOR_float = precio_unidad_FACTOR_float.toFixed(decimales_int);
		//alert(precio_unidad_FACTOR_float);
		precio_unidad_FACTOR_float = parseFloat(precio_unidad_FACTOR_float);
		//alert(typeof precio_unidad_FACTOR_float);
		precio_unidad_FACTOR_float = precio_unidad_FACTOR_float.format(2, 3, '.', ',');
		
		document.getElementById("costo_unidad_articulo_"+num_row).innerHTML=precio_unidad_FACTOR_float;
		
		
		
		//Precio para cliente final para calcular ganancia
		document.getElementById("costo_unidad_articulo_FINAL_"+num_row).value=parseFloat(articuloARRAY[7]).toFixed(decimales_int);
		
		//alert(articuloARRAY[9]);
		document.getElementById("producto_exento_"+num_row).value=articuloARRAY[9];
		
		//actualizamos info
		//alert("1. => "+cantidadArticulos);
		actualizaCostoTotalArticulo("cantidad_articulo_"+num_row);
		updateProductsTotal();
		codigo = document.getElementById('codigo_articulo_'+num_row).value;
		//actualizarCantidadProductoInventario('', codigo, num_row);
		cantidadNuevaHide = document.getElementById("cantidad_articulo_"+num_row).value;
		//alert(cantidadNuevaHide);
		document.getElementById("cantidad_articulo_anterior_"+num_row).value=cantidadNuevaHide;
		//agregarArticuloFactura(articuloARRAY);
		return;
	}
	
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////// FUNCIONES DE RESET //////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////

function resetRowFields(id_row, flag_cod){
	descripcion = document.getElementById("descripcion_articulo_"+id_row).innerHTML;
	if(descripcion.trim()!=''){ //Si hubo un producto, devuelva la cantidad al inventario
		//alert(descripcion);
		codigo = document.getElementById("codigo_articulo_anterior_"+id_row).value;
		//alert(codigo);
		//alert('resetRowFields');
		if(codigo==='00'){}
		else{
			//actualizarCantidadProductoInventario('0', codigo, id_row);
		}
	}

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

function resetAll(){
	resetAllRowsFields();
	resetCostosTotales();
}

function resetAllRowsFields(){
	var table = document.getElementById("tabla_productos");
	var rows = cantidadFilas(table);
	for (var i = 0; i < rows; i++) 
	{
		resetRowFields(i+1, true);
	}
}

function advertenciaSalida(){
	return "Está saliendo de esta página. Los productos seran devueltos a inventario!!! "
}

function deshacerFactura(){
	//resetAll();
	devolverProductos();
}

function salidaSesion(){
	resetAll();
	window.onbeforeunload=null;
	window.location = location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/home/logout';
	
}

window.onbeforeunload=advertenciaSalida;
window.onunload=deshacerFactura;

function resetCostosTotales(){
	document.getElementById("ganancia").value = "";
	document.getElementById("costo").value = "";
	document.getElementById("iva").value = "";
	document.getElementById("retencion").value = "";
	document.getElementById("costo_total").value = "";
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////// ACTUALIZAR COSTOS ///////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////

//Variables de costos

var costo_total_factura = 0.0;
var costo_sin_IVA_factura = 0.0;
var IVA_Factura = 0.0;
var ganancia_afiliado = 0.0;

var porcentaje_iva = 0.0;

function getIVAvalue_float(){	
	var impuesto_venta_float = parseFloat(document.getElementById("iva_porcentaje").value);
	return impuesto_venta_float/100; 
}

function setMainValues(){
	porcentaje_iva = getIVAvalue_float();
	//setFacturaTemporal();
}

function actualizaCostoUnidad(id){
	
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
	precio_total = precio_total
	precio_total = parseFloat(precio_total);
	costo_total.innerHTML = precio_total.format(decimales_int, 3, '.', ',');

	//Actualizamos el costo total
	actualizaCostosTotales(decimales_int);
	codigo = document.getElementById("codigo_articulo_"+num_row).value;

	inventario = document.getElementById("cantidad_articulo_"+num_row).value;
	
	
}

function actualizaCostosTotales(decimales_int){

	costo_sin_IVA_factura = 0.0;
	costo_total_factura = 0.0;
	IVA_Factura = 0.0;
	costo_cliente_final = 0.0;
	costo_retencion = 0.0;
	
	
	table = document.getElementById("tabla_productos");
	rows = cantidadFilas(table);
	
	//Recorremos la tabla
	for (var i = 0; i < rows; i++){	
            if($("#descripcion_articulo_"+(i+1)).html().trim()!==''){
                var a = {cantidad:parseInt($("#cantidad_articulo_"+(i+1)).val()), 
                        precio_unitario: parseFloat($("#costo_unidad_articulo_ORIGINAL_"+(i+1)).val()), 
                        descuento: ($.isNumeric($("#descuento_articulo_"+(i+1)).text()) ? parseFloat($("#descuento_articulo_"+(i+1)).text()) : 0), 
                        no_retencion: $("#producto_retencion_"+(i+1)).val(), 
                        precio_final: parseFloat($("#costo_unidad_articulo_FINAL_"+(i+1)).val().replace(/,/g, "")), 
                        exento: $("#producto_exento_"+(i+1)).val()};

                var aplicaRetencion = true;
                if(clienteEsDeTipoExento=="1" || !aplicarRetencionHacienda || clienteNoAplicaRetencion=="1"){
                    aplicaRetencion = false;
                }
 
                var detalle = getDetalleLinea(a, aplicaRetencion);
                console.log(detalle);
                IVA_Factura += detalle.iva;
                costo_sin_IVA_factura += detalle.subtotal;
                costo_retencion += detalle.retencion;
                costo_cliente_final += detalle.costo_final;
            }		
	}
        costo_total_factura = IVA_Factura + costo_sin_IVA_factura + costo_retencion;
	
	moneda = document.getElementById("tipo_moneda").value;
	if(moneda.indexOf('colone') != -1){
		costo_cliente_final = costo_cliente_final - costo_total_factura;
	}else{
		tipo_cambio_venta = document.getElementById("tipo_cambio_venta").value;
		factor_tipo_moneda_float = parseFloat(tipo_cambio_venta);
		costo_cliente_final = (costo_cliente_final/factor_tipo_moneda_float) - costo_total_factura;
		
		//Si es dolares, pasamos la retencion de colones a dolares
		costo_retencion = costo_retencion/factor_tipo_moneda_float;
	}

	//Calculamos la ganancia y le quitamos la retencion
	costo_cliente_final -= costo_retencion;
	//costo_cliente_final = costo_cliente_final.toFixed(decimales_int);
	
	//costo_sin_IVA_factura = costo_sin_IVA_factura.toFixed(decimales_int);
	//IVA_Factura = IVA_Factura.toFixed(decimales_int);
	
	//costo_total_factura = costo_total_factura.toFixed(decimales_int);
	
	//costo_retencion = costo_retencion.toFixed(decimales_int);
	
	//Como el toFixed devuelve un string debemos convertirlos de nuevo a float para formatear
	costo_cliente_final = parseFloat(costo_cliente_final);
	costo_sin_IVA_factura = parseFloat(costo_sin_IVA_factura);
	IVA_Factura = parseFloat(IVA_Factura) + parseFloat(costo_retencion);
	costo_total_factura = parseFloat(costo_total_factura);
	costo_retencion = parseFloat(costo_retencion);
	
	//Formateamos
	//costo_cliente_final = costo_cliente_final.format(decimales_int, 3, '.', ',');
	//costo_sin_IVA_factura = costo_sin_IVA_factura.format(decimales_int, 3, '.', ',');
	//IVA_Factura = IVA_Factura.format(decimales_int, 3, '.', ',');
	//costo_total_factura = costo_total_factura.format(decimales_int, 3, '.', ',');
	//costo_retencion = costo_retencion.format(decimales_int, 3, '.', ',');
	
	
	
	
	$("#ganancia").val(costo_cliente_final.format(decimales_int, 3, '.', ','));
	$("#costo").val(costo_sin_IVA_factura.format(decimales_int, 3, '.', ','));
	$("#iva").val(IVA_Factura.format(decimales_int, 3, '.', ','));
	$("#retencion").val(costo_retencion.format(decimales_int, 3, '.', ','));
	$("#costo_total").val(costo_total_factura.format(decimales_int, 3, '.', ','));
	
	/*alert(typeof costo_cliente_final);
	document.getElementById("ganancia").value = costo_cliente_final;
	
	document.getElementById("costo").value = costo_sin_IVA_factura;
	document.getElementById("iva").value = IVA_Factura;
	
	document.getElementById("costo_total").value = costo_total_factura;*/
}

function getPrecioTotalRow(num_row){
	//alert(num_row);
	/*precio_total = document.getElementById("costo_unidad_articulo_ORIGINAL_"+num_row).value;
	//alert("entro");
	if(precio_total.trim()!='')
	{
		precio_total_float = parseFloat(precio_total);
		//Preguntamos si esta en dolares
		moneda = document.getElementById("tipo_moneda").value;
		if(moneda.indexOf('colone') != -1){
			//costo_cliente_final = costo_cliente_final - costo_total_factura;
		}
		else{
			tipo_cambio_venta = document.getElementById("tipo_cambio_venta").value;
			factor_tipo_moneda_float = parseFloat(tipo_cambio_venta);
			precio_total_float = precio_total_float/factor_tipo_moneda_float;
			//costo_cliente_final = (costo_cliente_final/factor_tipo_moneda_float) - costo_total_factura;
		}
		
		cantidad = document.getElementById("cantidad_articulo_"+num_row).value;
		precio_total_float = precio_total_float*cantidad;
		return precio_total_float;
	}*/
	
	precio_total = document.getElementById("costo_total_articulo_"+num_row).innerHTML;
	
	//Quitamos el formato de moneda para que se lea bien
	precio_total = precio_total.replace(/,/g,'');
	
	//alert("entro");
	if(precio_total.trim()!='')
	{
		precio_total_float = parseFloat(precio_total);
		//Preguntamos si esta en dolares
		/*moneda = document.getElementById("tipo_moneda").value;
		if(moneda.indexOf('colone') != -1){
			//costo_cliente_final = costo_cliente_final - costo_total_factura;
		}
		else{
			tipo_cambio_venta = document.getElementById("tipo_cambio_venta").value;
			factor_tipo_moneda_float = parseFloat(tipo_cambio_venta);
			precio_total_float = precio_total_float/factor_tipo_moneda_float;
			//costo_cliente_final = (costo_cliente_final/factor_tipo_moneda_float) - costo_total_factura;
		}
		
		cantidad = document.getElementById("cantidad_articulo_"+num_row).value;
		precio_total_float = precio_total_float*cantidad;*/
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

function actualizaPreciosArticulos(cedula){	
	//Tomar codigo que tambien tenga descripcion, esto indicara que si es efectivo el producto.
	//Hacer ajax con el nuevo numero de cedula, con ese producto. getArticulo(codigo del producto, id, num_row, cedula)
	
	table = document.getElementById("tabla_productos");
	rows = cantidadFilas(table);
	//Capturar decimales
	decimales = $("#cantidad_decimales").val();
	decimales = parseInt(decimales);
	for (i = 0; i < rows; i++) 
	{
		incremental = i+1;
		codigo = $("#codigo_articulo_"+incremental).val();
		descripcion = $("#descripcion_articulo_"+incremental).html();
		if(codigo!=""&&descripcion!=""&&codigo!='00'){
			num_fila = incremental;
			$.ajax({
				url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/facturas/nueva/getArticuloJSON',
				type: "POST",
				async: false,
				data: {'cedula':cedula, 'codigo':codigo},		
				success: function(data, textStatus, jqXHR)
				{
					try{
						result = $.parseJSON('[' + data.trim() + ']');
						if(result[0].status==="error"){
							//mostrarErroresCargarArticulo(result[0].error, num_fila);
						}else if(result[0].status==="success"){	
							articulo = result[0].articulo;
							
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
														
							//Precio del articulo	
							precio_articulo_unitario = parseFloat(articulo.precio_cliente);
							precio_articulo_unitario = precio_articulo_unitario/factor_tipo_moneda;
							
							//Seteamos precio sin formato en input oculto	
							$("#costo_unidad_articulo_ORIGINAL_"+num_fila).val(precio_articulo_unitario);
							
							//Seteamos precio con formato en UI
							precio_articulo_unitario = precio_articulo_unitario.toFixed(decimales);
							precio_articulo_unitario = parseFloat(precio_articulo_unitario);
							
							descuento = parseFloat(articulo.descuento);
							cantidad = parseInt($("#cantidad_articulo_"+num_fila).val());
							
							precio_articulo_total = cantidad * (precio_articulo_unitario-(precio_articulo_unitario*(descuento/100)));
							precio_articulo_total = parseFloat(precio_articulo_total);
							precio_articulo_total = precio_articulo_total.format(decimales, 3, '.', ',');
							
							precio_articulo_unitario = precio_articulo_unitario.format(decimales, 3, '.', ',');
							$("#costo_unidad_articulo_"+num_fila).html(precio_articulo_unitario);
							$("#costo_total_articulo_"+num_fila).html(precio_articulo_total);
								
							//Seteamos el precio de cliente final para calcular ganancia
							$("#costo_unidad_articulo_FINAL_"+num_fila).val(parseFloat(articulo.precio_no_afiliado).toFixed(decimales));
							//actualizaCostoTotalArticulo("cantidad_articulo_"+num_fila);

							
							

							
						}
					}catch(e){
						//notyConTipo('¡La respuesta tiene un formato indebido, contacte al administrador!','error');
					}		
				},
				error: function (jqXHR, textStatus, errorThrown)
				{
			 
				}
			});
		}
	}	
	actualizaCostosTotales(decimales);
	updateProductsTotal();
}

var isCajaLoad = true;

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
		//actualizarCantidadProductoInventario('', codigo, row);	
		cantidad_actual = document.getElementById("cantidad_articulo_"+row).value;
		document.getElementById("cantidad_articulo_anterior_"+row).value=cantidad_actual;
	}
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////// ACTUALIZAR TOTAL PRODUCTOS ///////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////

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


/////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////// FORMATEO DE NUMEROS /////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////

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



function getDetalleLinea(a, aplicaRetencion){
    if(typeof aplicaRetencion === "undefined")
        aplicaRetencion = true;
    
    var linea = {};

    // CANTIDAD
    var cantidad = parseFloat(a.cantidad);
    
    // PRECIO UNITARIO
    var precioUnitarioSinIVA = removeIVA(parseFloat(a.precio_unitario));

    // MONTO TOTAL
    var precioTotalSinIVA = cantidad*precioUnitarioSinIVA;

    // DESCUENTO
    var descuentoPrecioSinIva = 0;
    if(parseFloat(a.descuento) > 0){
        descuentoPrecioSinIva = (precioTotalSinIVA * (parseFloat(a.descuento) / 100));
    }
	
     // SUBTOTAL
    var subTotalSinIVA = (precioTotalSinIVA - descuentoPrecioSinIva);
    linea.subtotal = parseFloat(subTotalSinIVA);

    // IMPUESTOS
    var iva = getIVAvalue_float();
    linea.iva = parseFloat((subTotalSinIVA * iva));
	linea.retencion = 0;
	linea.costo_final = 0;
    if(a.no_retencion == "0" && aplicaRetencion){
        // Para le retencion NO SE TOMA EN CUENTA EL DESCUENTO
        var precioFinalUnitarioSinIVA = removeIVA(parseFloat(a.precio_final));
        var precioFinalTotalSinIVA = cantidad*precioFinalUnitarioSinIVA;
        var montoDeImpuesto = precioFinalTotalSinIVA * iva;
        linea.retencion = montoDeImpuesto - linea.iva;
        linea.costo_final = (parseFloat(a.precio_final) - (parseFloat(a.precio_final) * (parseFloat(a.descuento) / 100))) * cantidad;
    }else{
		linea.costo_final = cantidad * parseFloat(a.precio_unitario);
	}
    
    if(a.exento == 1){ // Es exento
        linea.iva = 0;
        linea.retencion = 0;
    }

    return linea;
}

function removeIVA(price){
    var iva = getIVAvalue_float();
    return (price/(1+iva)); 
}