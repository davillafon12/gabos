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
		<title>Anular Recibos por Dinero</title>
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
		<!--SCRIPT DE EXPIRACION DE LA SESION-->
		<?php include PATH_LOG_OUT_HEADER;?>
		<!--SCRIPT DE JQUERY-->		
		<script src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE NOTY-->		
		<script src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE Impromptu-->		
		<script src="<?php echo base_url('application/scripts/jquery-impromptu.js'); ?>" type="text/javascript"></script>
		<!--CSS ESTILO DEL Impromptu-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/jquery-impromptu.css'); ?>">
		<!--CARGA DEL JQUERYUI-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.js'); ?>"></script>
		<!--CSS ESTILO DEL JQUERYUI-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.css'); ?>">
		<!--CSS ESTILO DE LA PAGINA-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/contabilidad/contabilidad_recibos_anular.css'); ?>">
		<!--AJAX-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/contabilidad/recibos/carga_recibos.js'); ?>"></script>
		<!--ANULAR-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/contabilidad/recibos/anular_recibo.js'); ?>"></script>
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
			<p class="titulo_wrapper">Anular Recibos Por Dinero</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
			<div class="contenedor">
				<table class="tablaInfo">
					<tr>
						<td>
							<p class="contact"><label for="cliente">Cliente</label></p>
						</td>
					</tr>
					<tr>
						<td>
							<p class="contact"><label for="cedula">Cédula:</label></p>
						</td>
						<td>
							<input id="cedula" class="input_uno" placeholder="Inserte el numero de cédula" name="cedula" autocomplete="off" type="text" onkeyup="buscarCedula(event);" required="" tabindex='1'>					
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
				<div class="selectores_facturas">
					<div class="div_posibles_facturas">
						<div class="titulo_cuadro">
							<p class="contact"><label >Recibos por Dinero:</label></p>
						</div>
						<div class="cuerpo_cuadro">
							<table class="tabla_facturas" id="tabla_recibos_dinero">
								<thead>
									<tr class="header">
										<th class="titulo_header_tabla" style="width: 110px;">Consecutivo</th>
										<th class="titulo_header_tabla" style="width: 110px;">Monto</th>
										<th class="titulo_header_tabla" style="width: 110px;">Saldo</th>
										<th class="titulo_header_tabla" style="">Fecha</th>
										<th class="titulo_header_tabla" style=""># Factura</th>
										<th class="titulo_header_tabla" style="">Anular</th>
									</tr>
								</thead>
								<tbody id="tbody_recibos_dinero">
								
								</tbody>
							</table>
						</div>
					</div>					
				</div>				
			</div>
			
		</div>
		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>