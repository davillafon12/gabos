<?php

class API_FE{
    
    private $gateway;
    private $logger;
    private $helper;
    
    public function __construct(){
        require_once PATH_REST_CLIENT;
        require_once PATH_API_LOGGER;
        require_once PATH_API_HELPER;
        $this->gateway = new RestClient([
            'base_url' => URL_API_CRLIBE,
            'curl_options' => [CURLOPT_CONNECTTIMEOUT => API_CRLIBRE_CURL_TIMEOUT]
        ]);
        $this->logger = new APILogger();
        $this->helper = new API_Helper();
    }
    
    public function internetIsOnline(){
        $bm = round(microtime(true) * 1000);
        $localClient = new RestClient([
            'base_url' => "https://www.google.com/",
            'curl_options' => [CURLOPT_CONNECTTIMEOUT => API_CRLIBRE_CURL_TIMEOUT]
        ]);
        $result = $localClient->get("/", []);
        $this->logger->info("internetIsOnline", "Checking internet status");
        if($result->info->http_code == 200){
            $ms = (round(microtime(true) * 1000)) - $bm;
            $this->logger->info("internetIsOnline", $ms."ms | Internet status is OK");
            return true;
        }else{
            $this->logger->error("internetIsOnline", "Internet status is OFFLINE");
            return false;
        }
    }
    
    public function uploadCertificate($user, $sessionKey, $certPath, $name){
        $bm = round(microtime(true) * 1000);
        $params = array(
            'w' => "fileUploader", 
            "r" => "subir_certif",
            "sessionKey" => $sessionKey,
            "fileToUpload" => "@".realpath($certPath),
            "iam" => $user
        );
        $this->logger->info("uploadCertificate", "Uploading certificate into API with params: ".json_encode($params));
        //Initialise the cURL var
        $ch = curl_init();

        //Get the response from cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //Set the Url
        curl_setopt($ch, CURLOPT_URL, URL_API_CRLIBE."/api.php");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, API_CRLIBRE_CURL_TIMEOUT);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        // Execute the request
        $response = (Array)json_decode(curl_exec($ch));
        
        if(is_array($response)){
            if(isset($response["resp"])){
                $response = (array) $response["resp"];
                if(isset($response["downloadCode"]) && isset($response["name"])){
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->info("uploadCertificate", $ms."ms | API returns ".json_encode($response));
                    return $response["downloadCode"];
                }else{
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->error("uploadCertificate", $ms."ms | 3 - API returns ".json_encode($response));
                    return false;
                }
            }else{
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->error("uploadCertificate", $ms."ms | 2 - API returns ".json_encode($response));
                return false;
            }
        }else{
            $ms = (round(microtime(true) * 1000)) - $bm;
            $this->logger->error("uploadCertificate", $ms."ms | 1 - API returns ".json_encode($response));
            return false;
        }
    }
    
    public function createClave($tipoCedula, $cedula, $codigoPais, $consecutivo, $situacion, $codigoSeguridad, $tipoDocumento){
        $bm = round(microtime(true) * 1000);
        $params = array(
            "tipoCedula" => $tipoCedula,
            "cedula" => $cedula,
            "codigoPais" => $codigoPais,
            "consecutivo" => $consecutivo,
            "situacion" => $situacion,
            "codigoSeguridad" => $codigoSeguridad,
            "tipoDocumento" => $tipoDocumento
        );
        $this->logger->info("createClave", "Creating clave into API with params: ".json_encode($params));
        
        $result = $this->helper->getClave($tipoDocumento, $tipoCedula, $cedula, $situacion, $codigoPais, $consecutivo, $codigoSeguridad);
        
        if(is_array($result)){
            if(isset($result["clave"]) && isset($result["consecutivo"])){
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->info("createClave", $ms."ms | API returns ".json_encode($result));
                    return $result;
                }else{
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->error("createClave", $ms."ms | 2 - API returns ".json_encode($result));
                    return false;
                }
        }else{
            $ms = (round(microtime(true) * 1000)) - $bm;
            $this->logger->error("createClave", $ms."ms | 1 - API returns ".json_encode($result));
            return false;
        }
    }

    public function crearXMLFactura($clave, $consecutivo, $fecha_emision,
                                    $emisor_nombre, $emisor_tipo_indetif, $emisor_num_identif, $nombre_comercial, $emisor_provincia, $emisor_canton, $emisor_distrito, $emisor_barrio, $emisor_otras_senas, $emisor_cod_pais_tel, $emisor_tel, $emisor_cod_pais_fax, $emisor_fax, $emisor_email,
                                    $receptor_nombre, $receptor_tipo_identif, $receptor_num_identif, $receptor_provincia, $receptor_canton, $receptor_distrito, $receptor_barrio, $receptor_cod_pais_tel, $receptor_tel, $receptor_cod_pais_fax, $receptor_fax, $receptor_email,
                                    $condicion_venta,
                                    $plazo_credito,
                                    $medio_pago,
                                    $cod_moneda,
                                    $tipo_cambio,
                                    $total_serv_gravados, $total_serv_exentos, $total_merc_gravada, $total_merc_exenta, $total_gravados, $total_exentos, $total_ventas, $total_descuentos, $total_ventas_neta, $total_impuestos, $total_comprobante,
                                    $otros,
                                    $productos){
        $bm = round(microtime(true) * 1000);
        $params = array(
            "clave" => $clave, 
            "consecutivo" => $consecutivo, 
            "fecha_emision" => $fecha_emision,
            "emisor_nombre" => $emisor_nombre, 
            "emisor_tipo_indetif" => $emisor_tipo_indetif, 
            "emisor_num_identif" => $emisor_num_identif, 
            "nombre_comercial" => $nombre_comercial, 
            "emisor_provincia" => $emisor_provincia, 
            "emisor_canton" => str_pad($emisor_canton,2,"0", STR_PAD_LEFT), 
            "emisor_distrito" => str_pad($emisor_distrito,2,"0", STR_PAD_LEFT), 
            "emisor_barrio" => str_pad($emisor_barrio,2,"0", STR_PAD_LEFT), 
            "emisor_otras_senas" => $emisor_otras_senas, 
            "emisor_cod_pais_tel" => $emisor_cod_pais_tel, 
            "emisor_tel" => str_replace("-", "", $emisor_tel), 
            "emisor_cod_pais_fax" => $emisor_cod_pais_fax, 
            "emisor_fax" => str_replace("-", "", $emisor_fax), 
            "emisor_email" => $emisor_email,
            "receptor_nombre" => $receptor_nombre, 
            "receptor_tipo_identif" => $receptor_tipo_identif, 
            "receptor_num_identif" => $receptor_num_identif, 
            "receptor_provincia" => $receptor_provincia, 
            "receptor_canton" => str_pad($receptor_canton,2,"0", STR_PAD_LEFT), 
            "receptor_distrito" => str_pad($receptor_distrito,2,"0", STR_PAD_LEFT), 
            "receptor_barrio" => str_pad($receptor_barrio,2,"0", STR_PAD_LEFT), 
            "receptor_cod_pais_tel" => $receptor_cod_pais_tel, 
            "receptor_tel" => str_replace("-", "", $receptor_tel), 
            "receptor_cod_pais_fax" => $receptor_cod_pais_fax, 
            "receptor_fax" => str_replace("-", "", $receptor_fax), 
            "receptor_email" => $receptor_email,
            "condicion_venta" => $condicion_venta,
            "plazo_credito" => $plazo_credito,
            "medio_pago" => $medio_pago,
            "cod_moneda" => $cod_moneda,
            "tipo_cambio" => $tipo_cambio,
            "total_serv_gravados" => $total_serv_gravados, 
            "total_serv_exentos" => $total_serv_exentos, 
            "total_merc_gravada" => $total_merc_gravada, 
            "total_merc_exenta" => $total_merc_exenta, 
            "total_gravados" => $total_gravados, 
            "total_exentos" => $total_exentos, 
            "total_ventas" => $total_ventas, 
            "total_descuentos" => $total_descuentos, 
            "total_ventas_neta" => $total_ventas_neta, 
            "total_impuestos" => $total_impuestos, 
            "total_comprobante" => $total_comprobante,
            "otros" => $otros,
            "detalles" => $productos
        );
        $this->logger->info("crearXMLFactura", "Creating factura XML into API with params: ".json_encode($params));
        $result = $this->helper->genXMLFe($clave, $consecutivo, $fecha_emision,
                                    $emisor_nombre, $emisor_tipo_indetif, $emisor_num_identif, $nombre_comercial, $emisor_provincia, $emisor_canton, $emisor_distrito, $emisor_barrio, $emisor_otras_senas, $emisor_cod_pais_tel, $emisor_tel, $emisor_cod_pais_fax, $emisor_fax, $emisor_email,
                                    $receptor_nombre, $receptor_tipo_identif, $receptor_num_identif, $receptor_provincia, $receptor_canton, $receptor_distrito, $receptor_barrio, $receptor_cod_pais_tel, $receptor_tel, $receptor_cod_pais_fax, $receptor_fax, $receptor_email,
                                    $condicion_venta,
                                    $plazo_credito,
                                    $medio_pago,
                                    $cod_moneda,
                                    $tipo_cambio,
                                    $total_serv_gravados, $total_serv_exentos, $total_merc_gravada, $total_merc_exenta, $total_gravados, $total_exentos, $total_ventas, $total_descuentos, $total_ventas_neta, $total_impuestos, $total_comprobante,
                                    $otros,
                                    $productos);
        
        if(is_array($result)){
            if(isset($result["clave"]) && isset($result["xml"])){
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->info("crearXMLFactura", $ms."ms | API returns ".json_encode($result));
                return $result;
            }else{
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->error("crearXMLFactura", $ms."ms | 2 - API returns ".json_encode($result));
                return false;
            }
        }else{
            $ms = (round(microtime(true) * 1000)) - $bm;
            $this->logger->error("crearXMLFactura", $ms."ms | 1 - API returns ".json_encode($result));
            return false;
        }
    }
    
    
    public function firmarDocumento($tokenCertificado, $xml, $pinCertificado, $tipoDocumento){
        $bm = round(microtime(true) * 1000);
        $params = array(
            'w' => "signXML", 
            "r" => "signFE", 
            "p12Url" => $tokenCertificado,
            "inXml" => $xml,
            "pinP12" => $pinCertificado,
            "tipodoc" => $tipoDocumento
        );
        $this->logger->info("firmarDocumento", "Signing XML into API with params: ".json_encode($params));
        $result = $this->gateway->post("api.php", $params);
        if($result->info->http_code == 200){
            $result = (Array) json_decode($result->response);
            if(isset($result["resp"])){
                $result["resp"] = (Array) $result["resp"];
                if(isset($result["resp"]["xmlFirmado"])){
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->info("firmarDocumento", $ms."ms | API returns ".json_encode($result));
                    return $result["resp"]["xmlFirmado"];
                }else{
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->error("firmarDocumento", $ms."ms | 3 - API returns ".json_encode($result));
                    return false;
                }
            }else{
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->error("firmarDocumento", $ms."ms | 2 - API returns ".json_encode($result));
                return false;
            }
        }else{
            $ms = (round(microtime(true) * 1000)) - $bm;
            $this->logger->error("firmarDocumento", $ms."ms | 1 - API returns ".json_encode($result));
            return false;
        }
    }
    
    public function solicitarToken($ambienteHacienda, $usuario, $password){
        $this->logger->info("token", "Solicitando token");
        if($tokenCache = $this->verificarExistenciaToken($ambienteHacienda, $usuario)){
            if((time() - intval($tokenCache["store_time"])) < intval($tokenCache["expires_in"])){
                $this->logger->info("token", "Retornando token de cache: ".  json_encode($tokenCache));
                return $tokenCache;
            }else if((time() - intval($tokenCache["store_time"])) < intval($tokenCache["refresh_expires_in"])){
                $this->logger->info("token", "Refrescando token: ".  json_encode($tokenCache));
                return $this->refrescarSesion($ambienteHacienda, $usuario, $tokenCache["refresh_token"]);
            }
        }
        $this->logger->info("token", "Solicitando nuevo token");
        return $this->crearNuevaSesion($ambienteHacienda, $usuario, $password);
    }
    
    private function crearNuevaSesion($ambienteHacienda, $usuario, $password){
        $bm = round(microtime(true) * 1000);
        $url = $ambienteHacienda == "api-stag" ? HACIENDA_TOKEN_API_STAG : HACIENDA_TOKEN_API_PROD;
        $localClient = new RestClient([
            'base_url' => $url,
            'curl_options' => [CURLOPT_CONNECTTIMEOUT => API_CRLIBRE_CURL_TIMEOUT]
        ]);
        $params = array(
            "grant_type" => "password",
            "username" => $usuario,
            "password" => $password,
            "client_id" => $ambienteHacienda
        );
        $result = $localClient->post("/token", $params);
        $this->logger->info("solicitarToken", "Request token into Hacienda API with params: ".json_encode($params));
        if($result->info->http_code == 200){
            $result = (Array) json_decode($result->response);
            if(isset($result["access_token"]) && isset($result["refresh_token"])){
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->info("solicitarToken", $ms."ms | API returns ".json_encode($result));
                $this->guardarNuevaInfoToken($ambienteHacienda, $usuario, $result);
                return $result;
            }else{
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->error("solicitarToken", $ms."ms | 2 - API returns ".json_encode($result));
                return false;
            }
        }else{
            $ms = (round(microtime(true) * 1000)) - $bm;
            $this->logger->error("solicitarToken", $ms."ms | 1 - API returns STATUS: ".$result->info->http_code." | HEADERS:".json_encode($result->headers)." RESPONSE:".json_encode($result->response)." INFO:".json_encode($result->info));
            return false;
        }
    }
    
    private function refrescarSesion($ambienteHacienda, $usuario, $refreshToken){
        $bm = round(microtime(true) * 1000);
        $url = $ambienteHacienda == "api-stag" ? HACIENDA_TOKEN_API_STAG : HACIENDA_TOKEN_API_PROD;
        $localClient = new RestClient([
            'base_url' => $url,
            'curl_options' => [CURLOPT_CONNECTTIMEOUT => API_CRLIBRE_CURL_TIMEOUT]
        ]);
        $params = array(
            "grant_type" => "refresh_token",
            "client_id" => $ambienteHacienda,
            "refresh_token" => $refreshToken
        );
        $result = $localClient->post("/token", $params);
        $this->logger->info("refrescarToken", "Refresh token into Hacienda API with params: ".json_encode($params));
        if($result->info->http_code == 200){
            $result = (Array) json_decode($result->response);
            if(isset($result["access_token"]) && isset($result["refresh_token"])){
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->info("refrescarToken", $ms."ms | API returns ".json_encode($result));
                $this->guardarNuevaInfoToken($ambienteHacienda, $usuario, $result);
                return $result;
            }else{
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->error("refrescarToken", $ms."ms | 2 - API returns ".json_encode($result));
                return false;
            }
        }else{
            $ms = (round(microtime(true) * 1000)) - $bm;
            $this->logger->error("refrescarToken", $ms."ms | 1 - API returns STATUS: ".$result->info->http_code." | HEADERS:".json_encode($result->headers)." RESPONSE:".json_encode($result->response)." INFO:".json_encode($result->info));
            return false;
        }
    }
    
    public function destruirSesion($ambienteHacienda, $usuario){
        $this->logger->info("destruirSesion", "Exiting session into Hacienda API");
        if($tokenCache = $this->verificarExistenciaToken($ambienteHacienda, $usuario)){
            $bm = round(microtime(true) * 1000);
            $url = $ambienteHacienda == "api-stag" ? HACIENDA_TOKEN_API_STAG : HACIENDA_TOKEN_API_PROD;
            $localClient = new RestClient([
                'base_url' => $url,
                'curl_options' => [CURLOPT_CONNECTTIMEOUT => API_CRLIBRE_CURL_TIMEOUT]
            ]);
            $params = array(
                "client_id" => $ambienteHacienda,
                "refresh_token" => $tokenCache["refresh_token"]
            );
            $result = $localClient->post("/logout", $params);
            $this->logger->info("destruirSesion", "Request token into Hacienda API with params: ".json_encode($params));
            if($result->info->http_code == 204){
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->info("destruirSesion", $ms."ms | Sesion destruida con exito. API returns");
                $this->guardarNuevaInfoToken($ambienteHacienda, $usuario, "");
            }else{
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->error("destruirSesion", $ms."ms | 1 - API returns STATUS: ".$result->info->http_code." | HEADERS:".json_encode($result->headers)." RESPONSE:".json_encode($result->response)." INFO:".json_encode($result->info));
                return false;
            }
        }else{
            $this->logger->info("destruirSesion", "Session has been destroyed by another process");
        }
    }
    
    private function guardarNuevaInfoToken($ambiente, $usuario, $tokenData){
        if(is_array($tokenData)){
            $tokenData["store_time"] = time();
            $tokenData = json_encode($tokenData);
        }
        file_put_contents(PATH_DOCUMENTOS_ELECTRONICOS."_".$ambiente."_".$usuario."_token_data", $tokenData);
    }
    
    private function verificarExistenciaToken($ambiente, $usuario){
        if(file_exists(PATH_DOCUMENTOS_ELECTRONICOS."_".$ambiente."_".$usuario."_token_data")){
            $contenido = file_get_contents(PATH_DOCUMENTOS_ELECTRONICOS."_".$ambiente."_".$usuario."_token_data");
            if($this->isJson($contenido)){
                return json_decode($contenido, true);
            }
        }
        return false;
    }
    
    private function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    
    public function enviarDocumento($ambienteHacienda, $clave, $fecha, $emisorTipoIdentificacion, $emisorIdentificacion, $receptorTipoIdentificacion, $receptorIdentificacion, $token, $xml, $consecutivoReceptor = null){
        $bm = round(microtime(true) * 1000);
        $url = $ambienteHacienda == "api-stag" ? HACIENDA_RECEPCION_API_STAG : HACIENDA_RECEPCION_API_PROD;
       
        $params = array(
            "clave" => $clave,
            "fecha" => $fecha,
            "emisor" => array(
                            "tipoIdentificacion"=> $emisorTipoIdentificacion,
                            "numeroIdentificacion"=> $emisorIdentificacion),
            "comprobanteXml" => $xml
        );
        
        if($receptorTipoIdentificacion != null){
            $params["receptor"] = array(
                "tipoIdentificacion"=> $receptorTipoIdentificacion,
                "numeroIdentificacion"=> $receptorIdentificacion
            );
        }
        
        if($consecutivoReceptor != null){
            $params["consecutivoReceptor"] = $consecutivoReceptor;
        }
        
        $this->logger->info("enviarDocumento", "Sending document to Hacienda API with params: ".json_encode($params));
        
        //Initialise the cURL var
        $ch = curl_init();

        //Get the response from cURL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //Set the Url
        curl_setopt($ch, CURLOPT_URL, $url."recepcion");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, API_CRLIBRE_CURL_TIMEOUT);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        $headr = array();
        $headr[] = "Authorization: Bearer $token";
        $headr[] = 'Content-Type: application/json';

        curl_setopt($ch, CURLOPT_HTTPHEADER,$headr);
        curl_setopt($ch, CURLOPT_POST,           1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params) ); 

        // Execute the request
        $response = curl_exec($ch);
        
        if(strpos($response, 'HTTP/1.1 202 Accepted') !== false){
            $ms = (round(microtime(true) * 1000)) - $bm;
            $this->logger->info("enviarDocumento", $ms."ms | API returns ".$response);
            return true;
        }else{
            $ms = (round(microtime(true) * 1000)) - $bm;
            $this->logger->error("enviarDocumento", $ms."ms | API returns ".$response);
            return false;
        }
    }
    
    public function revisarEstadoAceptacion($ambienteHacienda, $clave, $token){
        $bm = round(microtime(true) * 1000);
        $url = $ambienteHacienda == "api-stag" ? HACIENDA_RECEPCION_API_STAG : HACIENDA_RECEPCION_API_PROD;
        $localClient = new RestClient([
            'base_url' => $url,
            'curl_options' => [CURLOPT_CONNECTTIMEOUT => API_CRLIBRE_CURL_TIMEOUT, CURLOPT_HTTPHEADER => array("Authorization: Bearer $token")]
        ]);
        $this->logger->info("revisarEstadoAceptacion", "Revisando estado into Hacienda API with params: $clave");
        $result = $localClient->get("recepcion/$clave", array());
        $respuesta["status"] = false;
        $respuesta["data"] = array("ind-estado"=>"ERROR NO RESPUESTA");
        if($result->info->http_code == 200){
            $result = (Array) json_decode($result->response);
            if(isset($result["clave"]) && isset($result["ind-estado"])){
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->info("revisarEstadoAceptacion", $ms."ms | API returns ".json_encode($result));
                $respuesta["status"] = true;
                $respuesta["data"] = $result;
            }else{
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->error("revisarEstadoAceptacion", $ms."ms | 2 - API returns ".json_encode($result));
            }
        }else{
            $ms = (round(microtime(true) * 1000)) - $bm;
            $this->logger->error("revisarEstadoAceptacion", $ms."ms | 1 - API returns STATUS: ".$result->info->http_code." | HEADERS:".json_encode($result->headers)." RESPONSE:".json_encode($result->response)." INFO:".json_encode($result->info));
        }
        return $respuesta;
    }
    
    
    public function crearXMLNotaCredito($clave, $consecutivo, $fecha_emision,
                                    $emisor_nombre, $emisor_tipo_indetif, $emisor_num_identif, $nombre_comercial, $emisor_provincia, $emisor_canton, $emisor_distrito, $emisor_barrio, $emisor_otras_senas, $emisor_cod_pais_tel, $emisor_tel, $emisor_cod_pais_fax, $emisor_fax, $emisor_email,
                                    $receptor_nombre, $receptor_tipo_identif, $receptor_num_identif, $receptor_provincia, $receptor_canton, $receptor_distrito, $receptor_barrio, $receptor_cod_pais_tel, $receptor_tel, $receptor_cod_pais_fax, $receptor_fax, $receptor_email,
                                    $condicion_venta,
                                    $plazo_credito,
                                    $medio_pago,
                                    $cod_moneda,
                                    $tipo_cambio,
                                    $total_serv_gravados, $total_serv_exentos, $total_merc_gravada, $total_merc_exenta, $total_gravados, $total_exentos, $total_ventas, $total_descuentos, $total_ventas_neta, $total_impuestos, $total_comprobante,
                                    $otros,
                                    $productos,
                                    $tipoDocumento, $numeroDocumento, $razonDocumento, $codigoDocumento, $fechaEmisionDocumento){
        $bm = round(microtime(true) * 1000);
        $params = array(
            'w' => "genXML", 
            "r" => "gen_xml_nc",
            "clave" => $clave, 
            "consecutivo" => $consecutivo, 
            "fecha_emision" => $fecha_emision,
            "emisor_nombre" => $emisor_nombre, 
            "emisor_tipo_indetif" => $emisor_tipo_indetif, 
            "emisor_num_identif" => $emisor_num_identif, 
            "nombre_comercial" => $nombre_comercial, 
            "emisor_provincia" => $emisor_provincia, 
            "emisor_canton" => str_pad($emisor_canton,2,"0", STR_PAD_LEFT), 
            "emisor_distrito" => str_pad($emisor_distrito,2,"0", STR_PAD_LEFT), 
            "emisor_barrio" => str_pad($emisor_barrio,2,"0", STR_PAD_LEFT), 
            "emisor_otras_senas" => $emisor_otras_senas, 
            "emisor_cod_pais_tel" => $emisor_cod_pais_tel, 
            "emisor_tel" => str_replace("-", "", $emisor_tel), 
            "emisor_cod_pais_fax" => $emisor_cod_pais_fax, 
            "emisor_fax" => str_replace("-", "", $emisor_fax), 
            "emisor_email" => $emisor_email,
            "receptor_nombre" => $receptor_nombre, 
            "receptor_tipo_identif" => $receptor_tipo_identif, 
            "receptor_num_identif" => $receptor_num_identif, 
            "receptor_provincia" => $receptor_provincia, 
            "receptor_canton" => str_pad($receptor_canton,2,"0", STR_PAD_LEFT), 
            "receptor_distrito" => str_pad($receptor_distrito,2,"0", STR_PAD_LEFT), 
            "receptor_barrio" => str_pad($receptor_barrio,2,"0", STR_PAD_LEFT), 
            "receptor_cod_pais_tel" => $receptor_cod_pais_tel, 
            "receptor_tel" => str_replace("-", "", $receptor_tel), 
            "receptor_cod_pais_fax" => $receptor_cod_pais_fax, 
            "receptor_fax" => str_replace("-", "", $receptor_fax), 
            "receptor_email" => $receptor_email,
            "condicion_venta" => $condicion_venta,
            "plazo_credito" => $plazo_credito,
            "medio_pago" => $medio_pago,
            "cod_moneda" => $cod_moneda,
            "tipo_cambio" => $tipo_cambio,
            "total_serv_gravados" => $total_serv_gravados, 
            "total_serv_exentos" => $total_serv_exentos, 
            "total_merc_gravada" => $total_merc_gravada, 
            "total_merc_exenta" => $total_merc_exenta, 
            "total_gravados" => $total_gravados, 
            "total_exentos" => $total_exentos, 
            "total_ventas" => $total_ventas, 
            "total_descuentos" => $total_descuentos, 
            "total_ventas_neta" => $total_ventas_neta, 
            "total_impuestos" => $total_impuestos, 
            "total_comprobante" => $total_comprobante,
            "otros" => $otros,
            "detalles" => json_encode($productos),
            "infoRefeTipoDoc" => $tipoDocumento,
            "infoRefeNumero" => $numeroDocumento,
            "infoRefeFechaEmision" => $fechaEmisionDocumento,
            "infoRefeCodigo" => $codigoDocumento,
            "infoRefeRazon" => $razonDocumento
        );
        $this->logger->info("crearXMLNotaCredito", "Creating nota credito XML into API with params: ".json_encode($params));
        $resultOr = $this->gateway->post("api.php", $params);
        if($resultOr->info->http_code == 200){
            $result = (Array) json_decode($resultOr->response);
            if(isset($result["resp"])){
                $result["resp"] = (Array) $result["resp"];
                if(isset($result["resp"]["clave"]) && isset($result["resp"]["xml"])){
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->info("crearXMLNotaCredito", $ms."ms | API returns ".json_encode($result));
                    return $result["resp"];
                }else{
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->error("crearXMLNotaCredito", $ms."ms | 3 - API returns ".json_encode($result));
                    return false;
                }
            }else{
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->error("crearXMLNotaCredito", $ms."ms | 2 - API returns ".json_encode($resultOr));
                return false;
            }
        }else{
            $ms = (round(microtime(true) * 1000)) - $bm;
            $this->logger->error("crearXMLNotaCredito", $ms."ms | 1 - API returns ".json_encode($resultOr));
            return false;
        }
    }
    
    
    public function crearXMLMensajeReceptor($clave, $consecutivo, $fecha_emision, $emisor_num_identif, $receptor_num_identif, $mensaje, $detalleMensaje, $montoImpuestos, $montoTotal){
        $bm = round(microtime(true) * 1000);
        $params = array(
            'w' => "genXML", 
            "r" => "gen_xml_mr",
            "clave" => $clave, 
            "numero_consecutivo_receptor" => $consecutivo, 
            "fecha_emision_doc" => $fecha_emision,
            "numero_cedula_emisor" => $emisor_num_identif, 
            "numero_cedula_receptor" => $receptor_num_identif, 
            "mensaje" => $mensaje, 
            "detalle_mensaje" => $detalleMensaje, 
            "monto_total_impuesto" => $montoImpuestos, 
            "total_factura" => $montoTotal
        );
        $this->logger->info("crearXMLMensajeReceptor", "Creating mensaje receptor XML into API with params: ".json_encode($params));
        $resultOr = $this->gateway->post("api.php", $params);
        if($resultOr->info->http_code == 200){
            $result = (Array) json_decode($resultOr->response);
            if(isset($result["resp"])){
                $result["resp"] = (Array) $result["resp"];
                if(isset($result["resp"]["clave"]) && isset($result["resp"]["xml"])){
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->info("crearXMLMensajeReceptor", $ms."ms | API returns ".json_encode($result));
                    return $result["resp"];
                }else{
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->error("crearXMLMensajeReceptor", $ms."ms | 3 - API returns ".json_encode($result));
                    return false;
                }
            }else{
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->error("crearXMLMensajeReceptor", $ms."ms | 2 - API returns ".json_encode($resultOr));
                return false;
            }
        }else{
            $ms = (round(microtime(true) * 1000)) - $bm;
            $this->logger->error("crearXMLMensajeReceptor", $ms."ms | 1 - API returns ".json_encode($resultOr));
            return false;
        }
    }
}