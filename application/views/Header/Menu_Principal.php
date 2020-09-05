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
						<li><a href=".$Ruta_Base."clientes/autorizaciones>Autorizaciones</a></li>
						<li class='last'><a href=".$Ruta_Base."clientes/otros/cargaBitacoraClientes>Bitácora Clientes</a></li>
					</ul>
				</li>
				<li class='last'><a href=".$Ruta_Base."clientes/autorizaciones/verAutorizaciones>Ver Autorizaciones</a></li>
			</ul>
		</li>
		<li >
			<img class='icono' src=".$Ruta_Icono_Articulos." alt='Articulos' width='22' height='22'>
			<a href='#' class='dir'>Articulos</a>
			<ul>
				<li class='first_Level_2'><a href=".$Ruta_Base."articulos/bodega>Ingreso a Bodega</a></li>
				<li><a href='#' class='dir'>Traspaso</a>
					<ul>
						<li class='first'><a href=".$Ruta_Base."articulos/registrar>Individual</a></li>
						<li ><a href=".$Ruta_Base."articulos/traspaso>Tiendas</a></li>
						<li class='last'><a href=".$Ruta_Base."articulos/registrar/registro_masivo>Masivo</a></li>
					</ul>
				</li>
				<li><a href='#' class='dir'>Registro</a>
					<ul>
						<li class='first'><a href=".$Ruta_Base."articulos/ingresar/individual>Individual</a></li>
						<li class='last'><a href=".$Ruta_Base."articulos/ingresar/masivo>Masivo</a></li>
					</ul>
				</li>
				<li><a href='#' class='dir'>Edición</a>
					<ul>
						<li class='first'><a href=".$Ruta_Base."articulos/editar>Individual</a></li>
						<li ><a href=".$Ruta_Base."articulos/editar/edicionMasivo>Masiva</a></li>
						<li class='last'><a href=".$Ruta_Base."articulos/editar/imagenes>Imágenes</a></li>
					</ul>
				</li>
				<li><a href='#' class='dir'>Inventario</a>
					<ul>
						<li class='first'><a href=".$Ruta_Base."articulos/inventario/control>Control</a></li>
						<li class='last'><a href=".$Ruta_Base."articulos/inventario/consulta>Consulta</a></li>
					</ul>
				</li>
				<li><a href=".$Ruta_Base."articulos/editar/soloConsultaArticulos>Consultar</a></li>
				<li class='last'><a href=".$Ruta_Base."articulos/cambio>Cambio de Código</a></li>
			</ul>
		</li>
		<li >
			<img class='icono' src=".$Ruta_Icono_Facturacion." alt='Facturacion' width='22' height='22'>
			<a href='#' class='dir'>Facturaci&oacute;n</a>
			<ul>
				<li class='first_Level_2'><a href=".$Ruta_Base."facturas/nueva>Crear factura</a></li>
				<li><a href=".$Ruta_Base."facturas/proforma>Crear proforma</a></li>
				<li><a href=".$Ruta_Base."facturas/proforma/fijarProforma>Procesar proforma</a></li>
				<li class='last'><a href=".$Ruta_Base."facturas/caja>Caja</a></li>
			</ul>
		</li>
		<li >
			<img class='icono' src=".$Ruta_Icono_Contabilidad." alt='Contabilidad' width='22' height='22'>
			<a href='#' class='dir'>Contabilidad</a>
			<ul>
				<li class='first_Level_2'><a href=".$Ruta_Base."contabilidad/agregarComprasSucursal>Agregar Compras a Sucursales</a></li>
				<li><a href='#' class='dir'>Recibos de Dinero</a>
					<ul>
						<li class='first'><a href=".$Ruta_Base."contabilidad/recibos>Crear</a></li>
						<li><a href=".$Ruta_Base."contabilidad/anular>Anular</a></li>
						<li class='last'><a href=".$Ruta_Base."contabilidad/deposito>Confirmar Deposito</a></li>
					</ul>
				</li>
				<li><a href='#' class='dir'>Notas</a>
					<ul>
						<li class='first'><a href='".$Ruta_Base."contabilidad/notas/notasDebito'>Débito</a></li>
						<li class='last'><a href=".$Ruta_Base."contabilidad/notas/notasCredito>Crédito</a></li>
					</ul>
				</li>
				<li><a href='#' class='dir'>Cierres</a>
					<ul>
						<li class='first'><a href='".$Ruta_Base."contabilidad/retiro/parcial'>Retiro Parcial</a></li>
						<li class='last'><a href=".$Ruta_Base."contabilidad/cierre/caja>Cierre de Caja</a></li>
					</ul>
				</li>
				<li><a href='#' class='dir'>Consignaciones</a>
					<ul>
						<li class='first'><a href='".$Ruta_Base."contabilidad/consignaciones/crear'>Crear</a></li>
                                                <li><a href='".$Ruta_Base."contabilidad/consignaciones/editar'>Editar</a></li>
						<li class='last'><a href=".$Ruta_Base."contabilidad/consignaciones/facturar>Facturar</a></li>
					</ul>
				</li>
                                <li><a href='".$Ruta_Base."contabilidad/comprobantes'>Aceptar o Rechazar Comprobantes Electrónicos</a></li>
                                <li><a href='".$Ruta_Base."contabilidad/facturaElecCompra'>Crear Factura Electrónica de Compras</a></li>
				<li class='last'><a href='".$Ruta_Base."contabilidad/consultaVenta'>Consulta de Ventas</a></li>
			</ul>
		</li>
		<li>
			<img class='icono' src=".$Ruta_Icono_Consultas." alt='Consultas' width='22' height='22'>
			<a href='#' class='dir'>Consultas</a>
			<ul>
				<li class='first_Level_2'><a href='".$Ruta_Base."consulta/facturas'>Facturas</a></li>
				<li><a href='".$Ruta_Base."consulta/proformas'>Proformas</a></li>
                                <li><a href='".$Ruta_Base."consulta/consignaciones'>Consignaciones</a></li>
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
						<li><a href='".$Ruta_Base."reportes/reportes/facturas'>Facturas</a></li>
						<li class='last'><a href='".$Ruta_Base."reportes/reportes/articulos'>Articulos</a></li>
					</ul>
				</li>
				<li><a href='".$Ruta_Base."consulta/cambiosCodigo'>Cambios de Código</a></li>
                                <li><a href='".$Ruta_Base."consulta/comprobantesElectronicos'>Comprobantes Electrónicos</a></li>
				<li class='last'><a href='".$Ruta_Base."consulta/recibos'>Recibos de Dinero</a></li>
			</ul>
		</li>
		<li class='last_last'>
			<img class='icono' src=".$Ruta_Icono_Configuracion." alt='Configuracion' width='22' height='22'>
			<a href='#' class='dir'>Configuraci&oacute;n</a>
			<ul>
				<li class='first_Level_2'><a href='".$Ruta_Base."config'>General del sistema</a></li>
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
						<li class='last'><a href=".$Ruta_Base."articulos/editar/manejoArticulos>Manejo de articulos</a></li>
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