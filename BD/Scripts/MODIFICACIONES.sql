ALTER TABLE `tb_07_factura` ADD `Factura_Cliente_Exento` BOOLEAN NOT NULL AFTER `Factura_Estado`, ADD `Factura_Cliente_No_Retencion` INT NOT NULL AFTER `Factura_Cliente_Exento`, ADD `Factura_Cliente_Sucursal` INT NOT NULL AFTER `Factura_Cliente_No_Retencion`;
UPDATE tb_07_factura set`Factura_Cliente_Exento` = 0, `Factura_Cliente_No_Retencion` = 0, `Factura_Cliente_Sucursal` = 0;

ALTER TABLE `tb_10_proforma` ADD `Proforma_Cliente_Sucursal` BOOLEAN NOT NULL AFTER `Proforma_Estado`, ADD `Proforma_Cliente_Exento` BOOLEAN NOT NULL AFTER `Proforma_Cliente_Sucursal`, ADD `Proforma_Cliente_No_Retencion` BOOLEAN NOT NULL AFTER `Proforma_Cliente_Exento`;
UPDATE `tb_10_proforma` SET `Proforma_Cliente_Sucursal`=0,`Proforma_Cliente_Exento`=0,`Proforma_Cliente_No_Retencion`=0;
