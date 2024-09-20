 DELIMITER $$
CREATE DEFINER=`gabo_admin`@`%` PROCEDURE `PA_ArticulosExentos`(IN `paFechaI` VARCHAR(30), IN `paFechaF` VARCHAR(30), IN `paSucursal` VARCHAR(10))
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
  
   PREPARE smpt FROM @Query;
  
   EXECUTE smpt;
  
   DEALLOCATE PREPARE smpt;
 END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`gabo_admin`@`%` PROCEDURE `PA_CantArtVentaCliente`(IN `paSucursal` VARCHAR(10), IN `paFechaI` VARCHAR(30), IN `paFechaF` VARCHAR(30), IN `paCodigoI` VARCHAR(20), IN `paCedula` VARCHAR(20), IN `paSuDesamparados` VARCHAR(10), IN `paSuGarotasBonitas` VARCHAR(10))
BEGIN
	SET @wherePrincipal 		= CONCAT(' where   f.Factura_Vendedor_Sucursal =  ', '\'', paSucursal, '\'', 
								 ' and f.factura_estado <> \'anulada\' ',
								 ' and af.Articulo_Factura_Codigo = ', '\'', paCodigoI, '\'' , 
								 ' and UNIX_TIMESTAMP(f.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
								 ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')'); 
	SET @QUERY 			= CONCAT( 'select  af.Articulo_Factura_Codigo, 
											af.Articulo_Factura_Descripcion, 
											sum(af.Articulo_Factura_Cantidad) 
									from    tb_07_factura f 
											inner join tb_08_articulos_factura af on 
													  f.Factura_Consecutivo = af.TB_07_Factura_Factura_Consecutivo and 
													  f.Factura_Vendedor_Codigo = af.TB_07_Factura_Factura_Vendedor_Codigo and 
													  f.Factura_Vendedor_Sucursal = af.TB_07_Factura_Factura_Vendedor_Sucursal and 
													  f.TB_03_Cliente_Cliente_Cedula = af.TB_07_Factura_TB_03_Cliente_Cliente_Cedula');	
	SET @QUERYDESAMPA =  CONCAT( ' inner join tb_46_relacion_trueque des on f.Factura_Consecutivo  = des.Consecutivo and ' , 
									' des.Documento = \'factura\'');
	SET @WHEREDESAMPA = CONCAT(' AND f.Factura_Consecutivo NOT IN (
								  SELECT
								  f.Factura_Consecutivo AS Factura_Consecutivo
								FROM  tb_07_factura f 
									  inner join tb_46_relacion_trueque des on f.Factura_Consecutivo  = des.Consecutivo and
									  des.Documento = \'factura\'', @wherePrincipal, ')');
	IF paCedula <> 'false' then 
		SET @wherePrincipal = CONCAT(@wherePrincipal, ' and f.TB_03_Cliente_Cliente_Cedula = ', '\'', paCedula, '\'');
	END IF;
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'false' and (paSucursal = '2' or paSucursal = '7') then 
		SET @QUERY = CONCAT(@QUERY, @QUERYDESAMPA, @wherePrincipal);
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'true' and (paSucursal = '2' or paSucursal = '7') then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal, @WHEREDESAMPA);
	END IF;
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'true' and (paSucursal = '2' or paSucursal = '7') then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal);
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'false' and paSucursal <> '2' and paSucursal <> '7' then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal);
	END IF;
  
  
   PREPARE smpt FROM @Query;
  
   EXECUTE smpt;
  
   DEALLOCATE PREPARE smpt;
 END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`gabo_admin`@`%` PROCEDURE `PA_Cartelera`(IN `paFechaI` VARCHAR(30), IN `paFechaF` VARCHAR(30), IN `paSucursal` VARCHAR(10), IN `paCedula` VARCHAR(30), IN `paNombre` VARCHAR(100))
BEGIN
	SET @wherePrincipal 		= CONCAT(' where   cre.Credito_Sucursal_Codigo = ', '\'', paSucursal, '\'', ' and f.factura_estado <> \'anulada\' and cre.Credito_Saldo_Actual > 0 ');
	
	SET @QUERY 			= CONCAT( 'SELECT  distinct f.Factura_Consecutivo,  
										f.Factura_Fecha_Hora,
										DATE_ADD(f.factura_fecha_hora, INTERVAL cre.Credito_Numero_Dias DAY) as Fecha_Final,
										c.Cliente_Cedula,
										CONCAT(c.Cliente_Nombre, \' \', c.Cliente_Apellidos) as Nombre, 
										cre.Credito_Saldo_Inicial,
										cre.Credito_Saldo_Actual
										FROM tb_07_factura f 
								  inner join tb_03_cliente c on f.TB_03_Cliente_Cliente_Cedula = c.Cliente_Cedula
								  left join tb_24_credito cre on cre.Credito_Factura_Consecutivo = f.Factura_Consecutivo and 
											cre.Credito_Sucursal_Codigo = f.TB_02_Sucursal_Codigo and 
											cre.credito_vendedor_codigo = f.Factura_Vendedor_Codigo and 
											cre.Credito_Vendedor_Sucursal = f.Factura_Vendedor_Sucursal and 
											cre.credito_cliente_cedula = f.TB_03_Cliente_Cliente_Cedula 
								  left join tb_26_recibos_dinero recd on cre.Credito_Id = recd.Credito ');	
	IF paCedula <> 'null' THEN
		SET @wherePrincipal = CONCAT(@wherePrincipal, ' AND c.Cliente_Cedula = ', '\'',paCedula, '\'' );
	END IF;
	IF paNombre <> 'null' THEN 						   
		SET @wherePrincipal = CONCAT(@wherePrincipal, ' AND concat(c.Cliente_Nombre, \' \', c.Cliente_Apellidos) LIKE  ','\'',paNombre, '\'');
	END IF; 
	IF paFechaI <> 'null' AND paFechaF <> 'null' then 
		SET @wherePrincipal = CONCAT (@wherePrincipal, ' AND UNIX_TIMESTAMP(f.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'',paFechaI, '\'', ') AND UNIX_TIMESTAMP(', '\'',paFechaF, '\')');      
	END IF;
	SET @QUERY = CONCAT(@QUERY, @wherePrincipal, ' order by c.Cliente_Cedula, f.Factura_Fecha_Hora');
  
  
   PREPARE smpt FROM @Query;
  
   EXECUTE smpt;
  
   DEALLOCATE PREPARE smpt;
 END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`gabo_admin`@`%` PROCEDURE `PA_CarteleraSubReporte`(IN `paFechaI` VARCHAR(30), IN `paFechaF` VARCHAR(30), IN `paSucursal` VARCHAR(10), IN `paCedula` VARCHAR(30), IN `paNombre` VARCHAR(100))
BEGIN
	SET @wherePrincipal 		= CONCAT(' where   cre.Credito_Sucursal_Codigo = ', '\'', paSucursal, '\'', ' and f.factura_estado <> \'anulada\' and cre.Credito_Saldo_Actual > 0 ');
	
	SET @QUERY 			= CONCAT( 'SELECT  distinct f.Factura_Consecutivo,  
										f.Factura_Fecha_Hora,
										DATE_ADD(f.factura_fecha_hora, INTERVAL cre.Credito_Numero_Dias DAY) as Fecha_Final,
										c.Cliente_Cedula,
										CONCAT(c.Cliente_Nombre, \' \', c.Cliente_Apellidos) as Nombre, 
										cre.Credito_Saldo_Inicial,
										cre.Credito_Saldo_Actual
										FROM tb_07_factura f 
								  inner join tb_03_cliente c on f.TB_03_Cliente_Cliente_Cedula = c.Cliente_Cedula
								  left join tb_24_credito cre on cre.Credito_Factura_Consecutivo = f.Factura_Consecutivo and 
											cre.Credito_Sucursal_Codigo = f.TB_02_Sucursal_Codigo and 
											cre.credito_vendedor_codigo = f.Factura_Vendedor_Codigo and 
											cre.Credito_Vendedor_Sucursal = f.Factura_Vendedor_Sucursal and 
											cre.credito_cliente_cedula = f.TB_03_Cliente_Cliente_Cedula 
								  left join tb_26_recibos_dinero recd on cre.Credito_Id = recd.Credito ');	
	IF paCedula <> 'null' THEN
		SET @wherePrincipal = CONCAT(@wherePrincipal, ' AND c.Cliente_Cedula = ', '\'',paCedula, '\'' );
	END IF;
	IF paNombre <> 'null' THEN 						   
		SET @wherePrincipal = CONCAT(@wherePrincipal, ' AND concat(c.Cliente_Nombre, \' \', c.Cliente_Apellidos) LIKE  ','\'',paNombre, '\'');
	END IF; 
	IF paFechaI <> 'null' AND paFechaF <> 'null' then 
		SET @wherePrincipal = CONCAT (@wherePrincipal, ' AND UNIX_TIMESTAMP(f.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'',paFechaI, '\'', ') AND UNIX_TIMESTAMP(', '\'',paFechaF, '\')');      
	END IF;
	SET @QUERY = CONCAT(@QUERY, @wherePrincipal, ' order by c.Cliente_Cedula, f.Factura_Fecha_Hora');
	
	SET @QUERY = CONCAT('select a.Cliente_Cedula,
						   a.Nombre, 
						   sum(a.Credito_Saldo_Inicial) as Credito_Saldo_Inicial, 
						   sum(a.Credito_Saldo_Actual) as Credito_Saldo_Actual
						  from ( ', @QUERY, ' ) as a group by a.Cliente_Cedula' );
  
  
   PREPARE smpt FROM @Query;
  
   EXECUTE smpt;
  
   DEALLOCATE PREPARE smpt;
 END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`gabo_admin`@`%` PROCEDURE `PA_ClientesXDescuento`(IN `paSucursal` VARCHAR(10), IN `paCedula` VARCHAR(20), IN `paArticulo` VARCHAR(10), IN `paFamilia` VARCHAR(20))
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
  
  
  PREPARE smpt FROM @Query;
  
  EXECUTE smpt;
  
  DEALLOCATE PREPARE smpt;
 END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`gabo_admin`@`%` PROCEDURE `PA_ConsultaArticulos`(IN `paSucursal` VARCHAR(10), IN `paFamilia` VARCHAR(10), IN `paRangoC` VARCHAR(20), IN `paCodigoI` VARCHAR(20), IN `paCodigoF` VARCHAR(20), IN `paNumPrecio` VARCHAR(10), IN `paRangoP` VARCHAR(20), IN `paPrecioI` VARCHAR(20), IN `paPrecioF` VARCHAR(20), IN `paRangoCant` VARCHAR(20), IN `paCantidadI` VARCHAR(20), IN `paCantidadF` VARCHAR(20), IN `paRangoDef` VARCHAR(20), IN `paCantidadDefI` VARCHAR(20), IN `paCantidadDefF` VARCHAR(20), IN `paExento` VARCHAR(10))
BEGIN
	
	
	SET @SUCURSAL  		= CONCAT(' WHERE art.TB_02_Sucursal_Codigo =  '); 
	SET @FAMILIA  		= CONCAT(' AND art.TB_05_Familia_Familia_Codigo = '); 
	SET @NUMPRECIO  	= CONCAT(' AND art.Articulo_Codigo in ((select  pre2.TB_06_Articulo_Articulo_Codigo
																from 	tb_11_precios pre2
																where   pre2.Precio_Numero = '); 
	SET @EXENTO 		= CONCAT(' AND art.Articulo_Exento =  '); 
	SET @QUERY 			= CONCAT( 'SELECT  art.Articulo_Codigo Codigo, 
											art.TB_05_Familia_Familia_Codigo as familia,
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
											sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-5)))) as precio5,
											art.Articulo_Cantidad_Inventario * sum(pre.Precio_Monto*(1-abs(sign(Precio_Numero-0)))) as Totalprecio0											
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
		end if; 
	end if; 
  end If;   
  
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
			end if; 
		end if; 
	end If;   
  end If; 
  
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
			end if; 
		end if; 
	end If;   
  
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
			end if; 
		end if; 
	end If; 
  IF paExento <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, @EXENTO, '\'',paExento, '\'');      
  end If;     
  SET @QUERY = CONCAT (@QUERY, @AGRUPACION);   
  
  
   PREPARE smpt FROM @Query;
  
   EXECUTE smpt;
  
   DEALLOCATE PREPARE smpt;
 END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`gabo_admin`@`%` PROCEDURE `PA_ConsultaUsuarios`(IN `paSucursal` VARCHAR(10), IN `paFechaI` VARCHAR(30), IN `paFechaF` VARCHAR(30))
BEGIN
	SET @SUCURSAL  		= CONCAT(' WHERE user.TB_02_Sucursal_Codigo =  '); 
	SET @QUERY 			= CONCAT( 'SELECT  user.Usuario_Nombre nombre,
											user.Usuario_Apellidos apellidos,
											user.Usuario_Cedula cedula,
											user.Usuario_Celular celular,
											suc.Sucursal_Nombre nombreSucursal,
											user.Usuario_Rango puesto,
											case isnull(user.Usuario_Fecha_Cesantia) when 1 then \'Activo\' else \'Inactivo\' end as estado,
											user.Usuario_Fecha_Ingreso
									FROM    tb_01_usuario user
											inner join tb_02_sucursal suc on user.TB_02_Sucursal_Codigo = suc.Codigo');		
  IF paSucursal <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, @SUCURSAL, '\'',paSucursal, '\'');      
  end If; 
  IF paFechaI <> 'null' AND paFechaF <> 'null' then 
	SET @QUERY = CONCAT (@QUERY, 'AND UNIX_TIMESTAMP(user.Usuario_Fecha_Ingreso) BETWEEN UNIX_TIMESTAMP(', '\'',paFechaI, '\'', ') AND UNIX_TIMESTAMP(', '\'',paFechaF, '\')');      
  end If; 
  
  
  PREPARE smpt FROM @Query;
  
  EXECUTE smpt;
  
  DEALLOCATE PREPARE smpt;
 END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`gabo_admin`@`%` PROCEDURE `PA_ListaDeFacturasPorUsuario`(IN `paEstadoFactura` VARCHAR(30), IN `paFechaI` VARCHAR(30), IN `paFechaF` VARCHAR(30), IN `paSucursal` VARCHAR(10), IN `paEsSucursal` VARCHAR(10), IN `paSuDesamparados` VARCHAR(10), IN `paSuGarotasBonitas` VARCHAR(10))
BEGIN
	SET @wherePrincipal 		= CONCAT(' where   user.TB_02_Sucursal_Codigo = ', '\'', paSucursal, '\'', 
								 ' and fac.Factura_Estado in (', paEstadoFactura, ') ',
								 ' and UNIX_TIMESTAMP(fac.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
								 ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')'); 
	SET @whereCliente = CONCAT(' and cli.Cliente_EsSucursal = ', '\'', paEsSucursal, '\'');								 
	SET @QUERY 			= CONCAT( ' SELECT  user.Usuario_Nombre_Usuario usuario, 
											CONCAT(user.Usuario_Nombre, \' \', user.Usuario_Apellidos) nombre,
											fac.Factura_Consecutivo consecutivo, 
											fac.Factura_Fecha_Hora fecha, 
											fac.Factura_Monto_Total montoTotal, 
											fac.Factura_Monto_IVA montoIVA, 
											fac.Factura_Monto_Sin_IVA montoSinIVA
									FROM    tb_01_usuario user inner join 
											tb_07_factura fac on user.Usuario_Codigo = fac.Factura_Vendedor_Codigo and ');
	SET @wherePrinGarotas =	(' user.TB_02_Sucursal_Codigo = fac.TB_02_Sucursal_Codigo inner join 
								tb_03_cliente cli on fac.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula ');	
	SET @wherePrinDesampa =	(' user.TB_02_Sucursal_Codigo = fac.Factura_Vendedor_Sucursal inner join 
								tb_03_cliente cli on fac.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula ');												
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'false' then 
		SET @QUERY = CONCAT(@QUERY,@wherePrinDesampa);
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'true' then 
		SET @QUERY = CONCAT(@QUERY, @wherePrinGarotas);
	END IF;
	SET @QUERYDESAMPA =  CONCAT( ' inner join tb_46_relacion_trueque des on fac.Factura_Consecutivo  = des.Consecutivo and ' , 
									' des.Documento = \'factura\'');
	SET @WHEREDESAMPA = CONCAT(' AND fac.Factura_Consecutivo NOT IN (
								  SELECT
								  fac.Factura_Consecutivo AS Factura_Consecutivo
								FROM  tb_07_factura fac 
									  inner join tb_46_relacion_trueque des on fac.Factura_Consecutivo  = des.Consecutivo and
									  des.Documento = \'factura\'', @wherePrincipal, ')');
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'false' then 
		SET @QUERY = CONCAT(@QUERY, @QUERYDESAMPA, @wherePrincipal, @whereCliente, ' order by user.Usuario_Codigo, fac.Factura_Fecha_Hora');
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'true' then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal, @whereCliente, @WHEREDESAMPA, ' order by user.Usuario_Codigo, fac.Factura_Fecha_Hora');
	END IF;
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'true' then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal, @whereCliente, ' order by user.Usuario_Codigo, fac.Factura_Fecha_Hora');
	END IF;
  
  
   PREPARE smpt FROM @Query;
  
   EXECUTE smpt;
  
   DEALLOCATE PREPARE smpt;
 END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`gabo_admin`@`%` PROCEDURE `PA_ListaDeFacturasPorUsuarioCorregido`(IN `paEstadoFactura` VARCHAR(30), IN `paFechaI` VARCHAR(30), IN `paFechaF` VARCHAR(30), IN `paSucursal` VARCHAR(10), IN `paEsSucursal` VARCHAR(10), IN `paSuDesamparados` VARCHAR(10), IN `paSuGarotasBonitas` VARCHAR(10))
BEGIN
	SET @wherePrincipal 		= CONCAT(' where   user.TB_02_Sucursal_Codigo = ', '\'', paSucursal, '\'', 
								 ' and fac.Factura_Estado in (', paEstadoFactura, ') ',
								 ' and UNIX_TIMESTAMP(fac.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
								 ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')'); 
	SET @whereCliente = CONCAT(' and cli.Cliente_EsSucursal = ', '\'', paEsSucursal, '\'');								 
	SET @QUERY 			= CONCAT( ' SELECT  user.Usuario_Nombre_Usuario usuario, 
											CONCAT(user.Usuario_Nombre, \' \', user.Usuario_Apellidos) nombre,
											fac.Factura_Consecutivo consecutivo, 
											fac.Factura_Fecha_Hora fecha, 
											fac.Factura_Monto_Total montoTotal, 
											fac.Factura_Monto_IVA montoIVA, 
											fac.Factura_Monto_Sin_IVA montoSinIVA
									FROM    tb_01_usuario user inner join 
											tb_07_factura fac on user.Usuario_Codigo = fac.Factura_Vendedor_Codigo and ');
	SET @wherePrinGarotas =	(' user.TB_02_Sucursal_Codigo = fac.TB_02_Sucursal_Codigo inner join 
								tb_03_cliente cli on fac.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula ');	
	SET @wherePrinDesampa =	(' user.TB_02_Sucursal_Codigo = fac.Factura_Vendedor_Sucursal inner join 
								tb_03_cliente cli on fac.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula ');												
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'false' then 
		SET @QUERY = CONCAT(@QUERY,@wherePrinDesampa);
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'true' then 
		SET @QUERY = CONCAT(@QUERY, @wherePrinGarotas);
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'false' then 
		SET @QUERY = CONCAT(@QUERY, @wherePrinGarotas);
	END IF;
	SET @QUERYDESAMPA =  CONCAT( ' inner join tb_46_relacion_trueque des on fac.Factura_Consecutivo  = des.Consecutivo and ' , 
									' des.Documento = \'factura\'');
	SET @WHEREDESAMPA = CONCAT(' AND fac.Factura_Consecutivo NOT IN (
								  SELECT
								  fac.Factura_Consecutivo AS Factura_Consecutivo
								FROM  tb_07_factura fac 
									  inner join tb_46_relacion_trueque des on fac.Factura_Consecutivo  = des.Consecutivo and
									  des.Documento = \'factura\'', @wherePrincipal, ')');
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'false' then 
		SET @QUERY = CONCAT(@QUERY, @QUERYDESAMPA, @wherePrincipal, @whereCliente, ' order by user.Usuario_Codigo, fac.Factura_Fecha_Hora');
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'true' then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal, @whereCliente, @WHEREDESAMPA, ' order by user.Usuario_Codigo, fac.Factura_Fecha_Hora');
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'false' then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal, @whereCliente, ' order by user.Usuario_Codigo, fac.Factura_Fecha_Hora');
	END IF;
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'true' then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal, @whereCliente, ' order by user.Usuario_Codigo, fac.Factura_Fecha_Hora');
	END IF;
  
  
   PREPARE smpt FROM @Query;
  
   EXECUTE smpt;
  
   DEALLOCATE PREPARE smpt;
 END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`gabo_admin`@`%` PROCEDURE `PA_NotaCredito`(IN `paFechaI` VARCHAR(30), IN `paFechaF` VARCHAR(30), IN `paSucursal` VARCHAR(10), IN `paCedula` VARCHAR(30), IN `paNombre` VARCHAR(100), IN `paSuDesamparados` VARCHAR(10), IN `paSuGarotasBonitas` VARCHAR(10))
BEGIN
	SET @wherePrincipal 		= CONCAT(' where   noCre.Sucursal = ', '\'', paSucursal, '\'');
	
	SET @QUERY 			= CONCAT( 'SELECT DISTINCT cli.Cliente_Cedula,
											concat(cli.Cliente_Nombre, \' \', cli.Cliente_Apellidos) as Nombre,          
											noCre.Fecha_Creacion, 
											noCre.Factura_Aplicar, 
											noCre.Consecutivo, 
											noCre.Tipo_Pago, 
											noCre.Factura_Acreditar, 
											(SELECT  Sum(lpNoCre.Cantidad_Defectuoso * lpNoCre.Precio_Unitario) as MontoDefectuoso
															  FROM    tb_28_productos_notas_credito lpNoCre 
															  WHERE   lpNoCre.Nota_Credito_Consecutivo = noCre.Consecutivo and lpNoCre.Sucursal = noCre.Sucursal) as MontoDefectuoso, 
											(SELECT  Sum(lpNoCre.Cantidad_Bueno * lpNoCre.Precio_Unitario) as MontoDefectuoso
													  FROM    tb_28_productos_notas_credito lpNoCre 
													  WHERE   lpNoCre.Nota_Credito_Consecutivo = noCre.Consecutivo and lpNoCre.Sucursal = noCre.Sucursal) as MontoBueno, 
											(SELECT ifnull(Sum((lpNoCre.Precio_Final - (lpNoCre.Precio_Final / 1.13))-(lpNoCre.Precio_Unitario - (lpNoCre.Precio_Unitario / 1.13))), 0) as Total
													  FROM    tb_28_productos_notas_credito lpNoCre 
														INNER JOIN tb_27_notas_credito cre ON lpNoCre.nota_credito_consecutivo = cre.consecutivo AND lpNoCre.sucursal = cre.sucursal 
														INNER JOIN tb_03_cliente cli on cre.Cliente = cli.Cliente_Cedula 
													  WHERE   lpNoCre.Nota_Credito_Consecutivo = noCre.Consecutivo and lpNoCre.Sucursal = noCre.Sucursal  and lpNoCre.No_Retencion = 0 and cli.Aplica_Retencion = 1) as Retencion,		  
											(SELECT  (Sum(lpNoCre.Cantidad_Bueno * lpNoCre.Precio_Unitario) + Sum(lpNoCre.Cantidad_Defectuoso * lpNoCre.Precio_Unitario))
														+ ((lpNoCre.Precio_Final - (lpNoCre.Precio_Final / 1.13))-(lpNoCre.Precio_Unitario - (lpNoCre.Precio_Unitario / 1.13)))
														- (SELECT ifnull(Sum((lpNoCre.Precio_Final - (lpNoCre.Precio_Final / 1.13))-(lpNoCre.Precio_Unitario - (lpNoCre.Precio_Unitario / 1.13))), 0) as Total
															  FROM    tb_28_productos_notas_credito lpNoCre 
																INNER JOIN tb_27_notas_credito cre ON lpNoCre.nota_credito_consecutivo = cre.consecutivo AND lpNoCre.sucursal = cre.sucursal 
																INNER JOIN tb_03_cliente cli on cre.Cliente = cli.Cliente_Cedula 
															  WHERE   lpNoCre.Nota_Credito_Consecutivo = noCre.Consecutivo and lpNoCre.Sucursal = noCre.Sucursal  and lpNoCre.No_Retencion = 0 and cli.Aplica_Retencion = 1) as Total												  												  														  
													  FROM    tb_28_productos_notas_credito lpNoCre 
													  WHERE   lpNoCre.Nota_Credito_Consecutivo = noCre.Consecutivo and lpNoCre.Sucursal = noCre.Sucursal) as Total
									FROM    tb_03_cliente cli 
											inner join tb_27_notas_credito noCre  
											  on cli.Cliente_Cedula = noCre.Cliente
											inner join tb_28_productos_notas_credito pNoCre 
											  on pNoCre.Nota_Credito_Consecutivo = noCre.Consecutivo 
											  and pNoCre.Sucursal = noCre.Sucursal');	

	SET @QUERYDESAMPA =  CONCAT( ' inner join tb_46_relacion_trueque des on noCre.Consecutivo = des.Consecutivo and   ' , 
									' des.Documento = \'nota_credito\'');
	SET @WHEREDESAMPA = CONCAT(' AND noCre.Consecutivo NOT IN (
								  SELECT
								  noCre.Consecutivo AS Consecutivo
								FROM  tb_27_notas_credito noCre 
									  inner join tb_46_relacion_trueque des on noCre.Consecutivo  = des.Consecutivo and
									  des.Documento = \'nota_credito\' and des.Sucursal= \'7\' )');											  
	IF paCedula <> 'null' THEN
		SET @wherePrincipal = CONCAT(@wherePrincipal, ' AND cli.Cliente_Cedula = ', '\'',paCedula, '\'' );
	END IF;
	IF paNombre <> 'null' THEN 
		SET @wherePrincipal = CONCAT(@wherePrincipal, ' AND concat(cli.Cliente_Nombre, \' \', cli.Cliente_Apellidos) LIKE  ', '\'',paNombre, '\'' );
	END IF; 
	IF paFechaI <> 'null' AND paFechaF <> 'null' then 
		SET @wherePrincipal = CONCAT (@wherePrincipal, ' AND UNIX_TIMESTAMP(noCre.Fecha_Creacion) BETWEEN UNIX_TIMESTAMP(', '\'',paFechaI, '\'', ') AND UNIX_TIMESTAMP(', '\'',paFechaF, '\')');      
	END IF;	
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'false' and (paSucursal = '2' or paSucursal = '7') then 
		SET @QUERY = CONCAT(@QUERY, @QUERYDESAMPA, @wherePrincipal);
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'true' and (paSucursal = '2' or paSucursal = '7') then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal, @WHEREDESAMPA);
	END IF;
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'true' and (paSucursal = '2' or paSucursal = '7') then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal);
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'false' and paSucursal <> '2' and paSucursal <> '7' then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal);
	END IF;
  
  
   PREPARE smpt FROM @Query;
  
   EXECUTE smpt;
  
   DEALLOCATE PREPARE smpt;
 END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`gabo_admin`@`%` PROCEDURE `PA_ProcedenciaArticulo`(IN `paSucursal` VARCHAR(10), IN `paFechaI` VARCHAR(30), IN `paFechaF` VARCHAR(30), IN `paCodigoI` VARCHAR(20))
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
  
  
   PREPARE smpt FROM @Query;
  
   EXECUTE smpt;
  
   DEALLOCATE PREPARE smpt;
 END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`gabo_admin`@`%` PROCEDURE `PA_RecibosXDinero`(IN `paFechaI` VARCHAR(30), IN `paFechaF` VARCHAR(30), IN `paSucursal` VARCHAR(10), IN `paCedula` VARCHAR(30), IN `paNombre` VARCHAR(100), IN `paPendientes` VARCHAR(10))
BEGIN
	SET @wherePrincipal 		= CONCAT(' where   cre.Credito_Sucursal_Codigo = ', '\'', paSucursal, '\'');
	
	SET @QUERY 			= CONCAT( 'SELECT   cre.Credito_Factura_Consecutivo as consecutivoFac,
											recd.Consecutivo,  
											recd.Tipo_Pago,
											recd.Recibo_Fecha, 
											cli.Cliente_cedula as identificacion,
											concat(cli.Cliente_Nombre, \' \', cli.Cliente_Apellidos) as Nombre,																						
											If(recd.Recibo_Saldo+recd.Recibo_Cantidad=0,cre.Credito_Saldo_Inicial,recd.Recibo_Saldo+recd.Recibo_Cantidad) as Credito_Saldo_Inicial,
											recd.Recibo_Cantidad, 											
											recd.Recibo_Saldo as Credito_Saldo_Actual,
											recd.Anulado
									FROM    tb_26_recibos_dinero recd 
											inner join tb_24_credito cre on cre.Credito_Id = recd.Credito
											inner join tb_03_cliente cli on cre.Credito_Cliente_Cedula = cli.Cliente_Cedula ');	
	IF paCedula <> 'null' THEN
		SET @wherePrincipal = CONCAT(@wherePrincipal, ' AND cli.Cliente_Cedula = ', '\'',paCedula, '\'' );
	END IF;
	IF paNombre <> 'null' THEN 
		SET @wherePrincipal = CONCAT(@wherePrincipal, ' AND concat(cli.Cliente_Nombre, \' \', cli.Cliente_Apellidos) LIKE  ', '\'%',paNombre, '%\'' );
	END IF; 
	IF paFechaI <> 'null' AND paFechaF <> 'null' then 
		SET @wherePrincipal = CONCAT (@wherePrincipal, ' AND UNIX_TIMESTAMP(recd.Recibo_Fecha) BETWEEN UNIX_TIMESTAMP(', '\'',paFechaI, '\'', ') AND UNIX_TIMESTAMP(', '\'',paFechaF, '\')');      
	END IF;
	IF paPendientes <> 'null' AND paFechaF <> 'null' then 
		SET @wherePrincipal = CONCAT (@wherePrincipal, ' AND cre.Credito_Saldo_Actual > 0');      
	END IF;
	SET @QUERY = CONCAT(@QUERY, @wherePrincipal, ' AND recd.Anulado = 0');
       PREPARE smpt FROM @Query;
     EXECUTE smpt;
     DEALLOCATE PREPARE smpt;
 END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`gabo_admin`@`%` PROCEDURE `PA_RentabilidadXCliente`(IN `paEstadoFactura` VARCHAR(30), IN `paFechaI` VARCHAR(30), IN `paFechaF` VARCHAR(30), IN `paSucursal` VARCHAR(10), IN `paSuDesamparados` VARCHAR(10), IN `paTipoPago` VARCHAR(100), IN `paSuGarotasBonitas` VARCHAR(10))
BEGIN
	SET @wherePrincipal 		= CONCAT(' where   fac.TB_02_Sucursal_Codigo =  ', '\'', paSucursal, '\'', 
								 ' and fac.Factura_Estado in (', paEstadoFactura, ') ',
								 ' and fac.Factura_Tipo_Pago in (', paTipoPago, ') ' , 
								 ' and UNIX_TIMESTAMP(fac.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
								 ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')'); 
	SET @QUERY 			= CONCAT( 'SELECT fac.factura_consecutivo as codigoFactura,
										   fac.tb_03_cliente_cliente_cedula as cedula,
										   fac.factura_nombre_cliente as nombre,
										   fac.Factura_Monto_Sin_IVA as BaseImp,
										   fac.Factura_Retencion as retencion,
										   ifnull((SELECT sum(pre.precio_monto * arfa.articulo_factura_cantidad) AS total
												   FROM   tb_08_articulos_factura arfa
														  INNER JOIN tb_11_precios pre ON pre.tb_06_articulo_articulo_codigo = arfa.articulo_factura_codigo
																	 AND pre.precio_numero = 0
												   WHERE  arfa.tb_07_factura_factura_consecutivo = fac.factura_consecutivo
														  AND arfa.tb_07_factura_tb_02_sucursal_codigo = ' ,paSucursal,  '
														  AND pre.TB_06_Articulo_TB_02_Sucursal_Codigo = ' ,paSucursal,  '), 0) AS
										   TotalCosto,
										   ( fac.factura_monto_total / 1.13 ) - (ifnull((SELECT Sum( pre.precio_monto * arfa.articulo_factura_cantidad) AS total
													FROM tb_08_articulos_factura arfa
														 INNER JOIN tb_11_precios pre ON pre.tb_06_articulo_articulo_codigo = arfa.articulo_factura_codigo
																	AND pre.precio_numero = 0
													 WHERE arfa.tb_07_factura_factura_consecutivo = fac.factura_consecutivo
														 AND arfa.tb_07_factura_tb_02_sucursal_codigo = ' ,paSucursal,  '
														 AND pre.TB_06_Articulo_TB_02_Sucursal_Codigo = ' ,paSucursal,  '),0)) AS
										   TotalBeneficio,
										   ifnull(( ( ( fac.factura_monto_total / 1.13 ) - ((SELECT
													  Sum( pre.precio_monto * arfa.articulo_factura_cantidad) AS total
													  FROM tb_08_articulos_factura arfa
																 INNER JOIN tb_11_precios pre ON pre.tb_06_articulo_articulo_codigo = arfa.articulo_factura_codigo
																			AND pre.precio_numero = 0
													 WHERE
																 arfa.tb_07_factura_factura_consecutivo = fac.factura_consecutivo
																 AND arfa.tb_07_factura_tb_02_sucursal_codigo = ' ,paSucursal,  '
																 AND pre.TB_06_Articulo_TB_02_Sucursal_Codigo = ' ,paSucursal,  '))
													) /
															 (SELECT Sum(
															 pre.precio_monto * arfa.articulo_factura_cantidad)
																	 AS
																	 total
															  FROM
															 tb_08_articulos_factura arfa
															 INNER JOIN tb_11_precios pre
																	 ON pre.tb_06_articulo_articulo_codigo =
																		arfa.articulo_factura_codigo
																		AND pre.precio_numero = 0
															 WHERE
															 arfa.tb_07_factura_factura_consecutivo =
															 fac.factura_consecutivo
															 AND arfa.tb_07_factura_tb_02_sucursal_codigo = ' ,paSucursal,  '
															 AND pre.TB_06_Articulo_TB_02_Sucursal_Codigo = ' ,paSucursal,  ' ) ) *
												  100, 0) AS
										   margenbeneficio,
										   fac.factura_monto_total TotalNeto,
										   fac.Factura_Retencion retencion
									FROM   tb_07_factura fac ');	
	SET @QUERYDESAMPA =  CONCAT( ' inner join tb_46_relacion_trueque des on fac.Factura_Consecutivo  = des.Consecutivo and ' , 
									' des.Documento = \'factura\'');
	SET @WHEREDESAMPA = CONCAT(' AND fac.Factura_Consecutivo NOT IN (
								  SELECT
								  fac.Factura_Consecutivo AS Factura_Consecutivo
								FROM  tb_07_factura fac 
									  inner join tb_46_relacion_trueque des on fac.Factura_Consecutivo  = des.Consecutivo and
									  des.Documento = \'factura\'', @wherePrincipal, ')');
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'false' and (paSucursal = '2' or paSucursal = '7') then 
		SET @QUERY = CONCAT(@QUERY, @QUERYDESAMPA, @wherePrincipal);
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'true' and (paSucursal = '2' or paSucursal = '7') then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal, @WHEREDESAMPA);
	END IF;
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'true' and (paSucursal = '2' or paSucursal = '7') then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal);
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'false' and paSucursal <> '2' and paSucursal <> '7' then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal);
	END IF;
  
  
   PREPARE smpt FROM @Query;
  
   EXECUTE smpt;
  
   DEALLOCATE PREPARE smpt;
 END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`gabo_admin`@`%` PROCEDURE `PA_VentaXClienteFacturas`(IN `paSucursal` VARCHAR(10), IN `paFechaI` VARCHAR(30), IN `paFechaF` VARCHAR(30), IN `paEstadoFactura` VARCHAR(30), IN `paEsSucursal` VARCHAR(30), IN `paNombre` VARCHAR(50), IN `paCedula` VARCHAR(20), IN `paRango` VARCHAR(10), IN `paMontoI` VARCHAR(20), IN `paMontoF` VARCHAR(20))
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
		end if; 
	end if; 
  end If;   
  
  
  
  PREPARE smpt FROM @Query;
  
  EXECUTE smpt;
  
  DEALLOCATE PREPARE smpt;
 END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`gabo_admin`@`%` PROCEDURE `PA_VentaXClienteFacturasResumido`(IN `paSucursal` VARCHAR(10), IN `paFechaI` VARCHAR(30), IN `paFechaF` VARCHAR(30), IN `paEstadoFactura` VARCHAR(30), IN `paEsSucursal` VARCHAR(30), IN `paNombre` VARCHAR(50), IN `paCedula` VARCHAR(20), IN `paRango` VARCHAR(10), IN `paMontoI` VARCHAR(20), IN `paMontoF` VARCHAR(20))
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
		end if; 
	end if; 
  end If;   
  IF paSucursal <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, 'group by cli.Cliente_Cedula');   
  end If;  
  
  
  
  PREPARE smpt FROM @Query;
  
  EXECUTE smpt;
  
  DEALLOCATE PREPARE smpt;
 END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`gabo_admin`@`%` PROCEDURE `PA_VentaXClienteFacturasResumidoTemporal`(IN `paSucursal` VARCHAR(10), IN `paFechaI` VARCHAR(30), IN `paFechaF` VARCHAR(30), IN `paEstadoFactura` VARCHAR(30), IN `paEsSucursal` VARCHAR(30), IN `paNombre` VARCHAR(50), IN `paCedula` VARCHAR(20), IN `paRango` VARCHAR(10), IN `paMontoI` VARCHAR(20), IN `paMontoF` VARCHAR(20))
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
                  cli.cliente_celular as Celular, 
                  cli.cliente_telefono as Telefono, 
                  cli.cliente_correo_electronico as Email, 
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
		end if; 
	end if; 
  end If;   
  IF paSucursal <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, 'group by cli.Cliente_Cedula');   
  end If;  
  
  
  
  PREPARE smpt FROM @Query;
  
  EXECUTE smpt;
  
  DEALLOCATE PREPARE smpt;
 END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`gabo_admin`@`%` PROCEDURE `PA_VentaXClienteProforma`(IN `paSucursal` VARCHAR(10), IN `paFechaI` VARCHAR(30), IN `paFechaF` VARCHAR(30), IN `paEstadoProforma` VARCHAR(30), IN `paEsSucursal` VARCHAR(30), IN `paNombre` VARCHAR(50), IN `paCedula` VARCHAR(20), IN `paRango` VARCHAR(10), IN `paMontoI` VARCHAR(20), IN `paMontoF` VARCHAR(20))
BEGIN
	SET @where2 		= CONCAT(' where   cli.Cliente_EsSucursal = ', '\'', paEsSucursal, '\'', 
								 'and pro.TB_02_Sucursal_Codigo = ', '\'', paSucursal, '\'',
								 'and pro.Proforma_Estado =', '\'', paEstadoProforma, '\'', 
								 'and UNIX_TIMESTAMP(pro.Proforma_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
								 ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')'); 
	SET @QUERY 			= CONCAT( 'select  cli.Cliente_Cedula cedula,
											CONCAT(cli.Cliente_Nombre, " ", cli.Cliente_Apellidos) nombre,     
											pro.Proforma_Fecha_Hora fecha,
											pro.Proforma_Consecutivo consecutivo, 
											pro.Proforma_Monto_Total montoTotal, 
											pro.Proforma_Monto_IVA montoIVA, 
											pro.Proforma_Monto_Sin_IVA montoSinIVA, 
											pro.Proforma_Retencion
									from    tb_03_cliente cli inner join 
											tb_10_proforma pro on pro.TB_03_Cliente_Cliente_Cedula = cli.Cliente_Cedula');		
  IF paSucursal <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, @where2);      
  end If; 
  IF paNombre <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, 'and pro.Proforma_Nombre_Cliente like ','\'','%',paNombre,'%', '\'');      
  end If;   
  IF paCedula <> 'null' then 
    SET @QUERY = CONCAT (@QUERY, 'and cli.Cliente_Cedula =  ', '\'', paCedula, '\'');      
  end If;   
    
  IF paRango <> 'null' then 
	IF paRango = 'menorIgual' then
		SET @QUERY = CONCAT (@QUERY, 'and pro.protura_Monto_Total <= ', '\'',paMontoI, '\'');      
	ELSE 
		IF paRango = 'mayorIgual' then
			SET @QUERY = CONCAT (@QUERY, 'and pro.protura_Monto_Total >= ', '\'',paMontoI, '\'');      
		ELSE 
			IF paRango = 'between' then
				SET @QUERY = CONCAT (@QUERY, 'and pro.protura_Monto_Total BETWEEN ', '\'',paMontoI, '\'', ' AND ', '\'',paMontoF, '\'');      
			end if; 
		end if; 
	end if; 
  end If;   
  
  
  
  PREPARE smpt FROM @Query;
  
  EXECUTE smpt;
  
  DEALLOCATE PREPARE smpt;
 END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`gabo_admin`@`%` PROCEDURE `PA_VentaXClienteProformaResumido`(IN `paSucursal` VARCHAR(10), IN `paFechaI` VARCHAR(30), IN `paFechaF` VARCHAR(30), IN `paEstadoProforma` VARCHAR(30), IN `paEsSucursal` VARCHAR(30), IN `paNombre` VARCHAR(50), IN `paCedula` VARCHAR(20), IN `paRango` VARCHAR(10), IN `paMontoI` VARCHAR(20), IN `paMontoF` VARCHAR(20))
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
		end if; 
	end if; 
  end If;   
  SET @QUERY = CONCAT (@QUERY, ' group by cli.Cliente_Cedula'); 
  
  
  
  PREPARE smpt FROM @Query;
  
  EXECUTE smpt;
  
  DEALLOCATE PREPARE smpt;
 END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`gabo_admin`@`%` PROCEDURE `PA_VentaXMes`(IN `paEstadoFactura` VARCHAR(30), IN `paFechaI` VARCHAR(30), IN `paFechaF` VARCHAR(30), IN `paSucursal` VARCHAR(10), IN `paSuDesamparados` VARCHAR(10), IN `paTipoPago` VARCHAR(100), IN `paSuGarotasBonitas` VARCHAR(10))
BEGIN
	SET @wherePrincipal 		= CONCAT(' where   fac.TB_02_Sucursal_Codigo = ', '\'', paSucursal, '\'', 
								 ' and fac.Factura_Estado in (', paEstadoFactura, ') ',
								 ' and fac.Factura_Tipo_Pago in (', paTipoPago, ') ' , 
								 ' and UNIX_TIMESTAMP(fac.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
								 ') AND UNIX_TIMESTAMP(', '\'', paFechaF, '\'', ')'); 
	SET @QUERY 			= CONCAT( 'SELECT fac.Factura_Consecutivo AS Factura_Consecutivo,
									  fac.Factura_Nombre_Cliente AS Nombre_Cliente,
									  fac.Factura_Fecha_Hora AS Fecha_Hora,
									  fac.Factura_Monto_IVA AS Monto_IVA,
									  fac.Factura_Monto_Sin_IVA AS Monto_Sin_IVA,
									  fac.Factura_Retencion AS retencion,
									  fac.Factura_Monto_Total - fac.Factura_Retencion AS total,
									  fac.Factura_Monto_Total as TotalGlobal
									FROM  tb_07_factura fac ');	
	SET @QUERYDESAMPA =  CONCAT( ' inner join tb_46_relacion_trueque des on fac.Factura_Consecutivo  = des.Consecutivo and ' , 
									' des.Documento = \'factura\'');
	SET @WHEREDESAMPA = CONCAT(' AND fac.Factura_Consecutivo NOT IN (
								  SELECT
								  fac.Factura_Consecutivo AS Factura_Consecutivo
								FROM  tb_07_factura fac 
									  inner join tb_46_relacion_trueque des on fac.Factura_Consecutivo  = des.Consecutivo and
									  des.Documento = \'factura\'', @wherePrincipal, ')');
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'false' and (paSucursal = '2' or paSucursal = '7') then 
		SET @QUERY = CONCAT(@QUERY, @QUERYDESAMPA, @wherePrincipal);
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'true' and (paSucursal = '2' or paSucursal = '7') then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal, @WHEREDESAMPA);
	END IF;
	IF paSuDesamparados = 'true' AND paSuGarotasBonitas = 'true' and (paSucursal = '2' or paSucursal = '7') then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal);
	END IF;
	IF paSuDesamparados = 'false' AND paSuGarotasBonitas = 'false' and paSucursal <> '2' and paSucursal <> '7' then 
		SET @QUERY = CONCAT(@QUERY, @wherePrincipal);
	END IF;
  
  
   PREPARE smpt FROM @Query;
  
   EXECUTE smpt;
  
   DEALLOCATE PREPARE smpt;
 END$$
DELIMITER ;
