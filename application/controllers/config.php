<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class config extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		include 'get_session_data.php'; //Esto es para traer la informacion de la sesion
		$this->load->model('user','',TRUE);
		$this->load->model('empresa','',TRUE);
		$this->load->model('configuracion','',TRUE);
		$this->load->model('cliente','',TRUE);
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['entrar_configuracion'])
		{
			redirect('accesoDenegado', 'location');
		}
	}

	function index()
	{
		include 'get_session_data.php';
		//Traemos el array con toda la info
		$data['Familia_Empresas'] = $this->empresa->get_empresas_ids_array();
		$data['c_array'] = $this->configuracion->getConfiguracionArray();
		$this->load->helper(array('form'));
		$this->load->view('view_configuracion', $data);
	}

	function guardar()
	{
		if(isset($_POST['email'])&&
			isset($_POST['cant_dec'])&&
			isset($_POST['compra_dolar'])&&
			isset($_POST['venta_dolar'])&&
			isset($_POST['compra_min'])&&
			isset($_POST['compra_inter'])&&
			isset($_POST['cant_ses'])&&
			isset($_POST['iva_cant'])&&
			isset($_POST['ret_cant'])&&
			isset($_POST['sucursal'])
			){
				$email = $_POST['email'];
				$decimales = $_POST['cant_dec'];
				$compra_dolar = $_POST['compra_dolar'];
				$venta_dolar = $_POST['venta_dolar'];
				$compra_minima = $_POST['compra_min'];
				$compra_intermedia = $_POST['compra_inter'];
				$tiempo_sesion = $_POST['cant_ses'];
				$por_iva = $_POST['iva_cant'];
				$por_retencion = $_POST['ret_cant'];
				$sucursal_compras = $_POST['sucursal'];
				if(is_numeric($decimales)&&
					is_numeric($compra_dolar)&&
					is_numeric($venta_dolar)&&
					is_numeric($compra_minima)&&
					is_numeric($compra_intermedia)&&
					is_numeric($tiempo_sesion)&&
					is_numeric($por_iva)&&
					is_numeric($por_retencion)&&
					is_numeric($sucursal_compras)){
					if(filter_var($email, FILTER_VALIDATE_EMAIL)){
						if($this->empresa->getEmpresa($sucursal_compras)){

							$this->configuracion->actualizarCorreoAdmin($email);
							$this->configuracion->actualizarDecimales($decimales);
							$this->configuracion->actualizarCompraDolar($compra_dolar);
							$this->configuracion->actualizarVentaDolar($venta_dolar);
							$this->configuracion->actualizarCompraMinima($compra_minima);
							$this->configuracion->actualizarCompraIntermedia($compra_intermedia);
							$this->configuracion->actualizarTiempoSesion($tiempo_sesion);
							$this->configuracion->actualizarPorcentajeIVA($por_iva);
							$this->configuracion->actualizarPorcentajeRetencion($por_retencion);
							$this->configuracion->actualizarSucursalDefectoTraspasoCompras($sucursal_compras);

							if(isset($_POST['retencion'])){
								$this->configuracion->actualizarAplicarRetencion(1);
							}else{
								$this->configuracion->actualizarAplicarRetencion(0);
							}

							redirect('config', 'refresh');
						}else{
							//Sucursal no existe
							echo "sucursal no existe";
						}
					}else{
						//Correo electronico no valido
						echo "correo electronico no valido";
					}
				}else{
					//Datos numericos no validos
					echo "Datos numericos invalidos";
				}
		}else{
			//URL MALA
			echo "URL MALA";
		}
	}

	function actualizarEstadoClientes(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		include 'get_session_data.php';
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		if($permisos['actualizar_estado'])
		{
			$fechaUltimaActualizacion = $this->configuracion->getFechaUltimaActualizacion();
			date_default_timezone_set("America/Costa_Rica");
			$fechaActual = now();
			if(trim($fechaUltimaActualizacion)==''){
				$fechaUltimaActualizacion = strtotime('01-01-1970');
			}else{
				$fechaUltimaActualizacion = strtotime($fechaUltimaActualizacion);
			}
			$mesesPasados = (int)abs(($fechaActual - $fechaUltimaActualizacion)/(60*60*24*30));

			if($mesesPasados>=1){
			//if(true){
				if($clientes = $this->cliente->getClientes()){
					$montoMinimo = $this->configuracion->getMontoMinimoCompra();
					$montoIntermedio = $this->configuracion->getMontoIntermedioCompra();
					foreach($clientes as $cliente){
						//No valorar cliente contado y afiliado
						if($cliente->Cliente_Cedula!='1'&&$cliente->Cliente_Cedula!='0'){
							$totalCliente = $this->cliente->getMontoCompradoClienteRangoTiempo($cliente->Cliente_Cedula, $fechaUltimaActualizacion, $fechaActual);

							if($montoMinimo > $totalCliente){
								//Desactivar cliente
								//echo "Desactivar ".$cliente->Cliente_Cedula."<br>";
								$this->cliente->actualizar($cliente->Cliente_Cedula, array('Cliente_Estado' => 'inactivo'));
								$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario desactivo al cliente $cliente->Cliente_Cedula",$data['Sucursal_Codigo'],'edicion');
							}elseif($montoIntermedio > $totalCliente){
								//Semiactivar cliente
								//echo "Semiactivar ".$cliente->Cliente_Cedula."<br>";
								$this->cliente->actualizar($cliente->Cliente_Cedula, array('Cliente_Estado' => 'semiactivo'));
								$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario semiactivo al cliente $cliente->Cliente_Cedula",$data['Sucursal_Codigo'],'edicion');
							}else{
								//Activar cliente
								//echo "Activar ".$cliente->Cliente_Cedula."<br>";
								$this->cliente->actualizar($cliente->Cliente_Cedula, array('Cliente_Estado' => 'activo'));
								$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario activo al cliente $cliente->Cliente_Cedula",$data['Sucursal_Codigo'],'edicion');
							}
						}
					}
					$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario actualizo el estado de los clientes",$data['Sucursal_Codigo'],'edicion');
					$this->configuracion->actualizarUltimaActualizacionEstadoClientes(date('d-m-Y H:i:s', $fechaActual));
					unset($retorno['error']);
					$retorno['status'] = 'success';
					$retorno['fecha'] = date('d-m-Y H:i:s', $fechaActual);
				}else{
					$retorno['error'] = '4'; //No se pudo cargar los clientes
				}
			}else{
				$retorno['error'] = '3'; //No ha transcurrido un mes
			}
		}else{
			$retorno['error'] = '2'; //No tiene permiso para actualizar estado
		}
		echo json_encode($retorno);
	}


	function actualizarServidorImpresion(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1';
		if(isset($_POST['ip'])&&isset($_POST['puerto'])&&isset($_POST['protocolo'])){
			$ip = trim($_POST['ip']);
			$puerto = trim($_POST['puerto']);
			$protocolo = trim($_POST['protocolo']);
			if($this->esIPValida($ip)&&is_numeric($puerto)&&($protocolo == 'http' || $protocolo == 'https')){
				include 'get_session_data.php';
				$this->configuracion->actualizarDireccionIPServidorImpresion($ip);
				$this->configuracion->actualizarPuertoServidorImpresion($puerto);
				$this->configuracion->actualizarProtocoloServidorImpresion($protocolo);
				$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario actualizo la info. del servidor de impresión",$data['Sucursal_Codigo'],'edicion');
				unset($retorno['error']);
				$retorno['status'] = 'success';
			}else{
				$retorno['error'] = '6';
			}
		}else{
			$retorno['error'] = '5';
		}
		echo json_encode($retorno);
	}

	function esIPValida($valor){
		return true;
		//Se quito la validacion pues puede que sea una ip o puede que sea un dominio
		//return preg_match( '/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $valor );
	}

}// FIN DE LA CLASE


?>