<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class bodega extends CI_Controller {

	function __construct()
	{
		parent::__construct(); 
		
		$this->load->model('bodega_m','',TRUE);
		$this->load->model('user','',TRUE);	
		$this->load->model('empresa','',TRUE);	
	}

	function index()
	{
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion

		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

		if(!$permisos['ingreso_bodega'])
		{	
			redirect('accesoDenegado', 'location');
		}else{	
			$this->load->helper(array('form'));
			$data['nueva_subida'] = true;
			$empresas_actuales = $this->empresa->get_empresas_ids_array();
			$data['Familia_Empresas'] = $empresas_actuales;		
			$this->load->view('articulos/articulos_ingreso_bodega_view', $data);
		}
	}
	
	function cargar(){
		//print_r($_FILES);
		include PATH_USER_DATA;
		if(isset($_FILES['archivo_excel'])&&isset($_POST['sucursal'])){	
			$sucursal = $_POST['sucursal'];
			if($this->empresa->getEmpresa($sucursal)){		
				$resultado = $this->procesarExcel();
				//print_r($resultado);
				if($resultado['status']=='success'){
					//Verificamos que no hayan erroes, si los hay no procesar nada
					if(!sizeOf($resultado['erroresCantidad'])>0 && !sizeOf($resultado['erroresCosto'])>0){
						$articulos = $resultado['articulos'];
						foreach($articulos as $articulo){
							//Filtramos que el articulo no este dentro de los articulos con errores
							if(!in_array($articulo['cod'], $resultado['erroresCantidad']) && !in_array($articulo['cod'], $resultado['erroresCosto'])){
								//echo "<br>".$articulo['cod'];
								
								date_default_timezone_set("America/Costa_Rica");
								$fecha = date(DB_DATETIME_FORMAT, now());
								
								//Se agrega a bodega para validar a la hora del traspaso
								if($this->bodega_m->existeArticuloEnBodega($articulo['cod'], $sucursal)){
									//Si existe actualizamos
									$this->bodega_m->actualizarArticulo($articulo['cod'], $articulo['des'], $articulo['cos'], $articulo['can'], $sucursal);
								}else{
									//Si no existe lo agregamos
									$this->bodega_m->agregarArticulo($articulo['cod'], $articulo['des'], $articulo['cos'], $articulo['can'], $data['Usuario_Codigo'], $sucursal);
								}								
								
								//Se agrega como compra para generar reportes y llevar el record de compras
								$this->bodega_m->agregarCompra($articulo['cod'], $articulo['des'], $articulo['cos'], $articulo['can'], $fecha, $data['Usuario_Codigo'], $sucursal);
								
								$this->user->guardar_transaccion($data['Usuario_Codigo'], "El usuario ingresó a bodega/compra el articulo: ".$articulo['cod']." en la sucursal: ".$sucursal,$data['Sucursal_Codigo'],'bodega');
							}
						}
						//Todo salio bien
						redirect('articulos/bodega?s=1', 'location');
					}else{
						//Error en ciertos articulos
						//echo "Error en ciertos articulos";
						$this->load->helper(array('form'));
						$data['error'] = '5';
						$data['msj'] = 'Algunos artículos presentan problemas';
						$data['errorCosto'] = $resultado['erroresCosto'];
						$data['errorCantidad'] = $resultado['erroresCantidad'];
						$empresas_actuales = $this->empresa->get_empresas_ids_array();
						$data['Familia_Empresas'] = $empresas_actuales;		
						$this->load->view('articulos/articulos_ingreso_bodega_view', $data);
					}
				}else{
					if($resultado['error']=='1'){
						//echo "No se pudo leer y procesar el excel";
						$this->load->helper(array('form'));
						$data['error'] = '4';
						$data['msj'] = 'No se pudo procesar el archivo excel';
						$empresas_actuales = $this->empresa->get_empresas_ids_array();
						$data['Familia_Empresas'] = $empresas_actuales;		
						$this->load->view('articulos/articulos_ingreso_bodega_view', $data);
					}else if($resultado['error']=='2'){
						//echo "Columnas requeridas no vienen o estan en mal formato";
						$this->load->helper(array('form'));
						$data['error'] = '3';
						$data['msj'] = 'Columnas no válidas, o no están en orden';
						$empresas_actuales = $this->empresa->get_empresas_ids_array();
						$data['Familia_Empresas'] = $empresas_actuales;		
						$this->load->view('articulos/articulos_ingreso_bodega_view', $data);
					}
				}
			}else{
				//Sucursal no existe				
				$this->load->helper(array('form'));
				$data['error'] = '2';
				$data['msj'] = 'Sucursal seleccionada no existe';
				$empresas_actuales = $this->empresa->get_empresas_ids_array();
				$data['Familia_Empresas'] = $empresas_actuales;		
				$this->load->view('articulos/articulos_ingreso_bodega_view', $data);
			}			
		}else{
			//URL Mala
			//echo "URL mala";
			$this->load->helper(array('form'));
			$data['error'] = '1';
			$data['msj'] = 'La URL está incompleta, contacte al administrador';
			$empresas_actuales = $this->empresa->get_empresas_ids_array();
			$data['Familia_Empresas'] = $empresas_actuales;		
			$this->load->view('articulos/articulos_ingreso_bodega_view', $data);
		}		
	}	
	
	private function procesarExcel(){
		$resultado = array('status'=>'error','error'=>'1'); //Error generico de no se pudo realizar el proceso
		require_once './application/libraries/PHPExcel/IOFactory.php';
    	$objPHPExcel = PHPExcel_IOFactory::load($_FILES['archivo_excel']['tmp_name']);
		$cantidadHojas = 1; //Para que solo procese la primera hoja del excel
		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
			if($cantidadHojas == 1){
				$cantidadHojas++;
				//Probamos que el orden de las columnas sea el requerido
				$celda01 = $worksheet->getCellByColumnAndRow(0, 1)->getValue();
				$celda11 = $worksheet->getCellByColumnAndRow(1, 1)->getValue();
				$celda21 = $worksheet->getCellByColumnAndRow(2, 1)->getValue();
				$celda31 = $worksheet->getCellByColumnAndRow(3, 1)->getValue();
				
				if(trim($celda01) == 'CODIGO BRASIL' && trim($celda11) == 'DESCRIPCION' && trim($celda21) == 'COSTO' && trim($celda31) == 'CANTIDAD'){
					$highestRow = $worksheet->getHighestRow();
					//Lleva el control de cuales productos presentaron errores
					$erroresCosto = array();
					$erroresCantidad = array();
					$articulos = array();
					for ($row = 2; $row <= $highestRow; ++ $row){ 
						$codigoBra = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
						$descripcion = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
						$costo = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
						$cantidad = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
						
						if(!is_numeric($costo)){
							array_push($erroresCosto, $codigoBra);
						}
						if(!is_numeric($cantidad)){
							array_push($erroresCantidad, $codigoBra);
						}
						
						if(trim($codigoBra)!=''){ //Si la fila no es vacia
							array_push($articulos, array("cod"=>$codigoBra,"des"=>$descripcion,"cos"=>$costo,"can"=>$cantidad));
						}
					}
					$resultado["status"] = "success";
					unset($resultado["error"]);
					$resultado["articulos"] = $articulos;
					$resultado["erroresCantidad"] = $erroresCantidad;
					$resultado["erroresCosto"] = $erroresCosto;
				}else{
					//No tiene las columnas requeridas
					$resultado['error'] = '2';
				}
			}			
		}
		return $resultado;
	}
	
	function existeEnBodega(){
		$retorno['status'] = 'error';
		if(isset($_POST['codigo'])&&isset($_POST['sucursal'])){
			if($articulos = $this->bodega_m->existeArticuloEnBodega($_POST['codigo'], $_POST['sucursal'])){
				$retorno['status'] = 'success';
				foreach($articulos as $articulo){
					$retorno['cantidad'] = $articulo->Cantidad;
				}
			}
		}
		echo json_encode($retorno);
	}

}

?>