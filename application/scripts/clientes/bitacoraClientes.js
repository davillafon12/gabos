///////////////////////////////// VARIABLES  //////////////////////////////////////
var cedula = '';
var flagReload = false; //Bandera utilizada para ver si se recarga al cliente o no segun el resultado del ajax

function buscarCedula (e) {
    cedula = $("#cedula").val();
    getNombreCliente(cedula);	
}

function getNombreCliente(cedula){
	$("#ResultadoError").text("");
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/clientes/otros/getClienteBitacora',
		type: "POST",		
		//async: false,
		data: {'cedula':cedula},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');
				if(informacion[0].status==="error"){
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					setInformacion(informacion[0].cliente, informacion[0].cliente.bitacora);
				}
			}catch(e){
				notyError('¡La respuesta tiene un formato indebido, contacte al administrador!');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
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

function notyError(Mensaje){
	n = noty({
		layout: 'topRight',
		text: Mensaje,
		type: 'error',
		timeout: 4000
	});
}

function setInformacion(informacionArray, bitacoraMovimientos){
	$("#nombre").val(informacionArray.nombre+" "+informacionArray.apellidos);
	if (typeof bitacoraMovimientos !== 'undefined'){
		$("#ResultadoError").text("");
		setTable(convertir(bitacoraMovimientos)); 
	}else{
		limpiarTabla();
		$("#ResultadoError").text("No existen Registros !!!")
	}
	
}

function convertir(bitacoraMovimientos){
	ListaConvertir = [];
	for (i=0; i<bitacoraMovimientos.length; i++){
		var movimiento = [bitacoraMovimientos[i].Cedula, 
		bitacoraMovimientos[i].Nombre, 
		bitacoraMovimientos[i].Nombre_Usuario, 
		bitacoraMovimientos[i].Tipo_Transaccion, 
		bitacoraMovimientos[i].Fecha, 
		bitacoraMovimientos[i].Descripcion];
		ListaConvertir.push(movimiento);
	}
	return ListaConvertir; 

}

function setTable(Movimientos){
	$("#tabla_editar").dataTable().fnDestroy();
	$('#tabla_editar').DataTable( {
		data: Movimientos,
		'oLanguage': {
			'sUrl': location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/application/scripts/datatables/Spanish.txt'
		},
        columns: [
			{ title: "Fecha" },
			{ title: "Sucursal" },
			{ title: "Cédula" },			
			{ title: "Usuario" },
			{ title: "Tipo Transacción" },			
			{ title: "Descripción" }
        ]
	} );
}

function limpiarTabla(){
	$('#tabla_editar').DataTable().clear().draw(); 
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
			resetFields();
			$("#nombre").val('No existe cliente!!!');
		break;
		case '4':
			resetFields();
			$("#nombre").val('');
		break;
		case '5':
			resetFields();
			$("#nombre").val('No existe el registro de este Usuario!!!');
		break;
	}
}
