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
		<title>Registrar o Editar Autorizaciones</title>
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
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/clientes/autorizaciones.css'); ?>">		
		<!--SCRIPT DE EXPIRACION DE LA SESION-->
		<?php include '/../Header/log_out_from_browser_Script.php';?>
		
		<!--CARGA DEL JQUERY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>"></script>
		<!--CARGA DEL JQUERYUI-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.js'); ?>"></script>
		<!--CSS ESTILO DEL JQUERYUI-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.css'); ?>">
		<!--CARGA DEL NOTY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>"></script>
		<!--CARGA DE HERRAMIENTAS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/clientes/autorizacionesVer.js'); ?>"></script>
	</head>
	<body >
		<?php
			if(isset($error)){
				echo "<script>
					n = noty({
						   layout: 'topRight',
						   text: '".$error."',
						   type: 'error',
						   timeout: 4000
						});
				</script>";
			}
		?>
	
	
		<!--Incluir imagen de cabezera-->
		<?php include '/../Header/Header_Picture.php';?>
		
		<!--Incluir menu principal-->
		<?php include '/../Header/selector_menu.php';?>

		<!--Incluir informacion log in-->
		<?php include '/../Header/Log_In_Information.php';?>
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Registrar o Editar Autorizaciones</p>
			<hr class="division_wrapper">
			<div  class="contenido">
			
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
				
					<fieldset class="persona-uno">
						<legend>Persona Autorizada #1</legend>
						<table>
							<tr>
								<td>
									<p class="contact"><label for="cedula_persona_uno">Cédula:</label></p>								
								</td>
								<td>
									<input id="cedula_persona_uno" class="input-campos"  autocomplete="off" name="cedula_persona_uno" type="text" disabled>
								</td>
							</tr>
							<tr>
								<td>
									<p class="contact"><label for="nombre_persona_uno">Nombre:</label></p>								
								</td>
								<td>
									<input id="nombre_persona_uno" class="input-campos"  autocomplete="off" name="nombre_persona_uno" type="text" disabled>
								</td>
							</tr>
							<tr>
								<td>
									<p class="contact"><label for="apellido_persona_uno">Apellidos:</label></p>								
								</td>
								<td>
									<input id="apellido_persona_uno" class="input-campos"  autocomplete="off" name="apellido_persona_uno" type="text" disabled>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<p class="contact"><label for="carta_persona_uno">Carta:</label></p>								
									<div id="carta_persona_1" class="carta_imagen">
									</div>
								</td>
							</tr>
						</table>
					</fieldset>
					<fieldset class="persona-dos">
						<legend>Persona Autorizada #2</legend>
						<table>
							<tr>
								<td>
									<p class="contact"><label for="cedula_persona_dos">Cédula:</label></p>								
								</td>
								<td>
									<input id="cedula_persona_dos" class="input-campos"  autocomplete="off" name="cedula_persona_dos" type="text" disabled>
								</td>
							</tr>
							<tr>
								<td>
									<p class="contact"><label for="nombre_persona_dos">Nombre:</label></p>								
								</td>
								<td>
									<input id="nombre_persona_dos" class="input-campos"  autocomplete="off" name="nombre_persona_dos" type="text" disabled>
								</td>
							</tr>
							<tr>
								<td>
									<p class="contact"><label for="apellido_persona_dos">Apellidos:</label></p>								
								</td>
								<td>
									<input id="apellido_persona_dos" class="input-campos"  autocomplete="off" name="apellido_persona_dos" type="text" disabled>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<p class="contact"><label for="carta_persona_dos">Carta:</label></p>								
									<div id="carta_persona_2" class="carta_imagen">
									</div>
								</td>
							</tr>
						</table>
					</fieldset>	
			</div>		
		</div>
		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>
