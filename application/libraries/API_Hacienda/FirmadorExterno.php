<?php 

$p12Url = @$_POST['ruta'];
$pinP12 = @$_POST['clave'];
$inXml = @$_POST['xml'];
$tipoDocumento = @$_POST['tipoDocumento'];

require_once 'Firmador.php';

$fac = new Firmadocr();
//$inXmlUrl debe de ser en Base64
//$p12Url es un downloadcode previamente suministrado al subir el certificado en el modulo fileUploader -> subir_certif
//Tipo es el tipo de documento
// 01 FE
//02 ND
//03 NC
//04 TE
//05 06 07 Mensaje Receptor
echo $fac->firmar($p12Url, $pinP12, $inXml, $tipoDocumento);