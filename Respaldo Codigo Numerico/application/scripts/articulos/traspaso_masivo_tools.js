$(function() {
	$('#traspaso_inventario_masivo').submit(function() {
		archivo = $("#archivo_excel").val();
		if(archivo.trim()==''){
			notyMsg('¡Por favor seleccione un archivo!', 'error');
			return false;
		}
		
		return true;
	});
});

function notyMsg(Mensaje, tipo){
	n = noty({
			   layout: 'topRight',
			   text: Mensaje,
			   type: tipo,
			   timeout: 4000
			});
}