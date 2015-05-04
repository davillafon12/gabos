<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class editar extends CI_Controller {

 function __construct()
 {
    parent::__construct(); 
	$this->load->model('articulo','',TRUE);
	$this->load->model('empresa','',TRUE);
	$this->load->model('familia','',TRUE);
	$this->load->model('user','',TRUE);
	$this->load->model('bodega_m','',TRUE);
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
 
	function obtenerArticulosTabla(){
		include '/../get_session_data.php';
		//Un array que contiene el nombre de las columnas que se pueden ordenar
		$columnas = array(
								'0' => 'Articulo_Codigo',
								'1' => 'Articulo_Codigo',
								'2' => 'Articulo_Descripcion',
								'3' => 'Articulo_Cantidad_Inventario',
								'4' => 'Articulo_Descuento'
								);
		$query = $this->articulo->obtenerArticulosParaTabla($columnas[$_POST['order'][0]['column']], $_POST['order'][0]['dir'], $_POST['search']['value'], intval($_POST['start']), intval($_POST['length']), $data['Sucursal_Codigo']);
		
		$ruta_imagen = base_url('application/images/Icons');
		$articulosAMostrar = array();
		foreach($query->result() as $art){
			$auxArray = array(
				"<input class='checkbox'  type='checkbox' name='articulos_seleccionados' value='".$art->codigo."'>",
				$art->codigo,
				$art->descripcion,
				$art->inventario,
				$art->descuento,
				number_format($this->articulo->getPrecioProducto($art->codigo, 0, $data['Sucursal_Codigo']),2),
				number_format($this->articulo->getPrecioProducto($art->codigo, 1, $data['Sucursal_Codigo']),2),
				number_format($this->articulo->getPrecioProducto($art->codigo, 2, $data['Sucursal_Codigo']),2),
				"
				<div class='tab_opciones'>
					<a href='".base_url('')."articulos/editar/edicion?id=".$art->codigo."' ><img src=".$ruta_imagen."/editar.png width='21' height='21' title='Editar'></a>
				</div>
				"
			);
			array_push($articulosAMostrar, $auxArray);
		}
		
		$filtrados = $this->articulo->obtenerArticulosParaTablaFiltrados($columnas[$_POST['order'][0]['column']], $_POST['order'][0]['dir'], $_POST['search']['value'], intval($_POST['start']), intval($_POST['length']), $data['Sucursal_Codigo']);
		
		$retorno = array(
					'draw' => $_POST['draw'],
					'recordsTotal' => $this->articulo->getTotalArticulosEnSucursal($data['Sucursal_Codigo']),
					'recordsFiltered' => $filtrados -> num_rows(),
					'data' => $articulosAMostrar
				);
		echo json_encode($retorno);
	}
	
	function obtenerArticulosTablaManejo(){
		include '/../get_session_data.php';
		//Un array que contiene el nombre de las columnas que se pueden ordenar
		$columnas = array(
								'0' => 'Articulo_Codigo',
								'1' => 'Articulo_Codigo',
								'2' => 'Articulo_Descripcion',
								'3' => 'Articulo_Cantidad_Inventario',
								'4' => 'Articulo_Descuento'
								);
		$query = $this->articulo->obtenerArticulosParaTabla($columnas[$_POST['order'][0]['column']], $_POST['order'][0]['dir'], $_POST['search']['value'], intval($_POST['start']), intval($_POST['length']), $_POST['sucursal']);
		
		$ruta_imagen = base_url('application/images/Icons');
		$articulosAMostrar = array();
		foreach($query->result() as $art){
			$auxArray = array(
				"<input class='checkbox'  type='checkbox' name='articulos_seleccionados' value='".$art->codigo."'>",
				$art->codigo,
				$art->descripcion,
				$art->inventario,
				$art->descuento,
				number_format($this->articulo->getPrecioProducto($art->codigo, 0, $_POST['sucursal']),2),
				number_format($this->articulo->getPrecioProducto($art->codigo, 1, $_POST['sucursal']),2),
				number_format($this->articulo->getPrecioProducto($art->codigo, 2, $_POST['sucursal']),2),
				"
				<div class='tab_opciones'>
					<a href=".base_url('')."articulos/editar/edicion?id=".$art->codigo."&suc=".$_POST['sucursal']." ><img src=".$ruta_imagen."/editar.png width='21' height='21' title='Editar'></a>
				</div>
				"
			);
			array_push($articulosAMostrar, $auxArray);
		}
		
		$filtrados = $this->articulo->obtenerArticulosParaTablaFiltrados($columnas[$_POST['order'][0]['column']], $_POST['order'][0]['dir'], $_POST['search']['value'], intval($_POST['start']), intval($_POST['length']), $_POST['sucursal']);
		
		$retorno = array(
					'draw' => $_POST['draw'],
					'recordsTotal' => $this->articulo->getTotalArticulosEnSucursal($_POST['sucursal']),
					'recordsFiltered' => $filtrados -> num_rows(),
					'data' => $articulosAMostrar
				);
		echo json_encode($retorno);
	}
	
	
	
	function obtenerArticulosBodegaTablaManejo(){
		include '/../get_session_data.php';
		//Un array que contiene el nombre de las columnas que se pueden ordenar
		$columnas = array(
								'0' => 'Codigo',
								'1' => 'Descripcion',
								'2' => 'Cantidad'
								);
		$query = $this->bodega_m->obtenerArticulosBodegaParaTabla($columnas[$_POST['order'][0]['column']], $_POST['order'][0]['dir'], $_POST['search']['value'], intval($_POST['start']), intval($_POST['length']), $_POST['sucursal']);
		
		
		$articulosAMostrar = array();
		foreach($query->result() as $art){
			$auxArray = array(				
				$art->codigo,
				$art->descripcion,
				$art->inventario				
			);
			array_push($articulosAMostrar, $auxArray);
		}
		
		$filtrados = $this->bodega_m->obtenerArticulosBodegaParaTablaFiltrados($columnas[$_POST['order'][0]['column']], $_POST['order'][0]['dir'], $_POST['search']['value'], intval($_POST['start']), intval($_POST['length']), $_POST['sucursal']);
		
		$retorno = array(
					'draw' => $_POST['draw'],
					'recordsTotal' => $this->bodega_m->getTotalArticulosBodegaEnSucursal($_POST['sucursal']),
					'recordsFiltered' => $filtrados -> num_rows(),
					'data' => $articulosAMostrar
				);
		echo json_encode($retorno);
	}
	
	
 
 function edicion()
 {
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion	
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);	
	if(!$permisos['editar_codigo'])
	{	
	    redirect('accesoDenegado', 'location');	
	}
	
	if(isset($_GET['id'])){ //Verifica que traiga esa variable
		$sucursal = $data['Sucursal_Codigo'];
		//Si viene sucursal usamos la que viene, sino deja la del log del usuario
		if(isset($_GET['suc'])){
			if($this->empresa->getEmpresa($_GET['suc'])){
				$sucursal = $_GET['suc'];
			}							
		}
		if($result = $this->articulo->existe_Articulo($_GET['id'], $sucursal)){ //Verifica que exista el articulo
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
				$data['retencion'] = $row -> Articulo_No_Retencion;
				
				
				$URL_IMAGEN = $row->Articulo_Imagen_URL;				
				$ruta_a_preguntar = FCPATH.'application\\images\\articulos\\'.$URL_IMAGEN;				
				if(!file_exists($ruta_a_preguntar)){$URL_IMAGEN = '00.jpg';}
				$data['Articulo_Imagen_URL'] = $URL_IMAGEN;
				
				
				//$data['TB_05_Familia_Familia_Codigo'] = $row -> TB_05_Familia_Familia_Codigo;
				//$data['TB_02_Sucursal_Codigo'] = $row -> TB_02_Sucursal_Codigo;
				
				$data['costo_Editar'] = $this->articulo->getPrecioProducto($row->Articulo_Codigo, 0, $sucursal);
				$data['precio1_Editar'] = $this->articulo->getPrecioProducto($row->Articulo_Codigo, 1, $sucursal);
				$data['precio2_Editar'] = $this->articulo->getPrecioProducto($row->Articulo_Codigo, 2, $sucursal);
				$data['precio3_Editar'] = $this->articulo->getPrecioProducto($row->Articulo_Codigo, 3, $sucursal);
				$data['precio4_Editar'] = $this->articulo->getPrecioProducto($row->Articulo_Codigo, 4, $sucursal);
				$data['precio5_Editar'] = $this->articulo->getPrecioProducto($row->Articulo_Codigo, 5, $sucursal);
				
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
	
	
 }
 
 function actualizarArticulos()
 {
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
			
			
			
			//Si es exento
			$exento = 0;
			$exento = isset($_POST['exento']) && $_POST['exento']  ? "1" : "0";
			
			//Aplica Retencion
			$retencion = 0;
			$retencion = isset($_POST['retencion']) && $_POST['retencion']  ? "1" : "0";
			
			
			
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
							if(is_numeric($retencion)&&($retencion=='0'||$retencion=='1')){
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
															'Articulo_Exento' => $exento,
															'Articulo_No_Retencion'	 => $retencion											
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
								
										redirect('articulos/editar/edicion?id='.$_POST['articulo_codigo'].'&s=s&suc='.$_POST['sucursal'], 'location');
								}else{
									//echo "Algun precio no es valido";
									redirect('articulos/editar/edicion?id='.$_POST['articulo_codigo'].'&s=e&e=7', 'location');
								}
							}else{
								//echo "Retencion no valida";
								redirect('articulos/editar/edicion?id='.$_POST['articulo_codigo'].'&s=e&e=8', 'location');
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
	
	function agregarDescuentoMasivo(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['articulos'])&&isset($_POST['descuento'])){
			$articulos = json_decode($_POST['articulos']);
			$descuento = $_POST['descuento'];
			if(sizeof($articulos)>0){
				if(is_numeric($descuento)){
					if($descuento<=100&&$descuento>=0){
						
						include '/../get_session_data.php';
						$sucursal = $data['Sucursal_Codigo'];
						
						//Si viene sucursal usamos la que viene, sino deja la del log del usuario
						if(isset($_POST['sucursal'])){
							if($this->empresa->getEmpresa($_POST['sucursal'])){
								$sucursal = $_POST['sucursal'];
							}							
						}					
						
						foreach($articulos as $art){
							if($this->articulo->existe_Articulo($art,$sucursal)){
								$this->articulo->cambiarDescuento($art, $sucursal, $descuento);
							}
						}
						$retorno['status'] = 'success';
						unset($retorno['error']);
					}else{
						$retorno['error'] = '4'; //Descuento invalido
					}
				}else{
					$retorno['error'] = '4'; //Descuento invalido
				}
			}else{
				$retorno['error'] = '3'; //Sin articulos
			}
		}else{
			$retorno['error'] = '2'; //Mala URL			
		}
		echo json_encode($retorno);
	}
 
	function manejoArticulos()
	{	
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);	
		if(!$permisos['manejo_articulos'])
		{	
			redirect('accesoDenegado', 'location');	
		}
		$empresas_actuales = $this->empresa->get_empresas_ids_array();
		$data['Familia_Empresas'] = $empresas_actuales;		
		$this->load->view('articulos/manejo_articulos_view', $data);
	}
	
	function obtenerArticulosTemporales(){
		$retorno['status'] = 'error';
		$retorno['error'] = 1;
		if(isset($_POST['sucursal'])){
			$sucursal = $_POST['sucursal'];
			if($this->empresa->getEmpresa($sucursal)){
				$articulos = array();
				if($arts = $this->articulo->getArticulosFacturasTemporales($sucursal)){
					$articulos = $arts;
				}
				unset($retorno['error']);
				$retorno['status'] = 'success';
				$retorno['articulos'] = $articulos;
			}else{
				$retorno['error'] = 4;
			}
		}else{
			$retorno['error'] = 2;
		}
		echo json_encode($retorno);
	}
	
	function retornarArticulosTemporales(){
		$retorno['status'] = 'error';
		$retorno['error'] = 1;
		if(isset($_POST['sucursal'])){
			$sucursal = $_POST['sucursal'];
			if($this->empresa->getEmpresa($sucursal)){				
				if($articulos = $this->articulo->getArticulosFacturasTemporales($sucursal)){
					//Los devolvemos
					foreach($articulos as $art){
						$this->articulo->actualizarInventarioSUMA($art->Codigo_Articulo, $art->Cantidad, $sucursal);
					}
					//Los borramos de temporal
					$this->articulo->borrarArticulosTemporalesDeSucursal($sucursal);
					include '/../get_session_data.php';
					$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario retornó los artículos temporales de la sucursal $sucursal",$data['Sucursal_Codigo'],'devolucion');
					unset($retorno['error']);
					$retorno['status'] = 'success';
				}else{
					$retorno['error'] = 5;
				}				
			}else{
				$retorno['error'] = 4;
			}
		}else{
			$retorno['error'] = 2;
		}
		echo json_encode($retorno);		
	}
 
	
 }// FIN DE LA CLASE


?>