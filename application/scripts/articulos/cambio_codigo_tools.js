var codigoCambiarValido = false; //Variable que controla si se cargo un producto bueno
var codigoAbonarValido = false; //Variable que controla si se cargo un producto valido a abonar

$(function() {
    $("#codigo_abonar").numeric();
	$("#codigo_cambiar").numeric();
	$("#cantidad").numeric();
});

function getArticuloCambiar(){
	codigo = $("#codigo_cambiar").val();
	sucursal = $("#sucursal").val();
	
	if(codigo.trim()===''){
		cleanFieldCambiar();
		$("#status").css('display', 'none');
		codigoCambiarValido = false;
		return false;
	}
	
	$.ajax({
		url : location.protocol+'//'+document.domain+'/articulos/cambio/getArticulo',
		type: "POST",		
		async: false,
		data: {'codigo':codigo, 'sucursal':sucursal},				
		success: function(data, textStatus, jqXHR)
		{
			try{				
				informacion = $.parseJSON('[' + data.trim() + ']');				
				if(informacion[0].status==="error"){
					//manejarErrores(informacion[0].error, fila);
					cleanFieldCambiar();
					$("#status").css('display', 'inline');
					codigoCambiarValido = false;
				}else if(informacion[0].status==="success"){	
					$("#status").css('display', 'none');
					setProducto(informacion[0].articulo);
					codigoCambiarValido = true;
				}
			}catch(e){
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
	
}

function getArticuloAbonar(){
	codigo = $("#codigo_abonar").val();
	sucursal = $("#sucursal").val();
	
	if(codigo.trim()===''){
		cleanFieldAbonar();
		$("#status2").css('display', 'none');
		codigoAbonarValido = false;
		return false;
	}
	
	$.ajax({
		url : location.protocol+'//'+document.domain+'/articulos/cambio/getArticulo',
		type: "POST",		
		async: false,
		data: {'codigo':codigo, 'sucursal':sucursal},				
		success: function(data, textStatus, jqXHR)
		{
			try{				
				informacion = $.parseJSON('[' + data.trim() + ']');				
				if(informacion[0].status==="error"){
					//manejarErrores(informacion[0].error, fila);
					cleanFieldAbonar();
					$("#status2").css('display', 'inline');
					codigoAbonarValido = false;
				}else if(informacion[0].status==="success"){	
					$("#status2").css('display', 'none');
					setProductoAbonar(informacion[0].articulo);
					codigoAbonarValido = true;
				}
			}catch(e){
				notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
			}
		},
		error: function (jqXHR, textStatus, errorThrown)
		{}
	});
	
}

function cleanFieldCambiar(){
	$("#descripcion_cambiar").html('');
	$("#inventario").html('');
}

function cleanFieldAbonar(){
	$("#descripcion_abonar").html('');
	$("#cantidad").val('');
}

function setProducto(articulo){
	$("#descripcion_cambiar").html(articulo.descripcion);
	$("#inventario").html(articulo.inventario);
}

function setProductoAbonar(articulo){
	$("#descripcion_abonar").html(articulo.descripcion);
}

function notyMsg(Mensaje, tipo){
	n = noty({
			   layout: 'topRight',
			   text: Mensaje,
			   type: tipo,
			   timeout: 4000
			});
}