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
		<!--CSS ESTILO DE LA PAGINA-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/style_configuracion.css'); ?>">
		<!--SCRIPT DE HERRAMIENTAS-->		
		<script src="<?php echo base_url('application/scripts/configuracion/configuracion_tools.js'); ?>" type="text/javascript"></script>
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
			<!--<div class="post_message"><?php //if (isset($post_message)) {echo $post_message;}?></div>-->
			<!--<div class="warning_message"><?php //if (isset($c_array['message'])) {echo $c_array['message'];}?></div>-->
			<div  class="contenedor">
				<fieldset class="recuadro-general">
					<legend>General</legend>
					<?php 
						$attributes = array('name' => 'guardar_configuracion', 'class' => 'guardar_configuracion_form');
						echo form_open('config/guardar', $attributes); 							
					?>						
					<table>
						<tr>
							<td>
							<p class="contact"><label for="email">Correo Electrónico:</label></p> 
							</td>
							<td>
							<input class="input_uno" value="<?php echo $c_array['correo_administracion'];?>" id="email" autocomplete="off" name="email" required="" tabindex="1" type="text"> 
							</td>
						</tr>
						<tr>
							<td>
							<p class="contact"><label for="cant_dec">Cantidad de decimales:</label> </p>
							</td>
							<td>
							<input class="input_tres" value="<?php echo $c_array['cantidad_decimales'];?>" id="cant_dec" autocomplete="off" name="cant_dec" tabindex="2" type="text"> 
							</td>
						</tr>
						<tr>
							<td>
							<p class="contact"><label for="title_cambio">Tipo de cambio dolar</label></p> 
							</td>
						</tr>
						<tr>
							<td class="txt-right">
							<p class="contact"><label for="compra_dolar">Compra:</label></p>
							</td>
							<td>
								<p class="contact" style="display: inline;">$</p>
								<input class="input_dos" style="display: inline;" value="<?php echo $c_array['dolar_compra'];?>" autocomplete="off" name="compra_dolar" id="compra_dolar" tabindex="3" type="text"> 
							</td>
						</tr>
						<tr>
							<td class="txt-right">
							<p class="contact"><label for="venta_dolar">Venta:</label></p>
							</td>
							<td>
								<p class="contact" style="display: inline;">$</p>
								<input class="input_dos" style="display: inline;" value="<?php echo $c_array['dolar_venta'];?>" autocomplete="off" name="venta_dolar" id="venta_dolar" tabindex="4" type="text"> 
							</td>
						</tr>
						<tr>
							<td>
							<p class="contact"><label for="title_cambio">Montos de afiliados</label> </p>
							</td>
						</tr>
						<tr>
							<td class="txt-right">
							<p class="contact"><label for="compra_min">Mínimo:</label></p>
							</td>
							<td>
							<p class="contact" style="display: inline;">₡</p>
							<input class="input_dos" style="display: inline;" value="<?php echo $c_array['monto_minimo_compra_cliente'];?>" autocomplete="off" name="compra_min" id="compra_min" tabindex="5" type="text"> 
							</td>
						</tr>
						<tr>
							<td class="txt-right">
							<p class="contact"><label for="compra_inter">Intermedio:</label></p>
							</td>
							<td>
							<p class="contact" style="display: inline;">₡</p>
							<input class="input_dos" style="display: inline;" value="<?php echo $c_array['monto_intermedio_compra_cliente'];?>" autocomplete="off" name="compra_inter" id="compra_inter" tabindex="6" type="text"> 
							</td>
						</tr>
						<tr>
							<td>
							<p class="contact"><label for="cant_ses">Tiempo de sesión activa:</label> </p>
							</td>
							<td>
							<input class="input_tres" value="<?php echo $c_array['tiempo_sesion'];?>" id="cant_ses" autocomplete="off" name="cant_ses" tabindex="7" type="text"style="display: inline;"><p class="contact" style="display: inline;">s</p> 
							</td>
						</tr>
						<tr>
							<td>
							<p class="contact"><label for="iva_cant">Impuesto de Valor Agregado (IVA):</label> </p>
							</td>
							<td>
							<input class="input_tres" value="<?php echo $c_array['iva'];?>" id="iva_cant" autocomplete="off" name="iva_cant" tabindex="8" type="text" style="display: inline;"><p class="contact" style="display: inline;">%</p> 
							</td>
						</tr>
						<tr>
							<td>
							<p class="contact"><label for="ret_cant">Porcentaje Retención Datáfonos:</label> </p>
							</td>
							<td>
							<input class="input_tres" value="<?php echo $c_array['porcentaje_retencion_tarjetas_hacienda'];?>" id="iva_ret" autocomplete="off" name="ret_cant" tabindex="9" type="text" style="display: inline;"><p class="contact" style="display: inline;">%</p> 
							</td>
						</tr>
						<tr>
							<td>
								<p class="contact"><label for="sucursal">Empresa por defecto para traspaso de compras:</label> </p>
							</td>
							<td>
								<select class="input_uno" id="sucursal" name="sucursal">					
									<?php						
										foreach($Familia_Empresas as $Nombre_Empresa => $codigo_empresa)
										{
											echo "<option value='".$codigo_empresa."'";
											if($codigo_empresa == $c_array['codigo_empresa_traspaso_compras']){echo "selected";}
											echo">".$codigo_empresa." - ".$Nombre_Empresa."</option>";											
										}
									?>
								</select>
							</td>
						</tr>
						
						
						
					</table>
						
					<input class="boton" name="submit" id="submit" tabindex="7" value="Guardar" type="submit" <?php if (isset($c_array['flag'])) {echo "disabled";}?>>
					</form>
				</fieldset>
				<fieldset class="recuadro-clientes">
					<legend>Estado de clientes</legend>
					<p class="contact">Última actualización:</p>
					<p class="contact">
						<?php
							if(trim($c_array['ultima_actualizacion_estado_clientes'])==''){
								echo "Nunca";
							}else{
								echo $c_array['ultima_actualizacion_estado_clientes'];
							}
						?>
					</p>
					<input type="button" value="Actualizar Estado" class="boton" onclick="actualizarEstadoClientes()"/>
				</fieldset>
			</div>
			
				<!--<div id="timeout_show"></div>-->
        </div>		

		<!--Incluir footer-->
		<?php include 'Footer/Default_Footer.php';?>
	</body>
</html>