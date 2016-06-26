select  cli.Cliente_Cedula cedula,
        CONCAT(cli.Cliente_Nombre, " ", cli.Cliente_Apellidos) nombre,     
        cli.Cliente_Telefono telefono, 
        cli.Cliente_Correo_Electronico correo, 
        cli.Cliente_Celular celular, 
        cli.Cliente_Estado
from    tb_03_cliente cli 
where   cli.Cliente_Estado = 'activo'