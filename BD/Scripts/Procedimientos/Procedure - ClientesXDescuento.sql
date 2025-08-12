-- VENTA POR CLIENTES POR RANGOS DE FECHAS proTURA 
-- NOTA: ESTA VENTA TRAE TODAS LAS proTURAS DE LOS CLIENTES Y TRAE TODOS LOS CLIENTES

USE garotas_bonitas_main_db; 
DELIMITER ;;

CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_ClientesXDescuento
(
	IN paSucursal VARCHAR(10),
    IN paCedula VARCHAR(20), 
    IN paArticulo VARCHAR(10), 
	IN paFamilia VARCHAR(20)
 )
 BEGIN						 																
	SET @QUERY 			= CONCAT( 'select  cli.Cliente_Cedula cedula, 
											Concat(cli.Cliente_Nombre, " ", cli.Cliente_Apellidos) nombre,
											descli.Descuento_cliente_porcentaje descuCliente,
											0 as codFamilia, 
											\'\' as nomFamilia, 
											0 as montoFamilia, 
											0 as porFamilia,
											\'\' as codArticulo,
											\'\' as nomArticulo, 
											0 as monArticulo, 
											0 as porcArticulo
									from    tb_03_cliente cli 
											inner join 
											tb_21_descuento_cliente descli 
											  on  descli.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula');		
  IF paSucursal <> 'null' and paSucursal <>'' then 
    SET @QUERY = CONCAT (@QUERY, ' and descli.TB_02_Sucursal_Codigo = ', paSucursal);      
  end If; 
  IF paFamilia <> 'null' and paFamilia <>'' then 
    SET @QUERY = CONCAT (@QUERY, ' union              
									select  cli.Cliente_Cedula cedula, 
											Concat(cli.Cliente_Nombre, " ", cli.Cliente_Apellidos) nombre, 
											0 as descuCliente,
											fam.Familia_Codigo codFamilia,
											fam.Familia_Nombre  nomFamilia,
											desFam.Descuento_familia_monto montoFamilia, 
											desFam.Descuento_familia_porcentaje porFamilia,
											0 as codArticulo, 
											\'\' as nomArticulo, 
											0 as monArticulo, 
											0 as porcArticulo
									from    tb_03_cliente cli 
											inner join 
											tb_20_descuento_familia desFam 
											  on  cli.Cliente_Cedula = desFam.TB_03_Cliente_Cliente_Cedula and 
												  desFam.TB_05_Familia_TB_02_Sucursal_Codigo = ',paSucursal,'
											 inner join 
											tb_05_familia fam 
											  on  fam.Familia_Codigo = desFam.TB_05_Familia_Familia_Codigo and 
												  fam.TB_02_Sucursal_Codigo = ', paSucursal);      
  end If;  
  IF paArticulo <> 'null' and paArticulo <>'' then 
    SET @QUERY = CONCAT (@QUERY, ' union 
									select  cli.Cliente_Cedula cedula, 
											Concat(cli.Cliente_Nombre, " ", cli.Cliente_Apellidos) nombre,
											0 as descuCliente,
											0 as codFamilia, 
											\'\' as nomFamilia, 
											0 as montoFamilia, 
											0 as porFamilia,
											art.Articulo_Codigo codArticulo,
											art.Articulo_Descripcion nomArticulo, 
											prod.Descuento_producto_monto monArticulo, 
											prod.Descuento_producto_porcentaje porcArticulo
									from    tb_03_cliente cli 
											left join 
											tb_17_descuento_producto prod 
											  on  prod.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula and 
												  prod.TB_02_Sucursal_Codigo = ',paSucursal,'
											 inner join 
											tb_06_articulo art 
											  on  art.Articulo_Codigo = prod.TB_06_Articulo_Articulo_Codigo and 
												  art.TB_02_Sucursal_Codigo = ',paSucursal);      
  end If;   
  IF paCedula <> 'null' and paCedula <>'' then 
    SET @QUERY = CONCAT ('select * from ( ', @QUERY, ') a 
									where a.cedula = ',paCedula,'
 order by cedula, codArticulo, codFamilia');      
  end If;   
  -- select @QUERY as 'Resultado';  
  -- preparamos el objete Statement a partir de nuestra variable
  PREPARE smpt FROM @Query;
  -- ejecutamos el Statement
  EXECUTE smpt;
  -- liberamos la memoria
  DEALLOCATE PREPARE smpt;
 END
;;