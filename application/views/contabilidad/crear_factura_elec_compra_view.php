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
		<title>Factura Electrónica de Compras</title>
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
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.css'); ?>">
		<!--SCRIPT DE EXPIRACION DE LA SESION-->
		<?php include PATH_LOG_OUT_HEADER;?>
		<!--CARGA DEL JQUERY-->
        <script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('application/scripts/moment.min.js'); ?>"></script>
		<!--CARGA DE MODAL-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-impromptu.js'); ?>"></script>
		<!--CSS ESTILO DEL MODAL-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/jquery-impromptu.css'); ?>">
		<!--SCRIPT DE NOTY-->
		<script src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE NUMERIC-->
		<script src="<?php echo base_url('application/scripts/jquery.numeric.js'); ?>" type="text/javascript"></script>
		<!-- DATA TABES SCRIPT -->
                <script src="<?php echo base_url('/application/scripts/datatables/dataTablesNew.js');?>" type="text/javascript"></script>
                <!--CARGA DEL POPUP MODAL-->
        <script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.bpopup.min.js'); ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-impromptu.js'); ?>"></script>
		<!--CSS ESTILO DEL MODAL-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/jquery-impromptu.css'); ?>">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url("application/styles/contabilidad/fec.css?v=$javascript_cache_version"); ?>">
        <script src="<?php echo base_url("/application/scripts/contabilidad/fec_tools.js?v=$javascript_cache_version");?>" type="text/javascript"></script>
        <script>
            var _CANTIDAD_DECIMALES = <?= $c_array['cantidad_decimales'] ?>;
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
                <p class="titulo_wrapper">Crear FEC</p>
                <hr class="division_wrapper">

                <div id="contenido" class="contenido">
                    <div class="factura-wrapper">
                        <div class="header-container">
                            <table class="tabla-emisor">
                                <tbody>
                                    <tr>
                                        <td colspan="3">
                                            <label class="contact">Información sobre emisor:</label><hr>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="    width: 33%;">
                                            <label class="contact">Nombre:</label>
                                        </td>
                                        <td style="    width: 33%;">
                                            <input id="nombre_emisor" class="input_uno" placeholder="Nombre del emisor" name="nombre_emisor" type="text" required="" tabindex="1" autocomplete="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label class="contact">Tipo de Identificación:</label>
                                        </td>
                                        <td>
                                            <select class="input_uno" name="tipo_identificacion_emisor" id="tipo_identificacion_emisor">
                                                <?php foreach($tipo_identificacion as $tiKey => $tiValue): ?>
                                                    <option value="<?= $tiKey?>"><?= $tiValue ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label class="contact">Identificación:</label>
                                        </td>
                                        <td>
                                            <input id="identificacion_emisor" class="input_uno" placeholder="Identificación del emisor" name="identificacion_emisor" type="text" required="" tabindex="2" autocomplete="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label class="contact">Correo Electrónico:</label>
                                        </td>
                                        <td>
                                            <input id="email_emisor" class="input_uno" placeholder="Correo electrónico del emisor" name="email_emisor" type="text" required="" tabindex="3" autocomplete="">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <label class="contact">Otras Señas:</label>
                                        </td>
                                        <td>
                                            <input id="otras_sennas_emisor" class="input_uno" placeholder="Detalles de la direccion del emisor" name="otras_sennas_emisor" type="text" required="" tabindex="4" autocomplete="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label class="contact">Provincia:</label>
                                        </td>
                                        <td>
                                            <label class="contact">Cantón:</label>
                                        </td>
                                        <td style="    width: 33%;">
                                            <label class="contact">Distrito:</label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <select class="input_uno" name="emisor_provincia" id="emisor_provincia">
                                                <option value="0">Seleccione un provincia</option>
                                                <?php foreach($provincias as $p): ?>
                                                    <option value="<?= $p->ProvinciaID ?>"><?= $p->ProvinciaNombre ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="input_uno" name="emisor_canton" id="emisor_canton">

                                            </select>
                                        </td>
                                        <td>
                                            <select class="input_uno" name="emisor_distrito" id="emisor_distrito">

                                            </select>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                            <table class="tabla-factura">
                                <tbody>
                                    <tr>
                                        <td colspan="3">
                                            <label class="contact">Información sobre la factura:</label><hr>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label class="contact">Código de Actividad:</label>
                                        </td>
                                        <td>
                                            <input id="codigo_actividad_factura" class="input_uno" placeholder="Código de actividad de la factura" name="codigo_actividad_factura" type="text" required="" tabindex="1" autocomplete="">
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label class="contact">Fecha:</label>
                                        </td>
                                        <td>
                                            <input id="fecha_factura" class="input_uno" placeholder="DD-MM-YYYY hh:mm:ss" name="fecha_factura" type="text" required="" tabindex="1" autocomplete="">
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label class="contact">Condición de venta:</label>
                                        </td>
                                        <td>
                                            <select id="condicion_venta_factura" class="input_uno" name="condicion_venta_factura">
                                                <option value="-1">Seleccionar</option>
                                                <?php foreach($condicionesventa as $id => $name): ?>
                                                 <option value="<?= $id ?>"><?= $name ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr id="plazo_factura_row">
                                        <td>
                                            <label class="contact">Plazo de Crédito:</label>
                                        </td>
                                        <td>
                                            <input id="plazo_factura" class="input_uno" placeholder="Plazo de crédito (En dias)" name="plazo_factura" type="text" required="" tabindex="1" autocomplete="">
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label class="contact">Medio de pago:</label>
                                        </td>
                                        <td>
                                            <select id="tipo_pago_factura" class="input_uno" name="tipo_pago_factura">
                                                <option value="-1">Seleccionar</option>
                                                <?php foreach($tiposdepago as $id => $name): ?>
                                                 <option value="<?= $id ?>"><?= $name ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="details-container">
                            <table class="tabla-detalles-head">
                                <tbody>
                                    <tr>
                                        <td>
                                            <label class="contact">Detalles de la factura:</label><hr>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table class="tabla-detalles-body">
                                <tbody>
                                    <tr>
                                        <td style="    width: 11%;">
                                            <label class="contact">Código:</label>
                                        </td>
                                        <td style="    width: 13%;">
                                            <input id="codigo_detalle" class="input_uno" placeholder="Código del artículo" name="codigo_detalle" type="text" required="" tabindex="1" autocomplete="">
                                        </td>
                                        <td style="    width: 12%;">
                                            <label class="contact">Tipo Código:</label>
                                        </td>
                                        <td style="    width: 13%;">
                                            <select name="tipo_codigo_detalle" class="input_uno" id="tipo_codigo_detalle">
                                                <?php
                                                    foreach($tipos_codigo as $tc){
                                                        ?>
                                                            <option value="<?= $tc->Codigo ?>"><?= $tc->Descripcion ?></option>
                                                        <?php
                                                    }
                                                ?>
                                            </select>
                                        </td>
                                        <td style="    width: 12%;">
                                            <label class="contact">Detalle:</label>
                                        </td>
                                        <td colspan="3">
                                            <input id="detalle_detalle" class="input_uno" placeholder="Detalle del artículo" name="detalle_detalle" type="text" required="" tabindex="1" autocomplete="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label class="contact">Cantidad:</label>
                                        </td>
                                        <td>
                                            <input id="cantidad_detalle" class="input_uno" placeholder="Cantidad del artículo" name="cantidad_detalle" type="text" required="" tabindex="1" autocomplete="">
                                        </td>
                                        <td>
                                            <label class="contact">Unidad Medida:</label>
                                        </td>
                                        <td>
                                            <select name="unidad_medida_detalle" class="input_uno" id="unidad_medida_detalle">
                                                <?php
                                                    foreach($unidades_medida as $um){
                                                        ?>
                                                            <option value="<?= $um->Codigo ?>" <?= $um->Id == 85 ? "selected" : "" ?> ><?= $um->Codigo." - ".$um->Descripcion ?></option>
                                                        <?php
                                                    }
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <label class="contact">Precio Unitario:</label>
                                        </td>
                                        <td>
                                            <input id="precio_unitario_detalle" class="input_uno" placeholder="Precio unitario del artículo" name="precio_unitario_detalle" type="text" required="" tabindex="1" autocomplete="">
                                        </td>
                                        <td style="    width: 12%;">
                                            <label class="contact">Descuento (%):</label>
                                        </td>
                                        <td style="    width: 10%;">
                                            <input id="descuento_detalle" class="input_uno" placeholder="Descuento en porcentaje" name="descuento_detalle" type="text" required="" tabindex="1" autocomplete="" value="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label class="contact">Tarifa IVA (%):</label>
                                        </td>
                                        <td>
                                            <input id="tarifa_iva_detalle" class="input_uno" placeholder="IVA en porcentaje" name="tarifa_iva_detalle" type="text" required="" tabindex="1" autocomplete="">
                                        </td>
                                        <td>
                                            <label class="contact">Tipo Tarifa:</label>
                                        </td>
                                        <td>
                                            <select name="tipo_tarifa_detalle" class="input_uno" id="tipo_tarifa_detalle">
                                                <?php
                                                    foreach($tipos_tarifa as $tt){
                                                        ?>
                                                            <option value="<?= $tt->Id ?>" <?= $tt->Id == "08" ? "selected" : "" ?> ><?= $tt->Descripcion ?></option>
                                                        <?php
                                                    }
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <label class="contact">Tipo Impuesto:</label>
                                        </td>
                                        <td>
                                            <select name="tipo_impuesto_detalle" class="input_uno" id="tipo_impuesto_detalle">
                                                <?php
                                                    foreach($tipos_impuesto as $ti){
                                                        ?>
                                                            <option value="<?= $ti->Id ?>"><?= $ti->Descripcion ?></option>
                                                        <?php
                                                    }
                                                ?>
                                            </select>
                                        </td>
                                        <td>

                                        </td>
                                        <td>
                                            <button id="boton_agregar_detalle" class="boton_agregar_detalle">Agregar Detalle</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label class="contact">Código Cabys:</label>
                                        </td>
                                        <td>
                                            <input type="text" id="codigo_cabys" class="input_uno"  disabled/>
                                        </td>
                                        <td colspan="2">
                                            <input type="text" id="busqueda_codigo_cabys" class="input_uno" placeholder="Busque aquí el código Cabys"/>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table  class="tabla_productos">
                                <thead>
                                    <tr>
                                        <th class="th_codigo">Código</th>
                                        <th class="th_descripcion">Descripción</th>
                                        <th class="th_cantidad">Cantidad</th>
                                        <th class="th_descuento">Descuento</th>
                                        <th class="th_costo_unidad">Precio por unidad</th>
                                        <th class="th_costo_total">Precio total</th>
                                        <th class="th_eliminar"></th>
                                    </tr>
                                </thead>
                                <tbody id="tabla_productos">
                                </tbody>
                            </table>
                        </div>
                        <div class="send-buttom-container">
                            <button id="boton_crear_factura" class="boton_crear_factura">Crear Factura de Compra</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="envio_factura" id="envio_factura">
                <img class="envio_img" src="<?php echo base_url('application/images/enviandoFactura.gif'); ?>">
                <p class="envio_p">Guardando factura... <br>Por favor, espere.</p>
            </div>
            <!--Incluir footer-->
            <?php include PATH_FOOTER;?>
	</body>
</html>