$(document).ready(function(){
	setTable();
	setTableTemporal();
});

function setTable(){
	$('#tabla_editar').dataTable({
		"processing": true,
		"serverSide": true,
		"ajax": {
			"url": location.protocol+'//'+document.domain+'/articulos/editar/obtenerArticulosTablaManejo',
			"type": "POST",
			"data": function ( d ) {
					d.sucursal = $("#sucursalListaArticulos").val();
				}
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

function setTableTemporal(){
	$('#tabla_temporal').dataTable({
		'oLanguage': {
				'sUrl': location.protocol+'//'+document.domain+'/application/scripts/datatables/Spanish.txt'
			},
		"columns": [ 
					   null,    
					   null,					  
					]
	});
}

