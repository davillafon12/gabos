$(window).ready(function(){
    $("#emisor_provincia").change(getCantones);
    $("#emisor_canton").change(getDistritos);
    
    $("#condicion_venta_factura").change(revisarCondicionCredito);
    
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