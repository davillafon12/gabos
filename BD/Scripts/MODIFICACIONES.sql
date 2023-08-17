ALTER TABLE `tb_11_precios` ADD `Precio_Descuento` DOUBLE NOT NULL AFTER `Precio_Monto`;

UPDATE  tb_11_precios SET Precio_Descuento = (select Articulo_Descuento from tb_06_articulo where tb_11_precios.`TB_06_Articulo_Articulo_Codigo`= tb_06_articulo.Articulo_Codigo and tb_11_precios.`TB_06_Articulo_TB_02_Sucursal_Codigo` = tb_06_articulo. TB_02_Sucursal_Codigo);