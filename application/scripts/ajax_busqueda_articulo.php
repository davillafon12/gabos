<?php
	$Ruta_Base = base_url('');
	echo "<script>function getArticulo(str, id, num_row, cedula) {
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
			//document.getElementById('nombre').value=xmlhttp.responseText;
			//alert(xmlhttp.responseText);
			setDatosArticulo(xmlhttp.responseText.split(','), id, num_row);
			//return xmlhttp.responseText.split(',');
			//var flag = articuloXML.getElementsByTagName('flag');
	        //alert('Flag = '+flag);
		}
	  }
	  xmlhttp.open('GET','".$Ruta_Base."facturas/nueva/getArticuloXML?codigo='+str+'&cedula='+cedula,true);
	  xmlhttp.send();
	}
	</script>"
?>