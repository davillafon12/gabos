USE garotas_bonitas_main_db; 
DELIMITER ;;
CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_ProcedenciaArticulo
(
	IN paSucursal VARCHAR(10),
	IN paFechaI VARCHAR(30), 
	IN paFechaF VARCHAR(30),	
	IN paCodigoI VARCHAR(20)
 )
 BEGIN
	SET @wherePrincipal 		= CONCAT(' where   t.Codigo =  ', '\'', paCodigoI, '\'', 
								 ' and t.CodSucursal = ', '\'', paSucursal, '\'' , 
								 ' and UNIX_TIMESTAMP(t.Fecha) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
								 ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')'); 
	SET @QUERY 			= CONCAT( '
				select 	 t.id, 
						 t.Fecha, 
						 t.CodSucursal, 
						 t.`Sucursal Recibe`,
						 t.Codigo,
						 t.Cantidad,
						 t.Procedencia,
						 t.CantidadCambio
						from (
						select  ti.id, 
								ti.Fecha as \'Fecha\', 
								s.Codigo as \'CodSucursal\',
								s.Sucursal_Nombre as \'Sucursal Recibe\' , 
								ati.Codigo,
								ati.Cantidad, 
								\'Traspaso Inventario\' as \'Procedencia\',
								ifnull((select sum(acc.Cantidad) from tb_36_articulos_cambio_codigo acc where acc.Articulo_Abonado = ati.Codigo ), 0) as \'CantidadCambio\'
						from    tb_52_traspaso_inventario ti 
								inner join tb_02_sucursal s on ti.Sucursal_Recibe = s.Codigo
								inner join tb_53_articulos_traspaso_inventario ati on ti.Id = ati.Traspaso 
						union
						select  con.id, 
								con.Fecha_Hora as \'Fecha\', 
								s.Codigo as \'CodSucursal\',
								s.Sucursal_Nombre as \'Sucursal Recibe\', 
								aRc.Codigo, 
								aRc.Cantidad, 
								\'Consignación\' as \'Procedencia\',
								ifnull((select sum(acc.Cantidad) from tb_36_articulos_cambio_codigo acc where acc.Articulo_Abonado = aRc.Codigo ), 0) as \'CantidadCambio\'
						from    tb_49_consignacion con
								inner join tb_02_sucursal s on con.Sucursal_Recibe = s.Codigo
								inner join tb_50_articulos_consignacion aRc on con.id = aRc.Consignacion
						union
						select  tii.id, 
								tii.Fecha as \'Fecha\', 
								s.Codigo as \'CodSucursal\',
								s.Sucursal_Nombre as \'Sucursal Recibe\', 
								atii.Codigo, 
								atii.Cantidad,
								\'Factura Compra\' as \'Procedencia\',
								ifnull((select sum(acc.Cantidad) from tb_36_articulos_cambio_codigo acc where acc.Articulo_Abonado = atii.Codigo ), 0) as \'CantidadCambio\'
						from    tb_44_traspaso_inventario tii        
								inner join tb_02_sucursal s on tii.Sucursal_Entrada = s.Codigo
								inner join tb_45_articulos_traspaso_inventario atii on tii.Id = atii.Traspaso
						) as t ');
	SET @QUERY = CONCAT(@QUERY, @wherePrincipal, ' order by t.Fecha');
  -- select @QUERY as 'Resultado';  
  -- preparamos el objete Statement a partir de nuestra variable
   PREPARE smpt FROM @Query;
  -- ejecutamos el Statement
   EXECUTE smpt;
  -- liberamos la memoria 
   DEALLOCATE PREPARE smpt;
 END
;;