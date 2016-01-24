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
			$.prompt("No ha seleccionado algun banco");
		}	
        else
        {
			$.prompt("¡Esto eliminará de forma permanente la información!", {
				title: "¿Esta seguro que desea eliminar estos bancos?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											if(v){
												deactiveBancos(selected);
											}
										}
			});
		}		
	}
	
	
	
	function goEliminar(id)
	{
		var selected = new Array();
		selected.push(id);
		$.prompt("¡Esto eliminará de forma permanente la información!", {
				title: "¿Esta seguro que desea eliminar este banco?",
				buttons: { "Si, estoy seguro": true, "Cancelar": false },
				submit:function(e,v,m,f){
											if(v){
												deactiveBancos(selected);
											}
										}
			});
	}
	
	function deactiveBancos(sel_empr_array){
		var bancos = sel_empr_array.join(',');
		actualizarBancos(location.protocol+'//'+document.domain+(location.port ? ':'+location.port: '')+'/bancos/editar/eliminar?array='+sel_empr_array);
	}
	
	function actualizarBancos(url){
		ajax=AjaxCaller(); 
		ajax.open('GET', url, true); 
		ajax.onreadystatechange=function(){
			if(ajax.readyState==4){
				if(ajax.status==200){
					location.reload();
				}
			}
		}
		ajax.send(null);
	}
	
	