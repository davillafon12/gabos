<!--
PAGINA PRINCIPAL
DESARROLLADO POR:
-SIVIANY PRENDAS
-DAVID VILLALOBOS
PARA:
-GAROTAS BONITAS S.A.
2014
-->
<?php $this->contabilidad->getHeadNotaDebito(1, 0)?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Notas Débito</title>
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
		<!--SCRIPT DE JQUERY-->		
		<script src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE NOTY-->		
		<script src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE Impromptu-->		
		<script src="<?php echo base_url('application/scripts/jquery-impromptu.js'); ?>" type="text/javascript"></script>
		<!--CSS ESTILO DEL Impromptu-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/jquery-impromptu.css'); ?>">
		<!--CARGA DEL JQUERYUI-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.js'); ?>"></script>
		<!--CSS ESTILO DEL JQUERYUI-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.css'); ?>">
		<!--CSS ESTILO ESPECIFICO DE LA PAGINA-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/contabilidad/contabilidad_notas_debito.css'); ?>">
		<!--CARGA DE LAS HERRAMIENTAS DE CARGA DE PRODUCTO-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/contabilidad/notas/carga_productos_debito.js'); ?>"></script>
		<!--SCRIPT ENVIO DE NOTA-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/contabilidad/notas/envioNotaDebito.js'); ?>"></script>
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
			<p class="titulo_wrapper">Notas Débito</p>
			<hr class="division_wrapper">
			
			<div class="contenedor" >
				<span class="contact">Productos a Debitar</span>
				<p class="contact" style="display:inline;"><label for="impresion">Impresión:</label></p> 					
				<select id="tipo_impresion" onChange="cambiarTipoImpresion(this.value)" class="impresion" name="impresion" >
					<option value="t">PV</option>
					<option value="c">A4</option>
				</select>
				<table id="tabla_productos" class="tabla_productos" >
					<thead>
						<th class="th_codigo">Código</th>
						<th class="th_descripcion">Descripción</th>
						<th class="th_cantidad">Cantidad</th>
						<th class="th_bodega">Inventario</th>
					</thead>
					<tbody>
					
					<?php					
						//Creamos las filas de la tabla
						for($contador=0;$contador<10;$contador++)
						{
							$numero_id = $contador+1;
							$numero_tab = $contador+2;
							echo "<tr>
								<td>									
									<input id='articulo_".$numero_id."' tabindex='".$numero_tab."'  class='input_codigo_articulo' autocomplete='off' name='codigo_articulo' type='text' onkeyup='buscarArticulo(event, this.value, this.id);'>									
								</td>
								<td>
									<div class='articulo_specs' id='descripcion_articulo_".$numero_id."'></div>
									<div class='tooltip_imagen_articulo' id='tooltip_imagen_articulo_".$numero_id."'></div>
								</td>
								<td>
									<input id='cantidad_articulo_".$numero_id."' class='cantidad_articulo' autocomplete='off' type='number' min='1' onchange='validarMaxMinCantidad(this.id)' onkeyup='validarEventoCantidad(this.id, event)' >									
								</td>
								<td>
									<div class='articulo_specs' id='bodega_articulo_".$numero_id."'></div>
								</td>
							</tr>";
						}					
					?>
					</tbody>				
				</table>
				<hr class="divisor_footer">
				<div class="footer_notas">					
					<button class="boton_generar" id="boton_generar" onclick="generarNotaDebito()">Generar Nota</button>
				</div>
			</div><!-- contenedor -->			
		</div><!-- main_wrapper -->
		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>