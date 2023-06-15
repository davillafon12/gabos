<?php
Class factura extends CI_Model
{
        public $sucursalesData = array(
            //14 => 30
        );

	function getConsecutivo($id_empresa) //Traer el siguiente consecutivo de una empresa en particular
	{
            //return $this->getConsecutivoUltimaFactura($id_empresa)+1;
            return $this->getConsecutivoUltimaFactura($id_empresa)+1;
	}

        function getConsecutivoUltimaFactura($sucursal){
            //select max(Consecutivo) as ConsecutivoActual from tb_55_factura_electronica where Sucursal = 14
            $this -> db -> select('max(Factura_Consecutivo) as ConsecutivoActual');
            $this -> db -> from('TB_07_Factura');
            $this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);

            if(isset($this->sucursalesData[$sucursal])){
                $this -> db -> where('Factura_Consecutivo <', $this->sucursalesData[$sucursal]);
            }

            $query = $this -> db -> get();
            if($query->num_rows() == 0){
                return 0;
            }else{
                return $query->result()[0]->ConsecutivoActual;
            }
        }

        function existeFactura($consecutivo, $sucursal){
            $this -> db -> from('TB_07_Factura');
            $this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
            $this -> db -> where("Factura_Consecutivo", $consecutivo);
            $query = $this -> db -> get();

            return $query->num_rows() > 0;
        }

//	function getConsecutivoUltimaFactura($sucursal)
//	{
//		$this -> db -> select('Factura_Consecutivo');
//		$this -> db -> from('TB_07_Factura');
//		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
//		$this -> db -> order_by("Factura_Consecutivo", "desc");
//		$this -> db -> limit(1);
//		$query = $this -> db -> get();
//		/*$query ="select Factura_Consecutivo from TB_07_Factura order by Factura_Consecutivo DESC limit 1";
//        $res = $this->db->query($query);*/
//
//		if($query->num_rows()==0)
//		{
//			return false;
//		}
//		else
//		{
//			$consecutivo;
//			$result = $query->result();
//			foreach($result as $row)
//			{$consecutivo=$row->Factura_Consecutivo;}
//			return $consecutivo;
//		}
//	}

	function crearfactura($cedula, $nombre, $currency, $observaciones, $sucursal, $vendedor, $isProforma){
		$c_array = $this->getConfgArray();
		if($isProforma){ //Si es proforma agarramos el iva y el cambio de la proforma, no el actual
			$proforma = $this->getProformasHeaders($isProforma, $sucursal);
			foreach($proforma as $head){
				$c_array['iva']=$head->Proforma_Porcentaje_IVA;
				$c_array['dolar_venta']=$head->Proforma_Tipo_Cambio;
			}
		}
		$sucursalVendedor =  $sucursal;
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es una sucursal con trueque
				$sucursal = $this->sucursales_trueque[$sucursal];
				$this->truequeAplicado = true;
		}

                $consecutivo = $this->getConsecutivo($sucursal);

                // Preguntamos si el consecutivo ya existe, y si no vuelve a pedirlo
                do{
                    usleep(rand( 500000, 1500000 ));
                    $consecutivo = $this->getConsecutivo($sucursal);
                }while($this->existeFactura($consecutivo, $sucursal));

                //return $consecutivo;
                $this->load->model('cliente','',TRUE);
                $clienteArray = $this->cliente->getNombreCliente($cedula);
                date_default_timezone_set("America/Costa_Rica");
                $Current_datetime = date("y/m/d : H:i:s", now());
                $dataFactura = array(
                        'Factura_Consecutivo'=>$consecutivo,
                        'Factura_Observaciones'=>$observaciones,
                        'Factura_Estado'=>'pendiente',
                        'Factura_Moneda'=>$currency,
                        'Factura_porcentaje_iva'=>$c_array['iva'],
                        'Factura_tipo_cambio'=>$c_array['dolar_venta'],
                        'Factura_Nombre_Cliente'=>$nombre,
                        'TB_02_Sucursal_Codigo'=>$sucursal,
                        'Factura_Vendedor_Codigo'=>$vendedor,
                        'Factura_Vendedor_Sucursal'=>$sucursalVendedor,
                        'Factura_Fecha_Hora'=>$Current_datetime,
                        'TB_03_Cliente_Cliente_Cedula'=>$cedula,
                        'Factura_Cliente_Exento'=>$clienteArray['exento'],
                        'Factura_Cliente_No_Retencion'=>$clienteArray['retencion'],
                        'Factura_Cliente_Sucursal'=>$clienteArray['sucursal']
	                    );
	        $this->db->insert('TB_07_Factura',$dataFactura);

                if($this->truequeHabilitado && $this->truequeAplicado){ //Si se aplico el trueque, se debe guardar el documento
                    $datos = array("Consecutivo" => $consecutivo,
                                                    "Documento" => 'factura',
                                                    "Sucursal" => $sucursalVendedor);
                    $this->db->insert("tb_46_relacion_trueque", $datos);
                    $this->truequeAplicado = false;
		}

		return $this->existe_Factura($consecutivo, $sucursal);
	}

	function getProformasHeaders($consecutivo, $sucursal){
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

	function existe_Factura($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}

		$this -> db -> select('Factura_Consecutivo');
		$this -> db -> from('TB_07_Factura');
		$this -> db -> where('Factura_Consecutivo', $consecutivo);
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

	function addItemtoInvoice($codigo, $descripcion, $cantidad, $descuento, $exento, $retencion, $precio, $precioFinal, $consecutivo, $sucursal, $vendedor, $cliente, $imagen, $tipoCodigo = "01", $unidadMedida, $codigoCabys, $impuesto){
		$sucursalVendedor = $sucursal;
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$dataItem = array(
	                        'Articulo_Factura_Codigo'=>$codigo,
	                        'Articulo_Factura_Descripcion'=>$descripcion,
                            'Articulo_Factura_Cantidad'=>$cantidad,
                            'Articulo_Factura_Descuento'=>$descuento,
                            'Articulo_Factura_Exento'=>$exento,
                            'Articulo_Factura_No_Retencion'=>$retencion,
                            'Articulo_Factura_Precio_Unitario'=>$precio,
                            'Articulo_Factura_Precio_Final' => 	$precioFinal,
                            'Articulo_Factura_Imagen'=>$imagen,
                            'TB_07_Factura_Factura_Consecutivo'=>$consecutivo,
                            'TB_07_Factura_TB_02_Sucursal_Codigo'=>$sucursal,
                            'TB_07_Factura_Factura_Vendedor_Codigo'=>$vendedor,
                            'TB_07_Factura_Factura_Vendedor_Sucursal'=>$sucursalVendedor,
                            'TB_07_Factura_TB_03_Cliente_Cliente_Cedula'=>$cliente,
                            'TipoCodigo' => $tipoCodigo,
                            'UnidadMedida' => $unidadMedida,
                            'Codigo_Cabys' => $codigoCabys,
                            'Impuesto' => $impuesto
	                    );
	        $this->db->insert('TB_08_Articulos_Factura',$dataItem);
	}

	function getCostosTotalesFactura($consecutivo, $sucursal){

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
        $facturaEncabezado = $this->getFacturasHeaders($consecutivo, $sucursal)[0];
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
                if($articulos = $this->getItemsFactura($consecutivo, $sucursal)){
                    foreach($articulos as $a){
                        $detalleLinea = $this->getDetalleLinea($a, $aplicaRetencion);
                        $iva += $detalleLinea["iva"];
                        $retencion += $detalleLinea["retencion"];
                        $costo_sin_iva += $detalleLinea["subtotal"];
                    }
                }
                $costo_total += $iva + $retencion + $costo_sin_iva;
		return array(
                        'Factura_Monto_Total'=>$costo_total,
                        'Factura_Monto_IVA'=>$iva,
                        'Factura_Monto_Sin_IVA'=>$costo_sin_iva,
                        'Factura_Retencion'=>$retencion
                        );
	}

	function getItemsFactura($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				//$this -> db -> where('TB_07_Factura_Factura_Vendedor_Sucursal', $sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this -> db -> from('TB_08_Articulos_Factura');
		$this -> db -> where('TB_07_Factura_Factura_Consecutivo', $consecutivo);
		$this -> db -> where('TB_07_Factura_TB_02_Sucursal_Codigo', $sucursal);

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

	function getArticuloFactura($consecutivo, $sucursal, $articulo){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$this -> db -> where('TB_07_Factura_Factura_Vendedor_Sucursal', $sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this -> db -> from('TB_08_Articulos_Factura');
		$this -> db -> where('TB_07_Factura_Factura_Consecutivo', $consecutivo);
		$this -> db -> where('TB_07_Factura_TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Articulo_Factura_Codigo', $articulo);

		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   return $query->result()[0];
		}
		else
		{
		   return false;
		}
	}

	function getConfgArray()
	{
		return $this->configuracion->getConfiguracionArray();
	}

	function updateCostosTotales($data, $consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this->db->where('Factura_Consecutivo', $consecutivo);
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->update('TB_07_Factura' ,$data);
	}

	function getFacturasPendientes($sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->getFacturasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("Factura_Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->getFacturasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("Factura_Consecutivo", $facturas_trueque);
				}
		}

		$this -> db -> select('*');
		$this -> db -> from('TB_07_Factura');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Factura_Estado', 'pendiente');
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

	function getFacturasHeaders($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->getFacturasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("Factura_Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->getFacturasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("Factura_Consecutivo", $facturas_trueque);
				}
		}
		$this -> db -> select('*');
		$this -> db -> from('TB_07_Factura');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Factura_Consecutivo', $consecutivo);
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

	function getFacturasHeadersImpresion($consecutivo, $sucursal){
		/*//JOIN tb_01_usuario ON tb_01_usuario.Usuario_Codigo = TB_07_Factura.Factura_Vendedor_Codigo
		$this -> db -> select("Factura_Consecutivo AS consecutivo, TB_03_Cliente_Cliente_Cedula AS cliente_ced, Factura_Nombre_Cliente AS cliente_nom, Factura_Monto_Total AS total, Factura_Tipo_Pago AS tipo, Factura_Moneda AS moneda, Factura_tipo_cambio AS cambio");
		$this -> db -> from('TB_07_Factura');
		$this -> db -> join('tb_01_usuario', 'tb_01_usuario.Usuario_Codigo = TB_07_Factura.Factura_Vendedor_Codigo');
		$this -> db -> where('TB_07_Factura.TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('TB_07_Factura.Factura_Consecutivo', $consecutivo);
		$this-> db ->set("date_format(Factura_Fecha_Hora, '%d-%m-%Y %h:%i:%s %p') AS fecha", FALSE);
		//$this -> db -> limit(1);
		$query = $this -> db -> get();*/
		$queryLoco = "";

		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->getFacturasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$queryLoco = " AND TB_07_Factura.Factura_Consecutivo IN (".implode(',',$facturas_trueque).")";
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->getFacturasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$queryLoco = " AND TB_07_Factura.Factura_Consecutivo NOT IN (".implode(',',$facturas_trueque).")";
				}
		}

		$query = $this->db->query("
			SELECT Factura_Consecutivo AS consecutivo,
				date_format(Factura_Fecha_Hora, '%d-%m-%Y %h:%i:%s %p') AS fecha,
				TB_03_Cliente_Cliente_Cedula AS cliente_ced,
				Factura_Nombre_Cliente AS cliente_nom,
				Factura_Monto_Total AS total,
				Factura_Monto_Sin_IVA AS subtotal,
				Factura_Monto_IVA as total_iva,
				Factura_Retencion as retencion,
				Factura_Tipo_Pago AS tipo,
				Factura_Moneda AS moneda,
				Factura_tipo_cambio AS cambio,
				CONCAT_WS(' ', Usuario_Nombre, Usuario_Apellidos) AS vendedor,
				Factura_Observaciones AS observaciones,
				Factura_Entregado_Vuelto AS entregado_vuelto,
				Factura_Recibido_Vuelto AS recibido_vuelto,
				Factura_Estado AS estado
			FROM TB_07_Factura
			JOIN tb_01_usuario ON tb_01_usuario.Usuario_Codigo = TB_07_Factura.Factura_Vendedor_Codigo
			WHERE TB_07_Factura.TB_02_Sucursal_Codigo = $sucursal
			AND TB_07_Factura.Factura_Consecutivo = $consecutivo
			$queryLoco
		");

		if($query -> num_rows() != 0)
		{
		   return $query->result();
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
		$this -> db -> from('TB_07_Factura');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Factura_Consecutivo', $consecutivo);
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
		$this -> db -> select('Factura_Vendedor_Codigo');
		$this -> db -> from('TB_07_Factura');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Factura_Consecutivo', $consecutivo);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   $result = $query->result();
			foreach($result as $row)
			{
				return $row->Factura_Vendedor_Codigo;
			}
		}
		else
		{
		   return false;
		}
	}

	function getTipoCambio($consecutivo, $sucursal){
		$this -> db -> select('Factura_tipo_cambio');
		$this -> db -> from('TB_07_Factura');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Factura_Consecutivo', $consecutivo);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   $result = $query->result();
			foreach($result as $row)
			{
				return $row->Factura_tipo_cambio;
			}
		}
		else
		{
		   return false;
		}
	}

	function getMoneda($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this -> db -> select('Factura_Moneda');
		$this -> db -> from('TB_07_Factura');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Factura_Consecutivo', $consecutivo);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   $result = $query->result();
			foreach($result as $row)
			{
				return $row->Factura_Moneda;
			}
		}
		else
		{
		   return false;
		}
	}

	function getArticulosFactura($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this -> db -> select('*');
		$this -> db -> from('TB_08_Articulos_Factura');
		$this -> db -> where('TB_07_Factura_TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('TB_07_Factura_Factura_Consecutivo', $consecutivo);
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

	function getArticulosFacturaImpresion($consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		//echo "entro";
		$this -> db -> select('Articulo_Factura_Codigo AS codigo, Articulo_Factura_Descripcion AS descripcion, Articulo_Factura_Cantidad AS cantidad, Articulo_Factura_Descuento AS descuento, Articulo_Factura_Exento AS exento, Articulo_Factura_Precio_Unitario AS precio');
		$this -> db -> from('TB_08_Articulos_Factura');
		$this -> db -> where('TB_07_Factura_TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('TB_07_Factura_Factura_Consecutivo', $consecutivo);
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



	function actualizarFacturaHead($datos, $consecutivo, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->where('Factura_Consecutivo', $consecutivo);
		$this->db->update('TB_07_Factura' ,$datos);
	}

	function guardarPagoTarjeta($consecutivo, $sucursal, $transaccion, $comision, $vendedor, $cliente, $banco){
				$sucursalVendedor = $sucursal;
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$dataFactura = array(
						'Tarjeta_Numero_Transaccion'=>$transaccion,
						'Tarjeta_Comision_Banco'=>$comision,
						'TB_07_Factura_Factura_Consecutivo'=>$consecutivo,
						'TB_07_Factura_TB_02_Sucursal_Codigo'=>$sucursal,
						'TB_07_Factura_Factura_Vendedor_Codigo'=>$vendedor,
						'TB_07_Factura_Factura_Vendedor_Sucursal'=>$sucursalVendedor,
						'TB_07_Factura_TB_03_Cliente_Cliente_Cedula'=>$cliente,
						'TB_22_Banco_Banco_Codigo'=>$banco
					);
		$this->db->insert('TB_18_Tarjeta',$dataFactura);
		return $this->db->insert_id();
	}

	function guardarPagoCheque($consecutivo, $sucursal, $transaccion, $vendedor, $cliente, $banco){
		$sucursalVendedor = $sucursal;
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$dataFactura = array(
						'Cheque_Numero'=>$transaccion,
						'Banco'=>$banco,
						'TB_07_Factura_Factura_Consecutivo'=>$consecutivo,
						'TB_07_Factura_TB_02_Sucursal_Codigo'=>$sucursal,
						'TB_07_Factura_Factura_Vendedor_Codigo'=>$vendedor,
						'TB_07_Factura_Factura_Vendedor_Sucursal'=>$sucursalVendedor,
						'TB_07_Factura_TB_03_Cliente_Cliente_Cedula'=>$cliente
					);
		$this->db->insert('TB_13_Cheque',$dataFactura);
	}

	function guardarPagoDeposito($consecutivo, $sucursal, $transaccion, $vendedor, $cliente, $banco){
		$sucursalVendedor = $sucursal;
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$dataFactura = array(
						'Deposito_Numero_Transaccion'=>$transaccion,
						'TB_07_Factura_Factura_Consecutivo'=>$consecutivo,
						'TB_07_Factura_TB_02_Sucursal_Codigo'=>$sucursal,
						'TB_07_Factura_Factura_Vendedor_Codigo'=>$vendedor,
						'TB_07_Factura_Factura_Vendedor_Sucursal'=>$sucursalVendedor,
						'TB_07_Factura_TB_03_Cliente_Cliente_Cedula'=>$cliente,
						'TB_22_Banco_Banco_Codigo'=>$banco
					);
		$this->db->insert('TB_19_Deposito',$dataFactura);
	}

	function guardarPagoMixto($consecutivo, $sucursal, $transaccion, $comision, $vendedor, $cliente, $banco, $cantidadPagoTarjeta){

		//Creamos el pago con tarjeta primero
		$tarjeta = $this->guardarPagoTarjeta($consecutivo, $sucursal, $transaccion, $comision, $vendedor, $cliente, $banco);
		$sucursalVendedor = $sucursal;
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		//Creamos el pago mixto
		$dataFactura = array(
						'Mixto_Cantidad_Paga'=>$cantidadPagoTarjeta,
						'TB_18_Tarjeta_Tarjeta_Id'=>$tarjeta,
						'TB_18_Tarjeta_TB_07_Factura_Factura_Consecutivo'=>$consecutivo,
						'TB_18_Tarjeta_TB_07_Factura_TB_02_Sucursal_Codigo'=>$sucursal,
						'TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Codigo'=>$vendedor,
						'TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Sucursal'=>$sucursalVendedor,
						'TB_18_Tarjeta_TB_07_Factura_TB_03_Cliente_Cliente_Cedula'=>$cliente,
						'TB_18_Tarjeta_TB_22_Banco_Banco_Codigo'=>$banco
					);
		$this->db->insert('TB_23_Mixto',$dataFactura);
	}

	function guardarPagoCredito($consecutivo, $sucursal, $vendedor, $cliente, $numeroDias, $fecha, $saldo){
		$sucursalVendedor = $sucursal;
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$dataGuardar = array(
							'Credito_Numero_Dias' => $numeroDias,
							'Credito_Saldo_Actual' => $saldo,
							'Credito_Saldo_Inicial' => $saldo,
							'Credito_Fecha_Expedicion' => $fecha,
							'Credito_Factura_Consecutivo' => $consecutivo,
							'Credito_Sucursal_Codigo' => $sucursal,
							'Credito_Vendedor_Codigo' => $vendedor,
							'Credito_Vendedor_Sucursal' => $sucursalVendedor,
							'Credito_Cliente_Cedula' => $cliente
							);
		$this->db->insert('TB_24_Credito',$dataGuardar);
		return $this->db->insert_id();
	}

	function guardarPagoAbono($credito, $abono){
		$dataGuardar = array(
							'Abono' => $abono,
							'Credito' => $credito
							);
		$this->db->insert('tb_40_apartado',$dataGuardar);
	}

	function getAbonoApartado($sucursal, $consecutivo){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		/*
			SELECT 	tb_40_apartado.Abono
			FROM tb_40_apartado
			JOIN tb_24_credito ON tb_40_apartado.Credito = tb_24_credito.Credito_Id
			WHERE tb_24_credito.Credito_Factura_Consecutivo = 1
			AND tb_24_credito.Credito_Sucursal_Codigo = 0
		*/
		$this->db->select('tb_40_apartado.Abono');
		$this->db->from('tb_40_apartado');
		$this->db->join('tb_24_credito','tb_40_apartado.Credito = tb_24_credito.Credito_Id');
		$this->db->where('tb_24_credito.Credito_Factura_Consecutivo', $consecutivo);
		$this->db->where('tb_24_credito.Credito_Sucursal_Codigo', $sucursal);
		$query = $this->db->get();
		if($query -> num_rows() != 0)
		{
		   $result = $query->result();
		   return $result[0]->Abono;
		}
		else
		{
			return 0;
		}
	}

	function getCreditosClientePorSucursal($cedula, $sucursal){

		$this -> db -> select('*');
		$this -> db -> from('TB_24_Credito');
		$this -> db -> where('Credito_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Credito_Cliente_Cedula', $cedula);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		   return $query->result();
		}
		else
		{
			//Si no tiene creditos anteriores devuelve una array con 0, no se envia el cero como tal proque se parsea como false
		   return array((object)array('Credito_Saldo_Actual'=>'0'));
		}
	}

	function getCreditoClienteDeFactura($consecutivo, $sucursal, $cliente){
		$this -> db -> select('Credito_Numero_Dias');
		$this -> db -> from('TB_24_Credito');
		$this -> db -> where('Credito_Factura_Consecutivo', $consecutivo);
		$this -> db -> where('Credito_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Credito_Cliente_Cedula', $cliente);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
		    $query = $query->result();
			foreach($query as $row){
				return $row -> Credito_Numero_Dias;
			}
		}
		else
		{
			//Si no lo encuentra envia el minimo de dias
			return '8';
		}
	}

        function eliminarFacturaPorFallo($consecutivo, $sucursal){
            if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
                $sucursal = $this->sucursales_trueque[$sucursal];
            }
            $this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
            $this -> db -> where('Factura_Consecutivo', $consecutivo);
            $this->db->delete('tb_07_factura');
        }

	function eliminarArticulosFactura($consecutivo, $sucursal){
            if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
                $sucursal = $this->sucursales_trueque[$sucursal];
            }
            $this -> db -> where('TB_07_Factura_TB_02_Sucursal_Codigo', $sucursal);
            $this -> db -> where('TB_07_Factura_Factura_Consecutivo', $consecutivo);
            $this->db->delete('TB_08_Articulos_Factura');
	}

	function getMontoTotalPago($sucursal, $consecutivo){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this->db->where('Factura_Consecutivo', $consecutivo);
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->from('tb_07_factura');
		$query = $this -> db -> get();
		if($query -> num_rows() != 0)
		{
		    $query = $query->result();
			foreach($query as $row){
				return $row -> Factura_Monto_Total;
			}
		}
		else
		{
			return 0;
		}
	}

	function getMontoPagoTarjetaMixto($sucursal, $consecutivo){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
		$this->db->where('TB_18_Tarjeta_TB_07_Factura_Factura_Consecutivo', $consecutivo);
		$this->db->where('TB_18_Tarjeta_TB_07_Factura_TB_02_Sucursal_Codigo', $sucursal);
		$this->db->from('tb_23_mixto');
		$query = $this -> db -> get();
		if($query -> num_rows() != 0)
		{
		    $query = $query->result();
			foreach($query as $row){
				return $row -> Mixto_Cantidad_Paga;
			}
		}
		else
		{
			return 0;
		}
	}

	function getFacturasFiltradas($cliente, $desde, $hasta, $tipo, $estado, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->getFacturasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("Factura_Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->getFacturasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("Factura_Consecutivo", $facturas_trueque);
				}
		}
		$this->db->select("Factura_Consecutivo as consecutivo,
		                   Factura_Monto_Total as total,
						   Factura_Nombre_Cliente as cliente,
						   date_format(Factura_Fecha_Hora, '%d-%m-%Y %h:%i:%s %p') as fecha", false);
		$this->db->from('tb_07_factura');
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->setFiltradoCliente($cliente);
		$this->setFiltradoFechaDesde($desde);
		$this->setFiltradoFechaHasta($hasta);
		$this->setFiltradoTipo($tipo);
		$this->setFiltradoEstado($estado);
		$this->db->order_by('Factura_Consecutivo','desc');
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
			$this->db->where('Factura_Fecha_Hora >=', $fecha);
		}
	}

	function setFiltradoFechaHasta($fecha){
		if(trim($fecha)!=''){
			$fecha = $this->convertirFecha($fecha, " 23:59:59");
			$this->db->where('Factura_Fecha_Hora <=', $fecha);
		}
	}

	function setFiltradoTipo($tipos){
		if(sizeOf($tipos)>0){
			$this->db->where_in('Factura_Tipo_Pago', $tipos);
		}
	}

	function setFiltradoEstado($estados){
		if(sizeOf($estados)>0){
			$this->db->where_in('Factura_Estado', $estados);
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

	function getFacturasTrueque($sucursal){
            $this->db->select("Consecutivo");
            $this->db->from("tb_46_relacion_trueque");
            $this->db->where("Documento", "factura");
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

	function getFacturasTruequeResponde($sucursales){
			$this->db->select("Consecutivo");
			$this->db->from("tb_46_relacion_trueque");
			$this->db->where("Documento", "factura");
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

        function crearFacturaElectronica($sucursal, $cliente, $factura, $costos, $articulos, $tipoPago){
            $feedback["status"] = false;

            //Fix de cedula para desampa
            if($cliente->Cliente_Cedula == 31013507850){
                $cliente->Cliente_Cedula = 3101350785;
                $cliente->Cliente_Tipo_Cedula = "juridica";
            }

            // No vamos a aceptar receptores de pasaporte para FE
            if($cliente->NoReceptor || $cliente->Cliente_Tipo_Cedula == "pasaporte"){
                $cliente = null;
            }

            $responseData = $this->guardarDatosBasicosFacturaElectronica($tipoPago, $sucursal, $cliente, $factura, $costos, $articulos);

            if($resClave = $this->generarClaveYConsecutivoParaFacturaElectronica($factura->Factura_Consecutivo, $factura->TB_02_Sucursal_Codigo)){
                if($resXML = $this->generarXMLFactura($factura->Factura_Consecutivo, $factura->TB_02_Sucursal_Codigo)){
                    if($resXMLFirmado = $this->firmarXMLFactura($factura->Factura_Consecutivo, $factura->TB_02_Sucursal_Codigo)){
                        $feedback["data"] = $responseData;
                        $feedback["data"]["clave"] = $resClave["Clave"];
                        $feedback["status"] = true;
                        unset($feedback['error']);
                        log_message('error', "Se genero bien el XML firmado | Consecutivo: $factura->Factura_Consecutivo | Sucursal: $factura->TB_02_Sucursal_Codigo");
                    }else{
                        // ERROR AL FIRMAR EL XML DE FE
                        $feedback['error']='54';
                        log_message('error', "Error al firmar el XML | Consecutivo: $factura->Factura_Consecutivo | Sucursal: $factura->TB_02_Sucursal_Codigo");
                    }
                }else{
                    // ERROR AL GENERAR EL XML DE FE
                    $feedback['error']='53';
                    log_message('error', "Error al generar el XML | Consecutivo: $factura->Factura_Consecutivo | Sucursal: $factura->TB_02_Sucursal_Codigo");
                }
            }else{
                // ERROR AL GENERAR LA CLAVE
                $feedback["error"] = '52';
                log_message('error', "Error al generar la clave | Consecutivo: $factura->Factura_Consecutivo | Sucursal: $factura->TB_02_Sucursal_Codigo");
            }
            return $feedback;
        }

        function guardarDatosBasicosFacturaElectronica($tipoPago, $emisor, $receptor, $factura, $costos, $articulos){
            // Eliminamos informacion antigua de la misma factura
            $this->db->where("Consecutivo", $factura->Factura_Consecutivo);
            $this->db->where("Sucursal", $factura->TB_02_Sucursal_Codigo);
            $this->db->delete("tb_65_cuerpo_factura_electronica");

            $this->db->where("Consecutivo", $factura->Factura_Consecutivo);
            $this->db->where("Sucursal", $factura->TB_02_Sucursal_Codigo);
            $this->db->delete("tb_56_articulos_factura_electronica");

            $this->db->where("Consecutivo", $factura->Factura_Consecutivo);
            $this->db->where("Sucursal", $factura->TB_02_Sucursal_Codigo);
            $this->db->delete("tb_55_factura_electronica");

            // Guardamos el encabezado de la factura
            require_once PATH_API_HACIENDA;
            $api = new API_FE();
            date_default_timezone_set("America/Costa_Rica");
            $fechaFacturaActual = now();
            $situacion = $api->internetIsOnline() ? "normal" : "sininternet";
            $fechaEmision = date(DATE_ATOM, $fechaFacturaActual);
            $condicionVenta = $this->getCondicionVenta($tipoPago);
            $plazoCredito = "0";
            if(isset($tipoPago['canDias'])){
                $plazoCredito = $tipoPago['canDias'];
            }
            $medioPago = $this->getMedioPago($tipoPago);
            $codigoMoneda = $factura->Factura_Moneda == "colones" ? "CRC" : "USD";
            $tipoCambio = $factura->Factura_tipo_cambio;
            $otros = $factura->Factura_Observaciones;

            // Agregamos la info nueva
            $data = array(
                "Consecutivo" => $factura->Factura_Consecutivo,
                "Sucursal" => $factura->TB_02_Sucursal_Codigo,
                "FechaEmision" => $fechaEmision,
                "EmisorNombre" => $emisor->Sucursal_Nombre,
                "EmisorTipoIdentificacion" => $emisor->Tipo_Cedula,
                "EmisorIdentificacion" => $emisor->Sucursal_Cedula,
                "EmisorNombreComercial" => $emisor->Sucursal_Nombre,
                "EmisorProvincia" => $emisor->Provincia,
                "EmisorCanton" => str_pad($emisor->Canton,2,"0", STR_PAD_LEFT),
                "EmisorDistrito" => str_pad($emisor->Distrito,2,"0", STR_PAD_LEFT),
                "EmisorBarrio" => str_pad($emisor->Barrio,2,"0", STR_PAD_LEFT),
                "EmisorOtrasSennas" => $emisor->Sucursal_Direccion,
                "EmisorCodigoPaisTelefono" => $emisor->Codigo_Pais_Telefono,
                "EmisorTelefono" => str_replace("-", "", $emisor->Sucursal_Telefono),
                "EmisorCodigoPaisFax" => $emisor->Codigo_Pais_Fax,
                "EmisorFax" => str_replace("-", "", $emisor->Sucursal_Fax),
                "EmisorEmail" => $emisor->Sucursal_Email,
                "CondicionVenta" => $condicionVenta,
                "PlazoCredito" => $plazoCredito,
                "MedioPago" => $medioPago,
                "CodigoMoneda" => $codigoMoneda,
                "TipoCambio" => $tipoCambio,
                "TotalServiciosGravados" => $this->fn($costos['total_serv_gravados']),
                "TotalServiciosExentos" => $this->fn($costos['total_serv_exentos']),
                "TotalServiciosExonerados" => $this->fn($costos['total_serv_exonerados']),
                "TotalMercanciaGravada" => $this->fn($costos['total_merc_gravada']),
                "TotalMercanciaExenta" => $this->fn($costos['total_merc_exenta']),
                "TotalMercanciaExonerada" => $this->fn($costos['total_merc_exonerada']),
                "TotalGravados" => $this->fn($costos['total_gravados']),
                "TotalExentos" => $this->fn($costos['total_exentos']),
                "TotalExonerado" => $this->fn($costos['total_exonerado']),
                "TotalVentas" => $this->fn($costos['total_ventas']),
                "TotalDescuentos" => $this->fn($costos['total_descuentos']),
                "TotalVentasNeta" => $this->fn($costos['total_ventas_neta']),
                "TotalImpuestos" => $this->fn($costos['total_impuestos']),
                "TotalIVADevuelto" => $this->fn($costos['total_iva_devuelto']),
                "TotalOtrosCargos" => $this->fn($costos['total_otros_cargos']),
                "TotalComprobante" => $this->fn($costos['total_comprobante']),
                "Otros" => trim($otros) == "" ? "-" : trim($otros),
                "TipoDocumento" => $receptor == null ? TIQUETE_ELECTRONICO : FACTURA_ELECTRONICA,
                "CodigoPais" => CODIGO_PAIS,
                "ConsecutivoFormateado" => $this->formatearConsecutivo($factura->Factura_Consecutivo),
                "Situacion" => $situacion,
                "CodigoSeguridad" => rand(10000000,99999999),
                "RespuestaHaciendaEstado" => "sin_enviar",
                "CorreoEnviadoReceptor" => 0,
                "CodigoActividad" => $emisor->CodigoActividad
            );

            if($receptor != NULL){
                $data["ReceptorNombre"] = $receptor->Cliente_Nombre." ".$receptor->Cliente_Apellidos;
                $data["ReceptorTipoIdentificacion"] = $this->getTipoIdentificacionCliente($receptor->Cliente_Tipo_Cedula);
                $data["ReceptorIdentificacion"] = $receptor->Cliente_Cedula;
                $data["ReceptorProvincia"] = $receptor->Provincia;
                $data["ReceptorCanton"] = str_pad($receptor->Canton,2,"0", STR_PAD_LEFT);
                $data["ReceptorDistrito"] = str_pad($receptor->Distrito,2,"0", STR_PAD_LEFT);
                $data["ReceptorBarrio"] = str_pad($receptor->Barrio,2,"0", STR_PAD_LEFT);
                $data["ReceptorCodigoPaisTelefono"] = $receptor->Codigo_Pais_Telefono;
                $data["ReceptorTelefono"] = str_replace("-", "", $receptor->Cliente_Telefono);
                $data["ReceptorCodigoPaisFax"] = $receptor->Codigo_Pais_Fax;
                $data["ReceptorFax"] = str_replace("-", "", $receptor->Numero_Fax);
                $data["ReceptorEmail"] = $receptor->Cliente_Correo_Electronico;
            }

            $this->db->insert("tb_55_factura_electronica", $data);

            foreach ($articulos as $art){
                $data = array(
                    "Cantidad" => $art["cantidad"],
                    "UnidadMedida" => $art["unidadMedida"],
                    "Detalle" => $art["detalle"],
                    "PrecioUnitario" => $art["precioUnitario"],
                    "MontoTotal" => $art["montoTotal"],
                    "MontoDescuento" => $art["montoDescuento"],
                    "NaturalezaDescuento" => $art["naturalezaDescuento"],
                    "Subtotal" => $art["subtotal"],
                    "BaseImponible" => $art["base_imponible"],
                    "ImpuestoObject" => json_encode($art["impuesto"]),
                    "MontoTotalLinea" => $art["montoTotalLinea"],
                    "Consecutivo" => $factura->Factura_Consecutivo,
                    "Sucursal" => $factura->TB_02_Sucursal_Codigo,
                    "Codigo" => $art["codigo"],
                    "TipoCodigo" => $art["tipoCodigo"],
                    "CodigoCabys" => $art["codigoCabys"]
                );

                $this->db->insert("tb_56_articulos_factura_electronica", $data);
            }

            return array("situacion" => $situacion, "fecha" => $fechaFacturaActual);
        }

        function generarClaveYConsecutivoParaFacturaElectronica($consecutivo, $sucursal, $api = NULL){
            $this->db->select("EmisorTipoIdentificacion, EmisorIdentificacion, CodigoPais, ConsecutivoFormateado, Situacion, CodigoSeguridad, TipoDocumento");
            $this->db->from("tb_55_factura_electronica");
            $this->db->where("Consecutivo", $consecutivo);
            $this->db->where("Sucursal", $sucursal);

            $query = $this->db->get();
            if($query->num_rows()>0){
                if($api == NULL){
                    require_once PATH_API_HACIENDA;
                    $api = new API_FE();
                }
                $row = $query->result()[0];
                $tipoIdentificacion = "fisico";
                switch($row->EmisorTipoIdentificacion){
                    case "01":
                        $tipoIdentificacion = "fisico";
                    break;
                    case "02":
                        $tipoIdentificacion = "juridico";
                    break;
                    case "03":
                        $tipoIdentificacion = "dimex";
                    break;
                    case "04":
                        $tipoIdentificacion = "nite";
                    break;
                }
                if($claveRs = $api->createClave($tipoIdentificacion, $row->EmisorIdentificacion, $row->CodigoPais, $row->ConsecutivoFormateado, $row->Situacion, $row->CodigoSeguridad, $row->TipoDocumento)){
                    $data = array(
                        "Clave" => $claveRs["clave"],
                        "ConsecutivoHacienda" => $claveRs["consecutivo"]
                    );
                    $this->db->where("Consecutivo", $consecutivo);
                    $this->db->where("Sucursal", $sucursal);
                    $this->db->update("tb_55_factura_electronica", $data);
                    return $data;
                }
            }
            return false;
        }

        function generarXMLFactura($consecutivo, $sucursal, $api = NULL){
            $this->db->from("tb_55_factura_electronica");
            $this->db->where("Consecutivo", $consecutivo);
            $this->db->where("Sucursal", $sucursal);
            $query = $this->db->get();
            if($query->num_rows()>0){
                if($api == NULL){
                    require_once PATH_API_HACIENDA;
                    $api = new API_FE();
                }
                $factura = $query->result()[0];
                $this->db->from("tb_56_articulos_factura_electronica");
                $this->db->where("Consecutivo", $consecutivo);
                $this->db->where("Sucursal", $sucursal);
                $query = $this->db->get();
                if($query->num_rows()>0){
                    $articulos = $query->result();
                    $xmlRes = $api->crearXMLFactura($factura->Clave,
                                                    $factura->ConsecutivoHacienda,
                                                    $factura->FechaEmision,

                                                    $factura->EmisorNombre,
                                                    $factura->EmisorTipoIdentificacion,
                                                    $factura->EmisorIdentificacion,
                                                    $factura->EmisorNombreComercial,
                                                    $factura->EmisorProvincia,
                                                    $factura->EmisorCanton,
                                                    $factura->EmisorDistrito,
                                                    $factura->EmisorBarrio,
                                                    $factura->EmisorOtrasSennas,
                                                    $factura->EmisorCodigoPaisTelefono,
                                                    $factura->EmisorTelefono,
                                                    $factura->EmisorCodigoPaisFax,
                                                    $factura->EmisorFax,
                                                    $factura->EmisorEmail,

                                                    $factura->ReceptorNombre,
                                                    $factura->ReceptorTipoIdentificacion,
                                                    $factura->ReceptorIdentificacion,
                                                    $factura->ReceptorProvincia,
                                                    $factura->ReceptorCanton,
                                                    $factura->ReceptorDistrito,
                                                    $factura->ReceptorBarrio,
                                                    $factura->ReceptorCodigoPaisTelefono,
                                                    $factura->ReceptorTelefono,
                                                    $factura->ReceptorCodigoPaisFax,
                                                    $factura->ReceptorFax,
                                                    $factura->ReceptorEmail,

                                                    $factura->CondicionVenta,
                                                    $factura->PlazoCredito,
                                                    $factura->MedioPago,
                                                    $factura->CodigoMoneda,
                                                    $factura->TipoCambio,

                                                    $this->fn($factura->TotalServiciosGravados),
                                                    $this->fn($factura->TotalServiciosExentos),
                                                    $this->fn($factura->TotalMercanciaGravada),
                                                    $this->fn($factura->TotalMercanciaExenta),
                                                    $this->fn($factura->TotalGravados),
                                                    $this->fn($factura->TotalExentos),
                                                    $this->fn($factura->TotalVentas),
                                                    $this->fn($factura->TotalDescuentos),
                                                    $this->fn($factura->TotalVentasNeta),
                                                    $this->fn($factura->TotalImpuestos),
                                                    $this->fn($factura->TotalComprobante),

                                                    $factura->Otros,
                                                    $this->prepararArticulosParaXML($articulos),

                                                    $factura->CodigoActividad,
                                                    $factura->TotalServiciosExonerados,
                                                    $factura->TotalMercanciaExonerada,
                                                    $factura->TotalExonerado,
                                                    $factura->TotalIVADevuelto,
                                                    $factura->TotalOtrosCargos);
                    if($xmlRes){
                        $data = array(
                            "Consecutivo" => $consecutivo,
                            "Sucursal" => $sucursal,
                            "XMLSinFirmar" => $xmlRes["xml"]
                        );
                        $this->db->where("Consecutivo", $consecutivo);
                        $this->db->where("Sucursal", $sucursal);
                        $this->db->insert("tb_65_cuerpo_factura_electronica", $data);
                        return $data;
                    }
                }
            }
            return false;
        }

        function firmarXMLFactura($consecutivo, $sucursal, $api = NULL){
            $this->db->from("tb_55_factura_electronica");
            $this->db->where("Consecutivo", $consecutivo);
            $this->db->where("Sucursal", $sucursal);
            $query = $this->db->get();
            if($query->num_rows()>0){

                //Hay que traer el cuerpo
                $this->db->from("tb_65_cuerpo_factura_electronica");
                $this->db->where("Consecutivo", $consecutivo);
                $this->db->where("Sucursal", $sucursal);
                $queryCuerpo = $this->db->get();    
                if($queryCuerpo->num_rows()>0){
                    if($api == NULL){
                        require_once PATH_API_HACIENDA;
                        $api = new API_FE();
                    }  
                        
                    $factura = $query->result()[0];
                    $facturaCuerpo = $queryCuerpo->result()[0];
                    $this->db->from("tb_02_sucursal");
                    $this->db->where("Codigo", $sucursal);
                    $query = $this->db->get();
                    if($query->num_rows()>0){
                        $empresa = $query->result()[0];
                        if($xmlFirmado = $api->firmarDocumento($empresa->Token_Certificado_Tributa, $facturaCuerpo->XMLSinFirmar, $empresa->Pass_Certificado_Tributa, $factura->TipoDocumento)){
                            $data = array(
                                "XMLFirmado" => $xmlFirmado
                            );
                            $this->db->where("Consecutivo", $consecutivo);
                            $this->db->where("Sucursal", $sucursal);
                            $this->db->update("tb_65_cuerpo_factura_electronica", $data);
    
                            // Guardarmos el XML firmado en un archivo
                            $this->storeFile($factura->Clave.".xml", "fe", null, base64_decode($xmlFirmado, $factura->FechaEmision));
    
                            return $data;
                        }
                    }
                }
            }
            return false;
        }

        function enviarFacturaElectronicaAHacienda($consecutivo, $sucursal, $api = NULL){
            $this->db->from("tb_55_factura_electronica");
            $this->db->where("Consecutivo", $consecutivo);
            $this->db->where("Sucursal", $sucursal);
            $query = $this->db->get();
            if($query->num_rows()>0){
                if($api == NULL){
                    require_once PATH_API_HACIENDA;
                    $api = new API_FE();
                }

                //Hay que traer el cuerpo
                $this->db->from("tb_65_cuerpo_factura_electronica");
                $this->db->where("Consecutivo", $consecutivo);
                $this->db->where("Sucursal", $sucursal);
                $queryCuerpo = $this->db->get();    
                if($queryCuerpo->num_rows()>0){

                    $factura = $query->result()[0];
                    $facturaCuerpo = $queryCuerpo->result()[0];
                    $this->db->from("tb_02_sucursal");
                    $this->db->where("Codigo", $sucursal);
                    $query = $this->db->get();
                    if($query->num_rows()>0){
                        $empresa = $query->result()[0];
                        if($tokenData = $api->solicitarToken($empresa->Ambiente_Tributa, $empresa->Usuario_Tributa, $empresa->Pass_Tributa)){
                            if($resEnvio = $api->enviarDocumento($empresa->Ambiente_Tributa, $factura->Clave, $factura->FechaEmision, $factura->EmisorTipoIdentificacion, $factura->EmisorIdentificacion, $factura->ReceptorTipoIdentificacion, $factura->ReceptorIdentificacion, $tokenData["access_token"], $facturaCuerpo->XMLFirmado)){
                                $data = array(
                                    "RespuestaHaciendaEstado" => "procesando",
                                    "FechaRecibidoPorHacienda" => date("y/m/d : H:i:s")
                                );
                                $this->db->where("Consecutivo", $consecutivo);
                                $this->db->where("Sucursal", $sucursal);
                                $this->db->update("tb_55_factura_electronica", $data);

                                return true;
                                //return $this->getEstadoFacturaHacienda($api, $empresa, $factura, $tokenData, $consecutivo, $sucursal);
                            }else{
                                $data = array(
                                    "RespuestaHaciendaEstado" => "fallo_envio"
                                );
                                $this->db->where("Consecutivo", $consecutivo);
                                $this->db->where("Sucursal", $sucursal);
                                $this->db->update("tb_55_factura_electronica", $data);
                                log_message('error', "Error al enviar la factura a Hacienda | Consecutivo: $consecutivo | Sucursal: $sucursal");
                            }
                        }else{
                            $data = array(
                                "RespuestaHaciendaEstado" => "fallo_token"
                            );
                            $this->db->where("Consecutivo", $consecutivo);
                            $this->db->where("Sucursal", $sucursal);
                            $this->db->update("tb_55_factura_electronica", $data);
                            log_message('error', "Error al generar el token para envio de factura | Consecutivo: $consecutivo | Sucursal: $sucursal");
                        }
                    }else{
                        log_message('error', "No existe empresa para su envio | Consecutivo: $consecutivo | Sucursal: $sucursal");
                    }
                }else{
                    log_message('error', "No existe cuerpo de factura para su envio | Consecutivo: $consecutivo | Sucursal: $sucursal");
                }                
            }else{
                log_message('error', "No existe factura para su envio | Consecutivo: $consecutivo | Sucursal: $sucursal");
            }
            return false;
        }

        public function getEstadoFacturaHacienda($api, $empresa, $factura, $tokenData, $consecutivo, $sucursal){
            // Obtener resultado de la factura
            $resCheck = array();
            $counter = 0;
            do {
                if($counter > 0){ //Solo esperar despues del primer intento
                    sleep(2);
                }                
                $counter++;
                $mensajeError = "";
                $resCheck = $api->revisarEstadoAceptacion($empresa->Ambiente_Tributa, $factura->Clave, $tokenData["access_token"], $mensajeError);
                log_message("error", "Mensaje de error: [$mensajeError]");
                log_message('error', "Revisando estado de factura en Hacienda | Consecutivo: $consecutivo | Sucursal: $sucursal");

                if($mensajeError == "NO_HA_SIDO_RECIBIDO"){
                    log_message("error", "Factura Consecutivo: $consecutivo | Sucursal: $sucursal no ha sido recibida, vamos a enviarla de nuevo");
                    $this->enviarFacturaElectronicaAHacienda($consecutivo, $sucursal);
                }

            } while (trim(strtolower($resCheck["data"]["ind-estado"])) == "procesando" && $counter < 5);

            if($resCheck["status"]){
                $estado = trim(strtolower($resCheck["data"]["ind-estado"]));
                $xmlRespuesta = isset($resCheck["data"]["respuesta-xml"]) ? trim($resCheck["data"]["respuesta-xml"]) : "NO XML FROM HACIENDA";
                $data = array(
                    "RespuestaHaciendaEstado" => $estado,
                    "RespuestaHaciendaFecha" => date("y/m/d : H:i:s")
                );
                $this->db->where("Consecutivo", $consecutivo);
                $this->db->where("Sucursal", $sucursal);
                $this->db->update("tb_55_factura_electronica", $data);

                $data = array(
                    "RespuestaHaciendaXML" => $xmlRespuesta
                );
                $this->db->where("Consecutivo", $consecutivo);
                $this->db->where("Sucursal", $sucursal);
                $this->db->update("tb_65_cuerpo_factura_electronica", $data);

                log_message('error', "Se obtuvo el estado de hacienda <$estado> | Consecutivo: $consecutivo | Sucursal: $sucursal");

                // Guardarmos el XML firmado en un archivo
                //file_put_contents(PATH_DOCUMENTOS_ELECTRONICOS.$factura->Clave."-respuesta.xml",  base64_decode($xmlRespuesta);
                $this->storeFile($factura->Clave."-respuesta.xml", "fe", null, base64_decode($xmlRespuesta), $factura->FechaEmision);

                return array("status" => true, "estado_hacienda" => $estado);
            }else{
                log_message('error', "Error al revisar el estado de la factura en Hacienda | Consecutivo: $consecutivo | Sucursal: $sucursal");
            }
            return false;
        }

        public function regenerarFacturaElectronicaPorContingencia($consecutivo, $sucursal, $api = NULL){
            $this->db->from("tb_55_factura_electronica");
            $this->db->where("Consecutivo", $consecutivo);
            $this->db->where("Sucursal", $sucursal);
            $query = $this->db->get();
            if($query->num_rows()>0){
                $newData = array("Situacion" => "contingencia", "RespuestaHaciendaEstado" => "procesando");
                $this->db->where("Consecutivo", $consecutivo);
                $this->db->where("Sucursal", $sucursal);
                $this->db->update("tb_55_factura_electronica", $newData);

                if($api == NULL){
                    require_once PATH_API_HACIENDA;
                    $api = new API_FE();
                }
                if($resClave = $this->generarClaveYConsecutivoParaFacturaElectronica($consecutivo, $sucursal, $api)){
                    if($resXML = $this->generarXMLFactura($consecutivo, $sucursal, $api)){
                        if($resXMLFirmado = $this->firmarXMLFactura($consecutivo, $sucursal, $api)){
                            log_message('error', "Se regenero la factura para contingencia | Consecutivo: $consecutivo | Sucursal: $sucursal");
                            return true;
                        }else{
                            log_message('error', "Error al firmar el xml para factura de contingencia | Consecutivo: $consecutivo | Sucursal: $sucursal");
                        }
                    }else{
                        log_message('error', "Error al generar el xml para factura de contingencia | Consecutivo: $consecutivo | Sucursal: $sucursal");
                    }
                }else{
                    log_message('error', "Error al generar la clave para factura de contingencia | Consecutivo: $consecutivo | Sucursal: $sucursal");
                }
            }else{
                log_message('error', "No existe factura para generar su contingencia | Consecutivo: $consecutivo | Sucursal: $sucursal");
            }
            return false;
        }

        public function getFacturaElectronica($consecutivo, $sucursal){
            $sucursalFinal = $sucursal;
            if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
                $sucursalFinal = $this->sucursales_trueque[$sucursal];
            }
            $this->db->from("tb_55_factura_electronica");
            $this->db->where("Consecutivo", $consecutivo);
            $this->db->where("Sucursal", $sucursalFinal);
            $query = $this->db->get();
            if($query->num_rows()>0){
                return $query->result()[0];
            }else{
                return false;
            }
        }


        function marcarEnvioCorreoFacturaElectronica($sucursal, $consecutivo){
            $this->db->where("Consecutivo", $consecutivo);
            $this->db->where("Sucursal", $sucursal);
            $data = array(
                "CorreoEnviadoReceptor" => 1
            );
            $this->db->update("tb_55_factura_electronica", $data);
        }


























    function creditoDisponible($consecutivo, $Sucursal){
        if($facturaHead = $this->factura->getFacturasHeaders($consecutivo, $Sucursal)){
            //return "aaaaa";
            foreach($facturaHead as $row){
                    $cedula = $row->TB_03_Cliente_Cliente_Cedula;
                    $totalFactura = $row->Factura_Monto_Total;
            }
            if($creditos = $this->getCreditosClientePorSucursal($cedula, $Sucursal)){
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


    function validarCobrarFactura($consecutivo, $tipoPago){
        $facturaBODY['status']='error';
        $facturaBODY['error']='17'; //No se logro procesar la solicitud
        if(trim($consecutivo) != "" && is_array($tipoPago)){
            include PATH_USER_DATA;
            if(isset($tipoPago['tipo'])){
                // Si no es pago de credito, siempre tendra credito disponible
                // Si es pago a credito, lo marca como que no tiene para realizar la verificacion despues
                $tieneDisponibleCredito = $tipoPago['tipo'] == 'credito' ? false : true;
                //Realizamos la validacion si tiene o no credito
                if($tieneDisponibleCredito === false){
                    $tieneDisponibleCredito = $this->creditoDisponible($consecutivo, $data['Sucursal_Codigo']);
                }
                if($tieneDisponibleCredito){
                    if($factura = $this->getFacturasHeaders($consecutivo, $data['Sucursal_Codigo'])){
                        $factura = $factura[0];
                        if($cliente = $this->cliente->getClientes_Cedula($factura->TB_03_Cliente_Cliente_Cedula)){
                            $facturaBODY['factura']=$factura;
                            $facturaBODY['cliente']=$cliente[0];
                            $this->validarEmpresaYClienteCobrarFactura($facturaBODY);
                            if($facturaBODY["status"] == 'success'){
                                unset($facturaBODY['error']);
                            }
                        }else{
                            $facturaBODY['error']='25'; //No existe cliente
                        }
                    }else{
                        $facturaBODY['error']='19'; //Error no existe esa factura
                    }
                }else{
                    $facturaBODY['error']='24'; //Error no tiene credito disponible
                }
            }else{
                $facturaBODY['error']='18'; //Error de no leer encabezado del URL DE TIPO DE PAGO
            }
        }else{
            $facturaBODY['error']='16'; //Error de no leer encabezado del URL
        }
        return $facturaBODY;
    }

    function validarEmpresaYClienteCobrarFactura(&$facturaBODY){
        $facturaBODY['status']='error';
        if($empresaData = $this->empresa->getEmpresa($facturaBODY["factura"]->TB_02_Sucursal_Codigo)){
            $empresaData = $empresaData[0];
            if($empresaData->Sucursal_Estado == 1){ // Sucursal esta activa
                if($empresaData->Provincia>0 && $empresaData->Canton>0 && $empresaData->Distrito>0 && $empresaData->Barrio>0){
                    if(filter_var($empresaData->Sucursal_Email, FILTER_VALIDATE_EMAIL)){
                        if( (trim($empresaData->Usuario_Tributa) != "" &&
                            trim($empresaData->Pass_Tributa) != "" &&
                            trim($empresaData->Ambiente_Tributa) != "" &&
                            trim($empresaData->Token_Certificado_Tributa) != "" &&
                            trim($empresaData->Pass_Certificado_Tributa) != "") || $empresaData->RequiereFE == 0){
                            $facturaBODY["empresa"] = $empresaData;
                            if($articulosFactura = $this->getArticulosFactura($facturaBODY["factura"]->Factura_Consecutivo, $facturaBODY["factura"]->TB_02_Sucursal_Codigo)){
                                $costos = array(
                                    "total_serv_gravados" => 0,
                                    "total_serv_exentos" => 0,
                                    "total_serv_exonerados" => 0,
                                    "total_merc_gravada" => 0,
                                    "total_merc_exenta" => 0,
                                    "total_merc_exonerada" => 0,
                                    "total_gravados" => 0,
                                    "total_exentos" => 0,
                                    "total_exonerado" => 0,
                                    "total_ventas" => 0,
                                    "total_descuentos" => 0,
                                    "total_ventas_neta" => 0,
                                    "total_impuestos" => 0,
                                    "total_iva_devuelto" => 0,
                                    "total_otros_cargos" => 0,
                                    "total_comprobante" => 0
                                );
                                $artFinales = array();
                                foreach($articulosFactura as $a){
                                    $linea = $this->getDetalleLinea($a, $facturaBODY["cliente"]->Aplica_Retencion == 0);
                                    array_push($artFinales, $linea);

                                    $isMercancia = !in_array($a->UnidadMedida, $this->codigosUnidadDeServivicios);

                                    if($a->Articulo_Factura_Exento == 0){
                                        if($isMercancia){
                                            $costos["total_merc_gravada"] += $linea["montoTotal"];
                                        }else{
                                            $costos["total_serv_gravados"] += $linea["montoTotal"];
                                        }
                                        $costos["total_gravados"] += $linea["montoTotal"];
                                    }else{
                                        if($isMercancia){
                                            $costos["total_merc_exenta"] += $linea["montoTotal"];
                                            $costos["total_merc_exonerada"] += $linea["montoTotal"];
                                        }else{
                                            $costos["total_serv_exentos"] += $linea["montoTotal"];
                                            $costos["total_serv_exonerados"] += $linea["montoTotal"];
                                        }
                                        $costos["total_exentos"] += $linea["montoTotal"];
                                        $costos["total_exonerado"] += $linea["montoTotal"];
                                    }
                                    $costos["total_ventas"] += $linea["montoTotal"];

                                    if(isset($linea["montoDescuento"])){
                                        $costos["total_descuentos"] += $linea["montoDescuento"];
                                    }

                                    $impuesto = $linea["impuesto"][0]["monto"];
                                    $costos["total_impuestos"] += $impuesto;
                                }
                                $costos["total_exonerado"] =  $costos["total_serv_exonerados"] + $costos["total_merc_exonerada"];
                                $costos["total_ventas_neta"] = $costos["total_ventas"] - $costos["total_descuentos"];
                                $costos["total_comprobante"] = $costos["total_ventas_neta"] + $costos["total_impuestos"] + $costos["total_otros_cargos"];
                                $facturaBODY['articulos'] = $artFinales;
                                $facturaBODY['costos'] = $costos;
                                $facturaBODY['status']='success';
                                $facturaBODY['articulosOriginales'] = $articulosFactura;
                            }else{
                                // Factura no tiene articulos
                                $facturaBODY['error']='15';
                            }
                        }else{
                            // Empresa no tiene los valores minimos para crear FE
                            $facturaBODY['error']='30';
                        }
                    }else{
                        // Empresa debe tener un correo electrónico válido
                        $facturaBODY['error']='29';
                    }
                }else{
                    // Empresa debe actualizar domicilio
                    $facturaBODY['error']='28';
                }
            }else{
                // Empresa esta deshabilitada
                $facturaBODY['error']='27';
            }
        }else{
            // No existe empresa
            $facturaBODY['error']='26';
        }
    }

    public function envioHacienda($resFacturaElectronica, $responseCheck){
        $feStatus = array("status"=>false, "message" => "", "estado"=>"");
        // Si hay conexion por lo tanto enviar FE a Hacienda de una
        if($resFacturaElectronica["data"]["situacion"] == "normal"){
            if($resEnvio = $this->enviarFacturaElectronicaAHacienda($responseCheck["factura"]->Factura_Consecutivo, $responseCheck["factura"]->TB_02_Sucursal_Codigo)){
                if($resEnvio["estado_hacienda"] == "rechazado"){
                    log_message('error', "Factura fue RECHAZADA por Hacienda, debemos generar su respectiva nota de credito | Consecutivo: {$responseCheck["factura"]->Factura_Consecutivo} | Sucursal: {$responseCheck["factura"]->TB_02_Sucursal_Codigo}");
                    // Realizar Nota Credito
                    $feStatus["message"] = "La factura electrónica fue RECHAZADA por Hacienda, deberá generarla de nuevo. La factura ha sido ANULADA.";
                    $feStatus["estado"] = "rechazado";
                }else if($resEnvio["estado_hacienda"] == "aceptado"){
                    $feStatus["message"] = "Factura fue ACEPTADA por Hacienda";
                    $feStatus["status"] = true;
                    $feStatus["estado"] = "aceptado";
                    log_message('error', "Factura fue ACEPTADA por Hacienda | Consecutivo: {$responseCheck["factura"]->Factura_Consecutivo} | Sucursal: {$responseCheck["factura"]->TB_02_Sucursal_Codigo}");
                }else{
                    $feStatus["message"] = "Factura se envió a Hacienda pero no fue rechazada, ni aceptada";
                    $feStatus["estado"] = "no_envio";
                    log_message('error', "Hacienda envio otro estado {$resEnvio["estado_hacienda"]} | Consecutivo: {$responseCheck["factura"]->Factura_Consecutivo} | Sucursal: {$responseCheck["factura"]->TB_02_Sucursal_Codigo}");
                }
            }else{
                log_message('error', "No se pudo enviar la factura a Hacienda, debemos marcarla como contingencia | Consecutivo: {$responseCheck["factura"]->Factura_Consecutivo} | Sucursal: {$responseCheck["factura"]->TB_02_Sucursal_Codigo}");
                // Realizar documento de contingencia, porque al enviar a Hacienda algo fallo
                // Pasos a seguir
                //    1) Cambiar estado a contingencia
                //    2) Regenerar y actualizar clave
                //    3) Regenerar y actualizar XML
                //    5) Regenerar y actualizar XML Firmado
                $this->regenerarFacturaElectronicaPorContingencia($responseCheck["factura"]->Factura_Consecutivo, $responseCheck["factura"]->TB_02_Sucursal_Codigo);

                $feStatus["message"] = "Factura no se pudo enviar a Hacienda por fallo no reconocido";
                $feStatus["estado"] = "no_envio";
            }
        }else{
            $feStatus["message"] = "Factura no se pudo enviar a Hacienda por falta de internet";
            $feStatus["estado"] = "no_envio";
        }

        $_SESSION["flash_fe"] = $feStatus;
        return $feStatus;
    }


    function getFacturaElectronicaByClave($clave){
        $this->db->from("tb_55_factura_electronica");
        $this->db->where("Clave", $clave);
        $query = $this->db->get();

        if($query->num_rows() == 0){
            return false;
        }else{
            return $query->result()[0];
        }
    }


    function getFacturasRecibidasHacienda(){
        $this->db->where_in("RespuestaHaciendaEstado", array("recibido", "procesando"));
        $this->db->from("tb_55_factura_electronica");
        $query = $this->db->get();
        if($query->num_rows() == 0){
            return false;
        }else{
            return $query->result();
        }
    }

    function getFacturasSinEnviarAHacienda(){
        $this->db->where_in("RespuestaHaciendaEstado", array("sin_enviar", "fallo_token", "fallo_envio"));
        $this->db->from("tb_55_factura_electronica");
        $query = $this->db->get();
        if($query->num_rows() == 0){
            return false;
        }else{
            return $query->result();
        }
    }


    function guardarPDFFactura($consecutivo, $sucursal){
        if($empresa = $this->empresa->getEmpresaImpresion($sucursal)){
            if($facturaHead = $this->factura->getFacturasHeadersImpresion($consecutivo, $sucursal)){
                if($fElectornica = $this->factura->getFacturaElectronica($consecutivo, $sucursal)){
                    if($facturaBody = $this->factura->getArticulosFacturaImpresion($consecutivo, $sucursal)){
                            //Valoramos si un credito para poner la fecha de vencimiento
                            if($facturaHead[0] -> tipo == 'credito'){
                                    $diasCredito = $this->factura->getCreditoClienteDeFactura($consecutivo, $sucursal, $facturaHead[0] -> cliente_ced);
                                    $facturaHead[0] -> diasCredito = $diasCredito;
                                    $date = strtotime("+$diasCredito days", strtotime($facturaHead[0] -> fecha) );
                                    $facturaHead[0] -> fechaVencimiento = date('d-m-Y',$date);
                            }elseif($facturaHead[0] -> tipo == 'mixto'){
                                    $cantidadPagaTarjeta = $this->factura->getMontoPagoTarjetaMixto($sucursal, $consecutivo);
                                    $cantidadPagaContado = $facturaHead[0]->total - $cantidadPagaTarjeta;

                                    //Valorar si fue en colones o dolares
                                    if($facturaHead[0] -> moneda == 'dolares'){
                                            $cantidadPagaTarjeta = $cantidadPagaTarjeta/$facturaHead[0] -> cambio;
                                            $cantidadPagaContado = $cantidadPagaContado/$facturaHead[0] -> cambio;
                                    }

                                    $facturaHead[0] -> cantidadTarjeta = $cantidadPagaTarjeta;
                                    $facturaHead[0] -> cantidadContado = $cantidadPagaContado;
                            }elseif($facturaHead[0] -> tipo == 'apartado'){
                                    $abono = $this->factura->getAbonoApartado($sucursal, $consecutivo);
                                    //Valorar si fue en colones o dolares
                                    if($facturaHead[0] -> moneda == 'dolares'){
                                            $abono = $abono/$facturaHead[0] -> cambio;
                                    }
                                    $facturaHead[0] -> abono = $abono;
                            }
                            $facturaHead[0]->consecutivoH = $fElectornica->ConsecutivoHacienda;
                            $facturaHead[0]->clave = $fElectornica->Clave;
                            $facturaHead[0]->isTE = $fElectornica->ReceptorNombre == null;
                            $this->impresion_m->facturaPDF($empresa, $facturaHead, $facturaBody, true);
                    }else{
                        log_message('error', "No se genero el PDF de factura, no existen los articulos de la factura | Consecutivo: $consecutivo | Sucursal: $sucursal");
                    }
                }else{
                    log_message('error', "No se genero el PDF de factura, no existe la factura electronica | Consecutivo: $consecutivo | Sucursal: $sucursal");
                }
            }else{
                    log_message('error', "No se genero el PDF de factura, no existe la factura | Consecutivo: $consecutivo | Sucursal: $sucursal");
            }
        }else{
                log_message('error', "No se genero el PDF de factura, no existe la empresa | Consecutivo: $consecutivo | Sucursal: $sucursal");
        }
    }












    /*

         _____          _                         _
        |  ___|_ _  ___| |_ _   _ _ __ __ _    __| | ___
        | |_ / _` |/ __| __| | | | '__/ _` |  / _` |/ _ \
        |  _| (_| | (__| |_| |_| | | | (_| | | (_| |  __/
        |_|  \__,_|\___|\__|\__,_|_|  \__,_|  \__,_|\___|

          ____
         / ___|___  _ __ ___  _ __  _ __ __ _
        | |   / _ \| '_ ` _ \| '_ \| '__/ _` |
        | |__| (_) | | | | | | |_) | | | (_| |
         \____\___/|_| |_| |_| .__/|_|  \__,_|
                             |_|

    */

    function crearFacturaCompraElectronica($emisor, $receptor, $factura, $costos, $articulos){
        $feedback["status"] = false;

        //Fix de cedula para desampa
        if($receptor->Sucursal_Cedula == 31013507850){
            $receptor->Sucursal_Cedula = 3101350785;
        }

        $responseData = $this->guardarDatosBasicosFacturaDeComprasElectronica($emisor, $receptor, $factura, $costos, $articulos);

        if($resClave = $this->generarClaveYConsecutivoParaFacturaDeCompraElectronica($factura["consecutivo"], $factura["sucursal"])){
            if($resXML = $this->generarXMLFacturaCompraElectronica($factura["consecutivo"], $factura["sucursal"])){
                if($resXMLFirmado = $this->firmarXMLFacturaDeCompra($factura["consecutivo"], $factura["sucursal"])){
                    $feedback["data"] = $responseData;
                    $feedback["data"]["clave"] = $resClave["Clave"];
                    $feedback["status"] = true;
                    unset($feedback['error']);
                    log_message('error', "Se genero bien el XML firmado FEC | Consecutivo: {$factura["consecutivo"]} | Sucursal: ".$factura["sucursal"]);
                }else{
                    // ERROR AL FIRMAR EL XML DE FE
                    $feedback['error']='54';
                    log_message('error', "Error al firmar el XML FEC | Consecutivo: {$factura["consecutivo"]} | Sucursal: ".$factura["sucursal"]);
                }
            }else{
                // ERROR AL GENERAR EL XML DE FE
                $feedback['error']='53';
                log_message('error', "Error al generar el XML FEC | Consecutivo: {$factura["consecutivo"]} | Sucursal: ".$factura["sucursal"]);
            }
        }else{
            // ERROR AL GENERAR LA CLAVE
            $feedback["error"] = '52';
            log_message('error', "Error al generar la clave FEC | Consecutivo: {$factura["consecutivo"]} | Sucursal: ".$factura["sucursal"]);
        }
        return $feedback;
    }

    function guardarDatosBasicosFacturaDeComprasElectronica($emisor, $receptor, $factura, $costos, $articulos){
        // Eliminamos informacion antigua de la misma factura
        $this->db->where("Consecutivo", $factura["consecutivo"]);
        $this->db->where("Sucursal", $factura["sucursal"]);
        $this->db->delete("tb_62_articulos_factura_compra_electronica");

        $this->db->where("Consecutivo", $factura["consecutivo"]);
        $this->db->where("Sucursal", $factura["sucursal"]);
        $this->db->delete("tb_61_factura_compra_electronica");

        // Guardamos el encabezado de la factura
        require_once PATH_API_HACIENDA;
        $api = new API_FE();
        $situacion = $api->internetIsOnline() ? "normal" : "sininternet";

        // Agregamos la info nueva
        $data = array(
            "Consecutivo" => $factura["consecutivo"],
            "Sucursal" => $factura["sucursal"],
            "FechaEmision" => $factura["fecha"],
            "EmisorNombre" => $emisor["nombre"],
            "EmisorTipoIdentificacion" => $emisor["tipoIdentificacion"],
            "EmisorIdentificacion" => $emisor["identificacion"],
            "EmisorProvincia" => $emisor["provincia"],
            "EmisorCanton" => str_pad($emisor["canton"],2,"0", STR_PAD_LEFT),
            "EmisorDistrito" => str_pad($emisor["distrito"],2,"0", STR_PAD_LEFT),
            "EmisorOtrasSennas" => $emisor["direccion"],
            "EmisorEmail" => $emisor["email"],
            "CondicionVenta" => $factura["condicionVenta"],
            "PlazoCredito" => $factura["plazoCredito"],
            "MedioPago" => $factura["tipoPago"],
            "CodigoMoneda" => $factura["moneda"],
            "TipoCambio" => $factura["tipoCambio"],
            "TotalServiciosGravados" => $this->fn($costos['total_serv_gravados']),
            "TotalServiciosExentos" => $this->fn($costos['total_serv_exentos']),
            "TotalServiciosExonerados" => $this->fn($costos['total_serv_exonerados']),
            "TotalMercanciaGravada" => $this->fn($costos['total_merc_gravada']),
            "TotalMercanciaExenta" => $this->fn($costos['total_merc_exenta']),
            "TotalMercanciaExonerada" => $this->fn($costos['total_merc_exonerada']),
            "TotalGravados" => $this->fn($costos['total_gravados']),
            "TotalExentos" => $this->fn($costos['total_exentos']),
            "TotalExonerado" => $this->fn($costos['total_exonerado']),
            "TotalVentas" => $this->fn($costos['total_ventas']),
            "TotalDescuentos" => $this->fn($costos['total_descuentos']),
            "TotalVentasNeta" => $this->fn($costos['total_ventas_neta']),
            "TotalImpuestos" => $this->fn($costos['total_impuestos']),
            "TotalIVADevuelto" => $this->fn($costos['total_iva_devuelto']),
            "TotalOtrosCargos" => $this->fn($costos['total_otros_cargos']),
            "TotalComprobante" => $this->fn($costos['total_comprobante']),
            "TipoDocumento" => FACTURA_COMPRA_ELECTRONICA,
            "CodigoPais" => CODIGO_PAIS,
            "ConsecutivoFormateado" => $this->formatearConsecutivo($factura["consecutivo"]),
            "Situacion" => $situacion,
            "CodigoSeguridad" => rand(10000000,99999999),
            "RespuestaHaciendaEstado" => "sin_enviar",
            "CorreoEnviadoReceptor" => 0,
            "CodigoActividad" => $emisor["codigoActividad"]
        );

        if($receptor != NULL){
            $data["ReceptorNombre"] = $receptor->Sucursal_Nombre;
            $data["ReceptorTipoIdentificacion"] = $receptor->Tipo_Cedula;
            $data["ReceptorIdentificacion"] = $receptor->Sucursal_Cedula;
            $data["ReceptorProvincia"] = $receptor->Provincia;
            $data["ReceptorCanton"] = str_pad($receptor->Canton,2,"0", STR_PAD_LEFT);
            $data["ReceptorDistrito"] = str_pad($receptor->Distrito,2,"0", STR_PAD_LEFT);
            $data["ReceptorEmail"] = $receptor->Sucursal_Email;
        }

        $this->db->insert("tb_61_factura_compra_electronica", $data);

        foreach ($articulos as $art){
            $data = array(
                "Cantidad" => $art["cantidad"],
                "UnidadMedida" => $art["unidadMedida"],
                "Detalle" => $art["detalle"],
                "PrecioUnitario" => $art["precioUnitario"],
                "MontoTotal" => $art["montoTotal"],
                "BaseImponible" => $art["base_imponible"],
                "MontoDescuento" => $art["montoDescuento"],
                "NaturalezaDescuento" => $art["naturalezaDescuento"],
                "Subtotal" => $art["subtotal"],
                "ImpuestoObject" => json_encode($art["impuesto"]),
                "MontoTotalLinea" => $art["montoTotalLinea"],
                "Consecutivo" => $factura["consecutivo"],
                "Sucursal" => $factura["sucursal"],
                "Codigo" => $art["codigo"],
                "TipoCodigo" => $art["tipoCodigo"],
                "CodigoCabys" => $art["codigoCabys"]
            );

            $this->db->insert("tb_62_articulos_factura_compra_electronica", $data);
        }
        return array("situacion" => $situacion, "fecha" => $factura["fecha"]);
    }

    function getNuevoConsecutivoFEC($sucursal){
        $result = $this->db->query("select MAX(Consecutivo) as Consecutivo from tb_61_factura_compra_electronica where Sucursal = $sucursal");

        if($result->result()[0]->Consecutivo == NULL){
            return 1;
        }else{
            return $result->result()[0]->Consecutivo + 1;
        }
    }

    function generarClaveYConsecutivoParaFacturaDeCompraElectronica($consecutivo, $sucursal, $api = NULL){
        $this->db->select("ReceptorTipoIdentificacion, ReceptorIdentificacion, CodigoPais, ConsecutivoFormateado, Situacion, CodigoSeguridad, TipoDocumento");
        $this->db->from("tb_61_factura_compra_electronica");
        $this->db->where("Consecutivo", $consecutivo);
        $this->db->where("Sucursal", $sucursal);

        $query = $this->db->get();
        if($query->num_rows()>0){
            if($api == NULL){
                require_once PATH_API_HACIENDA;
                $api = new API_FE();
            }
            $row = $query->result()[0];
            $tipoIdentificacion = "fisico";
            switch($row->ReceptorTipoIdentificacion){
                case "01":
                    $tipoIdentificacion = "fisico";
                break;
                case "02":
                    $tipoIdentificacion = "juridico";
                break;
                case "03":
                    $tipoIdentificacion = "dimex";
                break;
                case "04":
                    $tipoIdentificacion = "nite";
                break;
            }
            if($claveRs = $api->createClave($tipoIdentificacion, $row->ReceptorIdentificacion, $row->CodigoPais, $row->ConsecutivoFormateado, $row->Situacion, $row->CodigoSeguridad, $row->TipoDocumento)){
                $data = array(
                    "Clave" => $claveRs["clave"],
                    "ConsecutivoHacienda" => $claveRs["consecutivo"]
                );
                $this->db->where("Consecutivo", $consecutivo);
                $this->db->where("Sucursal", $sucursal);
                $this->db->update("tb_61_factura_compra_electronica", $data);
                return $data;
            }
        }
        return false;
    }

    function generarXMLFacturaCompraElectronica($consecutivo, $sucursal, $api = NULL){
        $this->db->from("tb_61_factura_compra_electronica");
        $this->db->where("Consecutivo", $consecutivo);
        $this->db->where("Sucursal", $sucursal);
        $query = $this->db->get();
        if($query->num_rows()>0){
            if($api == NULL){
                require_once PATH_API_HACIENDA;
                $api = new API_FE();
            }
            $factura = $query->result()[0];
            $this->db->from("tb_62_articulos_factura_compra_electronica");
            $this->db->where("Consecutivo", $consecutivo);
            $this->db->where("Sucursal", $sucursal);
            $query = $this->db->get();
            if($query->num_rows()>0){
                $articulos = $query->result();
                $xmlRes = $api->crearXMLFactura($factura->Clave,
                                                $factura->ConsecutivoHacienda,
                                                $factura->FechaEmision,

                                                $factura->EmisorNombre,
                                                $factura->EmisorTipoIdentificacion,
                                                $factura->EmisorIdentificacion,
                                                $factura->EmisorNombre,
                                                $factura->EmisorProvincia,
                                                $factura->EmisorCanton,
                                                $factura->EmisorDistrito,
                                                null,
                                                $factura->EmisorOtrasSennas,
                                                null,
                                                null,
                                                null,
                                                null,
                                                $factura->EmisorEmail,

                                                $factura->ReceptorNombre,
                                                $factura->ReceptorTipoIdentificacion,
                                                $factura->ReceptorIdentificacion,
                                                $factura->ReceptorProvincia,
                                                $factura->ReceptorCanton,
                                                $factura->ReceptorDistrito,
                                                null,
                                                null,
                                                null,
                                                null,
                                                null,
                                                $factura->ReceptorEmail,

                                                $factura->CondicionVenta,
                                                $factura->PlazoCredito,
                                                $factura->MedioPago,
                                                $factura->CodigoMoneda,
                                                $factura->TipoCambio,

                                                $factura->TotalServiciosGravados,
                                                $factura->TotalServiciosExentos,
                                                $factura->TotalMercanciaGravada,
                                                $factura->TotalMercanciaExenta,
                                                $factura->TotalGravados,
                                                $factura->TotalExentos,
                                                $factura->TotalVentas,
                                                $factura->TotalDescuentos,
                                                $factura->TotalVentasNeta,
                                                $factura->TotalImpuestos,
                                                $factura->TotalComprobante,

                                                null,
                                                $this->prepararArticulosParaXML($articulos),

                                                $factura->CodigoActividad,
                                                $factura->TotalServiciosExonerados,
                                                $factura->TotalMercanciaExonerada,
                                                $factura->TotalExonerado,
                                                $factura->TotalIVADevuelto,
                                                $factura->TotalOtrosCargos,
                                                true);
                if($xmlRes){
                    $data = array(
                        "XMLSinFirmar" => $xmlRes["xml"]
                    );
                    $this->db->where("Consecutivo", $consecutivo);
                    $this->db->where("Sucursal", $sucursal);
                    $this->db->update("tb_61_factura_compra_electronica", $data);
                    return $data;
                }
            }
        }
        return false;
    }

    function firmarXMLFacturaDeCompra($consecutivo, $sucursal, $api = NULL){
        $this->db->from("tb_61_factura_compra_electronica");
        $this->db->where("Consecutivo", $consecutivo);
        $this->db->where("Sucursal", $sucursal);
        $query = $this->db->get();
        if($query->num_rows()>0){
            if($api == NULL){
                require_once PATH_API_HACIENDA;
                $api = new API_FE();
            }
            $factura = $query->result()[0];
            $this->db->from("tb_02_sucursal");
            $this->db->where("Codigo", $sucursal);
            $query = $this->db->get();
            if($query->num_rows()>0){
                $empresa = $query->result()[0];
                if($xmlFirmado = $api->firmarDocumento($empresa->Token_Certificado_Tributa, $factura->XMLSinFirmar, $empresa->Pass_Certificado_Tributa, $factura->TipoDocumento)){
                    $data = array(
                        "XMLFirmado" => $xmlFirmado
                    );
                    $this->db->where("Consecutivo", $consecutivo);
                    $this->db->where("Sucursal", $sucursal);
                    $this->db->update("tb_61_factura_compra_electronica", $data);

                    // Guardarmos el XML firmado en un archivo
                    $this->storeFile($factura->Clave.".xml", "fec", null, base64_decode($xmlFirmado), $factura->FechaEmision);

                    return $data;
                }
            }
        }
        return false;
    }

    function guardarPDFFacturaCompra($consecutivo, $sucursal){
        if($facturaElectronica = $this->getFacturaDeCompraElectronica($consecutivo, $sucursal)){
            if($leyenda = $this->empresa->getLeyendaEmpresa($sucursal)){
                if($articulosFEC = $this->getArticulosFacturaElectronicaCompra($consecutivo, $sucursal)){
                    $empresa = array(new stdClass());
                    $empresa[0]->nombre = $facturaElectronica->EmisorNombre;
                    $empresa[0]->administrador = "";
                    $empresa[0]->cedula = $facturaElectronica->EmisorIdentificacion;
                    $empresa[0]->telefono = "";
                    $empresa[0]->email = $facturaElectronica->EmisorEmail;
                    $empresa[0]->direccion = $facturaElectronica->EmisorOtrasSennas;
                    $empresa[0]->leyenda = $leyenda;

                    $factura = array(new stdClass());
                    $factura[0]->isTE = false;
                    $factura[0]->cliente_ced = $facturaElectronica->ReceptorIdentificacion;
                    $factura[0]->cliente_nom = $facturaElectronica->ReceptorNombre;
                    $factura[0]->consecutivoH = $facturaElectronica->ConsecutivoHacienda;
                    $factura[0]->fecha = $facturaElectronica->FechaEmision;
                    $factura[0]->moneda = "colones";
                    $factura[0]->estado = "cobrada";
                    $factura[0]->vendedor = "";
                    $factura[0]->tipo = $this->tiposdepago[$facturaElectronica->MedioPago];
                    $factura[0]->diasCredito = $facturaElectronica->PlazoCredito;
                    $factura[0]->fechaVencimiento = "";
                    $factura[0]->observaciones = "";
                    $factura[0]->subtotal = $facturaElectronica->TotalGravados - $facturaElectronica->TotalDescuentos;
                    $factura[0]->total_iva = $facturaElectronica->TotalImpuestos;
                    $factura[0]->total = $facturaElectronica->TotalComprobante;
                    $factura[0]->retencion = 0;
                    $factura[0]->clave = $facturaElectronica->Clave;
                    $factura[0]->isFEC = true;

                    $articulos = array();
                    foreach($articulosFEC as $a){
                        $ao = new stdClass();
                        $ao->cantidad = $a->Cantidad;
                        $ao->precio = $a->PrecioUnitario;
                        $ao->descuento = (($a->MontoDescuento * 100) / $a->PrecioUnitario) / $a->Cantidad;
                        $ao->codigo = $a->Codigo;
                        $ao->descripcion = $a->Detalle;
                        $ao->exento = false;
                        array_push($articulos, $ao);
                    }

                    $this->impresion_m->facturaPDF($empresa, $factura, $articulos, true);
                }else{
                    log_message('error', "No se genero el PDF de factura de compra, no se pudo obtener los articulos | Consecutivo: $consecutivo | Sucursal: $sucursal");
                }
            }else{
                log_message('error', "No se genero el PDF de factura de compra, no se pudo obtener leyenda | Consecutivo: $consecutivo | Sucursal: $sucursal");
            }
        }else{
            log_message('error', "No se genero el PDF de factura de compra, no existe la factura | Consecutivo: $consecutivo | Sucursal: $sucursal");
        }
    }

    function getFacturaDeCompraElectronica($consecutivo, $sucursal){
        $this->db->from("tb_61_factura_compra_electronica");
        $this->db->where("Consecutivo", $consecutivo);
        $this->db->where("Sucursal", $sucursal);
        $query = $this->db->get();
        if($query->num_rows()>0){
            return $query->result()[0];
        }else{
            return false;
        }
    }

    function getArticulosFacturaElectronicaCompra($consecutivo, $sucursal){
        $this->db->from("tb_62_articulos_factura_compra_electronica");
        $this->db->where("Consecutivo", $consecutivo);
        $this->db->where("Sucursal", $sucursal);
        $query = $this->db->get();
        if($query->num_rows()>0){
            return $query->result();
        }else{
            return false;
        }
    }

    function enviarFacturaElectronicaDeCompraAHacienda($consecutivo, $sucursal, $api = NULL){
        $this->db->from("tb_61_factura_compra_electronica");
        $this->db->where("Consecutivo", $consecutivo);
        $this->db->where("Sucursal", $sucursal);
        $query = $this->db->get();
        if($query->num_rows()>0){
            if($api == NULL){
                require_once PATH_API_HACIENDA;
                $api = new API_FE();
            }
            $factura = $query->result()[0];
            $this->db->from("tb_02_sucursal");
            $this->db->where("Codigo", $sucursal);
            $query = $this->db->get();
            if($query->num_rows()>0){
                $empresa = $query->result()[0];
                if($tokenData = $api->solicitarToken($empresa->Ambiente_Tributa, $empresa->Usuario_Tributa, $empresa->Pass_Tributa)){
                    if($resEnvio = $api->enviarDocumento($empresa->Ambiente_Tributa, $factura->Clave, $factura->FechaEmision, $factura->EmisorTipoIdentificacion, $factura->EmisorIdentificacion, $factura->ReceptorTipoIdentificacion, $factura->ReceptorIdentificacion, $tokenData["access_token"], $factura->XMLFirmado)){
                        $data = array(
                            "RespuestaHaciendaEstado" => "procesando",
                            "FechaRecibidoPorHacienda" => date("y/m/d : H:i:s")
                        );
                        $this->db->where("Consecutivo", $consecutivo);
                        $this->db->where("Sucursal", $sucursal);
                        $this->db->update("tb_61_factura_compra_electronica", $data);

                        return $this->getEstadoFacturaCompraHacienda($api, $empresa, $factura, $tokenData, $consecutivo, $sucursal);
                    }else{
                        $data = array(
                            "RespuestaHaciendaEstado" => "fallo_envio"
                        );
                        $this->db->where("Consecutivo", $consecutivo);
                        $this->db->where("Sucursal", $sucursal);
                        $this->db->update("tb_61_factura_compra_electronica", $data);
                        log_message('error', "Error al enviar la factura a Hacienda FEC | Consecutivo: $consecutivo | Sucursal: $sucursal");
                    }
                }else{
                    $data = array(
                        "RespuestaHaciendaEstado" => "fallo_token"
                    );
                    $this->db->where("Consecutivo", $consecutivo);
                    $this->db->where("Sucursal", $sucursal);
                    $this->db->update("tb_61_factura_compra_electronica", $data);
                    log_message('error', "Error al generar el token para envio de factura FEC | Consecutivo: $consecutivo | Sucursal: $sucursal");
                }
            }else{
                log_message('error', "No existe empresa para su envio FEC | Consecutivo: $consecutivo | Sucursal: $sucursal");
            }
        }else{
            log_message('error', "No existe factura para su envio FEC | Consecutivo: $consecutivo | Sucursal: $sucursal");
        }
        return false;
    }

    public function getEstadoFacturaCompraHacienda($api, $empresa, $factura, $tokenData, $consecutivo, $sucursal){
        // Obtener resultado de la factura
        $resCheck = array();
        $counter = 0;
        do {
            sleep(2);
            $counter++;
            $resCheck = $api->revisarEstadoAceptacion($empresa->Ambiente_Tributa, $factura->Clave, $tokenData["access_token"]);
            log_message('error', "Revisando estado de factura en Hacienda FEC | Consecutivo: $consecutivo | Sucursal: $sucursal");
        } while (trim(strtolower($resCheck["data"]["ind-estado"])) == "procesando" && $counter < 5);

        if($resCheck["status"]){
            $estado = trim(strtolower($resCheck["data"]["ind-estado"]));
            $xmlRespuesta = isset($resCheck["data"]["respuesta-xml"]) ? trim($resCheck["data"]["respuesta-xml"]) : "NO XML FROM HACIENDA";
            $data = array(
                "RespuestaHaciendaEstado" => $estado,
                "RespuestaHaciendaFecha" => date("y/m/d : H:i:s"),
                "RespuestaHaciendaXML" => $xmlRespuesta
            );
            $this->db->where("Consecutivo", $consecutivo);
            $this->db->where("Sucursal", $sucursal);
            $this->db->update("tb_61_factura_compra_electronica", $data);
            log_message('error', "Se obtuvo el estado de hacienda <$estado> FEC | Consecutivo: $consecutivo | Sucursal: $sucursal");

            // Guardarmos el XML firmado en un archivo
            //file_put_contents(PATH_DOCUMENTOS_ELECTRONICOS.$factura->Clave."-respuesta.xml",  base64_decode($xmlRespuesta);
            $this->storeFile($factura->Clave."-respuesta.xml", "fec", null, base64_decode($xmlRespuesta), $factura->FechaEmision);

            return array("status" => true, "estado_hacienda" => $estado);
        }else{
            log_message('error', "Error al revisar el estado de la factura en Hacienda FEC | Consecutivo: $consecutivo | Sucursal: $sucursal");
        }
        return false;
    }

    public function checkArchivosCorreoFE($archivos, $factura){
        foreach($archivos as $tipo => $ruta){
            switch($tipo){
                case "xml":
                    if (!file_exists($ruta)) {
                        $this->storeFile($factura->Clave.".xml", "fe", null, base64_decode($factura->XMLFirmado), $factura->FechaEmision);
                    }
                break;
                case "respuesta":
                    if (!file_exists($ruta)) {
                        $this->storeFile($factura->Clave."-respuesta.xml", "fe", null, base64_decode($factura->RespuestaHaciendaXML), $factura->FechaEmision);
                    }
                break;
                case "pdf":
                    if (!file_exists($ruta)) {
                        $this->guardarPDFFactura($factura->Consecutivo, $factura->Sucursal);
                    }
                break;
            }
        }
    }

}


?>
