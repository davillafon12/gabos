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
		<title>Consulta de Ventas</title>
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
		<!--CSS ESTILO ESPECIFICO DE LA PAG-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/contabilidad/contabilidad_consulta_ventas.css'); ?>">
		<!--SCRIPT DE HERRAMIENTAS-->		
		<script src="<?php echo base_url('application/scripts/contabilidad/consulta_venta_tools.js'); ?>" type="text/javascript"></script>
	</head>
	<body>
		<!--Incluir imagen de cabezera-->
		<?php include PATH_HEADER_PICTURE;?>
		
		<!--Incluir menu principal-->
		<?php include PATH_HEADER_SELECTOR_MENU;?>

		<!--Incluir informacion log in-->
		<?php include PATH_HEADER_LOG_IN_INFO;?>		
	
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Consulta de Ventas</p>
			<hr class="division_wrapper">
			
			<div class="contenedor" >
				<p class="contact">Sucursal:</p>
				<select class="input_dos" onchange="cargarInfoSucursal()" id="sucursal">
					<option value="-1">Seleccione una sucursal</option>
					<?php						
						foreach($Familia_Empresas as $Nombre_Empresa => $codigo_empresa)
						{
							echo "<option value='".$codigo_empresa."'";
							echo">".$codigo_empresa." - ".$Nombre_Empresa."</option>";											
						}
					?>
				</select>
				<hr>
				<p class="contact datos">Primera Factura:</p>
				<p class="contact datos" id="primera_factura">0</p>
				<p class="contact datos">Última Factura:</p>
				<p class="contact datos" id="ultima_factura">0</p>
				<p class="contact datos">Desde:</p>
				<p class="contact datos" id="hora_cierre">0</p>
				<p class="contact datos">Hasta:</p>
				<p class="contact datos" id="hora">0</p>
				<p class="contact datos">Total Venta:</p>
				<p class="contact datos" style="display: inline;">₡</p><p class="contact datos" style="display: inline;" id="total">0</p>
			</div><!-- contenedor -->			
		</div><!-- main_wrapper -->
		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>