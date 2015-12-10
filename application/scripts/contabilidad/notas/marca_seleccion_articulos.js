var productosMarcados = [];
var productosCreditar = [];
var productosMarcadosEliminar = [];
var contadorArticuloGenerico = 10000000; //Nuemro alto para que nunca llegue a ese punto

$(document).ready(function(){
	$("#precio_articulo_generico").numeric();
	$("#boton_agregar_articulo_generico").on('click', function(event){
			var descripcion = $("#descripcion_articulo_generico").val().trim();
			var precio = $("#precio_articulo_generico").val().trim();
			if(validarCedulaNombre()){
				if(descripcion !== "" && precio !== ""){
					if($.isNumeric(precio)){
							var cantProductosSeleccionados = parseInt($('#tabla_productos tr').length - 1) + parseInt($('#tabla_productos_seleccionados tr').length - 1);
					
							var html = "<tr class='bordes_tabla' onclick='marcarArticulo("+cantProductosSeleccionados+")' id='producto_row_"+cantProductosSeleccionados+"'><td class='celdas_tabla'><p class='contact' id='codigo_producto_"+cantProductosSeleccionados+"'>00</p></td><td class='celdas_tabla'><p class='contact' id='descripcion_"+cantProductosSeleccionados+"'>"+descripcion+"</p><input id='precio_"+cantProductosSeleccionados+"' type='hidden' value='"+precio+"'/></td><td class='celdas_tabla'><p class='contact' id='p_cantidad_original_"+cantProductosSeleccionados+"'>100</p></td></tr>";
							$("#tbody_productos").append(html);
							$("#descripcion_articulo_generico").val("");
							$("#precio_articulo_generico").val("");
					}else{
							notyMsg('¡El precio tiene un formato incorrecto!', 'error');
					}
				}else{
						notyMsg('¡La descripción y/o precio no pueden ser vacíos!', 'error');
				}
			}
		
			
	});
});

function marcarArticulo(id){
	//alert(id);
	if(productosMarcados.indexOf(id) > -1){
		//Eliminar item
		for(i = productosMarcados.length - 1; i >= 0; i--) {
			if(productosMarcados[i] === id) {
			   productosMarcados.splice(i, 1);
			}
		}
	}else{
		//Agregar item
		productosMarcados.push(id);
	}
	cambiarColorFondoProductosSeleccionadosA();
}

function cambiarColorFondoProductosSeleccionadosA(){
	$(".bordes_tabla").css("background", "none"); //Limpiamos todo
	//Ponemos el fondo solo a seleccionados
	for(i = productosMarcados.length - 1; i >= 0; i--) {
		$("#producto_row_"+productosMarcados[i]).css("background", "#999999");
	}	
}

function agregarProductosACreditar(){
	for(i = productosMarcados.length - 1; i >= 0; i--) {
		//Obtenemos los parrafos de la fila
		parrafos = $("#producto_row_"+productosMarcados[i]+" td");
		//Creamos una cadena para crear la fila con los parrafos, esto para agregar el onclick solo a estos td
		fila_nueva = '';
		for(j = 0; j < parrafos.length; j++){
			fila_nueva = fila_nueva+"<td class='celdas_tabla_producto_acreditar' onclick='marcarProductoParaEliminar("+productosMarcados[i]+")'>"+parrafos[j].innerHTML+"</td>";			
		}
		//Agregamos los campos editables
		fila_nueva = fila_nueva+"<td class='celdas_tabla_cantidades' ><input type='text' class='cantidades-devolver' value='0' id='celda_cantidad_defectuoso_"+productosMarcados[i]+"'/></td><td class='celdas_tabla_cantidades'><input type='text' class='cantidades-devolver' value='0' id='celda_cantidad_buena_"+productosMarcados[i]+"'/></td>";
		//row_a_copiar = $("#producto_row_"+productosMarcados[i]).html();		
		$("#tbody_productos_seleccionados").append("<tr class='bordes_tabla_creditar' id='producto_a_creditar_"+productosMarcados[i]+"'>"+fila_nueva+"</tr>");
		//Agregamos el producto a ser acreditado
		if(!isNumber(productosMarcados[i])){return false;}
		productosCreditar.push(parseInt(productosMarcados[i]));		
	}
	//Eliminamos los productos del primer cuadro
	eliminarProductosMarcados();
	//Reiniciamos el array de productos marcadas
	productosMarcados = [];	
}

function eliminarProductosMarcados(){
	for(i = productosMarcados.length - 1; i >= 0; i--) {
		$("#producto_row_"+productosMarcados[i]).remove();
	}
}

function marcarProductoParaEliminar(id){
	//alert(id);
	if(productosMarcadosEliminar.indexOf(id) > -1){
		//Eliminar item
		for(i = productosMarcadosEliminar.length - 1; i >= 0; i--) {
			if(productosMarcadosEliminar[i] === id) {
			   productosMarcadosEliminar.splice(i, 1);
			}
		}
	}else{
		//Agregar item
		productosMarcadosEliminar.push(id);
	}
	cambiarColorFondoProductosEliminar();
}

function cambiarColorFondoProductosEliminar(){
	$(".bordes_tabla_creditar").css("background", "none"); //Limpiamos todo
	//Ponemos el fondo solo a seleccionados
	for(i = productosMarcadosEliminar.length - 1; i >= 0; i--) {
		//alert(productosMarcadosEliminar[i]);
		$("#producto_a_creditar_"+productosMarcadosEliminar[i]).css("background", "#999999");
	}	
}

function eliminarProductosSeleccion(){
	for(i = productosMarcadosEliminar.length - 1; i >= 0; i--) {
		//Obtenemos los parrafos de la fila
		parrafos = $("#producto_a_creditar_"+productosMarcadosEliminar[i]+" td");
		
		//Creamos una cadena para crear la fila con los parrafos, esto para agregar el onclick solo a estos td
		fila_nueva = '';
		for(j = 0; j < parrafos.length && j<3; j++){ //Seleccione los primeros tres
			fila_nueva = fila_nueva+"<td class='celdas_tabla'>"+parrafos[j].innerHTML+"</td>";			
		}
		$("#tbody_productos").append("<tr class='bordes_tabla' onclick='marcarArticulo("+productosMarcadosEliminar[i]+")' id='producto_row_"+productosMarcadosEliminar[i]+"'>"+fila_nueva+"</tr>");
		
		//Eliminamos la factura a facturas por saldar
		for(j = productosCreditar.length - 1; j >= 0; j--) {
			if(productosCreditar[j] === productosMarcadosEliminar[i]) {
			   productosCreditar.splice(j, 1);
			}
		}		
		
	}
	//Eliminamos los productos del segundo cuadro
	eliminarProductosMarcadosEliminar();
	//Reiniciamos el array de productos marcados
	productosMarcadosEliminar = [];
	
}

function eliminarProductosMarcadosEliminar(){
	for(i = productosMarcadosEliminar.length - 1; i >= 0; i--) {
		$("#producto_a_creditar_"+productosMarcadosEliminar[i]).remove();
	}
}


//TOOLTIP DE LOS TITULOS DE LAS SIGLAS EN LA TABLA DE PRODUCTOS EN NOTA CREDITO
$(function() {
    $( "#titulo_cantidad_original" ).tooltip({
      show: null,
      position: {
        my: "left top",
        at: "left bottom"
      },
      open: function( event, ui ) {
        ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, "fast" );
      }
    });
	
	$( "#titulo_cantidad_defectuosa" ).tooltip({
      show: null,
      position: {
        my: "left top",
        at: "left bottom"
      },
      open: function( event, ui ) {
        ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, "fast" );
      }
    });
	
	$( "#titulo_cantidad_buena" ).tooltip({
      show: null,
      position: {
        my: "left top",
        at: "left bottom"
      },
      open: function( event, ui ) {
        ui.tooltip.animate({ top: ui.tooltip.position().top + 10 }, "fast" );
      }
    });
  });