<?php
    $ruta_imagen = base_url('application/images/scripts/loader.gif');
    $ruta_script = base_url('application/controllers/clientes/registrar/es_Cedula_Utilizada');	
	$Ruta_Base = base_url('');
	$ruta_base_imagenes_script = base_url('application/images/scripts');
	$ruta_imagen_delete = base_url('application/images/Icons');
echo 		
	"<script type='text/javascript'>
		

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