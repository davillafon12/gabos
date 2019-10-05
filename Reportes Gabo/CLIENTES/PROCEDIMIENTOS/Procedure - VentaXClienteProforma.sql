DROP PROCEDURE `gabo_fe`.`PA_VentaXClienteProforma`;

USE gabo_fe; 
DELIMITER ;;

CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_VentaXClienteProforma
(
	IN paSucursal VARCHAR(10),
	IN paFechaI VARCHAR(30), 
	IN paFechaF VARCHAR(30),
	IN paEstadoProforma VARCHAR(100),
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
	
	SET @where 		= CONCAT(' where   cli.Cliente_EsSucursal = ', '\'', paEsSucursal, '\'', 
								 'and pro.TB_02_Sucursal_Codigo = ', '\'', paSucursal, '\'',
								 'and pro.Proforma_Estado IN (', paEstadoProforma, 
								 ') and UNIX_TIMESTAMP(pro.Proforma_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
								 ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')'); 
	SET @whereUsoDesamparados 		= CONCAT(' where   cli.Cliente_EsSucursal = ', '\'', paEsSucursal, '\'', 
								 ' and pro2.TB_02_Sucursal_Codigo = ', '\'', paSucursal, '\'',
								 ' and pro2.Proforma_Estado IN (', paEstadoProforma, ')',
								 ' and UNIX_TIMESTAMP(pro2.Proforma_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
								 ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')');								 
	SET @QUERY 			= CONCAT( 'select  cli.Cliente_Cedula cedula,
											CONCAT(cli.Cliente_Nombre, " ", cli.Cliente_Apellidos) nombre,     
											pro.Proforma_Fecha_Hora fecha,
											pro.Proforma_Consecutivo consecutivo, 
											pro.Proforma_Monto_Total montoTotal, 
											pro.Proforma_Monto_IVA montoIVA, 
											pro.Proforma_Monto_Sin_IVA montoSinIVA, 
											pro.Proforma_Retencion,
											suc.Sucursal_Nombre
									from    tb_03_cliente cli inner join 
											tb_10_proforma pro on cli.Cliente_Cedula = pro.TB_03_Cliente_Cliente_Cedula');	
	SET @AgregarSucursal = (' inner join tb_02_sucursal suc on pro.tb_02_sucursal_codigo = suc.Codigo ');	  
	SET @wherePrinGarotas =	CONCAT(' and  pro.TB_02_Sucursal_Codigo = ', paSucursal);		
	SET @wherePrinDesampa =	CONCAT(' and  pro.Proforma_Vendedor_Sucursal = ',@CodigoDesamparados);			
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
    SET @QUERY = CONCAT (@QUERY, 'and pro.Proforma_Nombre_Cliente like ','\'','%',paNombre,'%', '\'');      
  end If;   
  IF paCedula <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, 'and cli.Cliente_Cedula =  ', '\'', paCedula, '\'');      
  end If;   
    -- CONSTRUCCIÓN WHERE RANGO CODIGOS ------------------------------------------------------------------
  IF paRango <> 'null' then 
	IF paRango = 'menorIgual' then
		SET @QUERY = CONCAT (@QUERY, 'and pro.Proforma_Monto_Total <= ', '\'',paMontoI, '\'');      
	ELSE 
		IF paRango = 'mayorIgual' then
			SET @QUERY = CONCAT (@QUERY, 'and pro.Proforma_Monto_Total >= ', '\'',paMontoI, '\'');      
		ELSE 
			IF paRango = 'between' then
				SET @QUERY = CONCAT (@QUERY, 'and pro.Proforma_Monto_Total BETWEEN ', '\'',paMontoI, '\'', ' AND ', '\'',paMontoF, '\'');      
			end if; 
		end if; -- FIN paRango = 'mayorIgual'	
	end if; -- FIN paRango = 'menorIgual' 
  end If;   -- FIN paRango <> 'null'
  -- CONSTRUCCIÓN WHERE RANGO CODIGOS ------------------------------------------------------------------  
  SET @QUERYDESAMPA =  CONCAT( ' inner join tb_46_relacion_trueque des on pro.Proforma_Consecutivo  = des.Consecutivo and ' , 
									' des.Documento = \'proforma\'');
	SET @WHEREDESAMPA = CONCAT(' AND pro.Proforma_Consecutivo NOT IN (
								  SELECT
								  pro2.Proforma_Consecutivo AS Proforma_Consecutivo
								FROM  tb_10_proforma pro2 
									  inner join tb_46_relacion_trueque des on pro2.Proforma_Consecutivo  = des.Consecutivo and
									  des.Documento = \'proforma\'', @whereUsoDesamparados, ')');
									  
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'false' then 
		SET @QUERY = CONCAT(@QUERY, @QUERYDESAMPA, @where, ' order by pro.Proforma_Fecha_Hora');
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'true' then 
		SET @QUERY = CONCAT(@QUERY, @where, @WHEREDESAMPA, ' order by pro.Proforma_Fecha_Hora');
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'false' then 
		SET @QUERY = CONCAT(@QUERY, @where, ' order by pro.Proforma_Fecha_Hora');
	END IF;
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'true' then 
		SET @QUERY = CONCAT(@QUERY,@AgregarSucursal, @where, ' order by pro.Proforma_Fecha_Hora');
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