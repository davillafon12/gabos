USE garotas_bonitas_main_db; 
DELIMITER ;;
CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_NotaCredito
(
	IN paFechaI VARCHAR(30), 
	IN paFechaF VARCHAR(30),
	IN paSucursal VARCHAR(10),
	IN paCedula VARCHAR(30),
	IN paNombre VARCHAR(100)
 )
 BEGIN
	SET @wherePrincipal 		= CONCAT(' where   noCre.Sucursal = ', '\'', paSucursal, '\'');
	
	SET @QUERY 			= CONCAT( 'SELECT DISTINCT cli.Cliente_Cedula,
											concat(cli.Cliente_Nombre, \' \', cli.Cliente_Apellidos) as Nombre,          
											noCre.Fecha_Creacion, 
											noCre.Factura_Aplicar, 
											noCre.Consecutivo, 
											noCre.Tipo_Pago, 
											noCre.Factura_Acreditar, 
											(SELECT  Sum(lpNoCre.Cantidad_Defectuoso * lpNoCre.Precio_Unitario) as MontoDefectuoso
															  FROM    tb_28_productos_notas_credito lpNoCre 
															  WHERE   lpNoCre.Nota_Credito_Consecutivo = noCre.Consecutivo and lpNoCre.Sucursal = noCre.Sucursal) as MontoDefectuoso, 
											(SELECT  Sum(lpNoCre.Cantidad_Bueno * lpNoCre.Precio_Unitario) as MontoDefectuoso
													  FROM    tb_28_productos_notas_credito lpNoCre 
													  WHERE   lpNoCre.Nota_Credito_Consecutivo = noCre.Consecutivo and lpNoCre.Sucursal = noCre.Sucursal) as MontoBueno, 
											(SELECT Sum((lpNoCre.Precio_Final - (lpNoCre.Precio_Final / 1.13))-(lpNoCre.Precio_Unitario - (lpNoCre.Precio_Unitario / 1.13)))as Total
													  FROM    tb_28_productos_notas_credito lpNoCre 
													  WHERE   lpNoCre.Nota_Credito_Consecutivo = noCre.Consecutivo and lpNoCre.Sucursal = noCre.Sucursal) as Retencion,		  
											(SELECT  (Sum(lpNoCre.Cantidad_Bueno * lpNoCre.Precio_Unitario) + Sum(lpNoCre.Cantidad_Defectuoso * lpNoCre.Precio_Unitario))
														+ ((lpNoCre.Precio_Final - (lpNoCre.Precio_Final / 1.13))-(lpNoCre.Precio_Unitario - (lpNoCre.Precio_Unitario / 1.13)))as Total
													  FROM    tb_28_productos_notas_credito lpNoCre 
													  WHERE   lpNoCre.Nota_Credito_Consecutivo = noCre.Consecutivo and lpNoCre.Sucursal = noCre.Sucursal) as Total
									FROM    tb_03_cliente cli 
											inner join tb_27_notas_credito noCre  
											  on cli.Cliente_Cedula = noCre.Cliente
											inner join tb_28_productos_notas_credito pNoCre 
											  on pNoCre.Nota_Credito_Consecutivo = noCre.Consecutivo and 
											  pNoCre.Sucursal = noCre.Sucursal ');	
	IF paCedula <> 'null' THEN
		SET @wherePrincipal = CONCAT(@wherePrincipal, ' AND cli.Cliente_Cedula = ', '\'',paCedula, '\'' );
	END IF;
	IF paNombre <> 'null' THEN 
		SET @wherePrincipal = CONCAT(@wherePrincipal, ' AND concat(cli.Cliente_Nombre, \' \', cli.Cliente_Apellidos) LIKE  ', '\'',paNombre, '\'' );
	END IF; 
	IF paFechaI <> 'null' AND paFechaF <> 'null' then 
		SET @wherePrincipal = CONCAT (@wherePrincipal, ' AND UNIX_TIMESTAMP(noCre.Fecha_Creacion) BETWEEN UNIX_TIMESTAMP(', '\'',paFechaI, '\'', ') AND UNIX_TIMESTAMP(', '\'',paFechaF, '\')');      
	END IF;
	SET @QUERY = CONCAT(@QUERY, @wherePrincipal);
  -- select @QUERY as 'Resultado';  
  -- preparamos el objete Statement a partir de nuestra variable
   PREPARE smpt FROM @Query;
  -- ejecutamos el Statement
   EXECUTE smpt;
  -- liberamos la memoria 
   DEALLOCATE PREPARE smpt;
 END
;;