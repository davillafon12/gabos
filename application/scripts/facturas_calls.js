var isCallByDescuento = false; // Variable usada para restear campos de nombre y factura si el usuario de la cancelar al administrador

function getArticulo(str, id, num_row, cedula) {
	url = '/facturas/nueva/getArticuloXML?codigo='+str+'&cedula='+cedula;
    datosArticulo = getandmakeCall(url);
	//alert(datosArticulo);
	datosArticuloARRAY = datosArticulo.split(',');
	//alert(datosArticuloARRAY[0]);
	
	setDatosArticulo(datosArticuloARRAY, id, num_row, 1);
	/*if(datosArticuloARRAY[0].trim()==='1'){
		setDatosArticulo(datosArticuloARRAY, id, num_row, 1);	
	}
	else{return false;}*/
}

/*function getNombreCliente(str) {
	url = '/facturas/nueva/getNombreCliente?cedula='+str;
    nombre_cliente = getandmakeCall(url);
	if(nombre_cliente.indexOf('Cliente Contad')!=-1)
	{
	//document.getElementById('nombre').disabled=false;
	}
	else
	{
	//document.getElementById('nombre').disabled=true;
	}
	document.getElementById('nombre').value=nombre_cliente;
	if(document.getElementById('nombre').value!='No existe cliente!!!')
	{
		enableArticulosInputs();
		//isActualizarCliente=true;
		actualizaPreciosArticulos(str);
	}
	else
	{
		disableArticulosInputs();
	}
	
}*/

var clienteCanBuy = true;
var infoClientePostAutorizacion = false;
var cedulaPostAuto = false;

function getNombreCliente(str){
	$.ajax({
		url : location.protocol+'//'+document.domain+'/facturas/nueva/getNombreCliente?cedula='+str,
		type: "POST",
		async: false,
		data: {'cedula':str},		
		success: function(data, textStatus, jqXHR)
		{
			try{
				result = $.parseJSON('[' + data.trim() + ']');
				if(result[0].status==="error"){
				//NO HAY CLIENTE QUE CORRESPONDA A ESA CEDULA
					$("#nombre").val('No existe cliente!!!');
					disableArticulosInputs();
				}else if(result[0].status==="success"){	
				//SI EXISTE EL CLIENTE
					switch(result[0].estado.trim()){ //Segun el estado del cliente debemos reportarlo
						case 'activo':						
							if(result[0].descuento){
								isCallByDescuento = true;
								//$("#nombre").val('');
								//$("#cedula").val('');
								$('#pop_up_administrador').bPopup({
									modalClose: false
								});
								notyConTipo('¡Este cliente tiene descuento y necesita autorización!', 'warning');
								document.getElementById("pop_usuario").select();
								numeroPopUp='5';
								infoClientePostAutorizacion = result[0];  //Se guarda la info para ser utilizada despues
								cedulaPostAuto = str; //Se guarda la info para ser utilizada despues
							}else{
								$("#nombre").val(result[0].nombre);
								enableArticulosInputs();
								actualizaPreciosArticulos(str);
								clienteCanBuy = true;
							}
							break;
						case 'semiactivo':
							//alert(result[0].descuento);
							if(result[0].descuento){ //Si el cliente tiene descuento pide autorizacion
								isCallByDescuento = true;
								$('#pop_up_administrador').bPopup({
									modalClose: false
								});
								notyConTipo('¡Este cliente tiene descuento y necesita autorización!', 'warning');
								document.getElementById("pop_usuario").select();
								numeroPopUp='5';
								infoClientePostAutorizacion = result[0];  //Se guarda la info para ser utilizada despues
								cedulaPostAuto = str; //Se guarda la info para ser utilizada despues
								/*$("#nombre").val(result[0].nombre);
								enableArticulosInputs();
								actualizaPreciosArticulos(str);
								notyConTipo('¡Este cliente no logró la meta mensual de compra!', 'warning');
								clienteCanBuy = true;*/
							}else{
								$("#nombre").val(result[0].nombre);
								enableArticulosInputs();
								actualizaPreciosArticulos(str);
								notyConTipo('¡Este cliente no logró la meta mensual de compra!', 'error');
								clienteCanBuy = true;
							}
							break;
						case 'inactivo':
							$("#nombre").val('');
							disableArticulosInputs();
							notyConTipo('¡Este cliente esta inactivo, contacte un administrador para poder activarlo!','error');
							clienteCanBuy = false;
							break;
					}									
				}
			}catch(e){
				notyConTipo('¡La respuesta tiene un formato indebido, contacte al administrador!','error');
			}		
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

function autorizadoClienteDescuento(){ //Despues de autorizado el cliente
	isCallByDescuento = false; //Como ya paso el log tons lo ponemos en false de nuevo
	$("#nombre").val(infoClientePostAutorizacion.nombre);
	enableArticulosInputs();
	actualizaPreciosArticulos(cedulaPostAuto);
	
	if(infoClientePostAutorizacion.estado==='semiactivo'){notyConTipo('¡Este cliente no logró la meta mensual de compra!', 'warning');}
	
	clienteCanBuy = true;	
	
	//Limpiamos variables
	infoClientePostAutorizacion = false;
	cedulaPostAuto = false;
	
	$("#codigo_articulo_1").select();
}

var codigoFacturaTemporal = -1;
var tokenFacturaTemporal = -1;

function setFacturaTemporal(){
	url = '/facturas/nueva/crearFacturaTemporal';
	datosFacturaTemporal = getandmakeCall(url);
	//alert(datosFacturaTemporal);
	if(datosFacturaTemporal.indexOf('fals') != -1){
		$('#error_crear_factura_popup').bPopup({
			modalClose: false
		});
		//Deshabilitamos la entrada de teclas
		$('html').bind('keypress', function(e)
		{
		   return false;
		});
	}
	else{
		if(codigoFacturaTemporal==-1&&tokenFacturaTemporal==-1){
			arrayFacturaTemporal = datosFacturaTemporal.split('|');
			codigoFacturaTemporal = arrayFacturaTemporal[0].trim();
			tokenFacturaTemporal = arrayFacturaTemporal[1];
		}
	}
	//alert("Codigo: "+codigoFacturaTemporal+"\nToken: "+tokenFacturaTemporal);
}

function agregarArticuloFactura(datosArticulo)
{
	//alert(datosArticulo[1]);
	codigo = datosArticulo[1];
	codigo = codigo.trim();
	if(codigo.indexOf('00')!=-1){
		//Si es generico no hacemos nada
	}
	else{
		datosArticulo = datosArticulo.join(",");//Convertimos a string
		datosArticulo = datosArticulo+","+codigoFacturaTemporal; //Agregamos el codigo de la factura
		url = '/facturas/nueva/agregarArticuloFactura?datosArticulo='+datosArticulo;
		datosArticulo = getandmakeCall(url);
		//alert(codigo);
	}
}

function getXMLHTTP(){
	if (window.XMLHttpRequest) 
	{
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} 
	else 
	{  // code for IE6, IE5
		xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');
	}
	return xmlhttp;
}

function getandmakeCall(URL){
	/*xmlhttp = getXMLHTTP();	
	xmlhttp.onreadystatechange=function() 
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200) 
		{				
			return xmlhttp.responseText;
		}
	}	
	xmlhttp.open('GET',URL,true);
	xmlhttp.send();*/
	AJAX = getXMLHTTP();
	if (AJAX) {
		AJAX.open("GET", location.protocol+'//'+document.domain+URL, false);                             
		AJAX.send(null);
		return AJAX.responseText;                                         
	} else {
		return false;
	}
}