USE garotas_bonitas_main_db; 
DELIMITER ;;

CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_ConsultaArticulos
(
	IN paSucursal VARCHAR(10),
	IN paFamilia VARCHAR(10), 
	IN paRangoC VARCHAR(20), 
	IN paCodigoI VARCHAR(20), 
	IN paCodigoF VARCHAR(20), 
	IN paNumPrecio VARCHAR(10), 
	IN paRangoP VARCHAR(20), 
	IN paPrecioI VARCHAR(20), 
	IN paPrecioF VARCHAR(20), 
	IN paRangoCant VARCHAR(20), 
	IN paCantidadI VARCHAR(20),
	IN paCantidadF VARCHAR(20),
	IN paRangoDef VARCHAR(20), 
	IN paCantidadDefI VARCHAR(20), 
	IN paCantidadDefF VARCHAR(20), 
	IN paExento VARCHAR(10)
 )
 BEGIN
	-- Creación de Variables propias del Procedimiento almacenado, para la creación dinamica del 
	-- query que se formara a partir de la información que se halla enviado al Procedimiento almacenado
	SET @SUCURSAL  		= CONCAT(' WHERE art.TB_02_Sucursal_Codigo =  '); 
	SET @FAMILIA  		= CONCAT(' AND art.TB_05_Familia_Familia_Codigo = '); 
	SET @NUMPRECIO  	= CONCAT(' AND art.Articulo_Codigo in ((select  pre2.TB_06_Articulo_Articulo_Codigo
																from 	tb_11_precios pre2
																where   pre2.Precio_Numero = '); 
	SET @EXENTO 		= CONCAT(' AND art.Articulo_Exento =  '); 
	SET @QUERY 			= CONCAT( 'SELECT  art.Articulo_Codigo Codigo, 
											art.Articulo_Descripcion descripcion,
											art.Articulo_Cantidad_Inventario CantInventario, 
											art.Articulo_Cantidad_Defectuoso CantDefectuoso, 
											art.Articulo_Exento Exento, 
											art.TB_05_Familia_Familia_Codigo FamCodigo, 
											art.TB_02_Sucursal_Codigo SucCodigo,
											art.Articulo_Descuento descuento,
											sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-0)))) as precio0, 
											sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-1)))) as precio1, 
											sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-2)))) as precio2, 
											sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-3)))) as precio3, 
											sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-4)))) as precio4, 
											sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-5)))) as precio5        
									FROM tb_06_articulo art
											inner join tb_11_precios pre on 
											art.Articulo_Codigo = pre.TB_06_Articulo_Articulo_Codigo and 
											art.TB_05_Familia_Familia_Codigo = pre.TB_06_Articulo_TB_05_Familia_Familia_Codigo and 
											art.TB_02_Sucursal_Codigo = pre.TB_06_Articulo_TB_02_Sucursal_Codigo ');	
    SET @AGRUPACION 	= CONCAT(' group by art.Articulo_Codigo'); 	
  IF paSucursal <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, @SUCURSAL, '\'',paSucursal, '\'');      
  end If; 
  IF paFamilia <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, @FAMILIA, '\'',paFamilia, '\'');      
  end If;   
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
