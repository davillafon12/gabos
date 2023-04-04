<?php
Class cliente extends CI_Model
{
	private $contador=1;


	function existe_Cliente($cedula){
		$this -> db -> select('Cliente_Cedula');
		$this -> db -> from('tb_03_cliente');
		$this -> db -> where('Cliente_Cedula', $cedula);
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

	function obtener_Imagen_Cliente($cedula){
		$this -> db -> select('Cliente_Imagen_URL');
		$this -> db -> from('tb_03_cliente');
		$this -> db -> where('Cliente_Cedula', $cedula);
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


	function registrar($nombre, $apellidos, $cedula, $tipo_cedula, $fecha_nacimiento, $celular, $telefono, $pais, $direccion, $observaciones, $direccion_url_imagen, $correo, $estado_Cliente, $calidad_Cliente, $tipo_pago_Cliente, $isSucursal, $exento, $aplicaRetencion, $usuarioID, $sucursalID, $codptel, $codpcel, $codpfax, $fax, $prov, $canton, $distr, $barrio, $noReceptor, $sucursalLiga = 2)
	{

		if($this->existe_Cliente($cedula)){
			return false;
		}
		else{
			date_default_timezone_set("America/Costa_Rica");
		    $Current_datetime = date("y/m/d : H:i:s", now());
			$data = array(
	                        'Cliente_Nombre'=>$nombre,
	                        'Cliente_Apellidos'=>$apellidos,
							'Cliente_Cedula'=>$cedula,
							'Cliente_Tipo_Cedula'=>$tipo_cedula,
							'Fecha_Nacimiento'=>$fecha_nacimiento,
							'Cliente_Celular'=>$celular,
							'Cliente_Telefono'=>$telefono,
							'Cliente_Fecha_Ingreso'=>$Current_datetime,
							'Cliente_Pais'=>$pais,
							'Cliente_Direccion'=>$direccion,
							'Cliente_Observaciones'=>$observaciones,
							'Cliente_Imagen_URL'=>$direccion_url_imagen,
							'Cliente_Correo_Electronico'=>$correo,
							'Cliente_Estado'=>$estado_Cliente,
							'Cliente_Calidad'=>$calidad_Cliente,
							'Cliente_Numero_Pago'=>$tipo_pago_Cliente,
							'Cliente_EsSucursal' => $isSucursal,
							'Cliente_EsExento' => $exento,
							'Aplica_Retencion' => $aplicaRetencion,
							'Sucursal_Ingreso' => $sucursalID,
							'Usuario_Ingreso' => $usuarioID,
							'Codigo_Pais_Telefono' => $codptel,
							'Codigo_Pais_Celular' => $codpcel,
							'Codigo_Pais_Fax' => $codpfax,
							'Numero_Fax' => $fax,
							'Provincia' => $prov,
							'Canton' => $canton,
							'Distrito' => $distr,
							'Barrio' => $barrio,
							'NoReceptor' => $noReceptor,
							'Empresa_Liga' => $sucursalLiga

	                    );
			try{
	        $this->db->insert('TB_03_Cliente',$data); }
			catch(Exception $e)
			{return false;}

			/*$data=array(); //Limpiamos el array data

			//Agregamos el descuento por separado a su tabla
			include '/../controllers/get_session_data.php';
			$arrayDescuento = array(
								'Descuento_cliente_porcentaje' =>$descuento,
								'TB_03_Cliente_Cliente_Cedula'=>$cedula,
								'TB_02_Sucursal_Codigo'=>$data['Sucursal_Codigo']
							);
			$this->db->insert('TB_21_Descuento_Cliente',$arrayDescuento);
			//Verificamos y retornamos si se guardo en base de datos


			//AGREGAMOS TOPE CREDITO
			$arrayCredito = array(
								'Credito_Cliente_Cantidad_Maxima' => $maxCredito,
								'TB_03_Cliente_Cliente_Cedula'=>$cedula,
								'TB_02_Sucursal_Codigo'=>$data['Sucursal_Codigo']
							);
			$this->db->insert('TB_25_Maximo_Credito_Cliente',$arrayCredito);*/

		}
		return $this->existe_Cliente($cedula);
	}
	function getClientes()
	{
		$this -> db -> select('*');
		$this -> db -> from('tb_03_cliente');
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

	function getClientes_Cedula($id)
	{
		$this -> db -> select('*');
		$this -> db -> from('tb_03_cliente');
		$this -> db -> where('Cliente_Cedula', $id);
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

	function isActivated($id)
	{
		$this -> db -> select('Cliente_Estado');
		$this -> db -> from('tb_03_cliente');
		$this -> db -> where('Cliente_Cedula', $id);
		$this -> db -> limit(1);
		$query = $this -> db -> get();
		$result = $query->result();

		foreach($result as $row)
        {
			if($row -> Cliente_Estado == 'activo')
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	function actualizar($id, $data)
	{
			$this->db->where('Cliente_Cedula', $id);
			$this->db->update('TB_03_Cliente' ,$data);
	}

	function getNombreCliente($id)
	{
		$this -> db -> select('*');
		$this -> db -> from('tb_03_cliente');
		$this -> db -> where('Cliente_Cedula', $id);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query->num_rows()==0)
		{return false;}
		else
		{
			include PATH_USER_DATA_DOUBLE; //Traemos la info para obtener la sucursal
			$result = $query->result();
			foreach($result as $row)
			{
                            $actualizar = ($row->Provincia < 1 || $row->Canton < 1 || $row->Distrito < 1 || $row->Barrio < 1);
                            $actualizar = !filter_var($row->Cliente_Correo_Electronico, FILTER_VALIDATE_EMAIL) || $actualizar;
                            $actualizar = $row->NoReceptor ? false : $actualizar;
                            return array('nombre'=>$row->Cliente_Nombre." ".$row->Cliente_Apellidos,
							 'estado'=>$row->Cliente_Estado,
							 'descuento'=>$this->getClienteDescuento($id, $data['Sucursal_Codigo']),
							 'exento' => $row->Cliente_EsExento,
							 'sucursal' => $row->Cliente_EsSucursal,
							 'retencion' => $row->Aplica_Retencion,
                                                         'actualizar' => $actualizar
							);
			}
		}
	}

	function tieneCreditosVencidosSinPagar($cedula, $sucursal){
		/*  SELECT * FROM tb_24_credito
			WHERE DATE_ADD(Credito_Fecha_Expedicion, INTERVAL Credito_Numero_Dias DAY) < CURDATE()
			AND Credito_Sucursal_Codigo
			AND Credito_Cliente_Cedula
			AND Credito_Saldo_Actual */
		$query = $this->db->query("SELECT * FROM tb_24_credito
									WHERE DATE_ADD(Credito_Fecha_Expedicion, INTERVAL Credito_Numero_Dias DAY) < CURDATE()
									AND Credito_Sucursal_Codigo = $sucursal
									AND Credito_Cliente_Cedula = '$cedula'
									AND Credito_Saldo_Actual > 1");
		return $query->num_rows()>0;
	}

	function getClienteDescuento($cedula, $sucursal){
		$this -> db -> select('*');
		$this -> db -> from('TB_21_Descuento_Cliente');
		$this -> db -> where('TB_03_Cliente_Cliente_Cedula', $cedula);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query->num_rows()==0)
		{return false;}
		else
		{
			$result = $query->result();
			foreach($result as $row)
			{
				$descuento = $row->Descuento_cliente_porcentaje;
				if($descuento<='0'){return false;}
				else{return $descuento;}
			}
		}
	}

	function clienteEsExentoDeIVA($cedula){
			$this -> db -> from('tb_03_cliente');
			$this -> db -> where('Cliente_Cedula', $cedula);
			$this -> db -> limit(1);
			$query = $this -> db -> get();
			if($query->num_rows()==0)
			{return false;}
			else
			{
				return $query->result()[0]->Cliente_EsExento;
			}
	}

	function clienteEsExentoDeRetencion($cedula){
			$this -> db -> from('tb_03_cliente');
			$this -> db -> where('Cliente_Cedula', $cedula);
			$this -> db -> limit(1);
			$query = $this -> db -> get();
			if($query->num_rows()==0)
			{return false;}
			else
			{
				return $query->result()[0]->Aplica_Retencion;
			}
	}

	function existeClienteDescuento($cedula, $sucursal){
		$this -> db -> select('*');
		$this -> db -> from('TB_21_Descuento_Cliente');
		$this -> db -> where('TB_03_Cliente_Cliente_Cedula', $cedula);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query->num_rows()==0)
		{return false;}
		else
		{return true;}
	}

	function existeClienteCredito($cedula, $sucursal){
		$this -> db -> select('*');
		$this -> db -> from('TB_25_Maximo_Credito_Cliente');
		$this -> db -> where('TB_03_Cliente_Cliente_Cedula', $cedula);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query->num_rows()==0)
		{return false;}
		else
		{return true;}
	}

	function getClienteMaximoCredito($cedula, $Sucursal){
		$this -> db -> select('Credito_Cliente_Cantidad_Maxima');
		$this -> db -> from('TB_25_Maximo_Credito_Cliente');
		$this -> db -> where('TB_03_Cliente_Cliente_Cedula', $cedula);
		$this -> db -> where('TB_02_Sucursal_Codigo', $Sucursal);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query->num_rows()==0)
		{return false;}
		else
		{
			$result = $query->result();
			foreach($result as $row)
			{
				return $row->Credito_Cliente_Cantidad_Maxima;
			}
		}
	}

	function getDescuentosDeClienteConProductos($cedula, $sucursal){
		$this -> db -> select('*');
		$this -> db -> from('TB_17_Descuento_Producto');
		$this -> db -> where('TB_03_Cliente_Cliente_Cedula', $cedula);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$query = $this -> db -> get();

		if($query->num_rows()==0)
		{return false;}
		else
		{
			return $query->result();
		}
	}

	function getDescuentosDeClienteConFamilias($cedula, $sucursal){
		$this -> db -> select('*');
		$this -> db -> from('TB_20_Descuento_Familia');
		$this -> db -> where('TB_03_Cliente_Cliente_Cedula', $cedula);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$query = $this -> db -> get();

		if($query->num_rows()==0)
		{return false;}
		else
		{
			return $query->result();
		}
	}

	function getNumeroPrecio($id)
	{
		$this -> db -> select('Cliente_Numero_Pago');
		$this -> db -> from('tb_03_cliente');
		$this -> db -> where('Cliente_Cedula', $id);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query->num_rows()==0)
		{return '3';} //Tarifa de cliente final no afiliado
		else
		{
			$result = $query->result();
			foreach($result as $row)
			{
				return $row->Cliente_Numero_Pago;
			}
		}

		//return '2'; //Por el momento retorna el tipo de cliente de contado
			//IMPLEMENTAR FUNCION
	}


	//SE AGREGO EL 15-07-14 POR DAVID/////////////////////////////////

	function getNombresClientesBusqueda($nombre){
		$this -> db -> select('Cliente_Cedula, Cliente_Nombre, Cliente_Apellidos');
		$this -> db -> from('tb_03_cliente');
		$this->db->like('Cliente_Nombre', $nombre);
        $this->db->or_like('Cliente_Apellidos', $nombre);
		$query = $this -> db -> get();

		if($query->num_rows()==0)
		{return false;} //No hay clientes con esos datos
		else
		{
			return $query->result();
			/*foreach($result as $row)
			{
				return $row->Cliente_Numero_Pago;
			}*/
		}
	}

	//CIERRE//////////////////////////////////////////////////////////

	function agregarDescuentoCliente($descuento, $sucursal, $cedula){
		$arrayDescuento = array(
							'Descuento_cliente_porcentaje' =>$descuento,
							'TB_03_Cliente_Cliente_Cedula'=>$cedula,
							'TB_02_Sucursal_Codigo'=>$sucursal
						);
		$this->db->insert('TB_21_Descuento_Cliente',$arrayDescuento);
	}

	function actualizarDescuentoCliente($descuento, $sucursal, $cedula){
		$this->db->where('TB_03_Cliente_Cliente_Cedula', $cedula);
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);

		$arrayDescuento = array(
							'Descuento_cliente_porcentaje' =>$descuento
						);

		$this->db->update('TB_21_Descuento_Cliente' ,$arrayDescuento);
	}

	function agregarCreditoCliente($credito, $sucursal, $cedula){
		$arrayDescuento = array(
							'Credito_Cliente_Cantidad_Maxima' =>$credito,
							'TB_03_Cliente_Cliente_Cedula'=>$cedula,
							'TB_02_Sucursal_Codigo'=>$sucursal
						);
		$this->db->insert('TB_25_Maximo_Credito_Cliente',$arrayDescuento);
	}

	function actualizarCreditoCliente($credito, $sucursal, $cedula){
		$this->db->where('TB_03_Cliente_Cliente_Cedula', $cedula);
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);

		$arrayDescuento = array(
							'Credito_Cliente_Cantidad_Maxima' =>$credito
						);

		$this->db->update('TB_25_Maximo_Credito_Cliente' ,$arrayDescuento);
	}


	function eliminarDescuentoProducto($idDescuentoProducto){
		$this->db->delete('TB_17_Descuento_Producto', array('Descuento_producto_id' => $idDescuentoProducto));
	}

	function existeDescuentoConProducto($codigo, $cedula, $sucursal){
		$this -> db -> select('*');
		$this -> db -> from('TB_17_Descuento_Producto');
		$this -> db -> where('TB_03_Cliente_Cliente_Cedula', $cedula);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('TB_06_Articulo_Articulo_Codigo', $codigo);
		$query = $this -> db -> get();

		if($query->num_rows()==0)
		{return false;}
		else
		{
			return $query->result();
		}
	}

	function agregarDescuentoDeProducto($codigo, $cedula, $sucursal, $descuentoPorcentaje, $familia){
		$numeroPrecio = $this->getNumeroPrecio($cedula);
		$precioArticulo = $this->articulo->getPrecioProducto($codigo, $numeroPrecio, $sucursal);
		$descuentoMonto = $precioArticulo - ($precioArticulo * ($descuentoPorcentaje)/100);
		$arrayDescuento = array(
							'TB_06_Articulo_Articulo_Codigo' =>$codigo,
							'TB_03_Cliente_Cliente_Cedula'=> $cedula,
							'Descuento_producto_monto' => $descuentoMonto,
							'Descuento_producto_porcentaje' => $descuentoPorcentaje,
							'TB_06_Articulo_TB_05_Familia_Familia_Codigo' => $familia,
							'TB_02_Sucursal_Codigo'=>$sucursal
						);
		$this->db->insert('TB_17_Descuento_Producto',$arrayDescuento);
	}

	function eliminarDescuentoFamilia($idDescuentoFamilia){
		$this->db->delete('TB_20_Descuento_Familia', array('Descuento_familia_id' => $idDescuentoFamilia));
	}

	function existeDescuentoConFamilia($codigo, $cedula, $sucursal){
		$this -> db -> select('*');
		$this -> db -> from('TB_20_Descuento_Familia');
		$this -> db -> where('TB_03_Cliente_Cliente_Cedula', $cedula);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('TB_05_Familia_Familia_Codigo', $codigo);
		$query = $this -> db -> get();

		if($query->num_rows()==0)
		{return false;}
		else
		{
			return $query->result();
		}
	}

	function agregarDescuentoDeFamilia($codigo, $cedula, $sucursal, $descuento){
		$arrayDescuento = array(
							'TB_05_Familia_Familia_Codigo' =>$codigo,
							'TB_03_Cliente_Cliente_Cedula'=> $cedula,
							'Descuento_familia_porcentaje' => $descuento,
							'TB_05_Familia_TB_02_Sucursal_Codigo' => $sucursal,
							'TB_02_Sucursal_Codigo'=>$sucursal
						);
		$this->db->insert('TB_20_Descuento_Familia',$arrayDescuento);
	}

	function verificarSiYaTieneAutorizacion($cedula, $secuencia){
		$this -> db -> select('*');
		$this -> db -> from('TB_16_Authclientes');
		$this -> db -> where('TB_03_Cliente_Cliente_Cedula', $cedula);
		$this -> db -> where('AuthClientes_Seq', $secuencia);
		$query = $this -> db -> get();

		if($query->num_rows()==0)
		{return false;}
		else
		{
			return $query->result();
		}
	}

	function agregarAutorizacion($persona, $cedula){
		$arrayAutorizacion = array(
									'AuthClientes_Cedula'=>$persona['cedula'],
									'AuthClientes_Nombre'=>$persona['nombre'],
									'AuthClientes_Apellidos'=>$persona['apellido'],
									'AuthClientes_Seq'=>$persona['secuencia'],
									'TB_03_Cliente_Cliente_Cedula'=>$cedula
									);
		$this->db->insert('TB_16_Authclientes',$arrayAutorizacion);
	}

	function actualizarImagenAutorizacion($cedula, $secuencia, $Imagen_URL){
		$arrayAutorizacion = array(
									'AuthClientes_Carta_URL'=>$Imagen_URL
									);
		$this->db->where('TB_03_Cliente_Cliente_Cedula', $cedula);
		$this->db->where('AuthClientes_Seq', $secuencia);

		$this->db->update('TB_16_Authclientes' ,$arrayAutorizacion);
	}

	function actualizarAutorizacion($persona, $cedula){
		$arrayAutorizacion = array(
									'AuthClientes_Cedula'=>$persona['cedula'],
									'AuthClientes_Nombre'=>$persona['nombre'],
									'AuthClientes_Apellidos'=>$persona['apellido']
									);

		$this->db->where('TB_03_Cliente_Cliente_Cedula', $cedula);
		$this->db->where('AuthClientes_Seq', $persona['secuencia']);

		$this->db->update('TB_16_Authclientes' ,$arrayAutorizacion);
	}


	function getFacturasConSaldo($cliente, $sucursal){
		$this->db->where('Credito_Vendedor_Sucursal', $sucursal);
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
			$sucursal = $this->sucursales_trueque[$sucursal];
		}

		$this->db->join("tb_07_factura","Credito_Factura_Consecutivo = Factura_Consecutivo");
		$this->db->where('Credito_Cliente_Cedula', $cliente);
		$this->db->where('Credito_Sucursal_Codigo', $sucursal);
		$this->db->where('TB_03_Cliente_Cliente_Cedula', $cliente);
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->where('Factura_Estado', 'cobrada');
		$this->db->where('Credito_Saldo_Actual !=', '0'); //Facturas con saldo
		$query = $this->db->get('tb_24_credito');
		if($query->num_rows()==0)
		{return false;}
		else
		{
			return $query->result();
		}
	}

	function getFacturasDeClienteEnSucursal($cliente, $sucursal){
		$this->load->model("factura", "", true);

		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->factura->getFacturasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("Factura_Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->factura->getFacturasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("Factura_Consecutivo", $facturas_trueque);
				}
		}
		//Solo cargamos facturas cobradas
		$this->db->where('Factura_Estado', 'cobrada');
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->where('TB_03_Cliente_Cliente_Cedula', $cliente);
		$query = $this->db->get('tb_07_factura');
		if($query->num_rows()==0)
		{return false;}
		else
		{
			return $query->result();
		}
	}

	function getFacturasDeClienteEnSucursalFiltradasCodigo($cliente, $sucursal, $codigo){
		//Solo cargamos facturas cobradas
		$query = $this->db->query("  SELECT tb_07_factura.Factura_Consecutivo,
											tb_07_factura.Factura_Fecha_Hora,
											tb_07_factura.Factura_Monto_Total
									 FROM   tb_07_factura,
											tb_08_articulos_factura
									 WHERE  tb_07_factura.Factura_Consecutivo = tb_08_articulos_factura.TB_07_Factura_Factura_Consecutivo
									 AND 	tb_07_factura.TB_03_Cliente_Cliente_Cedula = $cliente
									 AND 	tb_07_factura.TB_02_Sucursal_Codigo = $sucursal
									 AND 	tb_07_factura.Factura_Estado = 'cobrada'
									 AND 	tb_08_articulos_factura.Articulo_Factura_Codigo
									 LIKE   '%$codigo%'
									 GROUP BY tb_07_factura.Factura_Consecutivo");
		if($query->num_rows()==0)
		{return false;}
		else
		{
			return $query->result();
		}
	}

	function getFacturaDeClienteCobrada($consecutivo, $sucursal, $cliente){
		$this->load->model("factura", "", true);
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es trueque
				$facturas_trueque = $this->factura->getFacturasTrueque($sucursal);
				$sucursal = $this->sucursales_trueque[$sucursal];
				if(!empty($facturas_trueque)){
						$this->db->where_in("Factura_Consecutivo", $facturas_trueque);
				}
		}elseif($this->truequeHabilitado && $this->esUsadaComoSucursaldeRespaldo($sucursal)){
				$facturas_trueque = $this->factura->getFacturasTruequeResponde($this->getSucursalesTruequeFromSucursalResponde($sucursal));
				if(!empty($facturas_trueque)){
						$this->db->where_not_in("Factura_Consecutivo", $facturas_trueque);
				}
		}
		//Solo cargamos facturas cobradas
		$this->db->where('Factura_Estado', 'cobrada');
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->where('Factura_Consecutivo', $consecutivo);
		$this->db->where('TB_03_Cliente_Cliente_Cedula', $cliente);
		$query = $this->db->get('tb_07_factura');
		if($query->num_rows()==0)
		{return false;}
		else
		{
			return $query->result();
		}
	}

	function getMontoCompradoClienteRangoTiempo($cliente, $inicio, $final){
		$this->db->select("SUM(Factura_Monto_Total) AS TOTAL");
		$this->db->where('Factura_Estado', 'cobrada');
		$this->db->where('TB_03_Cliente_Cliente_Cedula', $cliente);
		$this->db->where('Factura_Fecha_Hora <', date("Y/m/d H:i:s", $final));
		$this->db->where('Factura_Fecha_Hora >', date("Y/m/d H:i:s", $inicio));
		$this->db->from('tb_07_factura');
		$query = $this->db->get();
		if($query->num_rows()==0)
		{return false;}
		else
		{
			$result = $query->result();
			return $result[0]->TOTAL;
		}
	}

	function eliminarAutorizacionCliente($secuencia, $cliente){
			$autorizacion = $this->verificarSiYaTieneAutorizacion($cliente, $secuencia);
			if($autorizacion){
					$nombreImagen = $autorizacion[0]->AuthClientes_Carta_URL;
					//Eliminamos el archivo de la imagen de la carta
					$ruta_a_preguntar = FCPATH.'application\\images\\cartas\\'.$nombreImagen;
					if (file_exists($ruta_a_preguntar)) {
			        unlink($ruta_a_preguntar);
			    }
			}
	    //Eliminamos el registro en la base de datos
			$this->db->where("AuthClientes_Seq", $secuencia);
			$this->db->where("TB_03_Cliente_Cliente_Cedula", $cliente);
			$this->db->delete("tb_16_authclientes");
	}

	function obtenerClientesParaTabla($columnaOrden, $tipoOrden, $busqueda, $inicio, $cantidad, $soloActivos = true){

		$estadoQuery = $soloActivos ? " AND Cliente_Estado = 'activo' " : "";

		return $this->db->query("
			SELECT 	Cliente_Cedula AS cedula,
					Cliente_Nombre AS nombre,
					Cliente_Apellidos AS apellidos,
					Cliente_Estado AS estado
			FROM tb_03_cliente
			WHERE (Cliente_Cedula LIKE '%$busqueda%' OR
				   Cliente_Nombre LIKE '%$busqueda%' OR
				   Cliente_Apellidos LIKE '%$busqueda%')
				   $estadoQuery
			ORDER BY $columnaOrden $tipoOrden
			LIMIT $inicio,$cantidad
		");
	}

	function obtenerClientesFiltradosParaTabla($columnaOrden, $tipoOrden, $busqueda, $inicio, $cantidad, $soloActivos = true){

		$estadoQuery = $soloActivos ? " AND Cliente_Estado = 'activo' " : "";

		return $this->db->query("
			SELECT 	Cliente_Cedula AS cedula,
					Cliente_Nombre AS nombre,
					Cliente_Apellidos AS apellidos,
					Cliente_Estado AS estado
			FROM tb_03_cliente
			WHERE (Cliente_Cedula LIKE '%$busqueda%' OR
				   Cliente_Nombre LIKE '%$busqueda%' OR
				   Cliente_Apellidos LIKE '%$busqueda%')
				   $estadoQuery
		");
	}

	function getTotalClientes($soloActivos = true){
		$this->db->from('tb_03_cliente');

		if($soloActivos){
			$this->db->where('Cliente_Estado', 'activo');
		}

		$query = $this -> db -> get();
		return $query -> num_rows();
	}

	function getCredito($factura, $sucursal, $cliente){
		$this->db->where("Credito_Factura_Consecutivo", $factura);
		$this->db->where("Credito_Sucursal_Codigo", $sucursal);
		$this->db->where("Credito_Cliente_Cedula", $cliente);
		$this->db->from('tb_24_credito');
		$query = $this -> db -> get();
		if($query->num_rows() == 0){
			return false;
		}else{
			return $query->result()[0];
		}
	}


	function obtenerBitacoraCliente($cedula){

		$query =  $this->db->query("
			SELECT  bc.Cliente_Cedula as 'Cedula',
					bc.Sucursal,
					suc.Sucursal_Nombre as 'Nombre',
					bc.Usuario,
					usu.Usuario_Nombre_Usuario as 'Nombre_Usuario' ,
					case bc.Trans_Tipo
						when 'Ingreso_Cliente' THEN 'Registro Cliente'
						when 'Edicion_Cliente' THEN 'Actualización del Cliente'
						when 'Actualiza_DesClien' THEN 'Actualización descuento Cliente'
						when 'Agrega_DesCliente' THEN 'Agregación descuento Cliente'
						when 'Actualiza_Credito' THEN 'Actualización crédito Cliente'
						when 'Agregar_Credito' THEN 'Agregación crédito Cliente'
						when 'Elimina_DesProducto' THEN 'Eliminación descuento producto'
						when 'Agrega_DesProducto' THEN 'Agrega descuento producto'
						when 'Elimina_DesFamilia' THEN 'Eliminación descuento familia'
						when 'Agrega_DesFamilia' THEN 'Agrega descuento familia'
					end as 'Tipo_Transaccion',
					bc.Trans_Fecha_Hora as 'Fecha',
					bc.Trans_Descripcion as 'Descripcion'
			FROM tb_60_bitacora_cliente bc
			INNER JOIN tb_02_sucursal suc on bc.Sucursal = suc.Codigo
			INNER JOIN tb_01_usuario usu on bc.Usuario = usu.Usuario_Codigo
			WHERE bc.Cliente_Cedula = '$cedula'");
		if($query->num_rows() == 0){
			return false;
		}else{
			return $query;
		}
	}
}


?>