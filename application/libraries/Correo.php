<?php
class Correo{
    
    public $host = 'smtp.ipower.com';
    public $SMTPAuth = true;
    public $Username = 'facturaelectronica@garotasbonitascr.com';
    public $Password = 'XKrty371@';   
    public $Port = 465;  
    public $From = 'facturaelectronica@garotasbonitascr.com';
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