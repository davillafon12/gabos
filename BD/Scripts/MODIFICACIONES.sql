ALTER TABLE  `tb_02_sucursal` ADD  `Usuario_Tributa` VARCHAR( 100 ) NOT NULL AFTER  `Sucursal_leyenda_tributacion` ,
ADD  `Pass_Tributa` VARCHAR( 100 ) NOT NULL AFTER  `Usuario_Tributa` ,
ADD  `Ambiente_Tributa` VARCHAR( 10 ) NOT NULL AFTER  `Pass_Tributa` ,
ADD  `Token_Certificado_Tributa` VARCHAR( 40 ) NOT NULL AFTER  `Ambiente_Tributa` ,
ADD  `Pass_Certificado_Tributa` VARCHAR( 4 ) NOT NULL AFTER  `Token_Certificado_Tributa`;

ALTER TABLE  `tb_02_sucursal` ADD  `Provincia` INT NOT NULL AFTER  `Pass_Certificado_Tributa` ,
ADD  `Canton` INT NOT NULL AFTER  `Provincia` ,
ADD  `Distrito` INT NOT NULL AFTER  `Canton` ,
ADD  `Barrio` INT NOT NULL AFTER  `Distrito` ,
ADD  `Tipo_Cedula` VARCHAR( 10 ) NOT NULL AFTER  `Barrio` ,
ADD  `Codigo_Pais_Telefono` VARCHAR( 5 ) NOT NULL AFTER  `Tipo_Cedula` ,
ADD  `Codigo_Pais_Fax` VARCHAR( 5 ) NOT NULL AFTER  `Codigo_Pais_Telefono` ;

UPDATE tb_02_sucursal SET Tipo_Cedula =  '02';

UPDATE tb_02_sucursal SET  `Codigo_Pais_Telefono` =  '506',`Codigo_Pais_Fax` =  '506';

CREATE TABLE IF NOT EXISTS `GAROTAS_BONITAS_MAIN_DB`.`tb_55_factura_electronica` (
  `Consecutivo` INT NOT NULL,
  `Sucursal` INT NOT NULL,
  `Clave` VARCHAR(100) NULL,
  `ConsecutivoHacienda` VARCHAR(30) NULL,
  `FechaEmision` VARCHAR(50) NULL,
  `EmisorNombre` VARCHAR(200) NULL,
  `EmisorTipoIdentificacion` VARCHAR(20) NULL,
  `EmisorIdentificacion` VARCHAR(50) NULL,
  `EmisorNombreComercial` VARCHAR(200) NULL,
  `EmisorProvincia` VARCHAR(2) NULL,
  `EmisorCanton` VARCHAR(2) NULL,
  `EmisorDistrito` VARCHAR(2) NULL,
  `EmisorBarrio` VARCHAR(2) NULL,
  `EmisorOtrasSennas` VARCHAR(200) NULL,
  `EmisorCodigoPaisTelefono` VARCHAR(4) NULL,
  `EmisorTelefono` VARCHAR(20) NULL,
  `EmisorCodigoPaisFax` VARCHAR(4) NULL,
  `EmisorFax` VARCHAR(20) NULL,
  `EmisorEmail` VARCHAR(200) NULL,
  `ReceptorNombre` VARCHAR(200) NULL,
  `ReceptorTipoIdentificacion` VARCHAR(20) NULL,
  `ReceptorIdentificacion` VARCHAR(50) NULL,
  `ReceptorProvincia` VARCHAR(2) NULL,
  `ReceptorCanton` VARCHAR(2) NULL,
  `ReceptorDistrito` VARCHAR(2) NULL,
  `ReceptorBarrio` VARCHAR(2) NULL,
  `ReceptorCodigoPaisTelefono` VARCHAR(4) NULL,
  `ReceptorTelefono` VARCHAR(20) NULL,
  `ReceptorCodigoPaisFax` VARCHAR(4) NULL,
  `ReceptorFax` VARCHAR(20) NULL,
  `ReceptorEmail` VARCHAR(200) NULL,
  `CondicionVenta` VARCHAR(30) NULL,
  `PlazoCredito` INT(3) NULL,
  `MedioPago` VARCHAR(10) NULL,
  `CodigoMoneda` VARCHAR(5) NULL,
  `TipoCambio` VARCHAR(20) NULL,
  `TotalServiciosGravados` VARCHAR(20) NULL,
  `TotalServiciosExentos` VARCHAR(20) NULL,
  `TotalMercanciaGravada` VARCHAR(20) NULL,
  `TotalMercanciaExenta` VARCHAR(20) NULL,
  `TotalGravados` VARCHAR(20) NULL,
  `TotalExentos` VARCHAR(20) NULL,
  `TotalVentas` VARCHAR(20) NULL,
  `TotalDescuentos` VARCHAR(20) NULL,
  `TotalVentasNeta` VARCHAR(20) NULL,
  `TotalImpuestos` VARCHAR(20) NULL,
  `TotalComprobante` VARCHAR(20) NULL,
  `Otros` VARCHAR(200) NULL,
  `XMLSinFirmar` TEXT NULL,
  `XMLFirmado` TEXT NULL,
  `FechaRecibidoPorHacienda` DATE NULL,
  `RespuestaHaciendaXML` TEXT NULL,
  `RespuestaHaciendaFecha` DATE NULL,
  `RespuestaHaciendaEstado` VARCHAR(20) NULL,
  `CorreoEnviadoReceptor` INT NULL,
  `TipoDocumento` VARCHAR(4) NULL,
  `CodigoPais` VARCHAR(4) NULL,
  `ConsecutivoFormateado` VARCHAR(11) NULL,
  `Situacion` VARCHAR(15) NULL,
  `CodigoSeguridad` VARCHAR(8) NULL,
  PRIMARY KEY (`Consecutivo`, `Sucursal`))
ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS `GAROTAS_BONITAS_MAIN_DB`.`tb_56_articulos_factura_electronica` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Cantidad` VARCHAR(7) NULL,
  `UnidadMedida` VARCHAR(10) NULL,
  `Detalle` VARCHAR(150) NULL,
  `PrecioUnitario` VARCHAR(20) NULL,
  `MontoTotal` VARCHAR(20) NULL,
  `MontoDescuento` VARCHAR(20) NULL,
  `NaturalezaDescuento` VARCHAR(20) NULL,
  `Subtotal` VARCHAR(20) NULL,
  `ImpuestoObject` TEXT NULL,
  `MontoTotalLinea` VARCHAR(20) NULL,
  `Consecutivo` INT NOT NULL,
  `Sucursal` INT NOT NULL,
  PRIMARY KEY (`Id`))
ENGINE = InnoDB;

ALTER TABLE  `tb_55_factura_electronica` CHANGE  `RespuestaHaciendaFecha`  `RespuestaHaciendaFecha` TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE  `tb_55_factura_electronica` CHANGE  `FechaRecibidoPorHacienda`  `FechaRecibidoPorHacienda` TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE  `tb_55_factura_electronica` CHANGE  `XMLSinFirmar`  `XMLSinFirmar` LONGTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE  `tb_55_factura_electronica` CHANGE  `XMLFirmado`  `XMLFirmado` LONGTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE  `tb_55_factura_electronica` CHANGE  `RespuestaHaciendaXML`  `RespuestaHaciendaXML` LONGTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE  `tb_03_cliente` ADD  `NoReceptor` BOOLEAN NOT NULL AFTER  `Aplica_Retencion`;

UPDATE  `tb_03_cliente` SET  `NoReceptor` =  '1' WHERE  `tb_03_cliente`.`Cliente_Cedula` =  '0';
UPDATE  `tb_03_cliente` SET  `NoReceptor` =  '1' WHERE  `tb_03_cliente`.`Cliente_Cedula` =  '1';
UPDATE  `tb_03_cliente` SET  `NoReceptor` =  '1' WHERE  `tb_03_cliente`.`Cliente_Cedula` =  '2';

CREATE TABLE IF NOT EXISTS `tb_57_nota_credito_electronica` (
  `Consecutivo` INT NOT NULL,
  `Sucursal` INT NOT NULL,
  `Clave` VARCHAR(100) NULL,
  `ConsecutivoHacienda` VARCHAR(30) NULL,
  `FechaEmision` VARCHAR(50) NULL,
  `EmisorNombre` VARCHAR(200) NULL,
  `EmisorTipoIdentificacion` VARCHAR(20) NULL,
  `EmisorIdentificacion` VARCHAR(50) NULL,
  `EmisorNombreComercial` VARCHAR(200) NULL,
  `EmisorProvincia` VARCHAR(2) NULL,
  `EmisorCanton` VARCHAR(2) NULL,
  `EmisorDistrito` VARCHAR(2) NULL,
  `EmisorBarrio` VARCHAR(2) NULL,
  `EmisorOtrasSennas` VARCHAR(200) NULL,
  `EmisorCodigoPaisTelefono` VARCHAR(4) NULL,
  `EmisorTelefono` VARCHAR(20) NULL,
  `EmisorCodigoPaisFax` VARCHAR(4) NULL,
  `EmisorFax` VARCHAR(20) NULL,
  `EmisorEmail` VARCHAR(200) NULL,
  `ReceptorNombre` VARCHAR(200) NULL,
  `ReceptorTipoIdentificacion` VARCHAR(20) NULL,
  `ReceptorIdentificacion` VARCHAR(50) NULL,
  `ReceptorProvincia` VARCHAR(2) NULL,
  `ReceptorCanton` VARCHAR(2) NULL,
  `ReceptorDistrito` VARCHAR(2) NULL,
  `ReceptorBarrio` VARCHAR(2) NULL,
  `ReceptorCodigoPaisTelefono` VARCHAR(4) NULL,
  `ReceptorTelefono` VARCHAR(20) NULL,
  `ReceptorCodigoPaisFax` VARCHAR(4) NULL,
  `ReceptorFax` VARCHAR(20) NULL,
  `ReceptorEmail` VARCHAR(200) NULL,
  `CondicionVenta` VARCHAR(30) NULL,
  `PlazoCredito` INT(3) NULL,
  `MedioPago` VARCHAR(10) NULL,
  `CodigoMoneda` VARCHAR(5) NULL,
  `TipoCambio` VARCHAR(20) NULL,
  `TotalServiciosGravados` VARCHAR(20) NULL,
  `TotalServiciosExentos` VARCHAR(20) NULL,
  `TotalMercanciaGravada` VARCHAR(20) NULL,
  `TotalMercanciaExenta` VARCHAR(20) NULL,
  `TotalGravados` VARCHAR(20) NULL,
  `TotalExentos` VARCHAR(20) NULL,
  `TotalVentas` VARCHAR(20) NULL,
  `TotalDescuentos` VARCHAR(20) NULL,
  `TotalVentasNeta` VARCHAR(20) NULL,
  `TotalImpuestos` VARCHAR(20) NULL,
  `TotalComprobante` VARCHAR(20) NULL,
  `Otros` VARCHAR(200) NULL,
  `XMLSinFirmar` TEXT NULL,
  `XMLFirmado` TEXT NULL,
  `FechaRecibidoPorHacienda` DATE NULL,
  `RespuestaHaciendaXML` TEXT NULL,
  `RespuestaHaciendaFecha` DATE NULL,
  `RespuestaHaciendaEstado` VARCHAR(20) NULL,
  `CorreoEnviadoReceptor` INT NULL,
  `TipoDocumento` VARCHAR(4) NULL,
  `CodigoPais` VARCHAR(4) NULL,
  `ConsecutivoFormateado` VARCHAR(11) NULL,
  `Situacion` VARCHAR(15) NULL,
  `CodigoSeguridad` VARCHAR(8) NULL,
  `DocumentoReferenciaNumero` VARCHAR(100) NULL,
  `DocumentoReferenciaTipo` VARCHAR(4) NULL,
  `DocumentoReferenciaFechaEmision` VARCHAR(50) NULL,
  `DocumentoReferenciaCodigo` VARCHAR(4) NULL,
  `DocumentoReferenciaRazon` VARCHAR(180) NULL,
  PRIMARY KEY (`Consecutivo`, `Sucursal`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1;

CREATE TABLE IF NOT EXISTS `tb_58_articulos_nota_credito_electronica` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Cantidad` VARCHAR(7) NULL,
  `UnidadMedida` VARCHAR(10) NULL,
  `Detalle` VARCHAR(150) NULL,
  `PrecioUnitario` VARCHAR(20) NULL,
  `MontoTotal` VARCHAR(20) NULL,
  `MontoDescuento` VARCHAR(20) NULL,
  `NaturalezaDescuento` VARCHAR(20) NULL,
  `Subtotal` VARCHAR(20) NULL,
  `ImpuestoObject` TEXT NULL,
  `MontoTotalLinea` VARCHAR(20) NULL,
  `Consecutivo` INT NOT NULL,
  `Sucursal` INT NOT NULL,
  PRIMARY KEY (`Id`))
ENGINE = InnoDB;

