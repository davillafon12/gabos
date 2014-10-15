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
		<title>Registro De Articulos</title>
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
		<script src="<?php echo base_url('application/scripts/jquery.maskedinput.js'); ?>" type="text/javascript"></script>
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-1.2.6.min.js'); ?>"></script>
		<?php include '/../../scripts/ajax_verify_articulos_id.php';?>	
		
	</head>
	<body onload="timeout()">
		<!--Incluir imagen de cabezera-->
		<?php include '/../Header/Header_Picture.php';?>
		
		<!--Incluir menu principal-->
		<?php include '/../Header/selector_menu.php';?>

		<!--Incluir informacion log in-->
		<?php include '/../Header/Log_In_Information.php';?>
		<!--CSS ESTILO DEL FORMULARIO-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/articulos/style_registrar.css'); ?>">
	
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Registro De Artículos</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
			<div  class="form">
			<?php 
				$attributes = array('name' => 'registrar_articulos_form', 'class' => 'registrar_articulos_form-form');
				
				echo form_open_multipart('articulos/registrar/registra_Articulo', $attributes);              
			?>	 
		
			<fieldset class="recuadro">	
			<legend>Información</legend>
			<table>
			<tr>
				<td>
					<label for="articulo_codigo" class="labelMedium">Código:</label> &nbsp; 
					<input id="articulo_codigo" autocomplete="off" name="articulo_codigo" onblur="verify_ID();" placeholder=""  required="" tabindex="1"> 
				</td>
				<td>
					<div id="status" class="status"></div>
				</td>
				<td>
					<!--<label for="articulo_codigo" class="labelMedium">Código Barras:</label> &nbsp; -->
					<div id= "cod_Barras"></div>
				</td>
					<div class="picture" >
						<input type="file" name="userfile" size="10" accept=".jpg,.png,.ico,.bmp"/>
					</div>
			</tr>
			<tr>
				<td colspan="3">
					<label for="articulo_descripcion" class="labelMedium">Descripción:</label> &nbsp;
					<input id="articulo_descripcion" class="input_MediumDes" autocomplete="off"  name="articulo_descripcion" placeholder="descripción" required="" tabindex="2" type="text"> 
				</td>
			</tr>
			<tr>
				<td>
					<label for="articulos_cantidad"  class="labelMedium">Cantidad:</label> &nbsp; &nbsp;
					<input id="articulos_cantidad" class="input_Small" autocomplete="off" name="articulos_cantidad" required="" tabindex="4">
				</td>
				<td>
					<label for="articulos_cantidad_defectuoso"  class="labelMedium">Cantidad Defectuosa:</label> &nbsp; &nbsp;
					<input id="articulos_cantidad_defectuoso" class="input_Small" autocomplete="off" name="articulos_cantidad_defectuoso" required="" tabindex="5">
				</td>
				<td>
				<input type="checkbox" name="exento" id="exento"  value="1" tabindex="6" ><label class="labelMedium" > Exento de IVI</label> <br>
				</td>				
			</tr>		
			<tr>
				<td>
	    			<label for="sucursal"  class="labelMedium">Empresa:</label>
	    			<select name="sucursal" class="styleSelect" tabindex="7">
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
	    			<label for="familia"  class="labelMedium">Familia:</label>
	    			<select name="familia" class="styleSelect" tabindex="8">
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
					<label for="descuento"  class="montos">Descuento:</label>
					<input id="descuento" class="montos" autocomplete="off" name="descuento" placeholder="xx"  tabindex="9">
				</td>
			</tr>				
			<tr>
				<td>
					<label for="costo"  class="montos">Costo:</label>
					<input id="costo" class="montos" autocomplete="off" name="costo" required=""  tabindex="10">
				</td>				
				<td>
					<label for="precio1"  class="montos">Precio 1:</label>
					<input id="precio1" class="montos" autocomplete="off" name="precio1" required=""  tabindex="11">
				</td>
				<td>
					<label for="precio2"  class="montos">Precio 2:</label>
					<input id="precio2" class="montos" autocomplete="off" name="precio2"  required="" tabindex="12">
				</td>
							
			</tr>
			<tr>
				<td>
					<label for="precio3"  class="montos">Precio 3:</label>
					<input id="precio3" class="montos" autocomplete="off" name="precio3"   tabindex="13">
				</td>	
				<td>
					<label for="precio4"  class="montos">Precio 4:</label>
					<input id="precio4" class="montos" autocomplete="off" name="precio4"  tabindex="14">
				</td>
				<td>
					<label for="precio5"  class="montos">Precio 5:</label>
					<input id="precio5" class="montos" autocomplete="off" name="precio5"  tabindex="15">
				</td>									
			</tr>			
			</table>
			</fieldset>

			<div class="divButton">			
				<input class="buttom" name="submit" id="submit" onsubmit="" tabindex="16" value="Registrar" type="submit" disabled>
				<a href='<?php echo base_url('home')?>' class='boton_volver'>Volver</a>
			</div>
		</form>
		</div>			
        </div>		

		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>