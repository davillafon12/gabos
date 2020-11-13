UPDATE tb_06_articulo SET CodigoCabys='3899799010200', Impuesto=13;

ALTER TABLE `tb_08_articulos_factura` ADD `Codigo_Cabys` VARCHAR( 14 ) NOT NULL ,
ADD `Impuesto` INT NOT NULL;

ALTER TABLE `tb_04_articulos_proforma` ADD `Codigo_Cabys` VARCHAR( 14 ) NOT NULL ,
ADD `Impuesto` INT NOT NULL;

ALTER TABLE `tb_51_lista_consignacion` ADD `Codigo_Cabys` VARCHAR( 14 ) NOT NULL ,
ADD `Impuesto` INT NOT NULL;