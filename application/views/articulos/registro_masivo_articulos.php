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
		<title>Traspaso Masivo de Artículos</title>
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
		<?php include '/../Header/log_out_from_browser_Script.php';?>
		<!--SCRIPT DE JQUERY-->		
		<script src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>" type="text/javascript"></script>
		<!--CSS ESTILO TRASPASO MASIVO ARTICULOS-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/articulos/style_traspaso_masivo.css'); ?>">
		<!--SCRIPT DE HERRAMIENTAS-->		
		<script src="<?php echo base_url('application/scripts/articulos/traspaso_masivo_tools.js'); ?>" type="text/javascript"></script>		
		<!--SCRIPT DE NOTY-->		
		<script src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>" type="text/javascript"></script>		
	</head>
	<body>
		<!--Incluir imagen de cabezera-->
		<?php include '/../Header/Header_Picture.php';?>
		
		<!--Incluir menu principal-->
		<?php include '/../Header/selector_menu.php';?>

		<!--Incluir informacion log in-->
		<?php include '/../Header/Log_In_Information.php';?>
	
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Traspaso Masivo de Artículos</p>
			<hr class="division_wrapper">
			<div class="contenedor">
				<table>
					<tr>
						<td>
							<p class="contact">Por favor tome en cuenta lo siguiente:</p>
						</td>
					</tr>
					<tr>
						<td>
						
						</td>
					</tr>
					<tr>
						<td>
							<p class="contact">1- Celdas requeridas <small>Nota: El orden es importante</small></p>
						</td>
					</tr>
					<tr>
						<td class="pad-l">
							<div class="imagen_celdas">
								<img src="<?php echo base_url('application/images/articulos/celdas_ingreso_inventario.png'); ?>"/>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<p class="contact">2- Formato de Excel <small>Requerido</small></p>
						</td>
					</tr>
					<tr>
						<td  class="pad-l">
							<p class="contact">Excel 97-2003 - xls</p>
						</td>
					</tr>			
				</table>
				<hr class="division-contenido">
				<?php 
					$attributes = array('name' => 'traspaso_inventario_masivo', 'class' => 'traspaso_inventario_masivo', 'id' => 'traspaso_inventario_masivo');
					
					echo form_open_multipart('articulos/registrar/carga_excel', $attributes); 
				?>
					<p class="contact label-sucursal">Sucursal:</p>
					<select class="input_dos" name="sucursal">
						<?php
							foreach($Familia_Empresas as $en => $ek){
								echo "<option value='$ek'>$en</option>";								
							}
						?>			
					</select>					
					<p class="contact">Seleccione el archivo a cargar:</p>
					<div class="pad-l mar-top">
						<input type="file" name="archivo_excel" id="archivo_excel" accept="application/vnd.ms-excel"/>
					</div>
					<input class="boton_procesar " value="Procesar" type="submit" />
				</form>
				<?php 
					if(isset($_GET['s'])&&$_GET['s']=='1'){
						echo "
							<div class='alert alert-success'>
								¡Se traspasaron los artículos con éxito a inventario!
							</div>
						";
					}
					if(isset($error)){
						echo "<div class='alert alert-danger'>
								ERROR $error - $msj ";
						if($error == '5'){ //Si es error con articulos, mostrar cuales articulos
							
							echo "<br><br><small class='bold'>Problemas con el código:</small>";
							if(sizeOf($erroresCodigo)<1){
								echo "<br><small>No hay artículos.</small>";
							}else{
								foreach($erroresCodigo as $art){
									echo "<br><small>- $art</small>";
								}
							}
							
							echo "<br><br><small class='bold'>Problemas con el Precio 1:</small>";
							if(sizeOf($erroresPrecio1)<1){
								echo "<br><small>No hay artículos.</small>";
							}else{
								foreach($erroresPrecio1 as $art){
									echo "<br><small>- $art</small>";
								}
							}
							
							echo "<br><br><small class='bold'>Problemas con el Precio 2:</small>";
							if(sizeOf($erroresPrecio2)<1){
								echo "<br><small>No hay artículos.</small>";
							}else{
								foreach($erroresPrecio2 as $art){
									echo "<br><small>- $art</small>";
								}
							}
							
							echo "<br><br><small class='bold'>Problemas con el Precio 3:</small>";
							if(sizeOf($erroresPrecio3)<1){
								echo "<br><small>No hay artículos.</small>";
							}else{
								foreach($erroresPrecio3 as $art){
									echo "<br><small>- $art</small>";
								}
							}
							
							echo "<br><br><small class='bold'>Problemas con el Precio 4:</small>";
							if(sizeOf($erroresPrecio4)<1){
								echo "<br><small>No hay artículos.</small>";
							}else{
								foreach($erroresPrecio4 as $art){
									echo "<br><small>- $art</small>";
								}
							}
							
							echo "<br><br><small class='bold'>Problemas con el Precio 5:</small>";
							if(sizeOf($erroresPrecio5)<1){
								echo "<br><small>No hay artículos.</small>";
							}else{
								foreach($erroresPrecio5 as $art){
									echo "<br><small>- $art</small>";
								}
							}
							
							echo "<br><br><small class='bold'>Problemas con la cantidad:</small>";
							if(sizeOf($erroresCantidad)<1){
								echo "<br><small>No hay artículos.</small>";
							}else{
								foreach($erroresCantidad as $art){
									echo "<br><small>- $art</small>";
								}
							}
							
							echo "<br><br><small class='bold'>Familia No Existe:</small>";
							if(sizeOf($erroresFamilia)<1){
								echo "<br><small>No hay artículos.</small>";
							}else{
								foreach($erroresFamilia as $art){
									echo "<br><small>- $art</small>";
								}
							}
							
							echo "<br><br><small class='bold'>Sucursal No Existe:</small>";
							if(sizeOf($erroresSucursal)<1){
								echo "<br><small>No hay artículos.</small>";
							}else{
								foreach($erroresSucursal as $art){
									echo "<br><small>- $art</small>";
								}
							}
							
							echo "<br><br><small class='bold'>Formato de Exento No Válido:</small>";
							if(sizeOf($erroresExento)<1){
								echo "<br><small>No hay artículos.</small>";
							}else{
								foreach($erroresExento as $art){
									echo "<br><small>- $art</small>";
								}
							}
							
							echo "<br><br><small class='bold'>Con Descuento no Válido:</small>";
							if(sizeOf($erroresDescuento)<1){
								echo "<br><small>No hay artículos.</small>";
							}else{
								foreach($erroresDescuento as $art){
									echo "<br><small>- $art</small>";
								}
							}
							
							echo "<br><br><small class='bold'>No existen en bodega:</small>";
							if(sizeOf($erroresCodBrasil)<1){
								echo "<br><small>No hay artículos.</small>";
							}else{
								foreach($erroresCodBrasil as $art){
									echo "<br><small>- $art</small>";
								}
							}
							
							echo "<br><br><small class='bold'>La cantidad es mayor a la disponible en bodega:</small>";
							if(sizeOf($erroresCantidadMayor)<1){
								echo "<br><small>No hay artículos.</small>";
							}else{
								foreach($erroresCantidadMayor as $art){
									echo "<br><small>- $art</small>";
								}
							}
							
							
						}
						echo "</div>";
					}
				?>
				
				
				
			</div><!-- Contenedor div -->
        </div>		

		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>