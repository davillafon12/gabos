function buscarCedula (e) 
{
	cedula = $("#cedula").val();
	if(!isNumber(cedula))
	{ 
		$("#nombre").val('');
		resetFields();
	}
	else
	{	
		getNombreCliente(cedula);
	}
}

function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

function getNombreCliente(cedula){
	$.ajax({
		url : location.protocol+'//'+document.domain+'/clientes/autorizaciones/getCliente',
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
					//alert(JSON.stringify(informacion[0].cliente, null, 4));
					setInformacion(informacion[0].cliente);
				}
			}catch(e){
				//alert(e);
				notyError('¡La respuesta tiene un formato indebido, contacte al administrador!');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

function setInformacion(informacionArray){
	$("#nombre").val(informacionArray.nombre+" "+informacionArray.apellidos);
	setPersonaUno(informacionArray.persona1);
	setPersonaDos(informacionArray.persona2);
}

function manejarErrores(error){
	switch(error){
		case '1':
			notyError('¡No se pudo tramitar la información, contacte al administrador!');
			resetFields();
		break;
		case '2':
			notyError('¡Error en el envio de la URL, contacte al administrador!');
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

function resetFields(){
	$("#cedula_persona_uno").val('');
	$("#nombre_persona_uno").val('');
	$("#apellido_persona_uno").val('');
	$("#cedula_persona_dos").val('');
	$("#nombre_persona_dos").val('');
	$("#apellido_persona_dos").val('');
	$("#carta_persona_1").html('');
	$("#carta_persona_2").html('');
}

function setPersonaUno(persona1){
	$("#cedula_persona_uno").val(persona1.cedula);
	$("#nombre_persona_uno").val(persona1.nombre);
	$("#apellido_persona_uno").val(persona1.apellidos);
	$("#carta_persona_1").html("<img src='"+location.protocol+"//"+document.domain+"/application/images/cartas/"+persona1.carta+"' width='400' height='500'/>");
}

function setPersonaDos(persona2){
	$("#cedula_persona_dos").val(persona2.cedula);
	$("#nombre_persona_dos").val(persona2.nombre);
	$("#apellido_persona_dos").val(persona2.apellidos);
	$("#carta_persona_2").html("<img src='"+location.protocol+"//"+document.domain+"/application/images/cartas/"+persona2.carta+"' width='400' height='500'/>");
}

function setUpLiveSearch(){	
	$("#nombre").autocomplete({
		  source: location.protocol+'//'+document.domain+'/facturas/nueva/getNombresClientesBusqueda',
		  minLength: 1,
		  select: function( event, ui ) {
			$("#cedula").val(ui.item.id);
			evt = document.createEvent("KeyboardEvent");
			buscarCedula(evt); 		  
		  }
		});
}