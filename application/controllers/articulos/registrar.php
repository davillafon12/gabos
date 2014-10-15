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
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	
	if(!$permisos['registrar_articulo'])
	{	
	   redirect('accesoDenegado', 'location');
	}
 }

 function index()
 {
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
	$this->load->helper(array('form'));
	$empresas_actuales = $this->empresa->get_empresas_ids_array();
	$familias_actuales = $this->familia->get_familias_ids_array(); 
	$data['Familia_Empresas'] = $empresas_actuales;
	$data['Familias'] = $familias_actuales;
	$this->load->view('articulos/articulos_registrar_view', $data);
 }
 
function es_Codigo_Utilizado()
 {
	$id_request=$_GET['id'];
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
	$ruta_base_imagenes_script = base_url('application/images/scripts');
	if($this->articulo->existe_Articulo($id_request, $data['Sucursal_Codigo']))
	{
		echo "true"; //echo "<img src=".$ruta_base_imagenes_script."/error.gif />";
	}
	else
	{
		echo "false"; //echo "<img src=".$ruta_base_imagenes_script."/tick.gif />";
	}
 } 
// Metodo encargado de registrar articulos 
 function registra_Articulo()
 {
	//$id_empresa = $this->input->post('codigo');
	$codigo_Articulo = $this->input->post('articulo_codigo');
	$descripcion_Articulo = $this->input->post('articulo_descripcion');
	$codigoBarras_articulo = $this->input->post('articulo_codigo');
	$cantidad_Articulos = $this->input->post('articulos_cantidad');
	$cantidad_Defectuosa = $this->input->post('articulos_cantidad_defectuoso');
	$descuento_Articulo = $this->input->post('descuento');
	$this->do_upload($codigo_Articulo); // aqui jala la imagen 
	$exento_articulo = $this->input->post('exento');
	$familia_articulo = $this->input->post('familia');	
	$empresa_Articulo = $this->input->post('sucursal');
	$costo_Articulo = $this->input->post('costo');
	$precio1_Articulo = $this->input->post('precio1');
	$precio2_Articulo = $this->input->post('precio2');
	$precio3_Articulo = $this->input->post('precio3');
	$precio4_Articulo = $this->input->post('precio4');
	$precio5_Articulo = $this->input->post('precio5');
	

	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
	$ruta_base_imagenes_script = base_url('application/images/scripts');
	if($this->articulo->registrar($codigo_Articulo, $descripcion_Articulo, $codigoBarras_articulo, $cantidad_Articulos, $cantidad_Defectuosa, $descuento_Articulo, $this->direccion_url_imagen, $exento_articulo,  $familia_articulo, $empresa_Articulo, $costo_Articulo, $precio1_Articulo, $precio2_Articulo, $precio3_Articulo,  $precio4_Articulo, $precio5_Articulo))
	{ //Si se ingreso bien a la BD
		//Titulo de la pagina
		$mensajeExento = "";
		if($codigoBarras_articulo){
			$mensajeExento = "Si";
		}
		else{
			$mensajeExento = "No";
		}
		$data['Titulo_Pagina'] = "Transacción Exitosa";
	
		$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ingreso el articulo ".mysql_real_escape_string($codigo_Articulo)." cantidad: ".mysql_real_escape_string($cantidad_Articulos),$data['Sucursal_Codigo'],'registro');
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
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		$data["contenedor"] = "";
		if($permisos['registrar_articulos_masivo'])
		{
		$this->load->helper(array('form'));
		$this->load->view('articulos/registro_masivo_articulos', $data);	
		}
		else{
		   redirect('accesoDenegado', 'location');
		}
    }   


    function carga_excel(){
		if(isset($_POST["submit"])){
			 $limite = 4194304;
			 $nombre_archivo = "cargarArticulos.xlsx";
			 $tipo_archivo = $_FILES['file']["type"];
			 $tamano_archivo = $_FILES['file']["size"];

			 if($tamano_archivo<=$limite){
				if(move_uploaded_file($_FILES['file']["tmp_name"], "application/upload/".$nombre_archivo)){
					include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
					$data["contenedor"] = $this->leer_excel();
					$this->load->helper(array('form'));
					$this->load->view('articulos/registro_masivo_articulos', $data);	
				}
				else{
					echo "No se ha podido transferir el archivo, verifique el tamaño del archivo e intente nuevamente.";
				}
		 	}
		}
    }    

     function leer_excel(){
    	require_once './application/libraries/PHPExcel/IOFactory.php';
    	$objPHPExcel = PHPExcel_IOFactory::load("application/upload/cargarArticulos.xlsx");
		$flag = true;
		$contenedor = array();
		$cont = 0;
		/*$conf_array = $this->XMLParser->getConfigArray();
		$monto_minimo_defecto = $conf_array['monto_minimo_compra'];
		$monto_maximo_defecto = $conf_array['monto_minimo_venta'];*/
		//echo "<table border=1>";
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
							//echo"<tr>";
						    $cell = $worksheet->getCellByColumnAndRow(0, $row);
						    $codigoBarras_articulo = $cell->getValue();
							//echo '<td>' . $codigoBarras_articulo . '</td>';
							$cell = $worksheet->getCellByColumnAndRow(1, $row);
							$codigo_Articulo = $cell->getValue();
							//echo '<td>' . $codigo_Articulo . '</td>';
							$contenedor[$cont][0]  = $codigo_Articulo;
							$cell = $worksheet->getCellByColumnAndRow(2, $row);
							$descripcion_Articulo = $cell->getValue();
							//echo '<td>' . $descripcion_Articulo . '</td>';
							$contenedor[$cont][1]  = $descripcion_Articulo;
							$cell = $worksheet->getCellByColumnAndRow(3, $row); // celda costo-articulo
							//$costo_Articulo = $cell->getValue();// celda costo-articulo
							$cell = $worksheet->getCellByColumnAndRow(4, $row); // obtenr valor precio1 articulo BORRAR
							$costo_temporal =$cell->getValue()/1.8; // obtiene valor precio1 articulo borrar
							$costo_Articulo = number_format($costo_temporal, 2, '.', '');
							//echo '<td>' . $costo_Articulo . '</td>';
							$contenedor[$cont][2]  = $costo_Articulo;
							$cell = $worksheet->getCellByColumnAndRow(4, $row);
							$precio1_Articulo =$cell->getValue();
							//echo '<td>' . $precio1_Articulo . '</td>';	
							$contenedor[$cont][3]  = $precio1_Articulo;
							$cell = $worksheet->getCellByColumnAndRow(5, $row);
							$precio2_Articulo =$cell->getValue();
							//echo '<td>' . $precio2_Articulo . '</td>';
							$contenedor[$cont][4]  = $precio2_Articulo;
							$cell = $worksheet->getCellByColumnAndRow(6, $row);
							$precio3_Articulo = $cell->getValue();
							//echo '<td>' . $precio3_Articulo . '</td>';
							$cell = $worksheet->getCellByColumnAndRow(7, $row);
							$precio4_Articulo = $cell->getValue();
							//echo '<td>' . $precio4_Articulo . '</td>';
							$cell = $worksheet->getCellByColumnAndRow(8, $row);
							$precio5_Articulo =$cell->getValue() ;
							//echo '<td>' . $precio5_Articulo . '</td>';
							$cell = $worksheet->getCellByColumnAndRow(9, $row);
							$familia_articulo = $cell->getValue();
							//echo '<td>' . $familia_articulo . '</td>';
							$cell = $worksheet->getCellByColumnAndRow(10, $row);
							$cantidad_Articulos = $cell->getValue();
							//echo '<td>' . $cantidad_Articulos . '</td>';							
							$cell = $worksheet->getCellByColumnAndRow(11, $row);
							$empresa_Articulo = $cell->getValue();
							//echo '<td>' . $empresa_Articulo . '</td>';
							$cell = $worksheet->getCellByColumnAndRow(12, $row);
							$exento_articulo = $cell->getValue();
							//echo '<td>' . $exento_articulo. '</td>';
							$cell = $worksheet->getCellByColumnAndRow(13, $row);
							$this->direccion_url_imagen = $cell->getValue();
							//echo '<td>' . $this->direccion_url_imagen. '</td>';	
							if($this->articulo->registrar($codigo_Articulo, $descripcion_Articulo, $codigoBarras_articulo, $cantidad_Articulos, 0, 0, $this->direccion_url_imagen, $exento_articulo,  $familia_articulo, $empresa_Articulo, $costo_Articulo, $precio1_Articulo, $precio2_Articulo, $precio3_Articulo,  $precio4_Articulo, $precio5_Articulo)){
								$contenedor[$cont][5]  = '<td class=\'estado_Ac\'>Guardado</td>';
							//	echo '<td> Agregado Correctamente</td>';
							}
							else{
								$contenedor[$cont][5]  = '<td class=\'estado_De\'>Error</td>';
							//	echo '<td> Error Agregandolo</td>';
							}
							$cont++;
							//echo"</tr>";
							
							
						
					}
					
					$flag = false; 	
				} // fin del if $flag 
		} // fin del foreach 
		//echo "</table>";
	return $contenedor;
    }// fin metodo leer_excel 

}

?>