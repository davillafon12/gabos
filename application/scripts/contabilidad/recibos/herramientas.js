var facturasMarcadas = [];
var facturasSaldar = [];
var facturasMarcadasEliminar = [];

function marcarFactura(id_credito){
	if(facturasMarcadas.indexOf(id_credito) > -1){
		//Eliminar item
		for(i = facturasMarcadas.length - 1; i >= 0; i--) {
			if(facturasMarcadas[i] === id_credito) {
			   facturasMarcadas.splice(i, 1);
			}
		}
	}else{
		//Agregar item
		facturasMarcadas.push(id_credito);
	}
	cambiarColorFondoFacturasPendientes();
}

function cambiarColorFondoFacturasPendientes(){
	$(".bordes_tabla").css("background", "none"); //Limpiamos todo
	//Ponemos el fondo solo a seleccionados
	for(i = facturasMarcadas.length - 1; i >= 0; i--) {
		$("#credito_row_"+facturasMarcadas[i]).css("background", "#999999");
	}	
}

function agregarFacturasSaldarSeleccion(){
	for(i = facturasMarcadas.length - 1; i >= 0; i--) {
		row_a_copiar = $("#credito_row_"+facturasMarcadas[i]).html();		
		$("#tbody_facturas_a_saldar").append("<tr class='bordes_tabla_saldar' onclick='marcarParaEliminar("+facturasMarcadas[i]+")' id='factura_saldar_"+facturasMarcadas[i]+"'>"+row_a_copiar+"</tr>");
		//Agregamos la factura a facturas por saldar
		if(!isNumber(facturasMarcadas[i])){return false;}
		facturasSaldar.push(parseInt(facturasMarcadas[i]));		
	}
	//Eliminamos las facturas del primer cuadro
	eliminarFacturasMarcadas();
	//Reiniciamos el array de factiras marcadas
	facturasMarcadas = [];
	//Actualizamos el saldo
	setSaldo();
}

function eliminarFacturasMarcadas(){
	for(i = facturasMarcadas.length - 1; i >= 0; i--) {
		$("#credito_row_"+facturasMarcadas[i]).remove();
	}
}

function marcarParaEliminar(id_credito){
	if(facturasMarcadasEliminar.indexOf(id_credito) > -1){
		//Eliminar item
		for(i = facturasMarcadasEliminar.length - 1; i >= 0; i--) {
			if(facturasMarcadasEliminar[i] === id_credito) {
			   facturasMarcadasEliminar.splice(i, 1);
			}
		}
	}else{
		//Agregar item
		facturasMarcadasEliminar.push(id_credito);
	}
	cambiarColorFondoFacturasSaldar();
}

function cambiarColorFondoFacturasSaldar(){
	$(".bordes_tabla_saldar").css("background", "none"); //Limpiamos todo
	//Ponemos el fondo solo a seleccionados
	for(i = facturasMarcadasEliminar.length - 1; i >= 0; i--) {
		$("#factura_saldar_"+facturasMarcadasEliminar[i]).css("background", "#999999");
	}	
}

function eliminarFacturasSaldarSeleccion(){
	for(i = facturasMarcadasEliminar.length - 1; i >= 0; i--) {
		row_a_copiar = $("#factura_saldar_"+facturasMarcadasEliminar[i]).html();		
		$("#tbody_posibles_facturas").append("<tr class='bordes_tabla' onclick='marcarFactura("+facturasMarcadasEliminar[i]+")' id='credito_row_"+facturasMarcadasEliminar[i]+"'>"+row_a_copiar+"</tr>");
		//Eliminamos la factura a facturas por saldar
		for(j = facturasSaldar.length - 1; j >= 0; j--) {
			if(facturasSaldar[j] === facturasMarcadasEliminar[i]) {
			   facturasSaldar.splice(j, 1);
			}
		}		
	}
	//Eliminamos las facturas del segundo cuadro
	eliminarFacturasMarcadasEliminar();
	//Reiniciamos el array de facturas marcadas
	facturasMarcadasEliminar = [];
	//Actualizamos el saldo
	setSaldo();
}

function eliminarFacturasMarcadasEliminar(){
	for(i = facturasMarcadasEliminar.length - 1; i >= 0; i--) {
		$("#factura_saldar_"+facturasMarcadasEliminar[i]).remove();
	}
}

function getFacturasPendientes(){
	facturasPorSaldar = $(".bordes_tabla");
	facturas = [];
	for(i = facturasPorSaldar.length - 1; i >= 0; i--) {
		item = facturasPorSaldar[i].id;
		item = item.replace('credito_row_','');
		facturas.push(parseInt(item));
	}
	return facturas;
}

function getFacturasPorSaldar(){
	facturasPorSaldar = $(".bordes_tabla_saldar");
	facturas = [];
	for(i = facturasPorSaldar.length - 1; i >= 0; i--) {
		item = facturasPorSaldar[i].id;
		item = item.replace('factura_saldar_','');
		facturas.push(parseInt(item));
	}
	return facturas;
}

function pasarTodasPendientesASaldar(){
	facturasMarcadas = getFacturasPendientes();
	agregarFacturasSaldarSeleccion();
}

function eliminarTodasASaldar(){
	facturasMarcadasEliminar = getFacturasPorSaldar();
	eliminarFacturasSaldarSeleccion();
}