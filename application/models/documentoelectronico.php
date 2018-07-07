<?php
Class DocumentoElectronico extends CI_Model {
    
    function guardarFacturaElectronica($tipo, 
                $codigoPais, 
                $cedulaReceptor, 
                $consecutivoFactura, 
                $sucursalFactura, 
                $situacion, 
                $codigoSeguridad, 
                $clave, 
                $consecutivoHacienda, 
                $fechaEmision, 
                $medioPago, 
                $xmlSinFirmar, 
                $xmlFirmado, 
                $costos, 
                $estadoHacienda){
            $data = array(
                "Tipo"=>$tipo,
                "Codigo_Pais"=>$codigoPais,
                "Cedula_Receptor"=>$cedulaReceptor,
                "Consecutivo_Factura"=>$consecutivoFactura,
                "Sucursal_Factura"=>$sucursalFactura,
                "Situacion"=>$situacion,
                "Codigo_Seguridad"=>$codigoSeguridad,
                "Clave"=>$clave,
                "Consecutivo_Hacienda"=>$consecutivoHacienda,
                "Fecha_Emision"=>$fechaEmision,
                "Medio_Pago"=>$medioPago,
                "XML_Sin_Firmar"=>$xmlSinFirmar,
                "XML_Firmado"=>$xmlFirmado,
                "Costos"=>$costos,
                "Estado_Hacienda"=>$estadoHacienda
            );
            $this->db->insert("tb_55_documento_electronico", $data);
        }
    
    public function actualizarEstadoHacienda($clave, $estado){
        $data = array(
                "Estado_Hacienda"=>$estado
            );
        $this->db->where("Clave", $clave);
        $this->db->update("tb_55_documento_electronico", $data);
    }
}
