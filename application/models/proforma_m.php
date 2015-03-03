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
		if($consecutivo = $this->getConsecutivo($sucursal)){
			//return $consecutivo;
			date_default_timezone_set("America/Costa_Rica");
			$Current_datetime = date("y/m/d : H:i:s", now());
			$dataProforma = array(
	                        'Proforma_Consecutivo'=>mysql_real_escape_string($consecutivo),
	                        'Proforma_Observaciones'=>mysql_real_escape_string($observaciones), 
							'Proforma_Estado'=>'pendiente',
							'Proforma_Moneda'=>mysql_real_escape_string($currency),
							'Proforma_Porcentaje_IVA'=>$c_array['iva'],
							'Proforma_Tipo_Cambio'=>$c_array['dolar_venta'],
							'Proforma_Nombre_Cliente'=>mysql_real_escape_string($nombre),
							'Proforma_Fecha_Hora'=>$Current_datetime,
							'TB_02_Sucursal_Codigo'=>$sucursal,
							'Proforma_Vendedor_Codigo'=>$vendedor,	
							'Proforma_Vendedor_Sucursal'=>$sucursal,	
							'TB_03_Cliente_Cliente_Cedula'=>mysql_real_escape_string($cedula)													
	                    );			
	        $this->db->insert('TB_10_Proforma',$dataProforma); 
			return $this->existe_Proforma($consecutivo, $sucursal);
		}else{
			return false;
		}		
	}
	
	function existe_Proforma($consecutivo, $sucursal){
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
	
	function addItemtoInvoice($codigo, $descripcion, $cantidad, $descuento, $exento, $precio, $consecutivo, $sucursal, $vendedor, $cliente, $imagen){
		$dataItem = array(
	                        'Articulo_Proforma_Codigo'=>mysql_real_escape_string($codigo),
	                        'Articulo_Proforma_Descripcion'=>mysql_real_escape_string($descripcion), 
							'Articulo_Proforma_Cantidad'=>mysql_real_escape_string($cantidad),
							'Articulo_Proforma_Descuento'=>mysql_real_escape_string($descuento),
							'Articulo_Proforma_Exento'=>mysql_real_escape_string($exento),
							'Articulo_Proforma_Precio_Unitario'=>mysql_real_escape_string($precio),	
							'Articulo_Proforma_Imagen'=>mysql_real_escape_string($imagen),
							'TB_10_Proforma_Proforma_Consecutivo'=>mysql_real_escape_string($consecutivo),
							'TB_10_Proforma_TB_02_Sucursal_Codigo'=>mysql_real_escape_string($sucursal),
							'TB_10_Proforma_Proforma_Vendedor_Codigo'=>mysql_real_escape_string($vendedor),
							'TB_10_Proforma_Proforma_Vendedor_Sucursal'=>mysql_real_escape_string($sucursal),
							'TB_10_Proforma_TB_03_Cliente_Cliente_Cedula'=>mysql_real_escape_string($cliente)							
	                    );			
	        $this->db->insert('TB_04_Articulos_Proforma',$dataItem);
	}
	
	function getCostosTotalesProforma($consecutivo, $sucursal){
		$costo_total = 0;
		$iva = 0;
		$costo_sin_iva = 0;
		
		$head = $this->getProformasHeaders($consecutivo, $sucursal);
		if($articulos = $this->getArticulosProforma($consecutivo, $sucursal)){
			foreach($articulos as $articulo)
			{
				//Calculamos el precio total de los articulos
				$precio_total_articulo = (($articulo->Articulo_Proforma_Precio_Unitario)-(($articulo->Articulo_Proforma_Precio_Unitario)*(($articulo->Articulo_Proforma_Descuento)/100)))*$articulo->Articulo_Proforma_Cantidad;
				
				//Calculamos los impuestos
				//Traemos el array de configuracion para obtener el porcentaje
				$c_array = $this->getConfgArray();
				$isExento = $articulo->Articulo_Proforma_Exento;
				if($isExento=='0'){
					$costo_sin_iva += $precio_total_articulo/(1+(floatval($head->Proforma_Porcentaje_IVA)/100));
				}
				else if($isExento=='1'){
					$costo_sin_iva += $precio_total_articulo;
				}
				$costo_total += $precio_total_articulo;
				//$costo_sin_iva += (($articulo->Articulo_Factura_Precio_Unitario)-(($articulo->Articulo_Factura_Precio_Unitario)*(($articulo->Articulo_Factura_Descuento)/100)))*$articulo->Articulo_Factura_Cantidad;
			}
			$iva = $costo_total-$costo_sin_iva;
		}
		
		return array('Proforma_Monto_Total'=>$costo_total, 'Proforma_Monto_IVA'=>$iva, 'Proforma_Monto_Sin_IVA'=>$costo_sin_iva);
	}	
	
	function getConfgArray()
	{
		/*$CI =& get_instance();
		$CI->load->model('XMLParser');
		return $CI->XMLParser->getConfigArray();*/
		return $this->configuracion->getConfiguracionArray();
	}
	
	function updateCostosTotales($data, $consecutivo, $sucursal){
		$this->db->where('Proforma_Consecutivo', $consecutivo);
		$this->db->where('TB_02_Sucursal_Codigo', $sucursal);
		$this->db->update('TB_10_Proforma' ,$data);
	}
	
	function getProformasPendientes($sucursal){
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
				Proforma_Observaciones as observaciones,
				date_format(Proforma_Fecha_Hora, '%d-%m-%Y %h:%i:%s %p') as fecha,
				Proforma_Moneda as moneda,
				Proforma_Nombre_Cliente as cliente_nom,
				TB_03_Cliente_Cliente_Cedula as cliente_ced,
				CONCAT_WS(' ', Usuario_Nombre, Usuario_Apellidos) AS vendedor 
			FROM TB_10_Proforma
			JOIN tb_01_usuario ON tb_01_usuario.Usuario_Codigo = TB_10_Proforma.Proforma_Vendedor_Codigo
			WHERE TB_10_Proforma.TB_02_Sucursal_Codigo = $sucursal
			AND TB_10_Proforma.Proforma_Consecutivo = $consecutivo
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
		$this->db->where('Proforma_Consecutivo', mysql_real_escape_string($consecutivo));
		$this->db->where('TB_02_Sucursal_Codigo', mysql_real_escape_string($sucursal));
		$this->db->update('TB_10_Proforma' ,$data);		
	}
	
	
}


?>