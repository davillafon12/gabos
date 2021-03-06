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
		<title>Editar Consignaciones</title>
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
		<!--CSS ESTILO DEL MAIN WRAPPER-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/contabilidad/contabilidad_consignaciones.css'); ?>">
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
		<!--CSS ESTILO DEL MODAL-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/jquery-impromptu.css'); ?>">
		<!--JQUERY IMPROMPTU-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-impromptu.js'); ?>"></script>
                <!--CARGA DEL NUMERIC-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.numeric.js'); ?>"></script>
		<!--HERRAMIENTAS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/contabilidad/crear_consignaciones.js?v='.$javascript_cache_version); ?>"></script>
                <!--CSS PROPIO-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/contabilidad/contabilidad_editar_consignaciones.css'); ?>">
                <!--HERRAMIENTAS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/contabilidad/editarConsignaciones.js?v='.$javascript_cache_version); ?>"></script>
		<script>
			var _PORCENTAJE_IVA = <?php echo $porcentaje_iva; ?>;
			var _CANTIDAD_DECIMALES = <?php echo $cantidad_decimales; ?>;
			var _APLICA_RETENCION = <?php echo $aplicar_retencion; ?>;
		</script>
                <style>
                    .boton-guardar {
                        margin-left: 5px;
                        background: #FF9900;
                        display: inline-block;
                        padding: 2px 6px 3px;
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
                </style>
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
			<p class="titulo_wrapper">Editar Consignaciones</p>
			<hr class="division_wrapper">
			<div class="factura_wrapper" >
                            <div class="filters">
                                <div class="contenedor-filtros">
                                    <table class='tabla-info'>
                                            <tr>
                                                    <td><p class="contact">Sucursal que consigna:</p></td>
                                                    <td><p class="contact">Sucursal que recibe consignación:</p></td>
                                            </tr>
                                            <tr>
                                                    <td>
                                                            <select class="input_uno" id="sucursal_entrega">	
                                                                    <option value="-1">Seleccione Sucursal</option>				
                                                                    <?php						
                                                                            foreach($Familia_Empresas as $Nombre_Empresa => $codigo_empresa)
                                                                            {
                                                                                    echo "<option value='".$codigo_empresa."'";
                                                                                    echo">".$codigo_empresa." - ".$Nombre_Empresa."</option>";											
                                                                            }
                                                                    ?>
                                                            </select>						
                                                    </td>
                                                    <td>
                                                            <select class="input_uno" id="sucursal_recibe">	
                                                                    <option value="-1">Seleccione Sucursal</option>					
                                                                    <?php						
                                                                            foreach($Familia_Empresas as $Nombre_Empresa => $codigo_empresa)
                                                                            {
                                                                                    echo "<option value='".$codigo_empresa."'";
                                                                                    echo">".$codigo_empresa." - ".$Nombre_Empresa."</option>";											
                                                                            }
                                                                    ?>
                                                            </select>	
                                                    </td>
                                            </tr>
                                            <tr>
                                                    <td><p class="contact">Desde:</p></td>
                                                    <td><p class="contact">Hasta:</p></td>
                                            </tr>
                                        <tr>
                                            <td>
                                                <input id="fecha_desde" class="input_uno" style="width: 100px;" autocomplete="off" type="text"/>
                                            </td>
                                            <td>
                                                <input id="fecha_hasta" class="input_uno" style="width: 100px;" autocomplete="off" type="text"/>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="contenedor-facturas">
                                        <table class="tabla-facturas">
                                                <thead>
                                                        <tr class="header">
                                                                <td class="titulo_header_tabla contact" style="  width: 50px;">#</td>
                                                                <td class="titulo_header_tabla contact">Consignación</td>
                                                                <td class="titulo_header_tabla contact">Recibe</td>
                                                                <td class="titulo_header_tabla contact" >Fecha</td>
                                                        </tr>
                                                </thead>
                                                <tbody id="consignaciones_filtradas">
                                                </tbody>
                                        </table>
                                </div>
                            </div>
                            <div class="contenedor-carga">
				<input type="button" class="boton_busqueda" onclick="llamarConsignaciones()" value="Realizar Búsqueda"/>
				<hr>
				<label class='contact'>Consecutivo:</label>
				<input id="consecutivo" class="input_uno" autocomplete="off" type="text" style="width: 100px;"/>
				<input type="button" class="boton-carga" onclick="cargarConsignacion()" value="Cargar Consignación"/>
				<p class="contact" style="display:inline;"><label for="impresion">Impresión:</label></p> 					
				<select id="tipo_impresion" onChange="cambiarTipoImpresion(this.value)" class="impresion" name="impresion" >
					<option value="c">A4</option>
				</select>
				<input type="button" class="boton_busqueda" onclick="imprimir()" value="Imprimir"/>
                                <input type="button" class="boton-guardar" onclick="guardarConsignacion()" value="Guardar"/>
				<hr>
                            </div>
				<table id="tabla_productos" class="tabla_productos" >
						<thead>
							<th class="th_codigo">Código</th>
							<th class="th_descripcion">Descripción</th>
							<th class="th_cantidad">Cantidad</th>
							<th class="th_bodega">Inventario</th>
							<th class="th_bodega">Descuento</th>
							<th class="th_precio">Precio Unidad</th>
							<th class="th_precio">Precio Total</th>
						</thead>
						<tbody id="cuerpo_tabla_articulos">
						
						
						</tbody>				
					</table>
					<div class="cant_total_articulos_div">
						<p class="cant_total_articulos_p">Cantidad Total de Articulos:</p>
						<p class="cant_total_articulos_p" id="cant_total_articulos">0</p>
					</div>
					<div class="tabla_costos">
					<table>
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
				
			</div>
			<div class="botones_final">
					<a href="javascript:;" id="aplicar_consignacion" class="boton_act_all">Aplicar Consignación</a>
				</div>
		</div>	
		<div class="envio_consignacion" id="envio_consignacion" style="display: none; text-align: center;">
			<img class="envio_img" src="<?php echo base_url('application/images/enviandoFactura.gif'); ?>">
			<p class="envio_p">Aplicando consignación... <br>Por favor, espere.</p>
		</div>
                <div class="envio_consignacion" id="guardado_consignacion" style="display: none; text-align: center;">
			<img class="envio_img" src="<?php echo base_url('application/images/enviandoFactura.gif'); ?>">
			<p class="envio_p">Guardando consignación... <br>Por favor, espere.</p>
		</div>
		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>
