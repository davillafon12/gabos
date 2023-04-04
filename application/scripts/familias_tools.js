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
	
	//Funcion que desactiva todas las familias seleccionadas
	function desAllChecked()
	{
		var selected = getCheckedCB();
		var amount_checks = selected.length;
		
		if(amount_checks==0)
        {
			$.prompt("No ha seleccionado alguna familia");
		}	
        else
        {
			$.prompt("¡Esto deshabilitara todos los articulos inscritos en estas familias!", {
				title: "¿Esta seguro que desea desactivar estas familias?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											if(v){deactiveFamilias(selected);}
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
			$.prompt("No ha seleccionado alguna empresa");
		}	
        else
        {
			$.prompt("¡Esto habilitara todos los articulos inscritos en estas familias!", {
				title: "¿Esta seguro que desea activar estas familias?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											if(v){activeFamilias(selected);}
										}
			});
		}		
	}
	
	function goDesactivar(id)
	{
		var selected = new Array();
		selected.push(id);
		$.prompt("¡Esto deshabilitara todos los articulos inscritos en esta familia!", {
				title: "¿Esta seguro que desea desactivar esta familia?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											if(v){deactiveFamilias(selected);}
										}
			});
	}
	
	function goActivar(id)
	{
		var selected = new Array();
		selected.push(id);
		$.prompt("¡Esto habilitara todos los articulos inscritos en esta familia!", {
				title: "¿Esta seguro que desea activar esta familia?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											if(v){activeFamilias(selected);}
										}
			});
	}