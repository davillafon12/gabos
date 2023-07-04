
var _CANTIDAD_DECIMALES = 2;
var _TOTAL_RETIROS_PARCIALES = 0;
var _TOTAL_DATAFONOS = 0;
var _TOTAL_NOTAS_CREDITO_TARJETA = 0;
var _TOTAL_PAGO_MIXTO_EFECTIVO = 0;
var _TOTAL_FACTURAS_EFECTIVO = 0;
var _TOTAL_RECIBOS_EFECTIVO = 0;
var _TOTAL_RECIBOS_ABONO= 0;
var _TOTAL_NOTAS_CREDITO_CONTADO = 0;
var _TOTAL_NOTAS_CREDITO_APARTADO = 0;
var _TOTAL_CREDITO = 0;
var _TOTAL_APARTADO = 0;
var _TOTAL_NOTAS_CREDITO = 0;
var _TOTAL_RETENCION_NOTAS_CREDITO = 0;
var _TOTAL_RETENCION = 0;
var _TOTAL_FACTURAS_DEPOSITO = 0;
var _TOTAL_NOTAS_CREDITO_DEPOSITO = 0;

$(document).ready(function(){
    cargarPrimeraYUltimaFactura();
    cargarRetirosParciales();
    cargarPagosDatafonos();
    cargarPagosMixtos();
    cargarRecibosDeDinero();
    cargarTotalFacturasContado();
    cargarTotalCredito();
    cargarTotalNotasCredito();
    cargarResumenTotalesNotasCredito();
    cargarTotaleNotasDebito();
    cargarTotalFacturasDeposito();
    cargarListaVendedores();
    cargarValoresFinales();
});

function f(number){
    f(number, false)
}

function f(number, sign){
    var floatNumber = parseFloat(number);
    var colones = "";
    if(sign){
        colones = "₡";
    }
    return colones + floatNumber.format(_CANTIDAD_DECIMALES, 3, '.', ',');
}

function doAjax(url, method, data, successCallback, errorCallback, beforeSend){
    $.ajax({
		url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+url,
		type: method,	
        dataType: "json",	
		data: data,				
		success: successCallback,
		error: errorCallback,
        beforeSend: beforeSend
	});
}

function actualizarTotales(){
    $("#totalDatafonos").val(_TOTAL_DATAFONOS - _TOTAL_NOTAS_CREDITO_TARJETA);
    $("#totalDatafonosVista").html(f(_TOTAL_DATAFONOS - _TOTAL_NOTAS_CREDITO_TARJETA, true));

    var totalFacturasEfectivoFinal = _TOTAL_FACTURAS_EFECTIVO + _TOTAL_PAGO_MIXTO_EFECTIVO;

    var totalFaltante = _TOTAL_RETIROS_PARCIALES - _TOTAL_RECIBOS_EFECTIVO - _TOTAL_RECIBOS_ABONO + _TOTAL_NOTAS_CREDITO_CONTADO - totalFacturasEfectivoFinal;

    var totalFacturasContado = totalFacturasEfectivoFinal -_TOTAL_NOTAS_CREDITO_CONTADO;

    var totalDatafonos = _TOTAL_DATAFONOS - _TOTAL_NOTAS_CREDITO_TARJETA;

    var totalApartadosFinal = _TOTAL_APARTADO - _TOTAL_NOTAS_CREDITO_APARTADO;

    var totalRetencionFinal = _TOTAL_RETENCION - _TOTAL_RETENCION_NOTAS_CREDITO;

    var totalDeposito = _TOTAL_FACTURAS_DEPOSITO - _TOTAL_NOTAS_CREDITO_DEPOSITO;


    $("#total_facturas_contado_p").html(f(totalFacturasContado, true));
    $("#totalRetirosParciales").val(totalFaltante);
    $("#parrafoTotalRetirosParciales").html(f(totalFaltante, true));
    $("#totalDatafonos").val(totalDatafonos);
    $("#totalDatafonosVista").html(f(totalDatafonos, true));
    $("#total_credito_p").html(f(_TOTAL_CREDITO, true));
    $("#total_apartado_p").html(f(totalApartadosFinal,true));
    $("#total_notas_credito_p").html(f(_TOTAL_NOTAS_CREDITO, true));
    $("#total_general_retencion_p").html(f(totalRetencionFinal, true));
    $("#total_deposito_p").html(f(totalDeposito, true));
}

function cargarPrimeraYUltimaFactura(){
    doAjax(
        "/contabilidad/cierre/getPrimeraYUltimaFactura",
        "GET",
        {fechaHoraActual: _FECHA_ACTUAL, fechaUltimoCierre: _FECHA_ULTIMO_CIERRE},
        function(data){
            if(data.status == "success"){
                $("#campo_primera_factura").html(data.primeraFactura);
                $("#campo_ultima_factura").html(data.ultimaFactura);
            }else{
                notyMsg("Error al cargar consecutivos de facturas, contacte al administrador. ERROR# " + data.error, "error");
            }
        },
        function(){
            notyMsg("Error al cargar primera y última factura, contacte al administrador.", "error");
        }, function(){}
    );
}

function cargarRetirosParciales(){
    doAjax(
        "/contabilidad/cierre/getRetirosParciales",
        "GET",
        {fechaHoraActual: _FECHA_ACTUAL, fechaUltimoCierre: _FECHA_ULTIMO_CIERRE},
        function(data){
            var totalRetirosParciales = 0;
            if(data.status == "success"){
                if(data.retirosParciales == false){
                    var noRetirosParciales = "<tr><td colspan='3'><p class='parrafo'>No hay retiros parciales. . .</p></td></tr>";
                    $("#tabla_retiros_parciales").append(noRetirosParciales);
                }else{
                    var contador = 1;
                    for(var retiroIndex in data.retirosParciales){
                        var retiro = data.retirosParciales[retiroIndex];
                        var retiroHTML = "<tr>" +
											"<td><p class='parrafo'>" + contador + "</p></td>" +
                                            "<td><p class='parrafo'>" + retiro.Fecha_Hora + "</p></td>" +
                                            "<td class='alg-right'><p class='parrafo'>" + f(retiro.Monto, true) + "</p></td>" +
                                        "</tr>";
                        $("#tabla_retiros_parciales").append(retiroHTML);
                        contador++;                    
                    }

                    totalRetirosParciales = data.totalRecibosParciales;
                }
                actualizarTotales();
            }else{
                notyMsg("Error al cargar retiros parciales, contacte al administrador. ERROR# " + data.error, "error");             
            }
            var totalesHtml =   '<tr>' +
                                    '<td colspan="2" class="alg-right borde-arriba"><p class="parrafo">Total:</p></td>' +						
                                    '<td class="alg-right borde-arriba"><p class="parrafo">' + f(totalRetirosParciales, true) + '</p></td>' +
                                '</tr>';
            $("#tabla_retiros_parciales").append(totalesHtml);
            $("#totalRetirosParciales").val(totalRetirosParciales);
            $("#parrafoTotalRetirosParciales").html(f(totalRetirosParciales, true));

            _TOTAL_RETIROS_PARCIALES = totalRetirosParciales;
        },
        function(){
            notyMsg("Error al cargar retiros parciales, contacte al administrador.", "error");
        }, function(){}
    );
}

function cargarPagosDatafonos(){
    doAjax(
        "/contabilidad/cierre/getResumenDatafonos",
        "GET",
        {fechaHoraActual: _FECHA_ACTUAL, fechaUltimoCierre: _FECHA_ULTIMO_CIERRE},
        function(data){
            var totalComision = 0;
            var totalRetencion = 0;
            var totalDatafonos = 0;
            if(data.status == "success"){
                if(data.datafonos.length == 0){
                    var sinDatafonosHTML = "<tr><td colspan='3'><p class='parrafo'>No hay datáfonos registrados. . .</p></td></tr>";
                    $("#tabla_resumen_datafonos").append(sinDatafonosHTML);
                }else{
                    for(var index in data.datafonos){
                        var datafono = data.datafonos[index];

                        totalComision += datafono.Total_Comision;
                        totalRetencion += datafono.Total_Retencion;
                        totalDatafonos += datafono.Total;

                        var datafonoHTML = "<tr>"+
                                                "<td><p class='parrafo' style='font-size: 11px;'>" + datafono.Banco_Codigo + " - " + datafono.Banco_Nombre + "</p></td>"+
                                                "<td class='alg-right'><p class='parrafo'>" + f(datafono.Total_Comision, true) + "</p></td>"+
                                                "<td class='alg-right'><p class='parrafo'>" + f(datafono.Total_Retencion, true) + "</p></td>"+
                                                "<td class='alg-right'><p class='parrafo'>" + f(datafono.Total, true) + "</p></td>"+
                                            "</tr>";
                        $("#tabla_resumen_datafonos").append(datafonoHTML);
                    }
                }
                actualizarTotales();
            }else{
                notyMsg("Error al cargar el resumen de datáfonos, contacte al administrador. ERROR# " + data.error, "error");             
            }
            var datafonosTotalesHTML = '<tr>'+
                                            '<td class="alg-right borde-arriba"><p class="parrafo">Totales:</p></td>'+
                                            '<td class="alg-right borde-arriba"><p class="parrafo">' + f(totalComision, true) + '</p></td>'+
                                            '<td class="alg-right borde-arriba"><p class="parrafo">' + f(totalRetencion, true) + '</p></td>'+                                          
                                            '<td class="alg-right borde-arriba"><p class="parrafo">' + f(totalDatafonos, true) + '</p></td>'+
                                        '</tr>';
            $("#tabla_resumen_datafonos").append(datafonosTotalesHTML);
            _TOTAL_DATAFONOS = totalDatafonos;
        },
        function(){
            notyMsg("Error al cargar el resumen de datáfonos, contacte al administrador.", "error");
        }, function(){}
    );
}

function cargarPagosMixtos(){
    doAjax(
        "/contabilidad/cierre/getResumenPagosMixtos",
        "GET",
        {fechaHoraActual: _FECHA_ACTUAL, fechaUltimoCierre: _FECHA_ULTIMO_CIERRE},
        function(data){
            
            if(data.status == "success"){
                $("#cantidad_facturas_pago_mixto").html(data.cantidadFacturas);
                $("#total_efectivo_pago_mixto").html(f(data.efectivo, true));
                $("#total_tarjetas_pago_mixto").html(f(data.tarjeta, true));
                $("#total_pago_mixto").html(f(data.total,true));

                _TOTAL_PAGO_MIXTO_EFECTIVO = data.efectivo;
                actualizarTotales();
            }else{
                notyMsg("Error al cargar el resumen de pagos mixtos, contacte al administrador. ERROR# " + data.error, "error");             
            }
        },
        function(){
            notyMsg("Error al cargar el resumen de pagos mixtos, contacte al administrador.", "error");
        }, function(){}
    );
}

function cargarRecibosDeDinero(){
    doAjax(
        "/contabilidad/cierre/getResumenRecibosDinero",
        "GET",
        {fechaHoraActual: _FECHA_ACTUAL, fechaUltimoCierre: _FECHA_ULTIMO_CIERRE},
        function(data){
            
            if(data.status == "success"){
                $("#recibos_dinero_efectivo").html(f(data.efectivo, true));
                $("#recibos_dinero_tarjeta").html(f(data.tarjeta, true));
                $("#recibos_dinero_deposito").html(f(data.deposito, true));
                $("#recibos_dinero_abonos").html(f(data.abonos, true));
                $("#recibos_dinero_total").html(f(data.total, true));

                _TOTAL_RECIBOS_EFECTIVO = data.efectivo;
                _TOTAL_RECIBOS_ABONO = data.abonos;

                actualizarTotales();
            }else{
                notyMsg("Error al cargar el resumen de recibos de dinero, contacte al administrador. ERROR# " + data.error, "error");             
            }
        },
        function(){
            notyMsg("Error al cargar el resumen de recibos de dinero, contacte al administrador.", "error");
        }, function(){}
    );
}

function cargarTotalFacturasContado(){
    doAjax(
        "/contabilidad/cierre/getTotalFacturasContado",
        "GET",
        {fechaHoraActual: _FECHA_ACTUAL, fechaUltimoCierre: _FECHA_ULTIMO_CIERRE},
        function(data){
            
            if(data.status == "success"){
                _TOTAL_FACTURAS_EFECTIVO = data.total_facturas_contado;

                actualizarTotales();
            }else{
                notyMsg("Error al cargar el total de facturas de contado, contacte al administrador. ERROR# " + data.error, "error");             
            }
        },
        function(){
            notyMsg("Error al cargar el total de facturas de contado, contacte al administrador.", "error");
        }, function(){}
    );
}

function cargarTotalCredito(){
    doAjax(
        "/contabilidad/cierre/getTotalCreditos",
        "GET",
        {fechaHoraActual: _FECHA_ACTUAL, fechaUltimoCierre: _FECHA_ULTIMO_CIERRE},
        function(data){
            
            if(data.status == "success"){
                _TOTAL_CREDITO = data.totalCredito;
                _TOTAL_APARTADO = data.totalApartado;
                actualizarTotales();
            }else{
                notyMsg("Error al cargar el total de créditos, contacte al administrador. ERROR# " + data.error, "error");             
            }
        },
        function(){
            notyMsg("Error al cargar el total de créditos, contacte al administrador.", "error");
        }, function(){}
    );
}

function cargarTotalNotasCredito(){
    doAjax(
        "/contabilidad/cierre/getTotalNotasCredito",
        "GET",
        {fechaHoraActual: _FECHA_ACTUAL, fechaUltimoCierre: _FECHA_ULTIMO_CIERRE},
        function(data){
            
            if(data.status == "success"){
                _TOTAL_NOTAS_CREDITO = data.total;
                actualizarTotales();
            }else{
                notyMsg("Error al cargar el total de notas crédito, contacte al administrador. ERROR# " + data.error, "error");             
            }
        },
        function(){
            notyMsg("Error al cargar el total de notas crédito, contacte al administrador.", "error");
        }, function(){}
    );
}

function cargarResumenTotalesNotasCredito(){
    doAjax(
        "/contabilidad/cierre/getResumeTotalesNotasCredito",
        "GET",
        {fechaHoraActual: _FECHA_ACTUAL, fechaUltimoCierre: _FECHA_ULTIMO_CIERRE},
        function(data){
            
            if(data.status == "success"){
                $("#total_nota_credito_contado_p").html(f(data.contado, true));
                $("#total_nota_credito_apartado_p").html(f(data.apartado, true));
                $("#total_nota_credito_cheque_p").html(f(data.cheque, true));
                $("#total_nota_credito_credito_p").html(f(data.credito, true));
                $("#total_nota_credito_deposito_p").html(f(data.deposito, true));
                $("#total_nota_credito_mixto_p").html(f(data.mixto, true));
                $("#total_nota_credito_tarjeta_p").html(f(data.tarjeta, true));

                _TOTAL_NOTAS_CREDITO_APARTADO = data.apartado;
                _TOTAL_NOTAS_CREDITO_CONTADO = data.contado;
                _TOTAL_NOTAS_CREDITO_TARJETA = data.tarjeta;
                _TOTAL_NOTAS_CREDITO_DEPOSITO = data.deposito;

                actualizarTotales();
            }else{
                notyMsg("Error al cargar el resumen de totales de notas crédito, contacte al administrador. ERROR# " + data.error, "error");             
            }
        },
        function(){
            notyMsg("Error al cargar el resumen de totales de notas crédito, contacte al administrador.", "error");
        }, function(){}
    );
}

function cargarTotaleNotasDebito(){
    doAjax(
        "/contabilidad/cierre/getTotaleNotasDebito",
        "GET",
        {fechaHoraActual: _FECHA_ACTUAL, fechaUltimoCierre: _FECHA_ULTIMO_CIERRE},
        function(data){
            
            if(data.status == "success"){
                $("#total_notas_debito_p").html(f(data.total, true));

                actualizarTotales();
            }else{
                notyMsg("Error al cargar el total de notas débito, contacte al administrador. ERROR# " + data.error, "error");             
            }
        },
        function(){
            notyMsg("Error al cargar el total de notas débito, contacte al administrador.", "error");
        }, function(){}
    );
}

function cargarTotalFacturasDeposito(){
    doAjax(
        "/contabilidad/cierre/getTotalFacturasDeposito",
        "GET",
        {fechaHoraActual: _FECHA_ACTUAL, fechaUltimoCierre: _FECHA_ULTIMO_CIERRE},
        function(data){
            
            if(data.status == "success"){
                _TOTAL_FACTURAS_DEPOSITO = data.total;

                actualizarTotales();
            }else{
                notyMsg("Error al cargar el total de facturas de depósito, contacte al administrador. ERROR# " + data.error, "error");             
            }
        },
        function(){
            notyMsg("Error al cargar el total de facturas de depósito, contacte al administrador.", "error");
        }, function(){}
    );
}

function cargarListaVendedores(){
    doAjax(
        "/contabilidad/cierre/getListaVendedores",
        "GET",
        {fechaHoraActual: _FECHA_ACTUAL, fechaUltimoCierre: _FECHA_ULTIMO_CIERRE},
        function(data){            
            if(data.status == "success"){
                var contador = 1;
                var vendedoresHTML = "<tr>";
                for(var index in data.vendidoVendedores){
                    var vendedor = data.vendidoVendedores[index][0];
                    
                    if(vendedor.usuario == null){
                        continue;
                    }
                    if(vendedor.usuario.trim() == ""){
                        continue;
                    }

                    vendedoresHTML += "<td style='text-align: left; width: 250px;'><p class='parrafo'>" + vendedor.usuario + "</p></td>" +
                                        "<td class='' style='width: 120px;'><p class='parrafo'>" + f(vendedor.total_vendido, true) + "</p></td>" ;

                    if(contador == 2){
                        contador = 1;
                        vendedoresHTML += "</tr><tr>";
                    }else{
                        contador++;
                    }
                }
                $("#tabla_vendido_por_vendedores").append(vendedoresHTML + "</tr>");

                var totalVendidoHTML = '<tr> <td colspan="4" class="alg-right borde-arriba"></td> </tr> <tr>' +
									        '<td colspan="4" class="alg-right">' +
											    '<label class="parrafo" style="font-size: 20px;">Total vendedores:</label>' +
											    '<label class="parrafo" style="font-size: 20px;">' + f(data.totalVendido, true)+ '</label>' +
									        '</td> </tr>';

                $("#tabla_vendido_por_vendedores").append(totalVendidoHTML);
            }else{
                notyMsg("Error al cargar la lista de vendedores, contacte al administrador. ERROR# " + data.error, "error");             
            }
        },
        function(){
            notyMsg("Error al cargar la lista de vendedores, contacte al administrador.", "error");
        }, function(){}
    );
}

function cargarValoresFinales(){
    doAjax(
        "/contabilidad/cierre/getValoresFinales",
        "GET",
        {fechaHoraActual: _FECHA_ACTUAL, fechaUltimoCierre: _FECHA_ULTIMO_CIERRE},
        function(data){
            
            if(data.status == "success"){
                _TOTAL_RETENCION = data.totalRetencion;

                $("#total_general_facturas_p").html(f(data.totalFacturas, true));
                $("#total_general_iva_p").html(f(data.totalIVA, true));
                actualizarTotales();
            }else{
                notyMsg("Error al cargar los valores finales, contacte al administrador. ERROR# " + data.error, "error");             
            }
        },
        function(){
            notyMsg("Error al cargar los valores finales, contacte al administrador.", "error");
        }, function(){}
    );
}