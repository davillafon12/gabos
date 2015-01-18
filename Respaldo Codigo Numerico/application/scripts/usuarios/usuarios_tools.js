function verify_ID(){			
	convertirResultado();
	var estatus = document.getElementById("statusNombre");
	var input = document.getElementById("cedula_usuario");
	var codigo =  input.value;
	for (var i = 0; i < 13; i++) {
		var str = codigo; 
		var res = str.replace("_", "");
		codigo = res;
	}
	var tamano = codigo.length; 
	if((valor==1 && tamano ==9) || (valor==2 && tamano == 13) || (valor==3 && tamano ==10)){
		if(codigo){
			estatus.innerHTML="<img src="+ruta_imagen+" />" ;
			callPage(Ruta_Base+"usuarios/registrar/es_Cedula_Utilizada?id="+codigo, estatus);
		}
	}
	else
	{estatus.innerHTML="";}
}
		
function verify_Name(){
	var estatus = document.getElementById("statusNombre");
	var input = document.getElementById("usuario_nombre_usuario");
	var codigo =  input.value;
	if(codigo){
		estatus.innerHTML="<img src="+ruta_imagen+" />" ;
		callPage(Ruta_Base+"usuarios/registrar/es_Nombre_Utilizada?id="+codigo, estatus);
	}
	else
	{estatus.innerHTML="";}
	
}		
function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

function AjaxCaller(){
	var xmlhttp=false;
	try{
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	}catch(e){
		try{
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}catch(E){
			xmlhttp = false;
		}
	}

	if(!xmlhttp && typeof XMLHttpRequest!="undefined"){
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}

function callPage(url, div){
	var boton = document.getElementById("submit");
	ajax=AjaxCaller(); 
	ajax.open("GET", url, true); 
	ajax.onreadystatechange=function(){
		if(ajax.readyState==4){
			if(ajax.status==200){
				//div.innerHTML=ajax.responseText;
				if(ajax.responseText.indexOf("tr") != -1)
				{						
					div.innerHTML = "<div class='status_2'><img src="+ruta_base_imagenes_script+"/error.gif /><p class='text_status'>¡No esta disponible!</p></div>";							
					boton.disabled=true;
				}
				else
				{
					div.innerHTML = "<div class='status_2'><img src="+ruta_base_imagenes_script+"/tick.gif /><p class='text_status'>¡Si esta disponible!</div></p>";
					boton.disabled=false;
				}
			}
		}
	}
	ajax.send(null);
}	


/*Metodo encargado de validar cedula y otros digitos numericos		*/
jQuery(function(){		 
	$("#cedula_usuario").mask("999999999");   
	$("#celular_usuario").mask("9999-9999");
	$("#telefono_usuario").mask("9999-9999");
	//$("#permisos_div").accordion({ collapsible: true, active: false} );	
});


function convertirResultado(){
	var selectCedula = document.getElementById("tipo_Cedula"); 
	var tipo = selectCedula.options[selectCedula.selectedIndex].text; 
	if(tipo =="Nacional"){
		valor = 1; 
	}
	else{
		if(tipo=="Residencia"){
			valor = 2; 
		}
		else{
			if(tipo=="Juridica"){
				valor = 3; 
			}
			else{
				valor = 1; 
			}
		}
	}
	
}
function tipoCedula(){	
	convertirResultado();
	var opcion = valor; 
	switch (opcion) {
		case 1:
			("#cedula_usuario").mask("999999999"); 
			break;
		case 2:
			("#cedula_usuario").mask("9999999999999"); 
			break;
		case 3:
			("#cedula_usuario").mask("9999999999"); 
			break;	
		default:
			("#cedula_usuario").mask("999999999"); 
			break;
	}
	
}

function  creaNombreUsuario(){
	var nombreUsuario = document.getElementById("nombre_usuario"); 
	var apellidosUsuario = document.getElementById("apellidos_usuario"); 
	var contenedorNombre = nombreUsuario.value; 
	var contenedorApellido = apellidosUsuario.value; 
	var res = contenedorApellido.split(" ");
	var arregloApellidos = res[0];
	var usuario = contenedorNombre.substring(0, 1);
	var usuarioTemp = usuario.concat(arregloApellidos); 
	usuarioFinal = usuarioTemp.toLowerCase();	
	document.getElementById("usuario_nombre_usuario").value = usuarioFinal;
	document.getElementById("usuario_password").value = usuarioFinal;
	verify_Name();
}

function actualizarPermisos(){
	rol = $("#select_rango").val();
	switch(rol){
		case 'vendedor': 
			setPermisos(permisosVendedor);
		break;
		case 'cajero': 
			setPermisos(permisosCajero);
		break;
		case 'administrador': 
			setPermisos(permisosAdmin);
		break;
		case 'avanzado': 
			setPermisos(permisosAvanz);
		break;
	}
}

function resetPermisos(){
	$(".input-permisos-checkbox").prop('checked', false);
}

function setPermisos(rolArray){
	resetPermisos();
	for (i = 0; i < rolArray.length; i++) { 
		$("#ch_"+rolArray[i]).prop('checked', true);
	}
}