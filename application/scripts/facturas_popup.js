var rowIDpopup=''; //Esta variable almacena el row de donde se origino el evento y donde se escribira el articulo
var fromCheck = true;
var numeroPopUp = '' //Tipo de popup a abrir

function openGenericProductDialog(rowID){ //Funcion para abrir el pop up
	$('#pop_up_administrador').bPopup({
		modalClose: false
	});
	document.getElementById("pop_usuario").select();
	rowIDpopup = rowID;
	numeroPopUp='1';
}

function validateNpass(currentID, nextID, e){
	//alert("Entro");
	if(fromCheck){}
	else{fromCheck=true; return false;}
	
	if(e!=null){
		if (e.keyCode == 13) 
		{
		    //Si viene del aceptar del modal del administrador validar si va para articulo o descuento
			if(nextID=='administrador'){
				if(numeroPopUp=='1'){nextID='pop_descripcion';}
				else if(numeroPopUp=='2'){nextID='pop_descuento_cambio';}
				else if(numeroPopUp=='3'){anularPost(); return false;}//Pop proveniente de caja
				else if(numeroPopUp=='4'){makeFacturaEditable(); return false;}//Pop proveniente de caja
				else if(numeroPopUp=='5'){autorizadoClienteDescuento(); return false;}
			}
			//if(currentID.trim=='pop_descripcion'){return false;}
			if(nextID=='boton_aceptar_popup'||nextID=='boton_aceptar_popup_admin'||nextID=='boton_aceptar_popup_desc'||nextID=='boton_aceptar_popup_cantidad'){document.getElementById(nextID).focus();}
			else if(nextID!=''){document.getElementById(nextID).select();}//Nos pasamos luego validamos			
			validatePopUp(currentID);			
		}
	}
}

function validatePopUp(currentID){
	switch(currentID) {
				case 'pop_descripcion':
					//Limpiamos de caracteres de escape
					pop_descripcion = document.getElementById(currentID).value;
					pop_descripcion = pop_descripcion.replace("&","");
					pop_descripcion = pop_descripcion.replace(";","");
					pop_descripcion = pop_descripcion.replace("/","");
					document.getElementById(currentID).value = pop_descripcion;
					break;
				case 'pop_cantidad':
					pop_cantidad = document.getElementById(currentID).value;
					pop_inventario = document.getElementById('pop_inventario').value;
					if(isNumber(pop_cantidad))
					{
						pop_cantidad=parseInt(pop_cantidad);
						if(pop_cantidad<1){pop_cantidad=1;}
						else if(pop_cantidad>pop_inventario){pop_cantidad=pop_inventario;}						
					}
					else
					{pop_cantidad=1;}
					document.getElementById(currentID).value = pop_cantidad;
					break;	
				case 'pop_descuento':
					pop_descuento = document.getElementById(currentID).value;
					if(isNumber(pop_descuento))
					{
						pop_descuento=parseInt(pop_descuento);
						if(pop_descuento<1){pop_descuento=0;}
						else if(pop_descuento>100){pop_descuento=100;}						
					}
					else
					{pop_descuento=0;}
					document.getElementById(currentID).value = pop_descuento;
					break;	
				case 'pop_costo_unidad':
					pop_costo_unidad = document.getElementById(currentID).value;
					decimales = document.getElementById("cantidad_decimales").value;
		            decimales_int = parseInt(decimales);
					if(isNumber(pop_costo_unidad))
					{
						//pop_costo_unidad=parseInt(pop_descuento);
						if(pop_costo_unidad<0){pop_costo_unidad=0.0;}						
						//pop_costo_unidad = pop_costo_unidad.toFixed(decimales_int);
					}
					else
					{pop_costo_unidad=0.0;}	
					pop_costo_unidad = parseFloat(pop_costo_unidad);
					document.getElementById(currentID).value = pop_costo_unidad.toFixed(decimales_int);
					break;					
				case 'boton_aceptar_popup':
					setArticuloFromPopup();
					closePopUp();
					doTabAfterPopup();
					break;
				case 'pop_descuento_cambio':
					pop_descuento_cambio = document.getElementById(currentID).value;
					if(isNumber(pop_descuento_cambio))
					{
						pop_descuento_cambio=parseInt(pop_descuento_cambio);
						if(pop_descuento_cambio<1){pop_descuento_cambio=0;}
						else if(pop_descuento_cambio>100){pop_descuento_cambio=100;}						
					}
					else
					{pop_descuento_cambio=0;}
					document.getElementById(currentID).value = pop_descuento_cambio;
					break;
				case 'pop_cantidad_agregar':
					pop_cantidad_agregar = document.getElementById(currentID).value;
					pop_inventario = document.getElementById('bodega_articulo_'+numRowArticuloRepetido).innerHTML;
					cantidad_actual = document.getElementById('cantidad_articulo_'+numRowArticuloRepetido).value;
					cantidad_actual = parseInt(cantidad_actual);
					pop_inventario = parseInt(pop_inventario);
					pop_inventario -= cantidad_actual;
					if(isNumber(pop_cantidad_agregar))
					{
						pop_cantidad_agregar=parseInt(pop_cantidad_agregar);
						if(pop_cantidad_agregar<1){pop_cantidad_agregar=1;}
						else if(pop_cantidad_agregar>pop_inventario){pop_cantidad_agregar=pop_inventario;}						
					}
					else
					{pop_cantidad_agregar=1;}
					document.getElementById(currentID).value = pop_cantidad_agregar;
					break;	
			}
}

function setArticuloFromPopup(){
	//alert(rowIDpopup);
	pop_descripcion = document.getElementById('pop_descripcion').value;
	pop_cantidad = document.getElementById('pop_cantidad').value;
	pop_inventario = document.getElementById('pop_inventario').value;
	pop_descuento = document.getElementById('pop_descuento').value;
	pop_costo_unidad = document.getElementById('pop_costo_unidad').value;
	
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
	articuloJSON = {"codigo":"00","descripcion":pop_descripcion,"inventario":pop_inventario,"descuento":pop_descuento,"familia":"0","precio_cliente":pop_costo_unidad,"precio_no_afiliado":pop_costo_unidad,"imagen":"Default.png","exento":"0","retencion":"0"};
	//datosArticulo = "1,00,"+pop_descripcion+","+pop_inventario+","+pop_descuento+",0,"+pop_costo_unidad+","+pop_costo_unidad+",00,0";
	num_row = rowIDpopup.replace("codigo_articulo_","");
	//setDatosArticulo(datosArticulo.split(','), rowIDpopup, num_row,pop_cantidad);
	setArticulo(articuloJSON, num_row);
}


var isCajaLoaded = false; //Variable utilizada para ver si estamos en caja y limpiar 
//campos de nombre y cedula cuando no hay autorizacion de descuento al traer un cliente

function closePopUp(){
	$('#pop_up_articulo').bPopup().close();
}

function clickAceptar(){
	validatePopUp('pop_descripcion');
	validatePopUp('pop_cantidad');
	validatePopUp('pop_inventario');
	validatePopUp('pop_descuento');
	validatePopUp('pop_costo_unidad');
	setArticuloFromPopup();
	closePopUp();
	doTabAfterPopup();
}

function closePopUp_Admin(){
	$('#pop_up_administrador').bPopup().close();
	if(isCallByDescuento){
		$("#cedula").val('');
		$("#nombre").val('');
		isCallByDescuento = false; //Proviene de facturasCall.js
	}
	if(isCajaLoaded&&seCambioFactura){
		$("#cedula").val('');
		$("#nombre").val('');
	}
	
}

function clickAceptar_Admin(event){
	if(checkAdminLog()){
		isCallByDescuento = false; // Eliminados el flag para que no borre los campos
		isCajaLoaded = false; //Para que no nos quite los campos de nombre y cedula
		closePopUp_Admin();
		isCajaLoaded = true; //Lo volvemos a poner
		if(numeroPopUp=='1'){ //Si es articulo
			$('#pop_up_articulo').bPopup({
				modalClose: false
			});		
		}
		else if(numeroPopUp=='2'){ //Si es descuento
			$('#pop_up_descuento').bPopup({
				modalClose: false
			});	
		}
		else if(numeroPopUp=='4'){ //Si es descuento
			event.stopPropagation();
			event.preventDefault();
			makeFacturaEditable();
		}
		//document.getElementById("pop_descripcion").select();
		//rowIDpopup = rowID;
	}
	else{
		n = noty({
					   layout: 'topRight',
					   text: 'Información incorrecta!!!',
					   type: 'error',
					   timeout: 4000
					});		
		fromCheck=false;
		document.getElementById("pop_usuario").select();
	}
}

function doTabAfterPopup(){
	tabRowORAdd(rowIDpopup, true);
}

function checkAdminLog(){
	usuario_check = document.getElementById("pop_usuario").value;
	contra_usuario = CryptoJS.MD5(document.getElementById("pop_password").value); //Lo encriptamos de una vez
	document.getElementById("pop_password").value=''; //Lo limpiamos para que no quede evidencia del pass
	document.getElementById("pop_usuario").value=''; //Limpiamos 
	
	url = '/facturas/nueva/checkUSR?user='+usuario_check+'&pass='+contra_usuario+'&tipo='+numeroPopUp;
    
	contra_usuario=''; //Limpiamos
	
	flag = getandmakeCall(url);
		
	if(flag.trim()=='-1'){return false;}
	else if(flag.trim()=='200'){return true;}
}

function changeDiscount(row_num){
	if (typeof seCambioFactura !== 'undefined') {
	    if(!seCambioFactura){return false;}
	}
	//alert(row_num);
	$('#pop_up_administrador').bPopup({
		modalClose: false
	});
	document.getElementById("pop_usuario").select();
	numeroPopUp='2';
	rowIDpopup=row_num;
}

function closePopUp_Des(){
	$('#pop_up_descuento').bPopup().close(); 
}

function clickAceptar_Des(){
	isFromAgregarCantidad=false;
	validatePopUp('pop_descuento_cambio');
	setDescuento();
	
	closePopUp_Des();
	//doTabAfterPopup();
}

function setDescuento(){
	descuento = document.getElementById("pop_descuento_cambio").value;	
	document.getElementById("descuento_articulo_"+rowIDpopup).innerHTML=descuento;
	
	//Cambiamos el costo del articulo
	/*costo_unidad = document.getElementById("costo_unidad_articulo_ORIGINAL_"+rowIDpopup).value;
	descuento = parseInt(descuento);
	costo_unidad = parseFloat(costo_unidad);	
	costo_unidad -= costo_unidad*(descuento/100);
	
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
	
	decimales = document.getElementById("cantidad_decimales").value;
	decimales_int = parseInt(decimales);
	
	costo_unidad = costo_unidad/factor_tipo_moneda_float;
	
	document.getElementById("costo_unidad_articulo_"+rowIDpopup).innerHTML=costo_unidad.toFixed(decimales_int);	
	*/
	actualizaCostoTotalArticulo("cantidad_articulo_"+rowIDpopup);	
	tabRowORAdd("codigo_articulo_"+rowIDpopup, true);
}

function agregarCantidadArticulosPopUp(num_row){
	$('#pop_up_cantidad').bPopup({
		modalClose: false
	});	
	rowIDpopup=num_row;
	document.getElementById("pop_cantidad_agregar").select();
}

function closePopUp_Can(){
	$('#pop_up_cantidad').bPopup().close(); 
}

function clickAceptar_Can(){
	validatePopUp('pop_cantidad_agregar');
	cantidad_actual = document.getElementById('cantidad_articulo_'+numRowArticuloRepetido).value;
	cantidad_actual = parseInt(cantidad_actual);
	cantidad_agregar = document.getElementById('pop_cantidad_agregar').value;
	cantidad_agregar = parseInt(cantidad_agregar);
	cantidad_final = cantidad_actual+cantidad_agregar;
	document.getElementById('cantidad_articulo_'+numRowArticuloRepetido).value=cantidad_final;
	cambiarCantidad('cantidad_articulo_'+numRowArticuloRepetido, null, cantidad_final);
	
	closePopUp_Can();
	//document.getElementById('cantidad_articulo_'+rowIDpopup).select();
	document.getElementById('codigo_articulo_'+rowIDpopup).value='';
	
	document.getElementById('codigo_articulo_'+rowIDpopup).focus();
}

