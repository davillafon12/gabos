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
        getContentTable('/empresas/editar/getMainTable', tabla);
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
                                                                   null,
                                                                   null,
                                                                   { 'bSortable': false }, 
                                                                   { 'bSortable': false },
                                                                   { 'bSortable': false },
                                                                   null,
                                                                   null,
                                                                   null,
                                                                   { 'bSortable': false } 
                                                            ],
            'sPaginationType': 'full_numbers',
            'oLanguage': {
                'sUrl': '/application/scripts/Spanish.txt'
           }
        } );
                }
        }}
        ajax.send(null);
}