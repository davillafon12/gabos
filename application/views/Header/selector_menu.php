<?php 
			if($Usuario_Rango=='vendedor'){
				include FCPATH.'application/views/Menu_Principal_Vendedor.php';
				echo 	"<style>
							ul.dropdown li.last_last {
								padding-right: 59px;								
							}
							
							.titulo_wrapper{
								margin-top: 31px;								
							}
						</style>";
			}elseif($Usuario_Rango=='cajero'){
				include FCPATH.'application/views/Header/Menu_Principal_Cajero.php';
				echo 	"<style>
							ul.dropdown li.last_last {
								padding-right: 59px;								
							}
						</style>";
			}
			else{
				include FCPATH.'application/views/Header/Menu_Principal.php';
			}
?>