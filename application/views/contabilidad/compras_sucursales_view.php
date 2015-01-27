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
		<title>Agregar Compras a Sucursales</title>
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
		<?php include '/../Header/log_out_from_browser_Script.php';?>
		<!--SCRIPT DE JQUERY-->		
		<script src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>" type="text/javascript"></script>		
		<!--SCRIPT DE NOTY-->		
		<script src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE NUMERIC-->		
		<script src="<?php echo base_url('application/scripts/jquery.numeric.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE Impromptu-->		
		<script src="<?php echo base_url('application/scripts/jquery-impromptu.js'); ?>" type="text/javascript"></script>
		<!--CSS ESTILO DEL Impromptu-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/jquery-impromptu.css'); ?>">
		<!--CSS ESTILO ESPECIFICO DE LA PAGINA-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/contabilidad/contabilidad_agregar_compras.css'); ?>">
		<!--SCRIPT DE HERRAMIENTAS-->		
		<script src="<?php echo base_url('application/scripts/contabilidad/agregarComprasSucursal_tools.js'); ?>" type="text/javascript"></script>
	</head>
	<body>
		<!--Incluir imagen de cabezera-->
		<?php include '/../Header/Header_Picture.php';?>
		
		<!--Incluir menu principal-->
		<?php include '/../Header/selector_menu.php';?>

		<!--Incluir informacion log in-->
		<?php include '/../Header/Log_In_Information.php';?>		
	
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Agregar Compras a Sucursales</p>
			<hr class="division_wrapper">
			
			<div class="contenedor" >
				<table>
					<tr>
						<td><p class="contact">Sucursal a agregar compras:</p></td>
						<td><p class="contact">Número de factura a agregar:</p></td>
						<td><p class="contact"><small>Factura de <?php echo $this->empresa->getNombreEmpresa($this->configuracion->getEmpresaDefectoTraspasoCompras());?></small></p></td>
					</tr>
					<tr>
						<td>
							<select class="input_uno" id="sucursal">					
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
							<input class="input_dos" type="text" id="numero_factura"/>
						</td>
						<td><input type="button" value="Cargar Factura" class="boton-cargar" onclick="cargarFactura()"/></td>
					</tr>
				</table>
				
				
				<hr>
				<div class="contenedor-tabla">
					<table>
						<thead>
							<tr class="header">
								<th class="titulo_header_tabla" style="width: 110px;">Código</th>
								<th class="titulo_header_tabla" style="width: 350px;">Descripción</th>
								<th class="titulo_header_tabla" style="width: 110px;">Cantidad</th>
								<th class="titulo_header_tabla" style="width: 110px;">Descuento</th>
								<th class="titulo_header_tabla" style="width: 110px;">P/Unitario</th>
								<th class="titulo_header_tabla" style="width: 110px;">Precio</th>
							</tr>
						</thead>
						<tbody id="productos_tabla">
						</tbody>
					</table>					
				</div>
				<input type="button" class="botonProcesar" value="Agregar Compras" onclick="procesarSolicitud()"/>
			</div><!-- contenedor -->			
		</div><!-- main_wrapper -->
		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>