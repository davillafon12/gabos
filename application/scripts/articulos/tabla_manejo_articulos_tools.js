$(document).ready(function(){
	setTable();
	setTableTemporal();
	setTableBodega();
});

function setTable(){
	$('#tabla_editar').dataTable({
		"processing": true,
		"serverSide": true,
		"ajax": {
			"url": location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/articulos/editar/obtenerArticulosTablaManejo',
			"type": "POST",
			"data": function ( d ) {
					d.sucursal = $("#sucursalListaArticulos").val();
				}
		},		
		'oLanguage': {
				'sUrl': location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/application/scripts/datatables/Spanish.txt'
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
						},
			"aLengthMenu": [[20, 50, 100, 500, 1000], [20, 50, 100, 500, 1000]]
	});
}

function setTableBodega(){
	$('#tabla_editar_bodega').dataTable({
		"processing": true,
		"serverSide": true,
		"ajax": {
			"url": location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/articulos/editar/obtenerArticulosBodegaTablaManejo',
			"type": "POST",
			"data": function ( d ) {
					d.sucursal = $("#sucursalListaArticulosBodega").val();
				}
		},		
		'oLanguage': {
				'sUrl': location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/application/scripts/datatables/Spanish.txt'
			},
		"columns": [ 					   
					   null,    
					   null,
					   null, 
					],
		 "drawCallback": function( settings ) {
							$("#contenido-bodega").css( "display", 'block' );
						}
	});
}

function setTableTemporal(){
	$('#tabla_temporal').dataTable({
		'oLanguage': {
				'sUrl': location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/application/scripts/datatables/Spanish.txt'
			},
		"columns": [ 
					   null,    
					   null,					  
					]
	});
}

