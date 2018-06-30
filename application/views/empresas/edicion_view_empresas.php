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
		<?php include PATH_LOG_OUT_HEADER;?>
		
		<!--CSS ESTILO DEL FORMULARIO-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/empresas/style_registrar.css'); ?>">
		<!--CARGA DEL JQUERY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>"></script>
		<!--CARGA DEL JQUERYUI-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.js'); ?>"></script>
		<!--CSS ESTILO DEL JQUERYUI-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.css'); ?>">
		<script type="text/javascript" src="<?php echo base_url('application/scripts/empresas/buscarCliente.js?v='.$javascript_cache_version); ?>"></script>
                <script type="text/javascript" src="<?php echo base_url('application/scripts/empresas/verifyId.js?v='.$javascript_cache_version); ?>"></script>
		
		
	</head>
	<body>
            <style>
                .form{
                        height: 610px;
                }
                .form.cert-contenedor{
                        height: 135px;
                }
            </style>
		<!--Incluir imagen de cabezera-->
		<?php include PATH_HEADER_PICTURE;?>
		
		<!--Incluir menu principal-->
		<?php include PATH_HEADER_SELECTOR_MENU;?>

		<!--Incluir informacion log in-->
		<?php include PATH_HEADER_LOG_IN_INFO;?>
		
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
					<td>
						<p class="contact"><label for="cliente_asociado">Cliente Asociado:</label></p> 
						<input id="cliente_asociado" autocomplete="off" name="cliente_asociado" placeholder="Seleccione Cliente"  tabindex="6" type="text" value="<?php echo $Empresa_Cliente_Nombre;?>"> 
						<input type="hidden" name="cliente_liga_id" id="cliente_liga_id" value="<?php echo $Empresa_Cliente_Id;?>"/>
					</td>
				</tr>	
                                <tr>
					<td>
						<p class="contact"><label for="direccion">Usuario Tributación:</label></p> 
						<input id="user_tributa" autocomplete="off" name="user_tributa" placeholder="Usuario generado en el ATV para la facturación electrónica"  tabindex="6" type="text" value='<?= $User_Tributa; ?>'> 
					</td>
					<td>
						<p class="contact"><label for="cliente_asociado">Contraseña Tributación:</label></p> 
						<input id="pass_tributa" autocomplete="off" name="pass_tributa" placeholder="Contraseña generada en el ATV para la facturación electrónica"  tabindex="6" type="password" value='<?= $Pass_Tributa; ?>'/> 
					</td>
				</tr>
                                <tr>
					<td>
						<p class="contact"><label for="direccion">Ambiente Tributación:</label></p> 
                                                <select name='ambiente_tributa'>
                                                    <option value='api-stag' <?= $Ambiente_Tributa == 'api-stag' ? 'selected' : ''; ?>>Pruebas</option>
                                                    <option value='api-prod' <?= $Ambiente_Tributa == 'api-stag' ? '' : 'selected'; ?>>Producción</option>
                                                </select>
                                              
					</td>
                                        <td>
						<p class="contact"><label for="cliente_asociado">PIN del certificado de Tributación:</label></p> 
						<input id="pin_tributa" autocomplete="off" name="pin_tributa" placeholder="PIN generado en el ATV para el certificado de facturación electrónica"  tabindex="6" type="password" value='<?= $Pin_Tributa; ?>'/> 
                                              
					</td>
				</tr>
				<tr>
					<td>
						<p class="contact"><label for="leyenda">Leyenda de la D.G.T.D.:</label></p> 
						<textarea id="leyenda" autocomplete="off" class="observaciones" placeholder="" name="leyenda" tabindex="8" cols="25" rows="5" maxlength="1000" ><?php echo $Empresa_Tributacion;?></textarea> 
						<p class="advertencia_longitud">Máximo 1000 caracteres</p> 
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
                <p class="contenido_wrapper">
                    <div  class="form cert-contenedor">
                        <?php 
                            echo form_open_multipart('empresas/editar/cargarCertificado', array()); 
                        ?>
                        <p class="titulo">Certificado de Ministerio de Hacienda</p>
                        <table>				
    			    <input autocomplete="off" name="codigo" class="input_codigo" type="hidden" value="<?php echo $Empresa_codigo;?>">
                            <tr>
                                    <td>
                                            <p class="contact"><label for="cedula_ju">Token:</label></p> 
                                            <input id="token_cert" autocomplete="off" name="token_certificado" placeholder="Aún no se ha cargado un certificado"  tabindex="1" type="text" value="<?php echo $Token_Tributa;?>" readonly='true'> 
                                    </td>
                                    <td>
                                            <p class="contact"><label for="name">Certificado:</label></p> 
                                            <input type="file" name="certificado_hacienda_file" size="10" id="certificado_hacienda" accept=".p12"/>
                                    </td>
                            </tr>
                        </table>
                        <br>
                            <br>
                        <input class="buttom" name="submit" tabindex="10" value="Cargar" type="submit">
                    </div>
                </p>
        </div>
               
                
            

		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>