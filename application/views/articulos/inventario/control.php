<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Control de Inventario</title>
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

        <link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/articulos/inventario/control.css'); ?>">
		<!--SCRIPT DE EXPIRACION DE LA SESION-->
		<?php include PATH_LOG_OUT_HEADER;?>
		<!--CARGA DEL JQUERY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>"></script>
		<!--CARGA DE MODAL-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-impromptu.js'); ?>"></script>
		<!--CSS ESTILO DEL MODAL-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/jquery-impromptu.css'); ?>">
		<!--SCRIPT DE NOTY-->
		<script src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE NUMERIC-->
        <script src="<?php echo base_url('application/scripts/jquery.numeric.js'); ?>" type="text/javascript"></script>

        <script src="<?php echo base_url('application/scripts/articulos/inventario/control.js'); ?>" type="text/javascript"></script>
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
			<p class="titulo_wrapper">Control de Inventario</p>
			<hr class="division_wrapper">
			<div id="contenido" class="contenido">
                <div class="selectores-menu">
                    <div class="titulo">
                        <label>Seleccione la empresa a cual va a realizar el control inventario:</label>
                    </div>
                    <div class="empresas-container">
                        <select id="empresa_seleccionada">
                            <option value="-1">Seleccione sucursal</option>
                            <?php foreach($sucursales as $name => $id){ ?>
                                <option value="<?= $id ?>"><?= $id." - ".$name ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="contenedor-tabla">
                    <div class="controllers">
                        <div class="header">
                            Información del artículo a comparar:
                        </div>
                        <div class="body">
                                <table>
                                    <tr>
                                        <td>
                                            Código:
                                        </td>
                                        <td><input type="text" id="articulo_a_comparar_id"/></td>
                                    </tr>
                                    <tr>
                                        <td>
                                        Defectuoso:
                                        </td>
                                        <td><input type="number" id="articulo_a_comparar_defectuoso"/></td>
                                    </tr>
                                    <tr>
                                        <td>
                                        Bueno:
                                        </td>
                                        <td><input type="number" id="articulo_a_comparar_bueno"/></td>
                                    </tr>
                                    <tr>
                                        <td>

                                        </td>
                                        <td>
                                            <button id="boton_agregar_articulo">Agregar</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <button id="boton_cargar_inventario">Cargar Inventario</button>
                                        </td>
                                    </tr>
                                </table>
                        </div>
                    </div>
                    <div class="tabla-inventario">
                        <div class="header">
                            <div class="nomenclatura">
                                <b>Bu</b> = Bueno &nbsp;
                                <b>De</b> = Defectuoso&nbsp;
                                <b>F</b> = Físico&nbsp;
                                <b>S</b> = Sistema&nbsp;
                                <b>B</b> = Balance&nbsp;
                            </div>
                            <div class="item" style="width: 12%">
                                Código
                            </div>
                            <div class="item"  style="width: 44%;">
                                Descripción
                            </div>
                            <div class="item" style="width: 7%;">
                                Bu F
                            </div>
                            <div class="item" style="width: 7%;">
                                Bu S
                            </div>
                            <div class="item" style="width: 7%;">
                                Bu B
                            </div>
                            <div class="item" style="width: 7%;">
                                De F
                            </div>
                            <div class="item" style="width: 7%;">
                                De S
                            </div>
                            <div class="item" style="width: 7%;">
                                De B
                            </div>
                        </div>
                        <div class="body" id="articulos_container">

                        </div>
                    </div>
                </div>
			</div>
        </div>
		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>