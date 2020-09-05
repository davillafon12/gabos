<?php
    $ruta_imagen = base_url('application/images/scripts/loader.gif');
    //$ruta_script = base_url('application/controllers/empresas/registrar/es_codigo_usado');	
	$Ruta_Base = base_url('');
	$ruta_base_imagenes_script = base_url('application/images/scripts');
	
echo 
	"<script type='text/javascript'>
	    
		
		function AjaxCaller(){
			var xmlhttp=false;
			try{
				xmlhttp = new ActiveXObject('Msxml2.XMLHTTP');
			}catch(e){
				try{
					xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
				}catch(E){
					xmlhttp = false;
				}
			}

			if(!xmlhttp && typeof XMLHttpRequest!='undefined'){
				xmlhttp = new XMLHttpRequest();
			}
			return xmlhttp;
		}

		function getTable()
		{
			var tabla = document.getElementById('contenido');
			getContentTable('".$Ruta_Base."articulos/editar/getMainTable', tabla);
		}
	
		
		function getContentTable(url, div){
		    
			ajax=AjaxCaller(); 
			ajax.open('GET', url, true); 
			ajax.onreadystatechange=function(){
				if(ajax.readyState==4){
					if(ajax.status==200){
						div.innerHTML=ajax.responseText;
						//$('#tabla_editar').tablesorter({headers: { 0: { sorter: false }, 3: { sorter: false }, 4: { sorter: false }, 5: { sorter: false }, 9: { sorter: false }}});						
						//$('#tabla_editar').filterTable({ filterSelector: '#input-filter'}); 
						$('#tabla_editar').dataTable({
						    'aoColumns':[ 
							               { 'bSortable': false }, 
										   null,    
										   { 'bSortable': false },
										   { 'bSortable': false },
										   null,
										   null,
										   null,
										   null,
										   null,
										   null,
										   null,
										   null,
										   { 'bSortable': false }, 
   									    ],
                            'sPaginationType': 'full_numbers',
                            'oLanguage': {
                                'sUrl': '".base_url('application/scripts/Spanish.txt')."'
                           }
                        } );
				}
			}}
			ajax.send(null);
		}
		
		function getContentTable2(url, div){
		    
			ajax=AjaxCaller(); 
			ajax.open('GET', url, true); 
			ajax.onreadystatechange=function(){
				if(ajax.readyState==4){
					if(ajax.status==200){
						div.innerHTML=ajax.responseText;
						//$('#tabla_editar').tablesorter({headers: { 0: { sorter: false }, 3: { sorter: false }, 4: { sorter: false }, 5: { sorter: false }, 9: { sorter: false }}});						
						//$('#tabla_editar').filterTable({ filterSelector: '#input-filter'}); 
						$('#tabla_editar').dataTable({
						    'aoColumns':[ 
							               { 'bSortable': false }, 
										   null,    
										   null,
										   null,
										   null,
										   { 'bSortable': false },
										   { 'bSortable': false },
										   { 'bSortable': false },
										   null,
										   null,
										   null,
										   null,
										   { 'bSortable': false },
										   null,
										   { 'bSortable': false },
   									    ],
                            'sPaginationType': 'full_numbers',
                            'oLanguage': {
                                'sUrl': '".base_url('application/scripts/Spanish.txt')."'
                           }
                        } );
				}
			}}
			ajax.send(null);
		}		
	</script>";

?>