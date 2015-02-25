<?php
Class articulo extends CI_Model
{
	function existe_Articulo($Codigo,$sucursal){
		$this -> db -> from('TB_06_Articulo');
		$this -> db -> where('Articulo_Codigo', mysql_real_escape_string($Codigo));
		$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		
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
	
	function getTotalArticulosEnSucursal($sucursal){
		$this->db->from('TB_06_Articulo');
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$query = $this -> db -> get();
		return $query -> num_rows();
	}
	
	function obtenerArticulosParaTabla($columnaOrden, $tipoOrden, $busqueda, $inicio, $cantidad, $sucursal){
		/*
			SELECT 	Articulo_Codigo AS codigo,
					Articulo_Descripcion AS descripcion,
					Articulo_Cantidad_Inventario AS inventario,
					Articulo_Descuento AS descuento
			FROM tb_06_articulo
			WHERE (Articulo_Codigo LIKE '%%' OR
				   Articulo_Descripcion LIKE '%%' OR
				   Articulo_Cantidad_Inventario LIKE '%%' OR
				   Articulo_Descuento LIKE '%%')
			AND    TB_02_Sucursal_Codigo = $sucursal
			ORDER BY Articulo_Codigo DESC
			LIMIT 40,60
		*/
		return $this->db->query("
			SELECT 	Articulo_Codigo AS codigo,
					Articulo_Descripcion AS descripcion,
					Articulo_Cantidad_Inventario AS inventario,
					Articulo_Descuento AS descuento
			FROM tb_06_articulo
			WHERE (Articulo_Codigo LIKE '%$busqueda%' OR
				   Articulo_Descripcion LIKE '%$busqueda%' OR
				   Articulo_Cantidad_Inventario LIKE '%$busqueda%' OR
				   Articulo_Descuento LIKE '%$busqueda%')
			AND    TB_02_Sucursal_Codigo = $sucursal
			ORDER BY $columnaOrden $tipoOrden
			LIMIT $inicio,$cantidad		
		");		
	}
	
	function obtenerArticulosParaTablaFiltrados($columnaOrden, $tipoOrden, $busqueda, $inicio, $cantidad, $sucursal){
		/*
			SELECT 	Articulo_Codigo AS codigo,
					Articulo_Descripcion AS descripcion,
					Articulo_Cantidad_Inventario AS inventario,
					Articulo_Descuento AS descuento
			FROM tb_06_articulo
			WHERE (Articulo_Codigo LIKE '%%' OR
				   Articulo_Descripcion LIKE '%%' OR
				   Articulo_Cantidad_Inventario LIKE '%%' OR
				   Articulo_Descuento LIKE '%%')
			ORDER BY Articulo_Codigo DESC
			LIMIT 40,60
		*/
		return $this->db->query("
			SELECT 	Articulo_Codigo AS codigo,
					Articulo_Descripcion AS descripcion,
					Articulo_Cantidad_Inventario AS inventario,
					Articulo_Descuento AS descuento
			FROM tb_06_articulo
			WHERE (Articulo_Codigo LIKE '%$busqueda%' OR
				   Articulo_Descripcion LIKE '%$busqueda%' OR
				   Articulo_Cantidad_Inventario LIKE '%$busqueda%' OR
				   Articulo_Descuento LIKE '%$busqueda%')	
			AND    TB_02_Sucursal_Codigo = $sucursal
		");		
	}
	
	function registrar($articulo_Codigo, $articulo_Descripcion, $articulo_Codigo_Barras, $articulo_Cantidad_Inventario, $articulo_Cantidad_Defectuoso, $articulo_Descuento, $Articulo_Imagen_URL, $Articulo_Exento, $TB_05_Familia_Familia_Codigo, $TB_02_Sucursal_Codigo, $costo, $precio1, $precio2, $precio3, $precio4, $precio5)
	{
		if($this->existe_Articulo($articulo_Codigo, $TB_02_Sucursal_Codigo)){
			return false;
		}
		else{
			$data = array(
	                        'Articulo_Codigo'=>mysql_real_escape_string($articulo_Codigo),
							'Articulo_Descripcion'=>mysql_real_escape_string($articulo_Descripcion),                        
							'Articulo_Codigo_Barras'=>mysql_real_escape_string($articulo_Codigo_Barras),                        
							'Articulo_Cantidad_Inventario'=>mysql_real_escape_string($articulo_Cantidad_Inventario),                        
							'Articulo_Cantidad_Defectuoso'=>mysql_real_escape_string($articulo_Cantidad_Defectuoso),                        
							'Articulo_Descuento'=>mysql_real_escape_string($articulo_Descuento), 
							'Articulo_Imagen_URL'=>mysql_real_escape_string($Articulo_Imagen_URL),
							'Articulo_Exento'=>mysql_real_escape_string($Articulo_Exento), 						                       
							'TB_05_Familia_Familia_Codigo'=>mysql_real_escape_string($TB_05_Familia_Familia_Codigo),
							'TB_02_Sucursal_Codigo'=>mysql_real_escape_string($TB_02_Sucursal_Codigo)
							
	                    );
			try{
	        	$this->db->insert('TB_06_Articulo',$data); 
	        	$this->registrar_Precio_Articulo(0, $costo, $articulo_Codigo, $TB_05_Familia_Familia_Codigo, $TB_02_Sucursal_Codigo);
	        	$this->registrar_Precio_Articulo(1, $precio1, $articulo_Codigo, $TB_05_Familia_Familia_Codigo, $TB_02_Sucursal_Codigo);
	        	$this->registrar_Precio_Articulo(2, $precio2, $articulo_Codigo, $TB_05_Familia_Familia_Codigo, $TB_02_Sucursal_Codigo);
	        	$this->registrar_Precio_Articulo(3, $precio3, $articulo_Codigo, $TB_05_Familia_Familia_Codigo, $TB_02_Sucursal_Codigo);
	        	$this->registrar_Precio_Articulo(4, $precio4, $articulo_Codigo, $TB_05_Familia_Familia_Codigo, $TB_02_Sucursal_Codigo);
	        	$this->registrar_Precio_Articulo(5, $precio5, $articulo_Codigo, $TB_05_Familia_Familia_Codigo, $TB_02_Sucursal_Codigo);


	    	}
			catch(Exception $e)
			{return false;}
		}
		return $this->existe_Articulo($articulo_Codigo, $TB_02_Sucursal_Codigo);
	}

	/*function getCantidadArticulos()
	{
		return $this->db->count_all('TB_06_Articulo');
	}*/

	function actualizar($codigo, $sucursal, $data)
	{
		$this->db->where('Articulo_Codigo', mysql_real_escape_string($codigo));
		$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this->db->update('TB_06_Articulo' ,$data);
	}	
	
	function actualizarPrecios($codigo, $sucursal, $precios){
		for($i = 0; $i<6; $i++){ //6 puesto que solo se manejan 5 precios por el momento
			$datos = array(
							'Precio_Monto' => $precios["p$i"]
						);
			$this->db->where('Precio_Numero', $i);	
			$this->db->where('TB_06_Articulo_Articulo_Codigo',$codigo);
			$this->db->where('TB_06_Articulo_TB_02_Sucursal_Codigo',$sucursal);			
			$this->db->update('tb_11_precios', $datos);
		}
	}

	function get_Articulos($sucursal)
	{
		$this -> db -> select('*');
		$this -> db -> from('TB_06_Articulo');
		$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
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
	
	function get_Articulo($codigo, $sucursal)
	{
		$this -> db -> select('*');
		$this -> db -> from('TB_06_Articulo');
		$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this -> db -> where('Articulo_Codigo', mysql_real_escape_string($codigo));
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
	
	function get_ArticuloFactura($codigo, $sucursal, $consecutivo)
	{
		$this -> db -> select('*');
		$this -> db -> from('TB_08_Articulos_Factura');
		$this -> db -> where('TB_07_Factura_TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this -> db -> where('Articulo_Factura_Codigo', mysql_real_escape_string($codigo));
		$this -> db -> where('TB_07_Factura_Factura_Consecutivo', mysql_real_escape_string($consecutivo));
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
			$items = $query->result();
			foreach($items as $item){
				return $item;
			}
			//return $query->result();
		}
		else
		{
		  return false;
		}
	}
	
	function getCantidadArticuloFactura($codigo, $sucursal, $consecutivo)
	{
		$this -> db -> select('Articulo_Factura_Cantidad');
		$this -> db -> from('TB_08_Articulos_Factura');
		$this -> db -> where('TB_07_Factura_TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this -> db -> where('Articulo_Factura_Codigo', mysql_real_escape_string($codigo));
		$this -> db -> where('TB_07_Factura_Factura_Consecutivo', mysql_real_escape_string($consecutivo));
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
			$items = $query->result();
			foreach($items as $item){
				return $item->Articulo_Factura_Cantidad;
			}
			//return $query->result();
		}
		else
		{
		  return false;
		}
	}

	function registrar_Precio_Articulo($Precio_Numero, $Precio_Monto, $TB_06_Articulo_Articulo_Codigo, $TB_06_Articulo_TB_05_Familia_Familia_Codigo, $sucursal)
	{
		$data = array(
                        'Precio_Numero'=>mysql_real_escape_string($Precio_Numero),
						'Precio_Monto'=>mysql_real_escape_string($Precio_Monto),                        
						'TB_06_Articulo_Articulo_Codigo'=>mysql_real_escape_string($TB_06_Articulo_Articulo_Codigo),
						'TB_06_Articulo_TB_05_Familia_Familia_Codigo'=>mysql_real_escape_string($TB_06_Articulo_TB_05_Familia_Familia_Codigo),                        
						'TB_06_Articulo_TB_02_Sucursal_Codigo'=>mysql_real_escape_string($sucursal)
                    );
		try{
        	$this->db->insert('TB_11_Precios',$data); 
    	}
		catch(Exception $e)
		{return false;}
	}

	function getCantidadPrecios_Precio_Articulo()
	{
		return $this->db->count_all('TB_11_Precios');
	}

	/*function actualizar_Precio_Articulo($codigo, $data, $sucursal)
	{
			$this->db->where('TB_06_Articulo_Articulo_Codigo', mysql_real_escape_string($codigo));
			$this -> db -> where('TB_06_Articulo_TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
			$this->db->update('TB_11_Precios' ,$data);
	}*/	





	// david 
	function getArticuloXML($codigo, $cedula, $sucursal)
	{
		//include '/../controllers/get_session_data.php';
		$this -> db -> select('Articulo_Codigo, Articulo_Descripcion, Articulo_Cantidad_Inventario, Articulo_Descuento, TB_05_Familia_Familia_Codigo, Articulo_Imagen_URL, Articulo_Exento');
		$this -> db -> from('TB_06_Articulo');
		$this -> db -> where('Articulo_Codigo', mysql_real_escape_string($codigo));
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		//$this -> db -> where('TB_05_Familia_Familia_Codigo', $data['']);
		$this -> db -> limit(1);
		
		$query = $this -> db -> get();
		
		$articuloXML;
		
		//Traemos el numero de precio utilizado por este cliente
		$numero_precio=$this->getNumeroPrecio($cedula);
			
		//echo $numero_precio."||";
		
		if($query -> num_rows() != 0)
		{
			$result = $query->result();
			foreach($result as $row)
			{
				$URL_IMAGEN = $row->Articulo_Imagen_URL;				
				$ruta_a_preguntar = FCPATH.'application\\images\\articulos\\'.$URL_IMAGEN.'.jpg';
				//return $ruta_a_preguntar;
				if(!file_exists($ruta_a_preguntar)){$URL_IMAGEN = '00';}
				//HAY QUE VALIDAR EL DESCUENTO POR FAMILIA, ARTICULO Y CLIENTE
				$descuento = $this->getDescuento($codigo, $sucursal, $cedula, $row->TB_05_Familia_Familia_Codigo, $row->Articulo_Descuento);
				//SE ENVIA EL DESCUENTO DEL ARTICULO 
				$articuloXML = "1,$codigo,".$row->Articulo_Descripcion.",".$row->Articulo_Cantidad_Inventario.",$descuento,".$row->TB_05_Familia_Familia_Codigo.",".$this->getPrecioProducto($codigo, $numero_precio, $sucursal).",".$this->getPrecioProducto($codigo, 1, $sucursal).",".$URL_IMAGEN.",".$row->Articulo_Exento;
				
				//return $ruta_a_preguntar ;
				
				return $articuloXML;
			}
			
		}
		else
		{
		    $articuloXML = "0,".$codigo.",0,0,0,0";
			return $articuloXML;
		}
	}
	
	function getArticuloArray($codigo, $cedula, $sucursal)
	{		
		$this -> db -> from('TB_06_Articulo');
		$this -> db -> where('Articulo_Codigo', mysql_real_escape_string($codigo));
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> limit(1);
		$query = $this -> db -> get();
				
		//Traemos el numero de precio utilizado por este cliente
		$numero_precio = $this->getNumeroPrecio($cedula);
			
		if($query -> num_rows() != 0)
		{
			$result = $query->result();
			foreach($result as $row)
			{
				$URL_IMAGEN = $row->Articulo_Imagen_URL;				
				$ruta_a_preguntar = FCPATH.'application\\images\\articulos\\'.$URL_IMAGEN;
				
				if(!file_exists($ruta_a_preguntar)){$URL_IMAGEN = '00.jpg';}
				
				$descuento = $this->getDescuento($codigo, $sucursal, $cedula, $row->TB_05_Familia_Familia_Codigo, $row->Articulo_Descuento);
				
				$articulo['codigo'] = $codigo;
				$articulo['descripcion'] = $row->Articulo_Descripcion;
				$articulo['inventario'] = $row->Articulo_Cantidad_Inventario;
				$articulo['descuento'] = $descuento;
				$articulo['familia'] = $row->TB_05_Familia_Familia_Codigo;
				$articulo['precio_cliente'] = $this->getPrecioProducto($codigo, $numero_precio, $sucursal);
				$articulo['precio_no_afiliado'] = $this->getPrecioProducto($codigo, 1, $sucursal);
				$articulo['imagen'] = $URL_IMAGEN;
				$articulo['exento'] = $row->Articulo_Exento;
								
				return $articulo;
			}			
		}
		else
		{
		    return false;
		}
	}
	
	function getDescuento($codigo, $sucursal, $cedula, $familia, $descuento_producto){
		$desCliente = $this->getDescuentoCliente($sucursal, $cedula);
		$desClienteFamilia = $this->getDescuentoClienteFamilia($sucursal, $cedula, $familia);
		$desClienteProducto = $this->getDescuentoClienteProducto($codigo, $sucursal, $cedula);
		$esSucursal = $this->esClienteTipoSucursal($cedula);
		
		if($desClienteProducto){ //Prioridad 1
			if($desClienteProducto<$descuento_producto){//Si el descuento del producto es mayor que al descuento del producto con ese cliente
				if(!$esSucursal){ //Si no es sucursal si ejecuta la condicion
					return $descuento_producto;
				}
			}
			return $desClienteProducto;
		}elseif($desClienteFamilia){  //Prioridad 2
			if($desClienteFamilia<$descuento_producto){//Si el descuento del producto es mayor que al descuento de la familia con ese cliente
				if(!$esSucursal){ //Si no es sucursal si ejecuta la condicion
					return $descuento_producto;
				}
			}
			return $desClienteFamilia;
		}elseif($desCliente){ //Prioridad 3
			if($desCliente<$descuento_producto){//Si el descuento del producto es mayor que al descuento del cliente
				if(!$esSucursal){ //Si no es sucursal si ejecuta la condicion
					return $descuento_producto;
				}
			}
			return $desCliente;
		}
		return $descuento_producto; //Prioridad 4
	}
	
	function esClienteTipoSucursal($cedula){
		$this -> db -> select('Cliente_EsSucursal');
		$this -> db -> from('TB_03_Cliente');	
		$this -> db -> where('Cliente_Cedula', $cedula);	
		$this -> db -> limit(1);		
		$query = $this -> db -> get();
		if($query -> num_rows() != 0)
		{
			$result = $query->result();
			foreach($result as $row)
			{	
				return $row->Cliente_EsSucursal;
			}			
		}else{return 0;}
	}
	
	function getDescuentoClienteFamilia($sucursal, $cedula, $familia){
		$this -> db -> select('*');
		$this -> db -> from('TB_20_Descuento_Familia');
		$this -> db -> where('TB_05_Familia_Familia_Codigo', $familia);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);	
		$this -> db -> where('TB_03_Cliente_Cliente_Cedula', $cedula);	
		$this -> db -> limit(1);		
		$query = $this -> db -> get();
		if($query -> num_rows() != 0)
		{
			$result = $query->result();
			foreach($result as $row)
			{			
				return $row->Descuento_familia_porcentaje;
			}			
		}else{return 0;}
	}
	
	function getDescuentoCliente($sucursal, $cedula){
		$this -> db -> select('*');
		$this -> db -> from('TB_21_Descuento_Cliente');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);	
		$this -> db -> where('TB_03_Cliente_Cliente_Cedula', $cedula);	
		$this -> db -> limit(1);		
		$query = $this -> db -> get();
		if($query -> num_rows() != 0)
		{
			$result = $query->result();
			foreach($result as $row)
			{			
				return $row->Descuento_cliente_porcentaje;
			}			
		}else{return 0;}
	}	
	
	function getDescuentoClienteProducto($codigo, $sucursal, $cedula){
		$this -> db -> select('*');
		$this -> db -> from('TB_17_Descuento_Producto');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);	
		$this -> db -> where('TB_03_Cliente_Cliente_Cedula', $cedula);	
		$this -> db -> where('TB_06_Articulo_Articulo_Codigo', $codigo);
		$this -> db -> limit(1);		
		$query = $this -> db -> get();
		if($query -> num_rows() != 0)
		{
			$result = $query->result();
			foreach($result as $row)
			{			
				return $row->Descuento_producto_porcentaje;
			}			
		}else{return 0;}
	}
	
	function getPrecioProducto($codigo_articulo, $numero_precio, $sucursal)
	{
		$this -> db -> select('Precio_Monto');
		$this -> db -> from('TB_11_Precios');
		$this -> db -> where('TB_06_Articulo_Articulo_Codigo', mysql_real_escape_string($codigo_articulo));
		$this -> db -> where('TB_06_Articulo_TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this -> db -> where('Precio_Numero', mysql_real_escape_string($numero_precio));
		$this -> db -> limit(1);
		$query = $this -> db -> get();
		if($query -> num_rows() != 0)
		{
			$result = $query->result();
			foreach($result as $row)
			{
				return $row->Precio_Monto;
			}
			
		}
		else
		{
		    return "0";
		}
		
	}
	
	function getNumeroPrecio($cedula)
	{
		$CI =& get_instance();
		$CI->load->model('cliente');
		return $CI->cliente->getNumeroPrecio($cedula);
	}
	
	function actualizarInventarioRESTA($codigo_producto, $cantidad, $sucursal){
		//echo 'Paso 1';
		if($codigo_producto!='00'){// Si no es generico sigue
			//La cantidad que ingresa por parametro es la cantidad a restar al inventario
			//Traemos la cantidad actual
			//echo 'Paso 1';
			$cantidadInventario = $this->inventarioActual($codigo_producto, $sucursal);
			if(!$cantidadInventario){// Si no se pudo obtener inventario			 
				return '1'; //numero de error para 'Error al obtener inventario' o 'No hay existencia de ese producto'
			}
			else{ //Si se obtuno el inventario
				//echo 'Paso 2';
				if($cantidadInventario<$cantidad){ //Si la cantidad es mayor a lo que hay en el inventario
					return '2'; //numero de error para 'Cantidad mayor a disponible en invenatrio'
				}
				else{
					//echo 'Paso 3';
					$nuevoInventario = $cantidadInventario-$cantidad;
					$data['Articulo_Cantidad_Inventario']=$nuevoInventario;
					$this->db->where('Articulo_Codigo', mysql_real_escape_string($codigo_producto));
					$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
			        $this->db->update('TB_06_Articulo' ,$data);
					return '3'; //Numero que afirma un buen ingreso
				}
			}
		}
		/*$data['Articulo_Cantidad_Inventario']=$cantidad;
		$this->db->where('Articulo_Codigo', mysql_real_escape_string($codigo_producto));
		$this->db->update('TB_06_Articulo' ,$data);
		return '-3';*/
	}
	
	function actualizarInventarioSUMA($codigo_producto, $cantidad, $sucursal){
		//echo 'Paso 1';
		if($codigo_producto!='00'){// Si no es generico sigue
			//La cantidad que ingresa por parametro es la cantidad a restar al inventario
			//Traemos la cantidad actual
			//echo 'Paso 1';
			$cantidadInventario = $this->inventarioActual($codigo_producto, $sucursal);
			//echo $cantidadInventario."<br>";
			if($cantidadInventario==false){// Si no se pudo obtener inventario			 
				//return '1'; //numero de error para 'Error al obtener inventario' o 'No hay existencia de ese producto'
				//SE CORRIGIO PUESTO NO AGREGABA CUANDO EL INVENTARIO ES CERO
				$cantidadInventario=0;
			}
			/*else{ //Si se obtuvo el inventario				
				$nuevoInventario = $cantidadInventario+$cantidad;
				//echo 'Actual: '.$cantidadInventario.' Siguiente: '.$nuevoInventario
				$data['Articulo_Cantidad_Inventario']=$nuevoInventario;
				$this->db->where('Articulo_Codigo', mysql_real_escape_string($codigo_producto));
				$this->db->update('TB_06_Articulo' ,$data);
				return '3'; //Numero que afirma un buen ingreso				
			}*/
			$nuevoInventario = $cantidadInventario+$cantidad;
			//echo 'Actual: '.$cantidadInventario.' Siguiente: '.$nuevoInventario
			$data['Articulo_Cantidad_Inventario']=$nuevoInventario;
			$this->db->where('Articulo_Codigo', mysql_real_escape_string($codigo_producto));
			$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
			$this->db->update('TB_06_Articulo' ,$data);
			return '3'; //Numero que afirma un buen ingreso
		}
		/*$data['Articulo_Cantidad_Inventario']=$cantidad;
		$this->db->where('Articulo_Codigo', mysql_real_escape_string($codigo_producto));
		$this->db->update('TB_06_Articulo' ,$data);
		return '-3';*/
	}
	
	function actualizarInventarioFacturaTemporal($codigo, $sucursal, $cantidadAgregar, $tokenFactura, $estaRestando){
		if($articulo = $this->getArticuloFacturaTemporal($codigo, $sucursal, $tokenFactura)){
			//Si existe actualizamos
			$cantidadActual = $articulo->Cantidad;
			if($estaRestando){
				$cantidadActual -= $cantidadAgregar;
			}else{
				$cantidadActual += $cantidadAgregar;
			}			
			//echo "A:".$cantidadAgregar."Ac:".$cantidadActual;
			$datos = array('Cantidad' => $cantidadActual);
			$this->db->where('Codigo_Articulo',$codigo);
			$this->db->where('Factura_Temporal',$tokenFactura);
			$this->db->where('Sucursal',$sucursal);
			$this->db->update('tb_41_productos_factura_temporal', $datos);
		}else{
			//Si no existe creamos
			$datos = array(
						'Codigo_Articulo' => $codigo,
						'Factura_Temporal' => $tokenFactura,
						'Sucursal' => $sucursal,
						'Cantidad' => $cantidadAgregar
						);
			$this->db->insert('tb_41_productos_factura_temporal', $datos);
		}
		$this->eliminarArticulosEnCeroFacturaTemporal($tokenFactura);
	}
	
	function getArticuloFacturaTemporal($codigo, $sucursal, $tokenFactura){
		$this->db->from('tb_41_productos_factura_temporal');
		$this->db->where('Codigo_Articulo',$codigo);
		$this->db->where('Factura_Temporal',$tokenFactura);
		$this->db->where('Sucursal',$sucursal);
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result()[0];
		}
	}
	
	function eliminarArticulosEnCeroFacturaTemporal($factura){
		$this->db->where('Factura_Temporal',$factura);
		$this->db->where('Cantidad',0);
		$this->db->delete('tb_41_productos_factura_temporal');
	}
	
	function eliminarFacturaTemporal($factura){
		$this->db->where('Factura_Temporal',$factura);
		$this->db->delete('tb_41_productos_factura_temporal');
	}
	
	function getProductosFacturaTemporal($factura){
		$this->db->from('tb_41_productos_factura_temporal');
		$this->db->where('Factura_Temporal',$factura);
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result();
		}
	}
	
	function actualizarInventarioSUMADefectuoso($codigo_producto, $cantidad, $sucursal){		
		if($codigo_producto!='00'){// Si no es generico sigue
			//La cantidad que ingresa por parametro es la cantidad a restar al inventario
			//Traemos la cantidad actual
			//echo 'Paso 1';
			$cantidadInventario = $this->inventarioDefectuosoActual($codigo_producto, $sucursal);
			//echo $cantidadInventario."<br>";
			if($cantidadInventario==false){// Si no se pudo obtener inventario			 
				//return '1'; //numero de error para 'Error al obtener inventario' o 'No hay existencia de ese producto'
				//SE CORRIGIO PUESTO NO AGREGABA CUANDO EL INVENTARIO ES CERO
				$cantidadInventario=0;
			}
			/*else{ //Si se obtuvo el inventario				
				$nuevoInventario = $cantidadInventario+$cantidad;
				//echo 'Actual: '.$cantidadInventario.' Siguiente: '.$nuevoInventario
				$data['Articulo_Cantidad_Inventario']=$nuevoInventario;
				$this->db->where('Articulo_Codigo', mysql_real_escape_string($codigo_producto));
				$this->db->update('TB_06_Articulo' ,$data);
				return '3'; //Numero que afirma un buen ingreso				
			}*/
			$nuevoInventario = $cantidadInventario+$cantidad;
			//echo 'Actual: '.$cantidadInventario.' Siguiente: '.$nuevoInventario
			$data['Articulo_Cantidad_Defectuoso']=$nuevoInventario;
			$this->db->where('Articulo_Codigo', mysql_real_escape_string($codigo_producto));
			$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
			$this->db->update('TB_06_Articulo' ,$data);
			return '3'; //Numero que afirma un buen ingreso
		}
		/*$data['Articulo_Cantidad_Inventario']=$cantidad;
		$this->db->where('Articulo_Codigo', mysql_real_escape_string($codigo_producto));
		$this->db->update('TB_06_Articulo' ,$data);
		return '-3';*/
	}
	
	function inventarioActual($codigo, $sucursal){
		$this -> db -> select('Articulo_Cantidad_Inventario');
		$this -> db -> from('TB_06_Articulo');
		$this -> db -> where('Articulo_Codigo', mysql_real_escape_string($codigo));
		$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this -> db -> limit(1);
		
		$query = $this -> db -> get();
		
		if($query -> num_rows() != 0)
		{
			$result = $query->result();
			foreach($result as $row)
			{
				return $row->Articulo_Cantidad_Inventario;
			}			
		}
		else
		{
		    return false;
		}
	}
	
	function inventarioDefectuosoActual($codigo, $sucursal){
		$this -> db -> select('Articulo_Cantidad_Defectuoso');
		$this -> db -> from('TB_06_Articulo');
		$this -> db -> where('Articulo_Codigo', mysql_real_escape_string($codigo));
		$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this -> db -> limit(1);
		
		$query = $this -> db -> get();
		
		if($query -> num_rows() != 0)
		{
			$result = $query->result();
			foreach($result as $row)
			{
				return $row->Articulo_Cantidad_Defectuoso;
			}			
		}
		else
		{
		    return false;
		}
	}
	
	function getArticuloDescripcion($codigo, $sucursal){
		$this -> db -> select('Articulo_Descripcion');
		$this -> db -> from('TB_06_Articulo');
		$this -> db -> where('Articulo_Codigo', mysql_real_escape_string($codigo));		
		$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this -> db -> limit(1);
		$query = $this -> db -> get();
		
		if($query -> num_rows() != 0)
		{
			$result = $query->result();
			foreach($result as $row)
			{
				return $row->Articulo_Descripcion;
			}			
		}
		else
		{
		    return false;
		}
	}
	
	function getArticuloImagen($codigo, $sucursal){
		$this -> db -> select('Articulo_Imagen_URL');
		$this -> db -> from('TB_06_Articulo');
		$this -> db -> where('Articulo_Codigo', mysql_real_escape_string($codigo));		
		$this -> db -> where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this -> db -> limit(1);
		$query = $this -> db -> get();
		
		if($query -> num_rows() != 0)
		{
			$result = $query->result();
			foreach($result as $row)
			{
				return $row->Articulo_Imagen_URL;
			}			
		}
		else
		{
		    return false;
		}
	}
	
	function crearCambioCodigo($sucursal, $fecha, $usuario){
		$datos = array(
						'Sucursal' => $sucursal,
						'Fecha' => $fecha,
						'Usuario' => $usuario
						);
		$this->db->insert('tb_35_cambio_codigo', $datos);
		return $this->db->insert_id();
	}
	
	function agregarArticuloCambioCodigo($cambio, $cambiado, $descripcionCambiado, $abonado, $descripcionAbonado, $cantidad){
		$datos = array(
						'Articulo_Cambio' => $cambiado,
						'Descripcion_Cambio' => $descripcionCambiado,
						'Articulo_Abonado' => $abonado,
						'Descripcion_Abonado' => $descripcionAbonado,
						'Cantidad' => $cantidad,
						'Cambio_Codigo' => $cambio
						);
		$this->db->insert('tb_36_articulos_cambio_codigo', $datos);
	}
	
	function cambiarDescuento($codigo, $sucursal, $descuento){
		$datos = array('Articulo_Descuento'=>$descuento);
		$this->db->where('Articulo_Codigo',$codigo);
		$this->db->where('TB_02_Sucursal_Codigo',$sucursal);
		$this->db->update('tb_06_articulo', $datos);
	}
	
	function getArticulosFacturasTemporales($sucursal){
		$this->db->where('Sucursal', $sucursal);
		$this->db->from('tb_41_productos_factura_temporal');
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result();
		}
	}
	
	function borrarArticulosTemporalesDeSucursal($sucursal){
		$this->db->where('Sucursal', $sucursal);
		$this->db->delete('tb_41_productos_factura_temporal');		
	}
	
	
} //FIN DE LA CLASE


?>