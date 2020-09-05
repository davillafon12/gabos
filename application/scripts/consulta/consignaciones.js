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
});

/**
 * Number.prototype.format(n, x, s, c)
 * 
 * param integer n: length of decimal
 * param integer x: length of whole part
 * param mixed   s: sections delimiter
 * param mixed   c: decimal delimiter
	12345678.9.format(2, 3, '.', ',');  // "12.345.678,90"
	123456.789.format(4, 4, ' ', ':');  // "12 3456:7890"
	12345678.9.format(0, 3, '-');       // "12-345-679"
 */
 
Number.prototype.format = function(n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};

function llamarConsignaciones(){
	if(validarFechas()){		
		$.ajax({
			url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/consulta/getConsignacionesFiltrados',
			type: "POST",	
                        dataType: "JSON",
			data: {'desde':$("#fecha_desde").val(),'hasta':$("#fecha_hasta").val(),'consigna':$("#sucursal_entrega").val(),'recibe':$("#sucursal_recibe").val(), tipo:tiposSeleccionados()},				
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
		data: {'consignacion':consecutivo,consulta:1},		
		success: function(data, textStatus, jqXHR)
		{			
                    try{
                        if(data.status == "success"){
                            iva_por = parseFloat(data.consignacion.iva); // Guarda el porcentaje del iva
                            $("#sucursal_entrega").val(data.consignacion.sucursal_entrega);
                            $("#sucursal_recibe").val(data.consignacion.sucursal_recibe);
                            $("#costo").val(parseFloat(data.consignacion.costo).format(_CANTIDAD_DECIMALES, 3, '.', ','));
                            $("#iva").val(parseFloat(data.consignacion.iva).format(_CANTIDAD_DECIMALES, 3, '.', ','));
                            $("#retencion").val(parseFloat(data.consignacion.retencion).format(_CANTIDAD_DECIMALES, 3, '.', ','));
                            $("#costo_total").val(parseFloat(data.consignacion.total).format(_CANTIDAD_DECIMALES, 3, '.', ','));
                            setProductosFactura(data.consignacion.articulos);
                            
                            _I_TOKEN = data.token;
                            _I_CONSIGNACION = data.consecutivo;
                            _I_SUCURSAL = data.sucursal;
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
                    +"<div class='articulo_specs' id='bodega_articulo_"+(i+1)+"'>" +productos[i].codigo+	"</div>"
                       
                +"</td>"
                +"<td>"
                        +"<div class='articulo_specs' id='descripcion_articulo_"+(i+1)+"'>"+productos[i].descripcion+"</div>"
                        +"<div class='tooltip_imagen_articulo' id='tooltip_imagen_articulo_"+(i+1)+"'></div>"
                +"</td>"
                +"<td>"
                    +"<div class='articulo_specs' id='bodega_articulo_"+(i+1)+"'>"+productos[i].cantidad+	"</div>"
                        								
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
                +"</td>"
                +"<td>"
                        +"<div class='precio_articulo' id='articulo_precio_total_muestra_"+(i+1)+"'>"+parseFloat(productos[i].pTotal).format(_CANTIDAD_DECIMALES, 3, '.', ',')+"</div>"
                +"</td>"
            +"</tr>";
            $("#cuerpo_tabla_articulos").append(nFila);
           
	}
	$("#cantidad_total_articulos").html(cantidadTotalDeArticulos);
}

function tiposSeleccionados(){
	tipos = [];
	selec = $("input[name='tipo']:checked");
	for(i=0; i<selec.length; i++){
		tipos.push(selec[i].value);
	}
	return tipos.join();
}

var _I_TOKEN = "";
var _I_CONSIGNACION = "";
var _I_SUCURSAL = "";
var anchoImpresion = 1024;
var alturaImpresion = 768;
function imprimir(){
    window.open(location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/impresion?t='+_I_TOKEN+'&d=con&n='+_I_CONSIGNACION+'&s='+_I_SUCURSAL+'&i=c','Impresion de Consignación','width='+anchoImpresion+',height='+alturaImpresion+',resizable=no,toolbar=no,location=no,menubar=no');
}