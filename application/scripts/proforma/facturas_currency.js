/////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////// TIPO DE MONEDA //////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////

function cambiarDisplayMoneda(moneda){
	inyectado = document.getElementsByClassName('tipo_moneda_display');
	
	table = document.getElementById("tabla_productos");
	cantidad_filas = cantidadFilas(table);
	tipo_cambio_venta = document.getElementById("tipo_cambio_venta").value;
	factor_tipo_moneda_float = parseFloat(tipo_cambio_venta);
	
	decimales = document.getElementById("cantidad_decimales").value;
	decimales_int = parseInt(decimales);
	
	if(moneda.indexOf('colone') != -1) //Si es colones
	{
		for (var i = 0; i < inyectado.length; i++) 
		{
			inyectado[i].innerHTML = '₡';
		}
		//alert("Entro");
		for (var i = 0; i < cantidad_filas; i++) 
		{
			row_id = i+1;
			precio_unitario = document.getElementById("costo_unidad_articulo_"+row_id).innerHTML;
			precio_unitario_original = document.getElementById("costo_unidad_articulo_ORIGINAL_"+row_id).value;
			//precio_unitario_final = document.getElementById("costo_unidad_articulo_FINAL_"+row_id).value;
			if(precio_unitario!="")
			{
				
				precio_unitario_float = parseFloat(precio_unitario_original);
				//precio_unitario_float = precio_unitario_float * factor_tipo_moneda_float;
				document.getElementById("costo_unidad_articulo_"+row_id).innerHTML=precio_unitario_float.toFixed(decimales_int);
				
				//calculamos el precio final del producto para sacar la ganancia
				//precio_unitario_final = precio_unitario_final * factor_tipo_moneda_float;
				//actualizamos el precio final
				//document.getElementById("costo_unidad_articulo_FINAL_"+row_id).value = precio_unitario_final;
				
				cantidad_articulo_id = "cantidad_articulo_"+row_id;
				cantidad_articulo_int = document.getElementById(cantidad_articulo_id).value;
				actualizaCostoTotalArticulo(cantidad_articulo_id);
			}
		}
	}
	else if(moneda.indexOf('dolare') != -1)
	{
		for (var i = 0; i < inyectado.length; i++) {
			inyectado[i].innerHTML = '&#036;';
		}
		for (var i = 0; i < cantidad_filas; i++) {
			row_id = i+1;
			//Precio unitario es el precio que se muestra con x cantidad de decimales
			//Precio original es el precio que viene de BD con todos los decimales y se usa este para los calculos pues es el precio real
			precio_unitario = document.getElementById("costo_unidad_articulo_"+row_id).innerHTML;
			precio_unitario_original = document.getElementById("costo_unidad_articulo_ORIGINAL_"+row_id).value;
			//precio_unitario_final = document.getElementById("costo_unidad_articulo_FINAL_"+row_id).value;
			if(precio_unitario!="")
			{			
				//alert(precio_unitario_original);
				//Calculamos el precio del articulo
				precio_unitario_float = parseFloat(precio_unitario_original);
				precio_unitario_float = precio_unitario_float / factor_tipo_moneda_float;
				//calculamos el precio final del producto para sacar la ganancia
				//precio_unitario_final = precio_unitario_final / factor_tipo_moneda_float;
				//actualizamos el precio final
				//document.getElementById("costo_unidad_articulo_FINAL_"+row_id).value = precio_unitario_final;
				//alert(precio_unitario_final);
				document.getElementById("costo_unidad_articulo_"+row_id).innerHTML=precio_unitario_float.toFixed(decimales_int);
			
				cantidad_articulo_id = "cantidad_articulo_"+row_id;
				cantidad_articulo_int = document.getElementById(cantidad_articulo_id).value;
				actualizaCostoTotalArticulo(cantidad_articulo_id);
				
			}
		}
	}	
}

//////////////////////////////////////////////////////////////////////////////
///////////////////////////// FORMATEO DE MONEDA /////////////////////////////
//////////////////////////////////////////////////////////////////////////////

Number.prototype.format = function(n, x) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
};