<?php
Class articulo extends CI_Model
{




	function existe_Articulo($Codigo,$sucursal){


		$this -> db -> from('TB_06_Articulo');
		$this -> db -> where('Articulo_Codigo', $Codigo);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);

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
				   Articulo_Descripcion LIKE '%$busqueda%')
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

	function registrar($articulo_Codigo, $articulo_Descripcion, $articulo_Codigo_Barras, $articulo_Cantidad_Inventario, $articulo_Cantidad_Defectuoso, $articulo_Descuento, $Articulo_Imagen_URL, $Articulo_Exento, $retencion,
	$TB_05_Familia_Familia_Codigo, $TB_02_Sucursal_Codigo, $costo, $precio1, $precio2, $precio3, $precio4, $precio5, $tipo_codigo = "01", $unidadmedida = "Unid", $codigoCabys = "", $impuesto = "13", $costoD = 0, $precio1D = 0, $precio2D = 0, $precio3D = 0,
	$precio4D = 0, $precio5D = 0)
	{
		if($this->existe_Articulo($articulo_Codigo, $TB_02_Sucursal_Codigo)){
			return false;
		}
		else{
			$data = array(
	                        'Articulo_Codigo'=>$articulo_Codigo,
							'Articulo_Descripcion'=>$articulo_Descripcion,
							'Articulo_Codigo_Barras'=>$articulo_Codigo_Barras,
							'Articulo_Cantidad_Inventario'=>$articulo_Cantidad_Inventario,
							'Articulo_Cantidad_Defectuoso'=>$articulo_Cantidad_Defectuoso,
							'Articulo_Descuento'=>$articulo_Descuento,
							'Articulo_Imagen_URL'=>$Articulo_Imagen_URL,
							'Articulo_Exento'=>$Articulo_Exento,
							'Articulo_No_Retencion'=>$retencion,
							'TB_05_Familia_Familia_Codigo'=>$TB_05_Familia_Familia_Codigo,
							'TB_02_Sucursal_Codigo'=>$TB_02_Sucursal_Codigo,
							'TipoCodigo'=>$tipo_codigo,
							'UnidadMedida'=>  $unidadmedida,
							'CodigoCabys' => $codigoCabys,
							'Impuesto' => $impuesto

	                    );
			try{
	        	$this->db->insert('TB_06_Articulo',$data);
	        	$this->registrar_Precio_Articulo(0, $costo, $articulo_Codigo, $TB_05_Familia_Familia_Codigo, $TB_02_Sucursal_Codigo, $costoD);
	        	$this->registrar_Precio_Articulo(1, $precio1, $articulo_Codigo, $TB_05_Familia_Familia_Codigo, $TB_02_Sucursal_Codigo, $precio1D);
	        	$this->registrar_Precio_Articulo(2, $precio2, $articulo_Codigo, $TB_05_Familia_Familia_Codigo, $TB_02_Sucursal_Codigo, $precio2D);
	        	$this->registrar_Precio_Articulo(3, $precio3, $articulo_Codigo, $TB_05_Familia_Familia_Codigo, $TB_02_Sucursal_Codigo, $precio3D);
	        	$this->registrar_Precio_Articulo(4, $precio4, $articulo_Codigo, $TB_05_Familia_Familia_Codigo, $TB_02_Sucursal_Codigo, $precio4D);
	        	$this->registrar_Precio_Articulo(5, $precio5, $articulo_Codigo, $TB_05_Familia_Familia_Codigo, $TB_02_Sucursal_Codigo, $precio5D);


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
		$this->db->where('Articulo_Codigo', $codigo);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
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

	//Nueva funcion para actualizar precios con descuentos detallados 20-10-2021
	function actualizarPreciosMasivo($precios, $sucursal, $codigoArticulo){
		foreach($precios as $numero => $precioMetadata){
			$this->actualizarPrecioMasivo($codigoArticulo, $sucursal, $numero, $precioMetadata["precio"], $precioMetadata["descuento"]);
		}
	}

	//Nueva funcion para actualizar precios y descuentos de un articulo 20-10-2021
	function actualizarPrecioMasivo($codigo, $sucursal, $numeroPrecio, $precio, $descuento){
		$datos = array(
			'Precio_Monto' => $precio,
			'Precio_Descuento' => $descuento
		);
		$this->db->where('Precio_Numero', $numeroPrecio);
		$this->db->where('TB_06_Articulo_Articulo_Codigo',$codigo);
		$this->db->where('TB_06_Articulo_TB_02_Sucursal_Codigo',$sucursal);
		$this->db->update('tb_11_precios', $datos);
	}

	function actualizarPrecio($codigo, $sucursal, $precio, $numeroPrecio){
		$datos = array(
						'Precio_Monto' => $precio
					);
		$this->db->where('Precio_Numero', $numeroPrecio);
		$this->db->where('TB_06_Articulo_Articulo_Codigo',$codigo);
		$this->db->where('TB_06_Articulo_TB_02_Sucursal_Codigo',$sucursal);
		$this->db->update('tb_11_precios', $datos);
	}

	function get_Articulos($sucursal)
	{
		$this -> db -> select('*');
		$this -> db -> from('TB_06_Articulo');
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
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
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Articulo_Codigo', $codigo);
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
		$this -> db -> where('TB_07_Factura_TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Articulo_Factura_Codigo', $codigo);
		$this -> db -> where('TB_07_Factura_Factura_Consecutivo', $consecutivo);
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
		$this -> db -> where('TB_07_Factura_TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Articulo_Factura_Codigo', $codigo);
		$this -> db -> where('TB_07_Factura_Factura_Consecutivo', $consecutivo);
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

	function registrar_Precio_Articulo($Precio_Numero, $Precio_Monto, $TB_06_Articulo_Articulo_Codigo, $TB_06_Articulo_TB_05_Familia_Familia_Codigo, $sucursal, $descuento = 0)
	{
		$data = array(
                        'Precio_Numero'=>$Precio_Numero,
						'Precio_Monto'=>$Precio_Monto,
						'Precio_Descuento'=>$descuento,
						'TB_06_Articulo_Articulo_Codigo'=>$TB_06_Articulo_Articulo_Codigo,
						'TB_06_Articulo_TB_05_Familia_Familia_Codigo'=>$TB_06_Articulo_TB_05_Familia_Familia_Codigo,
						'TB_06_Articulo_TB_02_Sucursal_Codigo'=>$sucursal
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
			$this->db->where('TB_06_Articulo_Articulo_Codigo', $codigo);
			$this -> db -> where('TB_06_Articulo_TB_02_Sucursal_Codigo', $sucursal);
			$this->db->update('TB_11_Precios' ,$data);
	}*/





	// david
	function getArticuloXML($codigo, $cedula, $sucursal)
	{
		//include '/../controllers/get_session_data.php';
		$this -> db -> select('Articulo_Codigo, Articulo_Descripcion, Articulo_Cantidad_Inventario, Articulo_Descuento, TB_05_Familia_Familia_Codigo, Articulo_Imagen_URL, Articulo_Exento');
		$this -> db -> from('TB_06_Articulo');
		$this -> db -> where('Articulo_Codigo', $codigo);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		//$this -> db -> where('TB_05_Familia_Familia_Codigo', $data['']);
		$this -> db -> limit(1);

		$query = $this -> db -> get();

		$articuloXML = "";

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
		$this -> db -> where('Articulo_Codigo', $codigo);
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
				$ruta_a_preguntar = CARPETA_IMAGENES.$URL_IMAGEN;

				if(!file_exists($ruta_a_preguntar)){$URL_IMAGEN = '00.jpg';}

				//Manera vieja de traer precios y descuentos
				//$articulo['precio_cliente'] = $this->getPrecioProducto($codigo, $numero_precio, $sucursal);
				//$articulo['precio_no_afiliado'] = $this->getPrecioProducto($codigo, 1, $sucursal);
				//$descuento = $this->getDescuento($codigo, $sucursal, $cedula, $row->TB_05_Familia_Familia_Codigo, $row->Articulo_Descuento);

				//Manera nueva de traer precios y descuentos
				$precioObject = $this->getPrecioDescuentoProductoCompleto($codigo, $numero_precio, $sucursal);
				$articulo['precio_cliente'] = $precioObject[$numero_precio]->Precio_Monto;
				$articulo['precio_no_afiliado'] = $precioObject[1]->Precio_Monto;
				$descuentoProducto = $precioObject[$numero_precio]->Precio_Descuento;
				$descuento = $this->getDescuento($codigo, $sucursal, $cedula, $row->TB_05_Familia_Familia_Codigo, $descuentoProducto);

				$articulo['codigo'] = $codigo;
				$articulo['descripcion'] = $row->Articulo_Descripcion;
				//Si es cliente defectuoso
				$articulo['inventario'] = trim($cedula) == "2" ? $row->Articulo_Cantidad_Defectuoso : $row->Articulo_Cantidad_Inventario;
				$articulo['descuento'] = $descuento;
				$articulo['familia'] = $row->TB_05_Familia_Familia_Codigo;
				$articulo['imagen'] = $URL_IMAGEN;
				$articulo['exento'] = $row->Articulo_Exento;
				$articulo['retencion'] = $row->Articulo_No_Retencion;
				$articulo['cabys'] = $row->CodigoCabys;

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
			if($desClienteFamilia == -1){
					return 0;
			}
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
				//Si se seteo que el cliente tiene un descuento de cero con una familia, este descuento debe ser prioritario
				//Si es cero se devuelve -1, para que en el metodo que valida descuento envie cero en lugar de seguir buscando descuentos
				$descuento = $row->Descuento_familia_porcentaje == 0 ? -1 : $row->Descuento_familia_porcentaje;
				return $descuento;

				//return $row->Descuento_familia_porcentaje;
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
			return $query->result()[0]->Descuento_producto_porcentaje;
		}else{return 0;}
	}

	function getPrecioDescuentoProductoCompleto($codigo_articulo, $numero_precio, $sucursal){
		$this -> db -> select('Precio_Numero, Precio_Monto, Precio_Descuento');
		$this -> db -> from('TB_11_Precios');
		$this -> db -> where('TB_06_Articulo_Articulo_Codigo', $codigo_articulo);
		$this -> db -> where('TB_06_Articulo_TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where_in('Precio_Numero', array($numero_precio,1));

		$query = $this -> db -> get();
		if($query -> num_rows() != 0){
			$result = $query->result();
			$precios = array();
			foreach($result as $row){
				$precios[$row->Precio_Numero] = $row;
			}
			return $precios;
		}else{
		    return false;
		}
	}

	function getPrecioProducto($codigo_articulo, $numero_precio, $sucursal)
	{
		$this -> db -> select('Precio_Monto');
		$this -> db -> from('TB_11_Precios');
		$this -> db -> where('TB_06_Articulo_Articulo_Codigo', $codigo_articulo);
		$this -> db -> where('TB_06_Articulo_TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Precio_Numero', $numero_precio);
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

	function getPrecioProductoObject($codigo_articulo, $numero_precio, $sucursal){
		$this -> db -> select('Precio_Monto, Precio_Descuento');
		$this -> db -> from('TB_11_Precios');
		$this -> db -> where('TB_06_Articulo_Articulo_Codigo', $codigo_articulo);
		$this -> db -> where('TB_06_Articulo_TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> where('Precio_Numero', $numero_precio);
		$this -> db -> limit(1);
		$query = $this -> db -> get();
		if($query -> num_rows() != 0){
			return $query->result()[0];
		}else{
		    return false;
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
					$this->db->where('Articulo_Codigo', $codigo_producto);
					$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
			        $this->db->update('TB_06_Articulo' ,$data);
					return '3'; //Numero que afirma un buen ingreso
				}
			}
		}
		/*$data['Articulo_Cantidad_Inventario']=$cantidad;
		$this->db->where('Articulo_Codigo', $codigo_producto);
		$this->db->update('TB_06_Articulo' ,$data);
		return '-3';*/
	}

	function actualizarInventarioSUMA($codigo_producto, $cantidad, $sucursal){
		//if($this->trueque && $sucursal == $this->cod_desampa){ //Si es desampa poner que es garotas
		//		$sucursal = $this->cod_garotas;
		//}
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
				$this->db->where('Articulo_Codigo', $codigo_producto);
				$this->db->update('TB_06_Articulo' ,$data);
				return '3'; //Numero que afirma un buen ingreso
			}*/
			$nuevoInventario = $cantidadInventario+$cantidad;
			//echo 'Actual: '.$cantidadInventario.' Siguiente: '.$nuevoInventario
			$data['Articulo_Cantidad_Inventario']=$nuevoInventario;
			$this->db->where('Articulo_Codigo', $codigo_producto);
			$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
			$this->db->update('TB_06_Articulo' ,$data);
			return '3'; //Numero que afirma un buen ingreso
		}
		/*$data['Articulo_Cantidad_Inventario']=$cantidad;
		$this->db->where('Articulo_Codigo', $codigo_producto);
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
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
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
				$this->db->where('Articulo_Codigo', $codigo_producto);
				$this->db->update('TB_06_Articulo' ,$data);
				return '3'; //Numero que afirma un buen ingreso
			}*/
			$nuevoInventario = $cantidadInventario+$cantidad;
			//echo 'Actual: '.$cantidadInventario.' Siguiente: '.$nuevoInventario
			$data['Articulo_Cantidad_Defectuoso']=$nuevoInventario;
			$this->db->where('Articulo_Codigo', $codigo_producto);
			$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
			$this->db->update('TB_06_Articulo' ,$data);
			return '3'; //Numero que afirma un buen ingreso
		}
		/*$data['Articulo_Cantidad_Inventario']=$cantidad;
		$this->db->where('Articulo_Codigo', $codigo_producto);
		$this->db->update('TB_06_Articulo' ,$data);
		return '-3';*/
	}

	function actualizarInventarioRESTADefectuoso($codigo_producto, $cantidad, $sucursal){
		if($this->truequeHabilitado && isset($this->sucursales_trueque[$sucursal])){ //Si es sucursal de trueque, poner la sucursal que responde
				$sucursal = $this->sucursales_trueque[$sucursal];
		}
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
			$nuevoInventario = $cantidadInventario-$cantidad;
			//echo 'Actual: '.$cantidadInventario.' Siguiente: '.$nuevoInventario
			$data['Articulo_Cantidad_Defectuoso']=$nuevoInventario;
			$this->db->where('Articulo_Codigo', $codigo_producto);
			$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
			$this->db->update('TB_06_Articulo' ,$data);
			return '3'; //Numero que afirma un buen ingreso
		}
	}

	function inventarioActual($codigo, $sucursal){
		$this -> db -> select('Articulo_Cantidad_Inventario');
		$this -> db -> from('TB_06_Articulo');
		$this -> db -> where('Articulo_Codigo', $codigo);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
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
		$this -> db -> where('Articulo_Codigo', $codigo);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
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
		$this -> db -> where('Articulo_Codigo', $codigo);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
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

        function getArticuloTipoCodigo($codigo, $sucursal){
		$this -> db -> select('TipoCodigo');
		$this -> db -> from('TB_06_Articulo');
		$this -> db -> where('Articulo_Codigo', $codigo);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
			$result = $query->result();
			foreach($result as $row)
			{
				return $row->TipoCodigo;
			}
		}
		else
		{
		    return false;
		}
	}

        function getArticuloUnidadMedida($codigo, $sucursal){
		$this -> db -> select('UnidadMedida');
		$this -> db -> from('TB_06_Articulo');
		$this -> db -> where('Articulo_Codigo', $codigo);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this -> db -> limit(1);
		$query = $this -> db -> get();

		if($query -> num_rows() != 0)
		{
			$result = $query->result();
			foreach($result as $row)
			{
				return $row->UnidadMedida;
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
		$this -> db -> where('Articulo_Codigo', $codigo);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
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

	function cambiarRetencion($codigo, $sucursal, $estado){
		$datos = array('Articulo_No_Retencion'=>$estado);
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

	function crearTraspasoInventario($sucursalEntrega, $sucursalRecibe, $fecha, $usuario){
		$datos = array(
			"Fecha"=> $fecha,
			"Sucursal_Entrega"=> $sucursalEntrega,
			"Sucursal_Recibe"=> $sucursalRecibe,
			"Usuario"=> $usuario
		);
		$this->db->insert("tb_52_traspaso_inventario", $datos);
		return $this->db->insert_id();
	}

	function agregarArticuloTraspasoInventario($traspaso, $codigo, $cantidad, $descripcion){
		$datos = array(
			"Traspaso"=>$traspaso,
			"Codigo"=>$codigo,
			"Cantidad"=>$cantidad,
			"Descripcion"=>$descripcion
		);
		$this->db->insert("tb_53_articulos_traspaso_inventario",$datos);
	}

	function getTraspasoInventario($traspaso){
		$this->db->where("Id", $traspaso);
		$this->db->from("tb_52_traspaso_inventario");
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result()[0];
		}
	}

	function getTraspasoInventarioParaImpresion($traspaso){
		$this->db->select("
				Id as consecutivo,
				date_format(Fecha, '%d-%m-%Y %h:%i:%s %p') AS fecha,
				Usuario as usuario,
				Sucursal_Entrega as sucursal_entrega,
				Sucursal_Recibe as sucursal_recibe
			", false);
		$this->db->where("Id", $traspaso);
		$this->db->from("tb_52_traspaso_inventario");
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result()[0];
		}
	}

	function getArticulosDeTraspaso($traspaso){
		$this->db->where("Traspaso", $traspaso);
		$this->db->from("tb_53_articulos_traspaso_inventario");
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result();
		}
	}

	function getArticulosDeTraspasoParaImpresion($traspaso){
		$this->db->where("Traspaso", $traspaso);
		$this->db->from("tb_53_articulos_traspaso_inventario");
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result();
		}
	}

	function getCambiosCodigoRangoFechas($sucursal, $inicio, $final){
		$this->db->select("tb_35_cambio_codigo.Id as consecutivo, tb_35_cambio_codigo.Fecha as fecha, tb_01_usuario.Usuario_Nombre as nombre, tb_01_usuario.Usuario_Apellidos as apellidos");
		$this->db->from('tb_35_cambio_codigo');
		$this->db->join("tb_01_usuario", "tb_01_usuario.Usuario_Codigo = tb_35_cambio_codigo.Usuario");

		$this->setFiltradoFechaDesde($inicio, "Fecha");
		$this->setFiltradoFechaHasta($final, "Fecha");
		$this->db->where('Sucursal', $sucursal);
		$this->db->order_by('Fecha', 'asc');

		$query = $this->db->get();
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{
			return $query->result();
		}
	}

	function setFiltradoFechaDesde($fecha, $campo){
		if(trim($fecha)!=''){
			$fecha = $this->convertirFecha($fecha, " 00:00:00");
			$this->db->where("$campo >=", $fecha);
		}
	}

	function setFiltradoFechaHasta($fecha, $campo){
		if(trim($fecha)!=''){
			$fecha = $this->convertirFecha($fecha, " 23:59:59");
			$this->db->where("$campo <=", $fecha);
		}
	}

	function getCambioCodigoHeader($consecutivo, $sucursal){
		$this->db->from("tb_35_cambio_codigo");
		$this->db->where("Id", $consecutivo);
		$this->db->where("Sucursal", $sucursal);

		$query = $this->db->get();
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{
			return $query->result()[0];
		}
	}

	function getCambioDeCodigoHeaderParaImpresion($sucursal, $consecutivo){
		$this->db->select("tb_35_cambio_codigo.Id as consecutivo, date_format(tb_35_cambio_codigo.Fecha, '%d-%m-%Y %h:%i:%s %p') as fecha, tb_01_usuario.Usuario_Nombre as nombre, tb_01_usuario.Usuario_Apellidos as apellidos",false);
		$this->db->from('tb_35_cambio_codigo');
		$this->db->join("tb_01_usuario", "tb_01_usuario.Usuario_Codigo = tb_35_cambio_codigo.Usuario");
		$this->db->where("tb_35_cambio_codigo.Id", $consecutivo);
		$this->db->where("tb_35_cambio_codigo.Sucursal", $sucursal);
		$query = $this->db->get();
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{
			return $query->result()[0];
		}
	}

	function getCambioCodigoArticulos($consecutivo){
		$this->db->from("tb_36_articulos_cambio_codigo");
		$this->db->where("Cambio_Codigo", $consecutivo);

		$query = $this->db->get();
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{
			return $query->result();
		}
	}


        function borrarArticulosDeSucursalCompleto($sucursal){
            $this->db->where('TB_06_Articulo_TB_02_Sucursal_Codigo', $sucursal);
            $this->db->delete('tb_11_precios');

            $this->db->where('TB_02_Sucursal_Codigo', $sucursal);
            $this->db->delete('tb_06_articulo');
	}

	function getArticuloParaControlInventario($codigo, $sucursal){
		/*
			SELECT 	a.Articulo_Descripcion as descripcion,
					a.Articulo_Cantidad_Inventario as bueno,
					a.Articulo_Cantidad_Defectuoso as defectuoso,
					p.Precio_Monto as costo
			FROM tb_06_articulo a
			JOIN tb_11_precios p ON a.Articulo_Codigo = p.TB_06_Articulo_Articulo_Codigo
			WHERE 	a.TB_02_Sucursal_Codigo = 14
					AND a.Articulo_Codigo = 111
					AND p.TB_06_Articulo_TB_02_Sucursal_Codigo = 14
					AND p.Precio_Numero = 0
		*/
		$this->db->select("a.Articulo_Descripcion as descripcion");
		$this->db->select("a.Articulo_Cantidad_Inventario as bueno");
		$this->db->select("a.Articulo_Cantidad_Defectuoso as defectuoso");
		$this->db->select("p.Precio_Monto as costo");

		$this->db->from("tb_06_articulo a");
		$this->db->join("tb_11_precios p", "a.Articulo_Codigo = p.TB_06_Articulo_Articulo_Codigo");
		$this->db->where("a.TB_02_Sucursal_Codigo", $sucursal);
		$this->db->where("a.Articulo_Codigo", $codigo);
		$this->db->where("p.TB_06_Articulo_TB_02_Sucursal_Codigo", $sucursal);
		$this->db->where("p.Precio_Numero", 0); // Precio de costo

		$query = $this->db->get();
		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{
			return $query->result()[0];
		}
	}

	function getArticulosConInventarioParaControlDeInventario($sucursal){
		/*
			SELECT 	a.Articulo_Descripcion as descripcion,
					a.Articulo_Cantidad_Inventario as bueno,
					a.Articulo_Cantidad_Defectuoso as defectuoso,
					p.Precio_Monto as costo
			FROM tb_06_articulo a
			JOIN tb_11_precios p ON a.Articulo_Codigo = p.TB_06_Articulo_Articulo_Codigo
			WHERE 	a.TB_02_Sucursal_Codigo = 14
					AND p.TB_06_Articulo_TB_02_Sucursal_Codigo = 14
					AND p.Precio_Numero = 0
		*/
		$query = $this->db->query("
			SELECT 	a.Articulo_Codigo AS codigo,
					a.Articulo_Descripcion AS descripcion,
					a.Articulo_Cantidad_Inventario as bueno,
					a.Articulo_Cantidad_Defectuoso as defectuoso,
					p.Precio_Monto as costo
			FROM tb_06_articulo a
			JOIN tb_11_precios p ON a.Articulo_Codigo = p.TB_06_Articulo_Articulo_Codigo
			WHERE 	(Articulo_Cantidad_Defectuoso > 0 OR
					Articulo_Cantidad_Inventario > 0)
			AND a.TB_02_Sucursal_Codigo = $sucursal
			AND p.TB_06_Articulo_TB_02_Sucursal_Codigo = $sucursal
			AND p.Precio_Numero = 0
			ORDER BY a.Articulo_Codigo ASC
		");

		if($query->num_rows()==0)
		{
			return false;
		}
		else
		{
			return $query->result();
		}
	}

	function generarControlInventario($sucursal, $creadoPor, $autorizadoPor){
		date_default_timezone_set("America/Costa_Rica");
		$fecha = date(DB_DATETIME_FORMAT, now());
		$datos = array(
			'Fecha_Creacion' => $fecha,
			'Creado_Por' => $creadoPor,
			'Empate_Autorizado_Por' => $autorizadoPor,
			'Sucursal' => $sucursal
		);
		$this->db->insert('tb_63_control_inventario', $datos);
		return $this->db->insert_id();
	}

	function agregarArticuloControlInventario($control, $codigo, $descripcion, $fBueno, $fDefectuoso, $sBueno, $sDefectuoso, $empatar, $costo){
		$datos = array(
			'Codigo' => $codigo,
			'Descripcion' => $descripcion,
			'Fisico_Defectuoso' => $fDefectuoso,
			'Fisico_Bueno' => $fBueno,
			'Sistema_Defectuoso' => $sDefectuoso,
			'Sistema_Bueno' => $sBueno,
			'Empatar' => $empatar,
			'Costo' => $costo,
			'Control_Inventario' => $control
		);
		$this->db->insert('tb_64_articulos_control_inventario', $datos);
	}

	function getControlesInventarioParaConsulta($sucursal, $desde, $hasta){
		/*
			SELECT ci.id as id, ci.Fecha_Creacion as fecha, CONCAT(u.Usuario_Nombre, ' ', u.Usuario_Apellidos) as usuario
			FROM tb_63_control_inventario ci
			JOIN tb_01_usuario u on u.Usuario_Codigo = ci.Creado_Por
			WHERE sucursal = 14
		*/
		$this->db->select("ci.id as id");
		$this->db->select("date_format(ci.Fecha_Creacion, '%d-%m-%Y %h:%i:%s %p') as fecha", false);
		$this->db->select("CONCAT(u.Usuario_Nombre, ' ', u.Usuario_Apellidos) as usuario", false);
		$this->db->from("tb_63_control_inventario ci");
		$this->db->join("tb_01_usuario u", "u.Usuario_Codigo = ci.Creado_Por");
		$this->db->where("ci.sucursal", $sucursal);

		if(trim($desde)!=''){
			$fecha = $this->convertirFecha($desde, " 00:00:00");
			$this->db->where('ci.Fecha_Creacion >=', $fecha);
		}

		if(trim($hasta)!=''){
			$fecha = $this->convertirFecha($hasta, " 23:59:59");
			//echo $fecha;
			$this->db->where('ci.Fecha_Creacion <=', $fecha);
		}

		$this->db->order_by("ci.id","desc");

		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result();
		}
	}

	private function convertirFecha($fecha, $horas){
		if(trim($fecha)!=''){
			$fecha = explode("/",$fecha);
			$fecha = $fecha[0]."-".$fecha[1]."-".$fecha[2].$horas;
			//echo $fecha;
			date_default_timezone_set("America/Costa_Rica");
			return date("Y-m-d : H:i:s", strtotime($fecha));
		}
		return $fecha;
	}

	public function getControlInventario($consecutivo){
		$this->db->from("tb_63_control_inventario");
		$this->db->where("id", $consecutivo);
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result()[0];
		}
	}

	public function getArticulosControlInventario($controlId){
		$this->db->from("tb_64_articulos_control_inventario");
		$this->db->where("Control_Inventario", $controlId);
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result();
		}
	}

	public function searchCodigosCabysPorNombre($search){
		$search = explode(" ", $search);
		$this->db->select("Descripcion_Bien_Servicio as value, Codigo_Bien_Servicio as id, Impuesto as impuesto");
		$this->db->from("catalogo_cabys");
		foreach($search as $term){
			if(!empty(trim($term))){
				$this->db->like('Descripcion_Bien_Servicio', $term);
			}
		}
		$query = $this->db->get();
		if($query->num_rows()==0){
			return array();
		}else{
			return $query->result();
		}
	}

	public function getInformacionCabysPorCodigo($codigo){
		$this->db->select("Descripcion_Bien_Servicio as descripcion, Codigo_Bien_Servicio as codigo, Impuesto as impuesto");
		$this->db->from("catalogo_cabys");
		$this->db->where("Codigo_Bien_Servicio", $codigo);
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result()[0];
		}
	}

	// Esto es usado para articulos de notas credito que no tienen codigo cabys, por haberse guardado antes del cambio
	public function getCodigoCabysArticuloOriginal($codigo, $sucursal){
		$this->db->select("CodigoCabys");
		$this->db->from("tb_06_articulo");
		$this->db->where("Articulo_Codigo", $codigo);
		$this->db->where("TB_02_Sucursal_Codigo", $sucursal);
		$query = $this->db->get();
		if($query->num_rows()==0){
			return false;
		}else{
			return $query->result()[0]->CodigoCabys;
		}
	}

	public function actualizarCodigoCabys($codigo, $sucursal, $cabys){
		$data['CodigoCabys']=$cabys;
		$this->db->where('Articulo_Codigo', $codigo);
		$this -> db -> where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->update('TB_06_Articulo' ,$data);

	}


} //FIN DE LA CLASE


?>