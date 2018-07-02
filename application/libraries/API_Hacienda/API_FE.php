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
        $localClient = new RestClient([
            'base_url' => "https://www.google.com/",
            'curl_options' => [CURLOPT_CONNECTTIMEOUT => API_CRLIBRE_CURL_TIMEOUT]
        ]);
        $result = $localClient->get("/", []);
        $this->logger->info("internetIsOnline", "Checking internet status");
        if($result->info->http_code == 200){
            $this->logger->info("internetIsOnline", "Internet status is OK");
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
        $params = ['w' => "ejemplo", "r" => "hola"];
        $this->logger->info("isUp", "Checking if API is online with params: ".json_encode($params));
        $result = $this->gateway->get("api.php", $params);
        if($result->info->http_code == 200){
            $result = (Array) json_decode($result->response);
            if(trim(@$result["resp"]) == "hola :)"){
                $this->logger->info("isUp", "API returns succesfully ".json_encode($result));
                return true;
            }else{
                $this->logger->error("isUp", "2 - API returns ".json_encode($result));
                return false;
            }
        }else{
            $this->logger->error("isUp", "1 - API fails ".json_encode($result));
            return false;
        }
    }
    
    public function logUser($user, $passw){
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
                    $this->logger->info("logUser", "API returns ".json_encode($result));
                    return $result["resp"]["sessionKey"];
                }else{
                    $this->logger->error("logUser", "3 - API returns ".json_encode($result));
                    return false;
                }
            }else{
                $this->logger->error("logUser", "2 - API returns ".json_encode($result));
                return false;
            }
        }else{
            $this->logger->error("logUser", "1 - API returns ".json_encode($result));
            return false;
        }
    }
    
    public function registerUser($nombreCompleto, $usuario, $email, $about, $country, $pwd){
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
                    $this->logger->info("registerUser", "API returns ".json_encode($result));
                    return $result["resp"]["sessionKey"];
                }else{
                    $this->logger->error("registerUser", "3 - API returns ".json_encode($result));
                    return false;
                }
            }else{
                $this->logger->error("registerUser", "2 - API returns ".json_encode($result));
                return false;
            }
        }else{
            $this->logger->error("registerUser", "1 - API returns ".json_encode($result));
            return false;
        }
    }
    
    public function logOutUser($sessionKey, $user){
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
                    $this->logger->info("logOutUser", "API returns ".json_encode($result));
                    return true;
                }else{
                    $this->logger->error("logOutUser", "3 - API returns ".json_encode($result));
                    return false;
                }
            }else{
                $this->logger->error("logOutUser", "2 - API returns ".json_encode($result));
                return false;
            }
        }else{
            $this->logger->error("logOutUser", "1 - API returns ".json_encode($result));
            return false;
        }
    }
    
    public function uploadCertificate($user, $sessionKey, $certPath, $name){
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
                    $this->logger->info("uploadCertificate", "API returns ".json_encode($response));
                    return $response["downloadCode"];
                }else{
                    $this->logger->error("uploadCertificate", "3 - API returns ".json_encode($response));
                    return false;
                }
            }else{
                $this->logger->error("uploadCertificate", "2 - API returns ".json_encode($response));
                return false;
            }
        }else{
            $this->logger->error("uploadCertificate", "1 - API returns ".json_encode($response));
            return false;
        }
    }
    
    public function createClave($tipoCedula, $cedula, $codigoPais, $consecutivo, $situacion, $codigoSeguridad, $tipoDocumento){
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
                $this->logger->info("createClave", "API returns ".json_encode($result));
                return (Array) $result["resp"];
            }else{
                $this->logger->error("createClave", "2 - API returns ".json_encode($result));
                return false;
            }
        }else{
            $this->logger->error("createClave", "1 - API returns ".json_encode($result));
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
            "emisor_canton" => $emisor_canton, 
            "emisor_distrito" => $emisor_distrito, 
            "emisor_barrio" => $emisor_barrio, 
            "emisor_otras_senas" => $emisor_otras_senas, 
            "emisor_cod_pais_tel" => $emisor_cod_pais_tel, 
            "emisor_tel" => $emisor_tel, 
            "emisor_cod_pais_fax" => $emisor_cod_pais_fax, 
            "emisor_fax" => $emisor_fax, 
            "emisor_email" => $emisor_email,
            "receptor_nombre" => $receptor_nombre, 
            "receptor_tipo_identif" => $receptor_tipo_identif, 
            "receptor_num_identif" => $receptor_num_identif, 
            "receptor_provincia" => $receptor_provincia, 
            "receptor_canton" => $receptor_canton, 
            "receptor_distrito" => $receptor_distrito, 
            "receptor_barrio" => $receptor_barrio, 
            "receptor_cod_pais_tel" => $receptor_cod_pais_tel, 
            "receptor_tel" => $receptor_tel, 
            "receptor_cod_pais_fax" => $receptor_cod_pais_fax, 
            "receptor_fax" => $receptor_fax, 
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
        $result = $this->gateway->post("api.php", $params);
        if($result->info->http_code == 200){
            $result = (Array) json_decode($result->response);
            if(isset($result["resp"])){
                $result["resp"] = (Array) $result["resp"];
                if(isset($result["resp"]["clave"]) && isset($result["resp"]["xml"])){
                    $this->logger->info("crearXMLFactura", "API returns ".json_encode($result));
                    return $result["resp"];
                }else{
                    $this->logger->error("crearXMLFactura", "3 - API returns ".json_encode($result));
                    return false;
                }
            }else{
                $this->logger->error("crearXMLFactura", "2 - API returns ".json_encode($result));
                return false;
            }
        }else{
            $this->logger->error("crearXMLFactura", "1 - API returns ".json_encode($result));
            return false;
        }
    }
}