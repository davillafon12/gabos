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
		<title>Depósitos de Recibos por Dinero</title>
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
		<?php include '/../Header/log_out_from_browser_Script.php';?>
		<!--SCRIPT DE JQUERY-->		
		<script src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>" type="text/javascript"></script>
		<!--CSS ESTILO ESPECIFICO PAGINA-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/contabilidad/contabilidad_recibos_deposito.css'); ?>">
		<!--SCRIPT DE NUMERIC-->		
		<script src="<?php echo base_url('application/scripts/jquery.numeric.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE HERRAMIENTAS-->		
		<script src="<?php echo base_url('application/scripts/contabilidad/recibos/herramientas_deposito.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE NOTY-->		
		<script src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE Impromptu-->		
		<script src="<?php echo base_url('application/scripts/jquery-impromptu.js'); ?>" type="text/javascript"></script>
		<!--CSS ESTILO DEL Impromptu-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/jquery-impromptu.css'); ?>">
	</head>
	<body >
		<!--Incluir imagen de cabezera-->
		<?php include '/../Header/Header_Picture.php';?>
		
		<!--Incluir menu principal-->
		<?php include '/../Header/selector_menu.php';?>

		<!--Incluir informacion log in-->
		<?php include '/../Header/Log_In_Information.php';?>		
	
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Depósitos de Recibos por Dinero</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
			<div class="contenedor">
				<table>
					<tr>
						<td>
							<p class="contact"><label for="recibo">Número de Recibo:</label></p>
						</td>
						<td>
							<input id="recibo" class="input_uno" placeholder="Inserte el numero de recibo" autocomplete="off" type="text" >
						</td>
					</tr>
					<tr>
						<td>
							<p class="contact"><label for="banco">Banco:</label></p>
						</td>
						<td>
							<select class="input_uno" id="banco">
								<?php
									foreach($bancos as $banco){
										echo "<option value='".$banco->Banco_Codigo."'>".$banco->Banco_Nombre."</option>";
									}						
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<p class="contact"><label for="deposito">Número de Depósito:</label></p>
						</td>
						<td>
							<input id="deposito" class="input_uno" placeholder="Inserte el numero de depósito" autocomplete="off" type="text" >
						</td>
					</tr>
				</table>
				<div class="footer_recibos">					
					<button class="boton_envio_deposito" id="boton_envio_deposito" onclick="crearDeposito()">Confirmar Depósito</button>
				</div>				
			</div>
			
		</div>
		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>