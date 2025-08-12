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
		<title>Registro De Clientes</title>
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
		<!--CSS ESTILO REGISTRO MASIVO CLIENTES-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/clientes/style_registro_masivo.css'); ?>">
		<!--SCRIPT DE EXPIRACION DE LA SESION-->
		<?php include PATH_LOG_OUT_HEADER;?>
		<!--SCRIPT DE AJAX JQUERY-->
		
		<script src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('application/scripts/jquery.maskedinput.js'); ?>" type="text/javascript"></script>
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-1.2.6.min.js'); ?>"></script>
		<?php include PATH_BUSCAR_CLIENTE_ID_SCRIPT;?>	
	</head>
	<body onload="timeout()">
		<!--Incluir imagen de cabezera-->
		<?php include PATH_HEADER_PICTURE;?>
		
		<!--Incluir menu principal-->
		<?php include PATH_HEADER_SELECTOR_MENU;?>

		<!--Incluir informacion log in-->
		<?php include PATH_HEADER_LOG_IN_INFO;?>
	
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Registro Masivo Clientes</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
			<div  class="form">
			<?php 
				$attributes = array('name' => 'registrar_cliente_form', 'class' => 'registrar_cliente_form-form');
				
				echo form_open_multipart('clientes/registrar/carga_excel', $attributes); 
			?>
			<fieldset class="recuadro">	
			<legend>Información</legend>
				<p class="formato"> Formato Celdas : </p>
				<br><p class="formato"> Tipo Cédula - Cédula - Estado - Nombre - Apellidos - Carnet - Celular - Teléfono - Email - País - Dirección - Descuento - Observaciones - Tipo pago </p>
				<br><p class="formato"> Formato Excel 2007</p>
					<label class="formato" for="file">Filename:</label>
					<input type="file" name="file" id="file"><br>	
					<input class="buttom" name="submit"  o tabindex="18" value="Procesar" type="submit" >
					<a href='<?php echo base_url('home')?>' class='boton_volver'>Volver</a>
					<div class="tabla">
					<?php 		
		
						
						if(is_array($contenedor)){
							echo '<br><table id="datos"><tr>';	
							foreach($contenedor as $conte)
							{
								echo '<tr>';
								echo '<td>' . $conte[0] . '</td>';
								echo '<td>' . $conte[1] . '</td>';
								echo '<td>' . $conte[2] . '</td>';
								echo '<td>' . $conte[3] . '</td>';
								echo '<td>' . $conte[4] . '</td>';
								echo '<td>' . $conte[5] . '</td>';							
								echo '</tr>';
							}
							echo '</table>';
						}
						/*
						echo "<PRE>";
						print_r($contenedor);
						echo "</PRE>";*/
					?>
				</div>
				</form>
			<table>
			<tr>
				
			
			</tr>

			</table>
			</fieldset>
		</form>

		</div>			
<!---->
        </div>		

		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>