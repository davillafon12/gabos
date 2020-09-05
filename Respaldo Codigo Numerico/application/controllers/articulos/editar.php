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
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion	
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);	
	if(!$permisos['editar_codigo'])
	{	
	    redirect('accesoDenegado', 'location');	
	}
	
	if(isset($_GET['id'])){ //Verifica que traiga esa variable
		if($result = $this->articulo->existe_Articulo($_GET['id'], $data['Sucursal_Codigo'])){ //Verifica que exista el articulo
			foreach($result as $row)
			{	
				$data['Articulo_Codigo'] = $row -> Articulo_Codigo;
				$data['Articulo_Descripcion'] = $row -> Articulo_Descripcion;
				$data['Articulo_Codigo_Barras'] = $row -> Articulo_Codigo_Barras;
				$data['Articulo_Cantidad_Inventario'] = $row -> Articulo_Cantidad_Inventario;
				$data['Articulo_Cantidad_Defectuoso'] = $row -> Articulo_Cantidad_Defectuoso;
				$data['Articulo_Descuento'] = $row -> Articulo_Descuento;
				//$data['Articulo_Imagen_URL'] = $row -> Articulo_Imagen_URL;
				$data['Articulo_Exento'] = $row -> Articulo_Exento;
				
				
				$URL_IMAGEN = $row->Articulo_Imagen_URL;				
				$ruta_a_preguntar = FCPATH.'application\\images\\articulos\\'.$URL_IMAGEN;				
				if(!file_exists($ruta_a_preguntar)){$URL_IMAGEN = '00.jpg';}
				$data['Articulo_Imagen_URL'] = $URL_IMAGEN;
				
				
				//$data['TB_05_Familia_Familia_Codigo'] = $row -> TB_05_Familia_Familia_Codigo;
				//$data['TB_02_Sucursal_Codigo'] = $row -> TB_02_Sucursal_Codigo;
				
				$data['costo_Editar'] = $this->articulo->getPrecioProducto($row->Articulo_Codigo, 0, $data['Sucursal_Codigo']);
				$data['precio1_Editar'] = $this->articulo->getPrecioProducto($row->Articulo_Codigo, 1, $data['Sucursal_Codigo']);
				$data['precio2_Editar'] = $this->articulo->getPrecioProducto($row->Articulo_Codigo, 2, $data['Sucursal_Codigo']);
				$data['precio3_Editar'] = $this->articulo->getPrecioProducto($row->Articulo_Codigo, 3, $data['Sucursal_Codigo']);
				$data['precio4_Editar'] = $this->articulo->getPrecioProducto($row->Articulo_Codigo, 4, $data['Sucursal_Codigo']);
				$data['precio5_Editar'] = $this->articulo->getPrecioProducto($row->Articulo_Codigo, 5, $data['Sucursal_Codigo']);
				
				$data['empresaId'] = $row -> TB_02_Sucursal_Codigo;
				$data['empresaNombre'] = $this->empresa->getNombreEmpresa($row -> TB_02_Sucursal_Codigo);
				
				$data['familiaId'] = $row -> TB_05_Familia_Familia_Codigo;
				$data['familiaNombre'] = $this->familia->getNombreFamiliaSucursal($row -> TB_05_Familia_Familia_Codigo, $row -> TB_02_Sucursal_Codigo);
				
				//$empresas_actuales = $this->empresa->get_empresas_ids_array($data['Sucursal_Codigo']);
				//$familias_actuales = $this->familia->get_familias_ids_array($data['Sucursal_Codigo']); 
				//$data['Familia_Empresas'] = $empresas_actuales;
				//$data['Familias'] = $familias_actuales;
			}
			$this->load->helper(array('form'));
			$this->load->view('articulos/articulos_edision_view', $data);
		}else{
			redirect('articulos/editar', 'location');
		}	
	}else{
		redirect('articulos/editar', 'location');	
	}
	
	/*
	
	
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
	}*/
 }
 
 function actualizarArticulos()
 {
	print_r($_POST);
	//print_r($_FILES);
	if(isset($_POST['articulo_codigo'])&&
		isset($_POST['sucursal'])){
		if($this->articulo->existe_Articulo($_POST['articulo_codigo'],$_POST['sucursal'])){
			$cantidad = $this->input->post('articulos_cantidad');
			$cantidadDefectuosa = $this->input->post('articulos_cantidad_defectuoso');
			$descuento = $this->input->post('descuento');
			
			$descripcion = $this->input->post('articulo_descripcion');
			
			$costo = $this->input->post('costo');
			$precio1 = $this->input->post('precio1');
			$precio2 = $this->input->post('precio2');
			$precio3 = $this->input->post('precio3');
			$precio4 = $this->input->post('precio4');
			$precio5 = $this->input->post('precio5');
			
			if(isset($_POST['exento'])){
				$exento = '1';
			}else{
				$exento = '0';
			}
			
			$foto = $this->articulo->getArticuloImagen($_POST['articulo_codigo'], $_POST['sucursal']);
			if(isset($_FILES['userfile']['name'])){
				$nombreFotoNueva = $_FILES['userfile']['name'];
				if(trim($nombreFotoNueva)!='')
				{
					//echo "Se actualiza foto";
					$foto = $nombreFotoNueva;
					$foto = $this->do_upload($_POST['articulo_codigo']);
					//Guardar nueva foto
				}else{
					//echo "no se actualiza foto";
				}
			}else{
				//echo "no se actualiza foto";
			}
			
			//echo "NOMBRE FOTO = $foto";
			
			if(is_numeric($cantidad)&&$cantidad>=0){
				if(is_numeric($cantidadDefectuosa)&&$cantidadDefectuosa>=0){
					if(is_numeric($descuento)&&$descuento>=0&&$descuento<=100){
						if(is_numeric($exento)&&($exento=='0'||$exento=='1')){
							if(is_numeric($costo)&&
								is_numeric($precio1)&&
								is_numeric($precio2)&&
								is_numeric($precio3)&&
								is_numeric($precio4)&&
								is_numeric($precio5)){
									//Actualizar info
									$info['dataBD'] = array(													
														'Articulo_Descripcion' => $descripcion,
														'Articulo_Cantidad_Inventario' => $cantidad,
														'Articulo_Cantidad_Defectuoso' => $cantidadDefectuosa,
														'Articulo_Descuento' => $descuento,
														'Articulo_Imagen_URL' => $foto,
														'Articulo_Exento' => $exento													
													);
									$info['precios'] = array(
														'p0' => $costo,
														'p1' => $precio1,
														'p2' => $precio2,
														'p3' => $precio3,
														'p4' => $precio4,
														'p5' => $precio5
													);
									$this->articulo->actualizar($_POST['articulo_codigo'], $_POST['sucursal'], $info['dataBD']);
									$this->articulo->actualizarPrecios($_POST['articulo_codigo'], $_POST['sucursal'], $info['precios']);
									
									include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
									$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario editó el artículo código: ".$_POST['articulo_codigo'],$data['Sucursal_Codigo'],'edicion');
							
									redirect('articulos/editar/edicion?id='.$_POST['articulo_codigo'].'&s=s', 'location');
							}else{
								//echo "Algun precio no es valido";
								redirect('articulos/editar/edicion?id='.$_POST['articulo_codigo'].'&s=e&e=7', 'location');
							}
						}else{
							//echo "Exento no valido";
							redirect('articulos/editar/edicion?id='.$_POST['articulo_codigo'].'&s=e&e=6', 'location');
						}
					}else{
						//echo "Decuento no valido";
						redirect('articulos/editar/edicion?id='.$_POST['articulo_codigo'].'&s=e&e=5', 'location');
					}
				}else{
					//echo "Cantidad defectuosa no valida";
					redirect('articulos/editar/edicion?id='.$_POST['articulo_codigo'].'&s=e&e=4', 'location');
				}
			}else{
				//echo "Cantidad no valida";
				redirect('articulos/editar/edicion?id='.$_POST['articulo_codigo'].'&s=e&e=3', 'location');
			}			
		}else{
			//echo "Articulo o Sucursal no existe";
			redirect('articulos/editar/edicion?id='.$_POST['articulo_codigo'].'&s=e&e=2', 'location');
		}
	}else{
		//echo "URL Mala";
		redirect('articulos/editar/edicion?id='.$_POST['articulo_codigo'].'&s=e&e=1', 'location');
	}
 
 	//echo"en construccion";
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
 
 function do_upload($cedula)
    {

       //especificamos a donde se va almacenar nuestra imagen
        $config['upload_path'] = 'application/images/articulos';
        //indicamos que tipo de archivos están permitidos
        $config['allowed_types'] = 'jpg|png';
        //indicamos el tamaño maximo permitido en este caso 1M
        $config['max_size'] = '5000';
        //le indicamos el ancho maximo permitido
        $config['max_width']  = '5000';
        //le indicamos el alto maximo permitodo
        $config['max_height']  = '5000';
        //Ponemos Nombre al archivo deseado
        $config['file_name']  = $cedula;
        //cargamos nuestra libreria con nuestra configuracion
        $this->load->library('upload', $config);
        //verificamos si existe errores
        //$this->upload->do_upload($field_name);
        //$field_name= $id_nombre; 

        if (!$this->upload->do_upload())
        {
        	//$this->direccion_url_imagen = "Default.png";
        }  
        else
        {
        	$data = array('upload_data' => $this->upload->data());
       
            foreach ($this->upload->data() as $item => $value){               
				if($item=="file_path"){
					$path=$value; 
				}if($item=="file_name"){
					$name=$value;
				}            
            }// end foreach
        	return $this->redimencionarImagen($path,$name);	
        }
    }  

    function redimencionarImagen($path,$name){
    	$config['image_library'] = 'gd2';
		$config['source_image']	= $path.$name; // le decimos donde esta la imagen que acabamos de subir
		$config['new_image']=$path."/thumb";
		//$config['create_thumb'] = TRUE;
		$config['maintain_ratio'] = TRUE;
		$config['quality'] = '100%';    // calidad de la imagen
		$config['width']	 = 200;
		$config['height']	= 200;
		$this->load->library('image_lib', $config);	
		if (!$this->image_lib->resize())
		{
			//$this->mensaje = $this->mensaje." error -> ".$this->image_lib->display_errors();
		}        
        $this->image_lib->resize();
		return $name;
    }
 
 
 
	
 }// FIN DE LA CLASE


?>