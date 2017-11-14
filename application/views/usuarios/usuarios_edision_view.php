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
		<title>Edición De Usuarios</title>
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
		<?php include FCPATH.'application/views/Header/log_out_from_browser_Script.php';?>
		<!--SCRIPT DE AJAX JQUERY-->

		<script src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('application/scripts/jquery.maskedinput.js'); ?>" type="text/javascript"></script>
		<script src="<?php echo base_url('application/scripts/usuarios/edicion_de_usuario_tools.js'); ?>" type="text/javascript"></script>
		<?php //include '/../../scripts/ajax_verify_usuarios_id.php';?>	
		<!--CSS ESTILO DEL FORMULARIO-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/usuarios/style_registrar.css'); ?>">
	</head>
	<body>
		<!--Incluir imagen de cabezera-->
		<?php include FCPATH.'application/views/Header/Header_Picture.php';?>
		
		<!--Incluir menu principal-->
		<?php include FCPATH.'application/views/Header/selector_menu.php';?>

		<!--Incluir informacion log in-->
		<?php include FCPATH.'application/views/Header/Log_In_Information.php';?>
		
	
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Edición De Usuarios</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
			<div  class="form">
				<?php 
					$attributes = array('name' => 'registrar_usuarios_form', 'class' => 'registrar_usuarios_form-form');
					
					echo form_open_multipart('usuarios/editar/actualizarUsuario', $attributes); 
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
					  'administra'     =>  'Administrador' , 
					);   


				?>	 
			
					<fieldset class="recuadro">	
					<legend>Información Personal</legend>
					
					<input type="hidden" name="codigo_usuario" value="<?php echo $Usuario_Codigo_Modificar;?>">
					<input type="hidden" name="sucursal_usuario_original" value="<?php echo $Sucursal_Codigo;?>">
					
					<table>
						<tr>
							<td>
								<label for="tipo_cedula_usuario" class="labelMedium">Cédula:</label>							
							</td>
							<td>
								<?php echo form_dropdown ( 'tipo_cedula_usuario' , $opc_Cedula ,  $Usuario_Tipo_Cedula, $opcCedulaMetodo);  ?>
								<input id="cedula_usuario" autocomplete="off" name="cedula_usuario"  placeholder="" required="" tabindex="2" style="width: 100px;" value="<?php echo $Usuario_Cedula;?>"> 
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
								<?php 					
									foreach($Familia_Empresas as $Nombre_Empresa => $codigo_empresa)
									{										
										if($Sucursal_Codigo==$codigo_empresa){
											echo "<label for='sucursal'  class='labelMedium'>".$codigo_empresa." - ".$Nombre_Empresa."</label>";
										}
									}
								?>
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
								<?php echo form_dropdown ( 'usuario_rango' , $opc_rango ,  $Usuario_Nivel, 'class="styleSelect", tabindex="11", id="select_rango", onchange="actualizarPermisos()"' );  ?>
							</td>	
							
						</tr>
						<tr>
							<td>
								<label class="labelMedium">Fecha Ingreso:</label>
							</td>
							<td>
								<label class="labelMedium"><?php echo $Usuario_Fecha_Ingreso;?></label>
							</td>
							<td>
								<label for="fecha_cesantia"  class="labelMedium">Fecha Cesantía:</label>
							</td>
							<td>
								<input id="fecha_cesantia" value="<?php echo $Usuario_Fecha_Cesantia;?>" class="input_Small" autocomplete="off" name="fecha_cesantia" type="date" tabindex="10">
							</td>
							<td>
								<label for="fecha_recontratación"  class="labelMedium">Fecha Recontratación:</label>
							</td>
							<td>
								<input id="fecha_recontratación" value="<?php echo $Usuario_Fecha_Recontratacion;?>" class="input_Small" autocomplete="off" name="fecha_recontratacion" type="date" tabindex="10">
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
								<input id="usuario_nombre_usuario" required="" class="montos" onblur="verify_Name();" autocomplete="off" name="usuario_nombre_usuario"  tabindex="12" type="text" value="<?php echo $Usuario_Nombre_User;?>">
								<input id="nombre_usuario_original" name="nombre_usuario_original" type="hidden" value="<?php echo $Usuario_Nombre_User;?>"/>
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
					
					<textarea id="observaciones" autocomplete="off" class="observaciones" placeholder="" tabindex="13"name="observaciones" cols="25" rows="7" maxlength="300" ><?php echo $Observaciones_Usuario;?></textarea> 
					<p class="advertencia_longitud">Máximo 300 caracteres</p> 
					
					</fieldset>
					
					<fieldset class="foto_recuadro">	
					<legend>Foto</legend>
					
					<img src="<?php echo $Imagen_Usuario;?>" height="150">
					
					</fieldset>
					
				
									
				
					<fieldset class="recuadro4">	
					<legend>Permisos</legend>					
							<table id="tablaPermisos">
								<?php
									if(isset($permisosUserLogin['editar_permisos'])&&$permisosUserLogin['editar_permisos']){
											include (FCPATH.'application/controllers/usuarios/permisos.php');
											$fila = 0;
											echo "<tr>";
											foreach($permisos as $valor => $contenido){
												if($fila % 5 == 0){echo "</tr><tr>";}
												$fila++;
												if(array_key_exists($valor,$permisos_usuario)){
													echo "<td><input class='input-permisos-checkbox' type='checkbox' name='permisos[]' id='ch_$valor' value='$valor'checked/></td><td><label class='permisos-label'>$contenido</label></td>";
												}else{
													echo "<td><input class='input-permisos-checkbox' type='checkbox' name='permisos[]' id='ch_$valor' value='$valor'/></td><td><label class='permisos-label'>$contenido</label></td>";
												}
											}
											echo "</tr>";
											
											echo "<script>
														var permisosVendedor = ".json_encode($permisosVendedor).";
														var permisosCajero = ".json_encode($permisosCajero).";
														var permisosAdmin = ".json_encode($permisosAdmin).";
														var permisosAvanz = ".json_encode($permisosAvanz).";
														
														
													</script>";
									}else{
											echo "<tr><td>Usted no puede editar permisos.</td></tr>";
									}
								?>
							</table>
					</fieldset>	
					
					
					
					<div class="divButton">		
						<input class="buttom" value="Volver" type="button" onclick="window.location.assign('<?php echo base_url('usuarios/editar')?>')">
						<input class="buttom" name="submit" id="submit" onsubmit="" tabindex="18" value="Actualizar" type="submit">						
					</div>
					
				</form>
				
			</div>
		</div>		

		<!--Incluir footer-->
		<?php include FCPATH.'application/views/Footer/Default_Footer.php';?>
	</body>
</html>