<html>
<head>
	<title>Impresion Termica</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<!--CSS ESTILO BASICO E IMAGEN HEADER DE LA PAGINA-->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/impresion/basico.css'); ?>">
</head>
<body onLoad="window.print();">
	<?php
		echo "<div class='text-center'>".$empresa -> Sucursal_Nombre."<br>
		Cédula: ".$empresa -> Sucursal_Cedula."<br>
		Tel.: ".$empresa -> Sucursal_Telefono."<br>
		Dirección: ".$empresa -> Sucursal_Direccion."<br>
		Email: ".$empresa -> Sucursal_Email."</div><br>
		<hr>
		<div class='bold'>".$documento -> tipo."</div> #".$fhead -> Factura_Consecutivo."<br>
		<div class='bold'>Fecha: </div>".date("d-m-Y  h:i:sA",strtotime($fhead -> Factura_Fecha_Hora))."<br>
		<hr>
		<div class='bold'>Cliente: </div>".$fhead -> TB_03_Cliente_Cliente_Cedula."<br>
		<div class='bold'>Nombre: </div>".$fhead -> Factura_Nombre_Cliente."<br>
		<hr>
		<div class='bold'>Forma de Pago: </div>".$fhead -> Factura_Tipo_Pago."<br>";
		
		if($fhead -> Factura_Tipo_Pago=='credito'){
			$diasCredito = $documento -> diasCredito;
			$date = strtotime("+$diasCredito days", strtotime($fhead -> Factura_Fecha_Hora) );
			echo "<div class='bold'>Fecha de vencimiento: </div>".date('d-m-Y',$date)."<br>";
		}
		
		echo "<div class='bold'>Moneda: </div>".$fhead -> Factura_Moneda."<br>
		
		<div class='bold'>Vendedor: </div>".$fhead -> Factura_Vendedor_Codigo."<hr>
		
		<table>
				<thead>
					<th>Artículo</th>
					<th>Cant.</th>
					<th>Desc.</th>
					<th>Precio</th>
				</thead>
				<tbody>
		";
		
		$totalArticulos = 0;
		
		foreach($fbody as $articulo){
			echo "<tr>
				<td>-".$articulo->Articulo_Factura_Descripcion."</td>
				<td class='text-center'>".$articulo->Articulo_Factura_Cantidad."</td>
				<td class='text-center'>".$articulo->Articulo_Factura_Descuento."</td>";
				$totalArticulos += $articulo->Articulo_Factura_Cantidad;
				if($articulo->Articulo_Factura_Descuento){
					$precio = $articulo->Articulo_Factura_Precio_Unitario;
					$descuento = $articulo->Articulo_Factura_Descuento;
					$descuento = $descuento/100;
					$descuento = $precio * $descuento;
					$precio -= $descuento;
					echo "<td class='text-right'>".number_format($precio, 2, ',', '.')."</td>";
				}else{
					echo "<td class='text-right'>".number_format($articulo->Articulo_Factura_Precio_Unitario, 2, ',', '.')."</td>";
				}			
			echo "<tr/>";
		}
		
		echo "</tbody></table>";
		
		echo "<hr><div class='bold'>Total de articulos: </div>$totalArticulos<hr>";
		echo "<div class='tabla-precios'><table class='tabla-costos'>				
				<tr>
					<td><div class='bold'>Total: </div></td><td class='text-right'>".number_format($fhead -> Factura_Monto_Total, 2, ',', '.')."</td>
				</tr>
				</table></div>
				<hr>
				<br>
				<div class='text-center'>
				Recibido conforme: ___________ 
				<br>
				<br>
				Los precios incluyen impuestos de venta.
				<br>
				Gracias por su visita
				<br>
				<br>".$empresa -> Sucursal_leyenda_tributacion."
				</div>";
				
				/*
				 Agregado de via y costo
				<tr>
					<td><div class='bold'>Costo: </div></td><td class='text-right'>".number_format($fhead -> Factura_Monto_Sin_IVA, 2, ',', '.')."</td>
				</tr>
				<tr>
					<td><div class='bold'>IVA: </div></td><td class='text-right'>".number_format($fhead -> Factura_Monto_IVA, 2, ',', '.')."</td>
				</tr>*/
	?>
</body>
</html>