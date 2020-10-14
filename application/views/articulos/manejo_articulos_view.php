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
		<title>Manejo De Artículos</title>
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
		<!--CARGA DEL JQUERY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>"></script> 		
		<!--CARGA DE MODAL-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-impromptu.js'); ?>"></script>
		<!--CSS ESTILO DEL MODAL-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/jquery-impromptu.css'); ?>">
		<!--SCRIPT DE NOTY-->		
		<script src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE NUMERIC-->		
		<script src="<?php echo base_url('application/scripts/jquery.numeric.js'); ?>" type="text/javascript"></script>
		<!-- DATA TABES SCRIPT -->
        <script src="<?php echo base_url('/application/scripts/datatables/dataTablesNew.js');?>" type="text/javascript"></script>
		<!--CARGA DE HERRAMIENTAS VARIAS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/articulos/manejo_articulos_tools.js'); ?>"></script>
		<!--CARGA DE HERRAMIENTAS TABLA-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/articulos/tabla_manejo_articulos_tools.js'); ?>"></script>
		<!--CSS ESTILO DE LA PAG Y TABLA-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/articulos/style_manejo_articulos.css'); ?>">
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
			<p class="titulo_wrapper">Manejo De Artículos</p>
			<hr class="division_wrapper">
		    
			<div class="contenido">
				<fieldset class="lista-articulos-fieldset" id="contenido">
					<legend>Lista de Artículos en Inventario</legend>
					<div class="contenedor-sucursal">
						<p class="contact label-sucursal">Sucursal:</p>
						<select class="input_dos" onchange="cargarArticulosSucursal()" id="sucursalListaArticulos">
							<?php
								foreach($Familia_Empresas as $en => $ek){
									echo "<option value='$ek'>$en</option>";								
								}
							?>			
						</select>
					</div>
					<table id='tabla_editar' class='tablaPrincipal'>
						<thead> 
							<th ></th>
							<th class='Sorted_enabled'>
								Código
							</th>
							<th class='Sorted_enabled'>
								Descripción
							</th>
							<th class='Sorted_enabled'>
								Inventario
							</th>
							<th class='Sorted_enabled'>
								Descuento
							</th>
							<th>
								Costo
							</th>
							<th>
								Precio Afiliado 
							</th>
							<th>
								Precio No Afiliado
							</th>
							<th >
								Opciones
							</th>
						</thead> 
						<tbody>
						</tbody>
					</table>
					<div class='div_bot_des'>
						<a href='javascript:;' onClick='selectAllCheckBox()' class='boton_desall'>Seleccionar Todo</a>
						<a href='javascript:;' onClick='resetCheckBox()' class='boton_desall'>Deseleccionar Todo</a>
					</div>
					<div class='div_descuento_ingreso'>
						<label class="contact">Descuento: </label><input type="text" class="descuento-input" id="descuento" autocomplete="off" /><label class="contact">%</label>
						<a href='javascript:;' onClick='agregarDescuentoMasivo()' class='boton_descuento'>Agregar</a>
					</div>
					<div class='div_descuento_ingreso'>
						<label class="contact">Retención: </label>
						<a href='javascript:;' onClick='habilitarRetencion()' class='boton_descuento'>Habilitar</a>
						<a href='javascript:;' onClick='deshabilitarRetencion()' class='boton_descuento' style="background: #FF5400">Deshabilitar</a>
					</div>
				</fieldset>
				<fieldset class="lista-articulos-bodega" id="contenido-bodega">
					<legend>Lista de Artículos en Bodega</legend>
					<div class="contenedor-sucursal">
						<p class="contact label-sucursal">Sucursal:</p>
						<select class="input_dos" onchange="cargarArticulosBodegaSucursal()" id="sucursalListaArticulosBodega">
							<?php
								foreach($Familia_Empresas as $en => $ek){
									echo "<option value='$ek'>$en</option>";								
								}
							?>			
						</select>
					</div>
					<table id='tabla_editar_bodega' class='tablaPrincipal'>
						<thead> 
							<th class='Sorted_enabled'>
								Código
							</th>
							<th class='Sorted_enabled'>
								Descripción
							</th>
							<th class='Sorted_enabled'>
								Inventario
							</th>							
						</thead> 
						<tbody>
						</tbody>
					</table>				
				</fieldset>
				<fieldset class="lista-articulos-temporales-fieldset">
					<legend>Artículos Temporales</legend>
					<div class="contenedor-sucursal">
						<p class="contact label-sucursal">Sucursal:</p>
						<select class="input_dos" onchange="actualizarProductosTemporales()" id="sucursalTemporalesArticulos">
							<?php
								foreach($Familia_Empresas as $en => $ek){
									echo "<option value='$ek'>$en</option>";								
								}
							?>			
						</select>
					</div>
					<table id='tabla_temporal' class='tablaPrincipal'>
						<thead> 
							<th class='Sorted_enabled'>
								Código
							</th>
							<th class='Sorted_enabled'>
								Cantidad
							</th>
						</thead> 
						<tbody id="cuerpo_articulos_temporales">
						</tbody>
					</table>
					<div class='div_bot_des'>
						<a href='javascript:;' onClick='devolverProductosTemporales()' class='boton_desall'>Devolver a Inventario</a>
					</div>
				</fieldset>
			</div>

			
        </div>
				<!--<div id="timeout_show"></div>-->
       		

		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>