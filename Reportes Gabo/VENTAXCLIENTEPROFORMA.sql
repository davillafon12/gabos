-- VENTA POR CLIENTES POR RANGOS DE FECHAS PROFORMA
-- NOTA: ESTA VENTA TRAE TODAS LAS PROFORMAS DE LOS CLIENTES Y TRAE TODOS LOS CLIENTES
        
select  cli.Cliente_Cedula cedula,
        CONCAT(cli.Cliente_Nombre, " ", cli.Cliente_Apellidos) nombre,     
        pro.Proforma_Fecha_Hora fecha,
        pro.Proforma_Consecutivo consecutivo, 
        pro.Proforma_Monto_Total montoTotal, 
        pro.Proforma_Monto_IVA montoIVA, 
        pro.Proforma_Monto_Sin_IVA montoSinIVA
from    tb_03_cliente cli inner join 
        tb_10_proforma pro on pro.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula
		
		
-- NOTA: ESTA VENTA TRAE TODOS LOS MONTOS DE LAS PROFORMAS DE LOS CLIENTES	
		
select  cli.Cliente_Cedula cedula, 
        CONCAT(cli.Cliente_Nombre, " ", cli.Cliente_Apellidos) nombre,
        (select  Sum(fac2.Factura_Monto_Total) v
         from tb_07_factura fac2 
         where fac2.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula and 
               fac2.TB_02_Sucursal_Codigo = 0) montoTotal,
        (select  Sum(fac2.Factura_Monto_IVA) v2
         from tb_07_factura fac2 
         where fac2.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula and 
               fac2.TB_02_Sucursal_Codigo = 0) montoIVA,
        (select  Sum(fac2.Factura_Monto_Sin_IVA) v3
         from tb_07_factura fac2 
         where fac2.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula and 
               fac2.TB_02_Sucursal_Codigo = 0) montoSinIVA    
from    tb_03_cliente cli inner join 
        tb_07_factura fac on fac.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula
where   cli.Cliente_EsSucursal = 0 and 
        fac.TB_02_Sucursal_Codigo = 0 and 
        fac.Factura_Estado = 'cobrada' and 
        UNIX_TIMESTAMP(fac.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP('2015-01-01 00:00:00') AND UNIX_TIMESTAMP('2015-04-09 00:00:00')   		
group by cli.Cliente_Cedula