var _SUCURSAL_SELECCIONADA = -1;
var _ARTICULOS = {};

$(window).ready(function(){

    $("#empresa_seleccionada").change(cambiarSucursal);
    $("#articulo_a_comparar_id").keyup(validarCodigo);
    $("#articulo_a_comparar_defectuoso").keyup(validarCantidadDefectuosa);
    $("#articulo_a_comparar_bueno").keyup(validarCantidadBuena);
    $("#boton_agregar_articulo").click(obtenerArticulo);
    $("#boton_cargar_inventario").click(cargarTodoInventario);

});

function cambiarSucursal(){
    _SUCURSAL_SELECCIONADA = $("#empresa_seleccionada").val();
    $("#articulo_a_comparar_id").focus();
}

function validarCodigo(e){
    var code = e.key;
    if(code==="Enter"){
        var codigo = $("#articulo_a_comparar_id").val().trim();
        if(codigo !== ""){
            $("#articulo_a_comparar_defectuoso").focus();
        }
    }
}

function validarCantidadDefectuosa(e){
    var code = e.key;
    if(code==="Enter"){
        var cantidad = $("#articulo_a_comparar_defectuoso").val();
        if(!$.isNumeric(cantidad)){
            $("#articulo_a_comparar_defectuoso").val(0);
        }else{
            var parseCantidad = parseInt(cantidad);
            if(parseCantidad < 0){
                $("#articulo_a_comparar_defectuoso").val(0);
            }
        }
        $("#articulo_a_comparar_bueno").focus();
    }
}

function validarCantidadBuena(e){
    var code = e.key;
    if(code==="Enter"){
        var cantidad = $("#articulo_a_comparar_bueno").val();
        if(!$.isNumeric(cantidad)){
            $("#articulo_a_comparar_bueno").val(0);
        }else{
            var parseCantidad = parseInt(cantidad);
            if(parseCantidad < 0){
                $("#articulo_a_comparar_bueno").val(0);
            }
        }
        $("#boton_agregar_articulo").focus();
    }
}

function obtenerArticulo(){
    if(_SUCURSAL_SELECCIONADA == -1){
        notyMsg('Por favor escoja una sucursal', 'error');
        return false;
    }

    var codigo = $("#articulo_a_comparar_id").val().trim();
    if(codigo == ""){
        notyMsg('Ingrese un código válido', 'error');
        return false;
    }

    var defectuoso = parseInt($("#articulo_a_comparar_defectuoso").val());
    if(!$.isNumeric(defectuoso)){
        notyMsg('Ingrese una cantidad defectuosa válida', 'error');
        return false;
    }

    if(defectuoso < 0){
        notyMsg('La cantidad defectuosa debe ser mayor a cero', 'error');
        return false;
    }

    var buena = parseInt($("#articulo_a_comparar_bueno").val());
    if(!$.isNumeric(buena)){
        notyMsg('Ingrese una cantidad buena válida', 'error');
        return false;
    }

    if(buena < 0){
        notyMsg('La cantidad buena debe ser mayor a cero', 'error');
        return false;
    }

    //SI el articulo ya existe en la tabla, no lo debemos cargar de nuevo, solo lo actualizamos
    if(typeof _ARTICULOS[codigo] != "undefined"){
        // Actualizamos
        var art = $.extend(true, {}, _ARTICULOS[codigo]);
        art.fbueno = buena;
        art.fdefectuoso = defectuoso;
        procesarArticulo(art);
        resetControles();
    }else{
        //Creamos
        $.ajax({
            url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/articulos/inventario/getArticulo',
            type: "POST",
            async: true,
            data: {'codigo':codigo, 'sucursal':_SUCURSAL_SELECCIONADA},
            dataType: "json",
            success: function(data, textStatus, jqXHR){
                if(data.code == 0){
                    var art = data.data;
                    art.fbueno = buena;
                    art.fdefectuoso = defectuoso;
                    art.bueno = parseInt(art.bueno);
                    art.defectuoso = parseInt(art.defectuoso);
                    procesarArticulo(art);
                    resetControles();
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
}

function procesarArticulo(articulo){
    if(typeof _ARTICULOS[articulo.codigo] == "undefined"){
        // creamos
        _ARTICULOS[articulo.codigo] = articulo;
        agregarFila(articulo.codigo);
        actualizarColoresFila(articulo.codigo);
    }else{
        // actualizamos
        _ARTICULOS[articulo.codigo].fbueno += parseInt(articulo.fbueno);
        _ARTICULOS[articulo.codigo].fdefectuoso += parseInt(articulo.fdefectuoso);
        actualizarValoresFila(articulo.codigo);
        actualizarColoresFila(articulo.codigo);
    }
}

function resetControles(){
    $("#articulo_a_comparar_id").val("");
    $("#articulo_a_comparar_defectuoso").val("");
    $("#articulo_a_comparar_bueno").val("");
    $("#articulo_a_comparar_id").focus();
}

function notyMsg(Mensaje, tipo){
	n = noty({
			   layout: 'topRight',
			   text: Mensaje,
			   type: tipo,
			   timeout: 4000
			});
}

function agregarFila(codigo){
    if(typeof _ARTICULOS[codigo] != "undefined"){
        var articulo = _ARTICULOS[codigo];
        var balanceBueno = parseInt(articulo.fbueno) - parseInt(articulo.bueno);
        var balanceDefectuoso = parseInt(articulo.fdefectuoso) - parseInt(articulo.defectuoso);

        var html = '<div class="item" style="width: 12%">'+
            articulo.codigo +
        '</div><div class="item"  style="width: 44%;">'+
            articulo.descripcion +
        '</div><div class="item fisico-bueno" style="width: 7%;">'+
            articulo.fbueno +
        '</div><div class="item" style="width: 7%;">'+
            articulo.bueno +
        '</div><div class="item balance-bueno" style="width: 7%;">'+
            balanceBueno +
        '</div><div class="item fisico-defectuoso" style="width: 7%;">'+
            articulo.fdefectuoso +
        '</div><div class="item" style="width: 7%;">'+
            articulo.defectuoso +
        '</div><div class="item balance-defectuoso" style="width: 7%;">'+
            balanceDefectuoso +
        '</div>';
        $("#articulos_container").append("<div class='articulo-fila-html' codigo='"+articulo.codigo+"'>"+html+"</div>");
    }else{
        console.error("No existe articulo al crear fila");
    }
}

function actualizarColoresFila(codigo){
    if(typeof _ARTICULOS[codigo] != "undefined"){
        var articulo = _ARTICULOS[codigo];
        var balanceBueno = parseInt(articulo.fbueno) - parseInt(articulo.bueno);
        var balanceDefectuoso = parseInt(articulo.fdefectuoso) - parseInt(articulo.defectuoso);

        var bbuenoElem = $(".articulo-fila-html[codigo='"+codigo+"']").find(".balance-bueno");
        bbuenoElem.removeClass("warning").removeClass("success").removeClass("error");
        if(balanceBueno < 0){
            bbuenoElem.addClass("error");
        }else if(balanceBueno > 0){
            bbuenoElem.addClass("warning");
        }else{
            bbuenoElem.addClass("success");
        }

        var bdefectuosoElem = $(".articulo-fila-html[codigo='"+codigo+"']").find(".balance-defectuoso");
        bdefectuosoElem.removeClass("warning").removeClass("success").removeClass("error");
        if(balanceDefectuoso < 0){
            bdefectuosoElem.addClass("error");
        }else if(balanceDefectuoso > 0){
            bdefectuosoElem.addClass("warning");
        }else{
            bdefectuosoElem.addClass("success");
        }
    }else{
        console.error("No existe articulo al actualizar colores");
    }
}

function actualizarValoresFila(codigo){
    if(typeof _ARTICULOS[codigo] != "undefined"){
        var articulo = _ARTICULOS[codigo];

        var balanceBueno = parseInt(articulo.fbueno) - parseInt(articulo.bueno);
        var balanceDefectuoso = parseInt(articulo.fdefectuoso) - parseInt(articulo.defectuoso);

        $(".articulo-fila-html[codigo='"+codigo+"']").find(".fisico-bueno").html(articulo.fbueno);
        $(".articulo-fila-html[codigo='"+codigo+"']").find(".fisico-defectuoso").html(articulo.fdefectuoso);
        $(".articulo-fila-html[codigo='"+codigo+"']").find(".balance-bueno").html(balanceBueno);
        $(".articulo-fila-html[codigo='"+codigo+"']").find(".balance-defectuoso").html(balanceDefectuoso);
    }else{
        console.error("No existe articulo al actualizar valores");
    }
}

function cargarTodoInventario(){
    if(_SUCURSAL_SELECCIONADA == -1){
        notyMsg('Por favor escoja una sucursal', 'error');
        return false;
    }

    $.prompt("¡Esto cargará todos los artículos con mas de una unidad en inventario!<br> Esto borrará todos los artículos en la tabla de comparación.", {
        title: "¿Esta seguro que desea cargar todo el inventario?",
        buttons: { "Si, estoy seguro": true, "Cancelar": false },
        submit:function(e,v,m,f){
            if(v){
                limpiarTabla();
                resetControles();
                $.ajax({
                    url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/articulos/inventario/getArticulos',
                    type: "POST",
                    async: true,
                    data: {'sucursal':_SUCURSAL_SELECCIONADA},
                    dataType: "json",
                    success: function(data, textStatus, jqXHR){
                        if(data.code == 0){
                            var arts = data.data;
                            for(var index in arts){
                                procesarArticulo(arts[index]);
                            }
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
        }
    });
}

function limpiarTabla(){
    $(".articulo-fila-html").remove();
    _ARTICULOS = {};
}