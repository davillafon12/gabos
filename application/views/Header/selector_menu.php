<?php 
			if($Usuario_Rango=='vendedor'){
				include PATH_MENU_PRINCIPAL_VENDEDOR;
				echo 	"<style>
							ul.dropdown li.last_last {
								padding-right: 59px;								
							}
							
							.titulo_wrapper{
								margin-top: 31px;								
							}
						</style>";
			}elseif($Usuario_Rango=='cajero'){
				include PATH_MENU_PRINCIPAL_CAJERO;
				echo 	"<style>
							ul.dropdown li.last_last {
								padding-right: 59px;								
							}
						</style>";
			}
			else{
				include PATH_MENU_PRINCIPAL;
			}
?>