var _CED_AVAILABLE = false;

$(window).ready(function(){
    $("#selector_provincia").change(getCantones);
    $("#selector_canton").change(getDistritos);
    $("#selector_distrito").change(getBarrios);
    
    $("#cedula").mask("999999999"); 	
    $("#celular").mask("9999-9999");
    $("#telefono").mask("9999-9999");
    $("#fax").mask("9999-9999");
    $("#cod_telefono").mask("999");
    $("#cod_celular").mask("999");
    $("#cod_fax").mask("999");
    $("#fecha_nacimiento").mask("99/99/9999");
    
    $('#formulario_registro_cliente').submit(chequearFormulario);
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

var valor; 
var identificacion; 
var opcionIden; 

function verify_IDI(){
    var input = document.getElementById('cedula').value;
    var estatus = document.getElementById('status');
}
    
function verify_ID(){
    convertirResultado();	
    var estatus = document.getElementById('status');
    var input = document.getElementById('cedula');
    var codigo =  input.value;
    if(valor != 4){
        for (var i = 0; i < 13; i++) {
            var str = codigo; 
            var res = str.replace("_", "");
            codigo = res;
        }	
    }            
    var tamano = codigo.length; 
    if((valor==1 && tamano ==9) || (valor==2 && tamano == 12) || (valor==3 && tamano ==10) || (valor ==4)){
        if(!codigo==' '){
                doAjax("/clientes/registrar/es_Cedula_Utilizada?id="+codigo, "text", true, "GET", {},function(data){
                    data = data.trim();
                    _CED_AVAILABLE = false;
                    if(data == "true"){
                        $("#status").html("<div class='status_2'><img src=/application/images/scripts/error.gif /><p class='text_status'>¡No esta disponible!</p></div>");							
                    }else{
                        _CED_AVAILABLE = true;
                        $("#status").html("<div class='status_2'><img src=/application/images/scripts/tick.gif /><p class='text_status'>¡Si esta disponible!</div></p>");
                    }
                }, function(){}, function(){});
        }
    }else{
        estatus.innerHTML='';
    }
}
    
function convertirResultado(){
    var selectCedula = document.getElementById("tipo_Cedula"); 
    var tipo = selectCedula.options[selectCedula.selectedIndex].text; 
    if(tipo =='Nacional'){
            valor = 1; 
    }else if(tipo=='Residencia'){
            valor = 2; 
    }else if(tipo=='Jurídica'){
            valor = 3; 
    }else if(tipo=='Pasaporte'){
            valor = 4; 
    }else{
            valor = 1;
    }			
}
		
function tipoCedula(){	
        convertirResultado();
        var opcion = valor; 
        switch (opcion) {
                case 1:
                        $("#cedula").mask("999999999"); 
                        break;
                case 2:
                        $("#cedula").mask("999999999999"); 
                        break;
                case 3:
                        $("#cedula").mask("9999999999"); 
                        break;
                case 4:
                        $("#cedula").unmask(); 
                        break;
                default:
                        $("#cedula").mask("999999999"); 
                        break;
        }

}

function chequearFormulario(){
    
    if(!_CED_AVAILABLE){
        notyConTipo("Cédula no es válida o no esta disponible", "error");
        return false;
    }
    
    if($("#nombre").val().trim() == ""){
        notyConTipo("Nombre no puede ser vacío", "error");
        return false;
    }
    
    if($("#apellidos").val().trim() == ""){
        notyConTipo("Nombre no puede ser vacío", "error");
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