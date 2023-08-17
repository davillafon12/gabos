<!--
PAGINA PRINCIPAL
DESARROLLADO POR:
-SIVIANY PRENDAS
-DAVID VILLALOBOS
PARA:
-GAROTAS BONITAS S.A.
2014
-->

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Bitácora Clientes</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="shortcut icon" href="<?php echo base_url('application/images/header_icon.png'); ?>">
		<!--CSS ESTILO BASICO E IMAGEN HEADER DE LA PAGINA-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/Header/Estilo_Basico.css'); ?>">		
		<!--CSS ESTILO DEL MENU-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/Header/Menu/Default_Style.css'); ?>">
		<!--CSS ESTILO DE LA INFO DE LOG IN-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/Header/Log_Out_Info_Style.css'); ?>">
		<!--CSS ESTILO DEL FOOTER-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/Footer/Default_Style.css'); ?>">
		<!--CSS ESTILO DEL MAIN WRAPPER-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/Main_Wrapper.css'); ?>">
		<!--CSS ESTILO PROPIO-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/clientes/bitacoraCliente.css'); ?>">		
		<!--CSS ESTILO DEL JQUERYUI-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.css'); ?>">
		<!--SCRIPT DE EXPIRACION DE LA SESION-->
		<!--CSS ESTILO DE LA TABLA-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/usuarios/style_bitacora.css'); ?>">				
		<?php include PATH_LOG_OUT_HEADER;?>					
		<!--CARGA DEL JQUERY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>"></script> 		
		<!--CARGA DEL JQUERYUI-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.js'); ?>"></script>
		<!--CARGA DEL NOTY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>"></script>
		<!--CARGA DE HERRAMIENTAS-->
		<script type="text/javascript" src="<?php echo base_url("application/scripts/clientes/bitacoraClientes.js?v=$javascript_cache_version"); ?>"></script>
		<!-- DATA TABES SCRIPT -->
        <script type="text/javascript" src="<?php echo base_url('/application/scripts/datatables/dataTablesNew.js');?>" type="text/javascript"></script>
	</head>
	<body >
		<!--Incluir imagen de cabezera-->
		<?php include PATH_HEADER_PICTURE;?>
		
		<!--Incluir menu principal-->
		<?php include PATH_HEADER_SELECTOR_MENU;?>

		<!--Incluir informacion log in-->
		<?php include PATH_HEADER_LOG_IN_INFO;?>
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Bitácora Clientes</p>
			<hr class="division_wrapper">
			<div  class="contenidoB">
					<table class="tablaInfo">
						<tr>
							<td>
								<p class="contact"><label for="cedula">Cédula:</label></p>
							</td>
							<td>
								<input id="cedula" class="input_uno" placeholder="Inserte el numero de cédula" name="cliente" type="text" onkeyup="buscarCedula(event);" required="" tabindex='1'>					
								<script>document.getElementById('cedula').focus();</script>
							</td>
						</tr>
						<tr>
							<td>
								<p class="contact"><label for="nombre">Nombre:</label></p>
							</td>
							<td>
								<input id="nombre" class="input_uno" placeholder="Inserte el nombre del cliente" autocomplete="off" name="nombre" type="text" >
								<script>setUpLiveSearch();</script>
							</td>						
						</tr>
					</table>
					<hr class="division-contenido">
						<table id='tabla_editar' class='tablaPrincipal'>
							<thead> 
								<tr>									
									<th class='sorted_enabled'>
										Fecha
									</th>
									<th class='sorted_enabled'>
										Sucursal
									</th>
									<th class='sorted_enabled'>
										Cédula
									</th>
									<th class='sorted_enabled'>
										Usuario
									</th>
									<th class='sorted_enabled'>
										Tipo Transacción
									</th>                                                                                                         
									<th class='sorted_enabled'>
										Descripción
									</th>
								</tr>
							</thead> 
							<tbody>						
							</tbody>
						</table>
						<h3 id="ResultadoError"></h3>
					<hr class="division-contenido">					
			</div>		
		</div>

		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>
