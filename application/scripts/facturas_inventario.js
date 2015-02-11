function calcularCantidadInventario(viejo, nuevo){
	//alert("Antes: "+viejo+" Despues: "+nuevo);
	viejo = parseInt(viejo);
	nuevo = parseInt(nuevo);
	if(viejo==1&&nuevo==1){return '2,0';}
	if(viejo==-1&&nuevo==1){return '1,1';} //1 es para resta
	if(viejo<nuevo){
		cantidad = nuevo-viejo;
		return '1,'+cantidad;//1 es para resta
	}
	else if(viejo>nuevo){
		cantidad = viejo-nuevo;
		return '2,'+cantidad;//2 es para suma
	}
}



function actualizarCantidadProductoInventario(cantidad, codigo, num_row)
{	
	if(cantidad.trim()==''){
		//alert(num_row);
		cantidad=document.getElementById("cantidad_articulo_"+num_row).value;
		//alert();
	}
	if(codigo=='00'){} //Si es generico no lo valora
	else{
		/*antes = document.getElementById("cantidad_articulo_anterior_"+num_row).value;
		//alert("Antes: "+antes+" Despues: "+cantidad);
		operacion = calcularCantidadInventario(antes,cantidad);
		//alert(operacion);
		if(operacion===undefined){return false;} //Si hay problemas no pase de aqui
		url = '/facturas/nueva/actualizarInventario?codigo='+codigo+'&operacion='+operacion;
		//alert(url);
		flag = getandmakeCall(url);
		flag = flag.trim();
		//alert(flag);
		if(flag==='3'){} //Todo salio bien
		else if(flag==='-1'){ //Operacion no permitida
			n = noty({
					   layout: 'topRight',
					   text: 'La operación de inventario no es permitida',
					   type: 'warning',
					   timeout: 4000
					});
		}
		else if(flag==='-2'){
			actualizarInventarioPorError(codigo, num_row, antes);
			n = noty({
					   layout: 'topRight',
					   text: 'El inventario ha cambiado!!!',
					   type: 'warning',
					   timeout: 4000
					});
		}*/
		antes = document.getElementById("cantidad_articulo_anterior_"+num_row).value;
		operacion = calcularCantidadInventario(antes,cantidad);
		if(operacion===undefined){return false;} //Si hay problemas no pase de aqui
		$.ajax({
			url : location.protocol+'//'+document.domain+'/facturas/nueva/actualizarInventario?codigo='+codigo+'&operacion='+operacion+'&token='+token_factura_temporal,
			type: "GET",
			async: false,		
			success: function(data, textStatus, jqXHR)
			{
				flag = data.trim();
				//alert(flag);
				if(flag==='3'){} //Todo salio bien
				else if(flag==='-1'){ //Operacion no permitida
					n = noty({
							   layout: 'topRight',
							   text: 'La operación de inventario no es permitida',
							   type: 'warning',
							   timeout: 4000
							});
				}
				else if(flag==='-2'){
					actualizarInventarioPorError(codigo, num_row, antes);
					n = noty({
							   layout: 'topRight',
							   text: 'El inventario ha cambiado!!!',
							   type: 'warning',
							   timeout: 4000
							});
				}	
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
		 
			}
		});
	}
}

function actualizarInventarioPorError(codigo, num_row, antes){
	cedula = document.getElementById("cedula").value;
	getArticulo(codigo, '', num_row, cedula);
	document.getElementById("cantidad_articulo_"+num_row).value = antes;
}

function devolverProductos(){
	$.ajax({
			url : location.protocol+'//'+document.domain+'/facturas/nueva/devolverProductos',
			type: "POST",
			async: false,
			data: {'token': token_factura_temporal},
			success: function(data, textStatus, jqXHR)
			{},
			error: function (jqXHR, textStatus, errorThrown)
			{}
		});
}