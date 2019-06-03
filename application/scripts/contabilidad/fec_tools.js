var _DETALLES_FACTURA = [];

$(window).ready(function(){
    $("#emisor_provincia").change(getCantones);
    $("#emisor_canton").change(getDistritos);
    
    $("#condicion_venta_factura").change(revisarCondicionCredito);

    $("#boton_agregar_detalle").click(agregarDetalle);
    
    revisarCondicionCredito();
});

function getCantones(e){
    var provincia = $(e.target).val();
    $("#emisor_canton, #emisor_distrito").html("");
    doAjax("/clientes/registrar/getCantones", "json", true, "POST", {provincia:provincia},function(data){
        if(data.status){
            $("#emisor_canton").append("<option value='0'>Seleccionar</option>");
            $.each(data.data, function(index, ca){
                $("#emisor_canton").append("<option value='"+ca.CantonID+"'>"+ca.CantonNombre+"</option>");
            });
        }
    }, function(){}, function(){});
}

function getDistritos(e){
    var canton = $(e.target).val();
    var provincia = $("#emisor_provincia").val();
    $("#emisor_distrito").html("");
    doAjax("/clientes/registrar/getDistritos", "json", true, "POST", {provincia:provincia,canton:canton},function(data){
        if(data.status){
            $("#emisor_distrito").append("<option value='0'>Seleccionar</option>");
            $.each(data.data, function(index, ca){
                $("#emisor_distrito").append("<option value='"+ca.DistritoID+"'>"+capitalizeFirstLetter(ca.DistritoNombre)+"</option>");
            });
        }
    }, function(){}, function(){});
}

function doAjax(url, datatype, async, method, params, doneFunction, errorFunction, alwaysFunction){
    $.ajax({
    url: url,
    dataType: datatype,
    async: async,
    method: method,
    data: params
    })
    .done(doneFunction)
    .error(errorFunction)
    .always(alwaysFunction);
}

function capitalizeFirstLetter(string){
  return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

var n = null;
function notyConTipo(Mensaje, tipo){
    n = noty({
        layout: 'topRight',
        text: Mensaje,
        type: tipo,
        timeout: 4000
    });
}

function isEmail(email) {
  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}

function revisarCondicionCredito(){
    var value = $("#condicion_venta_factura").val();
    if(value == "02"){
        $("#plazo_factura_row").show();
    }else{
        $("#plazo_factura_row").hide();
        $("#plazo_factura").val("");
    }
}

function agregarDetalle(){
    var validacion = validarDetalle();
    if(validacion){
        _DETALLES_FACTURA.push(validacion);
        resetCamposDetalle();
        dibujarProductosenTabla();
    }
}

function validarDetalle(){
    var codigo = $("#codigo_detalle").val().trim();
    var cantidad = $("#cantidad_detalle").val();
    var detalle = $("#detalle_detalle").val().trim();
    var precio = $("#precio_unitario_detalle").val();
    var descuento = $("#descuento_detalle").val();
    var tarifaIVA = $("#tarifa_iva_detalle").val();
    
    var tipoCodigo = $("#tipo_codigo_detalle").val();
    var unidadMedida = $("#unidad_medida_detalle").val();
    var tipoTarifa = $("#tipo_tarifa_detalle").val();
    var tipoImpuesto = $("#tipo_impuesto_detalle").val();

    if(codigo === ""){
        notyConTipo("Debe ingresar un código válido", "error");
        return false;
    }
        
    
    if(!$.isNumeric(cantidad)){
        notyConTipo("La cantidad no es válida", "error");
        return false;
    }

    cantidad = parseInt(cantidad);
    if(cantidad < 1){
        notyConTipo("La cantidad debe ser mayor a 0", "error");
        return false;
    }

    if(detalle === ""){
        notyConTipo("Debe ingresar una descripción válida", "error");
        return false;
    }

    if(!$.isNumeric(precio)){
        notyConTipo("El precio no es válido", "error");
        return false;
    }

    precio = parseFloat(precio);
    if(precio < 0.00000001){
        notyConTipo("El precio debe ser mayor a 0", "error");
        return false;
    }

    if(!$.isNumeric(descuento)){
        notyConTipo("El descuento no es válido", "error");
        return false;
    }
    
    descuento = parseFloat(descuento);
    if(descuento < 0 || descuento > 100){
        notyConTipo("El descuento debe estar entre 0% y 100%", "error");
        return false;
    }

    if(!$.isNumeric(tarifaIVA)){
        notyConTipo("La tarifa de IVA no es válida", "error");
        return false;
    }

    tarifaIVA = parseFloat(tarifaIVA);
    if(tarifaIVA < 0 || tarifaIVA > 100){
        notyConTipo("La tarifa debe estar entre 0% y 100%", "error");
        return false;
    }

    return {
        codigo: codigo,
        cantidad: cantidad,
        detalle: detalle,
        precio: precio,
        descuento: descuento,
        tarifaIVA: tarifaIVA,
        tipoCodigo: tipoCodigo,
        unidadMedida: unidadMedida,
        tipoTarifa: tipoTarifa,
        tipoImpuesto: tipoImpuesto
    };
}

function resetCamposDetalle(){
    $("#codigo_detalle").val("");
    $("#cantidad_detalle").val("");
    $("#detalle_detalle").val("");
    $("#precio_unitario_detalle").val("");
    $("#descuento_detalle").val(0);
    $("#tarifa_iva_detalle").val("");
}

function dibujarProductosenTabla(){
    $("#tabla_productos").html("");
    var cuerpo = "";
    for(var index in _DETALLES_FACTURA){
        var detalle = _DETALLES_FACTURA[index];
        var precioFinal = detalle.cantidad * detalle.precio;
        precioFinal = precioFinal - (precioFinal * (detalle.descuento / 100));
        cuerpo +=   "<tr>"
                        +"<td>"+detalle.codigo+"</td>"
                        +"<td>"+detalle.detalle+"</td>"
                        +"<td style='text-align: center;'>"+detalle.cantidad+"</td>"
                        +"<td style='text-align: center;'>"+detalle.descuento+"</td>"
                        +"<td style='text-align: right;'>"+detalle.precio+"</td>"
                        +"<td style='text-align: right;'>"+precioFinal+"</td>"
                        +"<td><div class='boton-eliminar-detalle' id-detalle='"+index+"'>x</div></td>"
                   +"</tr>";
    }
    $("#tabla_productos").html(cuerpo);
    $(".boton-eliminar-detalle").click(eliminarDetalle);
}

function eliminarDetalle(event){
    var id = parseInt($(event.target).attr("id-detalle"));
    _DETALLES_FACTURA.splice(id,1);
    dibujarProductosenTabla();
}