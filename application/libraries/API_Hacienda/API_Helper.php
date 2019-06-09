<?php

class API_Helper{
    
    public function getClave($tipoDocumento = "", $tipoCedula = "", $cedula = "", $situacion = "", $codigoPais = "", $consecutivo = "", $codigoSeguridad = "", $sucursal = "", $terminal = "") {

        $dia = date('d');
        $mes = date('m');
        $ano = date('y');


        //Validamos el parametro de cedula    
        if ($cedula == "" && strlen($cedula) == 0) {
            return "El valor cedula no debe ser vacio";
        } else if (!ctype_digit($cedula)) {
            return "El valor cedula no es numeral";
        }


        //Validamos el parametro de cedula    
        if ($codigoPais == "" && strlen($codigoPais) == 0) {
            return "El valor codigoPais no debe ser vacio";
        } else if (!ctype_digit($codigoPais)) {
            return "El valor codigoPais no es numeral";
        }


        //Validamos que venga el parametro de sucursal

        if ($sucursal == "" && strlen($sucursal) == 0) {
            $sucursal = "001";
        } else if (ctype_digit($sucursal)) {

            if (strlen($sucursal) < 3) {
                $sucursal = str_pad($sucursal, 3, "0", STR_PAD_LEFT);
            } else if (strlen($sucursal) != 3 && $sucursal != 0) {
                $arrayResp = array(
                    "error" => "Error en sucursal",
                    "razon" => "el tamaño es diferente de 3 digitos"
                );
                return $arrayResp;
            }
        } else {
            return "El valor sucursal no es numeral";
        }




        //Validamos que venga el parametro de terminal
        if ($terminal == "" && strlen($terminal) == 0) {
            $terminal = "00001";
        } else if (ctype_digit($terminal)) {

            if (strlen($terminal) < 5) {
                $terminal = str_pad($terminal, 5, "0", STR_PAD_LEFT);
            } else if (strlen($terminal) != 5 && $terminal != 0) {
                $arrayResp = array(
                    "error" => "Error en terminal",
                    "razon" => "el tamaño es diferente de 5 digitos"
                );
                return $arrayResp;
            }
        } else {
            return "El valor terminal no es numeral";
        }




        //Validamos el consecutivo


        if ($consecutivo == "" && strlen($consecutivo) == 0) {
            return "El consecutivo no puede ser vacio";
        } else if (strlen($consecutivo) < 10) {
            $consecutivo = str_pad($consecutivo, 10, "0", STR_PAD_LEFT);
        } else if (strlen($consecutivo) != 10 && $consecutivo != 0) {
            $arrayResp = array(
                "error" => "Error en consecutivo",
                "razon" => "el tamaño consecutivo es diferente de 10 digitos"
            );
            return $arrayResp;
        }


        //Validamos el codigoSeguridad


        if ($codigoSeguridad == "" && strlen($codigoSeguridad) == 0) {
            return "El consecutivo no puede ser vacio";
        } else if (strlen($codigoSeguridad) < 8) {
            $codigoSeguridad = str_pad($codigoSeguridad, 8, "0", STR_PAD_LEFT);
        } else if (strlen($codigoSeguridad) != 8 && $codigoSeguridad != 0) {
            $arrayResp = array(
                "error" => "Error en codigo Seguridad",
                "razon" => "el tamaño codigo Seguridad es diferente de 8 digitos"
            );
            return $arrayResp;
        }



        $tipoDoc = $tipoDocumento;

        $tipos = array("FE", "ND", "NC", "TE", "CCE", "CPCE", "RCE", "FEC");

        if (in_array($tipoDoc, $tipos)) {
            switch ($tipoDoc) {
                case 'FE': //Factura Electronica
                    $tipoDocumento = "01";
                    break;
                case 'ND': // Nota de Debito
                    $tipoDocumento = "02";
                    break;
                case 'NC': // Nota de Credito
                    $tipoDocumento = "03";
                    break;
                case 'TE': // Tiquete Electronico
                    $tipoDocumento = "04";
                    break;
                case 'CCE': // Confirmacion Comprabante Electronico
                    $tipoDocumento = "05";
                    break;
                case 'CPCE': // Confirmacion Parcial Comprbante Electronico
                    $tipoDocumento = "06";
                    break;
                case 'RCE': // Rechazo Comprobante Electronico
                    $tipoDocumento = "07";
                    break;
                case 'FEC': // Factura Elctronica de Compra
                    $tipoDocumento = "08";
                    break;
            }
        } else {
            return "No se encuentra tipo de documento";
        }

        $consecutivoFinal = $sucursal . $terminal . $tipoDocumento . $consecutivo;
        //-----------------------------------------------//
        //Numero de Cedula + el indice identificador

        $identificacion = "";
        $cedulas = array("fisico", "juridico", "dimex", "nite");
        if (in_array($tipoCedula, $cedulas)) {
            switch ($tipoCedula) {
                case 'fisico': //fisico se agregan 3 ceros para completar los 12 caracteres
                    $identificacion = "000" . $cedula;
                    break;
                case 'juridico': // juridico se agregan 2 ceros para completar los 12 caracteres
                    $identificacion = "00" . $cedula;
                    break;
                case 'dimex': // dimex puede ser de 11 0 12 caracteres
                    if (strlen($cedula) == 11) {
                        //En caso de ser 11 caracteres se le agrega un 0
                        $identificacion = "0" . $cedula;
                    }else if(strlen($cedula) == 12){
                        $identificacion =$cedula;
                    }else{
                        return "dimex incorrecto";
                    }
                    break;
                case 'nite': // nite se agregan 2 ceros para completar los 12 caracteres
                    $identificacion = "00" . $cedula;
                    break;
            }
        } else {
            return "No se encuentra tipo de cedula";
        }


        //-----------------------------------------------//
        //1	Normal	Comprobantes electrónicos que son generados y transmitidos en el mismo acto de compra-venta y prestación del servicio al sistema de validación de comprobantes electrónicos de la Dirección General de Tributación de Costa Rica.
        //2	Contingencia	Comprobantes electrónicos que sustituyen al comprobante físico emitido por contingencia.
        //3	Sin internet	Comprobantes que han sido generados y expresados en formato electrónico, pero no se cuenta con el respectivo acceso a internet para el envío inmediato de los mismos a la Dirección General de Tributación de Costa Rica.
        $situaciones = array("normal", "contingencia", "sininternet");
        if (in_array($situacion, $situaciones)) {
            switch ($situacion) {
                case 'normal': //normal
                    $situacion = 1;
                    break;
                case 'contingencia': // Situacion de contingencia
                    $situacion = 2;
                    break;
                case 'sininternet': //Situacion sin internet
                    $situacion = 3;
                    break;
            }
        } else {
            return "No se encuentra el tipo de situacion";
        }

        //-----------------------------------------------//     
        //Crea la clave 
        $clave = $codigoPais . $dia . $mes . $ano . $identificacion . $consecutivoFinal . $situacion . $codigoSeguridad;
        $arrayResp = array(
            "clave" => "$clave",
            "consecutivo" => "$consecutivoFinal",
        );
        return $arrayResp;
    }
    
    public function genXMLFe($clave, $consecutivo, $fechaEmision,
                            $emisorNombre, $emisorTipoIdentif, $emisorNumIdentif, $nombreComercial, $emisorProv, $emisorCanton, $emisorDistrito, $emisorBarrio, 
                            $emisorOtrasSenas, $emisorCodPaisTel, $emisorTel, $emisorCodPaisFax, $emisorFax, $emisorEmail,
                            $receptorNombre, $receptorTipoIdentif, $recenprotNumIdentif, $receptorProvincia, $receptorCanton, $receptorDistrito, 
                            $receptorBarrio, $receptorCodPaisTel, $receptorTel, $receptorCodPaisFax, $receptorFax, $receptorEmail,
                            $condVenta,
                            $plazoCredito,
                            $medio_pago,
                            $codMoneda,
                            $tipoCambio,
                            $totalServGravados, $totalServExentos, $totalMercGravadas, $totalMercExentas, $totalGravados, $totalExentos, $totalVentas, 
                            $totalDescuentos, $totalVentasNeta, $totalImp, $totalComprobante,
                            $otros,
                            $productos,
                            $codigoActividad, $totalServiciosExonerados, $totalMercanciaExonerada, $totalExonerado, $totalIVADevuelto, $totalOtrosCargos,
                            $esFacturaCompra = false, $receptorOtrasSenas = "-") {
        
        $noReceptor = (trim($receptorNombre) == "" || $receptorNombre == null || $receptorNombre == "null");
        //detalles de tiquete / factura
        $medioPago = explode(",", $medio_pago);
        $otrosType = "";
        //detalles de la compra

        $detalles = $productos;

        $openTag = '<FacturaElectronica xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/facturaElectronica" xsi:schemaLocation="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/facturaElectronica https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.3/facturaElectronica.xsd">';
        $closeTag = "</FacturaElectronica>";
        
        if($noReceptor){
            $openTag = '<TiqueteElectronico xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/tiqueteElectronico" xsi:schemaLocation="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/tiqueteElectronico https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.3/tiqueteElectronico.xsd">';
            $closeTag = "</TiqueteElectronico>";
        }

        if($esFacturaCompra){
            $openTag = '<FacturaElectronicaCompra xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/facturaElectronicaCompra" xsi:schemaLocation="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/facturaElectronicaCompra https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.3/facturaElectronicaCompra.xsd">';
            $closeTag = "</FacturaElectronicaCompra>";
        }
        
        $xmlString = '<?xml version="1.0" encoding="utf-8"?>
            ' . $openTag . '
            <Clave>' . $clave . '</Clave>
            <CodigoActividad>' . $codigoActividad . '</CodigoActividad>
            <NumeroConsecutivo>' . $consecutivo . '</NumeroConsecutivo>
            <FechaEmision>' . $fechaEmision . '</FechaEmision>
            <Emisor>
                <Nombre>' . $emisorNombre . '</Nombre>
                <Identificacion>
                    <Tipo>' . $emisorTipoIdentif . '</Tipo>
                    <Numero>' . $emisorNumIdentif . '</Numero>
                </Identificacion>
                <NombreComercial>' . $nombreComercial . '</NombreComercial>';

        if ($emisorProv == '' or $emisorCanton == '' or $emisorDistrito == '' or $emisorOtrasSenas == '') {

        } else {
            $xmlString .= '
                <Ubicacion>
                    <Provincia>' . $emisorProv . '</Provincia>
                    <Canton>' . $emisorCanton . '</Canton>
                    <Distrito>' . $emisorDistrito . '</Distrito>'
                    .($emisorBarrio == null ? '' : '<Barrio>' . $emisorBarrio . '</Barrio>').
                    '<OtrasSenas>' . $emisorOtrasSenas . '</OtrasSenas>
                </Ubicacion>';
        }

        if ($emisorCodPaisTel == '' or $emisorTel == '' or $emisorCodPaisTel == null or $emisorTel == null) {

        } else {
            $xmlString .= '
                <Telefono>
                    <CodigoPais>' . $emisorCodPaisTel . '</CodigoPais>
                    <NumTelefono>' . $emisorTel . '</NumTelefono>
                </Telefono>';
        }
        if ($emisorCodPaisFax == '' or $emisorFax == '' or $emisorCodPaisFax == null or $emisorFax == null) {

        } else {
            $xmlString .= '
                <Fax>
                    <CodigoPais>' . $emisorCodPaisFax . '</CodigoPais>
                    <NumTelefono>' . $emisorFax . '</NumTelefono>
                </Fax>';
        }
               $xmlString .= '<CorreoElectronico>' . $emisorEmail . '</CorreoElectronico>
            </Emisor>';

            if(!$noReceptor){
                $xmlString .= '<Receptor>
                    <Nombre>' . $receptorNombre . '</Nombre>
                    <Identificacion>
                        <Tipo>' . $receptorTipoIdentif . '</Tipo>
                        <Numero>' . $recenprotNumIdentif . '</Numero>
                    </Identificacion>';

                    if ($receptorProvincia == '' or $receptorCanton == '' or $receptorDistrito == '' or $receptorOtrasSenas == '') {

                    } else {

                       $xmlString .= '<Ubicacion>
                                             <Provincia>' . $receptorProvincia . '</Provincia>
                                            <Canton>' . $receptorCanton . '</Canton>
                                            <Distrito>' . $receptorDistrito . '</Distrito>'
                                            .($receptorBarrio == null ? '' : '<Barrio>' . $receptorBarrio . '</Barrio>').
                                            '<OtrasSenas>' . $receptorOtrasSenas . '</OtrasSenas>
                                    </Ubicacion>';
                    }

                    if ($receptorCodPaisTel == '' or $receptorTel == '' or $receptorCodPaisTel == null or $receptorTel == null) {

                    } else {


                     $xmlString .= '<Telefono>
                                              <CodigoPais>' . $receptorCodPaisTel . '</CodigoPais>
                                              <NumTelefono>' . $receptorTel . '</NumTelefono>
                                    </Telefono>';
                    }

                    if ($receptorCodPaisFax == '' or $receptorFax == '' or $receptorCodPaisFax == null or $receptorFax == null) {

                    } else {
                        $xmlString .= '<Fax>
                                              <CodigoPais>' . $receptorCodPaisFax . '</CodigoPais>
                                             <NumTelefono>' . $receptorFax . '</NumTelefono>
                                    </Fax>';
                    }



                    $xmlString .= '<CorreoElectronico>' . $receptorEmail . '</CorreoElectronico>
                </Receptor>';
            }
            $xmlString .= '<CondicionVenta>' . $condVenta . '</CondicionVenta>
            <PlazoCredito>' . $plazoCredito . '</PlazoCredito>';

            foreach($medioPago as $mp){
                $xmlString .= '<MedioPago>' . $mp . '</MedioPago>';
            }

            $xmlString .= '<DetalleServicio>';
   
        $l = 1;
        foreach ($detalles as $d) {
            $xmlString .= '<LineaDetalle>
                      <NumeroLinea>' . $l . '</NumeroLinea>
                      <Codigo>' . $d["codigo"] . '</Codigo>
                      <CodigoComercial>
                        <Tipo>' . $d["tipoCodigo"] . '</Tipo>
                        <Codigo>' . $d["codigo"] . '</Codigo>
                      </CodigoComercial>
                      <Cantidad>' . $d["cantidad"] . '</Cantidad>
                      <UnidadMedida>' . $d["unidadMedida"] . '</UnidadMedida>
                      <Detalle>' . $d["detalle"] . '</Detalle>
                      <PrecioUnitario>' . $d["precioUnitario"] . '</PrecioUnitario>
                      <MontoTotal>' . $d["montoTotal"] . '</MontoTotal>';
            if (isset($d["montoDescuento"]) && $d["montoDescuento"] != "") {
                
                $xmlString .= '<Descuento>'
                        . '<MontoDescuento>' . $d["montoDescuento"] . '</MontoDescuento>'
                        . '<NaturalezaDescuento>' . $d["naturalezaDescuento"] . '</NaturalezaDescuento>'
                        . '</Descuento>';
            }

            $xmlString .= '<SubTotal>' . $d["subtotal"] . '</SubTotal>'
                    . '<BaseImponible>' . $d["baseImponible"] . '</BaseImponible>';
            if (isset($d["impuesto"]) && $d["impuesto"] != "") {
                foreach ($d["impuesto"] as $i) {
                    $xmlString .= '<Impuesto>
                    <Codigo>' . $i->codigo . '</Codigo>
                    <CodigoTarifa>' . $i->codigoTarifa . '</CodigoTarifa>
                    <Tarifa>' . $i->tarifa . '</Tarifa>
                    <FactorIVA>' . $i->factorIVA . '</FactorIVA>
                    <Monto>' . $i->monto . '</Monto>';
                    if (isset($i->exoneracion) && $i->exoneracion != "") {
                        $xmlString .= '
                        <Exoneracion>
                            <TipoDocumento>' . $i->exoneracion->tipoDocumento . '</TipoDocumento>
                            <NumeroDocumento>' . $i->exoneracion->numeroDocumento . '</NumeroDocumento>
                            <NombreInstitucion>' . $i->exoneracion->nombreInstitucion . '</NombreInstitucion>
                            <FechaEmision>' . $i->exoneracion->fechaEmision . '</FechaEmision>
                            <PorcentajeExoneracion>' . $i->exoneracion->porcentajeCompra . '</PorcentajeExoneracion>
                            <MontoExoneracion>' . $i->exoneracion->montoImpuesto . '</MontoExoneracion>
                        </Exoneracion>';
                    }

                    $xmlString .= '</Impuesto>';
                }
            }


            $xmlString .= '<MontoTotalLinea>' . $d["montoTotalLinea"] . '</MontoTotalLinea>';
            $xmlString .= '</LineaDetalle>';
            $l++;
        }
        $xmlString .= '</DetalleServicio>
            <ResumenFactura>
            <CodigoTipoMoneda>
                <CodigoMoneda>' . $codMoneda . '</CodigoMoneda>
                <TipoCambio>' . $tipoCambio . '</TipoCambio>
            </CodigoTipoMoneda>
            <TotalServGravados>' . $totalServGravados . '</TotalServGravados>
            <TotalServExentos>' . $totalServExentos . '</TotalServExentos>
            <TotalServExonerado>' . $totalServiciosExonerados . '</TotalServExonerado>
            <TotalMercanciasGravadas>' . $totalMercGravadas . '</TotalMercanciasGravadas>
            <TotalMercanciasExentas>' . $totalMercExentas . '</TotalMercanciasExentas>
            <TotalMercExonerada>' . $totalMercanciaExonerada . '</TotalMercExonerada>
            <TotalGravado>' . $totalGravados . '</TotalGravado>
            <TotalExento>' . $totalExentos . '</TotalExento>
            <TotalExonerado>' . $totalExonerado . '</TotalExonerado>
            <TotalVenta>' . $totalVentas . '</TotalVenta>
            <TotalDescuentos>' . $totalDescuentos . '</TotalDescuentos>
            <TotalVentaNeta>' . $totalVentasNeta . '</TotalVentaNeta>
            <TotalImpuesto>' . $totalImp . '</TotalImpuesto>
            <TotalIVADevuelto>' . $totalIVADevuelto . '</TotalIVADevuelto>
            <TotalOtrosCargos>' . $totalOtrosCargos . '</TotalOtrosCargos>
            <TotalComprobante>' . $totalComprobante . '</TotalComprobante>
            </ResumenFactura>';
        if ($otros == '' or $otrosType == '' or $otros == null or $otrosType == null) {

        } else {
            $tipos = array("Otros", "OtroTexto", "OtroContenido");
            if (in_array($otrosType, $tipos)) {
                $xmlString .= '
                <Otros>
            <' . $otrosType . '>' . $otros . '</' . $otrosType . '>
            </Otros>';
            } else {

            }
        }
        $xmlString .= "
                $closeTag";
        $arrayResp = array(
            "clave" => $clave,
            "xml" => base64_encode($xmlString)
        );
        return $arrayResp;
    }
    
    function signFE($p12Url, $pinP12, $inXml, $tipoDoc) {
        require_once 'Firmador.php';
        $tipoDocumento = "";
        $tipos = array("FE", "ND", "NC", "TE","CCE","CPCE","RCE","FEC");
        if (in_array($tipoDoc, $tipos)) {
            switch ($tipoDoc) {
                case 'FE': //Factura Electronica
                    $tipoDocumento = "01";
                    break;
                case 'ND': // Nota de Debito
                    $tipoDocumento = "02";
                    break;
                case 'NC': // Nota de Credito
                    $tipoDocumento = "03";
                    break;
                case 'TE': // Tiquete Electronico
                    $tipoDocumento = "04";
                    break;
                case 'CCE': // Confirmacion Comprabante Electronico
                    $tipoDocumento = "05";
                    break;
                case 'CPCE': // Confirmacion Parcial Comprbante Electronico
                    $tipoDocumento = "06";
                    break;
                case 'RCE': // Rechazo Comprobante Electronico
                    $tipoDocumento = "07";
                    break;
                case 'FEC': // Factura Electronica de Compras
                    $tipoDocumento = "08";
                    break;
            }
        } else {
            return "No se encuentra tipo de documento";
        }


        $fac = new Firmadocr();
        //$inXmlUrl debe de ser en Base64 
        //$p12Url es un downloadcode previamente suministrado al subir el certificado en el modulo fileUploader -> subir_certif
        //Tipo es el tipo de documento 
        // 01 FE
        //02 ND
        //03 NC
        //04 TE
        //05 06 07 Mensaje Receptor
        $returnFile = $fac->firmar($p12Url, $pinP12, $inXml, $tipoDocumento);
        $arrayResp = array(
            "xmlFirmado" => $returnFile
        );

        return $arrayResp;
    }
    
    function genXMLNC($clave, $consecutivo, $fechaEmision,
                    $emisorNombre, $emisorTipoIdentif, $emisorNumIdentif, $nombreComercial, $emisorProv, $emisorCanton, $emisorDistrito, 
                    $emisorBarrio, $emisorOtrasSenas, $emisorCodPaisTel, $emisorTel, $emisorCodPaisFax, $emisorFax, $emisorEmail,
                    $receptorNombre, $receptorTipoIdentif, $recenprotNumIdentif, $receptorProvincia, $receptorCanton, $receptorDistrito, 
                    $receptorBarrio, $receptorCodPaisTel, $receptorTel, $receptorCodPaisFax, $receptorFax, $receptorEmail,
                    $condVenta,
                    $plazoCredito,
                    $medioPago,
                    $codMoneda,
                    $tipoCambio,
                    $totalServGravados, $totalServExentos, $totalMercGravadas, $totalMercExentas, $totalGravados, $totalExentos, $totalVentas, 
                    $totalDescuentos, $totalVentasNeta, $totalImp, $totalComprobante,
                    $otros,
                    $productos,
                    $infoRefeTipoDoc, $infoRefeNumero, $infoRefeRazon, $infoRefeCodigo, $infoRefeFechaEmision,
                    $codigoActividad, $totalServiciosExonerados, $totalMercanciaExonerada, $totalExonerado, $totalIVADevuelto, $totalOtrosCargos) {
   
        $receptorOtrasSenas = "";
        $noReceptor = (trim($receptorNombre) == "" || $receptorNombre == null || $receptorNombre == "null");
        $otrosType = "";
        //detalles de la compra
        $detalles = $productos;
        $medioPago = explode(",", $medioPago);
        //return $detalles;
        $xmlString = '<?xml version = "1.0" encoding = "utf-8"
        ?>
        <NotaCreditoElectronica xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/notaCreditoElectronica" xsi:schemaLocation="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/notaCreditoElectronica https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.3/notaCreditoElectronica.xsd">
        <Clave>' . $clave . '</Clave>
        <CodigoActividad>' . $codigoActividad . '</CodigoActividad>
        <NumeroConsecutivo>' . $consecutivo . '</NumeroConsecutivo>
        <FechaEmision>' . $fechaEmision . '</FechaEmision>
        <Emisor>
            <Nombre>' . $emisorNombre . '</Nombre>
            <Identificacion>
                <Tipo>' . $emisorTipoIdentif . '</Tipo>
                <Numero>' . $emisorNumIdentif . '</Numero>
            </Identificacion>
            <NombreComercial>' . $nombreComercial . '</NombreComercial>';


        if ($emisorProv == '' or $emisorCanton == '' or $emisorDistrito == '' or $emisorBarrio == '' or $emisorOtrasSenas == '') {

        } else {
            $xmlString .= '
            <Ubicacion>
                <Provincia>' . $emisorProv . '</Provincia>
                <Canton>' . $emisorCanton . '</Canton>
                <Distrito>' . $emisorDistrito . '</Distrito>
                <Barrio>' . $emisorBarrio . '</Barrio>
                <OtrasSenas>' . $emisorOtrasSenas . '</OtrasSenas>
            </Ubicacion>';
        }



        if ($emisorCodPaisTel == '' or $emisorTel == '') {

        } else {
            $xmlString .= '
            <Telefono>
                <CodigoPais>' . $emisorCodPaisTel . '</CodigoPais>
                <NumTelefono>' . $emisorTel . '</NumTelefono>
            </Telefono>';
        }
        if ($emisorCodPaisFax == '' or $emisorFax == '') {

        } else {
            $xmlString .= '
            <Fax>
                <CodigoPais>' . $emisorCodPaisFax . '</CodigoPais>
                <NumTelefono>' . $emisorFax . '</NumTelefono>
            </Fax>';
        }


        $xmlString .= '<CorreoElectronico>' . $emisorEmail . '</CorreoElectronico>

            </Emisor>';

        if(!$noReceptor){
            $xmlString .= '<Receptor>
                            <Nombre>' . $receptorNombre . '</Nombre>
                            <Identificacion>
                                <Tipo>' . $receptorTipoIdentif . '</Tipo>
                                <Numero>' . $recenprotNumIdentif . '</Numero>
                            </Identificacion>';

                    if ($receptorProvincia == '' or $receptorCanton == '' or $receptorDistrito == '' or $receptorBarrio == '' or $receptorOtrasSenas != '') {

                    } else {
                        $xmlString .= '
                                     <Ubicacion>
                                             <Provincia>' . $receptorProvincia . '</Provincia>
                                            <Canton>' . $receptorCanton . '</Canton>
                                            <Distrito>' . $receptorDistrito . '</Distrito>
                                            <Barrio>' . $receptorBarrio . '</Barrio>
                                            <OtrasSenas>' . $receptorOtrasSenas . '</OtrasSenas>
                                    </Ubicacion>';
                    }

                    if ($receptorCodPaisTel == '' or $receptorTel == '') {

                    } else {
                        $xmlString .= '
                                     <Telefono>
                                              <CodigoPais>' . $receptorCodPaisTel . '</CodigoPais>
                                              <NumTelefono>' . $receptorTel . '</NumTelefono>
                                    </Telefono>';
                    }

                    if ($receptorCodPaisFax == '' or $receptorFax == '') {

                    } else {
                        $xmlString .= '
                                     <Fax>
                                              <CodigoPais>' . $receptorCodPaisFax . '</CodigoPais>
                                             <NumTelefono>' . $receptorFax . '</NumTelefono>
                                    </Fax>';
                    }


                    $xmlString .= '

                            <CorreoElectronico>' . $receptorEmail . '</CorreoElectronico>
                        </Receptor>';
        }

        $xmlString .= '<CondicionVenta>' . $condVenta . '</CondicionVenta>
        <PlazoCredito>' . $plazoCredito . '</PlazoCredito>';

            foreach($medioPago as $mp){
                $xmlString .= '<MedioPago>' . $mp . '</MedioPago>';
            }

            $xmlString .= '<DetalleServicio>';

        $l = 1;
        foreach ($detalles as $d) {
            $xmlString .= '<LineaDetalle>
                      <NumeroLinea>' . $l . '</NumeroLinea>
                      <Codigo>' . $d["codigo"] . '</Codigo>
                      <CodigoComercial>
                        <Tipo>' . $d["tipoCodigo"] . '</Tipo>
                        <Codigo>' . $d["codigo"] . '</Codigo>
                      </CodigoComercial>
                      <Cantidad>' . $d["cantidad"] . '</Cantidad>
                      <UnidadMedida>' . $d["unidadMedida"] . '</UnidadMedida>
                      <Detalle>' . $d["detalle"] . '</Detalle>
                      <PrecioUnitario>' . $d["precioUnitario"] . '</PrecioUnitario>
                      <MontoTotal>' . $d["montoTotal"] . '</MontoTotal>';
            if (isset($d["montoDescuento"]) && $d["montoDescuento"] != "") {
                
                $xmlString .= '<Descuento>'
                        . '<MontoDescuento>' . $d["montoDescuento"] . '</MontoDescuento>'
                        . '<NaturalezaDescuento>' . $d["naturalezaDescuento"] . '</NaturalezaDescuento>'
                        . '</Descuento>';
            }

            $xmlString .= '<SubTotal>' . $d["subtotal"] . '</SubTotal>'
                    . '<BaseImponible>' . $d["baseImponible"] . '</BaseImponible>';
            if (isset($d["impuesto"]) && $d["impuesto"] != "") {
                foreach ($d["impuesto"] as $i) {
                    $xmlString .= '<Impuesto>
                    <Codigo>' . $i->codigo . '</Codigo>
                    <CodigoTarifa>' . $i->codigoTarifa . '</CodigoTarifa>
                    <Tarifa>' . $i->tarifa . '</Tarifa>
                    <FactorIVA>' . $i->factorIVA . '</FactorIVA>
                    <Monto>' . $i->monto . '</Monto>';
                    if (isset($i->exoneracion) && $i->exoneracion != "") {
                        $xmlString .= '
                        <Exoneracion>
                            <TipoDocumento>' . $i->exoneracion->tipoDocumento . '</TipoDocumento>
                            <NumeroDocumento>' . $i->exoneracion->numeroDocumento . '</NumeroDocumento>
                            <NombreInstitucion>' . $i->exoneracion->nombreInstitucion . '</NombreInstitucion>
                            <FechaEmision>' . $i->exoneracion->fechaEmision . '</FechaEmision>
                            <PorcentajeExoneracion>' . $i->exoneracion->porcentajeCompra . '</PorcentajeExoneracion>
                            <MontoExoneracion>' . $i->exoneracion->montoImpuesto . '</MontoExoneracion>
                        </Exoneracion>';
                    }

                    $xmlString .= '</Impuesto>';
                }
            }


            $xmlString .= '<MontoTotalLinea>' . $d["montoTotalLinea"] . '</MontoTotalLinea>';
            $xmlString .= '</LineaDetalle>';
            $l++;
        }
        $xmlString .= '</DetalleServicio>
        <ResumenFactura>
            <CodigoTipoMoneda>
                <CodigoMoneda>' . $codMoneda . '</CodigoMoneda>
                <TipoCambio>' . $tipoCambio . '</TipoCambio>
            </CodigoTipoMoneda>
            <TotalServGravados>' . $totalServGravados . '</TotalServGravados>
            <TotalServExentos>' . $totalServExentos . '</TotalServExentos>
            <TotalServExonerado>' . $totalServiciosExonerados . '</TotalServExonerado>
            <TotalMercanciasGravadas>' . $totalMercGravadas . '</TotalMercanciasGravadas>
            <TotalMercanciasExentas>' . $totalMercExentas . '</TotalMercanciasExentas>
            <TotalMercExonerada>' . $totalMercanciaExonerada . '</TotalMercExonerada>
            <TotalGravado>' . $totalGravados . '</TotalGravado>
            <TotalExento>' . $totalExentos . '</TotalExento>
            <TotalExonerado>' . $totalExonerado . '</TotalExonerado>
            <TotalVenta>' . $totalVentas . '</TotalVenta>
            <TotalDescuentos>' . $totalDescuentos . '</TotalDescuentos>
            <TotalVentaNeta>' . $totalVentasNeta . '</TotalVentaNeta>
            <TotalImpuesto>' . $totalImp . '</TotalImpuesto>
            <TotalIVADevuelto>' . $totalIVADevuelto . '</TotalIVADevuelto>
            <TotalOtrosCargos>' . $totalOtrosCargos . '</TotalOtrosCargos>
            <TotalComprobante>' . $totalComprobante . '</TotalComprobante>
        </ResumenFactura>
        <InformacionReferencia>
            <TipoDoc>' . $infoRefeTipoDoc . '</TipoDoc>
            <Numero>' . $infoRefeNumero . '</Numero>
            <FechaEmision>' . $infoRefeFechaEmision . '</FechaEmision>
            <Codigo>' . $infoRefeCodigo . '</Codigo>
            <Razon>' . $infoRefeRazon . '</Razon>
        </InformacionReferencia>';
             if ($otros == '' or $otrosType == '') {

        } else {
            $tipos = array("Otros", "OtroTexto", "OtroContenido");
            if (in_array($otrosType, $tipos)) {
                $xmlString .= '
                <Otros>
            <' . $otrosType . '>' . $otros . '</' . $otrosType . '>
            </Otros>';
            } else {

            }
        }
        $xmlString .= '
        </NotaCreditoElectronica>';
        $arrayResp = array(
            "clave" => $clave,
            "xml" => base64_encode($xmlString)
        );
        return $arrayResp;
    }
    
    function genXMLMr($clave, $numeroConsecutivoReceptor, $fechaEmisionDoc, $emisor_num_identif, $receptor_num_identif, $mensaje, $detalleMensaje, $montoTotalImpuesto, $totalFactura) {
        $numeroCedulaEmisor = str_pad($emisor_num_identif, 12, "0", STR_PAD_LEFT);
        $numeroCedulaReceptor = str_pad($receptor_num_identif, 12, "0", STR_PAD_LEFT);

        $xmlString = '<?xml version="1.0" encoding="utf-8"?>
        <MensajeReceptor  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/mensajeReceptor" xsi:schemaLocation="https://cdn.comprobanteselectronicos.go.cr/xml-schemas/v4.3/mensajeReceptor https://tribunet.hacienda.go.cr/docs/esquemas/2017/v4.3/mensajeReceptor.xsd">
        <Clave>' . $clave . '</Clave>
        <NumeroCedulaEmisor>' . $numeroCedulaEmisor . '</NumeroCedulaEmisor>
        <FechaEmisionDoc>' . $fechaEmisionDoc . '</FechaEmisionDoc>
        <Mensaje>' . $mensaje . '</Mensaje>';
        if (!empty($detalleMensaje)) {
            $xmlString .= '<DetalleMensaje>' . $detalleMensaje . '</DetalleMensaje>';
        }
        if (!empty($montoTotalImpuesto)) {
            $xmlString .= '<MontoTotalImpuesto>' . $montoTotalImpuesto . '</MontoTotalImpuesto>';
        }
        $xmlString .= '<TotalFactura>' . $totalFactura . '</TotalFactura>
        <NumeroCedulaReceptor>' . $numeroCedulaReceptor . '</NumeroCedulaReceptor>
        <NumeroConsecutivoReceptor>' . $numeroConsecutivoReceptor . '</NumeroConsecutivoReceptor>';

        $xmlString .= '</MensajeReceptor>';
        $arrayResp = array(
            "clave" => $clave,
            "xml" => base64_encode($xmlString)
        );
        return $arrayResp;
    }
}