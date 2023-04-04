USE gabo_trueque; 
DELIMITER ;;
CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_VentaXMes
(
	IN paEstadoFactura VARCHAR(30),
	IN paFechaI VARCHAR(30), 
	IN paFechaF VARCHAR(30),
	IN paSucursal VARCHAR(10),
	IN paSuDesamparados VARCHAR(10),
	IN paTipoPago VARCHAR(100),
	IN paSuGarotasBonitas VARCHAR(10)
 )
 BEGIN
	SET @wherePrincipal 		= CONCAT(' where   fac.TB_02_Sucursal_Codigo = ', '\'', paSucursal, '\'', 
								 ' and fac.Factura_Estado in (', paEstadoFactura, ') ',
								 ' and fac.Factura_Tipo_Pago in (', paTipoPago, ') ' , 
								 ' and UNIX_TIMESTAMP(fac.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
								 ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')'); 
	SET @QUERY 			= CONCAT( 'SELECT fac.Factura_Consecutivo AS Factura_Consecutivo,
									  fac.Factura_Nombre_Cliente AS Nombre_Cliente,
									  fac.Factura_Fecha_Hora AS Fecha_Hora,
									  fac.Factura_Monto_IVA AS Monto_IVA,
									  fac.Factura_Monto_Sin_IVA AS Monto_Sin_IVA,
									  fac.Factura_Retencion AS retencion,
									  fac.Factura_Monto_Total - fac.Factura_Retencion AS total,
									  fac.Factura_Monto_Total as TotalGlobal
									FROM  tb_07_factura fac ');	
	SET @QUERYDESAMPA =  CONCAT( ' inner join tb_46_relacion_trueque des on fac.Factura_Consecutivo  = des.Consecutivo and ' , 
									' des.Documento = \'factura\'');
	SET @WHEREDESAMPA = CONCAT(' AND fac.Factura_Consecutivo NOT IN (
								  SELECT
								  fac.Factura_Consecutivo AS Factura_Consecutivo
								FROM  tb_07_factura fac 
									  inner join tb_46_relacion_trueque des on fac.Factura_Consecutivo  = des.Consecutivo and
									  des.Documento = \'factura\'', @wherePrincipal, ')');
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