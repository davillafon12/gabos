
select pre.TB_06_Articulo_Articulo_Codigo,
       sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-0)))) as precio0, 
       sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-1)))) as precio1, 
       sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-2)))) as precio2, 
       sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-3)))) as precio3, 
       sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-4)))) as precio4, 
       sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-5)))) as precio5
       from tb_11_precios pre
where pre.TB_06_Articulo_Articulo_Codigo = 24950
and pre.TB_06_Articulo_TB_05_Familia_Familia_Codigo = 24 
and TB_06_Articulo_TB_02_Sucursal_Codigo = 0






SELECT  art.Articulo_Codigo Codigo, 
        art.Articulo_Cantidad_Inventario CantInventario, 
        art.Articulo_Cantidad_Defectuoso CantDefectuoso, 
        art.Articulo_Exento Exento, 
        art.TB_05_Familia_Familia_Codigo FamCodigo, 
        art.TB_02_Sucursal_Codigo SucCodigo,
        sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-0)))) as precio0, 
        sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-1)))) as precio1, 
        sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-2)))) as precio2, 
        sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-3)))) as precio3, 
        sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-4)))) as precio4, 
        sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-5)))) as precio5        
FROM tb_06_articulo art
        inner join tb_11_precios pre on 
        art.Articulo_Codigo = pre.TB_06_Articulo_Articulo_Codigo and 
        art.TB_05_Familia_Familia_Codigo = pre.TB_06_Articulo_TB_05_Familia_Familia_Codigo and 
        art.TB_02_Sucursal_Codigo = pre.TB_06_Articulo_TB_02_Sucursal_Codigo
where  
        art.TB_05_Familia_Familia_Codigo = 24 and 
        art.TB_02_Sucursal_Codigo = 0
        group by art.Articulo_Codigo