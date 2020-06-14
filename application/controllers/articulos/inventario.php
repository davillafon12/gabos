<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class inventario extends CI_Controller {

    public $usuario;

	function __construct(){
		parent::__construct();
		$this->checkPermission("control_inventario");

		$this->load->model('empresa','',TRUE);
		$this->load->model('articulo','',TRUE);
		$this->load->model('user','',TRUE);
	}

	function index(){}

	function control(){
		$sucursales = $this->empresa->get_empresas_ids_array();
		$this->userdata_wrap['sucursales'] = $sucursales;
		$this->userdata_wrap['javascript_cache_version'] = $this->javascriptCacheVersion;
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
							$this->articulo->agregarArticuloControlInventario($idControl, $art["codigo"], $art["descripcion"], $art["fbueno"], $art["fdefectuoso"], $art["bueno"], $art["defectuoso"], $art["empatar"]);
							//Si hay que empatar entonces cambiamos el inventario
							if($art["empatar"]){
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
			if($art["empatar"]){
				return true;
			}
		}
		return false;
	}

}