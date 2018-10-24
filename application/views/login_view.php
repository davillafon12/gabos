<!--
PAGINA DE LOGIN
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
		<title>Login</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="shortcut icon" href="<?php echo base_url('application/images/header_icon.png'); ?>">
	    <!--CSS ESTILO BASICO DE LA PAGINA-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/Header/Estilo_Basico.css'); ?>">
		<!--CSS ESTILO DEL LOG IN FORM-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/Log_In_Form_Style.css'); ?>">
	</head>
	
	<body>
		<!--Incluir imagen de cabezera-->
		<?php include 'Header/Header_Picture.php';?>

		<!--Formulario del login-->
		<div id="wrapper">
			<?php 
				
			    echo validation_errors(); 
			?>
			<?php 
				$attributes = array('name' => 'login-form', 'class' => 'login-form');
				
				echo form_open('verifylogin', $attributes); 
				
			?>				
			<div class="header">
				<h1>Ingreso GABO</h1>
				<h2>Factura Electrónica</h2>
				<span>Ingrese sus datos de usuario y contrase&ntilde;a para ingresar al sistema GABO</span>
			</div>		
			<div class="content">
				<input name="username" type="text" class="input username" placeholder="Usuario" id="username"/>
				<div class="user-icon"></div>
				<input name="password" type="password" class="input password" placeholder="Contrase&ntilde;a" id="password"/>
				<div class="pass-icon"></div>		
			</div>
			<div class="footer">
				<input type="submit" name="submit" value="Ingresar" class="button" />		
			</div>			
			</form>
		</div>
		<!--Termina Formulario del login-->
	</body>
</html>

