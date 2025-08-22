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
    public $codigosUnidadDeServivicios = array("Os","Spe","Sp","St");

    public $tiposdepago = array(
        "01" => "Efectivo",
        "02" => "Tarjeta",
        "03" => "Cheque",
        "04" => "Transferencia - depósito bancario",
        "05" => "Recaudado por terceros",
        "06" => "Sinpe Móvil",
        "07" => "Plataforma Digital",
        "99" => "Otros"
    );

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

        public function removeIVA($price, $decimales = 5){
            $this->load->model('configuracion','',TRUE);
            $confArray = $this->configuracion->getConfiguracionArray();
            $iva = (string) $confArray['iva']; // p.ej 13
            if (function_exists('bcdiv')) {
                $scale = $decimales + 4; // extra precision internal
                $factor = bcadd('1', bcdiv($iva, '100', $scale), $scale);   // 1 + iva/100
                $resultado = bcdiv((string)$price, $factor, $decimales);
                return (float)$resultado;
            }
            // Fallback float
            $factor = 1 + (float)$iva / 100;
            return round($price / $factor, $decimales);
        }

        public function getIVA($decimales = 5, $asFloat = true){
            $this->load->model('configuracion','',TRUE);
            $confArray = $this->configuracion->getConfiguracionArray();
            $iva = isset($confArray['iva']) ? (string)$confArray['iva'] : '0';
            if (function_exists('bcadd')) {
                $ivaFormateado = bcadd($iva, '0', $decimales); // normaliza y fija escala
            } else {
                $ivaFormateado = number_format((float)$iva, $decimales, '.', '');
            }
            return $asFloat ? (float)$ivaFormateado : $ivaFormateado;
        }

        private function getSessionMetadata(){
            if(!isset($_SESSION)){session_start();}
            return isset($_SESSION) ? $_SESSION : false;
        }

        public function getSucursalDeEstaSesion(){
            $data = $this->getSessionMetadata();
            if($data){
                return isset($data["usuario"]["Sucursal_Codigo"]) ? $data["usuario"]["Sucursal_Codigo"] : false;
            }else{
                return false;
            }
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
                case 'sinpe_movil':
                case 'plataforma_digital':
                    return "01";
                case 'credito':
                    return "02";
                case 'apartado':
                    return "04";
            }
        }

        function getMedioPago($tipoPago, $montoTotalFactura, $pagoMixtoObject){
            /*
                Corresponde al medio de pago empleado:
                - 01 Efectivo
                - 02 Tarjeta
                - 03 Cheque
                - 04 Transferencia - depósito bancario
                - 05 - Recaudado por terceros
                - 99 Otros
             */
            $totalFormateado = $this->fn($montoTotalFactura);            
            switch ($tipoPago['tipo']) {
                case 'contado':
                    return array(array("tipo" => '01', "total" => $totalFormateado, "otros" => ''));
                case 'tarjeta':
                    return array(array("tipo" => '02', "total" => $totalFormateado, "otros" => ''));
                case 'deposito':
                    return array(array("tipo" => '04', "total" => $totalFormateado, "otros" => ''));
                case 'cheque':
                    return array(array("tipo" => '03', "total" => $totalFormateado, "otros" => ''));
                case 'mixto':
                    $totalEfectivoEnMixto = $montoTotalFactura - $pagoMixtoObject->Mixto_Cantidad_Paga;
                    $codigoDePagoMixto = $pagoMixtoObject->Tipo_Pago == 'sinpe_movil' ? '06' : '01';
                    return array(
                        array("tipo" => $codigoDePagoMixto, "total" => $this->fn($totalEfectivoEnMixto), "otros" => ''),
                        array("tipo" => '02', "total" => $this->fn($pagoMixtoObject->Mixto_Cantidad_Paga), "otros" => '')
                    );
                case 'credito':
                    return array(array("tipo" => '99', "otros" => 'Credito', "total" => $totalFormateado));
                case 'apartado':
                    return array(array("tipo" => '99', "otros" => 'Apartado', "total" => $totalFormateado));
                case 'sinpe_movil':
                    return array(array("tipo" => '06', "total" => $totalFormateado, "otros" => ''));
                case 'plataforma_digital':
                    return array(array("tipo" => '06', "total" => $totalFormateado, "otros" => ''));
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
                $impuesto = json_decode($art->ImpuestoObject);
                $impuesto[0]->monto = $this->fn($impuesto[0]->monto);
                $impuesto[0]->tarifa = $this->fn($impuesto[0]->tarifa, 2);
                if(!property_exists($impuesto[0], 'factorIVA') && property_exists($impuesto[0], 'tarifa')){
                    $impuesto[0]->factorIVA = $impuesto[0]->factor;
                }
                if(!property_exists($impuesto[0], 'codigoTarifa')){
                    $impuesto[0]->codigoTarifa = "08";
                }
                $impuesto[0]->factorIVA = $this->fn($impuesto[0]->factorIVA, 2);
                $artt = array(
                    "cantidad" => $art->Cantidad,
                    "unidadMedida" => $art->UnidadMedida,
                    "detalle" => $art->Detalle,
                    "precioUnitario" => $this->fn($art->PrecioUnitario),
                    "montoTotal" => $this->fn($art->MontoTotal),
                    "montoDescuento" => $this->fn($art->MontoDescuento),
                    "tipoDescuento" => $art->TipoDescuento,
                    "naturalezaDescuento" => $art->NaturalezaDescuento,
                    "subtotal" => $this->fn($art->Subtotal),
                    "impuesto" =>  $impuesto,
                    "montoTotalLinea" => $this->fn($art->MontoTotalLinea),
                    "codigo" => $art->Codigo,
                    "codigoCabys" => $art->CodigoCabys,
                    "tipoCodigo" => $art->TipoCodigo,
                    "baseImponible" => $this->fn($art->BaseImponible)
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

        public function getDetalleLinea($a, $aplicaRetencion = false){
            $aplicaRetencion = false; // se mantiene el fix existente
            $linea = array();

            $useBc = function_exists('bcadd');
            $scaleCalc = 10;          // precisión interna
            $scaleOut  = 5;           // para montos (coincidir con HACIENDA_DECIMALES si aplica)

            // Helpers BCMath
            $mul = function($a,$b) use($useBc,$scaleCalc){ return $useBc ? bcmul((string)$a,(string)$b,$scaleCalc) : ( (float)$a * (float)$b ); };
            $add = function($a,$b) use($useBc,$scaleCalc){ return $useBc ? bcadd((string)$a,(string)$b,$scaleCalc) : ( (float)$a + (float)$b ); };
            $sub = function($a,$b) use($useBc,$scaleCalc){ return $useBc ? bcsub((string)$a,(string)$b,$scaleCalc) : ( (float)$a - (float)$b ); };
            $div = function($a,$b) use($useBc,$scaleCalc){ 
                if($b == 0 || $b === '0') return 0;
                return $useBc ? bcdiv((string)$a,(string)$b,$scaleCalc) : ( (float)$a / (float)$b ); 
            };

            // CANTIDAD
            $cantidad = (float)$a->Articulo_Factura_Cantidad;
            $linea["cantidad"] = $this->fn($cantidad, 3);

            // CODIGOS
            $linea["codigo"] = $a->Articulo_Factura_Codigo;
            $linea["tipoCodigo"] = $a->TipoCodigo;
            $linea["codigoCabys"] = $a->Codigo_Cabys;

            // UNIDAD
            $linea["unidadMedida"] = $a->UnidadMedida;

            // DETALLE
            $linea["detalleCompleto"] = $a->Articulo_Factura_Descripcion;
            $linea["detalle"] = substr($a->Articulo_Factura_Descripcion,0,159);

            // PRECIO UNITARIO SIN IVA (removeIVA ya usa BCMath si disponible)
            $precioUnitarioSinIVANum = $this->removeIVA((float)$a->Articulo_Factura_Precio_Unitario);
            $precioUnitarioSinIVA = $this->fn($precioUnitarioSinIVANum);
            $linea["precioUnitario"] = $precioUnitarioSinIVA;

            // MONTO TOTAL SIN IVA
            $precioTotalSinIVANum = $mul($cantidad, $precioUnitarioSinIVANum);
            $linea["montoTotal"] = $useBc ? (float)bcadd($precioTotalSinIVANum,'0', $scaleOut) : $precioTotalSinIVANum;

            // DESCUENTO
            $descuentoPrecioSinIvaNum = 0;
            $porcDesc = (float)$a->Articulo_Factura_Descuento;
            if($porcDesc > 0){
                $descuentoPrecioSinIvaNum = $mul($precioTotalSinIVANum, $div($porcDesc, 100));
                $linea["montoDescuento"] = $this->fn($descuentoPrecioSinIvaNum);
                $linea["tipoDescuento"] = $a->TipoDescuento;
                $linea["naturalezaDescuento"] = "Otorgado a cliente por empresa";
            }else{
                $linea["montoDescuento"] = 0;
                $linea["tipoDescuento"] = '07';
                $linea["naturalezaDescuento"] = "Ninguna";
            }

            // SUBTOTAL / BASE IMPONIBLE
            $subTotalSinIVANum = $sub($precioTotalSinIVANum, $descuentoPrecioSinIvaNum);
            $linea["subtotal"] = $useBc ? (float)bcadd($subTotalSinIVANum,'0',$scaleOut) : $subTotalSinIVANum;
            $linea["base_imponible"] = $linea["subtotal"];

            // IMPUESTOS
            $impuestos = array();
            $iva = $this->getIVA(); // porcentaje (float)
            $ivaFactor = $div($iva, 100);

            $montoDeImpuestoNum = $mul($subTotalSinIVANum, $ivaFactor);
            $linea["iva"] = $useBc ? (float)bcadd($montoDeImpuestoNum,'0',$scaleOut) : $montoDeImpuestoNum;
            $linea["retencion"] = 0;

            if($a->Articulo_Factura_No_Retencion == "0" && $aplicaRetencion){
                $precioFinalUnitarioSinIVANum = $this->removeIVA((float)$a->Articulo_Factura_Precio_Final);
                $precioFinalTotalSinIVANum = $mul($cantidad, $precioFinalUnitarioSinIVANum);
                $montoDeImpuestoNum = $mul($precioFinalTotalSinIVANum, $ivaFactor);
                $linea["retencion"] = ($useBc ? (float)bcsub($montoDeImpuestoNum, $linea["iva"], $scaleOut) : $montoDeImpuestoNum - $linea["iva"]);
            }

            if($a->Articulo_Factura_Exento == 1){
                $exoneracion = array(
                    "tipoDocumento" => "01",
                    "numeroDocumento" => "01",
                    "nombreInstitucion" => "Cliente",
                    "fechaEmision" => date(DATE_ATOM),
                    "montoImpuesto" => "9999999999999.99999",
                    "porcentajeCompra" => 100
                );
                // se adicionará luego al objeto impuesto
                $montoDeImpuestoNum = 0;
                $linea["iva"] = 0;
            }

            // factor IVA final (considerando retención)
            $factorIVAFinalNum = 0;
            if( ($useBc ? bccomp($subTotalSinIVANum,'0',$scaleCalc) : $subTotalSinIVANum > 0) ){
                $factorIVAFinalNum = $mul( $div($montoDeImpuestoNum, $subTotalSinIVANum), 100 );
            }

            $montoFinalDeImpuestoNum = $mul($subTotalSinIVANum, $div($factorIVAFinalNum, 100));

            $impuesto = array(
                "codigo" => ($a->Articulo_Factura_No_Retencion == "0" && $aplicaRetencion) ? "07" : "01",
                "codigoTarifa" => $a->Articulo_Factura_Exento == 1 ? "01" : "08",
                "tarifa" => $useBc ? (float)bcadd($factorIVAFinalNum,'0',2) : (float)round($factorIVAFinalNum,2),
                "factorIVA" => $useBc ? (float)bcadd($factorIVAFinalNum,'0',2) : (float)round($factorIVAFinalNum,2),
                "monto" => $useBc ? (float)bcadd($montoFinalDeImpuestoNum,'0',$scaleOut) : $montoFinalDeImpuestoNum
            );

            if($a->Articulo_Factura_Exento == 1){
                $impuesto["exoneracion"] = $exoneracion;
            }

            $impuestos[] = $impuesto;
            $linea["impuesto"] = $impuestos;

            // MONTO TOTAL LINEA
            $linea["montoTotalLinea"] = $useBc
                ? (float)bcadd($subTotalSinIVANum, (string)$impuesto["monto"], $scaleOut)
                : $subTotalSinIVANum + (float)$impuesto["monto"];

            return $linea;
        }


        public function getDetalleLineaNotaCredito($a, $aplicaRetencion = true){
            $linea = array();

            $useBc     = function_exists('bcadd');
            $scaleCalc = 10; // precisión interna
            $scaleOut  = 5;  // decimales para montos

            // Helpers BCMath
            $mul = function($x,$y) use($useBc,$scaleCalc){ return $useBc ? bcmul((string)$x,(string)$y,$scaleCalc) : ((float)$x * (float)$y); };
            $add = function($x,$y) use($useBc,$scaleCalc){ return $useBc ? bcadd((string)$x,(string)$y,$scaleCalc) : ((float)$x + (float)$y); };
            $sub = function($x,$y) use($useBc,$scaleCalc){ return $useBc ? bcsub((string)$x,(string)$y,$scaleCalc) : ((float)$x - (float)$y); };
            $div = function($x,$y) use($useBc,$scaleCalc){
                if($y == 0 || $y === '0') return 0;
                return $useBc ? bcdiv((string)$x,(string)$y,$scaleCalc) : ((float)$x / (float)$y);
            };

            // CANTIDAD (Bueno + Defectuoso)
            $cantidad = (float)$a->Cantidad_Bueno + (float)$a->Cantidad_Defectuoso;
            $linea["cantidad"] = $this->fn($cantidad, 3);

            // CÓDIGOS
            $linea["codigo"] = $a->Codigo;
            $linea["tipoCodigo"] = $a->TipoCodigo;
            $linea["codigoCabys"] = $a->CodigoCabys;

            // UNIDAD
            $linea["unidadMedida"] = "Unid";

            // DETALLE
            $linea["detalleCompleto"] = $a->Descripcion;
            $linea["detalle"] = substr($a->Descripcion,0,159);

            // PRECIO UNITARIO (reconstruir quitando descuento)
            $precioUnitConDesc = (float)$a->Precio_Unitario;
            $porcDesc = (float)$a->Descuento;
            if($porcDesc != 0 && $porcDesc != 100){
                // Precio_Unitario viene ya con descuento aplicado => revertimos
                $factorDesc = 1 - ($porcDesc/100);
                if($factorDesc != 0){
                    $precioUnitConDesc = $useBc
                        ? $div($precioUnitConDesc, $factorDesc)
                        : $precioUnitConDesc / $factorDesc;
                }
            }
            $precioUnitarioSinIVANum = $this->removeIVA($precioUnitConDesc);
            $linea["precioUnitario"] = $this->fn($precioUnitarioSinIVANum);

            // MONTO TOTAL SIN IVA
            $precioTotalSinIVANum = $mul($cantidad, $precioUnitarioSinIVANum);
            $linea["montoTotal"] = $useBc ? (float)bcadd($precioTotalSinIVANum,'0',$scaleOut) : (float)$precioTotalSinIVANum;

            // DESCUENTO
            $descuentoPrecioSinIvaNum = 0;
            if($porcDesc > 0){
                $descuentoPrecioSinIvaNum = $mul($precioTotalSinIVANum, $div($porcDesc, 100));
                $linea["montoDescuento"] = $this->fn($descuentoPrecioSinIvaNum);
                $linea["naturalezaDescuento"] = "Otorgado a cliente por empresa";
                $linea["codigoDescuento"] = $a->TipoDescuento;
            }else{
                $linea["montoDescuento"] = 0;
                $linea["naturalezaDescuento"] = "Ninguna";
                $linea["codigoDescuento"] = defined('CODIGO_DESCUENTO_DEFECTO') ? CODIGO_DESCUENTO_DEFECTO : '00';
            }

            // SUBTOTAL / BASE IMPONIBLE
            $subTotalSinIVANum = $sub($precioTotalSinIVANum, $descuentoPrecioSinIvaNum);
            $linea["subtotal"] = $useBc ? (float)bcadd($subTotalSinIVANum,'0',$scaleOut) : (float)$subTotalSinIVANum;
            $linea["base_imponible"] = $linea["subtotal"];

            // IMPUESTOS
            $impuestos = array();
            $iva = $this->getIVA(); // porcentaje
            $ivaFactor = $div($iva, 100);

            $montoDeImpuestoNum = $mul($subTotalSinIVANum, $ivaFactor);
            $linea["iva"] = $useBc ? (float)bcadd($montoDeImpuestoNum,'0',$scaleOut) : (float)$montoDeImpuestoNum;
            $linea["retencion"] = 0;

            if($a->No_Retencion == "0" && $aplicaRetencion){
                $precioFinalUnitarioSinIVANum = $this->removeIVA((float)$a->Precio_Final);
                $precioFinalTotalSinIVANum = $mul($cantidad, $precioFinalUnitarioSinIVANum);
                $montoImpuestoRetNum = $mul($precioFinalTotalSinIVANum, $ivaFactor);
                $linea["retencion"] = $useBc
                    ? (float)bcsub($montoImpuestoRetNum, $linea["iva"], $scaleOut)
                    : ((float)$montoImpuestoRetNum - $linea["iva"]);
                $montoDeImpuestoNum = $montoImpuestoRetNum; // actualizar para factor final
            }

            if($a->Exento == 1){
                $exoneracion = array(
                    "tipoDocumento" => "01",
                    "numeroDocumento" => "01",
                    "nombreInstitucion" => "Cliente",
                    "fechaEmision" => date(DATE_ATOM),
                    "montoImpuesto" => "9999999999999.99999",
                    "porcentajeCompra" => 100
                );
                $montoDeImpuestoNum = 0;
                $linea["iva"] = 0;
                $linea["retencion"] = 0;
            }

            // Factor IVA final
            $factorIVAFinalNum = 0;
            $subComp = ($useBc ? bccomp($subTotalSinIVANum,'0',$scaleCalc) : ($subTotalSinIVANum > 0));
            if($subComp > 0){
                $factorIVAFinalNum = $mul( $div($montoDeImpuestoNum, $subTotalSinIVANum), 100 );
            }

            // Monto final impuesto
            $montoFinalDeImpuestoNum = $mul($subTotalSinIVANum, $div($factorIVAFinalNum, 100));

            $impuesto = array(
                "codigo" => ($a->No_Retencion == "0" && $aplicaRetencion) ? "07" : "01",
                "codigoTarifa" => $a->Exento == 1 ? "01" : "08",
                "tarifa" => $this->fpad($this->fn($useBc ? (float)bcadd($factorIVAFinalNum,'0',2) : round($factorIVAFinalNum,2), 2), 5),
                "factorIVA" => $this->fpad($this->fn($useBc ? (float)bcadd($factorIVAFinalNum,'0',2) : round($factorIVAFinalNum,2), 2), 5),
                "monto" => $useBc ? (float)bcadd($montoFinalDeImpuestoNum,'0',$scaleOut) : (float)$montoFinalDeImpuestoNum
            );
            if($a->Exento == 1){
                $impuesto["exoneracion"] = $exoneracion;
            }

            $impuestos[] = $impuesto;
            $linea["impuesto"] = $impuestos;

            // MONTO TOTAL DE LA LINEA
            $linea["montoTotalLinea"] = $useBc
                ? (float)bcadd($subTotalSinIVANum, (string)$impuesto["monto"], $scaleOut)
                : ((float)$subTotalSinIVANum + (float)$impuesto["monto"]);

            return $linea;
        }

        public function getDetalleLineaProforma($a, $aplicaRetencion = true){
            $linea = array();

            // CANTIDAD
            $cantidad = floatval($a->Articulo_Proforma_Cantidad);
            $linea["cantidad"] = $this->fn($cantidad, 3);


            // PRECIO UNITARIO
            $precioUnitarioSinIVA = $this->removeIVA(floatval($a->Articulo_Proforma_Precio_Unitario));
            $linea["precioUnitario"] = $this->fn($precioUnitarioSinIVA);

            // MONTO TOTAL
            $precioTotalSinIVA = $cantidad*$precioUnitarioSinIVA;
            $linea["montoTotal"] = $this->fn($precioTotalSinIVA);

            // DESCUENTO
            $descuentoPrecioSinIva = 0;
            if(floatval($a->Articulo_Proforma_Descuento) > 0){
                $descuentoPrecioSinIva = round($precioTotalSinIVA * (floatval($a->Articulo_Proforma_Descuento) / 100), 0);
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
            $linea["iva"] = $subTotalSinIVA * ($iva / 100);
            $linea["retencion"] = 0;
            if($a->Articulo_Proforma_No_Retencion == "0" && $aplicaRetencion){
                $precioFinalUnitarioSinIVA = $this->removeIVA(floatval($a->Articulo_Proforma_Precio_Final));
                $precioFinalTotalSinIVA = $cantidad*$precioFinalUnitarioSinIVA;
                $montoDeImpuesto = round(($precioFinalTotalSinIVA * ($iva / 100)), 0);
                $linea["retencion"] = $montoDeImpuesto - $linea["iva"];
            }
            if($a->Articulo_Proforma_Exento == 1){ // Es exento
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
                $linea["iva"] = 0;
            }
            // Se debe cambiar el porcentaje de impuesto, ya que se debe tomar en cuenta la retencion
            $factorIVAFinal = 0;
            if($subTotalSinIVA > 0){
                $factorIVAFinal = (($montoDeImpuesto) * 100) / $subTotalSinIVA;
            }
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


        public function storeFile($name, $type, $file = null, $stream = null, $date = null){
            $finalPath = $this->getFinalPath($type, $date);

            if (!file_exists($finalPath)) {
                $oldmask = umask(0);
                mkdir($finalPath, 0777, true);
                umask($oldmask);
            }
            // Si es un archivo lo movemos, pero si no lo creamos
            if($file == null){
                file_put_contents($finalPath.$name, $stream);                
            }else{
                rename($file, $finalPath.$name);
            }

            chmod($finalPath.$name, 0770);
        }

        public function agregarImpuestoADesgloseDeImpuestos(&$desgloseImpuestos, $impuestoArticulo){
            $key = $impuestoArticulo["codigo"]."_".$impuestoArticulo["codigoTarifa"];
            if(!isset($desgloseImpuestos[$key])){
                $desgloseImpuestos[$key] = array("codigo" => $impuestoArticulo["codigo"], "tarifaCodigo" => $impuestoArticulo["codigoTarifa"], "monto" => 0);
            }
            $desgloseImpuestos[$key]["monto"] += $impuestoArticulo["monto"];
        }

        public function getFinalPath($type, $date = null){
            $finalPath = PATH_DOCUMENTOS_ELECTRONICOS;

            $date = $date == null ? time() : strtotime($date);

            switch($type){
                case "fe":
                    $finalPath .= "factura_electronica/".date("Y_m_d", $date)."/";
                break;
                case "nc":
                    $finalPath .= "nota_credito_electronica/".date("Y_m_d", $date)."/";
                break;
                case "nd":
                    $finalPath .= "nota_debito_electronica/".date("Y_m_d", $date)."/";
                break;
                case "mr":
                    $finalPath .= "mensaje_receptor_electronica/".date("Y_m_d", $date)."/";
                break;
                case "cer":
                    $finalPath .= "certificados/";
                break;
                case "fec":
                    $finalPath .= "factura_electronica_compra/".date("Y_m_d", $date)."/";
                break;
                case "rep":
                    $finalPath .= "recibo_electronico_pago/".date("Y_m_d", $date)."/";
                break;
                case "logo":
                    $finalPath = CARPETA_IMAGENES_LOGO;
                break;
            }

            return $finalPath;
        }

        public function getFinalPathWeb($type, $da){
            $finalPath = base_url('').PATH_DOCUMENTOS_ELECTRONICOS_WEB;

            $date = strtotime($da);

            switch($type){
                case "fe":
                    $finalPath .= "factura_electronica/".date("Y_m_d", $date)."/";
                break;
                case "nc":
                    $finalPath .= "nota_credito_electronica/".date("Y_m_d", $date)."/";
                break;
                case "nd":
                    $finalPath .= "nota_debito_electronica/".date("Y_m_d", $date)."/";
                break;
                case "mr":
                    $finalPath .= "mensaje_receptor_electronica/".date("Y_m_d", $date)."/";
                break;
                case "cer":
                    $finalPath .= "certificados/";
                break;
                case "fec":
                    $finalPath .= "factura_electronica_compra/".date("Y_m_d", $date)."/";
                break;
                case "rep":
                    $finalPath .= "recibo_electronico_pago/".date("Y_m_d", $date)."/";
                break;
            }

            return $finalPath;
        }

}
// END Model Class

/* End of file Model.php */
/* Location: ./system/core/Model.php */
