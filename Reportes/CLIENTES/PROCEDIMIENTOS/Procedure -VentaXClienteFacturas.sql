DROP PROCEDURE `gabo_fe`.`PA_VentaXClienteFacturas`;

USE gabo_fe; 
DELIMITER ;;
CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_VentaXClienteFacturas
(
	IN paSucursal VARCHAR(10),
	IN paFechaI VARCHAR(30), 
	IN paFechaF VARCHAR(30),
	IN paEstadoFactura VARCHAR(100),
	IN paEsSucursal VARCHAR(30),
	IN paNombre VARCHAR(50), 
	IN paCedula VARCHAR(20), 
	IN paRango VARCHAR(10), 
	IN paMontoI VARCHAR(20), 
	IN paMontoF VARCHAR(20),
	IN paSuDesamparados VARCHAR(10),
	IN paSuGarotasBonitas VARCHAR(10)
 )
 BEGIN
	SET @CodigoDesamparados = ' 7 '; 
	
	SET @where 		= CONCAT(' where cli.Cliente_EsSucursal = ', '\'', paEsSucursal, '\'', 
								 ' and fac.TB_02_Sucursal_Codigo = ', '\'', paSucursal, '\'',
								 ' and fac.Factura_Estado IN(', paEstadoFactura,')', 
								 ' and UNIX_TIMESTAMP(fac.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
								 ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')'); 
	SET @whereUsoDesamparados 		= CONCAT(' where   cli.Cliente_EsSucursal = ', '\'', paEsSucursal, '\'', 
								 ' and fac.TB_02_Sucursal_Codigo = ', '\'', paSucursal, '\'',
								 ' and fac.Factura_Estado IN (', paEstadoFactura, ')',
								 ' and UNIX_TIMESTAMP(fac.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
								 ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')');
	SET @QUERY 			= CONCAT( 'select  cli.Cliente_Cedula cedula, 
											CONCAT(cli.Cliente_Nombre, " ", cli.Cliente_Apellidos) nombre,
											fac.Factura_Fecha_Hora fecha,
											fac.Factura_Consecutivo consecutivo, 
											fac.Factura_Monto_Total montoTotal, 
											fac.Factura_Monto_IVA montoIVA, 
											fac.Factura_Monto_Sin_IVA montoSinIVA,
											fac.Factura_Retencion,
											suc.Sucursal_Nombre
									from    tb_03_cliente cli inner join 
											tb_07_factura fac on cli.Cliente_Cedula = fac.TB_03_Cliente_Cliente_Cedula ');		
	SET @AgregarSucursal = (' inner join tb_02_sucursal suc on fac.tb_02_sucursal_codigo = suc.Codigo ');	  
	SET @wherePrinGarotas =	CONCAT(' and  fac.TB_02_Sucursal_Codigo = ', paSucursal);		
	SET @wherePrinDesampa =	CONCAT(' and  fac.Factura_Vendedor_Sucursal = ',@CodigoDesamparados);			
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'false' then 
		SET @QUERY = CONCAT(@QUERY,@wherePrinDesampa, @AgregarSucursal);
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'true' then 
		SET @QUERY = CONCAT(@QUERY, @wherePrinGarotas, @AgregarSucursal);
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'false' then 
		SET @QUERY = CONCAT(@QUERY, @wherePrinGarotas, @AgregarSucursal);
	END IF;
	IF paNombre <> 'null' then 
    SET @where = CONCAT(@where, 'and fac.Factura_Nombre_Cliente like ','\'','%',paNombre,'%', '\'');      
	end If;   
	IF paCedula <> 'null' then 
		SET @where = CONCAT(@where, 'and cli.Cliente_Cedula =  ', '\'', paCedula, '\'');      
	end If;   
	-- CONSTRUCCIÓN WHERE RANGO CODIGOS ------------------------------------------------------------------
	IF paRango <> 'null' then 
		IF paRango = 'menorIgual' then
			SET @where = CONCAT(@where, 'and fac.Factura_Monto_Total <= ', '\'',paMontoI, '\'');      
		ELSE 
			IF paRango = 'mayorIgual' then
				SET @where = CONCAT(@where, 'and fac.Factura_Monto_Total >= ', '\'',paMontoI, '\'');      
			ELSE 
				IF paRango = 'between' then
					SET @where = CONCAT(@where, 'and fac.Factura_Monto_Total BETWEEN ', '\'',paMontoI, '\'', ' AND ', '\'',paMontoF, '\'');      
				end if; 
			end if; -- FIN paRango = 'mayorIgual'	
		end if; -- FIN paRango = 'menorIgual' 
	end If;   -- FIN paRango <> 'null'
	-- CONSTRUCCIÓN WHERE RANGO CODIGOS ------------------------------------------------------------------                    
    SET @QUERYDESAMPA =  CONCAT( ' inner join tb_46_relacion_trueque des on fac.Factura_Consecutivo  = des.Consecutivo and ' , 
									' des.Documento = \'factura\'');
	SET @WHEREDESAMPA = CONCAT(' AND fac.Factura_Consecutivo NOT IN (
								  SELECT
								  fac.Factura_Consecutivo AS Factura_Consecutivo
								FROM  tb_07_factura fac 
									  inner join tb_46_relacion_trueque des on fac.Factura_Consecutivo  = des.Consecutivo and
									  des.Documento = \'factura\'', @whereUsoDesamparados, ')');
									  
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'false' then 
		SET @QUERY = CONCAT(@QUERY, @QUERYDESAMPA, @where, ' order by fac.Factura_Fecha_Hora');
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'true' then 
		SET @QUERY = CONCAT(@QUERY, @where, @WHEREDESAMPA, ' order by fac.Factura_Fecha_Hora');
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'false' then 
		SET @QUERY = CONCAT(@QUERY, @where, ' order by fac.Factura_Fecha_Hora');
	END IF;
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'true' then 
		SET @QUERY = CONCAT(@QUERY,@AgregarSucursal, @where, ' order by fac.Factura_Fecha_Hora');
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