<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
class reportes extends CI_Controller {
	
	/*----------------------------PARAMETROS--------------------------------*/
	/*----------------------------------------------------------------------*/
	//Private $ruta = "http://localhost:8085/jasperserver/flow.html?_flowId=viewReportFlow&reportUnit=";	
	Private $ruta = "jasperserver/flow.html?_flowId=viewReportFlow&reportUnit=";	
	Private $IpInterna = "http://192.168.1.6:8085/"; 
	Private $IpExterna = "http://201.200.125.10:8085/"; 
	//Private $IpInterna = "http://localhost:8085/"; 
	//Private $IpExterna = "http://localhost:8085/"; 
	Private $usuario = "j_username=jasperadmin"; 
	Private $password = "j_password=jasperadmin"; 	
	/*----------------------------------------------------------------------*/
	
	 function __construct()
	 {		 		
	    parent::__construct();
		$this->load->model('user','',TRUE);
		$this->load->model('empresa','',TRUE);	
		$this->load->model('familia','',TRUE);		
	 }

	 function index()
	 {
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion			
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);		
		if($permisos['consulta_administradores'])
		{
			redirect('accesoDenegado', 'location');
		}
		else{
			redirect('accesoDenegado', 'location');
		}
	}
	
	/*----------------------------------------------------------------------*/
	/*---------------------REPORTES USUARIOS--------------------------------*/
	/*----------------------------------------------------------------------*/
	/*Funcionalidad: metodo que devuelve la información para cargar el combobox de
	Reportes de Usuarios*/
	function reportesUsuarios(){
		$reportes = array(
						'null' =>'Seleccione',
						'ListaUsuario' =>'Lista General Usuario',
						'ListaDefacturasPorUsuario' => 'Lista Facturas por Usuario'
		);
		return $reportes;
	}
	
	function comboEstadosFactura(){
		$estadoFactura = array(
						'cobrada' => 'COBRADAS',
						'anulada' => 'ANULADAS',						
						'pendiente' => 'PENDIENTES'
		);
		return $estadoFactura;
	}
	
	function usuarios(){
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		if($permisos['consulta_administradores'])
		{
			$this->load->helper(array('form')); 
			$empresas_actuales = $this->empresa->get_empresas_ids_array();
			$data['Empresas'] = $empresas_actuales;
			$data['Reportes'] = $this->reportesUsuarios();
			$data['EstadoFacturas'] = $this->comboEstadosFactura();
			$this->load->view('reportes/reportes_usuarios_view', $data);	
		}
		else{
		   redirect('accesoDenegado', 'location');
		}		
	}
	
	function usuariosReporte(){
		include '/../get_session_data.php'; //Esto es para traer la informacion de la session
		
		// parametros Obtenidos desde Interfaz 
		$fechaInicial = $this->input->post('fecha_inicial');
		$fechaFinal = $this->input->post('fecha_final');
		$Sucural = $this->input->post('sucursal');
		$Reporte =$this->input->post('tipo_reporte');	
        $EstadoFactura = $this->input->post('paEstadoFactura');	
		$EsSucursal = $this->input->post('paEsSucursal');
		$mFecha = $this->input->post('mFecha');
		if($mFecha!=1 && $Reporte == 'ListaUsuario'){ // si se cumple no se toma en cuenta las fechas y se asgina null
			$fechaInicial = 'null';
			$fechaFinal = 'null'; 
		}
		// Parametros quemados en codigo 			
		$direccion = "/reports/Gabo/Usuarios/";
		$txtRutaFinal = "";
		$ip = ""; 
		if($data['Sucursal_Codigo'] == 0){
			$ip = $this->IpInterna; 
		}
		else {
			$ip = $this->IpExterna; 
		}
		
		if($Reporte != null){
			if($Reporte == 'ListaUsuario'){
				$parametro1 = "paSucursal=".$Sucural; 
				$parametro2 = "paFechaI=".$this->convertirFecha($fechaInicial); 
				$parametro3 = "paFechaF=".$this->convertirFecha($fechaFinal); 
				$txtRutaFinal = $ip.''.$this->ruta.''.$direccion.$Reporte.'&'.$this->usuario.'&'.$this->password.'&'.$parametro1.'&'.$parametro2.'&'.$parametro3;
			}
			if($Reporte == 'ListaDefacturasPorUsuario'){
				$parametro1 = "paSucursal=".$Sucural; 
				$parametro2 = "paFechaI=".$this->convertirFecha($fechaInicial); 
				$parametro3 = "paFechaF=".$this->convertirFecha($fechaFinal); 
				$parametro4 = "paEstadoFactura=".$EstadoFactura; 		
				if($EsSucursal != 1){
					$EsSucursal = 0; 
				}
				$parametro5 = "paEsSucursal=".$EsSucursal;
				$txtRutaFinal = $ip.''.$this->ruta.''.$direccion.$Reporte.'&'.$this->usuario.'&'.$this->password.'&'.$parametro1.'&'.$parametro2.'&'.$parametro3.'&'.$parametro4.'&'.$parametro5;
			}
			$this->llamarReporte($txtRutaFinal);
		}
	
	}
	
	/*----------------------------------------------------------------------*/
	/*---------------------REPORTES ARTICULOS-------------------------------*/
	/*----------------------------------------------------------------------*/
	
	/*Funcionalidad: metodo que devuelve la información para cargar el combobox de
	Reportes de Articulos*/
	function reportesArticulos(){
		$reportes = array(
						'ListaArticulos1' =>'ListaArticulos1Sivi',
						'ListaArticulos2' => 'ListaArticulos2Sivi',						
						'ListaArticulos3' =>'ListaArticulos3Sivi'
		);
		return $reportes;
	}
	
	function comboRangos(){
		$rangos = array(
						'null' => 'SELECCIONE',
						'menorIgual' => 'MENOR O IGUAL <=',
						'mayorIgual' => 'MAYOR O IGUAL >=',						
						'between' => 'RANGOS'
		);
		return $rangos;
	}
	function comboPrecios(){
		$rangos = array(
						'null' => 'SELECCIONE',
						'1' => 'Precio 1',
						'2' => 'Precio 2',						
						'3' => 'Precio 3',
						'4' => 'Precio 4',
						'5' => 'Precio 5',
		);
		return $rangos;
	}
	
	function articulos(){
		include '/../get_session_data.php'; //Esto es para traer la informacion de la sesion
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		if($permisos['consulta_administradores'])
		{
			$this->load->helper(array('form')); 
			$empresas_actuales = $this->empresa->get_empresas_ids_array();
			$familias_actuales = $this->familia->get_familias_ids_array($data['Sucursal_Codigo']); 
			$data['Empresas'] = $empresas_actuales;
			$data['Familias'] = $familias_actuales; 
			$data['Reportes'] = $this->reportesArticulos();
			$data['Rangos'] = $this->comboRangos();
			$data['Precios'] = $this->comboPrecios();
			$this->load->view('reportes/reportes_articulos_view', $data);	
		}
		else{
		   redirect('accesoDenegado', 'location');
		}		
	}
	
	
	/*----------------------------------------------------------------------*/
	/*-------------FUNCIONES GENERICAS PARA CONVERSIONES O UTILIDADES-------*/
	/*----------------------------------------------------------------------*/
	function convertirFecha($fecha){
		//$fecha formato "DD-MM-YYYY"; 
		if($fecha !='null'){
			$newDate = date("Y-m-d", strtotime($fecha));
			$newDate = $newDate." 00:00:00";
			return $newDate;
		}
		return $fecha;
	}
	
	function llamarReporte($rutafinal){
		//	echo 'Prueba: '.$rutafinal; 
			header('Location:'.$rutafinal.'');	
	}

}

?>