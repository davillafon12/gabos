<?php
    $ruta_imagen = base_url('application/images/scripts/loader.gif');
    $ruta_script = base_url('application/controllers/clientes/registrar/es_Cedula_Utilizada');	
	$Ruta_Base = base_url('');
	$ruta_base_imagenes_script = base_url('application/images/scripts');
	$ruta_imagen_delete = base_url('application/images/Icons');
echo 		
	"<script type='text/javascript'>
		var valor; 
		var identificacion; 
		var opcionIden; 
		function verify_IDI(){
			var input = document.getElementById('cedula').value;
			var estatus = document.getElementById('status');

		}
	    function verify_ID(){
	    	convertirResultado();	
			var estatus = document.getElementById('status');
			var input = document.getElementById('cedula');
            var codigo =  input.value;
            for (var i = 0; i < 13; i++) {
		        var str = codigo; 
		        var res = str.replace(\"_\", \"\");
		        codigo = res;
		    }
		    var tamano = codigo.length; 
			if((valor==1 && tamano ==9) || (valor==2 && tamano == 13) || (valor==3 && tamano ==10)){
				if(!codigo==' '){
					estatus.innerHTML='<img src=".$ruta_imagen." />' ;
					callPage('".$Ruta_Base."clientes/registrar/es_Cedula_Utilizada?id='+codigo, estatus);
				}
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

		/*Metodo encargado de validar cedula y otros digitos numericos		*/
		jQuery(function($){		 
			$(\"#cedula\").mask(\"999999999\");   
		   $(\"#carnet\").mask(\"9999\"); 	
		   $(\"#celular\").mask(\"9999-9999\");
		   $(\"#telefono\").mask(\"9999-9999\");
		   $(\"#descuento\").mask(\"99\");
		   $(\"#cedula_autorizacion\").mask(\"9-9999-9999\"); 
		});


		function convertirResultado(){
			var selectCedula = document.getElementById(\"tipo_Cedula\"); 
			var tipo = selectCedula.options[selectCedula.selectedIndex].text; 
			if(tipo =='Nacional'){
				valor = 1; 
			}else if(tipo=='Residencia'){
				valor = 2; 
			}else if(tipo=='Jurídica'){
				valor = 3; 
			}else if(tipo=='Pasaporte'){
				valor = 4; 
			}else{
				valor = 1;
			}			
		}
		
		function tipoCedula(){	
			convertirResultado();
			var opcion = valor; 
			switch (opcion) {
				case 1:
					$(\"#cedula\").mask(\"999999999\"); 
					break;
				case 2:
					$(\"#cedula\").mask(\"999999999999\"); 
					break;
				case 3:
					$(\"#cedula\").mask(\"9999999999\"); 
					break;
				case 4:
					$(\"#cedula\").unmask(); 
					break;
				default:
					$(\"#cedula\").mask(\"999999999\"); 
					break;
			}
			
		}


		function borrarFila(a)
		{
			var tableBody = document.getElementById(\"myTable\");
			tableBody.deleteRow(a);
		}



		function agregarFila()
		{
			var tableBody = document.getElementById(\"myTable\");
			var posicion = document.getElementById(\"nombre_familia\").options.selectedIndex; //posicion
			var borrado = posicion+1;
			var searchText = document.getElementById(\"nombre_familia\").options[posicion].value.toLowerCase();
			var descuento = document.getElementById(\"descuento_familia\");
			var cellsOfRow=\"\";
			var flag= true;
			var compareWith=\"\";
			if(!descuento.value == ''){
				var i = 1; 
				var text = \"\";
				while( i < tableBody.rows.length)
				{
					compareWith = tableBody.rows[i].cells[0].innerHTML.toLowerCase();
					if (compareWith.indexOf(searchText) == 0){
						flag = false;
						break;
					}
					i++;
				}
				if(flag){
					var row = tableBody.insertRow(-1);
					var cell1 = row.insertCell(0);
					var cell2 = row.insertCell(1);
					cell2.setAttribute( 'colspan', '3');
					var cell3 = row.insertCell(2);
					var cell4 = row.insertCell(3);
					cell1.innerHTML = document.getElementById(\"nombre_familia\").options[posicion].value;
					cell2.innerHTML = document.getElementById(\"nombre_familia\").options[posicion].text;
					cell3.innerHTML = descuento.value;
					cell4.innerHTML = '<a Href=\"javascript:void(0)\" onclick=\"borrarFila('+borrado+');\"> Eliminar </ a>';

				}

				flag =true;
			}

		}

		




		
	</script>";

?>