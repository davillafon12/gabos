<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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
 * CodeIgniter Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/controllers.html
 */
class CI_Controller
{

    private static $instance;

    public $javascriptCacheVersion = 36;

    public $tiposIdentificacion = array(
        "01" => "Física",
        "02" => "Jurídica",
        "03" => "DIMEX",
        "04" => "NITE"
    );

    public $tipoDocumentosExoneracion = array(
        "01" => "Compras Autorizadas",
        "02" => "Ventas exentas a diplomáticos",
        "03" => "Orden de compra (instituciones publicas y otros organismos)",
        "04" => "Exenciones Direccion General de Hacienda",
        "05" => "Zonas Francas",
        "99" => "Otros"
    );

    public $tipoMensajesMensajeReceptor = array(
        "CCE" => "Aceptar",
        "CPCE" => "Parcial",
        "RCE" => "Rechazar"
    );

    public $condicionesdeventa = array(
        "01" => "Contado",
        "02" => "Crédito",
        "03" => "Consignación",
        "04" => "Apartado",
        "05" => "Arrendamiento con opción de compra",
        "06" => "Arrendamiento en función financiera",
        "07" => "Cobro a favor de un tercero",
        "08" => "Servicios prestados al Estado a crédito",
        "09" => "Pago del servicios prestado al Estado",
        "99" => "Otros"
    );

    public $tiposdepago = array(
        "01" => "Efectivo",
        "02" => "Tarjeta",
        "03" => "Cheque",
        "04" => "Transferencia - depósito bancario",
        "05" => "Recaudado por terceros",
        "99" => "Otros"
    );

    public $userdata_nombre;
    public $userdata_sucursal;
    public $userdata_wrap;

    /**
     * Constructor
     */
    public function __construct()
    {
        self::$instance = &$this;

        // Assign all the class objects that were instantiated by the
        // bootstrap file (CodeIgniter.php) to local class variables
        // so that CI can run as one big super object.
        foreach (is_loaded() as $var => $class) {
            $this->$var = &load_class($class);
        }

        $this->load = &load_class('Loader', 'core');

        $this->load->initialize();

        log_message('debug', "Controller Class Initialized");

        // Evitamos que entre en el login
        if($_SERVER['REQUEST_URI'] !== '/login' &&
                $_SERVER['REQUEST_URI'] !== '/verifylogin'){
            include PATH_USER_DATA; //Esto es para traer la informacion de la sesion
            $this->userdata_nombre = $data['Usuario_Codigo'];
            $this->userdata_sucursal = $data['Sucursal_Codigo'];
            $this->userdata_wrap = $data;
        }
    }

    public static function &get_instance()
    {
        return self::$instance;
    }

    public function fn($price, $decimales = HACIENDA_DECIMALES)
    {
        return number_format($price, $decimales, ".", "");
    }

    public function fpad($price, $amount, $placeholder = "0", $typePad = STR_PAD_LEFT)
    {
        return str_pad($price, $amount, $placeholder, $typePad);
    }

    public function removeIVA($price)
    {
        $this->load->model('configuracion', '', TRUE);
        $confArray = $this->configuracion->getConfiguracionArray();
        return $this->fn($price / (1 + (floatval($confArray['iva']) / 100)), $confArray['cantidad_decimales']);
    }

    public function getIVA()
    {
        $this->load->model('configuracion', '', TRUE);
        $confArray = $this->configuracion->getConfiguracionArray();
        return floatval($confArray['iva']);
    }

    public function getDetalleLinea($a)
    {
        $linea = array();

        // CANTIDAD
        $cantidad = floatval($a->Articulo_Factura_Cantidad);
        $linea["cantidad"] = $this->fn($cantidad, 3);

        // UNIDAD DE MEDIDA
        $linea["unidadMedida"] = "Unid";

        // DETALLE
        $linea["detalleCompleto"] = $a->Articulo_Factura_Descripcion;
        $linea["detalle"] = substr($a->Articulo_Factura_Descripcion, 0, 159);

        // PRECIO UNITARIO
        $precioUnitarioSinIVA = $this->removeIVA(floatval($a->Articulo_Factura_Precio_Unitario));
        $linea["precioUnitario"] = $this->fn($precioUnitarioSinIVA);

        // MONTO TOTAL
        $precioTotalSinIVA = $cantidad * $precioUnitarioSinIVA;
        $linea["montoTotal"] = $this->fn($precioTotalSinIVA);

        // DESCUENTO
        $descuentoPrecioSinIva = 0;
        if (floatval($a->Articulo_Factura_Descuento) > 0) {
            $descuentoPrecioSinIva = $precioTotalSinIVA * (floatval($a->Articulo_Factura_Descuento) / 100);
            $linea["montoDescuento"] = $this->fn($descuentoPrecioSinIva);
            $naturalezaDescuento = "Otorgado a cliente por empresa";
            $linea["naturalezaDescuento"] = $naturalezaDescuento;
        } else {
            $linea["montoDescuento"] = $this->fn(0);
            $linea["naturalezaDescuento"] = "Ninguna";
        }

        // SUBTOTAL
        $subTotalSinIVA = $precioTotalSinIVA - $descuentoPrecioSinIva;
        $linea["subtotal"] = $this->fn($subTotalSinIVA);

        // IMPUESTOS
        $impuestos = array();
        $iva = $this->getIVA();
        $montoDeImpuesto = $subTotalSinIVA * ($iva / 100);
        $montoDeImpuestoRetencion = 0;
        if ($a->Articulo_Factura_No_Retencion == "0") {
            $precioFinalUnitarioSinIVA = $this->removeIVA(floatval($a->Articulo_Factura_Precio_Final));
            $precioFinalTotalSinIVA = $cantidad * $precioFinalUnitarioSinIVA;
            $descuentoPrecioFinalSinIva = 0;
            if (floatval($a->Articulo_Factura_Descuento) > 0) {
                $descuentoPrecioFinalSinIva = $precioFinalTotalSinIVA * (floatval($a->Articulo_Factura_Descuento) / 100);
            }
            $subTotalFinalSinIVA = $precioFinalTotalSinIVA - $descuentoPrecioFinalSinIva;
            $montoDeImpuestoRetencion = ($subTotalFinalSinIVA * ($iva / 100)) - $montoDeImpuesto;
        }
        if ($a->Articulo_Factura_Exento == 1) { // Es exento
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
        $factorIVAFinal = (($montoDeImpuesto + $montoDeImpuestoRetencion) * 100) / $subTotalSinIVA;
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

    public function convertirProductosDeFacturaANotaCredito($articulos)
    {
        $newArticulos = array();
        foreach ($articulos as $art) {
            $a = new stdClass();
            $a->c = $art->Articulo_Factura_Codigo;
            $a->d = 0;
            $a->b = $art->Articulo_Factura_Cantidad;
            if ($art->Articulo_Factura_Codigo == "00") {
                $a->p = $art->Articulo_Factura_Precio_Unitario;
                $a->ds = $art->Articulo_Factura_Descripcion;
            }
            array_push($newArticulos, $a);
        }
        return $newArticulos;
    }

    public function checkPermission($permissionName){
        if(trim($permissionName) == "")
            redirect('accesoDenegado', 'location');

        if($permissionName == null)
            redirect('accesoDenegado', 'location');

        $this->load->model('user', '', TRUE);

        $permisos = $this->user->get_permisos($this->userdata_nombre, $this->userdata_sucursal);

		if(!$permisos[$permissionName])
			redirect('accesoDenegado', 'location');

    }

    public function getDefaultResponse(){
        $r = new stdClass();
        $r->code = 1;
        $r->msg = "Solicitud no procesada";
        $r->data = array();
        return $r;
    }
}
// END Controller class

/* End of file Controller.php */
/* Location: ./system/core/Controller.php */
