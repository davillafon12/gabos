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
		<title>Edición De Cliente</title>
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
		<!--CSS ESTILO ACCORDION-->
		
		<script src="<?php echo base_url('application/scripts/jquery-1.10.2.js'); ?>" type="text/javascript"></script>
	
		<!--CSS ESTILO DEL FORMULARIO-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/clientes/style_editar.css'); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/clientes/style_registrar.css'); ?>">
		<!--SCRIPT DE AJAX JQUERY-->
		<script src="<?php echo base_url('application/scripts/jquery.maskedinput.js'); ?>" type="text/javascript"></script>
		<?php include '/../../scripts/ajax_verify_cliente_id.php';?>	
	
	
	
	
	

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
			<p class="titulo_wrapper">Edición De Cliente</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
			<div  class="form">
			<?php 
				$attributes = array('name' => 'registrar_cliente_form', 'class' => 'registrar_cliente_form-form');
				echo form_open_multipart('clientes/editar/actualizarCliente', $attributes); 
				$opc_Cedula= array ( 
                  'nacional'   =>  'Nacional' , 
                  'residencia'     =>  'Residencia' , 
                  'juridica'     =>  'Jurídica' ,
				  'pasaporte' => 'Pasaporte'
                ); 
                $opcCedulaMetodo = 'id="tipo_Cedula", onblur="tipoCedula()", class="styleSelect", tabindex="1" , disabled';
                $opc_estadoCliente= array ( 
                  'activo'   =>  'Activo' , 
				  'semiactivo' => 'Semiactivo',
                  'inactivo'     =>  'Inactivo' , 
                ); 
                $opc_pagoCliente= array ( 
                  '1'   =>  'Contado Publico' , 
                  '2'   =>  'Contado Afiliado' ,
                  '3'   =>  'Pago 3' ,
                  '4'   =>  'Pago 4' , 
                ); 
			?>	 
		
			<fieldset class="recuadro">	
			<legend>Información Personal</legend>
			<!--aqui se debe de poner el tipo de cedula -->
			<table>
			<tr>
				<td>
					<label for="tipo_Cedula" class="labelMedium">Cédula:</label> &nbsp; 
					<?php echo form_dropdown ( 'tipo_Cedula' , $opc_Cedula ,  $Cliente_Tipo_Cedula, $opcCedulaMetodo);  ?>
					<input id="cedula" autocomplete="off" name="cedula"  placeholder="" value="<?php echo $Cliente_Cedula;?>" required="" tabindex="2" disabled> 
					<input type="hidden" name="cedula_res" value="<?php echo $Cliente_Cedula;?>">
				</td>

				<td>
					<div id="status" class="status"></div>
				</td>
				<td>
					<label class="labelMedium">Fecha Ingreso:</label> &nbsp;
					<label class="labelMedium"><?php echo $Cliente_Fecha_Ingreso;?></label>				
				</td>
			
			</tr>
			<tr>
				<td>
					<label for="nombre" class="labelMedium">Nombre:</label> &nbsp;
					<input id="nombre" value="<?php echo $Cliente_Nombre;?>" class="input_Medium" autocomplete="off"  name="nombre" required="" tabindex="4" type="text"> 
				</td>
				<td>
					<label for="apellidos" class="labelMedium2">Apellidos:</label>
					<input id="apellidos" value="<?php echo $Cliente_Apellidos;?>" class="input_Medium" autocomplete="off" name="apellidos"  required="" tabindex="5" type="text"> <br>
				</td>
				<td>
				<div class="estado-div">
					<label for="estado_Cliente" class="labeMedium">Estado: </label>
					<?php echo form_dropdown ( 'estado_Cliente' , $opc_estadoCliente ,  $Cliente_Estado, 'class="styleSelect", tabindex="3"');  ?>
				</div>
				</td>

			</tr>
			<tr>
				<td>
					<label for="carnet"  class="labelMedium">Carnet:</label> &nbsp; &nbsp;
					<input id="carnet" value="<?php echo $Cliente_Carnet_Numero;?>" class="input_Small" autocomplete="off" name="carnet"   tabindex="6" >
				</td>

	
				<td>
					<label for="celular"  class="labelMedium">Celular:</label> &nbsp; &nbsp;
					<input id="celular" value="<?php echo $Cliente_Celular;?>" class="input_Small" autocomplete="off" name="celular"  required="" tabindex="7">
				</td>
				<td>
					<label for="telefono"  class="labelMedium">Telefono:</label>
					<input id="telefono" value="<?php echo $Cliente_Telefono;?>" class="input_Small" autocomplete="off" name="telefono"  required="" tabindex="8"><br>						
				</td>
			</tr>
			<tr>
				<td>
					<label for="email"  class="labelMedium">Email:</label> &nbsp; &nbsp; &nbsp;
					<input id="email" value="<?php echo $Cliente_Correo_Electronico;?>" class="input_Medium" autocomplete="off" name="email" type="email" tabindex="9">
				</td>
				<td>
					<label for="pais" class="labelMedium">País:</label> &nbsp; &nbsp; &nbsp; &nbsp;
					<input id="pais" value="<?php echo $Cliente_Pais;?>" class="input_Medium" autocomplete="off"   tabindex="11" type="text" name="pais"> 	<br>
				</td>
				<td>
					<label for="essucursal" class="labelMedium" style="position: relative; top: -5px;">Sucursal:</label>  
					<input id="essucursal" tabindex="11" type="checkbox" name="issucursal" value="1" style="position: relative; top: -5px;" <?php if($isSucursal){echo "checked";}?>> 	
					<label for="esexento" class="labelMedium" style="position: relative; top: -5px;">Exento:</label>  
					<input id="esexento" tabindex="11" type="checkbox" name="esexento" value="1" style="position: relative; top: -5px;" <?php if($isExento){echo "checked";}?>> 	
					<label for="aplicaRetencion" class="labelMedium" style="position: relative; top: -5px;">Sin Retención:</label>  
					<input id="aplicaRetencion" tabindex="11" type="checkbox" name="aplicaRetencion" value="1" style="position: relative; top: -5px;" <?php if($aplicaRetencion){echo "checked";}?>> 	
				</td>

			</tr>			
			<tr>
				<td colspan="2">
					<label for="direccion" class="labelMedium">Dirección:</label>
					<input id="direccion" value="<?php echo $Cliente_Direccion;?>" class="input_Direccion" autocomplete="off" name="direccion" required=""  tabindex="12" type="text"><br>
				</td>
				<td>
					<div class="picture" >
						<input type="file" name="userfile" size="10" accept=".jpg,.png,.ico,.bmp"/>
					</div>
				</td>
			</tr>

			</table>
			</fieldset>

			<fieldset class="recuadro2">	
			<legend>Montos Compras</legend>

			
			<label for="tipo_pago_cliente"  class="montos-label">Tipo de pago:</label>
			<?php echo form_dropdown ( 'tipo_pago_cliente' , $opc_pagoCliente ,  $Cliente_Numero_Pago, 'class="styleSelectPago", tabindex="16"' );  ?>
			
			</fieldset>

			<fieldset class="recuadro3">	
			<legend>Observaciones</legend>
			<textarea id="observaciones" autocomplete="off" class="observaciones" placeholder="" tabindex="17"name="observaciones" cols="25" rows="7" maxlength="300" ><?php echo $Cliente_Observaciones;?></textarea> 
			<p class="advertencia_longitud">Máximo 300 caracteres</p> 
			</fieldset>

			<div class="divButton">			
				<input class="buttom" value="Volver" type="button" onclick="window.location.assign('<?php echo base_url('/clientes/editar')?>')">
				<input class="buttom" name="submit" id="submit" onsubmit="" tabindex="18" value="Actualizar" type="submit" >				
			</div>
		</form>	
		</div>
		</div>
		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>