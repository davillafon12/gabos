<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class editar extends CI_Controller {


	private $flag; 
	private $nombreImagenTemp; 
 function __construct()
 {
    parent::__construct(); 
	$this->load->model('user','',TRUE);
	$this->load->model('cliente','',TRUE);
	$this->load->model('empresa','',TRUE);
	$this->load->model('familia','',TRUE);
 }

 function index()
 {
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	if($permisos['editar_cliente'])
	{	
	    $this->load->view('clientes/clientes_editar_view', $data);	
	}
	else{
	   redirect('accesoDenegado', 'location');
	}	
 }

 function mostrar_todos_los_datos(){
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	
	if($permisos['editar_cliente'])
	{	
	    $this->load->view('clientes/clientes_editar2_view', $data);	
	}
	else{
	   redirect('accesoDenegado', 'location');
	}	

 }
  function getMainTable()
 {	
 	
	$ruta_imagen = base_url('application/images/Icons');
	if($result = $this->cliente->getClientes())
	{
		//echo "<div class='busqueda'><label for='input-filter'>Filtrar:</label><input type='search' id='input-filter' size='15' ></div>";
	    //echo "<div class='tablaP2rincipal'>";		
	    echo "<table id='tabla_editar' class='tablaPrincipal'>";
		echo "<thead> <tr>
						<th >
                            
                        </th>
                        <th class='Sorted_enabled'>
                            Nombre
                        </th>
                        <th class='Sorted_enabled'>
                            Apellidos
                        </th>
                        <th class='Sorted_enabled'>
                            Cédula
                        </th>
						<th class='Sorted_enabled'>
                            Estado Cliente
                        </th>
						<th >
                            Opciones
                        </th>
                    </tr></thead> <tbody>";
			foreach($result as $row)
			{
				echo "<tr class='table_row'>

						<td >
						"; 
						 if($row->Cliente_Cedula!=0&&$row->Cliente_Cedula!=1){
                            echo"<input class='checkbox'  type='checkbox' name='checkbox' value='".$row->Cliente_Cedula."'>";
                        }
                        echo"
                        </td>
                        <td>
                            ".$row->Cliente_Nombre."
                        </td>
                        <td >
                            ".$row->Cliente_Apellidos."
                        </td>
                        <td>
                            ".$row->Cliente_Cedula."
                        </td>
						<td>"; 
						if($row->Cliente_Estado=="activo")
						{
							echo "<div class='estado_Ac'>ACTIVADO</div><br>"; 
						}elseif($row->Cliente_Estado=="semiactivo"){
							echo "<div class='estado_Se'>SEMIACTIVO</div><br>"; 
						}else
						{
							echo "<div class='estado_De'>DESACTIVADO</div><br>"; 
						}                        
                        if($row->Cliente_Cedula!=0&&$row->Cliente_Cedula!=1){
		                    echo
		                    "</td>
							<td >
								<div class='tab_opciones'>
									<a href='".base_url('')."clientes/editar/edicion?id=".$row->Cliente_Cedula."' ><img src=".$ruta_imagen."/editar.png width='21' height='21' title='Editar'></a>
									<a href='javascript:;' onclick='goDesactivar(".$row->Cliente_Cedula.")'><img src=".$ruta_imagen."/eliminar.png width='17' height='17' title='Desactivar'></a>
									<a href='javascript:;' onclick='goActivar(".$row->Cliente_Cedula.")'><img src=".$ruta_imagen."/activar.png width='21' height='21' title='Activar'></a>
								</div>
							</td>";
						}else{
							echo"</td><td></td>"; 	
						}
                    echo"</tr>";
			}
		echo "</tbody></table>";
		//echo "</div>";
		echo "<div class='div_bot_des'>
					<a href='javascript:;' onClick='resetCheckBox()' class='boton_desall'>Deseleccionar Todo</a>
					<a href='javascript:;' onClick='desAllChecked()' class='boton_des_all' >Desactivar</a>
					<a href='javascript:;' onClick='actAllChecked()' class='boton_act_all' >Activar</a>
					<a href='".base_url('')."clientes/registrar' class='boton_agregar'>Agregar Cliente</a>					
			  </div>";
	}
 }//FIN DE GETTABLE


 function getMainTable2()
 {	
 	
	$ruta_imagen = base_url('application/images/Icons');
	if($result = $this->cliente->getClientes())
	{
		//echo "<div class='busqueda'><label for='input-filter'>Filtrar:</label><input type='search' id='input-filter' size='15' ></div>";
	    //echo "<div class='tablaP2rincipal'>";		
	    echo "<table id='tabla_editar' class='tablaPrincipal'>";
		echo "<thead> <tr>
						<th >
                            
                        </th>
                        <th class='Sorted_enabled'>
                            Nombre
                        </th>
                        <th class='Sorted_enabled'>
                            Apellidos
                        </th>
                        <th class='Sorted_enabled'>
                            Cédula
                        </th>                     
                        <th class='Sorted_enabled'>
                            Carnet
                        </th>       
                        <th >
                            Celular
                        </th>
                        <th >
                            Telefono
                        </th>
                        <th class='Sorted_enabled'>
                            Fecha Ingreso
                        </th>
                        <th >
                            Dirección
                        </th>
                        <th >
                            País
                        </th>
                        <th >
                            Observaciones
                        </th>
                        <th class='Sorted_enabled'>
                            Monto Maximo
                        </th>
                        <th class='Sorted_enabled'>
                            Monto Minimo
                        </th>
						<th class='Sorted_enabled'>
                            Estado Cliente
                        </th>
						<th class='Sorted_enabled'>
                            Calidad Cliente
                        </th>
						<th class='Sorted_enabled'>
                            Descuento
                        </th>
						<th >
                            Opciones
                        </th>
                    </tr></thead> <tbody>";
			foreach($result as $row)
			{
				echo "<tr class='table_row'>

						<td >
						"; 
						 if($row->Cliente_Cedula!=0){
                            echo"<input class='checkbox'  type='checkbox' name='checkbox' value='".$row->Cliente_Cedula."'>";
                        }
                        echo"
                        <td>
                            ".$row->Cliente_Nombre."
                        </td>
                        <td >
                            ".$row->Cliente_Apellidos."
                        </td>
                        <td>
                            ".$row->Cliente_Cedula."
                        </td>
						<td>
                            ".$row->Cliente_Carnet_Numero."
                        </td>
						<td>
                            ".$row->Cliente_Celular."
                        </td>
                        <td>
                            ".$row->Cliente_Telefono."
                        </td>
                        <td>
                            ".$row->Cliente_Fecha_Ingreso."
                        </td>
                            
                        <td>
                            ".$row->Cliente_Direccion." 
                        </td>
                        <td>
                         	".$row->Cliente_Pais."
                        </td>
                        <td>
                            ".$row->Cliente_Observaciones."
                        </td>
                        <td>
                            ".$row->Cliente_Maximo_Monto_Venta."
                        </td>
                        <td>
                            ".$row->Cliente_Minimo_Compra_Mensual."
                        </td>	
						<td>"; 
						If($row->Cliente_Estado=="activo")
						{
							echo "<div class='estado_Ac'>ACTIVADO</div><br>"; 
						}
						else
						{
							echo "<div class='estado_De'>DESACTIVADO</div><br>"; 
						}                        
                        echo
                        "</td>
                        <td>
                            ".$row->Cliente_Calidad."
                        </td>
                        <td>
                            ".$row->Cliente_Descuento."
                        </td>"; 


                        if($row->Cliente_Cedula!=0){
		                    echo
		                    "
							<td>
								<div class='tab_opciones'>
									<a href='".base_url('')."clientes/editar/edicion?id=".$row->Cliente_Cedula."' ><img src=".$ruta_imagen."/editar.png width='21' height='21' title='Editar'></a>
									<a href='javascript:;' onclick='goDesactivar(".$row->Cliente_Cedula.")'><img src=".$ruta_imagen."/eliminar.png width='17' height='17' title='Desactivar'></a>
									<a href='javascript:;' onclick='goActivar(".$row->Cliente_Cedula.")'><img src=".$ruta_imagen."/activar.png width='21' height='21' title='Activar'></a>
								</div>
							</td>";
						}else{
							echo"<td></td>"; 	
						}
                    echo"</tr>"; 
			}
		echo "</tbody></table>";
		//echo "</div>";
		echo "<div class='div_bot_des'>
					<a href='javascript:;' onClick='resetCheckBox()' class='boton_desall'>Deseleccionar Todo</a>
					<a href='javascript:;' onClick='desAllChecked()' class='boton_des_all' >Desactivar Seleccionados</a>
					<a href='javascript:;' onClick='actAllChecked()' class='boton_act_all' >Activar Seleccionados</a>
					<a href='".base_url('')."clientes/registrar' class='boton_agregar'>Agregar Cliente</a>
					<a href='".base_url('')."clientes/editar' class='boton_agregar'>Mostrar Datos Minimos</a>
			  </div>";
	}
 }//FIN DE GETTABLE



 function desactivar()
 {
 	$newEstado = 'inactivo';
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
	$cliente_a_desactivar=$_GET['array'];
	$cliente_a_desactivar=explode(',', $cliente_a_desactivar);
	$data_update['Cliente_Estado'] = $newEstado;
	foreach($cliente_a_desactivar as $cliente_id)
	{
		if($this->cliente->isActivated($cliente_id))
		{ 
			$this->cliente->actualizar($cliente_id, $data_update);
			$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario desactivo al cliente cédula: ".$cliente_id,$data['Sucursal_Codigo'],'edicion');
		}
	}
 }
 
 function activar()
 {
 	
	include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
	$newEstado = 'activo';
	$cliente_a_activar=$_GET['array'];
	$cliente_a_activar=explode(',', $cliente_a_activar);
	$data_update['Cliente_Estado'] = $newEstado;
	foreach($cliente_a_activar as $cliente_id)
	{
		if(!$this->cliente->isActivated($cliente_id))
		{
			$this->cliente->actualizar($cliente_id, $data_update);
			$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario activo al cliente cédula: ".$cliente_id,$data['Sucursal_Codigo'],'edicion');
		}
	}
 }

 function edicion()
 {
			include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
	
			$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
			
			if(!$permisos['editar_cliente'])
			{	
			    redirect('accesoDenegado', 'location');	
			}
			
			if(isset($_GET['id'])){
					$id_request=trim($_GET['id']);
					$ruta_base_imagenes_script = base_url('application/images/scripts');
					$ruta_imagen_cliente = base_url('application/images/Client_Photo/thumb');
					$nombre_cliente_edision = $this->cliente->getClientes_Cedula($id_request);
					
					if($result = $this->cliente->getClientes_Cedula($id_request))
					{
					    $this->load->helper(array('form'));
					    foreach($result as $row)
						{
						    $data['Cliente_Tipo_Cedula'] = $row -> Cliente_Tipo_Cedula;
								$data['Cliente_Cedula'] = $row -> Cliente_Cedula;
								$data['Cliente_Estado'] = $row -> Cliente_Estado;
								$data['Cliente_Imagen_URL'] = $ruta_imagen_cliente."/".$row -> Cliente_Imagen_URL;
								$data['Cliente_Nombre'] = $row -> Cliente_Nombre;
								$data['Cliente_Apellidos'] = $row -> Cliente_Apellidos;
								$data['Cliente_Carnet_Numero'] = $row -> Cliente_Carnet_Numero;
								$data['Cliente_Celular'] = $row -> Cliente_Celular;
								$data['Cliente_Telefono'] = $row -> Cliente_Telefono;
								$data['Cliente_Correo_Electronico'] = $row -> Cliente_Correo_Electronico;
								$formato_Fecha = substr($row -> Cliente_Fecha_Ingreso, -20, 10);
								$data['Cliente_Fecha_Ingreso'] = $formato_Fecha;
								$data['Cliente_Pais'] = $row -> Cliente_Pais;
								$data['Cliente_Direccion'] = $row -> Cliente_Direccion;
								$data['isSucursal'] = $row -> Cliente_EsSucursal;
								$data['isExento'] = $row -> Cliente_EsExento;
								$data['aplicaRetencion'] = $row -> Aplica_Retencion;
								$descuento = $this->cliente->getClienteDescuento($row -> Cliente_Cedula, $data['Sucursal_Codigo']);
								$maxCredito = $this->cliente->getClienteMaximoCredito($row -> Cliente_Cedula, $data['Sucursal_Codigo']);
								
								$data['cliente_descuento'] = $descuento;
								$data['maxCredito'] = $maxCredito;
								
								$data['Cliente_Observaciones'] = $row -> Cliente_Observaciones;
								$data['Cliente_Numero_Pago'] = $row -> Cliente_Numero_Pago;
						}
						$familias_actuales = $this->familia->get_familias_ids_array($data['Sucursal_Codigo']); 
						$data['Familias'] = $familias_actuales;
						$this->load->view('clientes/clientes_edision_view', $data);
					}else{
						redirect("clientes/editar","location");
					}
			}else{
				redirect("clientes/editar","location");
			}
 }
 
	 function actualizarCliente()
	 {
	 	date_default_timezone_set("America/Costa_Rica");
		$Current_datetime = date("y/m/d : H:i:s", now());
	 	
	 	//$tipo_Cedula = $this->input->post('tipo_Cedula');
		$cedula = $this->input->post('cedula_res'); //Traemos la cedula de respaldo por si se cambio la cedula
		$estado_Cliente = $this->input->post('estado_Cliente');
		$nombre = $this->input->post('nombre');
		$apellidos = $this->input->post('apellidos');
		$carnet = $this->input->post('carnet');
		$celular = $this->input->post('celular');
		$telefono = $this->input->post('telefono');
		$pais = $this->input->post('pais');
		$direccion = $this->input->post('direccion');
		//$fecha_Ingreso = $this->input->post('fecha_Ingreso');
		//$monto_minimo = $this->input->post('monto_minimo');
		//$monto_maximo = $this->input->post('monto_maximo');
		$email = $this->input->post('email');
		//$descuento = $this->input->post('descuento');
		$observaciones = $this->input->post('observaciones');
		$tipo_pago_cliente = $this->input->post('tipo_pago_cliente');
		
		//Si es sucursal
		$isSucursal = isset($_POST['issucursal']) && $_POST['issucursal']  ? "1" : "0";
		
		//Si es exento
		$exento = 0;
		$exento = isset($_POST['esexento']) && $_POST['esexento']  ? "1" : "0";
		
		//Aplica Retencion
		$aplicaRetencion = 0;
		$aplicaRetencion = isset($_POST['aplicaRetencion']) && $_POST['aplicaRetencion']  ? "1" : "0";
		

		if($result = $this->cliente->obtener_Imagen_Cliente($cedula)){
			foreach($result as $row)
			{
				$temp = $row -> Cliente_Imagen_URL;
				if(!$temp == "Default.png"){
					$this->flag = true; 
					$this->nombreImagenTemp = $temp; 
				}
				else{
					$this->flag = false; 
					$this->nombreImagenTemp = $temp; 
				}
			}
		}


		$this->do_upload($cedula); // metodo encargado de cargar la imagen con la cedula del usuario


		$data_update['Cliente_Nombre'] = mysql_real_escape_string($nombre);
		$data_update['Cliente_Apellidos'] = mysql_real_escape_string($apellidos);
		$data_update['Cliente_Carnet_Numero'] = mysql_real_escape_string($carnet);
		$data_update['Cliente_Celular'] = mysql_real_escape_string($celular);
		$data_update['Cliente_Telefono'] = mysql_real_escape_string($telefono);
		//$data_update['Cliente_Fecha_Ingreso'] = mysql_real_escape_string($fecha_Ingreso);
		$data_update['Cliente_Pais'] = mysql_real_escape_string($pais);
		$data_update['Cliente_Direccion'] = mysql_real_escape_string($direccion);
		$data_update['Cliente_Observaciones'] = mysql_real_escape_string($observaciones);
		$data_update['Cliente_Imagen_URL'] = mysql_real_escape_string($this->direccion_url_imagen);
		$data_update['Cliente_Correo_Electronico'] = mysql_real_escape_string($email);
		//$data_update['Cliente_Maximo_Monto_Venta'] = mysql_real_escape_string($monto_maximo);
		//$data_update['Cliente_Minimo_Compra_Mensual'] = mysql_real_escape_string($monto_minimo);
		$data_update['Cliente_Estado'] = mysql_real_escape_string($estado_Cliente);
		//$data_update['Cliente_Descuento'] = mysql_real_escape_string($descuento);
		$data_update['Cliente_Numero_Pago'] = mysql_real_escape_string($tipo_pago_cliente);
		$data_update['Cliente_EsSucursal'] = $isSucursal; 
		$data_update['Cliente_EsExento'] = $exento;
		$data_update['Aplica_Retencion'] = $aplicaRetencion;
		
		$this->cliente->actualizar(mysql_real_escape_string($cedula), $data_update);
		
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario editó el cliente codigo: ".mysql_real_escape_string($tipo_Cedula),$data['Sucursal_Codigo'],'edicion');
		
		redirect('clientes/editar', 'location');
		
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
        $config['overwrite'] = TRUE;

        $this->load->library('upload', $config);
        //verificamos si existe errores
        //$this->upload->do_upload($field_name);
        //$field_name= $id_nombre; 

        if (!$this->upload->do_upload())
        {
        	if($this->flag){	
        		$this->direccion_url_imagen = "Default.png";
        	}
        	else{
				$this->direccion_url_imagen = $this->nombreImagenTemp;        			
        	}
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



 
 
 
	
 }// FIN DE LA CLASE


?>