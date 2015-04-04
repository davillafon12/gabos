ALTER TABLE `tb_37_cierre_caja` ADD `Base` DOUBLE NOT NULL AFTER `Fecha`, ADD `Tipo_Cambio` DOUBLE NOT NULL AFTER `Base`, ADD `Total_Conteo` DOUBLE NOT NULL AFTER `Tipo_Cambio`;

ALTER TABLE `tb_38_moneda_cierre_caja` CHANGE `Id` `Id` INT(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `garotas_bonitas_main_db`.`tb_39_configuracion` (`Parametro`, `Valor`) VALUES ('aplicar_retencion', '0');

ALTER TABLE `tb_07_factura` ADD `Factura_Retencion` DOUBLE NOT NULL AFTER `Factura_porcentaje_iva`;

ALTER TABLE `tb_08_articulos_factura` ADD `Articulo_Factura_Precio_Final` DOUBLE NOT NULL AFTER `Articulo_Factura_Precio_Unitario`;

ALTER TABLE `tb_04_articulos_proforma` ADD `Articulo_Proforma_Precio_Final` DOUBLE NOT NULL AFTER `Articulo_Proforma_Precio_Unitario`;

ALTER TABLE `tb_10_proforma` ADD `Proforma_Retencion` DOUBLE NOT NULL AFTER `Proforma_Porcentaje_IVA`;