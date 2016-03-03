<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class caja extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('factura','',TRUE);
		$this->load->model('cliente','',TRUE);
		$this->load->model('articulo','',TRUE);
		$this->load->model('configuracion','',TRUE);
		$this->load->model('banco','',TRUE);
		$this->load->model('empresa','',TRUE);
		$this->load->model('proforma_m','',TRUE);
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['entrar_caja'])
		{	
		   redirect('accesoDenegado', 'location');
		}
	}

	function index()
	{
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		$this->load->helper(array('form'));
		$conf_array = $this->configuracion->getConfiguracionArray();
		$data['c_array'] = $conf_array;
		$bancos = $this->banco->getBancos();
		$data['bancos'] = $bancos;
		date_default_timezone_set("America/Costa_Rica");
		$fecha = date("y/m/d : H:i:s", now());
		$data['token_factura_temp'] = md5($fecha.$data['Usuario_Codigo'].$data['Sucursal_Codigo']);
		$this->load->view('facturas/view_caja_factura', $data);	
	}
	
	function getFacturasPendientes(){
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		$request="";
		if($facturas = $this->factura->getFacturasPendientes($data['Sucursal_Codigo'])){
			//echo "<table id='facturas_pendientes' class='facturas_pendientes'>";
			foreach($facturas as $factura){
					$ClienteArray = $this->cliente->getNombreCliente($factura->TB_03_Cliente_Cliente_Cedula);
					$NombreCliente = $ClienteArray['nombre'];
					
					if($factura->Factura_Nombre_Cliente!=''){ //Si el nombre del cliente de la factura es diferente de vacio 
						$NombreCliente  = $factura->Factura_Nombre_Cliente;
					}
					
					$request = $request . "
						<tr>
							<td>
								<div class='factura_row' ondblclick='cargaFactura($factura->Factura_Consecutivo)'>
									<!--<p class='consecutivo_factura_title'>Número:</p>-->
									<p class='consecutivo_factura'># $factura->Factura_Consecutivo</p>
									<hr class='divisor_facturas_row'>
									<p class='cliente_factura_title'>Cliente:</p>
									<p class='cliente_factura'>$NombreCliente</p>
								</div>
							</td>
						</tr>";
			}
								
					 //echo " </table>";
		}else{
			echo "<td>Hubo un error al cargar las facturas</td>";
		}
		
		echo $request;
	}
	
	function getFacturaHeaders(){
		//var_dump($_POST);
		$facturaHEAD['status']='error';
		$facturaHEAD['error']='14'; //No se logro procesar la factura
		if(isset($_POST['consecutivo'])){
			$consecutivo=$_POST['consecutivo'];
			include '/../get_session_data.php'; 
			//header('Content-Type: application/json');			
			if($facturaHEADS = $this->factura->getFacturasHeaders($consecutivo, $data['Sucursal_Codigo'])){
				//var_dump($consecutivo);
				//echo "crear array";
				foreach($facturaHEADS as $row){
					if($row->Factura_Estado=='pendiente'){				
						$facturaHEAD['status']='success';				
						$facturaHEAD['cedula']=$row->TB_03_Cliente_Cliente_Cedula;
						
						$ClienteArray = $this->cliente->getNombreCliente($facturaHEAD['cedula']);
						$NombreCliente = $ClienteArray['nombre'];
						
						if($row->Factura_Nombre_Cliente!=''){ //Si el nombre del cliente de la factura es diferente de vacio 
							$NombreCliente  = $row->Factura_Nombre_Cliente;
						}
						
						$facturaHEAD['nombre']= $NombreCliente;
						$facturaHEAD['moneda']=$row->Factura_Moneda;
						$facturaHEAD['total']=$row->Factura_Monto_Total;
						$facturaHEAD['iva']=$row->Factura_Monto_IVA;
						$facturaHEAD['costo']=$row->Factura_Monto_Sin_IVA;
						$facturaHEAD['observaciones']=$row->Factura_Observaciones;
						$facturaHEAD['ivapor']=$row->Factura_porcentaje_iva;
						$facturaHEAD['cambio']=$row->Factura_tipo_cambio;
						
						//Cargamos la variable que ocupamos de cliente
						$facturaHEAD['cliente_sucursal'] = $row->Factura_Cliente_Sucursal;
						$facturaHEAD['cliente_exento'] = $row->Factura_Cliente_Exento;
						$facturaHEAD['cliente_retencion'] = $row->Factura_Cliente_No_Retencion;
						
					}else if($row->Factura_Estado=='cobrada'){
						$facturaHEAD['status']='error';
						$facturaHEAD['error']='11';
						//echo json_encode($facturaHEAD); //Factura ya cobrada
					}else if($row->Factura_Estado=='anulada'){
						$facturaHEAD['status']='error';
						$facturaHEAD['error']='12';
						//echo json_encode($facturaHEAD); //Factura anulada
					}
				}
			}else{
				$facturaHEAD['status']='error';
				$facturaHEAD['error']='10';
				//echo json_encode($facturaHEAD); //Error para no se pudo cargar los headers
			}
		}else{
			//echo "Entro a 13";
			$facturaHEAD['status']='error';
			$facturaHEAD['error']='13'; //Error de no leer encabezado del URL
			//echo json_encode($facturaHEAD);
		}
		//echo "Entro a 13";
		echo json_encode($facturaHEAD);
	}
	
	function getFacturaHeadersConsulta(){
		$facturaHEAD['status']='error';
		$facturaHEAD['error']='14'; //No se logro procesar la factura
		if(isset($_POST['consecutivo'])){
			$consecutivo=$_POST['consecutivo'];
			include '/../get_session_data.php'; 		
			if($facturaHEADS = $this->factura->getFacturasHeaders($consecutivo, $data['Sucursal_Codigo'])){
				foreach($facturaHEADS as $row){			
					$facturaHEAD['status']='success';				
					$facturaHEAD['cedula']=$row->TB_03_Cliente_Cliente_Cedula;
					
					$ClienteArray = $this->cliente->getNombreCliente($facturaHEAD['cedula']);
					$NombreCliente = $ClienteArray['nombre'];
					
					if($row->Factura_Nombre_Cliente!=''){ //Si el nombre del cliente de la factura es diferente de vacio 
						$NombreCliente  = $row->Factura_Nombre_Cliente;
					}
					
					$facturaHEAD['nombre']= $NombreCliente;
					$facturaHEAD['moneda']=$row->Factura_Moneda;
					$facturaHEAD['total']=$row->Factura_Monto_Total;
					$facturaHEAD['iva']=$row->Factura_Monto_IVA;
					$facturaHEAD['retencion']=$row->Factura_Retencion;
					$facturaHEAD['costo']=$row->Factura_Monto_Sin_IVA;
					$facturaHEAD['observaciones']=$row->Factura_Observaciones;
					$facturaHEAD['ivapor']=$row->Factura_porcentaje_iva;
					$facturaHEAD['cambio']=$row->Factura_tipo_cambio;	
					$facturaHEAD['tipo']=$row->Factura_Estado;	
					$facturaHEAD['sucursal']= $data['Sucursal_Codigo'];
					$facturaHEAD['servidor_impresion']= $this->configuracion->getServidorImpresion();
					$facturaHEAD['token'] =  md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");
				}
			}else{
				$facturaHEAD['status']='error';
				$facturaHEAD['error']='10'; // No existe factura
			}
		}else{
			$facturaHEAD['status']='error';
			$facturaHEAD['error']='13'; //Error de no leer encabezado del URL
		}
		echo json_encode($facturaHEAD);
	}
	
	function getNombreCliente($cedula){
		return $this->cliente->getNombreCliente($cedula);
	}
	
	function getArticulosFactura(){
		$facturaBODY['status']='error';
		$facturaBODY['error']='17'; //No se logro procesar ls productos
		if(isset($_POST['consecutivo'])){
			$consecutivo=$_POST['consecutivo'];
			include '/../get_session_data.php'; 
			//header('Content-Type: application/json');			
			if($facturaPRODUCTS = $this->factura->getArticulosFactura($consecutivo, $data['Sucursal_Codigo'])){
				
				
				$facturaHEADS = $this->factura->getFacturasHeaders($consecutivo, $data['Sucursal_Codigo']);
				
				
				//var_dump($consecutivo);
				//echo "crear array";
				$facturaBODY['status']='success';
				$articulos=[];
				foreach($facturaPRODUCTS as $row){
					$articulo=[]; //Limpiamos el array
					$articulo['codigo']=$row->Articulo_Factura_Codigo;
					$articulo['descripcion']=$row->Articulo_Factura_Descripcion;
					$articulo['cantidad']=$row->Articulo_Factura_Cantidad;
					$articulo['descuento']=$row->Articulo_Factura_Descuento;
					$articulo['exento']=$row->Articulo_Factura_Exento;
					$articulo['retencion']=$row->Articulo_Factura_No_Retencion;
					$articulo['precio']=$row->Articulo_Factura_Precio_Unitario;
					$articulo['precioFinal']=$row->Articulo_Factura_Precio_Final;
					//Procesamos la imagen
					$articulo['imagen'] = $row->Articulo_Factura_Imagen;				
					$ruta_a_preguntar = FCPATH.'application\\images\\articulos\\'.$articulo['imagen'];
					//return $ruta_a_preguntar;
					if(!file_exists($ruta_a_preguntar)){$articulo['imagen'] = '00.jpg';}					
					if($inventario = $this->articulo->inventarioActual($articulo['codigo'], $data['Sucursal_Codigo'])){
						$articulo['bodega']=$inventario;
					}else{
						$articulo['bodega']='0';
					}
					if($articulo['codigo']=='00'){
						$articulo['bodega']='1000';
					}
					
					
					if(trim($facturaHEADS[0]->TB_03_Cliente_Cliente_Cedula) == 2){
						// Si es cliente de inventario defectuoso se debe poner la cantidad de bodega
						$articulo['bodega'] = $this->articulo->inventarioDefectuosoActual($articulo['codigo'], $data['Sucursal_Codigo']) - $articulo['cantidad'];
					}
					
					
					array_push($articulos, $articulo);
				}
				$facturaBODY['productos']=$articulos;
			}else{
				$facturaBODY['status']='error';
				$facturaBODY['error']='15'; //Error para no se pudo cargar los productos
			}
		}else{
			$facturaBODY['status']='error';
			$facturaBODY['error']='16'; //Error de no leer encabezado del URL
		}
		echo json_encode($facturaBODY);
	}
	
	function cobrarFactura(){
	
		$facturaBODY['status']='error';
		$facturaBODY['error']='17'; //No se logro procesar ls productos
		if(isset($_POST['consecutivo'])&&isset($_POST['tipoPago'])){
			$consecutivo = $_POST['consecutivo'];
			$tipoPago = $_POST['tipoPago']; //Obtenemos el array
			$tipoPago = json_decode($tipoPago, true);
			$tipoPago = $tipoPago[0]; //Sacamos el array con los datos
			
			$recibidoParaVuelto = $_POST['entregado'];
			$vuelto = $_POST['vuelto'];
			
			
			include '/../get_session_data.php';	
			if(isset($tipoPago['tipo'])){
				if($tipoPago['tipo']=='credito'){ //Si es pago a credito
				
					//echo $this->creditoDisponible($consecutivo, $data['Sucursal_Codigo']);
					if($this->creditoDisponible($consecutivo, $data['Sucursal_Codigo'])){
						if($this->factura->existe_Factura($consecutivo, $data['Sucursal_Codigo'])){							
							$facturaBODY['status']='success';
							
							//Para efecto de impresion
							$facturaBODY['sucursal']= $data['Sucursal_Codigo'];
							$facturaBODY['servidor_impresion']= $this->configuracion->getServidorImpresion();
							$facturaBODY['token'] =  md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");
							
							date_default_timezone_set("America/Costa_Rica");
							$Current_datetime = date("y/m/d : H:i:s", now());
							$datos = array(         
								'Factura_Tipo_Pago'=>mysql_real_escape_string($tipoPago['tipo']),
								'Factura_Fecha_Hora'=>$Current_datetime, 
								'Factura_Estado'=>'cobrada',
								'Factura_Entregado_Vuelto' => $vuelto,
								'Factura_Recibido_Vuelto' => $recibidoParaVuelto
							);
							
							$this->factura->actualizarFacturaHead($datos, $consecutivo, $data['Sucursal_Codigo']);
							
							//Agregamos tipo de pago
							//Tarjeta, Deposito, Cheque, Mixto, Apartado
							$this->guardarTipoPago($tipoPago, $consecutivo, $data['Sucursal_Codigo']);
							
							
							$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario cobro la factura consecutivo: $consecutivo",$data['Sucursal_Codigo'],'cobro');
						
						
							//Valorar si factura es de cliente defectuoso
							$facturaHeader = $this->factura->getFacturasHeaders($consecutivo, $data['Sucursal_Codigo'])[0];
							if(trim($facturaHeader->TB_03_Cliente_Cliente_Cedula == 2)){
								$this->descontarArticulosDefectuosos($consecutivo, $data['Sucursal_Codigo']);
							}
						
						}else{
							$facturaBODY['status']='error';
							$facturaBODY['error']='19'; //Error no existe esa factura
						}
					}else{
						$facturaBODY['status']='error';
						$facturaBODY['error']='24'; //Error no tiene credito disponible
					}
				}else{
					if($this->factura->existe_Factura($consecutivo, $data['Sucursal_Codigo'])){							
						$facturaBODY['status']='success';
						
						//Para efecto de impresion
						$facturaBODY['sucursal']= $data['Sucursal_Codigo'];
						$facturaBODY['servidor_impresion']= $this->configuracion->getServidorImpresion();
						$facturaBODY['token'] =  md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");
						
						date_default_timezone_set("America/Costa_Rica");
						$Current_datetime = date("y/m/d : H:i:s", now());
						$datos = array(         
							'Factura_Tipo_Pago'=>mysql_real_escape_string($tipoPago['tipo']),
							'Factura_Fecha_Hora'=>$Current_datetime, 
							'Factura_Estado'=>'cobrada',
							'Factura_Entregado_Vuelto' => $vuelto,
							'Factura_Recibido_Vuelto' => $recibidoParaVuelto
						);
						
						$this->factura->actualizarFacturaHead($datos, $consecutivo, $data['Sucursal_Codigo']);
						
						//Agregamos tipo de pago
						//Tarjeta, Deposito, Cheque y Mixto
						$this->guardarTipoPago($tipoPago, $consecutivo, $data['Sucursal_Codigo']);
						
						
						$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario cobro la factura consecutivo: $consecutivo",$data['Sucursal_Codigo'],'cobro');
					
					
						//Valorar si factura es de cliente defectuoso
						$facturaHeader = $this->factura->getFacturasHeaders($consecutivo, $data['Sucursal_Codigo'])[0];
						if(trim($facturaHeader->TB_03_Cliente_Cliente_Cedula == 2)){
							$this->descontarArticulosDefectuosos($consecutivo, $data['Sucursal_Codigo']);
						}
					}else{
						$facturaBODY['status']='error';
						$facturaBODY['error']='19'; //Error no existe esa factura
					}
				}
			}else{
				$facturaBODY['status']='error';
				$facturaBODY['error']='18'; //Error de no leer encabezado del URL DE TIPO DE PAGO
			}				
		}else{
			$facturaBODY['status']='error';
			$facturaBODY['error']='16'; //Error de no leer encabezado del URL
		}
		echo json_encode($facturaBODY);
	}
	
	function creditoDisponible($consecutivo, $Sucursal){
	
		if($facturaHead = $this->factura->getFacturasHeaders($consecutivo, $Sucursal)){
			//return "aaaaa";
			foreach($facturaHead as $row){				
				$cedula = $row->TB_03_Cliente_Cliente_Cedula;
				$totalFactura = $row->Factura_Monto_Total;
			}
			if($creditos = $this->factura->getCreditosClientePorSucursal($cedula, $Sucursal)){
				$saldoTotaldeCliente = 0;
				foreach($creditos as $credito){
					//Sumamos todos los saldos del cliente
					$saldoTotaldeCliente += $credito->Credito_Saldo_Actual;
				}
				//Sumamos todos los saldos con el total de la factura a cobrar
				$saldoTotaldeCliente += $totalFactura; 
				
				//Traemos el maximo credito permitido de un cliente
				if($maximoPermitidoDeCredito = $this->cliente->getClienteMaximoCredito($cedula, $Sucursal)){

					//Si el maximo permitido de credito es mayor o igual a todos los aldos mas la factura a cobrar
					if($maximoPermitidoDeCredito>=$saldoTotaldeCliente){return true;}
					else{return false;}
				}else{
					return false;
				}				
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	function descontarArticulosDefectuosos($consecutivo, $sucursal){
		$articulos = $this->factura->getArticulosFactura($consecutivo, $sucursal);
		foreach($articulos as $art){
			$this->articulo->actualizarInventarioRESTADefectuoso($art->Articulo_Factura_Codigo, $art->Articulo_Factura_Cantidad, $sucursal);
		}
	}
	
	function guardarTipoPago($tipoPago, $consecutivo, $sucursal){
		$cliente = $this->factura->getCliente($consecutivo, $sucursal);
		$vendedor = $this->factura->getVendedor($consecutivo, $sucursal);
		
		switch ($tipoPago['tipo']) {
			case 'tarjeta':
				$comision = $this->banco->getComision($tipoPago['banco']);
				$this->factura->guardarPagoTarjeta($consecutivo, $sucursal, $tipoPago['transaccion'], $comision, $vendedor, $cliente, $tipoPago['banco']);
				break;
			case 'deposito':
				$this->factura->guardarPagoDeposito($consecutivo, $sucursal, $tipoPago['deposito'], $vendedor, $cliente, $tipoPago['banco']);
				break;
			case 'cheque':
				$this->factura->guardarPagoCheque($consecutivo, $sucursal, $tipoPago['cheque'], $vendedor, $cliente, $tipoPago['banco']);
				break;
			case 'mixto':
				$comision = $this->banco->getComision($tipoPago['banco']);
				$moneda = $this->factura->getMoneda($consecutivo, $sucursal); 
				$cantidad = $tipoPago['cantidad'];
				$tipoCambio = $this->factura->getTipoCambio($consecutivo, $sucursal);
				if($moneda=='dolares'){
					$cantidad = $cantidad * $tipoCambio;
				}
				
				$this->factura->guardarPagoMixto($consecutivo, $sucursal, $tipoPago['transaccion'], $comision, $vendedor, $cliente, $tipoPago['banco'], $cantidad );
				break;
			case 'credito':
				date_default_timezone_set("America/Costa_Rica");
				$Current_datetime = date("y/m/d : H:i:s", now());
				$facturaHead = $this->factura->getFacturasHeaders($consecutivo, $sucursal);
				foreach($facturaHead as $row){					
					$totalFactura = $row->Factura_Monto_Total;
				}
			
				$this->factura->guardarPagoCredito($consecutivo, $sucursal, $vendedor, $cliente, $tipoPago['canDias'], $Current_datetime, $totalFactura);
				break;
			case 'apartado':
				//Guardamos como si fuera un credito y luego como apartado
				$abono = $tipoPago['abono'];
				if(!is_numeric($abono)){$abono=0;}
				date_default_timezone_set("America/Costa_Rica");
				$Current_datetime = date("y/m/d : H:i:s", now());
				$facturaHead = $this->factura->getFacturasHeaders($consecutivo, $sucursal);
				foreach($facturaHead as $row){					
					$totalFactura = $row->Factura_Monto_Total;
				}	
				
				$totalFactura = $totalFactura - $abono;				
				$credito = $this->factura->guardarPagoCredito($consecutivo, $sucursal, $vendedor, $cliente, 10000, $Current_datetime, $totalFactura);
				
				$moneda = $this->factura->getMoneda($consecutivo, $sucursal); 
				$tipoCambio = $this->factura->getTipoCambio($consecutivo, $sucursal);
				if($moneda=='dolares'){
					$abono = $abono * $tipoCambio;
				}
				$this->factura->guardarPagoAbono($credito, $abono);
				
				break;
		}
	}
	
	function anularFactura(){
		$facturaBODY['status']='error';
		$facturaBODY['error']='17'; //No se logro procesar ls productos
		if(isset($_POST['consecutivo'])){
			$consecutivo = $_POST['consecutivo'];
			include '/../get_session_data.php';	
			
			if($this->factura->existe_Factura($consecutivo, $data['Sucursal_Codigo'])){							
				$facturaBODY['status']='success';
				
				date_default_timezone_set("America/Costa_Rica");
				$Current_datetime = date("y/m/d : H:i:s", now());
				$datos = array(         
					'Factura_Tipo_Pago'=>'contado',
					'Factura_Fecha_Hora'=>$Current_datetime, 
					'Factura_Estado'=>'anulada'
				);
				
				$this->factura->actualizarFacturaHead($datos, $consecutivo, $data['Sucursal_Codigo']);
				$this->devolverProductosdeFactura($consecutivo, $data['Sucursal_Codigo']);
				$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario anulo la factura consecutivo: $consecutivo",$data['Sucursal_Codigo'],'anular');
				
			}else{
				$facturaBODY['status']='error';
				$facturaBODY['error']='19'; //Error no existe esa factura
			}
			
		}else{
			$facturaBODY['status']='error';
			$facturaBODY['error']='16'; //Error de no leer encabezado del URL
		}
		echo json_encode($facturaBODY);
	}
	
	function devolverProductosdeFactura($consecutivo, $sucursal){
		if($productos = $this->factura->getArticulosFactura($consecutivo, $sucursal)){
				foreach($productos as $producto){
					if($this->articulo->existe_Articulo($producto->Articulo_Factura_Codigo,$sucursal)){
						//si el producto existe
						if($producto->Articulo_Factura_Codigo=='00'){
							//si es generico no hacer nada
						}else{
							$this->articulo->actualizarInventarioSUMA($producto->Articulo_Factura_Codigo, $producto->Articulo_Factura_Cantidad, $sucursal);
						}
					}else{}
				}
		}
	}
	
	function devolverInventario(){
		//Limpiamos el log
		$myfile = fopen("application/logs/devolverInventario.txt", "w");
		fwrite($myfile, '');
		fclose($myfile);
		
		
		//file_put_contents('application/logs/devolverInventario.txt', "--Entro\n", FILE_APPEND);
		if(isset($_POST['consecutivo'])&&isset($_POST['items'])){
			$consecutivo = $_POST['consecutivo'];
			include '/../get_session_data.php';	
			
			$items = $_POST['items']; //Obtenemos el array
			$items = json_decode($items, true);
			
			foreach($items as $articulo){
				if($articulo['co']!='00'){ //NO ES GENERICO
					if($this->articulo->existe_Articulo($articulo['co'],$data['Sucursal_Codigo'])){
						if($articuloFactura = $this->articulo->get_ArticuloFactura($articulo['co'], $data['Sucursal_Codigo'], $consecutivo)){
							$cantidadBD = $articuloFactura->Articulo_Factura_Cantidad;
							$cantidadClient = $articulo['ca'];						
							if($cantidadBD!=$cantidadClient){
								if($cantidadBD<$cantidadClient){
									$cantidadDevolver = $cantidadClient-$cantidadBD;
									//SUMA AL INVENTARIO
									$this->articulo->actualizarInventarioSUMA($articulo['co'], $cantidadDevolver, $data['Sucursal_Codigo']);
									//file_put_contents('application/logs/devolverInventario.txt', "--SE SUMO AL INVENTARIO D:$cantidadDevolver BD:$cantidadBD CL:$cantidadClient\n", FILE_APPEND);
								}elseif($cantidadBD>$cantidadClient){
									$cantidadDevolver = $cantidadBD-$cantidadClient;
									//RESTAR AL INVENTARIO
									$this->articulo->actualizarInventarioRESTA($articulo['co'], $cantidadDevolver, $data['Sucursal_Codigo']);
									//file_put_contents('application/logs/devolverInventario.txt', "--SE RESTO AL INVENTARIO D:$cantidadDevolver BD:$cantidadBD CL:$cantidadClient\n", FILE_APPEND);
								}
							}else{
								//No hay que hacer nada
								//file_put_contents('application/logs/devolverInventario.txt', "--NO CAMBIO\n", FILE_APPEND);
							}
						}else{//Se agrego Articulo en edicion
							$this->articulo->actualizarInventarioSUMA($articulo['co'], $articulo['ca'], $data['Sucursal_Codigo']);
							//file_put_contents('application/logs/devolverInventario.txt', "--SE SUMO NUEVO\n", FILE_APPEND);
						}
					}else{
						//Si el articulo no existe no hacer nada
						//file_put_contents('application/logs/devolverInventario.txt', "--INEXISTENTE\n", FILE_APPEND);
					}
				}else{
					//Si el articulo no existe no hacer nada
						//file_put_contents('application/logs/devolverInventario.txt', "--GENERICO\n", FILE_APPEND);
				}				
			}
			//Revisar si algun producto fue eliminado en edicion
			$this->checkIfItemsWasDeletedAndRestore($items, $consecutivo, $data['Sucursal_Codigo']);
			
		}else{
			//file_put_contents('application/logs/devolverInventario.txt', "--URL MALA\n", FILE_APPEND);
		}		
	}
	
	function checkIfItemsWasDeletedAndRestore($ItemsCliente, $consecutivo, $sucursal){
		//file_put_contents('application/logs/devolverInventario.txt', "--SE VERIFICA CAMBIOS\n", FILE_APPEND);
		/*$articulosBD = $this->factura->getArticulosFactura($consecutivo, $sucursal); //obtenemos los productos de la factura
		$articulosBD = makeArrayItemsOnlyByKey($articulosBD, 'Articulo_Factura_Codigo');
		$ItemsClienteOnly = makeArrayItemsOnlyByKey($itemsArray, 'co');
		$mergedArray = getItemsEliminadosEnEdicion($articulosBD, $ItemsClienteOnly);
		if(count($mergedArray)>0){ //Si hay elementos quiere decir que si se eliminaron elementos
			file_put_contents('application/logs/devolverInventario.txt', "--SI HAY ELEMENTOS QUE SE ELIMINARION\n", FILE_APPEND);
			for($contador = 0; $contador<count($mergedArray); $contador++){
				
			}
		}*/
		//Obtener lista de la factura original
		$articulosBD = $this->factura->getArticulosFactura($consecutivo, $sucursal);
		//Homologar arrays
		$_ArBD = $this->makeResultItemsOnlyByKey($articulosBD, 'Articulo_Factura_Codigo');
		$_ArCL = $this->makeArrayItemsOnlyByKey($ItemsCliente, 'co');
		//Obtener la diferencia de array
		$_arrayDif = $this->getItemsEliminadosEnEdicion($_ArBD, $_ArCL);
		//file_put_contents('application/logs/devolverInventario.txt', print_r($_arrayDif), FILE_APPEND);
		if(count($_arrayDif)>0){//Si se eliminaron productos
			foreach($_arrayDif as $item){
				if($cantidadBD = $this->articulo->getCantidadArticuloFactura($item, $sucursal, $consecutivo)){
					//Restamos eso de inventario
					if($this->articulo->actualizarInventarioRESTA($item, $cantidadBD, $sucursal)=='3'){
						//file_put_contents('application/logs/devolverInventario.txt', "Articulo $item esta en la factura\n", FILE_APPEND);
						//file_put_contents('application/logs/devolverInventario.txt', "Salio bien\n", FILE_APPEND);
					}					
				}else{
					//file_put_contents('application/logs/devolverInventario.txt', "Articulo $item no esta en la factura\n", FILE_APPEND);
				}
			}
		}
	}
	
	function getItemsEliminadosEnEdicion($itemsEnBD, $itemsdeCliente){
		return array_diff($itemsEnBD,$itemsdeCliente);
	}
	
	function makeArrayItemsOnlyByKey($itemsArray, $key){
		$processed_array=[];
		foreach($itemsArray as $item){
			array_push($processed_array,$item[$key]);
			//file_put_contents('application/logs/devolverInventario.txt', "Entro\n", FILE_APPEND);
		}
		return $processed_array;
	}
	
	function makeResultItemsOnlyByKey($itemsResult, $key){
		$processed_array=[];
		foreach($itemsResult as $item){
			array_push($processed_array,$item->$key);
			//file_put_contents('application/logs/devolverInventario.txt', "Entro\n", FILE_APPEND);
		}
		return $processed_array;
	}
	
	function cambiarFactura(){
			$retorno['status'] = 'error';
			$retorno['error'] = 'cf_1';
			if(isset($_POST['head'])&&isset($_POST['items'])&&isset($_POST['consecutivo'])&&isset($_POST['token'])){
		  //Obtener las dos partes del post
			$info_factura = $_POST['head'];
			$items_factura = $_POST['items'];
			$consecutivo = $_POST['consecutivo'];
			//Decodificar el JSON del post
			$info_factura = json_decode($info_factura, true);			
			$items_factura = json_decode($items_factura, true);
			//Obtenemos la primera posicion del info_factura para obtener el array final
			$info_factura = $info_factura[0];
						
			include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			
			if(sizeOf($items_factura)>0){
					//ce:cedula_field, no:nombre_field, cu:tipo_moneda, ob:observaciones
					//CAMBIAMOS EL ENCABEZADO
					$datosHead = array(
										'TB_03_Cliente_Cliente_Cedula'=>$info_factura['ce'],
										'Factura_Nombre_Cliente'=>$info_factura['no'],
										'Factura_Moneda'=>$info_factura['cu'],
										'Factura_Observaciones'=>$info_factura['ob']
									);
					//Borramos la factura temporal
					$this->articulo->eliminarFacturaTemporal($_POST['token']);
									
					//ELIMINAMOS LOS PRODUCTOS
					$this->factura->eliminarArticulosFactura($consecutivo, $data['Sucursal_Codigo']);
					
					$this->factura->actualizarFacturaHead($datosHead, $consecutivo, $data['Sucursal_Codigo']);
								
					//LOS VOLVEMOS A AGREGAR LOS NUEVOS
					$cliente = $this->factura->getCliente($consecutivo, $data['Sucursal_Codigo']);
					
					$vendedor = $this->factura->getVendedor($consecutivo, $data['Sucursal_Codigo']);
					
					
					$this->agregarItemsFactura($items_factura, $consecutivo, $data['Sucursal_Codigo'], $vendedor, $cliente);
					
					$this->actualizarCostosFactura($consecutivo, $data['Sucursal_Codigo']);
				
					$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ".$data['Usuario_Codigo']." edito la factura consecutivo:$consecutivo", $data['Sucursal_Codigo'],'factura_edicion');
					
					unset($retorno['error']);
					$retorno['status'] = 'success';
			}else{
					$retorno['error'] = 'cf_3'; //No hay productos
			}
		}else{
			$retorno['error'] = 'cf_2'; //URL con mal formato
		} 
		echo json_encode($retorno);
	}
	
	function agregarItemsFactura($items_factura, $consecutivo, $sucursal, $vendedor, $cliente){
		foreach($items_factura as $item){
		//{co:codigo, de:descripcion, ca:cantidad, ds:descuento, pu:precio_unitario, ex:exento}
			if($item['co']=='00'){ //Si es generico					
					$this->factura->addItemtoInvoice($item['co'], $item['de'], $item['ca'], $item['ds'], $item['ex'], $item['re'], $item['pu'], $item['pu'], $consecutivo, $sucursal, $vendedor, $cliente,'');
			}else{ //Si es normal					
				if($this->articulo->existe_Articulo($item['co'], $sucursal)){ //Verificamos que el codigo exista
					//Obtenemos los datos que no vienen en el JSON
					$descripcion = $this->articulo->getArticuloDescripcion($item['co'], $sucursal);
					$imagen = $this->articulo->getArticuloImagen($item['co'], $sucursal);
					$precio = $this->articulo->getPrecioProducto($item['co'], $this->articulo->getNumeroPrecio($cliente), $sucursal);
					$precioFinal = $this->articulo->getPrecioProducto($item['co'], 1, $sucursal);
					$this->factura->addItemtoInvoice($item['co'], $descripcion, $item['ca'], $item['ds'], $item['ex'], $item['re'], $precio, $precioFinal, $consecutivo, $sucursal, $vendedor, $cliente, $imagen);
				}
			}
			
		}
		//$this->factura->addItemtoInvoice($codigo, $descripcion, $cantidad, $descuento, $exento, $precio, $consecutivo, $sucursal, $vendedor, $cliente);
	}
	
	function actualizarCostosFactura($consecutivo, $sucursal){
		$costosArray = $this->factura->getCostosTotalesFactura($consecutivo, $sucursal);
		$this->factura->updateCostosTotales($costosArray, $consecutivo, $sucursal);
	}
 
 
	//////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////// CARGA DE PROFORMAS /////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////
	
	function getProformaHeaders(){		
		$facturaHEAD['status']='error';
		$facturaHEAD['error']='14'; //No se logro procesar la factura
		if(isset($_POST['consecutivo'])){
			$consecutivo=$_POST['consecutivo'];
			include '/../get_session_data.php'; 
			if($this->proforma_m->existe_Proforma($consecutivo, $data['Sucursal_Codigo'])){
				if($facturaHEADS = $this->proforma_m->getProformasHeaders($consecutivo, $data['Sucursal_Codigo'])){
					
					foreach($facturaHEADS as $row){
						if($row->Proforma_Estado=='pendiente'){	
							$fecha_proforma = $row->Proforma_Fecha_Hora;
							date_default_timezone_set("America/Costa_Rica");
							$Current_datetime = date("Y-m-d H:i:s", now());		
							$datediff = abs(strtotime($Current_datetime) - strtotime($fecha_proforma));
							$dias = $datediff/(60*60*24);
							if(16>$dias){
								
								$facturaHEAD['status']='success';				
								$facturaHEAD['cedula']=$row->TB_03_Cliente_Cliente_Cedula;
								
								$ClienteArray = $this->cliente->getNombreCliente($facturaHEAD['cedula']);
								$NombreCliente = $ClienteArray['nombre'];
								
								$facturaHEAD['nombre']= $NombreCliente;
								$facturaHEAD['moneda']=$row->Proforma_Moneda;
								$facturaHEAD['total']=$row->Proforma_Monto_Total;
								$facturaHEAD['iva']=$row->Proforma_Monto_IVA;
								$facturaHEAD['costo']=$row->Proforma_Monto_Sin_IVA;
								$facturaHEAD['observaciones']=$row->Proforma_Observaciones;
								$facturaHEAD['ivapor']=$row->Proforma_Porcentaje_IVA;
								$facturaHEAD['cambio']=$row->Proforma_Tipo_Cambio;
								
								//Cargamos la variable que ocupamos de cliente
								$facturaHEAD['cliente_sucursal'] = $row->Proforma_Cliente_Sucursal;
								$facturaHEAD['cliente_exento'] = $row->Proforma_Cliente_Exento;
								$facturaHEAD['cliente_retencion'] = $row->Proforma_Cliente_No_Retencion;
								
							}else{
								$facturaHEAD['status']='error';
								$facturaHEAD['error']='22';
								
							}							
							
						}else if($row->Proforma_Estado=='cobrada'){
							$facturaHEAD['status']='error';
							$facturaHEAD['error']='21';
							//Factura ya cobrada
						}						
					}
				}else{
					$facturaHEAD['status']='error';
					$facturaHEAD['error']='10';
					 //Error para no se pudo cargar los headers
				}
			}else{
				$facturaHEAD['status']='error';
				$facturaHEAD['error']='20'; //Error de no existe proforma
			}
		}else{
			
			$facturaHEAD['status']='error';
			$facturaHEAD['error']='13'; //Error de no leer encabezado del URL
			
		}
		//echo "Entro a 13";
		echo json_encode($facturaHEAD);
	}
	
	function getProformaHeadersConsulta(){		
		$facturaHEAD['status']='error';
		$facturaHEAD['error']='14'; //No se logro procesar la factura
		if(isset($_POST['consecutivo'])){
			$consecutivo=$_POST['consecutivo'];
			include '/../get_session_data.php'; 
			if($this->proforma_m->existe_Proforma($consecutivo, $data['Sucursal_Codigo'])){
				if($facturaHEADS = $this->proforma_m->getProformasHeaders($consecutivo, $data['Sucursal_Codigo'])){
					
					foreach($facturaHEADS as $row){							
							$facturaHEAD['status']='success';				
							$facturaHEAD['cedula']=$row->TB_03_Cliente_Cliente_Cedula;								
							$ClienteArray = $this->cliente->getNombreCliente($facturaHEAD['cedula']);
							$NombreCliente = $ClienteArray['nombre'];								
							$facturaHEAD['nombre']= $NombreCliente;
							$facturaHEAD['moneda']=$row->Proforma_Moneda;
							$facturaHEAD['total']=$row->Proforma_Monto_Total;
							$facturaHEAD['iva']=$row->Proforma_Monto_IVA;
							$facturaHEAD['costo']=$row->Proforma_Monto_Sin_IVA;
							$facturaHEAD['observaciones']=$row->Proforma_Observaciones;
							$facturaHEAD['ivapor']=$row->Proforma_Porcentaje_IVA;
							$facturaHEAD['retencion']=$row->Proforma_Retencion;
							$facturaHEAD['cambio']=$row->Proforma_Tipo_Cambio;	
							
							$facturaHEAD['sucursal']= $data['Sucursal_Codigo'];
							$facturaHEAD['servidor_impresion']= $this->configuracion->getServidorImpresion();
							$facturaHEAD['token'] =  md5($data['Usuario_Codigo'].$data['Sucursal_Codigo']."GAimpresionBO");										
					}
				}else{
					$facturaHEAD['status']='error';
					$facturaHEAD['error']='10';
					 //Error para no se pudo cargar los headers
				}
			}else{
				$facturaHEAD['status']='error';
				$facturaHEAD['error']='20'; //Error de no existe proforma
			}
		}else{
			
			$facturaHEAD['status']='error';
			$facturaHEAD['error']='13'; //Error de no leer encabezado del URL
			
		}
		//echo "Entro a 13";
		echo json_encode($facturaHEAD);
	}
 
	function getArticulosProforma(){
		$facturaBODY['status']='error';
		$facturaBODY['error']='17'; //No se logro procesar ls productos
		if(isset($_POST['consecutivo'])){
			$consecutivo=$_POST['consecutivo'];
			include '/../get_session_data.php'; 
			//header('Content-Type: application/json');			
			if($facturaPRODUCTS = $this->proforma_m->getArticulosProforma($consecutivo, $data['Sucursal_Codigo'])){
				//var_dump($consecutivo);
				//echo "crear array";
				$facturaBODY['status']='success';
				$articulos=[];
				foreach($facturaPRODUCTS as $row){
					$articulo=[]; //Limpiamos el array
					$articulo['codigo']=$row->Articulo_Proforma_Codigo;
					$articulo['descripcion']=$row->Articulo_Proforma_Descripcion;
					$articulo['cantidad']=$row->Articulo_Proforma_Cantidad;
					$articulo['descuento']=$row->Articulo_Proforma_Descuento;
					$articulo['exento']=$row->Articulo_Proforma_Exento;
					$articulo['retencion']=$row->Articulo_Proforma_No_Retencion;
					$articulo['precio']=$row->Articulo_Proforma_Precio_Unitario;
					$articulo['precioFinal']=$row->Articulo_Proforma_Precio_Final;
					//Procesamos la imagen
					$articulo['imagen'] = $row->Articulo_Proforma_Imagen;				
					$ruta_a_preguntar = FCPATH.'application\\images\\articulos\\'.$articulo['imagen'].'.jpg';
					//return $ruta_a_preguntar;
					if(!file_exists($ruta_a_preguntar)){$articulo['imagen'] = '00';}					
					if($inventario = $this->articulo->inventarioActual($articulo['codigo'], $data['Sucursal_Codigo'])){
						$articulo['bodega']=$inventario;
					}else{
						$articulo['bodega']='0';
					}
					if($articulo['codigo']=='00'){
						$articulo['bodega']='1000';
					}
					array_push($articulos, $articulo);
				}
				$facturaBODY['productos']=$articulos;
			}else{
				$facturaBODY['status']='error';
				$facturaBODY['error']='15';
				//echo json_encode($facturaHEAD); //Error para no se pudo cargar los productos
			}
		}else{
			//echo "Entro a 13";
			$facturaBODY['status']='error';
			$facturaBODY['error']='16'; //Error de no leer encabezado del URL
			//echo json_encode($facturaHEAD);
		}
		//echo "Entro a 13";
		echo json_encode($facturaBODY);
	}
	
	function getArticulosProformaConsulta(){
		$facturaBODY['status']='error';
		$facturaBODY['error']='17'; //No se logro procesar ls productos
		if(isset($_POST['consecutivo'])){
			$consecutivo=$_POST['consecutivo'];
			include '/../get_session_data.php'; 
			//header('Content-Type: application/json');			
			if($facturaPRODUCTS = $this->proforma_m->getArticulosProforma($consecutivo, $data['Sucursal_Codigo'])){
				//var_dump($consecutivo);
				//echo "crear array";
				$facturaBODY['status']='success';
				$articulos=[];
				foreach($facturaPRODUCTS as $row){
					$articulo=[]; //Limpiamos el array
					$articulo['codigo']=$row->Articulo_Proforma_Codigo;
					$articulo['descripcion']=$row->Articulo_Proforma_Descripcion;
					$articulo['cantidad']=$row->Articulo_Proforma_Cantidad;
					$articulo['descuento']=$row->Articulo_Proforma_Descuento;
					$articulo['exento']=$row->Articulo_Proforma_Exento;
					$articulo['precio']=$row->Articulo_Proforma_Precio_Unitario;
					
					//Procesamos la imagen
					$articulo['imagen'] = $row->Articulo_Proforma_Imagen;				
					$ruta_a_preguntar = FCPATH.'application\\images\\articulos\\'.$articulo['imagen'].'.jpg';
					//return $ruta_a_preguntar;
					if(!file_exists($ruta_a_preguntar)){$articulo['imagen'] = '00';}					
					if($inventario = $this->articulo->inventarioActual($articulo['codigo'], $data['Sucursal_Codigo'])){
						$articulo['bodega']=$inventario;
					}else{
						$articulo['bodega']='0';
					}
					if($articulo['codigo']=='00'){
						$articulo['bodega']='1000';
					}
					array_push($articulos, $articulo);
				}
				$facturaBODY['productos']=$articulos;
			}else{
				$facturaBODY['status']='error';
				$facturaBODY['error']='15';
				//echo json_encode($facturaHEAD); //Error para no se pudo cargar los productos
			}
		}else{
			//echo "Entro a 13";
			$facturaBODY['status']='error';
			$facturaBODY['error']='16'; //Error de no leer encabezado del URL
			//echo json_encode($facturaHEAD);
		}
		//echo "Entro a 13";
		echo json_encode($facturaBODY);
	}
	
	function creaFacturaFromProforma(){
		$facturaBODY['status']='error';
		$facturaBODY['error']='17'; //No se logro procesar ls productos
		if(isset($_POST['consecutivo'])){
			
			
			$consecutivo = $_POST['consecutivo'];
			include '/../get_session_data.php';	
			
			if($this->proforma_m->existe_Proforma($consecutivo, $data['Sucursal_Codigo'])){							
				$articulos = $this->JSONArray($consecutivo, $data['Sucursal_Codigo']);
				if($this->hayProductosParaProforma($data['Sucursal_Codigo'], $articulos)){
					$proformaHEAD = $this->proforma_m->getProformasHeaders($consecutivo, $data['Sucursal_Codigo']);
					
					foreach($proformaHEAD as $HEAD){
						$info_factura['ce']=$HEAD->TB_03_Cliente_Cliente_Cedula;
						$info_factura['no']=$HEAD->Proforma_Nombre_Cliente;
						$info_factura['cu']=$HEAD->Proforma_Moneda;
						$info_factura['ob']=$HEAD->Proforma_Observaciones;
					}
					
				
					if($consecutivo_F = $this->factura->crearfactura($info_factura['ce'], $info_factura['no'], $info_factura['cu'], $info_factura['ob'], $data['Sucursal_Codigo'], $data['Usuario_Codigo'], $consecutivo)){
					$facturaBODY['status']='success';
					$facturaBODY['consecutivo']=$consecutivo_F;
					
					
					//{co:codigo, de:descripcion, ca:cantidad, ds:descuento, pu:precio_unitario, ex:exento}
					//$articulos = $this->JSONArray($consecutivo, $data['Sucursal_Codigo']);
					$this->bajarProductosDelInventario($articulos, $data['Sucursal_Codigo']);
					
					$this->agregarItemsFactura($articulos, $consecutivo_F, $data['Sucursal_Codigo'], $data['Usuario_Codigo'], $info_factura['ce']); //Agregamos los items				
					
					
					$this->actualizarCostosFactura($consecutivo_F, $data['Sucursal_Codigo']);
					
					
					//Cambiamos el estado de la proforma
					$proformaActualizacion['Proforma_Estado']='cobrada';
					$this->proforma_m->actualizar($consecutivo, $data['Sucursal_Codigo'], $proformaActualizacion);
					
					$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ".$data['Usuario_Codigo']." cobro la proforma consecutivo:$consecutivo", $data['Sucursal_Codigo'],'factura_envio');
					
					}else{
						$facturaBODY['status']='error';
						$facturaBODY['error']='20'; //Error de no existe proforma
					}
					
				}else{
					$facturaBODY['status']='error';
					$facturaBODY['error']='23'; //Error de no existe algun producto o no hay inventario suficiente
				}				
			}else{
				$facturaBODY['status']='error';
				$facturaBODY['error']='20'; //Error de no existe proforma
			}
			
		}else{
			$facturaBODY['status']='error';
			$facturaBODY['error']='16'; //Error de no leer encabezado del URL
		}		
		echo json_encode($facturaBODY);
	}	
		
	function JSONArray($consecutivo, $sucursal){
		//{co:codigo, de:descripcion, ca:cantidad, ds:descuento, pu:precio_unitario, ex:exento}
		$articulosProforma = $this->proforma_m->getArticulosProforma($consecutivo, $sucursal);
		$articulosFactura=[];
		foreach($articulosProforma as $articuloP){
			$articulo['co'] = $articuloP->Articulo_Proforma_Codigo;
			$articulo['de'] = $articuloP->Articulo_Proforma_Descripcion;
			$articulo['ca'] = $articuloP->Articulo_Proforma_Cantidad;
			$articulo['ds'] = $articuloP->Articulo_Proforma_Descuento;
			$articulo['pu'] = $articuloP->Articulo_Proforma_Precio_Unitario;
			$articulo['ex'] = $articuloP->Articulo_Proforma_Exento;
			$articulo['im'] = $articuloP->Articulo_Proforma_Imagen;
			array_push($articulosFactura, $articulo);
		}
		return $articulosFactura;
	}
	
	function hayProductosParaProforma($sucursal, $productos){  //Verifica que los articulos existan y tengan inventario
		foreach($productos as $producto){
			if(trim($producto['co'])!='00'){
					if($this->articulo->existe_Articulo($producto['co'],$sucursal)){
						//Calculamos el inventario disponible
						$invActual = (Int)$this->articulo->inventarioActual($producto['co'], $sucursal);
						$canProforma = (Int)$producto['ca'];
						$diferencia = $invActual-$canProforma;
						
						if($diferencia<0){return false;} //Si alguna cantidad queda en negativo significa que no hay suficiente en inevntario
					}else{
						return false;
					}	
			}		
		}
		return true;
		//return false;
	}
	
	function bajarProductosDelInventario($productos, $sucursal){
		foreach($productos as $producto){
			if($this->articulo->existe_Articulo($producto['co'],$sucursal)){				
				$canProforma = (Int)$producto['ca'];
				$this->articulo->actualizarInventarioRESTA($producto['co'], $canProforma, $sucursal);
			}else{
				return false;
			}			
		}
		return true;
	}
	
	function imprimirFactura(){
		if(isset($_GET['consecutivo'])&&isset($_GET['sucursal'])&&isset($_GET['tipo'])){
			$consecutivo = 	mysql_real_escape_string($_GET['consecutivo']);
			$sucursal = mysql_real_escape_string($_GET['sucursal']);
			$tipo = mysql_real_escape_string($_GET['tipo']);
			
			if($empresa = $this->empresa->getEmpresa($sucursal)){
				if($facturaHead = $this->factura->getFacturasHeaders($consecutivo, $sucursal)){
					if($facturaBody = $this->factura->getArticulosFactura($consecutivo, $sucursal)){
						if($tipo=='t'||$tipo=='c'){							
							$data['empresa'] = $empresa[0];
							$data['fhead'] = $facturaHead[0];
							$data['fbody'] = $facturaBody;
							$data['documento'] = (object)array('tipo'=>'Factura');
							
							//Valoramos si un credito para poner la fecha de vencimiento
							if($data['fhead'] -> Factura_Tipo_Pago == 'credito'){
								$diasCredito = $this->factura->getCreditoClienteDeFactura($consecutivo, $sucursal, $data['fhead'] -> TB_03_Cliente_Cliente_Cedula);
								$data['documento'] = (object)array('tipo'=>'Factura','diasCredito'=>$diasCredito);
							}
							
							switch($tipo){
								case 't':
									$this->load->view('facturas/view_impresion_termica', $data);
								break;
								case 'c':
									$this->load->view('facturas/view_impresion_carta', $data);
								break;
							}
						}else{
							echo "ERROR<br>Tipo de impresion no valida. . . <br>Contacte al administrador";
						}
					}else{
						echo "ERROR<br>Problema con los productos. . . <br>Contacte al administrador";
					}
				}else{
					echo "ERROR<br>Factura inexistente. . . <br>Contacte al administrador";
				}
			}else{
				echo "ERROR<br>No existe sucursal. . . <br>Contacte al administrador";
			}
		}else{
			echo "ERROR<br>URL incorrecta. . . <br>Contacte al administrador";
		}
	}
	
	
}// FIN DE LA CLASE


?>