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
                
                <link rel="stylesheet" type="text/css" href="<?php echo base_url("application/styles/contabilidad/fec.css?v=$javascript_cache_version"); ?>">
                    
                    <script src="<?php echo base_url("/application/scripts/contabilidad/fec_tools.js?v=$javascript_cache_version");?>" type="text/javascript"></script>
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
                        <table>
                            <tbody>
                                <tr>
                                    <td>
                                        <label class="contact">Información sobre emisor:</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="contact">&nbsp;</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="contact">Nombre:</label>
                                    </td>
                                    <td>
                                        <input id="nombre_emisor" class="input_uno" placeholder="Nombre del emisor" name="nombre_emisor" type="text" required="" tabindex="1" autocomplete="">
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
                                        <label class="contact">Provincia:</label>
                                    </td>
                                    <td>
                                        <label class="contact">Cantón:</label>
                                    </td>
                                    <td>
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
                                <tr>
                                    <td>
                                        <label class="contact">Otras Señas:</label>
                                    </td>
                                    <td>
                                        <input id="otras_sennas_emisor" class="input_uno" placeholder="Detalles de la direccion del emisor" name="otras_sennas_emisor" type="text" required="" tabindex="4" autocomplete="">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--Incluir footer-->
            <?php include PATH_FOOTER;?>
	</body>
</html>