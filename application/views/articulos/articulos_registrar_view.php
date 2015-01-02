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
		<title>Traspaso Individual De Artículos</title>
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
		<!--SCRIPT DE NOTY-->		
		<script src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE NUMERIC-->		
		<script src="<?php echo base_url('application/scripts/jquery.numeric.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE VERIFICACION DE CODIGOS-->
		<script src="<?php echo base_url('application/scripts/articulos/verificar_codigos_tools.js'); ?>" type="text/javascript"></script>
		<!--CSS ESTILO ESPECIFICO DE LA PAG-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/articulos/style_traspaso_individual.css'); ?>">
		
		
		<?php include '/../../scripts/ajax_verify_articulos_id.php';?>	
		
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
			<p class="titulo_wrapper">Traspaso Individual De Artículos</p>
			<hr class="division_wrapper">
			<?php 
				$attributes = array('name' => 'registrar_articulos_form', 'class' => 'registrar_articulos_form-form');
				
				echo form_open_multipart('articulos/registrar/registra_Articulo', $attributes);              
			?>	
			<div class="contenedor">
				<fieldset>
					<legend>Información Básica</legend>
					<table>
						<tr>
							<td>
								<label for="codigo_bodega" class="contact">Código de Bodega:</label>
							</td>
							<td>
								<input id="codigo_bodega" class="input_uno" autocomplete="off" name="codigo_bodega" placeholder=""  required="" onkeyup="verificarCodigoBodega(this.value)"> 
								<div id="status_bodega" class="status"></div>
							</td>
							<td>
								<p class="contact">Cantidad en Bodega:</p>
							</td>
							<td>
								<p class="contact" id="cantidad_bodega"></p>
							</td>
							<td colspan="2">
								<div id= "cod_Barras" class="cod_barras"></div>
							</td>
						</tr>
						<tr>
							<td colspan="6">
								<hr></hr>
							</td>
						</tr>
						<tr>
							<td>
								<label for="articulo_codigo" class="contact">Código:</label>
							</td>
							<td>
								<input id="articulo_codigo" class="input_uno" autocomplete="off" name="articulo_codigo" onblur="verificarCodigoArticulo()" placeholder=""  required="" disabled> 
								<div id="status" class="status"></div>
							</td>
							<td>
								<label for="articulo_descripcion" class="contact">Descripción:</label>
							</td>
							<td colspan="3">
								<input id="articulo_descripcion" class="input_descripcion" autocomplete="off"  name="articulo_descripcion" required="" type="text" disabled> 
							</td>
						</tr>
						<tr>
							<td>
								<label for="articulos_cantidad"  class="contact">Cantidad:</label>				
							</td>
							<td>
								<input id="articulos_cantidad" class="input_uno" autocomplete="off" name="articulos_cantidad" required="" disabled>
							</td>
							<td>
								<label for="articulos_cantidad_defectuoso"  class="contact">Cantidad Defectuosa:</label> 					
							</td>
							<td>
								<input id="articulos_cantidad_defectuoso" class="input_uno" autocomplete="off" name="articulos_cantidad_defectuoso" required="" disabled> 
							</td>
							<td>
								<label class="contact" > Exento de IVI</label>
							</td>
							<td>
								<input type="checkbox" name="exento" id="exento"  value="1" disabled>
							</td>
						</tr>
						<tr>
							<td>
								<label for="sucursal"  class="contact">Empresa:</label>					
							</td>
							<td>
								<select name="sucursal" class="input_dos" id="sucursal" onchange="verificarCodigoArticulo()" disabled>
									<?php 					
										foreach($Familia_Empresas as $Nombre_Empresa => $codigo_empresa)
										{
											echo "<option value='".$codigo_empresa."'";
											echo">".$codigo_empresa." - ".$Nombre_Empresa."</option>";
										}
									?>
								</select> 
							</td>
							<td>
								<label for="familia"  class="contact">Familia:</label>
	    							
							</td>
							<td>
								<select name="familia" class="input_dos" id="familia" disabled>
									<?php 					
										foreach($Familias as $Nombre_Familia => $codigo_familia)
										{
											echo "<option value='".$codigo_familia."'";
											echo">".$codigo_familia." - ".$Nombre_Familia."</option>";
										}
									?>
								</select>  	
							</td>
							<td>
								<label for="descuento"  class="contact">Descuento:</label>					
							</td>
							<td>
								<input id="descuento" class="input_uno" autocomplete="off" name="descuento" disabled>
							</td>
						</tr>
						<tr>
							<td>
								<label for="foto_articulo"  class="contact">Foto:</label>
							</td>
							<td>
								<input type="file" id="foto_articulo" class="input_dos" name="userfile" size="10" accept=".jpg,.png,.ico,.bmp" disabled>
							</td>
						</tr>
					</table>
				</fieldset>
				<fieldset class="field_precios">
					<legend>Precios</legend>
					<table>
						<tr>
							<td>
								<label for="costo"  class="contact">Costo:</label>					
							</td>
							<td>
								<input id="costo" class="input_uno" autocomplete="off" name="costo" required="" disabled>
							</td>
							<td>
								<label for="precio1"  class="contact">Precio 1:</label>					
							</td>
							<td>
								<input id="precio1" class="input_uno" autocomplete="off" name="precio1" required="" disabled>
							</td>
							<td>
								<label for="precio2"  class="contact">Precio 2:</label>					
							</td>
							<td>
								<input id="precio2" class="input_uno" autocomplete="off" name="precio2"  required="" disabled>
							</td>
						</tr>
						<tr>
							<td>
								<label for="precio3"  class="contact">Precio 3:</label>					
							</td>
							<td>
								<input id="precio3" class="input_uno" autocomplete="off" name="precio3" disabled>
							</td>
							<td>
								<label for="precio4"  class="contact">Precio 4:</label>					
							</td>
							<td>
								<input id="precio4" class="input_uno" autocomplete="off" name="precio4" disabled>
							</td>
							<td>
								<label for="precio5"  class="contact">Precio 5:</label>					
							</td>
							<td>
								<input id="precio5" class="input_uno" autocomplete="off" name="precio5" disabled>
							</td>
						</tr>
					</table>
				</fieldset>
				<div class="divButton">			
					<input class="boton" name="submit" value="Registrar" type="submit">
					<a class="boton_a" href='<?php echo base_url('home')?>' class='boton_volver'>Volver</a>
				</div>
			</form>
			</div><!-- contenedor -->
			
			
		</div>		

		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>