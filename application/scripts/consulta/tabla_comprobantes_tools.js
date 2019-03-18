$(document).ready(function(){
	setTable();
});

function setTable(){
	$('#tabla_editar').dataTable({
		"processing": true,
		"serverSide": true,
		"ajax": {
			"url": location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/consulta/obtenerComprobantesTabla',
			"type": "POST",
                        "data": function ( d ) {
					d.tipodocumento = $("#tipo_documento").val();
				}
		},
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
                            { 'orderable': false }
                         ],
		"drawCallback": function( settings ) {
                        $("#contenido").css( "display", 'block' );
                },
                "pageLength" : 100,
		"aLengthMenu": [[20, 50, 100, 500, 1000], [20, 50, 100, 500, 1000]],
                "order": [ 3, 'desc' ]
	});
}

function reenviarXML(clave){
    $.ajax({
        url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/consulta/reenviarDocumento',
        type: "POST",
        dataType: "json",
        data: {clave:clave, tipo:$("#tipo_documento").val()},	
        beforeSend: function(jqXHR, settings) {
            $('#envio_hacienda').bPopup({
                    modalClose: false
            });
        },
        success: function(data, textStatus, jqXHR){
            var documentoSeleccionado = $("#tipo_documento").val();
            window.location.replace(location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/consulta/comprobantesElectronicos?d='+documentoSeleccionado.toLowerCase());
        },
        error: function (jqXHR, textStatus, errorThrown){

        }
    });
}

function cambiarTipoDocumento(){
    $("#tabla_editar").dataTable().fnDraw();
}

function reenviarCorreo(clave, tipo){
    $("#tipo_documento").val(tipo);
    $.ajax({
        url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/consulta/reenviarCorreo',
        type: "POST",
        dataType: "json",
        data: {clave:clave, tipo:tipo},	
        success: function(data, textStatus, jqXHR){
            var documentoSeleccionado = $("#tipo_documento").val();
            window.location.replace(location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/consulta/comprobantesElectronicos?d='+documentoSeleccionado.toLowerCase());
        },
        error: function (jqXHR, textStatus, errorThrown){

        }
    });
}
