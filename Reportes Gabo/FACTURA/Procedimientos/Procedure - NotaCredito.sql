USE gabo_trueque; 
DELIMITER ;;
CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_NotaCredito
(
	IN paFechaI VARCHAR(30), 
	IN paFechaF VARCHAR(30),
	IN paSucursal VARCHAR(10),
	IN paCedula VARCHAR(30),
	IN paNombre VARCHAR(100),
	IN paSuDesamparados VARCHAR(10),
	IN paSuGarotasBonitas VARCHAR(10)
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
											(SELECT ifnull(Sum((lpNoCre.Precio_Final - (lpNoCre.Precio_Final / 1.13))-(lpNoCre.Precio_Unitario - (lpNoCre.Precio_Unitario / 1.13))), 0) as Total
													  FROM    tb_28_productos_notas_credito lpNoCre 
														INNER JOIN tb_27_notas_credito cre ON lpNoCre.nota_credito_consecutivo = cre.consecutivo AND lpNoCre.sucursal = cre.sucursal 
														INNER JOIN tb_03_cliente cli on cre.Cliente = cli.Cliente_Cedula 
													  WHERE   lpNoCre.Nota_Credito_Consecutivo = noCre.Consecutivo and lpNoCre.Sucursal = noCre.Sucursal  and lpNoCre.No_Retencion = 0 and cli.Aplica_Retencion = 1) as Retencion,		  
											(SELECT  (Sum(lpNoCre.Cantidad_Bueno * lpNoCre.Precio_Unitario) + Sum(lpNoCre.Cantidad_Defectuoso * lpNoCre.Precio_Unitario))
														+ ((lpNoCre.Precio_Final - (lpNoCre.Precio_Final / 1.13))-(lpNoCre.Precio_Unitario - (lpNoCre.Precio_Unitario / 1.13)))
														- (SELECT ifnull(Sum((lpNoCre.Precio_Final - (lpNoCre.Precio_Final / 1.13))-(lpNoCre.Precio_Unitario - (lpNoCre.Precio_Unitario / 1.13))), 0) as Total
															  FROM    tb_28_productos_notas_credito lpNoCre 
																INNER JOIN tb_27_notas_credito cre ON lpNoCre.nota_credito_consecutivo = cre.consecutivo AND lpNoCre.sucursal = cre.sucursal 
																INNER JOIN tb_03_cliente cli on cre.Cliente = cli.Cliente_Cedula 
															  WHERE   lpNoCre.Nota_Credito_Consecutivo = noCre.Consecutivo and lpNoCre.Sucursal = noCre.Sucursal  and lpNoCre.No_Retencion = 0 and cli.Aplica_Retencion = 1) as Total												  												  														  
													  FROM    tb_28_productos_notas_credito lpNoCre 
													  WHERE   lpNoCre.Nota_Credito_Consecutivo = noCre.Consecutivo and lpNoCre.Sucursal = noCre.Sucursal) as Total
									FROM    tb_03_cliente cli 
											inner join tb_27_notas_credito noCre  
											  on cli.Cliente_Cedula = noCre.Cliente
											inner join tb_28_productos_notas_credito pNoCre 
											  on pNoCre.Nota_Credito_Consecutivo = noCre.Consecutivo 
											  and pNoCre.Sucursal = noCre.Sucursal');	

	SET @QUERYDESAMPA =  CONCAT( ' inner join tb_46_relacion_trueque des on noCre.Consecutivo = des.Consecutivo and   ' , 
									' des.Documento = \'nota_credito\'');
	SET @WHEREDESAMPA = CONCAT(' AND noCre.Consecutivo NOT IN (
								  SELECT
								  noCre.Consecutivo AS Consecutivo
								FROM  tb_27_notas_credito noCre 
									  inner join tb_46_relacion_trueque des on noCre.Consecutivo  = des.Consecutivo and
									  des.Documento = \'nota_credito\' and des.Sucursal= \'7\' )');											  
	IF paCedula <> 'null' THEN
		SET @wherePrincipal = CONCAT(@wherePrincipal, ' AND cli.Cliente_Cedula = ', '\'',paCedula, '\'' );
	END IF;
	IF paNombre <> 'null' THEN 
		SET @wherePrincipal = CONCAT(@wherePrincipal, ' AND concat(cli.Cliente_Nombre, \' \', cli.Cliente_Apellidos) LIKE  ', '\'',paNombre, '\'' );
	END IF; 
	IF paFechaI <> 'null' AND paFechaF <> 'null' then 
		SET @wherePrincipal = CONCAT (@wherePrincipal, ' AND UNIX_TIMESTAMP(noCre.Fecha_Creacion) BETWEEN UNIX_TIMESTAMP(', '\'',paFechaI, '\'', ') AND UNIX_TIMESTAMP(', '\'',paFechaF, '\')');      
	END IF;	
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'false' and (paSucursal = '2' or paSucursal = '7') then 
		SET @QUERY = CONCAT(@QUERY, @QUERYDESAMPA, @wherePrincipal);
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'true' and (paSucursal = '2' or paSucursal = '7') then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal, @WHEREDESAMPA);
	END IF;
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'true' and (paSucursal = '2' or paSucursal = '7') then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal);
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'false' and paSucursal <> '2' and paSucursal <> '7' then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal);
	END IF;
  -- select @QUERY as 'Resultado';  
  -- preparamos el objete Statement a partir de nuestra variable
   PREPARE smpt FROM @Query;
  -- ejecutamos el Statement
   EXECUTE smpt;
  -- liberamos la memoria 
   DEALLOCATE PREPARE smpt;
 END
;;