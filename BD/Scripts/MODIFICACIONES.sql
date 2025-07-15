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