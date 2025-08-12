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
		<title>Cambio de Código</title>
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
		<!--SCRIPT DE NUMERIC-->		
		<script src="<?php echo base_url('application/scripts/jquery.numeric.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE Impromptu-->		
		<script src="<?php echo base_url('application/scripts/jquery-impromptu.js'); ?>" type="text/javascript"></script>
		<!--CSS ESTILO DEL Impromptu-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/jquery-impromptu.css'); ?>">
		<!--CSS ESTILO ESPECIFICO DE LA PAG-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/articulos/style_cambio_codigo.css'); ?>">
		<!--SCRIPT DE HERRAMIENTAS-->		
		<script src="<?php echo base_url('application/scripts/articulos/cambio_codigo_tools.js'); ?>" type="text/javascript"></script>		
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
			<p class="titulo_wrapper">Cambio de Código</p>
			<hr class="division_wrapper">
			<div class="contenedor">
				<table>
					<tr>
						<td class="pad-right border-right border-bottom" colspan="2">
							<p class="contact pre-advertencia">Artículo a cambiar código:</p>
							<div id="status" class="status">
								<img class="imagen-advertencia" src="<?php echo base_url('application/images/scripts/error.gif');?>">
								<p class="advertencia"> - No Existe!!!</p>
							</div>
						</td>
						<td class="border-bottom pad-right2" colspan="2">
							<p class="contact pre-advertencia">Artículo a abonar:</p>
							<div id="status2" class="status">
								<img class="imagen-advertencia" src="<?php echo base_url('application/images/scripts/error.gif');?>">
								<p class="advertencia"> - No Existe!!!</p>
							</div>
						</td>
					</tr>
					<tr>
						<td><label for="sucursal" class="contact">Sucursal:</label></td>
						<td class="pad-right border-right" >
							<select id="sucursal" class="input_dos" name="sucursal" onchange="cambioDeSucursal()">
								<?php 
									foreach($Familia_Empresas as $Nombre_Empresa => $codigo_empresa)
									{
										echo "<option value='".$codigo_empresa."'";
										echo">".$codigo_empresa." - ".$Nombre_Empresa."</option>";											
									}
								?>
							</select>
						</td>
						<td><label for="codigo_abonar" class="contact">Código:</label></td>
						<td class="pad-right2"><input id="codigo_abonar" class="input_uno" name="codigo_abonar" type="text" onkeyup="getArticuloAbonar()"/></td>
					</tr>
					<tr>
						<td><label for="codigo_cambiar" class="contact">Código:</label></td>
						<td class="pad-right border-right" ><input id="codigo_cambiar" class="input_uno" name="codigo_cambiar" type="text" onkeyup="getArticuloCambiar()"/></td>
						<td><label for="cantidad" class="contact">Cantidad:</label></td>
						<td class="pad-right2"><input id="cantidad" class="input_uno" name="cantidad" type="text"/></td>
					</tr>
					<tr>
						<td><p class="contact">Inventario:</p></td>
						<td class="pad-right border-right" >
							<p class="contact" id="inventario"></p>
							<input type="hidden" id="inventarioh"/>
						</td>
						<td><p class="contact">Descripción:</p></td>
						<td class="pad-right2"><p class="contact" id="descripcion_abonar"></p></td>
					</tr>
					<tr>
						<td class="border-bottom"><p class="contact">Descripción:</p></td>
						<td class="pad-right border-right border-bottom" ><p class="contact" id="descripcion_cambiar"></p></td>
						<td class="border-bottom" colspan="2"></td>
					</tr>
				</table>	
				<input type="button" class="boton_agregar" value="+ Agregar" onclick="agregarFila()"/>
				<hr class="divisor"/>
				<p class="contact">Articulos a realizar cambio:</p>
				<table class="tabla_articulos" id="tabla_articulos">
					<thead>
						<tr class="header">
							<th class="titulo_header_tabla" style="width: 80px;">Código</th>
							<th class="titulo_header_tabla" style="width: 250px;">Descripción</th>
							<th class="titulo_header_tabla" style="width: 25px;"></th>
							<th class="titulo_header_tabla" style="width: 80px;">Código</th>
							<th class="titulo_header_tabla" style="width: 250px;">Descripción</th>
							<th class="titulo_header_tabla" style="width: 70px;">Cantidad</th>
							<th class="titulo_header_tabla" style="width: 25px;"></th>
						</tr>
					</thead>
					<tbody id="tbody_articulos">
						
					</tbody>
				</table>
				<input type="button" class="boton" value="Realizar Cambio" onclick="realizarCambioCodigo()"/>				
			</div><!-- Contenedor div -->
        </div>		

		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>