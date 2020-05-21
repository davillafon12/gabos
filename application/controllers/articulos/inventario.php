<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class inventario extends CI_Controller {

    public $usuario;

	function __construct(){
		parent::__construct();
		$this->checkPermission("control_inventario");

		$this->load->model('empresa','',TRUE);
		$this->load->model('articulo','',TRUE);
	}

	function index(){}

	function control(){
		$sucursales = $this->empresa->get_empresas_ids_array();
		$this->userdata_wrap['sucursales'] = $sucursales;
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

}