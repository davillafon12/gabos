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
		<title>Edición De Clientes</title>
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
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/clientes/style_editar.css?v='.$javascript_cache_version); ?>">
		<!--CSS ESTILO DEL MODAL-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/jquery-impromptu.css'); ?>">
		<!--AJAX DE LA TABLA-->
		<?php //include '/../../scripts/cargar_tabla_edicion_clientes.php';?>
		<?php include PATH_DESACTIVAR_CLIENTES_SCRIPT;?>
		<!--CARGA DEL JQUERY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>"></script> 
		<!--CARGA DE HERRAMIENTAS VARIAS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/editar_clientes_tools.js?v='.$javascript_cache_version); ?>"></script>
		<!--CARGA DE MODAL-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-impromptu.js'); ?>"></script>
		<!-- DATA TABES SCRIPT -->
        <script src="<?php echo base_url('/application/scripts/datatables/dataTablesNew.js');?>" type="text/javascript"></script>
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
			<p class="titulo_wrapper">Edición De Clientes</p>
			<hr class="division_wrapper">
		    
			<div id="contenido">
				<? if($verClientesInactivos): ?>
					<button class="mostrar-inactivos-toggle" id="mostrar_inactivos_toggle">Mostrar Inactivos</button>
				<? endif; ?>
				<center><br><br>
					<table id='tabla_editar' class='tablaPrincipal'>
						<thead> 
							<th ></th>
	                        <th class='Sorted_enabled'>
	                            Nombre
	                        </th>
	                        <th class='Sorted_enabled'>
	                            Apellidos
	                        </th>
							<th class='Sorted_enabled'>
	                            Cédula
	                        </th>
	                        <th class='Sorted_enabled'>
	                            Estado Cliente
	                        </th>
							<th >
	                            Opciones
	                        </th>
	                    </thead> 
						<tbody>
						</tbody>
					</table>
				</center>
			</div>

			
        </div>
		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>