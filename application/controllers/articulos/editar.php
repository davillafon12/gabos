<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class editar extends CI_Controller {

 function __construct()
 {
    parent::__construct(); 
	$this->load->model('articulo','',TRUE);
	$this->load->model('empresa','',TRUE);
	$this->load->model('familia','',TRUE);
	$this->load->model('user','',TRUE);
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	
	if(!$permisos['editar_codigo'])
	{	
	   redirect('accesoDenegado', 'location');
	}
 }

 function index()
 {
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
	$this->load->helper(array('form'));
	$this->load->view('articulos/articulos_editar_view', $data);
 }
 
 function getMainTable()
 {	
	$ruta_imagen = base_url('application/images/Icons');
	include '/../get_session_data.php';
	if($result = $this->articulo->get_Articulos($data['Sucursal_Codigo']))
	{	
	    echo "<table id='tabla_editar' class='tablaPrincipal'>";
		echo "<thead> <tr>
						<th >
                            
                        </th>
                        <th class='Sorted_enabled'>
                            Código
                        </th>
                        <th>
                            Descripción
                        </th>
                        <th>
                            Código Barras
                        </th>
						<th class='Sorted_enabled'>
                            Cantidad Inventario
                        </th>
						<th class='Sorted_enabled'>
                            Cantidad Defectuoso
                        </th>
                        <th class='Sorted_enabled'>
                            Descuento
                        </th>
                        <th class='Sorted_enabled'>
                            Familia
                        </th>
                        <th class='Sorted_enabled'>
                            Costo
                        </th>
                        <th class='Sorted_enabled'>
                            Precio2
                        </th>
                        <th class='Sorted_enabled'>
                            Precio3
                        </th>
                        <th class='Sorted_enabled'>
                            Precio4
                        </th>
						<th >
                            Opciones
                        </th>
                    </tr></thead> <tbody>";
			foreach($result as $row)
			{
				//$precio1 = $this->articulo->get_Precios_ArticuloSeparado($row->Articulo_Codigo, 1); 
				echo "<tr class='table_row'>
						<td >
                            <input class='checkbox'  type='checkbox' name='checkbox' value='".$row->Articulo_Codigo."'>
                        </td>
                        <td>
                            ".$row->Articulo_Codigo."
                        </td>
                        <td>
                            ".$row->Articulo_Descripcion."
                        </td>
                        <td>
                            ".$row->Articulo_Codigo_Barras."
                        </td>
                        <td>
                            ".$row->Articulo_Cantidad_Inventario."
                        </td>
                        <td>
                            ".$row->Articulo_Cantidad_Defectuoso."
                        </td>
                        <td>
                            ".$row->Articulo_Descuento."
                        </td>
                        <td>
                            ".$row->TB_05_Familia_Familia_Codigo."
                        </td>
                        <td>
                            ".$this->articulo->getPrecioProducto($row->Articulo_Codigo, 0, $data['Sucursal_Codigo'])."
                        </td>
                        <td>
                            ".$this->articulo->getPrecioProducto($row->Articulo_Codigo, 1, $data['Sucursal_Codigo'])."
                        </td>
                        <td>
                            ".$this->articulo->getPrecioProducto($row->Articulo_Codigo, 2, $data['Sucursal_Codigo'])."
                        </td>
                        <td>
                            ".$this->articulo->getPrecioProducto($row->Articulo_Codigo, 3, $data['Sucursal_Codigo'])."
                        </td>                                                
						<td >
							<div class='tab_opciones'>
								<a href='".base_url('')."articulos/editar/edicion?id=".$row->Articulo_Codigo."' ><img src=".$ruta_imagen."/editar.png width='21' height='21' title='Editar'></a>
							</div>
						</td>
                    </tr>";
			}
		echo "</tbody></table>";
		//echo "</div>";
		echo "<div class='div_bot_des'>
					<a href='javascript:;' onClick='resetCheckBox()' class='boton_desall'>Deseleccionar Todo</a>
					<a href='".base_url('')."articulos/registrar' class='boton_agregar'>Agregar Articulo</a>
			  </div>";
	}
 }//FIN DE GETTABLE
 
 function edicion()
 {
	$id_request=$_GET['id'];
	$ruta_base_imagenes_script = base_url('application/images/scripts');
	$nombre_empresa = $this->empresa->getNombreEmpresa($id_request);
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
	
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	
	if(!$permisos['editar_codigo'])
	{	
	    redirect('accesoDenegado', 'location');	
	}
	if($result = $this->articulo->get_Articulos($data['Sucursal_Codigo']))
	{
	    $this->load->helper(array('form'));
	    foreach($result as $row)
		{
		    $data['Articulo_Codigo'] = $row -> Articulo_Codigo;
			$data['Articulo_Descripcion'] = $row -> Articulo_Descripcion;
			$data['Articulo_Codigo_Barras'] = $row -> Articulo_Codigo_Barras;
			$data['Articulo_Cantidad_Inventario'] = $row -> Articulo_Cantidad_Inventario;
			$data['Articulo_Cantidad_Defectuoso'] = $row -> Articulo_Cantidad_Defectuoso;
			$data['Articulo_Descuento'] = $row -> Articulo_Descuento;
			$data['Articulo_Imagen_URL'] = $row -> Articulo_Imagen_URL;
			$data['Articulo_Exento'] = $row -> Articulo_Exento;
			$data['TB_05_Familia_Familia_Codigo'] = $row -> TB_05_Familia_Familia_Codigo;
			$data['TB_02_Sucursal_Codigo'] = $row -> TB_02_Sucursal_Codigo;
			$data['costo_Editar'] = $this->articulo->getPrecioProducto($row->Articulo_Codigo, 0, $data['Sucursal_Codigo']);
			$data['precio1_Editar'] = $this->articulo->getPrecioProducto($row->Articulo_Codigo, 1, $data['Sucursal_Codigo']);
			$data['precio2_Editar'] = $this->articulo->getPrecioProducto($row->Articulo_Codigo, 2, $data['Sucursal_Codigo']);
			$data['precio3_Editar'] = $this->articulo->getPrecioProducto($row->Articulo_Codigo, 3, $data['Sucursal_Codigo']);
			$data['precio4_Editar'] = $this->articulo->getPrecioProducto($row->Articulo_Codigo, 4, $data['Sucursal_Codigo']);
			$data['precio5_Editar'] = $this->articulo->getPrecioProducto($row->Articulo_Codigo, 5, $data['Sucursal_Codigo']);
			$empresas_actuales = $this->empresa->get_empresas_ids_array($data['Sucursal_Codigo']);
			$familias_actuales = $this->familia->get_familias_ids_array($data['Sucursal_Codigo']); 
			$data['Familia_Empresas'] = $empresas_actuales;
			$data['Familias'] = $familias_actuales;
		}
		$this->load->view('articulos/articulos_edision_view', $data);
	}
	else
	{
		$data['Titulo_Pagina'] = "Transacción Fallida";
		$data['Mensaje_Push'] = "<div class='sub_div'><p class='titles'>Hubo un error al actualizar el articulo ".$articulo_codigo."! <img src=".$ruta_base_imagenes_script."/error.gif /></p></div><br>
		                         <div class='Informacion'>								 
					             <form action=".base_url('empresas/editar').">
									 <input class='buttom' tabindex='2' value='Volver' type='submit' >
				                 </form>								 
								 </div>";
		$this->load->view('articulos/view_informacion_guardado', $data);
	}
 }
 
 function actualizarArticulos()
 {
 	echo"en construccion";
	/*$id_empresa = $this->input->post('codigo');
	$nombre_empresa = $this->input->post('name');
	$telefono_empresa = $this->input->post('telefono');
	$observaciones_empresa = $this->input->post('observaciones');
	$direccion_empresa = $this->input->post('direccion');
		
	$data_update['Sucursal_Nombre'] = mysql_real_escape_string($nombre_empresa);
	$data_update['Sucursal_Telefono'] = mysql_real_escape_string($telefono_empresa);
	$data_update['Sucursal_Direccion'] = mysql_real_escape_string($direccion_empresa);
	$data_update['Sucursal_Observaciones'] = mysql_real_escape_string($observaciones_empresa);
	
	//echo $id_empresa;
	//echo $nombre_empresa;
	
	$this->empresa->actualizar(mysql_real_escape_string($id_empresa), $data_update);
	
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
	$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario editó la empresa codigo: ".mysql_real_escape_string($id_empresa),$data['Sucursal_Codigo'],'edicion');
	
	redirect('empresas/editar', 'location');
	*/
 }
 
 
 
	
 }// FIN DE LA CLASE


?>