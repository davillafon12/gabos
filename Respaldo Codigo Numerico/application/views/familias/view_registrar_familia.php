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
		<title>Registro De Familias</title>
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
		
		<!--CSS ESTILO DEL FORMULARIO-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/familias/style_registrar.css'); ?>">
		<!--SCRIPT DE AJAX JQUERY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-1.2.6.min.js'); ?>"></script>
		<?php include '/../../scripts/ajax_verify_familia_id.php';?>
		
		
	</head>
	<body >
		<!--Incluir imagen de cabezera-->
		<?php include '/../Header/Header_Picture.php';?>
		
		<!--Incluir menu principal-->
		<?php include '/../Header/selector_menu.php';?>

		<!--Incluir informacion log in-->
		<?php include '/../Header/Log_In_Information.php';?>
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Registro De Familias</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
				      <div  class="form">
			<?php 
				$attributes = array('name' => 'registrar_empresa_form', 'class' => 'registrar_empresa_form-form');
				
				echo form_open('familias/registrar/registraFamilia', $attributes); 
				
			?>	 
				<p class="titulo">Información de la familia</p>
				
			    <table>
				<!--<tr>
				<td>
				    <p class="contact"><label for="codigo">Código: </label></p> 
    			    <input id="codigo" autocomplete="off" onblur="verify_ID();" name="codigo" class="input_codigo" placeholder="Código de la familia" required="" tabindex="1" type="number" min="0" max="9999" >
			    </td>
				<td>
					<div id="status" class="status"></div>
				</td>
				</tr>-->
				<tr>
				<td>
    			<p class="contact"><label for="name">Nombre:</label></p> 
    			<input id="name" autocomplete="off" name="name" placeholder="Nombre de la familia" required="" tabindex="2" type="text"> 
				</td>
    			</tr>
				<!--<tr>
				<td>
    			<p class="contact"><label for="descuento">Descuento:</label></p> 
    			<input id="descuento" autocomplete="off" name="descuento" placeholder="Descuento de la familia" tabindex="3" type="number" min="0" max="100"> 
                </td>
				</tr>-->
				<tr>
				<td>
    			<p class="contact"><label for="sucursal">Empresa:</label></p> 
    			<select name="sucursal">
				<?php 					
					foreach($Familia_Empresas as $Nombre_Empresa => $codigo_empresa)
					{
						echo "<option value='".$codigo_empresa."'";
						echo">".$codigo_empresa." - ".$Nombre_Empresa."</option>";
					}
				?>
				</select> 
                </td>
				</tr>
				<tr>
				<td>
                <p class="contact"><label for="observaciones">Observaciones:</label></p> 
    			<textarea id="observaciones" autocomplete="off" class="observaciones" placeholder="" name="observaciones" tabindex="5" cols="25" rows="5" maxlength="300" ></textarea> 
    			<p class="advertencia_longitud">Máximo 300 caracteres</p> 
				</td>
				</tr> 
                <br>
				</table>
            <input class="buttom" name="submit" id="submit" tabindex="6" value="Registrar" type="submit" >
			<a href='<?php echo base_url('familias/familias')?>' class='boton_volver'>Volver</a>
<br>
<br>			
   </form> 
</div>
				<!--<div id="timeout_show"></div>-->
        </div>		

		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>