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
		<title>Registro De Usuarios</title>
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
		<script src="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.min.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('application/scripts/jquery.maskedinput.js'); ?>" type="text/javascript"></script>
		
		<script type="text/javascript" src="<?php echo base_url('application/scripts/usuarios/usuarios_tools.js'); ?>"></script>
		
		
		<script>
			<?php
				echo "var ruta_imagen = '".base_url('application/images/scripts/loader.gif')."';
				      var ruta_script = '".base_url('application/controllers/clientes/registrar/es_Cedula_Utilizada')."';	
				      var Ruta_Base = '".base_url('')."';
				      var ruta_base_imagenes_script = '".base_url('application/images/scripts')."';";
			?>
		</script>
		
	</head>
	<body >
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
					
					echo form_open_multipart('usuarios/registrar/registrar_Usuarios', $attributes); 
					$opcCedulaMetodo = 'id="tipo_Cedula", onblur="tipoCedula()", class="styleSelect", tabindex="1" ';
					$opc_Cedula= array ( 
					  'nacional'   =>  'Nacional' , 
					  'residencia'     =>  'Residencia' , 
					  'juridica'     =>  'Jurídica' , 
					  'pasaporte' => 'Pasaporte'
					);     
					$opc_rango= array ( 
					  'vendedor'   =>  'Vendedor' , 
					  'cajero'     =>  'Cajero' , 
					  'avanzado'     =>  'Usuario Avanzado' , 
					  'administrador'     =>  'Administrador' , 
					);   


				?>	 
				
					<fieldset class="recuadro">	
					<legend>Información Personal</legend>
					
				
					<table>
						<tr>
							<td>
								<label for="tipo_cedula_usuario" class="labelMedium">Cédula:</label>							
							</td>
							<td>
								<?php echo form_dropdown ( 'tipo_cedula_usuario' , $opc_Cedula ,  'nacional', $opcCedulaMetodo);  ?>
								<input id="cedula_usuario" autocomplete="off" name="cedula_usuario"  placeholder="" required="" tabindex="2"> 
							</td>						
						</tr>
						<tr>
							<td>
								<label for="nombre_usuario" class="labelMedium">Nombre:</label>						
							</td>
							<td>	
								<input id="nombre_usuario" class="input_Medium" autocomplete="off" required="" name="nombre_usuario" placeholder="" required="" tabindex="4" type="text"> 
							</td>
							<td>
								<label for="apellidos_usuario" class="labelMedium2">Apellidos:</label>							
							</td>
							<td>
								<input id="apellidos_usuario" class="input_Medium" required="" autocomplete="off" onblur="creaNombreUsuario();" name="apellidos_usuario"  placeholder=""  required="" tabindex="5" type="text"> 
							</td>
							<td>
								<label for="sucursal"  class="labelMedium">Empresa:</label>							
							</td>
							<td>
								<select name="sucursal" class="styleSelect" tabindex="8">
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
								<label for="celular_usuario"  class="labelMedium">Celular:</label>							
							</td>
							<td>
								<input id="celular_usuario" class="input_Small" autocomplete="off" name="celular_usuario" placeholder=""  tabindex="6">
							</td>
							<td>
								<label for="telefono_usuario"  class="labelMedium">Telefono:</label>							
							</td>
							<td>
								<input id="telefono_usuario" class="input_Small" autocomplete="off" name="telefono_usuario" placeholder=""  tabindex="7"><br>						
							</td>
							<td colspan="2">
								<div class="picture" >
									<input type="file" name="userfile" size="10" accept=".jpg,.png,.ico,.bmp"/>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<label for="email_usuario"  class="labelMedium">Email:</label> 							
							</td>
							<td>
								<input id="email_usuario" class="input_Medium" autocomplete="off" name="email_usuario" type="email" tabindex="9">
							</td>
							<td>
								<label for="usuario_rango" class="labelMedium">Rango Usuario:</label>						
							</td>
							<td>
								<?php echo form_dropdown ( 'usuario_rango' , $opc_rango ,  'vendedor', 'class="styleSelect", tabindex="11", id="select_rango", onchange="actualizarPermisos()"' );  ?>
							</td>
							
							
						</tr>			
					</table>

					</fieldset>
				

					<fieldset class="recuadro2">	
					<legend>Area Logueo</legend>
					
					<table>
						<tr>
							<td>
								<label for="usuario_nombre_usuario"  class="montos">Nombre Usuario:</label>
							</td>
							<td>
								<div id="statusNombre" class="statusNombre"></div>
							</td>		
						</tr>	
						<tr>
							<td>
								<input id="usuario_nombre_usuario" required="" class="montos" onblur="verify_Name();" autocomplete="off" name="usuario_nombre_usuario"  tabindex="12" type="text">
							</td>
						</tr>					
						<tr>
							<td>
								<label for="usuario_password"  class="montos">Password:</label>
							</td>
						</tr>
						<tr>
							<td>
								<input id="usuario_password" class="montos" required="" autocomplete="off" name="usuario_password"  type="password" readonly />
							</td>
						</tr>				
					</table>
					</fieldset>
			
					<fieldset class="recuadro3">	
					<legend>Observaciones</legend>
					
					<textarea id="observaciones" autocomplete="off" class="observaciones" placeholder="" tabindex="13"name="observaciones" cols="25" rows="7" maxlength="300" ></textarea> 
					<p class="advertencia_longitud">Máximo 300 caracteres</p> 
					
					</fieldset>
				
					<fieldset class="recuadro4 cuatro_registrar">	
					<legend>Permisos</legend>					
							<table id="tablaPermisos">
								<?php
									include ('/../../controllers/usuarios/permisos.php');
									$fila = 0;
									echo "<tr>";
									foreach($permisos as $valor => $contenido){
										if($fila % 5 == 0){echo "</tr><tr>";}
										$fila++;
										echo "<td><input class='input-permisos-checkbox' type='checkbox' name='permisos[]' id='ch_$valor' value='$valor'/></td><td><label class='permisos-label'>$contenido</label></td>";
									}
									echo "</tr>";
									
									echo "<script>
												var permisosVendedor = ".json_encode($permisosVendedor).";
												var permisosCajero = ".json_encode($permisosCajero).";
												var permisosAdmin = ".json_encode($permisosAdmin).";
												var permisosAvanz = ".json_encode($permisosAvanz).";
												actualizarPermisos();
											</script>";
								?>
							</table>
					</fieldset>	
				
					<div class="divButton">		
						<input class="buttom" value="Volver" type="button" onclick="window.location.assign('<?php echo base_url('home')?>')">
						<input class="buttom" name="submit" id="submit" onsubmit="" tabindex="18" value="Registrar" type="submit" disabled>						
					</div>
			
				</form>
				
			</div>		
		</div>
		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>