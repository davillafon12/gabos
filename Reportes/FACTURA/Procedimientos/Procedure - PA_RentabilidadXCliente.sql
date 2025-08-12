USE gabo_trueque; 
DELIMITER ;;
CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_RentabilidadXCliente
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
	SET @wherePrincipal 		= CONCAT(' where   fac.TB_02_Sucursal_Codigo =  ', '\'', paSucursal, '\'', 
								 ' and fac.Factura_Estado in (', paEstadoFactura, ') ',
								 ' and fac.Factura_Tipo_Pago in (', paTipoPago, ') ' , 
								 ' and UNIX_TIMESTAMP(fac.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
								 ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')'); 
	SET @QUERY 			= CONCAT( 'SELECT fac.factura_consecutivo as codigoFactura,
										   fac.tb_03_cliente_cliente_cedula as cedula,
										   fac.factura_nombre_cliente as nombre,
										   fac.Factura_Monto_Sin_IVA as BaseImp,
										   fac.Factura_Retencion as retencion,
										   ifnull((SELECT sum(pre.precio_monto * arfa.articulo_factura_cantidad) AS total
												   FROM   tb_08_articulos_factura arfa
														  INNER JOIN tb_11_precios pre ON pre.tb_06_articulo_articulo_codigo = arfa.articulo_factura_codigo
																	 AND pre.precio_numero = 0
												   WHERE  arfa.tb_07_factura_factura_consecutivo = fac.factura_consecutivo
														  AND arfa.tb_07_factura_tb_02_sucursal_codigo = ' ,paSucursal,  '
														  AND pre.TB_06_Articulo_TB_02_Sucursal_Codigo = ' ,paSucursal,  '), 0) AS
										   TotalCosto,
										   ( fac.factura_monto_total / 1.13 ) - (ifnull((SELECT Sum( pre.precio_monto * arfa.articulo_factura_cantidad) AS total
													FROM tb_08_articulos_factura arfa
														 INNER JOIN tb_11_precios pre ON pre.tb_06_articulo_articulo_codigo = arfa.articulo_factura_codigo
																	AND pre.precio_numero = 0
													 WHERE arfa.tb_07_factura_factura_consecutivo = fac.factura_consecutivo
														 AND arfa.tb_07_factura_tb_02_sucursal_codigo = ' ,paSucursal,  '
														 AND pre.TB_06_Articulo_TB_02_Sucursal_Codigo = ' ,paSucursal,  '),0)) AS
										   TotalBeneficio,
										   ifnull(( ( ( fac.factura_monto_total / 1.13 ) - ((SELECT
													  Sum( pre.precio_monto * arfa.articulo_factura_cantidad) AS total
													  FROM tb_08_articulos_factura arfa
																 INNER JOIN tb_11_precios pre ON pre.tb_06_articulo_articulo_codigo = arfa.articulo_factura_codigo
																			AND pre.precio_numero = 0
													 WHERE
																 arfa.tb_07_factura_factura_consecutivo = fac.factura_consecutivo
																 AND arfa.tb_07_factura_tb_02_sucursal_codigo = ' ,paSucursal,  '
																 AND pre.TB_06_Articulo_TB_02_Sucursal_Codigo = ' ,paSucursal,  '))
													) /
															 (SELECT Sum(
															 pre.precio_monto * arfa.articulo_factura_cantidad)
																	 AS
																	 total
															  FROM
															 tb_08_articulos_factura arfa
															 INNER JOIN tb_11_precios pre
																	 ON pre.tb_06_articulo_articulo_codigo =
																		arfa.articulo_factura_codigo
																		AND pre.precio_numero = 0
															 WHERE
															 arfa.tb_07_factura_factura_consecutivo =
															 fac.factura_consecutivo
															 AND arfa.tb_07_factura_tb_02_sucursal_codigo = ' ,paSucursal,  '
															 AND pre.TB_06_Articulo_TB_02_Sucursal_Codigo = ' ,paSucursal,  ' ) ) *
												  100, 0) AS
										   margenbeneficio,
										   fac.factura_monto_total TotalNeto,
										   fac.Factura_Retencion retencion
									FROM   tb_07_factura fac ');	
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