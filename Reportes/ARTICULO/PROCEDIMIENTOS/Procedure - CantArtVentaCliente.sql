USE garotas_bonitas_main_db; 
DELIMITER ;;
CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_CantArtVentaCliente
(
	IN paSucursal VARCHAR(10),
	IN paFechaI VARCHAR(30), 
	IN paFechaF VARCHAR(30),	
	IN paCodigoI VARCHAR(20), 
	IN paCedula VARCHAR(20), 
	IN paSuDesamparados VARCHAR(10),
	IN paSuGarotasBonitas VARCHAR(10)
 )
 BEGIN
	SET @wherePrincipal 		= CONCAT(' where   f.Factura_Vendedor_Sucursal =  ', '\'', paSucursal, '\'', 
								 ' and f.factura_estado <> \'anulada\' ',
								 ' and af.Articulo_Factura_Codigo = ', '\'', paCodigoI, '\'' , 
								 ' and UNIX_TIMESTAMP(f.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
								 ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')'); 
	SET @QUERY 			= CONCAT( 'select  af.Articulo_Factura_Codigo, 
											af.Articulo_Factura_Descripcion, 
											sum(af.Articulo_Factura_Cantidad) 
									from    tb_07_factura f 
											inner join tb_08_articulos_factura af on 
													  f.Factura_Consecutivo = af.TB_07_Factura_Factura_Consecutivo and 
													  f.Factura_Vendedor_Codigo = af.TB_07_Factura_Factura_Vendedor_Codigo and 
													  f.Factura_Vendedor_Sucursal = af.TB_07_Factura_Factura_Vendedor_Sucursal and 
													  f.TB_03_Cliente_Cliente_Cedula = af.TB_07_Factura_TB_03_Cliente_Cliente_Cedula');	
	SET @QUERYDESAMPA =  CONCAT( ' inner join tb_46_relacion_desampa des on f.Factura_Consecutivo  = des.Consecutivo and ' , 
									' des.Documento = \'factura\'');
	SET @WHEREDESAMPA = CONCAT(' AND f.Factura_Consecutivo NOT IN (
								  SELECT
								  f.Factura_Consecutivo AS Factura_Consecutivo
								FROM  tb_07_factura f 
									  inner join tb_46_relacion_desampa des on f.Factura_Consecutivo  = des.Consecutivo and
									  des.Documento = \'factura\'', @wherePrincipal, ')');
	IF paCedula <> 'false' then 
		SET @wherePrincipal = CONCAT(@wherePrincipal, ' and f.TB_03_Cliente_Cliente_Cedula = ', '\'', paCedula, '\'');
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