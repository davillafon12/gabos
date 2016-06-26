-- VENTA POR CLIENTES POR RANGOS DE FECHAS PROFORMA
-- NOTA: ESTA VENTA TRAE TODAS LAS PROFORMAS DE LOS CLIENTES Y TRAE TODOS LOS CLIENTES

USE garotas_bonitas_main_db; 
DELIMITER ;;

CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_VentaXClienteProformaResumido
(
	IN paSucursal VARCHAR(10),
	IN paFechaI VARCHAR(30), 
	IN paFechaF VARCHAR(30),
	IN paEstadoProforma VARCHAR(30),
	IN paEsSucursal VARCHAR(30),
	IN paNombre VARCHAR(50), 
	IN paCedula VARCHAR(20), 
	IN paRango VARCHAR(10), 
	IN paMontoI VARCHAR(20), 
	IN paMontoF VARCHAR(20)
 )
 BEGIN
 	set @WhereGenerico  = CONCAT(' where pro2.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula and 
										   pro2.TB_02_Sucursal_Codigo = ', '\'', paSucursal, '\'',' 
										   and pro2.Proforma_Estado =', '\'', paEstadoProforma, '\'', 
										   ' and UNIX_TIMESTAMP(pro2.Proforma_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
										  ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')'); 
										  
	SET @where2 		= CONCAT(' where   cli.Cliente_EsSucursal = ', '\'', paEsSucursal, '\'', 
								 ' and pro.TB_02_Sucursal_Codigo = ', '\'', paSucursal, '\'',
								 'and pro.Proforma_Estado =', '\'', paEstadoProforma, '\'', 
								 'and UNIX_TIMESTAMP(pro.Proforma_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
								 ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')'); 
								 
	SET @QUERY 			= CONCAT( 'select  cli.Cliente_Cedula cedula,
											CONCAT(cli.Cliente_Nombre, " ", cli.Cliente_Apellidos) nombre,     
											pro.Proforma_Fecha_Hora fecha,
											pro.Proforma_Consecutivo consecutivo, 
											(select  Sum(pro2.Proforma_Monto_Total) v
											 from tb_10_proforma pro2',@WhereGenerico ,') montoTotal,
											(select  Sum(pro2.Proforma_Monto_IVA) v
											 from tb_10_proforma pro2',@WhereGenerico ,') montoIVA, 
											(select  Sum(pro2.Proforma_Monto_Sin_IVA) v
											 from tb_10_proforma pro2',@WhereGenerico ,') montoSinIVA, 
											(select  Sum(pro2.Proforma_Retencion) v
											 from tb_10_proforma pro2',@WhereGenerico ,') Proforma_Retencion      											 
									from    tb_03_cliente cli inner join 
											tb_10_proforma pro on pro.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula');		
  IF paSucursal <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, @where2);      
  end If; 
  IF paNombre <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, ' and pro.Proforma_Nombre_Cliente like ','\'','%',paNombre,'%', '\'');      
  end If;   
  IF paCedula <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, ' and cli.Cliente_Cedula =  ', '\'', paCedula, '\'');      
  end If;   
    -- CONSTRUCCIÓN WHERE RANGO CODIGOS ------------------------------------------------------------------
  IF paRango <> 'null' then 
	IF paRango = 'menorIgual' then
		SET @QUERY = CONCAT (@QUERY, ' and pro.protura_Monto_Total <= ', '\'',paMontoI, '\'');      
	ELSE 
		IF paRango = 'mayorIgual' then
			SET @QUERY = CONCAT (@QUERY, ' and pro.protura_Monto_Total >= ', '\'',paMontoI, '\'');      
		ELSE 
			IF paRango = 'between' then
				SET @QUERY = CONCAT (@QUERY, ' and pro.protura_Monto_Total BETWEEN ', '\'',paMontoI, '\'', ' AND ', '\'',paMontoF, '\'');      
			end if; 
		end if; -- FIN paRango = 'mayorIgual'	
	end if; -- FIN paRango = 'menorIgual' 
  end If;   -- FIN paRango <> 'null'
  SET @QUERY = CONCAT (@QUERY, ' group by cli.Cliente_Cedula'); 
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