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

		function actualizarUsuarios(url){
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
		
		//Desactivamos usuarios AJAX 
		function deactiveUsuarios(sel_empr_array){
			var empresas = sel_empr_array.join(',');
			alert(\"entro a desactivar usuario \");
			actualizarUsuarios('".$Ruta_Base."usuarios/editar/desactivar?array='+sel_empr_array);
		}
		
		//Activamos Usuarios AJAX
		function activeUsuarios(sel_empr_array){
			var empresas = sel_empr_array.join(',');
			alert(\"entro a activar usuario \");
			actualizarUsuarios('".$Ruta_Base."usuarios/editar/activar?array='+sel_empr_array);
		}
		
	</script>";

?>