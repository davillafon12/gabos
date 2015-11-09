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
		<title>Consulta de Recibos por Dinero</title>
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
		<!--CARGA DEL JQUERY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>"></script>
		<!--CARGA DEL JQUERYUI-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.js'); ?>"></script>
		<!--CSS ESTILO DEL JQUERYUI-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.css'); ?>">
		<!--CARGA DEL NOTY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>"></script>
		<!--CARGA DEL NUMERIC-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.numeric.js'); ?>"></script>
		<!--CSS ESTILO ESPECIFICO DE LA PAG-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/consulta/estilo_facturas.css'); ?>">
		<!--CARGA DEL SCRIPT DE HERRAMIENTAS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/consulta/recibos_tools.js'); ?>"></script>
		<script>
			var decimales = '<?php echo $this->configuracion->getDecimales();?>';
		</script>
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
			<p class="titulo_wrapper">Consulta de Recibos de Dinero</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
			<div class="contenedor">
				<table class="tabla-filtrado">
					<tr><td><p class="contact">Filtros</p></td></tr>
					<tr>
						<td>
							<p class="contact">Cliente</p>
						</td>
						<td>
							
						</td>
						<td>
							<p class="contact">Fechas</p>
						</td>
					</tr>
					<tr>
						<td>
							<p class="contact">Cédula:</p>
						</td>
						<td>
							<input id="cedula" class="input_uno" placeholder="Inserte el numero de cédula" autocomplete="off" type="text" onkeyup="buscarCedula(event);" />
						</td>
						<td>
							<p class="contact">Desde:</p>
						</td>
						<td>
							<input id="fecha_desde" class="input_uno" style="width: 100px;" autocomplete="off" type="text"/>
						</td>
					</tr>
					<tr>
						<td>
							<p class="contact">Nombre:</p>
						</td>
						<td>
							<input id="nombre" class="input_uno ui-autocomplete-input" placeholder="Inserte el nombre del cliente" autocomplete="off" type="text" />
						</td>
						<td>
							<p class="contact">Hasta:</p>
						</td>
						<td>
							<input id="fecha_hasta" class="input_uno" style="width: 100px;" autocomplete="off" type="text"/>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="text-align: center;">
							<p class="contact">Tipo de Pago</p>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="text-align: center;">
							<input type="checkbox" name="tipo" value="contado"><div class="tipos_de_pago">Contado</div>
							<input type="checkbox" name="tipo" value="tarjeta"><div class="tipos_de_pago">Tarjeta</div>
							<input type="checkbox" name="tipo" value="deposito"><div class="tipos_de_pago">Deposito</div>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="text-align: center;">
							<p class="contact">Estado</p>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="text-align: center;">
							<input type="checkbox" name="estado" value="0"><div class="tipos_de_pago">Cobrados</div>
							<input type="checkbox" name="estado" value="1"><div class="tipos_de_pago">Anulados</div>
						</td>
					</tr>
				</table>
				<div class="contenedor-facturas">
					<table class="tabla-facturas">
						<thead>
							<tr class="header">
								<td class="titulo_header_tabla contact" style="  width: 50px;">#</td>
								<td class="titulo_header_tabla contact">Cliente</td>
								<td class="titulo_header_tabla contact" style="  width: 100px;">Fecha</td>
								<td class="titulo_header_tabla contact" style="  width: 100px;">Total</td>
							</tr>
						</thead>
						<tbody id="facturas_filtradas">
						</tbody>
					</table>
				</div>
				<input type="button" class="boton_busqueda" onclick="llamarFacturas()" value="Realizar Búsqueda"/>
				<hr>
				<label class='contact'>Consecutivo:</label>
				<input id="consecutivo" class="input_uno" autocomplete="off" type="text" style="width: 100px;"/>
				<input type="button" class="boton-carga" onclick="cargarRecibo()" value="Cargar Recibo"/>
				<p class="contact" style="display:inline;"><label for="impresion">Impresión:</label></p> 					
				<select id="tipo_impresion" onChange="cambiarTipoImpresion(this.value)" class="impresion" name="impresion" >
					<option value="t">PV</option>
					<option value="c">A4</option>
				</select>
				<input type="button" class="boton_busqueda" onclick="imprimir()" value="Imprimir"/>
				<hr>
				<table class="tabla-filtrado" style="border-right: 0px; width:100%;">
					<tr>
						<td colspan="8">
							<label class="contact">Cliente</label>
						</td>
					</tr>
					<tr>
						<td><label class="contact">Cédula: </label></td>
						<td><label class="contact" id="cedula_cliente"></label></td>
						<td><label class="contact">Nombre: </label></td>
						<td><label class="contact" id="nombre_cliente"></label></td>
					</tr>
					<tr>
						<td colspan="8">
							<label class="contact">Factura</label>
						</td>
					</tr>
					<tr>
						<td><label class="contact">Consecutivo: </label></td>
						<td><label class="contact" id="consecutivo_factura"></label></td>
						<td><label class="contact">Fecha: </label></td>
						<td><label class="contact" id="fecha_factura"></label></td>
						<td><label class="contact">Monto: </label></td>
						<td><label class="contact" id="monto_factura"></label></td>
					</tr>
					<tr>
						<td colspan="8">
							<label class="contact">Recibo</label>
						</td>
					</tr>
					<tr>
						<td><label class="contact">Fecha: </label></td>
						<td><label class="contact" id="fecha_recibo"></label></td>
						<td><label class="contact">Saldo Inicial: </label></td>
						<td><label class="contact" id="saldo_inicial"></label></td>
						<td><label class="contact">Saldo Actual: </label></td>
						<td><label class="contact" id="saldo_actual"></label></td>
						<td><label class="contact">Monto: </label></td>
						<td><label class="contact" id="monto_recibo"></label></td>
					</tr>	
					<tr>
						<td>
							<label class="contact">Comentarios:</label>
						</td>
					</tr>	
					<tr>
						<td colspan="8" id="comentarios" style="padding-left: 10px; font-size: 14px;">
						</td>
					</tr>				
				</table>
				
			</div><!--CONTENEDOR-->
        </div>		

		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>