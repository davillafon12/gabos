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
		<title>Configuración General</title>		
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
		<?php include 'Header/log_out_from_browser_Script.php';?>
		<!--CSS ESTILO DEL FORMULARIO-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/style_configuracion.css'); ?>">
	</head>
	<body >
		<!--Incluir imagen de cabezera-->
		<?php include 'Header/Header_Picture.php';?>
		
		<!--Incluir menu principal-->
		<?php include 'Header/selector_menu.php';?>

		<!--Incluir informacion log in-->
		<?php include 'Header/Log_In_Information.php';?>
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Configuración General</p>
			<hr class="division_wrapper">
			<div class="post_message"><?php if (isset($post_message)) {echo $post_message;}?></div>
			<div class="warning_message"><?php if (isset($c_array['message'])) {echo $c_array['message'];}?></div>
			<div  class="form">
			<?php 
				$attributes = array('name' => 'guardar_configuracion', 'class' => 'guardar_configuracion_form');
				
				echo form_open('configuracion/guardar', $attributes); 
				
			?>	 
				<p class="titulo">Configuración</p>
				
			    <table>
					<tr>
						<td>
						<p class="contact"><label for="email">Correo Electrónico:</label></p> 
						</td>
						<td>
						<input value="<?php echo $c_array['correo_administracion'];?>" id="email" autocomplete="off" name="email" required="" tabindex="1" type="text"> 
						</td>
					</tr>
					<tr>
						<td>
						<p class="contact"><label for="cant_dec">Cantidad de decimales:</label> </p>
						</td>
						<td>
						<input value="<?php echo $c_array['cantidad_decimales'];?>" id="cant_dec" autocomplete="off" name="cant_dec" tabindex="2" type="number" min="0" max="4"> 
						</td>
					</tr>
					<tr>
						<td>
						<p class="contact"><label for="title_cambio">Tipo de cambio dolar($)</label></p> 
						</td>
					</tr>
					<tr>
						<td>
						<p class="contactR"><label for="compra_dolar">Compra:</label></p>
						</td>
						<td>
						<input value="<?php echo $c_array['dolar_compra'];?>" autocomplete="off" name="compra_dolar" tabindex="3" type="number" min="1" step="0.01"> 
						</td>
					</tr>
					<tr>
						<td>
						<p class="contactR"><label for="venta_dolar">Venta:</label></p>
						</td>
						<td>
						<input value="<?php echo $c_array['dolar_venta'];?>" autocomplete="off" name="venta_dolar" tabindex="4" type="number" min="1" step="0.01"> 
						</td>
					</tr>
					<tr>
						<td>
						<p class="contact"><label for="title_cambio">Montos de afiliados</label> </p>
						</td>
					</tr>
					<tr>
						<td>
						<p class="contactR"><label for="compra_min">Mínimo:</label></p>
						</td>
						<td>
						<input value="<?php echo $c_array['monto_minimo_compra'];?>" autocomplete="off" name="compra_min" tabindex="5" type="number" min="1" step="0.01"> 
						</td>
					</tr>
					<tr>
						<td>
						<p class="contactR"><label for="venta_min">Intermedio:</label></p>
						</td>
						<td>
						<input value="<?php echo $c_array['monto_minimo_venta'];?>" autocomplete="off" name="venta_min" tabindex="6" type="number" min="1" step="0.01"> 
						</td>
					</tr>
					<tr>
						<td>
						<p class="contact"><label for="cant_ses">Tiempo de sesión activa:</label> </p>
						</td>
						<td>
						<input value="<?php echo $c_array['tiempo_sesion'];?>" id="cant_ses" autocomplete="off" name="cant_ses" tabindex="7" type="number" min="30"> 
						</td>
					</tr>
					<tr>
						<td>
						<p class="contact"><label for="iva_cant">Impuesto de Valor Agregado (IVA):</label> </p>
						</td>
						<td>
						<input value="<?php echo $c_array['iva'];?>" id="iva_cant" autocomplete="off" name="iva_cant" tabindex="8" type="number" min="0" max="100" step="0.01">% 
						</td>
					</tr>
				</table>
				<input class="buttom" name="submit" id="submit" tabindex="7" value="Guardar" type="submit" <?php if (isset($c_array['flag'])) {echo "disabled";}?>>
				<br>
				<br>			
			   </form> 
			</div>
			
				<!--<div id="timeout_show"></div>-->
        </div>		

		<!--Incluir footer-->
		<?php include 'Footer/Default_Footer.php';?>
	</body>
</html>