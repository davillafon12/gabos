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
	var cell3 = row.insertCell(2);
	var cell4 = row.insertCell(3);
	var cell5 = row.insertCell(4);
	var cell6 = row.insertCell(5);
	var cell7 = row.insertCell(6);

	// Add some text to the new cells:
	var cedula = document.getElementById('cedula').value;
	if(!isNumber(cedula))
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
					 +"<input id='producto_exento_"+siguienteFila+"' type='hidden' >";
	cell7.innerHTML = "<div class='articulo_specs' id='costo_total_articulo_"+siguienteFila+"'></div>";

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
	//actualizaCostoTotalArticulo(document.getElementById("cantidad_articulo_"+id).value, "cantidad_articulo_"+id, null);
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
	//var row_id = document.getElementById("last_id_clicked").value;
	//var id = row_id.replace("codigo_articulo_","");
		
	var id_int = parseInt(id);
	var indice_actual = array_pos_rows.indexOf(id_int);
	
	var index = obtenerIndex(1, indice_actual);
	agregarFila(index);
	//alert(index);
	//alert(array_pos_rows.join('\n'))
	//alert(obtenerIndex(where, id_int));
	
	//HideMenu("contextMenu",null);
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
	if(!isNumber(cedula))
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
    /*if (e.keyCode == 13) {
        //alert("Entro!!!");
		var cedula = document.getElementById('cedula').value;
		getNombreCliente(cedula);
	}*/
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
}

function disableArticulosInputs()
{
	var inputsCodigo = document.getElementsByClassName('input_codigo_articulo');
	for (var i = 0; i < inputsCodigo.length; i++) 
	{
		inputsCodigo[i].disabled=true;
	}
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////// TIPO DE FACTURA /////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////
		



/////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////// BUSQUEDA Y SETEO DEL ARTICULO EN TABLA //////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////

function filtrarKeys(e, id){
	if(e.which == 107){
		//alert('Entro');
		//codigo = document.getElementById(id).value;
        id_row = id.replace('codigo_articulo_','');
		//document.getElementById(id).value;
		resetRowFields(id_row, false);
		//return false;
    }
}

//element.on(?:copy|cut|paste)


//var entradas = 0;

var isFromAgregarCantidad = true;

function buscarArticulo(e, value, id){
	//entradas++;
	//Limpiamos el valor, el codigo del articulo
	value_clean = value.replace("&","");
	value_clean = value_clean.replace(";","");
	
	//Obtenemos el numero de fila
	id_row = id.replace("codigo_articulo_","");
	
	//Obtenemos la descripcion de la fila
	descripcion = document.getElementById("descripcion_articulo_"+id_row).innerHTML;	
	//alert(value_clean);
	//MANEJADOR DE EVENTOS
	//Para pasar a siguiente o agregar fila
	if(e!=null)
	{
		//Cuando sea Up down left right, no haga nada
		if(e.keyCode == 37||e.keyCode == 38||e.keyCode == 39||e.keyCode == 40||e.keyCode == 107){return false;}
		
		//Cuando si es producto generico, si es producto generico pasa 
		if (e.keyCode == 13) //Enter
		{			
			if(value_clean==='00'&&descripcion===''){openGenericProductDialog(id);}
			//alert(descripcion);
			if(descripcion!="")
			{
				//alert("Entro");
				tabRowORAdd(id, false);
				return false;
			}
		}
		
		//Si presiona backspace o delete resetea el row, y quedo en blanco
		if(e.keyCode == 8 || e.keyCode == 46) 
		{		
			if(value_clean.trim()=="")
			{
				resetRowFields(id_row, true);
			}
		}
		
		//Si presiona ctrl+x resetea el row
		if(e.ctrlKey && e.which === 88){ //Ctrl+x
			//var id_row = id.replace("codigo_articulo_","");
			resetRowFields(id_row, true);
		}
		
		//Si presiona ctrl+v no haga nada
		if(e.ctrlKey && e.which === 86){ //Ctrl+v
			return false;
		}
		
		//Si presiona ctrl+z
		if(e.ctrlKey && e.which === 90)
		{		
			resetRowFields(id_row, true);
			/*if(value_clean.trim()=="")
			{
				//alert(id_row);
				var id_row = id.replace("codigo_articulo_","");
				resetRowFields(id_row, true);
			}*/
		}
		
		//Pasar a fila o cantidad
		if (e.keyCode == 13) {
			tabRowORAdd(id, false);
		}
	}	
	
	//Limpiamos si se quita producto generico &&descripcion.trim()!=''
	if(value_clean.trim()=='0'&&descripcion!=""){
		//alert(descripcion);
		cod_anterior_generico = document.getElementById("codigo_articulo_anterior_"+id_row).value;
		//alert(cod_anterior_generico);
		if(cod_anterior_generico.trim()=='00'){resetRowFields(id_row, true);}		
	}
	
	if(articuloYaIngresado(value_clean.trim(), id)&&value_clean.trim()!='00')
	{//Si el articulo ya fue ingresado no lo vuelve a colocar
		if (e.keyCode == 13&&isFromAgregarCantidad) { //Si viene por primera vez
			isFromAgregarCantidad=false;
			agregarCantidadArticulosPopUp(id.replace("codigo_articulo_",""));
		}
		else if(isFromAgregarCantidad==false){isFromAgregarCantidad=true;}		
	}
	else
	{
		var num_row = id.replace("codigo_articulo_","");
		//alert(num_row);
		var cedula = document.getElementById('cedula').value;
		//showResult(cedula);
	
		//alert("entro");
		getArticulo(value_clean, id, num_row, cedula);		
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
		
		document.getElementById("tooltip_imagen_articulo_"+num_row).innerHTML = "<img src='http://"+document.domain+"/application/images/articulos/"+articuloARRAY[8]+".jpg' height='200' width='200'>";
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
		//document.getElementById("costo_unidad_articulo_"+num_row).innerHTML=precio_unidad_FACTOR_float.toFixed(decimales_int);
		
		
		
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

	if(flag_cod){document.getElementById("codigo_articulo_"+id_row).value="";}
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
	resetAll();
}

function salidaSesion(){
	/*resetAll();
	window.onbeforeunload=null;
	window.location = 'http://'+document.domain+'/home/logout';*/
	
}

/*window.onbeforeunload=advertenciaSalida;
window.onunload=deshacerFactura;*/

function resetCostosTotales(){
	document.getElementById("ganancia").value = "";
	document.getElementById("costo").value = "";
	document.getElementById("iva").value = "";
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
	//ACTUALIZAMOS OTROS CAMPOS
	//alert(id);
	if(isCajaLoad){
		validarMaxMinCantidad(id);
	}
	//alert(id);
	num_row = id.replace("cantidad_articulo_","");
	//alert(id);
	cantidad = document.getElementById("cantidad_articulo_"+num_row).value;
	updateProductsTotal();
	//Para pasar a siguiente o agregar fila
	/*if(e!=null){
		if (e.keyCode == 13) {
			tabRowORAdd(id);
		}
	}*/
	////////////////////////////

	//alert(cantidad+" "+id);
	
	descuento = document.getElementById('descuento_articulo_'+num_row).innerHTML;
	precio_unidad = document.getElementById('costo_unidad_articulo_ORIGINAL_'+num_row).value;
	//alert(precio_unidad);
	moneda = document.getElementById("tipo_moneda").value;
		if(moneda.indexOf('colone') != -1){
			//costo_cliente_final = costo_cliente_final - costo_total_factura;
		}
		else{
			tipo_cambio_venta = document.getElementById("tipo_cambio_venta").value;
			factor_tipo_moneda_float = parseFloat(tipo_cambio_venta);
			precio_unidad = precio_unidad/factor_tipo_moneda_float;
			//costo_cliente_final = (costo_cliente_final/factor_tipo_moneda_float) - costo_total_factura;
		}
		
	//Conversion de tipos
	descuento_float = parseFloat(descuento);
	precio_unidad_float = parseFloat(precio_unidad);
	cantidad_float = parseFloat(cantidad);
	//Calculos matematicos
	descuento_float = descuento_float/100;
	descuento_float = descuento_float*precio_unidad_float;
	precio_final_por_unidad = precio_unidad_float - descuento_float;
	precio_total = precio_final_por_unidad * cantidad_float;
	//Cargamos la cantidad de decimales permitidos
	var decimales = document.getElementById("cantidad_decimales").value;
	var decimales_int = parseInt(decimales);
	//Cargamos el valor al div
	var costo_total = document.getElementById("costo_total_articulo_"+num_row);
	
	//Formateamos el valor
	precio_total = precio_total.toFixed(decimales_int);
	precio_total = parseFloat(precio_total);		
	precio_total = precio_total.format(2, 3, '.', ',');	
	costo_total.innerHTML = precio_total;
	//costo_total.innerHTML = precio_total.toFixed(decimales_int);
	//alert(num_row);
	//Actualizamos el costo total
	actualizaCostosTotales(decimales_int);
	codigo = document.getElementById("codigo_articulo_"+num_row).value;
	//alert("2. => "+cantidad);
	//alert(cantidad);
	inventario = document.getElementById("cantidad_articulo_"+num_row).value;
	//alert('actualizaCostoTotalArticulo');
	//actualizarCantidadProductoInventario(inventario, codigo, num_row);
	//alert(cantidad);
	
}

function actualizaCostosTotales(decimales_int){
	//alert(porcentaje_iva);
	//Reiniciamos los valores
	costo_sin_IVA_factura = 0.0;
	costo_total_factura = 0.0;
	IVA_Factura = 0.0;
	costo_cliente_final = 0.0;
	//var costo_total_sin_iva_float = 0.0;
	
	var table = document.getElementById("tabla_productos");
	var rows = cantidadFilas(table);
	for (var i = 0; i < rows; i++) 
	{	
		precio_unitario_articulo = getPrecioTotalRow(i+1);
		precio_cliente_final_articulo = getPrecioTotalRowFINAL(i+1);
		//costo_total_sin_iva_float = costo_total_sin_iva_float + getPrecioTotalRow(i+1);
		
		isExento = document.getElementById("producto_exento_"+(i+1)).value;
		
		costo_unitario_articulo_sin_IVA = 0.0;
		
		if(isExento==='0'){
			costo_unitario_articulo_sin_IVA = precio_unitario_articulo/(1+porcentaje_iva);
		}
		else if(isExento==='1'){
			costo_unitario_articulo_sin_IVA = precio_unitario_articulo;
		}
		
		costo_sin_IVA_factura += costo_unitario_articulo_sin_IVA;
		//IVA_Factura += IVA_Factura + (precio_unitario_articulo-costo_unitario_articulo_sin_IVA);
		costo_total_factura += precio_unitario_articulo;
		costo_cliente_final += precio_cliente_final_articulo;
	}
	
	moneda = document.getElementById("tipo_moneda").value;
	if(moneda.indexOf('colone') != -1){
		costo_cliente_final = costo_cliente_final - costo_total_factura;}
	else{
		tipo_cambio_venta = document.getElementById("tipo_cambio_venta").value;
		factor_tipo_moneda_float = parseFloat(tipo_cambio_venta);
		costo_cliente_final = (costo_cliente_final/factor_tipo_moneda_float) - costo_total_factura;
	}
	//Formateo e impresion a UI
	//precio_unidad_FACTOR_float = precio_unidad_FACTOR_float.format(2, 3, '.', ',');
	
	IVA_Factura = costo_total_factura-costo_sin_IVA_factura;
	
	costo_cliente_final = costo_cliente_final.toFixed(decimales_int);
	costo_sin_IVA_factura = costo_sin_IVA_factura.toFixed(decimales_int);
	IVA_Factura = IVA_Factura.toFixed(decimales_int);
	costo_total_factura = costo_total_factura.toFixed(decimales_int);
	
	//Como el toFixed devuelve un string debemos convertirlos de nuevo a float para formatear
	costo_cliente_final = parseFloat(costo_cliente_final);
	costo_sin_IVA_factura = parseFloat(costo_sin_IVA_factura);
	IVA_Factura = parseFloat(IVA_Factura);
	costo_total_factura = parseFloat(costo_total_factura);
	
	//Formateamos
	costo_cliente_final = costo_cliente_final.format(2, 3, '.', ',');
	costo_sin_IVA_factura = costo_sin_IVA_factura.format(2, 3, '.', ',');
	IVA_Factura = IVA_Factura.format(2, 3, '.', ',');
	costo_total_factura = costo_total_factura.format(2, 3, '.', ',');
	
	
	
	
	
	$("#ganancia").val(costo_cliente_final);
	$("#costo").val(costo_sin_IVA_factura);
	$("#iva").val(IVA_Factura);
	$("#costo_total").val(costo_total_factura);
	
	/*document.getElementById("ganancia").value = costo_cliente_final.toFixed(decimales_int);
	//alert(costo_total_sin_iva_float);
	document.getElementById("costo").value = costo_sin_IVA_factura.toFixed(decimales_int);
	//var iva_total = costo_total_sin_iva_float*(impuesto_venta_float/100);
	IVA_Factura = costo_total_factura-costo_sin_IVA_factura;
	document.getElementById("iva").value = IVA_Factura.toFixed(decimales_int);
	//var costo_total = iva_total+costo_total_sin_iva_float;
	document.getElementById("costo_total").value = costo_total_factura.toFixed(decimales_int);
	*/
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
	precio_total = precio_total.replace(',','');
	
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
	//alert("entro");
	
	//Tomar codigo que tambien tenga descripcion, esto indicara que si es efectivo el producto.
	//Hacer ajax con el nuevo numero de cedula, con ese producto. getArticulo(codigo del producto, id, num_row, cedula)
	
	var table = document.getElementById("tabla_productos");
	var rows = cantidadFilas(table);
	for (var i = 0; i < rows; i++) 
	{
		incremental = i+1;
		id_codigo = "codigo_articulo_"+incremental;
		id_descripcion = "descripcion_articulo_"+incremental;
		codigo = document.getElementById(id_codigo).value;
		descripcion = document.getElementById(id_descripcion).innerHTML;
		if(codigo!=""&&descripcion!=""&&codigo!='00'){
			cantidad_articulo = document.getElementById("cantidad_articulo_"+incremental).value;
			inventario_guardado = document.getElementById("bodega_articulo_"+incremental).innerHTML;
			//alert('Inventario: '+inventario);
			//Devolvemos el inventario			
			//actualizarCantidadProductoInventario('0', codigo, incremental);
			//document.getElementById("cantidad_articulo_anterior_"+incremental).value=-1;
			//alert('codigo: '+codigo+" num_row: "+incremental+" Valor anterior: "+document.getElementById("cantidad_articulo_anterior_"+incremental).value);
			//////////////////////////////////////
			
			isActualizarCliente=true;
			//alert(isActualizarCliente);
			getArticulo(codigo, "codigo_articulo_"+incremental, incremental, cedula);
			//alert(cantidad_articulo);
			document.getElementById("cantidad_articulo_"+incremental).value=cantidad_articulo;
			//alert(inventario);
			document.getElementById("bodega_articulo_"+incremental).innerHTML=inventario_guardado;
			document.getElementById("cantidad_articulo_"+incremental).setAttribute("max",inventario_guardado);
			//alert(incremental);
			actualizaCostoTotalArticulo("cantidad_articulo_"+incremental);
			//actualizarCantidadProductoInventario('', codigo, incremental);
			cantidadNuevaHide = document.getElementById("cantidad_articulo_"+incremental).value;
			//alert(cantidadNuevaHide);
			document.getElementById("cantidad_articulo_anterior_"+incremental).value=cantidadNuevaHide;
		}
	}
	
	/*var table = document.getElementById("tabla_productos");
	var rows = cantidadFilas(table);
	for (var i = 0; i < rows; i++) 
	{
		var incremental = i+1;
		var id = "codigo_articulo_"+incremental;
		var value = document.getElementById(id).value;
		buscarArticulo(null, value, id);
	}*/
}

var isCajaLoad = true;

function cambiarCantidad(id, e, value){
	if(isCajaLoad){
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




