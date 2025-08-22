<?php
require(dirname(__FILE__) . '/hacienda/firmador.php');
use Hacienda\Firmador;

$pfx = @$_POST['p12Url'];
$pin = @$_POST['pinP12']; // PIN de 4 dígitos de la llave criptográfica
$xml = @$_POST['inXml'];

// Nuevo firmador
$firmador = new Firmador();

// Se firma XML y se recibe un string resultado en Base64
echo $firmador->firmarXml($pfx, $pin, base64_decode($xml), $firmador::TO_BASE64_STRING);