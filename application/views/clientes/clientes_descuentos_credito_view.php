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
		<title>Descuento y Crédito</title>
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
		<!--CSS ESTILO PROPIO-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/clientes/descuentoCredito.css'); ?>">		
		<!--SCRIPT DE EXPIRACION DE LA SESION-->
		<?php include '/../Header/log_out_from_browser_Script.php';?>
		
		<!--CARGA DEL JQUERY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>"></script>
		<!--CARGA DEL JQUERYUI-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.js'); ?>"></script>
		<!--CSS ESTILO DEL JQUERYUI-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.css'); ?>">
		<!--CARGA DEL NOTY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>"></script>
		<!--CARGA DE HERRAMIENTAS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/clientes/descuento_tools.js'); ?>"></script>
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
			<p class="titulo_wrapper">Descuentos y Crédito de Cliente</p>
			<hr class="division_wrapper">
			<div  class="contenido">
					<table class="tablaInfo">
						<tr>
							<td>
								<p class="contact"><label for="cliente">Cliente</label></p>
							</td>
						</tr>
						<tr>
							<td>
								<p class="contact"><label for="cedula">Cédula:</label></p>
							</td>
							<td>
								<input id="cedula" class="input_uno" placeholder="Inserte el numero de cédula" name="cliente" type="text" onkeyup="buscarCedula(event);" required="" tabindex='1'>					
								<script>document.getElementById('cedula').focus();</script>
							</td>
						</tr>
						<tr>
							<td>
								<p class="contact"><label for="nombre">Nombre:</label></p>
							</td>
							<td>
								<input id="nombre" class="input_uno" placeholder="Inserte el nombre del cliente" autocomplete="off" name="nombre" type="text" >
								<script>setUpLiveSearch();</script>
							</td>						
						</tr>
					</table>
					<hr class="division-contenido">
						<table>
							<tr>
								<td>
									<label for="descuento"  class="contact">Descuento:</label>
									<input id="descuento" class="input_uno descuento-cliente" autocomplete="off" name="descuento" value="00" onClick="$('#descuento').select()" type="text">%<a href="javascript:;" onclick="updateDescuento()" class="boton-cambiar">Cambiar</a>
								</td>
							</tr>
							<tr>
								<td>
									<label for="credito"  class="contact">Máximo de Crédito Disponible:</label>
									₡<input id="credito" class="input_uno input-credito" autocomplete="off" name="credito" value="0.0" type="text" onClick="$('#credito').select()" onBlur="formatCreditoField()"><a href="javascript:;" onclick="updateCredito()" class="boton-cambiar">Cambiar</a>
								</td>
							</tr>
						</table>
					<hr class="division-contenido">
					
					
				<p class="contact">Descuentos</p>
			
				<div class="div-tabla-productos">
					<table id="tabla_des_productos">
						<thead>
							<th class="borde-abajo">
								<p class="tiny-font ">Producto</p>
							</th>
							<th class="descripcion-thead borde-abajo">
								<p class="tiny-font">Descripción</p>
							</th>
							<th class="borde-abajo">
								<p class="tiny-font">Precio</p>
							</th>
							<th class="borde-abajo">
							</th>
						</thead>
						<tbody id="cuerpo_productos">
							<tr>
								<td colspan="4">
									<p class="tiny-font">No tiene descuentos</p>
								</td>
							</tr>
							<tr>
								<td class="borde-arriba">								
									<input class="input-codigo" type="text" id="codigo_producto" onkeyup="buscarArticulo();">
								</td>
								<td class="borde-arriba" ><p class="tiny-font" id="descripcion_producto"></p></td>
								<td class="borde-arriba">
									<input class="input-descuento" type="text" id="descuento_producto"/>
								</td>
								<td class="borde-arriba">
									<a href="javascript:;" onclick="agregarDescuentoProducto()" class="boton-cambiar">Agregar</a>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="div-tabla-familias">
					<table id="tabla_des_familias">
						<thead>
							<th class="borde-abajo">
								<p class="tiny-font">Familia</p>
							</th>
							<th class="descripcion-thead borde-abajo">
								<p class="tiny-font">Descripción</p>
							</th>
							<th class="borde-abajo">
								<p class="tiny-font">Porcentaje</p>
							</th>
							<th class="borde-abajo">
							</th>
						</thead>
						<tbody id="cuerpo_familia">
							<tr>
								<td colspan="4">
									<p class="tiny-font">No tiene descuentos</p>
								</td>
							</tr>
							
							<tr>
								<td class="borde-arriba">
									<input class="input-codigo" type="text" id="codigo_familia" onkeyup="buscarFamilia()"/>
								</td>
								<td class="borde-arriba"><p class="tiny-font" id="descripcion_familia"></p></td>
								<td class="borde-arriba">
									<input class="input-descuento" type="text" id="descuento_familia"/>
								</td>
								<td class="borde-arriba">
									<a href="javascript:;" onclick="" class="boton-cambiar">Agregar</a>
								</td>
							</tr>
						</tbody>
					</table>			
				</div>
			</div>		
		</div>
		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>
