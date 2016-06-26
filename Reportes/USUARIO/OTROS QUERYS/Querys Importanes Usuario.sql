SELECT  user.Usuario_Nombre nombre,
        user.Usuario_Apellidos apellidos,
        user.Usuario_Cedula cedula,
        user.Usuario_Celular celular,
        suc.Sucursal_Nombre nombreSucursal,
        user.Usuario_Rango puesto,
        case isnull(user.Usuario_Fecha_Cesantia) when 1 then 'Activo' else 'Inactivo' end as estado
FROM    tb_01_usuario user
        inner join tb_02_sucursal suc on user.TB_02_Sucursal_Codigo = suc.Codigo
		
		
		
		
SELECT  user.Usuario_Nombre nombre,
        user.Usuario_Apellidos apellidos,
        user.Usuario_Cedula cedula,
        user.Usuario_Celular celular,
        suc.Sucursal_Nombre nombreSucursal,
        user.Usuario_Rango puesto,
        case isnull(user.Usuario_Fecha_Cesantia) when 1 then 'Activo' else 'Inactivo' end as estado,
        user.Usuario_Fecha_Ingreso
FROM    tb_01_usuario user
        inner join tb_02_sucursal suc on user.TB_02_Sucursal_Codigo = suc.Codigo
WHERE  UNIX_TIMESTAMP(user.Usuario_Fecha_Ingreso) BETWEEN UNIX_TIMESTAMP('2014-01-01 00:00:00') AND UNIX_TIMESTAMP('2014-02-13 00:00:00')       