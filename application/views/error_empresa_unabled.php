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
		<title>Empresa Deshabilitada</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="shortcut icon" href="<?php echo base_url('application/images/header_icon.png'); ?>">
		<!--CSS ESTILO BASICO E IMAGEN HEADER DE LA PAGINA-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/Header/Estilo_Basico.css'); ?>">		
		<!--CSS ESTILO DE LA INFO DE LOG IN-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/Header/Log_Out_Info_Style.css'); ?>">
		<!--CSS ESTILO DEL FOOTER-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/Footer/Default_Style.css'); ?>">
		<!--CSS ESTILO DEL MAIN WRAPPER-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/Main_Wrapper.css'); ?>">
		<style type="text/css">
			.logout {
				margin-right:5px;
			}
		</style>
	</head>
	<body >
		<!--Incluir imagen de cabezera-->
		<?php include 'Header/Header_Picture.php';?>
		<!--Incluir informacion log in-->
		<?php include 'Header/Log_In_Information.php';?>
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Empresa Deshabilitada</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
				</br>La empresa, en la que usted esta inscrito, se encuentra desactivada y no podra realizar ninguna operación.</br>
				     Si considera que es un error, por favor contacte al administrador del sitio.</br></p>
				<!--<div id="timeout_show"></div>-->
        </div>
		<!--Incluir footer-->
		<?php include 'Footer/Default_Footer.php';?>
	</body>
</html>