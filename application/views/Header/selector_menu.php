<?php 
			if($Usuario_Rango=='vendedor'){
				include '/../Header/Menu_Principal_Vendedor.php';
				echo 	"<style>
							ul.dropdown li.last_last {
								padding-right: 59px;								
							}
						</style>";
			}elseif($Usuario_Rango=='cajero'){
				include '/../Header/Menu_Principal_Cajero.php';
				echo 	"<style>
							ul.dropdown li.last_last {
								padding-right: 59px;								
							}
						</style>";
			}
			else{
				include '/../Header/Menu_Principal.php';
			}
?>