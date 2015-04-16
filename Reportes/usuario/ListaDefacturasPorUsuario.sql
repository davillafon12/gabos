SELECT  user.Usuario_Nombre_Usuario usuario, 
        CONCAT(user.Usuario_Nombre, " ", user.Usuario_Apellidos) nombre,
        fac.Factura_Consecutivo consecutivo, 
        fac.Factura_Fecha_Hora fecha, 
        fac.Factura_Monto_Total montoTotal, 
        fac.Factura_Monto_IVA montoIVA, 
        fac.Factura_Monto_Sin_IVA montoSinIVA
FROM    tb_01_usuario user inner join 
        tb_07_factura fac on user.Usuario_Codigo = fac.Factura_Vendedor_Codigo and 
                             user.TB_02_Sucursal_Codigo = fac.TB_02_Sucursal_Codigo inner join 
        tb_03_cliente cli on fac.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula
where   user.TB_02_Sucursal_Codigo = 0 and
        fac.Factura_Estado = 'cobrada' and 
        cli.Cliente_EsSucursal = 0 and
        UNIX_TIMESTAMP(fac.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP('2015-01-01 00:00:00') AND UNIX_TIMESTAMP('2015-04-09 00:00:00')   		                             
        
		
