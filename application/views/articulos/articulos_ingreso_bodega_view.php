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
		<title>Ingreso a Bodega</title>
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
		<!--SCRIPT DE AJAX JQUERY-->		
		<script src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE HERRAMIENTAS-->		
		<script src="<?php echo base_url('application/scripts/articulos/ingreso_bodega_tools.js'); ?>" type="text/javascript"></script>
		<!--CSS ESTILO DE LA PAGINA ESPECIFICO-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/articulos/style_ingreso_bodega.css'); ?>">	
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
			<p class="titulo_wrapper">Ingreso a Bodega</p>
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
							<img src="<?php echo base_url('application/images/articulos/celdas_ingreso_bodega.png'); ?>"/>
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
					$attributes = array('name' => 'ingreso_bodega', 'class' => 'ingreso_bodega_form', 'id' => 'ingreso_bodega_form');
					
					echo form_open_multipart('articulos/bodega/cargar', $attributes); 
				?>
					<!-- Campo guarda una bandera para evitar reenviar el form-->
					
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
								¡Se agregaron los artículos con éxito a la bodega!
							</div>
						";
					}
					if(isset($error)){
						echo "<div class='alert alert-danger'>
								ERROR $error - $msj ";
						if($error == '5'){ //Si es error con articulos, mostrar cuales articulos
							echo "<br><br><small class='bold'>Problemas con el costo:</small>";
							if(sizeOf($errorCosto)<1){
								echo "<br><small>No hay artículos.</small>";
							}else{
								foreach($errorCosto as $art){
									echo "<br><small>- $art</small>";
								}
							}
							echo "<br><br><small class='bold'>Problemas con la cantidad:</small>";
							if(sizeOf($errorCantidad)<1){
								echo "<br><small>No hay artículos.</small>";
							}else{
								foreach($errorCantidad as $art){
									echo "<br><small>- $art</small>";
								}
							}
						}
						echo "</div>";
					}
				?>
				
				
				
			</div><!-- Contenedor div -->
		</div><!-- main_wrapper div -->	

       	

		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>