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
		<title>Caja</title>
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
		<!--CSS ESTILO DE LA FACTURA-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/facturas/factura_nueva_style.css'); ?>">
		<!--CSS ESTILO DEL SELECTOR DE FACTURAS-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/facturas/factura_caja_style.css'); ?>">
		<!--CSS ESTILO DEL MODAL-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/jquery-impromptu.css'); ?>">
		<!--SCRIPT DE EXPIRACION DE LA SESION-->
		<?php include PATH_LOG_OUT_HEADER;?>

		<!--CARGA DEL JQUERY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>"></script>
		<!--CARGA DEL JQUERYUI-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.js'); ?>"></script>
		<!--CSS ESTILO DEL JQUERYUI-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.css'); ?>">
		<!--CSS ESTILO AWESOME FONT-->
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

		<!--CARGA DEL POPUP MODAL-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.bpopup.min.js'); ?>"></script>
		<!--CARGA DEL NOTY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>"></script>

		<!--CARGA DEL TOOLTIP-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/facturas_tooltips.js?v='.$javascript_cache_version); ?>"></script>
		<!--SCRIPT DE LLAMADAS AJAX-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/facturas_calls.js?v='.$javascript_cache_version); ?>"></script>
		<!--SCRIPT DE HERRAMIENTAS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/facturas_tools.js?v='.$javascript_cache_version); ?>"></script>
		<!--SCRIPT DE CAMBIO MONEDA-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/facturas_currency.js?v='.$javascript_cache_version); ?>"></script>
		<!--CARGA DE LAS HERRAMIENTAS DEL POPUP MODAL-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/facturas_popup.js?v='.$javascript_cache_version); ?>"></script>
		<!--CARGA DE LAS HERRAMIENTAS DE INVENTARIO-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/facturas_inventario.js?v='.$javascript_cache_version); ?>"></script>
		<!--BUSQUEDA POR NOMBRE-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/facturas_searchName.js?v='.$javascript_cache_version); ?>"></script>
		<!--LIBRERIA ENCRYPTACION-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/cryptoJS.js?v='.$javascript_cache_version); ?>"></script>
		<!--HERRAMIENTAS PARA ENVIO A CAJA-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/factura_envio_caja.js?v='.$javascript_cache_version); ?>"></script>
		<!--HERRAMIENTAS PARA Facturas pendiente-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/facturas_pendientes.js?v='.$javascript_cache_version); ?>"></script>
		<!--HERRAMIENTAS PARA COBRO FACTURA-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/caja_cobrar_tools.js?v='.$javascript_cache_version); ?>"></script>
		<!--JQUERY IMPROMPTU-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-impromptu.js?v='.$javascript_cache_version); ?>"></script>
		<!--CARGA DE PROFORMAS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/factura_cargar_proforma.js?v='.$javascript_cache_version); ?>"></script>

		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/api-alert.css?v='.$javascript_cache_version); ?>">

		<script><?php
			echo "var sucursal=$Sucursal_Codigo;
				  var tipoImpresion='t';";
		?>
			var token_factura_temporal = '<?php echo $token_factura_temp;?>';
			var puedeRepetirProducto = <?php echo $this->user->isAdministradorPorCodigo($Usuario_Codigo)?>;
			var aplicarRetencionHacienda = <?php echo $this->configuracion->getAplicarRetencion();?>
		</script>

		<!--JQEURY SOLO NUMEROS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.numeric.js'); ?>"></script>



	</head>
	<body onload="setMainValues()" oncopy="return false" oncut="return false" onpaste="return false" style="min-width:1280px;">


		<script>
			//Anulamos los eventos de salida
			window.onbeforeunload=null;
			window.onunload=null;
		</script>

		<!--Incluir imagen de cabezera-->
		<?php include PATH_HEADER_PICTURE;?>

		<!--Incluir menu principal-->
		<?php include PATH_HEADER_SELECTOR_MENU;?>

		<!--Incluir informacion log in-->
		<?php include PATH_HEADER_LOG_IN_INFO;?>

		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Caja</p>
			<hr class="division_wrapper">

			<a href='javascript:;' onClick='mostrarFacturas();' id="boton_mostrarFactura" class='boton_mostrarFactura' >Mostrar Facturas</a>
			<a href='javascript:;' onClick='anularFactura()' class='boton_anular' >Anular</a>
			<?php if($puedeEditarFacturas){ ?>
			<a href='javascript:;' onClick='editarFactura()' class='boton_editar' >Editar</a>
			<?php }; ?>
			<a href='javascript:;' onClick='actualizarFactura()' id="boton_guardar_editar" class='boton_guardar_editar' title="Guardar factura editada"><i class="fa fa-floppy-o"></i></a>
			<a href='javascript:;' onClick='cobrarFactura()' class='boton_cobrar' >Cobrar</a>
		<div class="selector_facturas" id="selector_facturas">
			<table id='facturas_pendientes' class='facturas_pendientes'>
				<thead>
				</thead>
				<tbody id='cuerpoTablaPendientes'>
				</tbody>
			</table>
		</div>
		<script>getFacturas();</script>
		<?php
				$attributes = array('name' => 'envio_factura_creada', 'class' => 'envio_factura_creada', 'id' => 'envio_factura_creada');
				echo form_open('facturas/nueva/enviarCaja');

			?>



		<div class="factura_wrapper" >
			<input id="cantidad_decimales" type="hidden" value="<?php echo $c_array['cantidad_decimales'];?>">
			<input id="iva_porcentaje" type="hidden" value="<?php echo $c_array['iva'];?>">
			<input id="tipo_cambio_venta" type="hidden" value="<?php echo $c_array['dolar_venta'];?>">
			<table>
				<tr>
				<td>
					<p class="contact"><label for="cliente">Cliente</label></p>
				</td>
				<td>
				</td>
				<td>
				</td>
				<td>
				</td>
				<td>
				</td>
    			</tr>
				<tr>
				<td>
					<p class="contact"><label for="cedula">Cédula:</label></p>
				</td>
				<td>
					<input id="cedula" class="input_uno" name="cliente" type="text" onkeyup="buscarCedula(event);" required="" tabindex='1' disabled>
					<script>//document.getElementById('cedula').focus();</script>
				</td>
				<td>
					<p class="contact" style="display: inline;"><label for="moneda">Moneda:</label></p>
					<select id="tipo_moneda" onChange="cambiarDisplayMoneda(this.value);" class="moneda" name="moneda" disabled>
						<option value="colones">Colones - ₡</option>
						<option value="dolares">Dolares - &#036;</option>
					</select>
				</td>
				<td>
					<p class="contact" style="display:inline;"><label for="proforma">Proforma:</label></p>
					<input id="proforma" class="input_uno" name="proforma" type="text" required="" style="width:80px;">
					<img id="imagen_Cargar_proforma" src="<?php echo base_url('application/images/ajax-loader.gif'); ?>" />
					<script>
						$("#imagen_Cargar_proforma").hide();
						$("#proforma").keypress(function(e) {
							if(e.which == 13) {
								proforma = $("#proforma").val();
								if(isNumber(proforma)){
									$("#imagen_Cargar_proforma").show();
									setProforma(proforma);
								}
							}
						});
					</script>
				</td>
				<td>
					<p class="contact" style="display:inline;"><label for="impresion">Impresión:</label></p>
					<select id="tipo_impresion" onChange="cambiarTipoImpresion(this.value)" class="impresion" name="impresion" >
						<option value="t">PV</option>
						<option value="c">A4</option>
					</select>
				</td>
				<td>

				</td>
				<td>

				</td>
				</tr>
				<tr>
					<td>
						<p class="contact"><label for="nombre">Nombre:</label></p>
					</td>
					<td>
						<input id="nombre" class="input_uno" autocomplete="off" name="nombre" type="text" disabled>
						<script>setUpLiveSearch();</script>
					</td>
					<td>
						<p class="contact"><label for="vendedor" id="vendedor"></label></p>
					</td>
					<td>
						<p class="contact"><label for="cantidad_mixto" id="cantidad_mixto_label"></label></p>
					</td>
					<td >
						<p class="contact"><label for="monto_efectivo_mixto" id="monto_efectivo_mixto_label"></label></p>
					</td>
					<td >
						<p class="contact"><label for="banco" id="banco"></label></p>
					</td>
					<td>

					</td>
				</tr>
				<tr>
					<td>
						<p class="contact"><label for="tipo">Tipo:</label></p>
					</td>
					<td>
						<input type="radio" name="tipo" onClick="numTransaccion(this.value)" value="contado" ><div class="tipos_de_pago">Contado</div>

						<input type="radio" name="tipo" onClick="numTransaccion(this.value)" value="tarjeta" checked><div class="tipos_de_pago">Tarjeta</div>

						<input type="radio" name="tipo" onClick="numTransaccion(this.value)" value="cheque"><div class="tipos_de_pago">Cheque</div>

						<input type="radio" name="tipo" onClick="numTransaccion(this.value)" value="deposito"><div class="tipos_de_pago">Deposito</div>
						<br>
						<input type="radio" name="tipo" onClick="numTransaccion(this.value)" value="mixto"><div class="tipos_de_pago">Mixto</div>

						<input type="radio" name="tipo" onClick="numTransaccion(this.value)" value="credito"><div class="tipos_de_pago">Crédito</div>

						<input type="radio" name="tipo" onClick="numTransaccion(this.value)" value="apartado"><div class="tipos_de_pago">Apartado</div>
					</td>
					<td id="numero_transaccion_container">
					</td>
					<td id="cantidad_mixto_tarjeta">
					</td>
					<td id="monto_efectivo_mixto">
					</td>
					<td>
						<select id="banco_sel" class="moneda" name="banco_sel" style="width: 250px; display: none;">
							<?php
								foreach($bancos as $banco){
									echo "<option value='".$banco->Banco_Codigo."'>".$banco->Banco_Nombre."</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="boton-salto-linea on" id="boton_salto_linea">Saltar al cargar</div>
					</td>
				</tr>
			</table>

			<table id="tabla_productos" class="tabla_productos" >
				<thead>
					<th class="th_codigo">Código</th>
					<th class="th_descripcion">Descripción</th>
					<th class="th_cantidad">Cantidad</th>
					<th class="th_bodega">Inventario</th>
					<th class="th_descuento">Descuento</th>
					<th class="th_costo_unidad">Precio por unidad</th>
					<th class="th_costo_total">Precio total</th>
				</thead>
				<tbody id="contenidoArticulos" class="contenidoArticulos">


				</tbody>
			</table>

			<div class="cant_total_articulos_div">
				<p class="cant_total_articulos_p">Cantidad Total de Articulos:</p>
				<p class="cant_total_articulos_p" id="cant_total_articulos">0</p>
				<span style="    color: red; font-size: 9px; float: right; margin-top: 2px; margin-right: 10px;">* EL INVENTARIO MOSTRADO AQUI NO TOMA EN CUENTA LAS UNIDADES DE OTRAS FACTURAS PENDIENTES</span>
			</div>


			<div class="observaciones_div">
				<p class="contact"><label for="observaciones">Observaciones:</label></p>
				<textarea id="observaciones" autocomplete="off" class="observaciones" placeholder="" name="observaciones" cols="25" rows="5" maxlength="150" disabled></textarea>
    			<p class="advertencia_longitud">Máximo 150 caracteres</p>
			</div>
			<div class="tabla_costos">
				<table>
					<tr>
					<td>
						<p class="contact"><label for="ganancia"><!--Ganancia:--></label></p>
					</td>
					<td>
						<div id="tipo_moneda_display" class="tipo_moneda_display" style="display:none;"><!--₡--></div>
						<input id="ganancia" class="input_dos" autocomplete="off" name="ganancia" type="hidden" min="0" disabled>
					</td>
					</tr>
					<tr>
					<td>
						<p class="contact"><label for="costo">Monto:</label></p>
					</td>
					<td>
						<div id="tipo_moneda_display" class="tipo_moneda_display">₡</div>
						<input id="costo" class="input_dos" autocomplete="off" name="costo" type="text" disabled>
					</td>
					</tr>
					<tr>
					<td>
						<p class="contact"><label for="iva">IVA:</label></p>
					</td>
					<td>
						<div id="tipo_moneda_display" class="tipo_moneda_display">₡</div>
						<input id="iva" class="input_dos" autocomplete="off" name="iva" type="text" disabled>
					</td>
					</tr>
					<tr  style="display:none;">
					<td>
						<p class="contact"><label for="retencion">Retención:</label></p>
					</td>
					<td>
						<div id="tipo_moneda_display" class="tipo_moneda_display">₡</div>
						<input id="retencion" class="input_dos" autocomplete="off" name="retencion" type="text" disabled>
					</td>
					</tr>
					<tr>
					<td>
						<p class="contact"><label for="costo_total">Monto Total:</label></p>
					</td>
					<td>
						<div id="tipo_moneda_display" class="tipo_moneda_display">₡</div>
						<input id="costo_total" class="input_dos" autocomplete="off" name="costo_total" type="text" disabled>
					</td>
					</tr>
				</table>
			</div>
			<div style="clear: both;"></div>
		</div>

		</form>

		<div class="pop_up_articulo" id="pop_up_articulo">
			<p class="titulo_wrapper">Producto Genérico</p>
			<hr class="division_wrapper_2">

			<div class="inputs_popup">
				<p class="contact"><label for="pop_descripcion">Descripción:</label></p>
				<input id="pop_descripcion" class="pop_descripcion" autocomplete="off" name="pop_descripcion" type="text" value="Producto genérico" onkeyup="validateNpass(this.id, 'pop_cantidad', event)">
				<p class="contact"><label for="pop_cantidad">Cantidad:</label></p>
				<input id="pop_cantidad" class="pop_cantidad" autocomplete="off" name="pop_cantidad" type="number" value="1" onkeyup="validateNpass(this.id, 'pop_descuento', event)">
				<p class="contact"><label for="pop_inventario">Inventario:</label></p>
				<input id="pop_inventario" class="pop_inventario" autocomplete="off" name="pop_inventario" type="number" value="1000" disabled>
				<p class="contact"><label for="pop_descuento">Descuento:</label></p>
				<input id="pop_descuento" class="pop_descuento" autocomplete="off" name="pop_descuento" type="text" value="0" onkeyup="validateNpass(this.id, 'pop_costo_unidad', event)">
				<p class="contact"><label for="pop_costo_unidad">Precio por unidad:</label></p>
				<input id="pop_costo_unidad" class="pop_costo_unidad" autocomplete="off" name="pop_costo_unidad" type="text" value="0" onkeyup="validateNpass(this.id, 'boton_aceptar_popup', event)">
			</div>
			<br>
			<div class="buttoms_popup">
				<a href='javascript:;' onClick='closePopUp();' class='boton_desall' >Cancelar</a>
				<a href='javascript:;' onClick='clickAceptar();' onkeyup="validateNpass(this.id, '', event)" class='boton_act_all' id="boton_aceptar_popup">Aceptar</a>
			</div>
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
				<a href='javascript:;' onClick='clickAceptar_Admin(event);' class='boton_act_all' id="boton_aceptar_popup_admin" onkeyup="validateNpass(this.id, 'administrador', event)">Aceptar</a>
			</div>
		</div>


		<div class="pop_up_descuento" id="pop_up_descuento">
			<p class="titulo_wrapper">Descuento</p>
			<hr class="division_wrapper_2">

			<div class="inputs_popup">
				<p class="contact"><label for="pop_descuento_cambio">Ingrese el nuevo descuento:</label></p>
				<input id="pop_descuento_cambio" class="pop_descuento_cambio" autocomplete="off" name="pop_descuento_cambio" type="number" min='0' max='100' onkeyup="validateNpass(this.id, 'boton_aceptar_popup_desc', event)" value='0'>
			</div>
			<br>
			<div class="buttoms_popup">
				<a href='javascript:;' onClick='closePopUp_Des();' class='boton_desall' >Cancelar</a>
				<a href='javascript:;' onClick='clickAceptar_Des();' class='boton_act_all' id="boton_aceptar_popup_desc" >Aceptar</a>
			</div>
		</div>

		<div class="pop_up_cantidad" id="pop_up_cantidad">
			<p class="titulo_wrapper">Agregar Cantidad</p>
			<hr class="division_wrapper_2">

			<div class="inputs_popup">
				<p class="contact"><label for="pop_cantidad_agregar">Ingrese cuantas unidades mas:</label></p>
				<input id="pop_cantidad_agregar" class="pop_cantidad_agregar" autocomplete="off" name="pop_cantidad_agregar" type="number" min='0' onkeyup="validateNpass(this.id, 'boton_aceptar_popup_cantidad', event)" value='0'>
			</div>
			<br>
			<div class="buttoms_popup">
				<a href='javascript:;' onClick='closePopUp_Can();' class='boton_desall' >Cancelar</a>
				<a href='javascript:;' onClick='clickAceptar_Can();' class='boton_act_all' id="boton_aceptar_popup_cantidad" >Aceptar</a>
			</div>
		</div>

		<div class="pop_up_pago_vuelto" id="pop_up_pago_vuelto">

			<div class="inputs_popup">
				<table>
					<tr>
						<td>
							<p class="titulo_wrapper">Total:</p>
						</td>
						<td>
							<p class="titulo_wrapper montos_vuelto_p" id="cuadro_vuelto_total">0</p>
						</td>
					</tr>
					<tr>
						<td>
							<p class="titulo_wrapper">Entregado:</p>
						</td>
						<td>
							<input id="pop_cantidad_a_pagar" class="pop_cantidad_a_pagar montos_vuelto_p" autocomplete="off" onkeyup="moverAceptarBoton(event, this.value)">
							<script>
								$("#pop_cantidad_a_pagar").numeric();
							</script>
						</td>
					</tr>
					<tr>
						<td>
							<p class="titulo_wrapper">Vuelto:</p>
						</td>
						<td>
							<p class="titulo_wrapper montos_vuelto_p" id="vueltoDar">0</p>
						</td>
					</tr>
				</table>
			</div>
			<br>
			<div class="buttoms_popup">
				<a href='javascript:;' onClick='cerrarVuelto()' class='boton_desall' >Cancelar</a>
				<a href='javascript:;' onClick='cobrarEImprimirPostPopUp()' class='boton_act_all' id="boton_aceptar_popup_vuelto" >Cobrar e Imprimir</a>
			</div>
		</div>

		<div class="envio_factura" id="envio_factura">
			<img class="envio_img" src="<?php echo base_url('application/images/enviandoFactura.gif'); ?>">
			<p class="envio_p">Cobrando factura... <br>Por favor, espere.</p>
		</div>

                <div class="envio_factura" id="envio_anulacion">
			<img class="envio_img" src="<?php echo base_url('application/images/enviandoFactura.gif'); ?>">
			<p class="envio_p">Anulando factura... <br>Por favor, espere.</p>
		</div>

		<!--<div class="error_crear_factura_popup" id="error_crear_factura_popup">
			<p class="titulo_wrapper">Error al crear factura</p>
			<hr class="division_wrapper_2">
			<p class="contact">Por favor recargue la página.<br>
			                   Si la situación continúa, favor contacte al encargado de soporte.<br>
							   <a href='<?php echo base_url('facturas/nueva');?>'> Recargar página</a><br>
							   <a href='<?php echo base_url('home');?>'> Ir a inicio</a>
							   </p>
		</div>

		<div class="salida_pagina" id="salida_pagina">
			<p class="titulo_wrapper">¿Deshacer factura?</p>
			<hr class="division_wrapper_2">
			<p class="contact">Está a punto de salir de la página.<br>
			                   ¿Desea eliminar la factura?<br>
							   <div class="botones_salida">
							   <a href='<?php echo base_url('facturas/nueva');?>' class='boton_salida_S'> Sí</a>
							   <a href='<?php echo base_url('home');?>' class='boton_salida_N'> No</a>
							   </div>
							   </p>
		</div>-->

				<!--<div id="timeout_show"></div>-->
        </div>

		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>