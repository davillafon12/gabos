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
		<title>Edición De Empresas</title>
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
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/empresas/style_registrar.css'); ?>">
		<!--SCRIPT DE AJAX JQUERY-->
		<script type="text/javascript" src="<?php //echo base_url('application/scripts/jquery-1.2.6.min.js'); ?>"></script>
		<?php include '/../../scripts/ajax_verify_empresa_id.php';?>
		
		
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
			<p class="titulo_wrapper">Edición De Empresas</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
				      <div  class="form">
			<?php 
				$attributes = array('name' => 'registrar_empresa_form', 'class' => 'registrar_empresa_form-form');
				
				echo form_open('empresas/editar/actualizarEmpresa', $attributes); 
				
			?>	 
				<p class="titulo">Información de la empresa</p>
				
			    <table>				
    			    <input id="codigo" autocomplete="off" name="codigo" class="input_codigo" type="hidden" value="<?php echo $Empresa_codigo;?>">
			  	<tr>
					<td>
						<p class="contact"><label for="cedula_ju">Cédula Jurídica:</label></p> 
						<input id="cedula_ju" autocomplete="off" name="cedula_ju" placeholder="Cédula Jurídica de la empresa" required="" tabindex="1" type="text" value="<?php echo $Empresa_Cedula;?>"> 
					</td>
					<td>
						<p class="contact"><label for="name">Nombre:</label></p> 
						<input id="name" autocomplete="off" name="name" placeholder="Nombre de la empresa" required="" tabindex="2" type="text" value="<?php echo $Empresa_nombre;?>"> 
					</td>
				</tr>
				<tr>
					<td>
						<p class="contact"><label for="telefono">Teléfono:</label></p> 
						<input id="telefono" autocomplete="off" name="telefono" placeholder="Teléfono de la empresa" tabindex="3" type="text" value="<?php echo $Empresa_Telefono;?>"> 
					</td>
					<td>
						<p class="contact"><label for="fax">Fax:</label></p> 
						<input id="fax" autocomplete="off" name="fax" placeholder="Fax de la empresa" tabindex="4" type="text" value="<?php echo $Empresa_Fax;?>"> 
					</td>
				</tr>				
				<tr>
					<td>
						<p class="contact"><label for="email">Email:</label></p> 
						<input id="email" autocomplete="off" name="email" placeholder="Email de la empresa" tabindex="5" type="text" value="<?php echo $Empresa_Email;?>"> 
					</td>
					<td>
						<p class="contact"><label for="administrador">Administrador(a):</label></p> 
						<input autocomplete="off" name="administrador" placeholder="Nombre del administrador(a)" tabindex="7" type="text" value="<?php echo $Empresa_Direccion;?>">
					</td>
				</tr>
				<tr>
					<td>
						<p class="contact"><label for="direccion">Dirección:</label></p> 
						<input id="direccion" autocomplete="off" name="direccion" placeholder="Dirección de la empresa"  tabindex="6" type="text" value="<?php echo $Empresa_Administrador;?>"> 
					</td>
				</tr>				
				<tr>
					<td>
						<p class="contact"><label for="leyenda">Leyenda de la D.G.T.D.:</label></p> 
						<textarea id="leyenda" autocomplete="off" class="observaciones" placeholder="" name="leyenda" tabindex="8" cols="25" rows="5" maxlength="150" ><?php echo $Empresa_Tributacion;?></textarea> 
						<p class="advertencia_longitud">Máximo 150 caracteres</p> 
					</td>
					<td>
						<p class="contact_observaciones"><label for="observaciones">Observaciones:</label></p> 
						<textarea id="observaciones" autocomplete="off" class="observaciones" placeholder="" name="observaciones" tabindex="9" cols="25" rows="5" maxlength="300" ><?php echo $Empresa_Observaciones;?></textarea> 
						<p class="advertencia_longitud">Máximo 300 caracteres</p> 
					</td>
				</tr>
                <br>
				</table>
            <input class="buttom" name="submit" id="submit" onsubmit="" tabindex="10" value="Actualizar" type="submit">
			<a href='<?php echo base_url('empresas/editar')?>' class='boton_volver'>Volver</a>
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