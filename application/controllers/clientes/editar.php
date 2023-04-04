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
        $this->load->model('ubicacion','',TRUE);
 }

 function index()
 {
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion

	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	if($permisos['editar_cliente'])
	{
        $data['javascript_cache_version'] = $this->javascriptCacheVersion;
		$data['verClientesInactivos'] = @$permisos['ver_clientes_inactivos'] == true;	 

	    $this->load->view('clientes/clientes_editar_view', $data);
	}
	else{
	   redirect('accesoDenegado', 'location');
	}
 }

 function mostrar_todos_los_datos(){
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion

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
                        if($row->Cliente_Cedula!="0"&&$row->Cliente_Cedula!="1"&&$row->Cliente_Cedula!="2"){
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
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
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

	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
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

 function edicion(){
    include PATH_USER_DATA; //Esto es para traer la informacion de la sesion

    $permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

    if(!$permisos['editar_cliente']){
        redirect('accesoDenegado', 'location');
    }

    if(isset($_GET['id'])){
        $id_request=trim($_GET['id']);
        $ruta_base_imagenes_script = base_url('application/images/scripts');
        $ruta_imagen_cliente = base_url('application/images/Client_Photo/thumb');
        $nombre_cliente_edision = $this->cliente->getClientes_Cedula($id_request);

        if($result = $this->cliente->getClientes_Cedula($id_request)){
            $this->load->helper(array('form'));
            foreach($result as $row){
                $data = array_merge($data,(Array)$row);
                $data['Cliente_Imagen_URL'] = $ruta_imagen_cliente."/".$row -> Cliente_Imagen_URL;
                $data['isSucursal'] = $row -> Cliente_EsSucursal;
                $data['isExento'] = $row -> Cliente_EsExento;
                $data['aplicaRetencion'] = $row -> Aplica_Retencion;
                $data['noReceptor'] = $row -> NoReceptor;
				$data['empresaLiga'] = $row->Empresa_Liga;

                $provincias = $this->ubicacion->getProvincias();
                $data["provincias"] = $provincias;
                $cantones = $this->ubicacion->getCantones($row -> Provincia);
                $data["cantones"] = $cantones;
                $distritos = $this->ubicacion->getDistritos($row -> Provincia, $row -> Canton);
                $data["distritos"] = $distritos;
                $barrios = $this->ubicacion->getBarrios($row -> Provincia, $row -> Canton, $row->Distrito);
				$data["barrios"] = $barrios;

				$empresas_actuales = $this->empresa->get_empresas_ids_array();
            	$data['empresas'] = $empresas_actuales;

                $data['javascript_cache_version'] = $this->javascriptCacheVersion;
            }
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

                $cedula = $this->input->post('cedula_res');
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
				$sucursalLiga = $this->input->post('sucursal');

                $codptel = $this->input->post('codigo_telefono');
                $codpcel = $this->input->post('codigo_celular');
                $codpfax = $this->input->post('codigo_fax');
                $fax = $this->input->post('fax');
                $prov = $this->input->post('provincia');
                $canton = $this->input->post('canton');
                $distr = $this->input->post('distrito');
                $barrio = $this->input->post('barrio');

                //Si es sucursal
                $isSucursal = isset($_POST['issucursal']) && $_POST['issucursal']  ? "1" : "0";

                //Si es exento
                $exento = 0;
                $exento = isset($_POST['esexento']) && $_POST['esexento']  ? "1" : "0";

                //Aplica Retencion
                $aplicaRetencion = 0;
                $aplicaRetencion = isset($_POST['aplicaRetencion']) && $_POST['aplicaRetencion']  ? "1" : "0";

                //No receptor de factura electronica
                $noReceptor = 0;
                $noReceptor = isset($_POST['noReceptor']) && $_POST['noReceptor']  ? "1" : "0";


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


		$data_update['Cliente_Nombre'] = $nombre;
		$data_update['Cliente_Apellidos'] = $apellidos;
		$data_update['Fecha_Nacimiento'] = $fecha_nacimiento;
		$data_update['Cliente_Celular'] = $celular;
		$data_update['Cliente_Telefono'] = $telefono;
		$data_update['Cliente_Pais'] = $pais;
		$data_update['Cliente_Direccion'] = $direccion;
		$data_update['Cliente_Observaciones'] = $observaciones;
		$data_update['Cliente_Imagen_URL'] = $this->direccion_url_imagen;
		$data_update['Cliente_Correo_Electronico'] = $email;
		$data_update['Cliente_Estado'] = $estado_Cliente;
		$data_update['Cliente_Numero_Pago'] = $tipo_pago_cliente;
		$data_update['Cliente_EsSucursal'] = $isSucursal;
		$data_update['Cliente_EsExento'] = $exento;
		$data_update['Aplica_Retencion'] = $aplicaRetencion;
		$data_update['NoReceptor'] = $noReceptor;
		$data_update['Codigo_Pais_Telefono'] = $codptel;
		$data_update['Codigo_Pais_Celular'] = $codpcel;
		$data_update['Codigo_Pais_Fax'] = $codpfax;
		$data_update['Numero_Fax'] = $fax;
		$data_update['Provincia'] = $prov;
		$data_update['Canton'] = $canton;
		$data_update['Distrito'] = $distr;
		$data_update['Barrio'] = $barrio;
		$data_update['Empresa_Liga'] = $sucursalLiga;


		$this->cliente->actualizar($cedula, $data_update);

		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion

		$this->user->guardar_Bitacora_Cliente($cedula,
											$data['Sucursal_Codigo'],
											$data['Usuario_Codigo'],
											'Edicion_Cliente',
											'Edición Cliente : '. $nombre.' '. $apellidos .
											' Tipo Pago : '.$tipo_pago_cliente.
											' Email : '.$email.
											' Celular : '.$celular);

		$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario editó el cliente codigo: ".$tipo_Cedula,$data['Sucursal_Codigo'],'edicion');

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


	function obtenerClientesTabla(){
		include PATH_USER_DATA;
		//Un array que contiene el nombre de las columnas que se pueden ordenar
		$columnas = array(
								'0' => 'Cliente_Nombre',
								'1' => 'Cliente_Nombre',
								'2' => 'Cliente_Apellidos',
								'3' => 'Cliente_Cedula',
								'4' => 'Cliente_Estado'
								);

		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		if($permisos['editar_cliente'])

		$soloActivos = !(@$permisos['ver_clientes_inactivos'] == true && @$_POST['clientes_inactivos'] === 'true');

		$query = $this->cliente->obtenerClientesParaTabla($columnas[$_POST['order'][0]['column']], $_POST['order'][0]['dir'], $_POST['search']['value'], intval($_POST['start']), intval($_POST['length']), $soloActivos);

		$ruta_imagen = base_url('application/images/Icons');
		$clientesAMostrar = array();
		foreach($query->result() as $cli){
			if(trim($cli->cedula)==""){
				continue;
			}


			$estado = "";
			$opciones = "";
			if($cli->estado=="activo")
			{
				$estado = "<div class='estado_Ac'>ACTIVADO</div><br>";
			}elseif($cli->estado=="semiactivo"){
				$estado = "<div class='estado_Se'>SEMIACTIVO</div><br>";
			}else
			{
				$estado = "<div class='estado_De'>DESACTIVADO</div><br>";
			}
            if($cli->cedula!="0"&&$cli->cedula!="1"&&$cli->cedula!="2"){
                $opciones =
                "</td>
				<td >
					<div class='tab_opciones'>
						<a href='".base_url('')."clientes/editar/edicion?id=".$cli->cedula."' ><img src=".$ruta_imagen."/editar.png width='21' height='21' title='Editar'></a>";

						if($cli->estado=="activo"){
							$opciones .= "<a href='javascript:;' onclick='goDesactivar(".$cli->cedula.")'><img src=".$ruta_imagen."/eliminar.png width='17' height='17' title='Desactivar'></a>";
						}else{
							$opciones .= "<a href='javascript:;' onclick='goActivar(".$cli->cedula.")'><img src=".$ruta_imagen."/activar.png width='21' height='21' title='Activar'></a>";
						}
						
						
						$opciones .= "</div>
				</td>";
			}else{
				$opciones = "</td><td></td>";
			}


			$auxArray = array(
				$cli->cedula!=0&&$cli->cedula!=1 ? "<input class='checkbox'  type='checkbox' name='checkbox' value='".$cli->cedula."'>" : "",
				$cli->nombre,
				$cli->apellidos,
				$cli->cedula,
				$estado,
				$opciones
			);
			array_push($clientesAMostrar, $auxArray);
		}

		$filtrados = $this->cliente->obtenerClientesFiltradosParaTabla($columnas[$_POST['order'][0]['column']], $_POST['order'][0]['dir'], $_POST['search']['value'], intval($_POST['start']), intval($_POST['length']), $soloActivos);

		$retorno = array(
					'draw' => $_POST['draw'],
					'recordsTotal' => $this->cliente->getTotalClientes($soloActivos),
					'recordsFiltered' => $filtrados -> num_rows(),
					'data' => $clientesAMostrar
				);
		echo json_encode($retorno);
	}




 }// FIN DE LA CLASE


?>