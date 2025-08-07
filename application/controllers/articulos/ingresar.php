<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ingresar extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('articulo','',TRUE);
		$this->load->model('empresa','',TRUE);
		$this->load->model('familia','',TRUE);
		$this->load->model('user','',TRUE);
		$this->load->model('bodega_m','',TRUE);
                $this->load->model('catalogo','',TRUE);
	}

	function index()
	{
		redirect('home', 'location');
	}

	function individual(){
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion

		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['registrar_articulo_individual']){
		   redirect('accesoDenegado', 'location');
		}

		$this->load->helper(array('form'));
		$empresas_actuales = $this->empresa->get_empresas_ids_array();
		$familias_actuales = $this->familia->get_familias_ids_array($data['Sucursal_Codigo']);
		$tiposCodigos = $this->catalogo->getTipoCodigoProductoServicio();
		$unidadesMedida = $this->catalogo->getUnidadesDeMedida();
		$tipoDescuentos = $this->catalogo->getTipoDescuentos();
		$data['Familia_Empresas'] = $empresas_actuales;
		$data['Familias'] = $familias_actuales;
		$data['tipo_codigo'] = $tiposCodigos;
		$data['unidades_medida'] = $unidadesMedida;
		$data['tipoDescuentos'] = $tipoDescuentos;
		$data['javascriptCacheVersion'] = $this->javascriptCacheVersion;
		$this->load->view('articulos/articulos_ingreso_individual', $data);
	}

	function registrar(){
		//print_r($_POST);
		//exit;
		$codigo_Articulo = $this->input->post('articulo_codigo');
		//$codigoBrasil = $this->input->post('codigo_bodega');
		$descripcion_Articulo = $this->input->post('articulo_descripcion');
		$codigoBarras_articulo = $this->input->post('articulo_codigo');
		$cantidad_Articulos = $this->input->post('articulos_cantidad');
		$cantidad_Defectuosa = $this->input->post('articulos_cantidad_defectuoso');
		$descuento_Articulo = $this->input->post('descuento');
		$empresa_Articulo = $this->input->post('sucursal');
		$this->do_upload($codigo_Articulo.$empresa_Articulo); // aqui jala la imagen
		$exento_articulo = $this->input->post('exento');
		$retencion = $this->input->post('retencion');
		$familia_articulo = $this->input->post('familia');
		$costo_Articulo = $this->input->post('costo');
		$precio1_Articulo = $this->input->post('precio1');
		$precio2_Articulo = $this->input->post('precio2');
		$precio3_Articulo = $this->input->post('precio3');
		$precio4_Articulo = $this->input->post('precio4');
		$precio5_Articulo = $this->input->post('precio5');
		$tipo_codigo = $this->input->post('tipo_codigo');
		$unidad_medida = $this->input->post('unidad_medida');

		$codigoCabys = $this->input->post('codigo_cabys');
		$impuestoCabys = $this->input->post('impuesto_cabys');
		$codigoDescuento = $this->input->post('tipo_codigo_descuento');

		$unidad_medida = $this->catalogo->getUnidadDeMedidaById($unidad_medida)->Codigo;


		$costo_d = $this->input->post('costo_d');
		$precio1_d = $this->input->post('precio1_d');
		$precio2_d = $this->input->post('precio2_d');
		$precio3_d = $this->input->post('precio3_d');
		$precio4_d = $this->input->post('precio4_d');
		$precio5_d = $this->input->post('precio5_d');

		$costo_codigo_d = $this->input->post('costo_codigo_d');
		$precio1_codigo_d = $this->input->post('precio1_codigo_d');
		$precio2_codigo_d = $this->input->post('precio2_codigo_d');
		$precio3_codigo_d = $this->input->post('precio3_codigo_d');
		$precio4_codigo_d = $this->input->post('precio4_codigo_d');
		$precio5_codigo_d = $this->input->post('precio5_codigo_d');


		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		$ruta_base_imagenes_script = base_url('application/images/scripts');
		if($this->articulo->registrar(
			$codigo_Articulo, 
			$descripcion_Articulo, 
			$codigoBarras_articulo, 
			$cantidad_Articulos, 
			$cantidad_Defectuosa, 
			$descuento_Articulo, 
			$this->direccion_url_imagen,
			$exento_articulo, 
			$retencion, 
			$familia_articulo, 
			$empresa_Articulo, 
			$costo_Articulo, 
			$precio1_Articulo, 
			$precio2_Articulo, 
			$precio3_Articulo,  
			$precio4_Articulo, 
			$precio5_Articulo, 
			$tipo_codigo, 
			$unidad_medida, 
			$codigoCabys, 
			$impuestoCabys,
			$costo_d, 
			$precio1_d, 
			$precio2_d, 
			$precio3_d, 
			$precio4_d, 
			$precio5_d,
			$codigoDescuento,
			$costo_codigo_d,
			$precio1_codigo_d,
			$precio2_codigo_d,
			$precio3_codigo_d,
			$precio4_codigo_d,
			$precio5_codigo_d))
		{ //Si se ingreso bien a la BD
			//$this->bodega_m->restarCantidadBodega($cantidad_Articulos, $codigoBrasil, $empresa_Articulo);

			//Titulo de la pagina
			$mensajeExento = "";
			if($codigoBarras_articulo){
				$mensajeExento = "Si";
			}
			else{
				$mensajeExento = "No";
			}
			$data['Titulo_Pagina'] = "Transacción Exitosa";

			$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ingreso el articulo ".$codigo_Articulo." cantidad: ".$cantidad_Articulos,$data['Sucursal_Codigo'],'registro');
			$data['Mensaje_Push'] = "<div class='sub_div'><p class='titles'>El ingreso del articulo ".$codigo_Articulo." fue exitoso! <img src=".$ruta_base_imagenes_script."/tick.gif /></p></div><br>
									 <div class='Informacion'>
									 <form action=".base_url('articulos/ingresar/individual').">
									 <p class='titles'>Datos del Articulo:</p><br><hr>
									 <img src=".base_url('application/images/articulos/thumb/'.$this->direccion_url_imagen)." alt=\"Smiley face\" height=\"100\" width=\"100\"><br>
									 <p class='titles'>-Codigo:</p> <p class='content'>".$codigo_Articulo.".</p><br>
									 <p class='titles'>-Descripción:</p> <p class='content'>".$descripcion_Articulo.".</p><br>
									 <p class='titles'>-Cantidad Existente:</p> <p class='content'>".$cantidad_Articulos.".</p><br>
									 <p class='titles'>-Cantidad Defectuosa:</p> <p class='content'>".$cantidad_Defectuosa.".</p><br>
									 <p class='titles'>-Articulo Exento:</p> <p class='content'>".$mensajeExento.".</p><br>
									 <p class='titles'>-Descuento:</p> <p class='content'>".$descuento_Articulo.".</p><br>
									 <p class='titles'>-Familia:</p> <p class='content'>".$familia_articulo.".</p><br>
									 <p class='titles'>-Empresa:</p> <p class='content'>".$empresa_Articulo.".</p><br>
									 <p class='titles'>-Costo:</p> <p class='content'>".$costo_Articulo.".</p><br>
									 <p class='titles'>-Precio 1:</p> <p class='content'>".$precio1_Articulo.".</p><br>
									 <p class='titles'>-Precio 2:</p> <p class='content'>".$precio2_Articulo.".</p><br>
									 <p class='titles'>-Precio 3:</p> <p class='content'>".$precio3_Articulo.".</p><br>
									 <p class='titles'>-Precio 4:</p> <p class='content'>".$precio4_Articulo.".</p><br>
									 <p class='titles'>-Precio 5:</p> <p class='content'>".$precio5_Articulo.".</p><br>
									 <input class='buttom' tabindex='4' value='Registrar otro articulo' type='submit'>
									 <a href='".base_url('home')."' class='boton_volver'>Volver</a>
									 </form>
									 </div>";
			$this->load->view('articulos/view_informacion_guardado', $data);

		}
		else
		{ //Hubo un error  no se ingreso a la BD
			$data['Titulo_Pagina'] = "Transacción Fallida";
			$data['Mensaje_Push'] = "<div class='sub_div'><p class='titles'>Hubo un error al ingresar el articulo ".$codigo_Articulo."! <img src=".$ruta_base_imagenes_script."/error.gif /></p></div><br>
									 <div class='Informacion'>
									 <form action=".base_url('articulos/ingresar/individual').">
										 <input class='buttom' tabindex='2' value='Registrar otro articulo' type='submit' >
									 </form>
									 </div>";
			$this->load->view('articulos/view_informacion_guardado', $data);
		}
	}

	function getFamiliasSucursal(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['sucursal'])){
			$sucursal = $_POST['sucursal'];
			if($this->empresa->getEmpresa($sucursal)){
				if($familias = $this->familia->getFamilias($sucursal)){
					$fams = array();
					foreach($familias as $familia){
						array_push($fams, array(
												'codigo' => $familia->Familia_Codigo,
												'nombre' => $familia->Familia_Nombre
												));
					}
					unset($retorno['error']);
					$retorno['status'] = 'success';
					$retorno['familias'] = $fams;
				}else{
					$retorno['error'] = '4'; //No hay familias
				}
			}else{
				$retorno['error'] = '3'; //Sucursal no existe
			}
		}else{
			$retorno['error'] = '2'; //URL MALA
		}
		echo json_encode($retorno);

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
        	$this->direccion_url_imagen = "Default.png";
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
        	$this->redimencionarImagen($path,$name);
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
        $this->direccion_url_imagen = $name;
        $this->image_lib->resize();
    }

	function masivo(){
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion

		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['registrar_articulos_masivo'])
		{
		   redirect('accesoDenegado', 'location');
		}

		$data['javascriptCacheVersion'] = $this->javascriptCacheVersion;
		$this->load->helper(array('form'));
		$this->load->view('articulos/ingreso_masivo_articulos_view', $data);
	}


	function cargaMasiva(){

		include PATH_USER_DATA;
		if(isset($_FILES['archivo_excel'])){
				$resultado = $this->procesarExcel();
				//echo "<pre>"; print_r($resultado); die;
				if($resultado['status']=='success'){
					//Verificamos que no hayan erroes, si los hay no procesar nada
					if(	sizeOf($resultado["erroresCodigo"])==0 &&
						sizeOf($resultado["erroresCosto"])==0 &&
						sizeOf($resultado["erroresPrecio1"])==0 &&
						sizeOf($resultado["erroresPrecio2"])==0 &&
						sizeOf($resultado["erroresPrecio3"])==0 &&
						sizeOf($resultado["erroresPrecio4"])==0 &&
						sizeOf($resultado["erroresPrecio5"])==0 &&
						sizeOf($resultado["erroresCantidad"])==0 &&
						sizeOf($resultado["erroresFamilia"])==0 &&
						sizeOf($resultado["erroresSucursal"])==0 &&
						sizeOf($resultado["erroresExento"])==0 &&
						sizeOf($resultado["erroresRetencion"])==0 &&
						sizeOf($resultado["erroresDescuento"])==0 &&
						sizeOf($resultado["erroresTipoCodigo"])==0 &&
						sizeOf($resultado["erroresUnidadMedida"])==0 &&
						sizeOf($resultado["erroresCodigoCabys"])==0 &&
						sizeOf($resultado["erroresCodigoDescuento"])==0 ){
						//	die;
						$articulos = $resultado['articulos'];
						foreach($articulos as $articulo){
							$this->articulo->registrar($articulo['cod'],
							$articulo['des'],
							$articulo['cod'],
							$articulo['can'],
							0,
							$articulo['desc'],
							$articulo['ima'],
							$articulo['exe'],
							$articulo['ret'],
							$articulo['fam'],
							$articulo['suc'],
							$articulo['cos'],
							$articulo['p1'],
							$articulo['p2'],
							$articulo['p3'],
							$articulo['p4'],
							$articulo['p5'],
							$articulo["tipoCodigo"],
							$articulo["unidadMedia"],
							$articulo["codigoCabys"],
							$articulo["impuestoCabys"],
							$articulo['cosD'],
							$articulo['p1D'],
							$articulo['p2D'],
							$articulo['p3D'],
							$articulo['p4D'],
							$articulo['p5D'],
							$articulo['codigoDescuento'],
							$articulo['cosCD'],
							$articulo['p1CD'],
							$articulo['p2CD'],
							$articulo['p3CD'],
							$articulo['p4CD'],
							$articulo['p5CD']
						);

							$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario traspaso a inventario el articulo: ".$articulo['cod'],$data['Sucursal_Codigo'],'traspaso');
						}
						//Todo salio bien
						redirect('articulos//ingresar/masivo?s=1', 'location');
					}else{
						//Error en ciertos articulos
						//echo "Error en ciertos articulos";
						$this->load->helper(array('form'));
						$data['error'] = '5';
						$data['msj'] = 'Algunos artículos presentan problemas';
						//$data['errorCosto'] = $resultado['erroresCosto'];
						//$data['errorCantidad'] = $resultado['erroresCantidad'];
						$data['erroresCodigo'] = $resultado["erroresCodigo"];
						$data['erroresCosto'] = $resultado["erroresCosto"];
						$data['erroresPrecio1'] = $resultado["erroresPrecio1"];
						$data['erroresPrecio2'] = $resultado["erroresPrecio2"];
						$data['erroresPrecio3'] = $resultado["erroresPrecio3"];
						$data['erroresPrecio4'] = $resultado["erroresPrecio4"];
						$data['erroresPrecio5'] = $resultado["erroresPrecio5"];
						$data['erroresCantidad'] = $resultado["erroresCantidad"];
						$data['erroresFamilia'] = $resultado["erroresFamilia"];
						$data['erroresSucursal'] = $resultado["erroresSucursal"];
						$data['erroresExento'] = $resultado["erroresExento"];
						$data['erroresRetencion'] = $resultado["erroresRetencion"];
						$data['erroresDescuento'] = $resultado["erroresDescuento"];
						$data['erroresTipoCodigo'] = $resultado["erroresTipoCodigo"];
						$data['erroresUnidadMedida'] = $resultado["erroresUnidadMedida"];
						$data['erroresCodigoCabys'] = $resultado["erroresCodigoCabys"];
						$data['erroresCodigoDescuento'] = $resultado["erroresCodigoDescuento"];
						$data['javascriptCacheVersion'] = $this->javascriptCacheVersion;
						$this->load->view('articulos/ingreso_masivo_articulos_view', $data);
					}
				}else{
					if($resultado['error']=='1'){
						//echo "No se pudo leer y procesar el excel";
						$this->load->helper(array('form'));
						$data['error'] = '4';
						$data['msj'] = 'No se pudo procesar el archivo excel';
						$data['javascriptCacheVersion'] = $this->javascriptCacheVersion;
						$this->load->view('articulos/ingreso_masivo_articulos_view', $data);
					}else if($resultado['error']=='2'){
						//echo "Columnas requeridas no vienen o estan en mal formato";
						$this->load->helper(array('form'));
						$data['error'] = '3';
						$data['msj'] = 'Columnas no válidas, o no están en orden';
						$data['javascriptCacheVersion'] = $this->javascriptCacheVersion;
						$this->load->view('articulos/ingreso_masivo_articulos_view', $data);
					}
				}

		}else{
			//URL Mala
			//echo "URL mala";
			$this->load->helper(array('form'));
			$data['error'] = '1';
			$data['msj'] = 'La URL está incompleta, contacte al administrador';
			$data['javascriptCacheVersion'] = $this->javascriptCacheVersion;
			$this->load->view('articulos/ingreso_masivo_articulos_view', $data);
		}
	}

	function procesarExcel(){
		$resultado = array('status'=>'error','error'=>'1'); //Error generico de no se pudo realizar el proceso
		require './application/libraries/excel_reader2.php';
		$data = new Spreadsheet_Excel_Reader($_FILES['archivo_excel']['tmp_name']);

		$c1 = $data->val(1,1);
		$c2 = $data->val(1,2);
		$c3 = $data->val(1,3);
		$c4 = $data->val(1,4);
		$c5 = $data->val(1,5);
		$c6 = $data->val(1,6);
		$c7 = $data->val(1,7);
		$c8 = $data->val(1,8);
		$c9 = $data->val(1,9);
		$c10 = $data->val(1,10);
		$c11 = $data->val(1,11);
		$c12 = $data->val(1,12);
		$c13 = $data->val(1,13);
		$c14 = $data->val(1,14);
		$c15 = $data->val(1,15);
		$c16 = $data->val(1,16);
		$c17 = $data->val(1,17);
		$c18 = $data->val(1,18);
		$c19 = $data->val(1,19);
		$c20 = $data->val(1,20);
		$c21 = $data->val(1,21);
		$c22 = $data->val(1,22);
		$c23 = $data->val(1,23);
		$c24 = $data->val(1,24);
		$c25 = $data->val(1,25);
		$c26 = $data->val(1,26);
		$c27 = $data->val(1,27);
		$c28 = $data->val(1,28);
		$c29 = $data->val(1,29);
		$c30 = $data->val(1,30);
		$c31 = $data->val(1,31);

		if(	trim($c1) == 'CODIGO' &&
			trim($c2) == 'DESCRIPCION' &&
			trim($c3) == 'COSTO' &&
			trim($c4) == 'COSTO_DESCUENTO' &&
			trim($c5) == 'COSTO_CODIGO_DESCUENTO' &&
			trim($c6) == 'PRECIO_1' &&
			trim($c7) == 'PRECIO_1_DESCUENTO' &&
			trim($c8) == 'PRECIO_1_CODIGO_DESCUENTO' &&
			trim($c9) == 'PRECIO_2' &&
			trim($c10) == 'PRECIO_2_DESCUENTO' &&
			trim($c11) == 'PRECIO_2_CODIGO_DESCUENTO' &&
			trim($c12) == 'PRECIO_3' &&
			trim($c13) == 'PRECIO_3_DESCUENTO' &&
			trim($c14) == 'PRECIO_3_CODIGO_DESCUENTO' &&
			trim($c15) == 'PRECIO_4' &&
			trim($c16) == 'PRECIO_4_DESCUENTO' &&
			trim($c17) == 'PRECIO_4_CODIGO_DESCUENTO' &&
			trim($c18) == 'PRECIO_5' &&
			trim($c19) == 'PRECIO_5_DESCUENTO' &&
			trim($c20) == 'PRECIO_5_CODIGO_DESCUENTO' &&
			trim($c21) == 'SUCURSAL' &&
			trim($c22) == 'FAMILIA' &&
			trim($c23) == 'CANTIDAD' &&
			trim($c24) == 'EXENTO_IVA' &&
			trim($c25) == 'SIN_RETENCION' &&
			trim($c26) == 'DESCUENTO' &&
			trim($c27) == 'CODIGO_DESCUENTO' &&
			trim($c28) == 'NOMBRE_IMAGEN'&&
			trim($c29) == 'TIPO_CODIGO'&&
			trim($c30) == 'UNIDAD_MEDIDA'&&
			trim($c31) == 'CODIGO_CABYS'
			){
			$cantidadFilas = $data->rowcount($sheet_index=0);
			//Lleva el control de cuales productos presentaron errores
			$erroresCodigo = array();
			$erroresCosto = array();
			$erroresPrecio1 = array();
			$erroresPrecio2 = array();
			$erroresPrecio3 = array();
			$erroresPrecio4 = array();
			$erroresPrecio5 = array();
			$erroresCantidad = array();
			$erroresFamilia = array();
			$erroresSucursal = array();
			$erroresExento = array();
			$erroresRetencion = array();
			$erroresDescuento = array();
			$erroresTipoCodigo = array();
			$erroresUnidadMedida = array();
			$erroresCodigoCabys = array();
			$erroresCodigoDescuento = array();

			$articulos = array();

			for ($row = 2; $row <= $cantidadFilas; ++ $row)
			{
				$codigo = $data->val($row,1);
				$descripcion = $data->val($row,2);
				$costo = $data->val($row,3);
				$costoD = $data->val($row,4);
				$costoCD = $data->val($row,5);
				$p1 = $data->val($row,6);
				$p1D = $data->val($row,7);
				$p1CD = $data->val($row,8);
				$p2 = $data->val($row,9);
				$p2D = $data->val($row,10);
				$p2CD = $data->val($row,11);
				$p3 = $data->val($row,12);
				$p3D = $data->val($row,13);
				$p3CD = $data->val($row,14);
				$p4 = $data->val($row,15);
				$p4D = $data->val($row,16);
				$p4CD = $data->val($row,17);
				$p5 = $data->val($row,18);
				$p5D = $data->val($row,19);
				$p5CD = $data->val($row,20);
				$sucursal = $data->val($row,21);
				$familia = $data->val($row,22);
				$cantidad = $data->val($row,23);
				$exento = $data->val($row,24);
				$retencion = $data->val($row,25);
				$descuento = $data->val($row,26);
				$codigoDescuento = $data->val($row,27);
				$imagen = $data->val($row,28);
				$tipoCodigo = $data->val($row,29);
				$unidadMedida = $data->val($row,30);
				$codigoCabys = $data->val($row,31);

				//Revisamos si el codigo existe
				if($this->articulo->existe_Articulo($codigo,$sucursal)){
					array_push($erroresCodigo, $codigo);
				}
				//Revisamos que el costo sea numerico
				if(!is_numeric($costo)){
					array_push($erroresCosto, $codigo);
				}
				//Revisamos que el precio 2 sea numerico
				if(!is_numeric($p2)){
					array_push($erroresPrecio2, $codigo);
				}
				//Revisamos que el precio 3 sea numerico
				if(!is_numeric($p3)){
					array_push($erroresPrecio3, $codigo);
				}
				//Revisamos que el precio 3 sea numerico
				if(!is_numeric($p3)){
					array_push($erroresPrecio3, $codigo);
				}
				//Revisamos que el precio 4 sea numerico
				if(!is_numeric($p4)){
					array_push($erroresPrecio4, $codigo);
				}
				//Revisamos que el precio 5 sea numerico
				if(!is_numeric($p5)){
					array_push($erroresPrecio5, $codigo);
				}
				//Revisamos que la cantidad sea numerica y mayor a 0
				if(!is_numeric($cantidad)||$cantidad<0){
					array_push($erroresCantidad, $codigo);
				}

				//Revisamos que la sucursal exista
				if(!$this->empresa->getEmpresa($sucursal)){
					array_push($erroresSucursal, $codigo);
				}
				//Revisamos que la familia exista
				if(!$this->familia->existeFamilia($familia, $sucursal)){
					array_push($erroresFamilia, $codigo);
				}
				//Revisamos que el exento sea valido
				if(trim($exento)!='0'&&trim($exento)!='1'){
					array_push($erroresExento, $codigo);
				}
				//Revisamos que la retencion sea valida
				if(trim($retencion)!='0'&&trim($retencion)!='1'){
					array_push($erroresRetencion, $codigo);
				}
				//Revisamos que el descuento sea numerico y este entre 0 y 100
				if(!is_numeric($descuento)||$descuento<0||$descuento>100){
					array_push($erroresDescuento, $codigo);
				}

				//Revisamos que el descuento sea numerico y este entre 0 y 100
				if(!is_numeric($costoD)||$costoD<0||$costoD>100){
					array_push($erroresDescuento, $codigo);
				}

				//Revisamos que el descuento sea numerico y este entre 0 y 100
				if(!is_numeric($p1D)||$p1D<0||$p1D>100){
					array_push($erroresDescuento, $codigo);
				}

				//Revisamos que el descuento sea numerico y este entre 0 y 100
				if(!is_numeric($p2D)||$p2D<0||$p2D>100){
					array_push($erroresDescuento, $codigo);
				}

				//Revisamos que el descuento sea numerico y este entre 0 y 100
				if(!is_numeric($p3D)||$p3D<0||$p3D>100){
					array_push($erroresDescuento, $codigo);
				}

				//Revisamos que el descuento sea numerico y este entre 0 y 100
				if(!is_numeric($p4D)||$p4D<0||$p4D>100){
					array_push($erroresDescuento, $codigo);
				}

				//Revisamos que el descuento sea numerico y este entre 0 y 100
				if(!is_numeric($p5D)||$p5D<0||$p5D>100){
					array_push($erroresDescuento, $codigo);
				}

				//Revisamos el tipo de codigo
				if($this->catalogo->getTipoCodigoByCodigo($tipoCodigo) == false){
					array_push($erroresTipoCodigo, $codigo);
				}

				//Revisamos el unidad de medida
				if($this->catalogo->getUnidadDeMedidaByCodigo($unidadMedida) == false){
					array_push($erroresUnidadMedida, $codigo);
				}

				//Revisamos el codigo cabys
				$impuestoCabys = 0;
				if($cabysObject = $this->catalogo->getCabysFromCodigo($codigoCabys)){
					$impuestoCabys = $cabysObject->Impuesto;
				}else{
					array_push($erroresCodigoCabys, $codigo);
				}

				//Revisamos el codigo de descuento
				$codigosDeDescuento = array($costoCD, $p1CD, $p2CD, $p3CD, $p4CD, $p5CD, $codigoDescuento);
				foreach($codigosDeDescuento as $key => $cd){
					if($this->catalogo->getTipoDescuentoByCodigo($cd) == false){
						array_push($erroresCodigoDescuento, $codigo);
					}
				}
				

				array_push($articulos, array(
						"cod"=>$codigo,
						"des"=>$descripcion,
						"cos"=>str_replace(",",".",$costo),
						"cosD"=>str_replace(",",".",$costoD),
						"cosCD"=>$costoCD,
						"p1"=>str_replace(",",".",$p1),
						"p1D"=>str_replace(",",".",$p1D),
						"p1CD"=>$p1CD,
						"p2"=>str_replace(",",".",$p2),
						"p2D"=>str_replace(",",".",$p2D),
						"p2CD"=>$p2CD,
						"p3"=>str_replace(",",".",$p3),
						"p3D"=>str_replace(",",".",$p3D),
						"p3CD"=>$p3CD,
						"p4"=>str_replace(",",".",$p4),
						"p4D"=>str_replace(",",".",$p4D),
						"p4CD"=>$p4CD,
						"p5"=>str_replace(",",".",$p5),
						"p5D"=>str_replace(",",".",$p5D),
						"p5CD"=>$p5CD,
						"fam"=>$familia,
						"suc"=>$sucursal,
						"can"=>$cantidad,
						"exe"=>$exento,
						"ret"=>$retencion,
						"desc"=>str_replace(",",".",$descuento),
						"codigoDescuento"=>$codigoDescuento,
						"ima"=>$imagen,
						"tipoCodigo"=>$tipoCodigo,
						"unidadMedia"=>$unidadMedida,
						"codigoCabys"=>$codigoCabys,
						"impuestoCabys"=>$impuestoCabys
					)
				);
			}
			$resultado["status"] = "success";
			unset($resultado["error"]);
			$resultado["articulos"] = $articulos;

			$resultado["erroresCodigo"] = $erroresCodigo;
			$resultado["erroresCosto"] = $erroresCosto;
			$resultado["erroresPrecio1"] = $erroresPrecio1;
			$resultado["erroresPrecio2"] = $erroresPrecio2;
			$resultado["erroresPrecio3"] = $erroresPrecio3;
			$resultado["erroresPrecio4"] = $erroresPrecio4;
			$resultado["erroresPrecio5"] = $erroresPrecio5;
			$resultado["erroresCantidad"] = $erroresCantidad;
			$resultado["erroresFamilia"] = $erroresFamilia;
			$resultado["erroresSucursal"] = $erroresSucursal;
			$resultado["erroresExento"] = $erroresExento;
			$resultado["erroresRetencion"] = $erroresRetencion;
			$resultado["erroresDescuento"] = $erroresDescuento;
			$resultado["erroresTipoCodigo"] = $erroresTipoCodigo;
			$resultado["erroresUnidadMedida"] = $erroresUnidadMedida;
			$resultado["erroresCodigoCabys"] = $erroresCodigoCabys;
			$resultado["erroresCodigoDescuento"] = $erroresCodigoDescuento;
		}else{
			//No tiene las columnas requeridas
			$resultado['error'] = '2';
		}
		return $resultado;
	}

}

?>