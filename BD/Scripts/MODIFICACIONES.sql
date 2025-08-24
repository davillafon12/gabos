UPDATE `tb_06_articulo` SET `CodigoDescuento`='07' WHERE `CodigoDescuento` in ('01','02','03');

UPDATE `tb_11_precios` SET `Precio_Codigo_Descuento`='07' WHERE `Precio_Codigo_Descuento` in ('01','02','03');

UPDATE `tb_21_descuento_cliente` SET `TipoDescuento`='07' WHERE `TipoDescuento` in ('01','02','03');