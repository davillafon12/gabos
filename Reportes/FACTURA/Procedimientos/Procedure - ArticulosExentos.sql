USE garotas_bonitas_main_db; 
DELIMITER ;;
CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_ArticulosExentos
(
	IN paFechaI VARCHAR(30), 
	IN paFechaF VARCHAR(30),
	IN paSucursal VARCHAR(10)
 )
 BEGIN
	SET @wherePrincipal 		= CONCAT(' where   fac.Factura_Vendedor_Sucursal = ', '\'', paSucursal, '\'', ' and fac.factura_estado <> \'anulada\'  and artf.Articulo_Factura_Exento = 1 ');
	SET @SUBQUERY = CONCAT('select  artf.Articulo_Factura_Codigo,
									artf.Articulo_Factura_Descripcion, 
									fac.Factura_Consecutivo, 
									artf.Articulo_Factura_Cantidad, 
									artf.Articulo_Factura_Precio_Final 
							from    tb_07_factura fac 
									inner join tb_08_articulos_factura artf on 
								  fac.Factura_Consecutivo = artf.TB_07_Factura_Factura_Consecutivo and 
								  fac.Factura_Vendedor_Codigo = artf.TB_07_Factura_Factura_Vendedor_Codigo and 
								  fac.TB_03_Cliente_Cliente_Cedula = artf.TB_07_Factura_TB_03_Cliente_Cliente_Cedula and 
								  fac.TB_02_Sucursal_Codigo = artf.TB_07_Factura_TB_02_Sucursal_Codigo and 
								  fac.Factura_Vendedor_Sucursal = artf.TB_07_Factura_Factura_Vendedor_Sucursal ');	  
	SET @QUERY 			= CONCAT( 'select  count(t.Articulo_Factura_Cantidad) as Cantidad, 
											t.Articulo_Factura_Descripcion as Descripcion, 
											t.Articulo_Factura_Codigo as Codigo, 
											t.Articulo_Factura_Precio_Final PrecioFinal, 
											sum(t.Articulo_Factura_Precio_Final) as PrecioTotalFinal 
									from ( ');	
	IF paFechaI <> 'null' AND paFechaF <> 'null' then 
		SET @wherePrincipal = CONCAT (@wherePrincipal, ' AND UNIX_TIMESTAMP(fac.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'',paFechaI, '\'', ') AND UNIX_TIMESTAMP(', '\'',paFechaF, '\')');      
	END IF;
	SET @SUBQUERY = CONCAT(@SUBQUERY, @wherePrincipal);
	
	SET @QUERY = CONCAT(@QUERY, @SUBQUERY, ' ) as t group by t.Articulo_Factura_Codigo');
  -- select @QUERY as 'Resultado';  
   PREPARE smpt FROM @Query;
  -- ejecutamos el Statement
   EXECUTE smpt;
  -- liberamos la memoria 
   DEALLOCATE PREPARE smpt;
 END
;;