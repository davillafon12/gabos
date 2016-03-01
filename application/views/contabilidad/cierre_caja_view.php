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
		<title>Cierre de Caja</title>
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
		<!--SCRIPT DE NUMERIC-->		
		<script src="<?php echo base_url('application/scripts/jquery.numeric.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE NOTY-->		
		<script src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE Impromptu-->		
		<script src="<?php echo base_url('application/scripts/jquery-impromptu.js'); ?>" type="text/javascript"></script>
		<!--CSS ESTILO DEL Impromptu-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/jquery-impromptu.css'); ?>">
		<!--CSS ESTILO ESPECIFICO DE LA PAGINA-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/contabilidad/contabilidad_cierre_caja.css'); ?>">
		<!--SCRIPT DE HERRAMIENTAS-->		
		<script src="<?php echo base_url('application/scripts/contabilidad/cierres/herramientas_cierre_caja.js'); ?>" type="text/javascript"></script>
		<script>
			 var fechaReal = '<?php echo $fechaRealActual?>';
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
			<p class="titulo_wrapper">Cierre de Caja</p>
			<hr class="division_wrapper">
			
			<div class="contenedor" >
				<table class="tabla-principal">
					<tr>
						<td>
							<p class="titulo-2">Fecha: <?php echo $fechaActual;?></p>
						</td>
						<td>
							<label class="titulo-2" for="base_caja">Base: ₡</label>
							<input type="text" class="input-base-caja alg-right" id="base_caja" onblur="actualizarMontoTotalRetiro()" value="<?php echo $baseCaja;?>"/>
						</td>
					</tr>
					<tr>
						<td>
							<p class="titulo-2">Primera Factura: <?php echo $primeraFactura;?></p>
						</td>
						<td>
							<p class="titulo-2">Última Factura: <?php echo $ultimaFactura;?></p>
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
									<td><p class="parrafo"><input type="text" id="cant_50000" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 50000)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_50000">₡0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">₡ 20.000</p><input type="hidden" id="deno_20000"></td>
									<td><p class="parrafo"><input type="text" id="cant_20000" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 20000)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_20000">₡0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">₡ 10.000</p><input type="hidden" id="deno_10000"></td>
									<td><p class="parrafo"><input type="text" id="cant_10000" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 10000)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_10000">₡0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">₡ 5.000</p><input type="hidden" id="deno_5000"></td>
									<td><p class="parrafo"><input type="text" id="cant_5000" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 5000)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_5000">₡0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">₡ 2.000</p><input type="hidden" id="deno_2000"></td>
									<td><p class="parrafo"><input type="text" id="cant_2000" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 2000)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_2000">₡0,00</p></td>
								</tr>
								<tr>
									<td class="borde-abajo"><p class="parrafo">₡ 1.000</p><input type="hidden" id="deno_1000"></td>
									<td class="borde-abajo"><p class="parrafo"><input type="text" id="cant_1000" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 1000)" onclick="this.select()"></p></td>
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
									<td><p class="parrafo"><input type="text" id="cant_500" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 500)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_500">₡0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">₡ 100</p><input type="hidden" id="deno_100"></td>
									<td><p class="parrafo"><input type="text" id="cant_100" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 100)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_100">₡0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">₡ 50</p><input type="hidden" id="deno_50"></td>
									<td><p class="parrafo"><input type="text" id="cant_50" class="input-deno alg-right" value="0"  onkeyup="actualizarCantidad(this.value, 50)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_50">₡0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">₡ 25</p><input type="hidden" id="deno_25"></td>
									<td><p class="parrafo"><input type="text" id="cant_25" class="input-deno alg-right" value="0"  onkeyup="actualizarCantidad(this.value, 25)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_25">₡0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">₡ 10</p><input type="hidden" id="deno_10"></td>
									<td><p class="parrafo"><input type="text" id="cant_10" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 10)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_10">₡0,00</p></td>
								</tr>
								<tr>
									<td class="borde-abajo"><p class="parrafo">₡ 5</p><input type="hidden" id="deno_5"></td>
									<td class="borde-abajo"><p class="parrafo"><input type="text" id="cant_5" class="input-deno alg-right" value="0" onkeyup="actualizarCantidad(this.value, 5)" onclick="this.select()"></p></td>
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
									<td><p class="parrafo"><input type="text" id="cant_do_50" class="input-deno alg-right" value="0" onkeyup="actualizarCantidadDolar(this.value, 50)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_do_50">$0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">$ 20</p><input type="hidden" id="deno_do_20"></td>
									<td><p class="parrafo"><input type="text" id="cant_do_20" class="input-deno alg-right" value="0" onkeyup="actualizarCantidadDolar(this.value, 20)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_do_20">$0,00</p></td>
								</tr>
								<tr>
									<td><p class="parrafo">$ 10</p><input type="hidden" id="deno_do_10"></td>
									<td><p class="parrafo"><input type="text" id="cant_do_10" class="input-deno alg-right" value="0" onkeyup="actualizarCantidadDolar(this.value, 10)" onclick="this.select()"></p></td>
									<td class="alg-right"><p class="parrafo" id="total_do_10">$0,00</p></td>
								</tr>
								<tr>
									<td  class="borde-abajo"><p class="parrafo">$ 1</p><input type="hidden" id="deno_do_1"></td>
									<td  class="borde-abajo"><p class="parrafo"><input type="text" id="cant_do_1" class="input-deno alg-right" value="0" onkeyup="actualizarCantidadDolar(this.value, 1)" onclick="this.select()"></p></td>
									<td class="alg-right borde-abajo"><p class="parrafo" id="total_do_1">$0,00</p></td>
								</tr>
								<tr>
									<td colspan="2" class="alg-right"><p class="parrafo">Total:</p></td>
									<td class="alg-right"><p class="parrafo" id="total_dolares">$0,00</p></td>
								</tr>
							</table>
						</td>
						<td>
							<div class="contenedor-tipo-cambio">
								<label class="parrafo">Tipo de Cambio ₡</label><input id="tipo_cambio_dolar" class="input-deno alg-right" value="<?php echo $tipo_cambio;?>" onblur="validarYFormatearCantidadEscritaTipoCambio(this.value)" onclick="this.select()" type="text"/><br>
							</div>
							<div class="contenedor-total-denominaciones">
								<label class="parrafo">Total Conteo: ₡</label><label class="parrafo" id="input_retiro_parcial">0,00</label>
							</div>
							<div class="contenedor-bn">
								<label class="parrafo titulo-bn">BN Servicios:</label>
								<input type="text" id="cantidad_bn_servicios" value="0" onblur="validarCantidadBN(this)" onclick="$(this).select()"/>
							</div>
						</td>
					</tr>
					<tr><td colspan="2"><hr></td></tr>
					<tr>
						<td>
							<table class="tabla-retiros-parciales">
								<tr><td colspan="3"><p class="titulo-2">Retiros Parciales</p></td></tr>
								<tr>
									<td class="borde-abajo"><p class="parrafo"># Retiro</p></td>
									<td class="borde-abajo"><p class="parrafo">Fecha y Hora</p></td>
									<td class="borde-abajo"><p class="parrafo">Total</p></td>
								</tr>
								<?php
									if(!$retirosParciales){
										echo "<tr><td colspan='3'><p class='parrafo'>No hay retiros parciales. . .</p></td></tr>";
									}else{
										$contador = 1;
										foreach($retirosParciales as $ret){
											echo "<tr>
													<td><p class='parrafo'>$contador</p></td>
													<td><p class='parrafo'>".date('d-m-Y H:i:s', strtotime($ret->Fecha_Hora))."</p></td>
													<td class='alg-right'><p class='parrafo'>₡".number_format($ret->Monto,2,",",".")."</p></td>
												  </tr>";
											$contador++;
										}
									}
								?>
								<tr>
									<td colspan="2" class="alg-right borde-arriba"><p class="parrafo">Total:</p></td>									
									<td class="alg-right borde-arriba"><p class="parrafo">₡<?php echo number_format($totalRecibosParciales,2,",",".");?></p></td>
								</tr>
							</table>
						</td>
						<td>
							<table class="tabla-datafonos">
								<tr><td colspan="4"><p class="titulo-2">Datáfonos</p></td></tr>
								<tr>
									<td class="borde-abajo"><p class="parrafo">Banco</p></td>
									<td class="borde-abajo"><p class="parrafo">Comisión</p></td>
									<td class="borde-abajo"><p class="parrafo">Retención</p></td>
									<td class="borde-abajo"><p class="parrafo">Total</p></td>
								</tr>
								<?php
									if(sizeOf($pagoDatafonos['datafonos'])==0){
										echo "<tr><td colspan='3'><p class='parrafo'>No hay datáfonos registrados. . .</p></td></tr>";
									}else{
										foreach($pagoDatafonos['datafonos'] as $datafono){
											echo "
												<tr>
													<td><p class='parrafo' style='font-size: 11px;'>".$datafono->Banco_Codigo." - ".$datafono->Banco_Nombre."</p></td>
													<td class='alg-right'><p class='parrafo'>₡".number_format($datafono->Total_Comision,2,",",".")."</p></td>
													<td class='alg-right'><p class='parrafo'>₡".number_format($datafono->Total_Retencion,2,",",".")."</p></td>
													<td class='alg-right'><p class='parrafo'>₡".number_format($datafono->Total,2,",",".")."</p></td>
												</tr>
											";
										}
									}									
								?>
								<tr>
									<td class="alg-right borde-arriba"><p class="parrafo">Totales:</p></td>	
									<td class="alg-right borde-arriba"><p class="parrafo">₡<?php echo number_format($pagoDatafonos['totalComision'],2,",",".");?></p></td>
									<td class="alg-right borde-arriba"><p class="parrafo">₡<?php echo number_format($pagoDatafonos['totalRetencion'],2,",",".");?></p></td>
									<td class="alg-right borde-arriba"><p class="parrafo">₡<?php echo number_format($pagoDatafonos['totalDatafonos'],2,",",".");?></p></td>
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
									<td class=''><p class='parrafo'><?php echo $pagoMixto['cantidadFacturas'];?></p></td>
									<td class='alg-right'><p class='parrafo'>₡<?php echo number_format($pagoMixto['efectivo'],2,",",".");?></p></td>
									<td class='alg-right'><p class='parrafo'>₡<?php echo number_format($pagoMixto['tarjeta'],2,",",".");?></p></td>
								</tr>								
								<tr>
									<td colspan="2" class="alg-right borde-arriba"><p class="parrafo">Total:</p></td>									
									<td class="alg-right borde-arriba"><p class="parrafo">₡<?php echo number_format($pagoMixto['total'],2,",",".");?></p></td>
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
									<td class='alg-right'><p class='parrafo'>₡<?php echo number_format($recibos['efectivo'],2,",",".");?></p></td>
									<td class='alg-right'><p class='parrafo'>₡<?php echo number_format($recibos['tarjeta'],2,",",".");?></p></td>
									<td class='alg-right'><p class='parrafo'>₡<?php echo number_format($recibos['deposito'],2,",",".");?></p></td>
									<td class='alg-right'><p class='parrafo'>₡<?php echo number_format($recibos['abonos'],2,",",".");?></p></td>
								</tr>								
								<tr>
									<td colspan="3" class="alg-right borde-arriba"><p class="parrafo">Total:</p></td>									
									<td class="alg-right borde-arriba"><p class="parrafo">₡<?php echo number_format($recibos['total'],2,",",".");?></p></td>
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
									<td class=''><p class='parrafo'>₡<?php echo number_format($detalleNotasCredito['contado'],2,",",".");?></p></td>
									<td class=''><p class='parrafo'>₡<?php echo number_format($detalleNotasCredito['tarjeta'],2,",",".");?></p></td>
									<td class=''><p class='parrafo'>₡<?php echo number_format($detalleNotasCredito['cheque'],2,",",".");?></p></td>
									<td class=''><p class='parrafo'>₡<?php echo number_format($detalleNotasCredito['deposito'],2,",",".");?></p></td>
									<td class=''><p class='parrafo'>₡<?php echo number_format($detalleNotasCredito['mixto'],2,",",".");?></p></td>
									<td class=''><p class='parrafo'>₡<?php echo number_format($detalleNotasCredito['credito'],2,",",".");?></p></td>
									<td class=''><p class='parrafo'>₡<?php echo number_format($detalleNotasCredito['apartado'],2,",",".");?></p></td>
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
									<td class="borde-abajo"><p class="parrafo">Efectivo</p></td>
									<td class="borde-abajo"><p class="parrafo">Tarjetas</p></td>
									<td class="borde-abajo"><p class="parrafo">Créditos</p></td>
								</tr>
								<tr>
									<td class=''><p class='parrafo'>₡<?php echo number_format($totalFacturasContado,2,",",".");?></p></td>
									<td class=''>
										<input id="totalRetirosParciales" value="<?php echo $totalRecibosParciales; ?>" type="hidden"/>
										<p class='parrafo' id="parrafoTotalRetirosParciales">₡<?php echo number_format($totalRecibosParciales-$recibos['efectivo'],2,",",".");?></p>
									</td> 
									<td class=''><p class='parrafo'>₡<?php echo number_format($pagoDatafonos['totalDatafonos'],2,",",".");?></p></td>
									<td class=''><p class='parrafo'>₡<?php echo number_format($totalCreditos['totalCredito'],2,",",".");?></p></td>
									
								</tr>								
								<tr>
									<td class="borde-abajo"><p class="parrafo">Encomiendas (Depo.)</p></td>
									<td class="borde-abajo"><p class="parrafo">Apartados</p></td>
									<td class="borde-abajo"><p class="parrafo">Notas Crédito</p></td>
									<td class="borde-abajo"><p class="parrafo">Notas Débito</p></td>
								</tr>
								<tr>
									<td class=''><p class='parrafo'>₡<?php echo number_format($totalFacturasDeposito,2,",",".");?></p></td>
									<td class=''><p class='parrafo'>₡<?php echo number_format($totalCreditos['totalApartado'],2,",",".");?></p></td>
									<td class=''><p class='parrafo'>₡<?php echo number_format($totalNotasCredito['total'],2,",",".");?></p></td>
									<td class=''><p class='parrafo'>₡<?php echo number_format($totalNotasDebito['total'],2,",",".");?></p></td>
								</tr>
							</table>
						</td>						
					</tr>
					<tr><td colspan="2"><hr></td></tr>
					<tr>
						<td colspan="2">
							<table class="tabla-vendedores">
								<tr><td colspan="7"><p class="titulo-2">Vendedores</p></td></tr>
								<tr>
									<td class="borde-abajo"><p class="parrafo">Vendedor</p></td>
									<td class="borde-abajo"><p class="parrafo">Vendido</p></td>
									<td class="borde-abajo"><p class="parrafo">Vendedor</p></td>
									<td class="borde-abajo"><p class="parrafo">Vendido</p></td>
								</tr>
								<tr>
									<?php
										$contador = 1;
										foreach($vendedores['vendidoVendedores'] as $vendedor){
											$vendedor = $vendedor[0];											
											echo "<td style='text-align: left; width: 250px;'><p class='parrafo'>{$vendedor->usuario}</p></td>";
											echo "<td class='' style='width: 120px;'><p class='parrafo'>₡".number_format($vendedor->total_vendido,2,",",".")."</p></td>";
											if($contador == 2){
												$contador = 1;
												echo "</tr><tr>";
											}else{
												$contador++;
											}											
										}
									?>
								</tr>					
								<tr>
									<td colspan="4" class="alg-right borde-arriba"></td>
								</tr>
								<tr>
									<td colspan="4" class="alg-right">
											<label class="parrafo" style="font-size: 20px;">Total vendedores:</label>
											<label class="parrafo" style="font-size: 20px;">₡<?php echo number_format($vendedores['totalVendido'],2,",",".");?></label>	
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
													<td><label class="parrafo" style="font-size: 20px;">₡<?php echo number_format($valoresFinales['totalFacturas'],2,",",".");?> </label></td>
													<td><label class="parrafo" style="font-size: 20px;">₡<?php echo number_format($valoresFinales['totalIVA'],2,",",".");?> </label></td>
													<td><label class="parrafo" style="font-size: 20px;">₡<?php echo number_format($valoresFinales['totalRetencion'],2,",",".");?> </label></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr><td colspan="4"><hr></td></tr>
							</table>
							<button class="boton_envio_cobro" id="boton_envio_cobro" onclick="realizarCierreCaja()">Realizar Cierre</button>
						</td>						
					</tr>
				</table>
			</div><!-- contenedor -->			
		</div><!-- main_wrapper -->
		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>