<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class otros extends CI_Controller {
	 function __construct()
	 {
	    parent::__construct(); 
		$this->load->model('user','',TRUE);
		$this->load->model('cliente','',TRUE);
		$this->load->model('articulo','',TRUE);
		$this->load->model('familia','',TRUE);	
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			
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
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
			
		/*$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		
		if($permisos['otros_cliente'])
		{*/
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
					include '/../get_session_data.php';
					
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
							$pro['porcentaje'] = $producto -> Descuento_producto_porcentaje;
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
			include '/../get_session_data.php';
			
			if($this->cliente->existe_Cliente($cedula)){
				if($this->cliente->existeClienteDescuento($cedula, $data['Sucursal_Codigo'])){
					//Actualizamos descuento 
					$this->cliente->actualizarDescuentoCliente($descuento, $data['Sucursal_Codigo'], $cedula);
					$retorno['status'] = 'success';
				}else{
					//Agregamos descuento					
					$this->cliente->agregarDescuentoCliente($descuento, $data['Sucursal_Codigo'], $cedula);					
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
			include '/../get_session_data.php';
			
			if($this->cliente->existe_Cliente($cedula)){
				if($this->cliente->existeClienteCredito($cedula, $data['Sucursal_Codigo'])){
					//Actualizamos credito 
					$this->cliente->actualizarCreditoCliente($credito, $data['Sucursal_Codigo'], $cedula);
					$retorno['status'] = 'success';
				}else{
					//Agregamos credito					
					$this->cliente->agregarCreditoCliente($credito, $data['Sucursal_Codigo'], $cedula);					
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
			$idDescuentoProducto = $_POST['id'];
			$this->cliente->eliminarDescuentoProducto($idDescuentoProducto);
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
			include '/../get_session_data.php';
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
			include '/../get_session_data.php';
			if($pro = $this->articulo->get_Articulo($codigo,$data['Sucursal_Codigo'])){
				if($this->cliente->existe_Cliente($cedula)){
					if($this->cliente->existeDescuentoConProducto($codigo, $cedula, $data['Sucursal_Codigo']))
					{						
						$retorno['error'] = '7'; //Ya existe un descuento con ese producto y cliente y sucursal
					}else{
						foreach($pro as $row){
							$familia = $row -> TB_05_Familia_Familia_Codigo;
						}
						$this->cliente->agregarDescuentoDeProducto($codigo, $cedula, $data['Sucursal_Codigo'], $descuento, $familia);
						$retorno['status'] = 'success';	
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
			$idDescuentoFamilia = $_POST['id'];
			$this->cliente->eliminarDescuentoFamilia($idDescuentoFamilia);
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
			include '/../get_session_data.php';
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
			include '/../get_session_data.php';
			if($this->familia->getNombreFamiliaSucursal($codigo, $data['Sucursal_Codigo'])){
				if($this->cliente->existe_Cliente($cedula)){
					if($this->cliente->existeDescuentoConFamilia($codigo, $cedula, $data['Sucursal_Codigo']))
					{						
						$retorno['error'] = '9'; //Ya existe un descuento con esa familia y cliente y sucursal
					}else{						
						$this->cliente->agregarDescuentoDeFamilia($codigo, $cedula, $data['Sucursal_Codigo'], $descuento);
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
}

?>