SELECT  t.Articulo_Factura_Codigo , SUM(t.Articulo_Factura_Cantidad) , t.Articulo_Factura_Descripcion
FROM (
              SELECT  distinct
                      fac.Factura_Consecutivo,  
                      artf.Articulo_Factura_Codigo,
                      artf.Articulo_Factura_Cantidad,
                      artf.Articulo_Factura_Descripcion
              FROM    tb_07_factura fac 
                      inner join tb_08_articulos_factura artf on fac.Factura_Consecutivo = artf.TB_07_Factura_Factura_Consecutivo
                            and fac.TB_02_Sucursal_Codigo = artf.TB_07_Factura_TB_02_Sucursal_Codigo 
                            and fac.Factura_Vendedor_Codigo = artf.TB_07_Factura_Factura_Vendedor_Codigo 
                            and fac.TB_03_Cliente_Cliente_Cedula = artf.TB_07_Factura_TB_03_Cliente_Cliente_Cedula
              WHERE   fac.Factura_Consecutivo in (1,2, 4, 5) and
                      fac.TB_02_Sucursal_Codigo = 0) AS t
GROUP BY t.Articulo_Factura_Codigo
order by t.Articulo_Factura_Codigo desc
limit 3