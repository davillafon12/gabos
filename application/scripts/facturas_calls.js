var isCallByDescuento = false; // Variable usada para restear campos de nombre y factura si el usuario de la cancelar al administrador

function getArticulo(codigo, id_fila, num_fila, cedula) {
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/facturas/nueva/getArticuloJSON',
		type: "POST",
		async: true,
		data: {'cedula':cedula, 'codigo':codigo},
		success: function(data, textStatus, jqXHR)
		{
			try{
				result = $.parseJSON('[' + data.trim() + ']');
				if(result[0].status==="error"){
					mostrarErroresCargarArticulo(result[0].error, num_fila);
				}else if(result[0].status==="success"){
					resetRowFields(num_fila, false);
					setArticulo(result[0].articulo, num_fila);
				}
			}catch(e){
				notyConTipo('¡La respuesta tiene un formato indebido, contacte al administrador!','error');
                                console.error(e);
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{

		}
	});
}

function mostrarErroresCargarArticulo(error, num_fila){
	switch(error){
		case '1':
			resetRowFields(num_fila, true);
			notyConTipo('¡No se pudo cargar el artículo, contacte al administrador!','error');
		break;
		case '2':
			resetRowFields(num_fila, true);
			notyConTipo('¡URL indebida, contacte al administrador!','error');
		break;
		case '4':
			resetRowFields(num_fila, false);
			notyConTipo('¡No existe cliente o cédula inválida!','error');
		break;
		case '5':
			//No existe articulo
			resetRowFields(num_fila, false);
		break;
		case '6':
			resetRowFields(num_fila, false);
			notyConTipo('¡No hay más unidades en inventario!','warning');
		break;
		case '7':
			resetRowFields(num_fila, false);
			notyConTipo('¡Este artículo no tiene asignado código Cabys!','error');
		break;
	}
}

var clienteCanBuy = true;
var infoClientePostAutorizacion = false;
var cedulaPostAuto = false;
var clienteEsExento = false;

var clienteEsDeTipoSucursal = "0";
var clienteEsDeTipoExento = "0";
var clienteNoAplicaRetencion = "0";

function getNombreCliente(str){
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/facturas/nueva/getNombreCliente?cedula='+str,
		type: "POST",
		//async: false,
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

					if(result[0].actualizar){
						notyConTipo('¡Este cliente debe actualizar datos para poder facturar! <BR>Por favor actualizar datos del cliente.', 'error');
						return false;
					}

					if(result[0].tieneCreditosVencidos){
						notyConTipo('¡Este cliente tiene créditos vencidos que debe pagar! <BR>Por favor informarle al cliente ponerse al día para poder facturarle de nuevo.', 'error');
						return false;
					}


					if(str.trim() === "2"){
						$('#pop_up_administrador').bPopup({
							modalClose: false
						});
						notyConTipo('¡Este cliente requiere de autorización!', 'warning');
						document.getElementById("pop_usuario").select();
						numeroPopUp='5';
						infoClientePostAutorizacion = result[0];  //Se guarda la info para ser utilizada despues
						cedulaPostAuto = str; //Se guarda la info para ser utilizada despues
					}else{
							//SI EXISTE EL CLIENTE
							clienteEsDeTipoSucursal = result[0].sucursal;
							clienteEsDeTipoExento = result[0].exento;
							clienteNoAplicaRetencion = result[0].retencion;
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
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/facturas/nueva/crearFacturaTemporal',
		type: "GET",
		async: false,
		success: function(data, textStatus, jqXHR)
		{
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
		},
		error: function (jqXHR, textStatus, errorThrown)
		{

		}
	});
	/*url = '/facturas/nueva/crearFacturaTemporal';
	datosFacturaTemporal = getandmakeCall(url);
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
	}*/
}

function agregarArticuloFactura(datosArticulo)
{
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
		AJAX.open("GET", location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+URL, false);
		AJAX.send(null);
		return AJAX.responseText;
	} else {
		return false;
	}
}

