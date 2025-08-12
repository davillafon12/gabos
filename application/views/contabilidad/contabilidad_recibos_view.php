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
		<title>Recibos por Dinero</title>
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
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/contabilidad/contabilidad_recibos.css'); ?>">	
		<!--SCRIPT DE CARGA DE FACTURAS-->		
		<script src="<?php echo base_url("application/scripts/contabilidad/recibos/carga_facturas_ajax.js?v=$javascript_cache_version"); ?>" type="text/javascript"></script>
		<!--SCRIPT DE HERRAMIENTAS-->		
		<script src="<?php echo base_url("application/scripts/contabilidad/recibos/herramientas.js?v=$javascript_cache_version"); ?>" type="text/javascript"></script>
		<!--SCRIPT DE CALCULOS-->		
		<script src="<?php echo base_url("application/scripts/contabilidad/recibos/calculos.js?v=$javascript_cache_version"); ?>" type="text/javascript"></script>
		<!--SCRIPT DE PAGAR-->		
		<script src="<?php echo base_url("application/scripts/contabilidad/recibos/pagar_tools.js?v=$javascript_cache_version"); ?>" type="text/javascript"></script>
	</head>
	<body >
		<!--Incluir imagen de cabezera-->
		<?php include PATH_HEADER_PICTURE;?>
		
		<!--Incluir menu principal-->
		<?php include PATH_HEADER_SELECTOR_MENU;?>

		<!--Incluir informacion log in-->
		<?php include PATH_HEADER_LOG_IN_INFO;?>		
	
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Recibos Por Dinero</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
			<div class="contenedor">
				<table class="tablaInfo">
					<tr>
						<td>
							<p class="contact"><label for="cliente">Cliente</label></p>
						</td>
						<td>
						</td>
						<td>
							<p class="contact">Tipo de Pago:</p>
						</td>
						<td>
							<p class="contact" style="display:inline;"><label for="impresion">Impresión:</label></p> 					
							<select id="tipo_impresion" onChange="cambiarTipoImpresion(this.value)" class="impresion" name="impresion" >
								<option value="t">PV</option>
								<option value="c">A4</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<p class="contact"><label for="cedula">Cédula:</label></p>
						</td>
						<td>
							<input id="cedula" class="input_uno" placeholder="Inserte el numero de cédula" name="cedula" autocomplete="off" type="text" onkeyup="buscarCedula(event);" required="" tabindex='1'>					
							<script>document.getElementById('cedula').focus();</script>
						</td>
						<td>
							<input type="radio" name="tipo" onClick="numTransaccion(this.value)" value="contado"><div class="tipos_de_pago">Contado</div>
							<input type="radio" name="tipo" onClick="numTransaccion(this.value)" value="tarjeta" checked><div class="tipos_de_pago">Tarjeta</div>
							<input type="radio" name="tipo" onClick="numTransaccion(this.value)" value="deposito"><div class="tipos_de_pago">Deposito</div>
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
						<td>
							<div id="num_transaccion_field" style="display: none;">
								<p class="contact" style="display: inline;">Número de Autorización:</p>
								<input id='numero_transaccion' class='input_uno' style='width: 100px; margin-left: 5px;' autocomplete='off' name='numero_transaccion' required='' type='text'>
							</div>
							<div id="num_documento_field" style="display: none;">
								<p class="contact" style="display: inline;">Número de Documento:</p>
								<input id='numero_documento' class='input_uno' style='width: 100px; margin-left: 5px;' autocomplete='off' name='numero_documento' required='' type='text'>
							</div>
						</td>
						<td>
							<div id="datafono_field" style="display: none;">
								<p class="contact" style="display: inline;">Datáfono:</p>
								<select id="banco_sel" name="banco_sel" class='input_uno' style='width: 250px; margin-left: 5px;'>
									<?php
										foreach($bancos as $banco){
											echo "<option value='".$banco->Banco_Codigo."'>".$banco->Banco_Nombre."</option>";
										}						
									?>
								</select>
							</div>
						</td>
					</tr>
				</table>
				<hr class="division-contenido">
				<div class="selectores_facturas">
					<div class="div_posibles_facturas">
						<div class="titulo_cuadro">
							<p class="contact"><label >Facturas pendientes de Saldar:</label></p>
						</div>
						<div class="cuerpo_cuadro">
							<table class="tabla_facturas" id="tabla_posibles_facturas">
								<thead>
									<tr class="header">
										<th class="titulo_header_tabla" style="width: 110px;">Consecutivo</th>
										<th class="titulo_header_tabla" style="width: 110px;">Expedición</th>
										<th class="titulo_header_tabla" style="width: 110px;">Vence</th>
										<th class="titulo_header_tabla" style="">Saldo</th>
									</tr>
								</thead>
								<tbody id="tbody_posibles_facturas">
								
								</tbody>
							</table>
						</div>
					</div>
					<div class="div_facturas_saldar">
						<div class="titulo_cuadro">
							<p class="contact"><label >Facturas a Saldar:</label></p>
						</div>
						<div class="cuerpo_cuadro">
							<table class="tabla_facturas" id="tabla_facturas_a_saldar">
								<thead>
									<tr class="header">
										<th class="titulo_header_tabla" style="width: 110px;">Consecutivo</th>
										<th class="titulo_header_tabla" style="width: 110px;">Expedición</th>
										<th class="titulo_header_tabla" style="width: 110px;">Vence</th>
										<th class="titulo_header_tabla" style="">Saldo</th>
									</tr>
								</thead>
								<tbody id="tbody_facturas_a_saldar">
								
								</tbody>
							</table>

						</div>
					</div>
					<div class="div_botones_acciones">
						<button class="boton_accion" onclick="agregarFacturasSaldarSeleccion()"><img title="Agregar Facturas Seleccionadas" src="<?php echo base_url('application/images/recibos/flecha_derecha.png');?>"></img></button>
						<button class="boton_accion" onclick="eliminarFacturasSaldarSeleccion()"><img title="Quitar Facturas Seleccionadas" src="<?php echo base_url('application/images/recibos/flecha_izquierda.png');?>"></img></button>
						<button class="boton_accion" onclick="pasarTodasPendientesASaldar()"><img title="Agregar Todas Las Facturas" src="<?php echo base_url('application/images/recibos/flecha_doble_derecha.png');?>"></img></button>
						<button class="boton_accion" onclick="eliminarTodasASaldar()"><img title="Quitar Todas Las Facturas" src="<?php echo base_url('application/images/recibos/flecha_doble_izquierda.png');?>"></img></button>
					</div>
				</div>
				<hr class="division-contenido">
				<div class="comentarios-container">
						<p class="contact">Comentarios:</p>
						<textarea class="comentarios-recibo" id="comentarios"></textarea>
				</div>
				<div class="footer_recibos">
					<p class="titulo_saldo">Saldo a Pagar</p>
					<input type="text" onclick="seleccionarSaldoInput()" onblur="formatearSaldoInput()" onkeyup="filtrarEventosInputSaldo(event)" class="input_saldo_pagar" id="saldo_a_pagar_input"><br>
					<button class="boton_envio_cobro" id="boton_envio_cobro" onclick="pagar()">Pagar</button>
				</div>
			</div>
			
		</div>
		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>