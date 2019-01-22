DROP PROCEDURE `gabo_fe`.`PA_ClientesEstado`;

USE gabo_fe; 
DELIMITER ;;

CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_ClientesEstado
(
    IN paEstado VARCHAR(100),
    IN paSucursalIngreso VARCHAR(100)    
 )
 BEGIN	
	SET @QUERY 	= CONCAT( '
				select  cli.Cliente_Cedula cedula,
					CONCAT(cli.Cliente_Nombre, " ", cli.Cliente_Apellidos) nombre,     
					cli.Cliente_Telefono telefono, 
					cli.Cliente_Correo_Electronico correo, 
					cli.Cliente_Celular celular, 
					cli.Cliente_Estado, 
					suc.Codigo,
					suc.Sucursal_Nombre
					from    tb_03_cliente cli 
				inner join tb_02_sucursal suc on cli.Sucursal_Ingreso = suc.Codigo
					where   cli.Cliente_Estado IN(', paEstado ,')  and 
							Sucursal_Ingreso IN (', paSucursalIngreso ,')	order by cli.Cliente_Fecha_Ingreso'); 
  
  -- select @QUERY as 'Resultado';  
  -- preparamos el objete Statement a partir de nuestra variable
   PREPARE smpt FROM @Query;
  -- ejecutamos el Statement
   EXECUTE smpt;
  -- liberamos la memoria
   DEALLOCATE PREPARE smpt;							
 END
;;