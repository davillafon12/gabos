-- NOTA: ESTA VENTA TRAE TODOS LOS MONTOS DE LOS CLIENTES	
	
USE garotas_bonitas_main_db; 
DELIMITER ;;

CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_VentaXClienteFacturasResumido
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
	set @WhereGenerico  = CONCAT(' where fac2.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula and 
										   fac.TB_02_Sucursal_Codigo = ', '\'', paSucursal, '\'',' 
										   and fac.Factura_Estado =', '\'', paEstadoFactura, '\'', 
										   ' and UNIX_TIMESTAMP(fac2.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
										  ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')'); 
	SET @where2 		= CONCAT(' where   cli.Cliente_EsSucursal = ', '\'', paEsSucursal, '\'', 
								 'and fac.TB_02_Sucursal_Codigo = ', '\'', paSucursal, '\'',
								 'and fac.Factura_Estado =', '\'', paEstadoFactura, '\'', 
								 'and UNIX_TIMESTAMP(fac.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
								 ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')'); 
 IF paSucursal <> 'null' then 								 
	SET @QUERY 			= CONCAT( 'select  cli.Cliente_Cedula cedula, 
									CONCAT(cli.Cliente_Nombre, " ", cli.Cliente_Apellidos) nombre,
									(select  Sum(fac2.Factura_Monto_Total) v
									 from tb_07_factura fac2',@WhereGenerico ,') montoTotal,
									(select  Sum(fac2.Factura_Monto_IVA) v2
									 from tb_07_factura fac2',@WhereGenerico, ' ) montoIVA,
									(select  Sum(fac2.Factura_Monto_Sin_IVA) v3
									 from tb_07_factura fac2',@WhereGenerico, ' ) montoSinIVA,  
									(select  Sum(fac2.Factura_Retencion) v3
									 from tb_07_factura fac2',@WhereGenerico, ' ) retencion       
							from    tb_03_cliente cli inner join 
									tb_07_factura fac on fac.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula');		
ELSE
	set @QUERY 			= CONCAT('select  cli.Cliente_Cedula cedula, 
									CONCAT(cli.Cliente_Nombre, " ", cli.Cliente_Apellidos) nombre,
									fac.Factura_Monto_Total as montoTotal,
									fac.Factura_Monto_IVA as montoIVA,
									fac.Factura_Monto_Sin_IVA as montoSinIVA,  
									fac.Factura_Retencion as retencion       
							from    tb_03_cliente cli inner join 
									tb_07_factura fac on fac.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula');								
end If; 
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
		SET @QUERY = CONCAT (@QUERY, 'and (select  Sum(fac2.Factura_Monto_Total) v
										 from tb_07_factura fac2',
										 @WhereGenerico, ') <= ', '\'', paMontoI, '\'');      
	ELSE 
		IF paRango = 'mayorIgual' then
			SET @QUERY = CONCAT (@QUERY, 'and (select  Sum(fac2.Factura_Monto_Total) v
										 from tb_07_factura fac2',
										 @WhereGenerico, ') >= ', '\'', paMontoI, '\'');      
		ELSE 
			IF paRango = 'between' then
				SET @QUERY = CONCAT (@QUERY, 'and (select  Sum(fac2.Factura_Monto_Total) v
										 from tb_07_factura fac2',
										 @WhereGenerico, ') BETWEEN ', '\'',paMontoI, '\'', ' AND ', '\'',paMontoF, '\'');      
			end if; 
		end if; -- FIN paRango = 'mayorIgual'	
	end if; -- FIN paRango = 'menorIgual' 
  end If;   -- FIN paRango <> 'null'
  IF paSucursal <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, 'group by cli.Cliente_Cedula');   
  end If;  
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