$(window).on("ready", function(){
		$( "#cliente_asociado" ).autocomplete({
			  source: location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/facturas/nueva/getNombresClientesBusqueda',
			  minLength: 1,
			  select: function( event, ui ) {
										$("#cliente_liga_id").val(ui.item.id);
							  }
		});
});