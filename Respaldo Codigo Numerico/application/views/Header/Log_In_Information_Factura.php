<?php     
    //include '/../../controllers/get_session_data.php';	
	$Ruta_Imagen_Usuario = base_url("application/images/User_Photos/thumb/".$Usuario_Imagen_URL);
	echo "
	<div class='logout'>
			<div class='tooltip'> Bienvenido(a) ".$Usuario_Nombre_Usuario."
			<span> 
			  <b></b> 
			  <img class='imagen_tt' src='".$Ruta_Imagen_Usuario."' height='100'/>
			  <div class='info_tt'>
				  <p>".$Usuario_Nombre." ".$Usuario_Apellidos."<br></p><hr class='hr_tt'>
				  <p class='observaciones_tt'>Observaciones:<br>
				  ".$Usuario_Observaciones."</p>
				  
			  </div>
			  <a href='".base_url('')."usuarios/editar/verMiPerfil?id=".$Usuario_Codigo."' '  class='boton_tt'>Ver Mi Perfil</a>
			  
			  </span> 
			  </div>			
			<a class='link_salida' href='javascript:;' onClick='salidaSesion()'>Salir</a>
		</div>";
		
		
?>