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
	
	//Funcion que desactiva todas las empresas seleccionadas
	function desAllChecked()
	{
		var selected = getCheckedCB();
		var amount_checks = selected.length;
		
		if(amount_checks==0)
        {
			$.prompt("No ha seleccionado alguna empresa");
		}	
        else
        {
			$.prompt("¡Esto deshabilitara todos los usuarios inscritos en estas empresas!", {
				title: "¿Esta seguro que desea desactivar estas empresas?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											if(v){deactiveEmpresas(selected);}
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
			$.prompt("¡Esto habilitara todos los usuarios inscritos en estas empresas!", {
				title: "¿Esta seguro que desea activar estas empresas?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											if(v){activeEmpresas(selected);}
										}
			});
		}		
	}
	
	function goDesactivar(id)
	{
		var selected = new Array();
		selected.push(id);
		$.prompt("¡Esto deshabilitara todos los usuarios inscritos en esta empresa!", {
				title: "¿Esta seguro que desea desactivar esta empresa?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											if(v){deactiveEmpresas(selected);}
										}
			});
	}
	
	function goActivar(id)
	{
		var selected = new Array();
		selected.push(id);
		$.prompt("¡Esto habilitara todos los usuarios inscritos en esta empresa!", {
				title: "¿Esta seguro que desea activar esta empresa?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											if(v){activeEmpresas(selected);}
										}
			});
	}
	
	
	
	