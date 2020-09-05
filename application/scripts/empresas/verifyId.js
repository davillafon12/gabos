function verify_ID(){
        var estatus = document.getElementById('status');
        var input = document.getElementById('codigo');
        var codigo =  input.value;
        if(isNumber(codigo)){
                estatus.innerHTML='<img src="/application/images/scripts/loader.gif" />' ;
                /*callPage('".$ruta_script."?id='+codigo, estatus);*/
                /*callPage('/../home/logout', estatus);*/
                callPage('/empresas/registrar/es_codigo_usado?id='+codigo, estatus);
        }
        else
        {estatus.innerHTML='';}
}

function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
}

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

function callPage(url, div){
    var boton = document.getElementById('submit');
        ajax=AjaxCaller(); 
        ajax.open('GET', url, true); 
        ajax.onreadystatechange=function(){
                if(ajax.readyState==4){
                        if(ajax.status==200){
                                //div.innerHTML=ajax.responseText;
                            if(ajax.responseText.indexOf('tr') != -1)
                                {						
                                        div.innerHTML = "<div class='status_2'><img src='application/images/scripts/error.gif' /><p class='text_status'>¡No esta disponible!</p></div>";							
                                        boton.disabled=true;
                                }
                                else
                                {
                                        div.innerHTML = "<div class='status_2'><img src='application/images/scripts/tick.gif' /><p class='text_status'>¡Si esta disponible!</div></p>";
                                        boton.disabled=false;
                                }
                        }
                }
        }
        ajax.send(null);
}