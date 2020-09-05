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
		<title>Facturar Consignaciones</title>
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
		<!--CSS ESTILO DEL MAIN WRAPPER-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/contabilidad/contabilidad_consignaciones.css'); ?>">
		<!--SCRIPT DE EXPIRACION DE LA SESION-->
		<?php include PATH_LOG_OUT_HEADER;?>
		<!--CARGA DEL JQUERY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>"></script>
		<!--CARGA DEL JQUERYUI-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.js'); ?>"></script>
		<!--CSS ESTILO DEL JQUERYUI-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.css'); ?>">
		<!--CSS ESTILO AWESOME FONT-->
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		
		<!--CARGA DEL POPUP MODAL-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.bpopup.min.js'); ?>"></script>
		<!--CARGA DEL NOTY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>"></script>
		<!--CSS ESTILO DEL MODAL-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/jquery-impromptu.css'); ?>">
		<!--JQUERY IMPROMPTU-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-impromptu.js'); ?>"></script>
		<!--JQUERY NUMERIC-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.numeric.js'); ?>"></script>
		<!--HERRAMIENTAS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/contabilidad/facturar_consignaciones.js'); ?>"></script>
	
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
			<p class="titulo_wrapper">Facturar Consignaciones</p>
			<hr class="division_wrapper">
			<div class="factura_wrapper" >
				<table class='tabla-info'>
					<tr>
						<td><p class="contact">Sucursal que consigna:</p></td>
						<td><p class="contact">Sucursal que recibe consignación:</p></td>
						<td><p class="contact">Devolver artículos no facturados:</p></td>
					</tr>
					<tr>
						<td>
							<select class="input_uno" id="sucursal_entrega">	
								<option value="-1">Seleccione Sucursal</option>				
								<?php						
									foreach($Familia_Empresas as $Nombre_Empresa => $codigo_empresa)
									{
										echo "<option value='".$codigo_empresa."'";
										echo">".$codigo_empresa." - ".$Nombre_Empresa."</option>";											
									}
								?>
							</select>						
						</td>
						<td>
							<select class="input_uno" id="sucursal_recibe">	
								<option value="-1">Seleccione Sucursal</option>					
								<?php						
									foreach($Familia_Empresas as $Nombre_Empresa => $codigo_empresa)
									{
										echo "<option value='".$codigo_empresa."'";
										echo">".$codigo_empresa." - ".$Nombre_Empresa."</option>";											
									}
								?>
							</select>	
						</td>
						<td>
							<input type="checkbox" id="devolver_consignados_check" />	
						</td>
					</tr>
				</table>
				<table id="tabla_productos" class="tabla_productos" >
						<thead>
							<th style='width:20px;'><input type='checkbox' id='selector_general_articulos'/></th>
							<th class="th_codigo">Código</th>
							<th class="th_descripcion">Descripción</th>
							<th class="th_cantidad">Consignado</th>
							<th class="th_bodega">Cantidad</th>
							<th class="th_bodega">Descuento</th>
							<th class="th_bodega">Exento</th>
							<th class="th_precio">No Retención</th>
							<th class="th_precio">Precio Unidad</th>
							<th class="th_precio">Precio Total</th>
						</thead>
						<tbody id="cuerpo_tabla_articulos">
						</tbody>				
					</table>
					
				
			</div>
			<div class="botones_final">
				<a href="javascript:;" id="crear_factura_consignacion" class="boton_act_all">Crear Factura</a>
			</div>
		</div>	

		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>
