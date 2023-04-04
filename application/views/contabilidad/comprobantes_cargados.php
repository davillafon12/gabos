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
		<title>Aceptación o Rechazo de Comprobantes Electrónicos</title>
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
                <!--CARGA DEL POPUP MODAL-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.bpopup.min.js'); ?>"></script>
                
                <link rel="stylesheet" type="text/css" href="<?php echo base_url("application/styles/contabilidad/contabilidad_comprobantes.css?v=$javascript_cache_version"); ?>">
                <script type="text/javascript" src="<?php echo base_url("application/scripts/contabilidad/comprobantes_electronicos.js?v=$javascript_cache_version"); ?>"></script>
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
                <p class="titulo_wrapper">Aceptación o Rechazo</p>
                <hr class="division_wrapper">

                <div id="contenido" class="contenido">
                    <?php if(sizeof($comprobantes) > 0){ ?>
                        <table id='tabla_comprobantes' class='tablaPrincipal'>
                            <thead> 
                                <th class='Sorted_enabled'>
                                    Clave
                                </th>
                                <th class='Sorted_enabled'>
                                    Nombre Emisor
                                </th>
                                <th class='Sorted_enabled'>
                                    Cédula Emisor
                                </th>
                                <th class='Sorted_enabled'>
                                    Fecha 
                                </th>
                                <th class='Sorted_enabled'>
                                    Nombre Receptor
                                </th>
                                <th class='Sorted_enabled'>
                                    Cédula Receptor
                                </th>
                                <th class='Sorted_enabled'>
                                    Total Impuestos
                                </th>
                                <th class='Sorted_enabled'>
                                    Total Factura
                                </th>
                                <th>
                                    Estado
                                </th>
                            </thead> 
                            <tbody>
                                <?php foreach($comprobantes as $c){ ?>
                                    <tr>
                                        <td>
                                            <?= $c["clave"] ?>
                                        </td>
                                        <td>
                                            <?= $c["nombreEmisor"] ?>
                                        </td>
                                        <td>
                                            <?= $c["cedulaEmisor"] ?>
                                        </td>
                                        <td>
                                            <?= $c["fecha"] ?>
                                        </td>
                                        <td>
                                            <?= $c["receptorNombre"] ?>
                                        </td>
                                        <td>
                                            <?= $c["receptorCedula"] ?>
                                        </td>
                                        <td>
                                            <?= $c["totalImpuestos"] ?>
                                        </td>
                                        <td>
                                            <?= $c["totalFactura"] ?>
                                        </td>
                                        <td>
                                            <select class="selector-mensaje" clave-factura="<?= $c["clave"] ?>">
                                                <option value="0">Seleccionar</option>
                                                <?php foreach($tiposMensajes as $value => $content){ ?>
                                                    <option value="<?= $value ?>"><?= $content ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <button id="boton_procesar" class="boton-procesar">Procesar</button>
                    
                    
                    
                    
                    
                    <?php }else{ ?>
                        <center><?= $error ?></center>
                    <?php }?>
                </div>
                <div class="envio_factura" id="envio_hacienda">
                    <img class="envio_img" src="<?php echo base_url('application/images/enviandoFactura.gif'); ?>">
                    <p class="envio_p">Procesando Comprobantes... <br>Por favor, espere.</p>
                </div>	
            </div>
            <!--Incluir footer-->
            <?php include PATH_FOOTER;?>
	</body>
</html>