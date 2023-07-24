<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
class reportes extends CI_Controller {
	
	/*----------------------------PARAMETROS--------------------------------*/
	/*----------------------------------------------------------------------*/
	//Private $ruta = "http://localhost:8080/jasperserver/flow.html?_flowId=viewReportFlow&reportUnit=";	
	
	Private $ruta = "jasperserver/flow.html?_flowId=viewReportFlow&reportUnit=";	
	Private $IpInterna = "http://192.168.10.37:8181/"; 
	Private $IpExterna = "http://192.168.10.37:8181/"; 
	//Private $IpExterna = "http://201.200.125.10:8085/"; 
	Private $glCodigoSucGarotas = "2"; 
	//Private $IpInterna = "http://localhost:8080/";	 
	//Private $IpExterna = "http://localhost:8080/";
	Private $usuario = "j_username=gabo_vista_reportes"; 	
	Private $password = "j_password=f8SUOYv97Jh%5E*3gYk85B"; 	//Password codificado
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
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
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
	
	function usuarios(){
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
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
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		
		// parametros Obtenidos desde Interfaz 
		$fechaInicial = $this->input->post('fecha_inicial');
		$fechaFinal = $this->input->post('fecha_final');
		$Sucural = $this->input->post('sucursal');
		$Reporte =$this->input->post('tipo_reporte');	
        $EstadoFactura = $this->fnConstruirEstadoFactura($this->input->post('paEstadoFactura'));	        
		$EsSucursal = $this->input->post('paEsSucursal');
		$mFecha = $this->input->post('mFecha');
		if ($Sucural = 2){
			$Desamparados = "false";
			$GarotasBonitas = "true";	
		}
		else{
			if ($Sucural = 7){
				$Desamparados = "true";
				$GarotasBonitas = "false";			
			}
		}
		
		if($mFecha!=1 && $Reporte == 'ListaUsuario'){ // si se cumple no se toma en cuenta las fechas y se asgina null
			$fechaInicial = 'null';
			$fechaFinal = 'null'; 
		}
		// Parametros quemados en codigo 			
		$direccion = "/reports/Gabo/Usuarios/";
		$txtRutaFinal = "";
		$ip = ""; 
		if($data['Sucursal_Codigo'] == $this->glCodigoSucGarotas){
			$ip = $this->IpInterna; 
		}
		else {
			$ip = $this->IpExterna; 
		}
		if($Reporte != null){
			if($Reporte == 'ListaUsuario'){
				$parametro1 = "paSucursal=".$Sucural; 
				$parametro2 = "paFechaI=".$this->convertirFecha($fechaInicial, 0); 
				$parametro3 = "paFechaF=".$this->convertirFecha($fechaFinal, 1); 
				$txtRutaFinal = $ip.''.$this->ruta.''.$direccion.$Reporte.'&'.$this->usuario.'&'.$this->password.'&'.$parametro1.'&'.$parametro2.'&'.$parametro3;
			}
			if($Reporte == 'ListaDefacturasPorUsuario'){
				$parametro1 = "paSucursal=".$Sucural; 
				$parametro2 = "paFechaI=".$this->convertirFecha($fechaInicial, 0); 
				$parametro3 = "paFechaF=".$this->convertirFecha($fechaFinal, 1); 
				$parametro4 = "paEstadoFactura=".$EstadoFactura; 		
				if($EsSucursal != 1){
					$EsSucursal = 0; 
				}
				$parametro5 = "paEsSucursal=".$EsSucursal;
				$parametro6 = "paSuDesamparados=".$Desamparados;
				$parametro7 = "paSuGarotasBonitas=".$GarotasBonitas;
				$txtRutaFinal = $ip.''.$this->ruta.''.$direccion.$Reporte.'&'.$this->usuario.'&'.$this->password.'&'.$parametro1.'&'.$parametro2.'&'.$parametro3.'&'.$parametro4.'&'.$parametro5.'&'.$parametro6.'&'.$parametro7;
			}
			$this->llamarReporte($txtRutaFinal);
		}
	}

	
	
	
	/*----------------------------------------------------------------------*/
	/*---------------------REPORTES CLIENTES--------------------------------*/
	/*----------------------------------------------------------------------*/
	function reportesClientes(){
		$reportes = array(
						'null' =>'Seleccione',
						'VentaXClienteFacturas' =>'Venta por Clientes Facturación',
						'VentaXClienteFacturaResumido' => 'Venta Resumida por Clientes Facturación',
						'VentaXClienteProforma' => 'Venta por Clientes Proforma',
						'VentaXClienteProformaResumido' => 'Venta Resumida por Clientes Proforma',
						'ClienteEstado' => 'Mostrar Clientes por Estado',
						'ClientesXDescuento' => 'Clientes x Descuentos'
		);
		return $reportes;
	}
	
	function clientes(){
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
		if($permisos['consulta_administradores'])
		{
			$this->load->helper(array('form')); 
			$empresas_actuales = $this->empresa->get_empresas_ids_array();
			$data['Empresas'] = $empresas_actuales;
			$data['Reportes'] = $this->reportesClientes();
			$data['EstadoFacturas'] = $this->comboEstadosFactura();
			$data['Rangos'] = $this->comboRangos();
			$data['EstadoCliente'] = $this->estadosClientes();
			$this->load->view('reportes/reportes_clientes_view', $data);	
		}
		else{
		   redirect('accesoDenegado', 'location');
		}		
	}
	
	function clientesReportes(){
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		// parametros Obtenidos desde Interfaz 
		$fechaInicial = $this->input->post('fecha_inicial');
		$fechaFinal = $this->input->post('fecha_final');
		$Sucural = $this->input->post('sucursal');
		$Reporte =$this->input->post('tipo_reporte');	
        $EstadoFactura = $this->input->post('paEstadoFactura');	
		$EsSucursal = $this->input->post('paEsSucursal');
		$mNombre = $this->input->post('mNombre'); 
		$mCedula = $this->input->post('mCedula'); 
		$mRango = $this->input->post('mRango');
		
		$paNombre = $this->input->post('paNombre'); 
		$paCedula = $this->input->post('paCedula'); 
		$paRangoM = $this->input->post('rangoM');
		$paMontoI = $this->input->post('paMontoI');
		$paMontoF = $this->input->post('paMontoF');
		$paArticulo = $this->input->post('paArticulo'); 
		$paFamilia = $this->input->post('paFamilia');
		
		$paEstado = $this->input->post('paEstado'); 
		
		if($mNombre != 1){ $paNombre = 'null'; }
		if($mCedula != 1){ $paCedula = 'null'; }
		if($mRango != 1){ 
			$paRangoM = 'null'; 
			$paMontoI = 'null'; 
			$paMontoF = 'null'; 
		}
		if($paRangoM == 'menorIgual' || $paRangoM == 'mayorIgual'){
			$paMontoF = 'null'; 
		}
		// Parametros quemados en codigo 			
		$direccion = "/reports/Gabo/Clientes/";
		$txtRutaFinal = "";
		$ip = ""; 
		if($data['Sucursal_Codigo'] == $this->glCodigoSucGarotas){
			
			$ip = $this->IpInterna; 
		}
		else {
			$ip = $this->IpExterna; 
		}
		if($Reporte != null){
			$parametro1 = "paSucursal=".$Sucural; 
			$parametro2 = "paFechaI=".$this->convertirFecha($fechaInicial, 0); 
			$parametro3 = "paFechaF=".$this->convertirFecha($fechaFinal, 1); 
			if($Reporte == 'VentaXClienteProforma' || $Reporte =='VentaXClienteProformaResumido'){
				$parametro4 = "paEstadoProforma=".$EstadoFactura; 				
			}
			else{
				$parametro4 = "paEstadoFactura=".$EstadoFactura; 			
			}			
			if($EsSucursal != 1){
				$EsSucursal = 0; 
			}
			$parametro5 = "paEsSucursal=".$EsSucursal;
			$parametro6 = "paNombre=".$paNombre; 		
			$parametro7 = "paCedula=".$paCedula; 		
			$parametro8 = "paRango=".$paRangoM; 		
			$parametro9 = "paMontoI=".$paMontoI; 		
			$parametro10 = "paMontoF=".$paMontoF; 	
			$parametro11 = "paEstado=".$paEstado; 
			$parametro12 = "paArticulo=".$paArticulo; 
			$parametro13 = "paFamilia=".$paFamilia;
			if($Reporte == 'ClienteEstado'){
				$txtRutaFinal = $ip.''.$this->ruta.''.$direccion.$Reporte.'&'.$this->usuario.'&'.$this->password.'&'.$parametro11;			
			}	
			else{
				if($Reporte == 'ClientesXDescuento'){
						$txtRutaFinal = $ip.''.$this->ruta.''.$direccion.$Reporte.'&'.$this->usuario.'&'.$this->password.'&'.$parametro1.'&'.$parametro7.'&'.$parametro12.'&'.$parametro13;		
				}
				else{
					$txtRutaFinal = $ip.''.$this->ruta.''.$direccion.$Reporte.'&'.$this->usuario.'&'.$this->password.'&'.$parametro1.'&'.$parametro2.'&'.$parametro3.'&'.$parametro4.'&'.$parametro5.'&'.$parametro6.'&'.$parametro7.'&'.$parametro8.'&'.$parametro9.'&'.$parametro10;			
				}				
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
						'null' =>'Seleccione',
						'InventarioArticulos' =>'Inventario artículos',
						'CantArtVentaCliente' => 'Cantidad artículos Vendidos', 
						'ProcedenciaArticulo' => 'Procedencia artículo'
		);
		return $reportes;
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
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
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
	
	
	function articulosReportes(){
		include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
		// parametros Obtenidos desde Interfaz 
		$paReporte =$this->input->post('tipo_reporte');	
		$paSucursal = $this->input->post('sucursal');
		$paFamilia = $this->input->post('familia');		
		$paRangoC = $this->input->post('rangoCodigo');
		$paCodigoI = $this->input->post('CodigoI');
		$paCodigoF = $this->input->post('CodigoF');
		$paNumPrecio = $this->input->post('precio');
		$paRangoP = $this->input->post('rangoPrecios');
		$paPrecioI = $this->input->post('PrecioI');
		$paPrecioF = $this->input->post('PrecioF');
		$paRangoCant = $this->input->post('rangoArticulos');
		$paCantidadI = $this->input->post('CantidadI');
		$paCantidadF = $this->input->post('CantidadF');
		$paRangoDef = $this->input->post('rangoArticulosDef');
		$paCantidadDefI = $this->input->post('CantidadDefI');
		$paCantidadDefF = $this->input->post('CantidadDefF');
		$paExento = $this->input->post('paExento');
		
		$Desamparados = $this->obtenerCheck($this->input->post('check_Desamparados'));
		$GarotasBonitas = $this->obtenerCheck($this->input->post('check_GarotasBonitas'));
		$fechaInicial = $this->input->post('fecha_inicial');
		$fechaFinal = $this->input->post('fecha_final');
		$paCedula = $this->input->post('paCedula'); 
		$paCodigo = $this->input->post('Codigo'); 
		if(empty($paCedula)){
			$paCedula = "false";
		}
		
		
		//Switch para Rango Codigos
		if($paSucursal == ''){ $paSucursal = 'null';}	
		if($paExento != 1){ $paExento = 0;}		
		//if($paNumPrecio == ''){$paNumPrecio = 'null';}
		switch ($paRangoC) {
			case "null":
				$paCodigoI = "null";
				$paCodigoF = "null";
				break;
			case "menorIgual":
				$paCodigoF = "null";
				break;
			case "mayorIgual":
				$paCodigoF = "null";
				break;
			default;
		}
		//Switch para Rango Precios
		if($paNumPrecio != 'null'){
			switch ($paRangoP) {
				case "null":
					$paPrecioI = "null";
					$paPrecioF = "null";
					break;
				case "menorIgual":
					$paPrecioF = "null";
					break;
				case "mayorIgual":
					$paPrecioF = "null";
					break;
				default;
			}
		}
		else{
			$paRangoP = "null";
			$paPrecioI = "null";
			$paPrecioF = "null";
		}	
		//Switch para Rango Articulos
		switch ($paRangoCant) {
			case "null":
				$paCantidadI = "null";
				$paCantidadF = "null";
				break;
			case "menorIgual":
				$paCantidadF = "null";
				break;
			case "mayorIgual":
				$paCantidadF = "null";
				break;
			default;
		}
		//Switch para Rango Articulos Defectuosos 
		switch ($paRangoDef) {
			case "null":
				$paCantidadDefI = "null";
				$paCantidadDefF = "null";
				break;
			case "menorIgual":
				$paCantidadDefF = "null";
				break;
			case "mayorIgual":
				$paCantidadDefF = "null";
				break;
			default;
		}
		//Parametros quemados en codigo 			
		$direccion = "/reports/Gabo/Articulos/";
		$txtRutaFinal = "";
		$ip = ""; 
		if($data['Sucursal_Codigo'] == $this->glCodigoSucGarotas){
			$ip = $this->IpInterna; 
		}
		else {
			$ip = $this->IpExterna; 
		}
		if($paReporte != 'null' && $paReporte == "InventarioArticulos"){
			 $parametro1 = "paSucursal=".$paSucursal; 
			 $parametro2 = "paFamilia=".$paFamilia; 		
			 $parametro3 = "paRangoC=".$paRangoC; 		
			 $parametro4 = "paCodigoI=".$paCodigoI; 		
			 $parametro5 = "paCodigoF=".$paCodigoF; 		
			 $parametro6 = "paNumPrecio=".$paNumPrecio; 		
			 $parametro7 = "paRangoP=".$paRangoP; 		
			 $parametro8 = "paPrecioI=".$paPrecioI; 		
			 $parametro9 = "paPrecioF=".$paPrecioF; 		
			 $parametro10 = "paRangoCant=".$paRangoCant; 		
			 $parametro11 = "paCantidadI=".$paCantidadI; 		
			 $parametro12 = "paCantidadF=".$paCantidadF;
			 $parametro13 = "paRangoDef=".$paRangoDef;
			 $parametro14 = "paCantidadDefI=".$paCantidadDefI;
			 $parametro15 = "paCantidadDefF=".$paCantidadDefF;
			 $parametro16 = "paExento=".$paExento;		 
			 $txtRutaFinal = $ip.''.$this->ruta.''.$direccion.$paReporte.'&'.$this->usuario.'&'.$this->password.'&'.$parametro1.'&'.$parametro2.'&'.$parametro3.'&'.$parametro4.'&'.$parametro5.'&'.$parametro6;			
			 $txtRutaFinal = $txtRutaFinal.'&'.$parametro7.'&'.$parametro8.'&'.$parametro9.'&'.$parametro10.'&'.$parametro11.'&'.$parametro12.'&'.$parametro13.'&'.$parametro14.'&'.$parametro15.'&'.$parametro16;
		}	
		if($paReporte != 'null' && $paReporte == "CantArtVentaCliente"){ 
			$parametro1 = "paSucursal=".$paSucursal; 
			$parametro2 = "paFechaI=".$this->convertirFecha($fechaInicial, 0); 
			$parametro3 = "paFechaF=".$this->convertirFecha($fechaFinal, 1); 	
			$parametro4 = "paCodigoI=".$paCodigo; 
			$parametro5 = "paCedula=".$paCedula; 
			$parametro6 = "paSuDesamparados=".$Desamparados;
			$parametro7 = "paSuGarotasBonitas=".$GarotasBonitas;
			$txtRutaFinal = $ip.''.$this->ruta.''.$direccion.$paReporte.'&'.$this->usuario.'&'.$this->password.'&'.$parametro1.'&'.$parametro2.'&'.$parametro3.'&'.$parametro4.'&'.$parametro5.'&'.$parametro6.'&'.$parametro7;			
		}
		if($paReporte != 'null' && $paReporte == "ProcedenciaArticulo"){ 
			$parametro1 = "paSucursal=".$paSucursal; 
			$parametro2 = "paFechaI=".$this->convertirFecha($fechaInicial, 0); 
			$parametro3 = "paFechaF=".$this->convertirFecha($fechaFinal, 1); 	
			$parametro4 = "paCodigoI=".$paCodigo; 
			$txtRutaFinal = $ip.''.$this->ruta.''.$direccion.$paReporte.'&'.$this->usuario.'&'.$this->password.'&'.$parametro1.'&'.$parametro2.'&'.$parametro3.'&'.$parametro4;			
		}
		
		$this->llamarReporte($txtRutaFinal);
	}	
	
	
	/*----------------------------------------------------------------------*/
	/*---------------------REPORTES FACTURAS--------------------------------*/
	/*----------------------------------------------------------------------*/
	
	function reportesFacturas(){
		$reportes = array(
						'null' =>'Seleccione',
						'RentabilidadXCliente' => 'Rentabilidad por Clientes',
						'VentasXMes' => 'Ventas por Mes', 					
						'RecibosXDinero' => 'Recibos por Dinero', 
						'NotaCredito' => 'Notas Crédito',
						'Cartelera' => 'Cartera',
						'CarteleraTotalizacion' => 'Cartera Resumida',
						'ArticulosExentos' => 'Artículos Exentos',
		);
		return $reportes;
	}
		function facturas(){
			include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
			$permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);
			if($permisos['consulta_administradores'])
			{
				$this->load->helper(array('form')); 
				$empresas_actuales = $this->empresa->get_empresas_ids_array();
				$familias_actuales = $this->familia->get_familias_ids_array($data['Sucursal_Codigo']); 
				$data['Empresas'] = $empresas_actuales;
				$data['EstadoFacturas'] = $this->comboEstadosFactura();
				$data['TipoPagoFactura'] = $this->comboTipoPagoFactura(); 
				$data['Reportes'] = $this->reportesFacturas();
				//$data['Rangos'] = $this->comboRangos();
				//$data['Precios'] = $this->comboPrecios();
				$this->load->view('reportes/reportes_facturas_view', $data);	
			}
			else{
			   redirect('accesoDenegado', 'location');
			}		
		}
			
			
		 function facturasReporte(){
			 include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
			//parametros Obtenidos desde Interfaz 
			$fechaInicial = $this->input->post('fecha_inicial');
			$fechaFinal = $this->input->post('fecha_final');
			$Sucural = $this->input->post('sucursal');
			$paReporte =$this->input->post('tipo_reporte');	
			$EstadoFactura = $this->fnConstruirEstadoFactura($this->input->post('paEstadoFactura'));	
			$TipoPagoFactura = $this->fnConstruirTipoPagoFactura($this->input->post('paTipoPagoFactura'));
			$Desamparados = $this->obtenerCheck($this->input->post('check_Desamparados'));
			$GarotasBonitas = $this->obtenerCheck($this->input->post('check_GarotasBonitas'));
			$mNombre = $this->input->post('mNombre'); 
			$mCedula = $this->input->post('mCedula'); 
			$mPendiente = $this->obtenerCheck($this->input->post('mPendiente'));
			$paNombre = $this->input->post('paNombre'); 
			$paCedula = $this->input->post('paCedula'); 
			if($mNombre != 1){ $paNombre = 'null'; }
			if($mCedula != 1){ $paCedula = 'null'; }
			if($mPendiente != 1){ $mPendiente = 'null'; }
			//Parametros quemados en codigo 		
			$parametro1 = "paSucursal=".$Sucural; 
			$parametro2 = "paFechaI=".$this->convertirFecha($fechaInicial, 0); 
			$parametro3 = "paFechaF=".$this->convertirFecha($fechaFinal, 1); 
			$parametro4 = "paEstadoFactura=".$EstadoFactura; 
			$parametro5 = "paTipoPago=".$TipoPagoFactura; 
			$parametro6 = "paSuDesamparados=".$Desamparados;
			$parametro7 = "paSuGarotasBonitas=".$GarotasBonitas;
			$parametro8 = "paNombre=".$paNombre;
			$parametro9 = "paCedula=".$paCedula;
			$parametro10 = "paPendientes=".$mPendiente;
			$direccion = "/reports/Gabo/Facturas/";
			$txtRutaFinal = "";
			$ip = ""; 
			if($data['Sucursal_Codigo'] == 0){
				$ip = $this->IpInterna; 
			}
			else {
				$ip = $this->IpExterna; 
			}
			if ($paReporte == 'RentabilidadXCliente' || $paReporte == 'VentasXMes'){
				$txtRutaFinal = $ip.''.$this->ruta.''.$direccion.$paReporte.'&'.$this->usuario.'&'.$this->password.'&'.$parametro1.'&'.$parametro2.'&'.$parametro3.'&'.$parametro4.'&'.$parametro5.'&'.$parametro6.'&'.$parametro7;	
			}
			if ($paReporte == 'RecibosXDinero'){
				$txtRutaFinal = $ip.''.$this->ruta.''.$direccion.$paReporte.'&'.$this->usuario.'&'.$this->password.'&'.$parametro1.'&'.$parametro2.'&'.$parametro3.'&'.$parametro8.'&'.$parametro9.'&'.$parametro10;	
			}
			if ($paReporte == 'NotaCredito' || $paReporte == 'Cartelera'|| $paReporte == 'CarteleraTotalizacion'){
				$txtRutaFinal = $ip.''.$this->ruta.''.$direccion.$paReporte.'&'.$this->usuario.'&'.$this->password.'&'.$parametro1.'&'.$parametro2.'&'.$parametro3.'&'.$parametro6.'&'.$parametro7.'&'.$parametro8.'&'.$parametro9;	
			}			
			if ($paReporte == 'ArticulosExentos') {
				$txtRutaFinal = $ip.''.$this->ruta.''.$direccion.$paReporte.'&'.$this->usuario.'&'.$this->password.'&'.$parametro1.'&'.$parametro2.'&'.$parametro3;
			}	
			$this->llamarReporte($txtRutaFinal);
		 }		
		 
		 
		 
		 function fnConstruirEstadoFactura($Parametro){
		 $resultado = ""; 
		 if ($Parametro == 'todos')
		 {
			 $resultado = "'cobrada', 'anulada'";
		 }
		 else 
		 {
			 $resultado = "'".$Parametro."'";
		 }
		 return $resultado;
	 }
	 
	 function fnConstruirTipoPagoFactura($Parametro){
		 $resultado = ""; 
		 if ($Parametro == 'todos')
		 {
			 $resultado = "'contado', 'tarjeta', 'mixto', 'deposito', 'cheque', 'apartado', 'credito'";
		 }
		 else 
		 {
			 $resultado = "'".$Parametro."'";
		 }
		 return $resultado;
	 }
	
	/*----------------------------------------------------------------------*/
	/*-------------FUNCIONES GENERICAS PARA CONVERSIONES O UTILIDADES-------*/
	/*----------------------------------------------------------------------*/
	// Si se recibe en la variable $final = 0 es fecha Inicial si $final = 1 es la fecha final
	function convertirFecha($fecha, $final){
		//$fecha formato "DD-MM-YYYY"; 
		if($fecha !='null'){
			$newDate = date("Y-m-d", strtotime($fecha));
			if($final == 1){
				$newDate = $newDate." 23:59:59";
			}
			else{
				$newDate = $newDate." 00:00:00";
			}
			return $newDate;
		}
		return $fecha;
	}
	
	function llamarReporte($rutafinal){
		//	echo 'Prueba: '.$rutafinal; 
			header('Location:'.$rutafinal.'');	
	}
	
	function comboEstadosFactura(){
		$estadoFactura = array(
						'cobrada' => 'COBRADAS',
						'anulada' => 'ANULADAS',						
						'pendiente' => 'PENDIENTES', 
						'todos' => 'TODOS'
		);
		return $estadoFactura;
	}
	
	function comboTipoPagoFactura(){
		$tipoPagoFactura = array(
						'contado' => 'CONTADO',
						'tarjeta' => 'TARJETAS',
						'mixto' => 'PAGO MIXTO',
						'deposito' => 'DEPOSITO',
						'cheque' => 'CHEQUE',
						'apartado' => 'APARTADO',
						'credito' => 'CREDITO', 
						'todos' => 'TODOS'
		); 
		return $tipoPagoFactura; 
	}
	
	function estadosClientes(){
		$reportes = array(
						'activo' =>'Activo',
						'inactivo' => 'Inactivo',
						'semiactivo' => 'Semi Activo'
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
	
	function obtenerCheck($valor){
		if ($valor == 1){
			return 'true';
		}
		else{
			return 'false';
		}
	}

}

?>