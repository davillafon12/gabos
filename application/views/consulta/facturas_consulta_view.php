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
		<title>Consulta de Facturas</title>
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
		<!--CARGA DEL POPUP MODAL-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.bpopup.min.js'); ?>"></script>
		<!--CARGA DEL NUMERIC-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.numeric.js'); ?>"></script>
		<!--CSS ESTILO ESPECIFICO DE LA PAG-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/consulta/estilo_facturas.css'); ?>">
		<!--CARGA DEL SCRIPT DE HERRAMIENTAS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/consulta/facturas_tools.js?v='.$javascript_cache_version); ?>"></script>
		<!--JQUERY IMPROMPTU-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-impromptu.js'); ?>"></script>
		<!--CSS ESTILO DEL MODAL-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/jquery-impromptu.css'); ?>">
		<!--LIBRERIA ENCRYPTACION-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/cryptoJS.js'); ?>"></script>
		<script>
			var decimales = '<?php echo $this->configuracion->getDecimales();?>';
		</script>
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
			<p class="titulo_wrapper">Consulta de Facturas</p>
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
							<input type="checkbox" name="tipo" value="cheque"><div class="tipos_de_pago">Cheque</div>
							<input type="checkbox" name="tipo" value="deposito"><div class="tipos_de_pago">Deposito</div>
							<input type="checkbox" name="tipo" value="mixto"><div class="tipos_de_pago">Mixto</div>
							<input type="checkbox" name="tipo" value="credito"><div class="tipos_de_pago">Crédito</div>
							<input type="checkbox" name="tipo" value="apartado"><div class="tipos_de_pago">Apartado</div>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="text-align: center;">
							<p class="contact">Estado</p>
						</td>
					</tr>
					<tr>
						<td colspan="4" style="text-align: center;">
							<input type="checkbox" name="estado" value="cobrada"><div class="tipos_de_pago">Cobradas</div>
							<input type="checkbox" name="estado" value="anulada"><div class="tipos_de_pago">Anuladas</div>
							<input type="checkbox" name="estado" value="pendiente"><div class="tipos_de_pago">Pendientes</div>
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
				<input type="button" class="boton-carga" onclick="cargarFactura()" value="Cargar Factura"/>
				<p class="contact" style="display:inline;"><label for="impresion">Impresión:</label></p> 					
				<select id="tipo_impresion" onChange="cambiarTipoImpresion(this.value)" class="impresion" name="impresion" >
					<option value="t">PV</option>
					<option value="c">A4</option>
				</select>
				<input type="button" class="boton_busqueda" onclick="imprimir()" value="Imprimir"/>
				<input type="button" class="boton_anular" onclick="anularFactura()" value="Anular"/>
				<table id="tabla_productos" class="tabla_productos">
					<thead>
						<tr><th class="th_codigo">Código</th>
						<th class="th_descripcion">Descripción</th>
						<th class="th_cantidad">Cantidad</th>
						<th class="th_exento">E</th>
						<th class="th_descuento">Descuento</th>
						<th class="th_costo_unidad">Precio por unidad</th>
						<th class="th_costo_total">Precio total</th>	
					</tr></thead>
					<tbody id="contenidoArticulos" class="contenidoArticulos">
					
					</tbody>				
				</table>
				<div class="cantidad-art-contenedor">
					<label class="contact">Cantidad Artículos: <span id="cantidad_total_articulos">0</span></label>
				</div>
				<div class="observaciones_div">
					<p class="contact"><label for="observaciones">Observaciones:</label></p>
					<textarea id="observaciones" autocomplete="off" class="observaciones" placeholder="" name="observaciones" cols="25" rows="5" maxlength="150" disabled=""></textarea> 
					<p class="advertencia_longitud">Máximo 150 caracteres</p>				
				</div>
				<div class="tabla_costos">
					<table>
						<tbody><tr>
						<td>
							<p class="contact"><label for="ganancia"><!--Ganancia:--></label></p> 
						</td>
						<td>
							<div id="tipo_moneda_display" class="tipo_moneda_display" style="display:none;"><!--₡--></div>
							<input id="ganancia" class="input_dos" autocomplete="off" name="ganancia" type="hidden" min="0" disabled=""> 					
						</td>
						</tr>
						<tr>
						<td>
							<p class="contact"><label for="costo">Monto:</label></p> 
						</td>
						<td>
							<div id="tipo_moneda_display" class="tipo_moneda_display">₡</div>
							<input id="costo" class="input_dos" autocomplete="off" name="costo" type="text" disabled=""> 					
						</td>
						</tr>
						<tr>
						<td>
							<p class="contact"><label for="iva">IVA:</label></p> 
						</td>
						<td>
							<div id="tipo_moneda_display" class="tipo_moneda_display">₡</div>
							<input id="iva" class="input_dos" autocomplete="off" name="iva" type="text" disabled=""> 
						</td>
						</tr>
						<tr  style="display:none;">
						<td>
							<p class="contact"><label for="retencion">Retención:</label></p> 
						</td>
						<td>
							<div id="tipo_moneda_display" class="tipo_moneda_display">₡</div>
							<input id="retencion" class="input_dos" autocomplete="off" name="retencion" type="text" disabled=""> 
						</td>
						</tr>
						<tr>
						<td>
							<p class="contact"><label for="costo_total">Monto Total:</label></p> 
						</td>
						<td>
							<div id="tipo_moneda_display" class="tipo_moneda_display">₡</div>
							<input id="costo_total" class="input_dos" autocomplete="off" name="costo_total" type="text" disabled=""> 
						</td>
						</tr>
					</tbody></table>
				</div>
			</div><!--CONTENEDOR-->
        </div>		
		
		<div class="pop_up_administrador" id="pop_up_administrador">
			<p class="titulo_wrapper">Administrador</p>
			<hr class="division_wrapper_2">
			
			<div class="inputs_popup">
				<p class="contact"><label for="pop_usuario">Usuario:</label></p> 
				<input id="pop_usuario" class="pop_usuario" autocomplete="off" name="pop_usuario" type="text" onkeyup="validateNpass(this.id, 'pop_password', event)">
				<p class="contact"><label for="pop_password">Contraseña:</label></p> 
				<input id="pop_password" class="pop_password" autocomplete="off" name="pop_password" type="password" onkeyup="validateNpass(this.id, 'boton_aceptar_popup_admin', event)">
			</div>			
			<br>
			<div class="buttoms_popup">
				<a href='javascript:;' onClick='closePopUp_Admin();' class='boton_desall' >Cancelar</a>
				<a href='javascript:;' onClick="clickAceptar_Admin(event)" class='boton_act_all' id="boton_aceptar_popup_admin" onkeyup="validateNpass(this.id, 'administrador', event)">Aceptar</a>
			</div>
		</div>
		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>