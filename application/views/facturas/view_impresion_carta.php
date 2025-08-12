<html>

<head>
   <title>Impresion A4</title>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <style type="text/css">
		.mypg {
			width: 210mm; 
			height: 297mm;
			*margin: 5mm;
		}
   </style>

</head>

<body>
   <div class="mypg">
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
		<div class='bold'>Forma de Pago: </div>".$fhead -> Factura_Tipo_Pago."<br>";?>

   </div>
   <div class="mypg">
      <h4 align="center">Page 2</h4>

   </div>
   <div class="mypg"">
      <h4 align="center">Page 3</h4>

   </div>
   <div class="mypg">
      <h4 align="center">Page 4</h4>

   </div>
</body>

</html>