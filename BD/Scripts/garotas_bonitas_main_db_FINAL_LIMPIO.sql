-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-10-2015 a las 01:22:26
-- Versión del servidor: 5.6.21
-- Versión de PHP: 5.5.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `garotas_bonitas_main_db`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`consulta`@`%` PROCEDURE `PA_ClientesXDescuento`(
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
 END$$

CREATE DEFINER=`consulta`@`%` PROCEDURE `PA_ConsultaUsuarios`(
	IN paSucursal VARCHAR(10),
	IN paFechaI VARCHAR(30), 
	IN paFechaF VARCHAR(30)
 )
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
  -- select @QUERY as 'Resultado';  
  -- preparamos el objete Statement a partir de nuestra variable
  PREPARE smpt FROM @Query;
  -- ejecutamos el Statement
  EXECUTE smpt;
  -- liberamos la memoria
  DEALLOCATE PREPARE smpt;
 END$$

CREATE DEFINER=`consulta`@`%` PROCEDURE `PA_VentaXClienteFacturas`(
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
								 'and fac.TB_02_Sucursal_Codigo = ', '\'', paSucursal, '\'',
								 'and fac.Factura_Estado =', '\'', paEstadoFactura, '\'', 
								 'and UNIX_TIMESTAMP(fac.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'', paFechaI, '\'', 
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
  select @QUERY as 'Resultado';  
  -- preparamos el objete Statement a partir de nuestra variable
   PREPARE smpt FROM @Query;
  -- ejecutamos el Statement
   EXECUTE smpt;
  -- liberamos la memoria
   DEALLOCATE PREPARE smpt;
 END$$

CREATE DEFINER=`consulta`@`%` PROCEDURE `PA_VentaXClienteFacturasResumido`(
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
 END$$

CREATE DEFINER=`consulta`@`%` PROCEDURE `PA_VentaXClienteProforma`(
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
    -- CONSTRUCCIÓN WHERE RANGO CODIGOS ------------------------------------------------------------------
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
 END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_01_usuario`
--

CREATE TABLE IF NOT EXISTS `tb_01_usuario` (
`Usuario_Codigo` int(11) NOT NULL,
  `Usuario_Nombre` varchar(20) DEFAULT NULL,
  `Usuario_Apellidos` varchar(40) DEFAULT NULL,
  `Usuario_Cedula` bigint(20) DEFAULT NULL,
  `Usuario_Tipo_Cedula` varchar(10) DEFAULT NULL,
  `Usuario_Celular` varchar(15) DEFAULT NULL,
  `Usuario_Telefono` varchar(15) DEFAULT NULL,
  `Usuario_Fecha_Ingreso` timestamp NULL DEFAULT NULL,
  `Usuario_Fecha_Cesantia` timestamp NULL DEFAULT NULL,
  `Usuario_Fecha_Recontratacion` timestamp NULL DEFAULT NULL,
  `Usuario_Nombre_Usuario` varchar(20) DEFAULT NULL,
  `Usuario_Observaciones` varchar(150) DEFAULT NULL,
  `Usuario_Password` varchar(100) DEFAULT NULL,
  `Usuario_Imagen_URL` varchar(100) DEFAULT NULL,
  `Usuario_Correo_Electronico` varchar(30) DEFAULT NULL,
  `Usuario_Rango` varchar(10) DEFAULT NULL,
  `TB_02_Sucursal_Codigo` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_01_usuario`
--

INSERT INTO `tb_01_usuario` (`Usuario_Codigo`, `Usuario_Nombre`, `Usuario_Apellidos`, `Usuario_Cedula`, `Usuario_Tipo_Cedula`, `Usuario_Celular`, `Usuario_Telefono`, `Usuario_Fecha_Ingreso`, `Usuario_Fecha_Cesantia`, `Usuario_Fecha_Recontratacion`, `Usuario_Nombre_Usuario`, `Usuario_Observaciones`, `Usuario_Password`, `Usuario_Imagen_URL`, `Usuario_Correo_Electronico`, `Usuario_Rango`, `TB_02_Sucursal_Codigo`) VALUES
(1, 'David', 'Villalobos Fonseca', 402040954, 'Nacional', '8327-5345', '2268-8368', '2014-02-02 12:00:00', NULL, NULL, 'David_test', 'Usuario testing', '49ff630e0642355953dece12a68da694', '402040954_0.png', 'davillafon12@gmail.com', 'avanzado', 0),
(2, 'Siviany', 'Prendas Zamora', 401240584, 'Nacional', '8542-7454', '2237-4585', '2014-02-13 12:00:00', NULL, NULL, 'Siviany_Test', 'Usuario testing', '21232f297a57a5a743894a0e4a801fc3', '2.png', 'sprendas88@gmail.com', 'avanzado', 0),
(3, 'Esteban', 'Prendas Zamora', 112010752, 'nacional', '8411-6368', '2278-5429', '2015-09-11 22:07:57', NULL, NULL, 'eprendasgb', 'Administrador del Sistema', '377e4f643476424952946c34d31b5c9e', '112010752_0.jpg', 'esteban@garotasbonitascr.com', 'administra', 0),
(4, 'Esteban', 'Prendas Zamora', 112010752, 'nacional', '8411-6368', '2278-5429', '2015-09-11 22:09:31', NULL, NULL, 'eprendasipm', '', '377e4f643476424952946c34d31b5c9e', '112010752_1.jpg', 'esteban@garotasbonitascr.com', 'administra', 1),
(5, 'Esteban', 'Prendas Zamora', 112010752, 'nacional', '8411-6368', '2278-5429', '2015-09-11 22:10:42', NULL, NULL, 'eprendasgbg', '', '377e4f643476424952946c34d31b5c9e', 'Default.png', 'esteban@garotasbonitascr.com', 'administra', 2),
(6, 'Esteban', 'Prendas Zamora', 112010752, 'nacional', '8411-6368', '2278-5429', '2015-09-11 22:11:46', NULL, NULL, 'eprendassg', '', '377e4f643476424952946c34d31b5c9e', 'Default.png', 'esteban@garotasbonitascr.com', 'administra', 3),
(7, 'Esteban', 'Prendas Zamora', 112010752, 'nacional', '8411-6368', '2278-5429', '2015-09-11 22:12:56', NULL, NULL, 'eprendasgemas', '', '377e4f643476424952946c34d31b5c9e', 'Default.png', 'esteban@garotasbonitascr.com', 'administra', 4),
(8, 'Esteban', 'Prendas Zamora', 112010752, 'nacional', '8411-6368', '2278-5429', '2015-09-11 22:13:59', NULL, NULL, 'eprendasde', '', '377e4f643476424952946c34d31b5c9e', 'Default.png', 'esteban@garotasbonitascr.com', 'administra', 5),
(9, 'Esteban', 'Prendas Zamora', 112010752, 'nacional', '8411-6368', '2278-5429', '2015-09-11 22:15:40', NULL, NULL, 'eprendaspruebasipm', '', '377e4f643476424952946c34d31b5c9e', 'Default.png', 'esteban@garotasbonitascr.com', 'administra', 6),
(10, 'Esteban', 'Prendas Zamora', 112010752, 'nacional', '8411-6368', '2278-5429', '2015-09-11 22:16:49', NULL, NULL, 'eprendaspruebasgb', '', '377e4f643476424952946c34d31b5c9e', 'Default.png', 'esteban@garotasbonitascr.com', 'administra', 7),
(11, 'Juan ', 'Vargas Barquero', 304860001, 'nacional', '6280-3418', '6280-3418', '2015-09-12 15:48:00', NULL, NULL, 'jvargasgb', '', '3bca705e966fa3b8d1525d7f86e3a8d1', '304860001_0.jpg', 'jadolfo-1995@hotmail.com', 'avanzado', 0),
(12, 'Marilyn', 'Vivas Ayerdis', 112220361, 'nacional', '7213-1650', '2254-2233', '2015-09-12 20:27:23', NULL, NULL, 'mvivasgb', '', '986c52867fde2b08b1d266514219d189', 'Default.png', '', 'cajero', 0),
(13, 'Digna ', 'Quiros Carvajal', 301900516, 'nacional', '8336-8704', '2254-7793', '2015-09-12 20:41:29', NULL, NULL, 'dquirosgb', '', 'b93c14b8ff6e92882b9cbf3b1bb63e19', '301900516_0.jpg', 'DIGNA.QUIROS@GMAIL.COM', 'vendedor', 0),
(14, 'Javier ', 'Portillo Ayala', 115430721, 'nacional', '6059-9392', '6059-9392', '2015-09-12 20:50:33', NULL, NULL, 'jportillogb', '', '7fc8944957bd9339524a41a8a0b81341', '115430721_0.jpg', '', 'avanzado', 0),
(15, 'Gabriel ', 'Gazel Angulo', 114730473, 'nacional', '8840-1545', '8840-1545', '2015-09-12 20:59:00', NULL, NULL, 'ggazelgb', '', '1c4e35b90f4a6ff9696d52c242a46172', '114730473_0.jpg', 'gabriel7g107@hotmail.com', 'avanzado', 0),
(16, 'Priscilla', 'Gazel Sanchez', 111580360, 'nacional', '8806-3550', '8806-3550', '2015-09-12 21:18:19', NULL, NULL, 'pgazelgb', '', 'd681058e73d892f3a1d085766d2ee084', '111580360_0.jpg', 'priga-07@hotmail.com', 'vendedor', 0),
(17, 'Jostin', 'Sanchez Hernandez', 116260826, 'nacional', '8796-7283', '8796-7283', '2015-09-12 21:38:38', NULL, NULL, 'jsanchezgb', '', 'e44e088932bbfd603d700625ffd57ea8', '116260826_0.jpg', '', 'avanzado', 0),
(18, 'Daniel', 'Calderon Mendez', 115120659, 'nacional', '8871-0985', '8871-0985', '2015-09-12 21:49:36', '2015-10-19 16:10:11', NULL, 'dcalderongb', '', '333d7ae9998bec63de6673dea981e9d3', 'Default.png', 'danielboom492@hotmail.com', 'vendedor', 0),
(19, 'Juan Felix', 'Barquero Gamez', 116720783, 'nacional', '7104-3656', '8379-4890', '2015-09-12 22:38:12', NULL, NULL, 'jbarquerogb', '', 'a5dcf63af24ebced307e4e7631b62c13', '116720783_0.jpg', '', 'vendedor', 0),
(20, 'Yadir', 'Caballero Collado', 155808246, 'residencia', '8566-0442', '8566-0442', '2015-09-12 23:02:01', '2015-10-19 16:09:56', NULL, 'ycaballerogb', '', 'a4cad96a701c9576f709cb53591a950d', '', 'jackyadir22@hotmail.com', 'vendedor', 0),
(21, 'BRYAN ', 'ROJAS CORDERO', 114850059, 'nacional', '7016-2274', '7016-2274', '2015-10-19 17:00:58', NULL, NULL, 'brojasgb', '', '62a2959c1310701b1469ff66f769e823', 'Default.png', 'blcrrojas@hotmail.com', 'vendedor', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_02_sucursal`
--

CREATE TABLE IF NOT EXISTS `tb_02_sucursal` (
  `Codigo` int(11) NOT NULL,
  `Sucursal_Cedula` varchar(100) NOT NULL,
  `Sucursal_Nombre` varchar(200) DEFAULT NULL,
  `Sucursal_Telefono` varchar(15) DEFAULT NULL,
  `Sucursal_Fax` varchar(45) DEFAULT NULL,
  `Sucursal_Email` varchar(45) DEFAULT NULL,
  `Sucursal_Direccion` varchar(100) DEFAULT NULL,
  `Sucursal_Observaciones` varchar(300) DEFAULT NULL,
  `Sucursal_Fecha_Ingreso` timestamp NULL DEFAULT NULL,
  `Sucursal_Fecha_Desactivacion` timestamp NULL DEFAULT NULL,
  `Sucursal_Creador` varchar(45) DEFAULT NULL,
  `Sucursal_Estado` tinyint(1) DEFAULT NULL,
  `Sucursal_Administrador` varchar(45) DEFAULT NULL,
  `Sucursal_leyenda_tributacion` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_02_sucursal`
--

INSERT INTO `tb_02_sucursal` (`Codigo`, `Sucursal_Cedula`, `Sucursal_Nombre`, `Sucursal_Telefono`, `Sucursal_Fax`, `Sucursal_Email`, `Sucursal_Direccion`, `Sucursal_Observaciones`, `Sucursal_Fecha_Ingreso`, `Sucursal_Fecha_Desactivacion`, `Sucursal_Creador`, `Sucursal_Estado`, `Sucursal_Administrador`, `Sucursal_leyenda_tributacion`) VALUES
(0, '3-101-350785', 'INVERSIONES GAROTAS BONITAS', '2221-8127', '2223-4870', 'garotasbonitas@ice.co.cr', 'Avenida Segunda, Del Banco Popular 175mts al Sur', '', '2014-04-04 02:30:13', NULL, 'eprendas', 1, '', 'Autorizado mediante resolución No.11-97 de la D.G.T.D de fecha: 05/09/1997. La Gaceta No. 171'),
(1, '3-101-481279 ', 'COMPAÑÍA INVERSIONES IPANEMA', '2233-3619', '', 'info@ipanemacr.com', 'EDIFICIO LAS ARCADAS, PLANTA BAJA, AV SEGUNDA', '', '2015-02-07 11:57:55', NULL, 'eprendas', 1, 'CARMEN ', 'Autorizado mediante resolución No.11-97 de la D.G.T.D de fecha: 05/09/1997. La Gaceta No. 171'),
(2, '1-1700-1386032', 'GAROTAS BY GAROTAS', '2441-87-24', '2441-87-24', 'garotasbonitas@ice.co.cr', 'PLAZA IGLESIA CORAZON DE JESUS, 25MTS SUR, LO', '', '2015-02-07 12:00:39', NULL, 'eprendas', 1, 'ROSA ESNEDA BETANCUR ESTRADA ', 'Autorizado mediante resolución No.11-97 de la D.G.T.D de fecha: 05/09/1997. La Gaceta No. 171'),
(3, '117000798632', 'SUEÑOS DE GAROTAS', '27702279 ', '27702279 ', 'garotasbonitas@ice.co.cr', 'COSTADO OESTE DE LA MUNICIPALIDAD DE PEREZ ZE', '', '2015-02-07 12:02:34', NULL, 'eprendas', 1, 'ALBERTO RODRIGUEZ RODRIGUEZ', 'Autorizado mediante resolución No.11-97 de la D.G.T.D de fecha: 05/09/1997. La Gaceta No. 171'),
(4, '1-1315-0780', 'GEMAS BY GAROTAS', '2253-57-76', '', 'gemas2014@yahoo.com', 'GUADALUPE, FRENTE A LA CLINICA RICARDO JIMENE', '', '2015-04-19 03:42:10', '2015-10-19 17:11:45', 'eprendas', 0, 'Andres Morales Vargas', ''),
(5, '3-101-350785', 'GAROTAS BONITAS DESAMPARADOS', '2259-6968', '2259-6968', '', 'DESAMPARADOS CENTRO COMERCIAL LA VILLA LOCAL ', '', '2015-04-21 10:29:13', NULL, 'eprendasip', 1, 'Juan Carlos Ulloa', 'Autorizado mediante resolución No.11-97 de la D.G.T.D de fecha: 05/09/1997. La Gaceta No. 171'),
(6, '3-101-481279 ', 'IPANEMA PRUEBAS ', '2233-3619', '', 'info@ipanemacr.com', 'EDIFICIO LAS ARCADAS, PLANTA BAJA, AV SEGUNDA', '', '2015-06-23 12:18:48', NULL, 'eprendas', 1, 'Margoth Murilloa', 'Autorizado mediante resolución No.11-97 de la D.G.T.D de fecha: 05/09/1997. La Gaceta No. 171'),
(7, '3-101-350785', 'GAROTAS BONITAS PRUEBAS', '2221-8127', '2223-4870', 'garotasbonitas@ice.co.cr', 'Avenida Segunda, Del Banco Popular 175mts al Sur', '', '2015-06-24 06:55:39', NULL, 'eprendas', 1, '', 'Autorizado mediante resolución No.11-97 de la D.G.T.D de fecha: 05/09/1997. La Gaceta No. 171');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_03_cliente`
--

CREATE TABLE IF NOT EXISTS `tb_03_cliente` (
  `Cliente_Cedula` varchar(50) NOT NULL,
  `Cliente_Nombre` varchar(20) DEFAULT '',
  `Cliente_Apellidos` varchar(40) DEFAULT '',
  `Cliente_Tipo_Cedula` varchar(10) DEFAULT NULL,
  `Cliente_Carnet_Numero` int(11) DEFAULT NULL,
  `Cliente_Celular` varchar(15) DEFAULT NULL,
  `Cliente_Telefono` varchar(15) DEFAULT NULL,
  `Cliente_Fecha_Ingreso` timestamp NULL DEFAULT NULL,
  `Cliente_Pais` varchar(15) DEFAULT NULL,
  `Cliente_Direccion` varchar(100) DEFAULT NULL,
  `Cliente_Observaciones` varchar(150) DEFAULT NULL,
  `Cliente_Imagen_URL` varchar(100) DEFAULT NULL,
  `Cliente_Correo_Electronico` varchar(30) DEFAULT NULL,
  `Cliente_Estado` varchar(10) DEFAULT NULL,
  `Cliente_Calidad` int(11) DEFAULT NULL,
  `Cliente_Numero_Pago` int(11) DEFAULT NULL,
  `Cliente_EsSucursal` tinyint(1) DEFAULT NULL,
  `Cliente_EsExento` tinyint(1) NOT NULL,
  `Aplica_Retencion` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_03_cliente`
--

INSERT INTO `tb_03_cliente` (`Cliente_Cedula`, `Cliente_Nombre`, `Cliente_Apellidos`, `Cliente_Tipo_Cedula`, `Cliente_Carnet_Numero`, `Cliente_Celular`, `Cliente_Telefono`, `Cliente_Fecha_Ingreso`, `Cliente_Pais`, `Cliente_Direccion`, `Cliente_Observaciones`, `Cliente_Imagen_URL`, `Cliente_Correo_Electronico`, `Cliente_Estado`, `Cliente_Calidad`, `Cliente_Numero_Pago`, `Cliente_EsSucursal`, `Cliente_EsExento`, `Aplica_Retencion`) VALUES
(0, 'Cliente Contado', 'Afiliado', 'nacional', 0, '', '', '0000-00-00 00:00:00', 'Costa Rica', 'San Jose', '', '', '', 'activo', 5, 2, 0, 0, 0),
(1, 'Cliente Contado', 'Corriente', 'nacional', 0, '', '', '0000-00-00 00:00:00', 'Costa Rica', 'San Jose', '', '', '', 'activo', 5, 1, 0, 0, 0),
(102100774, 'MARIA ', 'QUIROS  MENESES', 'nacional', 8273, '8336-2270', '2221-3176', '2015-10-05 22:49:10', 'CR', 'SAN JOSE ', '', 'Default.png', 'MQUIROS@ICE.CO.CR', 'activo', 5, 2, 0, 0, 0),
(102310944, 'ZORAIDA', 'PERAZA GARRO', 'nacional', 0, '2221-0539', '2221-0539', '2015-09-14 15:33:38', 'COSTARRICENSE', '5 ESQUINAS DE TIBAS', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(102750322, 'SUZETTE', 'WRIGHT HEADLY', 'nacional', 0, '8775-7606', '8775-7606', '2015-09-21 17:14:24', 'COSTA RICA ', 'CALLE FALLAS, DESAMPARADOS, SAN JOSE, COSTA RICA ', '', 'Default.png', 'jbw062015@gmail.com', 'activo', 5, 2, 0, 0, 0),
(103050183, 'ANITA ', 'ROBLES MENA ', 'nacional', 0, '8534-3411', '2292-6949', '2015-09-21 16:10:52', 'COSTA RICA ', 'IPIS, GUADALUPE, SAN JOSE, COSTA RICA ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(103160037, 'IRIS MARIA ', 'CARVAJAL LOPEZ', 'nacional', 0, '8843-5845', '2223-4836', '2015-09-16 23:49:26', 'CR', 'CALLE 1 Y 3 AVENIDA 16 SAN  JOSE.', '', 'Default.png', 'VALENZCAR@HOTMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(103620892, 'MANUELA', 'MESEN MONGE', 'nacional', 0, '8362-3762', '2237-3549', '2015-09-18 23:19:36', 'CR', 'SAN SEBASTIAN  SAN JOSE.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(103890280, 'ANA VICTORIA', 'MESEN QUIROS', 'nacional', 0, '8972-3331', '8972-3331', '2015-09-16 17:57:36', 'COSTA RICA', 'BARRIO LUJÁN, SAN JOSÉ, COSTA RICA.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(103950395, 'DALIA', 'ORTIZ   ESTRADA', 'nacional', 0, '8782-9808', '2270-1512', '2015-09-16 23:55:51', '', 'DESAMPARADOS  SAN JOSE', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(104200993, 'ANA LORENA', 'CESPEDES SANCHEZ', 'nacional', 0, '7108-4541', '7108-4541', '2015-09-24 15:00:09', 'Costa Rica', 'Curridabat, San José, Costa Rica', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(104320342, 'ELIZABETH', 'SANABRIA MADRIGAL', 'nacional', 0, '8420-0002', '2225-0946', '2015-10-05 18:58:38', 'Costa Rica', 'San Pedro, Montes de Oca, San José, Costa Rica', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(104470461, 'SANDRA ', 'DURAN SEQUEIRA', 'nacional', 0, '7290-5505', '7290-5505', '2015-10-05 23:41:26', 'CR', 'ALAJUELITA SAN JOSE.', '', 'Default.png', 'SANDRADURAN15@YAHOO.COM', 'activo', 5, 2, 0, 0, 0),
(104500518, 'RITA', 'DUARTE VALERIN', 'nacional', 0, '8893-0642', '2259-1319', '2015-09-18 00:23:13', 'CR', 'DESAMPARADOS.', '', 'Default.png', 'RITADUARTE25@HOTMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(104510798, 'MARIA EUGENIA ', 'VILLALOBOS DELGADO ', 'nacional', 0, '8366-2226', '2261-7382', '2015-09-14 15:20:28', 'COSTA RICA ', 'LA AURORA, HEREDIA, COSTA RICA ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(104720595, 'ROXANA ', 'MONTOYA GARITA', 'nacional', 0, '8551-0238', '2273-3303', '2015-09-22 16:06:17', 'COSTA RICA ', 'GRANADILLA NORTE, CURRIDABAT, SAN JOSE, COSTA RICA ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(104790441, 'LEIDY', 'ACUÑA SANDVAL ', 'nacional', 0, '8733-2481', '8552-9618', '2015-10-02 22:40:56', 'CR', 'PAVAS SAN JOSE.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(104970002, 'MARIA EUGENIA', 'HAUG DELGADO', 'nacional', 0, '2410-0483', '2410-0483', '2015-09-18 23:05:03', 'Costa Rica', 'Acosta, San José, Costa Rica.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(104970249, 'ROSA MARÍA ', 'CESPEDES ALVAREZ', 'nacional', 0, '8570-9632', '8570-9632', '2015-09-17 15:37:10', 'Costa Rica', 'Guararí, Heredia, Costa Rica.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(104990597, 'MAYRA ', 'SALAZAR QUESADA', 'nacional', 0, '8993-1623', '2252-0652', '2015-09-22 16:03:59', 'COSTA RICA ', 'HATILLO 5, SAN JOSE, COSTA RICA ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(105030531, 'ROXANNA', 'BOGARÍN ULATE', 'nacional', 0, '8841-1522', '2291-4935', '2015-09-18 23:07:50', 'Costa Rica', 'Hatillo, San José, Costa Rica.', '', 'Default.png', 'katytyka@yahoo.com.mx', 'activo', 5, 2, 0, 0, 0),
(105080290, 'MARIBEL', 'SEAS MENDEZ', 'nacional', 0, '8974-6733', '2276-4497', '2015-09-17 23:11:12', 'COSTARICA', 'SANJOSE, RIO AZUL', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(105140840, 'TIENDA MARY BY GAROT', '(MARIA GODINEZ ZARATE) SAN MARCOS DE TAR', 'nacional', 0, '7017-1781', '2546-1575', '2015-02-13 06:49:37', 'COSTA RICA', 'COSTADO NORTE DEL TEMPLO CATOLICO DE SAN MARCOS', '', 'Default.png', 'fuentedelibertad@gmail.com', 'activo', 5, 2, 1, 0, 1),
(105210007, 'MARIA DE LOS ANGELES', 'VARGAS GARCIA', 'nacional', 0, '8836-8461', '8836-8461', '2015-10-20 20:03:44', 'COSTARRICENSE', 'SAN JOSE', '', 'Default.png', 'mvargas8@hotmail.com', 'activo', 5, 2, 0, 0, 0),
(105300051, 'NIDIA', 'LOPEZ TERCERO', 'nacional', 0, '6003-6028', '2560-5461', '2015-09-30 23:05:27', 'CR', 'LAGUNILLA DE HEREDIA', '', 'Default.png', 'NTERCERO30@HOTMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(105300225, 'TIENDA HEREDIA', '(WILFREDY PIEDRA POCHET)', 'nacional', 0, '2560-\r\n\r\n1557', '2560-1557', '2015-02-13 06:39:29', 'COSTA RICA', 'HEREDIA-DEL \r\n\r\nCORREO 450 OESTE', '', 'Default.png', '', 'activo', 5, 2, 1, 0, 1),
(105440360, 'ELIZABETH', 'AVALOS GODINEZ', 'nacional', 0, '8503-9980', '8503-9980', '2015-10-03 22:05:56', 'Costa Rica', 'San Rafael Abajo, barrio Los Angeles, diagonal al Maxi Pali', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(105460022, '  ILIANA MARITZA DE ', 'SALAZAR HERRERA', 'nacional', 0, '8653-3552', '8653-3552', '2015-09-25 21:41:27', 'COSTARRICENSE', 'POCORA', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(105630498, 'ANA MARLENE ', 'ZUÑIGA MORA ', 'nacional', 0, '8432-1206', '8432-1206', '2015-09-14 15:23:56', '', 'CONCEPCION DE ALAJUELITA, SAN JOSE, COSTA RICA ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(105740838, 'ELIZABETH', 'CAMPOS CASCANTE', 'nacional', 0, '8558-3702', '8558-3702', '2015-09-14 18:03:53', 'COSTA RICA', 'SANJOSE, CALLE BLANCOS', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(105900075, 'MARGARITA', 'CARMONA  ARAYA.', 'nacional', 0, '8963-0596', '8963-0596', '2015-09-18 18:49:23', 'CR', 'SAN SEBASTIAN  SAN JOSE.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(105950215, 'SILVIA ', 'DURAN FERNANDEZ ', 'nacional', 0, '8707-7319', '2273-9513', '2015-09-21 16:06:37', 'COSTA RICA ', 'GRANADILLA NORTE, CURRIDABAT, SAN JOSE', '', 'Default.png', 'silviaduran@costarricense.cr', 'activo', 5, 2, 0, 0, 0),
(105960601, 'MARIA  YOLANDA', 'TENORIO  MORA', 'nacional', 12474, '7066-6571', '2273-5384', '2015-09-21 23:18:47', 'CR', 'GRANADILLA NORTE CURRIDABAT SAN JOSE.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(106020650, 'MARTHA ', 'SALAZAR ARIAS', 'nacional', 0, '8982-5979', '8982-5979', '2015-09-18 23:18:21', 'COSTARICA', 'ALAJUELITA , SANJOSE ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(106220018, 'GRETTEL ', 'JIMENEZ SALAS ', 'nacional', 0, '8569-3755', '8569-3755', '2015-09-22 15:53:48', 'COSTARRICENSE', 'HATILLO CENTRO ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(106250321, 'GRACE', 'VARGAS AZOFEIFA', 'nacional', 0, '8398-3744', '2711-0096', '2015-02-13 06:54:13', 'COSTA RICA', 'GUAPILES', '', 'Default.png', 'gracevargas06@gmail.com', 'activo', 5, 2, 1, 0, 0),
(106340439, 'EMILIA ', 'SOLANO HERRERA', 'nacional', 0, '8992-7922', '2254-8303', '2015-09-17 21:52:28', 'CR', 'ALAJUELITA SAN JOSE.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(106540175, 'OLGA MARTA', 'BURGOS CASTRO', 'nacional', 0, '8315-7526', '8315-7526', '2015-09-26 19:05:09', 'CR', 'ALAJUELITA  SAN JOSE.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(106550218, 'ILIANA', 'MENDEZ MADRIGAL', 'nacional', 0, '8310-1168', '2416-7931', '2015-09-21 22:15:46', 'COSTARICA', 'SANJOSE, PURISCAL', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(106550675, 'RITA ', 'MADRIGAL CHAVARRIA ', 'nacional', 0, '6060-5081', '2416-6420', '2015-09-21 17:01:00', 'COSTA RICA ', 'PURISCAL, SAN JOSE, COSTA RICA ', '', 'Default.png', 'lealfema@hotmail.com', 'activo', 5, 2, 0, 0, 0),
(106560729, 'YOLANDA ', 'MESEN LOPEZ ', 'nacional', 0, '7205-5478', '7205-5478', '2015-09-26 15:32:21', 'COSTA RICA ', 'ACOSTA, SAN JOSE, COSTA RICA ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(106600175, 'VILMA ', 'CARRANZA CORLLA', 'nacional', 0, '8980-1104', '2245-7994', '2015-10-20 19:34:33', 'COSTARRICENSE', 'MORAVIA', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(106740963, 'ROXANA', 'MORA  GUZMAN.', 'nacional', 6062, '8694-3516', '2250-6064', '2015-10-10 18:01:35', 'CR', 'SAN FRANCISCO DE DOS RIOS SAN JOSE.', '', 'Default.png', 'ROXANAMORAGUSMAN@YAHOO.COM', 'activo', 5, 2, 0, 0, 0),
(107010357, 'RITA PATRICIA ', 'MARIN AGUILAR', 'nacional', 0, '8349-2549', '2252-1512', '2015-09-14 20:33:36', '', 'ALAJUELITA SAN JOSE COSTA  RICA ', '', 'Default.png', 'ritapa67@hotmail.com', 'activo', 5, 2, 0, 0, 0),
(107160684, 'KATTIA ISABEL', 'UREÑA DELGADO', 'nacional', 0, '7018-6302', '7018-6302', '2015-10-20 19:59:12', 'COSTARRICENSE', 'SANTA ANA', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(107200749, 'ANA', 'CAMACHO CARVAJAL', 'nacional', 0, '8745-3830', '2230-0200', '2015-09-24 15:11:39', 'Costa Rica', 'Aserrí, San José, Costa Rica', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(107310529, 'ISABEL', 'ABARCA SALAZAR', 'nacional', 0, '8987-4932', '2214-3186', '2015-09-18 18:52:49', 'Costa Rica', 'Hatillo, San José, Costa Rica.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(107320157, 'VICTORIA', 'AGUERO NAVARRO', 'nacional', 0, '8665-3077', '2515-0732', '2015-09-18 00:26:30', 'CR', 'DESAMPARADOS', '', 'Default.png', 'VICTORIA968@HOTMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(107410117, 'NIDIA ', 'RIVERA  ESPINOZA', 'nacional', 0, '8860-8533', '2275-0511', '2015-09-14 20:07:54', 'CR', 'SAN RAFAEL ABAJO DESAMPARADOS.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(107450507, 'MARGOTH ', 'VARGAS VARGAS ', 'nacional', 0, '6077-4633', '6077-4633', '2015-09-18 15:50:31', 'COSTA RICA ', 'PAVAS, SAN JOSE, COSTA RICA ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(107510547, 'BETTINIA', 'ARRIETA ARAYA', 'nacional', 0, '8760-7095', '8760-7095', '2015-09-24 22:07:24', 'COSTARRICENSE', 'RIO FRIO, SARAPIQUI', 'COORDINADOR: CARLOS MARTINEZ MARTINEZ', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(107770677, 'JENNY', 'GUZMAN RAMIREZ', 'nacional', 0, '8943-7524', '2214-2149', '2015-09-14 22:59:41', 'CR', 'HATILLO SAN JOSE.', '', 'Default.png', 'JGUSMANLG@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(107880179, 'ERIC ', 'RAMIREZ VARGAS ', 'nacional', 0, '8328-0041', '8328-0041', '2015-10-20 19:25:43', 'COSTARRICENSE ', 'DESAMPARADOS ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(107930163, 'JENNY  MARIA', 'LIZANO MORALES', 'nacional', 12336, '8646-9845', '8646-9845', '2015-10-17 00:07:37', 'CR', 'CORONADO SAN JOSE', '', 'Default.png', 'JENNYLIZANOMORALES@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(107980962, 'Xiomara', 'Navarro Carpio', 'nacional', 0, '8864-6915', '2250-1349', '2015-09-14 22:29:10', 'Costa Rica', 'Calle Fallas, San José, Costa Rica.', '', 'Default.png', 'x-navarro@gmail.com', 'activo', 5, 2, 0, 0, 0),
(108160063, 'TERESITA ', 'FALLAS HIDALGO', 'nacional', 0, '8707-5567', '0000-0000', '2015-10-06 22:50:04', 'COSTA RICA', 'ACOSTA', '', 'Default.png', 'tere.fallas@hotmail.com', 'activo', 5, 2, 0, 0, 0),
(108170124, 'MARIA GISELLE.', 'BONILLA VALVERDE', 'nacional', 0, '8842-0725', '8842-1025', '2015-10-02 15:11:40', 'CR', 'SAN RAFAEL ABAJO DESAMPARADOS.', '', 'Default.png', 'GUISBONILLA@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(108260755, 'IRIS', 'MIRANDA AGUERO', 'nacional', 0, '7102-0519', '8513-4807', '2015-09-14 15:37:40', 'COSTARRICENSE', 'ESCAZU', '', 'Default.png', 'IRISMA48@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(108400588, 'PRISCILLA ', 'MARQUEZ MAYORGA', 'nacional', 0, '8349-8522', '2232-4024', '2015-09-25 17:27:53', 'COSTARICA', 'PAVAS, SANJOSE', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(108550171, 'LAURA', 'SALAS CASTRO', 'nacional', 0, '8330-0714', '8330-0714', '2015-10-07 14:48:27', '', 'ESCAZU CENTRO SAN JOSE.', '', 'Default.png', 'LCASTRO1314@HOTMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(109080335, 'Olga ', 'Campos Barboza', 'nacional', 0, '8479-4817', '8479-4817', '2015-09-14 18:00:24', 'Costa Rica', 'Ciudad Colón', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(109120559, 'ALEJANDRA', ' JIMENEZ DUARTE', 'nacional', 0, '8378-1699', '8378-1699', '2015-09-21 16:03:32', 'COSTA RICA ', 'SAN FRANCISCO DE DOS RIOS, SAN JOSE, COSTA RICA ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(109250134, 'LAURA', 'UREÑA VILLEGAS ', 'nacional', 0, '8405-8607', '8405-8607', '2015-09-22 15:55:19', 'COSTARRICENSE ', 'HEREDIA, SAN PABLO', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(109280965, 'MARIA DE LOS ANGELES', 'RETANA MATARRITA ', 'nacional', 0, '8369-8698', '8369-8698', '2015-09-26 21:11:29', 'COSTA RICA ', 'SAN JOSECITO, ALAJUELITA, SAN JOSE, COSTA RICA', '', 'Default.png', 'maret18@gamil.com', 'activo', 5, 2, 0, 0, 0),
(109340511, 'MARIA JULIA ', 'VASQUEZ JIMENEZ ', 'nacional', 0, '8322-0691', '8322-0691', '2015-09-18 15:46:39', 'COSTA RICA ', 'ESCAZU CENTRO, SAN JOSE, COSTA RICA ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(109940234, 'KARLA BORBON', 'BORBON GARITA', 'nacional', 0, '6249-4805', '8830-6814', '2015-09-22 23:04:50', 'Costa Rica', 'San Rafael de Montes de Oca, 200m sur y 25 este del bar Leyenda', '', 'Default.png', '1978sofi@gmail.com', 'activo', 5, 2, 0, 0, 0),
(110260997, 'SHIRLEY SUSANA', 'ZAMORA DELGADO', 'nacional', 0, '8689-3152', '8601-6102', '2015-09-17 19:42:54', '', 'HEREDIA', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(110450776, 'Adriana', 'Sibaja Monge', 'nacional', 0, '8438-3859', '2410-1510', '2015-09-14 17:58:42', 'Costa Rica', 'Acosta', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(110480131, 'MARCIA ', 'PORRAS MASIS', 'nacional', 0, '6229-7082', '6229-7082', '2015-09-26 22:43:01', 'COSTARICA', 'PAVAS, SANJOSE', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(110560181, 'CINDY', 'CASTRO PEREZ', 'nacional', 0, '8859-8512', '8859-2512', '2015-09-14 19:46:08', 'CR', 'CURRIDABAD  SAN JOSE.', '', 'Default.png', 'CINCASTRO79@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(110690328, 'JESSICA', 'LARA LARA', 'nacional', 0, '8324-3206', '7102-9695', '2015-09-24 15:18:00', 'Costa Rica', 'San Miguel de Desamparados, San José, Costa Rica.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(110790390, 'JOHANNA', 'CALDERON HERNANDEZ', 'nacional', 0, '8650-1001', '8650-1001', '2015-09-25 18:24:35', 'Costa Rica', 'Escazú, San José, Costa Rica', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(111090702, 'SUSAN', 'SOLÍS QUESADA', 'nacional', 0, '8400-6112', '8400-6112', '2015-09-25 18:26:50', 'Costa Rica', 'Tirrases, San José, Costa Rica', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(111100360, 'SHIRLEY', 'VENEGAS BARQUERO', 'nacional', 0, '8860-9421', '2252-6212', '2015-10-16 17:43:49', 'CR', 'COLONIA 15 HATILLO', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(111140108, 'KATHERINE MARIA ', 'TAYLOR GUEVARA ', 'nacional', 0, '8457-6367', '8457-6367', '2015-09-22 15:52:03', 'COSTARRICENSE ', 'HATILLO', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(111170978, 'HEIDY MAIRENE', 'CASTRO  ARIAS.', 'nacional', 0, '7234-2319', '2282-1476', '2015-09-16 15:45:19', 'CR', 'SANTA ANA SAN JOSE.', '', 'Default.png', 'MAIRENE.CASTRO@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(111180319, 'YESENIA  ', 'ARCIA   DELGADO', 'nacional', 0, '8708-0143', '8708-0143', '2015-09-23 23:26:35', 'CR.', 'HEREDIA.', 'COMPRA EN  HEREDIA.', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(111290552, 'ANDREA', 'MARIN ANGULO', 'nacional', 0, '8559-0090', '2229-9306', '2015-09-18 23:10:45', 'COSTARICA', 'PURRAL DE GUADALUPE, SAN JOSE', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(111350064, 'WENDY', 'SERRANO CASTILLO', 'nacional', 0, '8817-8589', '8817-8589', '2015-10-09 17:51:16', 'Costa Rica', 'Tres Ríos, Costa Rica', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(111350826, 'RUTH MARY.', 'ZUÑIGA DELGADO.', 'nacional', 0, '8684-0763', '2294-8825', '2015-10-06 20:16:29', 'CR', 'CORONADO SAN JOSE.', 'NOTA- COMPRA EN GEMAS BY GAROTAS.', 'Default.png', 'MARIZU126@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(111390115, 'JACQUELINE PAOLA', 'TORRES RODRIGUEZ', 'nacional', 0, '8776-2687', '2275-1356', '2015-09-12 20:27:25', '', 'SAN RAFAEL ABAJO DESAMPARADOS', '', 'Default.png', 'JACKYTRODRIGUEZA@HOTMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(111410790, 'NATALIA', 'SALAS GARITA', 'nacional', 0, '8822-1360', '2417-1291', '2015-09-19 20:50:03', 'COSTARICA', 'PURISCAL, SANJOSE', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(111470848, 'LAURA', 'RODRIGUEZ GUTIERREZ', 'nacional', 0, '8788-2240', '8788-2240', '2015-09-14 15:31:08', 'COSTARRICENSE ', 'ALAJUELITA', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(111490109, 'GABRIELA .', 'ARGUEDAS  FALLAS.', 'nacional', 8899, '8329-0796', '2252-4680', '2015-09-29 21:58:54', 'CR', 'COSEPCION ABAJO ALAJUELITA.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(111600133, 'CAROLINA ', 'QUIROS AGUILAR ', 'nacional', 0, '8717-8664', '8717-8664', '2015-09-21 15:36:42', 'COSTA RICA', 'ZAPOTE, SAN JOSE, COSTA RICA ', '', 'Default.png', 'cquiros83@hotmail.com', 'activo', 5, 2, 0, 0, 0),
(111670353, 'TERESA', 'MORALES SOTO', 'nacional', 0, '8875-7623', '2245-3667', '2015-09-30 23:24:38', 'Costa Rica', 'Guadalupe, San José, Costa Rica', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(111680310, 'RELFA', 'CASTILLO RODRIGUEZ', 'nacional', 0, '8814-6795', '8814-6795', '2015-09-29 17:48:49', 'Costa Rica', 'Paso Ancho, San José, Costa Rica', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(111750771, 'JESSICA', 'SANCHEZ BARRANTES', 'nacional', 0, '8947-4352', '8947-4352', '2015-09-25 18:29:17', '', 'Curridabat, San José, Costa Rica.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(111850292, 'JULIO CÉSAR', 'ZUMBADO ESQUIVEL', 'nacional', 0, '7068-0063', '7088-3514', '2015-09-17 22:58:09', 'Costa Rica', 'Hatillo 2, San José, Costa Rica.', '', 'Default.png', 'julcef1893@gmail.com', 'activo', 5, 2, 0, 0, 0),
(111890872, 'LETICIA', 'MORA BONILLA', 'nacional', 0, '8957-1004', '8957-1004', '2015-09-24 22:02:57', 'COSTARRICENSE', 'CARIARI, POCOCI, LIMON', 'COORDINADOR: CARLOS MARTINEZ MARTINEZ', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(111990800, 'MARGARITA', 'ZUÑIGA RIVERA ', 'nacional', 0, '8980-9599', '2288-4940', '2015-09-16 17:34:12', '', 'SAN ANTONIA DE ESCAZU, SAN JOSE, COSTA RICA ', '', 'Default.png', 'mzuniga208@hotmail.com', 'activo', 5, 2, 0, 0, 0),
(112050164, 'SHARON', 'TORRES  LEAN', 'nacional', 0, '8895-5430', '2240-7837', '2015-10-19 17:42:09', 'CR', 'TIBAS SAN JOSE', '', 'Default.png', 'STLNAM@YAHOO.ES', 'activo', 5, 2, 0, 0, 0),
(112060834, 'ANGIE', 'MONTERO CESPEDES', 'nacional', 0, '7108-4541', '7108-4541', '2015-09-22 15:56:52', 'COSTARRICENSE', 'CURRIDABAT', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(112120657, 'MELISSA', 'SANCHEZ CASTRO', 'nacional', 0, '8491-8394', '2410-3633', '2015-09-17 17:40:24', 'Costa Rica', 'Acosta, San José, Costa Rica.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(112180031, 'MARIELA ', 'MORA MONGE', 'nacional', 0, '8430-0834', '4031-2440', '2015-09-30 18:14:03', 'COSTARICA', 'SAN MIGUEL DE HEREDIA', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(112180310, 'Estefany', 'Vargas Aguero', 'nacional', 0, '8860-2846', '8860-2846', '2015-09-14 21:46:22', 'Costa Rica', 'San José, Hatillo Centro.', '', 'Default.png', 'sthephany2408@gmail.com', 'activo', 5, 2, 0, 0, 0),
(112330074, 'DINIA', 'FALLAS SOLANO', 'nacional', 0, '8686-7625', '8686-7625', '2015-10-03 22:03:48', 'COSTARICA', 'ASERRI, SANJOSE', '', 'Default.png', 'diniafs80@gmail.com', 'activo', 5, 2, 0, 0, 0),
(112510178, 'MARÍA LAURA', 'CAMPOS VARGAS', 'nacional', 0, '7125-2459', '2271-4511', '2015-09-16 16:19:00', 'Costa Rica', 'Curridabat Centro, San José Costa Rica.', '', 'Default.png', 'lauracv85@gmail.com', 'activo', 5, 2, 0, 0, 0),
(112650952, 'NATHALIE MELISSA', 'CASTILLO RAMIREZ', 'nacional', 0, '8621-6442', '8621-6442', '2015-09-12 20:55:56', '', 'HATILLO 5', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(112720079, 'LUCRECIA ', 'SERRANO MORA ', 'nacional', 0, '6070-4229', '2510-2773', '2015-09-18 15:53:45', 'COSTA RICA ', 'SAN MIGUEL, DESAMPARADOS, SAN JOSE, COSTA RICA ', '', 'Default.png', 'lucreciaserranomora@gmail.com', 'activo', 5, 2, 0, 0, 0),
(112770841, 'JETZABEL', 'MUÑOZ CARTIN', 'nacional', 0, '8820-6907', '8820-6907', '2015-09-14 16:07:00', 'COSTARRICENSE', 'DESAMPARADOS', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(112860303, 'VILVIA', 'LEON JIMENEZ', 'nacional', 0, '8836-8461', '8836-8461', '2015-10-20 20:06:39', 'COSTARRICENSE', 'SAN FELPE DE ALAJUELITA', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(112920952, 'SANDRA', 'PRADO ROJAS', 'nacional', 0, '8597-3050', '8597-3050', '2015-10-05 22:54:39', 'CR', 'SAN GABRIEL DE ASERRI.', '', 'Default.png', 'SPRADO@ASERRI.CO.CR', 'activo', 5, 2, 0, 0, 0),
(113050545, 'KAROL VANESSA.', 'HERRERA QUESADA.', 'nacional', 11027, '8858-6872', '2279-4193', '2015-09-14 19:54:18', 'CR', 'TRES RIOS CARTAGO.', '', 'Default.png', 'KAVHA20@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(113050635, 'KRISTEL', 'TOLEDO JIMENEZ', 'nacional', 0, '8312-0741', '8312-0741', '2015-09-14 15:32:36', 'COSTARRICENSE', 'DESAMPARADOS', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(113070309, 'ANGIE', 'PRADO  RODRIGUEZ', 'nacional', 0, '8878-5422', '2250-1326', '2015-09-18 23:26:28', 'CR', 'DESAMPARADOS SAN JOSE', '', 'Default.png', 'APRADO87@HOTMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(113090399, 'ERICKA ', 'PEÑA SOTO', 'nacional', 0, '8435-4499', '4034-1016', '2015-09-21 15:43:34', 'COSTA RICA ', 'NARANJO, ALAJUELA, COSTA RICA ', '', 'Default.png', 'epena@ice.go.cr', 'activo', 5, 2, 0, 0, 0),
(113150590, 'DAHIANA', 'SOLIS VARGAS ', 'nacional', 0, '7104-3566', '2281-3043', '2015-09-21 18:41:29', 'COSTA RICA ', 'SAN PEDRO, SAN JOSE, COSTA RICA ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(113170336, 'Mariana', 'Zuñiga Trejos', 'nacional', 0, '7011-1007', '2224-0712', '2015-09-16 17:22:35', 'Costa Rica', 'San Pedro Montes de Oca, San José Costa Rica.', '', 'Default.png', 'marzutre@yahoo.com', 'activo', 5, 2, 0, 0, 0),
(113240513, 'HANNIA', 'GARRO BENAVIDES', 'nacional', 0, '8658-7314', '2501-8533', '2015-09-21 15:12:04', 'Costa Rica.', 'Aserrí, San José, Costa Rica', '', 'Default.png', 'hnaflorga@gmail.com', 'activo', 5, 2, 0, 0, 0),
(113480057, 'ALEJANDRA', 'VIQUEZ CONTRERAS', 'nacional', 0, '7048-9860', '2296-2200', '2015-09-28 22:38:33', 'Costa Rica', 'Uruca, San José, Costa Rica', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(113590797, 'EVANY  YORJANY', 'CORDERO  MARIN.', 'nacional', 0, '6114-2765', '6114-2765', '2015-09-17 21:40:28', 'CR', 'DESAMPARADOS  SAN JOSE.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(113620323, 'SUGEY FAINIER', 'MONTES ZAMORA', 'nacional', 0, '7113-0604', '2250-5762', '2015-09-18 20:58:52', 'COSRA RICA ', 'CURRIDABAT, SAN JOSE, COSTA RICA ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(113690392, 'DIANA ', 'GUZMAN GODINEZ', 'nacional', 0, '8994-3201', '8994-3201', '2015-10-07 15:43:07', 'COSTARICA', 'PURISCAL, SANJOSE', '', 'Default.png', 'dianaguzman.g@gmail.com', 'activo', 5, 2, 0, 0, 0),
(113690962, 'SOFIA', 'MENDEZ ROJAS.', 'nacional', 0, '8689-6321', '2286-0128', '2015-09-17 21:37:32', 'CR', 'SAN SEBASTIAN SAN JOSE.', '', 'Default.png', 'SOFIMR0887@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(113740791, 'ERICK  RODRIGO.', 'MARTINEZ CAMACHO.', 'nacional', 0, '7293-9262', '2789-9505', '2015-09-22 21:48:23', 'CR', 'GOLFITO  PUNTARENAS.', 'COMPRA EN  PEREZ ZELEDON.', 'Default.png', 'ERICKMC11@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(113760208, 'MARCELA ', 'VEGA    BARQUERO', 'nacional', 0, '7209-6578', '7209-6578', '2015-09-16 23:53:27', 'CR', 'DESAMPARADOS  SAN JOSE.', '', 'Default.png', 'MARCELAVEGA44@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(113950635, 'JOYCE', 'MENDEZ NUÑEZ', 'nacional', 0, '8925-7942', '2529-0395', '2015-09-25 22:27:31', 'COSTARICA', 'MORAVIA, SANJOSE', '', 'Default.png', 'mendeznunez2667@gmail.com', 'activo', 5, 2, 0, 0, 0),
(114220871, 'KARLA VANESSA', 'VARGAS CERDAS', 'nacional', 0, '8364-4996', '2230-3481', '2015-09-25 15:48:42', 'COSTARICA', 'ASERRI, SANJOSE', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(114470185, 'KAREN .', 'FUENTES  SANCHEZ.', 'nacional', 0, '8718-7449', '8718-7449', '2015-09-17 21:42:51', 'CR', 'CURRIDABAD  SAN JOSE.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(114480451, 'KARINA', 'MONTERO ROMERO', 'nacional', 0, '8556-3816', '8556-3816', '2015-09-14 20:02:28', 'CR', 'GUADALUPE  SAN JOSE.', '', 'Default.png', 'KRIMONRO@HOTMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(114640332, 'WENDY', 'MATAMOROS VARGAS', 'nacional', 0, '8320-5733', '2261-2507', '2015-09-24 15:09:13', 'Costa Rica', 'Heredia, Costa Rica', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(114790333, '  KAREN ANDREA', 'ALVAREZ BUSTOS', 'nacional', 0, '8529-8938', '8529-8938', '2015-09-25 21:00:45', 'COSTARRICENSE', 'CARTAGENA GUANACASTE', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(115370525, 'DAYANA DE LOS ANGELE', 'HIDALGO MORA', 'nacional', 0, '7114-5944', '7039-3450', '2015-09-12 22:19:44', '', 'ALAJUELITA', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(115380868, 'KARINA ', 'GOMEZ VALVERDE ', 'nacional', 0, '8312-5514', '8312-5514', '2015-09-16 18:03:34', 'COSTA RICA ', 'LA AURORA DE ALAJUELITA, SAN JOSE, COSTA RICA ', '', 'Default.png', 'kgomezv@lafise.com', 'activo', 5, 2, 0, 0, 0),
(116150583, 'DANIELA ', 'ROMERO RAMIREZ', 'nacional', 0, '8601-3487', '8601-3487', '2015-10-16 17:34:18', 'CR', 'DESAMPARADOS  SAN JOSE', '', 'Default.png', 'DANI.ROMERO0518@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(116150612, 'PRISCILA', 'FERNANDEZ PICADO ', 'nacional', 0, '8418-3200', '8418-3200', '2015-09-21 18:37:25', 'COSTA RICA ', 'PURISCAL, SAN JOSE, COSTA RICA ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(116770852, 'YOSELIN', 'ARIAS VEGA', 'nacional', 0, '7059-1588', '2275-2367', '2015-10-06 19:19:12', 'CR', 'CONSEPCION ABAJO ALJUELITA SAN JOSE.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(202570102, 'LILLIAM', 'OVARES LEON', 'nacional', 7376, '8551-6658', '2275-2947', '2015-10-02 23:56:44', 'CR', 'CONSEPCION DE ALAJUELITA SAN JOSE', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(202630830, 'MARIA ESTHER', 'ARROYO  MUÑOZ', 'nacional', 0, '2226-5501', '2226-5501', '2015-09-16 23:44:33', 'CR', 'SAN SEBASTIAN SAN JOSE', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(203070167, 'MARTA', 'SALAZAR DELGADO', 'nacional', 0, '8695-2822', '2260-5436', '2015-09-18 15:12:37', 'COSTARRICENSE ', 'HEREDIA, MIRA FLORES', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(203310584, 'IRMA ', 'MAYORGA RODRIGUEZ ', 'nacional', 0, '8786-2098', '8786-2098', '2015-09-26 15:34:18', 'COSTA RICA ', 'UPALA, ALAJUELA, COSTA RICA ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(203330251, 'ANA', 'RODRIGUEZ RODRIGUEZ', 'nacional', 0, '8943-6447', '8943-6447', '2015-10-03 21:57:57', 'Costa Rica', 'Zarcero, Alajuela, Costa Rica', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(203670725, 'TIENDA SHENOA ', '(PALMARES) ', 'nacional', 0, '8965-5715', '2452-0014', '2015-02-14 10:21:57', 'COSTA RICA', 'COSTADO NORTE DEL \r\n\r\nPARQUE CENTRAL MALL CEPSA', 'Jose Mario Cespedes Lobo  CED: 2-690-784 \r\n\r\n\\\r\\\\n\\\\r\\\\nTRANSPORTE PALMARES ', 'Default.png', 'distrib.shenoa@gmail.com', 'activo', 5, 2, 1, 0, 1),
(203690284, 'MAYRA LIDIETH ', 'QUESADA MORA', 'nacional', 0, '8908-6831', '8908-6831', '2015-09-22 16:08:38', 'COSTA RICA ', 'EL CARMEN DE GUADALUPE, SAN JOSE, COSTA RICA ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(203760403, 'ANA LORENA ', 'LIZANO JIMENEZ ', 'nacional', 0, '8757-4898', '2416-4226', '2015-09-18 15:43:47', 'COSTA RICA ', 'PURISCAL, SAN JOSE, COSTA RICA', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(203940310, 'CARMEN LIDIA GERARDA', ' VILLEGAS RODRIGUEZ', 'nacional', 0, '6208-2162', '6208-2162', '2015-09-25 21:05:26', 'COSTARRICENSE', 'SAN RAMON', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(204220286, 'MYRIAM BEATRIZ ', 'SEQUEIRA ARGUELLO', 'nacional', 0, '8721-6649', '4702-9118', '2015-09-19 19:40:27', 'COSTA RICA ', 'ATENAS, BARRIO FATIMA, ALAJUELA, COSTA RICA ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(204240296, 'AIDA', 'RODRIGUEZ FERNANDEZ ', 'nacional', 0, '8742-1740', '2276-4202', '2015-09-19 19:38:01', 'COSTA RICA ', 'CURRIDABAT, SAN JOSE, COSTA RICA', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(205090370, 'SONIA CRISTINA', 'GRANADOS  SEGURA', 'nacional', 0, '6040-4135', '6040-4135', '2015-10-20 20:01:28', 'CR', 'LINDA VISTA RIO AZUL CARTAGO', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(205230211, 'MAGALI ', 'ALVAREZ ALFARO', 'nacional', 0, '8486-6922', '2766-6534', '2015-09-22 20:35:57', '', 'SARAPIQUI', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(205700333, 'CAROL ', 'MEJIAS BOLAÑOS', 'nacional', 0, '8820-0054', '8820-0054', '2015-10-20 20:09:49', 'COSTARRICENSE', 'PALMARES', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(206330992, 'MARIA CRISTINA.', 'MENDEZ BLANCO.', 'nacional', 0, '8314-8411', '8314-8411', '2015-09-17 22:35:38', 'CR', 'DESAMPARADOS  SAN JOSE.', '', 'Default.png', 'CTINAMEBIA@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(207530411, 'DANIELA', 'VALLE OBANDO', 'nacional', 10824, '6064-8042', '8501-4934', '2015-10-05 22:57:55', 'CR', 'LOS CHILES ALAJUELA', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(302000647, 'TIENDA CARTAGO', '(MIRYAM RODRIGUEZ BRENES)', 'nacional', 0, '2553-4037', '2553-4037', '2015-02-13 06:26:04', 'COSTA RICA', '350 OESTE IGLESIA CAPUCHINOS ', '', 'Default.png', '', 'activo', 5, 2, 1, 0, 1),
(302010584, 'TIENDA TURRIALBA', '(MARIELOS MORA TORRES)', 'nacional', 0, '2556-2914', '2556-2914', '2015-02-13 10:51:11', 'COSTA RICA', 'CARTAGO-TURRIALBA', '', 'Default.png', '', 'activo', 5, 2, 1, 0, 1),
(302700767, 'ROSITA', 'SALGUERO FERNANDEZ', 'nacional', 0, '8518-1711', '8518-1711', '2015-09-21 15:09:24', 'Costa Rica.', 'Barrio Luján, San José, Costa Rica.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(302730152, 'TIENDA RYS (ROSA MAR', ' SALAZAR ABARCA)', 'nacional', 0, '8829-7663', '2279-6844', '2015-06-06 10:23:23', '', 'CARTAGO - TRES \r\n\r\nRIOS', '', 'Default.png', '', 'activo', 5, 2, 1, 0, 1),
(303000122, 'MARIA DE LOS ANGELES', 'SANCHEZ  RODRIGUEZ.', 'nacional', 0, '8636-7066', '8636-7066', '2015-09-17 22:26:43', 'CR', 'TURRIALBA  CARTAGO.', '', 'Default.png', 'MAYISANCHEZ.R@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(303230930, 'NORMA', 'AGUILAR SALAZAR', 'nacional', 0, '8651-9963', '2294-3983', '2015-09-25 21:59:11', 'Costa Rica', 'Ipis de Goicoechea, San José, Costa Rica', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(303300242, 'TIENDA TURRIALBA', '(ALEJANDRA QUESADA PORRAS', 'nacional', 0, '2557-\r\n\r\n7575', '2557-7575', '2015-02-13 10:49:02', 'COSTA RICA', 'CARTAGO-\r\n\r\nTURRIALBA', '', 'Default.png', '', 'activo', 5, 2, 1, 0, 1),
(303370124, 'TIENDA PARAISO-CARTA', '(IEANA MONTOYA RODRIGUEZ)', 'nacional', 0, '2575-1439', '2575-1439', '2015-02-13 10:57:25', 'COSTA RICA', 'CARTAGO-PARAISO', '', 'Default.png', '', 'activo', 5, 2, 1, 0, 1),
(303900212, 'JOHANNA', 'OBANDO VARGAS', 'nacional', 0, '8875-3082', '8875-3082', '2015-09-30 23:09:16', '', 'CONSEPCION ARRIBA ALAJUELITA SAN JOSE.', '', 'Default.png', 'JOBANDO@HOTMAIL.CS', 'activo', 5, 2, 0, 0, 0),
(304210288, 'MAYRA ALEJANDRA', 'ROMERO CORRALES', 'nacional', 0, '8507-6238', '8507-6238', '2015-09-25 21:43:23', 'COSTARRICENSE', 'TURRIALBA', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(401080744, 'NIDIA DEL CARMEN', 'MONTOYA SANCHEZ', 'nacional', 0, '8764-7827', '2265-1948', '2015-09-21 20:45:06', 'Costa Rica', 'Heredia, Costa Rica', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(401230530, 'RODRIGO ANTONIO', 'OTAROLA SOLIS', 'nacional', 0, '6001-5604', '2262-0058', '2015-09-21 23:40:17', '', 'BARVA DE HEREDIA, SANTA LUCIA', '', 'Default.png', 'rotarola1960@gmail.com', 'activo', 5, 2, 0, 0, 0),
(401370518, 'MERCEDES', 'CHACON SOLORZANO', 'nacional', 0, '6207-1196', '6207-1196', '2015-10-20 19:58:35', 'CR', 'TIRRASES DE CURRIDABAD SAN JOSE', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(401960881, 'HEILYN VANESSA', 'VINDAS GARITA', 'nacional', 0, '8302-4507', '2261-3629', '2015-10-10 20:03:29', 'CR', 'GUARARI EL CACAO CASA #213.', '', 'Default.png', 'HV2G87@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(402030529, 'Monica', 'Fernandez Arce', 'nacional', 0, '7109-6881', '7109-6881', '2015-09-14 15:24:07', '', 'La Carpio', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(402240098, 'ARGIERE VANESSA ', 'GARCIA RIVAS', 'nacional', 0, '8420-7947', '2718-6447', '2015-09-25 20:48:06', 'COSTARRICENSE', 'BATAN, LIMON', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(501450458, 'XINIA', 'MARTINEZ MARTINEZ', 'nacional', 0, '8632-3312', '8632-3312', '2015-09-14 19:57:06', 'CR', 'LA AURORA DE ALAJUELITA.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(501650334, 'MARJORIE', 'TORRES  ULLOA.', 'nacional', 12763, '8583-5287', '2233-5303', '2015-09-14 19:37:07', 'CR', 'BARRIO LA CRUZ SAN JOSE', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(501820700, 'MARIA MERCEDES', 'OBANDO', 'nacional', 0, '8355-7633', '8355-7633', '2015-09-18 23:40:40', 'Costa Rica', 'Lomas, San Josè, Costa Rica.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(501850567, 'JUANA', 'JIMENEZ MENDOZA', 'nacional', 0, '8989-6594', '2292-2140', '2015-09-22 15:13:59', 'Costa Rica', 'San Antonio de Coronado, San José, Costa Rica.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(502160443, 'JULIETA.', 'SALAZAR  VEGA.', 'nacional', 11988, '8398-4157', '8398-4157', '2015-09-17 22:28:56', 'CR', 'BARRIAL DE HEREDIA.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(502180823, 'MIRIAM', 'CRUZ SANCHEZ', 'nacional', 0, '8658-8512', '8658-8512', '2015-09-24 22:25:57', 'COSTARRICENSE', 'CARIARI, POCOCI, LIMON', 'COORDINADOR: CARLOS MARTINEZ MARTINEZ', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(502310338, 'MARIA ISABEL', 'CANO MARTINEZ', 'nacional', 5402, '8762-3503', '2229-5455', '2015-10-19 20:15:40', 'CR', 'PURRAL DE GUADALUPE  SAN JOSE', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(502640547, 'MARILIA', 'OBANDO GUTIERREZ', 'nacional', 0, '8601-1741', '8601-1741', '2015-09-17 15:34:16', 'Costa Rica', 'San Juan de Dios de Desamparados.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(503080691, 'MARILYN', 'ANGULO  ENRIQUEZ', 'nacional', 0, '8788-8470', '2684-9079', '2015-09-16 17:52:19', 'CR', 'NICOYA   GUANACASTE.', '', 'Default.png', 'MARILYNANGULOENRIQUEZ@GMAIL.CO', 'activo', 5, 2, 0, 0, 0),
(503420414, 'LAURA VANESSA', 'DÍAZ MORERA', 'nacional', 0, '7081-9801', '7081-9801', '2015-09-16 17:51:52', ' COSTA RICA', 'MOZOTAL, IPIS DE GUADALUPE, SAN JOSÉ COSTA RICA.', '', 'Default.png', 'laudm04@gmail.com', 'activo', 5, 2, 0, 0, 0),
(503420777, 'LILLIANA YADIRA ', 'RODRIGUEZ MARCHENA', 'nacional', 0, '8319-4065', '2651-1374', '2015-09-25 20:59:19', 'COSTARRICENSE', 'BELEN CARRILLO,GUANACASTE', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(503590364, 'HAZEL.', 'ROVIRA   ALVAREZ', 'nacional', 0, '8853-8649', '8853-8649', '2015-09-19 19:52:00', 'CR.', 'LIBERIA  GUANACASTE.', 'COMPRA  A  LIGIA CAICEDO.', 'Default.png', 'HAZELROVIRA@HOTMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(504040685, 'MELISA', 'BLANCO CORTES', 'nacional', 0, '8866-1881', '8866-1881', '2015-09-14 15:34:55', 'COSTARRICENSE', 'UPALA', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(504390835, 'REINA MARGARITA', 'CENTENO ESPINOZA', 'nacional', 0, '7015-9077', '7015-9077', '2015-10-09 17:55:04', 'Costa Rica', 'Acosta, San José, Costa Rica', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(600570554, 'Luz María', 'Alvarado Madrigal', 'nacional', 0, '8354-3637', '2252-5980', '2015-09-16 16:20:49', 'Costa Rica', 'San Bosco, San José Costa Rica.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(600790976, 'CARLOS ALBERTO', 'MARTINEZ MARTINEZ', 'nacional', 0, '8446-2733', '2767-0918', '2015-09-24 20:38:06', 'COSTARRICENSE', 'CARIARI, POCOCI, LIMON', 'ISMAEL FLORES', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(600990153, 'GUISELLE .', 'VELASQUEZ SOLERA.', 'nacional', 13477, '8811-4500', '2234-2212', '2015-09-19 21:03:58', 'CR', 'CURRIDABAT SAN JOSE.', '', 'Default.png', 'GUIS2511@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(601160247, 'JUSTINA ', 'RAMIREZ HERNANDEZ', 'nacional', 0, '6233-4644', '6233-4644', '2015-09-12 20:40:27', '', 'ALAJUELITA', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(601180237, 'PAULINA ', 'ELIZONDO AZOFEIFA ', 'nacional', 0, '6231-0159', '2289-3834', '2015-09-26 15:36:37', 'COSTA RICA ', 'ESCAZU CENTRO, SAN JOSE, COSTA RICA ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(601320774, 'SHIRLEY MARIA ', 'QUESADA OVIEDO ', 'nacional', 0, '8827-2892', '8827-2892', '2015-09-21 17:10:33', 'COSTA RICA ', 'SAN JOSE CENTRO, COSTA RICA ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(601410021, 'LIDIA', 'BARAHONA HERRERA.', 'nacional', 0, '8542-8550', '8542-8550', '2015-09-19 20:47:54', 'CR', 'HEREDIA ', 'COMPRA EN HEREDIA.', 'Default.png', 'LIDIA.58@LIBE.COM', 'activo', 5, 2, 0, 0, 0),
(601540758, 'Nidia', 'Jara Rodriguez', 'nacional', 0, '8647-7008', '8647-7008', '2015-09-14 21:48:00', 'Costa Rica', 'Aurora de Alajuelita, San José Costa Rica.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(601620122, 'ANA  ELIETH ', 'CORRALES  VEGA.', 'nacional', 0, '6196-2608', '6196-2608', '2015-09-14 23:45:41', 'CR', 'TIBAS  SAN JOSE', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(601650986, 'FLOR MARIA ', 'ARAYA MONTERO', 'nacional', 0, '8426-2075', '8426-2075', '2015-09-18 23:16:11', 'COSTARICA', 'GRANADILLA NORTE DE CURRIDABAT, SANJOSE', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(601730644, 'BRAULIA  MARIA.', 'AGUERO CEDEÑO.', 'nacional', 0, '7049-8413', '2232-9976', '2015-09-17 21:34:08', 'CR', 'HATILLO 8 SAN JOSE.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(602020721, 'GLADYS IVETH', 'GOMEZ MORALES', 'nacional', 0, '8421-4672', '8421-4672', '2015-09-24 21:58:43', 'COSTARRICENSE', 'POCOCI, LIMON', 'COORDINADOR: CARLOS MARTINEZ MARTINEZ', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(602150660, 'YESENIA', 'QUESADA  GONZALEZ', 'nacional', 13274, '8430-7291', '8430-7291', '2015-10-17 00:01:55', 'CR', 'RITA POCOCI  LIMON', '', 'Default.png', 'ACNEBEBE27@HOTMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(602420246, 'AIDA', 'GONZALEZ   MORA.', 'nacional', 0, '8922-5843', '2250-4283', '2015-09-22 18:49:40', 'CR', 'TORRES MOLINOS  DESAMPARADOS.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(602680992, 'HENRY DIMARCO ', 'VILLEGAS (LIBERIA)', 'nacional', 0, '8872-5167', '8872-5167', '2015-02-13 11:11:36', 'COSTA RICA', 'GUANACASTE-LIBERIA, BARRIO CAPULLIN DE LA IGLESIA', '', 'Default.png', '', 'activo', 5, 2, 1, 0, 0),
(602800056, 'BERNAL', 'VARGAS   PRENDAS.', 'nacional', 0, '7018-0796', '2293-3368', '2015-09-18 18:47:15', '', 'BELEN DE HEREDIA', '', 'Default.png', 'CPA66A@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(602840307, 'YAHAIRA', 'JARA PEREZ', 'nacional', 0, '8703-7083', '8664-7380', '2015-09-28 22:40:56', 'Costa Rica', 'Curridabat, San José, Costa Rica', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(603890450, 'YERLI', 'MAROTO CALDERON', 'nacional', 0, '8973-2333', '8973-2333', '2015-09-28 22:35:13', 'COSTARICA', 'SABANILLA MONTES DE OCA, SANJOSE', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(700481016, 'DAMARIS', 'ALVAREZ  MORALES', 'nacional', 0, '8502-5910', '8502-5910', '2015-10-02 23:59:22', 'CR', 'HATILLO CENTRO SAN JOSE', '', 'Default.png', 'DAMARISALVAREZ@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(700590939, 'MIRIAM MARGARET.', 'MC CARTHY.', 'nacional', 11215, '8896-5581', '2290-8367', '2015-09-21 15:34:03', 'CR', 'LAS MAGNOLIAS  LA URUCA SAN JOSE.', '', 'Default.png', '3MVACTCOR@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(700720728, 'LIGIA', 'VINDAS MORAGA.', 'nacional', 0, '8326-1292', '2798-0143', '2015-09-19 20:53:01', 'CR', 'LIMON CENTRO', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(700740468, 'MARIA DE LOS ANGELES', 'SALGUERO FERNANDEZ', 'nacional', 0, '8461-9857', '8461-9857', '2015-09-17 23:13:20', 'COSTARICA', 'TRES X DE TURRIALBA', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(700780247, 'CIANNY', 'GORDON MORALES', 'nacional', 0, '8349-9288', '0000-0000', '2015-10-05 23:54:49', 'COSTA RICA', 'CALLE BLANCOS', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(700830448, 'YADIRA LORENA', 'ORTIZ RUIZ', 'nacional', 0, '7020-3515', '7020-3515', '2015-09-25 20:56:53', '    COSTARRICEN', 'LIMON', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(700970173, 'LAURA', 'JIMENEZ SOLANO', 'nacional', 0, '8337-7233', '2710-4468', '2015-10-05 15:53:29', 'Costa Rica', 'GUAPILES', '', 'Default.png', 'lauraj037@gmail.com', 'activo', 5, 2, 0, 0, 0),
(701130509, 'TAISIULING', 'MARTINEZ SALAS', 'nacional', 0, '8427-3512', '2798-0961', '2015-09-25 21:57:26', 'COSTARRICENSE', 'LIMON CENTRO', '', 'Default.png', 'tmartinez@mariainmaculadalimon', 'activo', 5, 2, 0, 0, 0),
(701130900, 'ANGELA MARIA', 'REÑASCO CAJINA', 'nacional', 0, '8717-3767', '2709-8062', '2015-09-24 17:57:33', 'COSTARRICENSE', 'CARIARI, LIMON', 'Ismael Flores', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(701240355, 'MARIA GABRIELA', 'SERRANO CORRALES', 'nacional', 0, '6218-3214', '2710-5403', '2015-09-24 18:04:40', 'COSTARRICENSE', 'GUAPILES, LIMON', 'COORDINADOR: ANGELA REÑASCO CAJINA', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(701240987, 'YAMILETH', 'BORBON SALAZAR', 'nacional', 0, '8991-6975', '8991-6975', '2015-09-21 16:22:06', 'COSTARICA', 'LIMON, GUAPILES', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(701270344, 'CINTIA GISELA', ' MASIS SANCHEZ', 'nacional', 0, '8645-8304', '8645-8304', '2015-09-26 21:35:55', 'COSTARRICENSE', 'SIQUIRRES', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(701350531, 'VIVIAN SARAY', 'VIDAURRE REYES', 'nacional', 0, '8717-3767', '8717-3767', '2015-09-24 20:31:46', 'COSTARRICENSE', 'GUAPILES, LIMON', 'COORDINADOR: ANGELA REÑASCO CAJINA', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(701450985, 'ANA YANCY .', 'RAMIREZ CHAVEZ.', 'nacional', 0, '5701-0884', '5701-0884', '2015-09-14 20:04:50', 'CR', 'GUACIMO LIMON.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(701630077, 'LIBNI', 'FALLAS  ROJAS', 'nacional', 0, '8864-5528', '2274-1544', '2015-10-19 20:12:30', 'CR', 'GUATUZO PATARRA DESAMPARADOS SAN JOSE', '', 'Default.png', 'LIBNIFR@GMAIL.COM', 'activo', 5, 2, 0, 0, 0),
(701870386, 'CLAUDIA', 'RODRIGUEZ SANCHEZ ', 'nacional', 0, '8323-0746', '8323-0746', '2015-09-14 15:36:09', 'COSATRRICENSE', 'SIQUIRRES, GUAPILES', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(701900411, 'MARIAN', 'CRUZ ROJAS (TIENDA GUAPILES)', 'nacional', 0, '8545-0794', '2711-0096', '2015-09-29 21:04:09', '', 'GUAPILES', 'HIJA DE GRACE VARGAS AZOFEIFA', 'Default.png', '', 'activo', 5, 2, 1, 0, 1),
(702040635, 'JOISY ', 'HIDALGO FERNANDEZ', 'nacional', 0, '8766-7392', '8766-7392', '2015-09-30 18:12:07', 'COSTARICA', 'GUAPILES', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(702190813, 'CANDY PRISCILA', 'PEREZ REÑASCO', 'nacional', 0, '8827-2634', '8827-2634', '2015-09-24 20:28:36', 'COSTARRICENSE', 'GUAPILES, LIMON', 'COORDINADOR: ANGELA REÑASCO CAJINA', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(702360713, 'TATIANA ', 'CHAVES VARGAS', 'nacional', 0, '6113-8694', '6113-8694', '2015-10-10 21:46:39', 'CR', 'GUADALUPE SAN JOSE.', 'COMPRA EN GUADALUPE', 'Default.png', 'TATIANA.CHAVES.VARGAS@GMAIL.CO', 'activo', 5, 2, 0, 0, 0),
(800500042, 'CLAUDIA', 'RAMIREZ MIXTER', 'nacional', 0, '6108-8599', '6108-8599', '2015-10-05 23:25:06', 'COSTARICA', 'CURRIDABAT, SANJOSE', '', 'Default.png', 'c10t08.ale@gmail.com', 'activo', 5, 2, 0, 0, 0),
(800960494, 'LUZ MARINA ', 'ORTEGA ORTEGA ', 'nacional', 0, '8559-3918', '2518-1261', '2015-09-21 15:59:02', 'COSTA RICA ', 'TRES RIOS, CARTAGO', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(800980303, 'ANGELLA OFELIA', 'ALCANTARA OCHICUA', 'nacional', 0, '8743-2584', '2274-1791', '2015-09-16 17:49:34', 'Costa Rica', 'TIRRASES, SAN JOSÉ COSTA RICA.', '', 'Default.png', 'kellyalcantara2011@hotmail.com', 'activo', 5, 2, 0, 0, 0),
(800990293, 'YORLINA ', 'RODRIGUEZ BRICEÑO', 'nacional', 0, '8325-4784', '2290-1533', '2015-09-21 17:06:59', 'COSTA RICA ', 'LA CARPIO, SAN JOSE, COSTA RICA ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(801080795, 'TIENDA LIBERIA', '(LIGIA \r\n\r\nCAICEDO ROA)', 'nacional', 0, '2665-3149', '2665-3149', '2015-02-13 06:37:40', 'COSTA RICA', 'GUANACASTE- LIBERIA', '', 'Default.png', '', 'activo', 5, 2, 1, 0, 1),
(900420815, 'LUCIA', 'ARCE CERDAS', 'nacional', 0, '8923-4124', '2249-1925', '2015-10-20 19:30:53', 'COSTARRICENSE', 'CIUDAD COLON', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(900690725, 'MARVIN', 'ZUÑIGA GARITA', 'nacional', 0, '8484-1655', '8484-1655', '2015-09-28 22:16:49', 'Costa Rica', 'Alajuelita, San José, Costa Rica', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(3101350785, 'GAROTAS BONITAS', 'SAN JOSE', 'juridica', 0, '2221-8127', '2221-8127', '2015-04-21 10:31:10', '', 'AV.SEGUNDA, DEL BANCO POPULAR 175 mts Sur.', '', 'Default.png', '', 'activo', 5, 2, 1, 0, 0),
(3101481279, 'TIENDA COMPAÑIA', 'INVERSIONES \r\n\r\nIPANEMA', 'juridica', 0, '2233-3619', '2233-3619', '2015-02-13 06:01:06', 'COSTA RICA', 'EDIFICIO LAS ARCADAS, PLANTA BAJA, AV SEGUNDA \r\n\r\n', 'CATALINA ECHEVERRIA BETANCUR\\\\r\\\\nRepresentante Legal', 'Default.png', 'info@ipanemacr.com', 'activo', 5, 3, 1, 0, 1),
(3106672246, 'DISTRIBUIDORA ARENAS', 'DE NICOYA S.C.', 'juridica', 0, '2685-4042', '2685-4042', '2015-02-13 10:55:17', 'COSTA RICA', 'GUANACASTE-NICOYA, 75MTS ESTE DE SUPERCOMPRO', '', 'Default.png', '', 'activo', 5, 2, 1, 0, 1),
(31013507850, 'TIENDA DESAMPARADOS', 'SUCURSAL', 'pasaporte', 0, '2259-6968', '2250-1980', '2015-06-03 11:30:17', '', 'DESAMPARADOS', '', 'Default.png', '', 'activo', 5, 2, 1, 0, 1),
(117000798632, 'TIENDA SUEÑOS', 'DE GAROTAS', 'residencia', 0, '2770-2279', '2770-2279', '2015-02-13 06:03:20', 'COSTA RICA', 'COSTADO \r\n\r\nOESTE DE LA MUNICIPALIDAD DE PEREZ ZELEDON                              \r\n\r\n', 'ALBERTO RODRIGUEZ RODRIGUEZ\\\\r\\\\nRepresentante Legal', '0117000798632.jpg', 'msgarotasbonitas@gmail.com', 'activo', 5, 2, 1, 0, 1),
(117001386032, 'TIENDA GAROTAS', 'BY GAROTAS', 'residencia', 0, '2441-8724', '2441-8724', '2015-02-13 05:57:01', 'COSTA RICA', 'PLAZA \r\n\r\nIGLESIA CORAZON DE JESUS, 25MTS SUR, LOCAL 2                        ', 'ROSA ESNEDA BETANCUR ESTRADA\\\\\\\\r\\\\\\\\nRepresentante Legal', '117001386032.jpg', 'garotasbonitas@ice.co.cr', 'activo', 5, 2, 1, 0, 1),
(155800454801, 'JANIXIA GASSALY', 'VANEGAS GARCIA', 'residencia', 0, '7015-2519', '2263-0529', '2015-09-14 18:34:33', '', 'HEREDIA', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155800948623, 'DELIA EMILIA.', 'THOSOM  CHACON.', 'residencia', 0, '8580-8638', '2213-3079', '2015-09-29 21:52:39', 'CR', 'LOMAS DEL RIO PAVAS SAN JOSE.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155801632026, 'GEORGINA', 'ROJAS MARENCO', 'residencia', 0, '8554-2730', '8554-2730', '2015-09-21 15:15:18', 'COSTARICA', 'SANJOSE, LA CARPIO', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155802741036, 'PATRICIA', 'PEREZ  LEAL', 'residencia', 0, '6212-1786', '6212-1786', '2015-09-14 20:15:30', 'CR', 'SAGRADA  FAMILIA  SAN JOSE.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155803106914, 'KARELIA MARGARITA', 'JUAREZ ARAUZ', 'residencia', 0, '8782-7549', '7083-6493', '2015-09-12 21:21:56', '', 'RIO AZUL', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155803251433, 'JAMILETH', 'MARIN JARQUIN ', 'residencia', 0, '2433-4504', '2249-3839', '2015-09-17 23:26:02', 'COSTA RICA ', 'CUIDAD COLON, SAN JOSE, COSTA RICA', '', 'Default.png', 'yamimaja@hotmail.com', 'activo', 5, 2, 0, 0, 0),
(155806957026, 'ANA  BENIGNA.', 'ESPINOZA  RIVERA.', 'residencia', 0, '6084-9116', '6084-8116', '2015-09-17 22:31:51', 'CR', 'EL CARMEN GUADALUPE SAN JOSE.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155807070428, 'MERCEDES LIZETH', 'PEREZ  JUAREZ', 'residencia', 0, '6144-3338', '6144-3338', '2015-09-14 20:12:18', '', 'ESCAZU SAN JOSE.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155807298028, 'MARÍA AUXILIADORA', 'JIMENEZ', 'residencia', 0, '8733-7913', '2226-3681', '2015-09-18 23:10:50', 'Nicaragua', 'San Sebastián, San José, Costa Rica.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155807741218, 'ADELA YESSENIA', 'SAAVEDRA ROMERO', 'residencia', 0, '8320-2233', '2256-1348', '2015-09-12 21:03:19', '', 'SAN JOSE', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155807931122, 'MARIA TERESA', 'AGUIRRE', 'residencia', 0, '8312-4348', '6187-0954', '2015-09-14 16:05:48', 'NICARAGUENSE', 'HATILLO', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155808046533, 'NUBIA', 'SOLIS  SOLIS.', 'residencia', 0, '7264-7200', '7264-7200', '2015-09-18 18:52:10', 'CR', 'SAN  ANTONIO DE BELEN  HEREDIA.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155808693015, 'AIDA MARIA ', 'MERCADO', 'residencia', 0, '8962-2754', '8734-4218', '2015-09-18 23:13:20', 'COSTARICA', 'ESCAZU, SANOJOSE', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155809633212, 'MARLENE', 'ALVAREZ MAIRENA.', 'residencia', 0, '8812-7623', '2248-2068', '2015-10-05 23:45:44', 'CR', 'BARRIO MEXICO SAN JOSE .', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155809725004, 'PAULINA', 'PANIAGUA PANIAGUA', 'residencia', 0, '8317-1073', '8317-1073', '2015-09-28 22:15:01', 'Nicaragua', 'Zapote, San José, Costa Rica', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155809733503, 'CARMINIA MARIA', 'TORREZ', 'residencia', 0, '7043-0568', '7043-0568', '2015-09-22 16:01:44', 'NICARAGUENSE ', 'BARRIO DE LOS ANGELES', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0);
INSERT INTO `tb_03_cliente` (`Cliente_Cedula`, `Cliente_Nombre`, `Cliente_Apellidos`, `Cliente_Tipo_Cedula`, `Cliente_Carnet_Numero`, `Cliente_Celular`, `Cliente_Telefono`, `Cliente_Fecha_Ingreso`, `Cliente_Pais`, `Cliente_Direccion`, `Cliente_Observaciones`, `Cliente_Imagen_URL`, `Cliente_Correo_Electronico`, `Cliente_Estado`, `Cliente_Calidad`, `Cliente_Numero_Pago`, `Cliente_EsSucursal`, `Cliente_EsExento`, `Aplica_Retencion`) VALUES
(155810529025, 'MARÍA AUXILIADORA', 'CASTRO LOPEZ', 'residencia', 0, '6288-6909', '6288-6909', '2015-09-17 15:40:33', 'Nicaragua', 'Sagrada Familia, San José, Costa Rica.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155810661106, 'MARÍA DEL CARMEN', 'VILLAREAL SOLÍS', 'residencia', 0, '6060-4762', '6060-4762', '2015-09-17 15:43:08', 'Nicaragua', 'Escazú, San José, Costa Rica.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155811412116, 'LOURDES DEL SOCORRO', 'MENDEZ', 'residencia', 0, '6229-7082', '6229-7082', '2015-09-26 22:45:04', 'COSTARICA', 'HEREDIA', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155811888418, 'MARIA DEL CARMEN.', 'AGUILAR GUTIERREZ.', 'residencia', 0, '6246-1827', '2219-3490', '2015-09-14 19:41:25', 'CR', 'DESAMPARADOS  SAN JOSE.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155811913222, 'HELLEN DAMARIS', 'DIAZ LOAISIGA', 'residencia', 0, '8393-5623', '8577-7994', '2015-09-18 18:56:56', 'Nicaragua', 'Limón, Costa Rica.', ' Estaba ingresada con otro apellido y el número de identificación incorrecto.', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155812468422, 'MARGOTH', 'CANALES ARIAS', 'residencia', 0, '8663-4672', '8663-4672', '2015-09-30 22:04:39', 'Nicaragua', 'Cariari, Limòn, Costa Rica', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155812664609, 'MARIA', 'MENDOZA CHEVEZ', 'residencia', 0, '6028-4895', '2244-8851', '2015-09-21 15:07:08', 'Nicaragua', 'Santo Domingo de Heredia, Costa Rica.', '', 'Default.png', 'mariamendozach21@gmail.com', 'activo', 5, 2, 0, 0, 0),
(155812891522, 'ALMA NINOSKA.', 'CALDERA  CENTENO.', 'residencia', 10408, '6257-8950', '6257-8950', '2015-09-21 20:53:12', 'CR', 'RIO AZUL CARTAGO.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155813006128, 'Antonia del Carmen', 'Obando Suazo', 'residencia', 0, '8691-4035', '8691-4035', '2015-09-14 15:22:45', 'Costa Rica', 'La Carpio', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155814140035, 'MARIA RAQUEL ', 'CARRILLO  ABURTO', 'residencia', 0, '7095-0512', '7095-0512', '2015-10-19 15:32:51', 'CR', 'ESCAZU  SAN JOSE', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155815157614, 'YAMILETH', 'QUINO ORTEGA', 'residencia', 9041, '8914-0612', '8914-0612', '2015-09-14 23:43:07', 'CR', 'PASO ANCHO  SAN JOSE.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155816000800, 'LESLIE', 'CRUZ UMAÑA', 'residencia', 0, '6205-7416', '6205-7416', '2015-10-05 19:01:48', 'Nicaragua', 'Lomas, Pavas, San José, Costa Rica', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155818209113, 'AMPARO', 'GARCIA LOPEZ', 'residencia', 0, '8457-2449', '8457-2449', '2015-09-18 19:31:46', 'COSTARICA', 'SANJOSE, CARPIO', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155818688909, 'ANA PATRICIA ', 'PALMA CARVAJAL', 'residencia', 0, '8571-7929', '8397-8875', '2015-09-12 20:32:37', '', 'GUACHIPELIN', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155819157725, 'LUCIANA ', 'RODRIGUEZ AREVALO', 'residencia', 0, '6010-2859', '0000-0000', '2015-10-05 23:53:28', 'NICARAGUA', 'Barrio san josé de alajuela', '', 'Default.png', 'lucianarodriguezarevalo@hotmai', 'activo', 5, 2, 0, 0, 0),
(155819375031, 'DEYANIRA ', 'URBINA  LUMBIS', 'residencia', 12929, '7137-8400', '7137-8400', '2015-09-16 23:41:21', 'CR', 'RIO AZUL  CARTAGO ', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155819666120, 'ABIGAIL', 'CAMPOS GARCIA.', 'residencia', 11207, '8786-1259', '8786-1259', '2015-09-26 19:01:07', 'CR', 'COCO ALAJUELA.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155819888426, 'KARLA PATRICIA ', 'AROZTEGUI PEREZ ', 'residencia', 0, '6129-2486', '6037-5581', '2015-09-21 15:53:22', 'COSTA RICA ', 'SAN ANTONIO DE ESCAZU', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155820603200, 'ERICKA', 'VEGA MARTÍNEZ', 'residencia', 0, '7198-4813', '7198-4813', '2015-09-17 22:53:48', 'Nicaragua', 'Desamparados, San José, Costa Rica.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155821228435, 'NAIMA', 'COREA  NUÑEZ.', 'residencia', 0, '6002-9067', '6002-9067', '2015-09-16 17:55:20', 'CR', 'CRISTO REY  SAN JOSE.', '', 'Default.png', '', 'activo', 5, 2, 0, 0, 0),
(155822148221, 'SAMANTHA', 'SILVA HERNANDEZ', 'residencia', 0, '8587-3749', '8587-3749', '2015-09-16 17:56:04', 'NICARAGUA', 'RÍO AZUL, CARTAGO, COSTA RICA', '', 'Default.png', 'samasihe92@gmail.com', 'activo', 5, 2, 0, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_04_articulos_proforma`
--

CREATE TABLE IF NOT EXISTS `tb_04_articulos_proforma` (
`Articulo_Proforma_Id` int(11) NOT NULL,
  `Articulo_Proforma_Codigo` varchar(30) DEFAULT NULL,
  `Articulo_Proforma_Descripcion` varchar(45) DEFAULT NULL,
  `Articulo_Proforma_Cantidad` int(11) DEFAULT NULL,
  `Articulo_Proforma_Descuento` varchar(45) DEFAULT NULL,
  `Articulo_Proforma_Exento` varchar(45) DEFAULT NULL,
  `Articulo_Proforma_No_Retencion` tinyint(1) NOT NULL,
  `Articulo_Proforma_Precio_Unitario` varchar(45) DEFAULT NULL,
  `Articulo_Proforma_Precio_Final` double NOT NULL,
  `Articulo_Proforma_Imagen` varchar(45) DEFAULT NULL,
  `TB_10_Proforma_Proforma_Consecutivo` int(11) NOT NULL,
  `TB_10_Proforma_TB_02_Sucursal_Codigo` int(11) NOT NULL,
  `TB_10_Proforma_Proforma_Vendedor_Codigo` int(11) NOT NULL,
  `TB_10_Proforma_Proforma_Vendedor_Sucursal` int(11) NOT NULL,
  `TB_10_Proforma_TB_03_Cliente_Cliente_Cedula` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_05_familia`
--

CREATE TABLE IF NOT EXISTS `tb_05_familia` (
  `Familia_Codigo` int(11) NOT NULL,
  `Familia_Nombre` varchar(20) DEFAULT NULL,
  `Familia_Observaciones` varchar(150) DEFAULT NULL,
  `Familia_Fecha_Creacion` timestamp NULL DEFAULT NULL,
  `Familia_Fecha_Desactivacion` timestamp NULL DEFAULT NULL,
  `Familia_Creador` varchar(45) DEFAULT NULL,
  `TB_02_Sucursal_Codigo` int(11) NOT NULL,
  `Familia_Estado` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_05_familia`
--

INSERT INTO `tb_05_familia` (`Familia_Codigo`, `Familia_Nombre`, `Familia_Observaciones`, `Familia_Fecha_Creacion`, `Familia_Fecha_Desactivacion`, `Familia_Creador`, `TB_02_Sucursal_Codigo`, `Familia_Estado`) VALUES
(0, 'FAMILIA BASE', '', '2015-02-07 11:42:47', NULL, 'eprendas', 0, 1),
(0, 'FAMILIA BASE', '', '2015-02-07 17:42:47', NULL, 'eprendas', 1, 1),
(0, 'FAMILIA BASE', '', '2015-02-07 17:42:47', NULL, 'eprendas', 2, 1),
(0, 'FAMILIA BASE', '', '2015-02-07 17:42:47', NULL, 'eprendas', 3, 1),
(0, 'FAMILIA BASE', '', '2015-02-07 11:42:47', NULL, 'eprendas', 4, 1),
(0, 'SHOWROOM', '', '2015-06-03 03:34:06', NULL, 'eprendas', 5, 1),
(0, 'FAMILIA BASE', '', '2015-02-07 17:42:47', NULL, 'eprendas', 6, 1),
(0, 'FAMILIA BASE', '', '2015-02-07 17:42:47', NULL, 'eprendas', 7, 1),
(1, 'SIN DESCUENTOS', '', '2015-02-07 11:43:01', NULL, 'eprendas', 0, 1),
(1, 'SIN DESCUENTOS', '', '2015-02-07 17:43:01', NULL, 'eprendas', 1, 1),
(1, 'SIN DESCUENTOS', '', '2015-02-07 17:43:01', NULL, 'eprendas', 2, 1),
(1, 'SIN DESCUENTOS', '', '2015-02-07 17:43:01', NULL, 'eprendas', 3, 1),
(1, 'SIN DESCUENTOS', '', '2015-02-07 11:43:01', NULL, 'eprendas', 4, 1),
(1, 'SIN DESCUENTOS', '', '2015-02-07 17:43:01', NULL, 'eprendas', 6, 1),
(1, 'SIN DESCUENTOS', '', '2015-02-07 17:43:01', NULL, 'eprendas', 7, 1),
(2, 'DESCUENTOS 10-12-20', '', '2015-02-07 11:43:22', NULL, 'eprendas', 0, 1),
(2, 'DESCUENTOS 10-12-20', '', '2015-02-07 17:43:22', NULL, 'eprendas', 1, 1),
(2, 'DESCUENTOS 10-12-20', '', '2015-02-07 17:43:22', NULL, 'eprendas', 2, 1),
(2, 'DESCUENTOS 10-12-20', '', '2015-02-07 17:43:22', NULL, 'eprendas', 3, 1),
(2, 'DESCUENTOS 10-12-20', '', '2015-02-07 11:43:22', NULL, 'eprendas', 4, 1),
(2, 'DESCUENTOS 10-12-20', '', '2015-02-07 17:43:22', NULL, 'eprendas', 6, 1),
(2, 'DESCUENTOS 10-12-20', '', '2015-02-07 17:43:22', NULL, 'eprendas', 7, 1),
(3, 'MERCADERIA EXPORTACI', '', '2015-02-07 11:43:42', NULL, 'eprendas', 0, 1),
(3, 'MERCADERIA EXPORTACI', '', '2015-02-07 17:43:42', NULL, 'eprendas', 1, 1),
(3, 'MERCADERIA EXPORTACI', '', '2015-02-07 17:43:42', NULL, 'eprendas', 2, 1),
(3, 'MERCADERIA EXPORTACI', '', '2015-02-07 17:43:42', NULL, 'eprendas', 3, 1),
(3, 'MERCADERIA EXPORTACI', '', '2015-02-07 11:43:42', NULL, 'eprendas', 4, 1),
(3, 'MERCADERIA EXPORTACI', '', '2015-02-07 17:43:42', NULL, 'eprendas', 6, 1),
(3, 'MERCADERIA EXPORTACI', '', '2015-02-07 17:43:42', NULL, 'eprendas', 7, 1),
(4, 'SECCION VENDEDORA (A', '', '2015-02-07 11:44:06', NULL, 'eprendas', 0, 1),
(4, 'SECCION VENDEDORA (A', '', '2015-02-07 17:44:06', NULL, 'eprendas', 1, 1),
(4, 'SECCION VENDEDORA (A', '', '2015-02-07 17:44:06', NULL, 'eprendas', 2, 1),
(4, 'SECCION VENDEDORA (A', '', '2015-02-07 17:44:06', NULL, 'eprendas', 3, 1),
(4, 'SECCION VENDEDORA (A', '', '2015-02-07 11:44:06', NULL, 'eprendas', 4, 1),
(4, 'SECCION VENDEDORA (A', '', '2015-02-07 17:44:06', NULL, 'eprendas', 6, 1),
(4, 'SECCION VENDEDORA (A', '', '2015-02-07 17:44:06', NULL, 'eprendas', 7, 1),
(5, 'DESCUENTO 10/40', '', '2015-02-07 11:44:21', NULL, 'eprendas', 0, 1),
(5, 'DESCUENTO 10/40', '', '2015-02-07 17:44:21', NULL, 'eprendas', 1, 1),
(5, 'DESCUENTO 10/40', '', '2015-02-07 17:44:21', NULL, 'eprendas', 2, 1),
(5, 'DESCUENTO 10/40', '', '2015-02-07 17:44:21', NULL, 'eprendas', 3, 1),
(5, 'DESCUENTO 10/40', '', '2015-02-07 11:44:21', NULL, 'eprendas', 4, 1),
(5, 'DESCUENTO 10/40', '', '2015-02-07 17:44:21', NULL, 'eprendas', 6, 1),
(5, 'DESCUENTO 10/40', '', '2015-02-07 17:44:21', NULL, 'eprendas', 7, 1),
(6, 'EXHIBIDORES ACRILICO', '', '2015-02-07 11:45:20', NULL, 'eprendas', 0, 1),
(6, 'EXHIBIDORES ACRILICO', '', '2015-02-07 17:45:20', NULL, 'eprendas', 1, 1),
(6, 'EXHIBIDORES ACRILICO', '', '2015-02-07 17:45:20', NULL, 'eprendas', 2, 1),
(6, 'EXHIBIDORES ACRILICO', '', '2015-02-07 17:45:20', NULL, 'eprendas', 3, 1),
(6, 'EXHIBIDORES ACRILICO', '', '2015-02-07 11:45:20', NULL, 'eprendas', 4, 1),
(6, 'EXHIBIDORES ACRILICO', '', '2015-02-07 17:45:20', NULL, 'eprendas', 6, 1),
(6, 'EXHIBIDORES ACRILICO', '', '2015-02-07 17:45:20', NULL, 'eprendas', 7, 1),
(7, 'IPANEMA PLATA 9.25', '', '2015-02-07 11:45:33', NULL, 'eprendas', 0, 1),
(7, 'IPANEMA PLATA 9.25', '', '2015-02-07 17:45:33', NULL, 'eprendas', 1, 1),
(7, 'IPANEMA PLATA 9.25', '', '2015-02-07 17:45:33', NULL, 'eprendas', 2, 1),
(7, 'IPANEMA PLATA 9.25', '', '2015-02-07 17:45:33', NULL, 'eprendas', 3, 1),
(7, 'IPANEMA PLATA 9.25', '', '2015-02-07 11:45:33', NULL, 'eprendas', 4, 1),
(7, 'IPANEMA PLATA 9.25', '', '2015-02-07 17:45:33', NULL, 'eprendas', 6, 1),
(7, 'IPANEMA PLATA 9.25', '', '2015-02-07 17:45:33', NULL, 'eprendas', 7, 1),
(8, 'PERFUMES', '', '2015-02-07 11:45:50', NULL, 'eprendas', 0, 1),
(8, 'PERFUMES', '', '2015-02-07 17:45:50', NULL, 'eprendas', 1, 1),
(8, 'PERFUMES', '', '2015-02-07 17:45:50', NULL, 'eprendas', 2, 1),
(8, 'PERFUMES', '', '2015-02-07 17:45:50', NULL, 'eprendas', 3, 1),
(8, 'PERFUMES', '', '2015-02-07 11:45:50', NULL, 'eprendas', 4, 1),
(8, 'PERFUMES', '', '2015-02-07 17:45:50', NULL, 'eprendas', 6, 1),
(8, 'PERFUMES', '', '2015-02-07 17:45:50', NULL, 'eprendas', 7, 1),
(9, 'RELOJES', '', '2015-02-07 11:45:58', NULL, 'eprendas', 0, 1),
(9, 'RELOJES', '', '2015-02-07 17:45:58', NULL, 'eprendas', 1, 1),
(9, 'RELOJES', '', '2015-02-07 17:45:58', NULL, 'eprendas', 2, 1),
(9, 'RELOJES', '', '2015-02-07 17:45:58', NULL, 'eprendas', 3, 1),
(9, 'RELOJES', '', '2015-02-07 11:45:58', NULL, 'eprendas', 4, 1),
(9, 'RELOJES', '', '2015-02-07 17:45:58', NULL, 'eprendas', 6, 1),
(9, 'RELOJES', '', '2015-02-07 17:45:58', NULL, 'eprendas', 7, 1),
(10, 'LENTES UNISEX', '', '2015-02-07 11:46:12', NULL, 'eprendas', 0, 1),
(10, 'LENTES UNISEX', '', '2015-02-07 17:46:12', NULL, 'eprendas', 1, 1),
(10, 'LENTES UNISEX', '', '2015-02-07 17:46:12', NULL, 'eprendas', 2, 1),
(10, 'LENTES UNISEX', '', '2015-02-07 17:46:12', NULL, 'eprendas', 3, 1),
(10, 'LENTES UNISEX', '', '2015-02-07 11:46:12', NULL, 'eprendas', 4, 1),
(10, 'LENTES UNISEX', '', '2015-02-07 17:46:12', NULL, 'eprendas', 6, 1),
(10, 'LENTES UNISEX', '', '2015-02-07 17:46:12', NULL, 'eprendas', 7, 1),
(11, 'HOGAR 25%', '', '2015-02-07 11:46:32', NULL, 'eprendas', 0, 1),
(11, 'HOGAR 25%', '', '2015-02-07 17:46:32', NULL, 'eprendas', 1, 1),
(11, 'HOGAR 25%', '', '2015-02-07 17:46:32', NULL, 'eprendas', 2, 1),
(11, 'HOGAR 25%', '', '2015-02-07 17:46:32', NULL, 'eprendas', 3, 1),
(11, 'HOGAR 25%', '', '2015-02-07 11:46:32', NULL, 'eprendas', 4, 1),
(11, 'HOGAR 25%', '', '2015-02-07 17:46:32', NULL, 'eprendas', 6, 1),
(11, 'HOGAR 25%', '', '2015-02-07 17:46:32', NULL, 'eprendas', 7, 1),
(12, 'BAZAR 15%', '', '2015-02-07 11:46:42', NULL, 'eprendas', 0, 1),
(12, 'BAZAR 15%', '', '2015-02-07 17:46:42', NULL, 'eprendas', 1, 1),
(12, 'BAZAR 15%', '', '2015-02-07 17:46:42', NULL, 'eprendas', 2, 1),
(12, 'BAZAR 15%', '', '2015-02-07 17:46:42', NULL, 'eprendas', 3, 1),
(12, 'BAZAR 15%', '', '2015-02-07 11:46:42', NULL, 'eprendas', 4, 1),
(12, 'BAZAR 15%', '', '2015-02-07 17:46:42', NULL, 'eprendas', 6, 1),
(12, 'BAZAR 15%', '', '2015-02-07 17:46:42', NULL, 'eprendas', 7, 1),
(13, 'BAZAR 10%', '', '2015-02-07 11:47:03', NULL, 'eprendas', 0, 1),
(13, 'BAZAR 10%', '', '2015-02-07 17:47:03', NULL, 'eprendas', 1, 1),
(13, 'BAZAR 10%', '', '2015-02-07 17:47:03', NULL, 'eprendas', 2, 1),
(13, 'BAZAR 10%', '', '2015-02-07 17:47:03', NULL, 'eprendas', 3, 1),
(13, 'BAZAR 10%', '', '2015-02-07 11:47:03', NULL, 'eprendas', 4, 1),
(13, 'BAZAR 10%', '', '2015-02-07 17:47:03', NULL, 'eprendas', 6, 1),
(13, 'BAZAR 10%', '', '2015-02-07 17:47:03', NULL, 'eprendas', 7, 1),
(14, 'ENVIOS Y MENSAJERIA', '', '2015-02-07 11:47:16', NULL, 'eprendas', 0, 1),
(14, 'ENVIOS Y MENSAJERIA', '', '2015-02-07 17:47:16', NULL, 'eprendas', 1, 1),
(14, 'ENVIOS Y MENSAJERIA', '', '2015-02-07 17:47:16', NULL, 'eprendas', 2, 1),
(14, 'ENVIOS Y MENSAJERIA', '', '2015-02-07 17:47:16', NULL, 'eprendas', 3, 1),
(14, 'ENVIOS Y MENSAJERIA', '', '2015-02-07 11:47:16', NULL, 'eprendas', 4, 1),
(14, 'ENVIOS Y MENSAJERIA', '', '2015-02-07 17:47:16', NULL, 'eprendas', 6, 1),
(14, 'ENVIOS Y MENSAJERIA', '', '2015-02-07 17:47:16', NULL, 'eprendas', 7, 1),
(15, 'BILLETERAS', '', '2015-02-07 11:47:33', NULL, 'eprendas', 0, 1),
(15, 'BILLETERAS', '', '2015-02-07 17:47:33', NULL, 'eprendas', 1, 1),
(15, 'BILLETERAS', '', '2015-02-07 17:47:33', NULL, 'eprendas', 2, 1),
(15, 'BILLETERAS', '', '2015-02-07 17:47:33', NULL, 'eprendas', 3, 1),
(15, 'BILLETERAS', '', '2015-02-07 11:47:33', NULL, 'eprendas', 4, 1),
(15, 'BILLETERAS', '', '2015-02-07 17:47:33', NULL, 'eprendas', 6, 1),
(15, 'BILLETERAS', '', '2015-02-07 17:47:33', NULL, 'eprendas', 7, 1),
(16, 'ESTUCHE LENTES', '', '2015-02-07 11:49:22', NULL, 'eprendas', 0, 1),
(16, 'ESTUCHE LENTES', '', '2015-02-07 17:49:22', NULL, 'eprendas', 1, 1),
(16, 'ESTUCHE LENTES', '', '2015-02-07 17:49:22', NULL, 'eprendas', 2, 1),
(16, 'ESTUCHE LENTES', '', '2015-02-07 17:49:22', NULL, 'eprendas', 3, 1),
(16, 'ESTUCHE LENTES', '', '2015-02-07 11:49:22', NULL, 'eprendas', 4, 1),
(16, 'ESTUCHE LENTES', '', '2015-02-07 17:49:22', NULL, 'eprendas', 6, 1),
(16, 'ESTUCHE LENTES', '', '2015-02-07 17:49:22', NULL, 'eprendas', 7, 1),
(17, 'BOLSOS', '', '2015-02-07 11:49:34', NULL, 'eprendas', 0, 1),
(17, 'BOLSOS', '', '2015-02-07 17:49:34', NULL, 'eprendas', 1, 1),
(17, 'BOLSOS', '', '2015-02-07 17:49:34', NULL, 'eprendas', 2, 1),
(17, 'BOLSOS', '', '2015-02-07 17:49:34', NULL, 'eprendas', 3, 1),
(17, 'BOLSOS', '', '2015-02-07 11:49:34', NULL, 'eprendas', 4, 1),
(17, 'BOLSOS', '', '2015-02-07 17:49:34', NULL, 'eprendas', 6, 1),
(17, 'BOLSOS', '', '2015-02-07 17:49:34', NULL, 'eprendas', 7, 1),
(18, 'FAJAS', '', '2015-02-07 11:49:42', NULL, 'eprendas', 0, 1),
(18, 'FAJAS', '', '2015-02-07 17:49:42', NULL, 'eprendas', 1, 1),
(18, 'FAJAS', '', '2015-02-07 17:49:42', NULL, 'eprendas', 2, 1),
(18, 'FAJAS', '', '2015-02-07 17:49:42', NULL, 'eprendas', 3, 1),
(18, 'FAJAS', '', '2015-02-07 11:49:42', NULL, 'eprendas', 4, 1),
(18, 'FAJAS', '', '2015-02-07 17:49:42', NULL, 'eprendas', 6, 1),
(18, 'FAJAS', '', '2015-02-07 17:49:42', NULL, 'eprendas', 7, 1),
(19, 'CAJAS VARIAS', '', '2015-02-07 11:49:50', NULL, 'eprendas', 0, 1),
(19, 'CAJAS VARIAS', '', '2015-02-07 17:49:50', NULL, 'eprendas', 1, 1),
(19, 'CAJAS VARIAS', '', '2015-02-07 17:49:50', NULL, 'eprendas', 2, 1),
(19, 'CAJAS VARIAS', '', '2015-02-07 17:49:50', NULL, 'eprendas', 3, 1),
(19, 'CAJAS VARIAS', '', '2015-02-07 11:49:50', NULL, 'eprendas', 4, 1),
(19, 'CAJAS VARIAS', '', '2015-02-07 17:49:50', NULL, 'eprendas', 6, 1),
(19, 'CAJAS VARIAS', '', '2015-02-07 17:49:50', NULL, 'eprendas', 7, 1),
(20, 'CAJAS 10%', '', '2015-02-07 11:50:00', NULL, 'eprendas', 0, 1),
(20, 'CAJAS 10%', '', '2015-02-07 17:50:00', NULL, 'eprendas', 1, 1),
(20, 'CAJAS 10%', '', '2015-02-07 17:50:00', NULL, 'eprendas', 2, 1),
(20, 'CAJAS 10%', '', '2015-02-07 17:50:00', NULL, 'eprendas', 3, 1),
(20, 'CAJAS 10%', '', '2015-02-07 11:50:00', NULL, 'eprendas', 4, 1),
(20, 'CAJAS 10%', '', '2015-02-07 17:50:00', NULL, 'eprendas', 6, 1),
(20, 'CAJAS 10%', '', '2015-02-07 17:50:00', NULL, 'eprendas', 7, 1),
(21, 'OFERTAS', '', '2015-02-07 11:50:10', NULL, 'eprendas', 0, 1),
(21, 'OFERTAS', '', '2015-02-07 17:50:10', NULL, 'eprendas', 1, 1),
(21, 'OFERTAS', '', '2015-02-07 17:50:10', NULL, 'eprendas', 2, 1),
(21, 'OFERTAS', '', '2015-02-07 17:50:10', NULL, 'eprendas', 3, 1),
(21, 'OFERTAS', '', '2015-02-07 11:50:10', NULL, 'eprendas', 4, 1),
(21, 'OFERTAS', '', '2015-02-07 17:50:10', NULL, 'eprendas', 6, 1),
(21, 'OFERTAS', '', '2015-02-07 17:50:10', NULL, 'eprendas', 7, 1),
(22, 'PROMOCIONES 30/40', '', '2015-02-07 11:50:23', NULL, 'eprendas', 0, 1),
(22, 'PROMOCIONES 30/40', '', '2015-02-07 17:50:23', NULL, 'eprendas', 1, 1),
(22, 'PROMOCIONES 30/40', '', '2015-02-07 17:50:23', NULL, 'eprendas', 2, 1),
(22, 'PROMOCIONES 30/40', '', '2015-02-07 17:50:23', NULL, 'eprendas', 3, 1),
(22, 'PROMOCIONES 30/40', '', '2015-02-07 11:50:23', NULL, 'eprendas', 4, 1),
(22, 'PROMOCIONES 30/40', '', '2015-02-07 17:50:23', NULL, 'eprendas', 6, 1),
(22, 'PROMOCIONES 30/40', '', '2015-02-07 17:50:23', NULL, 'eprendas', 7, 1),
(23, 'BELA', '', '2015-02-07 11:50:36', NULL, 'eprendas', 0, 1),
(23, 'BELA', '', '2015-02-07 17:50:36', NULL, 'eprendas', 1, 1),
(23, 'BELA', '', '2015-02-07 17:50:36', NULL, 'eprendas', 2, 1),
(23, 'BELA', '', '2015-02-07 17:50:36', NULL, 'eprendas', 3, 1),
(23, 'BELA', '', '2015-02-07 11:50:36', NULL, 'eprendas', 4, 1),
(23, 'BELA', '', '2015-02-07 17:50:36', NULL, 'eprendas', 6, 1),
(23, 'BELA', '', '2015-02-07 17:50:36', NULL, 'eprendas', 7, 1),
(24, 'ORO LAMINADO', '', '2015-02-07 11:50:48', NULL, 'eprendas', 0, 1),
(24, 'ORO LAMINADO', '', '2015-02-07 17:50:48', NULL, 'eprendas', 1, 1),
(24, 'ORO LAMINADO', '', '2015-02-07 17:50:48', NULL, 'eprendas', 2, 1),
(24, 'ORO LAMINADO', '', '2015-02-07 17:50:48', NULL, 'eprendas', 3, 1),
(24, 'ORO LAMINADO', '', '2015-02-07 11:50:48', NULL, 'eprendas', 4, 1),
(24, 'ORO LAMINADO', '', '2015-02-07 17:50:48', NULL, 'eprendas', 6, 1),
(24, 'ORO LAMINADO', '', '2015-02-07 17:50:48', NULL, 'eprendas', 7, 1),
(25, 'PLATA LAMINADA', '', '2015-02-07 11:50:58', NULL, 'eprendas', 0, 1),
(25, 'PLATA LAMINADA', '', '2015-02-07 17:50:58', NULL, 'eprendas', 1, 1),
(25, 'PLATA LAMINADA', '', '2015-02-07 17:50:58', NULL, 'eprendas', 2, 1),
(25, 'PLATA LAMINADA', '', '2015-02-07 17:50:58', NULL, 'eprendas', 3, 1),
(25, 'PLATA LAMINADA', '', '2015-02-07 11:50:58', NULL, 'eprendas', 4, 1),
(25, 'PLATA LAMINADA', '', '2015-02-07 17:50:58', NULL, 'eprendas', 6, 1),
(25, 'PLATA LAMINADA', '', '2015-02-07 17:50:58', NULL, 'eprendas', 7, 1),
(26, 'ACERO', '', '2015-02-07 11:51:15', NULL, 'eprendas', 0, 1),
(26, 'ACERO', '', '2015-02-07 17:51:15', NULL, 'eprendas', 1, 1),
(26, 'ACERO', '', '2015-02-07 17:51:15', NULL, 'eprendas', 2, 1),
(26, 'ACERO', '', '2015-02-07 17:51:15', NULL, 'eprendas', 3, 1),
(26, 'ACERO', '', '2015-02-07 11:51:15', NULL, 'eprendas', 4, 1),
(26, 'ACERO', '', '2015-02-07 17:51:15', NULL, 'eprendas', 6, 1),
(26, 'ACERO', '', '2015-02-07 17:51:15', NULL, 'eprendas', 7, 1),
(27, 'PLATA RODINADA', '', '2015-02-07 11:51:24', NULL, 'eprendas', 0, 1),
(27, 'PLATA RODINADA', '', '2015-02-07 17:51:24', NULL, 'eprendas', 1, 1),
(27, 'PLATA RODINADA', '', '2015-02-07 17:51:24', NULL, 'eprendas', 2, 1),
(27, 'PLATA RODINADA', '', '2015-02-07 17:51:24', NULL, 'eprendas', 3, 1),
(27, 'PLATA RODINADA', '', '2015-02-07 11:51:24', NULL, 'eprendas', 4, 1),
(27, 'PLATA RODINADA', '', '2015-02-07 17:51:24', NULL, 'eprendas', 6, 1),
(27, 'PLATA RODINADA', '', '2015-02-07 17:51:24', NULL, 'eprendas', 7, 1),
(28, 'PLATA 9.25', '', '2015-02-07 11:51:35', NULL, 'eprendas', 0, 1),
(28, 'PLATA 9.25', '', '2015-02-07 17:51:35', NULL, 'eprendas', 1, 1),
(28, 'PLATA 9.25', '', '2015-02-07 17:51:35', NULL, 'eprendas', 2, 1),
(28, 'PLATA 9.25', '', '2015-02-07 17:51:35', NULL, 'eprendas', 3, 1),
(28, 'PLATA 9.25', '', '2015-02-07 11:51:35', NULL, 'eprendas', 4, 1),
(28, 'PLATA 9.25', '', '2015-02-07 17:51:35', NULL, 'eprendas', 6, 1),
(28, 'PLATA 9.25', '', '2015-02-07 17:51:35', NULL, 'eprendas', 7, 1),
(29, 'INFANTIL', '', '2015-02-07 11:51:49', NULL, 'eprendas', 0, 1),
(29, 'INFANTIL', '', '2015-02-07 17:51:49', NULL, 'eprendas', 1, 1),
(29, 'INFANTIL', '', '2015-02-07 17:51:49', NULL, 'eprendas', 2, 1),
(29, 'INFANTIL', '', '2015-02-07 17:51:49', NULL, 'eprendas', 3, 1),
(29, 'INFANTIL', '', '2015-02-07 11:51:49', NULL, 'eprendas', 4, 1),
(29, 'INFANTIL', '', '2015-02-07 17:51:49', NULL, 'eprendas', 6, 1),
(29, 'INFANTIL', '', '2015-02-07 17:51:49', NULL, 'eprendas', 7, 1),
(30, 'DORIS', '', '2015-02-07 11:51:57', NULL, 'eprendas', 0, 1),
(30, 'DORIS', '', '2015-02-07 17:51:57', NULL, 'eprendas', 1, 1),
(30, 'DORIS', '', '2015-02-07 17:51:57', NULL, 'eprendas', 2, 1),
(30, 'DORIS', '', '2015-02-07 17:51:57', NULL, 'eprendas', 3, 1),
(30, 'DORIS', '', '2015-02-07 11:51:57', NULL, 'eprendas', 4, 1),
(30, 'DORIS', '', '2015-02-07 17:51:57', NULL, 'eprendas', 6, 1),
(30, 'DORIS', '', '2015-02-07 17:51:57', NULL, 'eprendas', 7, 1),
(31, 'CATALOGOS', '', '2015-02-07 11:52:06', NULL, 'eprendas', 0, 1),
(31, 'CATALOGOS', '', '2015-02-07 17:52:06', NULL, 'eprendas', 1, 1),
(31, 'CATALOGOS', '', '2015-02-07 17:52:06', NULL, 'eprendas', 2, 1),
(31, 'CATALOGOS', '', '2015-02-07 17:52:06', NULL, 'eprendas', 3, 1),
(31, 'CATALOGOS', '', '2015-02-07 11:52:06', NULL, 'eprendas', 4, 1),
(31, 'CATALOGOS', '', '2015-02-07 17:52:06', NULL, 'eprendas', 6, 1),
(31, 'CATALOGOS', '', '2015-02-07 17:52:06', NULL, 'eprendas', 7, 1),
(32, 'BISUTERIA', '', '2015-02-07 11:52:16', NULL, 'eprendas', 0, 1),
(32, 'BISUTERIA', '', '2015-02-07 17:52:16', NULL, 'eprendas', 1, 1),
(32, 'BISUTERIA', '', '2015-02-07 17:52:16', NULL, 'eprendas', 2, 1),
(32, 'BISUTERIA', '', '2015-02-07 17:52:16', NULL, 'eprendas', 3, 1),
(32, 'BISUTERIA', '', '2015-02-07 11:52:16', NULL, 'eprendas', 4, 1),
(32, 'BISUTERIA', '', '2015-02-07 17:52:16', NULL, 'eprendas', 6, 1),
(32, 'BISUTERIA', '', '2015-02-07 17:52:16', NULL, 'eprendas', 7, 1),
(33, 'PALADIO', '', '2015-02-07 11:52:26', NULL, 'eprendas', 0, 1),
(33, 'PALADIO', '', '2015-02-07 17:52:26', NULL, 'eprendas', 1, 1),
(33, 'PALADIO', '', '2015-02-07 17:52:26', NULL, 'eprendas', 2, 1),
(33, 'PALADIO', '', '2015-02-07 17:52:26', NULL, 'eprendas', 3, 1),
(33, 'PALADIO', '', '2015-02-07 11:52:26', NULL, 'eprendas', 4, 1),
(33, 'PALADIO', '', '2015-02-07 17:52:26', NULL, 'eprendas', 6, 1),
(33, 'PALADIO', '', '2015-02-07 17:52:26', NULL, 'eprendas', 7, 1),
(34, 'ORO 10K Y PLATA 9.25', '', '2015-04-19 09:14:19', NULL, 'eprendas', 0, 1),
(34, 'ORO 10K Y PLATA 9.25', '', '2015-04-19 09:14:19', NULL, 'eprendas', 1, 1),
(34, 'ORO 10K Y PLATA 9.25', '', '2015-04-19 09:14:19', NULL, 'eprendas', 2, 1),
(34, 'ORO 10K Y PLATA 9.25', '', '2015-04-19 09:14:19', NULL, 'eprendas', 3, 1),
(34, 'ORO 10K Y PLATA 9.25', '', '2015-04-19 09:14:19', NULL, 'eprendas', 4, 1),
(34, 'ORO 10K Y PLATA 9.25', '', '2015-04-19 15:14:19', NULL, 'eprendas', 6, 1),
(34, 'ORO 10K Y PLATA 9.25', '', '2015-04-19 15:14:19', NULL, 'eprendas', 7, 1),
(35, 'SHOWROOM', '', '2015-06-03 03:32:53', NULL, 'eprendas', 0, 1),
(35, 'SHOWROOM', '', '2015-06-03 03:32:33', NULL, 'eprendas', 1, 1),
(35, 'SHOWROOM', '', '2015-06-03 03:33:16', NULL, 'eprendas', 2, 1),
(35, 'SHOWROOM', '', '2015-06-03 03:33:33', NULL, 'eprendas', 3, 1),
(35, 'SHOWROOM', '', '2015-06-03 03:33:50', NULL, 'eprendas', 4, 1),
(35, 'SHOWROOM', '', '2015-06-03 09:33:50', NULL, 'eprendas', 6, 1),
(35, 'SHOWROOM', '', '2015-06-03 09:33:50', NULL, 'eprendas', 7, 1),
(36, 'GRAFFITI', '', '2015-02-07 17:42:47', NULL, 'eprendas', 0, 1),
(36, 'GRAFFITI', '', '2015-02-07 17:42:47', NULL, 'eprendas', 1, 1),
(36, 'GRAFFITI', '', '2015-02-07 17:42:47', NULL, 'eprendas', 2, 1),
(36, 'GRAFFITI', '', '2015-02-07 17:42:47', NULL, 'eprendas', 3, 1),
(36, 'GRAFFITI', '', '2015-02-07 17:42:47', NULL, 'eprendas', 4, 1),
(36, 'GRAFFITI', '', '2015-02-07 17:42:47', NULL, 'eprendas', 5, 1),
(36, 'GRAFFITI', '', '2015-02-07 17:42:47', NULL, 'eprendas', 6, 1),
(36, 'GRAFFITI', '', '2015-02-07 17:42:47', NULL, 'eprendas', 7, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_06_articulo`
--

CREATE TABLE IF NOT EXISTS `tb_06_articulo` (
  `Articulo_Codigo` varchar(30) NOT NULL,
  `Articulo_Descripcion` varchar(150) DEFAULT NULL,
  `Articulo_Codigo_Barras` varchar(45) DEFAULT NULL,
  `Articulo_Cantidad_Inventario` int(11) DEFAULT NULL,
  `Articulo_Cantidad_Defectuoso` int(11) DEFAULT NULL,
  `Articulo_Descuento` int(11) DEFAULT NULL,
  `Articulo_Imagen_URL` varchar(45) DEFAULT NULL,
  `Articulo_Exento` tinyint(1) DEFAULT NULL,
  `Articulo_No_Retencion` tinyint(1) NOT NULL,
  `TB_05_Familia_Familia_Codigo` int(11) NOT NULL,
  `TB_02_Sucursal_Codigo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_06_articulo`
--

INSERT INTO `tb_06_articulo` (`Articulo_Codigo`, `Articulo_Descripcion`, `Articulo_Codigo_Barras`, `Articulo_Cantidad_Inventario`, `Articulo_Cantidad_Defectuoso`, `Articulo_Descuento`, `Articulo_Imagen_URL`, `Articulo_Exento`, `Articulo_No_Retencion`, `TB_05_Familia_Familia_Codigo`, `TB_02_Sucursal_Codigo`) VALUES
('1', 'PRUEBA', '1', 9, 0, 0, 'Default.png', 0, 1, 1, 0),
('1', 'PRUEBA', '1', 5, 0, 0, 'Default.png', 0, 0, 1, 2),
('10', 'ANILLO DRUSA DORADO', '10', 10, 0, 0, '9', 0, 1, 0, 0),
('10', 'ANILLO DRUSA DORADO', '10', 10, 0, 0, '9', 0, 1, 0, 1),
('10', 'ANILLO DRUSA DORADO', '10', 0, 0, 0, '9', 0, 1, 0, 2),
('10', 'ANILLO DRUSA DORADO', '10', 0, 0, 0, '9', 0, 1, 0, 3),
('11', 'ARO TRIPLE TRES OROS', '11', 9, 0, 0, '10', 0, 1, 0, 0),
('11', 'ARO TRIPLE TRES OROS', '11', 10, 0, 0, '10', 0, 1, 0, 1),
('11', 'ARO TRIPLE TRES OROS', '11', 0, 0, 0, '10', 0, 1, 0, 2),
('11', 'ARO TRIPLE TRES OROS', '11', 0, 0, 0, '10', 0, 1, 0, 3),
('2', 'ANILLO COMPROMISO O.L. ', '2', 24, 0, 0, '1', 0, 0, 0, 0),
('3', 'CADENA GRUESA ESLABONES', '3', 25, 0, 0, '2', 0, 0, 0, 0),
('4', 'RELOJ DAMA', '4', 23, 0, 0, '3.jpg', 0, 1, 0, 0),
('5', 'CADENA + PULSERA CABALLERO', '5', 20, 0, 0, '4.jpg', 0, 1, 0, 0),
('6', 'ANILLO DRUSA DORADO', '6', 10, 0, 0, '5', 0, 1, 0, 0),
('6', 'ANILLO DRUSA DORADO', '6', 10, 0, 0, '5', 0, 1, 0, 1),
('6', 'ANILLO DRUSA DORADO', '6', 0, 0, 0, '5', 0, 1, 0, 2),
('6', 'ANILLO DRUSA DORADO', '6', 0, 0, 0, '5', 0, 1, 0, 3),
('7', 'ARO TRIPLE TRES OROS', '7', 10, 0, 0, '6', 0, 1, 0, 0),
('7', 'ARO TRIPLE TRES OROS', '7', 10, 0, 0, '6', 0, 1, 0, 1),
('7', 'ARO TRIPLE TRES OROS', '7', 0, 0, 0, '6', 0, 1, 0, 2),
('7', 'ARO TRIPLE TRES OROS', '7', 0, 0, 0, '6', 0, 1, 0, 3),
('8', 'ANILLO DRUSA DORADO', '8', 10, 0, 0, '7', 0, 1, 0, 0),
('8', 'ANILLO DRUSA DORADO', '8', 10, 0, 0, '7', 0, 1, 0, 1),
('8', 'ANILLO DRUSA DORADO', '8', 0, 0, 0, '7', 0, 1, 0, 2),
('8', 'ANILLO DRUSA DORADO', '8', 0, 0, 0, '7', 0, 1, 0, 3),
('9', 'ARO TRIPLE TRES OROS', '9', 10, 0, 0, '8', 0, 1, 0, 0),
('9', 'ARO TRIPLE TRES OROS', '9', 10, 0, 0, '8', 0, 1, 0, 1),
('9', 'ARO TRIPLE TRES OROS', '9', 0, 0, 0, '8', 0, 1, 0, 2),
('9', 'ARO TRIPLE TRES OROS', '9', 0, 0, 0, '8', 0, 1, 0, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_07_factura`
--

CREATE TABLE IF NOT EXISTS `tb_07_factura` (
  `Factura_Consecutivo` int(11) NOT NULL,
  `Factura_Monto_Total` double DEFAULT NULL,
  `Factura_Monto_Sin_IVA` double DEFAULT NULL,
  `Factura_Monto_IVA` double DEFAULT NULL,
  `Factura_Observaciones` varchar(150) DEFAULT NULL,
  `Factura_Tipo_Pago` varchar(10) DEFAULT NULL,
  `Factura_Fecha_Hora` timestamp NULL DEFAULT NULL,
  `Factura_Estado` varchar(10) DEFAULT NULL,
  `Factura_Cliente_Exento` tinyint(1) NOT NULL,
  `Factura_Cliente_No_Retencion` int(11) NOT NULL,
  `Factura_Cliente_Sucursal` int(11) NOT NULL,
  `Factura_Moneda` varchar(15) DEFAULT NULL,
  `Factura_porcentaje_iva` double DEFAULT NULL,
  `Factura_Retencion` double NOT NULL,
  `Factura_tipo_cambio` double DEFAULT NULL,
  `Factura_Nombre_Cliente` varchar(150) DEFAULT NULL,
  `TB_02_Sucursal_Codigo` int(11) NOT NULL,
  `Factura_Vendedor_Codigo` int(11) NOT NULL,
  `Factura_Vendedor_Sucursal` int(11) NOT NULL,
  `TB_03_Cliente_Cliente_Cedula` varchar(50)  NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_07_factura`
--

INSERT INTO `tb_07_factura` (`Factura_Consecutivo`, `Factura_Monto_Total`, `Factura_Monto_Sin_IVA`, `Factura_Monto_IVA`, `Factura_Observaciones`, `Factura_Tipo_Pago`, `Factura_Fecha_Hora`, `Factura_Estado`, `Factura_Cliente_Exento`, `Factura_Cliente_No_Retencion`, `Factura_Cliente_Sucursal`, `Factura_Moneda`, `Factura_porcentaje_iva`, `Factura_Retencion`, `Factura_tipo_cambio`, `Factura_Nombre_Cliente`, `TB_02_Sucursal_Codigo`, `Factura_Vendedor_Codigo`, `Factura_Vendedor_Sucursal`, `TB_03_Cliente_Cliente_Cedula`) VALUES
(1, 2000, 1769.9115044248, 230.08849557522, '', 'contado', '2015-10-19 21:27:23', 'anulada', 0, 1, 1, 'colones', 13, 0, 542.45, 'TIENDA HEREDIA (WILFREDY PIEDRA POCHET)', 0, 14, 0, 105300225),
(2, 10000, 8849.5575221239, 1150.4424778761, '', 'contado', '2015-10-19 21:27:08', 'anulada', 0, 1, 1, 'colones', 13, 0, 542.45, 'TIENDA GAROTAS BY GAROTAS', 0, 3, 0, 117001386032),
(3, 4000, 3539.8230088496, 460.17699115044, '', 'contado', '2015-10-19 21:26:47', 'anulada', 0, 0, 0, 'colones', 13, 0, 540.46, 'Cliente Contado Corriente', 0, 3, 0, 1),
(4, 25410, 22486.725663717, 2923.2743362832, '', 'credito', '2015-10-19 22:16:06', 'cobrada', 0, 1, 1, 'colones', 13, 0, 540.46, 'TIENDA GAROTAS BY GAROTAS', 0, 3, 0, 117001386032);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_08_articulos_factura`
--

CREATE TABLE IF NOT EXISTS `tb_08_articulos_factura` (
`Articulo_Factura_id` int(11) NOT NULL,
  `Articulo_Factura_Codigo` varchar(30) DEFAULT NULL,
  `Articulo_Factura_Descripcion` varchar(45) DEFAULT NULL,
  `Articulo_Factura_Cantidad` int(11) DEFAULT NULL,
  `Articulo_Factura_Descuento` varchar(45) DEFAULT NULL,
  `Articulo_Factura_Exento` varchar(45) DEFAULT NULL,
  `Articulo_Factura_No_Retencion` tinyint(1) NOT NULL,
  `Articulo_Factura_Precio_Unitario` varchar(45) DEFAULT NULL,
  `Articulo_Factura_Precio_Final` double NOT NULL,
  `Articulo_Factura_Imagen` varchar(45) DEFAULT NULL,
  `TB_07_Factura_Factura_Consecutivo` int(11) NOT NULL,
  `TB_07_Factura_TB_02_Sucursal_Codigo` int(11) NOT NULL,
  `TB_07_Factura_Factura_Vendedor_Codigo` int(11) NOT NULL,
  `TB_07_Factura_Factura_Vendedor_Sucursal` int(11) NOT NULL,
  `TB_07_Factura_TB_03_Cliente_Cliente_Cedula` varchar(50)  NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_08_articulos_factura`
--

INSERT INTO `tb_08_articulos_factura` (`Articulo_Factura_id`, `Articulo_Factura_Codigo`, `Articulo_Factura_Descripcion`, `Articulo_Factura_Cantidad`, `Articulo_Factura_Descuento`, `Articulo_Factura_Exento`, `Articulo_Factura_No_Retencion`, `Articulo_Factura_Precio_Unitario`, `Articulo_Factura_Precio_Final`, `Articulo_Factura_Imagen`, `TB_07_Factura_Factura_Consecutivo`, `TB_07_Factura_TB_02_Sucursal_Codigo`, `TB_07_Factura_Factura_Vendedor_Codigo`, `TB_07_Factura_Factura_Vendedor_Sucursal`, `TB_07_Factura_TB_03_Cliente_Cliente_Cedula`) VALUES
(1, '1', 'PRUEBA', 1, '0', '0', 1, '2000', 4000, 'Default.png', 1, 0, 14, 0, 105300225),
(2, '1', 'PRUEBA', 5, '0', '0', 1, '2000', 4000, 'Default.png', 2, 0, 3, 0, 117001386032),
(3, '1', 'PRUEBA', 1, '0', '0', 1, '4000', 4000, 'Default.png', 3, 0, 3, 0, 1),
(4, '2', 'ANILLO COMPROMISO O.L. ', 1, '35', '0', 0, '3800', 4200, '1', 4, 0, 3, 0, 117001386032),
(5, '4', 'RELOJ DAMA', 2, '10', '0', 1, '3800', 4200, '3.jpg', 4, 0, 3, 0, 117001386032),
(6, '5', 'CADENA + PULSERA CABALLERO', 5, '25', '0', 1, '3500', 4000, '4.jpg', 4, 0, 3, 0, 117001386032),
(7, '11', 'ARO TRIPLE TRES OROS', 1, '15', '0', 1, '3500', 4000, '10', 4, 0, 3, 0, 117001386032);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_09_compras`
--

CREATE TABLE IF NOT EXISTS `tb_09_compras` (
`Id` int(11) NOT NULL,
  `Codigo` varchar(30) DEFAULT NULL,
  `Descripcion` varchar(150) DEFAULT NULL,
  `Costo` double DEFAULT NULL,
  `Cantidad` int(11) DEFAULT NULL,
  `Fecha_Ingreso` timestamp NULL DEFAULT NULL,
  `Usuario` int(11) NOT NULL,
  `Sucursal` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_09_compras`
--

INSERT INTO `tb_09_compras` (`Id`, `Codigo`, `Descripcion`, `Costo`, `Cantidad`, `Fecha_Ingreso`, `Usuario`, `Sucursal`) VALUES
(1, '1', 'PRUEBA', 2000, 5, '2015-09-25 21:05:24', 3, 2),
(2, '11/AN03JUN2015', 'ANILLO COMPROMISO O.L. ', 2000.35, 100, '2015-10-19 17:59:06', 10, 0),
(3, '26/BRMAY2015', 'CADENA GRUESA ESLABONES', 2686.36, 100, '2015-10-19 17:59:06', 10, 0),
(4, '18-2491', 'RELOJ DAMA', 1435.36, 100, '2015-10-19 17:59:06', 10, 0),
(5, '27-CHIMAY2015', 'CADENA + PULSERA CABALLERO', 1325.3, 100, '2015-10-19 17:59:06', 10, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_10_proforma`
--

CREATE TABLE IF NOT EXISTS `tb_10_proforma` (
  `Proforma_Consecutivo` int(11) NOT NULL,
  `Proforma_Monto_Total` double DEFAULT NULL,
  `Proforma_Monto_Sin_IVA` double DEFAULT NULL,
  `Proforma_Monto_IVA` double DEFAULT NULL,
  `Proforma_Observaciones` varchar(150) DEFAULT NULL,
  `Proforma_Fecha_Hora` timestamp NULL DEFAULT NULL,
  `Proforma_Estado` varchar(10) DEFAULT NULL,
  `Proforma_Cliente_Sucursal` tinyint(1) NOT NULL,
  `Proforma_Cliente_Exento` tinyint(1) NOT NULL,
  `Proforma_Cliente_No_Retencion` tinyint(1) NOT NULL,
  `Proforma_Moneda` varchar(15) DEFAULT NULL,
  `Proforma_Porcentaje_IVA` double DEFAULT NULL,
  `Proforma_Retencion` double NOT NULL,
  `Proforma_Tipo_Cambio` double DEFAULT NULL,
  `Proforma_Nombre_Cliente` varchar(150) DEFAULT NULL,
  `TB_02_Sucursal_Codigo` int(11) NOT NULL,
  `Proforma_Vendedor_Codigo` int(11) NOT NULL,
  `Proforma_Vendedor_Sucursal` int(11) NOT NULL,
  `TB_03_Cliente_Cliente_Cedula` varchar(50)  NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_11_precios`
--

CREATE TABLE IF NOT EXISTS `tb_11_precios` (
`Precio_Id` int(11) NOT NULL,
  `Precio_Numero` int(11) NOT NULL DEFAULT '0',
  `Precio_Monto` double NOT NULL DEFAULT '0',
  `TB_06_Articulo_Articulo_Codigo` varchar(30) NOT NULL,
  `TB_06_Articulo_TB_05_Familia_Familia_Codigo` int(11) NOT NULL,
  `TB_06_Articulo_TB_02_Sucursal_Codigo` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_11_precios`
--

INSERT INTO `tb_11_precios` (`Precio_Id`, `Precio_Numero`, `Precio_Monto`, `TB_06_Articulo_Articulo_Codigo`, `TB_06_Articulo_TB_05_Familia_Familia_Codigo`, `TB_06_Articulo_TB_02_Sucursal_Codigo`) VALUES
(1, 0, 1000, '1', 1, 0),
(2, 1, 4000, '1', 1, 0),
(3, 2, 2000, '1', 1, 0),
(4, 3, 0, '1', 1, 0),
(5, 4, 0, '1', 1, 0),
(6, 5, 0, '1', 1, 0),
(7, 0, 2000, '1', 1, 2),
(8, 1, 2000, '1', 1, 2),
(9, 2, 2000, '1', 1, 2),
(10, 3, 2000, '1', 1, 2),
(11, 4, 2000, '1', 1, 2),
(12, 5, 2000, '1', 1, 2),
(13, 0, 2111.11, '2', 0, 0),
(14, 1, 4200, '2', 0, 0),
(15, 2, 3800, '2', 0, 0),
(16, 3, 2900, '2', 0, 0),
(17, 4, 0, '2', 0, 0),
(18, 5, 0, '2', 0, 0),
(19, 0, 2722.22, '3', 0, 0),
(20, 1, 4000, '3', 0, 0),
(21, 2, 3500, '3', 0, 0),
(22, 3, 2800, '3', 0, 0),
(23, 4, 0, '3', 0, 0),
(24, 5, 0, '3', 0, 0),
(25, 0, 2111.11, '4', 0, 0),
(26, 1, 4200, '4', 0, 0),
(27, 2, 3800, '4', 0, 0),
(28, 3, 3000, '4', 0, 0),
(29, 4, 0, '4', 0, 0),
(30, 5, 0, '4', 0, 0),
(31, 0, 1600.12, '5', 0, 0),
(32, 1, 4000, '5', 0, 0),
(33, 2, 3500, '5', 0, 0),
(34, 3, 2500, '5', 0, 0),
(35, 4, 0, '5', 0, 0),
(36, 5, 0, '5', 0, 0),
(37, 0, 2111.11, '6', 0, 0),
(38, 1, 4200, '6', 0, 0),
(39, 2, 3800, '6', 0, 0),
(40, 3, 2900, '6', 0, 0),
(41, 4, 0, '6', 0, 0),
(42, 5, 0, '6', 0, 0),
(43, 0, 2722.22, '7', 0, 0),
(44, 1, 4000, '7', 0, 0),
(45, 2, 3500, '7', 0, 0),
(46, 3, 2700, '7', 0, 0),
(47, 4, 0, '7', 0, 0),
(48, 5, 0, '7', 0, 0),
(49, 0, 2111.11, '8', 0, 0),
(50, 1, 4200, '8', 0, 0),
(51, 2, 3800, '8', 0, 0),
(52, 3, 2800, '8', 0, 0),
(53, 4, 0, '8', 0, 0),
(54, 5, 0, '8', 0, 0),
(55, 0, 2722.22, '9', 0, 0),
(56, 1, 4000, '9', 0, 0),
(57, 2, 3500, '9', 0, 0),
(58, 3, 2700, '9', 0, 0),
(59, 4, 0, '9', 0, 0),
(60, 5, 0, '9', 0, 0),
(61, 0, 2111.11, '10', 0, 0),
(62, 1, 4200, '10', 0, 0),
(63, 2, 3800, '10', 0, 0),
(64, 3, 2900, '10', 0, 0),
(65, 4, 0, '10', 0, 0),
(66, 5, 0, '10', 0, 0),
(67, 0, 2722.22, '11', 0, 0),
(68, 1, 4000, '11', 0, 0),
(69, 2, 3500, '11', 0, 0),
(70, 3, 2700, '11', 0, 0),
(71, 4, 0, '11', 0, 0),
(72, 5, 0, '11', 0, 0),
(73, 0, 2111.11, '6', 0, 1),
(74, 1, 4200, '6', 0, 1),
(75, 2, 3800, '6', 0, 1),
(76, 3, 2900, '6', 0, 1),
(77, 4, 0, '6', 0, 1),
(78, 5, 0, '6', 0, 1),
(79, 0, 2722.22, '7', 0, 1),
(80, 1, 4000, '7', 0, 1),
(81, 2, 3500, '7', 0, 1),
(82, 3, 2700, '7', 0, 1),
(83, 4, 0, '7', 0, 1),
(84, 5, 0, '7', 0, 1),
(85, 0, 2111.11, '8', 0, 1),
(86, 1, 4200, '8', 0, 1),
(87, 2, 3800, '8', 0, 1),
(88, 3, 2800, '8', 0, 1),
(89, 4, 0, '8', 0, 1),
(90, 5, 0, '8', 0, 1),
(91, 0, 2722.22, '9', 0, 1),
(92, 1, 4000, '9', 0, 1),
(93, 2, 3500, '9', 0, 1),
(94, 3, 2700, '9', 0, 1),
(95, 4, 0, '9', 0, 1),
(96, 5, 0, '9', 0, 1),
(97, 0, 2111.11, '10', 0, 1),
(98, 1, 4200, '10', 0, 1),
(99, 2, 3800, '10', 0, 1),
(100, 3, 2900, '10', 0, 1),
(101, 4, 0, '10', 0, 1),
(102, 5, 0, '10', 0, 1),
(103, 0, 2722.22, '11', 0, 1),
(104, 1, 4000, '11', 0, 1),
(105, 2, 3500, '11', 0, 1),
(106, 3, 2700, '11', 0, 1),
(107, 4, 0, '11', 0, 1),
(108, 5, 0, '11', 0, 1),
(109, 0, 2111.11, '6', 0, 3),
(110, 1, 4200, '6', 0, 3),
(111, 2, 3800, '6', 0, 3),
(112, 3, 2900, '6', 0, 3),
(113, 4, 0, '6', 0, 3),
(114, 5, 0, '6', 0, 3),
(115, 0, 2722.22, '7', 0, 3),
(116, 1, 4000, '7', 0, 3),
(117, 2, 3500, '7', 0, 3),
(118, 3, 2700, '7', 0, 3),
(119, 4, 0, '7', 0, 3),
(120, 5, 0, '7', 0, 3),
(121, 0, 2111.11, '8', 0, 3),
(122, 1, 4200, '8', 0, 3),
(123, 2, 3800, '8', 0, 3),
(124, 3, 2800, '8', 0, 3),
(125, 4, 0, '8', 0, 3),
(126, 5, 0, '8', 0, 3),
(127, 0, 2722.22, '9', 0, 3),
(128, 1, 4000, '9', 0, 3),
(129, 2, 3500, '9', 0, 3),
(130, 3, 2700, '9', 0, 3),
(131, 4, 0, '9', 0, 3),
(132, 5, 0, '9', 0, 3),
(133, 0, 2111.11, '10', 0, 3),
(134, 1, 4200, '10', 0, 3),
(135, 2, 3800, '10', 0, 3),
(136, 3, 2900, '10', 0, 3),
(137, 4, 0, '10', 0, 3),
(138, 5, 0, '10', 0, 3),
(139, 0, 2722.22, '11', 0, 3),
(140, 1, 4000, '11', 0, 3),
(141, 2, 3500, '11', 0, 3),
(142, 3, 2700, '11', 0, 3),
(143, 4, 0, '11', 0, 3),
(144, 5, 0, '11', 0, 3),
(145, 0, 2111.11, '6', 0, 2),
(146, 1, 4200, '6', 0, 2),
(147, 2, 3800, '6', 0, 2),
(148, 3, 2900, '6', 0, 2),
(149, 4, 0, '6', 0, 2),
(150, 5, 0, '6', 0, 2),
(151, 0, 2722.22, '7', 0, 2),
(152, 1, 4000, '7', 0, 2),
(153, 2, 3500, '7', 0, 2),
(154, 3, 2700, '7', 0, 2),
(155, 4, 0, '7', 0, 2),
(156, 5, 0, '7', 0, 2),
(157, 0, 2111.11, '8', 0, 2),
(158, 1, 4200, '8', 0, 2),
(159, 2, 3800, '8', 0, 2),
(160, 3, 2800, '8', 0, 2),
(161, 4, 0, '8', 0, 2),
(162, 5, 0, '8', 0, 2),
(163, 0, 2722.22, '9', 0, 2),
(164, 1, 4000, '9', 0, 2),
(165, 2, 3500, '9', 0, 2),
(166, 3, 2700, '9', 0, 2),
(167, 4, 0, '9', 0, 2),
(168, 5, 0, '9', 0, 2),
(169, 0, 2111.11, '10', 0, 2),
(170, 1, 4200, '10', 0, 2),
(171, 2, 3800, '10', 0, 2),
(172, 3, 2900, '10', 0, 2),
(173, 4, 0, '10', 0, 2),
(174, 5, 0, '10', 0, 2),
(175, 0, 2722.22, '11', 0, 2),
(176, 1, 4000, '11', 0, 2),
(177, 2, 3500, '11', 0, 2),
(178, 3, 2700, '11', 0, 2),
(179, 4, 0, '11', 0, 2),
(180, 5, 0, '11', 0, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_12_transacciones`
--

CREATE TABLE IF NOT EXISTS `tb_12_transacciones` (
`Trans_Codigo` int(11) NOT NULL,
  `Trans_Descripcion` varchar(150) DEFAULT NULL,
  `Trans_Fecha_Hora` timestamp NULL DEFAULT NULL,
  `Trans_Tipo` varchar(15) DEFAULT NULL,
  `Trans_IP` varchar(40) DEFAULT NULL,
  `TB_01_Usuario_Usuario_Codigo` int(11) NOT NULL,
  `TB_01_Usuario_TB_02_Sucursal_Codigo` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=851 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_12_transacciones`
--

INSERT INTO `tb_12_transacciones` (`Trans_Codigo`, `Trans_Descripcion`, `Trans_Fecha_Hora`, `Trans_Tipo`, `Trans_IP`, `TB_01_Usuario_Usuario_Codigo`, `TB_01_Usuario_TB_02_Sucursal_Codigo`) VALUES
(8, 'El usuario se logueo al sistema', '2015-09-11 22:06:36', 'login', '192.168.1.3', 1, 0),
(9, 'El usuario ingreso el usuario Esteban codigo: 3', '2015-09-11 22:07:58', 'registro', '192.168.1.3', 1, 0),
(10, 'El usuario ingreso el usuario Esteban codigo: 4', '2015-09-11 22:09:32', 'registro', '192.168.1.3', 1, 0),
(11, 'El usuario ingreso el usuario Esteban codigo: 5', '2015-09-11 22:10:43', 'registro', '192.168.1.3', 1, 0),
(12, 'El usuario ingreso el usuario Esteban codigo: 6', '2015-09-11 22:11:47', 'registro', '192.168.1.3', 1, 0),
(13, 'El usuario ingreso el usuario Esteban codigo: 7', '2015-09-11 22:12:57', 'registro', '192.168.1.3', 1, 0),
(14, 'El usuario ingreso el usuario Esteban codigo: 8', '2015-09-11 22:14:01', 'registro', '192.168.1.3', 1, 0),
(15, 'El usuario ingreso el usuario Esteban codigo: 9', '2015-09-11 22:15:41', 'registro', '192.168.1.3', 1, 0),
(16, 'El usuario ingreso el usuario Esteban codigo: 10', '2015-09-11 22:16:50', 'registro', '192.168.1.3', 1, 0),
(17, 'El usuario editó el usuario codigo: 3', '2015-09-11 22:22:23', 'edicion', '192.168.1.3', 1, 0),
(18, 'El usuario editó el usuario codigo: 4', '2015-09-11 22:22:43', 'edicion', '192.168.1.3', 1, 0),
(19, 'El usuario editó el usuario codigo: 5', '2015-09-11 22:23:07', 'edicion', '192.168.1.3', 1, 0),
(20, 'El usuario editó el usuario codigo: 6', '2015-09-11 22:23:22', 'edicion', '192.168.1.3', 1, 0),
(21, 'El usuario editó el usuario codigo: 7', '2015-09-11 22:23:34', 'edicion', '192.168.1.3', 1, 0),
(22, 'El usuario editó el usuario codigo: 7', '2015-09-11 22:23:43', 'edicion', '192.168.1.3', 1, 0),
(23, 'El usuario editó el usuario codigo: 8', '2015-09-11 22:24:07', 'edicion', '192.168.1.3', 1, 0),
(24, 'El usuario editó el usuario codigo: 9', '2015-09-11 22:24:29', 'edicion', '192.168.1.3', 1, 0),
(25, 'El usuario editó el usuario codigo: 10', '2015-09-11 22:24:50', 'edicion', '192.168.1.3', 1, 0),
(26, 'El usuario salio del sistema', '2015-09-11 22:26:28', 'login', '192.168.1.3', 1, 0),
(27, 'El usuario se logueo al sistema', '2015-09-11 22:26:38', 'login', '192.168.1.3', 3, 0),
(28, 'El usuario editó el usuario codigo: 1', '2015-09-11 22:27:03', 'edicion', '192.168.1.3', 3, 0),
(29, 'El usuario editó el usuario codigo: 2', '2015-09-11 22:29:40', 'edicion', '192.168.1.3', 3, 0),
(30, 'El usuario editó el usuario codigo: 1', '2015-09-11 22:29:52', 'edicion', '192.168.1.3', 3, 0),
(31, 'El usuario editó el usuario codigo: 3', '2015-09-11 22:35:42', 'edicion', '192.168.1.3', 3, 0),
(32, 'El usuario editó el usuario codigo: 4', '2015-09-11 22:36:09', 'edicion', '192.168.1.3', 3, 0),
(33, 'El usuario se logueo al sistema', '2015-09-12 00:09:57', 'login', '192.168.1.3', 3, 0),
(34, 'El usuario salio del sistema', '2015-09-12 00:10:42', 'login', '192.168.1.3', 3, 0),
(35, 'El usuario se logueo al sistema', '2015-09-12 15:39:00', 'login', '192.168.1.3', 3, 0),
(36, 'El usuario ingreso el usuario Juan  codigo: 11', '2015-09-12 15:48:01', 'registro', '192.168.1.3', 3, 0),
(37, 'El usuario editó el usuario codigo: 11', '2015-09-12 15:48:21', 'edicion', '192.168.1.3', 3, 0),
(38, 'El usuario salio del sistema', '2015-09-12 15:48:30', 'login', '192.168.1.3', 3, 0),
(39, 'El usuario se logueo al sistema', '2015-09-12 15:48:42', 'login', '192.168.1.3', 11, 0),
(40, 'jvargasgb autorizo articulo generico, sesion de: 11', '2015-09-12 16:59:28', 'autoriza', '192.168.1.3', 11, 0),
(41, 'El usuario se logueo al sistema', '2015-09-12 19:28:04', 'login', '192.168.1.3', 11, 0),
(42, 'El usuario salio del sistema', '2015-09-12 19:28:12', 'login', '192.168.1.3', 11, 0),
(43, 'El usuario se logueo al sistema', '2015-09-12 20:23:25', 'login', '192.168.1.3', 3, 0),
(44, 'El usuario se logueo al sistema', '2015-09-12 20:25:30', 'login', '192.168.1.3', 11, 0),
(45, 'El usuario ingreso el usuario Marilyn codigo: 12', '2015-09-12 20:27:24', 'registro', '192.168.1.3', 3, 0),
(46, 'El usuario ingreso el cliente JACQUELINE PAOLA codigo: 111390115', '2015-09-12 20:27:25', 'registro', '192.168.1.3', 11, 0),
(47, 'El usuario editó el usuario codigo: 12', '2015-09-12 20:29:41', 'edicion', '192.168.1.3', 3, 0),
(48, 'El usuario salio del sistema', '2015-09-12 20:29:52', 'login', '192.168.1.3', 3, 0),
(49, 'El usuario se logueo al sistema', '2015-09-12 20:30:58', 'login', '192.168.1.3', 12, 0),
(50, 'El usuario salio del sistema', '2015-09-12 20:32:29', 'login', '192.168.1.3', 12, 0),
(51, 'El usuario ingreso el cliente ANA PATRICIA  codigo: 155818688909', '2015-09-12 20:32:37', 'registro', '192.168.1.3', 11, 0),
(52, 'El usuario se logueo al sistema', '2015-09-12 20:32:43', 'login', '192.168.1.3', 3, 0),
(53, 'El usuario salio del sistema', '2015-09-12 20:35:22', 'login', '192.168.1.3', 3, 0),
(54, 'El usuario se logueo al sistema', '2015-09-12 20:38:13', 'login', '192.168.1.3', 3, 0),
(55, 'El usuario ingreso el cliente JUSTINA  codigo: 601160247', '2015-09-12 20:40:27', 'registro', '192.168.1.3', 11, 0),
(56, 'El usuario ingreso el usuario Digna  codigo: 13', '2015-09-12 20:41:30', 'registro', '192.168.1.3', 3, 0),
(57, 'El usuario editó el usuario codigo: 13', '2015-09-12 20:42:12', 'edicion', '192.168.1.3', 3, 0),
(58, 'El usuario salio del sistema', '2015-09-12 20:42:16', 'login', '192.168.1.3', 3, 0),
(59, 'El usuario se logueo al sistema', '2015-09-12 20:43:07', 'login', '192.168.1.3', 13, 0),
(60, 'El usuario salio del sistema', '2015-09-12 20:46:34', 'login', '192.168.1.3', 13, 0),
(61, 'El usuario se logueo al sistema', '2015-09-12 20:46:40', 'login', '192.168.1.3', 3, 0),
(62, 'El usuario ingreso el usuario Javier  codigo: 14', '2015-09-12 20:50:33', 'registro', '192.168.1.3', 3, 0),
(63, 'El usuario editó el usuario codigo: 14', '2015-09-12 20:51:39', 'edicion', '192.168.1.3', 3, 0),
(64, 'El usuario ingreso el cliente NATHALIE MELISSA codigo: 112650952', '2015-09-12 20:55:56', 'registro', '192.168.1.3', 11, 0),
(65, 'El usuario ingreso el usuario Gabriel  codigo: 15', '2015-09-12 20:59:00', 'registro', '192.168.1.3', 3, 0),
(66, 'El usuario editó el usuario codigo: 15', '2015-09-12 20:59:31', 'edicion', '192.168.1.3', 3, 0),
(67, 'El usuario salio del sistema', '2015-09-12 20:59:34', 'login', '192.168.1.3', 3, 0),
(68, 'El usuario se logueo al sistema', '2015-09-12 21:00:01', 'login', '192.168.1.3', 15, 0),
(69, 'El usuario ingreso el cliente ADELA YESSENIA codigo: 155807741218', '2015-09-12 21:03:19', 'registro', '192.168.1.3', 11, 0),
(70, 'El usuario salio del sistema', '2015-09-12 21:06:13', 'login', '192.168.1.3', 15, 0),
(71, 'El usuario se logueo al sistema', '2015-09-12 21:06:21', 'login', '192.168.1.3', 3, 0),
(72, 'El usuario editó el usuario codigo: 15', '2015-09-12 21:06:43', 'edicion', '192.168.1.3', 3, 0),
(73, 'El usuario salio del sistema', '2015-09-12 21:06:45', 'login', '192.168.1.3', 3, 0),
(74, 'El usuario se logueo al sistema', '2015-09-12 21:07:07', 'login', '192.168.1.3', 15, 0),
(75, 'El usuario salio del sistema', '2015-09-12 21:07:44', 'login', '192.168.1.3', 15, 0),
(76, 'El usuario se logueo al sistema', '2015-09-12 21:11:10', 'login', '192.168.1.3', 3, 0),
(77, 'El usuario ingreso el usuario Priscilla codigo: 16', '2015-09-12 21:18:19', 'registro', '192.168.1.3', 3, 0),
(78, 'El usuario editó el usuario codigo: 16', '2015-09-12 21:20:04', 'edicion', '192.168.1.3', 3, 0),
(79, 'El usuario salio del sistema', '2015-09-12 21:20:07', 'login', '192.168.1.3', 3, 0),
(80, 'El usuario se logueo al sistema', '2015-09-12 21:20:52', 'login', '192.168.1.3', 16, 0),
(81, 'El usuario ingreso el cliente KARELIA MARGARITA codigo: 155803106914', '2015-09-12 21:21:56', 'registro', '192.168.1.3', 11, 0),
(82, 'El usuario salio del sistema', '2015-09-12 21:22:37', 'login', '192.168.1.3', 16, 0),
(83, 'El usuario se logueo al sistema', '2015-09-12 21:22:46', 'login', '192.168.1.3', 3, 0),
(84, 'El usuario ingreso el usuario Jostin codigo: 17', '2015-09-12 21:38:39', 'registro', '192.168.1.3', 3, 0),
(85, 'El usuario editó el usuario codigo: 17', '2015-09-12 21:39:30', 'edicion', '192.168.1.3', 3, 0),
(86, 'El usuario salio del sistema', '2015-09-12 21:39:33', 'login', '192.168.1.3', 3, 0),
(87, 'El usuario se logueo al sistema', '2015-09-12 21:39:59', 'login', '192.168.1.3', 17, 0),
(88, 'El usuario salio del sistema', '2015-09-12 21:41:43', 'login', '192.168.1.3', 17, 0),
(89, 'El usuario se logueo al sistema', '2015-09-12 21:41:51', 'login', '192.168.1.3', 3, 0),
(90, 'El usuario salio del sistema', '2015-09-12 21:42:46', 'login', '192.168.1.3', 3, 0),
(91, 'El usuario se logueo al sistema', '2015-09-12 21:45:49', 'login', '192.168.1.3', 3, 0),
(92, 'El usuario ingreso el usuario Daniel codigo: 18', '2015-09-12 21:49:36', 'registro', '192.168.1.3', 3, 0),
(93, 'El usuario editó el usuario codigo: 18', '2015-09-12 21:51:15', 'edicion', '192.168.1.3', 3, 0),
(94, 'El usuario salio del sistema', '2015-09-12 21:51:37', 'login', '192.168.1.3', 3, 0),
(95, 'El usuario se logueo al sistema', '2015-09-12 21:52:50', 'login', '192.168.1.3', 18, 0),
(96, 'El usuario salio del sistema', '2015-09-12 22:05:04', 'login', '192.168.1.3', 18, 0),
(97, 'El usuario se logueo al sistema', '2015-09-12 22:05:28', 'login', '192.168.1.3', 3, 0),
(98, 'El usuario editó el cliente codigo: ', '2015-09-12 22:05:46', 'edicion', '192.168.1.3', 3, 0),
(99, 'El usuario editó el cliente codigo: ', '2015-09-12 22:06:29', 'edicion', '192.168.1.3', 3, 0),
(100, 'El usuario salio del sistema', '2015-09-12 22:07:18', 'login', '192.168.1.3', 3, 0),
(101, 'El usuario se logueo al sistema', '2015-09-12 22:08:16', 'login', '192.168.1.3', 3, 0),
(102, 'El usuario salio del sistema', '2015-09-12 22:19:25', 'login', '192.168.1.3', 3, 0),
(103, 'El usuario ingreso el cliente DAYANA DE LOS ANGELES codigo: 115370525', '2015-09-12 22:19:44', 'registro', '192.168.1.3', 11, 0),
(104, 'El usuario se logueo al sistema', '2015-09-12 22:36:24', 'login', '192.168.1.3', 3, 0),
(105, 'El usuario ingreso el usuario Juan Felix codigo: 19', '2015-09-12 22:38:13', 'registro', '192.168.1.3', 3, 0),
(106, 'El usuario editó el usuario codigo: 19', '2015-09-12 22:38:40', 'edicion', '192.168.1.3', 3, 0),
(107, 'El usuario salio del sistema', '2015-09-12 22:38:43', 'login', '192.168.1.3', 3, 0),
(108, 'El usuario se logueo al sistema', '2015-09-12 22:39:01', 'login', '192.168.1.3', 19, 0),
(109, 'El usuario salio del sistema', '2015-09-12 22:42:51', 'login', '192.168.1.3', 19, 0),
(110, 'El usuario se logueo al sistema', '2015-09-12 22:43:03', 'login', '192.168.1.3', 3, 0),
(111, 'El usuario se logueo al sistema', '2015-09-12 22:43:03', 'login', '192.168.1.3', 3, 0),
(112, 'El usuario ingreso el usuario Yadir codigo: 20', '2015-09-12 23:02:02', 'registro', '192.168.1.3', 3, 0),
(113, 'El usuario editó el usuario codigo: 20', '2015-09-12 23:02:29', 'edicion', '192.168.1.3', 3, 0),
(114, 'El usuario salio del sistema', '2015-09-12 23:02:33', 'login', '192.168.1.3', 3, 0),
(115, 'El usuario se logueo al sistema', '2015-09-12 23:02:49', 'login', '192.168.1.3', 20, 0),
(116, 'El usuario salio del sistema', '2015-09-12 23:05:04', 'login', '192.168.1.3', 20, 0),
(117, 'El usuario se logueo al sistema', '2015-09-14 15:17:30', 'login', '192.168.1.3', 17, 0),
(118, 'El usuario se logueo al sistema', '2015-09-14 15:17:55', 'login', '192.168.1.3', 19, 0),
(119, 'El usuario se logueo al sistema', '2015-09-14 15:19:00', 'login', '192.168.1.3', 18, 0),
(120, 'El usuario ingreso el cliente MARIA EUGENIA  codigo: 104510798', '2015-09-14 15:20:28', 'registro', '192.168.1.3', 19, 0),
(121, 'El usuario ingreso el cliente Antonia del Carmen codigo: 155813006128', '2015-09-14 15:22:46', 'registro', '192.168.1.3', 18, 0),
(122, 'El usuario ingreso el cliente ANA MARLENE  codigo: 105630498', '2015-09-14 15:23:56', 'registro', '192.168.1.3', 19, 0),
(123, 'El usuario ingreso el cliente Monica codigo: 402030529', '2015-09-14 15:24:07', 'registro', '192.168.1.3', 18, 0),
(124, 'El usuario salio del sistema', '2015-09-14 15:26:02', 'login', '192.168.1.3', 18, 0),
(125, 'El usuario ingreso el cliente LAURA codigo: 111470848', '2015-09-14 15:31:08', 'registro', '192.168.1.3', 17, 0),
(126, 'El usuario ingreso el cliente KRISTEL codigo: 113050635', '2015-09-14 15:32:36', 'registro', '192.168.1.3', 17, 0),
(127, 'El usuario ingreso el cliente ZORAIDA codigo: 102310944', '2015-09-14 15:33:38', 'registro', '192.168.1.3', 17, 0),
(128, 'El usuario ingreso el cliente MELISA codigo: 504040685', '2015-09-14 15:34:56', 'registro', '192.168.1.3', 17, 0),
(129, 'El usuario ingreso el cliente CLAUDIA codigo: 701870386', '2015-09-14 15:36:09', 'registro', '192.168.1.3', 17, 0),
(130, 'El usuario ingreso el cliente IRIS codigo: 108260755', '2015-09-14 15:37:40', 'registro', '192.168.1.3', 17, 0),
(131, 'El usuario salio del sistema', '2015-09-14 15:38:42', 'login', '192.168.1.3', 17, 0),
(132, 'El usuario se logueo al sistema', '2015-09-14 16:04:23', 'login', '192.168.1.3', 17, 0),
(133, 'El usuario ingreso el cliente MARIA TERESA codigo: 155807931122', '2015-09-14 16:05:48', 'registro', '192.168.1.3', 17, 0),
(134, 'El usuario ingreso el cliente JETZABEL codigo: 112770841', '2015-09-14 16:07:00', 'registro', '192.168.1.3', 17, 0),
(136, 'El usuario se logueo al sistema', '2015-09-14 17:53:21', 'login', '192.168.1.3', 18, 0),
(137, 'El usuario cambio su perfil: 18', '2015-09-14 17:55:08', 'edicion', '192.168.1.3', 18, 0),
(138, 'El usuario cambio su perfil: 18', '2015-09-14 17:55:56', 'edicion', '192.168.1.3', 18, 0),
(139, 'El usuario salio del sistema', '2015-09-14 17:56:05', 'login', '192.168.1.3', 18, 0),
(140, 'El usuario se logueo al sistema', '2015-09-14 17:56:17', 'login', '192.168.1.3', 18, 0),
(143, 'El usuario ingreso el cliente Adriana codigo: 110450776', '2015-09-14 17:58:42', 'registro', '192.168.1.3', 18, 0),
(144, 'El usuario se logueo al sistema', '2015-09-14 17:59:45', 'login', '192.168.1.3', 20, 0),
(145, 'El usuario ingreso el cliente Olga  codigo: 109080335', '2015-09-14 18:00:24', 'registro', '192.168.1.3', 18, 0),
(146, 'El usuario salio del sistema', '2015-09-14 18:00:32', 'login', '192.168.1.3', 18, 0),
(147, 'El usuario ingreso el cliente ELIZABETH codigo: 105740838', '2015-09-14 18:03:53', 'registro', '192.168.1.3', 20, 0),
(149, 'El usuario se logueo al sistema', '2015-09-14 18:33:19', 'login', '192.168.1.3', 11, 0),
(150, 'El usuario ingreso el cliente JANIXIA GASSALY codigo: 155800454801', '2015-09-14 18:34:33', 'registro', '192.168.1.3', 11, 0),
(151, 'El usuario se logueo al sistema', '2015-09-14 19:30:38', 'login', '192.168.1.3', 13, 0),
(152, 'El usuario ingreso el cliente MARJORIE codigo: 501650334', '2015-09-14 19:37:07', 'registro', '192.168.1.3', 13, 0),
(153, 'El usuario ingreso el cliente MARIA DEL CARMEN. codigo: 155811888418', '2015-09-14 19:41:25', 'registro', '192.168.1.3', 13, 0),
(154, 'El usuario ingreso el cliente CINDY codigo: 110560181', '2015-09-14 19:46:08', 'registro', '192.168.1.3', 13, 0),
(155, 'El usuario ingreso el cliente KAROL VANESSA. codigo: 113050545', '2015-09-14 19:54:18', 'registro', '192.168.1.3', 13, 0),
(156, 'El usuario ingreso el cliente XINIA codigo: 501450458', '2015-09-14 19:57:06', 'registro', '192.168.1.3', 13, 0),
(157, 'El usuario ingreso el cliente KARINA codigo: 114480451', '2015-09-14 20:02:28', 'registro', '192.168.1.3', 13, 0),
(159, 'El usuario ingreso el cliente ANA YANCY . codigo: 701450985', '2015-09-14 20:04:51', 'registro', '192.168.1.3', 13, 0),
(160, 'El usuario ingreso el cliente NIDIA  codigo: 107410117', '2015-09-14 20:07:55', 'registro', '192.168.1.3', 13, 0),
(161, 'El usuario ingreso el cliente MERCEDES LIZETH codigo: 155807070428', '2015-09-14 20:12:18', 'registro', '192.168.1.3', 13, 0),
(162, 'El usuario ingreso el cliente PATRICIA codigo: 155802741036', '2015-09-14 20:15:30', 'registro', '192.168.1.3', 13, 0),
(163, 'El usuario se logueo al sistema', '2015-09-14 20:30:55', 'login', '192.168.1.3', 19, 0),
(164, 'El usuario ingreso el cliente RITA PATRICIA  codigo: 107010357', '2015-09-14 20:33:36', 'registro', '192.168.1.3', 19, 0),
(165, 'El usuario se logueo al sistema', '2015-09-14 20:37:52', 'login', '192.168.1.3', 19, 0),
(166, 'El usuario salio del sistema', '2015-09-14 20:38:01', 'login', '192.168.1.3', 19, 0),
(167, 'El usuario salio del sistema', '2015-09-14 20:39:35', 'login', '192.168.1.3', 19, 0),
(168, 'El usuario se logueo al sistema', '2015-09-14 21:43:48', 'login', '192.168.1.3', 18, 0),
(169, 'El usuario ingreso el cliente Estefany codigo: 112180310', '2015-09-14 21:46:22', 'registro', '192.168.1.3', 18, 0),
(170, 'El usuario ingreso el cliente Nidia codigo: 601540758', '2015-09-14 21:48:00', 'registro', '192.168.1.3', 18, 0),
(171, 'El usuario editó el cliente codigo: ', '2015-09-14 21:50:09', 'edicion', '192.168.1.3', 18, 0),
(172, 'El usuario salio del sistema', '2015-09-14 21:50:19', 'login', '192.168.1.3', 18, 0),
(173, 'El usuario se logueo al sistema', '2015-09-14 22:26:25', 'login', '192.168.1.3', 18, 0),
(174, 'El usuario ingreso el cliente Xiomara codigo: 107980962', '2015-09-14 22:29:10', 'registro', '192.168.1.3', 18, 0),
(176, 'El usuario se logueo al sistema', '2015-09-14 22:55:41', 'login', '192.168.1.3', 13, 0),
(177, 'El usuario ingreso el cliente JENNY codigo: 107770677', '2015-09-14 22:59:41', 'registro', '192.168.1.3', 13, 0),
(178, 'El usuario ingreso el cliente YAMILETH codigo: 155815157614', '2015-09-14 23:43:07', 'registro', '192.168.1.3', 13, 0),
(179, 'El usuario ingreso el cliente ANA  ELIETH  codigo: 601620122', '2015-09-14 23:45:41', 'registro', '192.168.1.3', 13, 0),
(180, 'El usuario se logueo al sistema', '2015-09-16 15:12:13', 'login', '192.168.1.3', 20, 0),
(181, 'El usuario se logueo al sistema', '2015-09-16 15:28:41', 'login', '192.168.1.3', 19, 0),
(182, 'El usuario se logueo al sistema', '2015-09-16 15:42:01', 'login', '192.168.1.3', 13, 0),
(183, 'El usuario ingreso el cliente HEIDY MAIRENE codigo: 111170978', '2015-09-16 15:45:19', 'registro', '192.168.1.3', 13, 0),
(184, 'El usuario salio del sistema', '2015-09-16 15:52:42', 'login', '192.168.1.3', 19, 0),
(185, 'El usuario se logueo al sistema', '2015-09-16 15:55:27', 'login', '192.168.1.3', 11, 0),
(186, 'El usuario se logueo al sistema', '2015-09-16 16:14:38', 'login', '192.168.1.3', 18, 0),
(187, 'El usuario ingreso el cliente María Laura codigo: 112510178', '2015-09-16 16:19:00', 'registro', '192.168.1.3', 18, 0),
(188, 'El usuario ingreso el cliente Luz María codigo: 600570554', '2015-09-16 16:20:49', 'registro', '192.168.1.3', 18, 0),
(189, 'El usuario salio del sistema', '2015-09-16 16:20:59', 'login', '192.168.1.3', 18, 0),
(190, 'El usuario se logueo al sistema', '2015-09-16 17:05:57', 'login', '192.168.1.3', 18, 0),
(191, 'El usuario salio del sistema', '2015-09-16 17:07:15', 'login', '192.168.1.3', 18, 0),
(192, 'El usuario se logueo al sistema', '2015-09-16 17:18:50', 'login', '192.168.1.3', 18, 0),
(193, 'El usuario se logueo al sistema', '2015-09-16 17:21:52', 'login', '192.168.1.3', 19, 0),
(194, 'El usuario ingreso el cliente Mariana codigo: 113170336', '2015-09-16 17:22:36', 'registro', '192.168.1.3', 18, 0),
(195, 'El usuario salio del sistema', '2015-09-16 17:22:51', 'login', '192.168.1.3', 19, 0),
(196, 'El usuario se logueo al sistema', '2015-09-16 17:29:15', 'login', '192.168.1.3', 19, 0),
(197, 'El usuario ingreso el cliente MARGARITA codigo: 111990800', '2015-09-16 17:34:12', 'registro', '192.168.1.3', 19, 0),
(198, 'El usuario se logueo al sistema', '2015-09-16 17:35:50', 'login', '192.168.1.3', 3, 0),
(199, 'El usuario salio del sistema', '2015-09-16 17:37:15', 'login', '192.168.1.3', 3, 0),
(200, 'El usuario se logueo al sistema', '2015-09-16 17:37:50', 'login', '192.168.1.3', 3, 0),
(201, 'El usuario editó el cliente codigo: ', '2015-09-16 17:38:53', 'edicion', '192.168.1.3', 3, 0),
(202, 'El usuario salio del sistema', '2015-09-16 17:38:57', 'login', '192.168.1.3', 3, 0),
(203, 'El usuario salio del sistema', '2015-09-16 17:39:16', 'login', '192.168.1.3', 19, 0),
(204, 'El usuario se logueo al sistema', '2015-09-16 17:46:25', 'login', '192.168.1.3', 18, 0),
(205, 'El usuario salio del sistema', '2015-09-16 17:46:50', 'login', '192.168.1.3', 18, 0),
(206, 'El usuario se logueo al sistema', '2015-09-16 17:49:14', 'login', '192.168.1.3', 13, 0),
(207, 'El usuario ingreso el cliente ANGELLA OFELIA codigo: 800980303', '2015-09-16 17:49:35', 'registro', '192.168.1.3', 18, 0),
(208, 'El usuario ingreso el cliente LAURA VANESSA codigo: 503420414', '2015-09-16 17:51:52', 'registro', '192.168.1.3', 18, 0),
(209, 'El usuario ingreso el cliente MARILYN codigo: 503080691', '2015-09-16 17:52:19', 'registro', '192.168.1.3', 13, 0),
(210, 'El usuario ingreso el cliente NAIMA codigo: 155821228435', '2015-09-16 17:55:20', 'registro', '192.168.1.3', 13, 0),
(211, 'El usuario ingreso el cliente SAMANTHA codigo: 155822148221', '2015-09-16 17:56:04', 'registro', '192.168.1.3', 18, 0),
(212, 'El usuario ingreso el cliente ANA VICTORIA codigo: 103890280', '2015-09-16 17:57:36', 'registro', '192.168.1.3', 18, 0),
(213, 'El usuario salio del sistema', '2015-09-16 17:59:00', 'login', '192.168.1.3', 18, 0),
(214, 'El usuario se logueo al sistema', '2015-09-16 18:00:38', 'login', '192.168.1.3', 19, 0),
(215, 'El usuario ingreso el cliente KARINA  codigo: 115380868', '2015-09-16 18:03:34', 'registro', '192.168.1.3', 19, 0),
(216, 'El usuario salio del sistema', '2015-09-16 18:03:49', 'login', '192.168.1.3', 19, 0),
(218, 'El usuario se logueo al sistema', '2015-09-16 23:37:07', 'login', '192.168.1.3', 13, 0),
(219, 'El usuario ingreso el cliente DEYANIRA  codigo: 155819375031', '2015-09-16 23:41:21', 'registro', '192.168.1.3', 13, 0),
(220, 'El usuario ingreso el cliente MARIA ESTHER codigo: 202630830', '2015-09-16 23:44:33', 'registro', '192.168.1.3', 13, 0),
(221, 'El usuario ingreso el cliente IRIS MARIA  codigo: 103160037', '2015-09-16 23:49:26', 'registro', '192.168.1.3', 13, 0),
(222, 'El usuario ingreso el cliente MARCELA  codigo: 113760208', '2015-09-16 23:53:27', 'registro', '192.168.1.3', 13, 0),
(223, 'El usuario ingreso el cliente DALIA codigo: 103950395', '2015-09-16 23:55:51', 'registro', '192.168.1.3', 13, 0),
(224, 'El usuario se logueo al sistema', '2015-09-17 15:32:17', 'login', '192.168.1.3', 18, 0),
(225, 'El usuario ingreso el cliente MARILIA codigo: 502640547', '2015-09-17 15:34:16', 'registro', '192.168.1.3', 18, 0),
(226, 'El usuario ingreso el cliente ROSA MARÍA  codigo: 104970249', '2015-09-17 15:37:10', 'registro', '192.168.1.3', 18, 0),
(227, 'El usuario ingreso el cliente MARÍA AUXILIADORA codigo: 155810529025', '2015-09-17 15:40:33', 'registro', '192.168.1.3', 18, 0),
(228, 'El usuario ingreso el cliente MARÍA DEL CARMEN codigo: 155810661106', '2015-09-17 15:43:08', 'registro', '192.168.1.3', 18, 0),
(229, 'El usuario salio del sistema', '2015-09-17 15:43:27', 'login', '192.168.1.3', 18, 0),
(230, 'El usuario se logueo al sistema', '2015-09-17 17:38:02', 'login', '192.168.1.3', 18, 0),
(231, 'El usuario ingreso el cliente MELISSA codigo: 112120657', '2015-09-17 17:40:24', 'registro', '192.168.1.3', 18, 0),
(232, 'El usuario salio del sistema', '2015-09-17 17:41:04', 'login', '192.168.1.3', 18, 0),
(233, 'El usuario se logueo al sistema', '2015-09-17 18:19:42', 'login', '192.168.1.3', 20, 0),
(234, 'El usuario salio del sistema', '2015-09-17 18:47:20', 'login', '192.168.1.3', 20, 0),
(235, 'El usuario se logueo al sistema', '2015-09-17 19:41:49', 'login', '192.168.1.3', 11, 0),
(236, 'El usuario ingreso el cliente SHIRLEY SUSANA codigo: 110260997', '2015-09-17 19:42:55', 'registro', '192.168.1.3', 11, 0),
(237, 'El usuario se logueo al sistema', '2015-09-17 21:31:51', 'login', '192.168.1.3', 13, 0),
(238, 'El usuario ingreso el cliente BRAULIA  MARIA. codigo: 601730644', '2015-09-17 21:34:08', 'registro', '192.168.1.3', 13, 0),
(239, 'El usuario ingreso el cliente SOFIA codigo: 113690962', '2015-09-17 21:37:32', 'registro', '192.168.1.3', 13, 0),
(240, 'El usuario ingreso el cliente EVANY  YORJANY codigo: 113590797', '2015-09-17 21:40:28', 'registro', '192.168.1.3', 13, 0),
(241, 'El usuario ingreso el cliente KAREN . codigo: 114470185', '2015-09-17 21:42:51', 'registro', '192.168.1.3', 13, 0),
(242, 'El usuario se logueo al sistema', '2015-09-17 21:44:01', 'login', '192.168.1.3', 20, 0),
(243, 'El usuario ingreso el cliente EMILIA  codigo: 106340439', '2015-09-17 21:52:28', 'registro', '192.168.1.3', 13, 0),
(245, 'El usuario ingreso el cliente MARIA DE LOS ANGELES. codigo: 303000122', '2015-09-17 22:26:43', 'registro', '192.168.1.3', 13, 0),
(246, 'El usuario ingreso el cliente JULIETA. codigo: 502160443', '2015-09-17 22:28:56', 'registro', '192.168.1.3', 13, 0),
(247, 'El usuario ingreso el cliente ANA  BENIGNA. codigo: 155806957026', '2015-09-17 22:31:51', 'registro', '192.168.1.3', 13, 0),
(248, 'El usuario ingreso el cliente MARIA CRISTINA. codigo: 206330992', '2015-09-17 22:35:38', 'registro', '192.168.1.3', 13, 0),
(249, 'El usuario se logueo al sistema', '2015-09-17 22:50:22', 'login', '192.168.1.3', 18, 0),
(250, 'El usuario ingreso el cliente ERICKA codigo: 155820603200', '2015-09-17 22:53:48', 'registro', '192.168.1.3', 18, 0),
(251, 'El usuario ingreso el cliente JULIO CÉSAR codigo: 111850292', '2015-09-17 22:58:09', 'registro', '192.168.1.3', 18, 0),
(252, 'El usuario salio del sistema', '2015-09-17 23:01:14', 'login', '192.168.1.3', 18, 0),
(253, 'El usuario ingreso el cliente MARIBEL codigo: 105080290', '2015-09-17 23:11:12', 'registro', '192.168.1.3', 20, 0),
(254, 'El usuario ingreso el cliente MARIA DE LOS ANGELES codigo: 700740468', '2015-09-17 23:13:20', 'registro', '192.168.1.3', 20, 0),
(255, 'El usuario editó el cliente codigo: ', '2015-09-17 23:15:15', 'edicion', '192.168.1.3', 20, 0),
(256, 'El usuario se logueo al sistema', '2015-09-17 23:22:48', 'login', '192.168.1.3', 19, 0),
(257, 'El usuario ingreso el cliente JAMILETH codigo: 155803251433', '2015-09-17 23:26:03', 'registro', '192.168.1.3', 19, 0),
(258, 'El usuario ingreso el cliente RITA codigo: 104500518', '2015-09-18 00:23:13', 'registro', '192.168.1.3', 13, 0),
(259, 'El usuario ingreso el cliente VICTORIA codigo: 107320157', '2015-09-18 00:26:30', 'registro', '192.168.1.3', 13, 0),
(260, 'El usuario se logueo al sistema', '2015-09-18 15:10:27', 'login', '192.168.1.3', 17, 0),
(261, 'El usuario ingreso el cliente MARTA codigo: 203070167', '2015-09-18 15:12:37', 'registro', '192.168.1.3', 17, 0),
(262, 'El usuario se logueo al sistema', '2015-09-18 15:12:49', 'login', '192.168.1.3', 18, 0),
(263, 'El usuario editó el cliente codigo: ', '2015-09-18 15:13:17', 'edicion', '192.168.1.3', 17, 0),
(264, 'El usuario cambio su perfil: 18', '2015-09-18 15:13:39', 'edicion', '192.168.1.3', 18, 0),
(265, 'El usuario salio del sistema', '2015-09-18 15:13:43', 'login', '192.168.1.3', 18, 0),
(266, 'El usuario se logueo al sistema', '2015-09-18 15:14:08', 'login', '192.168.1.3', 18, 0),
(267, 'El usuario salio del sistema', '2015-09-18 15:14:15', 'login', '192.168.1.3', 18, 0),
(268, 'El usuario se logueo al sistema', '2015-09-18 15:15:45', 'login', '192.168.1.3', 17, 0),
(269, 'El usuario se logueo al sistema', '2015-09-18 15:29:07', 'login', '192.168.1.3', 18, 0),
(270, 'El usuario salio del sistema', '2015-09-18 15:30:49', 'login', '192.168.1.3', 18, 0),
(271, 'El usuario se logueo al sistema', '2015-09-18 15:41:50', 'login', '192.168.1.3', 19, 0),
(272, 'El usuario ingreso el cliente ANA LORENA  codigo: 203760403', '2015-09-18 15:43:48', 'registro', '192.168.1.3', 19, 0),
(273, 'El usuario ingreso el cliente MARIA JULIA  codigo: 109340511', '2015-09-18 15:46:39', 'registro', '192.168.1.3', 19, 0),
(274, 'El usuario ingreso el cliente MARGOTH  codigo: 107450507', '2015-09-18 15:50:32', 'registro', '192.168.1.3', 19, 0),
(275, 'El usuario ingreso el cliente LUCRECIA  codigo: 112720079', '2015-09-18 15:53:46', 'registro', '192.168.1.3', 19, 0),
(276, 'El usuario salio del sistema', '2015-09-18 15:57:03', 'login', '192.168.1.3', 19, 0),
(277, 'El usuario se logueo al sistema', '2015-09-18 16:17:35', 'login', '192.168.1.3', 20, 0),
(279, 'El usuario se logueo al sistema', '2015-09-18 18:43:47', 'login', '192.168.1.3', 13, 0),
(280, 'El usuario se logueo al sistema', '2015-09-18 18:44:38', 'login', '192.168.1.3', 20, 0),
(281, 'El usuario ingreso el cliente BERNAL codigo: 602800056', '2015-09-18 18:47:15', 'registro', '192.168.1.3', 13, 0),
(282, 'El usuario ingreso el cliente MARGARITA codigo: 105900075', '2015-09-18 18:49:23', 'registro', '192.168.1.3', 13, 0),
(283, 'El usuario se logueo al sistema', '2015-09-18 18:50:43', 'login', '192.168.1.3', 18, 0),
(284, 'El usuario ingreso el cliente NUBIA codigo: 155808046533', '2015-09-18 18:52:10', 'registro', '192.168.1.3', 13, 0),
(285, 'El usuario ingreso el cliente ISABEL codigo: 107310529', '2015-09-18 18:52:49', 'registro', '192.168.1.3', 18, 0),
(286, 'El usuario ingreso el cliente HELLEN DAMARIS codigo: 155811913222', '2015-09-18 18:56:56', 'registro', '192.168.1.3', 18, 0),
(287, 'El usuario salio del sistema', '2015-09-18 18:57:07', 'login', '192.168.1.3', 18, 0),
(288, 'El usuario se logueo al sistema', '2015-09-18 19:29:42', 'login', '192.168.1.3', 20, 0),
(289, 'El usuario ingreso el cliente AMPARO codigo: 155818209113', '2015-09-18 19:31:46', 'registro', '192.168.1.3', 20, 0),
(290, 'El usuario salio del sistema', '2015-09-18 19:33:41', 'login', '192.168.1.3', 20, 0),
(292, 'El usuario se logueo al sistema', '2015-09-18 20:53:25', 'login', '192.168.1.3', 11, 0),
(293, 'El usuario se logueo al sistema', '2015-09-18 20:56:15', 'login', '192.168.1.3', 19, 0),
(294, 'El usuario ingreso el cliente SUGEY FAINIER codigo: 113620323', '2015-09-18 20:58:53', 'registro', '192.168.1.3', 19, 0),
(295, 'El usuario salio del sistema', '2015-09-18 20:59:39', 'login', '192.168.1.3', 19, 0),
(296, 'El usuario se logueo al sistema', '2015-09-18 23:00:01', 'login', '192.168.1.3', 19, 0),
(298, 'El usuario salio del sistema', '2015-09-18 23:02:07', 'login', '192.168.1.3', 19, 0),
(299, 'El usuario se logueo al sistema', '2015-09-18 23:02:48', 'login', '192.168.1.3', 18, 0),
(300, 'El usuario ingreso el cliente MARIA EUGENIA codigo: 104970002', '2015-09-18 23:05:03', 'registro', '192.168.1.3', 18, 0),
(301, 'El usuario ingreso el cliente ROXANNA codigo: 105030531', '2015-09-18 23:07:50', 'registro', '192.168.1.3', 18, 0),
(302, 'El usuario se logueo al sistema', '2015-09-18 23:08:45', 'login', '192.168.1.3', 20, 0),
(303, 'El usuario ingreso el cliente ANDREA codigo: 111290552', '2015-09-18 23:10:45', 'registro', '192.168.1.3', 20, 0),
(304, 'El usuario ingreso el cliente MARÍA AUXILIADORA codigo: 155807298028', '2015-09-18 23:10:51', 'registro', '192.168.1.3', 18, 0),
(305, 'El usuario salio del sistema', '2015-09-18 23:10:59', 'login', '192.168.1.3', 18, 0),
(306, 'El usuario ingreso el cliente AIDA MARIA  codigo: 155808693015', '2015-09-18 23:13:20', 'registro', '192.168.1.3', 20, 0),
(307, 'El usuario ingreso el cliente FLOR MARIA  codigo: 601650986', '2015-09-18 23:16:11', 'registro', '192.168.1.3', 20, 0),
(308, 'El usuario se logueo al sistema', '2015-09-18 23:16:52', 'login', '192.168.1.3', 13, 0),
(309, 'El usuario ingreso el cliente MARTHA  codigo: 106020650', '2015-09-18 23:18:21', 'registro', '192.168.1.3', 20, 0),
(310, 'El usuario ingreso el cliente MANUELA codigo: 103620892', '2015-09-18 23:19:36', 'registro', '192.168.1.3', 13, 0),
(311, 'El usuario ingreso el cliente ANGIE codigo: 113070309', '2015-09-18 23:26:28', 'registro', '192.168.1.3', 13, 0),
(312, 'El usuario se logueo al sistema', '2015-09-18 23:30:36', 'login', '192.168.1.3', 20, 0),
(313, 'El usuario salio del sistema', '2015-09-18 23:31:05', 'login', '192.168.1.3', 20, 0),
(314, 'El usuario se logueo al sistema', '2015-09-18 23:38:49', 'login', '192.168.1.3', 18, 0),
(315, 'El usuario ingreso el cliente MARIA MERCEDES codigo: 501820700', '2015-09-18 23:40:41', 'registro', '192.168.1.3', 18, 0),
(316, 'El usuario salio del sistema', '2015-09-18 23:40:53', 'login', '192.168.1.3', 18, 0),
(317, 'El usuario se logueo al sistema', '2015-09-18 23:41:51', 'login', '192.168.1.3', 20, 0),
(318, 'El usuario se logueo al sistema', '2015-09-18 23:44:25', 'login', '192.168.1.3', 18, 0),
(319, 'El usuario editó el cliente codigo: ', '2015-09-18 23:46:10', 'edicion', '192.168.1.3', 18, 0),
(320, 'El usuario salio del sistema', '2015-09-18 23:46:22', 'login', '192.168.1.3', 20, 0),
(321, 'El usuario salio del sistema', '2015-09-18 23:48:07', 'login', '192.168.1.3', 18, 0),
(322, 'El usuario se logueo al sistema', '2015-09-19 15:29:22', 'login', '192.168.1.3', 18, 0),
(323, 'El usuario salio del sistema', '2015-09-19 15:31:30', 'login', '192.168.1.3', 18, 0),
(324, 'El usuario se logueo al sistema', '2015-09-19 15:54:20', 'login', '192.168.1.3', 18, 0),
(325, 'El usuario se logueo al sistema', '2015-09-19 16:15:19', 'login', '192.168.1.3', 19, 0),
(326, 'El usuario salio del sistema', '2015-09-19 16:15:44', 'login', '192.168.1.3', 19, 0),
(327, 'El usuario se logueo al sistema', '2015-09-19 16:32:02', 'login', '192.168.1.3', 19, 0),
(328, 'El usuario salio del sistema', '2015-09-19 16:32:13', 'login', '192.168.1.3', 19, 0),
(330, 'El usuario se logueo al sistema', '2015-09-19 19:36:40', 'login', '192.168.1.3', 19, 0),
(331, 'El usuario ingreso el cliente AIDA codigo: 204240296', '2015-09-19 19:38:01', 'registro', '192.168.1.3', 19, 0),
(332, 'El usuario ingreso el cliente MYRIAM BEATRIZ  codigo: 204220286', '2015-09-19 19:40:27', 'registro', '192.168.1.3', 19, 0),
(333, 'El usuario salio del sistema', '2015-09-19 19:40:37', 'login', '192.168.1.3', 19, 0),
(334, 'El usuario se logueo al sistema', '2015-09-19 19:41:35', 'login', '192.168.1.3', 13, 0),
(335, 'El usuario ingreso el cliente HAZEL. codigo: 503590364', '2015-09-19 19:52:00', 'registro', '192.168.1.3', 13, 0),
(336, 'El usuario se logueo al sistema', '2015-09-19 20:47:42', 'login', '192.168.1.3', 20, 0),
(337, 'El usuario ingreso el cliente LIDIA codigo: 601410021', '2015-09-19 20:47:54', 'registro', '192.168.1.3', 13, 0),
(338, 'El usuario ingreso el cliente NATALIA codigo: 111410790', '2015-09-19 20:50:03', 'registro', '192.168.1.3', 20, 0),
(339, 'El usuario ingreso el cliente LIGIA codigo: 700720728', '2015-09-19 20:53:01', 'registro', '192.168.1.3', 13, 0),
(340, 'El usuario ingreso el cliente GUISELLE . codigo: 600990153', '2015-09-19 21:03:58', 'registro', '192.168.1.3', 13, 0),
(341, 'El usuario se logueo al sistema', '2015-09-19 22:19:34', 'login', '192.168.1.3', 18, 0),
(342, 'El usuario salio del sistema', '2015-09-19 22:26:30', 'login', '192.168.1.3', 18, 0),
(344, 'El usuario se logueo al sistema', '2015-09-21 15:04:48', 'login', '192.168.1.3', 18, 0),
(345, 'El usuario ingreso el cliente MARIA codigo: 155812664609', '2015-09-21 15:07:08', 'registro', '192.168.1.3', 18, 0),
(346, 'El usuario ingreso el cliente ROSITA codigo: 302700767', '2015-09-21 15:09:24', 'registro', '192.168.1.3', 18, 0),
(347, 'El usuario ingreso el cliente HANNIA codigo: 113240513', '2015-09-21 15:12:04', 'registro', '192.168.1.3', 18, 0),
(348, 'El usuario salio del sistema', '2015-09-21 15:13:01', 'login', '192.168.1.3', 18, 0),
(349, 'El usuario se logueo al sistema', '2015-09-21 15:13:18', 'login', '192.168.1.3', 20, 0),
(350, 'El usuario ingreso el cliente GEORGINA codigo: 155801632026', '2015-09-21 15:15:18', 'registro', '192.168.1.3', 20, 0),
(351, 'El usuario salio del sistema', '2015-09-21 15:15:35', 'login', '192.168.1.3', 20, 0),
(352, 'El usuario se logueo al sistema', '2015-09-21 15:25:21', 'login', '192.168.1.3', 13, 0),
(353, 'El usuario se logueo al sistema', '2015-09-21 15:30:04', 'login', '192.168.1.3', 19, 0),
(354, 'El usuario ingreso el cliente MIRIAM MARGARET. codigo: 700590939', '2015-09-21 15:34:03', 'registro', '192.168.1.3', 13, 0),
(355, 'El usuario ingreso el cliente CAROLINA  codigo: 111600133', '2015-09-21 15:36:42', 'registro', '192.168.1.3', 19, 0),
(356, 'El usuario ingreso el cliente ERICKA  codigo: 113090399', '2015-09-21 15:43:34', 'registro', '192.168.1.3', 19, 0),
(357, 'El usuario ingreso el cliente KARLA PATRICIA  codigo: 155819888426', '2015-09-21 15:53:22', 'registro', '192.168.1.3', 19, 0),
(358, 'El usuario ingreso el cliente LUZ MARINA  codigo: 800960494', '2015-09-21 15:59:03', 'registro', '192.168.1.3', 19, 0),
(359, 'El usuario ingreso el cliente ALEJANDRA codigo: 109120559', '2015-09-21 16:03:32', 'registro', '192.168.1.3', 19, 0),
(360, 'El usuario ingreso el cliente SILVIA  codigo: 105950215', '2015-09-21 16:06:37', 'registro', '192.168.1.3', 19, 0),
(361, 'El usuario ingreso el cliente ANITA  codigo: 103050183', '2015-09-21 16:10:52', 'registro', '192.168.1.3', 19, 0),
(362, 'El usuario se logueo al sistema', '2015-09-21 16:19:56', 'login', '192.168.1.3', 20, 0),
(363, 'El usuario ingreso el cliente YAMILETH codigo: 701240987', '2015-09-21 16:22:06', 'registro', '192.168.1.3', 20, 0),
(364, 'El usuario salio del sistema', '2015-09-21 16:22:33', 'login', '192.168.1.3', 20, 0),
(365, 'El usuario ingreso el cliente RITA  codigo: 106550675', '2015-09-21 17:01:01', 'registro', '192.168.1.3', 19, 0),
(366, 'El usuario salio del sistema', '2015-09-21 17:01:10', 'login', '192.168.1.3', 19, 0),
(367, 'El usuario se logueo al sistema', '2015-09-21 17:05:54', 'login', '192.168.1.3', 19, 0),
(368, 'El usuario ingreso el cliente YORLINA  codigo: 800990293', '2015-09-21 17:06:59', 'registro', '192.168.1.3', 19, 0),
(369, 'El usuario editó el cliente codigo: ', '2015-09-21 17:07:30', 'edicion', '192.168.1.3', 19, 0),
(370, 'El usuario editó el cliente codigo: ', '2015-09-21 17:08:15', 'edicion', '192.168.1.3', 19, 0),
(371, 'El usuario ingreso el cliente SHIRLEY MARIA  codigo: 601320774', '2015-09-21 17:10:34', 'registro', '192.168.1.3', 19, 0),
(372, 'El usuario ingreso el cliente SUZETTE codigo: 102750322', '2015-09-21 17:14:24', 'registro', '192.168.1.3', 19, 0),
(373, 'El usuario salio del sistema', '2015-09-21 17:14:37', 'login', '192.168.1.3', 19, 0),
(374, 'El usuario se logueo al sistema', '2015-09-21 17:17:18', 'login', '192.168.1.3', 17, 0),
(375, 'El usuario salio del sistema', '2015-09-21 17:26:06', 'login', '192.168.1.3', 17, 0),
(377, 'El usuario se logueo al sistema', '2015-09-21 17:34:49', 'login', '192.168.1.3', 19, 0),
(378, 'El usuario salio del sistema', '2015-09-21 17:37:14', 'login', '192.168.1.3', 19, 0),
(379, 'El usuario se logueo al sistema', '2015-09-21 17:37:30', 'login', '192.168.1.3', 19, 0),
(380, 'El usuario salio del sistema', '2015-09-21 17:37:59', 'login', '192.168.1.3', 19, 0),
(381, 'El usuario se logueo al sistema', '2015-09-21 17:39:51', 'login', '192.168.1.3', 3, 0),
(382, 'El usuario se logueo al sistema', '2015-09-21 17:39:51', 'login', '192.168.1.3', 3, 0),
(383, 'El usuario salio del sistema', '2015-09-21 17:42:52', 'login', '192.168.1.3', 3, 0),
(384, 'El usuario se logueo al sistema', '2015-09-21 18:16:57', 'login', '192.168.1.3', 19, 0),
(385, 'El usuario salio del sistema', '2015-09-21 18:17:29', 'login', '192.168.1.3', 19, 0),
(386, 'El usuario se logueo al sistema', '2015-09-21 18:18:24', 'login', '192.168.1.3', 19, 0),
(387, 'El usuario salio del sistema', '2015-09-21 18:19:20', 'login', '192.168.1.3', 19, 0),
(388, 'El usuario se logueo al sistema', '2015-09-21 18:35:10', 'login', '192.168.1.3', 19, 0),
(389, 'El usuario ingreso el cliente PRISCILA codigo: 116150612', '2015-09-21 18:37:25', 'registro', '192.168.1.3', 19, 0),
(390, 'El usuario ingreso el cliente DAHIANA codigo: 113150590', '2015-09-21 18:41:29', 'registro', '192.168.1.3', 19, 0),
(392, 'El usuario se logueo al sistema', '2015-09-21 20:41:53', 'login', '192.168.1.3', 18, 0),
(393, 'El usuario cambio su perfil: 18', '2015-09-21 20:42:50', 'edicion', '192.168.1.3', 18, 0),
(394, 'El usuario salio del sistema', '2015-09-21 20:42:54', 'login', '192.168.1.3', 18, 0),
(395, 'El usuario se logueo al sistema', '2015-09-21 20:43:05', 'login', '192.168.1.3', 18, 0),
(396, 'El usuario ingreso el cliente NIDIA DEL CARMEN codigo: 401080744', '2015-09-21 20:45:06', 'registro', '192.168.1.3', 18, 0),
(397, 'El usuario salio del sistema', '2015-09-21 20:45:45', 'login', '192.168.1.3', 18, 0),
(398, 'El usuario se logueo al sistema', '2015-09-21 20:50:10', 'login', '192.168.1.3', 13, 0),
(399, 'El usuario ingreso el cliente ALMA NINOSKA. codigo: 155812891522', '2015-09-21 20:53:12', 'registro', '192.168.1.3', 13, 0),
(400, 'El usuario se logueo al sistema', '2015-09-21 21:00:17', 'login', '192.168.1.3', 20, 0),
(401, 'El usuario se logueo al sistema', '2015-09-21 21:07:09', 'login', '192.168.1.3', 18, 0),
(402, 'El usuario salio del sistema', '2015-09-21 21:07:42', 'login', '192.168.1.3', 18, 0),
(403, 'El usuario se logueo al sistema', '2015-09-21 21:08:29', 'login', '192.168.1.3', 18, 0),
(404, 'El usuario salio del sistema', '2015-09-21 21:10:51', 'login', '192.168.1.3', 18, 0),
(405, 'El usuario salio del sistema', '2015-09-21 22:11:10', 'login', '192.168.1.3', 20, 0),
(406, 'El usuario se logueo al sistema', '2015-09-21 22:12:14', 'login', '192.168.1.3', 20, 0),
(407, 'El usuario ingreso el cliente ILIANA codigo: 106550218', '2015-09-21 22:15:46', 'registro', '192.168.1.3', 20, 0),
(408, 'El usuario salio del sistema', '2015-09-21 22:15:57', 'login', '192.168.1.3', 20, 0),
(410, 'El usuario se logueo al sistema', '2015-09-21 23:15:20', 'login', '192.168.1.3', 13, 0),
(411, 'El usuario ingreso el cliente MARIA  YOLANDA codigo: 105960601', '2015-09-21 23:18:47', 'registro', '192.168.1.3', 13, 0),
(412, 'El usuario se logueo al sistema', '2015-09-21 23:25:15', 'login', '192.168.1.3', 14, 0),
(413, 'El usuario salio del sistema', '2015-09-21 23:26:19', 'login', '192.168.1.3', 14, 0),
(414, 'El usuario se logueo al sistema', '2015-09-21 23:35:45', 'login', '192.168.1.3', 14, 0),
(415, 'El usuario ingreso el cliente RODRIGO ANTONIO codigo: 401230530', '2015-09-21 23:40:17', 'registro', '192.168.1.3', 14, 0),
(416, 'El usuario salio del sistema', '2015-09-21 23:41:18', 'login', '192.168.1.3', 14, 0),
(417, 'El usuario se logueo al sistema', '2015-09-22 00:05:33', 'login', '192.168.1.3', 3, 0),
(418, 'El usuario se logueo al sistema', '2015-09-22 15:09:20', 'login', '192.168.1.3', 18, 0),
(419, 'El usuario ingreso el cliente JUANA codigo: 501850567', '2015-09-22 15:13:59', 'registro', '192.168.1.3', 18, 0),
(420, 'El usuario salio del sistema', '2015-09-22 15:14:31', 'login', '192.168.1.3', 18, 0),
(421, 'El usuario se logueo al sistema', '2015-09-22 15:43:48', 'login', '192.168.1.3', 17, 0),
(422, 'El usuario se logueo al sistema', '2015-09-22 15:48:29', 'login', '192.168.1.3', 17, 0),
(423, 'El usuario ingreso el cliente KATHERINE MARIA  codigo: 111140108', '2015-09-22 15:52:03', 'registro', '192.168.1.3', 17, 0),
(424, 'El usuario ingreso el cliente GRETTEL  codigo: 106220018', '2015-09-22 15:53:48', 'registro', '192.168.1.3', 17, 0),
(425, 'El usuario ingreso el cliente LAURA codigo: 109250134', '2015-09-22 15:55:19', 'registro', '192.168.1.3', 17, 0),
(426, 'El usuario ingreso el cliente ANGIE codigo: 112060834', '2015-09-22 15:56:52', 'registro', '192.168.1.3', 17, 0),
(427, 'El usuario ingreso el cliente CARMINIA MARIA codigo: 155809733503', '2015-09-22 16:01:44', 'registro', '192.168.1.3', 17, 0),
(428, 'El usuario se logueo al sistema', '2015-09-22 16:02:19', 'login', '192.168.1.3', 19, 0),
(429, 'El usuario ingreso el cliente MAYRA  codigo: 104990597', '2015-09-22 16:03:59', 'registro', '192.168.1.3', 19, 0),
(430, 'El usuario ingreso el cliente ROXANA  codigo: 104720595', '2015-09-22 16:06:17', 'registro', '192.168.1.3', 19, 0),
(431, 'El usuario ingreso el cliente MAYRA LIDIETH  codigo: 203690284', '2015-09-22 16:08:38', 'registro', '192.168.1.3', 19, 0),
(432, 'El usuario salio del sistema', '2015-09-22 16:09:10', 'login', '192.168.1.3', 19, 0),
(433, 'El usuario salio del sistema', '2015-09-22 16:09:19', 'login', '192.168.1.3', 17, 0),
(434, 'El usuario se logueo al sistema', '2015-09-22 16:42:12', 'login', '192.168.1.3', 19, 0),
(435, 'El usuario salio del sistema', '2015-09-22 16:42:26', 'login', '192.168.1.3', 19, 0),
(436, 'El usuario se logueo al sistema', '2015-09-22 18:46:32', 'login', '192.168.1.3', 13, 0),
(437, 'El usuario ingreso el cliente AIDA codigo: 602420246', '2015-09-22 18:49:40', 'registro', '192.168.1.3', 13, 0),
(438, 'El usuario se logueo al sistema', '2015-09-22 19:04:23', 'login', '192.168.1.3', 20, 0),
(439, 'El usuario salio del sistema', '2015-09-22 20:02:54', 'login', '192.168.1.3', 20, 0),
(440, 'El usuario se logueo al sistema', '2015-09-22 20:19:46', 'login', '192.168.1.3', 19, 0),
(441, 'El usuario salio del sistema', '2015-09-22 20:21:54', 'login', '192.168.1.3', 19, 0),
(442, 'El usuario se logueo al sistema', '2015-09-22 20:34:47', 'login', '192.168.1.3', 11, 0),
(443, 'El usuario ingreso el cliente MAGALI  codigo: 205230211', '2015-09-22 20:35:57', 'registro', '192.168.1.3', 11, 0),
(445, 'El usuario se logueo al sistema', '2015-09-22 21:39:51', 'login', '192.168.1.3', 13, 0),
(446, 'El usuario ingreso el cliente ERICK  RODRIGO. codigo: 113740791', '2015-09-22 21:48:23', 'registro', '192.168.1.3', 13, 0),
(448, 'El usuario se logueo al sistema', '2015-09-22 23:02:05', 'login', '192.168.1.3', 18, 0),
(449, 'El usuario ingreso el cliente KARLA BORBON codigo: 109940234', '2015-09-22 23:04:50', 'registro', '192.168.1.3', 18, 0),
(450, 'El usuario salio del sistema', '2015-09-22 23:06:06', 'login', '192.168.1.3', 18, 0),
(451, 'El usuario se logueo al sistema', '2015-09-23 22:51:51', 'login', '192.168.1.3', 18, 0),
(452, 'El usuario salio del sistema', '2015-09-23 22:54:01', 'login', '192.168.1.3', 18, 0),
(453, 'El usuario se logueo al sistema', '2015-09-23 23:22:45', 'login', '192.168.1.3', 13, 0),
(454, 'El usuario ingreso el cliente YESENIA   codigo: 111180319', '2015-09-23 23:26:35', 'registro', '192.168.1.3', 13, 0),
(455, 'El usuario se logueo al sistema', '2015-09-24 14:58:01', 'login', '192.168.1.3', 18, 0),
(456, 'El usuario ingreso el cliente ANA LORENA codigo: 104200993', '2015-09-24 15:00:09', 'registro', '192.168.1.3', 18, 0),
(457, 'El usuario ingreso el cliente WENDY codigo: 114640332', '2015-09-24 15:09:13', 'registro', '192.168.1.3', 18, 0),
(458, 'El usuario ingreso el cliente ANA codigo: 107200749', '2015-09-24 15:11:40', 'registro', '192.168.1.3', 18, 0),
(459, 'El usuario ingreso el cliente JESSICA codigo: 110690328', '2015-09-24 15:18:00', 'registro', '192.168.1.3', 18, 0),
(460, 'El usuario salio del sistema', '2015-09-24 15:18:12', 'login', '192.168.1.3', 18, 0),
(461, 'El usuario se logueo al sistema', '2015-09-24 15:55:45', 'login', '192.168.1.3', 18, 0),
(462, 'El usuario salio del sistema', '2015-09-24 15:55:58', 'login', '192.168.1.3', 18, 0),
(463, 'El usuario se logueo al sistema', '2015-09-24 17:03:15', 'login', '192.168.1.3', 3, 0),
(464, 'El usuario salio del sistema', '2015-09-24 17:03:37', 'login', '192.168.1.3', 3, 0),
(465, 'El usuario se logueo al sistema', '2015-09-24 17:03:45', 'login', '192.168.1.3', 15, 0),
(466, 'El usuario cambio su perfil: 15', '2015-09-24 17:05:14', 'edicion', '192.168.1.3', 15, 0),
(467, 'El usuario ingreso el cliente Angela Maria codigo: 701130900', '2015-09-24 17:57:33', 'registro', '192.168.1.3', 15, 0),
(468, 'El usuario ingreso el cliente MARIA GABRIELA codigo: 701240355', '2015-09-24 18:04:40', 'registro', '192.168.1.3', 15, 0),
(469, 'El usuario editó el cliente codigo: ', '2015-09-24 18:06:23', 'edicion', '192.168.1.3', 15, 0),
(470, 'El usuario ingreso el cliente CANDY PRISCILA codigo: 702190813', '2015-09-24 20:28:36', 'registro', '192.168.1.3', 15, 0),
(471, 'El usuario ingreso el cliente VIVIAN SARAY codigo: 701350531', '2015-09-24 20:31:46', 'registro', '192.168.1.3', 15, 0),
(472, 'El usuario ingreso el cliente CARLOS ALBERTO codigo: 600790976', '2015-09-24 20:38:06', 'registro', '192.168.1.3', 15, 0),
(473, 'El usuario salio del sistema', '2015-09-24 21:20:49', 'login', '192.168.1.3', 15, 0),
(474, 'El usuario se logueo al sistema', '2015-09-24 21:50:53', 'login', '192.168.1.3', 14, 0),
(475, 'El usuario editó el cliente codigo: ', '2015-09-24 21:53:22', 'edicion', '192.168.1.3', 14, 0),
(476, 'El usuario salio del sistema', '2015-09-24 21:54:03', 'login', '192.168.1.3', 14, 0),
(477, 'El usuario se logueo al sistema', '2015-09-24 21:54:41', 'login', '192.168.1.3', 15, 0),
(478, 'El usuario ingreso el cliente GLADYS IVETH codigo: 602020721', '2015-09-24 21:58:43', 'registro', '192.168.1.3', 15, 0),
(479, 'El usuario ingreso el cliente LETICIA codigo: 111890872', '2015-09-24 22:02:57', 'registro', '192.168.1.3', 15, 0),
(480, 'El usuario ingreso el cliente BETTINIA codigo: 107510547', '2015-09-24 22:07:25', 'registro', '192.168.1.3', 15, 0),
(481, 'El usuario se logueo al sistema', '2015-09-24 22:23:11', 'login', '192.168.1.3', 11, 0),
(482, 'El usuario ingreso el cliente MIRIAM codigo: 502180823', '2015-09-24 22:25:58', 'registro', '192.168.1.3', 15, 0),
(483, 'El usuario salio del sistema', '2015-09-24 23:20:28', 'login', '192.168.1.3', 15, 0),
(484, 'El usuario se logueo al sistema', '2015-09-25 15:45:57', 'login', '192.168.1.3', 20, 0),
(485, 'El usuario ingreso el cliente KARLA VANESSA codigo: 114220871', '2015-09-25 15:48:42', 'registro', '192.168.1.3', 20, 0),
(486, 'El usuario salio del sistema', '2015-09-25 15:48:53', 'login', '192.168.1.3', 20, 0),
(487, 'El usuario se logueo al sistema', '2015-09-25 17:25:49', 'login', '192.168.1.3', 20, 0),
(488, 'El usuario ingreso el cliente PRISCILLA  codigo: 108400588', '2015-09-25 17:27:54', 'registro', '192.168.1.3', 20, 0),
(489, 'El usuario editó el cliente codigo: ', '2015-09-25 17:32:43', 'edicion', '192.168.1.3', 20, 0),
(490, 'El usuario salio del sistema', '2015-09-25 17:32:54', 'login', '192.168.1.3', 20, 0),
(491, 'El usuario se logueo al sistema', '2015-09-25 18:22:39', 'login', '192.168.1.3', 18, 0),
(492, 'El usuario ingreso el cliente JOHANNA codigo: 110790390', '2015-09-25 18:24:36', 'registro', '192.168.1.3', 18, 0),
(493, 'El usuario ingreso el cliente SUSAN codigo: 111090702', '2015-09-25 18:26:50', 'registro', '192.168.1.3', 18, 0),
(494, 'El usuario ingreso el cliente JESSICA codigo: 111750771', '2015-09-25 18:29:17', 'registro', '192.168.1.3', 18, 0),
(495, 'El usuario salio del sistema', '2015-09-25 18:29:24', 'login', '192.168.1.3', 18, 0),
(496, 'El usuario se logueo al sistema', '2015-09-25 20:26:31', 'login', '192.168.1.3', 14, 0),
(497, 'El usuario ingreso el articulo 1 cantidad: 1', '2015-09-25 20:33:00', 'registro', '192.168.1.3', 14, 0),
(498, 'El usuario 14 envio a caja la factura consecutivo:1', '2015-09-25 20:38:26', 'factura_envio', '192.168.1.3', 14, 0),
(499, 'El usuario se logueo al sistema', '2015-09-25 20:39:10', 'login', '192.168.1.3', 3, 0);
INSERT INTO `tb_12_transacciones` (`Trans_Codigo`, `Trans_Descripcion`, `Trans_Fecha_Hora`, `Trans_Tipo`, `Trans_IP`, `TB_01_Usuario_Usuario_Codigo`, `TB_01_Usuario_TB_02_Sucursal_Codigo`) VALUES
(500, 'El usuario cobro la factura consecutivo: 1', '2015-09-25 20:39:43', 'cobro', '192.168.1.3', 3, 0),
(501, 'El usuario se logueo al sistema', '2015-09-25 20:40:31', 'login', '192.168.1.3', 16, 0),
(502, 'El usuario se logueo al sistema', '2015-09-25 20:40:42', 'login', '192.168.1.3', 3, 0),
(503, 'El usuario editó el usuario codigo: 12', '2015-09-25 20:41:21', 'edicion', '192.168.1.3', 3, 0),
(504, 'El usuario editó el usuario codigo: 12', '2015-09-25 20:41:55', 'edicion', '192.168.1.3', 3, 0),
(505, 'El usuario salio del sistema', '2015-09-25 20:42:09', 'login', '192.168.1.3', 3, 0),
(506, 'El usuario se logueo al sistema', '2015-09-25 20:43:55', 'login', '192.168.1.3', 12, 0),
(507, 'El usuario salio del sistema', '2015-09-25 20:44:02', 'login', '192.168.1.3', 12, 0),
(508, 'El usuario se logueo al sistema', '2015-09-25 20:44:27', 'login', '192.168.1.3', 12, 0),
(509, 'El usuario ingreso el cliente ARGIERE VANESSA  codigo: 402240098', '2015-09-25 20:48:06', 'registro', '192.168.1.3', 16, 0),
(510, 'El usuario se logueo al sistema', '2015-09-25 20:51:47', 'login', '192.168.1.3', 14, 0),
(511, 'El usuario editó el cliente codigo: ', '2015-09-25 20:51:50', 'edicion', '192.168.1.3', 16, 0),
(512, 'El usuario editó el cliente codigo: ', '2015-09-25 20:52:44', 'edicion', '192.168.1.3', 16, 0),
(513, 'El usuario realizo la nota credito: 1', '2015-09-25 20:53:46', 'nota', '192.168.1.3', 14, 0),
(514, 'El usuario ingreso el cliente YADIRA LORENA codigo: 700830448', '2015-09-25 20:56:53', 'registro', '192.168.1.3', 12, 0),
(515, 'El usuario ingreso el cliente LILLIANA YADIRA  codigo: 503420777', '2015-09-25 20:59:19', 'registro', '192.168.1.3', 16, 0),
(516, 'El usuario editó el artículo código: 1', '2015-09-25 20:59:35', 'edicion', '192.168.1.3', 14, 0),
(517, 'El usuario salio del sistema', '2015-09-25 20:59:56', 'login', '192.168.1.3', 14, 0),
(518, 'El usuario se logueo al sistema', '2015-09-25 21:00:09', 'login', '192.168.1.3', 3, 0),
(519, 'El usuario ingreso el cliente   KAREN ANDREA codigo: 114790333', '2015-09-25 21:00:45', 'registro', '192.168.1.3', 12, 0),
(520, 'El usuario 3 envio a caja la factura consecutivo:2', '2015-09-25 21:01:13', 'factura_envio', '192.168.1.3', 3, 0),
(521, 'El usuario cobro la factura consecutivo: 2', '2015-09-25 21:01:38', 'cobro', '192.168.1.3', 3, 0),
(522, 'El usuario salio del sistema', '2015-09-25 21:01:46', 'login', '192.168.1.3', 3, 0),
(523, 'El usuario se logueo al sistema', '2015-09-25 21:01:56', 'login', '192.168.1.3', 5, 2),
(524, 'El usuario salio del sistema', '2015-09-25 21:02:06', 'login', '192.168.1.3', 5, 2),
(525, 'El usuario se logueo al sistema', '2015-09-25 21:02:13', 'login', '192.168.1.3', 3, 0),
(526, 'El usuario agrego la factura #2 como compras de la sucursal 2', '2015-09-25 21:05:24', 'compras', '192.168.1.3', 3, 0),
(527, 'El usuario ingreso el cliente CARMEN LIDIA GERARDA codigo: 203940310', '2015-09-25 21:05:26', 'registro', '192.168.1.3', 12, 0),
(528, 'El usuario salio del sistema', '2015-09-25 21:06:13', 'login', '192.168.1.3', 3, 0),
(529, 'El usuario se logueo al sistema', '2015-09-25 21:06:23', 'login', '192.168.1.3', 5, 2),
(530, 'El usuario salio del sistema', '2015-09-25 21:07:14', 'login', '192.168.1.3', 5, 2),
(531, 'El usuario se logueo al sistema', '2015-09-25 21:33:07', 'login', '192.168.1.3', 20, 0),
(532, 'El usuario salio del sistema', '2015-09-25 21:34:11', 'login', '192.168.1.3', 20, 0),
(533, 'El usuario ingreso el cliente   ILIANA MARITZA DE LA TRINIDAD codigo: 105460022', '2015-09-25 21:41:28', 'registro', '192.168.1.3', 12, 0),
(534, 'El usuario ingreso el cliente MAYRA ALEJANDRA codigo: 304210288', '2015-09-25 21:43:23', 'registro', '192.168.1.3', 16, 0),
(535, 'El usuario se logueo al sistema', '2015-09-25 21:55:32', 'login', '192.168.1.3', 18, 0),
(536, 'El usuario ingreso el cliente TAISIULING codigo: 701130509', '2015-09-25 21:57:26', 'registro', '192.168.1.3', 16, 0),
(537, 'El usuario ingreso el cliente NORMA codigo: 303230930', '2015-09-25 21:59:11', 'registro', '192.168.1.3', 18, 0),
(538, 'El usuario salio del sistema', '2015-09-25 21:59:44', 'login', '192.168.1.3', 18, 0),
(539, 'El usuario se logueo al sistema', '2015-09-25 22:22:47', 'login', '192.168.1.3', 20, 0),
(540, 'El usuario ingreso el cliente JOYCE codigo: 113950635', '2015-09-25 22:27:32', 'registro', '192.168.1.3', 20, 0),
(541, 'El usuario editó el cliente codigo: ', '2015-09-25 22:30:19', 'edicion', '192.168.1.3', 20, 0),
(542, 'El usuario salio del sistema', '2015-09-25 22:30:30', 'login', '192.168.1.3', 20, 0),
(543, 'El usuario se logueo al sistema', '2015-09-25 23:24:53', 'login', '192.168.1.3', 18, 0),
(544, 'El usuario salio del sistema', '2015-09-25 23:34:19', 'login', '192.168.1.3', 18, 0),
(545, 'El usuario se logueo al sistema', '2015-09-25 23:56:18', 'login', '192.168.1.3', 18, 0),
(546, 'El usuario salio del sistema', '2015-09-25 23:57:13', 'login', '192.168.1.3', 18, 0),
(547, 'El usuario se logueo al sistema', '2015-09-26 15:06:48', 'login', '192.168.1.3', 12, 0),
(548, 'El usuario se logueo al sistema', '2015-09-26 15:29:38', 'login', '192.168.1.3', 19, 0),
(549, 'El usuario ingreso el cliente YOLANDA  codigo: 106560729', '2015-09-26 15:32:22', 'registro', '192.168.1.3', 19, 0),
(550, 'El usuario ingreso el cliente IRMA  codigo: 203310584', '2015-09-26 15:34:19', 'registro', '192.168.1.3', 19, 0),
(551, 'El usuario ingreso el cliente PAULINA  codigo: 601180237', '2015-09-26 15:36:38', 'registro', '192.168.1.3', 19, 0),
(552, 'El usuario salio del sistema', '2015-09-26 15:37:01', 'login', '192.168.1.3', 19, 0),
(553, 'El usuario se logueo al sistema', '2015-09-26 18:57:34', 'login', '192.168.1.3', 13, 0),
(554, 'El usuario ingreso el cliente ABIGAIL codigo: 155819666120', '2015-09-26 19:01:07', 'registro', '192.168.1.3', 13, 0),
(555, 'El usuario ingreso el cliente OLGA MARTA codigo: 106540175', '2015-09-26 19:05:09', 'registro', '192.168.1.3', 13, 0),
(556, 'El usuario se logueo al sistema', '2015-09-26 20:47:14', 'login', '192.168.1.3', 20, 0),
(557, 'El usuario salio del sistema', '2015-09-26 20:48:10', 'login', '192.168.1.3', 20, 0),
(558, 'El usuario se logueo al sistema', '2015-09-26 20:54:37', 'login', '192.168.1.3', 19, 0),
(559, 'El usuario salio del sistema', '2015-09-26 20:55:10', 'login', '192.168.1.3', 19, 0),
(560, 'El usuario se logueo al sistema', '2015-09-26 20:55:40', 'login', '192.168.1.3', 19, 0),
(561, 'El usuario salio del sistema', '2015-09-26 20:56:27', 'login', '192.168.1.3', 19, 0),
(562, 'El usuario se logueo al sistema', '2015-09-26 21:07:14', 'login', '192.168.1.3', 19, 0),
(563, 'El usuario ingreso el cliente MARIA DE LOS ANGELES  codigo: 109280965', '2015-09-26 21:11:29', 'registro', '192.168.1.3', 19, 0),
(564, 'El usuario salio del sistema', '2015-09-26 21:11:39', 'login', '192.168.1.3', 19, 0),
(565, 'El usuario se logueo al sistema', '2015-09-26 21:20:55', 'login', '192.168.1.3', 16, 0),
(566, 'El usuario ingreso el cliente CINTIA GISELA codigo: 701270344', '2015-09-26 21:35:55', 'registro', '192.168.1.3', 16, 0),
(567, 'El usuario se logueo al sistema', '2015-09-26 22:41:40', 'login', '192.168.1.3', 20, 0),
(568, 'El usuario ingreso el cliente MARCIA  codigo: 110480131', '2015-09-26 22:43:01', 'registro', '192.168.1.3', 20, 0),
(569, 'El usuario ingreso el cliente LOURDES DEL SOCORRO codigo: 155811412116', '2015-09-26 22:45:04', 'registro', '192.168.1.3', 20, 0),
(570, 'El usuario salio del sistema', '2015-09-26 22:45:36', 'login', '192.168.1.3', 20, 0),
(571, 'El usuario se logueo al sistema', '2015-09-28 15:55:25', 'login', '192.168.1.3', 20, 0),
(572, 'El usuario salio del sistema', '2015-09-28 15:56:10', 'login', '192.168.1.3', 20, 0),
(573, 'El usuario se logueo al sistema', '2015-09-28 22:11:10', 'login', '192.168.1.3', 18, 0),
(574, 'El usuario ingreso el cliente PAULINA codigo: 155809725004', '2015-09-28 22:15:01', 'registro', '192.168.1.3', 18, 0),
(575, 'El usuario ingreso el cliente MARVIN codigo: 900690725', '2015-09-28 22:16:49', 'registro', '192.168.1.3', 18, 0),
(576, 'El usuario salio del sistema', '2015-09-28 22:17:08', 'login', '192.168.1.3', 18, 0),
(577, 'El usuario se logueo al sistema', '2015-09-28 22:33:05', 'login', '192.168.1.3', 20, 0),
(578, 'El usuario ingreso el cliente YERLI codigo: 603890450', '2015-09-28 22:35:13', 'registro', '192.168.1.3', 20, 0),
(579, 'El usuario se logueo al sistema', '2015-09-28 22:36:36', 'login', '192.168.1.3', 18, 0),
(580, 'El usuario ingreso el cliente ALEJANDRA codigo: 113480057', '2015-09-28 22:38:33', 'registro', '192.168.1.3', 18, 0),
(581, 'El usuario ingreso el cliente YAHAIRA codigo: 602840307', '2015-09-28 22:40:56', 'registro', '192.168.1.3', 18, 0),
(582, 'El usuario salio del sistema', '2015-09-28 22:41:50', 'login', '192.168.1.3', 18, 0),
(583, 'El usuario salio del sistema', '2015-09-28 23:10:39', 'login', '192.168.1.3', 20, 0),
(584, 'El usuario se logueo al sistema', '2015-09-29 17:46:15', 'login', '192.168.1.3', 18, 0),
(585, 'El usuario ingreso el cliente RELFA codigo: 111680310', '2015-09-29 17:48:50', 'registro', '192.168.1.3', 18, 0),
(586, 'El usuario salio del sistema', '2015-09-29 17:49:44', 'login', '192.168.1.3', 18, 0),
(587, 'El usuario se logueo al sistema', '2015-09-29 19:34:24', 'login', '192.168.1.3', 18, 0),
(588, 'El usuario salio del sistema', '2015-09-29 19:35:11', 'login', '192.168.1.3', 18, 0),
(589, 'El usuario se logueo al sistema', '2015-09-29 19:58:13', 'login', '192.168.1.3', 18, 0),
(590, 'El usuario salio del sistema', '2015-09-29 19:58:24', 'login', '192.168.1.3', 18, 0),
(591, 'El usuario se logueo al sistema', '2015-09-29 19:58:58', 'login', '192.168.1.3', 18, 0),
(592, 'El usuario salio del sistema', '2015-09-29 19:59:53', 'login', '192.168.1.3', 18, 0),
(593, 'El usuario se logueo al sistema', '2015-09-29 21:02:17', 'login', '192.168.1.3', 3, 0),
(594, 'El usuario ingreso el cliente MARIAN codigo: 701900411', '2015-09-29 21:04:09', 'registro', '192.168.1.3', 3, 0),
(595, 'El usuario salio del sistema', '2015-09-29 21:05:58', 'login', '192.168.1.3', 3, 0),
(596, 'El usuario se logueo al sistema', '2015-09-29 21:06:08', 'login', '192.168.1.3', 3, 0),
(597, 'El usuario editó el usuario codigo: 11', '2015-09-29 21:06:50', 'edicion', '192.168.1.3', 3, 0),
(598, 'El usuario editó el usuario codigo: 13', '2015-09-29 21:07:11', 'edicion', '192.168.1.3', 3, 0),
(599, 'El usuario editó el usuario codigo: 14', '2015-09-29 21:07:37', 'edicion', '192.168.1.3', 3, 0),
(600, 'El usuario editó el usuario codigo: 15', '2015-09-29 21:07:55', 'edicion', '192.168.1.3', 3, 0),
(601, 'El usuario editó el usuario codigo: 16', '2015-09-29 21:08:17', 'edicion', '192.168.1.3', 3, 0),
(602, 'El usuario editó el usuario codigo: 17', '2015-09-29 21:08:37', 'edicion', '192.168.1.3', 3, 0),
(603, 'El usuario editó el usuario codigo: 19', '2015-09-29 21:09:00', 'edicion', '192.168.1.3', 3, 0),
(604, 'El usuario salio del sistema', '2015-09-29 21:09:11', 'login', '192.168.1.3', 3, 0),
(605, 'El usuario se logueo al sistema', '2015-09-29 21:47:41', 'login', '192.168.1.3', 13, 0),
(606, 'El usuario ingreso el cliente DELIA EMILIA. codigo: 155800948623', '2015-09-29 21:52:39', 'registro', '192.168.1.3', 13, 0),
(607, 'El usuario ingreso el cliente GABRIELA . codigo: 111490109', '2015-09-29 21:58:54', 'registro', '192.168.1.3', 13, 0),
(608, 'El usuario se logueo al sistema', '2015-09-29 22:23:46', 'login', '192.168.1.3', 20, 0),
(609, 'El usuario se logueo al sistema', '2015-09-29 22:33:06', 'login', '192.168.1.3', 20, 0),
(610, 'El usuario se logueo al sistema', '2015-09-30 16:25:22', 'login', '192.168.1.3', 20, 0),
(611, 'El usuario ingreso el cliente JOISY  codigo: 702040635', '2015-09-30 18:12:07', 'registro', '192.168.1.3', 20, 0),
(612, 'El usuario ingreso el cliente MARIELA  codigo: 112180031', '2015-09-30 18:14:03', 'registro', '192.168.1.3', 20, 0),
(613, 'El usuario salio del sistema', '2015-09-30 20:06:04', 'login', '192.168.1.3', 20, 0),
(614, 'El usuario se logueo al sistema', '2015-09-30 22:01:06', 'login', '192.168.1.3', 18, 0),
(615, 'El usuario salio del sistema', '2015-09-30 22:01:11', 'login', '192.168.1.3', 18, 0),
(616, 'El usuario se logueo al sistema', '2015-09-30 22:01:57', 'login', '192.168.1.3', 18, 0),
(617, 'El usuario ingreso el cliente MARGOTH codigo: 155812468422', '2015-09-30 22:04:39', 'registro', '192.168.1.3', 18, 0),
(618, 'El usuario salio del sistema', '2015-09-30 22:05:11', 'login', '192.168.1.3', 18, 0),
(619, 'El usuario se logueo al sistema', '2015-09-30 22:59:00', 'login', '192.168.1.3', 13, 0),
(620, 'El usuario ingreso el cliente NIDIA codigo: 105300051', '2015-09-30 23:05:27', 'registro', '192.168.1.3', 13, 0),
(621, 'El usuario ingreso el cliente JOHANNA codigo: 303900212', '2015-09-30 23:09:16', 'registro', '192.168.1.3', 13, 0),
(622, 'El usuario se logueo al sistema', '2015-09-30 23:22:56', 'login', '192.168.1.3', 18, 0),
(623, 'El usuario ingreso el cliente TERESA codigo: 111670353', '2015-09-30 23:24:38', 'registro', '192.168.1.3', 18, 0),
(624, 'El usuario salio del sistema', '2015-09-30 23:24:53', 'login', '192.168.1.3', 18, 0),
(625, 'El usuario se logueo al sistema', '2015-09-30 23:59:47', 'login', '192.168.1.3', 18, 0),
(626, 'El usuario salio del sistema', '2015-10-01 00:00:32', 'login', '192.168.1.3', 18, 0),
(627, 'El usuario se logueo al sistema', '2015-10-02 15:05:24', 'login', '192.168.1.3', 13, 0),
(628, 'El usuario ingreso el cliente MARIA GISELLE. codigo: 108170124', '2015-10-02 15:11:40', 'registro', '192.168.1.3', 13, 0),
(630, 'El usuario se logueo al sistema', '2015-10-02 18:30:39', 'login', '192.168.1.3', 3, 0),
(631, 'El usuario salio del sistema', '2015-10-02 18:31:24', 'login', '192.168.1.3', 3, 0),
(632, 'El usuario se logueo al sistema', '2015-10-02 22:36:51', 'login', '192.168.1.3', 13, 0),
(633, 'El usuario ingreso el cliente LEIDY codigo: 104790441', '2015-10-02 22:40:56', 'registro', '192.168.1.3', 13, 0),
(634, 'El usuario ingreso el cliente LILLIAM codigo: 202570102', '2015-10-02 23:56:45', 'registro', '192.168.1.3', 13, 0),
(635, 'El usuario ingreso el cliente DAMARIS codigo: 700481016', '2015-10-02 23:59:22', 'registro', '192.168.1.3', 13, 0),
(636, 'El usuario se logueo al sistema', '2015-10-03 21:51:44', 'login', '192.168.1.3', 20, 0),
(637, 'El usuario se logueo al sistema', '2015-10-03 21:53:49', 'login', '192.168.1.3', 18, 0),
(638, 'El usuario ingreso el cliente ANA codigo: 203330251', '2015-10-03 21:57:57', 'registro', '192.168.1.3', 18, 0),
(639, 'El usuario ingreso el cliente DINIA codigo: 112330074', '2015-10-03 22:03:48', 'registro', '192.168.1.3', 20, 0),
(640, 'El usuario ingreso el cliente ELIZABETH codigo: 105440360', '2015-10-03 22:05:56', 'registro', '192.168.1.3', 18, 0),
(641, 'El usuario salio del sistema', '2015-10-03 22:12:11', 'login', '192.168.1.3', 20, 0),
(642, 'El usuario se logueo al sistema', '2015-10-05 15:47:25', 'login', '192.168.1.3', 14, 0),
(643, 'El usuario ingreso el cliente LAURA codigo: 700970173', '2015-10-05 15:53:29', 'registro', '192.168.1.3', 14, 0),
(644, 'El usuario salio del sistema', '2015-10-05 15:53:36', 'login', '192.168.1.3', 14, 0),
(645, 'El usuario se logueo al sistema', '2015-10-05 17:47:35', 'login', '192.168.1.3', 18, 0),
(646, 'El usuario salio del sistema', '2015-10-05 17:54:31', 'login', '192.168.1.3', 18, 0),
(647, 'El usuario se logueo al sistema', '2015-10-05 18:56:11', 'login', '192.168.1.3', 18, 0),
(648, 'El usuario se logueo al sistema', '2015-10-05 18:56:11', 'login', '192.168.1.3', 18, 0),
(649, 'El usuario ingreso el cliente ELIZABETH codigo: 104320342', '2015-10-05 18:58:38', 'registro', '192.168.1.3', 18, 0),
(650, 'El usuario ingreso el cliente LESLIE codigo: 155816000800', '2015-10-05 19:01:48', 'registro', '192.168.1.3', 18, 0),
(651, 'El usuario salio del sistema', '2015-10-05 19:03:15', 'login', '192.168.1.3', 18, 0),
(652, 'El usuario se logueo al sistema', '2015-10-05 22:42:41', 'login', '192.168.1.3', 13, 0),
(653, 'El usuario ingreso el cliente MARIA  codigo: 102100774', '2015-10-05 22:49:10', 'registro', '192.168.1.3', 13, 0),
(654, 'El usuario ingreso el cliente SANDRA codigo: 112920952', '2015-10-05 22:54:39', 'registro', '192.168.1.3', 13, 0),
(655, 'El usuario ingreso el cliente DANIELA codigo: 207530411', '2015-10-05 22:57:56', 'registro', '192.168.1.3', 13, 0),
(656, 'El usuario se logueo al sistema', '2015-10-05 23:21:24', 'login', '192.168.1.3', 20, 0),
(657, 'El usuario ingreso el cliente CLAUDIA codigo: 800500042', '2015-10-05 23:25:06', 'registro', '192.168.1.3', 20, 0),
(658, 'El usuario salio del sistema', '2015-10-05 23:25:30', 'login', '192.168.1.3', 20, 0),
(659, 'El usuario se logueo al sistema', '2015-10-05 23:30:17', 'login', '192.168.1.3', 20, 0),
(660, 'El usuario salio del sistema', '2015-10-05 23:31:05', 'login', '192.168.1.3', 20, 0),
(661, 'El usuario ingreso el cliente SANDRA  codigo: 104470461', '2015-10-05 23:41:26', 'registro', '192.168.1.3', 13, 0),
(662, 'El usuario ingreso el cliente MARLENE codigo: 155809633212', '2015-10-05 23:45:44', 'registro', '192.168.1.3', 13, 0),
(663, 'El usuario se logueo al sistema', '2015-10-05 23:48:28', 'login', '192.168.1.3', 14, 0),
(664, 'El usuario ingreso el cliente LUCIANA  codigo: 155819157725', '2015-10-05 23:53:28', 'registro', '192.168.1.3', 14, 0),
(665, 'El usuario ingreso el cliente CIANNY codigo: 700780247', '2015-10-05 23:54:49', 'registro', '192.168.1.3', 14, 0),
(666, 'El usuario se logueo al sistema', '2015-10-06 19:16:28', 'login', '192.168.1.3', 13, 0),
(667, 'El usuario ingreso el cliente YOSELIN codigo: 116770852', '2015-10-06 19:19:13', 'registro', '192.168.1.3', 13, 0),
(668, 'El usuario se logueo al sistema', '2015-10-06 20:10:46', 'login', '192.168.1.3', 13, 0),
(669, 'El usuario ingreso el cliente RUTH MARY. codigo: 111350826', '2015-10-06 20:16:29', 'registro', '192.168.1.3', 13, 0),
(671, 'El usuario se logueo al sistema', '2015-10-06 22:42:42', 'login', '192.168.1.3', 14, 0),
(672, 'El usuario cambio su perfil: 14', '2015-10-06 22:43:01', 'edicion', '192.168.1.3', 14, 0),
(673, 'El usuario ingreso el cliente TERESITA  codigo: 108160063', '2015-10-06 22:50:04', 'registro', '192.168.1.3', 14, 0),
(674, 'El usuario se logueo al sistema', '2015-10-06 23:35:53', 'login', '192.168.1.3', 20, 0),
(675, 'El usuario se logueo al sistema', '2015-10-07 14:44:37', 'login', '192.168.1.3', 13, 0),
(676, 'El usuario ingreso el cliente LAURA codigo: 108550171', '2015-10-07 14:48:27', 'registro', '192.168.1.3', 13, 0),
(677, 'El usuario se logueo al sistema', '2015-10-07 15:39:53', 'login', '192.168.1.3', 20, 0),
(678, 'El usuario ingreso el cliente DIANA  codigo: 113690392', '2015-10-07 15:43:08', 'registro', '192.168.1.3', 20, 0),
(679, 'El usuario salio del sistema', '2015-10-07 15:46:57', 'login', '192.168.1.3', 20, 0),
(680, 'El usuario se logueo al sistema', '2015-10-07 15:56:18', 'login', '192.168.1.3', 20, 0),
(681, 'El usuario salio del sistema', '2015-10-07 15:56:37', 'login', '192.168.1.3', 20, 0),
(682, 'El usuario se logueo al sistema', '2015-10-08 15:47:18', 'login', '192.168.1.3', 20, 0),
(683, 'El usuario salio del sistema', '2015-10-08 15:48:06', 'login', '192.168.1.3', 20, 0),
(684, 'El usuario se logueo al sistema', '2015-10-09 17:49:02', 'login', '192.168.1.3', 18, 0),
(685, 'El usuario ingreso el cliente WENDY codigo: 111350064', '2015-10-09 17:51:16', 'registro', '192.168.1.3', 18, 0),
(686, 'El usuario ingreso el cliente REINA MARGARITA codigo: 504390835', '2015-10-09 17:55:04', 'registro', '192.168.1.3', 18, 0),
(687, 'El usuario salio del sistema', '2015-10-09 17:56:10', 'login', '192.168.1.3', 18, 0),
(688, 'El usuario se logueo al sistema', '2015-10-10 16:35:44', 'login', '192.168.1.3', 13, 0),
(689, 'El usuario ingreso el cliente ROXANA codigo: 106740963', '2015-10-10 18:01:35', 'registro', '192.168.1.3', 13, 0),
(690, 'El usuario se logueo al sistema', '2015-10-10 19:59:56', 'login', '192.168.1.3', 13, 0),
(691, 'El usuario ingreso el cliente HEILYN VANESSA codigo: 401960881', '2015-10-10 20:03:30', 'registro', '192.168.1.3', 13, 0),
(692, 'El usuario se logueo al sistema', '2015-10-10 21:41:07', 'login', '192.168.1.3', 13, 0),
(693, 'El usuario ingreso el cliente TATIANA  codigo: 702360713', '2015-10-10 21:46:39', 'registro', '192.168.1.3', 13, 0),
(694, 'El usuario editó el cliente codigo: ', '2015-10-10 21:51:10', 'edicion', '192.168.1.3', 13, 0),
(695, 'El usuario editó el cliente codigo: ', '2015-10-10 21:51:43', 'edicion', '192.168.1.3', 13, 0),
(696, 'El usuario se logueo al sistema', '2015-10-13 18:52:15', 'login', '192.168.1.3', 13, 0),
(697, 'El usuario se logueo al sistema', '2015-10-16 17:30:21', 'login', '192.168.1.3', 13, 0),
(698, 'El usuario ingreso el cliente DANIELA  codigo: 116150583', '2015-10-16 17:34:18', 'registro', '192.168.1.3', 13, 0),
(699, 'El usuario ingreso el cliente SHIRLEY codigo: 111100360', '2015-10-16 17:43:49', 'registro', '192.168.1.3', 13, 0),
(700, 'El usuario se logueo al sistema', '2015-10-16 22:43:48', 'login', '192.168.1.3', 13, 0),
(701, 'El usuario ingreso el cliente YESENIA codigo: 602150660', '2015-10-17 00:01:55', 'registro', '192.168.1.3', 13, 0),
(702, 'El usuario ingreso el cliente JENNY  MARIA codigo: 107930163', '2015-10-17 00:07:37', 'registro', '192.168.1.3', 13, 0),
(703, 'El usuario se logueo al sistema', '2015-10-19 15:20:27', 'login', '192.168.1.3', 13, 0),
(704, 'El usuario ingreso el cliente MARIA RAQUEL  codigo: 155814140035', '2015-10-19 15:32:51', 'registro', '192.168.1.3', 13, 0),
(705, 'El usuario se logueo al sistema', '2015-10-19 15:48:33', 'login', '192.168.1.3', 3, 0),
(706, 'El usuario activo al usuario código: 20', '2015-10-19 16:09:56', 'edicion', '192.168.1.3', 3, 0),
(707, 'El usuario activo al usuario código: 18', '2015-10-19 16:10:11', 'edicion', '192.168.1.3', 3, 0),
(708, 'El usuario ingreso el usuario BRYAN  codigo: 21', '2015-10-19 17:00:58', 'registro', '192.168.1.3', 3, 0),
(709, 'El usuario salio del sistema', '2015-10-19 17:01:01', 'login', '192.168.1.3', 3, 0),
(710, 'El usuario se logueo al sistema', '2015-10-19 17:01:32', 'login', '192.168.1.3', 21, 0),
(711, 'El usuario cambio su perfil: 21', '2015-10-19 17:02:08', 'edicion', '192.168.1.3', 21, 0),
(712, 'El usuario salio del sistema', '2015-10-19 17:02:11', 'login', '192.168.1.3', 21, 0),
(713, 'El usuario se logueo al sistema', '2015-10-19 17:02:25', 'login', '192.168.1.3', 21, 0),
(714, 'El usuario salio del sistema', '2015-10-19 17:02:34', 'login', '192.168.1.3', 21, 0),
(715, 'El usuario se logueo al sistema', '2015-10-19 17:02:43', 'login', '192.168.1.3', 3, 0),
(716, 'El usuario desactivo a la empresa codigo: 4', '2015-10-19 17:11:45', 'edicion', '192.168.1.3', 3, 0),
(717, 'El usuario salio del sistema', '2015-10-19 17:12:58', 'login', '192.168.1.3', 3, 0),
(718, 'El usuario se logueo al sistema', '2015-10-19 17:13:11', 'login', '192.168.1.3', 10, 7),
(719, 'El usuario salio del sistema', '2015-10-19 17:20:41', 'login', '192.168.1.3', 10, 7),
(720, 'El usuario se logueo al sistema', '2015-10-19 17:23:56', 'login', '192.168.1.3', 10, 7),
(721, 'El usuario salio del sistema', '2015-10-19 17:29:33', 'login', '192.168.1.3', 10, 7),
(722, 'El usuario salio del sistema', '2015-10-19 17:32:51', 'login', '192.168.1.3', 13, 0),
(723, 'El usuario se logueo al sistema', '2015-10-19 17:38:39', 'login', '192.168.1.3', 13, 0),
(724, 'El usuario ingreso el cliente SHARON codigo: 112050164', '2015-10-19 17:42:09', 'registro', '192.168.1.3', 13, 0),
(725, 'El usuario se logueo al sistema', '2015-10-19 17:58:08', 'login', '192.168.1.3', 10, 7),
(726, 'El usuario ingresó a bodega/compra el articulo: 11/AN03JUN2015 en la sucursal: 0', '2015-10-19 17:59:06', 'bodega', '192.168.1.3', 10, 7),
(727, 'El usuario ingresó a bodega/compra el articulo: 26/BRMAY2015 en la sucursal: 0', '2015-10-19 17:59:06', 'bodega', '192.168.1.3', 10, 7),
(728, 'El usuario ingresó a bodega/compra el articulo: 18-2491 en la sucursal: 0', '2015-10-19 17:59:06', 'bodega', '192.168.1.3', 10, 7),
(729, 'El usuario ingresó a bodega/compra el articulo: 27-CHIMAY2015 en la sucursal: 0', '2015-10-19 17:59:06', 'bodega', '192.168.1.3', 10, 7),
(730, 'El usuario salio del sistema', '2015-10-19 18:03:10', 'login', '192.168.1.3', 10, 7),
(731, 'El usuario se logueo al sistema', '2015-10-19 18:03:17', 'login', '192.168.1.3', 3, 0),
(732, 'El usuario traspaso a inventario el articulo: 2', '2015-10-19 18:09:18', 'traspaso', '192.168.1.3', 3, 0),
(733, 'El usuario traspaso a inventario el articulo: 3', '2015-10-19 18:09:18', 'traspaso', '192.168.1.3', 3, 0),
(734, 'El usuario traspaso a inventario el articulo: 4', '2015-10-19 18:09:18', 'traspaso', '192.168.1.3', 3, 0),
(735, 'El usuario traspaso a inventario el articulo: 5', '2015-10-19 18:09:19', 'traspaso', '192.168.1.3', 3, 0),
(736, 'El usuario salio del sistema', '2015-10-19 18:17:08', 'login', '192.168.1.3', 3, 0),
(737, 'El usuario se logueo al sistema', '2015-10-19 18:18:49', 'login', '192.168.1.3', 3, 0),
(738, 'El usuario salio del sistema', '2015-10-19 18:24:30', 'login', '192.168.1.3', 3, 0),
(739, 'El usuario se logueo al sistema', '2015-10-19 18:26:23', 'login', '192.168.1.3', 3, 0),
(740, 'El usuario traspaso a inventario el articulo: 6', '2015-10-19 18:26:40', 'traspaso', '192.168.1.3', 3, 0),
(741, 'El usuario traspaso a inventario el articulo: 7', '2015-10-19 18:26:40', 'traspaso', '192.168.1.3', 3, 0),
(742, 'El usuario traspaso a inventario el articulo: 8', '2015-10-19 18:26:41', 'traspaso', '192.168.1.3', 3, 0),
(743, 'El usuario traspaso a inventario el articulo: 9', '2015-10-19 18:26:41', 'traspaso', '192.168.1.3', 3, 0),
(744, 'El usuario traspaso a inventario el articulo: 10', '2015-10-19 18:26:41', 'traspaso', '192.168.1.3', 3, 0),
(745, 'El usuario traspaso a inventario el articulo: 11', '2015-10-19 18:26:42', 'traspaso', '192.168.1.3', 3, 0),
(746, 'El usuario salio del sistema', '2015-10-19 18:49:19', 'login', '192.168.1.3', 3, 0),
(747, 'El usuario se logueo al sistema', '2015-10-19 18:49:35', 'login', '192.168.1.3', 3, 0),
(748, 'El usuario traspaso a inventario el articulo: 6', '2015-10-19 18:51:55', 'traspaso', '192.168.1.3', 3, 0),
(749, 'El usuario traspaso a inventario el articulo: 7', '2015-10-19 18:51:55', 'traspaso', '192.168.1.3', 3, 0),
(750, 'El usuario traspaso a inventario el articulo: 8', '2015-10-19 18:51:55', 'traspaso', '192.168.1.3', 3, 0),
(751, 'El usuario traspaso a inventario el articulo: 9', '2015-10-19 18:51:55', 'traspaso', '192.168.1.3', 3, 0),
(752, 'El usuario traspaso a inventario el articulo: 10', '2015-10-19 18:51:56', 'traspaso', '192.168.1.3', 3, 0),
(753, 'El usuario traspaso a inventario el articulo: 11', '2015-10-19 18:51:56', 'traspaso', '192.168.1.3', 3, 0),
(754, 'El usuario salio del sistema', '2015-10-19 18:55:52', 'login', '192.168.1.3', 3, 0),
(755, 'El usuario se logueo al sistema', '2015-10-19 18:56:00', 'login', '192.168.1.3', 4, 1),
(756, 'El usuario salio del sistema', '2015-10-19 18:56:08', 'login', '192.168.1.3', 4, 1),
(757, 'El usuario se logueo al sistema', '2015-10-19 18:56:17', 'login', '192.168.1.3', 5, 2),
(758, 'El usuario salio del sistema', '2015-10-19 18:57:31', 'login', '192.168.1.3', 5, 2),
(759, 'El usuario se logueo al sistema', '2015-10-19 18:57:41', 'login', '192.168.1.3', 6, 3),
(760, 'El usuario traspaso a inventario el articulo: 6', '2015-10-19 18:59:13', 'traspaso', '192.168.1.3', 6, 3),
(761, 'El usuario traspaso a inventario el articulo: 7', '2015-10-19 18:59:14', 'traspaso', '192.168.1.3', 6, 3),
(762, 'El usuario traspaso a inventario el articulo: 8', '2015-10-19 18:59:14', 'traspaso', '192.168.1.3', 6, 3),
(763, 'El usuario traspaso a inventario el articulo: 9', '2015-10-19 18:59:14', 'traspaso', '192.168.1.3', 6, 3),
(764, 'El usuario traspaso a inventario el articulo: 10', '2015-10-19 18:59:14', 'traspaso', '192.168.1.3', 6, 3),
(765, 'El usuario traspaso a inventario el articulo: 11', '2015-10-19 18:59:14', 'traspaso', '192.168.1.3', 6, 3),
(766, 'El usuario salio del sistema', '2015-10-19 19:01:45', 'login', '192.168.1.3', 6, 3),
(767, 'El usuario se logueo al sistema', '2015-10-19 19:01:56', 'login', '192.168.1.3', 3, 0),
(768, 'El usuario traspaso a inventario el articulo: 6', '2015-10-19 19:02:14', 'traspaso', '192.168.1.3', 3, 0),
(769, 'El usuario traspaso a inventario el articulo: 7', '2015-10-19 19:02:14', 'traspaso', '192.168.1.3', 3, 0),
(770, 'El usuario traspaso a inventario el articulo: 8', '2015-10-19 19:02:14', 'traspaso', '192.168.1.3', 3, 0),
(771, 'El usuario traspaso a inventario el articulo: 9', '2015-10-19 19:02:15', 'traspaso', '192.168.1.3', 3, 0),
(772, 'El usuario traspaso a inventario el articulo: 10', '2015-10-19 19:02:15', 'traspaso', '192.168.1.3', 3, 0),
(773, 'El usuario traspaso a inventario el articulo: 11', '2015-10-19 19:02:15', 'traspaso', '192.168.1.3', 3, 0),
(774, 'El usuario salio del sistema', '2015-10-19 19:02:21', 'login', '192.168.1.3', 3, 0),
(775, 'El usuario se logueo al sistema', '2015-10-19 19:02:29', 'login', '192.168.1.3', 5, 2),
(776, 'El usuario salio del sistema', '2015-10-19 19:02:48', 'login', '192.168.1.3', 5, 2),
(777, 'El usuario se logueo al sistema', '2015-10-19 19:02:55', 'login', '192.168.1.3', 3, 0),
(778, 'El usuario salio del sistema', '2015-10-19 19:11:01', 'login', '192.168.1.3', 3, 0),
(779, 'El usuario se logueo al sistema', '2015-10-19 19:11:51', 'login', '192.168.1.3', 3, 0),
(780, 'El usuario salio del sistema', '2015-10-19 19:16:33', 'login', '192.168.1.3', 3, 0),
(781, 'El usuario se logueo al sistema', '2015-10-19 20:01:59', 'login', '192.168.1.3', 3, 0),
(782, 'El usuario se logueo al sistema', '2015-10-19 20:09:02', 'login', '192.168.1.3', 13, 0),
(783, 'El usuario ingreso el cliente LIBNI codigo: 701630077', '2015-10-19 20:12:30', 'registro', '192.168.1.3', 13, 0),
(784, 'El usuario salio del sistema', '2015-10-19 20:13:31', 'login', '192.168.1.3', 3, 0),
(785, 'El usuario ingreso el cliente MARIA ISABEL codigo: 502310338', '2015-10-19 20:15:40', 'registro', '192.168.1.3', 13, 0),
(786, 'El usuario salio del sistema', '2015-10-19 20:20:40', 'login', '192.168.1.3', 13, 0),
(787, 'El usuario se logueo al sistema', '2015-10-19 20:45:09', 'login', '192.168.1.3', 3, 0),
(788, 'El usuario 3 envio a caja la factura consecutivo:3', '2015-10-19 20:57:51', 'factura_envio', '192.168.1.3', 3, 0),
(789, 'El usuario cobro la factura consecutivo: 3', '2015-10-19 20:58:09', 'cobro', '192.168.1.3', 3, 0),
(790, 'eprendasgb autorizo anular factura, sesion de: 3', '2015-10-19 21:26:46', 'autoriza', '192.168.1.3', 3, 0),
(791, 'El usuario anulo la factura consecutivo: 3', '2015-10-19 21:26:47', 'anular', '192.168.1.3', 3, 0),
(792, 'eprendasgb autorizo anular factura, sesion de: 3', '2015-10-19 21:27:06', 'autoriza', '192.168.1.3', 3, 0),
(793, 'El usuario anulo la factura consecutivo: 2', '2015-10-19 21:27:08', 'anular', '192.168.1.3', 3, 0),
(794, 'eprendasgb autorizo anular factura, sesion de: 3', '2015-10-19 21:27:21', 'autoriza', '192.168.1.3', 3, 0),
(795, 'El usuario anulo la factura consecutivo: 1', '2015-10-19 21:27:23', 'anular', '192.168.1.3', 3, 0),
(796, 'El usuario se logueo al sistema', '2015-10-19 21:27:58', 'login', '192.168.1.3', 3, 0),
(797, 'El usuario se logueo al sistema', '2015-10-19 21:33:48', 'login', '192.168.1.3', 4, 1),
(798, 'El usuario salio del sistema', '2015-10-19 21:42:31', 'login', '192.168.1.3', 4, 1),
(799, 'El usuario se logueo al sistema', '2015-10-19 22:09:56', 'login', '192.168.1.3', 3, 0),
(800, 'eprendasgb autorizo un descuento, sesion de: 3', '2015-10-19 22:14:45', 'autoriza', '192.168.1.3', 3, 0),
(801, 'eprendasgb autorizo un descuento, sesion de: 3', '2015-10-19 22:15:04', 'autoriza', '192.168.1.3', 3, 0),
(802, 'eprendasgb autorizo un descuento, sesion de: 3', '2015-10-19 22:15:19', 'autoriza', '192.168.1.3', 3, 0),
(803, 'eprendasgb autorizo un descuento, sesion de: 3', '2015-10-19 22:15:35', 'autoriza', '192.168.1.3', 3, 0),
(804, 'El usuario 3 envio a caja la factura consecutivo:4', '2015-10-19 22:15:46', 'factura_envio', '192.168.1.3', 3, 0),
(805, 'El usuario cobro la factura consecutivo: 4', '2015-10-19 22:16:06', 'cobro', '192.168.1.3', 3, 0),
(806, 'El usuario salio del sistema', '2015-10-19 22:40:12', 'login', '192.168.1.3', 3, 0),
(807, 'El usuario se logueo al sistema', '2015-10-19 22:45:48', 'login', '192.168.1.3', 3, 0),
(808, 'El usuario salio del sistema', '2015-10-19 22:50:49', 'login', '192.168.1.3', 3, 0),
(809, 'El usuario se logueo al sistema', '2015-10-19 23:02:10', 'login', '192.168.1.3', 3, 0),
(810, 'El usuario salio del sistema', '2015-10-19 23:07:23', 'login', '192.168.1.3', 3, 0),
(811, 'El usuario se logueo al sistema', '2015-10-19 23:48:05', 'login', '192.168.1.3', 3, 0),
(812, 'El usuario se logueo al sistema', '2015-10-20 00:24:09', 'login', '192.168.1.3', 3, 0),
(813, 'El usuario salio del sistema', '2015-10-20 00:25:20', 'login', '192.168.1.3', 3, 0),
(814, 'El usuario se logueo al sistema', '2015-10-20 16:02:15', 'login', '192.168.1.3', 3, 0),
(815, 'El usuario actualizo la info. del servidor de impresión', '2015-10-20 16:03:25', 'edicion', '192.168.1.3', 3, 0),
(816, 'El usuario actualizo la info. del servidor de impresión', '2015-10-20 16:04:35', 'edicion', '192.168.1.3', 3, 0),
(817, 'El usuario salio del sistema', '2015-10-20 16:13:33', 'login', '192.168.1.3', 3, 0),
(818, 'El usuario se logueo al sistema', '2015-10-20 16:42:44', 'login', '192.168.1.3', 3, 0),
(819, 'El usuario actualizo la info. del servidor de impresión', '2015-10-20 16:51:16', 'edicion', '192.168.1.3', 3, 0),
(820, 'El usuario salio del sistema', '2015-10-20 17:08:47', 'login', '192.168.1.3', 3, 0),
(821, 'El usuario se logueo al sistema', '2015-10-20 17:23:08', 'login', '192.168.1.3', 3, 0),
(822, 'El usuario salio del sistema', '2015-10-20 17:23:47', 'login', '192.168.1.3', 3, 0),
(823, 'El usuario se logueo al sistema', '2015-10-20 17:35:42', 'login', '192.168.1.3', 3, 0),
(824, 'El usuario salio del sistema', '2015-10-20 17:42:44', 'login', '192.168.1.3', 3, 0),
(825, 'El usuario se logueo al sistema', '2015-10-20 17:45:57', 'login', '192.168.1.3', 3, 0),
(826, 'El usuario salio del sistema', '2015-10-20 17:53:54', 'login', '192.168.1.3', 3, 0),
(827, 'El usuario se logueo al sistema', '2015-10-20 17:54:15', 'login', '192.168.1.3', 3, 0),
(828, 'El usuario salio del sistema', '2015-10-20 18:01:50', 'login', '192.168.1.3', 3, 0),
(829, 'El usuario se logueo al sistema', '2015-10-20 19:23:58', 'login', '192.168.1.3', 17, 0),
(830, 'El usuario ingreso el cliente ERIC  codigo: 107880179', '2015-10-20 19:25:44', 'registro', '192.168.1.3', 17, 0),
(831, 'El usuario ingreso el cliente LUCIA codigo: 900420815', '2015-10-20 19:30:53', 'registro', '192.168.1.3', 17, 0),
(832, 'El usuario ingreso el cliente VILMA  codigo: 106600175', '2015-10-20 19:34:33', 'registro', '192.168.1.3', 17, 0),
(833, 'El usuario salio del sistema', '2015-10-20 19:41:55', 'login', '192.168.1.3', 17, 0),
(834, 'El usuario se logueo al sistema', '2015-10-20 19:52:41', 'login', '192.168.1.3', 13, 0),
(835, 'El usuario se logueo al sistema', '2015-10-20 19:55:37', 'login', '192.168.1.3', 17, 0),
(836, 'El usuario ingreso el cliente MERCEDES codigo: 401370518', '2015-10-20 19:58:35', 'registro', '192.168.1.3', 13, 0),
(837, 'El usuario ingreso el cliente KATTIA ISABEL codigo: 107160684', '2015-10-20 19:59:12', 'registro', '192.168.1.3', 17, 0),
(838, 'El usuario ingreso el cliente SONIA CRISTINA codigo: 205090370', '2015-10-20 20:01:28', 'registro', '192.168.1.3', 13, 0),
(839, 'El usuario ingreso el cliente MARIA DE LOS ANGELES codigo: 105210007', '2015-10-20 20:03:44', 'registro', '192.168.1.3', 17, 0),
(840, 'El usuario cambio su perfil: 13', '2015-10-20 20:03:56', 'edicion', '192.168.1.3', 13, 0),
(841, 'El usuario salio del sistema', '2015-10-20 20:04:30', 'login', '192.168.1.3', 13, 0),
(842, 'El usuario ingreso el cliente VILVIA codigo: 112860303', '2015-10-20 20:06:39', 'registro', '192.168.1.3', 17, 0),
(843, 'El usuario ingreso el cliente CAROL  codigo: 205700333', '2015-10-20 20:09:49', 'registro', '192.168.1.3', 17, 0),
(844, 'El usuario salio del sistema', '2015-10-20 20:10:34', 'login', '192.168.1.3', 17, 0),
(845, 'El usuario se logueo al sistema', '2015-10-20 20:11:58', 'login', '192.168.1.3', 17, 0),
(846, 'El usuario salio del sistema', '2015-10-20 20:13:19', 'login', '192.168.1.3', 17, 0),
(847, 'El usuario se logueo al sistema', '2015-10-20 22:50:17', 'login', '192.168.1.3', 3, 0),
(848, 'El usuario se logueo al sistema', '2015-10-20 22:52:30', 'login', '192.168.1.3', 3, 0),
(849, 'El usuario salio del sistema', '2015-10-20 22:56:24', 'login', '192.168.1.3', 3, 0),
(850, 'El usuario salio del sistema', '2015-10-20 22:58:50', 'login', '192.168.1.3', 3, 0);

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_13_cheque`
--
CREATE TABLE IF NOT EXISTS `TB_13_Cheque` (
  `Cheque_Id` INT NOT NULL,
  `Cheque_Numero` VARCHAR(45) NULL,
  `Banco` INT NOT NULL,
  `TB_07_Factura_Factura_Consecutivo` INT NOT NULL,
  `TB_07_Factura_TB_02_Sucursal_Codigo` INT NOT NULL,
  `TB_07_Factura_Factura_Vendedor_Codigo` INT NOT NULL,
  `TB_07_Factura_Factura_Vendedor_Sucursal` INT NOT NULL,
  `TB_07_Factura_TB_03_Cliente_Cliente_Cedula` VARCHAR(50) NOT NULL
)
ENGINE = InnoDB DEFAULT CHARSET=utf8;;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_14_sesiones`
--

CREATE TABLE IF NOT EXISTS `tb_14_sesiones` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_14_sesiones`
--

INSERT INTO `tb_14_sesiones` (`session_id`, `ip_address`, `user_agent`, `last_activity`, `user_data`) VALUES
('3608486781baccc6ba57ff89aba1ede3', '192.168.1.3', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.71 Safari/537.36', 1445381784, ''),
('bdf4cea5d0ad91d61e0d2babfe77191a', '82.118.237.106', 'Mozilla/5.0 (Windows NT 5.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1', 1445381543, ''),
('eb75483e8da789a30b1d83d2cd6c20d6', '109.30.109.9', '0', 1445378366, ''),
('f0c533741c4d1896b881980957bfbcfb', '192.168.1.3', 'Mozilla/5.0 (Windows NT 5.2; rv:40.0) Gecko/20100101 Firefox/40.0', 1445381930, ''),
('f729cbc91f8c6af3aacce1a9131d34a9', '190.167.157.119', '0', 1445380661, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_15_permisos`
--

CREATE TABLE IF NOT EXISTS `tb_15_permisos` (
`Permisos_Id` int(11) NOT NULL,
  `Permisos_Area` varchar(30) DEFAULT NULL,
  `Permisos_Value` tinyint(1) DEFAULT NULL,
  `TB_01_Usuario_Usuario_Codigo` int(11) NOT NULL,
  `TB_01_Usuario_TB_02_Sucursal_Codigo` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2130 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_15_permisos`
--

INSERT INTO `tb_15_permisos` (`Permisos_Id`, `Permisos_Area`, `Permisos_Value`, `TB_01_Usuario_Usuario_Codigo`, `TB_01_Usuario_TB_02_Sucursal_Codigo`) VALUES
(1257, 'activar_empresa', 1, 5, 2),
(1258, 'activar_familias', 1, 5, 2),
(1259, 'actualizar_estado', 1, 5, 2),
(1260, 'compras_sucursales', 1, 5, 2),
(1261, 'anular_recibos', 1, 5, 2),
(1262, 'anular_facturas', 1, 5, 2),
(1263, 'cambio_codigo_articulo', 1, 5, 2),
(1264, 'consultar_ventas', 1, 5, 2),
(1265, 'cierre_caja', 1, 5, 2),
(1266, 'crear_factura', 1, 5, 2),
(1267, 'crear_proforma', 1, 5, 2),
(1268, 'crear_retiros', 1, 5, 2),
(1269, 'entrar_notas', 1, 5, 2),
(1270, 'entrar_notas_d', 1, 5, 2),
(1271, 'deposito_recibos', 1, 5, 2),
(1272, 'desactivar_banco', 1, 5, 2),
(1273, 'desactivar_empresa', 1, 5, 2),
(1274, 'desactivar_familias', 1, 5, 2),
(1275, 'editar_autorizacion', 1, 5, 2),
(1276, 'editar_banco', 1, 5, 2),
(1277, 'editar_cliente', 1, 5, 2),
(1278, 'editar_codigo', 1, 5, 2),
(1279, 'editar_empresa', 1, 5, 2),
(1280, 'editar_familias', 1, 5, 2),
(1281, 'editar_permisos', 1, 5, 2),
(1282, 'editar_usuarios', 1, 5, 2),
(1283, 'entrada_familias', 1, 5, 2),
(1284, 'entrar_banco', 1, 5, 2),
(1285, 'entrar_caja', 1, 5, 2),
(1286, 'entrar_configuracion', 1, 5, 2),
(1287, 'entrar_empresa', 1, 5, 2),
(1288, 'entrar_recibos', 1, 5, 2),
(1289, 'ingreso_bodega', 1, 5, 2),
(1290, 'manejo_articulos', 1, 5, 2),
(1291, 'otros_cliente', 1, 5, 2),
(1292, 'realizar_consulta', 1, 5, 2),
(1293, 'registrar_articulo_individual', 1, 5, 2),
(1294, 'registrar_articulos_masivo', 1, 5, 2),
(1295, 'registrar_banco', 1, 5, 2),
(1296, 'registrar_cliente', 1, 5, 2),
(1297, 'registrar_empresa', 1, 5, 2),
(1298, 'registrar_familia', 1, 5, 2),
(1299, 'registrar_usuarios', 1, 5, 2),
(1300, 'traspaso_individual_articulo', 1, 5, 2),
(1301, 'traspaso_articulos_masivo', 1, 5, 2),
(1302, 'ver_autorizacion', 1, 5, 2),
(1303, 'consulta_normal', 1, 5, 2),
(1304, 'consulta_administradores', 1, 5, 2),
(1305, 'consulta_cajas', 1, 5, 2),
(1306, 'ver_bitacora', 1, 5, 2),
(1307, 'activar_empresa', 1, 6, 3),
(1308, 'activar_familias', 1, 6, 3),
(1309, 'actualizar_estado', 1, 6, 3),
(1310, 'compras_sucursales', 1, 6, 3),
(1311, 'anular_recibos', 1, 6, 3),
(1312, 'anular_facturas', 1, 6, 3),
(1313, 'cambio_codigo_articulo', 1, 6, 3),
(1314, 'consultar_ventas', 1, 6, 3),
(1315, 'cierre_caja', 1, 6, 3),
(1316, 'crear_factura', 1, 6, 3),
(1317, 'crear_proforma', 1, 6, 3),
(1318, 'crear_retiros', 1, 6, 3),
(1319, 'entrar_notas', 1, 6, 3),
(1320, 'entrar_notas_d', 1, 6, 3),
(1321, 'deposito_recibos', 1, 6, 3),
(1322, 'desactivar_banco', 1, 6, 3),
(1323, 'desactivar_empresa', 1, 6, 3),
(1324, 'desactivar_familias', 1, 6, 3),
(1325, 'editar_autorizacion', 1, 6, 3),
(1326, 'editar_banco', 1, 6, 3),
(1327, 'editar_cliente', 1, 6, 3),
(1328, 'editar_codigo', 1, 6, 3),
(1329, 'editar_empresa', 1, 6, 3),
(1330, 'editar_familias', 1, 6, 3),
(1331, 'editar_permisos', 1, 6, 3),
(1332, 'editar_usuarios', 1, 6, 3),
(1333, 'entrada_familias', 1, 6, 3),
(1334, 'entrar_banco', 1, 6, 3),
(1335, 'entrar_caja', 1, 6, 3),
(1336, 'entrar_configuracion', 1, 6, 3),
(1337, 'entrar_empresa', 1, 6, 3),
(1338, 'entrar_recibos', 1, 6, 3),
(1339, 'ingreso_bodega', 1, 6, 3),
(1340, 'manejo_articulos', 1, 6, 3),
(1341, 'otros_cliente', 1, 6, 3),
(1342, 'realizar_consulta', 1, 6, 3),
(1343, 'registrar_articulo_individual', 1, 6, 3),
(1344, 'registrar_articulos_masivo', 1, 6, 3),
(1345, 'registrar_banco', 1, 6, 3),
(1346, 'registrar_cliente', 1, 6, 3),
(1347, 'registrar_empresa', 1, 6, 3),
(1348, 'registrar_familia', 1, 6, 3),
(1349, 'registrar_usuarios', 1, 6, 3),
(1350, 'traspaso_individual_articulo', 1, 6, 3),
(1351, 'traspaso_articulos_masivo', 1, 6, 3),
(1352, 'ver_autorizacion', 1, 6, 3),
(1353, 'consulta_normal', 1, 6, 3),
(1354, 'consulta_administradores', 1, 6, 3),
(1355, 'consulta_cajas', 1, 6, 3),
(1356, 'ver_bitacora', 1, 6, 3),
(1357, 'activar_empresa', 1, 7, 4),
(1358, 'activar_familias', 1, 7, 4),
(1359, 'actualizar_estado', 1, 7, 4),
(1360, 'compras_sucursales', 1, 7, 4),
(1361, 'anular_recibos', 1, 7, 4),
(1362, 'anular_facturas', 1, 7, 4),
(1363, 'cambio_codigo_articulo', 1, 7, 4),
(1364, 'consultar_ventas', 1, 7, 4),
(1365, 'cierre_caja', 1, 7, 4),
(1366, 'crear_factura', 1, 7, 4),
(1367, 'crear_proforma', 1, 7, 4),
(1368, 'crear_retiros', 1, 7, 4),
(1369, 'entrar_notas', 1, 7, 4),
(1370, 'entrar_notas_d', 1, 7, 4),
(1371, 'deposito_recibos', 1, 7, 4),
(1372, 'desactivar_banco', 1, 7, 4),
(1373, 'desactivar_empresa', 1, 7, 4),
(1374, 'desactivar_familias', 1, 7, 4),
(1375, 'editar_autorizacion', 1, 7, 4),
(1376, 'editar_banco', 1, 7, 4),
(1377, 'editar_cliente', 1, 7, 4),
(1378, 'editar_codigo', 1, 7, 4),
(1379, 'editar_empresa', 1, 7, 4),
(1380, 'editar_familias', 1, 7, 4),
(1381, 'editar_permisos', 1, 7, 4),
(1382, 'editar_usuarios', 1, 7, 4),
(1383, 'entrada_familias', 1, 7, 4),
(1384, 'entrar_banco', 1, 7, 4),
(1385, 'entrar_caja', 1, 7, 4),
(1386, 'entrar_configuracion', 1, 7, 4),
(1387, 'entrar_empresa', 1, 7, 4),
(1388, 'entrar_recibos', 1, 7, 4),
(1389, 'ingreso_bodega', 1, 7, 4),
(1390, 'manejo_articulos', 1, 7, 4),
(1391, 'otros_cliente', 1, 7, 4),
(1392, 'realizar_consulta', 1, 7, 4),
(1393, 'registrar_articulo_individual', 1, 7, 4),
(1394, 'registrar_articulos_masivo', 1, 7, 4),
(1395, 'registrar_banco', 1, 7, 4),
(1396, 'registrar_cliente', 1, 7, 4),
(1397, 'registrar_empresa', 1, 7, 4),
(1398, 'registrar_familia', 1, 7, 4),
(1399, 'registrar_usuarios', 1, 7, 4),
(1400, 'traspaso_individual_articulo', 1, 7, 4),
(1401, 'traspaso_articulos_masivo', 1, 7, 4),
(1402, 'ver_autorizacion', 1, 7, 4),
(1403, 'consulta_normal', 1, 7, 4),
(1404, 'consulta_administradores', 1, 7, 4),
(1405, 'consulta_cajas', 1, 7, 4),
(1406, 'ver_bitacora', 1, 7, 4),
(1407, 'activar_empresa', 1, 8, 5),
(1408, 'activar_familias', 1, 8, 5),
(1409, 'actualizar_estado', 1, 8, 5),
(1410, 'compras_sucursales', 1, 8, 5),
(1411, 'anular_recibos', 1, 8, 5),
(1412, 'anular_facturas', 1, 8, 5),
(1413, 'cambio_codigo_articulo', 1, 8, 5),
(1414, 'consultar_ventas', 1, 8, 5),
(1415, 'cierre_caja', 1, 8, 5),
(1416, 'crear_factura', 1, 8, 5),
(1417, 'crear_proforma', 1, 8, 5),
(1418, 'crear_retiros', 1, 8, 5),
(1419, 'entrar_notas', 1, 8, 5),
(1420, 'entrar_notas_d', 1, 8, 5),
(1421, 'deposito_recibos', 1, 8, 5),
(1422, 'desactivar_banco', 1, 8, 5),
(1423, 'desactivar_empresa', 1, 8, 5),
(1424, 'desactivar_familias', 1, 8, 5),
(1425, 'editar_autorizacion', 1, 8, 5),
(1426, 'editar_banco', 1, 8, 5),
(1427, 'editar_cliente', 1, 8, 5),
(1428, 'editar_codigo', 1, 8, 5),
(1429, 'editar_empresa', 1, 8, 5),
(1430, 'editar_familias', 1, 8, 5),
(1431, 'editar_permisos', 1, 8, 5),
(1432, 'editar_usuarios', 1, 8, 5),
(1433, 'entrada_familias', 1, 8, 5),
(1434, 'entrar_banco', 1, 8, 5),
(1435, 'entrar_caja', 1, 8, 5),
(1436, 'entrar_configuracion', 1, 8, 5),
(1437, 'entrar_empresa', 1, 8, 5),
(1438, 'entrar_recibos', 1, 8, 5),
(1439, 'ingreso_bodega', 1, 8, 5),
(1440, 'manejo_articulos', 1, 8, 5),
(1441, 'otros_cliente', 1, 8, 5),
(1442, 'realizar_consulta', 1, 8, 5),
(1443, 'registrar_articulo_individual', 1, 8, 5),
(1444, 'registrar_articulos_masivo', 1, 8, 5),
(1445, 'registrar_banco', 1, 8, 5),
(1446, 'registrar_cliente', 1, 8, 5),
(1447, 'registrar_empresa', 1, 8, 5),
(1448, 'registrar_familia', 1, 8, 5),
(1449, 'registrar_usuarios', 1, 8, 5),
(1450, 'traspaso_individual_articulo', 1, 8, 5),
(1451, 'traspaso_articulos_masivo', 1, 8, 5),
(1452, 'ver_autorizacion', 1, 8, 5),
(1453, 'consulta_normal', 1, 8, 5),
(1454, 'consulta_administradores', 1, 8, 5),
(1455, 'consulta_cajas', 1, 8, 5),
(1456, 'ver_bitacora', 1, 8, 5),
(1457, 'activar_empresa', 1, 9, 6),
(1458, 'activar_familias', 1, 9, 6),
(1459, 'actualizar_estado', 1, 9, 6),
(1460, 'compras_sucursales', 1, 9, 6),
(1461, 'anular_recibos', 1, 9, 6),
(1462, 'anular_facturas', 1, 9, 6),
(1463, 'cambio_codigo_articulo', 1, 9, 6),
(1464, 'consultar_ventas', 1, 9, 6),
(1465, 'cierre_caja', 1, 9, 6),
(1466, 'crear_factura', 1, 9, 6),
(1467, 'crear_proforma', 1, 9, 6),
(1468, 'crear_retiros', 1, 9, 6),
(1469, 'entrar_notas', 1, 9, 6),
(1470, 'entrar_notas_d', 1, 9, 6),
(1471, 'deposito_recibos', 1, 9, 6),
(1472, 'desactivar_banco', 1, 9, 6),
(1473, 'desactivar_empresa', 1, 9, 6),
(1474, 'desactivar_familias', 1, 9, 6),
(1475, 'editar_autorizacion', 1, 9, 6),
(1476, 'editar_banco', 1, 9, 6),
(1477, 'editar_cliente', 1, 9, 6),
(1478, 'editar_codigo', 1, 9, 6),
(1479, 'editar_empresa', 1, 9, 6),
(1480, 'editar_familias', 1, 9, 6),
(1481, 'editar_permisos', 1, 9, 6),
(1482, 'editar_usuarios', 1, 9, 6),
(1483, 'entrada_familias', 1, 9, 6),
(1484, 'entrar_banco', 1, 9, 6),
(1485, 'entrar_caja', 1, 9, 6),
(1486, 'entrar_configuracion', 1, 9, 6),
(1487, 'entrar_empresa', 1, 9, 6),
(1488, 'entrar_recibos', 1, 9, 6),
(1489, 'ingreso_bodega', 1, 9, 6),
(1490, 'manejo_articulos', 1, 9, 6),
(1491, 'otros_cliente', 1, 9, 6),
(1492, 'realizar_consulta', 1, 9, 6),
(1493, 'registrar_articulo_individual', 1, 9, 6),
(1494, 'registrar_articulos_masivo', 1, 9, 6),
(1495, 'registrar_banco', 1, 9, 6),
(1496, 'registrar_cliente', 1, 9, 6),
(1497, 'registrar_empresa', 1, 9, 6),
(1498, 'registrar_familia', 1, 9, 6),
(1499, 'registrar_usuarios', 1, 9, 6),
(1500, 'traspaso_individual_articulo', 1, 9, 6),
(1501, 'traspaso_articulos_masivo', 1, 9, 6),
(1502, 'ver_autorizacion', 1, 9, 6),
(1503, 'consulta_normal', 1, 9, 6),
(1504, 'consulta_administradores', 1, 9, 6),
(1505, 'consulta_cajas', 1, 9, 6),
(1506, 'ver_bitacora', 1, 9, 6),
(1507, 'activar_empresa', 1, 10, 7),
(1508, 'activar_familias', 1, 10, 7),
(1509, 'actualizar_estado', 1, 10, 7),
(1510, 'compras_sucursales', 1, 10, 7),
(1511, 'anular_recibos', 1, 10, 7),
(1512, 'anular_facturas', 1, 10, 7),
(1513, 'cambio_codigo_articulo', 1, 10, 7),
(1514, 'consultar_ventas', 1, 10, 7),
(1515, 'cierre_caja', 1, 10, 7),
(1516, 'crear_factura', 1, 10, 7),
(1517, 'crear_proforma', 1, 10, 7),
(1518, 'crear_retiros', 1, 10, 7),
(1519, 'entrar_notas', 1, 10, 7),
(1520, 'entrar_notas_d', 1, 10, 7),
(1521, 'deposito_recibos', 1, 10, 7),
(1522, 'desactivar_banco', 1, 10, 7),
(1523, 'desactivar_empresa', 1, 10, 7),
(1524, 'desactivar_familias', 1, 10, 7),
(1525, 'editar_autorizacion', 1, 10, 7),
(1526, 'editar_banco', 1, 10, 7),
(1527, 'editar_cliente', 1, 10, 7),
(1528, 'editar_codigo', 1, 10, 7),
(1529, 'editar_empresa', 1, 10, 7),
(1530, 'editar_familias', 1, 10, 7),
(1531, 'editar_permisos', 1, 10, 7),
(1532, 'editar_usuarios', 1, 10, 7),
(1533, 'entrada_familias', 1, 10, 7),
(1534, 'entrar_banco', 1, 10, 7),
(1535, 'entrar_caja', 1, 10, 7),
(1536, 'entrar_configuracion', 1, 10, 7),
(1537, 'entrar_empresa', 1, 10, 7),
(1538, 'entrar_recibos', 1, 10, 7),
(1539, 'ingreso_bodega', 1, 10, 7),
(1540, 'manejo_articulos', 1, 10, 7),
(1541, 'otros_cliente', 1, 10, 7),
(1542, 'realizar_consulta', 1, 10, 7),
(1543, 'registrar_articulo_individual', 1, 10, 7),
(1544, 'registrar_articulos_masivo', 1, 10, 7),
(1545, 'registrar_banco', 1, 10, 7),
(1546, 'registrar_cliente', 1, 10, 7),
(1547, 'registrar_empresa', 1, 10, 7),
(1548, 'registrar_familia', 1, 10, 7),
(1549, 'registrar_usuarios', 1, 10, 7),
(1550, 'traspaso_individual_articulo', 1, 10, 7),
(1551, 'traspaso_articulos_masivo', 1, 10, 7),
(1552, 'ver_autorizacion', 1, 10, 7),
(1553, 'consulta_normal', 1, 10, 7),
(1554, 'consulta_administradores', 1, 10, 7),
(1555, 'consulta_cajas', 1, 10, 7),
(1556, 'ver_bitacora', 1, 10, 7),
(1607, 'activar_empresa', 1, 2, 0),
(1608, 'activar_familias', 1, 2, 0),
(1609, 'actualizar_estado', 1, 2, 0),
(1610, 'compras_sucursales', 1, 2, 0),
(1611, 'anular_recibos', 1, 2, 0),
(1612, 'anular_facturas', 1, 2, 0),
(1613, 'cambio_codigo_articulo', 1, 2, 0),
(1614, 'consultar_ventas', 1, 2, 0),
(1615, 'cierre_caja', 1, 2, 0),
(1616, 'crear_factura', 1, 2, 0),
(1617, 'crear_proforma', 1, 2, 0),
(1618, 'crear_retiros', 1, 2, 0),
(1619, 'entrar_notas', 1, 2, 0),
(1620, 'entrar_notas_d', 1, 2, 0),
(1621, 'deposito_recibos', 1, 2, 0),
(1622, 'desactivar_banco', 1, 2, 0),
(1623, 'desactivar_empresa', 1, 2, 0),
(1624, 'desactivar_familias', 1, 2, 0),
(1625, 'editar_autorizacion', 1, 2, 0),
(1626, 'editar_banco', 1, 2, 0),
(1627, 'editar_cliente', 1, 2, 0),
(1628, 'editar_codigo', 1, 2, 0),
(1629, 'editar_empresa', 1, 2, 0),
(1630, 'editar_familias', 1, 2, 0),
(1631, 'editar_permisos', 1, 2, 0),
(1632, 'editar_usuarios', 1, 2, 0),
(1633, 'entrada_familias', 1, 2, 0),
(1634, 'entrar_banco', 1, 2, 0),
(1635, 'entrar_caja', 1, 2, 0),
(1636, 'entrar_configuracion', 1, 2, 0),
(1637, 'entrar_empresa', 1, 2, 0),
(1638, 'entrar_recibos', 1, 2, 0),
(1639, 'ingreso_bodega', 1, 2, 0),
(1640, 'manejo_articulos', 1, 2, 0),
(1641, 'otros_cliente', 1, 2, 0),
(1642, 'realizar_consulta', 1, 2, 0),
(1643, 'registrar_articulo_individual', 1, 2, 0),
(1644, 'registrar_articulos_masivo', 1, 2, 0),
(1645, 'registrar_banco', 1, 2, 0),
(1646, 'registrar_cliente', 1, 2, 0),
(1647, 'registrar_empresa', 1, 2, 0),
(1648, 'registrar_familia', 1, 2, 0),
(1649, 'registrar_usuarios', 1, 2, 0),
(1650, 'traspaso_individual_articulo', 1, 2, 0),
(1651, 'traspaso_articulos_masivo', 1, 2, 0),
(1652, 'ver_autorizacion', 1, 2, 0),
(1653, 'consulta_normal', 1, 2, 0),
(1654, 'consulta_administradores', 1, 2, 0),
(1655, 'consulta_cajas', 1, 2, 0),
(1656, 'ver_bitacora', 1, 2, 0),
(1657, 'activar_empresa', 1, 1, 0),
(1658, 'activar_familias', 1, 1, 0),
(1659, 'actualizar_estado', 1, 1, 0),
(1660, 'compras_sucursales', 1, 1, 0),
(1661, 'anular_recibos', 1, 1, 0),
(1662, 'anular_facturas', 1, 1, 0),
(1663, 'cambio_codigo_articulo', 1, 1, 0),
(1664, 'consultar_ventas', 1, 1, 0),
(1665, 'cierre_caja', 1, 1, 0),
(1666, 'crear_factura', 1, 1, 0),
(1667, 'crear_proforma', 1, 1, 0),
(1668, 'crear_retiros', 1, 1, 0),
(1669, 'entrar_notas', 1, 1, 0),
(1670, 'entrar_notas_d', 1, 1, 0),
(1671, 'deposito_recibos', 1, 1, 0),
(1672, 'desactivar_banco', 1, 1, 0),
(1673, 'desactivar_empresa', 1, 1, 0),
(1674, 'desactivar_familias', 1, 1, 0),
(1675, 'editar_autorizacion', 1, 1, 0),
(1676, 'editar_banco', 1, 1, 0),
(1677, 'editar_cliente', 1, 1, 0),
(1678, 'editar_codigo', 1, 1, 0),
(1679, 'editar_empresa', 1, 1, 0),
(1680, 'editar_familias', 1, 1, 0),
(1681, 'editar_permisos', 1, 1, 0),
(1682, 'editar_usuarios', 1, 1, 0),
(1683, 'entrada_familias', 1, 1, 0),
(1684, 'entrar_banco', 1, 1, 0),
(1685, 'entrar_caja', 1, 1, 0),
(1686, 'entrar_configuracion', 1, 1, 0),
(1687, 'entrar_empresa', 1, 1, 0),
(1688, 'entrar_recibos', 1, 1, 0),
(1689, 'ingreso_bodega', 1, 1, 0),
(1690, 'manejo_articulos', 1, 1, 0),
(1691, 'otros_cliente', 1, 1, 0),
(1692, 'realizar_consulta', 1, 1, 0),
(1693, 'registrar_articulo_individual', 1, 1, 0),
(1694, 'registrar_articulos_masivo', 1, 1, 0),
(1695, 'registrar_banco', 1, 1, 0),
(1696, 'registrar_cliente', 1, 1, 0),
(1697, 'registrar_empresa', 1, 1, 0),
(1698, 'registrar_familia', 1, 1, 0),
(1699, 'registrar_usuarios', 1, 1, 0),
(1700, 'traspaso_individual_articulo', 1, 1, 0),
(1701, 'traspaso_articulos_masivo', 1, 1, 0),
(1702, 'ver_autorizacion', 1, 1, 0),
(1703, 'consulta_normal', 1, 1, 0),
(1704, 'consulta_administradores', 1, 1, 0),
(1705, 'consulta_cajas', 1, 1, 0),
(1706, 'ver_bitacora', 1, 1, 0),
(1707, 'activar_empresa', 1, 3, 0),
(1708, 'activar_familias', 1, 3, 0),
(1709, 'actualizar_estado', 1, 3, 0),
(1710, 'compras_sucursales', 1, 3, 0),
(1711, 'anular_recibos', 1, 3, 0),
(1712, 'anular_facturas', 1, 3, 0),
(1713, 'cambio_codigo_articulo', 1, 3, 0),
(1714, 'consultar_ventas', 1, 3, 0),
(1715, 'cierre_caja', 1, 3, 0),
(1716, 'crear_factura', 1, 3, 0),
(1717, 'crear_proforma', 1, 3, 0),
(1718, 'crear_retiros', 1, 3, 0),
(1719, 'entrar_notas', 1, 3, 0),
(1720, 'entrar_notas_d', 1, 3, 0),
(1721, 'deposito_recibos', 1, 3, 0),
(1722, 'desactivar_banco', 1, 3, 0),
(1723, 'desactivar_empresa', 1, 3, 0),
(1724, 'desactivar_familias', 1, 3, 0),
(1725, 'editar_autorizacion', 1, 3, 0),
(1726, 'editar_banco', 1, 3, 0),
(1727, 'editar_cliente', 1, 3, 0),
(1728, 'editar_codigo', 1, 3, 0),
(1729, 'editar_empresa', 1, 3, 0),
(1730, 'editar_familias', 1, 3, 0),
(1731, 'editar_permisos', 1, 3, 0),
(1732, 'editar_usuarios', 1, 3, 0),
(1733, 'entrada_familias', 1, 3, 0),
(1734, 'entrar_banco', 1, 3, 0),
(1735, 'entrar_caja', 1, 3, 0),
(1736, 'entrar_configuracion', 1, 3, 0),
(1737, 'entrar_empresa', 1, 3, 0),
(1738, 'entrar_recibos', 1, 3, 0),
(1739, 'ingreso_bodega', 1, 3, 0),
(1740, 'manejo_articulos', 1, 3, 0),
(1741, 'otros_cliente', 1, 3, 0),
(1742, 'realizar_consulta', 1, 3, 0),
(1743, 'registrar_articulo_individual', 1, 3, 0),
(1744, 'registrar_articulos_masivo', 1, 3, 0),
(1745, 'registrar_banco', 1, 3, 0),
(1746, 'registrar_cliente', 1, 3, 0),
(1747, 'registrar_empresa', 1, 3, 0),
(1748, 'registrar_familia', 1, 3, 0),
(1749, 'registrar_usuarios', 1, 3, 0),
(1750, 'traspaso_individual_articulo', 1, 3, 0),
(1751, 'traspaso_articulos_masivo', 1, 3, 0),
(1752, 'ver_autorizacion', 1, 3, 0),
(1753, 'consulta_normal', 1, 3, 0),
(1754, 'consulta_administradores', 1, 3, 0),
(1755, 'consulta_cajas', 1, 3, 0),
(1756, 'ver_bitacora', 1, 3, 0),
(1757, 'activar_empresa', 1, 4, 1),
(1758, 'activar_familias', 1, 4, 1),
(1759, 'actualizar_estado', 1, 4, 1),
(1760, 'compras_sucursales', 1, 4, 1),
(1761, 'anular_recibos', 1, 4, 1),
(1762, 'anular_facturas', 1, 4, 1),
(1763, 'cambio_codigo_articulo', 1, 4, 1),
(1764, 'consultar_ventas', 1, 4, 1),
(1765, 'cierre_caja', 1, 4, 1),
(1766, 'crear_factura', 1, 4, 1),
(1767, 'crear_proforma', 1, 4, 1),
(1768, 'crear_retiros', 1, 4, 1),
(1769, 'entrar_notas', 1, 4, 1),
(1770, 'entrar_notas_d', 1, 4, 1),
(1771, 'deposito_recibos', 1, 4, 1),
(1772, 'desactivar_banco', 1, 4, 1),
(1773, 'desactivar_empresa', 1, 4, 1),
(1774, 'desactivar_familias', 1, 4, 1),
(1775, 'editar_autorizacion', 1, 4, 1),
(1776, 'editar_banco', 1, 4, 1),
(1777, 'editar_cliente', 1, 4, 1),
(1778, 'editar_codigo', 1, 4, 1),
(1779, 'editar_empresa', 1, 4, 1),
(1780, 'editar_familias', 1, 4, 1),
(1781, 'editar_permisos', 1, 4, 1),
(1782, 'editar_usuarios', 1, 4, 1),
(1783, 'entrada_familias', 1, 4, 1),
(1784, 'entrar_banco', 1, 4, 1),
(1785, 'entrar_caja', 1, 4, 1),
(1786, 'entrar_configuracion', 1, 4, 1),
(1787, 'entrar_empresa', 1, 4, 1),
(1788, 'entrar_recibos', 1, 4, 1),
(1789, 'ingreso_bodega', 1, 4, 1),
(1790, 'manejo_articulos', 1, 4, 1),
(1791, 'otros_cliente', 1, 4, 1),
(1792, 'realizar_consulta', 1, 4, 1),
(1793, 'registrar_articulo_individual', 1, 4, 1),
(1794, 'registrar_articulos_masivo', 1, 4, 1),
(1795, 'registrar_banco', 1, 4, 1),
(1796, 'registrar_cliente', 1, 4, 1),
(1797, 'registrar_empresa', 1, 4, 1),
(1798, 'registrar_familia', 1, 4, 1),
(1799, 'registrar_usuarios', 1, 4, 1),
(1800, 'traspaso_individual_articulo', 1, 4, 1),
(1801, 'traspaso_articulos_masivo', 1, 4, 1),
(1802, 'ver_autorizacion', 1, 4, 1),
(1803, 'consulta_normal', 1, 4, 1),
(1804, 'consulta_administradores', 1, 4, 1),
(1805, 'consulta_cajas', 1, 4, 1),
(1806, 'ver_bitacora', 1, 4, 1),
(1993, 'crear_factura', 1, 18, 0),
(1994, 'crear_proforma', 1, 18, 0),
(1995, 'editar_cliente', 1, 18, 0),
(1996, 'registrar_cliente', 1, 18, 0),
(1997, 'ver_autorizacion', 1, 18, 0),
(2013, 'crear_factura', 1, 20, 0),
(2014, 'crear_proforma', 1, 20, 0),
(2015, 'editar_cliente', 1, 20, 0),
(2016, 'registrar_cliente', 1, 20, 0),
(2017, 'ver_autorizacion', 1, 20, 0),
(2032, 'cierre_caja', 1, 12, 0),
(2033, 'crear_factura', 1, 12, 0),
(2034, 'crear_proforma', 1, 12, 0),
(2035, 'crear_retiros', 1, 12, 0),
(2036, 'entrar_notas', 1, 12, 0),
(2037, 'editar_autorizacion', 1, 12, 0),
(2038, 'editar_cliente', 1, 12, 0),
(2039, 'entrar_caja', 1, 12, 0),
(2040, 'entrar_recibos', 1, 12, 0),
(2041, 'realizar_consulta', 1, 12, 0),
(2042, 'registrar_cliente', 1, 12, 0),
(2043, 'ver_autorizacion', 1, 12, 0),
(2044, 'consulta_normal', 1, 12, 0),
(2045, 'consulta_cajas', 1, 12, 0),
(2046, 'anular_recibos', 1, 11, 0),
(2047, 'anular_facturas', 1, 11, 0),
(2048, 'cierre_caja', 1, 11, 0),
(2049, 'crear_factura', 1, 11, 0),
(2050, 'crear_proforma', 1, 11, 0),
(2051, 'crear_retiros', 1, 11, 0),
(2052, 'entrar_notas', 1, 11, 0),
(2053, 'deposito_recibos', 1, 11, 0),
(2054, 'editar_autorizacion', 1, 11, 0),
(2055, 'editar_cliente', 1, 11, 0),
(2056, 'editar_usuarios', 1, 11, 0),
(2057, 'entrar_caja', 1, 11, 0),
(2058, 'entrar_recibos', 1, 11, 0),
(2059, 'realizar_consulta', 1, 11, 0),
(2060, 'registrar_cliente', 1, 11, 0),
(2061, 'ver_autorizacion', 1, 11, 0),
(2062, 'consulta_normal', 1, 11, 0),
(2063, 'consulta_cajas', 1, 11, 0),
(2064, 'crear_factura', 1, 13, 0),
(2065, 'crear_proforma', 1, 13, 0),
(2066, 'editar_cliente', 1, 13, 0),
(2067, 'registrar_cliente', 1, 13, 0),
(2068, 'ver_autorizacion', 1, 13, 0),
(2069, 'compras_sucursales', 1, 14, 0),
(2070, 'anular_recibos', 1, 14, 0),
(2071, 'cambio_codigo_articulo', 1, 14, 0),
(2072, 'crear_factura', 1, 14, 0),
(2073, 'crear_proforma', 1, 14, 0),
(2074, 'entrar_notas', 1, 14, 0),
(2075, 'entrar_notas_d', 1, 14, 0),
(2076, 'deposito_recibos', 1, 14, 0),
(2077, 'editar_autorizacion', 1, 14, 0),
(2078, 'editar_cliente', 1, 14, 0),
(2079, 'editar_codigo', 1, 14, 0),
(2080, 'entrar_caja', 1, 14, 0),
(2081, 'entrar_configuracion', 1, 14, 0),
(2082, 'entrar_empresa', 1, 14, 0),
(2083, 'ingreso_bodega', 1, 14, 0),
(2084, 'manejo_articulos', 1, 14, 0),
(2085, 'realizar_consulta', 1, 14, 0),
(2086, 'registrar_articulo_individual', 1, 14, 0),
(2087, 'registrar_articulos_masivo', 1, 14, 0),
(2088, 'registrar_cliente', 1, 14, 0),
(2089, 'traspaso_individual_articulo', 1, 14, 0),
(2090, 'traspaso_articulos_masivo', 1, 14, 0),
(2091, 'ver_autorizacion', 1, 14, 0),
(2092, 'crear_factura', 1, 15, 0),
(2093, 'crear_proforma', 1, 15, 0),
(2094, 'editar_autorizacion', 1, 15, 0),
(2095, 'editar_cliente', 1, 15, 0),
(2096, 'realizar_consulta', 1, 15, 0),
(2097, 'registrar_cliente', 1, 15, 0),
(2098, 'ver_autorizacion', 1, 15, 0),
(2099, 'crear_factura', 1, 16, 0),
(2100, 'crear_proforma', 1, 16, 0),
(2101, 'editar_cliente', 1, 16, 0),
(2102, 'registrar_cliente', 1, 16, 0),
(2103, 'ver_autorizacion', 1, 16, 0),
(2104, 'cierre_caja', 1, 17, 0),
(2105, 'crear_factura', 1, 17, 0),
(2106, 'crear_proforma', 1, 17, 0),
(2107, 'crear_retiros', 1, 17, 0),
(2108, 'entrar_notas', 1, 17, 0),
(2109, 'entrar_notas_d', 1, 17, 0),
(2110, 'deposito_recibos', 1, 17, 0),
(2111, 'editar_autorizacion', 1, 17, 0),
(2112, 'editar_cliente', 1, 17, 0),
(2113, 'entrar_caja', 1, 17, 0),
(2114, 'entrar_recibos', 1, 17, 0),
(2115, 'registrar_banco', 1, 17, 0),
(2116, 'registrar_cliente', 1, 17, 0),
(2117, 'ver_autorizacion', 1, 17, 0),
(2118, 'consulta_normal', 1, 17, 0),
(2119, 'consulta_cajas', 1, 17, 0),
(2120, 'crear_factura', 1, 19, 0),
(2121, 'crear_proforma', 1, 19, 0),
(2122, 'editar_cliente', 1, 19, 0),
(2123, 'registrar_cliente', 1, 19, 0),
(2124, 'ver_autorizacion', 1, 19, 0),
(2125, 'crear_factura', 1, 21, 0),
(2126, 'crear_proforma', 1, 21, 0),
(2127, 'editar_cliente', 1, 21, 0),
(2128, 'registrar_cliente', 1, 21, 0),
(2129, 'ver_autorizacion', 1, 21, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_16_authclientes`
--

CREATE TABLE IF NOT EXISTS `tb_16_authclientes` (
  `AuthClientes_Id` int(11) NOT NULL,
  `AuthClientes_Cedula` int(11) DEFAULT NULL,
  `AuthClientes_Nombre` varchar(45) DEFAULT NULL,
  `AuthClientes_Apellidos` varchar(45) DEFAULT NULL,
  `AuthClientes_Carta_URL` varchar(45) DEFAULT NULL,
  `AuthClientes_Seq` int(11) DEFAULT NULL,
  `TB_03_Cliente_Cliente_Cedula` varchar(50)  NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_16_authclientes`
--

INSERT INTO `tb_16_authclientes` (`AuthClientes_Id`, `AuthClientes_Cedula`, `AuthClientes_Nombre`, `AuthClientes_Apellidos`, `AuthClientes_Carta_URL`, `AuthClientes_Seq`, `TB_03_Cliente_Cliente_Cedula`) VALUES
(0, 104430228, 'KAREN ELOISE', 'HEADLY MULLINGS', 'default.png', 1, 102750322),
(0, 112010752, 'Esteban ', 'Prendas Zamora', NULL, 1, 105300225),
(0, 113860540, 'ANA YANCY', 'CALVO DURAN', 'default.png', 1, 105950215),
(0, 303190684, 'YESENIA ', 'CALDERON MORA', 'default.png', 1, 106340439),
(0, 107940317, 'LUIS FERNANDO', 'VILLALOBOS CHAVES', 'default.png', 1, 110260997),
(0, 702210639, 'CARLOS ALBERTO', 'CHACON GRANADOS', 'default.png', 1, 110450776),
(0, 109990878, 'SHIRLEY ', 'GONZALEZ VARGAS ', 'default.png', 1, 111140108),
(0, 104200993, 'ANA LORENA ', 'CESPEDES SANCHEZ ', 'default.png', 1, 112060834),
(0, 502270259, 'NURIA MAYELA', 'ZAMORA SOTO', 'default.png', 1, 113620323),
(0, 206530433, 'LIZ MELISSA', 'UGALDE VARGAS', 'default.png', 1, 205230211);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_17_descuento_producto`
--

CREATE TABLE IF NOT EXISTS `tb_17_descuento_producto` (
`Descuento_producto_id` int(11) NOT NULL,
  `Descuento_producto_monto` double DEFAULT NULL,
  `Descuento_producto_porcentaje` double DEFAULT NULL,
  `TB_06_Articulo_Articulo_Codigo` varchar(30) NOT NULL,
  `TB_06_Articulo_TB_05_Familia_Familia_Codigo` int(11) NOT NULL,
  `TB_03_Cliente_Cliente_Cedula` varchar(50)  NOT NULL,
  `TB_02_Sucursal_Codigo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_18_tarjeta`
--

CREATE TABLE IF NOT EXISTS `tb_18_tarjeta` (
`Tarjeta_Id` int(11) NOT NULL,
  `Tarjeta_Numero_Transaccion` varchar(45) DEFAULT NULL,
  `Tarjeta_Comision_Banco` float DEFAULT NULL,
  `TB_07_Factura_Factura_Consecutivo` int(11) NOT NULL,
  `TB_07_Factura_TB_02_Sucursal_Codigo` int(11) NOT NULL,
  `TB_07_Factura_Factura_Vendedor_Codigo` int(11) NOT NULL,
  `TB_07_Factura_Factura_Vendedor_Sucursal` int(11) NOT NULL,
  `TB_07_Factura_TB_03_Cliente_Cliente_Cedula` varchar(50)  NOT NULL,
  `TB_22_Banco_Banco_Codigo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_19_deposito`
--

CREATE TABLE IF NOT EXISTS `tb_19_deposito` (
  `Deposito_Id` int(11) NOT NULL,
  `Deposito_Numero_Transaccion` varchar(45) DEFAULT NULL,
  `TB_07_Factura_Factura_Consecutivo` int(11) NOT NULL,
  `TB_07_Factura_TB_02_Sucursal_Codigo` int(11) NOT NULL,
  `TB_07_Factura_Factura_Vendedor_Codigo` int(11) NOT NULL,
  `TB_07_Factura_Factura_Vendedor_Sucursal` int(11) NOT NULL,
  `TB_07_Factura_TB_03_Cliente_Cliente_Cedula` varchar(50)  NOT NULL,
  `TB_22_Banco_Banco_Codigo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_20_descuento_familia`
--

CREATE TABLE IF NOT EXISTS `tb_20_descuento_familia` (
`Descuento_familia_id` int(11) NOT NULL,
  `Descuento_familia_monto` double DEFAULT NULL,
  `Descuento_familia_porcentaje` double DEFAULT NULL,
  `TB_05_Familia_Familia_Codigo` int(11) NOT NULL,
  `TB_05_Familia_TB_02_Sucursal_Codigo` int(11) NOT NULL,
  `TB_03_Cliente_Cliente_Cedula` varchar(50)  NOT NULL,
  `TB_02_Sucursal_Codigo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_21_descuento_cliente`
--

CREATE TABLE IF NOT EXISTS `tb_21_descuento_cliente` (
`Descuento_cliente_id` int(11) NOT NULL,
  `Descuento_cliente_porcentaje` double DEFAULT NULL,
  `TB_03_Cliente_Cliente_Cedula` varchar(50)  NOT NULL,
  `TB_02_Sucursal_Codigo` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_21_descuento_cliente`
--

INSERT INTO `tb_21_descuento_cliente` (`Descuento_cliente_id`, `Descuento_cliente_porcentaje`, `TB_03_Cliente_Cliente_Cedula`, `TB_02_Sucursal_Codigo`) VALUES
(1, 35, 105300225, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_22_banco`
--

CREATE TABLE IF NOT EXISTS `tb_22_banco` (
`Banco_Codigo` int(11) NOT NULL,
  `Banco_Nombre` varchar(45) DEFAULT NULL,
  `Banco_Comision_Porcentaje` float DEFAULT NULL,
  `Banco_Creado_Por` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_22_banco`
--

INSERT INTO `tb_22_banco` (`Banco_Codigo`, `Banco_Nombre`, `Banco_Comision_Porcentaje`, `Banco_Creado_Por`) VALUES
(1, 'BNCR', 4.08, 4),
(2, 'CREDOMATIC', 5.05, 4),
(3, 'BANCREDITO', 3.6, 4),
(4, 'BCR', 0, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_23_mixto`
--

CREATE TABLE IF NOT EXISTS `tb_23_mixto` (
`Mixto_Id` int(11) NOT NULL,
  `Mixto_Cantidad_Paga` float DEFAULT NULL,
  `TB_18_Tarjeta_Tarjeta_Id` int(11) NOT NULL,
  `TB_18_Tarjeta_TB_07_Factura_Factura_Consecutivo` int(11) NOT NULL,
  `TB_18_Tarjeta_TB_07_Factura_TB_02_Sucursal_Codigo` int(11) NOT NULL,
  `TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Codigo` int(11) NOT NULL,
  `TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Sucursal` int(11) NOT NULL,
  `TB_18_Tarjeta_TB_07_Factura_TB_03_Cliente_Cliente_Cedula` varchar(50)  NOT NULL,
  `TB_18_Tarjeta_TB_22_Banco_Banco_Codigo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_24_credito`
--

CREATE TABLE IF NOT EXISTS `tb_24_credito` (
`Credito_Id` int(11) NOT NULL,
  `Credito_Numero_Dias` int(11) DEFAULT NULL,
  `Credito_Saldo_Actual` double DEFAULT NULL,
  `Credito_Saldo_Inicial` double DEFAULT NULL,
  `Credito_Generico` varchar(150) DEFAULT NULL,
  `Credito_Fecha_Expedicion` timestamp NULL DEFAULT NULL,
  `Credito_Factura_Consecutivo` int(11) NOT NULL,
  `Credito_Sucursal_Codigo` int(11) NOT NULL,
  `Credito_Vendedor_Codigo` int(11) NOT NULL,
  `Credito_Vendedor_Sucursal` int(11) NOT NULL,
  `Credito_Cliente_Cedula` varchar(50)  NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_24_credito`
--

INSERT INTO `tb_24_credito` (`Credito_Id`, `Credito_Numero_Dias`, `Credito_Saldo_Actual`, `Credito_Saldo_Inicial`, `Credito_Generico`, `Credito_Fecha_Expedicion`, `Credito_Factura_Consecutivo`, `Credito_Sucursal_Codigo`, `Credito_Vendedor_Codigo`, `Credito_Vendedor_Sucursal`, `Credito_Cliente_Cedula`) VALUES
(1, 8, 2000, 2000, NULL, '2015-09-25 20:39:43', 1, 0, 14, 0, 105300225),
(2, 8, 10000, 10000, NULL, '2015-09-25 21:01:38', 2, 0, 3, 0, 117001386032),
(3, 8, 25410, 25410, NULL, '2015-10-19 22:16:06', 4, 0, 3, 0, 117001386032);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_25_maximo_credito_cliente`
--

CREATE TABLE IF NOT EXISTS `tb_25_maximo_credito_cliente` (
`Credito_Cliente_Id` int(11) NOT NULL,
  `Credito_Cliente_Cantidad_Maxima` double DEFAULT NULL,
  `TB_03_Cliente_Cliente_Cedula` varchar(50)  NOT NULL,
  `TB_02_Sucursal_Codigo` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_25_maximo_credito_cliente`
--

INSERT INTO `tb_25_maximo_credito_cliente` (`Credito_Cliente_Id`, `Credito_Cliente_Cantidad_Maxima`, `TB_03_Cliente_Cliente_Cedula`, `TB_02_Sucursal_Codigo`) VALUES
(1, 5000, 105300225, 0),
(2, 50000, 117001386032, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_26_recibos_dinero`
--

CREATE TABLE IF NOT EXISTS `tb_26_recibos_dinero` (
`Consecutivo` int(11) NOT NULL,
  `Recibo_Cantidad` float DEFAULT NULL,
  `Recibo_Fecha` timestamp NULL DEFAULT NULL,
  `Recibo_Saldo` float DEFAULT NULL,
  `Anulado` tinyint(1) NOT NULL DEFAULT '0',
  `Tipo_Pago` varchar(20) NOT NULL,
  `Credito` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_27_notas_credito`
--

CREATE TABLE IF NOT EXISTS `tb_27_notas_credito` (
  `Consecutivo` int(11) NOT NULL,
  `Nombre_Cliente` varchar(60) DEFAULT NULL,
  `Fecha_Creacion` timestamp NULL DEFAULT NULL,
  `Factura_Acreditar` int(11) NOT NULL,
  `Factura_Aplicar` int(11) NOT NULL,
  `Tipo_Pago` varchar(10) NOT NULL,
  `Moneda` varchar(15) NOT NULL,
  `Por_IVA` double NOT NULL,
  `Tipo_Cambio` double NOT NULL,
  `Sucursal` int(11) NOT NULL,
  `Cliente` varchar(50)  NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_27_notas_credito`
--

INSERT INTO `tb_27_notas_credito` (`Consecutivo`, `Nombre_Cliente`, `Fecha_Creacion`, `Factura_Acreditar`, `Factura_Aplicar`, `Tipo_Pago`, `Moneda`, `Por_IVA`, `Tipo_Cambio`, `Sucursal`, `Cliente`) VALUES
(1, 'TIENDA HEREDIA (WILFREDY PIEDRA POCHET)', '2015-09-25 20:53:45', 1, 1, 'contado', 'colones', 13, 530.45, 0, 105300225);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_28_productos_notas_credito`
--

CREATE TABLE IF NOT EXISTS `tb_28_productos_notas_credito` (
`Id` int(11) NOT NULL,
  `Codigo` varchar(30) DEFAULT NULL,
  `Descripcion` varchar(150) DEFAULT NULL,
  `Cantidad_Bueno` int(11) DEFAULT NULL,
  `Cantidad_Defectuoso` int(11) DEFAULT NULL,
  `Precio_Unitario` double DEFAULT NULL,
  `Nota_Credito_Consecutivo` int(11) NOT NULL,
  `Sucursal` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_28_productos_notas_credito`
--

INSERT INTO `tb_28_productos_notas_credito` (`Id`, `Codigo`, `Descripcion`, `Cantidad_Bueno`, `Cantidad_Defectuoso`, `Precio_Unitario`, `Nota_Credito_Consecutivo`, `Sucursal`) VALUES
(1, '1', 'PRUEBA', 1, 0, 2000, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_29_deposito_recibo`
--

CREATE TABLE IF NOT EXISTS `tb_29_deposito_recibo` (
`Id` int(11) NOT NULL,
  `Banco_Id` int(11) DEFAULT NULL,
  `Banco_Nombre` varchar(45) DEFAULT NULL,
  `Numero_Deposito` varchar(45) DEFAULT NULL,
  `Fecha` timestamp NULL DEFAULT NULL,
  `Recibo` int(11) NOT NULL,
  `Credito` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_30_notas_debito`
--

CREATE TABLE IF NOT EXISTS `tb_30_notas_debito` (
  `Consecutivo` int(11) NOT NULL,
  `Fecha` timestamp NULL DEFAULT NULL,
  `Impuesto_Porcentaje` float NOT NULL,
  `Observaciones` varchar(150) NOT NULL,
  `Usuario` int(11) NOT NULL,
  `Sucursal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_31_productos_notas_debito`
--

CREATE TABLE IF NOT EXISTS `tb_31_productos_notas_debito` (
`Id` int(11) NOT NULL,
  `Codigo` varchar(30) DEFAULT NULL,
  `Descripcion` varchar(150) DEFAULT NULL,
  `Cantidad_Debitar` int(11) DEFAULT NULL,
  `Precio_Unitario` double DEFAULT NULL,
  `Nota_Debito_Consecutivo` int(11) NOT NULL,
  `Sucursal` int(11) NOT NULL,
  `Usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_32_tarjeta_recibos`
--

CREATE TABLE IF NOT EXISTS `tb_32_tarjeta_recibos` (
`Id` int(11) NOT NULL,
  `Numero_Autorizacion` varchar(45) DEFAULT NULL,
  `Comision_Por` float DEFAULT NULL,
  `Banco` int(11) NOT NULL,
  `Recibo` int(11) NOT NULL,
  `Credito` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_33_retiros_parciales`
--

CREATE TABLE IF NOT EXISTS `tb_33_retiros_parciales` (
`Id` int(11) NOT NULL,
  `Monto` double DEFAULT NULL,
  `Fecha_Hora` timestamp NULL DEFAULT NULL,
  `Tipo_Cambio` double NOT NULL,
  `Usuario` int(11) NOT NULL,
  `Sucursal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_34_bodega`
--

CREATE TABLE IF NOT EXISTS `tb_34_bodega` (
  `Codigo` varchar(30) NOT NULL,
  `Descripcion` varchar(150) DEFAULT NULL,
  `Costo` double DEFAULT NULL,
  `Cantidad` int(11) DEFAULT NULL,
  `Usuario` int(11) NOT NULL,
  `Sucursal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_34_bodega`
--

INSERT INTO `tb_34_bodega` (`Codigo`, `Descripcion`, `Costo`, `Cantidad`, `Usuario`, `Sucursal`) VALUES
('11/AN03JUN2015', 'ANILLO COMPROMISO O.L. ', 2000.35, 75, 10, 0),
('18-2491', 'RELOJ DAMA', 1435.36, 75, 10, 0),
('26/BRMAY2015', 'CADENA GRUESA ESLABONES', 2686.36, 75, 10, 0),
('27-CHIMAY2015', 'CADENA + PULSERA CABALLERO', 1325.3, 75, 10, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_35_cambio_codigo`
--

CREATE TABLE IF NOT EXISTS `tb_35_cambio_codigo` (
`Id` int(11) NOT NULL,
  `Fecha` timestamp NULL DEFAULT NULL,
  `Usuario` int(11) NOT NULL,
  `Sucursal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_36_articulos_cambio_codigo`
--

CREATE TABLE IF NOT EXISTS `tb_36_articulos_cambio_codigo` (
`Id` int(11) NOT NULL,
  `Articulo_Cambio` varchar(30) DEFAULT NULL,
  `Descripcion_Cambio` varchar(150) DEFAULT NULL,
  `Articulo_Abonado` varchar(30) DEFAULT NULL,
  `Descripcion_Abonado` varchar(150) DEFAULT NULL,
  `Cantidad` int(11) DEFAULT NULL,
  `Cambio_Codigo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_37_cierre_caja`
--

CREATE TABLE IF NOT EXISTS `tb_37_cierre_caja` (
`Id` int(11) NOT NULL,
  `Fecha` timestamp NULL DEFAULT NULL,
  `Base` double NOT NULL,
  `Tipo_Cambio` double NOT NULL,
  `Total_Conteo` double NOT NULL,
  `Sucursal` int(11) NOT NULL,
  `Usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_38_moneda_cierre_caja`
--

CREATE TABLE IF NOT EXISTS `tb_38_moneda_cierre_caja` (
`Id` int(11) NOT NULL,
  `Denominacion` int(11) DEFAULT NULL,
  `Cantidad` int(11) DEFAULT NULL,
  `Tipo` varchar(15) DEFAULT NULL,
  `Moneda` varchar(15) DEFAULT NULL,
  `Cierre_Caja` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_39_configuracion`
--

CREATE TABLE IF NOT EXISTS `tb_39_configuracion` (
  `Parametro` varchar(50) NOT NULL,
  `Valor` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_39_configuracion`
--

INSERT INTO `tb_39_configuracion` (`Parametro`, `Valor`) VALUES
('aplicar_retencion', '1'),
('cantidad_decimales', '2'),
('codigo_empresa_traspaso_compras', '0'),
('correo_administracion', 'esteban@garotasbonitascr.com'),
('direccion_ip_servidor_impresion', '192.168.1.4'),
('dolar_compra', '528.01'),
('dolar_venta', '540.46'),
('iva', '13'),
('monto_intermedio_compra_cliente', '15000'),
('monto_minimo_compra_cliente', '20000'),
('porcentaje_retencion_tarjetas_hacienda', '5.31'),
('protocolo_servidor_impresion', 'http'),
('puerto_servidor_impresion', '8080'),
('tiempo_sesion', '300'),
('ultima_actualizacion_estado_clientes', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_40_apartado`
--

CREATE TABLE IF NOT EXISTS `tb_40_apartado` (
`Id` int(11) NOT NULL,
  `Abono` double DEFAULT NULL,
  `Credito` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_41_productos_factura_temporal`
--

CREATE TABLE IF NOT EXISTS `tb_41_productos_factura_temporal` (
`Id` int(11) NOT NULL,
  `Codigo_Articulo` varchar(20) NOT NULL,
  `Cantidad` int(11) DEFAULT '0',
  `Factura_Temporal` varchar(32) DEFAULT NULL,
  `Sucursal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_42_moneda_retiro_parcial`
--

CREATE TABLE IF NOT EXISTS `tb_42_moneda_retiro_parcial` (
`Id` int(11) NOT NULL,
  `Denominacion` int(11) DEFAULT NULL,
  `Cantidad` int(11) DEFAULT NULL,
  `Tipo` varchar(15) DEFAULT NULL,
  `Moneda` varchar(15) DEFAULT NULL,
  `Retiro` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_43_deposito_recibo`
--

CREATE TABLE IF NOT EXISTS `tb_43_deposito_recibo` (
`Id` int(11) NOT NULL,
  `Numero_Documento` varchar(45) DEFAULT NULL,
  `Recibo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_44_traspaso_inventario`
--

CREATE TABLE IF NOT EXISTS `tb_44_traspaso_inventario` (
`Id` int(11) NOT NULL,
  `Sucursal_Salida` int(11) NOT NULL,
  `Sucursal_Entrada` int(11) NOT NULL,
  `Fecha` timestamp NULL DEFAULT NULL,
  `Usuario` int(11) NOT NULL,
  `Factura_Traspasada` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_44_traspaso_inventario`
--

INSERT INTO `tb_44_traspaso_inventario` (`Id`, `Sucursal_Salida`, `Sucursal_Entrada`, `Fecha`, `Usuario`, `Factura_Traspasada`) VALUES
(1, 0, 2, '2015-09-25 21:05:24', 3, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_45_articulos_traspaso_inventario`
--

CREATE TABLE IF NOT EXISTS `tb_45_articulos_traspaso_inventario` (
`Id` int(11) NOT NULL,
  `Codigo` varchar(20) DEFAULT NULL,
  `Descripcion` varchar(150) DEFAULT NULL,
  `Cantidad` int(11) DEFAULT NULL,
  `Traspaso` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_45_articulos_traspaso_inventario`
--

INSERT INTO `tb_45_articulos_traspaso_inventario` (`Id`, `Codigo`, `Descripcion`, `Cantidad`, `Traspaso`) VALUES
(1, '1', 'PRUEBA', 5, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_46_relacion_desampa`
--

CREATE TABLE IF NOT EXISTS `tb_46_relacion_desampa` (
`Id` int(11) NOT NULL,
  `Consecutivo` int(11) NOT NULL,
  `Documento` varchar(50) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_46_relacion_desampa`
--

INSERT INTO `tb_46_relacion_desampa` (`Id`, `Consecutivo`, `Documento`) VALUES
(1, 8, 'factura'),
(2, 9, 'factura');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tb_01_usuario`
--
ALTER TABLE `tb_01_usuario`
 ADD PRIMARY KEY (`Usuario_Codigo`,`TB_02_Sucursal_Codigo`), ADD KEY `fk_TB_01_Usuario_TB_02_Sucursal1_idx` (`TB_02_Sucursal_Codigo`);

--
-- Indices de la tabla `tb_02_sucursal`
--
ALTER TABLE `tb_02_sucursal`
 ADD PRIMARY KEY (`Codigo`);

--
-- Indices de la tabla `tb_03_cliente`
--
ALTER TABLE `tb_03_cliente`
 ADD PRIMARY KEY (`Cliente_Cedula`);

--
-- Indices de la tabla `tb_04_articulos_proforma`
--
ALTER TABLE `tb_04_articulos_proforma`
 ADD PRIMARY KEY (`Articulo_Proforma_Id`,`TB_10_Proforma_Proforma_Consecutivo`,`TB_10_Proforma_TB_02_Sucursal_Codigo`,`TB_10_Proforma_Proforma_Vendedor_Codigo`,`TB_10_Proforma_Proforma_Vendedor_Sucursal`,`TB_10_Proforma_TB_03_Cliente_Cliente_Cedula`), ADD KEY `fk_TB_10_Proforma_has_TB_06_Articulo_TB_10_Proforma1_idx` (`TB_10_Proforma_Proforma_Consecutivo`,`TB_10_Proforma_TB_02_Sucursal_Codigo`,`TB_10_Proforma_Proforma_Vendedor_Codigo`,`TB_10_Proforma_Proforma_Vendedor_Sucursal`,`TB_10_Proforma_TB_03_Cliente_Cliente_Cedula`);

--
-- Indices de la tabla `tb_05_familia`
--
ALTER TABLE `tb_05_familia`
 ADD PRIMARY KEY (`Familia_Codigo`,`TB_02_Sucursal_Codigo`), ADD KEY `fk_TB_05_Familia_TB_02_Sucursal1_idx` (`TB_02_Sucursal_Codigo`);

--
-- Indices de la tabla `tb_06_articulo`
--
ALTER TABLE `tb_06_articulo`
 ADD PRIMARY KEY (`Articulo_Codigo`,`TB_05_Familia_Familia_Codigo`,`TB_02_Sucursal_Codigo`), ADD KEY `fk_TB_06_Articulo_TB_05_Familia1_idx` (`TB_05_Familia_Familia_Codigo`), ADD KEY `fk_TB_06_Articulo_TB_02_Sucursal1_idx` (`TB_02_Sucursal_Codigo`);

--
-- Indices de la tabla `tb_07_factura`
--
ALTER TABLE `tb_07_factura`
 ADD PRIMARY KEY (`Factura_Consecutivo`,`TB_02_Sucursal_Codigo`,`Factura_Vendedor_Codigo`,`Factura_Vendedor_Sucursal`,`TB_03_Cliente_Cliente_Cedula`), ADD KEY `fk_TB_07_Factura_TB_03_Cliente1_idx` (`TB_03_Cliente_Cliente_Cedula`);

--
-- Indices de la tabla `tb_08_articulos_factura`
--
ALTER TABLE `tb_08_articulos_factura`
 ADD PRIMARY KEY (`Articulo_Factura_id`,`TB_07_Factura_Factura_Consecutivo`,`TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_07_Factura_Factura_Vendedor_Codigo`,`TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_07_Factura_TB_03_Cliente_Cliente_Cedula`), ADD KEY `fk_TB_08_Articulos_Factura_TB_07_Factura1_idx` (`TB_07_Factura_Factura_Consecutivo`,`TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_07_Factura_Factura_Vendedor_Codigo`,`TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_07_Factura_TB_03_Cliente_Cliente_Cedula`);

--
-- Indices de la tabla `tb_09_compras`
--
ALTER TABLE `tb_09_compras`
 ADD PRIMARY KEY (`Id`), ADD KEY `fk_TB_09_Bodega_TB_02_Sucursal1_idx` (`Sucursal`), ADD KEY `fk_TB_09_Bodega_TB_01_Usuario1_idx` (`Usuario`);

--
-- Indices de la tabla `tb_10_proforma`
--
ALTER TABLE `tb_10_proforma`
 ADD PRIMARY KEY (`Proforma_Consecutivo`,`TB_02_Sucursal_Codigo`,`Proforma_Vendedor_Codigo`,`Proforma_Vendedor_Sucursal`,`TB_03_Cliente_Cliente_Cedula`), ADD KEY `fk_TB_10_Proforma_TB_02_Sucursal1_idx` (`TB_02_Sucursal_Codigo`), ADD KEY `fk_TB_10_Proforma_TB_01_Usuario1_idx` (`Proforma_Vendedor_Codigo`,`Proforma_Vendedor_Sucursal`), ADD KEY `fk_TB_10_Proforma_TB_03_Cliente1_idx` (`TB_03_Cliente_Cliente_Cedula`);

--
-- Indices de la tabla `tb_11_precios`
--
ALTER TABLE `tb_11_precios`
 ADD PRIMARY KEY (`Precio_Id`,`TB_06_Articulo_Articulo_Codigo`,`TB_06_Articulo_TB_05_Familia_Familia_Codigo`,`TB_06_Articulo_TB_02_Sucursal_Codigo`), ADD KEY `fk_TB_11_Precios_TB_06_Articulo1_idx` (`TB_06_Articulo_Articulo_Codigo`,`TB_06_Articulo_TB_05_Familia_Familia_Codigo`,`TB_06_Articulo_TB_02_Sucursal_Codigo`);

--
-- Indices de la tabla `tb_12_transacciones`
--
ALTER TABLE `tb_12_transacciones`
 ADD PRIMARY KEY (`Trans_Codigo`,`TB_01_Usuario_Usuario_Codigo`,`TB_01_Usuario_TB_02_Sucursal_Codigo`), ADD KEY `fk_TB_12_Transacciones_TB_01_Usuario1_idx` (`TB_01_Usuario_Usuario_Codigo`,`TB_01_Usuario_TB_02_Sucursal_Codigo`);

--
-- Indices de la tabla `tb_13_cheque`
--
ALTER TABLE `tb_13_cheque`
 ADD PRIMARY KEY (`Cheque_Id`,`TB_07_Factura_Factura_Consecutivo`,`TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_07_Factura_Factura_Vendedor_Codigo`,`TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_07_Factura_TB_03_Cliente_Cliente_Cedula`), ADD KEY `fk_TB_13_Cheque_TB_07_Factura1_idx` (`TB_07_Factura_Factura_Consecutivo`,`TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_07_Factura_Factura_Vendedor_Codigo`,`TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_07_Factura_TB_03_Cliente_Cliente_Cedula`);

--
-- Indices de la tabla `tb_14_sesiones`
--
ALTER TABLE `tb_14_sesiones`
 ADD PRIMARY KEY (`session_id`), ADD KEY `'last_activity_idx'` (`last_activity`);

--
-- Indices de la tabla `tb_15_permisos`
--
ALTER TABLE `tb_15_permisos`
 ADD PRIMARY KEY (`Permisos_Id`,`TB_01_Usuario_Usuario_Codigo`,`TB_01_Usuario_TB_02_Sucursal_Codigo`), ADD KEY `fk_TB_15_Permisos_TB_01_Usuario1_idx` (`TB_01_Usuario_Usuario_Codigo`,`TB_01_Usuario_TB_02_Sucursal_Codigo`);

--
-- Indices de la tabla `tb_16_authclientes`
--
ALTER TABLE `tb_16_authclientes`
 ADD PRIMARY KEY (`TB_03_Cliente_Cliente_Cedula`,`AuthClientes_Id`), ADD KEY `fk_TB_16_AuthClientes_TB_03_Cliente1_idx` (`TB_03_Cliente_Cliente_Cedula`);

--
-- Indices de la tabla `tb_17_descuento_producto`
--
ALTER TABLE `tb_17_descuento_producto`
 ADD PRIMARY KEY (`Descuento_producto_id`,`TB_06_Articulo_Articulo_Codigo`,`TB_06_Articulo_TB_05_Familia_Familia_Codigo`,`TB_03_Cliente_Cliente_Cedula`,`TB_02_Sucursal_Codigo`), ADD KEY `fk_TB_21_Descuento_Producto_TB_06_Articulo1_idx` (`TB_06_Articulo_Articulo_Codigo`,`TB_06_Articulo_TB_05_Familia_Familia_Codigo`), ADD KEY `fk_TB_21_Descuento_Producto_TB_03_Cliente1_idx` (`TB_03_Cliente_Cliente_Cedula`), ADD KEY `fk_TB_21_Descuento_Producto_TB_02_Sucursal1_idx` (`TB_02_Sucursal_Codigo`);

--
-- Indices de la tabla `tb_18_tarjeta`
--
ALTER TABLE `tb_18_tarjeta`
 ADD PRIMARY KEY (`Tarjeta_Id`,`TB_07_Factura_Factura_Consecutivo`,`TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_07_Factura_Factura_Vendedor_Codigo`,`TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_07_Factura_TB_03_Cliente_Cliente_Cedula`,`TB_22_Banco_Banco_Codigo`), ADD KEY `fk_TB_18_Tarjeta_TB_07_Factura1_idx` (`TB_07_Factura_Factura_Consecutivo`,`TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_07_Factura_Factura_Vendedor_Codigo`,`TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_07_Factura_TB_03_Cliente_Cliente_Cedula`), ADD KEY `fk_TB_18_Tarjeta_TB_22_Banco1_idx` (`TB_22_Banco_Banco_Codigo`);

--
-- Indices de la tabla `tb_19_deposito`
--
ALTER TABLE `tb_19_deposito`
 ADD PRIMARY KEY (`Deposito_Id`,`TB_07_Factura_Factura_Consecutivo`,`TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_07_Factura_Factura_Vendedor_Codigo`,`TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_07_Factura_TB_03_Cliente_Cliente_Cedula`,`TB_22_Banco_Banco_Codigo`), ADD KEY `fk_TB_19_Deposito_TB_07_Factura1_idx` (`TB_07_Factura_Factura_Consecutivo`,`TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_07_Factura_Factura_Vendedor_Codigo`,`TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_07_Factura_TB_03_Cliente_Cliente_Cedula`), ADD KEY `fk_TB_19_Deposito_TB_22_Banco1_idx` (`TB_22_Banco_Banco_Codigo`);

--
-- Indices de la tabla `tb_20_descuento_familia`
--
ALTER TABLE `tb_20_descuento_familia`
 ADD PRIMARY KEY (`Descuento_familia_id`,`TB_05_Familia_Familia_Codigo`,`TB_05_Familia_TB_02_Sucursal_Codigo`,`TB_03_Cliente_Cliente_Cedula`,`TB_02_Sucursal_Codigo`), ADD KEY `fk_TB_20_Descuento_familia_TB_05_Familia1_idx` (`TB_05_Familia_Familia_Codigo`,`TB_05_Familia_TB_02_Sucursal_Codigo`), ADD KEY `fk_TB_20_Descuento_Familia_TB_03_Cliente1_idx` (`TB_03_Cliente_Cliente_Cedula`), ADD KEY `fk_TB_20_Descuento_Familia_TB_02_Sucursal1_idx` (`TB_02_Sucursal_Codigo`);

--
-- Indices de la tabla `tb_21_descuento_cliente`
--
ALTER TABLE `tb_21_descuento_cliente`
 ADD PRIMARY KEY (`Descuento_cliente_id`,`TB_03_Cliente_Cliente_Cedula`,`TB_02_Sucursal_Codigo`), ADD KEY `fk_TB_24_Descuento_Cliente_TB_03_Cliente1_idx` (`TB_03_Cliente_Cliente_Cedula`), ADD KEY `fk_TB_24_Descuento_Cliente_TB_02_Sucursal1_idx` (`TB_02_Sucursal_Codigo`);

--
-- Indices de la tabla `tb_22_banco`
--
ALTER TABLE `tb_22_banco`
 ADD PRIMARY KEY (`Banco_Codigo`);

--
-- Indices de la tabla `tb_23_mixto`
--
ALTER TABLE `tb_23_mixto`
 ADD PRIMARY KEY (`Mixto_Id`,`TB_18_Tarjeta_Tarjeta_Id`,`TB_18_Tarjeta_TB_07_Factura_Factura_Consecutivo`,`TB_18_Tarjeta_TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Codigo`,`TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_18_Tarjeta_TB_07_Factura_TB_03_Cliente_Cliente_Cedula`,`TB_18_Tarjeta_TB_22_Banco_Banco_Codigo`), ADD KEY `fk_TB_23_Mixto_TB_18_Tarjeta1_idx` (`TB_18_Tarjeta_Tarjeta_Id`,`TB_18_Tarjeta_TB_07_Factura_Factura_Consecutivo`,`TB_18_Tarjeta_TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Codigo`,`TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_18_Tarjeta_TB_07_Factura_TB_03_Cliente_Cliente_Cedula`,`TB_18_Tarjeta_TB_22_Banco_Banco_Codigo`);

--
-- Indices de la tabla `tb_24_credito`
--
ALTER TABLE `tb_24_credito`
 ADD PRIMARY KEY (`Credito_Id`,`Credito_Factura_Consecutivo`,`Credito_Sucursal_Codigo`,`Credito_Vendedor_Codigo`,`Credito_Vendedor_Sucursal`,`Credito_Cliente_Cedula`), ADD KEY `fk_TB_24_Credito_TB_07_Factura1_idx` (`Credito_Factura_Consecutivo`,`Credito_Sucursal_Codigo`,`Credito_Vendedor_Codigo`,`Credito_Vendedor_Sucursal`,`Credito_Cliente_Cedula`);

--
-- Indices de la tabla `tb_25_maximo_credito_cliente`
--
ALTER TABLE `tb_25_maximo_credito_cliente`
 ADD PRIMARY KEY (`Credito_Cliente_Id`,`TB_03_Cliente_Cliente_Cedula`,`TB_02_Sucursal_Codigo`), ADD KEY `fk_TB_25_Maximo_Credito_Cliente_TB_03_Cliente1_idx` (`TB_03_Cliente_Cliente_Cedula`), ADD KEY `fk_TB_25_Maximo_Credito_Cliente_TB_02_Sucursal1_idx` (`TB_02_Sucursal_Codigo`);

--
-- Indices de la tabla `tb_26_recibos_dinero`
--
ALTER TABLE `tb_26_recibos_dinero`
 ADD PRIMARY KEY (`Consecutivo`,`Credito`), ADD KEY `fk_TB_26_Recibos_Dinero_TB_24_Credito1_idx` (`Credito`);

--
-- Indices de la tabla `tb_27_notas_credito`
--
ALTER TABLE `tb_27_notas_credito`
 ADD PRIMARY KEY (`Consecutivo`,`Factura_Acreditar`,`Factura_Aplicar`,`Sucursal`,`Cliente`), ADD KEY `fk_TB_27_Notas_Credito_TB_07_Factura1_idx` (`Factura_Acreditar`), ADD KEY `fk_TB_27_Notas_Credito_TB_07_Factura2_idx` (`Factura_Aplicar`), ADD KEY `fk_TB_27_Notas_Credito_TB_02_Sucursal1_idx` (`Sucursal`), ADD KEY `fk_TB_27_Notas_Credito_TB_03_Cliente1_idx` (`Cliente`);

--
-- Indices de la tabla `tb_28_productos_notas_credito`
--
ALTER TABLE `tb_28_productos_notas_credito`
 ADD PRIMARY KEY (`Id`,`Nota_Credito_Consecutivo`,`Sucursal`), ADD KEY `fk_TB_28_Productos_Notas_Credito_TB_27_Notas_Credito1_idx` (`Nota_Credito_Consecutivo`), ADD KEY `fk_TB_28_Productos_Notas_Credito_TB_02_Sucursal1_idx` (`Sucursal`);

--
-- Indices de la tabla `tb_29_deposito_recibo`
--
ALTER TABLE `tb_29_deposito_recibo`
 ADD PRIMARY KEY (`Id`,`Recibo`,`Credito`), ADD KEY `fk_TB_29_Deposito_Recibo_TB_26_Recibos_Dinero1_idx` (`Recibo`,`Credito`);

--
-- Indices de la tabla `tb_30_notas_debito`
--
ALTER TABLE `tb_30_notas_debito`
 ADD PRIMARY KEY (`Consecutivo`,`Sucursal`,`Usuario`), ADD KEY `fk_TB_30_Notas_Debito_TB_02_Sucursal1_idx` (`Sucursal`), ADD KEY `fk_TB_30_Notas_Debito_TB_01_Usuario1_idx` (`Usuario`);

--
-- Indices de la tabla `tb_31_productos_notas_debito`
--
ALTER TABLE `tb_31_productos_notas_debito`
 ADD PRIMARY KEY (`Id`,`Nota_Debito_Consecutivo`,`Sucursal`,`Usuario`), ADD KEY `fk_TB_31_Productos_Notas_Debito_TB_30_Notas_Debito1_idx` (`Nota_Debito_Consecutivo`,`Sucursal`,`Usuario`);

--
-- Indices de la tabla `tb_32_tarjeta_recibos`
--
ALTER TABLE `tb_32_tarjeta_recibos`
 ADD PRIMARY KEY (`Id`,`Recibo`,`Credito`,`Banco`), ADD KEY `fk_TB_32_Tarjeta_Recibos_TB_26_Recibos_Dinero1_idx` (`Recibo`,`Credito`), ADD KEY `fk_TB_32_Tarjeta_Recibos_TB_22_Banco1_idx` (`Banco`);

--
-- Indices de la tabla `tb_33_retiros_parciales`
--
ALTER TABLE `tb_33_retiros_parciales`
 ADD PRIMARY KEY (`Id`), ADD KEY `fk_TB_33_Retiros_Parciales_TB_01_Usuario1_idx` (`Usuario`), ADD KEY `fk_TB_33_Retiros_Parciales_TB_02_Sucursal1_idx` (`Sucursal`);

--
-- Indices de la tabla `tb_34_bodega`
--
ALTER TABLE `tb_34_bodega`
 ADD PRIMARY KEY (`Codigo`), ADD KEY `fk_TB_34_Bodega_TB_01_Usuario1_idx` (`Usuario`), ADD KEY `fk_TB_34_Bodega_TB_02_Sucursal1_idx` (`Sucursal`);

--
-- Indices de la tabla `tb_35_cambio_codigo`
--
ALTER TABLE `tb_35_cambio_codigo`
 ADD PRIMARY KEY (`Id`), ADD KEY `fk_TB_35_Cambio_Codigo_TB_01_Usuario1_idx` (`Usuario`), ADD KEY `fk_TB_35_Cambio_Codigo_TB_02_Sucursal1_idx` (`Sucursal`);

--
-- Indices de la tabla `tb_36_articulos_cambio_codigo`
--
ALTER TABLE `tb_36_articulos_cambio_codigo`
 ADD PRIMARY KEY (`Id`), ADD KEY `fk_TB_36_Articulos_Cambio_Codigo_TB_35_Cambio_Codigo1_idx` (`Cambio_Codigo`);

--
-- Indices de la tabla `tb_37_cierre_caja`
--
ALTER TABLE `tb_37_cierre_caja`
 ADD PRIMARY KEY (`Id`), ADD KEY `fk_TB_37_Cierre_Caja_TB_02_Sucursal1_idx` (`Sucursal`), ADD KEY `fk_TB_37_Cierre_Caja_TB_01_Usuario1_idx` (`Usuario`);

--
-- Indices de la tabla `tb_38_moneda_cierre_caja`
--
ALTER TABLE `tb_38_moneda_cierre_caja`
 ADD PRIMARY KEY (`Id`), ADD KEY `fk_TB_38_Moneda_Cierre_Caja_TB_37_Cierre_Caja1_idx` (`Cierre_Caja`);

--
-- Indices de la tabla `tb_39_configuracion`
--
ALTER TABLE `tb_39_configuracion`
 ADD PRIMARY KEY (`Parametro`), ADD KEY `Parametro` (`Parametro`);

--
-- Indices de la tabla `tb_40_apartado`
--
ALTER TABLE `tb_40_apartado`
 ADD PRIMARY KEY (`Id`), ADD KEY `fk_TB_40_Apartado_TB_24_Credito1_idx` (`Credito`);

--
-- Indices de la tabla `tb_41_productos_factura_temporal`
--
ALTER TABLE `tb_41_productos_factura_temporal`
 ADD PRIMARY KEY (`Id`), ADD KEY `fk_TB_41_Productos_Factura_Temporal_TB_02_Sucursal1_idx` (`Sucursal`), ADD KEY `fk_TB_41_Productos_Factura_Temporal_TB_06_Articulo1_idx` (`Codigo_Articulo`);

--
-- Indices de la tabla `tb_42_moneda_retiro_parcial`
--
ALTER TABLE `tb_42_moneda_retiro_parcial`
 ADD PRIMARY KEY (`Id`), ADD KEY `fk_TB_42_Moneda_Retiro_Parcial_TB_33_Retiros_Parciales1_idx` (`Retiro`);

--
-- Indices de la tabla `tb_43_deposito_recibo`
--
ALTER TABLE `tb_43_deposito_recibo`
 ADD PRIMARY KEY (`Id`), ADD KEY `fk_TB_43_Deposito_Recibo_TB_26_Recibos_Dinero1_idx` (`Recibo`);

--
-- Indices de la tabla `tb_44_traspaso_inventario`
--
ALTER TABLE `tb_44_traspaso_inventario`
 ADD PRIMARY KEY (`Id`,`Sucursal_Salida`,`Sucursal_Entrada`,`Usuario`,`Factura_Traspasada`), ADD KEY `fk_TB_44_Traspaso_Inventario_TB_02_Sucursal1_idx` (`Sucursal_Salida`), ADD KEY `fk_TB_44_Traspaso_Inventario_TB_02_Sucursal2_idx` (`Sucursal_Entrada`), ADD KEY `fk_TB_44_Traspaso_Inventario_TB_01_Usuario1_idx` (`Usuario`), ADD KEY `fk_TB_44_Traspaso_Inventario_TB_07_Factura1_idx` (`Factura_Traspasada`);

--
-- Indices de la tabla `tb_45_articulos_traspaso_inventario`
--
ALTER TABLE `tb_45_articulos_traspaso_inventario`
 ADD PRIMARY KEY (`Id`,`Traspaso`), ADD KEY `fk_TB_45_Articulos_Traspaso_Inventario_TB_44_Traspaso_Inven_idx` (`Traspaso`);

--
-- Indices de la tabla `tb_46_relacion_desampa`
--
ALTER TABLE `tb_46_relacion_desampa`
 ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tb_01_usuario`
--
ALTER TABLE `tb_01_usuario`
MODIFY `Usuario_Codigo` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT de la tabla `tb_04_articulos_proforma`
--
ALTER TABLE `tb_04_articulos_proforma`
MODIFY `Articulo_Proforma_Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tb_08_articulos_factura`
--
ALTER TABLE `tb_08_articulos_factura`
MODIFY `Articulo_Factura_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de la tabla `tb_09_compras`
--
ALTER TABLE `tb_09_compras`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `tb_11_precios`
--
ALTER TABLE `tb_11_precios`
MODIFY `Precio_Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=181;
--
-- AUTO_INCREMENT de la tabla `tb_12_transacciones`
--
ALTER TABLE `tb_12_transacciones`
MODIFY `Trans_Codigo` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=851;
--
-- AUTO_INCREMENT de la tabla `tb_13_cheque`
--
ALTER TABLE `tb_13_cheque`
MODIFY `Cheque_Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tb_15_permisos`
--
ALTER TABLE `tb_15_permisos`
MODIFY `Permisos_Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2130;
--
-- AUTO_INCREMENT de la tabla `tb_17_descuento_producto`
--
ALTER TABLE `tb_17_descuento_producto`
MODIFY `Descuento_producto_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tb_18_tarjeta`
--
ALTER TABLE `tb_18_tarjeta`
MODIFY `Tarjeta_Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tb_20_descuento_familia`
--
ALTER TABLE `tb_20_descuento_familia`
MODIFY `Descuento_familia_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tb_21_descuento_cliente`
--
ALTER TABLE `tb_21_descuento_cliente`
MODIFY `Descuento_cliente_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `tb_22_banco`
--
ALTER TABLE `tb_22_banco`
MODIFY `Banco_Codigo` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `tb_23_mixto`
--
ALTER TABLE `tb_23_mixto`
MODIFY `Mixto_Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tb_24_credito`
--
ALTER TABLE `tb_24_credito`
MODIFY `Credito_Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla `tb_25_maximo_credito_cliente`
--
ALTER TABLE `tb_25_maximo_credito_cliente`
MODIFY `Credito_Cliente_Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `tb_26_recibos_dinero`
--
ALTER TABLE `tb_26_recibos_dinero`
MODIFY `Consecutivo` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tb_28_productos_notas_credito`
--
ALTER TABLE `tb_28_productos_notas_credito`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `tb_29_deposito_recibo`
--
ALTER TABLE `tb_29_deposito_recibo`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tb_31_productos_notas_debito`
--
ALTER TABLE `tb_31_productos_notas_debito`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tb_32_tarjeta_recibos`
--
ALTER TABLE `tb_32_tarjeta_recibos`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tb_33_retiros_parciales`
--
ALTER TABLE `tb_33_retiros_parciales`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tb_35_cambio_codigo`
--
ALTER TABLE `tb_35_cambio_codigo`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tb_36_articulos_cambio_codigo`
--
ALTER TABLE `tb_36_articulos_cambio_codigo`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tb_37_cierre_caja`
--
ALTER TABLE `tb_37_cierre_caja`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tb_38_moneda_cierre_caja`
--
ALTER TABLE `tb_38_moneda_cierre_caja`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tb_40_apartado`
--
ALTER TABLE `tb_40_apartado`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tb_41_productos_factura_temporal`
--
ALTER TABLE `tb_41_productos_factura_temporal`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tb_42_moneda_retiro_parcial`
--
ALTER TABLE `tb_42_moneda_retiro_parcial`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tb_43_deposito_recibo`
--
ALTER TABLE `tb_43_deposito_recibo`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tb_44_traspaso_inventario`
--
ALTER TABLE `tb_44_traspaso_inventario`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `tb_45_articulos_traspaso_inventario`
--
ALTER TABLE `tb_45_articulos_traspaso_inventario`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `tb_46_relacion_desampa`
--
ALTER TABLE `tb_46_relacion_desampa`
MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tb_01_usuario`
--
ALTER TABLE `tb_01_usuario`
ADD CONSTRAINT `fk_TB_01_Usuario_TB_02_Sucursal1` FOREIGN KEY (`TB_02_Sucursal_Codigo`) REFERENCES `tb_02_sucursal` (`Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_04_articulos_proforma`
--
ALTER TABLE `tb_04_articulos_proforma`
ADD CONSTRAINT `fk_TB_10_Proforma_has_TB_06_Articulo_TB_10_Proforma1` FOREIGN KEY (`TB_10_Proforma_Proforma_Consecutivo`, `TB_10_Proforma_TB_02_Sucursal_Codigo`, `TB_10_Proforma_Proforma_Vendedor_Codigo`, `TB_10_Proforma_Proforma_Vendedor_Sucursal`, `TB_10_Proforma_TB_03_Cliente_Cliente_Cedula`) REFERENCES `tb_10_proforma` (`Proforma_Consecutivo`, `TB_02_Sucursal_Codigo`, `Proforma_Vendedor_Codigo`, `Proforma_Vendedor_Sucursal`, `TB_03_Cliente_Cliente_Cedula`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_05_familia`
--
ALTER TABLE `tb_05_familia`
ADD CONSTRAINT `fk_TB_05_Familia_TB_02_Sucursal1` FOREIGN KEY (`TB_02_Sucursal_Codigo`) REFERENCES `tb_02_sucursal` (`Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_06_articulo`
--
ALTER TABLE `tb_06_articulo`
ADD CONSTRAINT `fk_TB_06_Articulo_TB_02_Sucursal1` FOREIGN KEY (`TB_02_Sucursal_Codigo`) REFERENCES `tb_02_sucursal` (`Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_06_Articulo_TB_05_Familia1` FOREIGN KEY (`TB_05_Familia_Familia_Codigo`) REFERENCES `tb_05_familia` (`Familia_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_07_factura`
--
ALTER TABLE `tb_07_factura`
ADD CONSTRAINT `fk_TB_07_Factura_TB_03_Cliente1` FOREIGN KEY (`TB_03_Cliente_Cliente_Cedula`) REFERENCES `tb_03_cliente` (`Cliente_Cedula`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_08_articulos_factura`
--
ALTER TABLE `tb_08_articulos_factura`
ADD CONSTRAINT `fk_TB_08_Articulos_Factura_TB_07_Factura1` FOREIGN KEY (`TB_07_Factura_Factura_Consecutivo`, `TB_07_Factura_TB_02_Sucursal_Codigo`, `TB_07_Factura_Factura_Vendedor_Codigo`, `TB_07_Factura_Factura_Vendedor_Sucursal`, `TB_07_Factura_TB_03_Cliente_Cliente_Cedula`) REFERENCES `tb_07_factura` (`Factura_Consecutivo`, `TB_02_Sucursal_Codigo`, `Factura_Vendedor_Codigo`, `Factura_Vendedor_Sucursal`, `TB_03_Cliente_Cliente_Cedula`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_09_compras`
--
ALTER TABLE `tb_09_compras`
ADD CONSTRAINT `fk_TB_09_Bodega_TB_01_Usuario1` FOREIGN KEY (`Usuario`) REFERENCES `tb_01_usuario` (`Usuario_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_09_Bodega_TB_02_Sucursal1` FOREIGN KEY (`Sucursal`) REFERENCES `tb_02_sucursal` (`Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_10_proforma`
--
ALTER TABLE `tb_10_proforma`
ADD CONSTRAINT `fk_TB_10_Proforma_TB_01_Usuario1` FOREIGN KEY (`Proforma_Vendedor_Codigo`, `Proforma_Vendedor_Sucursal`) REFERENCES `tb_01_usuario` (`Usuario_Codigo`, `TB_02_Sucursal_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_10_Proforma_TB_02_Sucursal1` FOREIGN KEY (`TB_02_Sucursal_Codigo`) REFERENCES `tb_02_sucursal` (`Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_10_Proforma_TB_03_Cliente1` FOREIGN KEY (`TB_03_Cliente_Cliente_Cedula`) REFERENCES `tb_03_cliente` (`Cliente_Cedula`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_11_precios`
--
ALTER TABLE `tb_11_precios`
ADD CONSTRAINT `fk_TB_11_Precios_TB_06_Articulo1` FOREIGN KEY (`TB_06_Articulo_Articulo_Codigo`, `TB_06_Articulo_TB_05_Familia_Familia_Codigo`, `TB_06_Articulo_TB_02_Sucursal_Codigo`) REFERENCES `tb_06_articulo` (`Articulo_Codigo`, `TB_05_Familia_Familia_Codigo`, `TB_02_Sucursal_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_12_transacciones`
--
ALTER TABLE `tb_12_transacciones`
ADD CONSTRAINT `fk_TB_12_Transacciones_TB_01_Usuario1` FOREIGN KEY (`TB_01_Usuario_Usuario_Codigo`, `TB_01_Usuario_TB_02_Sucursal_Codigo`) REFERENCES `tb_01_usuario` (`Usuario_Codigo`, `TB_02_Sucursal_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_13_cheque`
--
ALTER TABLE `tb_13_cheque`
ADD CONSTRAINT `fk_TB_13_Cheque_TB_07_Factura1` FOREIGN KEY (`TB_07_Factura_Factura_Consecutivo`, `TB_07_Factura_TB_02_Sucursal_Codigo`, `TB_07_Factura_Factura_Vendedor_Codigo`, `TB_07_Factura_Factura_Vendedor_Sucursal`, `TB_07_Factura_TB_03_Cliente_Cliente_Cedula`) REFERENCES `tb_07_factura` (`Factura_Consecutivo`, `TB_02_Sucursal_Codigo`, `Factura_Vendedor_Codigo`, `Factura_Vendedor_Sucursal`, `TB_03_Cliente_Cliente_Cedula`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_13_Cheque_TB_22_Banco1` FOREIGN KEY (`Banco`)  REFERENCES `TB_22_Banco` (`Banco_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;
--
-- Filtros para la tabla `tb_15_permisos`
--
ALTER TABLE `tb_15_permisos`
ADD CONSTRAINT `fk_TB_15_Permisos_TB_01_Usuario1` FOREIGN KEY (`TB_01_Usuario_Usuario_Codigo`, `TB_01_Usuario_TB_02_Sucursal_Codigo`) REFERENCES `tb_01_usuario` (`Usuario_Codigo`, `TB_02_Sucursal_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_16_authclientes`
--
ALTER TABLE `tb_16_authclientes`
ADD CONSTRAINT `fk_TB_16_AuthClientes_TB_03_Cliente1` FOREIGN KEY (`TB_03_Cliente_Cliente_Cedula`) REFERENCES `tb_03_cliente` (`Cliente_Cedula`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_17_descuento_producto`
--
ALTER TABLE `tb_17_descuento_producto`
ADD CONSTRAINT `fk_TB_21_Descuento_Producto_TB_02_Sucursal1` FOREIGN KEY (`TB_02_Sucursal_Codigo`) REFERENCES `tb_02_sucursal` (`Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_21_Descuento_Producto_TB_03_Cliente1` FOREIGN KEY (`TB_03_Cliente_Cliente_Cedula`) REFERENCES `tb_03_cliente` (`Cliente_Cedula`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_21_Descuento_Producto_TB_06_Articulo1` FOREIGN KEY (`TB_06_Articulo_Articulo_Codigo`, `TB_06_Articulo_TB_05_Familia_Familia_Codigo`) REFERENCES `tb_06_articulo` (`Articulo_Codigo`, `TB_05_Familia_Familia_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_18_tarjeta`
--
ALTER TABLE `tb_18_tarjeta`
ADD CONSTRAINT `fk_TB_18_Tarjeta_TB_07_Factura1` FOREIGN KEY (`TB_07_Factura_Factura_Consecutivo`, `TB_07_Factura_TB_02_Sucursal_Codigo`, `TB_07_Factura_Factura_Vendedor_Codigo`, `TB_07_Factura_Factura_Vendedor_Sucursal`, `TB_07_Factura_TB_03_Cliente_Cliente_Cedula`) REFERENCES `tb_07_factura` (`Factura_Consecutivo`, `TB_02_Sucursal_Codigo`, `Factura_Vendedor_Codigo`, `Factura_Vendedor_Sucursal`, `TB_03_Cliente_Cliente_Cedula`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_18_Tarjeta_TB_22_Banco1` FOREIGN KEY (`TB_22_Banco_Banco_Codigo`) REFERENCES `tb_22_banco` (`Banco_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_19_deposito`
--
ALTER TABLE `tb_19_deposito`
ADD CONSTRAINT `fk_TB_19_Deposito_TB_07_Factura1` FOREIGN KEY (`TB_07_Factura_Factura_Consecutivo`, `TB_07_Factura_TB_02_Sucursal_Codigo`, `TB_07_Factura_Factura_Vendedor_Codigo`, `TB_07_Factura_Factura_Vendedor_Sucursal`, `TB_07_Factura_TB_03_Cliente_Cliente_Cedula`) REFERENCES `tb_07_factura` (`Factura_Consecutivo`, `TB_02_Sucursal_Codigo`, `Factura_Vendedor_Codigo`, `Factura_Vendedor_Sucursal`, `TB_03_Cliente_Cliente_Cedula`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_19_Deposito_TB_22_Banco1` FOREIGN KEY (`TB_22_Banco_Banco_Codigo`) REFERENCES `tb_22_banco` (`Banco_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_20_descuento_familia`
--
ALTER TABLE `tb_20_descuento_familia`
ADD CONSTRAINT `fk_TB_20_Descuento_Familia_TB_02_Sucursal1` FOREIGN KEY (`TB_02_Sucursal_Codigo`) REFERENCES `tb_02_sucursal` (`Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_20_Descuento_Familia_TB_03_Cliente1` FOREIGN KEY (`TB_03_Cliente_Cliente_Cedula`) REFERENCES `tb_03_cliente` (`Cliente_Cedula`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_20_Descuento_familia_TB_05_Familia1` FOREIGN KEY (`TB_05_Familia_Familia_Codigo`, `TB_05_Familia_TB_02_Sucursal_Codigo`) REFERENCES `tb_05_familia` (`Familia_Codigo`, `TB_02_Sucursal_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_21_descuento_cliente`
--
ALTER TABLE `tb_21_descuento_cliente`
ADD CONSTRAINT `fk_TB_24_Descuento_Cliente_TB_02_Sucursal1` FOREIGN KEY (`TB_02_Sucursal_Codigo`) REFERENCES `tb_02_sucursal` (`Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_24_Descuento_Cliente_TB_03_Cliente1` FOREIGN KEY (`TB_03_Cliente_Cliente_Cedula`) REFERENCES `tb_03_cliente` (`Cliente_Cedula`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_23_mixto`
--
ALTER TABLE `tb_23_mixto`
ADD CONSTRAINT `fk_TB_23_Mixto_TB_18_Tarjeta1` FOREIGN KEY (`TB_18_Tarjeta_Tarjeta_Id`, `TB_18_Tarjeta_TB_07_Factura_Factura_Consecutivo`, `TB_18_Tarjeta_TB_07_Factura_TB_02_Sucursal_Codigo`, `TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Codigo`, `TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Sucursal`, `TB_18_Tarjeta_TB_07_Factura_TB_03_Cliente_Cliente_Cedula`, `TB_18_Tarjeta_TB_22_Banco_Banco_Codigo`) REFERENCES `tb_18_tarjeta` (`Tarjeta_Id`, `TB_07_Factura_Factura_Consecutivo`, `TB_07_Factura_TB_02_Sucursal_Codigo`, `TB_07_Factura_Factura_Vendedor_Codigo`, `TB_07_Factura_Factura_Vendedor_Sucursal`, `TB_07_Factura_TB_03_Cliente_Cliente_Cedula`, `TB_22_Banco_Banco_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_24_credito`
--
ALTER TABLE `tb_24_credito`
ADD CONSTRAINT `fk_TB_24_Credito_TB_07_Factura1` FOREIGN KEY (`Credito_Factura_Consecutivo`, `Credito_Sucursal_Codigo`, `Credito_Vendedor_Codigo`, `Credito_Vendedor_Sucursal`, `Credito_Cliente_Cedula`) REFERENCES `tb_07_factura` (`Factura_Consecutivo`, `TB_02_Sucursal_Codigo`, `Factura_Vendedor_Codigo`, `Factura_Vendedor_Sucursal`, `TB_03_Cliente_Cliente_Cedula`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_25_maximo_credito_cliente`
--
ALTER TABLE `tb_25_maximo_credito_cliente`
ADD CONSTRAINT `fk_TB_25_Maximo_Credito_Cliente_TB_02_Sucursal1` FOREIGN KEY (`TB_02_Sucursal_Codigo`) REFERENCES `tb_02_sucursal` (`Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_25_Maximo_Credito_Cliente_TB_03_Cliente1` FOREIGN KEY (`TB_03_Cliente_Cliente_Cedula`) REFERENCES `tb_03_cliente` (`Cliente_Cedula`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_26_recibos_dinero`
--
ALTER TABLE `tb_26_recibos_dinero`
ADD CONSTRAINT `fk_TB_26_Recibos_Dinero_TB_24_Credito1` FOREIGN KEY (`Credito`) REFERENCES `tb_24_credito` (`Credito_Id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_27_notas_credito`
--
ALTER TABLE `tb_27_notas_credito`
ADD CONSTRAINT `fk_TB_27_Notas_Credito_TB_02_Sucursal1` FOREIGN KEY (`Sucursal`) REFERENCES `tb_02_sucursal` (`Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_27_Notas_Credito_TB_03_Cliente1` FOREIGN KEY (`Cliente`) REFERENCES `tb_03_cliente` (`Cliente_Cedula`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_27_Notas_Credito_TB_07_Factura1` FOREIGN KEY (`Factura_Acreditar`) REFERENCES `tb_07_factura` (`Factura_Consecutivo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_27_Notas_Credito_TB_07_Factura2` FOREIGN KEY (`Factura_Aplicar`) REFERENCES `tb_07_factura` (`Factura_Consecutivo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_28_productos_notas_credito`
--
ALTER TABLE `tb_28_productos_notas_credito`
ADD CONSTRAINT `fk_TB_28_Productos_Notas_Credito_TB_02_Sucursal1` FOREIGN KEY (`Sucursal`) REFERENCES `tb_02_sucursal` (`Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_28_Productos_Notas_Credito_TB_27_Notas_Credito1` FOREIGN KEY (`Nota_Credito_Consecutivo`) REFERENCES `tb_27_notas_credito` (`Consecutivo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_29_deposito_recibo`
--
ALTER TABLE `tb_29_deposito_recibo`
ADD CONSTRAINT `fk_TB_29_Deposito_Recibo_TB_26_Recibos_Dinero1` FOREIGN KEY (`Recibo`, `Credito`) REFERENCES `tb_26_recibos_dinero` (`Consecutivo`, `Credito`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_30_notas_debito`
--
ALTER TABLE `tb_30_notas_debito`
ADD CONSTRAINT `fk_TB_30_Notas_Debito_TB_01_Usuario1` FOREIGN KEY (`Usuario`) REFERENCES `tb_01_usuario` (`Usuario_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_30_Notas_Debito_TB_02_Sucursal1` FOREIGN KEY (`Sucursal`) REFERENCES `tb_02_sucursal` (`Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_31_productos_notas_debito`
--
ALTER TABLE `tb_31_productos_notas_debito`
ADD CONSTRAINT `fk_TB_31_Productos_Notas_Debito_TB_30_Notas_Debito1` FOREIGN KEY (`Nota_Debito_Consecutivo`, `Sucursal`, `Usuario`) REFERENCES `tb_30_notas_debito` (`Consecutivo`, `Sucursal`, `Usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_32_tarjeta_recibos`
--
ALTER TABLE `tb_32_tarjeta_recibos`
ADD CONSTRAINT `fk_TB_32_Tarjeta_Recibos_TB_22_Banco1` FOREIGN KEY (`Banco`) REFERENCES `tb_22_banco` (`Banco_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_32_Tarjeta_Recibos_TB_26_Recibos_Dinero1` FOREIGN KEY (`Recibo`, `Credito`) REFERENCES `tb_26_recibos_dinero` (`Consecutivo`, `Credito`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_33_retiros_parciales`
--
ALTER TABLE `tb_33_retiros_parciales`
ADD CONSTRAINT `fk_TB_33_Retiros_Parciales_TB_01_Usuario1` FOREIGN KEY (`Usuario`) REFERENCES `tb_01_usuario` (`Usuario_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_33_Retiros_Parciales_TB_02_Sucursal1` FOREIGN KEY (`Sucursal`) REFERENCES `tb_02_sucursal` (`Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_34_bodega`
--
ALTER TABLE `tb_34_bodega`
ADD CONSTRAINT `fk_TB_34_Bodega_TB_01_Usuario1` FOREIGN KEY (`Usuario`) REFERENCES `tb_01_usuario` (`Usuario_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_34_Bodega_TB_02_Sucursal1` FOREIGN KEY (`Sucursal`) REFERENCES `tb_02_sucursal` (`Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_35_cambio_codigo`
--
ALTER TABLE `tb_35_cambio_codigo`
ADD CONSTRAINT `fk_TB_35_Cambio_Codigo_TB_01_Usuario1` FOREIGN KEY (`Usuario`) REFERENCES `tb_01_usuario` (`Usuario_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_35_Cambio_Codigo_TB_02_Sucursal1` FOREIGN KEY (`Sucursal`) REFERENCES `tb_02_sucursal` (`Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_36_articulos_cambio_codigo`
--
ALTER TABLE `tb_36_articulos_cambio_codigo`
ADD CONSTRAINT `fk_TB_36_Articulos_Cambio_Codigo_TB_35_Cambio_Codigo1` FOREIGN KEY (`Cambio_Codigo`) REFERENCES `tb_35_cambio_codigo` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_37_cierre_caja`
--
ALTER TABLE `tb_37_cierre_caja`
ADD CONSTRAINT `fk_TB_37_Cierre_Caja_TB_01_Usuario1` FOREIGN KEY (`Usuario`) REFERENCES `tb_01_usuario` (`Usuario_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_37_Cierre_Caja_TB_02_Sucursal1` FOREIGN KEY (`Sucursal`) REFERENCES `tb_02_sucursal` (`Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_38_moneda_cierre_caja`
--
ALTER TABLE `tb_38_moneda_cierre_caja`
ADD CONSTRAINT `fk_TB_38_Moneda_Cierre_Caja_TB_37_Cierre_Caja1` FOREIGN KEY (`Cierre_Caja`) REFERENCES `tb_37_cierre_caja` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_40_apartado`
--
ALTER TABLE `tb_40_apartado`
ADD CONSTRAINT `fk_TB_40_Apartado_TB_24_Credito1` FOREIGN KEY (`Credito`) REFERENCES `tb_24_credito` (`Credito_Id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_41_productos_factura_temporal`
--
ALTER TABLE `tb_41_productos_factura_temporal`
ADD CONSTRAINT `fk_TB_41_Productos_Factura_Temporal_TB_02_Sucursal1` FOREIGN KEY (`Sucursal`) REFERENCES `tb_02_sucursal` (`Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_41_Productos_Factura_Temporal_TB_06_Articulo1` FOREIGN KEY (`Codigo_Articulo`) REFERENCES `tb_06_articulo` (`Articulo_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_42_moneda_retiro_parcial`
--
ALTER TABLE `tb_42_moneda_retiro_parcial`
ADD CONSTRAINT `fk_TB_42_Moneda_Retiro_Parcial_TB_33_Retiros_Parciales1` FOREIGN KEY (`Retiro`) REFERENCES `tb_33_retiros_parciales` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_43_deposito_recibo`
--
ALTER TABLE `tb_43_deposito_recibo`
ADD CONSTRAINT `fk_TB_43_Deposito_Recibo_TB_26_Recibos_Dinero1` FOREIGN KEY (`Recibo`) REFERENCES `tb_26_recibos_dinero` (`Consecutivo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_44_traspaso_inventario`
--
ALTER TABLE `tb_44_traspaso_inventario`
ADD CONSTRAINT `fk_TB_44_Traspaso_Inventario_TB_01_Usuario1` FOREIGN KEY (`Usuario`) REFERENCES `tb_01_usuario` (`Usuario_Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_44_Traspaso_Inventario_TB_02_Sucursal1` FOREIGN KEY (`Sucursal_Salida`) REFERENCES `tb_02_sucursal` (`Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_44_Traspaso_Inventario_TB_02_Sucursal2` FOREIGN KEY (`Sucursal_Entrada`) REFERENCES `tb_02_sucursal` (`Codigo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `fk_TB_44_Traspaso_Inventario_TB_07_Factura1` FOREIGN KEY (`Factura_Traspasada`) REFERENCES `tb_07_factura` (`Factura_Consecutivo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tb_45_articulos_traspaso_inventario`
--
ALTER TABLE `tb_45_articulos_traspaso_inventario`
ADD CONSTRAINT `fk_TB_45_Articulos_Traspaso_Inventario_TB_44_Traspaso_Inventa1` FOREIGN KEY (`Traspaso`) REFERENCES `tb_44_traspaso_inventario` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

CREATE TABLE IF NOT EXISTS `TB_47_Descuento_Proforma` (
  `Id` INT NOT NULL,
  `Proforma` INT NOT NULL,
  `Sucursal` INT NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `fk_TB_47_Descuento_Proforma_TB_10_Proforma1_idx` (`Proforma` ASC),
  INDEX `fk_TB_47_Descuento_Proforma_TB_02_Sucursal1_idx` (`Sucursal` ASC),
  CONSTRAINT `fk_TB_47_Descuento_Proforma_TB_10_Proforma1`
    FOREIGN KEY (`Proforma`)
    REFERENCES `TB_10_Proforma` (`Proforma_Consecutivo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_TB_47_Descuento_Proforma_TB_02_Sucursal1`
    FOREIGN KEY (`Sucursal`)
    REFERENCES `TB_02_Sucursal` (`Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tb_26_recibos_dinero` ADD `Comentarios` VARCHAR(1000) NOT NULL AFTER `Tipo_Pago`;

CREATE TABLE IF NOT EXISTS `TB_48_Relacion_Sucursal_Cliente` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Sucursal` INT NOT NULL,
  `Cliente` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `fk_TB_48_Relacion_Sucursal_Cliente_TB_02_Sucursal1_idx` (`Sucursal` ASC),
  INDEX `fk_TB_48_Relacion_Sucursal_Cliente_TB_03_Cliente1_idx` (`Cliente` ASC),
  CONSTRAINT `fk_TB_48_Relacion_Sucursal_Cliente_TB_02_Sucursal1`
    FOREIGN KEY (`Sucursal`)
    REFERENCES `TB_02_Sucursal` (`Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_TB_48_Relacion_Sucursal_Cliente_TB_03_Cliente1`
    FOREIGN KEY (`Cliente`)
    REFERENCES `TB_03_Cliente` (`Cliente_Cedula`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `TB_49_Consignacion` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Fecha_Hora` TIMESTAMP NULL,
  `Porcentaje_IVA` DOUBLE NULL,
  `IVA` DOUBLE NULL,
  `Retencion` DOUBLE NULL,
  `Costo` DOUBLE NULL,
  `Total` DOUBLE NULL,
  `Sucursal_Recibe_Exenta` INT NULL,
  `Sucursal_Recibe_No_Retencion` INT NULL,
  `Usuario` INT NOT NULL,
  `Sucursal_Entrega` INT NOT NULL,
  `Sucursal_Recibe` INT NOT NULL,
  `Sucursal_Recibe_Cliente_Liga` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `fk_TB_49_Consignacion_TB_01_Usuario1_idx` (`Usuario` ASC),
  INDEX `fk_TB_49_Consignacion_TB_02_Sucursal1_idx` (`Sucursal_Entrega` ASC),
  INDEX `fk_TB_49_Consignacion_TB_02_Sucursal2_idx` (`Sucursal_Recibe` ASC),
  CONSTRAINT `fk_TB_49_Consignacion_TB_01_Usuario1`
    FOREIGN KEY (`Usuario`)
    REFERENCES `TB_01_Usuario` (`Usuario_Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_TB_49_Consignacion_TB_02_Sucursal1`
    FOREIGN KEY (`Sucursal_Entrega`)
    REFERENCES `TB_02_Sucursal` (`Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_TB_49_Consignacion_TB_02_Sucursal2`
    FOREIGN KEY (`Sucursal_Recibe`)
    REFERENCES `TB_02_Sucursal` (`Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `TB_50_Articulos_Consignacion` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Codigo` VARCHAR(30) NULL,
  `Descripcion` VARCHAR(45) NULL,
  `Cantidad` INT NULL,
  `Descuento` DOUBLE NULL,
  `Precio_Unidad` DOUBLE NULL,
  `Precio_Total` DOUBLE NULL,
  `Exento` TINYINT(1) NULL,
  `Retencion` TINYINT(1) NULL,
  `Imagen` VARCHAR(45) NULL,
  `Consignacion` INT NULL,
  PRIMARY KEY (`Id`),
  INDEX `fk_TB_50_Articulos_Consignacion_TB_49_Consignacion1_idx` (`Consignacion` ASC),
  CONSTRAINT `fk_TB_50_Articulos_Consignacion_TB_49_Consignacion1`
    FOREIGN KEY (`Consignacion`)
    REFERENCES `TB_49_Consignacion` (`Id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `TB_51_Lista_Consignacion` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Codigo` VARCHAR(30) NULL,
  `Descripcion` VARCHAR(45) NULL,
  `Cantidad` INT NULL,
  `Descuento` DOUBLE NULL,
  `Precio_Unidad` DOUBLE NULL,
  `Precio_Total` DOUBLE NULL,
  `Exento` TINYINT(1) NULL,
  `Retencion` TINYINT(1) NULL,
  `Imagen` VARCHAR(45) NULL,
  `Sucursal_Entrega` INT NOT NULL,
  `Sucursal_Recibe` INT NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `fk_TB_50_Lista_Consignacion_TB_02_Sucursal1_idx` (`Sucursal_Entrega` ASC),
  INDEX `fk_TB_50_Lista_Consignacion_TB_02_Sucursal2_idx` (`Sucursal_Recibe` ASC),
  CONSTRAINT `fk_TB_50_Lista_Consignacion_TB_02_Sucursal1`
    FOREIGN KEY (`Sucursal_Entrega`)
    REFERENCES `TB_02_Sucursal` (`Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_TB_50_Lista_Consignacion_TB_02_Sucursal2`
    FOREIGN KEY (`Sucursal_Recibe`)
    REFERENCES `TB_02_Sucursal` (`Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tb_03_cliente` (`Cliente_Cedula`, `Cliente_Nombre`, `Cliente_Apellidos`, `Cliente_Tipo_Cedula`, `Cliente_Carnet_Numero`, `Cliente_Celular`, `Cliente_Telefono`, `Cliente_Fecha_Ingreso`, `Cliente_Pais`, `Cliente_Direccion`, `Cliente_Observaciones`, `Cliente_Imagen_URL`, `Cliente_Correo_Electronico`, `Cliente_Estado`, `Cliente_Calidad`, `Cliente_Numero_Pago`, `Cliente_EsSucursal`, `Cliente_EsExento`, `Aplica_Retencion`) VALUES ('2', 'Cliente Contado', 'Defectuoso', 'nacional', '0', NULL, NULL, '2015-12-09 00:00:00', 'Costa Rica', 'San Jose', NULL, NULL, NULL, 'activo', '5', '0', '0', '0', '0');

ALTER TABLE `tb_51_lista_consignacion` ADD `Precio_Final` DOUBLE NOT NULL AFTER `Precio_Total`;

ALTER TABLE `tb_50_articulos_consignacion` ADD `Precio_Final` DOUBLE NOT NULL AFTER `Precio_Total`;