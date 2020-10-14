	//Reset todo los checkboxes
	function resetCheckBox(){
		$('tbody tr td input[type="checkbox"]').each(function(){
            $(this).prop('checked', false);
			});
	}
	
	//Retorna un array con los checkboxes chequeados
	function getCheckedCB()
	{
		var selected = new Array();
        $('tbody tr td input[type="checkbox"]:checked').each(function() {
			   selected.push($(this).val());
		  });
		 return selected;
	}
	
	//Funcion que desactiva todos los cilentes  seleccionados
	function desAllChecked()
	{
		var selected = getCheckedCB();
		var amount_checks = selected.length;
		
		if(amount_checks==0)
        {
			$.prompt("No ha seleccionado ningun cliente");
		}	
        else
        {
			$.prompt("¡Esto deshabilitara todos los clientes seleccionados!", {
				title: "¿Esta seguro que desea desactivar este cliente?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											if(v){deactiveClientes(selected);}
										}
			});
		}		
	}
	
	function actAllChecked()
	{
		var selected = getCheckedCB();
		var amount_checks = selected.length;
		
		if(amount_checks==0)
        {
			$.prompt("No ha seleccionado ningun cliente");
		}	
        else
        {
			$.prompt("¡Esto habilitara a todos los clientes seleccionados!", {
				title: "¿Esta seguro que desea activar el cliente?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											if(v){activeClientes(selected);}
										}
			});
		}		
	}
	
	function goDesactivar(id)
	{
		var selected = new Array();
		selected.push(id);
		$.prompt("¡Esto deshabilitara al cliente!", {
				title: "¿Esta seguro que desea desactivar este cliente?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											if(v){deactiveClientes(selected);}
										}
			});
	}
	
	function goActivar(id)
	{
		var selected = new Array();
		selected.push(id);
		$.prompt("¡Esto habilitara al cliente!", {
				title: "¿Esta seguro que desea activar el cliente?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											if(v){activeClientes(selected);}
										}
			});
	}

/*
	$('#tipo_Cedula').change(function () {
	    swith($(this).val()) {
	    case 'nacional':
	        alert("Es tena"); 
	        break;.....
	    }
	})

	jQuery(function($){

	   $("#cedula").mask("9-9999-9999");

	});

*/
	
	
	
$(document).ready(function(){
	setTable();
});

function setTable(){
	$('#tabla_editar').dataTable({
		"processing": true,
		"serverSide": true,
		"ajax": {
			"url": location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/clientes/editar/obtenerClientesTabla',
			"type": "POST",
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
					   { 'orderable': false }
					],
		 "drawCallback": function( settings ) {
							$("#contenido").css( "display", 'block' );
						},
			"aLengthMenu": [[20, 50, 100, 500, 1000], [20, 50, 100, 500, 1000]]
	});
}
	
	
	