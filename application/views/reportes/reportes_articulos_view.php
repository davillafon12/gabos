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
		<title>Registro De Usuarios</title>
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
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/base/minified/jquery-ui.min.css'); ?>">		
		<!--SCRIPT DE EXPIRACION DE LA SESION-->
		<?php include '/../Header/log_out_from_browser_Script.php';?>
		<!--SCRIPT DE AJAX JQUERY-->		
		<script src="<?php echo base_url('application/scripts/reportes/jquery-2.0.3.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('application/scripts/reportes/jquery-2.0.3.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('application/scripts/reportes/jquery-ui-1.10.3.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('application/scripts/reportes/jquery-ui-1.10.3.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('application/scripts/reportes/jquery.validate.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('application/scripts/reportes/utilitarios.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('application/scripts/reportes/reportesArticulos.js'); ?>" type="text/javascript"></script>
	
	</head>
	<body >
		<!--Incluir imagen de cabezera-->
		<?php include '/../Header/Header_Picture.php';?>		
		<!--Incluir menu principal-->
		<?php include '/../Header/selector_menu.php';?>
		<!--Incluir informacion log in-->
		<?php include '/../Header/Log_In_Information.php';?>
		<!--CSS ESTILO DEL FORMULARIO-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/reportes/articulo.css'); ?>">
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Consulta De Artículos</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
			<div  class="form">
				<?php 
					$attributes = array('name' => 'reporte_articulo_form', 'class' => 'reporte_articulo_form-form');					
					echo form_open_multipart('reportes/reportes/articulosReportes', $attributes); 
				?>	 
				
					<fieldset class="recuadro">	
					<legend>Filtración Reportes</legend>	
					<p id="temp"></p>
					<table>
						<tr>
							<td>
								<label for="tipo_reporte_Seleccionado" class="labelMedium">Tipo Reporte:</label>							
							</td>
							<td>
								<select id="tipo_reporte" name="tipo_reporte" class="styleSelect" tabindex="8">
								<?php 					
									foreach($Reportes as $codigo_reporte => $nombre_reporte )
									{
										echo "<option value='".$codigo_reporte."'";
										echo">".$nombre_reporte."</option>";
									}
								?>
								</select> 
							</td>
							<td>
								<label for="sucursal"  class="labelMedium">Empresa:</label>							
							</td>
							<td colspan=3>
								<select id="sucursal"  name="sucursal" class="styleSelect" tabindex="8">
								<?php 					
									foreach($Empresas as $Nombre_Empresa => $codigo_empresa)
									{
										echo "<option value='".$codigo_empresa."'";
										echo">".$codigo_empresa." - ".$Nombre_Empresa."</option>";
									}
								?>
								</select> 
							</td>
						</tr>
						<tr class="sucDesamparados">
							<td>
								<label for="lafecha_inicial"  class="labelMedium">Desamparados:</label>							
							</td>
							<td>
								<input type="checkbox"name="check_Desamparados" value="1">
							</td>
							<td>
								<label for="lafecha_inicial"  class="labelMedium">GarotasBonitas:</label>							
							</td>
							<td>
								<input type="checkbox"name="check_GarotasBonitas" value="1">
							</td>
						</tr>
						<tr class="rArticulos">
							<td>
								<label for="familia"  class="labelMedium">Familia:</label>							
							</td>
							<td>
								<select name="familia" class="styleSelect" tabindex="8">
								<?php 				
									echo "<option value='null'>SELECCIONE</option>";
									foreach($Familias as $Nombre_Familia =>$codigo_Familia)
									{
										echo "<option value='".$codigo_Familia."'";
										echo">".$Nombre_Familia."</option>";
									}
								?>
								</select> 
							</td>
							<td colspan=4>
							</td>
						</tr>	
						<tr class="rArticulos">
							<td>
								<label for="rango_codigo"  class="labelMedium">Rango Código:</label>						
							</td>
							<td>
								<select name="rangoCodigo" id="rangoCodigo" class="styleSelect" tabindex="8">
								<?php 				
									foreach($Rangos as $codigo_Rango => $Nombre_Rango)
									{
										echo "<option value='".$codigo_Rango."'";
										echo">".$Nombre_Rango."</option>";
									}
								?>
								</select> 
							</td>
							<td class="rCodigoI">
								<label for="CodigoI"  class="labelMedium">Código Inicial:</label>						
							</td>
							<td class="rCodigoI"> 
								<input id="CodigoI" class="input_Small" autocomplete="off" name="CodigoI"><br>
							</td>
							<td class="rCodigoF">
								<label for="CodigoF"  class="labelMedium">Código Final:</label>						
							</td>
							<td class="rCodigoF">
								<input id="CodigoF" class="input_Small" autocomplete="off" name="CodigoF"><br>
							</td>
						</tr>
						<tr class="rArticulos">
							<td colspan=2>
								<label for="precio"  class="labelMedium">Número de Precio:</label>							
							</td>
							<td>
								<select name="precio" id="precio" class="styleSelect" tabindex="8">
								<?php 				
									foreach($Precios as $codigo_Precio =>$Nombre_Precio)
									{
										echo "<option value='".$codigo_Precio."'";
										echo">".$Nombre_Precio."</option>";
									}
								?>
								</select> 
							</td>
							<td colspan=3>
							</td>
						</tr>
						<tr class="mRangoPrecios">
							<td>
								<label for="rango_precio"  class="labelMedium">Rango Precio:</label>						
							</td>
							<td>
								<select name="rangoPrecios" id="rangoPrecios" class="styleSelect" tabindex="8">
								<?php 				
									foreach($Rangos as $codigo_Rango => $Nombre_Rango)
									{
										echo "<option value='".$codigo_Rango."'";
										echo">".$Nombre_Rango."</option>";
									}
								?>
								</select> 
							</td>
							<td class="rPrecioI">
								<label for="PrecioI"  class="labelMedium">Precio Inicial:</label>						
							</td>
							<td class="rPrecioI">
								<input id="PrecioI" class="input_Small" autocomplete="off" name="PrecioI"><br>
							</td>
							<td class="rPrecioF">
								<label for="PrecioF"  class="labelMedium">Precio Final:</label>						
							</td>
							<td class="rPrecioF">
								<input id="PrecioF" class="input_Small" autocomplete="off" name="PrecioF"><br>
							</td>
						</tr>	
						<tr class="rArticulos">
							<td>
								<label for="rango_articulos"  class="labelMedium">Rango Artículos:</label>						
							</td>
							<td>
								<select name="rangoArticulos" id="rangoArticulos" class="styleSelect" tabindex="8">
								<?php 				
									foreach($Rangos as $codigo_Rango => $Nombre_Rango)
									{
										echo "<option value='".$codigo_Rango."'";
										echo">".$Nombre_Rango."</option>";
									}
								?>
								</select> 
							</td>
							<td class="rArticulosI">
								<label for="CantidadI"  class="labelMedium">Cantidad Inicial:</label>						
							</td>
							<td class="rArticulosI">
								<input id="CantidadI" class="input_Small" autocomplete="off" name="CantidadI"><br>
							</td>
							<td class="rArticulosF">
								<label for="CantidadF"  class="labelMedium">Cantidad Final:</label>						
							</td>
							<td class="rArticulosF">
								<input id="CantidadF" class="input_Small" autocomplete="off" name="CantidadF"><br>
							</td>
						</tr>	
						<tr class="rArticulos">
							<td>
								<label for="rango_articulosDef"  class="labelMedium">Rango Artículos Def:</label>						
							</td>
							<td>
								<select name="rangoArticulosDef" id="rangoArticulosDef" class="styleSelect" tabindex="8">
								<?php 				
									foreach($Rangos as $codigo_Rango => $Nombre_Rango)
									{
										echo "<option value='".$codigo_Rango."'";
										echo">".$Nombre_Rango."</option>";
									}
								?>
								</select> 
							</td>
							<td class="rArticulosDefI">
								<label for="CantidadDefI"  class="labelMedium">Cantidad Inicial:</label>						
							</td>
							<td class="rArticulosDefI">
								<input id="CantidadDefI" class="input_Small" autocomplete="off" name="CantidadDefI"><br>
							</td>
							<td class="rArticulosDefF">
								<label for="CantidadDefF"  class="labelMedium">Cantidad Final:</label>						
							</td>
							<td class="rArticulosDefF">
								<input id="CantidadDefF" class="input_Small" autocomplete="off" name="CantidadDefF"><br>
							</td>
						</tr>	
						<tr class="rArticulos">
							<td>
								<label for="paExento"  class="labelMedium">Exento:</label>						
							</td>
							<td>
								<input id="paExento" type="Checkbox" name="paExento" value="1">Exento<br>
							</td>
						</tr>
						<tr class="rFechas">
							<td>
								<label for="lafecha_inicial"  class="labelMedium">Fecha Inicial:</label>							
							</td>
							<td>
								<input id="fecha_inicial" class="input_calendar" autocomplete="off" name="fecha_inicial">
							</td>
							<td>
								<label for="lafecha_final"  class="labelMedium">Fecha Final:</label>							
							</td>
							<td>
								<input id="fecha_final" class="input_calendar" autocomplete="off" name="fecha_final"><br>						
							</td>
						</tr>
						<tr class="rCodigoCedula">
							<td class="rCodigo">
								<label for="Codigo"  class="labelMedium">Código:</label>						
							</td>
							<td class="rCodigo" colspan="2"> 
								<input id="Codigo" class="input_Small" autocomplete="off" name="Codigo"><br>
							</td>
							<td class="rCedula">
								<label for="paCedula"  class="labelMedium">Cédula:</label>						
							</td>
							<td class="rCedula" colspan="2">
								<input type="text" id="paCedula" class="input_Small" autocomplete="off" name="paCedula">
							</td>
						</tr>	
					</table>
					</fieldset>
					<div class="divButton">		
						<input class="buttom" value="Volver" type="button" onclick="window.location.assign('<?php echo base_url('home')?>')">
						<input class="buttom" name="submit" id="submit" onsubmit="" tabindex="18" value="Consultar" type="submit">						
					</div>			
				<?php
					echo form_close();
				?>				
			</div>		
		</div>
		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>