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
		<title>Subida Masiva de Imágenes</title>
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
		<!--SCRIPT DE AJAX JQUERY-->
		<script src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE NOTY-->
		<script src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>" type="text/javascript"></script>

        <link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/articulos/style_edicion_masiva.css'); ?>">
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
			<p class="titulo_wrapper">Subida Masiva de Imágenes</p>
			<hr class="division_wrapper">
			<div class="contenedor">
				<?php
					$attributes = array('name' => 'edicion_masiva', 'class' => 'edicion_masiva', 'id' => 'edicion_masiva');

					echo form_open_multipart('articulos/editar/actualizarMasivoImagenes', $attributes);
				?>
					<!-- Campo guarda una bandera para evitar reenviar el form-->
					<p class="contact">Seleccione el(los) archivo(s) a cargar:</p>
					<div class="pad-l mar-top">
						<input type="file" name="imagenes[]" id="imagenes" accept="image/jpg,imagen/jpeg,image/gif,image/png" multiple/>
                    </div>
                    <p class="contact" style="margin-top: 15px;"><strong>Nota:</strong> Solo se permiten archivos .jpg, .jpeg, .gif, .png con un peso máximo de 1 MB.<br>El nombre de cada imagen debe ser igual al código del artículo a actualizar</p>
					<input class="boton_procesar " value="Actualizar" type="submit" />
				</form>
				<?php
					if(isset($_GET['s'])&&$_GET['s']=='1'){
						echo "
							<div class='alert alert-success'>
								¡Se actualizaron los artículos con éxito!
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
						}
						echo "</div>";
					}
				?>



			</div><!-- Contenedor div -->
		</div><!-- main_wrapper div -->



		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>