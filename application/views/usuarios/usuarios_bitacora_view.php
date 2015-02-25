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
		<title>Bitácora De Usuarios</title>
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
		<!--CSS ESTILO DE LA TABLA-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/usuarios/style_bitacora.css'); ?>">				
		<!--CARGA DEL JQUERY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>"></script> 		
		<!-- DATA TABES SCRIPT -->
        <script src="<?php echo base_url('/application/scripts/datatables/dataTablesNew.js');?>" type="text/javascript"></script>
	</head>
	<body >
		<!--Incluir imagen de cabezera-->
		<?php include '/../Header/Header_Picture.php';?>
		
		<!--Incluir menu principal-->
		<?php include '/../Header/selector_menu.php';?>

		<!--Incluir informacion log in-->
		<?php include '/../Header/Log_In_Information.php';?>
		
		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Bitácora De Usuarios</p>
			<hr class="division_wrapper">
		    
			<div class="contenido" id="contenido">
				<div class="contenedor-sucursal">
					<p class="contact label-sucursal">Sucursal:</p>
					<select class="input_dos" onchange="cambiarSucursal()" id="sucursal">
						<?php
							foreach($Familia_Empresas as $en => $ek){
								echo "<option value='$ek'>$en</option>";								
							}
						?>			
					</select>
				</div>
				<table id='tabla_editar' class='tablaPrincipal'>
					<thead> 
						<tr>									
							<th class='sorted_enabled'>
								Número
							</th>
							<th class='sorted_enabled'>
								Descripción
							</th>
							<th class='sorted_enabled'>
								Fecha y Hora
							</th>
							<th class='sorted_enabled'>
								Tipo
							</th>
							<th class='sorted_enabled'>
								IP
							</th>                                                                                                            
							<th class='sorted_enabled'>
								Usuario
							</th>
						</tr>
					</thead> 
					<tbody>						
					</tbody>
				</table>
			</div>			
        </div> 

		<script>
			$(document).ready(function(){
				setTable();
			});

			function setTable(){
				$('#tabla_editar').dataTable({
					"processing": true,
					"serverSide": true,
					"ajax": {
						"url": location.protocol+'//'+document.domain+'/usuarios/bitacora/obtenerTransaccionesTabla',
						"type": "POST",
						"data": function ( d ) {
								d.sucursal = $("#sucursal").val();
							}
					},		
					'oLanguage': {
							'sUrl': location.protocol+'//'+document.domain+'/application/scripts/datatables/Spanish.txt'
						},
					"columns": [ 								   
								   null,    
								   null,
								   null,
								   null,
								   null,
								   null, 
								],
					"drawCallback": function( settings ) {
							$("#contenido").css( "display", 'block' );
						}
				});
			}
			
			function cambiarSucursal(){
				$("#tabla_editar").dataTable().fnDraw();
			}
		</script>

		<!--Incluir footer-->
		<?php include '/../Footer/Default_Footer.php';?>
	</body>
</html>