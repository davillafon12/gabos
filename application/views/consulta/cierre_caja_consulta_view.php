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
		<title>Consulta de Cierres de Caja</title>
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
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/consulta/estilo_retiros_parciales.css'); ?>">
		<!--CSS ESTILO ESPECIFICO DE LA PAG-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/consulta/estilo_cierre_caja.css'); ?>">
		<!--CARGA DEL SCRIPT DE HERRAMIENTAS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/consulta/cierre_caja.js'); ?>"></script>
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
		<?php include '/../Header/Header_Picture.php';?>
		
		<!--Incluir menu principal-->
		<?php include '/../Header/selector_menu.php';?>

		<!--Incluir informacion log in-->
		<?php include '/../Header/Log_In_Information.php';?>
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Consulta de Cierres de Caja</p>
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
				<input type="button" class="boton-carga" onclick="cargarFactura()" value="Cargar Cierre"/>
				<p class="contact" style="display:inline;"><label for="impresion">Impresión:</label></p> 					
				<select id="tipo_impresion" onChange="cambiarTipoImpresion(this.value)" class="impresion" name="impresion" >
					<option value="c">A4</option>
				</select>
				<input type="button" class="boton_busqueda" onclick="imprimir()" value="Imprimir"/>
				<hr>
		
		
				<table style="margin: auto;">
					<tr>
						<td>
							<p class="titulo-2" id="fecha_cierre">Fecha: </p>
						</td>
						<td>
							<label class="titulo-2" for="base_caja">Base: ₡</label>
							<input type="text" class="input-base-caja alg-right" id="base_caja" disabled/>
						</td>
					</tr>
					<tr>
						<td>
							<p class="titulo-2" id="primera_factura">Primera Factura: </p>
						</td>
						<td>
							<p class="titulo-2" id="ultima_factura">Última Factura: </p>
						</td>
					</tr>
					<tr><td colspan="2"><hr></td></tr>
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
								<label class="contact">Total ₡</label><input class="input_uno alg-right" name="input_retiro_parcial" id="input_retiro_parcial" type="text" style="  width: 150px;" disabled /><br>
								<label class="contact">BN Servicios Contado ₡</label><input class="input_uno alg-right" name="input_bn_servicios" id="input_bn_servicios" type="text" style="  width: 150px;" disabled /><br>
								<label class="contact">BN Servicios Tarjeta ₡</label><input class="input_uno alg-right" name="input_bn_servicios" id="input_bn_servicios_credito" type="text" style="  width: 150px;" disabled />
							</div>							
						</td>
					</tr>
					<tr>
						<td>
							<table class="tabla-retiros-parciales" id="contenido_retiros_parciales">
								<tr><td colspan="3"><p class="titulo-2">Retiros Parciales</p></td></tr>
								<tr>
									<td class="borde-abajo"><p class="parrafo"># Retiro</p></td>
									<td class="borde-abajo"><p class="parrafo">Fecha y Hora</p></td>
									<td class="borde-abajo"><p class="parrafo">Total</p></td>
								</tr>
								<tr><td colspan='3'><p class='parrafo'>No hay retiros parciales. . .</p></td></tr>																							
								<tr>
									<td colspan="2" class="alg-right borde-arriba"><p class="parrafo">Total:</p></td>									
									<td class="alg-right borde-arriba"><p class="parrafo" id="total_retiros">₡0</p></td>
								</tr>
							</table>
						</td>
						<td>
							<table class="tabla-datafonos" id="contenido_datafonos">
								<tr><td colspan="4"><p class="titulo-2">Datáfonos</p></td></tr>
								<tr>
									<td class="borde-abajo"><p class="parrafo">Banco</p></td>
									<td class="borde-abajo"><p class="parrafo">Comisión</p></td>
									<td class="borde-abajo"><p class="parrafo">Retención</p></td>
									<td class="borde-abajo"><p class="parrafo">Total</p></td>
								</tr>
								<tr><td colspan="4"><p class="parrafo">No se han cargado datos. . .</p></td></tr>
								<tr>
									<td class="alg-right borde-arriba"><p class="parrafo">Totales:</p></td>	
									<td class="alg-right borde-arriba"><p class="parrafo">₡0</p></td>
									<td class="alg-right borde-arriba"><p class="parrafo">₡0</p></td>
									<td class="alg-right borde-arriba"><p class="parrafo">₡0</p></td>
								</tr>
								<tr>
									<td class="alg-right borde-arriba" colspan="3"><p class="parrafo">Total Con BN Servicios (Tarjetas):</p></td>	
									<td class="alg-right borde-arriba"><p class="parrafo">₡0</p></td>
								</tr>
							</table>
						</td>						
					</tr>
					<tr><td colspan="2"><hr></td></tr>
					<tr>
						<td>
							<table class="tabla-pagos-mixtos">
								<tr><td colspan="3"><p class="titulo-2">Pago Mixtos</p></td></tr>
								<tr>
									<td class="borde-abajo"><p class="parrafo">Cant. Facturas</p></td>
									<td class="borde-abajo"><p class="parrafo">Efectivo</p></td>
									<td class="borde-abajo"><p class="parrafo">Tarjeta</p></td>
								</tr>
								<tr>
									<td class=''><p class='parrafo' id="cant_facturas_mixto">0</p></td>
									<td class='alg-right'><p class='parrafo' id="total_efectivo_mixto">₡0</p></td>
									<td class='alg-right'><p class='parrafo' id="total_tarjeta_mixto">₡0</p></td>
								</tr>								
								<tr>
									<td colspan="2" class="alg-right borde-arriba"><p class="parrafo">Total:</p></td>									
									<td class="alg-right borde-arriba"><p class="parrafo" id="total_mixto">₡0</p></td>
								</tr>
							</table>
						</td>
						<td>
							<table class="tabla-recibos-dinero">
								<tr><td colspan="4"><p class="titulo-2">Recibos Por Dinero</p></td></tr>
								<tr>
									<td class="borde-abajo"><p class="parrafo">Contado</p></td>
									<td class="borde-abajo"><p class="parrafo">Tarjeta</p></td>
									<td class="borde-abajo"><p class="parrafo">Deposito</p></td>
									<td class="borde-abajo"><p class="parrafo">Abonos</p></td>
								</tr>
								<tr>
									<td class='alg-right'><p class='parrafo' id="recibo_contado">₡0</p></td>
									<td class='alg-right'><p class='parrafo' id="recibo_tarjeta">₡0</p></td>
									<td class='alg-right'><p class='parrafo' id="recibo_deposito">₡0</p></td>
									<td class='alg-right'><p class='parrafo' id="recibo_abono">₡0</p></td>
								</tr>								
								<tr>
									<td colspan="3" class="alg-right borde-arriba"><p class="parrafo">Total:</p></td>									
									<td class="alg-right borde-arriba"><p class="parrafo" id="total_recibos_dinero">₡0</p></td>
								</tr>
							</table>
						</td>						
					</tr>
					<tr><td colspan="2"><hr></td></tr>
					<tr>
						<td colspan="2">
							<table class="tabla-costos-totales">
								<tr><td colspan="7"><p class="titulo-2">Notas Crédito</p></td></tr>
								<tr>
									<td class="borde-abajo"><p class="parrafo">Contado</p></td>
									<td class="borde-abajo"><p class="parrafo">Tarjeta</p></td>
									<td class="borde-abajo"><p class="parrafo">Cheque</p></td>
									<td class="borde-abajo"><p class="parrafo">Depósito</p></td>
									<td class="borde-abajo"><p class="parrafo">Mixto</p></td>
									<td class="borde-abajo"><p class="parrafo">Crédito</p></td>
									<td class="borde-abajo"><p class="parrafo">Apartado</p></td>
								</tr>
								<tr>
									<td class=''><p class='parrafo' id="nota_credito_contado">₡0</p></td>
									<td class=''><p class='parrafo' id="nota_credito_tarjeta">₡0</p></td>
									<td class=''><p class='parrafo' id="nota_credito_cheque">₡0</p></td>
									<td class=''><p class='parrafo' id="nota_credito_deposito">₡0</p></td>
									<td class=''><p class='parrafo' id="nota_credito_mixto">₡0</p></td>
									<td class=''><p class='parrafo' id="nota_credito_credito">₡0</p></td>
									<td class=''><p class='parrafo' id="nota_credito_apartado">₡0</p></td>
								</tr>	
							</table>
						</td>						
					</tr>
					<tr><td colspan="2"><hr></td></tr>
					<tr>
						<td colspan="2">
							<table class="tabla-costos-totales">
								<tr><td colspan="7"><p class="titulo-2">Otros Totales</p></td></tr>
								<tr>
									<td class="borde-abajo"><p class="parrafo">Facturas de Contado</p></td>
									<td class="borde-abajo"><p class="parrafo">Faltante / Sobrante</p></td>
									<td class="borde-abajo"><p class="parrafo">Tarjetas</p></td>
									<td class="borde-abajo"><p class="parrafo">Créditos</p></td>
									
								</tr>
								<tr>
									<td class=''><p class='parrafo' id="totales_factura_contado">₡0</p></td>
									<td class=''><p class='parrafo' id="totales_efectivo">₡0</p></td>
									<td class=''><p class='parrafo' id="totales_tarjetas">₡0</p></td>
									<td class=''><p class='parrafo' id="totales_creditos">₡0</p></td>
								</tr>								
								<tr>
									<td class="borde-abajo"><p class="parrafo">Encomiendas (Depo.)</p></td>
									<td class="borde-abajo"><p class="parrafo">Apartados</p></td>
									<td class="borde-abajo"><p class="parrafo">Notas Crédito</p></td>
									<td class="borde-abajo"><p class="parrafo">Notas Débito</p></td>
								</tr>
								<tr>
									<td class=''><p class='parrafo' id="totales_encomienda">₡0</p></td>
									<td class=''><p class='parrafo' id="totales_apartados">₡0</p></td>
									<td class=''><p class='parrafo' id="totales_notas_credito">₡0</p></td>
									<td class=''><p class='parrafo' id="totales_notas_debito">₡0</p></td>
								</tr>
							</table>
						</td>						
					</tr>
					<tr><td colspan="2"><hr></td></tr>
					<tr>
						<td colspan="2">
							<table class="tabla-vendedores" id="tabla_vendedores">
								<tr><td colspan="7"><p class="titulo-2">Vendedores</p></td></tr>
								<tr>
									<td class="borde-abajo"><p class="parrafo">Vendedor</p></td>
									<td class="borde-abajo"><p class="parrafo">Vendido</p></td>
								</tr>
								<tr>
									<td colspan="7" class="alg-right borde-arriba"></td>
								</tr>														
							</table>							
						</td>						
					</tr>
					<tr><td colspan="4"><hr></td></tr>
					<tr>
						<td colspan="4">
							<table style="width:100%; text-align:center;">
								<tr>
										<td><label class="parrafo" style="font-size: 20px;">Total Vendido</label></td>
										<td><label class="parrafo" style="font-size: 20px;">Total IVA</label></td>
										<td><label class="parrafo" style="font-size: 20px;">Total Retención</label></td>
								</tr>
								<tr>
										<td><label class="parrafo" style="font-size: 20px;" id="totalVendido">₡0</label></td>
										<td><label class="parrafo" style="font-size: 20px;" id="totalIVA">₡0</label></td>
										<td><label class="parrafo" style="font-size: 20px;" id="totalRetencion">₡0</label></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr><td colspan="4"><hr></td></tr>
				</table>
		
		
				
			</div><!--CONTENEDOR-->
     </div>		

		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>