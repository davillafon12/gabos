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
		<?php include PATH_LOG_OUT_HEADER;?>
		<!--SCRIPT DE AJAX JQUERY-->		
		<script src="<?php echo base_url('application/scripts/reportes/jquery-2.0.3.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('application/scripts/reportes/jquery-2.0.3.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('application/scripts/reportes/jquery-ui-1.10.3.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('application/scripts/reportes/jquery-ui-1.10.3.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('application/scripts/reportes/jquery.validate.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('application/scripts/reportes/Utilitarios.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('application/scripts/reportes/reportesClientes.js'); ?>" type="text/javascript"></script>
	
	</head>
	<body >
		<!--Incluir imagen de cabezera-->
		<?php include PATH_HEADER_PICTURE;?>
		
		<!--Incluir menu principal-->
		<?php include PATH_HEADER_SELECTOR_MENU;?>

		<!--Incluir informacion log in-->
		<?php include PATH_HEADER_LOG_IN_INFO;?>
		<!--CSS ESTILO DEL FORMULARIO-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/reportes/reporte.css'); ?>">
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Consulta De Clientes</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
			<div  class="form">
				<?php 
					$attributes = array('name' => 'reporte_clientes_form', 'class' => 'reporte_clientes_form-form');					
					echo form_open_multipart('reportes/reportes/clientesReportes', $attributes); 
				?>	 
				
					<fieldset class="recuadro">	
					<legend>Filtración Reportes</legend>							
					<table>
						<tr>
							<td>
								<label for="tipo_Reporte_seleccionado" class="labelMedium">Tipo Reporte:</label>							
							</td>
							<td>
								<select  id="tipo_reporte" name="tipo_reporte" class="styleSelect" tabindex="8">
								<?php 					
									foreach($Reportes as $codigo_reporte => $nombre_reporte )
									{
										echo "<option value='".$codigo_reporte."'";
										echo">".$nombre_reporte."</option>";
									}
								?>
								</select> 
							</td>
							<td class="Ssucursal">
								<label for="sucursal"  class="labelMedium">Empresa:</label>							
							</td>
							<td class="Ssucursal">
								<select id="sucursal" name="sucursal" class="styleSelect" tabindex="8">
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
						<tr class="fFechas">
							<td>
								<label for="lafecha_inicial"  class="labelMedium">FiltrarFechas:</label>							
							</td>
							<td colspan="3">
								<input type="checkbox" id="mFecha" name="mFecha" value="1"> Mostrar Fechas<br>
							</td>
						</tr>
						<tr class="rFechas">
							<td>
								<label for="fecha_inicial"  class="labelMedium">Fecha Inicial:</label>							
							</td>
							<td>
								<input id="fecha_inicial" class="input_calendar" autocomplete="off" name="fecha_inicial">
							</td>
							<td>
								<label for="fecha_final"  class="labelMedium">Fecha Final:</label>							
							</td>
							<td>
								<input id="fecha_final" class="input_calendar" autocomplete="off" name="fecha_final"><br>						
							</td>
						</tr>	
						<tr class="mEstado">
							<td>
								<label for="Nombre_Estado"  class="labelMedium">Estado:</label>							
							</td>
							<td>
								<select name="paEstado" class="styleSelect" tabindex="8">
								<?php 					
									foreach($EstadoCliente as $Codigo_Fstado => $Nombre_Estado)
									{
										echo "<option value='".$Codigo_Fstado."'";
										echo">".$Nombre_Estado."</option>";
									}
								?>
								</select> 
							</td>
							<td>
								<label for="sucursalTodas"  class="labelMedium">Sucursal Ingreso:</label>							
							</td>
							<td>
								<select id="sucursalTodas" name="sucursalTodas" class="styleSelect" tabindex="8">
								<?php 					
									foreach($EmpresasTodas as $Nombre_Empresa => $codigo_empresa)
									{
										echo "<option value='".$codigo_empresa."'";
										echo">".$codigo_empresa." - ".$Nombre_Empresa."</option>";
									}
								?>
								</select> 
							</td>							
						</tr>
						<tr class="uFacturas">
							<td>
								<label for="paEstadoFactura" id="paEstadoFacturaLabel" class="labelMedium">Estado Factura:</label>							
							</td>
							<td>
								<select name="paEstadoFactura" id="paEstadoFactura" class="styleSelect" tabindex="8">
								<?php 					
									foreach($EstadoFacturas as $Codigo_Factura => $Nombre_Factura)
									{
										echo "<option value='".$Codigo_Factura."'";
										echo">".$Nombre_Factura."</option>";
									}
								?>
								</select> 
							</td>
							<td>
								<label for="paEsSucursal"  class="labelMedium">Sucursal:</label>							
							</td>
							<td>
								<input type="checkbox" id="paEsSucursal" name="paEsSucursal" value="1"> Es Sucursal<br>
							</td>
						</tr>
						<tr class="uProformas">
							<td>
								<label for="paEstadoProforma" id="paEstadoProformaLabel" class="labelMedium">Estado Proforma:</label>							
							</td>
							<td>
								<select name="paEstadoProforma" id="paEstadoProforma" class="styleSelect" tabindex="8">
								<?php 					
									foreach($EstadoProforma as $Codigo_Factura => $Nombre_Factura)
									{
										echo "<option value='".$Codigo_Factura."'";
										echo">".$Nombre_Factura."</option>";
									}
								?>
								</select> 
							</td>
							<td>
								<label for="paEsSucursal"  class="labelMedium">Sucursal:</label>							
							</td>
							<td>
								<input type="checkbox" id="paEsSucursal" name="paEsSucursal" value="1"> Es Sucursal<br>
							</td>
						</tr>						
						<tr class="mNombre">
							<td rowspan= "3">
								<label for="mNombre"  class="labelMedium">Filtrar Por:</label>							
							</td>
							<td>
								<input type="checkbox" id="mNombre" name="mNombre" value="1">Nombre<br>
							</td>
							<td class="fNombre">
								<label for="fNombre">Nombre:</label>							
							</td>
							<td class="fNombre">
								<input type="text" id="paNombre" class="input_Small" name="paNombre" value=""><br>
							</td>
						</tr>
						<tr class="mArticulo">
							<td rowspan= "3">
								<label for="mFiltro"  class="labelMedium">Filtrar Por:</label>							
							</td>
							<td colspan= "3">
								<input type="checkbox" id="paArticulo" name="paArticulo" value="1" checked>Artículos<br>
							</td>
						</tr>
						<tr class="mFamilia">
							<td colspan= "4">
								<input type="checkbox" id="paFamilia" name="paFamilia" value="1" checked>Familia<br>
							</td>
						</tr>
						<tr class="mCedula">
							<td>
								<input type="checkbox" id="mCedula" name="mCedula" value="1">Cédula<br>
							</td>
							<td class="fCedula">
								<label for="fCedula"  class="input_Small">Cédula</label>							
							</td>
							<td class="fCedula">
								<input type="text" id="paCedula" class="input_Small" autocomplete="off" name="paCedula">
							</td>
						</tr>
						<tr class="mRango">
							<td>
								<input type="checkbox" id="mRango" name="mRango" value="1">Precio Total<br>
							</td>
							<td class="fRango">
								<label for="fCedula"  class="labelMedium">Rangos:</label>							
							</td>
							<td class="fRango">
								<select name="rangoM" id="rangoM"class="styleSelect" tabindex="8">
								<?php 				
									foreach($Rangos as $codigo_Rango => $Nombre_Rango)
									{
										echo "<option value='".$codigo_Rango."'";
										echo">".$Nombre_Rango."</option>";
									}
								?>
								</select> 
							</td>
						</tr>
						<tr>
							<td class="fRango1">
								<label for="fRango1" class="labelMedium">Desde:</label>							
							</td>
							<td class="fRango1">
								<input type="text" id="paMontoI" class="input_Small" autocomplete="off" name="paMontoI">
							</td>
							<td class="fRango2">
								<label for="fRango2"  class="labelMedium">Hasta:</label>							
							</td>
							<td class="fRango2">
								<input type="text" id="paMontoF" class="input_Small" autocomplete="off" name="paMontoF">
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
		<?php include PATH_FOOTER;?>
	</body>
</html>