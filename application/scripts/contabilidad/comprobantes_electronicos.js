$(document).ready(function(){
    setTable();
    $("#boton_procesar").click(procesarFacturas);
});

function setTable(){
	$('#tabla_comprobantes').dataTable({
		'oLanguage': {
                            'sUrl': location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/application/scripts/datatables/Spanish.txt'
			},
		"columns": [  
                            null,    
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            { 'orderable': false }
                         ],
		"drawCallback": function( settings ) {
                        $("#contenido").css( "display", 'block' );
                },
		"aLengthMenu": [[20, 50, 100, 500, 1000], [20, 50, 100, 500, 1000]],
                "order": [ 1, 'desc' ]
	});
}

function procesarFacturas(){
    var facturas = [];
    var hayError = false;
    
    $.each($(".selector-mensaje"), function(index, element){
        var clave = $(element).attr("clave-factura");
        var estado = $(element).val();
        
        if(estado === "0"){
            notyMsg('Todos los comprobantes deben marcarse como aceptado, rechazado o parcialmente aceptado', 'error');
            hayError = true;
            return;
        }
        
        
        facturas.push({clave:clave, estado:estado});
    });
    
    if(!hayError){
        $.ajax({
            url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/contabilidad/comprobantes/procesar',
            type: "POST",
            dataType: "json",
            data: {facturas:JSON.stringify(facturas)},	
            beforeSend: function(jqXHR, settings) {
                $('#envio_hacienda').bPopup({
                    modalClose: false
                });
            },
            success: function(data, textStatus, jqXHR){
                if(data.status){
                    // Refrescamos en consulta de comprobantes
                }else{
                    $('#envio_hacienda').bPopup().close(); 
                    notyMsg(data.error, 'error');
                }
            },
            error: function (jqXHR, textStatus, errorThrown){
                $('#envio_hacienda').bPopup().close(); 
                notyMsg('¡La respuesta tiene un formato indebido, contacte al administrador!', 'error');
            }
        }); 
    }
    
}

function notyMsg(Mensaje, tipo){
    n = noty({
        layout: 'topRight',
        text: Mensaje,
        type: tipo,
        timeout: 10000
     });
}