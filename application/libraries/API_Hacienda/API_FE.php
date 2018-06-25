<?php

class API_FE{
    
    private $gateway;
    
    public function __construct(){
        require_once PATH_REST_CLIENT;
        $this->gateway = new RestClient([
            'base_url' => URL_API_CRLIBE,
            'curl_options' => [CURLOPT_CONNECTTIMEOUT => 5]
        ]);
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
        $result = $this->gateway->get("api.php", ['w' => "ejemplo", "r" => "hola"]);
        if($result->info->http_code == 200){
            $result = (Array) json_decode($result->response);
            if(trim(@$result["resp"]) == "hola :)"){
                return true;
            }else{
                return false;
            }
        }else{
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
        $result = $this->gateway->get("api.php", $params);
        if($result->info->http_code == 200){
            $result = (Array) json_decode($result->response);
            if(isset($result["resp"])){
                $result["resp"] = (array) $result["resp"];
                if(isset($result["resp"]["sessionKey"]) && isset($result["resp"]["userName"])){
                    return $result["resp"]["sessionKey"];
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
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
        $result = $this->gateway->get("api.php", $params);
        if($result->info->http_code == 200){
            $result = (array) json_decode($result->response);
            if(isset($result["resp"])){
                $result["resp"] = (array) $result["resp"];
                if(isset($result["resp"]["sessionKey"]) && isset($result["resp"]["userName"])){
                    return $result["resp"]["sessionKey"];
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
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
        $result = $this->gateway->get("api.php", $params);
        if($result->info->http_code == 200){
            $result = (Array) json_decode($result->response);
            if(isset($result["resp"])){
                if(trim(@$result["resp"]) == "good bye"){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    
}