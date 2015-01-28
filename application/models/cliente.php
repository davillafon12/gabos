<?php
Class cliente extends CI_Model
{
	private $contador=1;


	function existe_Cliente($cedula){
		$this -> db -> select('Cliente_Cedula');
		$this -> db -> from('TB_03_Cliente');
		$this -> db -> where('Cliente_Cedula', mysql_real_escape_string($cedula));
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
		$this -> db -> from('TB_03_Cliente');
		$this -> db -> where('Cliente_Cedula', mysql_real_escape_string($cedula));
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


	function registrar($nombre, $apellidos, $cedula, $tipo_cedula, $carnet_cliente, $celular, $telefono, $pais, $direccion, $observaciones, $direccion_url_imagen, $correo, $estado_Cliente, $calidad_Cliente, $tipo_pago_Cliente, $isSucursal, $exento)
	{
		
		if($this->existe_Cliente($cedula)){
			return false;
		}
		else{
			date_default_timezone_set("America/Costa_Rica");
		    $Current_datetime = date("y/m/d : H:i:s", now());
			$data = array(
	                        'Cliente_Nombre'=>mysql_real_escape_string($nombre),
	                        'Cliente_Apellidos'=>mysql_real_escape_string($apellidos), 
							'Cliente_Cedula'=>mysql_real_escape_string($cedula),
							'Cliente_Tipo_Cedula'=>mysql_real_escape_string($tipo_cedula),
							'Cliente_Carnet_Numero'=>$carnet_cliente,
							'Cliente_Celular'=>mysql_real_escape_string($celular),	
							'Cliente_Telefono'=>mysql_real_escape_string($telefono),	
							'Cliente_Fecha_Ingreso'=>mysql_real_escape_string($Current_datetime),	
							'Cliente_Pais'=>mysql_real_escape_string($pais),	
							'Cliente_Direccion'=>mysql_real_escape_string($direccion),	
							'Cliente_Observaciones'=>mysql_real_escape_string($observaciones),	
							'Cliente_Imagen_URL'=>mysql_real_escape_string($direccion_url_imagen),	
							'Cliente_Correo_Electronico'=>mysql_real_escape_string($correo),	
							'Cliente_Estado'=>mysql_real_escape_string($estado_Cliente),	
							'Cliente_Calidad'=>mysql_real_escape_string($calidad_Cliente),								
							'Cliente_Numero_Pago'=>mysql_real_escape_string($tipo_pago_Cliente),
							'Cliente_EsSucursal' => mysql_real_escape_string($isSucursal),
							'Cliente_EsExento' => mysql_real_escape_string($exento)
	                    );
			try{
	        $this->db->insert('TB_03_Cliente',$data); }
			catch(Exception $e)
			{return false;}
			
			/*$data=array(); //Limpiamos el array data 
			
			//Agregamos el descuento por separado a su tabla
			include '/../controllers/get_session_data.php';
			$arrayDescuento = array(
								'Descuento_cliente_porcentaje' =>mysql_real_escape_string($descuento),
								'TB_03_Cliente_Cliente_Cedula'=>mysql_real_escape_string($cedula),
								'TB_02_Sucursal_Codigo'=>$data['Sucursal_Codigo']
							);
			$this->db->insert('TB_21_Descuento_Cliente',$arrayDescuento);
			//Verificamos y retornamos si se guardo en base de datos
			
			
			//AGREGAMOS TOPE CREDITO
			$arrayCredito = array(
								'Credito_Cliente_Cantidad_Maxima' => mysql_real_escape_string($maxCredito),
								'TB_03_Cliente_Cliente_Cedula'=>mysql_real_escape_string($cedula),
								'TB_02_Sucursal_Codigo'=>$data['Sucursal_Codigo']
							);
			$this->db->insert('TB_25_Maximo_Credito_Cliente',$arrayCredito);*/
			
		}
		return $this->existe_Cliente($cedula);
	}
	function getClientes()
	{
		$this -> db -> select('*');
		$this -> db -> from('TB_03_Cliente');
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
		$this -> db -> from('TB_03_Cliente');		
		$this -> db -> where('Cliente_Cedula', mysql_real_escape_string($id));
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
		$this -> db -> from('TB_03_Cliente');
		$this -> db -> where('Cliente_Cedula', mysql_real_escape_string($id));
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
			$this->db->where('Cliente_Cedula', mysql_real_escape_string($id));
			$this->db->update('TB_03_Cliente' ,$data);
	}	
	
	function getNombreCliente($id)
	{
		$this -> db -> select('*');
		$this -> db -> from('TB_03_Cliente');
		$this -> db -> where('Cliente_Cedula', mysql_real_escape_string($id));
		$this -> db -> limit(1);
		$query = $this -> db -> get();
				
		if($query->num_rows()==0)
		{return false;}
		else
		{
			include '/../controllers/get_session_data.php'; //Traemos la info para obtener la sucursal
			$result = $query->result();
			foreach($result as $row)
			{						
				return array('nombre'=>$row->Cliente_Nombre." ".$row->Cliente_Apellidos,
							 'estado'=>$row->Cliente_Estado,
							 'descuento'=>$this->getClienteDescuento(mysql_real_escape_string($id), $data['Sucursal_Codigo']),
							 'exento' => $row->Cliente_EsExento
							);
			}
		}	
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
		$this -> db -> where('TB_03_Cliente_Cliente_Cedula', mysql_real_escape_string($cedula));
		$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
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
		$this -> db -> where('TB_03_Cliente_Cliente_Cedula', mysql_real_escape_string($cedula));
		$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
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
		$this -> db -> from('TB_03_Cliente');
		$this -> db -> where('Cliente_Cedula', mysql_real_escape_string($id));
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
		$this -> db -> from('TB_03_Cliente');
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
							'Descuento_cliente_porcentaje' =>mysql_real_escape_string($descuento),
							'TB_03_Cliente_Cliente_Cedula'=>mysql_real_escape_string($cedula),
							'TB_02_Sucursal_Codigo'=>$sucursal
						);
		$this->db->insert('TB_21_Descuento_Cliente',$arrayDescuento);
	}
	
	function actualizarDescuentoCliente($descuento, $sucursal, $cedula){
		$this->db->where('TB_03_Cliente_Cliente_Cedula', mysql_real_escape_string($cedula));
		$this->db->where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		
		$arrayDescuento = array(
							'Descuento_cliente_porcentaje' =>mysql_real_escape_string($descuento)
						);
						
		$this->db->update('TB_21_Descuento_Cliente' ,$arrayDescuento);
	}
	
	function agregarCreditoCliente($credito, $sucursal, $cedula){
		$arrayDescuento = array(
							'Credito_Cliente_Cantidad_Maxima' =>mysql_real_escape_string($credito),
							'TB_03_Cliente_Cliente_Cedula'=>mysql_real_escape_string($cedula),
							'TB_02_Sucursal_Codigo'=>$sucursal
						);
		$this->db->insert('TB_25_Maximo_Credito_Cliente',$arrayDescuento);
	}
	
	function actualizarCreditoCliente($credito, $sucursal, $cedula){
		$this->db->where('TB_03_Cliente_Cliente_Cedula', mysql_real_escape_string($cedula));
		$this->db->where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		
		$arrayDescuento = array(
							'Credito_Cliente_Cantidad_Maxima' =>mysql_real_escape_string($credito)
						);
						
		$this->db->update('TB_25_Maximo_Credito_Cliente' ,$arrayDescuento);
	}
	
	
	function eliminarDescuentoProducto($idDescuentoProducto){
		$this->db->delete('TB_17_Descuento_Producto', array('Descuento_producto_id' => mysql_real_escape_string($idDescuentoProducto))); 
	}
	
	function existeDescuentoConProducto($codigo, $cedula, $sucursal){
		$this -> db -> select('*');
		$this -> db -> from('TB_17_Descuento_Producto');
		$this -> db -> where('TB_03_Cliente_Cliente_Cedula', mysql_real_escape_string($cedula));
		$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this -> db -> where('TB_06_Articulo_Articulo_Codigo', mysql_real_escape_string($codigo));
		$query = $this -> db -> get();
		
		if($query->num_rows()==0)
		{return false;} 
		else
		{
			return $query->result();
		}
	}
	
	function agregarDescuentoDeProducto($codigo, $cedula, $sucursal, $descuento, $familia){
		$arrayDescuento = array(
							'TB_06_Articulo_Articulo_Codigo' =>mysql_real_escape_string($codigo),
							'TB_03_Cliente_Cliente_Cedula'=> mysql_real_escape_string($cedula),
							'Descuento_producto_porcentaje' => mysql_real_escape_string($descuento),
							'TB_06_Articulo_TB_05_Familia_Familia_Codigo' => mysql_real_escape_string($familia),
							'TB_02_Sucursal_Codigo'=>$sucursal
						);
		$this->db->insert('TB_17_Descuento_Producto',$arrayDescuento);
	}
	
	function eliminarDescuentoFamilia($idDescuentoFamilia){
		$this->db->delete('TB_20_Descuento_Familia', array('Descuento_familia_id' => mysql_real_escape_string($idDescuentoFamilia))); 
	}
	
	function existeDescuentoConFamilia($codigo, $cedula, $sucursal){
		$this -> db -> select('*');
		$this -> db -> from('TB_20_Descuento_Familia');
		$this -> db -> where('TB_03_Cliente_Cliente_Cedula', mysql_real_escape_string($cedula));
		$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this -> db -> where('TB_05_Familia_Familia_Codigo', mysql_real_escape_string($codigo));
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
							'TB_05_Familia_Familia_Codigo' =>mysql_real_escape_string($codigo),
							'TB_03_Cliente_Cliente_Cedula'=> mysql_real_escape_string($cedula),
							'Descuento_familia_porcentaje' => mysql_real_escape_string($descuento),
							'TB_05_Familia_TB_02_Sucursal_Codigo' => $sucursal,
							'TB_02_Sucursal_Codigo'=>$sucursal
						);
		$this->db->insert('TB_20_Descuento_Familia',$arrayDescuento);
	}

	function verificarSiYaTieneAutorizacion($cedula, $secuencia){
		$this -> db -> select('*');
		$this -> db -> from('TB_16_Authclientes');
		$this -> db -> where('TB_03_Cliente_Cliente_Cedula', mysql_real_escape_string($cedula));
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
									'AuthClientes_Cedula'=>mysql_real_escape_string($persona['cedula']),
									'AuthClientes_Nombre'=>mysql_real_escape_string($persona['nombre']),
									'AuthClientes_Apellidos'=>mysql_real_escape_string($persona['apellido']),
									'AuthClientes_Seq'=>mysql_real_escape_string($persona['secuencia']),
									'TB_03_Cliente_Cliente_Cedula'=>$cedula
									);
		$this->db->insert('TB_16_Authclientes',$arrayAutorizacion);
	}
	
	function actualizarImagenAutorizacion($cedula, $secuencia, $Imagen_URL){
		$arrayAutorizacion = array(
									'AuthClientes_Carta_URL'=>$Imagen_URL
									);
		$this->db->where('TB_03_Cliente_Cliente_Cedula', mysql_real_escape_string($cedula));
		$this->db->where('AuthClientes_Seq', mysql_real_escape_string($secuencia));
									
		$this->db->update('TB_16_Authclientes' ,$arrayAutorizacion);
	}
	
	function actualizarAutorizacion($persona, $cedula){
		$arrayAutorizacion = array(
									'AuthClientes_Cedula'=>mysql_real_escape_string($persona['cedula']),
									'AuthClientes_Nombre'=>mysql_real_escape_string($persona['nombre']),
									'AuthClientes_Apellidos'=>mysql_real_escape_string($persona['apellido'])									
									);
									
		$this->db->where('TB_03_Cliente_Cliente_Cedula', mysql_real_escape_string($cedula));
		$this->db->where('AuthClientes_Seq', mysql_real_escape_string($persona['secuencia']));
									
		$this->db->update('TB_16_Authclientes' ,$arrayAutorizacion);
	}
	
	
	function getFacturasConSaldo($cliente, $sucursal){
		$this->db->where('Credito_Cliente_Cedula', $cliente);
		$this->db->where('Credito_Sucursal_Codigo', $sucursal);
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
		$this->db->select("SUM('Factura_Monto_Total') AS TOTAL");
		$this->db->where('Factura_Estado', 'cobrada');
		$this->db->where('TB_03_Cliente_Cliente_Cedula', $cliente);
		$this->db->where('Factura_Fecha_Hora <', $final);
		$this->db->where('Factura_Fecha_Hora >', $inicio);
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
}


?>