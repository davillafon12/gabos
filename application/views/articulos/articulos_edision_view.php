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
		<title>Edición De Articulos</title>
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
		<!--SCRIPT DE AJAX JQUERY-->
		<script src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>" type="text/javascript"></script>
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jqueryUI/jquery-ui.js'); ?>"></script>
		<!--SCRIPT DE NOTY-->
		<script src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>" type="text/javascript"></script>
		<!--SCRIPT DE NUMERIC-->
		<script src="<?php echo base_url('application/scripts/jquery.numeric.js'); ?>" type="text/javascript"></script>
		<!--CSS ESTILO ESPECIFICO DE LA PAG-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/articulos/style_edicion_articulo.css'); ?>">
		<!--SCRIPT DE HERRAMIENTAS-->
		<script src="<?php echo base_url('application/scripts/articulos/edicion_articulo_tools.js?v='.$javascriptCacheVersion); ?>" type="text/javascript"></script>

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
			<p class="titulo_wrapper">Edición De Un Artículo</p>
			<hr class="division_wrapper">

			<?php
				if(isset($_GET['s'])&&$_GET['s']=='s'){
					echo "
						<div class='alert alert-success'>
							¡Se actualizó el artículo con éxito!
						</div>
					";
				}elseif(isset($_GET['s'])&&$_GET['s']=='e'){
					$errorMsg = 'Error Desconocido';
					if(isset($_GET['e'])){
						switch($_GET['e']){
							case '1': $errorMsg = '1 - URL mala, por favor contacte al administrador';
							break;
							case '2': $errorMsg = '2 - Sucursal y/o Artículo no existen';
							break;
							case '3': $errorMsg = '3 - Cantidad ingresada no válida';
							break;
							case '4': $errorMsg = '4 - Cantidad defectuosa ingresada no válida';
							break;
							case '5': $errorMsg = '5 - Descuento ingresado no válido';
							break;
							case '6': $errorMsg = '6 - Exento ingresado no válido';
							break;
							case '7': $errorMsg = '7 - Alguno de los precios no es válido';
							break;
							case '8': $errorMsg = '8 - Sin retención no válida';
							break;
						}
					}
					echo "
						<div class='alert alert-error'>
							¡Hubó un error al actualizar el artículo!<br>
							$errorMsg
						</div>
					";
				}


				$attributes = array('name' => 'actualizar_articulos_form', 'id' => 'actualizar_articulos_form', 'class' => 'actualizar_articulos_form-form');

				echo form_open_multipart('articulos/editar/actualizarArticulos', $attributes);
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
								<p class="contact"><?php echo $Articulo_Codigo;?></p>
								<input name="articulo_codigo" type="hidden" value="<?php echo $Articulo_Codigo;?>">
							</td>
							<td>
								<label for="articulo_descripcion" class="contact">Descripción:</label>
							</td>
							<td colspan="3">
								<input id="articulo_descripcion" class="input_descripcion" autocomplete="off"  name="articulo_descripcion" required="" type="text" value="<?php echo $Articulo_Descripcion;?>">
							</td>
						</tr>
						<tr>
							<td>
								<label for="articulos_cantidad"  class="contact">Cantidad:</label>
							</td>
							<td>
								<input id="articulos_cantidad" class="input_uno" autocomplete="off" name="articulos_cantidad" required="" value="<?php echo $Articulo_Cantidad_Inventario;?>" onclick="this.select()">
							</td>
							<td>
								<label for="articulos_cantidad_defectuoso"  class="contact">Cantidad Defectuosa:</label>
							</td>
							<td>
								<input id="articulos_cantidad_defectuoso" class="input_uno" autocomplete="off" name="articulos_cantidad_defectuoso" required="" value="<?php echo $Articulo_Cantidad_Defectuoso;?>" onclick="this.select()">
							</td>
							<td>
								<label class="contact" > Exento de IVI</label>
							</td>
							<td>
								<input type="checkbox" name="exento" id="exento"  value="1" <?php if($Articulo_Exento){echo "checked";} ?>>
							</td>
						</tr>
						<tr>
							<td>
								<label for="sucursal"  class="contact">Empresa:</label>
							</td>
							<td>
								<p class="contact"><?php echo "$empresaId - $empresaNombre";?></p>
								<input type="hidden" name="sucursal" value="<?php echo $empresaId;?>">
							</td>
							<td>
								<label for="familia"  class="contact">Familia:</label>
							</td>
							<td>
								<p class="contact"><?php echo "$familiaId - $familiaNombre";?></p>
								<input type="hidden" name="familia" value="<?php echo $familiaId;?>">
							</td>
							<td>
								<label for="descuento"  class="contact">Descuento:</label>
							</td>
							<td>
								<input id="descuento" class="input_uno" autocomplete="off" name="descuento" value="<?php echo $Articulo_Descuento;?>" onclick="this.select()">
							</td>
						</tr>
						<tr>
							<td>
								<label for="foto_articulo"  class="contact">Foto:</label>
							</td>
							<td>
								<input type="file" id="foto_articulo" class="input_dos" name="userfile" size="10" accept=".jpg,.png,.ico,.bmp" >
							</td>
							<td>
								<label class="contact" > Sin Retención</label>
							</td>
							<td>
								<input type="checkbox" name="retencion" id="retencion"  value="1" <?php if($retencion){echo "checked";}?> >
							</td>
                                                    <td>
                                                        <label class="contact">Tipo Código:</label>
                                                    </td>
                                                    <td>
                                                        <select name="tipo_codigo" class="input_dos">
                                                            <?php
                                                                foreach($tipos_codigo as $tc){
                                                                    ?>
                                                            <option value="<?= $tc->Codigo ?>" <?= $tc->Codigo == $tipoCodigo ? "selected" : "" ?>><?= $tc->Descripcion ?></option>
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
                                                            <option value="<?= $um->Id ?>" <?= $unidadMedida == $um->Codigo ? "selected" : "" ?>><?= $um->Codigo." - ".$um->Descripcion ?></option>
                                                                    <?php
                                                                }
                                                            ?>
                                                        </select>
													</td>
													<td>
                                                        <label class="contact">Código Cabys:</label>
                                                    </td>
                                                    <td>
														<input type="text" id="codigo_cabys_display" class="input_dos" value="<?= $cabysCodigo ?>" disabled/>
														<input type="text" name="codigo_cabys" id="codigo_cabys" value="<?= $cabysCodigo ?>" style="display:none;"/>
														<input type="text" name="impuesto_cabys" id="impuesto_cabys" value="<?= $cabysImpuesto ?>" style="display:none;"/>
													</td>
													<td colspan="2">
                                                        <input type="text" id="busqueda_codigo_cabys" class="input_dos" placeholder="Busque aquí el código Cabys" value="<?= $cabysDescripcion ?>"/>
                                                    </td>
						</tr>
					</table>
				</fieldset>
				<fieldset class="field_precios">
					<legend>Precios</legend>
					<table>
						<tr>
							<td style="width:70px;">

							</td>
							<td style="width:110px;">
								<label class="contact">Monto</label>
							</td>
							<td>
								<label class="contact">Descuento</label>
							</td>
						</tr>
						<tr>
							<td>
								<label for="costo"  class="contact">Costo:</label>
							</td>
							<td>
								<input class="input_uno" value="*********" name="costo-mascara">
								<input id="costo" type="hidden" class="input_uno" autocomplete="off" name="costo" required="" value="<?php echo $costo_Editar->Precio_Monto;?>">
							</td>
							<td>
								<input class="input_uno" value="*********" name="costo-mascara-d">
								<input id="costo_d" type="hidden" class="input_uno" autocomplete="off" name="costo_d" required="" value="<?php echo $costo_Editar->Precio_Descuento;?>">
							</td>
						</tr>
						<tr>
							<td>
								<label for="precio1"  class="contact">Precio 1:</label>
							</td>
							<td>
								<input id="precio1" class="input_uno" autocomplete="off" name="precio1" required="" value="<?php echo $precio1_Editar->Precio_Monto;?>">
							</td>
							<td>
								<input id="precio1_d" class="input_uno" autocomplete="off" name="precio1_d" required="" value="<?php echo $precio1_Editar->Precio_Descuento;?>">
							</td>
						</tr>
						<tr>
							<td>
								<label for="precio2"  class="contact">Precio 2:</label>
							</td>
							<td>
								<input id="precio2" class="input_uno" autocomplete="off" name="precio2"  required="" value="<?php echo $precio2_Editar->Precio_Monto;?>">
							</td>
							<td>
								<input id="precio2_D" class="input_uno" autocomplete="off" name="precio2_d"  required="" value="<?php echo $precio2_Editar->Precio_Descuento;?>">
							</td>
						</tr>
						<tr>
							<td>
								<label for="precio3"  class="contact">Precio 3:</label>
							</td>
							<td>
								<input id="precio3" class="input_uno" autocomplete="off" name="precio3" value="<?php echo $precio3_Editar->Precio_Monto;?>">
							</td>
							<td>
								<input id="precio3_d" class="input_uno" autocomplete="off" name="precio3_d" value="<?php echo $precio3_Editar->Precio_Descuento;?>">
							</td>
						</tr>
						<tr>
							<td>
								<label for="precio4"  class="contact">Precio 4:</label>
							</td>
							<td>
								<input id="precio4" class="input_uno" autocomplete="off" name="precio4" value="<?php echo $precio4_Editar->Precio_Monto;?>">
							</td>
							<td>
								<input id="precio4_d" class="input_uno" autocomplete="off" name="precio4_d" value="<?php echo $precio4_Editar->Precio_Descuento;?>">
							</td>
						</tr>
						<tr>
							<td>
								<label for="precio5"  class="contact">Precio 5:</label>
							</td>
							<td>
								<input id="precio5" class="input_uno" autocomplete="off" name="precio5" value="<?php echo $precio5_Editar->Precio_Monto;?>">
							</td>
							<td>
								<input id="precio5_d" class="input_uno" autocomplete="off" name="precio5_d" value="<?php echo $precio5_Editar->Precio_Descuento;?>">
							</td>
						</tr>
					</table>
				</fieldset>
				<fieldset class="field_foto">
					<legend>Foto</legend>
					<img class="foto_articulo" id="foto_thumb" src="<?php echo base_url("application/images/articulos/$Articulo_Imagen_URL");?>" height="55"/>
					<div class="imagen-grande" ><img src="<?php echo base_url("application/images/articulos/$Articulo_Imagen_URL");?>" width="200" height="200"/></div>

				</fieldset>
				<fieldset class="field_barras">
					<legend>Codigo de Barras</legend>
					<img src="<?php echo base_url("application/libraries/barcode.php?codetype=Code25&size=55&text=$Articulo_Codigo_Barras");?>"  height="50" width="180"/>
				</fieldset>
				<div class="divButton">
					<input class="boton" name="submit" value="Actualizar" type="submit">
					<a class="boton_a" href='<?php echo base_url('articulos/editar')?>' class='boton_volver'>Volver</a>
				</div>
			</form>
			</div><!-- contenedor -->
		</div>

		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>