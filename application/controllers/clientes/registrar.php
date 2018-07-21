<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
class registrar extends CI_Controller {


	private $mensaje; 
	private $ancho_Imagen= 100;
	private $alto_Imagen=100;
	private $direccion_Url_Imagen = " ";
	private $calidad_Cliente = 5; 
	private $isSucursal = 0;


	 function __construct()
	 {
	    parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('cliente','',TRUE);
		$this->load->model('configuracion','',TRUE);
                $this->load->model('ubicacion','',TRUE);
		/*$conf_array = $this->XMLParser->getConfigArray();
		$this->monto_minimo_defecto = $conf_array['monto_minimo_compra'];
		$this->monto_maximo_defecto = $conf_array['monto_minimo_venta'];*/
	 }

	function index(){
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		
		if($permisos['registrar_cliente'])
		{
                    $provincias = $this->ubicacion->getProvincias();
                    $this->load->helper(array('form'));
                    $data["provincias"] = $provincias;
                    $data['javascript_cache_version'] = $this->javascriptCacheVersion;
                    $this->load->view('clientes/clientes_registrar_view', $data);	
		}
		else{
                    redirect('accesoDenegado', 'location');
		}
	}
        
	function es_Cedula_Utilizada(){
            $id_request=$_GET['id'];
            $ruta_base_imagenes_script = base_url('application/images/scripts');
            if($this->cliente->existe_Cliente($id_request)){
                echo "true"; //echo "<img src=".$ruta_base_imagenes_script."/error.gif />";
            }else{
                echo "false"; //echo "<img src=".$ruta_base_imagenes_script."/tick.gif />";
            }
	}
         
    function registrarClientes(){
        $tipo_Cedula = $this->input->post('tipo_Cedula');
        $cedula = $this->input->post('cedula');
        $estado_Cliente = $this->input->post('estado_Cliente');
        $nombre = $this->input->post('nombre');
        $apellidos = $this->input->post('apellidos');
        $fecha_nacimiento = $this->input->post('fecha_nacimiento');
        $celular = $this->input->post('celular');
        $telefono = $this->input->post('telefono');
        $pais = $this->input->post('pais');
        $direccion = $this->input->post('direccion');
        $email = $this->input->post('email');
        $observaciones = $this->input->post('observaciones');
        $tipo_pago_cliente = $this->input->post('tipo_pago_cliente');
        
        $codptel = $this->input->post('codigo_telefono');
        $codpcel = $this->input->post('codigo_celular');
        $codpfax = $this->input->post('codigo_fax');
        $fax = $this->input->post('fax');
        $prov = $this->input->post('provincia');
        $canton = $this->input->post('canton');
        $distr = $this->input->post('distrito');
        $barrio = $this->input->post('barrio');

        //Si es sucursal
        $this->isSucursal = isset($_POST['issucursal']) && $_POST['issucursal']  ? "1" : "0";

        //Si es exento
        $exento = 0;
        $exento = isset($_POST['esexento']) && $_POST['esexento']  ? "1" : "0";

        //Aplica Retencion
        $aplicaRetencion = 0;
        $aplicaRetencion = isset($_POST['aplicaRetencion']) && $_POST['aplicaRetencion']  ? "1" : "0";
        
        //No receptor de factura electronica
        $noReceptor = 0;
        $noReceptor = isset($_POST['noReceptor']) && $_POST['noReceptor']  ? "1" : "0";

        $this->do_upload($cedula); // metodo encargado de cargar la imagen con la cedula del usuario

        include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
        $ruta_base_imagenes_script = base_url('application/images/scripts');
        if($this->cliente->registrar(   $nombre, 
                                        $apellidos , 
                                        $cedula, 
                                        $tipo_Cedula, 
                                        $fecha_nacimiento, 
                                        $celular, 
                                        $telefono, 
                                        $pais, 
                                        $direccion, 
                                        $observaciones, 
                                        $this->direccion_url_imagen, 
                                        $email, 
                                        $estado_Cliente, 
                                        $this->calidad_Cliente, 
                                        $tipo_pago_cliente, 
                                        $this->isSucursal, 
                                        $exento, 
                                        $aplicaRetencion, 
                                        $data['Usuario_Codigo'],
                                        $data['Sucursal_Codigo'],
                                        $codptel, 
                                        $codpcel, 
                                        $codpfax,   
                                        $fax, 
                                        $prov, 
                                        $canton, 
                                        $distr, 
                                        $barrio,
                                        $noReceptor)){ //Si se ingreso bien a la BD
                //Titulo de la pagina
                $data['Titulo_Pagina'] = "Transacción Exitosa";

                $this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ingreso el cliente ".mysql_real_escape_string($nombre)." codigo: ".mysql_real_escape_string($cedula),$data['Sucursal_Codigo'],'registro');
            $data['Mensaje_Push'] = "<div class='sub_div'><p class='titles'>El ingreso del cliente ".$nombre." fue exitoso! <img src=".$ruta_base_imagenes_script."/tick.gif /></p></div><br>
                                         <div class='Informacion'>
                                                     <form action=".base_url('clientes/registrar').">

                                                                 <p class='titles'>Datos del cliente:</p><br><hr>
                                                                 <img src=".base_url('application/images/Client_Photo/thumb/'.$this->direccion_url_imagen)." alt=\"Smiley face\" height=\"100\" width=\"100\"><br>
                                                                 <p class='titles'>-Nombre:</p> <p class='content'>".$nombre." ".$apellidos.".</p><br>
                                                                 <p class='titles'>-Cédula:</p> <p class='content'>".$cedula.".</p><br>
                                                                 <p class='titles'>-Tipo Cédula:</p> <p class='content'>".$tipo_Cedula.".</p><br>
                                                                 <p class='titles'>-Fecha de Nacimiento:</p> <p class='content'>".$fecha_nacimiento.".</p><br>
                                                                 <p class='titles'>-Celular:</p> <p class='content'>".$celular.".</p><br>
                                                                 <p class='titles'>-Telefono:</p> <p class='content'>".$telefono.".</p><br>
                                                                 <p class='titles'>-País:</p> <p class='content'>".$pais.".</p><br>
                                                                 <p class='titles'>-Dirección:</p> <p class='content'>".$direccion.".</p><br>
                                                                 <p class='titles'>-Email:</p> <p class='content'>".$email.".</p><br>
                                                                 <p class='titles'>-Tipo Pago:</p> <p class='content'>".$tipo_pago_cliente.".</p><br>
                                                                 <p class='titles'>-Estado:</p> <p class='content'>".$estado_Cliente.".</p><br>								
                                                                 <p class='titles'>-Observaciones:</h3> </p><br><p class='content_ob'>
                                                                 ".$observaciones.".</p>
                                                                 <input class='buttom' tabindex='4' value='Registrar otro cliente' type='submit'>
                                                                 <a href='".base_url('home')."' class='boton_volver' style='  top: 15px;  left: 80px;  padding: 4px 10px 4px;'>Volver</a>
                                                 </form>
                                                                 </div>";
                $this->load->view('clientes/view_informacion_guardado', $data);

        }else{ //Hubo un error  no se ingreso a la BD
                $data['Titulo_Pagina'] = "Transacción Fallida";
                $data['Mensaje_Push'] = "<div class='sub_div'><p class='titles'>Hubo un error al ingresar el cliente ".$nombre."! <img src=".$ruta_base_imagenes_script."/error.gif /></p></div><br>
                                         <div class='Informacion'>								 
                                                     <form action=".base_url('clientes/registrar').">
                                                                         <input class='buttom' tabindex='2' value='Registrar otro cliente' type='submit' >
                                                 </form>
                                                                 </div>";
                $this->load->view('clientes/view_informacion_guardado', $data);
        }
    }

    function do_upload($cedula)
    {

       //especificamos a donde se va almacenar nuestra imagen
        $config['upload_path'] = 'application/images/Client_Photo';
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
        	//$this->redimencionarImagen(base_url("application/images/Client_Photo"),"Default.png");
            //almacenamos el error que existe
            //$this->mensaje = "Hubo un error subiendo la imagen :". $this->upload->display_errors();
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
        	//$this->mensaje ="Imagen subida correctamente"; 
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
		$config['width']	 = $this->ancho_Imagen;
		$config['height']	= $this->alto_Imagen;
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
		$data["contenedor"] = "";
		if($permisos['registrar_usuarios_masivo'])
		{
		$this->load->helper(array('form'));
		$this->load->view('clientes/registro_masivo_clientes', $data);	
		}
		else{
		   redirect('accesoDenegado', 'location');
		}
    }

    function carga_excel(){
		if(isset($_POST["submit"])){
			 $limite = 4194304;
			 $nombre_archivo = "cargar.xlsx";
			 $tipo_archivo = $_FILES['file']["type"];
			 $tamano_archivo = $_FILES['file']["size"];

			 if($tamano_archivo<=$limite){
				if(move_uploaded_file($_FILES['file']["tmp_name"], "application/upload/".$nombre_archivo)){
					include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
					$data["contenedor"] = $this->leer_excel();
					$this->load->helper(array('form'));
					$this->load->view('clientes/registro_masivo_clientes', $data);	
				}
				else{
					echo "No se ha podido transferir el archivo, verifique el tamaño del archivo e intente nuevamente.";
				}
		 	}
		}
    }

    function leer_excel(){
    	require_once './application/libraries/PHPExcel/IOFactory.php';
    	$objPHPExcel = PHPExcel_IOFactory::load("application/upload/cargar.xlsx");
		$flag = true;
		$contenedor = array();
		$cont = 0;
		$conf_array = $this->configuracion->getConfiguracionArray();
		$monto_minimo_defecto = $conf_array['monto_minimo_compra'];
		$monto_maximo_defecto = $conf_array['monto_minimo_venta'];
		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
				if($flag){
					$worksheetTitle     = $worksheet->getTitle();
					$highestRow         = $worksheet->getHighestRow(); // e.g. 10
					$highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
					$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
					$nrColumns = ord($highestColumn) - 64;
					//echo "<br>Pagina ".$worksheetTitle." Tiene  ".$nrColumns . ' Columnas (A-' . $highestColumn . ') ';
					//echo ' y  ' . $highestRow . ' Filas.';	
					
					for ($row = 1; $row <= $highestRow; ++ $row) {  // numero de filas      
							
						    $cell = $worksheet->getCellByColumnAndRow(0, $row);
						    $tipo_Cedula = $cell->getValue();
							//echo '<td>' . $tipo_Cedula . '</td>';
							$cell = $worksheet->getCellByColumnAndRow(1, $row);
							$cedula = $cell->getValue();
							//--echo '<td>' . $cedula . '</td>';
							$contenedor[$cont][0]  = $cedula;
							$cell = $worksheet->getCellByColumnAndRow(2, $row);
							$estado_Cliente = $cell->getValue();
							//--echo '<td>' . $estado_Cliente . '</td>';
							$contenedor[$cont][1]  = $estado_Cliente;
							$cell = $worksheet->getCellByColumnAndRow(3, $row);
							$nombre = $cell->getValue();
							//--echo '<td>' . $nombre . '</td>';
							$contenedor[$cont][2]  = $nombre;
							$cell = $worksheet->getCellByColumnAndRow(4, $row);
							$apellidos =$cell->getValue();
							//--echo '<td>' . $apellidos . '</td>';
							$contenedor[$cont][3]  = $apellidos;
							$cell = $worksheet->getCellByColumnAndRow(5, $row);
							$carnet =$cell->getValue();
							//echo '<td>' . $carnet . '</td>';
							$cell = $worksheet->getCellByColumnAndRow(6, $row);
							$celular = $cell->getValue();
							//--echo '<td>' . $celular . '</td>';
							$contenedor[$cont][4]  = $celular;
							$cell = $worksheet->getCellByColumnAndRow(7, $row);
							$telefono = $cell->getValue();
							//echo '<td>' . $telefono . '</td>';
							$cell = $worksheet->getCellByColumnAndRow(8, $row);
							$email =$cell->getValue() ;
							//echo '<td>' . $email . '</td>';
							$cell = $worksheet->getCellByColumnAndRow(9, $row);
							$pais = $cell->getValue();
							//echo '<td>' . $pais . '</td>';
							$cell = $worksheet->getCellByColumnAndRow(10, $row);
							$direccion = $cell->getValue();
							//echo '<td>' . $direccion . '</td>';
							$cell = $worksheet->getCellByColumnAndRow(11, $row);
							$descuento = $cell->getValue();
							//echo '<td>' . $descuento. '</td>';
							$cell = $worksheet->getCellByColumnAndRow(12, $row);
							$observaciones = $cell->getValue();
							//echo '<td>' . $observaciones. '</td>';	
						    $cell = $worksheet->getCellByColumnAndRow(13, $row);
							$tipo_pago_cliente = $cell->getValue();	
							//echo '<td>' . $tipo_pago_cliente. '</td>';	
							//echo '<td>' . $monto_minimo_defecto. '</td>';
							//echo '<td>' . $monto_maximo_defecto. '</td>';
							if($this->cliente->registrar($nombre, $apellidos , $cedula, $tipo_Cedula, $carnet, $celular, $telefono, $pais, $direccion, $observaciones, "Default.png", $email, $monto_maximo_defecto, $monto_minimo_defecto, $estado_Cliente, $this->calidad_Cliente, $descuento, $tipo_pago_cliente)){
								$contenedor[$cont][5]  = '<td class=\'estado_Ac\'>Guardado</td>';
								//echo '<td> Agregado Correctamente</td>';
							}
							else{
								$contenedor[$cont][5]  = '<td class=\'estado_De\'>Error</td>';
								//echo '<td> Error Agregandolo</td>';
							}
							$cont++;
							
							
						
					}
					
					$flag = false; 	
				} // fin del if $flag 
		} // fin del foreach 

	return $contenedor;
    }// fin metodo leer_excel 

    public function getCantones(){
        $r["status"] = 0;
        $r["error"] = "No se pudo procesar solicitud";
        $provincia = $this->input->post('provincia');
        
        if(is_numeric($provincia)){
            $cantones = $this->ubicacion->getCantones($provincia);
            unset($r["error"]);
            $r["status"] = 1;
            $r["data"] = $cantones;
        }else{
            $r["error"] = "Pronvicia no es válida";
        }
        echo json_encode($r);
    }
    
    public function getDistritos(){
        $r["status"] = 0;
        $r["error"] = "No se pudo procesar solicitud";
        $canton = $this->input->post('canton');
        $provincia = $this->input->post('provincia');
        if(is_numeric($canton) && is_numeric($provincia)){
            $distritos = $this->ubicacion->getDistritos($provincia, $canton);
            unset($r["error"]);
            $r["status"] = 1;
            $r["data"] = $distritos;
        }else{
            $r["error"] = "Cantón no es válido";
        }
        echo json_encode($r);
    }
    
    public function getBarrios(){
        $r["status"] = 0;
        $r["error"] = "No se pudo procesar solicitud";
        $canton = $this->input->post('canton');
        $provincia = $this->input->post('provincia');
        $distrito = $this->input->post('distrito');
        if(is_numeric($canton) && is_numeric($provincia) && is_numeric($distrito)){
            $barrios = $this->ubicacion->getBarrios($provincia, $canton, $distrito);
            unset($r["error"]);
            $r["status"] = 1;
            $r["data"] = $barrios;
        }else{
            $r["error"] = "Provincia, Cantón o Distrito no es válido";
        }
        echo json_encode($r);
    }
}

?>