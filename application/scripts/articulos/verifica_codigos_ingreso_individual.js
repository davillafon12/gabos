var codigoDisponible = false; //lleva el control si el codigo nuevo digitado esta disponible o no

function habilitarCampos(){
	$("#articulo_descripcion").prop('disabled', false);
	$("#articulos_cantidad").prop('disabled', false);
	$("#articulos_cantidad_defectuoso").prop('disabled', false);
	$("#exento").prop('disabled', false);
	$("#boton_submit").prop('disabled', false);
	$("#familia").prop('disabled', false);
	$("#descuento").prop('disabled', false);
	$("#foto_articulo").prop('disabled', false);
	$("#costo").prop('disabled', false);
	$("#precio1").prop('disabled', false);
	$("#precio2").prop('disabled', false);
	$("#precio3").prop('disabled', false);
	$("#precio4").prop('disabled', false);
	$("#precio5").prop('disabled', false);
}

function deshabilitarCampos(){
	$("#articulo_descripcion").prop('disabled', true);
	$("#articulos_cantidad").prop('disabled', true);
	$("#articulos_cantidad_defectuoso").prop('disabled', true);
	$("#exento").prop('disabled', true);
	$("#boton_submit").prop('disabled', true);
	$("#familia").prop('disabled', true);
	$("#descuento").prop('disabled', true);
	$("#foto_articulo").prop('disabled', true);
	$("#costo").prop('disabled', true);
	$("#precio1").prop('disabled', true);
	$("#precio2").prop('disabled', true);
	$("#precio3").prop('disabled', true);
	$("#precio4").prop('disabled', true);
	$("#precio5").prop('disabled', true);
}

function notyMsg(Mensaje, tipo){
	n = noty({
			   layout: 'topRight',
			   text: Mensaje,
			   type: tipo,
			   timeout: 4000
			});
}

function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

function verificarCodigoArticulo(){
	codigo = $("#articulo_codigo").val();
	sucursal = $("#sucursal").val();
	
	if(codigo.trim()==''||sucursal.trim()==''){
		//En caso de que sea vacio		
		codigoDisponible = false;
		$("#status").html('');	
		return false;
	}
	
	/*if(!isNumber(codigo)){
		codigoDisponible = false;
		$("#status").html('');	
		notyMsg('Código de Artículo no Válido', 'error');
		deshabilitarCampos();
		return false;
	}*/
	
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/articulos/registrar/es_Codigo_Utilizado',
		type: "POST",		
		async: false,
		data: {'codigo':codigo, 'sucursal':sucursal},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');
				if(informacion[0].status==="error"){					
					codigoDisponible = false;
					$("#status").html("<img src="+location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+"/application/images/scripts/error.gif />");
					$("#cod_Barras").html('');
					deshabilitarCampos();
				}else if(informacion[0].status==="success"){					
					codigoDisponible = true;
					$("#status").html("<img src="+location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+"/application/images/scripts/tick.gif />");
					
					//Crear codigo barras
					$("#cod_Barras").html("<img src="+location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+"/application/libraries/barcode.php?codetype=Code25&size=40&text="+codigo+"/>");	
					habilitarCampos();
				}
			}catch(e){				
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

$(function() {
    //$("#articulo_codigo").numeric();
	$("#articulos_cantidad").numeric();
	$("#articulos_cantidad_defectuoso").numeric();
	$("#descuento").numeric();
	$("#costo").numeric(",");
	$("#precio1").numeric(",");
	$("#precio2").numeric(",");
	$("#precio3").numeric(",");
	$("#precio4").numeric(",");
	$("#precio5").numeric(",");
});

function llamarCodigoBarras(codigo){
	codigo_barras = document.getElementById('');
	codigo_barras.innerHTML='<img alt=\"12345\" src=\"../application/libraries/barcode.php?codetype=Code25&size=40&text='+codigo+'\"/>';
}

function cambiosSucursal(){
	verificarCodigoArticulo();
	cargarFamilias();
}

function cargarFamilias(){
	sucursal = $("#sucursal").val();
	
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/articulos/ingresar/getFamiliasSucursal',
		type: "POST",		
		async: false,
		data: {'sucursal':sucursal},				
		success: function(data, textStatus, jqXHR)
		{
			try{
				informacion = $.parseJSON('[' + data.trim() + ']');
				if(informacion[0].status==="error"){					
					
				}else if(informacion[0].status==="success"){					
					setFamilias(informacion[0].familias);
				}
			}catch(e){				
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
}

function setFamilias(familias){	
	cuerpo = '';
	for(i=0; i<familias.length; i++){
		cuerpo += "<option value='"+familias[i].codigo+"'>"+familias[i].nombre+"</option>";
	}
	$("#familia").html(cuerpo);
}