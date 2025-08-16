-- Agregar codigo actividad para el receptor
ALTER TABLE `tb_03_cliente` ADD `Codigo_Actividad` VARCHAR(6) AFTER `NoReceptor`;
ALTER TABLE `tb_55_factura_electronica` ADD `ReceptorCodigoActividad` VARCHAR(6) AFTER `ReceptorEmail`;
ALTER TABLE `tb_57_nota_credito_electronica` ADD `ReceptorCodigoActividad` VARCHAR(6) NULL DEFAULT NULL AFTER `ReceptorEmail`;

-- Cambios campo de barrio
ALTER TABLE `tb_55_factura_electronica` CHANGE `EmisorBarrio` `EmisorBarrio` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `tb_55_factura_electronica` CHANGE `ReceptorBarrio` `ReceptorBarrio` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `tb_57_nota_credito_electronica` CHANGE `EmisorBarrio` `EmisorBarrio` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `tb_57_nota_credito_electronica` CHANGE `ReceptorBarrio` `ReceptorBarrio` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

-- Cambios descuento
ALTER TABLE `tb_56_articulos_factura_electronica` ADD `TipoDescuento` VARCHAR(2) NULL AFTER `MontoDescuento`;
ALTER TABLE `tb_58_articulos_nota_credito_electronica` ADD `TipoDescuento` VARCHAR(2) NULL AFTER `MontoDescuento`;

-- Cambios medio de pago
ALTER TABLE `tb_55_factura_electronica` ADD `MedioPagoObject` TEXT NOT NULL AFTER `MedioPago`;
ALTER TABLE `tb_57_nota_credito_electronica` ADD `MedioPagoObject` TEXT NOT NULL AFTER `MedioPago`;
ALTER TABLE `tb_07_factura` CHANGE `Factura_Tipo_Pago` `Factura_Tipo_Pago` VARCHAR(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL;
ALTER TABLE `tb_27_notas_credito` CHANGE `Tipo_Pago` `Tipo_Pago` VARCHAR(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL;


-- Cambios para desglose total de impuestos
ALTER TABLE `tb_55_factura_electronica` ADD `DesgloseTotalImpuestosObject` TEXT NOT NULL AFTER `TotalImpuestos`;
ALTER TABLE `tb_57_nota_credito_electronica` ADD `DesgloseTotalImpuestosObject` TEXT NOT NULL AFTER `TotalImpuestos`;

-- Cambios plazo credito
ALTER TABLE `tb_55_factura_electronica` CHANGE `PlazoCredito` `PlazoCredito` INT(5) NULL DEFAULT NULL;
ALTER TABLE `tb_57_nota_credito_electronica` CHANGE `PlazoCredito` `PlazoCredito` INT(5) NULL DEFAULT NULL;

-- Actualizar leyenda FE 4.4
UPDATE tb_02_sucursal set Sucursal_leyenda_tributacion = 'Emitida conforme los lineamientos técnicos y normativos establecidos en la resolución N°  MH-DGT-RES-0027-2024 de las ocho horas veinte minutos del trece de noviembre de dos mil veinticuatro' where Codigo in (0,1,2,3,4,7);

-- Descuentos
CREATE TABLE `catalogo_tipo_descuento` (
  `id` int(11) NOT NULL,
  `codigo` varchar(2) NOT NULL,
  `descripcion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `catalogo_tipo_descuento`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `catalogo_tipo_descuento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

INSERT INTO catalogo_tipo_descuento (codigo, descripcion) VALUES 
('01', 'Por Regalía'),
('02', 'Por Regalía o Bonificaciones IVA Cobrado al Cliente'),
('03', 'Por Bonificación'),
('04', 'Por volumen'),
('05', 'Por Temporada'),
('06', 'Promocional'),
('07', 'Comercial'),
('08', 'Por frecuencia'),
('09', 'Sostenido '),
('99', 'Otros');

ALTER TABLE `tb_06_articulo` ADD `CodigoDescuento` VARCHAR(2) NOT NULL AFTER `Impuesto`;
ALTER TABLE `tb_06_articulo` CHANGE `CodigoDescuento` `CodigoDescuento` VARCHAR(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL;
ALTER TABLE `tb_11_precios` ADD `Precio_Codigo_Descuento` VARCHAR(2) NULL DEFAULT NULL AFTER `Precio_Descuento`;
ALTER TABLE `tb_21_descuento_cliente` ADD `TipoDescuento` VARCHAR(2) NOT NULL DEFAULT '07' AFTER `Descuento_cliente_porcentaje`;

UPDATE tb_11_precios SET Precio_Codigo_Descuento = '07';
UPDATE tb_06_articulo SET CodigoDescuento = '07';

ALTER TABLE `tb_08_articulos_factura` ADD `TipoDescuento` VARCHAR(2) NOT NULL DEFAULT '07' AFTER `Impuesto`;
ALTER TABLE `tb_04_articulos_proforma` ADD `TipoDescuento` VARCHAR(2) NOT NULL DEFAULT '07' AFTER `Impuesto`;
ALTER TABLE `tb_28_productos_notas_credito` ADD `TipoDescuento` VARCHAR(2) NOT NULL DEFAULT '07' AFTER `Descuento`;
ALTER TABLE `tb_50_articulos_consignacion` ADD `Codigo_Descuento` VARCHAR(2) NOT NULL DEFAULT '07' AFTER `Descuento`;
ALTER TABLE `tb_51_lista_consignacion` ADD `Codigo_Descuento` VARCHAR(2) NOT NULL DEFAULT '07' AFTER `Descuento`;

-- Cambios para tipo de pago mixto
ALTER TABLE `tb_23_mixto` ADD `Tipo_Pago` VARCHAR(20) NOT NULL DEFAULT 'contado' AFTER `Mixto_Cantidad_Paga`;


-- Cambios para otras señas del emisor y receptor
ALTER TABLE `tb_55_factura_electronica` ADD `ReceptorOtrasSennas` VARCHAR(200) NOT NULL AFTER `ReceptorBarrio`;
ALTER TABLE `tb_55_factura_electronica` CHANGE `ReceptorOtrasSennas` `ReceptorOtrasSennas` VARCHAR(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;


