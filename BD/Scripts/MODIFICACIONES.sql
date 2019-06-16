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

ALTER TABLE  `tb_28_productos_notas_credito` ADD  `TipoCodigo` VARCHAR( 2 ) NOT NULL AFTER  `Codigo`;

ALTER TABLE  `tb_58_articulos_nota_credito_electronica` ADD  `Codigo` VARCHAR( 30 ) NOT NULL AFTER  `Id` ,
ADD  `TipoCodigo` VARCHAR( 2 ) NOT NULL AFTER  `Codigo`;

ALTER TABLE  `tb_58_articulos_nota_credito_electronica` CHANGE  `Codigo`  `Codigo` VARCHAR( 30 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;

ALTER TABLE  `tb_58_articulos_nota_credito_electronica` CHANGE  `TipoCodigo`  `TipoCodigo` VARCHAR( 2 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;

ALTER TABLE  `tb_58_articulos_nota_credito_electronica` ADD  `BaseImponible` VARCHAR( 20 ) NULL AFTER  `NaturalezaDescuento`;

ALTER TABLE  `tb_57_nota_credito_electronica` ADD  `CodigoActividad` VARCHAR( 20 ) NULL ,
ADD  `TotalServiciosExonerados` VARCHAR( 20 ) NULL ,
ADD  `TotalMercanciaExonerada` VARCHAR( 20 ) NULL ,
ADD  `TotalExonerado` VARCHAR( 20 ) NULL ,
ADD  `TotalIVADevuelto` VARCHAR( 20 ) NULL ,
ADD  `TotalOtrosCargos` VARCHAR( 20 ) NULL;


--
-- Estructura de tabla para la tabla `catalogo_unidad_medida`
--

CREATE TABLE IF NOT EXISTS `catalogo_unidad_medida` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Codigo` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `Descripcion` varchar(40) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=96 ;

--
-- Volcado de datos para la tabla `catalogo_unidad_medida`
--

INSERT INTO `catalogo_unidad_medida` (`Id`, `Codigo`, `Descripcion`) VALUES
(1, 'Al', 'Alquiler de uso habitacional'),
(2, 'Alc', 'Alquiler de uso comercial'),
(3, 'Cm', 'Comisiones'),
(4, 'I', 'Intereses'),
(5, 'Os', 'Otro tipo de servicios'),
(6, 'Sp', 'Servicios Profesionales'),
(7, 'Spe', 'Servicios Personales'),
(8, 'St', 'Servicios Técnicos'),
(9, 'm', 'Metro'),
(10, 'kg', 'Kilogramo'),
(11, 's', 'Segundo'),
(12, 'A', 'Ampere'),
(13, 'K', 'Kelvin'),
(14, 'mol', 'Mol'),
(15, 'cd', 'Candela'),
(16, 'm²', 'metro cuadrado'),
(17, 'm³', 'metro cúbico'),
(18, 'm/s', 'metro por segundo'),
(19, 'm/s²', 'metro por segundo cuadrado'),
(20, '1/m', '1 por metro'),
(21, 'kg/m³', 'kilogramo por metro cúbico'),
(22, 'A/m²', 'ampere por metro cuadrado'),
(23, 'A/m', 'ampere por metro'),
(24, 'mol/m³', 'mol por metro cúbico'),
(25, 'cd/m²', 'candela por metro cuadrado'),
(26, '1', 'uno (indice de refracción)'),
(27, 'rad', 'radián'),
(28, 'sr', 'estereorradián'),
(29, 'Hz', 'hertz'),
(30, 'N', 'newton'),
(31, 'Pa', 'Pascal'),
(32, 'J', 'Joule'),
(33, 'W', 'Watt'),
(34, 'C', 'coulomb'),
(35, 'V', 'volt'),
(36, 'F', 'Farad'),
(37, 'Ω', 'ohm'),
(38, 'S', 'siemens'),
(39, 'Wb', 'weber'),
(40, 'T', 'Tesla'),
(41, 'H', 'henry'),
(42, '°C', 'grado Celsius'),
(43, 'lm', 'lumen'),
(44, 'lx', 'lux'),
(45, 'Bq', 'Becquerel'),
(46, 'Gy', 'gray'),
(47, 'Sv', 'sievert'),
(48, 'kat', 'katal'),
(49, 'Pa·s', 'pascal segundo'),
(50, 'N·m', 'newton metro'),
(51, 'N/m', 'newton por metro'),
(52, 'rad/s', 'radián por segundo'),
(53, 'rad/s²', 'radián por segundo cuadrado'),
(54, 'W/m²', 'watt por metro cuadrado'),
(55, 'J/K', 'joule por kelvin'),
(56, 'J/(kg·K)', 'joule por kilogramo kelvin'),
(57, 'J/kg', 'joule por kilogramo'),
(58, 'W/(m·K)', 'watt por metro kevin'),
(59, 'J/m³', 'joule por metro cúbico'),
(60, 'V/m', 'volt por metro'),
(61, 'C/m³', 'coulomb por metro cúbico'),
(62, 'C/m²', 'coulomb por metro cuadrado'),
(63, 'F/m', 'farad por metro'),
(64, 'H/m', 'henry por metro'),
(65, 'J/mol', 'joule por mol'),
(66, 'J/(mol·K)', 'joule por mol kelvin'),
(67, 'C/kg', 'coulomb por kilogramo'),
(68, 'Gy/s', 'gray por segundo'),
(69, 'W/sr', 'watt por estereorradián'),
(70, 'W/(m²·sr)', 'watt por metro cuadrado estereorradián\n'),
(71, 'kat/m³', 'katal por metro cúbico'),
(72, 'min', 'minuto'),
(73, 'h', 'hora'),
(74, 'd', 'día'),
(75, 'º', 'grado'),
(76, '´', 'minuto'),
(77, '´´', 'segundo'),
(78, 'L', 'litro'),
(79, 't', 'tonelada'),
(80, 'Np', 'Neper'),
(81, 'B', 'Bel'),
(82, 'eV', 'electronvolt'),
(83, 'u', 'unidad de masa atómica unificada'),
(84, 'ua', 'unidad astronómica'),
(85, 'Unid', 'unidad'),
(86, 'Gal', 'galón'),
(87, 'g', 'gramo'),
(88, 'Km', 'kilometro'),
(89, 'Kw', 'kilovatios'),
(90, 'In', 'pulgada'),
(91, 'cm', 'centimetro'),
(92, 'mL', 'mililitro'),
(93, 'mm', 'milimetro'),
(94, 'Oz', 'onzas'),
(95, 'Otros', 'Otros');

ALTER TABLE  `tb_06_articulo` ADD  `UnidadMedida` VARCHAR( 10 ) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL AFTER  `TipoCodigo`;

ALTER TABLE  `tb_08_articulos_factura` ADD  `UnidadMedida` VARCHAR( 10 ) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL AFTER  `TipoCodigo`;

UPDATE tb_06_articulo SET  `UnidadMedida` =  'Unid';


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catalogo_tipo_impuesto`
--

CREATE TABLE IF NOT EXISTS `catalogo_tipo_impuesto` (
  `Id` varchar(2) NOT NULL,
  `Descripcion` varchar(100) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `catalogo_tipo_impuesto`
--

INSERT INTO `catalogo_tipo_impuesto` (`Id`, `Descripcion`) VALUES
('01', 'Impuesto al Valor Agregado'),
('02', 'Impuesto Selectivo de Consumo'),
('03', 'Impuesto unico a los combustivos'),
('04', 'Impuesto especifico de bebidas alcohólicas'),
('05', 'impuesto especifico sobre las bebidas envasadas sin contenido alcoholico y jabones de tocador'),
('06', 'impuesto a los productos de tabaco'),
('07', 'IVA (cálculo especial)'),
('08', 'IVA Régimen de Bienes Usados (Factor)'),
('12', 'Impuesto Especifico al Cemento'),
('99', 'Otros');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `catalogo_tipo_tarifa`
--

CREATE TABLE IF NOT EXISTS `catalogo_tipo_tarifa` (
  `Id` varchar(2) NOT NULL,
  `Descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `catalogo_tipo_tarifa`
--

INSERT INTO `catalogo_tipo_tarifa` (`Id`, `Descripcion`) VALUES
('01', 'Tarifa 0% (Exento)'),
('02', 'Tarifa Reducida 1%'),
('03', 'Tarifa reducida 2%'),
('04', 'Tarifa reducida 4%'),
('05', 'Transitorio 0%'),
('06', 'Transitorio 4%'),
('07', 'Transitorio 8%'),
('08', 'Tarifa General 13%');








-- ------------------------------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------------------------------














CREATE TABLE IF NOT EXISTS `tb_61_factura_compra_electronica` (
  `Consecutivo` int(11) NOT NULL,
  `Sucursal` int(11) NOT NULL,
  `Clave` varchar(100) DEFAULT NULL,
  `ConsecutivoHacienda` varchar(30) DEFAULT NULL,
  `FechaEmision` varchar(50) DEFAULT NULL,
  `EmisorNombre` varchar(200) DEFAULT NULL,
  `EmisorTipoIdentificacion` varchar(20) DEFAULT NULL,
  `EmisorIdentificacion` varchar(50) DEFAULT NULL,
  `EmisorProvincia` varchar(2) DEFAULT NULL,
  `EmisorCanton` varchar(2) DEFAULT NULL,
  `EmisorDistrito` varchar(2) DEFAULT NULL,
  `EmisorOtrasSennas` varchar(200) DEFAULT NULL,
  `EmisorEmail` varchar(200) DEFAULT NULL,
  `ReceptorNombre` varchar(200) DEFAULT NULL,
  `ReceptorTipoIdentificacion` varchar(20) DEFAULT NULL,
  `ReceptorIdentificacion` varchar(50) DEFAULT NULL,
  `ReceptorProvincia` varchar(2) DEFAULT NULL,
  `ReceptorCanton` varchar(2) DEFAULT NULL,
  `ReceptorDistrito` varchar(2) DEFAULT NULL,
  `ReceptorEmail` varchar(200) DEFAULT NULL,
  `CondicionVenta` varchar(30) DEFAULT NULL,
  `PlazoCredito` int(3) DEFAULT NULL,
  `MedioPago` varchar(10) DEFAULT NULL,
  `CodigoMoneda` varchar(5) DEFAULT NULL,
  `TipoCambio` varchar(20) DEFAULT NULL,
  `TotalServiciosGravados` varchar(20) DEFAULT NULL,
  `TotalServiciosExentos` varchar(20) DEFAULT NULL,
  `TotalServiciosExonerados` varchar(20) DEFAULT NULL,
  `TotalMercanciaGravada` varchar(20) DEFAULT NULL,
  `TotalMercanciaExenta` varchar(20) DEFAULT NULL,
  `TotalMercanciaExonerada` varchar(20) DEFAULT NULL,
  `TotalGravados` varchar(20) DEFAULT NULL,
  `TotalExentos` varchar(20) DEFAULT NULL,
  `TotalExonerado` varchar(20) DEFAULT NULL,
  `TotalVentas` varchar(20) DEFAULT NULL,
  `TotalDescuentos` varchar(20) DEFAULT NULL,
  `TotalVentasNeta` varchar(20) DEFAULT NULL,
  `TotalImpuestos` varchar(20) DEFAULT NULL,
  `TotalIVADevuelto` varchar(20) DEFAULT NULL,
  `TotalOtrosCargos` varchar(20) DEFAULT NULL,
  `TotalComprobante` varchar(20) DEFAULT NULL,
  `XMLSinFirmar` longtext,
  `XMLFirmado` longtext,
  `FechaRecibidoPorHacienda` timestamp NULL DEFAULT NULL,
  `RespuestaHaciendaXML` longtext,
  `RespuestaHaciendaFecha` timestamp NULL DEFAULT NULL,
  `RespuestaHaciendaEstado` varchar(20) DEFAULT NULL,
  `CorreoEnviadoReceptor` int(11) DEFAULT NULL,
  `TipoDocumento` varchar(4) DEFAULT NULL,
  `CodigoPais` varchar(4) DEFAULT NULL,
  `ConsecutivoFormateado` varchar(11) DEFAULT NULL,
  `Situacion` varchar(15) DEFAULT NULL,
  `CodigoSeguridad` varchar(8) DEFAULT NULL,
  `CodigoActividad` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`Consecutivo`,`Sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `tb_62_articulos_factura_compra_electronica` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Codigo` varchar(30) NOT NULL,
  `TipoCodigo` varchar(2) NOT NULL,
  `Cantidad` varchar(7) DEFAULT NULL,
  `UnidadMedida` varchar(10) DEFAULT NULL,
  `Detalle` varchar(150) DEFAULT NULL,
  `PrecioUnitario` varchar(20) DEFAULT NULL,
  `MontoTotal` varchar(20) DEFAULT NULL,
  `MontoDescuento` varchar(20) DEFAULT NULL,
  `NaturalezaDescuento` varchar(20) DEFAULT NULL,
  `Subtotal` varchar(20) DEFAULT NULL,
  `ImpuestoObject` text,
  `MontoTotalLinea` varchar(20) DEFAULT NULL,
  `Consecutivo` int(11) NOT NULL,
  `Sucursal` int(11) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE  `tb_62_articulos_factura_compra_electronica` ADD  `BaseImponible` VARCHAR( 20 ) NOT NULL AFTER  `MontoTotal`;








-- ------------------------------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------------------------------
-- ------------------------------------------------------------------------------------------------------------















ALTER TABLE  `tb_02_sucursal` ADD  `RequiereFE` BOOLEAN NOT NULL AFTER  `CodigoActividad`;

UPDATE tb_02_sucursal SET RequiereFE =1;