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
		<title>Notas Credito</title>
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
                    <!--CARGA DEL POPUP MODAL-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.bpopup.min.js'); ?>"></script>
		<!--CSS ESTILO ESPECIFICO DE LA PAGINA-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/contabilidad/contabilidad_notas_credito.css?v='.$javascript_cache_version); ?>">	
		<!--CARGA DE HERRAMIENTAS DE ESTILO-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/contabilidad/notas/estilo_notas.js?v='.$javascript_cache_version); ?>"></script>
		<!--CARGA DE FACTURAS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/contabilidad/notas/carga_facturas.js?v='.$javascript_cache_version); ?>"></script>
		<!--CARGA DE PRODUCTOS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/contabilidad/notas/carga_productos.js?v='.$javascript_cache_version); ?>"></script>
		<!--GESTOR DE FACTURA A APLICAR-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/contabilidad/notas/gestor_factura_aplicar.js?v='.$javascript_cache_version); ?>"></script>
		<!--SELECCION Y MARCA DE ARTICULOS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/contabilidad/notas/marca_seleccion_articulos.js?v='.$javascript_cache_version); ?>"></script>
		<!--ENVIO DE NOTA-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/contabilidad/notas/envio_nota_credito.js?v='.$javascript_cache_version); ?>"></script>
		<!--NUMERIC-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.numeric.js'); ?>"></script>
	</head>
	<body onload="cambiarEstiloBotones()">
		<!--Incluir imagen de cabezera-->
		<?php include PATH_HEADER_PICTURE;?>
		
		<!--Incluir menu principal-->
		<?php include PATH_HEADER_SELECTOR_MENU;?>

		<!--Incluir informacion log in-->
		<?php include PATH_HEADER_LOG_IN_INFO;?>		
	
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Notas Credito</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
			<div class="contenedor">
				<table>
					<tr>
						<td>
							<p class="contact"><label for="cliente">Cliente</label></p>
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
							<p class="contact" style="display:inline;"><label for="impresion">Impresión:</label></p> 					
							<select id="tipo_impresion" onChange="cambiarTipoImpresion(this.value)" class="impresion" name="impresion" >
								<option value="t">PV</option>
								<option value="c">A4</option>
							</select>
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
					</tr>
				</table>
				<hr class="division-contenido">
				<div class="selectores_facturas">
					<div class="div_facturas">
						<div class="titulo_cuadro">
							<p class="contact"><label >Facturas:</label></p>
						</div>
						<div class="cuerpo_cuadro">
							<table class="tabla_facturas" id="tabla_facturas">
								<thead>
									<tr class="header">
										<th class="titulo_header_tabla" style="width: 110px;">Consecutivo</th>
										<th class="titulo_header_tabla" style="width: 110px;">Fecha</th>
										<th class="titulo_header_tabla" style="width: 110px;">Monto</th>
									</tr>
								</thead>
								<tbody id="tbody_facturas">
								
								</tbody>
							</table>
						</div>
					</div>
				</div><!-- selectores_facturas -->
				<div class="filtro_codigo">
					<table>
						<tr>
							<td>
								<p class="contact"><label for="codigo_busqueda">Código:</label></p>
							</td>
							<td>
								<input id="codigo_busqueda" class="input_uno" placeholder="Inserte código para filtrar" name="codigo_busqueda" autocomplete="off" type="text" onkeyup="filtrarFacturasPorCodigo(this.value)" >					
							</td>
						</tr>
						<tr>
							<td>
								<p class="contact"><label for="factura_aplicar">Factura a aplicar:</label></p>
							</td>
							<td>
								<input id="factura_aplicar" class="input_uno" placeholder="Inserte consecutivo de factura" name="factura_aplicar" autocomplete="off" type="text" onkeyup="validarFacturaAplicar(this.value)" >					
							</td>
						</tr>
					</table>
				</div>
				<hr class="division-contenido">
				<div class="selectores_productos">
					<div class="div_productos">
						<div class="titulo_cuadro">
							<p class="contact"><label >Productos de la Factura:</label></p>
						</div>
						<div class="cuerpo_cuadro_grande">
							<table class="tabla_productos" id="tabla_productos">
								<thead>
									<tr class="header">
										<th class="titulo_header_tabla" style="width: 70px;">Código</th>
										<th class="titulo_header_tabla" >Descripción</th>
										<th class="titulo_header_tabla" style="width: 70px;">Cantidad</th>
									</tr>
								</thead>
								<tbody id="tbody_productos">
								
								</tbody>
							</table>
						</div>
					</div>
				</div><!-- selectores_productos -->
				
				<div class="selectores_productos_seleccionados">
					<div class="div_productos_seleccionados">
						<div class="titulo_cuadro">
							<p class="contact"><label >Productos a Descontar de la Factura:</label></p>
						</div>
						<div class="cuerpo_cuadro_grande">
							<table class="tabla_productos_seleccionados" id="tabla_productos_seleccionados">
								<thead>
									<tr class="header">
										<th class="titulo_header_tabla" style="width: 70px;">Código</th>
										<th class="titulo_header_tabla" >Descripción</th>
										<th id="titulo_cantidad_original" class="titulo_header_tabla titulo-cantidad" style="width: 30px;" title="Cantidad Original Facturada">C</th>
										<th id="titulo_cantidad_defectuosa" class="titulo_header_tabla titulo-cantidad" style="width: 30px;" title="Cantidad Defectuosa">D</th>
										<th id="titulo_cantidad_buena" class="titulo_header_tabla titulo-cantidad" style="width: 30px;" title="Cantidad en Buen Estado">B</th>
									</tr>
								</thead>
								<tbody id="tbody_productos_seleccionados">
								
								</tbody>
							</table>
						</div>
					</div>
				</div><!-- selectores_productos -->
				<div class="div_botones_acciones" id="div_botones_acciones">
					<button class="boton_accion" onclick="agregarProductosACreditar()"><img title="Agregar Facturas Seleccionadas" src="<?php echo base_url('application/images/recibos/flecha_derecha.png');?>"></img></button>
					<button class="boton_accion" onclick="eliminarProductosSeleccion()"><img title="Quitar Facturas Seleccionadas" src="<?php echo base_url('application/images/recibos/flecha_izquierda.png');?>"></img></button>
					
				</div><!-- div_botones_acciones -->
				<hr class="divisor_footer">
				<div class="footer_notas">	
					<div class="contenedor-producto-generico" style="    width: 600px; display: inline-block;     float: left;     text-align: left;">
						<label class="contact">Artículo Genérico:</label>
						<table  class="titulo_header_tabla" style="text-align:left;">
							<tr>
								<td>
									Descripción:
								</td>
								<td>
									<input type="text" id="descripcion_articulo_generico" class="input_uno" style="width: 400px;"/>
								</td>
							</tr>
							<tr>
								<td>
									Precio:
								</td>
								<td>
									<input type="text" id="precio_articulo_generico" class="input_uno" style="width: 100px;"/>
								</td>
							</tr>
						</table>
						<button class="boton-articulo-generico" id="boton_agregar_articulo_generico">Agregar</button>
					</div>				
					<button class="boton_generar" id="boton_generar" onclick="enviarNotaCredito()">Generar Nota</button>
				</div>
			</div><!-- contenedor -->			
		</div><!-- main_wrapper -->
                <div class="envio_nota" id="envio_nota" style="display: none; text-align: center;">
			<img class="envio_img" src="<?php echo base_url('application/images/enviandoFactura.gif'); ?>">
			<p class="envio_p">Creando nota de crédito... <br>Por favor, espere.</p>
		</div>
		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>