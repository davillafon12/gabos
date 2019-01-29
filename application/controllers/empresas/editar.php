<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class editar extends CI_Controller {

 function __construct()
 {
    parent::__construct(); 
	$this->load->model('user','',TRUE);
	$this->load->model('empresa','',TRUE);
        $this->load->model('ubicacion','',TRUE);
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	
	if(!$permisos['entrar_empresa'])
	{	
	    redirect('accesoDenegado', 'location');	
	}
 }

 function index()
 {
	/*include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	
	if($permisos['editar_empresa'])
	{	
	    $this->load->view('empresas/editar_view_empresas', $data);	
	}
	else{
	   redirect('accesoDenegado', 'location');
	}	*/
	 include PATH_USER_DATA;
         $data['javascript_cache_version'] = $this->javascriptCacheVersion;
	 $this->load->view('empresas/editar_view_empresas', $data);	
 }
 
 function getMainTable()
 {	
	$ruta_imagen = base_url('application/images/Icons');
	if($result = $this->empresa->getEmpresas())
	{
		//echo "<div class='busqueda'><label for='input-filter'>Filtrar:</label><input type='search' id='input-filter' size='15' ></div>";
	    //echo "<div class='tablaP2rincipal'>";		
	    echo "<table id='tabla_editar' class='tablaPrincipal'>";
		echo "<thead> <tr>
						<th >
                            
                        </th>
                        <th class='Sorted_enabled'>
                            Código
                        </th>
						<th class='Sorted_enabled'>
                            Cédula Jurídica
                        </th>
                        <th class='Sorted_enabled'>
                            Nombre
                        </th>
                        <th >
                            Teléfono
                        </th>
						<th >
                            Dirección
                        </th>
						<th >
                            Observaciones
                        </th>
						<th class='Sorted_enabled'>
                            Estado
                        </th>
						<th class='Sorted_enabled'>
                            Administrador(a)
                        </th >
						<th class='Sorted_enabled'>
                            Creado Por
                        </th>
						<th >
                            Opciones
                        </th>
                    </tr></thead> <tbody>";
			foreach($result as $row)
			{
				echo "<tr class='table_row'>
						<td >
                            <input class='checkbox'  type='checkbox' name='checkbox' value='".$row->Codigo."'>
                        </td>
                        <td>
                            ".$row->Codigo."
                        </td>
						<td>
							".$row->Sucursal_Cedula."
						</td>
                        <td >
                            ".$row->Sucursal_Nombre."
                        </td>
                        <td>
                            ".$row->Sucursal_Telefono."
                        </td>
						<td>
                            ".$row->Sucursal_Direccion."
                        </td>
						<td class='Tab_Observaciones'>
                            ".$row->Sucursal_Observaciones."
                        </td>
						<td class='tab_fecha'>";
                        If($row->Sucursal_Estado)
						{
							echo "<div class='estado_Ac'>ACTIVADO</div><br>
								  Ingreso = ".$row->Sucursal_Fecha_Ingreso; 
						}
						else
						{
							echo "<div class='estado_De'>DESACTIVADO</div><br>
								  Ingreso = ".$row->Sucursal_Fecha_Ingreso."<br>
								  Salida = ".$row->Sucursal_Fecha_Desactivacion;
						}
                        echo "</td >
						<td class='tab_fecha'>
                            ".$row->Sucursal_Administrador."
                        </td>
						<td>
                            ".$row->Sucursal_Creador."
                        </td>
						<td >
							<div class='tab_opciones'>
								<a href='".base_url('')."empresas/editar/edicion?id=".$row->Codigo."' ><img src=".$ruta_imagen."/editar.png width='21' height='21' title='Editar'></a>
								<a href='javascript:;' onclick='goDesactivar(".$row->Codigo.")'><img src=".$ruta_imagen."/eliminar.png width='17' height='17' title='Desactivar'></a>
								<a href='javascript:;' onclick='goActivar(".$row->Codigo.")'><img src=".$ruta_imagen."/activar.png width='21' height='21' title='Activar'></a>
							</div>
						</td>
                    </tr>";
			}
		echo "</tbody></table>";
		//echo "</div>";
		echo "<div class='div_bot_des'>
					<a href='javascript:;' onClick='resetCheckBox()' class='boton_desall'>Deseleccionar Todo</a>
					<a href='javascript:;' onClick='desAllChecked()' class='boton_des_all' >Desactivar</a>
					<a href='javascript:;' onClick='actAllChecked()' class='boton_act_all' >Activar</a>
					<a href='".base_url('')."empresas/registrar' class='boton_agregar'>Agregar Empresa</a>
			  </div>";
	}
 }//FIN DE GETTABLE
 
 function desactivar()
 {
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
	
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	
	if(!$permisos['desactivar_empresa'])
	{	
	    redirect('accesoDenegado', 'location');	
	}
	
	date_default_timezone_set("America/Costa_Rica");
	$Current_datetime = date("y/m/d : H:i:s", now());
	$empresas=$_GET['array'];
	$empresas=explode(',', $empresas);
	$data_update['Sucursal_Fecha_Desactivacion'] = $Current_datetime;
	$data_update['Sucursal_Estado'] = 0;
	foreach($empresas as $empresa_id)
	{
		if($this->empresa->isActivated($empresa_id))
		{ 
			$this->empresa->actualizar($empresa_id, $data_update);
			$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario desactivo a la empresa codigo: ".$empresa_id,$data['Sucursal_Codigo'],'edicion');
		}
	}
 }
 
 function activar()
 {
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
	
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	
	if(!$permisos['activar_empresa'])
	{	
	    redirect('accesoDenegado', 'location');	
	}
	
	date_default_timezone_set("America/Costa_Rica");
	$Current_datetime = date("y/m/d : H:i:s", now());
	$empresas=$_GET['array'];
	$empresas=explode(',', $empresas);
	$data_update['Sucursal_Fecha_Ingreso'] = $Current_datetime;
	$data_update['Sucursal_Fecha_Desactivacion'] = NULL;
	$data_update['Sucursal_Estado'] = 1;
	foreach($empresas as $empresa_id)
	{
		if(!$this->empresa->isActivated($empresa_id))
		{
			$this->empresa->actualizar($empresa_id, $data_update);
			$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario activo a la empresa codigo: ".$empresa_id,$data['Sucursal_Codigo'],'edicion');
		}
	}
 }
 
 function edicion()
 {
	$id_request=$_GET['id'];
	$ruta_base_imagenes_script = base_url('application/images/scripts');
	$nombre_empresa = $this->empresa->getNombreEmpresa($id_request);
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
	$data['javascript_cache_version'] = $this->javascriptCacheVersion;
	$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
	
	if(!$permisos['editar_empresa'])
	{	
	    redirect('accesoDenegado', 'location');	
	}
	
	//echo $nombre_empresa;
	if($result = $this->empresa->getEmpresa($id_request))
	{
	    $this->load->helper(array('form'));
	    foreach($result as $row)
		{
		    $data['Empresa_codigo'] = $id_request;
				$data['Empresa_Cedula'] = $row -> Sucursal_Cedula;
				$data['Empresa_nombre'] = $row -> Sucursal_Nombre;
				$data['Empresa_Telefono'] = $row -> Sucursal_Telefono;
				$data['Empresa_Fax'] = $row -> Sucursal_Fax;
				$data['Empresa_Email'] = $row -> Sucursal_Email;
				$data['Empresa_Direccion'] = $row -> Sucursal_Direccion;
				$data['Empresa_Administrador']=$row-> Sucursal_Administrador;
				$data['Empresa_Tributacion']=$row-> Sucursal_leyenda_tributacion;
				$data['Empresa_Observaciones'] = $row -> Sucursal_Observaciones;
                                
                                $data['User_Tributa'] = $row -> Usuario_Tributa;
                                $data['Pass_Tributa'] = $row -> Pass_Tributa;
                                $data['Pin_Tributa'] = $row -> Pass_Certificado_Tributa;
                                $data['Ambiente_Tributa'] = $row -> Ambiente_Tributa;
                                $data['Token_Tributa'] = $row -> Token_Certificado_Tributa;
                                
                                $data['tipo_cedula'] = $row -> Tipo_Cedula;
                                $data['cod_telefono'] = $row -> Codigo_Pais_Telefono;
                                $data['cod_fax'] = $row -> Codigo_Pais_Fax;
                                $data['Provincia'] = $row -> Provincia;
                                $data['Canton'] = $row -> Canton;
                                $data['Distrito'] = $row -> Distrito;
                                $data['Barrio'] = $row -> Barrio;
                                
                                $data['tiposIdentificacion'] = $this->tiposIdentificacion;
                                $provincias = $this->ubicacion->getProvincias();
                                $data["provincias"] = $provincias;
                                $cantones = $this->ubicacion->getCantones($row -> Provincia);
                                $data["cantones"] = $cantones;
                                $distritos = $this->ubicacion->getDistritos($row -> Provincia, $row -> Canton);
                                $data["distritos"] = $distritos;
                                $barrios = $this->ubicacion->getBarrios($row -> Provincia, $row -> Canton, $row->Distrito);
                                $data["barrios"] = $barrios;
				
				$ligaCliente = $this->empresa->getClienteLigaByEmpresa($id_request);
				
				if($ligaCliente){
					$data['Empresa_Cliente_Nombre'] = $ligaCliente->informacion['nombre'];
					$data['Empresa_Cliente_Id'] = $ligaCliente->Cliente;
				}else{
					$data['Empresa_Cliente_Nombre'] = "";
					$data['Empresa_Cliente_Id'] = "";
				}
		}
		$this->load->view('empresas/edicion_view_empresas', $data);
	}
	else
	{
		$data['Titulo_Pagina'] = "Transacción Fallida";
		$data['Mensaje_Push'] = "<div class='sub_div'><p class='titles'>Hubo un error al actualizar la empresa ".$nombre_empresa."! <img src=".$ruta_base_imagenes_script."/error.gif /></p></div><br>
		                         <div class='Informacion'>								 
					             <form action=".base_url('empresas/editar').">
									 <input class='buttom' tabindex='2' value='Volver' type='submit' >
				                 </form>								 
								 </div>";
		$this->load->view('empresas/view_informacion_guardado', $data);
	}
 }
 
 function actualizarEmpresa()
 {
	$id_empresa = $this->input->post('codigo');
	$cedula_empresa = $this->input->post('cedula_ju');
	$nombre_empresa = $this->input->post('name');
	$telefono_empresa = $this->input->post('telefono');
	$fax_empresa = $this->input->post('fax');
	$email_empresa = $this->input->post('email');
	$direccion_empresa = $this->input->post('direccion');
	$administrador_empresa = $this->input->post('administrador');
	$observaciones_empresa = $this->input->post('observaciones');
	$leyenda_tributacion = $this->input->post('leyenda');
        
        $user_tributa = trim($this->input->post("user_tributa"));
        $pass_tributa = trim($this->input->post("pass_tributa"));
        $ambiente_tributa = trim($this->input->post("ambiente_tributa"));
        $pin_tributa = trim($this->input->post("pin_tributa"));
	
	$liga_cliente_nombre = trim($this->input->post("cliente_asociado"));
	$liga_cliente = $liga_cliente_nombre != "" ? trim($this->input->post('cliente_liga_id')) : "";
	
        $tipo_identificacion = $this->input->post('tipo_identificacion');
        $cod_telefono_empresa = $this->input->post('cod_tel');
	$cod_fax_empresa = $this->input->post('cod_fax');
        $provincia = trim($this->input->post("provincia"));
        $canton = trim($this->input->post("canton"));
        $distrito = trim($this->input->post("distrito"));
        $barrio = trim($this->input->post("barrio"));
		
	$data_update['Sucursal_Cedula'] = mysql_real_escape_string($cedula_empresa);
	$data_update['Sucursal_Nombre'] = mysql_real_escape_string($nombre_empresa);
	$data_update['Sucursal_Telefono'] = mysql_real_escape_string($telefono_empresa);
	$data_update['Sucursal_Fax'] = mysql_real_escape_string($fax_empresa);
	$data_update['Sucursal_Email'] = mysql_real_escape_string($email_empresa);
	$data_update['Sucursal_Direccion'] = mysql_real_escape_string($direccion_empresa);
	$data_update['Sucursal_Administrador'] = mysql_real_escape_string($administrador_empresa);
	$data_update['Sucursal_Observaciones'] = mysql_real_escape_string($observaciones_empresa);
	$data_update['Sucursal_leyenda_tributacion'] = mysql_real_escape_string($leyenda_tributacion);
        
        $data_update['Usuario_Tributa'] = mysql_real_escape_string($user_tributa);
        $data_update['Pass_Tributa'] = mysql_real_escape_string($pass_tributa);
        $data_update['Ambiente_Tributa'] = mysql_real_escape_string($ambiente_tributa);
        $data_update['Pass_Certificado_Tributa'] = mysql_real_escape_string($pin_tributa);
        
        $data_update['Provincia'] = mysql_real_escape_string($provincia);
        $data_update['Canton'] = mysql_real_escape_string($canton);
        $data_update['Distrito'] = mysql_real_escape_string($distrito);
        $data_update['Barrio'] = mysql_real_escape_string($barrio);
        $data_update['Tipo_Cedula'] = mysql_real_escape_string($tipo_identificacion);
        $data_update['Codigo_Pais_Telefono'] = mysql_real_escape_string($cod_telefono_empresa);
        $data_update['Codigo_Pais_Fax'] = mysql_real_escape_string($cod_fax_empresa);
	
	$this->empresa->actualizar(mysql_real_escape_string($id_empresa), $data_update);
	
	
	$this->empresa->actualizarLigaEmpresaCliente($id_empresa, $liga_cliente);
	
	
	include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
	$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario editó la empresa codigo: ".mysql_real_escape_string($id_empresa),$data['Sucursal_Codigo'],'edicion');
	
	redirect('empresas/editar', 'location');
 }
 
 
    public function cargarCertificado(){
        $id_empresa = $this->input->post('codigo');
        if($empresa = $this->empresa->getEmpresa($id_empresa)){
            $empresa = $empresa[0];
            if(isset($_FILES["certificado_hacienda_file"])){
                $cert_file = $_FILES["certificado_hacienda_file"];
                if(is_array($cert_file)){
                    if($cert_file["type"] == "application/x-pkcs12"){
                        $oldLocation = $cert_file["tmp_name"];
                        $name = md5("sucursal_".$id_empresa."_token_certificate");
                        $this->empresa->storeFile($name.".p12", "cer", $oldLocation);
                        $params = array("Token_Certificado_Tributa" => $name);
                        $this->empresa->actualizar($id_empresa, $params);
                        $this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario subió el certificado con token : ".mysql_real_escape_string($name),$data['Sucursal_Codigo'],'edicion');
                        redirect('empresas/editar/edicion?id='.$id_empresa, 'location');
                    }else{
                        // No tiene el formato adecuado de .p12
                        exit("Debe seleccionar un certificado a subir - ERR: 4");
                    }
                }else{
                    // No es un array
                    exit("Debe seleccionar un certificado a subir - ERR: 3");
                }
            }else{
                // No viene el archivo
                exit("Debe seleccionar un certificado a subir - ERR: 2");
            }
        }else{
            exit("Empresa ingresada no existe - ERR: 1");
        }
    }
	
}// FIN DE LA CLASE


?>