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
			$.prompt("No ha seleccionado ningun usuario");
		}	
        else
        {
			$.prompt("¡Esto deshabilitara todos los usuarios seleccionados!", {
				title: "¿Esta seguro que desea desactivar este usuario?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											if(v){deactiveUsuarios(selected);}
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
			$.prompt("No ha seleccionado ningun Usuario");
		}	
        else
        {
			$.prompt("¡Esto habilitara a todos los usuarios seleccionados!", {
				title: "¿Esta seguro que desea activar el usuario?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											if(v){activeUsuarios(selected);}
										}
			});
		}		
	}
	
	function goDesactivar(id)
	{
		var selected = new Array();
		selected.push(id);
		$.prompt("¡Esto deshabilitara al Usuario!", {
				title: "¿Esta seguro que desea desactivar este usuario?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											if(v){deactiveUsuarios(selected);}
										}
			});
	}
	
	function goActivar(id)
	{
		var selected = new Array();
		selected.push(id);
		$.prompt("¡Esto habilitara al Usuario!", {
				title: "¿Esta seguro que desea activar el Usuario?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											if(v){activeUsuarios(selected);}
										}
			});
	}


	
	
	
	