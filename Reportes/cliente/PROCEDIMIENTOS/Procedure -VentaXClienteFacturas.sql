-- VENTA POR CLIENTES POR RANGOS DE FECHAS FACTURA 
-- NOTA: ESTA VENTA TRAE TODAS LAS FACTURAS DE LOS CLIENTES Y TRAE TODOS LOS CLIENTES

USE garotas_bonitas_main_db; 
DELIMITER ;;

CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_VentaXClienteFacturas
(
	IN paSucursal VARCHAR(10),
	IN paFechaI VARCHAR(30), 
	IN paFechaF VARCHAR(30),
	IN paEstadoFactura VARCHAR(30),
	IN paEsSucursal VARCHAR(30),
	IN paNombre VARCHAR(50), 
	IN paCedula VARCHAR(20), 
	IN paRango VARCHAR(10), 
	IN paMontoI VARCHAR(20), 
	IN paMontoF VARCHAR(20)
 )
 BEGIN
	SET @where2 		= CONCAT(' where   cli.Cliente_EsSucursal = ', '\'', paEsSucursal, '\'', 
								 ' and fac.TB_02_Sucursal_Codigo = ', '\'', paSucursal, '\'',
								 ' and fac.Factura_Estado =', '\'', paEstadoFactura, '\'', 
								 ' and UNIX_TIMESTAMP(fac.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
								 ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')'); 
	SET @QUERY 			= CONCAT( 'select  cli.Cliente_Cedula cedula, 
											CONCAT(cli.Cliente_Nombre, " ", cli.Cliente_Apellidos) nombre,
											fac.Factura_Fecha_Hora fecha,
											fac.Factura_Consecutivo consecutivo, 
											fac.Factura_Monto_Total montoTotal, 
											fac.Factura_Monto_IVA montoIVA, 
											fac.Factura_Monto_Sin_IVA montoSinIVA,
											fac.Factura_Retencion
									from    tb_03_cliente cli inner join 
											tb_07_factura fac on fac.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula');		
  IF paSucursal <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, @where2);      
  end If; 
  IF paNombre <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, 'and fac.Factura_Nombre_Cliente like ','\'','%',paNombre,'%', '\'');      
  end If;   
  IF paCedula <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, 'and cli.Cliente_Cedula =  ', '\'', paCedula, '\'');      
  end If;   
    -- CONSTRUCCIÓN WHERE RANGO CODIGOS ------------------------------------------------------------------
  IF paRango <> 'null' then 
	IF paRango = 'menorIgual' then
		SET @QUERY = CONCAT (@QUERY, 'and fac.Factura_Monto_Total <= ', '\'',paMontoI, '\'');      
	ELSE 
		IF paRango = 'mayorIgual' then
			SET @QUERY = CONCAT (@QUERY, 'and fac.Factura_Monto_Total >= ', '\'',paMontoI, '\'');      
		ELSE 
			IF paRango = 'between' then
				SET @QUERY = CONCAT (@QUERY, 'and fac.Factura_Monto_Total BETWEEN ', '\'',paMontoI, '\'', ' AND ', '\'',paMontoF, '\'');      
			end if; 
		end if; -- FIN paRango = 'mayorIgual'	
	end if; -- FIN paRango = 'menorIgual' 
  end If;   -- FIN paRango <> 'null'
  -- CONSTRUCCIÓN WHERE RANGO CODIGOS ------------------------------------------------------------------  
  -- select @QUERY as 'Resultado';  
  -- preparamos el objete Statement a partir de nuestra variable
  PREPARE smpt FROM @Query;
  -- ejecutamos el Statement
  EXECUTE smpt;
  -- liberamos la memoria
  DEALLOCATE PREPARE smpt;
 END
;;



-- select  cli.Cliente_Cedula cedula, 
--        CONCAT(cli.Cliente_Nombre, " ", cli.Cliente_Apellidos) nombre,
--        fac.Factura_Fecha_Hora fecha,
--        fac.Factura_Consecutivo consecutivo, 
--        fac.Factura_Monto_Total montoTotal, 
--        fac.Factura_Monto_IVA montoIVA, 
--        fac.Factura_Monto_Sin_IVA montoSinIVA,
--         fac.Factura_Retencion
-- from    tb_03_cliente cli inner join 
--         tb_07_factura fac on fac.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula
-- where   cli.Cliente_EsSucursal = 0 and 
--         fac.TB_02_Sucursal_Codigo = 0 and 
--        fac.Factura_Estado = 'cobrada' and 
--        UNIX_TIMESTAMP(fac.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP('2015-01-01 00:00:00') AND UNIX_TIMESTAMP('2015-04-09 00:00:00')   
		
		

		
		