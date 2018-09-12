<?php

class API_FE{
    
    private $gateway;
    private $logger;
    
    public function __construct(){
        require_once PATH_REST_CLIENT;
        require_once PATH_API_LOGGER;
        $this->gateway = new RestClient([
            'base_url' => URL_API_CRLIBE,
            'curl_options' => [CURLOPT_CONNECTTIMEOUT => API_CRLIBRE_CURL_TIMEOUT]
        ]);
        $this->logger = new APILogger();
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
    
    public function setUpLogin($data){
        $apife = new API_FE();
        $isUp = $apife->isUp();
        $sessionKey = false;
        if($isUp){
            $sessionKey = $apife->logUser($data["Usuario_Nombre_Usuario"], strtolower(str_replace(" ", "", $data["Usuario_Nombre_Usuario"])).$data["Sucursal_Codigo"]);
            //Si no se logueo es porque debemos crear el usuario
            if($sessionKey === false){
                $sessionKey = $apife->registerUser($data["Usuario_Nombre"]." ".$data["Usuario_Apellidos"], $data["Usuario_Nombre_Usuario"], $data["Usuario_Nombre_Usuario"]."@garotasbonitas.cr", $data["Usuario_Rango"], "CR", strtolower(str_replace(" ", "", $data["Usuario_Nombre_Usuario"])).$data["Sucursal_Codigo"]);
            } 
            if($sessionKey !== false){
                $_SESSION["api_sessionkey"] = $sessionKey;
                $_SESSION["api_passw"] = strtolower(str_replace(" ", "", $data["Usuario_Nombre_Usuario"])).$data["Sucursal_Codigo"];
                $_SESSION["api_sessionup"] = true;
            }else{
                $_SESSION["api_sessionkey"] = "";
                $_SESSION["api_sessionup"] = false;
                $_SESSION["api_passw"] = "";
            }
        }
        return array("isUp" => $isUp, "sessionKey" => $sessionKey);
    }
     
    public function isUp(){
        $bm = round(microtime(true) * 1000);
        $params = ['w' => "ejemplo", "r" => "hola"];
        $this->logger->info("isUp", "Checking if API is online with params: ".json_encode($params));
        $result = $this->gateway->get("api.php", $params);
        if($result->info->http_code == 200){
            $result = (Array) json_decode($result->response);
            if(trim(@$result["resp"]) == "hola :)"){
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->info("isUp", $ms."ms | API returns succesfully ".json_encode($result));
                return true;
            }else{
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->error("isUp", $ms."ms | 2 - API returns ".json_encode($result));
                return false;
            }
        }else{
            $ms = (round(microtime(true) * 1000)) - $bm;
            $this->logger->error("isUp", $ms."ms | 1 - API fails ".json_encode($result));
            return false;
        }
    }
    
    public function logUser($user, $passw){
        $bm = round(microtime(true) * 1000);
        $params = array(
            'w' => "users", 
            "r" => "users_log_me_in",
            "userName" => $user,
            "pwd" => $passw
        );
        $this->logger->info("logUser", "Login user into API with params: ".json_encode($params));
        $result = $this->gateway->get("api.php", $params);
        if($result->info->http_code == 200){
            $result = (Array) json_decode($result->response);
            if(isset($result["resp"])){
                $result["resp"] = (array) $result["resp"];
                if(isset($result["resp"]["sessionKey"]) && isset($result["resp"]["userName"])){
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->info("logUser", $ms."ms | API returns ".json_encode($result));
                    return $result["resp"]["sessionKey"];
                }else{
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->error("logUser", $ms."ms | 3 - API returns ".json_encode($result));
                    return false;
                }
            }else{
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->error("logUser", $ms."ms | 2 - API returns ".json_encode($result));
                return false;
            }
        }else{
            $ms = (round(microtime(true) * 1000)) - $bm;
            $this->logger->error("logUser", $ms."ms | 1 - API returns ".json_encode($result));
            return false;
        }
    }
    
    public function registerUser($nombreCompleto, $usuario, $email, $about, $country, $pwd){
        $bm = round(microtime(true) * 1000);
        $params = array(
            'w' => "users", 
            "r" => "users_register",
            "fullName" => $nombreCompleto, 
            "userName" => $usuario,
            "email" => $email,
            "about" => $about,
            "country" => $country,
            "pwd" => $pwd
        );
        $this->logger->info("registerUser", "Register user into API with params: ".json_encode($params));
        $result = $this->gateway->get("api.php", $params);
        if($result->info->http_code == 200){
            $result = (array) json_decode($result->response);
            if(isset($result["resp"])){
                $result["resp"] = (array) $result["resp"];
                if(isset($result["resp"]["sessionKey"]) && isset($result["resp"]["userName"])){
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->info("registerUser", $ms."ms | API returns ".json_encode($result));
                    return $result["resp"]["sessionKey"];
                }else{
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->error("registerUser", $ms."ms | 3 - API returns ".json_encode($result));
                    return false;
                }
            }else{
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->error("registerUser", $ms."ms | 2 - API returns ".json_encode($result));
                return false;
            }
        }else{
            $ms = (round(microtime(true) * 1000)) - $bm;
            $this->logger->error("registerUser", $ms."ms | 1 - API returns ".json_encode($result));
            return false;
        }
    }
    
    public function logOutUser($sessionKey, $user){
        $bm = round(microtime(true) * 1000);
        $params = array(
            'w' => "users", 
            "r" => "users_log_me_out",
            "sessionKey" => $sessionKey,
            "iam" => $user
        );
        $this->logger->info("logOutUser", "Logout user into API with params: ".json_encode($params));
        $result = $this->gateway->get("api.php", $params);
        if($result->info->http_code == 200){
            $result = (Array) json_decode($result->response);
            if(isset($result["resp"])){
                if(trim(@$result["resp"]) == "good bye"){
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->info("logOutUser", $ms."ms | API returns ".json_encode($result));
                    return true;
                }else{
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->error("logOutUser", $ms."ms | 3 - API returns ".json_encode($result));
                    return false;
                }
            }else{
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->error("logOutUser", $ms."ms | 2 - API returns ".json_encode($result));
                return false;
            }
        }else{
            $ms = (round(microtime(true) * 1000)) - $bm;
            $this->logger->error("logOutUser", $ms."ms | 1 - API returns ".json_encode($result));
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
            'w' => "clave", 
            "r" => "clave",
            "tipoCedula" => $tipoCedula,
            "cedula" => $cedula,
            "codigoPais" => $codigoPais,
            "consecutivo" => $consecutivo,
            "situacion" => $situacion,
            "codigoSeguridad" => $codigoSeguridad,
            "tipoDocumento" => $tipoDocumento
        );
        $this->logger->info("createClave", "Creating clave into API with params: ".json_encode($params));
        $result = $this->gateway->get("api.php", $params);
        if($result->info->http_code == 200){
            $result = (Array) json_decode($result->response);
            if(isset($result["resp"])){
                $result["resp"] = (Array) $result["resp"];
                if(isset($result["resp"]["clave"]) && isset($result["resp"]["consecutivo"])){
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->info("createClave", $ms."ms | API returns ".json_encode($result));
                    return (Array) $result["resp"];
                }else{
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->error("createClave", $ms."ms | 3 - API returns ".json_encode($result));
                    return false;
                }
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
            'w' => "genXML", 
            "r" => "gen_xml_fe",
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
            "detalles" => json_encode($productos)
        );
        $this->logger->info("crearXMLFactura", "Creating factura XML into API with params: ".json_encode($params));
        $resultOr = $this->gateway->post("api.php", $params);
        if($resultOr->info->http_code == 200){
            $result = (Array) json_decode($resultOr->response);
            if(isset($result["resp"])){
                $result["resp"] = (Array) $result["resp"];
                if(isset($result["resp"]["clave"]) && isset($result["resp"]["xml"])){
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->info("crearXMLFactura", $ms."ms | API returns ".json_encode($result));
                    return $result["resp"];
                }else{
                    $ms = (round(microtime(true) * 1000)) - $bm;
                    $this->logger->error("crearXMLFactura", $ms."ms | 3 - API returns ".json_encode($result));
                    return false;
                }
            }else{
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->error("crearXMLFactura", $ms."ms | 2 - API returns ".json_encode($resultOr));
                return false;
            }
        }else{
            $ms = (round(microtime(true) * 1000)) - $bm;
            $this->logger->error("crearXMLFactura", $ms."ms | 1 - API returns ".json_encode($resultOr));
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
        $result = $localClient->post("/", $params);
        $this->logger->info("solicitarToken", "Request token into Hacienda API with params: ".json_encode($params));
        if($result->info->http_code == 200){
            $result = (Array) json_decode($result->response);
            if(isset($result["access_token"]) && isset($result["refresh_token"])){
                $ms = (round(microtime(true) * 1000)) - $bm;
                $this->logger->info("solicitarToken", $ms."ms | API returns ".json_encode($result));
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