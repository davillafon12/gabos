var _DETALLES_FACTURA = [];

$(window).ready(function(){
    $("#emisor_provincia").change(getCantones);
    $("#emisor_canton").change(getDistritos);
    
    $("#condicion_venta_factura").change(revisarCondicionCredito);

    $("#boton_agregar_detalle").click(agregarDetalle);

    $("#boton_crear_factura").click(crearFactura);
    
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
        precio: precio.toFixed(_CANTIDAD_DECIMALES),
        descuento: descuento.toFixed(_CANTIDAD_DECIMALES),
        tarifaIVA: tarifaIVA.toFixed(_CANTIDAD_DECIMALES),
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

function resetCamposPrincipales(){
    $("#nombre_emisor").val("");
    $("#identificacion_emisor").val("");
    $("#email_emisor").val("");
    $("#otras_sennas_emisor").val("");
    $("#emisor_provincia").val(0);
    $("#codigo_actividad_factura").val("");
    $("#fecha_factura").val("");
    $("#condicion_venta_factura").val(-1);
    $("#tipo_pago_factura").val(-1);
}

function dibujarProductosenTabla(){
    $("#tabla_productos").html("");
    var cuerpo = "";
    for(var index in _DETALLES_FACTURA){
        var detalle = _DETALLES_FACTURA[index];
        var precioSinIVA = detalle.precio / (1 + detalle.tarifaIVA / 100);
        precioSinIVA = precioSinIVA.toFixed(_CANTIDAD_DECIMALES);

        var precioFinalSinDescuentoSinIVA = precioSinIVA * detalle.cantidad;
        precioFinalSinDescuentoSinIVA = precioFinalSinDescuentoSinIVA.toFixed(_CANTIDAD_DECIMALES);

        var precioFinalConDescuentoSinIVA = precioFinalSinDescuentoSinIVA - (precioFinalSinDescuentoSinIVA * (detalle.descuento / 100));
        precioFinalConDescuentoSinIVA = precioFinalConDescuentoSinIVA.toFixed(_CANTIDAD_DECIMALES);

        var precioFinalConDescuentoConIVA =+ precioFinalConDescuentoSinIVA + precioFinalConDescuentoSinIVA * (detalle.tarifaIVA / 100);
        precioFinalConDescuentoConIVA = precioFinalConDescuentoConIVA.toFixed(_CANTIDAD_DECIMALES);

        cuerpo +=   "<tr>"
                        +"<td>"+detalle.codigo+"</td>"
                        +"<td>"+detalle.detalle+"</td>"
                        +"<td style='text-align: center;'>"+detalle.cantidad+"</td>"
                        +"<td style='text-align: center;'>"+detalle.descuento+"</td>"
                        +"<td style='text-align: right;'>"+detalle.precio+"</td>"
                        +"<td style='text-align: right;'>"+precioFinalConDescuentoConIVA+"</td>"
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

function crearFactura(){
    var resultado = validarCrearFactura();
    if(resultado){
        $.prompt("¡Esto creará una factura de compra!", {
            title: "¿Esta seguro que desea generar esta factura?",
            buttons: { "Si, estoy seguro": true, "Cancelar": false },
            submit:function(e,v,m,f){
                if(v){
                    $('#envio_factura').bPopup({
						modalClose: false
                    });	
                    doAjax("/contabilidad/facturaElecCompra/crearFactura", "json", true, "POST", resultado, function(data){
                        if(data.status == 1){
                            notyConTipo("Se creó la factura electrónica de compra con éxito", "success");
                            resetCamposDetalle();
                            resetCamposPrincipales();
                            _DETALLES_FACTURA={};
                            dibujarProductosenTabla();
                        }else{
                            notyConTipo(data.msg, "error");
                        }
                    }, function(){
                        notyConTipo("Hubo un error en el servidor al crear la factura de compra, favor contactar al administrador", "error");
                    }, function(){
                        $('#envio_factura').bPopup().close();
                    });					
                }
            }
        });
    }
}

function validarCrearFactura(){
    var nombreEmisor = $("#nombre_emisor").val().trim();
    var tipoIdentificacionEmisor = $("#tipo_identificacion_emisor").val();
    var identificacionEmisor = $("#identificacion_emisor").val().trim().replace(/\-/, "");
    var emailEmisor = $("#email_emisor").val().trim();
    var otrasSennasEmisor = $("#otras_sennas_emisor").val().trim();
    var provinciaEmisor = $("#emisor_provincia").val();
    var cantonEmisor = $("#emisor_canton").val();
    var distritoEmisor = $("#emisor_distrito").val();
    var codigoActividadEmisor = $("#codigo_actividad_factura").val().trim();
    var fechaFactura = $("#fecha_factura").val().trim();
    var condicionVenta = $("#condicion_venta_factura").val();
    var tipoPago = $("#tipo_pago_factura").val();
    var plazoCredito = $("#plazo_factura").val().trim();

    if(nombreEmisor == ""){
        notyConTipo("Debe ingresar el nombre del emisor", "error");
        return false;
    }

    if(identificacionEmisor == ""){
        notyConTipo("Debe ingresar la identificación del emisor", "error");
        return false;
    }

    if(emailEmisor == ""){
        notyConTipo("Debe ingresar el correo electrónico del emisor", "error");
        return false;
    }

    if(!validateEmail(emailEmisor)){
        notyConTipo("Debe ingresar un correo electrónico con formato válido", "error");
        return false;
    }

    if(otrasSennasEmisor == ""){
        notyConTipo("Debe ingresar la dirección del emisor", "error");
        return false;
    }

    if(provinciaEmisor == 0){
        notyConTipo("Debe escoger la provincia del emisor", "error");
        return false;
    }

    if(cantonEmisor == null || cantonEmisor == 0){
        notyConTipo("Debe escoger el cantón del emisor", "error");
        return false;
    }

    if(distritoEmisor == null || distritoEmisor == 0){
        notyConTipo("Debe escoger el distrito del emisor", "error");
        return false;
    }

    if(codigoActividadEmisor == ""){
        notyConTipo("Debe ingresar el código de actividad del emisor", "error");
        return false;
    }

    if(!$.isNumeric(codigoActividadEmisor)){
        notyConTipo("Debe ingresar un código de actividad válido", "error");
        return false;
    }

    if(fechaFactura == ""){
        notyConTipo("Debe ingresar la fecha y hora de la factura", "error");
        return false;
    }

    if(!moment(fechaFactura,"DD-MM-YYYY HH:mm:ss", true).isValid()){
        notyConTipo("Debe ingresar una fecha con formato válido", "error");
        return false;
    }

    if(condicionVenta == -1){
        notyConTipo("Debe ingresar un tipo de condición de venta", "error");
        return false;
    }

    //Si es credito
    if(condicionVenta == "02"){
        if(!$.isNumeric(plazoCredito)){
            notyConTipo("Debe ingresar un plazo de crédito válido", "error");
            return false;
        }

        if(plazoCredito < 1){
            notyConTipo("El plazo de crédito debe ser mayor o igual a un día", "error");
            return false;
        }
    }

    if(tipoPago == -1){
        notyConTipo("Debe ingresar un tipo de pago", "error");
        return false;
    }

    if(_DETALLES_FACTURA.length <= 0){
        notyConTipo("Debe ingresar al menos un artículo o servicio a la factura", "error");
        return false;
    }

    return {
        nombreEmisor: nombreEmisor,
        tipoIdentificacionEmisor: tipoIdentificacionEmisor,
        identificacionEmisor: identificacionEmisor,
        emailEmisor: emailEmisor,
        otrasSennasEmisor: otrasSennasEmisor,
        provinciaEmisor: provinciaEmisor,
        cantonEmisor: cantonEmisor,
        distritoEmisor: distritoEmisor,
        codigoActividadEmisor: codigoActividadEmisor,
        fechaFactura: fechaFactura,
        condicionVenta: condicionVenta,
        plazoCredito: plazoCredito,
        tipoPago: tipoPago,
        detalles: JSON.stringify(_DETALLES_FACTURA)
    };
}

function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
  }