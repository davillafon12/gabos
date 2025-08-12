<?php
class Correo{
    
    public $host = 'CORREO_SMTP_URL';
    public $SMTPAuth = true;
    public $Username = 'CORREO_EMAIL_USER';
    public $Password = 'CORREO_EMAIL_USER_PASSWORD';   
    public $Port = 'CORREO_SMTP_PORT';  
    public $From = 'CORREO_EMAIL_USER';
    public $isHTML = true;
    
    
    function enviarCorreo($para, $asunto, $cuerpo, $fromName, $attachs = array()){
        require_once 'phpmailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;
        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = $this->host;  // Specify main and backup SMTP servers
        $mail->SMTPAuth = $this->SMTPAuth;                               // Enable SMTP authentication
        $mail->Username = $this->Username;                 // SMTP username
        $mail->Password = $this->Password;                           // SMTP password
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = $this->Port;                                    // TCP port to connect to
        $mail->From = $this->From;
        $mail->FromName = $fromName;
        $mail->addAddress($para);              
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->CharSet = "utf-8";
        $mail->Subject = $asunto;
        $mail->Body    = $cuerpo;
        
        if(sizeof($attachs)>0){
            foreach($attachs as $fileN){
                $mail->addAttachment($fileN);
            }
        }
        
        if(!$mail->send()) {
            return false;
        } else {
            return true;
        }
    }
}
