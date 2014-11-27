function anularRecibo(recibo, credito){
	//alert(recibo+" "+credito);
	$.prompt("¡Esto anulará este recibo de dinero y aumentará el saldo!", {
					title: "¿Esta seguro que desea anular este recibo?",
					buttons: { "Si, estoy seguro": true, "Cancelar": false },
					submit:function(e,v,m,f){
												if(v){													
													sendAnular(recibo, credito);
												}
											}
				});	
}

function sendAnular(recibo, credito){
	$.ajax({
		url : location.protocol+'//'+document.domain+'/contabilidad/anular/anularRecibo',
		type: "POST",		
		//async: false,
		data: {'recibo':recibo, 'credito':credito},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');				
				if(informacion[0].status==="error"){					
					manejarErrores(informacion[0].error);
				}else if(informacion[0].status==="success"){
					//setInformacion(informacion[0]);
					notyMsg('¡Se anuló el recibo con éxito!', 'success');
					buscarCedula(null);
				}
			}catch(e){
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}