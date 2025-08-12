<?php
    
	$Ruta_Base = base_url(''); 
	
echo 
	"<script type='text/javascript'>
	    	
		function AjaxCaller(){
			var xmlhttp=false;
			try{
				xmlhttp = new ActiveXObject('Msxml2.XMLHTTP');
			}catch(e){
				try{
					xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
				}catch(E){
					xmlhttp = false;
				}
			}

			if(!xmlhttp && typeof XMLHttpRequest!='undefined'){
				xmlhttp = new XMLHttpRequest();
			}
			return xmlhttp;
		}

		function actualizarEmpresas(url){
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
		
		//Desactivamos familias AJAX
		function deactiveFamilias(sel_empr_array){
			var familias = sel_empr_array.join(',');
			actualizarEmpresas('".$Ruta_Base."familias/familias/desactivar?array='+sel_empr_array);
		}
		
		//Activamos familias AJAX
		function activeFamilias(sel_empr_array){
			var familias = sel_empr_array.join(',');
			actualizarEmpresas('".$Ruta_Base."familias/familias/activar?array='+sel_empr_array);
		}
		
	</script>";

?>