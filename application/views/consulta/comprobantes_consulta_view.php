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
		<title>Consulta de Comprobantes Electrónicos</title>
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
		<!--CARGA DE HERRAMIENTAS TABLA-->
		<script type="text/javascript" src="<?php echo base_url("application/scripts/consulta/tabla_comprobantes_tools.js?v=$javascript_cache_version"); ?>"></script>
		<!--CSS ESTILO DE LA PAG Y TABLA-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url("application/styles/consulta/estilo_comprobantes.css?v=$javascript_cache_version"); ?>">
                <!--CARGA DEL POPUP MODAL-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.bpopup.min.js'); ?>"></script>
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
			<p class="titulo_wrapper">Comprobantes Electrónicos</p>
			<hr class="division_wrapper">
                            <?php 
                                $documentoSeleccionado = @$_GET["d"];
                            ?>
			<div id="contenido" class="contenido">
                            <label class='label-documento'>Tipo Documento:</label>
                            <select id='tipo_documento' onchange='cambiarTipoDocumento()'>
                                <option value="FE" <?= $documentoSeleccionado == "fe" ? "selected" : "" ?>>Factura Electrónica</option>
                                <option value="NC" <?= $documentoSeleccionado == "nc" ? "selected" : "" ?>>Nota Crédito</option>
                                <option value="MR" <?= $documentoSeleccionado == "mr" ? "selected" : "" ?>>Mensaje Receptor</option>
                            </select>
				<table id='tabla_editar' class='tablaPrincipal' data-order='[[ 3, "asc" ]]'>
					<thead> 
                        <th class='Sorted_enabled'>
                            Clave
                        </th>
			<th class='Sorted_enabled'>
                            Consecutivo
                        </th>
                        <th class='Sorted_enabled'>
                            Cliente
                        </th>
                        <th class='Sorted_enabled'>
                            Fecha
                        </th>
                        <th class='Sorted_enabled'>
                            Correo Enviado
                        </th>
                        <th class='Sorted_enabled'>
                            Estado
                        </th>
			<th>
                            Opciones
                        </th>
                    </thead> 
					<tbody>
					</tbody>
				</table>
			</div>

		<div class="envio_factura" id="envio_hacienda">
                        <img class="envio_img" src="<?php echo base_url('application/images/enviandoFactura.gif'); ?>">
                        <p class="envio_p">Enviando documento... <br>Por favor, espere.</p>
                </div>	
        </div>
				<!--<div id="timeout_show"></div>-->
       		

		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>