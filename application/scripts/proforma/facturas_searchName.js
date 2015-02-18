function setUpLiveSearch(){	
	$( "#nombre" ).autocomplete({
		  source: location.protocol+'//'+document.domain+'/facturas/nueva/getNombresClientesBusqueda',
		  minLength: 1,
		  select: function( event, ui ) {
			document.getElementById("cedula").value=ui.item.id;
			//$("#nombre").val('');
			var evt = document.createEvent("KeyboardEvent");
			buscarCedula(evt); 
		   // alert("Selected: " + ui.item.id + " aka " + ui.item.value);
		    doTabCodigoArticulo("codigo_articulo_1");
		  
		  }
		});
}

