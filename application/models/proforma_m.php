<?php 
Class proforma_m extends CI_Model
{
	
	function getConsecutivo($id_empresa) //Traer el siguiente consecutivo de una empresa en particular
	{
		return $this->getConsecutivoUltimaProforma($id_empresa)+1;
	}
	
	
	function getConsecutivoUltimaProforma($sucursal)
	{
		$this -> db -> select('Proforma_Consecutivo');
		$this -> db -> from('TB_10_Proforma');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> order_by("Proforma_Consecutivo", "desc");
		$this -> db -> limit(1);
		$query = $this -> db -> get();
		/*$query ="select Factura_Consecutivo from TB_07_Factura order by Factura_Consecutivo DESC limit 1";
        $res = $this->db->query($query);*/
	
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{
			$consecutivo;
			$result = $query->result();
			foreach($result as $row)
			{$consecutivo=$row->Proforma_Consecutivo;}
			return $consecutivo;
		}
	}
	
	function crearProforma($cedula, $nombre, $currency, $observaciones, $sucursal, $vendedor){
		$c_array = $this->getConfgArray();
		$sucursalVendedor =  $sucursal;
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es una sucursal con trueque
				$sucursal = $this->sucursales_trueque[$sucursal];
				$this->truequeAplicado = true;
		}
		if($consecutivo = $this->getConsecutivo($sucursal)){
			//return $consecutivo;
			date_default_timezone_set("America/Costa_Rica");
			$Current_datetime = date("y/m/d : H:i:s", now());
			$this->load->model('cliente','',TRUE);
			$clienteArray = $this->cliente->getNombreCliente($cedula);
			$dataProforma = array(
	                        'Proforma_Consecutivo'=>mysql_real_escape_string($consecutivo),
	                        'Proforma_Observaciones'=>mysql_real_escape_string($observaciones), 
													'Proforma_Estado'=>'sin_procesar',
													'Proforma_Moneda'=>mysql_real_escape_string($currency),
													'Proforma_Porcentaje_IVA'=>$c_array['iva'],
													'Proforma_Tipo_Cambio'=>$c_array['dolar_venta'],
													'Proforma_Nombre_Cliente'=>mysql_real_escape_string($nombre),
													'Proforma_Fecha_Hora'=>$Current_datetime,
													'TB_02_Sucursal_Codigo'=>$sucursal,
													'Proforma_Vendedor_Codigo'=>$vendedor,	
													'Proforma_Vendedor_Sucursal'=>$sucursalVendedor,	
													'TB_03_Cliente_Cliente_Cedula'=>mysql_real_escape_string($cedula),
													'Proforma_Cliente_Sucursal'=>$clienteArray['sucursal'],
													'Proforma_Cliente_Exento'=>$clienteArray['exento'],
													'Proforma_Cliente_No_Retencion'=>$clienteArray['retencion']													
	                    );			
	        $this->db->insert('TB_10_Proforma',$dataProforma); 
		    
			if($this->truequeHabilitado && $this->truequeAplicado){ //Si se aplico el trueque, se debe guardar el documento
				$datos = array("Consecutivo" => $consecutivo,
								"Documento" => 'proforma',
								"Sucursal" => $sucursalVendedor);
				$this->db->insert("tb_46_relacion_trueque", $datos);
				$this->truequeAplicado = false;
			}
			return $this->existe_Proforma($consecutivo, $sucursal);
		}else{
			return false;
		}		
	}
	
	function existe_Proforma($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this -> db -> select('Proforma_Consecutivo');
		$this -> db -> from('TB_10_Proforma');
		$this -> db -> where('Proforma_Consecutivo', $consecutivo);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> limit(1);

		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		  //return false;
		  return $consecutivo; //Devolvemos consecutivo para valorar en controlador
		}
		else
		{
		  return false;
		}
	}
	
	function addItemtoInvoice($codigo, $descripcion, $cantidad, $descuento, $exento, $retencion, $precio, $precioFinal, $consecutivo, $sucursal, $vendedor, $cliente, $imagen){
		$sucursalVendedor =  $sucursal;
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$dataItem = array(
              'Articulo_Proforma_Codigo'=>mysql_real_escape_string($codigo),
              'Articulo_Proforma_Descripcion'=>mysql_real_escape_string($descripcion), 
							'Articulo_Proforma_Cantidad'=>mysql_real_escape_string($cantidad),
							'Articulo_Proforma_Descuento'=>mysql_real_escape_string($descuento),
							'Articulo_Proforma_Exento'=>mysql_real_escape_string($exento),
							'Articulo_Proforma_No_Retencion'=>mysql_real_escape_string($retencion),
							'Articulo_Proforma_Precio_Unitario'=>mysql_real_escape_string($precio),
							'Articulo_Proforma_Precio_Final'=>mysql_real_escape_string($precioFinal),	
							'Articulo_Proforma_Imagen'=>mysql_real_escape_string($imagen),
							'TB_10_Proforma_Proforma_Consecutivo'=>mysql_real_escape_string($consecutivo),
							'TB_10_Proforma_TB_02_Sucursal_Codigo'=>mysql_real_escape_string($sucursal),
							'TB_10_Proforma_Proforma_Vendedor_Codigo'=>mysql_real_escape_string($vendedor),
							'TB_10_Proforma_Proforma_Vendedor_Sucursal'=>mysql_real_escape_string($sucursalVendedor),
							'TB_10_Proforma_TB_03_Cliente_Cliente_Cedula'=>mysql_real_escape_string($cliente)							
	                    );			
	        $this->db->insert('TB_04_Articulos_Proforma',$dataItem);
	}
	
	function getCostosTotalesProforma($consecutivo, $sucursal){
            $costo_total = 0;
            $iva = 0;
            $costo_sin_iva = 0;
            $retencion = 0;
            $totalGravados = 0;
            $totalExentos = 0;
            $descuento = 0;
            $this->load->model('articulo','',TRUE);
            $this->load->model('cliente','',TRUE);
            //Traemos el array de configuracion para obtener el porcentaje
            $c_array = $this->getConfgArray();

            //Obtenemos la info del cliente para ver si es exento y/o aplica retencion
            $facturaEncabezado = $this->getProformasHeaders($consecutivo, $sucursal)[0];
            $clienteEsExento = $this->cliente->clienteEsExentoDeIVA($facturaEncabezado->TB_03_Cliente_Cliente_Cedula);
            $clienteNoAplicaRetencion = $this->cliente->clienteEsExentoDeRetencion($facturaEncabezado->TB_03_Cliente_Cliente_Cedula);


            // NUEVA METODOLOGIA
            $costo_total = 0;
            $iva = 0;
            $costo_sin_iva = 0;
            $retencion = 0;
            $aplicaRetencion = true;
            if(!$c_array['aplicar_retencion'] || $clienteNoAplicaRetencion || $clienteEsExento){
                $aplicaRetencion = false;
            }
            if($articulos = $this->getArticulosProforma($consecutivo, $sucursal)){
                foreach($articulos as $a){
                    $detalleLinea = $this->getDetalleLineaProforma($a, $aplicaRetencion);
                    $iva += $detalleLinea["iva"];
                    $retencion += $detalleLinea["retencion"];
                    $costo_sin_iva += $detalleLinea["subtotal"];
                }
            }
            $costo_total += $iva + $retencion + $costo_sin_iva;

            return array(
                'Proforma_Monto_Total'=>$costo_total, 
                'Proforma_Monto_IVA'=>$iva, 
                'Proforma_Monto_Sin_IVA'=>$costo_sin_iva, 
                'Proforma_Retencion'=>$retencion);

	}	
	
	function getConfgArray()
	{
		/*$CI =& get_instance();
		$CI->load->model('XMLParser');
		return $CI->XMLParser->getConfigArray();*/
		return $this->configuracion->getConfiguracionArray();
	}
	
	function updateCostosTotales($data, $consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this->db->where('Proforma_Consecutivo', $consecutivo);
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->update('TB_10_Proforma' ,$data);
	}
	
	function getProformasPendientes($sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this -> db -> select('*');
		$this -> db -> from('TB_10_Proforma');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Proforma_Estado', 'pendiente');
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   return $query->result();
		}
		else
		{
		   return false;
		}
	}
	
	function getProformasHeaders($consecutivo, $sucursal){
		
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->getProformasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("Proforma_Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->getProformasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("Proforma_Consecutivo", $facturas_trueque);
				}
		}
		$this -> db -> select('*');
		$this -> db -> from('TB_10_Proforma');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Proforma_Consecutivo', $consecutivo);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   return $query->result();
		}
		else
		{
		   return false;
		}
	}
	
	function getProformasHeadersImpresion($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		/*$this -> db -> select('Proforma_Consecutivo as consecutivo, 
								Proforma_Monto_Total as total,
								Proforma_Monto_Sin_IVA as subtotal,
								Proforma_Monto_IVA as iva,
								Proforma_Observaciones as observaciones,
								Proforma_Fecha_Hora as fecha,
								Proforma_Moneda as moneda,
								Proforma_Nombre_Cliente as cliente_nombre,
								TB_03_Cliente_Cliente_Cedula as cliente_cedula,
								Proforma_Vendedor_Sucursal as vendedor');
		$this -> db -> from('TB_10_Proforma');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Proforma_Consecutivo', $consecutivo);
		$this -> db -> limit(1);
		$query = $this -> db -> get();*/
		
		$query = $this->db->query("
			SELECT 
				Proforma_Consecutivo as consecutivo, 
				Proforma_Monto_Total as total,
				Proforma_Monto_Sin_IVA as subtotal,
				Proforma_Monto_IVA as total_iva,
				Proforma_Retencion as retencion,
				Proforma_Estado as estado,
				Proforma_Observaciones as observaciones,
				date_format(Proforma_Fecha_Hora, '%d-%m-%Y %h:%i:%s %p') as fecha,
				Proforma_Moneda as moneda,
				Proforma_Nombre_Cliente as cliente_nom,
				Proforma_Tipo_Cambio as cambio,
				TB_03_Cliente_Cliente_Cedula as cliente_ced,
				CONCAT_WS(' ', Usuario_Nombre, Usuario_Apellidos) AS vendedor 
			FROM TB_10_Proforma
			JOIN tb_01_usuario ON tb_01_usuario.Usuario_Codigo = TB_10_Proforma.Proforma_Vendedor_Codigo
			WHERE TB_10_Proforma.TB_02_Sucursal_Codigo = $sucursal
			AND TB_10_Proforma.Proforma_Consecutivo = $consecutivo
		");

		if($query -> num_rows() != 0)
		{
				$articulosDescontados = $this->getProformaConArticulosDescontados($consecutivo, $sucursal) ? true : false;
				$result = $query->result();
				$result[0]->articulosDescontados = $articulosDescontados;
		   	return $result;
		}
		else
		{
		   return false;
		}
	}
	
	function getCliente($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this -> db -> select('TB_03_Cliente_Cliente_Cedula');
		$this -> db -> from('TB_10_Proforma');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Proforma_Consecutivo', $consecutivo);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   $result = $query->result();
			foreach($result as $row)
			{	
				return $row->TB_03_Cliente_Cliente_Cedula;
			}
		}
		else
		{
		   return false;
		}
	}
	
	function getVendedor($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this -> db -> select('Proforma_Vendedor_Codigo');
		$this -> db -> from('TB_10_Proforma');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Proforma_Consecutivo', $consecutivo);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   $result = $query->result();
			foreach($result as $row)
			{	
				return $row->Proforma_Vendedor_Codigo;
			}
		}
		else
		{
		   return false;
		}
	}
	
	function getArticulosProforma($consecutivo, $sucursal){
		//echo "entro";
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this -> db -> select('*');
		$this -> db -> from('TB_04_Articulos_Proforma');
		$this -> db -> where('TB_10_Proforma_TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('TB_10_Proforma_Proforma_Consecutivo', $consecutivo);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   return $query->result();
		}
		else
		{
		   return false;
		}
	}
	
	function getArticulosProformaImpresion($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this -> db -> select('
				Articulo_Proforma_Codigo AS codigo, 
				Articulo_Proforma_Descripcion AS descripcion, 
				Articulo_Proforma_Cantidad AS cantidad, 
				Articulo_Proforma_Descuento AS descuento, 
				Articulo_Proforma_Exento AS exento, 
				Articulo_Proforma_Precio_Unitario AS precio');
		$this -> db -> from('TB_04_Articulos_Proforma');
		$this -> db -> where('TB_10_Proforma_TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('TB_10_Proforma_Proforma_Consecutivo', $consecutivo);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   return $query->result();
		}
		else
		{
		   return false;
		}
	}
	
	function actualizar($consecutivo, $sucursal, $data)
	{ 
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this->db->where('Proforma_Consecutivo', mysql_real_escape_string($consecutivo));
		$this->db->where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this->db->update('TB_10_Proforma' ,$data);		
	}
	
	function getProformasFiltradas($cliente, $desde, $hasta, $sucursal, $estado){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->getProformasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("Proforma_Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->getProformasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("Proforma_Consecutivo", $facturas_trueque);
				}
		}
		$this->db->select("Proforma_Consecutivo as consecutivo,
		                   Proforma_Monto_Total as total,
						   Proforma_Nombre_Cliente as cliente,
						   date_format(Proforma_Fecha_Hora, '%d-%m-%Y %h:%i:%s %p') as fecha", false);
		$this->db->from('tb_10_proforma');
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->setFiltradoCliente($cliente);
		$this->setFiltradoFechaDesde($desde);
		$this->setFiltradoFechaHasta($hasta);
		$this->setFiltradoEstado($estado);
		$query = $this -> db -> get();
		if($query -> num_rows() != 0)
		{
		    return $query->result();			
		}
		else
		{
			return false;
		}
	}
	
	function getProformasSinProcesar($cliente, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->getProformasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("Proforma_Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->getProformasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("Proforma_Consecutivo", $facturas_trueque);
				}
		}
		$this->db->select("Proforma_Consecutivo as consecutivo,
		                   Proforma_Monto_Total as total,
						   Proforma_Nombre_Cliente as cliente,
						   date_format(Proforma_Fecha_Hora, '%d-%m-%Y %h:%i:%s %p') as fecha", false);
		$this->db->from('tb_10_proforma');
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->where('Proforma_Estado', "sin_proces");
		$this->setFiltradoCliente($cliente);
		$query = $this -> db -> get();
		if($query -> num_rows() != 0)
		{
		    return $query->result();			
		}
		else
		{
			return false;
		}
	}
	
	function setFiltradoCliente($cliente){
		if(trim($cliente)!=''){
			$this->db->where('TB_03_Cliente_Cliente_Cedula', $cliente);
		}
	}
	
	function setFiltradoFechaDesde($fecha){
		if(trim($fecha)!=''){
			$fecha = $this->convertirFecha($fecha, " 00:00:00");
			$this->db->where('Proforma_Fecha_Hora >=', $fecha);
		}
	}
	
	function setFiltradoFechaHasta($fecha){
		if(trim($fecha)!=''){
			$fecha = $this->convertirFecha($fecha, " 23:59:59");
			$this->db->where('Proforma_Fecha_Hora <=', $fecha);
		}
	}
	
	function setFiltradoEstado($estados){
		if(sizeOf($estados)>0){
			$this->db->where_in('Proforma_Estado', $estados);
		}
	}
	
	function convertirFecha($fecha, $horas){		
		if(trim($fecha)!=''){
			$fecha = explode("/",$fecha);
			$fecha = $fecha[0]."-".$fecha[1]."-".$fecha[2].$horas;
			//echo $fecha;
			date_default_timezone_set("America/Costa_Rica");
			return date("Y-m-d : H:i:s", strtotime($fecha));
		}		
		return $fecha;
	}
	
	function getProformasTrueque($sucursal){
			$this->db->select("Consecutivo");
			$this->db->from("tb_46_relacion_trueque");
			$this->db->where("Documento", "proforma");
			$this->db->where("Sucursal", $sucursal);
			$query = $this->db->get();
			if($query->num_rows()==0){
					return array();
			}else{
					$facturas = array();
					foreach($query->result() as $f){
							array_push($facturas, $f->Consecutivo);
					}
					return $facturas;
			}
	}
	
	function getProformasTruequeResponde($sucursales){
			$this->db->select("Consecutivo");
			$this->db->from("tb_46_relacion_trueque");
			$this->db->where("Documento", "proforma");
			$this->db->where_in("Sucursal", $sucursales);
			$query = $this->db->get();
			if($query->num_rows()==0){
					return array();
			}else{
					$facturas = array();
					foreach($query->result() as $f){
							array_push($facturas, $f->Consecutivo);
					}
					return $facturas;
			}
	}
	
	function getProformaConArticulosDescontados($proforma, $sucursal){
			$this->db->from("TB_47_Descuento_Proforma");
			$this->db->where("Sucursal", $sucursal);
			$this->db->where("Proforma", $proforma);
			$query = $this->db->get();
			if($query->num_rows()==0){
					return false;
			}else{
					return $query->result();
			}
	}
	
	function marcarProformaConDescuentoProductos($proforma, $sucursal){
			$datos = array(
					"Proforma" => $proforma,
					"Sucursal" => $sucursal
			);
			$this->db->insert("TB_47_Descuento_Proforma", $datos);
			
			$datos = array("Proforma_Estado" => "descontada");
			$this->db->where('Proforma_Consecutivo', $proforma);
			$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
			$this->db->update("tb_10_proforma", $datos);
	}

	
	function marcarComoProformaFacturada($proforma, $sucursal){
		$datos = array("Proforma_Estado" => "facturada");
		$this->db->where('Proforma_Consecutivo', $proforma);
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->update("tb_10_proforma", $datos);
	}
	
	function marcarComoProformaAnulada($proforma, $sucursal){
		$datos = array("Proforma_Estado" => "anulada");
		$this->db->where('Proforma_Consecutivo', $proforma);
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->update("tb_10_proforma", $datos);
	}
	
	function marcarComoProformaPagada($proforma, $sucursal){
		$datos = array("Proforma_Estado" => "pagada");
		$this->db->where('Proforma_Consecutivo', $proforma);
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->update("tb_10_proforma", $datos);
	}
	
	function eliminarProductosDeProforma($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this->db->where("TB_10_Proforma_TB_02_Sucursal_Codigo", $sucursal);
		$this->db->where("TB_10_Proforma_Proforma_Consecutivo", $consecutivo);
		$this->db->delete("tb_04_articulos_proforma");
	}
	
	function actualizarHeadProforma($datos, $consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this->db->where('Proforma_Consecutivo', $consecutivo);
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->update("tb_10_proforma", $datos);
	}
	
	function marcarComoProformaPendiente($proforma, $sucursal){
		$datos = array("Proforma_Estado" => "pendiente");
		$this->db->where('Proforma_Consecutivo', $proforma);
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->update("tb_10_proforma", $datos);
	}
	
/*
	function getCliente($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this -> db -> select('TB_03_Cliente_Cliente_Cedula');
		$this -> db -> from('tb_10_proforma');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Proforma_Consecutivo', $consecutivo);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   $result = $query->result();
			foreach($result as $row)
			{	
				return $row->TB_03_Cliente_Cliente_Cedula;
			}
		}
		else
		{
		   return false;
		}
	}
*/
	
/*
	function getVendedor($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this -> db -> select('Proforma_Vendedor_Codigo');
		$this -> db -> from('tb_10_proforma');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Proforma_Consecutivo', $consecutivo);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   $result = $query->result();
			foreach($result as $row)
			{	
				return $row->Proforma_Vendedor_Codigo;
			}
		}
		else
		{
		   return false;
		}
	}
*/
}


?>