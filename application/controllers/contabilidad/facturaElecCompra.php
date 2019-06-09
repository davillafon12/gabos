<?php
use ___PHPSTORM_HELPERS\object;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class facturaElecCompra extends CI_Controller {

    function __construct(){
            parent::__construct(); 
            $this->load->model('user','',TRUE);
            $this->load->model('cliente','',TRUE);
            $this->load->model('contabilidad','',TRUE);
            $this->load->model('empresa','',TRUE);
            $this->load->model('ubicacion','',TRUE);
            $this->load->model('catalogo','',TRUE);
            $this->load->model('factura','',TRUE);
            $this->load->model('configuracion','',TRUE);
    }
    
    function index(){
        include PATH_USER_DATA; //Esto es para traer la informacion de la sesion

        $permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

        if($permisos['crear_factura_elec_compra']){
            $data['javascript_cache_version'] = $this->javascriptCacheVersion;
            $data['provincias'] = $this->ubicacion->getProvincias();
            $data['condicionesventa'] = $this->condicionesdeventa;
            $data['tiposdepago'] = $this->tiposdepago;

            $conf_array = $this->configuracion->getConfiguracionArray();
		    $data['c_array'] = $conf_array;
            
            $tiposCodigos = $this->catalogo->getTipoCodigoProductoServicio();
            $data['tipos_codigo'] = $tiposCodigos;
            $unidadesMedida = $this->catalogo->getUnidadesDeMedida();
            $data['unidades_medida'] = $unidadesMedida;
            $tiposImpuesto = $this->catalogo->getTipoImpuestos();
            $data['tipos_impuesto'] = $tiposImpuesto;
            $tiposTarifa = $this->catalogo->getTipoTarifas();
            $data['tipos_tarifa'] = $tiposTarifa;
            $data['tipo_identificacion'] = $this->tiposIdentificacion;
            $this->load->view('contabilidad/crear_factura_elec_compra_view', $data);	
        }else{
           redirect('accesoDenegado', 'location');
        }
    }

    function crearFactura(){
        $r["status"] = 0;
        $r["msg"] = "No se pudo realizar la solicitud";
        include PATH_USER_DATA; //Esto es para traer la informacion de la sesion

        $permisos = $this->user->get_permisos($data['Usuario_Codigo'], $data['Sucursal_Codigo']);

        if($permisos['crear_factura_elec_compra']){
            $nombre = trim(@$_POST["nombreEmisor"]);
            $tipoIdentificacion = trim(@$_POST["tipoIdentificacionEmisor"]);
            $identificacion = trim(@$_POST["identificacionEmisor"]);
            $email = trim(@$_POST["emailEmisor"]);
            $otrasSennas = trim(@$_POST["otrasSennasEmisor"]);
            $provincia = trim(@$_POST["provinciaEmisor"]);
            $canton = trim(@$_POST["cantonEmisor"]);
            $distrito = trim(@$_POST["distritoEmisor"]);
            $codigoActividad = trim(@$_POST["codigoActividadEmisor"]);
            $fechaFactura = trim(@$_POST["fechaFactura"]);
            $condicionVenta = trim(@$_POST["condicionVenta"]);
            $plazoCredito = trim(@$_POST["plazoCredito"]);
            $tipoPago = trim(@$_POST["tipoPago"]);
            $detalles = json_decode(trim(@$_POST["detalles"]), true);
   
            if($nombre != ""){
                if($identificacion != ""){
                    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                        if($otrasSennas != ""){
                            if($codigoActividad != ""){
                                if(DateTime::createFromFormat('d-m-Y H:i:s', $fechaFactura) !== false){
                                    if(sizeof($detalles) > 0){
                                        $checkDetalles = $this->revisarDetalles($detalles);
                                        if($checkDetalles === true){
                                            $validacionCredito = $this->validarPlazoCredito($condicionVenta, $plazoCredito);
                                            if($validacionCredito === true){
                                                $emisor = array(
                                                    "nombre" => $nombre,
                                                    "tipoIdentificacion" => $tipoIdentificacion,
                                                    "identificacion" => $identificacion,
                                                    "provincia" => $provincia,
                                                    "canton" => $canton,
                                                    "distrito" => $distrito,
                                                    "direccion" => $otrasSennas,
                                                    "email" => $email,
                                                    "codigoActividad" => $codigoActividad
                                                );

                                                $receptor = $this->empresa->getEmpresa($data['Sucursal_Codigo'])[0];

                                                if($condicionVenta != "02"){
                                                    $plazoCredito = 0;
                                                }
                                                $factura = array(
                                                    "consecutivo" => $this->factura->getNuevoConsecutivoFEC($data['Sucursal_Codigo']),
                                                    "sucursal" => $data['Sucursal_Codigo'],
                                                    "fecha" => date(DATE_ATOM, strtotime($fechaFactura)),
                                                    "condicionVenta" => $condicionVenta,
                                                    "plazoCredito" => $plazoCredito,
                                                    "tipoPago" => $tipoPago,
                                                    "moneda" => "CRC",
                                                    "tipoCambio" => 1
                                                );

                                                $articulosYCostos = $this->convertirArticulosALineaDetalle($detalles);

                                                $r["res"] = $this->factura->crearFacturaCompraElectronica($emisor, $receptor, $factura, $articulosYCostos["costos"], $articulosYCostos["articulos"]);
                                                $r["status"] = 1;
                                                unset($r["msg"]);
                                            }else{
                                                $r["msg"] = $validacionCredito;
                                            }
                                        }else{
                                            $r["msg"] = $checkDetalles;
                                        }
                                    }else{
                                        $r["msg"] = "Debe ingresar al menos un detalle";
                                    }
                                }else{
                                    $r["msg"] = "Debe ingresar una fecha válida";
                                }
                            }else{
                                $r["msg"] = "Debe ingresar un código de actividad válido";
                            }
                        }else{
                            $r["msg"] = "Debe ingresar una dirección válida";
                        }
                    }else{
                        $r["msg"] = "Debe ingresar un correo electrónico válido";
                    }
                }else{
                    $r["msg"] = "Debe ingresar una identifiación válida";
                }
            }else{
                $r["msg"] = "Debe ingresar un nombre válido";
            }
        }else{
            $r["msg"] = "No tiene autorización para realizar esta operación";
        }
        echo json_encode($r);
    }

    function revisarDetalles($detalles){
        $index = 1;
        foreach($detalles as $linea){
            if($linea["codigo"] == "")
                return "Línea detalle $index: Código inválido";

            if(!is_numeric($linea["cantidad"]))
                return "Línea detalle $index: Cantidad inválido";

            if($linea["cantidad"] < 1)
                return "Línea detalle $index: Cantidad debe ser mayor a cero";

            if(!is_numeric($linea["precio"]))
                return "Línea detalle $index: Precio inválido";

            if($linea["precio"] < 0)
                return "Línea detalle $index: Precio no debe ser menor a cero";
            
            if(!is_numeric($linea["descuento"]))
                return "Línea detalle $index: Descuento inválido";

            if($linea["descuento"] < 0 || $linea["descuento"] > 100)
                return "Línea detalle $index: Descuento debe estar entre cero y 100";

            if(!is_numeric($linea["tarifaIVA"]))
                return "Línea detalle $index: Tarifa de IVA inválida";

            if($linea["tarifaIVA"] < 0 || $linea["tarifaIVA"] > 100)
                return "Línea detalle $index: Tarifa de IVA debe estar entre cero y 100";

            $index++;
        }
        return true;
    }

    function validarPlazoCredito($condicionVenta, $plazo){
        if($condicionVenta == "02"){
            if(!is_numeric($plazo)){
                return "Debe ingresar un plazo de crédito válido";
            }
            if($plazo < 1){
                return "El plazo de crédito debe ser mayor o igual a un día";
            }
        }
        return true;
    }

    function convertirArticulosALineaDetalle($detalles){
        $nuevasLineas = array();
        /*
            codigo: codigo,
            cantidad: cantidad,
            detalle: detalle,
            precio: precio,
            descuento: descuento,
            tarifaIVA: tarifaIVA,
            tipoCodigo: tipoCodigo,
            unidadMedida: unidadMedida,
            tipoTarifa: tipoTarifa,
            tipoImpuesto: tipoImpuesto
        */
        $costos = array(
            "total_serv_gravados" => 0,
            "total_serv_exentos" => 0,
            "total_serv_exonerados" => 0,
            "total_merc_gravada" => 0,
            "total_merc_exenta" => 0,
            "total_merc_exonerada" => 0,
            "total_gravados" => 0,
            "total_exentos" => 0,
            "total_exonerado" => 0,
            "total_ventas" => 0,
            "total_descuentos" => 0,
            "total_ventas_neta" => 0,
            "total_impuestos" => 0,
            "total_iva_devuelto" => 0,
            "total_otros_cargos" => 0,
            "total_comprobante" => 0
        );
        foreach($detalles as $d){
            $a = new stdClass();
            $a->Articulo_Factura_Cantidad = $d["cantidad"];
            $a->Articulo_Factura_Codigo = $d["codigo"];
            $a->TipoCodigo = $d["tipoCodigo"];
            $a->UnidadMedida = $d["unidadMedida"];
            $a->Articulo_Factura_Descripcion = $d["detalle"];
            $a->Articulo_Factura_Precio_Unitario = $d["precio"];
            $a->Articulo_Factura_Descuento = $d["descuento"];
            $a->Articulo_Factura_No_Retencion = 1;
            $a->Articulo_Factura_Exento = 0;
            $linea = $this->factura->getDetalleLinea($a, false);
            array_push($nuevasLineas, $linea);

            $isMercancia = !in_array($a->UnidadMedida, $this->factura->codigosUnidadDeServivicios);
                                    
            if($a->Articulo_Factura_Exento == 0){
                if($isMercancia){
                    $costos["total_merc_gravada"] += $linea["montoTotal"];
                }else{
                    $costos["total_serv_gravados"] += $linea["montoTotal"];
                }
                $costos["total_gravados"] += $linea["montoTotal"];
            }else{
                if($isMercancia){
                    $costos["total_merc_exenta"] += $linea["montoTotal"];
                    $costos["total_merc_exonerada"] += $linea["montoTotal"];
                }else{
                    $costos["total_serv_exentos"] += $linea["montoTotal"];
                    $costos["total_serv_exonerados"] += $linea["montoTotal"];
                }
                $costos["total_exentos"] += $linea["montoTotal"];
                $costos["total_exonerado"] += $linea["montoTotal"];
            }
            $costos["total_ventas"] += $linea["montoTotal"];
            
            if(isset($linea["montoDescuento"])){
                $costos["total_descuentos"] += $linea["montoDescuento"];
            }
            
            $impuesto = $linea["impuesto"][0]["monto"];
            $costos["total_impuestos"] += $impuesto;
        }
        $costos["total_exonerado"] =  $costos["total_serv_exonerados"] + $costos["total_merc_exonerada"];
        $costos["total_ventas_neta"] = $costos["total_ventas"] - $costos["total_descuentos"];
        $costos["total_comprobante"] = $costos["total_ventas_neta"] + $costos["total_impuestos"] + $costos["total_otros_cargos"];
        return array("articulos"=>$nuevasLineas, "costos"=>$costos);
    }
}