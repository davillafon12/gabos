ALTER TABLE `tb_37_cierre_caja` ADD `Base` DOUBLE NOT NULL AFTER `Fecha`, ADD `Tipo_Cambio` DOUBLE NOT NULL AFTER `Base`, ADD `Total_Conteo` DOUBLE NOT NULL AFTER `Tipo_Cambio`;

ALTER TABLE `tb_38_moneda_cierre_caja` CHANGE `Id` `Id` INT(11) NOT NULL AUTO_INCREMENT;