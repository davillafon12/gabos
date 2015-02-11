$(document).ready(function(){
	setTable();
});

function setTable(){
	$('#tabla_editar').dataTable({
		"processing": true,
		"serverSide": true,
		"ajax": {
			"url": location.protocol+'//'+document.domain+'/articulos/editar/obtenerArticulosTabla',
			"type": "POST",
		},
		'oLanguage': {
				'sUrl': location.protocol+'//'+document.domain+'/application/scripts/datatables/Spanish.txt'
			},
		"columns": [ 
					   { 'orderable': false }, 
					   null,    
					   null,
					   null,
					   null,
					   { 'orderable': false },
					   { 'orderable': false },
					   { 'orderable': false },
					   { 'orderable': false }, 
					],
		 "drawCallback": function( settings ) {
							$("#contenido").css( "display", 'block' );
						}
	});
}