ALTER TABLE `tb_02_sucursal` CHANGE `Sucursal_leyenda_tributacion` `Sucursal_leyenda_tributacion` VARCHAR(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `tb_07_factura` ADD `Factura_Entregado_Vuelto` VARCHAR(30) NOT NULL , ADD `Factura_Recibido_Vuelto` VARCHAR(30) NOT NULL ;

ALTER TABLE `tb_30_notas_debito` ADD `Sucursal_Recibe` VARCHAR(10) NOT NULL , ADD `Sucursal_Entrega` VARCHAR(10) NOT NULL ;