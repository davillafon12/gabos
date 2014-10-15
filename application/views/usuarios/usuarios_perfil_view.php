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
		<!--SCRIPT DE EXPIRACION DE LA SESION-->
		<?php include '/../Header/log_out_from_browser_Script.php';?>
		<!--SCRIPT DE AJAX JQUERY-->

		<script src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('application/scripts/jquery.maskedinput.js'); ?>" type="text/javascript"></script>
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-1.2.6.min.js'); ?>"></script>
		<?php include '/../../scripts/ajax_verify_usuarios_id.php';?>	
		
	</head>
	<body onload="timeout()">
		<!--Incluir imagen de cabezera-->
		<?php include '/../Header/Header_Picture.php';?>
		
		<!--Incluir menu principal-->
		<?php include '/../Header/selector_menu.php';?>

		<!--Incluir informacion log in-->
		<?php include '/../Header/Log_In_Information.php';?>
		<!--CSS ESTILO DEL FORMULARIO-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/usuarios/style_registrar.css'); ?>">
	
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Registro De Usuarios</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
			<div  class="form">
			<?php 
				$attributes = array('name' => 'registrar_usuarios_form', 'class' => 'registrar_usuarios_form-form');
				echo form_open_multipart('usuarios/editar/actualizarMiPerfil', $attributes);                         
			?>	 
		
			<fieldset class="recuadro">	
			<legend>Información Personal</legend>

			<table class= "sample">
			<tr>
				<td>
					<label for="tipo_cedula" class="labelMedium">Cédula:</label> 
					<label for="tipo_cedula" class="labelMedium"><?php echo $Usuario_Tipo_Cedula;?></label> 

					
				</td>
				<td>
					<label for="cedula_usuarioo" class="labelMedium"><?php echo $Usuario_Cedula;?></label> 
					<input id="cedula_usuario" hidden="hidden" value="<?php echo $Usuario_Cedula;?>" name="cedula_usuario"> 
				<div class="picture" >
					<img src="<?php echo $Imagen_Usuario;?>" alt="Smiley face" height="100" width="100">
				</div>
				</td>
				<td>
					<label for="sucursal"  class="labelMedium">Sucursal:</label>
					<label for="sucursal"  class="labelMedium"><?php echo $Sucursal_Nombre;?></label>
				</td>	
			</tr>
			<tr>
				<td colspan="2">
					<label for="nombre_usuario" class="labelMedium">Nombre:</label>
					<label for="nombre_usuario" class="labelMedium"><?php echo $Nombre_Usuario." ".$Apellidos_Usuario;?></label>
				</td>
				<td rowspan="6">
						<div>
							<legend>Observaciones</legend>
							<textarea id="observaciones" autocomplete="off" class="observaciones" placeholder="" tabindex="17"name="observaciones" cols="15" rows="3" maxlength="300" disabled= "disabled"><?php echo $Observaciones_Usuario;?></textarea> 
						</div>
					</td>	

			</tr>
			<tr>
				<td>
					<label for="celular_usuario"  class="labelMedium">Celular:</label>
					<label for="celular_usuario"  class="labelMedium"><?php echo $Usuario_Celular;?></label> 
				</td>
				<td>
					<label for="telefono_usuario"  class="labelMedium">Telefono:</label>
					<label for="telefono_usuario"  class="labelMedium"><?php echo $Usuario_Telefono;?></label>
				</td>
			</tr>
			<tr>
				<td>
					<label for="email_usuario"  class="labelMedium">Email:</label>
					<label for="email_usuario"  class="labelMedium"><?php echo $Usuario_Correo_Electronico;?></label>
				</td>
				<td>
				<label for="usuario_rango" class="labelMedium">Rango Usuario:</label>			
				<label for="usuario_rango"  class="labelMedium"><?php echo $Usuario_Rango;?></label>
				</td>
				<tr>
					<td>
						<label for="usuario_nombre_usuario"  class="montos" >Nombre Usuario:</label>
					</td>	
					<td>
						<label for="usuario_nombre_usuario"  class="montos" ><?php echo $Usuario_Nombre_User;?></label>
					</td>	
				</tr>				
				<tr>
					<td>
						<label for="usuario_password"  class="montos">Password:</label>

					</td>
					<td>
						<input id="usuario_password" class="updateUser" required="" autocomplete="off" name="usuario_password" tabindex="1" type="password">
					</td>
				</tr>			
				<tr>
					
				</tr>	
			</tr>	
			</table>

			<div class="divButton">			
				<input class="buttom" name="submit" id="submit" onsubmit="" tabindex="2" value="Actualizar" type="submit">
				<a href='<?php echo base_url('home')?>' class='boton_volver'>Volver</a>	
			</div>
		</form>
        </div>
</div>		

		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>