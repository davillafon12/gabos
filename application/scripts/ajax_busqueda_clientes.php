<?php
	$Ruta_Base = base_url('');
	echo "<script>function getNombreCliente(str) {
	  
	  if (window.XMLHttpRequest) 
	  {
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	  } 
	  else 
	  {  // code for IE6, IE5
		xmlhttp=new ActiveXObject('Microsoft.XMLHTTP');
	  }
	  xmlhttp.onreadystatechange=function() 
	  {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) 
		{
			var nombre_cliente = xmlhttp.responseText;
			if(nombre_cliente.indexOf('Cliente Contad')!=-1)
			{document.getElementById('nombre').disabled=false;}
			else
			{document.getElementById('nombre').disabled=true;}
			document.getElementById('nombre').value=nombre_cliente;
			if(document.getElementById('nombre').value!='No existe cliente!!!')
			{
				enableArticulosInputs();
			}
			else
			{
				disableArticulosInputs();
			}
		}
	  }
	  xmlhttp.open('GET','".$Ruta_Base."facturas/nueva/getNombreCliente?cedula='+str,true);
	  xmlhttp.send();
	}
	</script>"
?>