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
		<title>Ingreso Masivo de Artículos</title>
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
		<!--CSS ESTILO TRASPASO MASIVO ARTICULOS-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/articulos/style_ingreso_masivo.css?v='.$javascriptCacheVersion); ?>">
		<!--SCRIPT DE HERRAMIENTAS-->
		<script src="<?php echo base_url('application/scripts/articulos/ingreso_masivo_tools.js?v='.$javascriptCacheVersion); ?>" type="text/javascript"></script>
		<!--SCRIPT DE NOTY-->
		<script src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>" type="text/javascript"></script>
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
			<p class="titulo_wrapper">Ingreso Masivo de Artículos</p>
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
								<img src="<?php echo base_url('application/images/articulos/celdas_ingreso_inventario_nobrasil.png'); ?>"/>
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

					echo form_open_multipart('articulos/ingresar/cargaMasiva', $attributes);
				?>
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
								¡Se ingresaron los artículos con éxito a inventario!
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

							echo "<br><br><small class='bold'>Formato de la retención No Válido:</small>";
							if(sizeOf($erroresRetencion)<1){
								echo "<br><small>No hay artículos.</small>";
							}else{
								foreach($erroresRetencion as $art){
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

							echo "<br><br><small class='bold'>Con Tipo de Código no Válido:</small>";
							if(sizeOf($erroresTipoCodigo)<1){
								echo "<br><small>No hay artículos.</small>";
							}else{
								foreach($erroresTipoCodigo as $art){
									echo "<br><small>- $art</small>";
								}
							}

							echo "<br><br><small class='bold'>Con Unidad de Medida no Válida:</small>";
							if(sizeOf($erroresUnidadMedida)<1){
								echo "<br><small>No hay artículos.</small>";
							}else{
								foreach($erroresUnidadMedida as $art){
									echo "<br><small>- $art</small>";
								}
							}

							echo "<br><br><small class='bold'>Con Código Cabys no Válido:</small>";
							if(sizeOf($erroresCodigoCabys)<1){
								echo "<br><small>No hay artículos.</small>";
							}else{
								foreach($erroresCodigoCabys as $art){
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
		<?php include PATH_FOOTER;?>
	</body>
</html>