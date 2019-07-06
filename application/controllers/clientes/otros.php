<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class otros extends CI_Controller {
	 function __construct()
	 {
	    parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('cliente','',TRUE);
		$this->load->model('articulo','',TRUE);
		$this->load->model('familia','',TRUE);	
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		if($permisos['otros_cliente'])
		{
			//$this->load->view('clientes/clientes_descuentos_credito_view', $data);	
		}
		else{
		   redirect('accesoDenegado', 'location');
		}
	 }

	 function index()
	 {
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
			
		/*$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		
		if($permisos['otros_cliente'])
		{*/
                        $data['javascript_cache_version'] = $this->javascriptCacheVersion;
			$this->load->view('clientes/clientes_descuentos_credito_view', $data);	
		/*}
		else{
		   redirect('accesoDenegado', 'location');
		}*/
		
	}
	
	function getCliente(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['cedula'])){
			$cedula = $_POST['cedula']; 
			if(trim($cedula) == '1' || trim($cedula) == '0'){
				$retorno['error'] = '4'; //Error cliente contado y afiliado
			}else{
				if($clienteArray = $this->cliente->getClientes_Cedula($cedula)){
					//$retorno['status'] = 'success';
					foreach($clienteArray as $row){
						$cliente['nombre'] = $row-> Cliente_Nombre;
						$cliente['apellidos'] = $row-> Cliente_Apellidos;						
					}
					include PATH_USER_DATA;
					
					//Descuento propio del cliente
					if($descuento_cliente = $this->cliente->getClienteDescuento($cedula, $data['Sucursal_Codigo'])){
					}else{
						$descuento_cliente = 0;
					}
					$cliente['descuento'] = $descuento_cliente;
					
					
					//Maximo credito del cliente
					if($max_credito_cliente = $this->cliente->getClienteMaximoCredito($cedula, $data['Sucursal_Codigo'])){
					}else{
						$max_credito_cliente = 0;
					}
					$cliente['maxCredito'] = $max_credito_cliente;
					
					//Descuento con productos del cliente
					if($descuentoProductos = $this->cliente->getDescuentosDeClienteConProductos($cedula, $data['Sucursal_Codigo'])){
						$desProductos = array();
						foreach($descuentoProductos as $producto){
							$pro['id'] = $producto -> Descuento_producto_id;
							$pro['porcentaje'] = $producto -> Descuento_producto_monto;
							$pro['codigo'] = $producto -> TB_06_Articulo_Articulo_Codigo;
							$pro['descripcion'] = $this -> articulo -> getArticuloDescripcion($producto -> TB_06_Articulo_Articulo_Codigo, $data['Sucursal_Codigo']);
							array_push($desProductos, $pro);
						}
						$cliente['desProductos'] = $desProductos;
					}else{
						$cliente['desProductos'] = array();
					}	

					//Descuentos con familias del cliente
					if($descuentoFamilias = $this->cliente->getDescuentosDeClienteConFamilias($cedula, $data['Sucursal_Codigo'])){
						$desFamilias = array();
						foreach($descuentoFamilias as $familia){
							$fam['id'] = $familia -> Descuento_familia_id;
							$fam['porcentaje'] = $familia -> Descuento_familia_porcentaje;
							$fam['codigo'] = $familia -> TB_05_Familia_Familia_Codigo;
							$fam['descripcion'] = $this -> familia -> getNombreFamiliaSucursal($familia -> TB_05_Familia_Familia_Codigo, $data['Sucursal_Codigo']);
							array_push($desFamilias, $fam);
						}
						$cliente['desFamilias'] = $desFamilias;
					}else{
						$cliente['desFamilias'] = array();
					}
					
					
					/// TODO SALIO BIEN
					$retorno['status'] = 'success';
					$retorno['cliente'] = $cliente;
				}else{
					$retorno['error'] = '3'; //Error no hay cliente
				}
			}
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);
	}
	
	function actualizarDescuento(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['cedula'])&&isset($_POST['descuento'])){
			$cedula = $_POST['cedula']; 
			$descuento = $_POST['descuento']; 
			include PATH_USER_DATA;
			
			if($this->cliente->existe_Cliente($cedula)){
				if($this->cliente->existeClienteDescuento($cedula, $data['Sucursal_Codigo'])){
					//Actualizamos descuento 
					$this->cliente->actualizarDescuentoCliente($descuento, $data['Sucursal_Codigo'], $cedula);

					$this->user->guardar_Bitacora_Cliente($cedula, 
								$data['Sucursal_Codigo'], 
								$data['Usuario_Codigo'], 
								'Actualiza_DesClien', 
								'Actualización descuento : '. $descuento);
					$retorno['status'] = 'success';
				}else{
					//Agregamos descuento					
					$this->cliente->agregarDescuentoCliente($descuento, $data['Sucursal_Codigo'], $cedula);					
					$this->user->guardar_Bitacora_Cliente($cedula, 
								$data['Sucursal_Codigo'], 
								$data['Usuario_Codigo'], 
								'Agrega_DesCliente', 
								'Agregar descuento : '. $descuento);
					$retorno['status'] = 'success';
				}			
			}else{
				$retorno['error'] = '5'; //Error no existe cliente
			}
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);
	}
	
	function actualizarCredito(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['cedula'])&&isset($_POST['credito'])){
			$cedula = $_POST['cedula']; 
			$credito = $_POST['credito']; 
			include PATH_USER_DATA;
			
			if($this->cliente->existe_Cliente($cedula)){
				if($this->cliente->existeClienteCredito($cedula, $data['Sucursal_Codigo'])){
					//Actualizamos credito 
					$this->cliente->actualizarCreditoCliente($credito, $data['Sucursal_Codigo'], $cedula);
					$this->user->guardar_Bitacora_Cliente($cedula, 
								$data['Sucursal_Codigo'], 
								$data['Usuario_Codigo'], 
								'Actualiza_Credito', 
								'Actualización del Crédito : '. $credito);
					$retorno['status'] = 'success';
				}else{
					//Agregamos credito					
					$this->cliente->agregarCreditoCliente($credito, $data['Sucursal_Codigo'], $cedula);					
					$this->user->guardar_Bitacora_Cliente($cedula, 
								$data['Sucursal_Codigo'], 
								$data['Usuario_Codigo'], 
								'Agregar_Credito', 
								'Agregar Crédito : '. $credito);
					$retorno['status'] = 'success';
				}			
			}else{
				$retorno['error'] = '5'; //Error no existe cliente
			}
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);
	}
	
	function eliminarDescuentoProducto(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['id'])){
			include PATH_USER_DATA;
			$idDescuentoProducto = $_POST['id'];
			$cedula = $_POST['cedula'];
			$codigoProducto = $_POST['codigo'];
			$this->cliente->eliminarDescuentoProducto($idDescuentoProducto);
			$this->user->guardar_Bitacora_Cliente($cedula, 
								$data['Sucursal_Codigo'], 
								$data['Usuario_Codigo'], 
								'Elimina_DesProducto', 
								'Eliminación de Descuento del producto: '. $codigoProducto);

			$retorno['status'] = 'success';
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);
	}
	
	function getArticulo(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['codigo'])){
			$codigo = $_POST['codigo'];
			include PATH_USER_DATA;
			if($articulo = $this->articulo->get_Articulo($codigo, $data['Sucursal_Codigo'])){
				foreach($articulo as $row)
				{$descripcion = $row->Articulo_Descripcion;}
				$retorno['status'] = 'success';
				$retorno['descripcion'] = $descripcion;				
			}else{
				$retorno['error'] = '6'; //Producto no existe
			}
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);
	}
	
	function setDescuentoProducto(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['codigo'])&&isset($_POST['cedula'])&&isset($_POST['descuento'])){
			$codigo = $_POST['codigo'];
			$cedula = $_POST['cedula'];
			$descuento = $_POST['descuento'];
			include PATH_USER_DATA;
			if($pro = $this->articulo->get_Articulo($codigo,$data['Sucursal_Codigo'])){
				if($this->cliente->existe_Cliente($cedula)){
					if($this->cliente->existeDescuentoConProducto($codigo, $cedula, $data['Sucursal_Codigo']))
					{						
						$retorno['error'] = '7'; //Ya existe un descuento con ese producto y cliente y sucursal
					}else{
						foreach($pro as $row){
							$familia = $row -> TB_05_Familia_Familia_Codigo;
						}
						
						$numeroPrecio = $this->cliente->getNumeroPrecio($cedula);
						$precioArticulo = $this->articulo->getPrecioProducto($codigo, $numeroPrecio, $data['Sucursal_Codigo']);
						
						$diferencia = $precioArticulo - $descuento;
						
						$descuentoPorcentaje = ($diferencia*100)/$precioArticulo;
						
						if($diferencia>=0){
							$this->cliente->agregarDescuentoDeProducto($codigo, $cedula, $data['Sucursal_Codigo'], $descuentoPorcentaje, $familia);
							$this->user->guardar_Bitacora_Cliente($cedula, 
							$data['Sucursal_Codigo'], 
							$data['Usuario_Codigo'], 
							'Agrega_DesProducto', 
							'Agregación Descuento : '.$descuentoPorcentaje." % al producto: ". $codigo);

							$retorno['status'] = 'success';							
						}else{
							$retorno['error'] = '10'; //Descuento mayor al precio del cliente							
						}						
					}			
				}else{
					$retorno['error'] = '5'; //Cliente no existe
				}							
			}else{
				$retorno['error'] = '6'; //Producto no existe
			}
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);
	}
	
	/********************************************************************************
	*
	*                       Mantenimeinto de descuentos de familia
	*/
	
	function eliminarDescuentoFamilia(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['id'])){
			include PATH_USER_DATA;
			$idDescuentoFamilia = $_POST['id'];
			$cedula = $_POST['cedula'];
			$codigoFamilia = $_POST['codigo'];
			$this->cliente->eliminarDescuentoFamilia($idDescuentoFamilia);
			$this->user->guardar_Bitacora_Cliente($cedula, 
								$data['Sucursal_Codigo'], 
								$data['Usuario_Codigo'], 
								'Elimina_DesFamilia', 
								'Eliminación de Descuento Familia: '. $codigoFamilia);
			$retorno['status'] = 'success';
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);
	}
	
	function getFamilia(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['codigo'])){
			$codigo = $_POST['codigo'];
			include PATH_USER_DATA;
			if($familia = $this->familia->getNombreFamiliaSucursal($codigo, $data['Sucursal_Codigo'])){
				$retorno['status'] = 'success';
				$retorno['descripcion'] = $familia;				
			}else{
				$retorno['error'] = '6'; //Familia no existe
			}
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);
	}
	
	function setDescuentoFamilia(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['codigo'])&&isset($_POST['cedula'])&&isset($_POST['descuento'])){
			$codigo = $_POST['codigo'];
			$cedula = $_POST['cedula'];
			$descuento = $_POST['descuento'];
			include PATH_USER_DATA;
			if($this->familia->getNombreFamiliaSucursal($codigo, $data['Sucursal_Codigo'])){
				if($this->cliente->existe_Cliente($cedula)){
					if($this->cliente->existeDescuentoConFamilia($codigo, $cedula, $data['Sucursal_Codigo']))
					{						
						$retorno['error'] = '9'; //Ya existe un descuento con esa familia y cliente y sucursal
					}else{						
						$this->cliente->agregarDescuentoDeFamilia($codigo, $cedula, $data['Sucursal_Codigo'], $descuento);
						$this->user->guardar_Bitacora_Cliente($cedula, 
								$data['Sucursal_Codigo'], 
								$data['Usuario_Codigo'], 
								'Agrega_DesFamilia', 
								'Agregación de Descuento Familia: '. $codigo);
						$retorno['status'] = 'success';	
					}			
				}else{
					$retorno['error'] = '5'; //Cliente no existe
				}							
			}else{
				$retorno['error'] = '8'; //Familia no existe
			}
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);
	}

	/********************************************************************************
	*
	*                       Consultar bitacora de clientes 
	*/
	function cargaBitacoraClientes(){
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		$data['javascript_cache_version'] = $this->javascriptCacheVersion;
		$this->load->view('clientes/clientes_bitacora_view', $data);
	}

	function getClienteBitacora(){
		$retorno['status'] = 'error';
		$retorno['error'] = '1'; //No se proceso la solicitud
		if(isset($_POST['cedula'])){
			$cedula = $_POST['cedula']; 
			if(trim($cedula) == '1' || trim($cedula) == '0'){
				$retorno['error'] = '4'; //Error cliente contado y afiliado
			}else{
				if($clienteArray = $this->cliente->getClientes_Cedula($cedula)){
					foreach($clienteArray as $row){
						$cliente['nombre'] = $row-> Cliente_Nombre;
						$cliente['apellidos'] = $row-> Cliente_Apellidos;						
					}
					include PATH_USER_DATA;
					//Descuento con productos del cliente
					$bitacoraCliente = $this->cliente->obtenerBitacoraCliente($cedula); 					
					if($bitacoraCliente != false){	
						$listaRegistroBitacora = array();
						foreach($bitacoraCliente->result() as $registro){
							$pro['Fecha'] = $registro->Fecha;
							$pro['Nombre'] = $registro->Nombre;
							$pro['Cedula'] = $registro->Cedula;						
							$pro['Nombre_Usuario'] = $registro->Nombre_Usuario;
							$pro['Tipo_Transaccion'] = $registro->Tipo_Transaccion;							
							$pro['Descripcion'] = $registro->Descripcion;
							array_push($listaRegistroBitacora, $registro);
						}
						$cliente['bitacora'] = $listaRegistroBitacora;						
					}
					else{
						$retorno['error'] = '5'; //No existen registros de esa cédula
					}
					/// TODO SALIO BIEN
					$retorno['status'] = 'success';
					$retorno['cliente'] = $cliente;
				}else{
					$retorno['error'] = '3'; //Error no hay cliente
				}
			}
		}else{
			$retorno['error'] = '2'; //Error en la URL
		}
		echo json_encode($retorno);
	}	
}

?>