USE garotas_bonitas_main_db; 
DELIMITER ;;

CREATE DEFINER = 'consulta'@'localhost' PROCEDURE PA_ConsultaUsuarios
(
	IN paSucursal VARCHAR(10),
	IN paFechaI VARCHAR(30), 
	IN paFechaF VARCHAR(30)
 )
 BEGIN
	SET @SUCURSAL  		= CONCAT(' WHERE user.TB_02_Sucursal_Codigo =  '); 
	SET @QUERY 			= CONCAT( 'SELECT  user.Usuario_Nombre nombre,
											user.Usuario_Apellidos apellidos,
											user.Usuario_Cedula cedula,
											user.Usuario_Celular celular,
											suc.Sucursal_Nombre nombreSucursal,
											user.Usuario_Rango puesto,
											case isnull(user.Usuario_Fecha_Cesantia) when 1 then \'Activo\' else \'Inactivo\' end as estado,
											user.Usuario_Fecha_Ingreso
									FROM    tb_01_usuario user
											inner join tb_02_sucursal suc on user.TB_02_Sucursal_Codigo = suc.Codigo');		
  IF paSucursal <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, @SUCURSAL, '\'',paSucursal, '\'');      
  end If; 
  IF paFechaI <> 'null' AND paFechaF <> 'null' then 
	SET @QUERY = CONCAT (@QUERY, 'AND UNIX_TIMESTAMP(user.Usuario_Fecha_Ingreso) BETWEEN UNIX_TIMESTAMP(', '\'',paFechaI, '\'', ') AND UNIX_TIMESTAMP(', '\'',paFechaF, '\')');      
  end If; 
  -- select @QUERY as 'Resultado';  
  -- preparamos el objete Statement a partir de nuestra variable
  PREPARE smpt FROM @Query;
  -- ejecutamos el Statement
  EXECUTE smpt;
  -- liberamos la memoria
  DEALLOCATE PREPARE smpt;
 END
;;
