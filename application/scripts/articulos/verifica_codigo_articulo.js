function verify_ID(){
	estatus = document.getElementById('status');
	codigo =  $("#articulo_codigo").val();
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
	boton = document.getElementById('submit');
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

function llamarCodigoBarras(){
	input = document.getElementById('articulo_codigo');
	codigo_barras = document.getElementById('cod_Barras');
	codigo =  input.value;
	codigo_barras.innerHTML='<center><img alt=\"12345\" src=\"../application/libraries/barcode.php?codetype=Code25&size=40&text='+codigo+'\"/></center>';
}/application/libraries/barcode.php?codetype=Code25&size=40&text='+codigo+'\"/></center>';
}*/