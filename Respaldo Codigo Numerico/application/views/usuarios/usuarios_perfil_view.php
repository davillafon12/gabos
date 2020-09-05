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
		<title>Mi perfil</title>
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
		
		<?php include '/../../scripts/ajax_verify_usuarios_id.php';?>	
		
	</head>
	<body >
		<!--Incluir imagen de cabezera-->
		<?php include '/../Header/Header_Picture.php';?>
		
		<!--Incluir menu principal-->
		<?php include '/../Header/selector_menu.php';?>

		<!--Incluir informacion log in-->
		<?php include '/../Header/Log_In_Information.php';?>
		<!--CSS ESTILO DEL FORMULARIO-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/usuarios/style_perfil.css'); ?>">
	
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Mi Perfil</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
			
			
			<div  class="form">
				<?php 
					$attributes = array('name' => 'mi_perfil_form', 'class' => 'mi_perfil_form');
					
					echo form_open_multipart('usuarios/editar/actualizarMiPerfil', $attributes); 
					$opcCedulaMetodo = 'id="tipo_Cedula", onblur="tipoCedula()", class="styleSelect", tabindex="1" ';
					$opc_Cedula= array ( 
					  'nacional'   =>  'Nacional' , 
					  'residencia'     =>  'Residencia' , 
					  'juridica'     =>  'Juridica' , 
					);     
					$opc_rango= array ( 
					  'vendedor'   =>  'Vendedor' , 
					  'cajero'     =>  'Cajero' , 
					  'avanzado'     =>  'Usuario Avanzado' , 
					  'administra'     =>  'Administrador' , 
					);   


				?>	 
			
					<fieldset class="recuadro">	
					<legend>Información Personal</legend>
					
					<input type="hidden" name="codigo_usuario" value="<?php echo $Usuario_Codigo_Modificar;?>">
					<input type="hidden" name="sucursal_usuario_original" value="<?php echo $Sucursal_Codigo;?>">
					<input type="hidden" name="cedula_usuario"  value="<?php echo $Usuario_Cedula;?>" >
					<table>
						<tr>
							<td>
								<label for="tipo_cedula_usuario" class="labelMedium">Cédula:</label>							
							</td>
							<td>								
								<input value="<?php echo $Usuario_Cedula;?>" disabled>								 
							</td>						
						</tr>
						<tr>
							<td>
								<label for="nombre_usuario" class="labelMedium">Nombre:</label>						
							</td>
							<td>	
								<input id="nombre_usuario" class="input_Medium" autocomplete="off"  name="nombre_usuario" placeholder="" required="" tabindex="4" type="text" value="<?php echo $Nombre_Usuario;?>"> 
							</td>
							<td>
								<label for="apellidos_usuario" class="labelMedium2">Apellidos:</label>							
							</td>
							<td>
								<input id="apellidos_usuario" class="input_Medium" autocomplete="off" onblur="creaNombreUsuario();" name="apellidos_usuario"  placeholder=""  required="" tabindex="5" type="text" value="<?php echo $Apellidos_Usuario;?>"> 
							</td>
							<td colspan="2">
								<div class="picture" >
									<input type="file" name="userfile" size="10" accept=".jpg,.png,.ico,.bmp"/>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<label for="celular_usuario"  class="labelMedium">Celular:</label>							
							</td>
							<td>
								<input id="celular_usuario" class="input_Small" autocomplete="off" name="celular_usuario" required="" placeholder=""  tabindex="6" value="<?php echo $Usuario_Celular;?>">
							</td>
							<td>
								<label for="telefono_usuario"  class="labelMedium">Telefono:</label>							
							</td>
							<td>
								<input id="telefono_usuario" class="input_Small" autocomplete="off" name="telefono_usuario" placeholder=""  tabindex="7" value="<?php echo $Usuario_Telefono;?>"><br>						
							</td>
							<td>
								<label for="sucursal"  class="labelMedium">Empresa:</label>							
							</td>
							<td>								
								<label class="labelMedium"><?php echo $Sucursal_Nombre;?></label>
							</td>
							
						</tr>
						<tr>
							<td>
								<label for="email_usuario"  class="labelMedium">Email:</label> 							
							</td>
							<td>
								<input id="email_usuario" class="input_Medium" autocomplete="off" name="email_usuario" type="email" tabindex="9" value="<?php echo $Usuario_Correo_Electronico;?>">
							</td>
							<td>
								<label for="usuario_rango" class="labelMedium">Rango Usuario:</label>						
							</td>
							<td>
								<label class="labelMedium"><?php echo $opc_rango[$Usuario_Nivel];?></label>
							</td>	
							
						</tr>
						<tr>
							<td>
								<label class="labelMedium">Fecha Ingreso:</label>
							</td>
							<td>
								<label class="labelMedium"><?php echo $Usuario_Fecha_Ingreso;?></label>
							</td>							
						</tr>						
					</table>

					</fieldset>

					<fieldset class="recuadro2">	
					<legend>Area Logueo</legend>
					
					<table>
						<tr>
							<td>
								<label for="usuario_nombre_usuario"  class="montos" >Nombre Usuario:</label>
							</td>
							<td>
								<div id="statusNombre" class="statusNombre"></div>
							</td>		
						</tr>	
						<tr>
							<td>
								<label class="labelMedium"><?php echo $Usuario_Nombre_User;?></label>								
							</td>
						</tr>	
						<tr>
							<td>
								<label for="usuario_password_actual"  class="montos">Password Actual:</label>
							</td>
						</tr>
						<tr>
							<td>
								<input id="usuario_password_actual" class="montos" autocomplete="off" name="usuario_password_actual"  type="password">								 
							</td>
						</tr>	
						<tr>
							<td>
								<label for="usuario_password"  class="montos">Password:</label>
							</td>
						</tr>
						<tr>
							<td>
								<input id="usuario_password" class="montos" autocomplete="off" name="usuario_password"  type="password">
								<p class="advertencia_longitud" >Dejar en blanco si no se desea cambiar</p> 
							</td>
						</tr>				
					</table>
					</fieldset>

					<fieldset class="recuadro3">	
					<legend>Observaciones</legend>
					
					<textarea id="observaciones" autocomplete="off" class="observaciones" placeholder="" tabindex="13"name="observaciones" cols="25" rows="7" maxlength="300" disabled><?php echo $Observaciones_Usuario;?></textarea> 
					<p class="advertencia_longitud">Máximo 300 caracteres</p> 
					
					</fieldset>
					
					<fieldset class="foto_recuadro">	
					<legend>Foto</legend>
					
					<img src="<?php echo $Imagen_Usuario;?>" height="150">
					
					</fieldset>					
					
					<div class="divButton">		
						<input class="buttom" value="Volver" type="button" onclick="window.location.assign('<?php echo base_url('usuarios/editar')?>')">
						<input class="buttom" name="submit" id="submit" onsubmit="" tabindex="18" value="Actualizar" type="submit">						
					</div>
					
				</form>
				
			</div>
</div>		

		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>