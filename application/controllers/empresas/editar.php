<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class editar extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('user','',TRUE);
		$this->load->model('empresa','',TRUE);
		$this->load->model('ubicacion','',TRUE);
		$permisos = $this->user->get_permisos($this->userdata_codigo, $this->userdata_sucursal);

		if(!$permisos['entrar_empresa']){
			redirect('accesoDenegado', 'location');
		}
	}

	function index(){
		$this->load->view('empresas/editar_view_empresas', $this->userdata_wrap);
	}

	function getMainTable(){
		$ruta_imagen = base_url('application/images/Icons');
		if($result = $this->empresa->getEmpresas()){
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

	function desactivar(){
		$permisos = $this->user->get_permisos($this->userdata_codigo, $this->userdata_sucursal);

		if(!$permisos['desactivar_empresa']){
			redirect('accesoDenegado', 'location');
		}

		date_default_timezone_set("America/Costa_Rica");
		$Current_datetime = date(DB_DATETIME_FORMAT, now());
		$empresas=$_GET['array'];
		$empresas=explode(',', $empresas);
		$data_update['Sucursal_Fecha_Desactivacion'] = $Current_datetime;
		$data_update['Sucursal_Estado'] = 0;
		foreach($empresas as $empresa_id)
		{
			if($this->empresa->isActivated($empresa_id))
			{
				$this->empresa->actualizar($empresa_id, $data_update);
				$this->user->guardar_transaccion($this->userdata_codigo, "El usuario desactivo a la empresa codigo: ".$empresa_id,$this->userdata_sucursal,'edicion');
			}
		}
	}

	function activar(){
		$permisos = $this->user->get_permisos($this->userdata_codigo, $this->userdata_sucursal);

		if(!$permisos['activar_empresa'])
		{
			redirect('accesoDenegado', 'location');
		}

		date_default_timezone_set("America/Costa_Rica");
		$Current_datetime = date(DB_DATETIME_FORMAT, now());
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
				$this->user->guardar_transaccion($this->userdata_codigo, "El usuario activo a la empresa codigo: ".$empresa_id,$this->userdata_sucursal,'edicion');
			}
		}
	}

	function edicion(){
		$id_request=$_GET['id'];
		$ruta_base_imagenes_script = base_url('application/images/scripts');
		$nombre_empresa = $this->empresa->getNombreEmpresa($id_request);

		$data = $this->userdata_wrap;

		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['editar_empresa']){
			redirect('accesoDenegado', 'location');
		}

		if($result = $this->empresa->getEmpresa($id_request)){
			$this->load->helper(array('form'));
			foreach($result as $row){
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
				$data['CodigoActividad'] = $row -> CodigoActividad;
				$data['RequiereFE'] = $row -> RequiereFE == 1;

				$data['Logo'] = $row -> Logo;

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

		}else{

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

	function actualizarEmpresa(){
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

		$codigo_actividad = trim($this->input->post("codigo_actividad"));

		$requiereFE = trim($this->input->post("is_factura_electronica")) == "1" ? 1 : 0;


		$data_update['Sucursal_Cedula'] = $cedula_empresa;
		$data_update['Sucursal_Nombre'] = $nombre_empresa;
		$data_update['Sucursal_Telefono'] = $telefono_empresa;
		$data_update['Sucursal_Fax'] = $fax_empresa;
		$data_update['Sucursal_Email'] = $email_empresa;
		$data_update['Sucursal_Direccion'] = $direccion_empresa;
		$data_update['Sucursal_Administrador'] = $administrador_empresa;
		$data_update['Sucursal_Observaciones'] = $observaciones_empresa;
		$data_update['Sucursal_leyenda_tributacion'] = $leyenda_tributacion;
		$data_update['Usuario_Tributa'] = $user_tributa;
		$data_update['Pass_Tributa'] = $pass_tributa;
		$data_update['Ambiente_Tributa'] = $ambiente_tributa;
		$data_update['Pass_Certificado_Tributa'] = $pin_tributa;
		$data_update['Provincia'] = $provincia;
		$data_update['Canton'] = $canton;
		$data_update['Distrito'] = $distrito;
		$data_update['Barrio'] = $barrio;
		$data_update['Tipo_Cedula'] = $tipo_identificacion;
		$data_update['Codigo_Pais_Telefono'] = $cod_telefono_empresa;
		$data_update['Codigo_Pais_Fax'] = $cod_fax_empresa;
		$data_update['CodigoActividad'] = $codigo_actividad;
		$data_update['RequiereFE'] = $requiereFE;

		$this->empresa->actualizar($id_empresa, $data_update);
		$this->empresa->actualizarLigaEmpresaCliente($id_empresa, $liga_cliente);

		$this->user->guardar_transaccion($this->userdata_codigo, "El usuario editó la empresa codigo: ".$id_empresa,$this->userdata_sucursal,'edicion');

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
                        $this->user->guardar_transaccion($this->userdata_codigo, "El usuario subió el certificado con token : ".$name,$this->userdata_sucursal,'edicion');
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

	public function cargarLogo(){
        $id_empresa = $this->input->post('codigo');
        if($empresa = $this->empresa->getEmpresa($id_empresa)){
			$empresa = $empresa[0];
            if(isset($_FILES["nuevo_logo_file"])){
				$logoFile = $_FILES["nuevo_logo_file"];
                if(is_array($logoFile)){
                    if(strpos($logoFile["type"], 'image/') !== false){
						$oldLocation = $logoFile["tmp_name"];
						$format = str_replace("image/", "", $logoFile["type"]);
						$name = "logo_sucursal_$id_empresa.$format";
                        $this->empresa->storeFile($name, "logo", $oldLocation);
                        $params = array("Logo" => $name);
                        $this->empresa->actualizar($id_empresa, $params);
                        $this->user->guardar_transaccion($this->userdata_codigo, "El usuario subió un nuevo logo para la sucursal: ".$id_empresa,$this->userdata_sucursal,'edicion');
                        redirect('empresas/editar/edicion?id='.$id_empresa, 'location');
                    }else{
                        // No tiene el formato adecuado
                        exit("Debe seleccionar una imagen a subir - ERR: 4");
                    }
                }else{
                    // No es un array
                    exit("Debe seleccionar un logo a subir - ERR: 3");
                }
            }else{
                // No viene el archivo
                exit("Debe seleccionar un logo a subir - ERR: 2");
            }
        }else{
            exit("Empresa ingresada no existe - ERR: 1");
        }
    }

}// FIN DE LA CLASE
