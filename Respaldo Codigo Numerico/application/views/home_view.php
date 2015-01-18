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
		<title>Inicio</title>
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
		<?php include 'Header/log_out_from_browser_Script.php';?>
	</head>
	<body >
		<!--Incluir imagen de cabezera-->
		<?php include 'Header/Header_Picture.php';?>
		
		<!--Incluir menu principal-->
		<?php include 'Header/selector_menu.php';?>

		<!--Incluir informacion log in-->
		<?php include 'Header/Log_In_Information.php';?>
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">P&aacute;gina De Inicio</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
				</br>Bienvenido a la p&aacute;gina principal del sistema de facturaci&oacute;n y control de inventario de Garotas Bonitas S.A.</br>
				     Utilice el men&uacute; de arriba para seleccionar alguna tarea de inter&eacute;s.</br></br></p>
				<!--<div id="timeout_show"></div>-->
        </div>		

		<!--Incluir footer-->
		<?php include 'Footer/Default_Footer.php';?>
	</body>
</html>
