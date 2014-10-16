<?php
	$Ruta_Icono_Home = base_url('application/images/Icons/home.png');
	$Ruta_Icono_Clientes = base_url('application/images/Icons/Clientes.ico');
	$Ruta_Icono_Articulos = base_url('application/images/Icons/Articulos.ico');
	$Ruta_Icono_Facturacion = base_url('application/images/Icons/Facturacion.ico');
	$Ruta_Icono_Contabilidad = base_url('application/images/Icons/Contabilidad.png');
	$Ruta_Icono_Consultas = base_url('application/images/Icons/Consultas.ico');
	$Ruta_Icono_Configuracion = base_url('application/images/Icons/Configuracion.ico');
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
				<li><a href=".$Ruta_Base."clientes/editar>Edición</a></li>
				<li><a href=".$Ruta_Base."clientes/registrar/registro_masivo>Registro Masivo Clientes</a></li>
				<li><a href='#' class='dir'>Otros</a>
					<ul>
						<li class='first'><a href=".$Ruta_Base."clientes/otros>Descuentos y Credito</a></li>
						<li class='last'><a href=".$Ruta_Base."clientes/autorizaciones>Autorizaciones</a></li>
					</ul>
				</li>
				<li class='last'><a href=".$Ruta_Base."clientes/autorizaciones/verAutorizaciones>Ver Autorizaciones</a></li>
			</ul>
		</li>
		<li >
			<img class='icono' src=".$Ruta_Icono_Articulos." alt='Articulos' width='22' height='22'>
			<a href='#' class='dir'>Articulos</a>
			<ul>
				<li class='first_Level_2'><a href=".$Ruta_Base."articulos/registrar>Ingresar</a></li>
				<li><a href=".$Ruta_Base."articulos/editar>Edición</a></li>
				<li><a href=".$Ruta_Base."articulos/registrar/registro_masivo>Registro Masivo</a></li>
				<li><a href='#'>Importación</a></li>
				<li><a href='#'>Exportación</a></li>
				<li class='last'><a href='#'>Consulta</a></li>
			</ul>
		</li>
		<li >
			<img class='icono' src=".$Ruta_Icono_Facturacion." alt='Facturacion' width='22' height='22'>
			<a href='#' class='dir'>Facturaci&oacute;n</a>
			<ul>
				<li class='first_Level_2'><a href=".$Ruta_Base."facturas/nueva>Crear factura</a></li>
				<li><a href=".$Ruta_Base."facturas/proforma>Crear proforma</a></li>
				<li class='last'><a href=".$Ruta_Base."facturas/caja>Caja</a></li>
			</ul>
		</li>
		<li >
			<img class='icono' src=".$Ruta_Icono_Contabilidad." alt='Contabilidad' width='22' height='22'>
			<a href='#' class='dir'>Contabilidad</a>	
			<ul>
				<li class='first_Level_2'><a href=".$Ruta_Base."contabilidad/recibos>Recibos de dinero</a></li>
				<li><a href='#' class='dir'>Notas</a>
					<ul>
						<li class='first'><a href='#'>Débito</a></li>
						<li class='last'><a href=".$Ruta_Base."contabilidad/notas/notasCredito>Crédito</a></li>
					</ul>
				</li>				
				<li><a href='#'>Cambio de código</a></li>
				<li class='last'><a href='#'>Inventario</a></li>
			</ul>
		</li>
		<li>
			<img class='icono' src=".$Ruta_Icono_Consultas." alt='Consultas' width='22' height='22'>
			<a href='#' class='dir'>Consultas</a>	
			<ul>
				<li class='first_Level_2'><a href='#'>Clientes</a></li>
				<li><a href='#' class='dir'>Inventarios</a>
					<ul>
						<li class='first'><a href='#'>Transito</a></li>
						<li><a href='#'>Actual</a></li>
						<li><a href='#'>Defectuoso</a></li>
						<li class='last'><a href='#'>Otras empresas</a></li>
					</ul>
				</li>
				<li><a href='#'>Usuarios</a></li>
				<li><a href='#'>Bitacora de transacciones</a></li>
				<li><a href='#'>Estados de cuenta</a></li>
				<li><a href='#'>Articulos</a></li>
				<li class='last'><a href='#'>Ventas y compras</a></li>
			</ul>
		</li>
		<li class='last_last'>
			<img class='icono' src=".$Ruta_Icono_Configuracion." alt='Configuracion' width='22' height='22'>
			<a href='#' class='dir'>Configuraci&oacute;n</a>
			<ul>
				<li class='first_Level_2'><a href='".$Ruta_Base."configuracion'>General del sistema</a></li>
				<li><a href='#' class='dir'>Usuarios</a>
					<ul>
						<li class='first'><a href=".$Ruta_Base."usuarios/registrar>Registro</a></li>
						<li><a href=".$Ruta_Base."usuarios/editar>Edición</a></li>						
						<li class='last'><a href=".$Ruta_Base."usuarios/bitacora>Bitacora</a></li>
					</ul>
				</li>
				<li ><a class='dir' href='#'>Empresas</a>
					<ul>
						<li class='first'><a href=".$Ruta_Base."empresas/editar>Registro y Edición</a></li>						
						<li class='last'><a href='#'>Manejo de articulos</a></li>
					</ul>
				</li>
				<li >
					<a href=".$Ruta_Base."bancos/editar>Bancos</a>
				</li>
				<li class='last'><a href=".$Ruta_Base."familias/familias>Familias</a></li>
			</ul>
		</li>
	</ul>";
?>