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
		<title>Empresas</title>
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
		
		<!--CSS ESTILO DE LA TABLA-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/empresas/style_editar.css'); ?>">
		<!--CSS ESTILO DEL MODAL-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/jquery-impromptu.css'); ?>">
		
		<!--CARGA DEL JQUERY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>"></script> 
		<!--CARGA DE HERRAMIENTAS VARIAS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/editar_empresas_tools.js?v='.$javascript_cache_version); ?>"></script>
		<!--CARGA DE MODAL-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-impromptu.js'); ?>"></script>
		<!--CARGA DEL DATATABLES-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.dataTables.js'); ?>"></script>
                
                <script type="text/javascript" src="<?php echo base_url('application/scripts/empresas/carga_tabla.js?v='.$javascript_cache_version); ?>"></script>
                <script type="text/javascript" src="<?php echo base_url('application/scripts/empresas/desactivar.js?v='.$javascript_cache_version); ?>"></script>
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
			<p class="titulo_wrapper">Empresas</p>
			<hr class="division_wrapper">
		    
			<div id="contenido">
				<center><br><br>
					<img src="<?php echo base_url('application/images/cargando.gif'); ?>" /><br>
					Cargando empresas. . .<br>
					Espere por favor. . .<br><br><br>
				</center>
			</div>

			<script type="text/javascript">
			   getTable();			   
			</script>
			
        </div>
				<!--<div id="timeout_show"></div>-->
       		

		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>