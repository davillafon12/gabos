var consecutivoActual = 0; // Guarda el consecutivo de la factura con la que se esta trabajando
var tipo_moneda = 'colones'; //Guarda el tipo de moneda que se esta cargando en la factura
var iva_por = 0; // Guarda el porcentaje del iva
var tipo_cambio = 1; //Guarda el tipo de cambio
var tipo_factura = ''; //Guarda el tipo de factura, ya que si es factura pendiente no la imprime
var sucursal = 0;
var servidorImpresion = 0;
var token = '';

$(function() {
	 $.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        prevText: '<Ant',
        nextText: 'Sig>',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
	$( "#fecha_desde" ).datepicker();
	$( "#fecha_hasta" ).datepicker();
	
	$("#consecutivo").numeric();
        $("#aplicar_consignacion").bind("click", aplicarConsignacion);
});

function llamarConsignaciones(){
	if(validarFechas()){		
		$.ajax({
			url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/contabilidad/consignaciones/getConsignacionesFiltrados',
			type: "POST",	
                        dataType: "JSON",
			data: {'desde':$("#fecha_desde").val(),'hasta':$("#fecha_hasta").val(),'consigna':$("#sucursal_entrega").val(),'recibe':$("#sucursal_recibe").val()},				
			success: function(data, textStatus, jqXHR)
			{
				try{
                                    if(data.status == "success"){
                                        cargarConsignaciones(data.consignaciones);
                                    }else{
                                        notyMsg(data.error, 'error');
                                    }
				}catch(e){
					notyMsg('¡Hubo un error al cargar las consignaciones, contacte al administrador!', 'error');
				}
			},
			error: function (jqXHR, textStatus, errorThrown)
			{}
		});
	}
}

function validarFechas(){
	desde = $("#fecha_desde").val();
	hasta = $("#fecha_hasta").val();
	patt = /^(0?[1-9]|[12][0-9]|3[01])[\/](0?[1-9]|1[012])[\/]\d{4}$/;
	if(desde.trim()!=''){
		//Validamos
		if(!patt.test(desde.trim())){
			notyMsg('La fecha -desde- no tiene un formato válido', 'error');
			return false;
		}
		
		
	}
	if(hasta.trim()!=''){
		//Validamos
		if(!patt.test(hasta.trim())){
			notyMsg('La fecha -hasta- no tiene un formato válido', 'error');
			return false;
		}
		
	}
	
	if(desde.trim()!=''&&hasta.trim()!=''){
		//Pasar de dd/mm/yyyy a yyyy-mm-dd
		desde = desde.split("/");	
		desde = desde[2]+"-"+desde[1]+"-"+desde[0];
		desde = new Date(desde);
		//Pasar de dd/mm/yyyy a yyyy-mm-dd
		hasta = hasta.split("/");	
		hasta = hasta[2]+"-"+hasta[1]+"-"+hasta[0];
		hasta = new Date(hasta);
		
		if(desde>hasta){
			notyMsg('La fecha -desde- debe ser menor a la fecha -hasta-', 'error');
			return false;
		}
		
	}	
	return true;
}

function notyMsg(Mensaje, tipo){
	n = noty({
            layout: 'topRight',
            text: Mensaje,
            type: tipo,
            timeout: 4000
         });
}

function cargarConsignaciones(consignaciones){
    cuerpoTabla = '';
    for(i = 0; i<consignaciones.length; i++){
            cuerpoTabla += "<tr class='bordes_tabla_factura' onclick='seleccionarConsignacion("+consignaciones[i].consecutivo+")'><td class='contact' style='text-align:center;'>"+consignaciones[i].consecutivo+"</td><td class='contact'>"+consignaciones[i].entrega+"</td><td class='contact'>"+consignaciones[i].recibe+"</td><td class='contact' style='text-align:center;'>"+consignaciones[i].fecha+"</td></tr>";
    }
    $("#consignaciones_filtradas").html(cuerpoTabla);
}

function seleccionarConsignacion(consecutivo){
    $("#consecutivo").val(consecutivo);
    cargarConsignacion();
}

function cargarConsignacion(){
	var consecutivo = $("#consecutivo").val();
	if(consecutivo.trim()===''){
		notyMsg('Debe ingresar un consecutivo válido', 'error');
		return false;
	}
	cargarAllConsignacion(consecutivo);
	consecutivoActual = consecutivo; //Asignamos el consecutivo actual para realizar operaciones
}

function cargarAllConsignacion(consecutivo){
	$.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+"/contabilidad/consignaciones/getConsignacion",
		type: "POST",
                dataType: "JSON",
		data: {'consignacion':consecutivo},		
		success: function(data, textStatus, jqXHR)
		{			
                    try{
                        if(data.status == "success"){
                            iva_por = parseFloat(data.consignacion.iva); // Guarda el porcentaje del iva
                            $("#sucursal_entrega").val(data.consignacion.sucursal_entrega);
                            $("#sucursal_recibe").val(data.consignacion.sucursal_recibe);
                            setProductosFactura(data.consignacion.articulos);
                        }else{
                            notyMsg("El servidor no respondió de una manera adecuada, favor contactar al administrador.", "error");
                        }
                    }
                    catch(e){
                            console.log(e);
                            notyMsg("Error al cargar la consignación, contacte al administrador. ERROR E0", "error");
                    }			
		},
		error: function (jqXHR, textStatus, errorThrown)
		{
                    notyMsg("Respuesta inválidad del servidor, contacte al administrador", "error");
                }
	});
} 

function setProductosFactura(productos){	
	
	$("#cuerpo_tabla_articulos").html('');	
	cantidad = productos.length;
	var cantidadTotalDeArticulos = 0;
	for (var i = 0; i < cantidad; i++) 
	{
		/*fila = "<tr>";
		fila += "<td><label class='contact'>"+productos[i].codigo+"</label></td>";
		fila += "<td><div class='contact' id='descripcion_articulo_"+(i+1)+"'>"+productos[i].descripcion+"</div>"
				+"<div class='tooltip_imagen_articulo' id='tooltip_imagen_articulo_"+(i+1)+"'><img src='"+location.protocol+"//"+document.domain+(location.port ? ':'+location.port: '')+"/application/images/articulos/"+productos[i].imagen+"' height='200' width='200'></div></td>";
		fila += "<td style='text-align: center;'><label class='contact' id='cantidad_articulo_"+(i+1)+"'>"+productos[i].cantidad+"</label></td>";	
		cantidadTotalDeArticulos += parseInt(productos[i].cantidad);
		if(productos[i].exento==='1'){fila += "<td><label class='contact'>E</label>";}else{fila += "<td><label class='contact'></label>";};
		fila += "<input id='producto_exento_"+(i+1)+"' type='hidden' value='"+productos[i].exento+"'></td>";
		
		fila += "<td style='text-align: center;'><label class='contact' id='descuento_articulo_"+(i+1)+"'>"+parseFloat(productos[i].descuento).format(_CANTIDAD_DECIMALES, 3, '.', ',')+"</label></td>";
		
		//Traemos decimales
		_CANTIDAD_DECIMALES = parseInt(_CANTIDAD_DECIMALES); //Esta variable se inicializa en la vista!!		
		precioUI = parseFloat(productos[i].precio);
		precioUI = precioUI.format(_CANTIDAD_DECIMALES, 3, '.', ',');
		
		if(tipo_moneda==='dolares'){
			precio = parseFloat(productos[i].precio) / tipo_cambio;
			precioUI =  precio.format(_CANTIDAD_DECIMALES, 3, '.', ',');			
		}
		
		fila += "<td style='text-align: right;'><label class='contact'>"+precioUI+"</label>"
				+"<input id='costo_unidad_articulo_"+(i+1)+"' type='hidden' value='"+productos[i].precio+"'></td>";				
		fila += "<td style='text-align: right;'><label class='contact' id='costo_total_articulo_"+(i+1)+"'></label></td>";
		
		fila += "</tr>";

		$("#cuerpo_tabla_articulos").append(fila);
		
		//agregarTooltip("#descripcion_articulo_"+(i+1));	*/
            var nFila = "<tr>"
                +"<td>"									
                        +"<input id='articulo_"+(i+1)+"' tabindex='"+i+"'  class='input_codigo_articulo' autocomplete='off' name='codigo_articulo' type='text' value='"+productos[i].codigo+"' />				"					
                        +"<input type='hidden' id='exento_articulo_"+(i+1)+"' value='"+productos[i].exento+"' />"
                        +"<input type='hidden' id='retencion_articulo_"+(i+1)+"' value='"+productos[i].retencion+"'/>"
                +"</td>"
                +"<td>"
                        +"<div class='articulo_specs' id='descripcion_articulo_"+(i+1)+"'>"+productos[i].descripcion+"</div>"
                        +"<div class='tooltip_imagen_articulo' id='tooltip_imagen_articulo_"+(i+1)+"'></div>"
                +"</td>"
                +"<td>"
                        +"<input id='cantidad_articulo_"+(i+1)+"' class='cantidad_articulo' autocomplete='off' type='number' min='1' value='"+productos[i].cantidad+"' />"									
                +"</td>"
                +"<td>"
                        +"<div class='articulo_specs' id='bodega_articulo_"+(i+1)+"'>"+productos[i].inventario+"</div>"
                +"</td>"
                +"<td>"
                        +"<div class='articulo_specs' id='descuento_articulo_muestra_"+(i+1)+"'>"+productos[i].descuento+"</div>"
                        +"<input id='descuento_articulo_"+(i+1)+"' type='hidden' value='"+productos[i].descuento+"'/>"
                +"</td>"
                +"<td>"
                        +"<div class='precio_articulo' id='articulo_precio_unidad_muestra_"+(i+1)+"'>"+parseFloat(productos[i].pUnidad).format(_CANTIDAD_DECIMALES, 3, '.', ',')+"</div>"
                        +"<input id='articulo_precio_unidad_"+(i+1)+"' type='hidden' value='"+productos[i].pUnidad+"'/>"
                        +"<input id='articulo_precio_unidad_final_"+(i+1)+"' type='hidden' value='"+productos[i].pFinal+"'/>"
                +"</td>"
                +"<td>"
                        +"<div class='precio_articulo' id='articulo_precio_total_muestra_"+(i+1)+"'></div>"
                        +"<input id='articulo_precio_total_"+(i+1)+"' type='hidden'/>"
                        +"<input id='articulo_precio_total_sin_descuento_"+(i+1)+"' type='hidden'/>"
                +"</td>"
            +"</tr>";
            $("#cuerpo_tabla_articulos").append(nFila);
            $("#articulo_"+(i+1)).select();
            $("#articulo_"+(i+1)).keyup(cargarArticulo);
            $("#cantidad_articulo_"+(i+1)).keyup(validarCantidad);
            $("#cantidad_articulo_"+(i+1)).change(validarCantidad);
            actualizarPrecioTotalFila(i+1);
	}
	$("#cantidad_total_articulos").html(cantidadTotalDeArticulos);
	//setCostos(cantidad);
}

function aplicarConsignacion(){
    cargarArrayArticulos();
    if(validarExistenciaProductosEnTabla()){
        if(validarSucursales()){
            $.prompt("¡Esto dará en consignación los artículos ingresados!", {
                    title: "¿Esta seguro que desea aplicar la consignación?",
                    buttons: { "Si, estoy seguro": true, "Cancelar": false },
                    submit:function(e,v,m,f){
                                    if(v){			
                                            $('#envio_consignacion').bPopup({
                                                    modalClose: false
                                            });											
                                                    var parametros = {
                                                                    sucursalRecibe :  $("#sucursal_recibe").val().trim(),
                                                                    sucursalEntrega:  $("#sucursal_entrega").val().trim(),
                                                                    articulos      :  JSON.stringify(_ARRAY_ARTICULOS),
                                                                    costo 	   :  $("#costo").val(), 
                                                                    iva 	   :  $("#iva").val(),
                                                                    retencion	   :  $("#retencion").val(),
                                                                    total          :  $("#costo_total").val(),
                                                                    porcentaje_iva :	_PORCENTAJE_IVA,
                                                                    consignacion   :  consecutivoActual
                                                    };
                                                    doAjax("/contabilidad/consignaciones/aplicarConsignacion", "POST", true, parametros, "JSON", resultadoAplicarConsignacion, function(){
                                                                    notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
                                                    });
                                    }
                    }
            });
        }
    }
}

function resultadoAplicarConsignacion(data){
	$('#envio_consignacion').bPopup().close();	
		if(data.status === "success"){
				resetAllFields();
				notyMsg('¡Se aplicó la consignación con éxito!', 'success');
				
				//Impresion carta
				window.open(location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/impresion?t='+data.token+'&d=con&n='+data.consignacion+'&s='+data.sucursal+'&i=c','Impresion de Consignación','width='+anchoImpresion+',height='+alturaImpresion+',resizable=no,toolbar=no,location=no,menubar=no');
                                window.location.reload();
				
		}else{
			notyMsg(data.error, 'error');
		}
}

function guardarConsignacion(){
    cargarArrayArticulos();
    if(validarExistenciaProductosEnTabla()){
        if(validarSucursales()){
            $.prompt("¡Esto modificará los artículos ingresados en la consignación!", {
                    title: "¿Esta seguro que desea guardar la consignación?",
                    buttons: { "Si, estoy seguro": true, "Cancelar": false },
                    submit:function(e,v,m,f){
                                    if(v){			
                                            $('#guardado_consignacion').bPopup({
                                                    modalClose: false
                                            });											
                                                    var parametros = {
                                                                    sucursalRecibe :  $("#sucursal_recibe").val().trim(),
                                                                    sucursalEntrega:  $("#sucursal_entrega").val().trim(),
                                                                    articulos      :  JSON.stringify(_ARRAY_ARTICULOS),
                                                                    costo 	   :  $("#costo").val(), 
                                                                    iva 	   :  $("#iva").val(),
                                                                    retencion	   :  $("#retencion").val(),
                                                                    total          :  $("#costo_total").val(),
                                                                    porcentaje_iva :  _PORCENTAJE_IVA,
                                                                    consignacion   :  consecutivoActual
                                                    };
                                                    doAjax("/contabilidad/consignaciones/guardarConsignacion", "POST", true, parametros, "JSON", resultadoGuardarConsignacion, function(){
                                                                    notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
                                                    });
                                    }
                    }
            });
        }
    }
}

function resultadoGuardarConsignacion(data){
	$('#guardado_consignacion').bPopup().close();	
		if(data.status === "success"){
                    notyMsg('¡Se guardo la consignación con éxito!', 'success');
		}else{
			notyMsg(data.error, 'error');
		}
}