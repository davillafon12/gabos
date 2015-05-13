<?php
	$Ruta_Icono_Home = base_url('application/images/Icons/home.png');
	$Ruta_Icono_Clientes = base_url('application/images/Icons/Clientes.ico');	
	$Ruta_Icono_Facturacion = base_url('application/images/Icons/Facturacion.ico');	
	$Ruta_Base = base_url('');
	$Ruta_Icono_Consultas = base_url('application/images/Icons/Consultas.ico');
	$Ruta_Icono_Contabilidad = base_url('application/images/Icons/Contabilidad.png');
	
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
				<li><a href=".$Ruta_Base."clientes/editar>Edición</a></li>
				<li class='last'><a href=".$Ruta_Base."clientes/autorizaciones/verAutorizaciones>Ver Autorizaciones</a></li>
			</ul>
		</li>		
		<li>
			<img class='icono' src=".$Ruta_Icono_Facturacion." alt='Facturacion' width='22' height='22'>
			<a href='#' class='dir'>Facturaci&oacute;n</a>
			<ul>
				<li class='first_Level_2'><a href=".$Ruta_Base."facturas/nueva>Crear factura</a></li>
				<li><a href=".$Ruta_Base."facturas/caja>Caja</a></li>
				<li class='last' ><a href=".$Ruta_Base."facturas/proforma>Crear proforma</a></li>
			</ul>
		</li>
		<li>
			<img class='icono' src=".$Ruta_Icono_Consultas." alt='Consultas' width='22' height='22'>
			<a href='#' class='dir'>Consultas</a>	
			<ul>
				<li class='first_Level_2'><a href='".$Ruta_Base."consulta/facturas'>Facturas</a></li>	
				<li><a href='".$Ruta_Base."consulta/proformas'>Proformas</a></li>			
				<li><a href='#' class='dir'>Notas</a>
					<ul>
						<li class='first'><a href='".$Ruta_Base."consulta/notasCredito'>Crédito</a></li>
						<li class='last'><a href='".$Ruta_Base."consulta/notasDebito'>Débito</a></li>
					</ul>
				</li>
				<li><a href='#' class='dir'>Cierres</a>
					<ul>
						<li class='first'><a href='".$Ruta_Base."consulta/retiroParcial'>Retiro Parcial</a></li>
						<li class='last'><a href=".$Ruta_Base."consulta/cierreCaja>Cierre de Caja</a></li>
					</ul>
				</li>
				<li><a href='#' class='dir'>Reportes</a>
					<ul>
						<li class='first'><a href='".$Ruta_Base."reportes/reportes/usuarios'>Usuarios</a></li>
						<li><a href='".$Ruta_Base."reportes/reportes/clientes'>Clientes</a></li>
						<li class='last'><a href='".$Ruta_Base."reportes/reportes/articulos'>Articulos</a></li>
					</ul>
				</li>
				<li class='last'><a href='".$Ruta_Base."consulta/recibos'>Recibos de Dinero</a></li>
			</ul>
		</li>
		<li class='last_last'>
			<img class='icono' src=".$Ruta_Icono_Contabilidad." alt='Contabilidad' width='22' height='22'>
			<a href='#' class='dir'>Contabilidad</a>	
			<ul>				
				<li class='first_Level_2'><a href=".$Ruta_Base."contabilidad/recibos>Recibos de Dinero</a></li>
				<li><a href=".$Ruta_Base."contabilidad/notas/notasCredito>Notas Crédito</a></li>				
				<li class='last'><a href='#' class='dir'>Cierres</a>
					<ul>
						<li class='first'><a href='".$Ruta_Base."contabilidad/retiro/parcial'>Retiro Parcial</a></li>
						<li class='last'><a href=".$Ruta_Base."contabilidad/cierre/caja>Cierre de Caja</a></li>
					</ul>
				</li>	
			</ul>
		</li>
	</ul>";
?>