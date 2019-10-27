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
    $this->load->model('catalogo','',TRUE);
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion

	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

	if(!$permisos['editar_codigo'])
	{
	   redirect('accesoDenegado', 'location');
	}
 }

 function index()
 {
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
	$this->load->helper(array('form'));
	$this->load->view('articulos/articulos_editar_view', $data);
 }


 function soloConsultaArticulos(){
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
	$this->load->helper(array('form'));
	$this->load->view('articulos/articulos_consultar_view', $data);
 }

 function getMainTable()
 {
	$ruta_imagen = base_url('application/images/Icons');
	include PATH_USER_DATA;
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
                            <input type='hidden' value='".$this->articulo->getPrecioProducto($row->Articulo_Codigo, 0, $data['Sucursal_Codigo'])."'>
                            ******
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
		include PATH_USER_DATA;
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
				"<input type='hidden' value='".number_format($this->articulo->getPrecioProducto($art->codigo, 0, $data['Sucursal_Codigo']),2)."'> *****",
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

	function obtenerArticulosSoloConsulta(){
		include PATH_USER_DATA;
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
				$art->codigo,
				$art->descripcion,
				$art->inventario,
				$art->descuento,
				number_format($this->articulo->getPrecioProducto($art->codigo, 1, $data['Sucursal_Codigo']),2),
				number_format($this->articulo->getPrecioProducto($art->codigo, 2, $data['Sucursal_Codigo']),2),
				number_format($this->articulo->getPrecioProducto($art->codigo, 4, $data['Sucursal_Codigo']),2)
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
		include PATH_USER_DATA;
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
		include PATH_USER_DATA;
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
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	if(!$permisos['editar_codigo'])
	{
	    redirect('accesoDenegado', 'location');
	}

	if(isset($_GET['id'])){ //Verifica que traiga esa variable
		$sucursal = $data['Sucursal_Codigo'];
                $tiposCodigos = $this->catalogo->getTipoCodigoProductoServicio();
                $data['tipos_codigo'] = $tiposCodigos;
                $unidadesMedida = $this->catalogo->getUnidadesDeMedida();
                $data['unidades_medida'] = $unidadesMedida;
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
                                $data['tipoCodigo'] = $row -> TipoCodigo;
                                $data['unidadMedida'] = $row -> UnidadMedida;


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

			$costo = is_numeric($this->input->post('costo-mascara')) ? $this->input->post('costo-mascara') : $this->input->post('costo');
			$precio1 = $this->input->post('precio1');
			$precio2 = $this->input->post('precio2');
			$precio3 = $this->input->post('precio3');
			$precio4 = $this->input->post('precio4');
			$precio5 = $this->input->post('precio5');


			$tipo_codigo = $this->input->post('tipo_codigo');

                        $unidad_medida = $this->input->post('unidad_medida');

                        $unidad_medida = $this->catalogo->getUnidadDeMedidaById($unidad_medida)->Codigo;

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
															'Articulo_No_Retencion'	 => $retencion,
                                                                                                                        'TipoCodigo' => $tipo_codigo,
                                                                                                                        'UnidadMedida' => $unidad_medida
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

										include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
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

	$data_update['Sucursal_Nombre'] = $nombre_empresa);
	$data_update['Sucursal_Telefono'] = $telefono_empresa);
	$data_update['Sucursal_Direccion'] = $direccion_empresa);
	$data_update['Sucursal_Observaciones'] = $observaciones_empresa);

	//echo $id_empresa;
	//echo $nombre_empresa;

	$this->empresa->actualizar($id_empresa), $data_update);

	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
	$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario editó la empresa codigo: ".$id_empresa),$data['Sucursal_Codigo'],'edicion');

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

						include PATH_USER_DATA;
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

	function actualizarRetencionMasivo(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['articulos'])&&isset($_POST['estado'])){
			$articulos = json_decode($_POST['articulos']);
			$estado = $_POST['estado'];
			if(sizeof($articulos)>0){
				if(is_numeric($estado)){
					if($estado == '1' || $estado == '0'){

						include PATH_USER_DATA;
						$sucursal = $data['Sucursal_Codigo'];

						//Si viene sucursal usamos la que viene, sino deja la del log del usuario
						if(isset($_POST['sucursal'])){
							if($this->empresa->getEmpresa($_POST['sucursal'])){
								$sucursal = $_POST['sucursal'];
							}
						}

						foreach($articulos as $art){
							if($this->articulo->existe_Articulo($art,$sucursal)){
									$this->articulo->cambiarRetencion($art, $sucursal, $estado);
							}
						}
						$retorno['status'] = 'success';
						unset($retorno['error']);
					}else{
						$retorno['error'] = '6'; //Estado invalido
					}
				}else{
					$retorno['error'] = '6'; //Estado invalido
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
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
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
					include PATH_USER_DATA;
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

	function edicionMasivo()
	{
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		$this->load->helper(array('form'));
		$this->load->view('articulos/articulos_editar_masivo_view', $data);
	}

	function actualizarMasivo(){
		//print_r($_FILES);
		include PATH_USER_DATA;
		if(isset($_FILES['archivo_excel'])){
			$resultado = $this->procesarExcelEdicionMasiva();
			if($resultado['status']=='success'){
				//Verificamos que no hayan erroes, si los hay no procesar nada
				if(sizeOf($resultado['erroresCosto']) == 0){
					$articulos = $resultado['articulos'];
					foreach($articulos as $articulo){
						$codigo = $articulo["codigo"];
						$sucursal = $data['Sucursal_Codigo'];
						if($articuloBD = $this->articulo->existe_Articulo($codigo, $sucursal)){
							//Actualizamos descripcion
							$update = array("Articulo_Descripcion"=>$articulo["descripcion"]);
							$this->articulo->actualizar($codigo, $sucursal, $update);

							//Actualizamos precios
							$this->articulo->actualizarPrecios($codigo, $sucursal, $articulo);

							$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario actualizó el articulo de manera masiva: ".$codigo." en la sucursal: ".$sucursal,$data['Sucursal_Codigo'],'actualizar_masivo_articulo');
						}
					}
					//Todo salio bien
					redirect('articulos/editar/edicionMasivo?s=1', 'location');
				}else{
					//Error en ciertos articulos
					//echo "Error en ciertos articulos";
					$this->load->helper(array('form'));
					$data['error'] = '5';
					$data['msj'] = 'Algunos artículos presentan problemas';
					$data['errorCosto'] = $resultado['erroresCosto'];
					$this->load->view('articulos/articulos_editar_masivo_view', $data);
				}
			}else{
				if($resultado['error']=='1'){
					//echo "No se pudo leer y procesar el excel";
					$this->load->helper(array('form'));
					$data['error'] = '4';
					$data['msj'] = 'No se pudo procesar el archivo excel';
					$this->load->view('articulos/articulos_editar_masivo_view', $data);
				}else if($resultado['error']=='2'){
					//echo "Columnas requeridas no vienen o estan en mal formato";
					$this->load->helper(array('form'));
					$data['error'] = '3';
					$data['msj'] = 'Columnas no válidas, o no están en orden';
					$this->load->view('articulos/articulos_editar_masivo_view', $data);
				}
			}
		}else{
			//URL Mala
			//echo "URL mala";
			$this->load->helper(array('form'));
			$data['error'] = '1';
			$data['msj'] = 'La URL está incompleta, contacte al administrador';
			$this->load->view('articulos/articulos_editar_masivo_view', $data);
		}
	}

	private function procesarExcelEdicionMasiva(){
		$resultado = array('status'=>'error','error'=>'1'); //Error generico de no se pudo realizar el proceso
		require_once './application/libraries/PHPExcel/IOFactory.php';
    	$objPHPExcel = PHPExcel_IOFactory::load($_FILES['archivo_excel']['tmp_name']);
		$cantidadHojas = 1; //Para que solo procese la primera hoja del excel
		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
			if($cantidadHojas == 1){
				$cantidadHojas++;
				//Probamos que el orden de las columnas sea el requerido
				$celda01 = $worksheet->getCellByColumnAndRow(0, 1)->getValue();
				$celda11 = $worksheet->getCellByColumnAndRow(1, 1)->getValue();
				$celda21 = $worksheet->getCellByColumnAndRow(2, 1)->getValue();
				$celda31 = $worksheet->getCellByColumnAndRow(3, 1)->getValue();
				$celda41 = $worksheet->getCellByColumnAndRow(4, 1)->getValue();
				$celda51 = $worksheet->getCellByColumnAndRow(5, 1)->getValue();
				$celda61 = $worksheet->getCellByColumnAndRow(6, 1)->getValue();

				if(trim($celda01) == 'CODIGO' && trim($celda11) == 'DESCRIPCION' && trim($celda21) == 'PRECIO 1'
																					&& trim($celda31) == 'PRECIO 2'
																					&& trim($celda41) == 'PRECIO 3'
																					&& trim($celda51) == 'PRECIO 4'
																					&& trim($celda61) == 'PRECIO 5'){
					$highestRow = $worksheet->getHighestRow();
					//Lleva el control de cuales productos presentaron errores
					$erroresCosto = array();
					$articulos = array();
					for ($row = 2; $row <= $highestRow; ++ $row){
						$codigo = trim($worksheet->getCellByColumnAndRow(0, $row)->getValue());
						$descripcion = trim($worksheet->getCellByColumnAndRow(1, $row)->getValue());

						if($codigo != "" && $descripcion != ""){
							$art = array("codigo"=>$codigo,"descripcion"=>$descripcion);


							for($index = 2; $index <= 6; $index++){
								if(!is_numeric($worksheet->getCellByColumnAndRow($index, $row)->getValue())){
									array_push($erroresCosto, $codigo);
								}else{
									$art["p".($index-1)] = $worksheet->getCellByColumnAndRow($index, $row)->getValue();
								}
							}

							array_push($articulos, $art);
						}
					}
					$resultado["status"] = "success";
					unset($resultado["error"]);
					$resultado["articulos"] = $articulos;
					$resultado["erroresCosto"] = $erroresCosto;
				}else{
					//No tiene las columnas requeridas
					$resultado['error'] = '2';
				}
			}
		}
		return $resultado;
	}

	function imagenes()
	{
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		$this->load->helper(array('form'));
		$this->load->view('articulos/articulos_imagenes_masivo_view', $data);
	}

	function actualizarMasivoImagenes(){
		if(isset($_FILES['imagenes'])){
			if(sizeof($_FILES['imagenes']['name']) > 0){
				if($this->revisarTamanoImagen($_FILES['imagenes'])){
					if($this->revisarFormatoImagen($_FILES['imagenes'])){
						include PATH_USER_DATA;
						$imagenes = $_FILES['imagenes'];
						$cantidad = sizeof($imagenes['name']);
						for($index = 0; $index < $cantidad; $index++){
							$ext = ".".pathinfo($imagenes['name'][$index], PATHINFO_EXTENSION);
							$codigo = str_replace($ext, "", $imagenes['name'][$index]);
							$tmpPath = $imagenes['tmp_name'][$index];
							$fullName = $imagenes['name'][$index];
							$sucursal = $data['Sucursal_Codigo'];

							//Actualizamos en la tabla
							$update = array("Articulo_Imagen_URL"=>$fullName);
							$this->articulo->actualizar($codigo, $sucursal, $update);

							//Actualizamos el archivo
							$ruta_a_preguntar = FCPATH.'application/images/articulos/'.$fullName;
							move_uploaded_file($tmpPath, $ruta_a_preguntar);
						}
						redirect('articulos/editar/imagenes?s=1', 'location');
					}else{
						$this->load->helper(array('form'));
						$data['error'] = '4';
						$data['msj'] = 'Las imagenes tienen un formato indebido';
						$this->load->view('articulos/articulos_imagenes_masivo_view', $data);
					}
				}else{
					$this->load->helper(array('form'));
					$data['error'] = '3';
					$data['msj'] = 'Las imagenes deben ser menores a 1MB';
					$this->load->view('articulos/articulos_imagenes_masivo_view', $data);
				}
			}else{
				$this->load->helper(array('form'));
				$data['error'] = '2';
				$data['msj'] = 'Debe seleccionar al menos una imagen para actualizar';
				$this->load->view('articulos/articulos_imagenes_masivo_view', $data);
			}
		}else{
			//URL Mala
			//echo "URL mala";
			$this->load->helper(array('form'));
			$data['error'] = '1';
			$data['msj'] = 'La URL está incompleta, contacte al administrador';
			$this->load->view('articulos/articulos_imagenes_masivo_view', $data);
		}
	}

	private function revisarTamanoImagen($imagenes){
		$cantidad = sizeof($imagenes['name']);
		for($index = 0; $index < $cantidad; $index++){
			if(floatval($imagenes['size'][$index]) > 1024000){
				return false;
			}
		}
		return true;
	}

	private function revisarFormatoImagen($imagenes){
		$formatos = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
		$cantidad = sizeof($imagenes['name']);
		for($index = 0; $index < $cantidad; $index++){
			$ext = pathinfo($imagenes['name'][$index], PATHINFO_EXTENSION);
			if(!array_key_exists($ext, $formatos)){
				return false;
			}
		}
		return true;
	}

 }// FIN DE LA CLASE
