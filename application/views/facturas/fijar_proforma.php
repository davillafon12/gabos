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
		<title>Consulta de Proformas</title>
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
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/consulta/estilo_proformas.css'); ?>">
		<!--CARGA DEL SCRIPT DE HERRAMIENTAS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/proforma/proforma_fijar_tools.js?v='.$javascriptCacheVersion); ?>"></script>
                <!--CARGA DEL SCRIPT DE HERRAMIENTAS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/proforma/facturas_popup.js?v='.$javascriptCacheVersion); ?>"></script>
		<!--JQUERY IMPROMPTU-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-impromptu.js'); ?>"></script>
		<!--CARGA DEL POPUP MODAL-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.bpopup.min.js'); ?>"></script>
			<!--LIBRERIA ENCRYPTACION-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/cryptoJS.js'); ?>"></script>
		<!--CSS ESTILO DEL MODAL-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/jquery-impromptu.css'); ?>">
		<!--CSS ESTILO AWESOME FONT-->
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
		<script>
			var decimales = '<?php echo $this->configuracion->getDecimales();?>';
		</script>
		
		<style>
			.cant_total_articulos_div {
			    margin-left: 10px;
			}
			.cant_total_articulos_p {
			    color: #000;
			    font-weight: bold;
			    font-size: 14px;
			    font-family: Arial, Helvetica;
			    margin-top: 0px;
			    margin-bottom: 0px;
			    display: inline;
			}
			.imagen_arrow {
			    display: none;
			}
			.imagen_arrow {
			    position: absolute;
			    cursor: pointer;
			    margin-top: -2px;
			    margin-left: -10px;
			    z-index: 0;
			}
			.input_codigo_articulo {
			    width: 100%;
			    box-sizing: border-box;
			    -webkit-box-sizing: border-box;
			    -moz-box-sizing: border-box;
			    z-index: 2;
			}
			.articulo_specs {
			    color: #000;
			    font-weight: bold;
			    font-size: 14px;
			    font-family: Arial, Helvetica;
			}
			.cantidad_articulo {
			    width: 100%;
			    box-sizing: border-box;
			    -webkit-box-sizing: border-box;
			    -moz-box-sizing: border-box;
			    text-align: center;
			    color: #000;
			    font-weight: bold;
			    font-size: 14px;
			    font-family: Arial, Helvetica;
			}
			

			.pop_up_administrador {
			    display: none;
			    width: 300px;
			    height: 190px;
			    border: 1px #bfbfbf solid;
			    background-color: #f0f0f0;
			    -webkit-border-radius: 10px;
			    -moz-border-radius: 10px;
			    border-radius: 10px;
			    -moz-box-shadow: 0px 0px 8px 4px #888888;
			    -webkit-box-shadow: 0px 0px 8px 4px #888888;
			    box-shadow: 0px 0px 8px 4px #888888;
			}
			.division_wrapper_2 {
			    display: block;
			    border: none;
			    color: white;
			    height: 2px;
			    width: 75%;
			    margin-left: 0px;
			    background-image: -webkit-linear-gradient(left, rgba(0,0,0,0.75), rgba(0,0,0,0.75), rgba(0,0,0,0));
			    background-image: -moz-linear-gradient(left, rgba(0,0,0,0.75), rgba(0,0,0,0.75), rgba(0,0,0,0));
			    background-image: -ms-linear-gradient(left, rgba(0,0,0,0.75), rgba(0,0,0,0.75), rgba(0,0,0,0));
			    background-image: -o-linear-gradient(left, rgba(0,0,0,0.75), rgba(0,0,0,0.75), rgba(0,0,0,0));
			}
			
			.inputs_popup {
			    margin-left: 10px;
			}
			
			.boton_desall {
			    background: #4b8df9;
			    display: inline-block;
			    padding: 5px 10px 6px;
			    color: #fbf7f7;
			    text-decoration: none;
			    font-weight: bold;
			    line-height: 1;
			    -moz-border-radius: 5px;
			    -webkit-border-radius: 5px;
			    border-radius: 5px;
			    -moz-box-shadow: 0 1px 3px #999;
			    -webkit-box-shadow: 0 1px 3px #999;
			    box-shadow: 0 1px 3px #999;
			    text-shadow: 0 -1px 1px #222;
			    border: none;
			    cursor: pointer;
			    font-size: 14px;
			    font-family: Verdana, Geneva, sans-serif;
			}
			
			.boton_act_all {
			    margin-left: 5px;
			    margin-bottom: 5px;
			    background: #59B659;
			    display: inline-block;
			    padding: 5px 10px 6px;
			    color: #fbf7f7;
			    text-decoration: none;
			    font-weight: bold;
			    line-height: 1;
			    -moz-border-radius: 5px;
			    -webkit-border-radius: 5px;
			    border-radius: 5px;
			    -moz-box-shadow: 0 1px 3px #999;
			    -webkit-box-shadow: 0 1px 3px #999;
			    box-shadow: 0 1px 3px #999;
			    text-shadow: 0 -1px 1px #222;
			    border: none;
			    cursor: pointer;
			    font-size: 14px;
			    font-family: Verdana, Geneva, sans-serif;
			}
			
			.buttoms_popup {
			    text-align: right;
			    margin-right: 10px;
			    margin-top: -10px;
			}
			
			.pop_up_descuento {
			    display: none;
			    width: 300px;
			    height: 140px;
			    border: 1px #bfbfbf solid;
			    background-color: #f0f0f0;
			    -webkit-border-radius: 10px;
			    -moz-border-radius: 10px;
			    border-radius: 10px;
			    -moz-box-shadow: 0px 0px 8px 4px #888888;
			    -webkit-box-shadow: 0px 0px 8px 4px #888888;
			    box-shadow: 0px 0px 8px 4px #888888;
			}
			
			.pop_up_articulo {
			    display: none;
			    width: 300px;
			    height: 310px;
			    border: 1px #bfbfbf solid;
			    background-color: #f0f0f0;
			    -webkit-border-radius: 10px;
			    -moz-border-radius: 10px;
			    border-radius: 10px;
			    -moz-box-shadow: 0px 0px 8px 4px #888888;
			    -webkit-box-shadow: 0px 0px 8px 4px #888888;
			    box-shadow: 0px 0px 8px 4px #888888;
			}
			.boton_guardar_editar {
			            background: rgb(236, 176, 27);
			        float:right;
			        margin-left:5px;
				    padding: 2px 3px 3px;
				    color: #fbf7f7;
				    text-decoration: none;
				    font-weight: bold;
				    line-height: 1;
				    -moz-border-radius: 5px;
				    -webkit-border-radius: 5px;
				    border-radius: 5px;
				    -moz-box-shadow: 0 1px 3px #999;
				    -webkit-box-shadow: 0 1px 3px #999;
				    box-shadow: 0 1px 3px #999;
				    text-shadow: 0 -1px 1px #222;
				    border: none;
				    cursor: pointer;
				    font-size: 15px;
				    font-family: Verdana, Geneva, sans-serif;
				    position: relative;
				    /* top: 1.4px; */
				    display: none;
		    }
		</style>
		
		<script>
			
			<?php
			echo "var sucursal=$Sucursal_Codigo;
				  var tipoImpresion='t';";
		?>
			var puedeRepetirProducto = <?php echo $this->user->isAdministradorPorCodigo($Usuario_Codigo)?>; 
			var aplicarRetencionHacienda = <?php echo $this->configuracion->getAplicarRetencion();?>
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
			<p class="titulo_wrapper">Consulta de Proformas</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
			<div class="contenedor" style="width: 98%; margin: 0 auto;">
				<table class="tabla-filtrado">
					<tr><td><p class="contact">Filtros</p></td></tr>
					<tr>
						<td>
							<p class="contact">Cliente</p>
						</td>
						<td>
							
						</td>
					</tr>
					<tr>
						<td>
							<p class="contact">Cédula:</p>
						</td>
						<td>
							<input id="cedula" class="input_uno" placeholder="Inserte el numero de cédula" autocomplete="off" type="text" onkeyup="buscarCedula(event);" />
						</td>
					</tr>
					<tr>
						<td>
							<p class="contact">Nombre:</p>
						</td>
						<td>
							<input id="nombre" class="input_uno ui-autocomplete-input" placeholder="Inserte el nombre del cliente" autocomplete="off" type="text" />
						</td>
					</tr>
					<tr>
						
					</tr>
					<tr>
											</tr>					
				</table>
				<div class="contenedor-facturas" style="top: -190px; margin-bottom: -200px;">
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
				<input type="button" class="boton-carga" onclick="cargarFactura()" value="Cargar"/>
				<input type="button" class="boton_busqueda" onclick="procesarProforma()" id="boton_procesar" value="Procesar" style="    background:rgba(142, 68, 173, 0.54); float:right;     margin-left: 10px;     cursor: not-allowed;" disabled/>
				<a href="javascript:;" onclick="actualizarFactura()" id="boton_guardar_editar" class="boton_guardar_editar" title="Guardar proforma editada" ><i class="fa fa-floppy-o"></i></a>
				<input type="button" class="boton_busqueda" onclick="makeProformaEditable()" id="boton_editar" value="Editar" style="    background:rgba(236, 176, 27, 0.54); float:right;     margin-left: 10px;     cursor: not-allowed;" disabled/>

				<select id="tipo_moneda" class="moneda" name="moneda" style="display:none" disabled>
					<option value="colones" selected="">Colones - ₡</option>
					<option value="dolares">Dolares - &#036;</option>
				</select>
				<input id="cantidad_decimales" type="hidden" value="<?php echo $c_array['cantidad_decimales'];?>">
				<input id="iva_porcentaje" type="hidden" value="<?php echo $c_array['iva'];?>">
				<input id="tipo_cambio_venta" type="hidden" value="<?php echo $c_array['dolar_venta'];?>">
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
		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>