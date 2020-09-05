$(window).ready(function(){
    $("#selector_provincia").change(getCantones);
    $("#selector_canton").change(getDistritos);
    $("#selector_distrito").change(getBarrios);
    $("#telefono").mask("9999-9999");
    $("#fax").mask("9999-9999");
    $("#cod_telefono").mask("999");
    $("#cod_fax").mask("999");
    
    $('#registrar_empresa_form').submit(chequearFormulario);

    $("#is_factura_electronica").click(toggleFacturaElectronica);
});

function getCantones(e){
    var provincia = $(e.target).val();
    $("#selector_canton, #selector_distrito, #selector_barrio").html("");
    doAjax("/clientes/registrar/getCantones", "json", true, "POST", {provincia:provincia},function(data){
        if(data.status){
            $("#selector_canton").append("<option value='0'>Seleccionar</option>");
            $.each(data.data, function(index, ca){
                $("#selector_canton").append("<option value='"+ca.CantonID+"'>"+ca.CantonNombre+"</option>");
            });
        }
    }, function(){}, function(){});
}

function getDistritos(e){
    var canton = $(e.target).val();
    var provincia = $("#selector_provincia").val();
    $("#selector_distrito, #selector_barrio").html("");
    doAjax("/clientes/registrar/getDistritos", "json", true, "POST", {provincia:provincia,canton:canton},function(data){
        if(data.status){
            $("#selector_distrito").append("<option value='0'>Seleccionar</option>");
            $.each(data.data, function(index, ca){
                $("#selector_distrito").append("<option value='"+ca.DistritoID+"'>"+capitalizeFirstLetter(ca.DistritoNombre)+"</option>");
            });
        }
    }, function(){}, function(){});
}

function getBarrios(e){
    var distrito = $(e.target).val();
    var provincia = $("#selector_provincia").val();
    var canton = $("#selector_canton").val();
    $("#selector_barrio").html("");
    doAjax("/clientes/registrar/getBarrios", "json", true, "POST", {provincia:provincia, canton:canton, distrito:distrito},function(data){
        if(data.status){
            $("#selector_barrio").append("<option value='0'>Seleccionar</option>");
            $.each(data.data, function(index, ca){
                $("#selector_barrio").append("<option value='"+ca.BarrioID+"'>"+ca.BarrioNombre+"</option>");
            });
        }
    }, function(){}, function(){});
}

function chequearFormulario(){
    
    if($("#cedula_ju").val().trim() == ""){
        notyConTipo("Cédula no puede ser vacia", "error");
        return false;
    }
    
    if($("#name").val().trim() == ""){
        notyConTipo("Nombre no puede ser vacío", "error");
        return false;
    }
    
    if($("#telefono").val().trim() == ""){
        notyConTipo("Teléfono no puede ser vacío", "error");
        return false;
    }
    
    if(!isEmail($("#email").val().trim())){
        notyConTipo("Email no es válido o es vacío", "error");
        return false;
    }
    
    if($("#selector_provincia").val() == 0){
        notyConTipo("Debe seleccionar una provincia", "error");
        return false;
    }
    
    if($("#selector_canton").val() == 0){
        notyConTipo("Debe seleccionar un cantón", "error");
        return false;
    }
    
    if($("#selector_distrito").val() == 0){
        notyConTipo("Debe seleccionar un distrito", "error");
        return false;
    }
    
    if($("#selector_barrio").val() == 0){
        notyConTipo("Debe seleccionar un barrio", "error");
        return false;
    }
    
    return true;
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

function toggleFacturaElectronica(){
    var isChecked = $("#is_factura_electronica").is(":checked");
    $("#codigo_actividad").prop('disabled', !isChecked);
    $("#user_tributa").prop('disabled', !isChecked);
    $("#pass_tributa").prop('disabled', !isChecked);
    $("#ambiente_tributacion").prop('disabled', !isChecked);
    $("#pin_tributa").prop('disabled', !isChecked);
}