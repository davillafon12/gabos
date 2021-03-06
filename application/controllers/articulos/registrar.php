<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class registrar extends CI_Controller {


	private $direccion_url_imagen = " ";

 function __construct()
 {
    parent::__construct(); 
	$this->load->model('articulo','',TRUE);
	$this->load->model('empresa','',TRUE);
	$this->load->model('familia','',TRUE);
	$this->load->model('user','',TRUE);
	$this->load->model('bodega_m','',TRUE);
	
 }

 function index()
 {
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	
	if(!$permisos['traspaso_individual_articulo'])
	{	
	   redirect('accesoDenegado', 'location');
	}
	
	$this->load->helper(array('form'));
	$empresas_actuales = $this->empresa->get_empresas_ids_array();
	$familias_actuales = $this->familia->get_familias_ids_array($data['Sucursal_Codigo']); 
	$data['Familia_Empresas'] = $empresas_actuales;
	$data['Familias'] = $familias_actuales;
	$this->load->view('articulos/articulos_registrar_view', $data);
	
 }
 
	function es_Codigo_Utilizado()
	{
		$retorno['status'] = 'error';
		if(isset($_POST['codigo'])&&isset($_POST['sucursal'])){
			include PATH_USER_DATA;
			if(!$this->articulo->existe_Articulo($_POST['codigo'], $_POST['sucursal'])){
				$retorno['status'] = 'success';
			}
		}
		echo json_encode($retorno);
		/*$id_request=$_GET['id'];
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		$ruta_base_imagenes_script = base_url('application/images/scripts');
		if($this->articulo->existe_Articulo($id_request, $data['Sucursal_Codigo']))
		{
			echo "true"; //echo "<img src=".$ruta_base_imagenes_script."/error.gif />";
		}
		else
		{
			echo "false"; //echo "<img src=".$ruta_base_imagenes_script."/tick.gif />";
		}*/
	} 
// Metodo encargado de registrar articulos 
 function registra_Articulo()
 {
	//$id_empresa = $this->input->post('codigo');
	$codigo_Articulo = $this->input->post('articulo_codigo');
	$codigoBrasil = $this->input->post('codigo_bodega');
	$descripcion_Articulo = $this->input->post('articulo_descripcion');
	$codigoBarras_articulo = $this->input->post('articulo_codigo');
	$cantidad_Articulos = $this->input->post('articulos_cantidad');
	$cantidad_Defectuosa = $this->input->post('articulos_cantidad_defectuoso');
	$descuento_Articulo = $this->input->post('descuento');
	$this->do_upload($codigo_Articulo); // aqui jala la imagen 
	$exento_articulo = $this->input->post('exento');
	$retencion = $this->input->post('retencion');
	$familia_articulo = $this->input->post('familia');	
	$empresa_Articulo = $this->input->post('sucursal');
	$costo_Articulo = $this->input->post('costo');
	$precio1_Articulo = $this->input->post('precio1');
	$precio2_Articulo = $this->input->post('precio2');
	$precio3_Articulo = $this->input->post('precio3');
	$precio4_Articulo = $this->input->post('precio4');
	$precio5_Articulo = $this->input->post('precio5');
	

	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
	$ruta_base_imagenes_script = base_url('application/images/scripts');
	if($this->articulo->registrar($codigo_Articulo, $descripcion_Articulo, $codigoBarras_articulo, $cantidad_Articulos, $cantidad_Defectuosa, $descuento_Articulo, $this->direccion_url_imagen, $exento_articulo, $retencion, $familia_articulo, $empresa_Articulo, $costo_Articulo, $precio1_Articulo, $precio2_Articulo, $precio3_Articulo,  $precio4_Articulo, $precio5_Articulo))
	{ //Si se ingreso bien a la BD
		$this->bodega_m->restarCantidadBodega($cantidad_Articulos, $codigoBrasil, $empresa_Articulo);
		
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
					             <form action=".base_url('articulos/registrar').">				                 				
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
					             <form action=".base_url('articulos/registrar').">
									 <input class='buttom' tabindex='2' value='Registrar otro articulo' type='submit' >
				                 </form>
								 </div>";
		$this->load->view('articulos/view_informacion_guardado', $data);
	}

	
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

    function registro_masivo(){
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		if($permisos['traspaso_articulos_masivo'])
		{
			$this->load->helper(array('form'));
			$empresas_actuales = $this->empresa->get_empresas_ids_array();
			$data['Familia_Empresas'] = $empresas_actuales;	
			$this->load->view('articulos/registro_masivo_articulos', $data);	
		}
		else{
		   redirect('accesoDenegado', 'location');
		}
    }   


    function carga_excel(){
		
				include PATH_USER_DATA;
				if(isset($_FILES['archivo_excel'])&&isset($_POST['sucursal'])){
					$sucursal = $_POST['sucursal'];
					if($this->empresa->getEmpresa($sucursal)){	
						$resultado = $this->procesarExcel($sucursal);
						//print_r($resultado);
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
								sizeOf($resultado["erroresCodBrasil"])==0 &&
								sizeOf($resultado["erroresCantidadMayor"])==0 ){
								$articulos = $resultado['articulos'];
								foreach($articulos as $articulo){
									
									/*
										"cod"=>$codigo,
										"des"=>$descripcion,
										"cos"=>$costo,
										"p1"=>$p1,
										"p2"=>$p2,
										"p3"=>$p3,
										"p4"=>$p4,
										"p5"=>$p5,
										"fam"=>$familia,
										"suc"=>$sucursal,
										"can"=>$cantidad,
										"exe"=>$exento,
										"ret"=>$retencion,
										"desc"=>$descuento,
										"ima"=>$imagen,
										"cbra"=>$cod_brasil
									*/
									$this->articulo->registrar($articulo['cod'], $articulo['des'], $articulo['cod'], $articulo['can'], 0, $articulo['desc'], $articulo['ima'], $articulo['exe'],  $articulo['ret'], $articulo['fam'], $articulo['suc'], $articulo['cos'], $articulo['p1'], $articulo['p2'], $articulo['p3'],  $articulo['p4'], $articulo['p5']);
									$this->bodega_m->restarCantidadBodega($articulo['can'], $articulo['cbra'], $articulo['suc']);
									$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario traspaso a inventario el articulo: ".$articulo['cod'],$data['Sucursal_Codigo'],'traspaso');
								}
								//Todo salio bien
								redirect('articulos/registrar/registro_masivo?s=1', 'location');
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
								$data['erroresCodBrasil'] = $resultado["erroresCodBrasil"];
								$data['erroresCantidadMayor'] = $resultado["erroresCantidadMayor"];
								$empresas_actuales = $this->empresa->get_empresas_ids_array();
								$data['Familia_Empresas'] = $empresas_actuales;	
								$this->load->view('articulos/registro_masivo_articulos', $data);
							}
						}else{
							if($resultado['error']=='1'){
								//echo "No se pudo leer y procesar el excel";
								$this->load->helper(array('form'));
								$data['error'] = '4';
								$data['msj'] = 'No se pudo procesar el archivo excel';
								$empresas_actuales = $this->empresa->get_empresas_ids_array();
								$data['Familia_Empresas'] = $empresas_actuales;	
								$this->load->view('articulos/registro_masivo_articulos', $data);
							}else if($resultado['error']=='2'){
								//echo "Columnas requeridas no vienen o estan en mal formato";
								$this->load->helper(array('form'));
								$data['error'] = '3';
								$data['msj'] = 'Columnas no válidas, o no están en orden';
								$empresas_actuales = $this->empresa->get_empresas_ids_array();
								$data['Familia_Empresas'] = $empresas_actuales;	
								$this->load->view('articulos/registro_masivo_articulos', $data);
							}
						}
					}else{
						//Sucursal no existe			
						$this->load->helper(array('form'));
						$data['error'] = '2';
						$data['msj'] = 'Sucursal seleccionada no existe';
						$empresas_actuales = $this->empresa->get_empresas_ids_array();
						$data['Familia_Empresas'] = $empresas_actuales;	
						$this->load->view('articulos/registro_masivo_articulos', $data);
					}			
				}else{
					//URL Mala
					//echo "URL mala";
					$this->load->helper(array('form'));
					$data['error'] = '1';
					$data['msj'] = 'La URL está incompleta, contacte al administrador';
					$empresas_actuales = $this->empresa->get_empresas_ids_array();
					$data['Familia_Empresas'] = $empresas_actuales;	
					$this->load->view('articulos/registro_masivo_articulos', $data);
				}
    }  

	function procesarExcel($sucursal){
		$resultado = array('status'=>'error','error'=>'1'); //Error generico de no se pudo realizar el proceso
		require_once './application/libraries/PHPExcel/IOFactory.php';
    	$objPHPExcel = PHPExcel_IOFactory::load($_FILES['archivo_excel']['tmp_name']);
		$cantidadHojas = 1; //Para que solo procese la primera hoja del excel
		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
			if($cantidadHojas == 1){
				$cantidadHojas++;
				//Probamos que el orden de las columnas sea el requerido
				$c1 = $worksheet->getCellByColumnAndRow(0, 1)->getValue();
				$c2 = $worksheet->getCellByColumnAndRow(1, 1)->getValue();
				$c3 = $worksheet->getCellByColumnAndRow(2, 1)->getValue();
				$c4 = $worksheet->getCellByColumnAndRow(3, 1)->getValue();
				$c5 = $worksheet->getCellByColumnAndRow(4, 1)->getValue();
				$c6 = $worksheet->getCellByColumnAndRow(5, 1)->getValue();
				$c7 = $worksheet->getCellByColumnAndRow(6, 1)->getValue();
				$c8 = $worksheet->getCellByColumnAndRow(7, 1)->getValue();
				$c9 = $worksheet->getCellByColumnAndRow(8, 1)->getValue();
				$c11 = $worksheet->getCellByColumnAndRow(9, 1)->getValue();
				$c12 = $worksheet->getCellByColumnAndRow(10, 1)->getValue();
				$c10 = $worksheet->getCellByColumnAndRow(11, 1)->getValue(); 
				$c13 = $worksheet->getCellByColumnAndRow(12, 1)->getValue();
				$c14 = $worksheet->getCellByColumnAndRow(13, 1)->getValue();
				$c15 = $worksheet->getCellByColumnAndRow(14, 1)->getValue();
				
				if(	trim($c1) == 'CODIGO' && 
					trim($c2) == 'DESCRIPCION' && 
					trim($c3) == 'COSTO' && 
					trim($c4) == 'PRECIO_1' &&
					trim($c5) == 'PRECIO_2' &&
					trim($c6) == 'PRECIO_3' &&
					trim($c7) == 'PRECIO_4' &&
					trim($c8) == 'PRECIO_5' &&
					trim($c9) == 'FAMILIA' &&	
					trim($c10) == 'SIN_RETENCION' &&					
					trim($c11) == 'CANTIDAD' &&
					trim($c12) == 'EXENTO_IVA' &&
					trim($c13) == 'DESCUENTO' &&
					trim($c14) == 'NOMBRE_IMAGEN' &&
					trim($c15) == 'COD_BRASIL' 
					){	
					
					$highestRow = $worksheet->getHighestRow();
					
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
					$erroresCodBrasil = array();
					$erroresCantidadMayor = array();
					
					$articulos = array();
					
					for ($row = 2; $row <= $highestRow; ++ $row){ 
						$codigo = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
						$descripcion = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
						$costo = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
						$p1 = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
						$p2 = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
						$p3 = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
						$p4 = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
						$p5 = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
						$familia = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
						//$sucursal = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
						//$sucursal = 0; //Siempre se pasara a Garotas Bonitas Central
						$cantidad = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
						$exento = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
						$retencion = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
						$descuento = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
						$imagen = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
						$cod_brasil = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
						
						//Revisamos si el codigo esta repetido
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
						}else{
							//Verificamos que la cantidad a traspasar no sea mayor a la de la bodega
							$cantidadBodega = $this->bodega_m->getCantidadArticulo($cod_brasil, $sucursal);
							if($cantidadBodega<$cantidad){
								array_push($erroresCantidadMayor, $codigo);
							}	
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
						//Revisamos que el articulo exista en bodega
						if(!$this->bodega_m->existeArticuloEnBodega($cod_brasil, $sucursal)){
							array_push($erroresCodBrasil, $codigo);
						}						
						
						array_push($articulos, array(	
														"cod"=>$codigo,
														"des"=>$descripcion,
														"cos"=>$costo,
														"p1"=>$p1,
														"p2"=>$p2,
														"p3"=>$p3,
														"p4"=>$p4,
														"p5"=>$p5,
														"fam"=>$familia,
														"suc"=>$sucursal,
														"can"=>$cantidad,
														"exe"=>$exento,
														"ret"=>$retencion,
														"desc"=>$descuento,
														"ima"=>$imagen,
														"cbra"=>$cod_brasil
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
					$resultado["erroresCodBrasil"] = $erroresCodBrasil;
					$resultado["erroresCantidadMayor"] = $erroresCantidadMayor;
				}else{
					//No tiene las columnas requeridas
					$resultado['error'] = '2';
				}
			}			
		}
		return $resultado;
	}

     

}

?>