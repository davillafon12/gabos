ALTER TABLE  `tb_02_sucursal` ADD  `CodigoActividad` VARCHAR( 6 ) NOT NULL;

CREATE TABLE IF NOT EXISTS `catalogo_tipo_codigo_articulo` (
  `Codigo` varchar(2) NOT NULL,
  `Descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `Catalogo_Tipo_Codigo_Articulo`
--

INSERT INTO `Catalogo_Tipo_Codigo_Articulo` (`Codigo`, `Descripcion`) VALUES
('01', 'Código del producto del vendedor'),
('02', 'Código del producto del comprador'),
('03', 'Código del producto asignado por la industria'),
('04', 'Código uso interno'),
('99', 'Otros');

ALTER TABLE  `tb_06_articulo` ADD  `TipoCodigo` VARCHAR( 2 ) NOT NULL AFTER  `Articulo_No_Retencion`;

UPDATE tb_06_articulo SET  `TipoCodigo` =  '01';

ALTER TABLE  `tb_56_articulos_factura_electronica` ADD  `Codigo` VARCHAR( 30 ) NOT NULL AFTER  `Id` ,
ADD  `TipoCodigo` VARCHAR( 2 ) NOT NULL AFTER  `Codigo`;

ALTER TABLE  `tb_08_articulos_factura` ADD  `TipoCodigo` VARCHAR( 2 ) NOT NULL;

ALTER TABLE  `tb_55_factura_electronica` ADD  `CodigoActividad` VARCHAR( 6 ) NOT NULL;

ALTER TABLE  `tb_55_factura_electronica` ADD  `TotalServiciosExonerados` VARCHAR( 20 ) NOT NULL AFTER  `TotalServiciosExentos`;

ALTER TABLE  `tb_55_factura_electronica` ADD  `TotalMercanciaExonerada` VARCHAR( 20 ) NOT NULL AFTER  `TotalMercanciaExenta`;

ALTER TABLE  `tb_55_factura_electronica` ADD  `TotalExonerado` VARCHAR( 20 ) NOT NULL AFTER  `TotalExentos`;

ALTER TABLE  `tb_55_factura_electronica` ADD  `TotalIVADevuelto` VARCHAR( 20 ) NOT NULL AFTER  `TotalImpuestos`;

ALTER TABLE  `tb_55_factura_electronica` CHANGE  `TotalServiciosExonerados`  `TotalServiciosExonerados` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;

ALTER TABLE  `tb_55_factura_electronica` CHANGE  `TotalExonerado`  `TotalExonerado` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;

ALTER TABLE  `tb_55_factura_electronica` CHANGE  `TotalMercanciaExonerada`  `TotalMercanciaExonerada` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;

ALTER TABLE  `tb_55_factura_electronica` CHANGE  `TotalIVADevuelto`  `TotalIVADevuelto` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;

ALTER TABLE  `tb_55_factura_electronica` CHANGE  `CodigoActividad`  `CodigoActividad` VARCHAR( 6 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;

ALTER TABLE  `tb_55_factura_electronica` ADD  `TotalOtrosCargos` VARCHAR( 20 ) NULL AFTER  `TotalIVADevuelto`;

ALTER TABLE  `tb_56_articulos_factura_electronica` ADD  `BaseImponible` VARCHAR( 20 ) NULL AFTER  `Subtotal`;