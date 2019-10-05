USE garotas_bonitas_main_db; 
DELIMITER ;;

CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_ConsultaArticulosVendidos
(
	IN paSucursal VARCHAR(10),
	IN paFechaI VARCHAR(30), 
	IN paFechaF VARCHAR(30),
	IN paCodigo VARCHAR(20), 
	IN paTop VARCHAR(20), 
	IN paDESC VARCHAR(10), 
	IN paASC VARCHAR(10)
 )
 BEGIN
	-- Creación de Variables propias del Procedimiento almacenado, para la creación dinamica del 
	-- query que se formara a partir de la información que se halla enviado al Procedimiento almacenado
	SET @QUERY 			= CONCAT( 'SELECT t.Articulo_Factura_Codigo , SUM(t.Articulo_Factura_Cantidad) , t.Articulo_Factura_Descripcion
									FROM (
										  SELECT  distinct
												  fac.Factura_Consecutivo,  
												  artf.Articulo_Factura_Codigo,
												  artf.Articulo_Factura_Cantidad,
												  artf.Articulo_Factura_Descripcion
										  FROM    tb_07_factura fac 
												  inner join tb_08_articulos_factura artf on fac.Factura_Consecutivo = artf.TB_07_Factura_Factura_Consecutivo
														and fac.TB_02_Sucursal_Codigo = artf.TB_07_Factura_TB_02_Sucursal_Codigo 
														and fac.Factura_Vendedor_Codigo = artf.TB_07_Factura_Factura_Vendedor_Codigo 
														and fac.TB_03_Cliente_Cliente_Cedula = artf.TB_07_Factura_TB_03_Cliente_Cliente_Cedula
										  ) AS t');	
	-- Contrucción Query Interno  
	IF paSucursal <> 'null' then 
		SET @whereInterno = CONCAT(' where fac.TB_02_Sucursal_Codigo = ', '\'', paSucursal, '\''); 
	end If; 
	IF paFechaI <> 'null' then 
		IF paFechaF <> 'null' then 
			SET @whereInterno = CONCAT(@whereInterno, ' and UNIX_TIMESTAMP(fac.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
			') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')'); 
		end If; -- Fin del If paFechaF <> 'null'
	end If; -- Fin del If paFechaI <> 'null'
	IF paCodigo <> 'null' then 
		SET @whereInterno = CONCAT (@whereInterno, ' artf.Articulo_Factura_Codigo = ', paCodigo);      
	end If;
	IF paTop <> 'null' THEN 
		SET @QUERY = CONCAT(@QUERY, ' LIMIT ',paTop);     
	END IF;   										
    SET @AGRUPACION 	= CONCAT(' GROUP BY t.Articulo_Factura_Codigo
									order by SUM(t.Articulo_Factura_Cantidad)'); 	
 
     
  -- CONSTRUCCIÓN WHERE RANGO CODIGOS ------------------------------------------------------------------
  IF paRangoC <> 'null' then 
	IF paRangoC = 'menorIgual' then
		SET @QUERY = CONCAT (@QUERY, 'AND art.Articulo_Codigo <= ', '\'',paCodigoI, '\'');      
	ELSE 
		IF paRangoC = 'mayorIgual' then
			SET @QUERY = CONCAT (@QUERY, 'AND art.Articulo_Codigo >= ', '\'',paCodigoI, '\'');      
		ELSE 
			IF paRangoC = 'between' then
				SET @QUERY = CONCAT (@QUERY, 'AND art.Articulo_Codigo BETWEEN ', '\'',paCodigoI, '\'', ' AND ', '\'',paCodigoF, '\'');      
			end if; 
		end if; -- FIN paRangoC = 'mayorIgual'	
	end if; -- FIN paRangoC = 'menorIgual' 
  end If;   -- FIN paRangoC <> 'null'
  -- CONSTRUCCIÓN WHERE RANGO CODIGOS ------------------------------------------------------------------
  IF paNumPrecio <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, @NUMPRECIO, '\'',paNumPrecio, '\''); 
	IF paRangoP <> 'null' then 
		IF paRangoP = 'menorIgual' then
			SET @QUERY = CONCAT (@QUERY, 'AND pre2.Precio_Monto <= ', '\'',paPrecioI, '\'', '))');      
		ELSE 
			IF paRangoP = 'mayorIgual' then
				SET @QUERY = CONCAT (@QUERY, 'AND pre2.Precio_Monto >= ', '\'',paPrecioI, '\'', '))');      
			ELSE 
				IF paRangoP = 'between' then
					SET @QUERY = CONCAT (@QUERY, 'AND pre2.Precio_Monto  BETWEEN ', '\'',paPrecioI, '\'', ' AND ', '\'',paPrecioF, '\'', '))');      
				end if; 
			end if; -- FIN paRangoP = 'mayorIgual'	
		end if; -- FIN paRangoP = 'menorIgual' 
	end If;   -- FIN paRangoP <> 'null'
  end If; -- FIN paNumPrecio <> 'null'  
  -- CONSTRUCCIÓN WHERE RANGO CANTIDADES ARTICULOS ----------------------------------------------
	IF paRangoCant <> 'null' then 
		IF paRangoCant = 'menorIgual' then
			SET @QUERY = CONCAT (@QUERY, 'AND art.Articulo_Cantidad_Inventario  <= ', '\'',paCantidadI, '\'');      
		ELSE 
			IF paRangoCant = 'mayorIgual' then
				SET @QUERY = CONCAT (@QUERY, 'AND art.Articulo_Cantidad_Inventario  >= ', '\'',paCantidadI, '\'');      
			ELSE 
				IF paRangoCant = 'between' then
					SET @QUERY = CONCAT (@QUERY, 'AND art.Articulo_Cantidad_Inventario  BETWEEN ', '\'',paCantidadI, '\'', ' AND ', '\'',paCantidadF, '\'');      
				end if; 
			end if; -- FIN paRangoCant = 'mayorIgual'	
		end if; -- FIN paRangoCant = 'menorIgual' 
	end If;   -- FIN paRangoCant <> 'null'       
  -- CONSTRUCCIÓN WHERE RANGO CANTIDADES ARTICULOS DEFECTUOSAS----------------------------------
	IF paRangoDef <> 'null' then 
		IF paRangoDef = 'menorIgual' then
			SET @QUERY = CONCAT (@QUERY, 'AND art.Articulo_Cantidad_Defectuoso  <= ', '\'',paCantidadDefI, '\'');      
		ELSE 
			IF paRangoDef = 'mayorIgual' then
				SET @QUERY = CONCAT (@QUERY, 'AND art.Articulo_Cantidad_Defectuoso  >= ', '\'',paCantidadDefI, '\'');      
			ELSE 
				IF paRangoDef = 'between' then
					SET @QUERY = CONCAT (@QUERY, 'AND art.Articulo_Cantidad_Defectuoso  BETWEEN ', '\'',paCantidadDefI, '\'', ' AND ', '\'',paCantidadDefF, '\'');      
				end if; 
			end if; -- FIN paRangoDef = 'mayorIgual'	
		end if; -- FIN paRangoDef = 'menorIgual' 
	end If; -- FIN paRangoDef <> 'null'
  IF paExento <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, @EXENTO, '\'',paExento, '\'');      
  end If;     
  SET @QUERY = CONCAT (@QUERY, @AGRUPACION);   
  -- select @QUERY as 'Resultado'; 
  -- preparamos el objete Statement a partir de nuestra variable
  PREPARE smpt FROM @Query;
  -- ejecutamos el Statement
  EXECUTE smpt;
  -- liberamos la memoria
  DEALLOCATE PREPARE smpt;
 END
;;
