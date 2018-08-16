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
		"aLengthMenu": [[20, 50, 100, 500, 1000], [20, 50, 100, 500, 1000]],
                "order": [ 3, 'asc' ]
	});
}

function reenviarXML(clave){
    $.ajax({
        url : location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/consulta/reenviarDocumento',
        type: "POST",
        dataType: "json",
        data: {'clave':clave},	
        beforeSend: function(jqXHR, settings) {
            $('#envio_hacienda').bPopup({
                    modalClose: false
            });
        },
        success: function(data, textStatus, jqXHR){
            window.location.reload();
        },
        error: function (jqXHR, textStatus, errorThrown){

        }
    });
}

function cambiarTipoDocumento(){
    $("#tabla_editar").dataTable().fnDraw();
}