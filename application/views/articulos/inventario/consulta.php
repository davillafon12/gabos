<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Consulta | Control de Inventario</title>
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

        <link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/articulos/inventario/consulta.css?v='.$javascript_cache_version); ?>">
		<!--SCRIPT DE EXPIRACION DE LA SESION-->
		<?php include PATH_LOG_OUT_HEADER;?>
		<!--CARGA DEL JQUERY-->
        <script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>"></script>
        <!--CARGA DEL JQUERYUI-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.js'); ?>"></script>
		<!--CSS ESTILO DEL JQUERYUI-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.css'); ?>">
		<!--CARGA DE MODAL-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-impromptu.js'); ?>"></script>
		<!--CSS ESTILO DEL MODAL-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/jquery-impromptu.css'); ?>">
		<!--SCRIPT DE NOTY-->
		<script src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE NUMERIC-->
        <script src="<?php echo base_url('application/scripts/jquery.numeric.js'); ?>" type="text/javascript"></script>

        <script src="<?php echo base_url('application/scripts/articulos/inventario/consulta.js?v='.$javascript_cache_version); ?>" type="text/javascript"></script>

		<script>
            const _CANTIDAD_DECIMALES = <?= $decimales ?>;
        </script>
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
			<p class="titulo_wrapper">Consulta de Control de Inventario</p>
			<hr class="division_wrapper">
			<div id="contenido" class="contenido">
                <table class="tabla-filtrado">
					<tr><td><p class="contact">Filtros</p></td></tr>
					<tr>
						<td>
							<p class="contact">Sucursal</p>
						</td>
						<td>

						</td>
						<td>
							<p class="contact">Fechas</p>
						</td>
					</tr>
					<tr>
						<td colspan="2">
                            <select id="empresa_seleccionada" class="input_uno">
                                <option value="-1">Seleccione sucursal</option>
                                <?php foreach($sucursales as $name => $id){ ?>
                                    <option value="<?= $id ?>"><?= $id." - ".$name ?></option>
                                <?php } ?>
                            </select>
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

						</td>
						<td>

						</td>
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
								<td class="titulo_header_tabla contact">Generado Por</td>
								<td class="titulo_header_tabla contact">Fecha</td>
							</tr>
						</thead>
						<tbody id="facturas_filtradas">
						</tbody>
					</table>
				</div>
				<input type="button" class="boton_busqueda" id="boton_carga_controles" value="Realizar Búsqueda"/>
				<hr>
				<label class='contact'>Consecutivo:</label>
				<input id="consecutivo" class="input_uno" autocomplete="off" type="text" style="width: 100px;"/>
				<input type="button" class="boton-carga" onclick="cargarControl()" value="Cargar Control"/>
				<p class="contact" style="display:inline;"><label for="impresion">Formato:</label></p>
				<select id="tipo_impresion" class="impresion" name="impresion" >
					<option value="pdf">PDF</option>
					<option value="excel">XLS</option>
				</select>
				<input type="button" class="boton_busqueda" onclick="descargar()" value="Descargar"/>
				<table id="tabla_productos" class="tabla_productos">
					<thead>
						<tr><th class="th_codigo">Código</th>
						<th class="th_descripcion">Descripción</th>
						<th class="th_exento">E</th>
						<th class="th_cantidad">F Bueno</th>
						<th class="th_cantidad">S Bueno</th>
						<th class="th_cantidad">B Bueno</th>
						<th class="th_cantidad">F Defec</th>
						<th class="th_cantidad">S Defec</th>
						<th class="th_cantidad">B Defec</th>
					</tr></thead>
					<tbody id="contenidoArticulos" class="contenidoArticulos">

					</tbody>
				</table>
				<div class="contenedor-costos-finales">
					<div class="contenedor-titulos">
						<label>Costos Totales<small class="small-disclaimer">Según cantidades físicas</small></label>

						<input type="text"  style="    opacity: 0;"/>
					</div>
					<div class="contenedor-costo-bueno">
						<label>Bueno</label>
						<input type="text" id="costo_bueno" disabled/>
					</div>
					<div class="contenedor-costo-defectuoso">
						<label>Defectuoso</label>
						<input type="text" id="costo_defectuoso" disabled/>
					</div>
					<div class="contenedor-costo-total">
						<label>Total</label>
						<input type="text" id="costo_total" disabled/>
					</div>
				</div>
			</div>
        </div>
		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>