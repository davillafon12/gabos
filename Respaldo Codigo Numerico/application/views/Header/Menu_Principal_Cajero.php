<?php
	$Ruta_Icono_Home = base_url('application/images/Icons/home.png');
	$Ruta_Icono_Clientes = base_url('application/images/Icons/Clientes.ico');	
	$Ruta_Icono_Facturacion = base_url('application/images/Icons/Facturacion.ico');	
	$Ruta_Base = base_url('');
	
	echo 
	"<ul id='nav' class='dropdown dropdown-horizontal'>
		<li class='first'>
			<img class='icono' src=".$Ruta_Icono_Home." alt='Clientes' width='25' height='25'>
			<a class='primero' href=".$Ruta_Base."home >Inicio</a>			
		</li>
		<li >
			<img class='icono' src=".$Ruta_Icono_Clientes." alt='Clientes' width='22' height='22'>
			<a href='#' class='dir'>Clientes</a>
			<ul>
				<li class='first_Level_2'><a href=".$Ruta_Base."clientes/registrar>Registrar</a></li> 
				<li class='last'><a href='#'>Consulta</a></li>
			</ul>
		</li>		
		<li class='last_last'>
			<img class='icono' src=".$Ruta_Icono_Facturacion." alt='Facturacion' width='22' height='22'>
			<a href='#' class='dir'>Facturaci&oacute;n</a>
			<ul>				
				<li><a href=".$Ruta_Base."facturas/caja>Caja</a></li>
				<li class='last'><a href='#'>Consulta</a></li>
			</ul>
		</li>		
	</ul>";
?>