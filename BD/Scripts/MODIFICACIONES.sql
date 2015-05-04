ALTER TABLE `tb_03_cliente` ADD `Aplica_Retencion` BOOLEAN NOT NULL AFTER `Cliente_EsExento`;
UPDATE `tb_03_cliente` SET `Aplica_Retencion` = 0;
ALTER TABLE `tb_06_articulo` ADD `Articulo_No_Retencion` BOOLEAN NOT NULL AFTER `Articulo_Exento`;
UPDATE `tb_06_articulo` SET `Articulo_No_Retencion` = 0;
ALTER TABLE `tb_08_articulos_factura` ADD `Articulo_Factura_No_Retencion` BOOLEAN NOT NULL AFTER `Articulo_Factura_Exento`;
update `tb_08_articulos_factura` set Articulo_Factura_No_Retencion = 0;
ALTER TABLE `tb_04_articulos_proforma` ADD `Articulo_Proforma_No_Retencion` BOOLEAN NOT NULL AFTER `Articulo_Proforma_Exento`;
update `tb_04_articulos_proforma` set Articulo_Proforma_No_Retencion = 0;

