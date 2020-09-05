var _SUCURSAL_SELECCIONADA = -1;

$(window).ready(function(){
    $("#empresa_seleccionada").change(cambiarSucursal);

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

    $("#boton_carga_controles").click(cargarControles);

    const params = new URLSearchParams(window.location.search);
    if(params.has("c")){
        const consecutivo = params.get("c");
        seleccionarControl(consecutivo);
    }
});

function cambiarSucursal(){
    _SUCURSAL_SELECCIONADA = $("#empresa_seleccionada").val();
}

function cargarControles(){
    if(_SUCURSAL_SELECCIONADA == -1){
        notyMsg('Por favor escoja una sucursal', 'error');
        return false;
    }

    $("#facturas_filtradas").html("");

    $.ajax({
        url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/articulos/inventario/getControles',
        type: "POST",
        data: {sucursal:_SUCURSAL_SELECCIONADA, desde: $("#fecha_desde").val(), hasta: $("#fecha_hasta").val()},
        dataType: "json",
        success: function(data, textStatus, jqXHR){
            if(data.code == 0){
                montarControles(data.data.controles);
            }else{
                notyMsg(data.msg, 'error');
            }
        },
        error: function (jqXHR, textStatus, errorThrown){
            console.error(textStatus);
            console.error(errorThrown);
            notyMsg('Hubo un error al cargar la información del servidor', 'error');
        }
    });
}

function notyMsg(Mensaje, tipo){
	n = noty({
			   layout: 'topRight',
			   text: Mensaje,
			   type: tipo,
			   timeout: 4000
			});
}

function montarControles(controles){
	cuerpoTabla = '';
	for(i = 0; i<controles.length; i++){
		cuerpoTabla += "<tr class='bordes_tabla_factura' onclick='seleccionarControl("+controles[i].id+")'><td class='contact' style='text-align:center;'>"+controles[i].id+"</td><td class='contact'>"+controles[i].usuario+"</td><td class='contact' style='text-align:center;'>"+controles[i].fecha+"</td></tr>";
	}
	$("#facturas_filtradas").html(cuerpoTabla);
}

function seleccionarControl(control){
	$("#consecutivo").val(control);
	cargarControl();
}

function cargarControl(){
	consecutivo = $("#consecutivo").val();
	if(consecutivo.trim()===''){
		notyMsg('Debe ingresar un consecutivo válido', 'error');
		return false;
	}

    $.ajax({
        url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/articulos/inventario/getControl',
        type: "POST",
        data: {consecutivo:consecutivo},
        dataType: "json",
        success: function(data, textStatus, jqXHR){
            if(data.code == 0){
                setProductosControl(data.data.articulos);
            }else{
                notyMsg(data.msg, 'error');
            }
        },
        error: function (jqXHR, textStatus, errorThrown){
            console.error(textStatus);
            console.error(errorThrown);
            notyMsg('Hubo un error al cargar la información del servidor', 'error');
        }
    });
}

function setProductosControl(productos){
    var costoBueno = 0;
    var costoDefectuoso = 0;

	$("#contenidoArticulos").html('');
	cantidad = productos.length;
	for (var i = 0; i < cantidad; i++){
        var balanceBueno = productos[i].Fisico_Bueno - productos[i].Sistema_Bueno;
        var balanceDefectuoso = productos[i].Fisico_Defectuoso - productos[i].Sistema_Defectuoso;

        var classBB = "red";
        if(balanceBueno == 0){
            classBB = "green";
        }else if(balanceBueno > 0){
            classBB = "yellow";
        }

        var classBD = "red";
        if(balanceDefectuoso == 0){
            classBD = "green";
        }else if(balanceDefectuoso > 0){
            classBD = "yellow";
        }

		fila = "<tr>";
		fila += "<td><label class='contact'>"+productos[i].Codigo+"</label></td>";
        fila += "<td><div class='contact'>"+productos[i].Descripcion+"</div>";
        if(productos[i].Empatar==='1'){fila += "<td><label class='contact'>E</label>";}else{fila += "<td><label class='contact'></label>";};
        fila += "<td style='text-align: center;'><label class='contact'>"+productos[i].Fisico_Bueno+"</label></td>";
        fila += "<td style='text-align: center;'><label class='contact'>"+productos[i].Sistema_Bueno+"</label></td>";
        fila += "<td style='text-align: center;' class='"+classBB+"'><label class='contact'>"+balanceBueno+"</label></td>";
        fila += "<td style='text-align: center;'><label class='contact'>"+productos[i].Fisico_Defectuoso+"</label></td>";
        fila += "<td style='text-align: center;'><label class='contact'>"+productos[i].Sistema_Defectuoso+"</label></td>";
        fila += "<td style='text-align: center;' class='"+classBD+"'><label class='contact'>"+balanceDefectuoso+"</label></td>";
        fila += "</tr>";

        $("#contenidoArticulos").append(fila);

        costoBueno += parseInt(productos[i].Fisico_Bueno) * parseFloat(productos[i].Costo);
        costoDefectuoso += parseInt(productos[i].Fisico_Defectuoso) * parseFloat(productos[i].Costo);
    }

    var costoTotal = costoBueno + costoDefectuoso;

    $("#costo_bueno").val(costoBueno.format(_CANTIDAD_DECIMALES, 3, '.', ','));
    $("#costo_defectuoso").val(costoDefectuoso.format(_CANTIDAD_DECIMALES, 3, '.', ','));
    $("#costo_total").val(costoTotal.format(_CANTIDAD_DECIMALES, 3, '.', ','));
}

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