<?php
    $ruta_imagen = base_url('application/images/scripts/loader.gif');
    $ruta_script = base_url('application/controllers/clientes/registrar/es_Cedula_Utilizada');	
	$Ruta_Base = base_url('');
	$Ruta_Libreria = base_url('application/libraries/barcode');
	$ruta_base_imagenes_script = base_url('application/images/scripts');
	$ruta_imagen_delete = base_url('application/images/Icons');
echo 		
	"<script type='text/javascript'>

	    function verify_ID(){
			var estatus = document.getElementById('status');
			var input = document.getElementById('articulo_codigo');
            var codigo =  input.value;
			if(!codigo==' '){
				estatus.innerHTML='<img src=".$ruta_imagen." />' ;
				callPage('".$Ruta_Base."articulos/registrar/es_Codigo_Utilizado?id='+codigo, estatus);
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
			llamarCodigoBarras();
		    var boton = document.getElementById('submit');
			ajax=AjaxCaller(); 
			ajax.open('GET', url, true); 
			ajax.onreadystatechange=function(){
				if(ajax.readyState==4){
					if(ajax.status==200){
						//div.innerHTML=ajax.responseText;
					    if(ajax.responseText.indexOf('tr') != -1)
						{						
							div.innerHTML = '<img src=".$ruta_base_imagenes_script."/error.gif />';							
							boton.disabled=true;

						}
						else
						{
							div.innerHTML = '<img src=".$ruta_base_imagenes_script."/tick.gif />';
							boton.disabled=false;
						}
					}
				}
			}
			ajax.send(null);
		}	

		function llamarCodigoBarras(){
			var input = document.getElementById('articulo_codigo');
			var codigo_barras = document.getElementById('cod_Barras');
			var codigo =  input.value;
			codigo_barras.innerHTML='<img alt=\"12345\" src=\"../application/libraries/barcode.php?codetype=Code25&size=40&text='+codigo+'\"/>';
		}

		
		




		
	</script>";

?>