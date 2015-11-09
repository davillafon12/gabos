<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class recibos extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('cliente','',TRUE);
		$this->load->model('contabilidad','',TRUE);	
		$this->load->model('banco','',TRUE);
	}

	function index()
	{
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if($permisos['entrar_recibos'])
		{
			$this->load->helper(array('form'));
			$bancos = $this->banco->getBancos();
			$data['bancos'] = $bancos;
			$this->load->view('contabilidad/contabilidad_recibos_view', $data);				
		}
		else{
		   redirect('accesoDenegado', 'location');
		}
	}
	
	function getFacturasCliente(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['cedula'])){
			$cedula = $_POST['cedula']; 
			/*if(trim($cedula) == '1' || trim($cedula) == '0'){
				$retorno['error'] = '4'; //Error cliente contado y afiliado
			}else{*/
				if($clienteArray = $this->cliente->getClientes_Cedula($cedula)){
					include '/../get_session_data.php';					
					if($creditos = $this->cliente->getFacturasConSaldo($cedula, $data['Sucursal_Codigo'])){
						$creditosArray = array();
						foreach($creditos as $credito){
							$diasCredito = $credito -> Credito_Numero_Dias;
							$fechaExpedicion = strtotime($credito -> Credito_Fecha_Expedicion);
							$fechaVencimiento = strtotime("+$diasCredito days", $fechaExpedicion);
							$cred = array(
											'credito_id' => $credito -> Credito_Id,
											'factura_consecutivo' => $credito -> Credito_Factura_Consecutivo,
											'fecha_expedicion' => date('d-m-Y',$fechaExpedicion),
											'fecha_vencimiento' => date('d-m-Y',$fechaVencimiento),
											'saldo' => $credito -> Credito_Saldo_Actual
											);
							array_push($creditosArray, $cred);
						}

						foreach($clienteArray as $row){
							$cliente['nombre'] = $row-> Cliente_Nombre;
							$cliente['apellidos'] = $row-> Cliente_Apellidos;						
						}
						/// TODO SALIO BIEN
						$retorno['status'] = 'success';
						$retorno['creditos'] = $creditosArray;
						$retorno['cliente'] = $cliente;
					}else{
						$retorno['error'] = '5'; //Error no tiene facturas pendientes
					}					
				}else{
					$retorno['error'] = '3'; //Error no hay cliente
				}
			//}
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);
		
	}
	
	function saldarFacturas(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['cedula'])&&isset($_POST['saldoAPagar'])&&isset($_POST['facturas'])&&isset($_POST['tipoPago'])&&isset($_POST['comentarios'])){
			$cedula = $_POST['cedula'];
			if($this->cliente->getClientes_Cedula($cedula)){
				$saldoAPagar = $_POST['saldoAPagar'];
				if(is_numeric($saldoAPagar)){
					$tipoPago = $_POST['tipoPago']; //Obtenemos el array
					$tipoPago = json_decode($tipoPago, true);
					$tipoPago = $tipoPago[0]; //Sacamos el array con los datos
					//Verificamos que sea el tipo de pago autorizado
					if($tipoPago['tipo']=='contado'||$tipoPago['tipo']=='tarjeta'||$tipoPago['tipo']=='deposito'){
						//Estas facturas son los creditos realizados					
						$facturas = json_decode($_POST['facturas'], true);
						$comentarios = trim($_POST['comentarios']);
						if($recibos = $this->procesarFacturas($cedula, $saldoAPagar, $facturas, $tipoPago, $comentarios)){
							include '/../get_session_data.php';					
							$retorno['status'] = 'success';
							unset($retorno['error']);
							$retorno['recibos'] = $recibos;
							$retorno['sucursal']= $data['Sucursal_Codigo'];
							$retorno['servidor_impresion']= $this->configuracion->getServidorImpresion();
							$retorno['token'] =  md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");
							//La transaccion se guarda en $this->procesarFacturas
						}else{
							$retorno['error'] = '9'; //Error pagando facturas
						}
					}else{
						$retorno['error'] = '8'; //Error tipo de pago no valido
					}
				}else{
					$retorno['error'] = '7'; //Error saldo no es numerico
				}
			}else{
				$retorno['error'] = '6'; //Error no hay cliente
			}
		}else{			
			$retorno['error'] = '2'; //Error en la URL, no viene las variables
		}		
		echo json_encode($retorno);
	}
	
	private function procesarFacturas($cedula, $saldoAPagar, $facturas, $tipoPago, $comentarios){
		//Creamos un array que contendra las facturas que realmente existen
		$facturasExistentes = array();
		foreach($facturas as $factura){
			if($this->existeFacturaConSaldo($factura)){
				//Si existe la factura con credito la metemos al array
				array_push($facturasExistentes, $factura);
			}
		}
		
		$saldoALiquidar = $saldoAPagar;
		
		//Ya que tenemos las facturas existentes, las traemos en orden de menor consecutivo a mayor
		if($facturasASaldar = $this->contabilidad->getCreditosOrdenadosPorConsecutivoMenorAMayor($facturasExistentes)){
			//Hacemos un array con los recibos para enviar a JS y que este los envie a impresion
			$recibos = array();
			foreach($facturasASaldar as $facturaASaldar){
				$codigoCredito = $facturaASaldar->Credito_Id;
				$saldoActual = $facturaASaldar->Credito_Saldo_Actual;
				
				$cliente = $facturaASaldar->Credito_Cliente_Cedula;
				$sucursal = $facturaASaldar->Credito_Sucursal_Codigo;
				$vendedor = $facturaASaldar->Credito_Vendedor_Codigo;
				$factura = $facturaASaldar->Credito_Factura_Consecutivo;
				
				//DEBE AGREGARSE A UNA TABLA DE RECIBOS
				//Se agrega en cada caso en particular
				
				//DEBE DEVOLVER EL SALDO A MAX CREDITO DEL CLIENTE				
				//-> No aplica ya que cuando se cobra una factura con credito se calcula los saldos
				
				include '/../get_session_data.php';	
				
				if($saldoActual <= $saldoALiquidar){ //Si el saldo de esta factura es menor o igual al saldo enviado
					//Puedo saldar toda la factura
					$saldoALiquidar -= $saldoActual;
					$this->contabilidad->saldarFactura($codigoCredito, 0);
					
					//Agregar recibo
					$codigoRecibo = $this->contabilidad->agregarRecibo($sucursal, $codigoCredito, 0, $saldoActual, $tipoPago['tipo'], $comentarios);
					
					//Agregamos tipo de pago
					$this->guardarPago($tipoPago, $codigoRecibo, $codigoCredito);
					
					array_push($recibos, $codigoRecibo);
					
					//Guardamos transaccion
					$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario realizo el recibo de dinero: $codigoRecibo del credito: $codigoCredito",$data['Sucursal_Codigo'],'recibo');
					
				}elseif($saldoALiquidar>0){ //Si el saldo de esta factura es mayor al ingresado pero mayor a cero
					//Puedo saldar parte de la misma
					$saldoActual -= $saldoALiquidar;
					$this->contabilidad->saldarFactura($codigoCredito, $saldoActual);
					
					//Agregar recibo
					$codigoRecibo = $this->contabilidad->agregarRecibo($sucursal, $codigoCredito, $saldoActual, $saldoALiquidar, $tipoPago['tipo'], $comentarios);
					
					//Agregamos tipo de pago
					$this->guardarPago($tipoPago, $codigoRecibo, $codigoCredito);
					
					//Ya se uso todo el saldo, ponerlo en cero
					$saldoALiquidar=0;
					array_push($recibos, $codigoRecibo);
					
					//Guardamos transaccion
					$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario realizo el recibo de dinero: $codigoRecibo del credito: $codigoCredito",$data['Sucursal_Codigo'],'recibo');
				}
				
			}
			return $recibos;
		}else{
			return false;
		}
		//Si no devuelve true antes tons fallo todo
		return false;
	}
	
	private function existeFacturaConSaldo($idFactura){
		return $this->contabilidad->existeFacturaPorId($idFactura);
	}
	
	private function guardarPago($tipoPago, $recibo, $credito){
		//EL TIPO DE PAGO DE CONTADO O CREDITO SE ENVIA PARA GUARDAR EN LA TABLA DE RECIBO Y NO AQUI
		switch ($tipoPago['tipo']) {
			case 'tarjeta':
				$comision = $this->banco->getComision($tipoPago['banco']);
				$this->contabilidad->guardarPagoTarjeta($tipoPago['transaccion'], $tipoPago['banco'], $comision, $recibo, $credito);
				//$this->contabilidad->guardarPagoTarjeta($consecutivo, $sucursal, $tipoPago['transaccion'], $comision, $vendedor, $cliente, $tipoPago['banco']);
				break;
			case 'deposito':
				$this->contabilidad->guardarTipoDeposito($tipoPago['documento'], $recibo);				
				break;
		}
	}
	
}

?>