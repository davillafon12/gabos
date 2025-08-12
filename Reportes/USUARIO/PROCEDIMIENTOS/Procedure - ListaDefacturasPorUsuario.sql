DROP PROCEDURE `gabo_fe`.`PA_ListaDeFacturasPorUsuario`;


USE gabo_fe; 
DELIMITER ;;
CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_ListaDeFacturasPorUsuario(
	IN paEstadoFactura VARCHAR(30),
	IN paFechaI VARCHAR(30), 
	IN paFechaF VARCHAR(30),
	IN paSucursal VARCHAR(10),
	IN paEsSucursal VARCHAR(10),
	IN paSuDesamparados VARCHAR(10),
	IN paSuGarotasBonitas VARCHAR(10)
 )
BEGIN
	SET @wherePrincipal 		= CONCAT(' where   user.TB_02_Sucursal_Codigo = ', '\'', paSucursal, '\'', 
								 ' and fac.Factura_Estado in (',paEstadoFactura,') ',
								 ' and UNIX_TIMESTAMP(fac.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
								 ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')'); 
	SET @whereCliente = CONCAT(' and cli.Cliente_EsSucursal = ', '\'', paEsSucursal, '\'');								 
	SET @QUERY 			= CONCAT( ' SELECT  user.Usuario_Nombre_Usuario usuario, 
											CONCAT(user.Usuario_Nombre, \' \', user.Usuario_Apellidos) nombre,
											fac.Factura_Consecutivo consecutivo, 
											fac.Factura_Fecha_Hora fecha, 
											fac.Factura_Monto_Total montoTotal, 
											fac.Factura_Monto_IVA montoIVA, 
											fac.Factura_Monto_Sin_IVA montoSinIVA, 
											suc.Sucursal_Nombre
									FROM    tb_01_usuario user inner join 
											tb_07_factura fac on user.Usuario_Codigo = fac.Factura_Vendedor_Codigo ');
	SET @AgregarCliente = (' inner join tb_03_cliente cli on fac.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula  ');
	SET @AgregarSucursal = (' inner join tb_02_sucursal suc on fac.tb_02_sucursal_codigo = suc.Codigo ');	 
	SET @wherePrinGarotas =	CONCAT(' and  user.TB_02_Sucursal_Codigo = fac.TB_02_Sucursal_Codigo ', @AgregarCliente);	
	SET @wherePrinDesampa =	CONCAT(' and  user.TB_02_Sucursal_Codigo = fac.Factura_Vendedor_Sucursal ', @AgregarCliente);			
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'false' then 
		SET @QUERY = CONCAT(@QUERY,@wherePrinDesampa, @AgregarSucursal);
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'true' then 
		SET @QUERY = CONCAT(@QUERY, @wherePrinGarotas, @AgregarSucursal);
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'false' then 
		SET @QUERY = CONCAT(@QUERY, @wherePrinGarotas, @AgregarSucursal);
	END IF;
	SET @QUERYDESAMPA =  CONCAT( ' inner join tb_46_relacion_trueque des on fac.Factura_Consecutivo  = des.Consecutivo and ' , 
									' des.Documento = \'factura\'');
	SET @WHEREDESAMPA = CONCAT(' AND fac.Factura_Consecutivo NOT IN (
								  SELECT
								  fac.Factura_Consecutivo AS Factura_Consecutivo
								FROM  tb_07_factura fac 
									  inner join tb_46_relacion_trueque des on fac.Factura_Consecutivo  = des.Consecutivo and
									  des.Documento = \'factura\'', @wherePrincipal, ')');
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'false' then 
		SET @QUERY = CONCAT(@QUERY, @QUERYDESAMPA, @wherePrincipal, @whereCliente, ' order by user.Usuario_Codigo, fac.Factura_Fecha_Hora');
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'true' then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal, @whereCliente, @WHEREDESAMPA, ' order by user.Usuario_Codigo, fac.Factura_Fecha_Hora');
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'false' then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal, @whereCliente, ' order by user.Usuario_Codigo, fac.Factura_Fecha_Hora');
	END IF;
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'true' then 
		SET @QUERY = CONCAT(@QUERY,@AgregarSucursal,@AgregarCliente, @wherePrincipal, @whereCliente, ' order by user.Usuario_Codigo, fac.Factura_Fecha_Hora');
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
