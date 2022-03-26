<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class inventario extends CI_Controller {

    public $usuario;

	function __construct(){
		parent::__construct();
		$this->checkPermission("control_inventario");

		$this->load->model('empresa','',TRUE);
		$this->load->model('articulo','',TRUE);
		$this->load->model('user','',TRUE);
		$this->load->model('configuracion','',TRUE);
		$this->load->model('pdfgenerator','',TRUE);
	}

	function index(){}

	function control(){
		$sucursales = $this->empresa->get_empresas_ids_array();
		$this->userdata_wrap['sucursales'] = $sucursales;
		$this->userdata_wrap['javascript_cache_version'] = $this->javascriptCacheVersion;
		$conf_array = $this->configuracion->getConfiguracionArray();
		$this->userdata_wrap['decimales'] = intval($conf_array["cantidad_decimales"]);
		$this->load->view('articulos/inventario/control', $this->userdata_wrap);
	}

	function getArticulo(){
		$codigo = trim(@$_POST["codigo"]);
		$sucursal = trim(@$_POST["sucursal"]);
		$r = $this->getDefaultResponse();

		if($codigo !== ""){
			if($articulo = $this->articulo->getArticuloParaControlInventario($codigo, $sucursal)){
				$r->code = 0;
				$r->msg = "";
				$articulo->codigo = $codigo;
				$r->data = $articulo;
			}else{
				$r->code = 3;
				$r->msg = "Artículo no existe";
			}
		}else{
			$r->code = 2;
			$r->msg = "Código no puede ser vacío";
		}

		echo json_encode($r);
	}

	function getArticulos(){
		$sucursal = trim(@$_POST["sucursal"]);
		$r = $this->getDefaultResponse();

		if($sucursal !== ""){
			if($articulos = $this->articulo->getArticulosConInventarioParaControlDeInventario($sucursal)){
				$r->code = 0;
				$r->msg = "";
				for($i = 0; $i < sizeof($articulos); $i++){
					$articulos[$i]->fbueno = 0;
					$articulos[$i]->fdefectuoso = 0;
					$articulos[$i]->bueno = intval($articulos[$i]->bueno);
					$articulos[$i]->defectuoso = intval($articulos[$i]->defectuoso);
					$articulos[$i]->empatar = false;
				}
				$r->data = $articulos;
			}else{
				$r->code = 3;
				$r->msg = "No hay artículos con inventario en dicha sucursal";
			}
		}else{
			$r->code = 2;
			$r->msg = "Sucursal no puede ser vacío";
		}

		echo json_encode($r);
	}

	public function autorizar(){
		$user = trim(@$_POST["user"]);
		$pass = trim(@$_POST["pass"]);

		$r = $this->getDefaultResponse();
		if($usuario = $this->user->login($user, $pass, false)){
			$usuario = $usuario[0];
			if($this->user->hasPermission($usuario->Usuario_Codigo, $usuario->TB_02_Sucursal_Codigo, "empatar_inventario")){
				$r->code = 0;
				$r->msg = "";
				$r->data = array("usuario"=>$usuario->Usuario_Codigo, "sucursal"=>$usuario->TB_02_Sucursal_Codigo, "otorgado"=>true);
			}else{
				$r->code = 3;
				$r->msg = "Usuario no tiene permisos para realizar esta operación";
			}
		}else{
			$r->code = 2;
			$r->msg = "Usuario o contraseña incorrecta";
		}

		echo json_encode($r);
	}

	public function generar(){
		$articulos = json_decode(trim(@$_POST["articulos"]), true);
		$autorizacion = json_decode(trim(@$_POST["autorizacion"]), true);
		$sucursal = trim(@$_POST["sucursal"]);

		$r = $this->getDefaultResponse();
		if($this->empresa->getEmpresa($sucursal)){
			if(sizeof($articulos) > 0){
				$autorizado = false;
				$requiereAutorizacion = $this->hayQueEmpatarArticulos($articulos);
				if($requiereAutorizacion){
					$codigoUserAutoriza = @$autorizacion["usuario"];
					$sucursalUserAutoriza = @$autorizacion["sucursal"];
					if($this->user->hasPermission($codigoUserAutoriza, $sucursalUserAutoriza, "empatar_inventario")){
						$autorizado = true;
					}
				}

				if(($autorizado && $requiereAutorizacion) || ($autorizado == false && $requiereAutorizacion == false)){
					//Creamos el control como tal
					if($idControl = $this->articulo->generarControlInventario($sucursal, $this->userdata_nombre, $requiereAutorizacion ? @$autorizacion["usuario"] : $this->userdata_nombre)){
						//Agregamos los articulos al control
						foreach($articulos as $art){
							$this->articulo->agregarArticuloControlInventario($idControl, $art["codigo"], $art["descripcion"], $art["fbueno"], $art["fdefectuoso"], $art["bueno"], $art["defectuoso"], isset($art["empatar"]) ? $art["empatar"] : false, $art["costo"]);
							//Si hay que empatar entonces cambiamos el inventario
							if(isset($art["empatar"]) && $art["empatar"]){
								$artUpdate = array(
									"Articulo_Cantidad_Inventario"=> $art["fbueno"],
									"Articulo_Cantidad_Defectuoso" => $art["fdefectuoso"]);
								$this->articulo->actualizar($art["codigo"], $sucursal, $artUpdate);
								$this->user->guardar_transaccion($this->userdata_nombre, "Empato el articulo cod: ".$art["codigo"].", control: ".$idControl, $this->userdata_sucursal, 'empatar articulo');
							}
						}
						$this->user->guardar_transaccion($this->userdata_nombre, "Genero el control de inventario id: ".$idControl, $this->userdata_sucursal, 'control inventario');
						$r->code = 0;
						$r->msg = "";
						$r->data = array("control"=>$idControl);
					}else{
						$r->code = 5;
						$r->msg = "No se pudo crear el control de inventario";
					}
				}else{
					$r->code = 4;
					$r->msg = "El usuario ingresado para autorizar no tiene permisos o no existe";
				}
			}else{
				$r->code = 3;
				$r->msg = "Debe agregar al menos un artículo al control de inventario";
			}
		}else{
			$r->code = 2;
			$r->msg = "Sucursal ingresada no existe";
		}

		echo json_encode($r);
	}

	private function hayQueEmpatarArticulos($articulos){
		foreach($articulos as $art){
			if(isset($art["empatar"]) && $art["empatar"]){
				return true;
			}
		}
		return false;
	}

	function consulta(){
		$sucursales = $this->empresa->get_empresas_ids_array();
		$this->userdata_wrap['sucursales'] = $sucursales;
		$this->userdata_wrap['javascript_cache_version'] = $this->javascriptCacheVersion;
		$conf_array = $this->configuracion->getConfiguracionArray();
		$this->userdata_wrap['decimales'] = intval($conf_array["cantidad_decimales"]);
		$this->load->view('articulos/inventario/consulta', $this->userdata_wrap);
	}

	public function getControles(){
		$sucursal = trim(@$_POST["sucursal"]);
		$desde = trim(@$_POST["desde"]);
		$hasta = trim(@$_POST["hasta"]);
		$r = $this->getDefaultResponse();

		if($sucursal !== ""){
			if($controles = $this->articulo->getControlesInventarioParaConsulta($sucursal, $desde, $hasta)){
				$r->code = 0;
				$r->msg = "";
				$r->data = array("controles"=>$controles);
			}else{
				$r->code = 3;
				$r->msg = "No hay controles de inventario con los filtros seleccionados";
			}
		}else{
			$r->code = 2;
			$r->msg = "Sucursal no puede ser vacío";
		}

		echo json_encode($r);
	}

	public function getControl(){
		$consecutivo = trim(@$_POST["consecutivo"]);
		$r = $this->getDefaultResponse();

		if(is_numeric($consecutivo)){
			if($control = $this->articulo->getControlInventario($consecutivo)){
				if($articulos = $this->articulo->getArticulosControlInventario($consecutivo)){
					$r->code = 0;
					$r->msg = "";
					$r->data = array("control"=>$control, "articulos"=>$articulos);
				}else{
					$r->code = 4;
					$r->msg = "No existen artículos para el control de inventario solicitado";
				}
			}else{
				$r->code = 3;
				$r->msg = "No existe control de inventario con dicho consecutivo";
			}
		}else{
			$r->code = 2;
			$r->msg = "El consecutivo no es válido";
		}

		echo json_encode($r);
	}

	public function descarga(){
		$consecutivo = trim(@$_GET["c"]);
		$sucursal = trim(@$_GET["s"]);
		$tipo = trim(@$_GET["t"]);
		if($empresa = $this->empresa->getEmpresa($sucursal)){
			if($control = $this->articulo->getControlInventario($consecutivo)){
				if($articulos = $this->articulo->getArticulosControlInventario($consecutivo)){
					if($tipo == "excel" || $tipo == "pdf"){
						if($tipo == "pdf"){
							$this->createPDFControl($empresa[0], $control, $articulos);
						}else if($tipo == "excel"){

						}
					}else{
						die('Formato de archivo invalido');
					}
				}else{
					die('Articulos no existen');
				}
			}else{
				die('Control no existe');
			}
		}else{
			die('Empresa no existe');
		}
	}

	private function createPDFControl($sucursal, $controlHead, $controlArticulos){
		$pdf = $this->pdfgenerator->generatePDF();

		//FOR TESTING
		$controlArticulos = array_merge($controlArticulos, $controlArticulos);
		$controlArticulos = array_merge($controlArticulos, $controlArticulos);
		$controlArticulos = array_merge($controlArticulos, $controlArticulos);
		$controlArticulos = array_merge($controlArticulos, $controlArticulos);
		$controlArticulos = array_merge($controlArticulos, $controlArticulos);
		$controlArticulos = array_merge($controlArticulos, $controlArticulos);

		$articulosPorPagina = 50;

		$cantidadProductos = sizeof($controlArticulos);
		$paginasADibujar = $this->pdfgenerator->getAmountPagesToDraw($cantidadProductos, $articulosPorPagina);
        $paginaActual = 1;

		$datos = new stdClass();
		$datos->empresa = $sucursal;
		$datos->control = $controlHead;
		$datos->paginaActual = $paginaActual;
		$datos->cantidadPaginas = $paginasADibujar;
		$datos->usuarioQueRealiza = $this->user->getUserById($controlHead->Creado_Por);
		$datos->usuarioQueEmpata = $this->user->getUserById($controlHead->Empate_Autorizado_Por);

		//var_dump($paginasADibujar);

		$titles = array(
			array("title"=>"Código","width"=>19),
			array("title"=>"Descripción","width"=>76),
			array("title"=>"E","width"=>5),
			array("title"=>"F Bueno","width"=>15),
			array("title"=>"S Bueno","width"=>15),
			array("title"=>"B Bueno","width"=>15),
			array("title"=>"F Defec","width"=>15),
			array("title"=>"S Defec","width"=>15),
			array("title"=>"B Defec","width"=>15)
		);

		$totales = $this->generarCostos($controlArticulos);

		$values = array(
			array("title"=>"Bueno", "content"=>$totales["bueno"]),
			array("title"=>"Defectuoso", "content"=>$totales["defectuoso"]),
			array("title"=>"Total", "content"=>$totales["total"])
		);



		//var_dump($datos);
		//$numeroPagina = 0;
		$inicio = 0;
		$final = $articulosPorPagina - 1;
       	while($paginasADibujar >= $paginaActual){
			//Agregamos pag
			$pdf->AddPage();

			$this->pdfgenerator->drawHeader($pdf, CONTROL_DE_INVENTARIO, $datos)
								->drawProductsContainer($pdf, $titles, 55, 204, 261)
								->drawFooter($pdf, CONTROL_DE_INVENTARIO, $values, 261);

			$this->addArticulosToPDF($pdf, $controlArticulos, $inicio, $final);

			$inicio +=  $articulosPorPagina - 1;
			$final +=  $articulosPorPagina - 1;
			$final = $final > sizeof($controlArticulos) ? sizeof($controlArticulos) : $final;

			$paginaActual++;
			//$numeroPagina++;
        }

		//Imprimimos documento
		$pdf->Output();
	}

	private function generarCostos($articulos){
		$totales = array("bueno"=>0,"defectuoso"=>0,"total"=>0);

		foreach($articulos as $articulo){
			$totales["bueno"] += floatval($articulo->Costo) * intval($articulo->Fisico_Bueno);
			$totales["defectuoso"] += floatval($articulo->Costo) * intval($articulo->Fisico_Defectuoso);
			$totales["total"] += floatval($articulo->Costo) * intval($articulo->Fisico_Bueno) + floatval($articulo->Costo) * intval($articulo->Fisico_Defectuoso);
		}

		return $totales;
	}

	private function addArticulosToPDF(&$pdf, $articulos, $inicio, $final){
		$y = 65;
		for($i = $inicio; $i < $final; $i++){
			$articulo = $articulos[$i];
			$item = array(
				array("content"=>$articulo->Codigo, "width"=>19, "align"=>"C"),
				array("content"=>$articulo->Descripcion, "width"=>76, "align"=>"L"),
				array("content"=>$this->fe($articulo->Empatar), "width"=>5, "align"=>"C"),
				array("content"=>$articulo->Fisico_Bueno, "width"=>15, "align"=>"R"),
				array("content"=>$articulo->Sistema_Bueno, "width"=>15, "align"=>"R"),
				array("content"=>intval($articulo->Sistema_Bueno) - intval($articulo->Fisico_Bueno), "width"=>15, "align"=>"R"),
				array("content"=>$articulo->Fisico_Defectuoso, "width"=>15, "align"=>"R"),
				array("content"=>$articulo->Sistema_Defectuoso, "width"=>15, "align"=>"R"),
				array("content"=>intval($articulo->Sistema_Defectuoso) - intval($articulo->Fisico_Defectuoso), "width"=>15, "align"=>"R")
			);
			$this->pdfgenerator->addItemToBody($pdf, $item, $y);
			$y += 4;
		}
	}

	private function fni($numero){
		return number_format($numero,$this->configuracion->getDecimales());
	}

	private function fe($valor){
			if($valor){
					return 'E';
			}else{
					return ' ';
			}
	}
}