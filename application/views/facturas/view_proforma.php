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
		<title>Proforma</title>
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
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/Facturas/factura_nueva_style.css'); ?>">
		
		<!--SCRIPT DE EXPIRACION DE LA SESION-->
		<?php include '/../header/log_out_from_browser_Script.php';?>
		
		<!--CARGA DEL JQUERY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>"></script>
		<!--CARGA DEL JQUERYUI-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.js'); ?>"></script>
		<!--CSS ESTILO DEL JQUERYUI-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.css'); ?>">
		
		<!--CARGA DEL POPUP MODAL-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.bpopup.min.js'); ?>"></script>
		<!--CARGA DEL NOTY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>"></script>
		
		<!--CARGA DEL TOOLTIP-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/proforma/facturas_tooltips.js'); ?>"></script>
		<!--SCRIPT DE LLAMADAS AJAX-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/proforma/facturas_calls.js'); ?>"></script>
		<!--SCRIPT DE HERRAMIENTAS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/proforma/facturas_tools.js'); ?>"></script>
		<!--SCRIPT DE CAMBIO MONEDA-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/proforma/facturas_currency.js'); ?>"></script>
		<!--CARGA DE LAS HERRAMIENTAS DEL POPUP MODAL-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/proforma/facturas_popup.js'); ?>"></script>
		
		<!--BUSQUEDA POR NOMBRE-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/proforma/facturas_searchName.js'); ?>"></script>
		<!--LIBRERIA ENCRYPTACION-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/cryptoJS.js'); ?>"></script>
		<!--HERRAMIENTAS PARA ENVIO A CAJA-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/proforma/factura_envio_caja.js'); ?>"></script>
		
		<script>
			var puedeRepetirProducto = <?php echo $this->user->isAdministradorPorCodigo($Usuario_Codigo)?>; 
			var aplicarRetencionHacienda = <?php echo $this->configuracion->getAplicarRetencion();?>
		</script>
	
	</head>
	<body onload="setMainValues()" oncopy="return false" oncut="return false" onpaste="return false">
		<!--Incluir imagen de cabezera-->
		<?php include '/../Header/Header_Picture.php';?>
		
		<!--Incluir menu principal-->
		<?php include '/../Header/selector_menu.php';?>

		<!--Incluir informacion log in-->
		<?php include '/../Header/Log_In_Information.php';?>
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Proforma</p>
			<hr class="division_wrapper">
			
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
					<!--<p class="contact"><label for="vendedor">Vendedor:&nbsp;<?php echo $Usuario_Nombre." ".$Usuario_Apellidos;?></label></p> 
					<input id="vendedor_id" autocomplete="off" name="vendedor_id" type="hidden" value="<?php echo $Usuario_Codigo;?>" disabled>
					-->
				</td>
    			</tr>
				<tr>
				<td>
					<p class="contact"><label for="cedula">Cédula:</label></p>
				</td>
				<td>
					<input id="cedula" class="input_uno" placeholder="Inserte el numero de cédula" name="cliente" type="text" onkeyup="buscarCedula(event);" required="" tabindex='1' autocomplete="" >					
					<script>document.getElementById('cedula').focus();</script>
				</td>
				<td>
					<p class="contact"><label for="moneda">Moneda:</label></p>
				</td>
				<td>
					<select id="tipo_moneda" onChange="cambiarDisplayMoneda(this.value);" class="moneda" name="moneda">
						<option value="colones">Colones - ₡</option>
						<option value="dolares">Dolares - &#036;</option>
					</select>					
				</td>
				<td>
					<p class="contact"><label for="vendedor">Vendedor:&nbsp;<?php echo $Usuario_Nombre." ".$Usuario_Apellidos;?></label></p> 
					<input id="vendedor_id" autocomplete="off" name="vendedor_id" type="hidden" value="<?php echo $Usuario_Codigo;?>" disabled>
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
					
				</td>
				<td>
					
				</td>
				<!--
				<td>
					<p class="contact"><label for="tipo">Tipo:</label></p>
				</td>
				<td>
					<input type="hidden" name="tipo" onClick="numTransaccion(this.value)" value="contado" checked><div class="tipos_de_pago">Contado</div>
					<input type="hidden" name="tipo" onClick="numTransaccion(this.value)" value="tarjeta"><div class="tipos_de_pago">Tarjeta</div>
					<input type="hidden" name="tipo" onClick="numTransaccion(this.value)" value="cheque"><div class="tipos_de_pago">Cheque</div>
					<input type="hidden" name="tipo" onClick="numTransaccion(this.value)" value="deposito"><div class="tipos_de_pago">Deposito</div>
					<input type="hidden" name="tipo" onClick="numTransaccion(this.value)" value="mixto"><div class="tipos_de_pago">Mixto</div>
				</td>
				<td id="numero_transaccion">
					
				</td>
				-->
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
				<tbody>
				
				<?php
				//<input id='cantidad_articulo_".$numero_id."' class='cantidad_articulo' autocomplete='off' name='cantidad_articulo' type='number' min='1' onchange='actualizaCostoTotalArticulo(this.value, this.id, event);' onkeyup='manejarEventoCantidadArticulo(event, this.value, this.id);' disabled>
				//Creamos las filas de la tabla
				for($contador=0;$contador<10;$contador++)
				{
					$numero_id = $contador+1;
					$numero_tab = $contador+2;
					echo "<tr id='articulo_".$numero_id."'>
						<td>	
							<img class='imagen_arrow' title='Agregar Fila' src='/../application/scripts/Images/agregar_row.gif' width='14' height='7' onClick='agregarByCM(".$numero_id.")'/>
							<input tabindex='".$numero_tab."' id='codigo_articulo_".$numero_id."' class='input_codigo_articulo' autocomplete='off' name='codigo_articulo' type='text' onkeyup='buscarArticulo(event, this.value, this.id);' onkeydown='filtrarKeys(event, this.id);' disabled>
							<input id='codigo_articulo_anterior_".$numero_id."' type='hidden' >
						</td>
						<td>
							<div class='articulo_specs' id='descripcion_articulo_".$numero_id."'></div>
							<div class='tooltip_imagen_articulo' id='tooltip_imagen_articulo_".$numero_id."'></div>
						</td>
						<td>
							<input id='cantidad_articulo_".$numero_id."' class='cantidad_articulo' autocomplete='off' name='cantidad_articulo' type='number' min='1' onchange='cambiarCantidad(this.id, event, this.value);' onkeyup='cambiarCantidad(this.id, event, this.value);' disabled>
							<input id='cantidad_articulo_anterior_".$numero_id."' type='hidden' value='-1'>
						</td>
						<td>
							<div class='articulo_specs' id='bodega_articulo_".$numero_id."'></div>
						</td>						
						<td>
							<div class='articulo_specs' id='descuento_articulo_".$numero_id."' ondblclick='changeDiscount(".$numero_id.")'></div>
						</td>
						<td>
							<div class='articulo_specs' id='costo_unidad_articulo_".$numero_id."'></div>
							<input id='costo_unidad_articulo_ORIGINAL_".$numero_id."' type='hidden' >
							<input id='costo_unidad_articulo_FINAL_".$numero_id."' type='hidden' >
							<input id='producto_exento_".$numero_id."' type='hidden' >
						</td>
						<td>
							<div class='articulo_specs' id='costo_total_articulo_".$numero_id."'></div>
						</td>
					</tr>";
				}
				
				?>
				</tbody>				
			</table>
			
			<div class="cant_total_articulos_div">
				<p class="cant_total_articulos_p">Cantidad Total de Articulos:</p>
				<p class="cant_total_articulos_p" id="cant_total_articulos">0</p>
			</div>
			
			
			<div class="observaciones_div">
				<p class="contact"><label for="observaciones">Observaciones:</label></p>
				<textarea id="observaciones" autocomplete="off" class="observaciones" placeholder="" name="observaciones" cols="25" rows="5" maxlength="150" >Esta proforma tiene una validez de solo 15 días, a partir del día de creación</textarea> 
    			<p class="advertencia_longitud">Máximo 150 caracteres</p>				
			</div>
			<div class="tabla_costos">
				<table>
					<tr>
					<td>
						<p class="contact"><label for="ganancia">Ganancia:</label></p> 
					</td>
					<td>
						<div id="tipo_moneda_display" class="tipo_moneda_display">₡</div>
						<input id="ganancia" class="input_dos" autocomplete="off" name="ganancia" type="text" disabled> 					
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
					<tr>
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
		<div class="botones_final">
		<a href='javascript:;' onClick='resetAll();' class='boton_desall' >Limpiar</a>
		<a href='javascript:;' onClick='toCajaSubmit()' class='boton_act_all' >Guardar e Imprimir</a>
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
		
		<div class="envio_factura" id="envio_factura">
			<img class="envio_img" src="<?php echo base_url('application/images/enviandoFactura.gif'); ?>">
			<p class="envio_p">Enviando proforma... <br>Por favor, espere.</p>
		</div>
		
		<!--<div class="error_crear_factura_popup" id="error_crear_factura_popup">
			<p class="titulo_wrapper">Error al crear factura</p>
			<hr class="division_wrapper_2">
			<p class="contact">Por favor recargue la página.<br>
			                   Si la situación continúa, favor contacte al encargado de soporte.<br>
							   <a href='<?php //echo base_url('facturas/nueva');?>'> Recargar página</a><br>
							   <a href='<?php// echo base_url('home');?>'> Ir a inicio</a>
							   </p> 	
		</div>
		
		<div class="salida_pagina" id="salida_pagina">
			<p class="titulo_wrapper">¿Deshacer factura?</p>
			<hr class="division_wrapper_2">
			<p class="contact">Está a punto de salir de la página.<br>
			                   ¿Desea eliminar la factura?<br>
							   <div class="botones_salida">
							   <a href='<?php //echo base_url('facturas/nueva');?>' class='boton_salida_S'> Sí</a>
							   <a href='<?php //echo base_url('home');?>' class='boton_salida_N'> No</a>
							   </div>
							   </p> 
		</div>-->
			
				<!--<div id="timeout_show"></div>-->
        </div>		

		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>