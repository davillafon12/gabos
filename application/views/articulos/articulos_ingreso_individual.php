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
		<title>Ingreso Individual De Artículos</title>
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
		<!--CSS ESTILO DEL JQUERYUI-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.css'); ?>">
		<!--SCRIPT DE EXPIRACION DE LA SESION-->
		<?php include PATH_LOG_OUT_HEADER;?>
		<!--SCRIPT DE AJAX JQUERY-->
		<script src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>" type="text/javascript"></script>
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.js'); ?>"></script>
		<!--SCRIPT DE NOTY-->
		<script src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE NUMERIC-->
		<script src="<?php echo base_url('application/scripts/jquery.numeric.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE VERIFICACION DE CODIGOS-->
		<script src="<?php echo base_url('application/scripts/articulos/verifica_codigos_ingreso_individual.js?v='.$javascriptCacheVersion); ?>" type="text/javascript"></script>
		<!--CSS ESTILO ESPECIFICO DE LA PAG-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/articulos/style_ingreso_individual.css?v='.$javascriptCacheVersion); ?>">
		<!--SCRIPT DE HERRAMIENTAS-->
		<script src="<?php echo base_url('application/scripts/articulos/ingreso_individual_tools.js?v='.$javascriptCacheVersion); ?>" type="text/javascript"></script>
	</head>
	<body>
		<!--Incluir imagen de cabezera-->
		<?php include PATH_HEADER_PICTURE;?>

		<!--Incluir menu principal-->
		<?php include PATH_HEADER_SELECTOR_MENU;?>

		<!--Incluir informacion log in-->
		<?php include PATH_HEADER_LOG_IN_INFO;?>


		<!-- CUERPO DE LA PAGINA ACTUAL-->
		<div class="main_wrapper">
			<p class="titulo_wrapper">Ingreso Individual De Artículos</p>
			<hr class="division_wrapper">
			<?php
				$attributes = array('name' => 'registrar_articulos_form', 'id' => 'registrar_articulos_form', 'class' => 'registrar_articulos_form-form');

				echo form_open_multipart('articulos/ingresar/registrar', $attributes);
			?>
			<div class="contenedor">
				<fieldset>
					<legend>Información Básica</legend>
					<table>
						<tr>
							<td>
								<label for="articulo_codigo" class="contact">Código:</label>
							</td>
							<td>
								<input id="articulo_codigo" class="input_uno" autocomplete="off" name="articulo_codigo" onblur="verificarCodigoArticulo()" placeholder=""  required="" >
								<div id="status" class="status"></div>
							</td>
							<td>
								<label for="articulo_descripcion" class="contact">Descripción:</label>
							</td>
							<td colspan="3">
								<input id="articulo_descripcion" class="input_descripcion" autocomplete="off"  name="articulo_descripcion" required="" type="text" disabled>
							</td>
						</tr>
						<tr>
							<td>
								<label for="articulos_cantidad"  class="contact">Cantidad:</label>
							</td>
							<td>
								<input id="articulos_cantidad" class="input_uno" autocomplete="off" name="articulos_cantidad" required="" disabled>
							</td>
							<td>
								<label for="articulos_cantidad_defectuoso"  class="contact">Cantidad Defectuosa:</label>
							</td>
							<td>
								<input id="articulos_cantidad_defectuoso" class="input_uno" autocomplete="off" name="articulos_cantidad_defectuoso" required="" disabled>
							</td>
							<td>
								<label class="contact" > Exento de IVI</label>
							</td>
							<td>
								<input type="checkbox" name="exento" id="exento"  value="1" disabled>
							</td>
						</tr>
						<tr>
							<td>
								<label for="sucursal"  class="contact">Empresa:</label>
							</td>
							<td>
								<select name="sucursal" class="input_dos" id="sucursal" onchange="cambiosSucursal()" >
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
								<label for="familia"  class="contact">Familia:</label>

							</td>
							<td>
								<select name="familia" class="input_dos" id="familia" disabled>
									<?php
										foreach($Familias as $Nombre_Familia => $codigo_familia)
										{
											echo "<option value='".$codigo_familia."'";
											echo">".$codigo_familia." - ".$Nombre_Familia."</option>";
										}
									?>
								</select>
							</td>
							<td>
								<label for="descuento"  class="contact">Descuento:</label>
							</td>
							<td>
								<input id="descuento" class="input_uno" autocomplete="off" name="descuento" disabled>
							</td>
						</tr>
						<tr>
							<td>
								<label for="foto_articulo"  class="contact">Foto:</label>
							</td>
							<td>
								<input type="file" id="foto_articulo" class="input_dos" name="userfile" size="10" accept=".jpg,.png,.ico,.bmp" disabled>
							</td>
							<td>
								<label class="contact" > Sin Retención</label>
							</td>
							<td>
								<input type="checkbox" name="retencion" id="retencion"  value="1" >
							</td>
                                                    <td>
                                                        <label class="contact">Tipo Código:</label>
                                                    </td>
                                                    <td>
                                                        <select name="tipo_codigo" class="input_dos">
                                                            <?php
                                                                foreach($tipo_codigo as $tc){
                                                                    ?>
                                                            <option value="<?= $tc->Codigo ?>"><?= $tc->Descripcion ?></option>
                                                                    <?php
                                                                }
                                                            ?>
                                                        </select>
                                                    </td>
						</tr>
                                                <tr>
                                                    <td>
                                                        <label class="contact">Unidad Medida:</label>
                                                    </td>
                                                    <td>
                                                        <select name="unidad_medida" class="input_dos">
                                                            <?php
                                                                foreach($unidades_medida as $um){
                                                                    ?>
                                                            <option value="<?= $um->Id ?>"><?= $um->Codigo." - ".$um->Descripcion ?></option>
                                                                    <?php
                                                                }
                                                            ?>
                                                        </select>
                                                    </td>
													<td>
                                                        <label class="contact">Código Cabys:</label>
                                                    </td>
                                                    <td>
														<input type="text" id="codigo_cabys_display" class="input_dos" disabled/>
														<input type="text" name="codigo_cabys" id="codigo_cabys" style="display:none;"/>
														<input type="text" name="impuesto_cabys" id="impuesto_cabys" style="display:none;"/>
													</td>
													<td colspan="2">
                                                        <input type="text" id="busqueda_codigo_cabys" class="input_dos" placeholder="Busque aquí el código Cabys"/>
                                                    </td>
						</tr>
					</table>
				</fieldset>
				<fieldset class="field_precios">
					<legend>Precios</legend>
					<table>
						<tr>
							<td>
								<label for="costo"  class="contact">Costo:</label>
							</td>
							<td>
								<input id="costo" class="input_uno" autocomplete="off" name="costo" required="" disabled>
							</td>
							<td>
								<label for="precio1"  class="contact">Precio 1:</label>
							</td>
							<td>
								<input id="precio1" class="input_uno" autocomplete="off" name="precio1" required="" disabled>
							</td>
							<td>
								<label for="precio2"  class="contact">Precio 2:</label>
							</td>
							<td>
								<input id="precio2" class="input_uno" autocomplete="off" name="precio2"  required="" disabled>
							</td>
						</tr>
						<tr>
							<td>
								<label for="precio3"  class="contact">Precio 3:</label>
							</td>
							<td>
								<input id="precio3" class="input_uno" autocomplete="off" name="precio3" disabled>
							</td>
							<td>
								<label for="precio4"  class="contact">Precio 4:</label>
							</td>
							<td>
								<input id="precio4" class="input_uno" autocomplete="off" name="precio4" disabled>
							</td>
							<td>
								<label for="precio5"  class="contact">Precio 5:</label>
							</td>
							<td>
								<input id="precio5" class="input_uno" autocomplete="off" name="precio5" disabled>
							</td>
						</tr>
					</table>
				</fieldset>
				<div id= "cod_Barras" class="cod_barras"></div>
				<div class="divButton">
					<input class="boton" id="boton_submit" name="submit" value="Registrar" type="submit" disabled>
					<a class="boton_a" href='<?php echo base_url('home')?>' class='boton_volver'>Volver</a>
				</div>
			</form>
			</div><!-- contenedor -->


		</div>

		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>