<?php
    $ruta_imagen = base_url('application/images/scripts/loader.gif');
    $ruta_script = base_url('application/controllers/empresas/registrar/es_codigo_usado');	
	$Ruta_Base = base_url('');
	$ruta_base_imagenes_script = base_url('application/images/scripts');
	
echo 
	"<script type='text/javascript'>
	    function verify_ID(){
			var estatus = document.getElementById('status');
			var input = document.getElementById('codigo');
            var codigo =  input.value;
			if(isNumber(codigo)){
				estatus.innerHTML='<img src=".$ruta_imagen." />' ;
				/*callPage('".$ruta_script."?id='+codigo, estatus);*/
				/*callPage('/../home/logout', estatus);*/
				callPage('".$Ruta_Base."familias/registrar/es_codigo_usado?id='+codigo, estatus);
			}
			else
			{estatus.innerHTML='';}
		}
		
		function isNumber(n) {
			return !isNaN(parseFloat(n)) && isFinite(n);
		}
		
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

		function callPage(url, div){
		    var boton = document.getElementById('submit');
			ajax=AjaxCaller(); 
			ajax.open('GET', url, true); 
			ajax.onreadystatechange=function(){
				if(ajax.readyState==4){
					if(ajax.status==200){
						//div.innerHTML=ajax.responseText;
					    if(ajax.responseText.indexOf('tr') != -1)
						{						
							div.innerHTML = \"<div class='status_2'><img src=".$ruta_base_imagenes_script."/error.gif /><p class='text_status'>¡No esta disponible!</p></div>\";							
							boton.disabled=true;
						}
						else
						{
							div.innerHTML = \"<div class='status_2'><img src=".$ruta_base_imagenes_script."/tick.gif /><p class='text_status'>¡Si esta disponible!</div></p>\";
							boton.disabled=false;
						}
					}
				}
			}
			ajax.send(null);
		}
		
	</script>";

?>