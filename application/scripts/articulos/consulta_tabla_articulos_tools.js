$(document).ready(function(){
	setTable();
});

function setTable(){
	$('#tabla_editar').dataTable({
		"processing": true,
		"serverSide": true,
		"ajax": {
			"url": location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/articulos/editar/obtenerArticulosSoloConsulta',
			"type": "POST",
		},
		'oLanguage': {
				'sUrl': location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/application/scripts/datatables/Spanish.txt'
			},
		"columns": [ 
					   null,    
					   null,
					   null,
					   null,
					   { 'orderable': false },
					   { 'orderable': false },
					   { 'orderable': false },
					],
		 "drawCallback": function( settings ) {
							$("#contenido").css( "display", 'block' );
						},
			"aLengthMenu": [[20, 50, 100, 500, 1000], [20, 50, 100, 500, 1000]]
	});
}