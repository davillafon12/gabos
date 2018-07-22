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
		<title>Edición De Cliente</title>
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
		<!--CSS ESTILO ACCORDION-->
		
		<script src="<?php echo base_url('application/scripts/jquery-1.10.2.js'); ?>" type="text/javascript"></script>
	
		<!--CSS ESTILO DEL FORMULARIO-->
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/clientes/style_editar.css?v='.$javascript_cache_version); ?>">
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/clientes/style_registrar.css?v='.$javascript_cache_version); ?>">
		<!--SCRIPT DE AJAX JQUERY-->
		<script src="<?php echo base_url('application/scripts/jquery.maskedinput.js'); ?>" type="text/javascript"></script>
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.numeric.js'); ?>" type="text/javascript"></script>

                <!--CARGA DEL TOOLS-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/clientes/editar.js?v='.$javascript_cache_version); ?>"></script>
		<!--CARGA DEL NOTY-->
		<script type="text/javascript" src="<?php echo base_url('application/scripts/jquery.noty.packaged.min.js'); ?>"></script>
			
	
	
	
	

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
			<p class="titulo_wrapper">Edición De Cliente</p>
			<hr class="division_wrapper">
			<p class="contenido_wrapper">
			<div  class="form">
            <?php 
                $attributes = array('name' => 'registrar_cliente_form', 'class' => 'registrar_cliente_form-form', 'id' => 'formulario_actualizacion_cliente');
                echo form_open_multipart('clientes/editar/actualizarCliente', $attributes); 
				
                $opc_estadoCliente= array ( 
                  'activo'   =>  'Activo' , 
				  'semiactivo' => 'Semiactivo',
                  'inactivo'     =>  'Inactivo' , 
                ); 
                $opc_pagoCliente= array ( 
                  '1'   =>  'Contado Publico' , 
                  '2'   =>  'Contado Afiliado' ,
                  '3'   =>  'Pago 3' ,
                  '4'   =>  'Pago 4' , 
                  '5'   =>  'Pago 5' 
                ); 
            ?>	 
		
                <fieldset class="recuadro">	
                    <legend>Información Personal</legend>
                    <table>
                    <tr>
                            <td>
                                    <label for="tipo_Cedula" class="label_form">Tipo Cédula:</label> 
                            </td>

                            <td>
                                <label for="tipo_Cedula" class="label_form"><?= ucfirst($Cliente_Tipo_Cedula) ?></label>   
                            </td>
                            <td>
                                    <label for="tipo_Cedula" class="label_form"># Cédula:</label>
                            </td>
                            <td>
                                <label for="tipo_Cedula" class="label_form"><?= $Cliente_Cedula ?></label>
                                <input type="hidden" name="cedula_res" value="<?= $Cliente_Cedula ?>"> 
                            </td>

                    </tr>
                    <tr>
                            <td>
                                <label for="nombre" class="label_form">Nombre:</label>
                            </td>
                            <td>
                                <input id="nombre" class="input_form" autocomplete="off"  name="nombre"  tabindex="4" type="text" value="<?= $Cliente_Nombre ?>"> 
                            </td>
                            <td>
                                <label for="apellidos" class="label_form">Apellidos:</label>
                            </td>
                            <td>
                                <input id="apellidos" class="input_form" autocomplete="off" name="apellidos"   tabindex="5" type="text" value="<?= $Cliente_Apellidos ?>">
                            </td>

                    </tr>
                    <tr>
                            <td>
                                <label for="carnet"  class="label_form">Fecha de Nacimiento:</label> 
                            </td>
                            <td>
                                <input class="input_form" placeholder="día/mes/año" autocomplete="off" name="fecha_nacimiento" id="fecha_nacimiento"  tabindex="6" value="<?= $Fecha_Nacimiento?>">
                            </td>
                            <td>
                                <label for="email"  class="label_form">Email:</label>
                            </td>
                            <td>
                                <input id="email" class="input_form" autocomplete="off" name="email" type="email" tabindex="9" value="<?= $Cliente_Correo_Electronico ?>">
                            </td>

                    </tr>
                    <tr>
                            <td>
                                <label for="estado_Cliente" class="label_form">Estado: </label>
                            </td>
                            <td>
                                <?php echo form_dropdown ( 'estado_Cliente' , $opc_estadoCliente ,  $Cliente_Estado, 'class="input_form", tabindex="3", id="estado_cliente_input"');  ?>
                            </td>
                            <td>
                                <label for="foto_cliente" class="label_form">Foto: </label>
                            </td>
                            <td>
                                <input type="file" name="userfile" size="10" id="foto_cliente" accept=".jpg,.png,.ico,.bmp"/>
                            </td>

                    </tr>			
                    <tr>
                        <td colspan="2">
                                <label for="essucursal" class="label_form" >Sucursal:</label>  
                                <input id="essucursal" tabindex="11" type="checkbox" name="issucursal" value="1" class="check_cliente" <?php if($isSucursal){echo "checked";}?> > 	
                                <label for="esexento" class="label_form" >Exento:</label>  
                                <input id="esexento" tabindex="11" type="checkbox" name="esexento" value="1"  class="check_cliente" <?php if($isExento){echo "checked";}?>> 	
                                <label for="aplicaRetencion" class="label_form" >Sin Retención:</label>  
                                <input id="aplicaRetencion" tabindex="11" type="checkbox" name="aplicaRetencion" value="1"   class="check_cliente" <?php if($aplicaRetencion){echo "checked";}?>> 
                            </td>
                        <td>
                            <label class="label_form">Fecha Ingreso:</label> 
                        </td>
                        <td>
                            <label class="label_form"><?= $Cliente_Fecha_Ingreso ?></label> 
                        </td>
                    </tr>

                    </table>
                </fieldset>            
                            
                <fieldset class="recuadro">	
                    <legend>Teléfonos</legend>
                    <table class="tabla-telefonos">
                    <tr>
                            <td>
                                <label for="telefono"  class="label_form">Telefono:</label>
                            </td>
                            <td>
                                <input id="cod_telefono" class="input_form_small codigo-area" autocomplete="off" name="codigo_telefono"   tabindex="8" value="<?= $Codigo_Pais_Telefono ?>"/>
                                <input id="telefono" class="input_form small" autocomplete="off" name="telefono"   tabindex="8" value="<?= $Cliente_Telefono ?>">
                            </td>
                            <td>
                                <label for="celular"  class="label_form">Celular:</label>
                            </td>
                            <td>
                                <input id="cod_celular" class="input_form_small codigo-area" autocomplete="off" name="codigo_celular"   tabindex="8" value="<?= $Codigo_Pais_Celular ?>" />
                                <input id="celular" class="input_form small" autocomplete="off" name="celular"   tabindex="7" value="<?= $Cliente_Celular ?>">
                            </td>
                            <td>
                                <label for="fax"  class="label_form">Fax:</label> 
                            </td>
                            <td>
                                <input id="cod_fax" class="input_form_small codigo-area" autocomplete="off" name="codigo_fax"   tabindex="8" value="<?= $Codigo_Pais_Fax ?>"/>
                                <input id="fax" class="input_form small" autocomplete="off" name="fax"   tabindex="6" value="<?= $Numero_Fax ?>" >
                            </td>


                    </tr>

                    </table>
                </fieldset>            
                            
                <fieldset class="recuadro">	
                    <legend>Domicilio</legend>
                    <table>


                    <tr>
                            <td>
                                <label for="direccion" class="label_form">Dirección:</label>
                            </td>
                            <td colspan="5">
                                <input id="direccion"  class="input_form" autocomplete="off" name="direccion"   tabindex="12" type="text" value="<?= $Cliente_Direccion ?>">
                            </td>
                            <td>
                                <label for="pais" class="label_form">País:</label>
                            </td>
                            <td>
                                <input id="pais" class="input_form" autocomplete="off"   tabindex="11" type="text" name="pais" style="    width: 150px;" value="<?= $Cliente_Pais ?>">
                            </td>
                    </tr>
                        <tr>
                            <td>
                                <label for="email"  class="labelMedium">Provincia:</label>
                            </td>
                            <td>
                                <select name="provincia" class="input_form input-dom" id="selector_provincia"> 
                                <option value="0">Seleccionar</option>
                                <?php 
                                    foreach($provincias as $prov):
                                ?>
                                    <option value="<?= $prov->ProvinciaID ?>" <?= $prov->ProvinciaID == $Provincia ? "selected" : "" ?>><?= $prov->ProvinciaNombre ?></option>
                                <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <label for="email"  class="labelMedium">Cantón:</label>
                            </td>
                            <td>
                                <select name="canton" class="input_form input-dom" id="selector_canton">
                                <option value="0">Seleccionar</option>
                                <?php 
                                    foreach($cantones as $can):
                                ?>
                                    <option value="<?= $can->CantonID ?>" <?= $can->CantonID == $Canton ? "selected" : "" ?>><?= $can->CantonNombre ?></option>
                                <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <label for="email"  class="labelMedium">Distrito:</label>
                            </td>
                            <td>
                                <select name="distrito" class="input_form input-dom" id="selector_distrito">
                                <option value="0">Seleccionar</option>
                                <?php 
                                    foreach($distritos as $dis):
                                ?>
                                <option value="<?= $dis->DistritoID ?>" <?= $dis->DistritoID == $Distrito ? "selected" : "" ?>><?= ucwords(strtolower($dis->DistritoNombre)) ?></option>
                                <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <label for="email"  class="labelMedium">Barrio:</label>
                            </td>
                            <td>
                                <select name="barrio" class="input_form input-dom" id="selector_barrio"> 
                                <option value="0">Seleccionar</option>
                                <?php 
                                    foreach($barrios as $bar):
                                ?>
                                    <option value="<?= $bar->BarrioID ?>" <?= $bar->BarrioID == $Barrio ? "selected" : "" ?>><?= $bar->BarrioNombre ?></option>
                                <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>

                    </table>
                </fieldset>           
                            
                <fieldset class="recuadro2">	
                    <legend>Montos Compras</legend>
                    <label for="tipo_pago_cliente"  class="montos-label">Tipo de pago:</label>
                    <?php echo form_dropdown ( 'tipo_pago_cliente' , $opc_pagoCliente ,  $Cliente_Numero_Pago, 'class="styleSelectPago", tabindex="16"' );  ?>
		</fieldset>

		<fieldset class="recuadro3">	
                    <legend>Observaciones</legend>
                    <textarea id="observaciones" autocomplete="off" class="observaciones" placeholder="" tabindex="17"name="observaciones" cols="25" rows="7" maxlength="300" ><?php echo $Cliente_Observaciones;?></textarea> 
                    <p class="advertencia_longitud">Máximo 300 caracteres</p> 
                </fieldset>

                <fieldset class="foto_recuadro">	
                    <legend>Foto</legend>
                    <img src="<?php echo $Cliente_Imagen_URL; ?>" height="150">
		</fieldset>

                <div class="divButton">			
                        <input class="buttom" value="Volver" type="button" onclick="window.location.assign('<?php echo base_url('/clientes/editar')?>')">
                        <input class="buttom" name="submit" id="submit" onsubmit="" tabindex="18" value="Actualizar" type="submit" >				
                </div>
		</form>	
		</div>
		</div>
		<!--Incluir footer-->
		<?php include PATH_FOOTER;?>
	</body>
</html>