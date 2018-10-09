<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Model Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/config.html
 */
class CI_Model {
	
	/* Debido al desmadre con desampa, cuando se facturo o hace otra cosa, todos los documentos de desampa se hacen
	con los docs de garotas*/
	public $truequeHabilitado = true;
	public $truequeAplicado = false; 
	public $sucursales_trueque = array(7=>2);
	
	public function esUsadaComoSucursaldeRespaldo($sucursal){
		foreach($this->sucursales_trueque as $key => $content){
			if($content == $sucursal){
				return true;
			}
		}
		return false;
	}
	
	public function getSucursalesTruequeFromSucursalResponde($sucursalResponde){
		$sucursales = array();
		foreach($this->sucursales_trueque as $key => $content){
			if($content == $sucursalResponde){
				array_push($sucursales, $key);
			}
		}
		return $sucursales;
	}

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function __construct()
	{
		log_message('debug', "Model Class Initialized");
	}

	/**
	 * __get
	 *
	 * Allows models to access CI's loaded classes using the same
	 * syntax as controllers.
	 *
	 * @param	string
	 * @access private
	 */
	function __get($key)
	{
		$CI =& get_instance();
		return $CI->$key;
	}
        
        public function fn($price, $decimales = HACIENDA_DECIMALES){
            return number_format($price, $decimales, ".", "");
        }
        
        public function fpad($price, $amount, $placeholder = "0", $typePad = STR_PAD_LEFT){
            return str_pad($price,$amount,$placeholder, $typePad);
        }
        
        public function removeIVA($price){
            $this->load->model('configuracion','',TRUE);
            $confArray = $this->configuracion->getConfiguracionArray();
            return $this->fn($price/(1+(floatval($confArray['iva'])/100)), $confArray['cantidad_decimales']);
        }
        
        public function getIVA(){
            $this->load->model('configuracion','',TRUE);
            $confArray = $this->configuracion->getConfiguracionArray();
            return floatval($confArray['iva']);
        }
        
        function getCondicionVenta($tipoPago){
            /*
            Condiciones de la venta: 
            - 01 Contado
            - 02 Crédito 
            - 03 Consignación
            - 04 Apartado 
            - 05 Arrendamiento con opción de compra 
            - 06 Arrendamiento en función financiera 
            - 99 Otros
             */
            switch ($tipoPago['tipo']) {
                case 'contado':
                case 'tarjeta':
                case 'deposito':
                case 'cheque':
                case 'mixto':
                    return "01";
                case 'credito':
                    return "02";
                case 'apartado':
                    return "04";
            }
        }

        function getMedioPago($tipoPago){
            /*
                Corresponde al medio de pago empleado: 
                - 01 Efectivo
                - 02 Tarjeta
                - 03 Cheque
                - 04 Transferencia - depósito bancario 
                - 05 - Recaudado por terceros
                - 99 Otros
             */
            switch ($tipoPago['tipo']) {
                case 'contado':
                    return "01";
                case 'tarjeta':
                    return "02";
                case 'deposito':
                    return "04";
                case 'cheque':
                    return "03";
                case 'mixto':
                    return "01,02";
                case 'credito':
                    return "99";
                case 'apartado':
                    return "99";
            }
        }
        
        function getTipoIdentificacionCliente($tipo){
            switch($tipo){
                case 'nacional':
                    return "01";
                case 'residencia':
                    return "03";
                case 'juridica':
                    return "02";
                case 'pasaporte':
                    return "04";
            }
        }
        
        function prepararArticulosParaXML($articulos){
            $finalArray = array();
            
            foreach($articulos as $art){
                $artt = array(
                    "cantidad" => $art->Cantidad,
                    "unidadMedida" => $art->UnidadMedida,
                    "detalle" => $art->Detalle,
                    "precioUnitario" => $art->PrecioUnitario,
                    "montoTotal" => $art->MontoTotal,
                    "montoDescuento" => $art->MontoDescuento,
                    "naturalezaDescuento" => $art->NaturalezaDescuento,
                    "subtotal" => $art->Subtotal,
                    "impuesto" =>  json_decode($art->ImpuestoObject),
                    "montoTotalLinea" => $art->MontoTotalLinea
                );
                array_push($finalArray, $artt);
            }
            
            return $finalArray;
        }
        
        function formatearConsecutivo($consecutivo){
            $consecutivo = $consecutivo."";
            $len = strlen($consecutivo);
            for($counter = $len; $counter < 10; $counter++){
                $consecutivo = "0".$consecutivo;
            }
            return $consecutivo;
        }
        
        public function getDetalleLinea($a, $aplicaRetencion = true){
            $linea = array();
            
            // CANTIDAD
            $cantidad = floatval($a->Articulo_Factura_Cantidad);
            $linea["cantidad"] = $this->fn($cantidad, 3);
            
            // UNIDAD DE MEDIDA
            $linea["unidadMedida"] = "Unid";
            
            // DETALLE
            $linea["detalleCompleto"] = $a->Articulo_Factura_Descripcion;
            $linea["detalle"] = substr($a->Articulo_Factura_Descripcion,0,159);
            
            // PRECIO UNITARIO
            $precioUnitarioSinIVA = $this->removeIVA(floatval($a->Articulo_Factura_Precio_Unitario));
            $linea["precioUnitario"] = $this->fn($precioUnitarioSinIVA);
            
            // MONTO TOTAL
            $precioTotalSinIVA = $cantidad*$precioUnitarioSinIVA;
            $linea["montoTotal"] = $this->fn($precioTotalSinIVA);
            
            // DESCUENTO
            $descuentoPrecioSinIva = 0;
            if(floatval($a->Articulo_Factura_Descuento) > 0){
                $descuentoPrecioSinIva = round($precioTotalSinIVA * (floatval($a->Articulo_Factura_Descuento) / 100), 0);
                $linea["montoDescuento"] = $this->fn($descuentoPrecioSinIva);
                $naturalezaDescuento = "Otorgado a cliente por empresa";
                $linea["naturalezaDescuento"] = $naturalezaDescuento;
            }else{
                $linea["montoDescuento"] = $this->fn(0);
                $linea["naturalezaDescuento"] = "Ninguna";
            }
            
             // SUBTOTAL
            $subTotalSinIVA = round($precioTotalSinIVA, 0) - $descuentoPrecioSinIva;
            $linea["subtotal"] = $this->fn(round($subTotalSinIVA, 0));
            
            // IMPUESTOS
            $impuestos = array();
            $iva = $this->getIVA();
            $montoDeImpuesto = $subTotalSinIVA * ($iva / 100);
            if($a->Articulo_Factura_No_Retencion == "0" && $aplicaRetencion){
                $precioFinalUnitarioSinIVA = $this->removeIVA(floatval($a->Articulo_Factura_Precio_Final));
                $precioFinalTotalSinIVA = $cantidad*$precioFinalUnitarioSinIVA;
                $descuentoPrecioFinalSinIva = 0;
                if(floatval($a->Articulo_Factura_Descuento) > 0){
                    $descuentoPrecioFinalSinIva = $precioFinalTotalSinIVA * (floatval($a->Articulo_Factura_Descuento) / 100);
                }
                $subTotalFinalSinIVA = $precioFinalTotalSinIVA - $descuentoPrecioFinalSinIva;
                $montoDeImpuesto = round(($subTotalFinalSinIVA * ($iva / 100)), 0);
            }
            if($a->Articulo_Factura_Exento == 1){ // Es exento
                // POR EL MOMENTO ESTA INFO ESTA AMARRADA, PERO DEBE OBTENERSE DE LA INFO DEL CLIENTE LO CUAL DEBE IMPLEMENTARSE 
                $exoneracion = array(
                    "tipoDocumento" => "01", // Compras Autorizadas
                    "numeroDocumento" => "01",
                    "nombreInstitucion" => "Cliente",
                    "fechaEmision" => date(DATE_ATOM),
                    "montoImpuesto" => "9999999999999.99999",
                    "porcentajeCompra" => 100
                );
                $impuesto["exoneracion"] = $exoneracion;
                $montoDeImpuesto = 0;
            }
            // Se debe cambiar el porcentaje de impuesto, ya que se debe tomar en cuenta la retencion
            $factorIVAFinal = (($montoDeImpuesto) * 100) / $subTotalSinIVA;
            $montoFinalDeImpuesto = $subTotalSinIVA * ($factorIVAFinal / 100);
            $impuesto = array(
                "codigo" => "01", // "Impuesto General sobre las ventas"
                "tarifa" => $this->fpad($this->fn($factorIVAFinal, 2), 5),
                "monto" => $this->fn(round($montoFinalDeImpuesto, 0))
            );
            
            array_push($impuestos, $impuesto);
            $linea["impuesto"] = $impuestos;
            
            // MONTO TOTAL DE LA LINEA
            $linea["montoTotalLinea"] = $this->fn(round($subTotalSinIVA + floatval($impuesto["monto"]), 0));
            
            return $linea;
        }
        
        
        public function getDetalleLineaNotaCredito($a, $aplicaRetencion = true){
            $linea = array();
            
            // CANTIDAD
            $cantidad = floatval($a->Cantidad_Bueno) + floatval($a->Cantidad_Defectuoso);
            $linea["cantidad"] = $this->fn($cantidad, 3);
            
            // UNIDAD DE MEDIDA
            $linea["unidadMedida"] = "Unid";
            
            // DETALLE
            $linea["detalleCompleto"] = $a->Descripcion;
            $linea["detalle"] = substr($a->Descripcion,0,159);
            
            // PRECIO UNITARIO
            $precioUnitarioSinIVA = $this->removeIVA(floatval($a->Precio_Unitario));
            $linea["precioUnitario"] = $this->fn($precioUnitarioSinIVA);
            
            // MONTO TOTAL
            $precioTotalSinIVA = $cantidad*$precioUnitarioSinIVA;
            $linea["montoTotal"] = $this->fn($precioTotalSinIVA);
            
            // DESCUENTO
            $descuentoPrecioSinIva = 0;
//            if(floatval($a->Descuento) > 0){
//                $descuentoPrecioSinIva = $precioTotalSinIVA * (floatval($a->Descuento) / 100);
//                $linea["montoDescuento"] = $this->fn($descuentoPrecioSinIva);
//                $naturalezaDescuento = "Otorgado a cliente por empresa";
//                $linea["naturalezaDescuento"] = $naturalezaDescuento;
//            }else{
                $linea["montoDescuento"] = $this->fn(0);
                $linea["naturalezaDescuento"] = "Ninguna";
//            }
            
             // SUBTOTAL
            $subTotalSinIVA = $precioTotalSinIVA - $descuentoPrecioSinIva;
            $linea["subtotal"] = $this->fn($subTotalSinIVA);
            
            // IMPUESTOS
            $impuestos = array();
            $iva = $this->getIVA();
            $montoDeImpuesto = $subTotalSinIVA * ($iva / 100);
            if($a->No_Retencion == "0" && $aplicaRetencion){
                $precioFinalUnitarioSinIVA = $this->removeIVA(floatval($a->Precio_Final));
                $precioFinalTotalSinIVA = $cantidad*$precioFinalUnitarioSinIVA;
                $descuentoPrecioFinalSinIva = 0;
                if(floatval($a->Descuento) > 0){
                    $descuentoPrecioFinalSinIva = $precioFinalTotalSinIVA * (floatval($a->Descuento) / 100);
                }
                $subTotalFinalSinIVA = $precioFinalTotalSinIVA - $descuentoPrecioFinalSinIva;
                $montoDeImpuesto = ($subTotalFinalSinIVA * ($iva / 100));
            }
            if($a->Exento == 1){ // Es exento
                // POR EL MOMENTO ESTA INFO ESTA AMARRADA, PERO DEBE OBTENERSE DE LA INFO DEL CLIENTE LO CUAL DEBE IMPLEMENTARSE 
                $exoneracion = array(
                    "tipoDocumento" => "01", // Compras Autorizadas
                    "numeroDocumento" => "01",
                    "nombreInstitucion" => "Cliente",
                    "fechaEmision" => date(DATE_ATOM),
                    "montoImpuesto" => "9999999999999.99999",
                    "porcentajeCompra" => 100
                );
                $impuesto["exoneracion"] = $exoneracion;
                $montoDeImpuesto = 0;
            }
            // Se debe cambiar el porcentaje de impuesto, ya que se debe tomar en cuenta la retencion
            $factorIVAFinal = (($montoDeImpuesto) * 100) / $subTotalSinIVA;
            $montoFinalDeImpuesto = $subTotalSinIVA * ($factorIVAFinal / 100);
            $impuesto = array(
                "codigo" => "01", // "Impuesto General sobre las ventas"
                "tarifa" => $this->fpad($this->fn($factorIVAFinal, 2), 5),
                "monto" => $this->fn($montoFinalDeImpuesto)
            );
            
            array_push($impuestos, $impuesto);
            $linea["impuesto"] = $impuestos;
            
            // MONTO TOTAL DE LA LINEA
            $linea["montoTotalLinea"] = $this->fn($subTotalSinIVA + floatval($impuesto["monto"]));
            
            return $linea;
        }
}
// END Model Class

/* End of file Model.php */
/* Location: ./system/core/Model.php */