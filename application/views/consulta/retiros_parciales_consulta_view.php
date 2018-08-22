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
		<title>Consulta de Retiros Parciales</title>
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
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/consulta/estilo_retiros_parciales.css'); ?>">
		<!--CARGA DEL SCRIPT DE HERRAMIENTAS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/consulta/retiros_parciales.js'); ?>"></script>
		<script>
			var decimales = '<?php echo $this->configuracion->getDecimales();?>';
		</script>
		<style>
				.tabla-filtrado {
				  width: 200px;
				  border-right: 0px;
				}
		</style>
	</head>
	<body>
		<!--Incluir imagen de cabezera-->
		<?php include PATH_HEADER_PICTURE;?>
		
		<!--Incluir menu principal-->
		<?php include PATH_HEADER_SELECTOR_MENU;?>

		<!--Incluir informacion log in-->
		<?php include PATH_HEADER_LOG_IN_INFO;?>
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Consulta de Retiros Parciales</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
			<div class="contenedor">
				<table class="tabla-filtrado" style="margin-bottom: 90px;">
					<tr><td><p class="contact">Filtros</p></td></tr>
					<tr>
						<td>
							<p class="contact">Fechas</p>
						</td>
					</tr>
					<tr>						
						<td>
							<p class="contact">Desde:</p>
						</td>
						<td>
							<input id="fecha_desde" class="input_uno" style="width: 100px;" autocomplete="off" type="text"/>
						</td>
					</tr>
					<tr>
						<td>
							<p class="contact">Hasta:</p>
						</td>
						<td>
							<input id="fecha_hasta" class="input_uno" style="width: 100px;" autocomplete="off" type="text"/>
						</td>
					</tr>					
				</table>
				<div class="contenedor-facturas">
					<table class="tabla-facturas">
						<thead>
							<tr class="header">
								<td class="titulo_header_tabla contact" style="  width: 50px;">#</td>
								<td class="titulo_header_tabla contact">Usuario</td>
								<td class="titulo_header_tabla contact" >Fecha</td>
								<td class="titulo_header_tabla contact" >Total</td>
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
				<input type="button" class="boton-carga" onclick="cargarFactura()" value="Cargar Retiro"/>
				<p class="contact" style="display:inline;"><label for="impresion">Impresión:</label></p> 					
				<select id="tipo_impresion" onChange="cambiarTipoImpresion(this.value)" class="impresion" name="impresion" >
					<option value="t">PV</option>
				</select>
				<input type="button" class="boton_busqueda" onclick="imprimir()" value="Imprimir"/>
				<hr>
		
		
				<table style="margin: auto;">
					<tr>
						<td>
							<table class="tabla-denominaciones">
								<tr><td colspan="3"><p class="titulo-2">Billetes</p></td></tr>
								<tr>
									<td class="borde-abajo"><p class="parrafo">Denominación</p></td>
									<td class="borde-abajo"><p class="parrafo">Cantidad</p></td>
									<td class="borde-abajo" style="min-width: 100px;"><p class="parrafo">Total</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">₡ 50.000</p><input type="hidden" id="deno_50000"></td>
									<td><p class="parrafo"><input disabled type="text" id="cant_50000" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 50000)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_50000">₡0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">₡ 20.000</p><input type="hidden" id="deno_20000"></td>
									<td><p class="parrafo"><input disabled type="text" id="cant_20000" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 20000)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_20000">₡0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">₡ 10.000</p><input type="hidden" id="deno_10000"></td>
									<td><p class="parrafo"><input disabled type="text" id="cant_10000" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 10000)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_10000">₡0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">₡ 5.000</p><input type="hidden" id="deno_5000"></td>
									<td><p class="parrafo"><input disabled type="text" id="cant_5000" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 5000)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_5000">₡0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">₡ 2.000</p><input type="hidden" id="deno_2000"></td>
									<td><p class="parrafo"><input disabled type="text" id="cant_2000" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 2000)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_2000">₡0,00</p></td>
								</tr>
								<tr>
									<td class="borde-abajo"><p class="parrafo">₡ 1.000</p><input type="hidden" id="deno_1000"></td>
									<td class="borde-abajo"><p class="parrafo"><input disabled type="text" id="cant_1000" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 1000)" onclick="this.select()"></p></td>
									<td class="borde-abajo alg-right"><p class="parrafo" id="total_1000">₡0,00</p></td>
								</tr>
								<tr>
									<td colspan="2" class="alg-right"><p class="parrafo">Total:</p></td>
									<td class="alg-right"><p class="parrafo" id="total_billetes">₡0,00</p></td>
								</tr>
							</table>
						</td>
						<td>
							<table class="tabla-denominaciones">
								<tr><td colspan="3"><p class="titulo-2">Monedas</p></td></tr>
								<tr>
									<td class="borde-abajo"><p class="parrafo">Denominación</p></td>
									<td class="borde-abajo"><p class="parrafo">Cantidad</p></td>
									<td class="borde-abajo" style="min-width: 100px;"><p class="parrafo">Total</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">₡ 500</p><input type="hidden" id="deno_500"></td>
									<td><p class="parrafo"><input disabled type="text" id="cant_500" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 500)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_500">₡0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">₡ 100</p><input type="hidden" id="deno_100"></td>
									<td><p class="parrafo"><input disabled type="text" id="cant_100" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 100)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_100">₡0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">₡ 50</p><input type="hidden" id="deno_50"></td>
									<td><p class="parrafo"><input disabled type="text" id="cant_50" class="input-deno alg-right" value="0"  onkeyup="actualizarCantidad(this.value, 50)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_50">₡0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">₡ 25</p><input type="hidden" id="deno_25"></td>
									<td><p class="parrafo"><input disabled type="text" id="cant_25" class="input-deno alg-right" value="0"  onkeyup="actualizarCantidad(this.value, 25)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_25">₡0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">₡ 10</p><input type="hidden" id="deno_10"></td>
									<td><p class="parrafo"><input disabled type="text" id="cant_10" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 10)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_10">₡0,00</p></td>
								</tr>
								<tr>
									<td class="borde-abajo"><p class="parrafo">₡ 5</p><input type="hidden" id="deno_5"></td>
									<td class="borde-abajo"><p class="parrafo"><input disabled type="text" id="cant_5" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 5)" onclick="this.select()"></p></td>
									<td class="borde-abajo alg-right"><p class="parrafo" id="total_5">₡0,00</p></td>
								</tr>
								<tr>
									<td colspan="2" class="alg-right"><p class="parrafo">Total:</p></td>
									<td class="alg-right"><p class="parrafo" id="total_monedas">₡0,00</p></td>
								</tr>
							</table>
						</td>						
					</tr>
					<tr>
						<td>
							<table class="tabla-denominaciones">
								<tr><td colspan="3"><p class="titulo-2">Dólares</p></td></tr>
								<tr>
									<td class="borde-abajo"><p class="parrafo">Denominación</p></td>
									<td class="borde-abajo"><p class="parrafo">Cantidad</p></td>
									<td class="borde-abajo" style="min-width: 100px;"><p class="parrafo">Total</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">$ 50</p><input type="hidden" id="deno_do_50"></td>
									<td><p class="parrafo"><input disabled type="text" id="cant_do_50" class="input-deno alg-right" value="0" onkeyup="actualizarCantidadDolar(this.value, 50)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_do_50">$0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">$ 20</p><input type="hidden" id="deno_do_20"></td>
									<td><p class="parrafo"><input disabled type="text" id="cant_do_20" class="input-deno alg-right" value="0" onkeyup="actualizarCantidadDolar(this.value, 20)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_do_20">$0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">$ 10</p><input type="hidden" id="deno_do_10"></td>
									<td><p class="parrafo"><input disabled type="text" id="cant_do_10" class="input-deno alg-right" value="0" onkeyup="actualizarCantidadDolar(this.value, 10)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_do_10">$0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">$ 1</p><input type="hidden" id="deno_do_1"></td>
									<td><p class="parrafo"><input disabled type="text" id="cant_do_1" class="input-deno alg-right" value="0" onkeyup="actualizarCantidadDolar(this.value, 1)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_do_1">$0,00</p></td>
								</tr>
								<tr>
									<td colspan="2" class="alg-right"><p class="parrafo">Total:</p></td>
									<td class="alg-right"><p class="parrafo" id="total_dolares">$0,00</p></td>
								</tr>
							</table>
						</td>
						<td>
							<div class="footer_notas">	
								<label class="parrafo">Tipo de Cambio ₡</label><input id="tipo_cambio_dolar" class="input-deno alg-right" type="text" disabled /><br>
								<label class="contact">Total ₡</label><input class="input_uno alg-right" name="input_retiro_parcial" id="input_retiro_parcial" type="text" style="  width: 150px;" disabled />
							</div>							
						</td>
					</tr>
				</table>
		
		
				
			</div><!--CONTENEDOR-->
     </div>		

		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>