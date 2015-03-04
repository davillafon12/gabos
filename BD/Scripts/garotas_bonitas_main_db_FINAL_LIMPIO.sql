-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-01-2015 a las 04:34:48
-- Versión del servidor: 5.6.16
-- Versión de PHP: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `garotas_bonitas_main_db`
--
CREATE DATABASE IF NOT EXISTS `garotas_bonitas_main_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `garotas_bonitas_main_db`;



-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_02_sucursal`
--

CREATE TABLE IF NOT EXISTS `tb_02_sucursal` (
  `Codigo` int(11) NOT NULL,
  `Sucursal_Cedula` varchar(100) NOT NULL,
  `Sucursal_Nombre` varchar(20) DEFAULT NULL,
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
  `Sucursal_leyenda_tributacion` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_02_sucursal`
--

INSERT INTO `tb_02_sucursal` (`Codigo`, `Sucursal_Cedula`, `Sucursal_Nombre`, `Sucursal_Telefono`, `Sucursal_Fax`, `Sucursal_Email`, `Sucursal_Direccion`, `Sucursal_Observaciones`, `Sucursal_Fecha_Ingreso`, `Sucursal_Fecha_Desactivacion`, `Sucursal_Creador`, `Sucursal_Estado`, `Sucursal_Administrador`, `Sucursal_leyenda_tributacion`) VALUES
(0, '3-101-350785', 'Inversiones Garotas ', '2221-8127', '2223-4870', 'garotasbonitas@ice.co.cr', 'Avenida Segunda, Edificio Las Arcadas 4to piso, Frente al Ministerio de Hacienda', 'Testing', '2014-04-03 14:30:13', NULL, 'David_test', 1, 'Sin Definir', 'Autorizado mediante resolución No.11-97 de la D.G.T.D de fecha: 05/09/1997. La Gaceta No. 171');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_01_usuario`
--

CREATE TABLE IF NOT EXISTS `tb_01_usuario` (
  `Usuario_Codigo` int(11) NOT NULL AUTO_INCREMENT,
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
  `TB_02_Sucursal_Codigo` int(11) NOT NULL,
  PRIMARY KEY (`Usuario_Codigo`,`TB_02_Sucursal_Codigo`),
  KEY `fk_TB_01_Usuario_TB_02_Sucursal1_idx` (`TB_02_Sucursal_Codigo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Volcado de datos para la tabla `tb_01_usuario`
--

INSERT INTO `tb_01_usuario` (`Usuario_Codigo`, `Usuario_Nombre`, `Usuario_Apellidos`, `Usuario_Cedula`, `Usuario_Tipo_Cedula`, `Usuario_Celular`, `Usuario_Telefono`, `Usuario_Fecha_Ingreso`, `Usuario_Fecha_Cesantia`, `Usuario_Fecha_Recontratacion`, `Usuario_Nombre_Usuario`, `Usuario_Observaciones`, `Usuario_Password`, `Usuario_Imagen_URL`, `Usuario_Correo_Electronico`, `Usuario_Rango`, `TB_02_Sucursal_Codigo`) VALUES
(1, 'David', 'Villalobos Fonseca', 402040954, 'Nacional', '8327-5345', '2268-8368', '2014-02-02 00:00:00', NULL, NULL, 'David_test', 'Usuario testing', '49ff630e0642355953dece12a68da694', '402040954_0.png', 'davillafon12@gmail.com', 'avanzado', 0),
(2, 'Siviany', 'Prendas Zamora', 401240584, 'Nacional', '85427454', '22374585', '2014-02-13 00:00:00', NULL, NULL, 'Siviany_Test', 'Usuario testing', '21232f297a57a5a743894a0e4a801fc3', '2.png', 'sprendas88@gmail.com', 'avanzado', 0),
(3, 'David ', 'Villalobos', 402040952, 'nacional', '5555-5555', '6666-6666', '2014-07-17 00:00:00', NULL, '2014-10-16 06:00:00', 'villa', '', 'ed3f29fee9e28e36f7cdc8115e0642e9', '402040952_0.png', 'david@admin.cr', 'administra', 0),
(4, 'Esteban', 'Prendas Zamora', 112010752, 'nacional', '8411-6368', '2278-5429', '2014-12-16 17:56:27', NULL, NULL, 'eprendas', '', 'c610d7eb88ac759067eac5a61b4957cc', 'Default.png', 'esteban@garotasbonitascr.com', 'avanzado', 0),
(5, 'JUAN', 'VARGAS BARQUERO', 304860001, 'nacional', '8661-4624', '', '2014-12-16 22:46:23', NULL, NULL, 'jvargas', '', '6ea6df9407ae14873ffbfca3fd06ee03', 'Default.png', '', 'cajero', 0),
(6, 'jose pablo', 'bonilla castillo', 113500583, 'nacional', '8775-1440', '8775-1440', '2015-01-03 18:38:41', NULL, NULL, 'jbonilla', '', 'ce871ab21a6c7ba65b04461eab26f166', 'Default.png', '', 'vendedor', 0),
(7, 'ISMAEL OMAR', 'GUTIERREZ PEREZ', 114130256, 'nacional', '6173-3716', '2230-8812', '2015-01-03 18:51:38', NULL, NULL, 'igutierrez', '', 'c49931740ae86dd7e14ebd236a1b371b', 'Default.png', 'isma_35@live.com', 'vendedor', 0),
(8, 'DIGNA', 'QUIROS CARVAJAL', 301900516, 'nacional', '8336-8704', '2254-7793', '2015-01-03 18:55:57', NULL, NULL, 'dquiros', '', 'dd5812769bed285f793b53c94aa436e5', 'Default.png', 'digna.quiros@gmail.com', 'vendedor', 0),
(9, 'KEVIN', 'SANDOVAL ROJAS', 114510504, 'nacional', '7007-5997', '7007-5997', '2015-01-03 19:22:19', NULL, NULL, 'ksandoval', '', '1755fee3c0c83123560e782ec56d5205', 'Default.png', '', 'vendedor', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_03_cliente`
--

CREATE TABLE IF NOT EXISTS `tb_03_cliente` (
  `Cliente_Cedula` bigint(20) NOT NULL,
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
  PRIMARY KEY (`Cliente_Cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_03_cliente`
--

INSERT INTO `tb_03_cliente` (`Cliente_Cedula`, `Cliente_Nombre`, `Cliente_Apellidos`, `Cliente_Tipo_Cedula`, `Cliente_Carnet_Numero`, `Cliente_Celular`, `Cliente_Telefono`, `Cliente_Fecha_Ingreso`, `Cliente_Pais`, `Cliente_Direccion`, `Cliente_Observaciones`, `Cliente_Imagen_URL`, `Cliente_Correo_Electronico`, `Cliente_Estado`, `Cliente_Calidad`, `Cliente_Numero_Pago`, `Cliente_EsSucursal`, `Cliente_EsExento`) VALUES
(0, 'Cliente Contado', 'Afiliado', 'nacional', 0, '', '', '0000-00-00 00:00:00', 'Costa Rica', 'San Jose', '', '', '', 'activo', 5, 1, 0, 0),
(1, 'Cliente Contado', 'Corriente', 'nacional', 0, '', '', '0000-00-00 00:00:00', 'Costa Rica', 'San Jose', '', '', '', 'activo', 5, 2, 0, 0),
(103050183, 'ANITA', 'ROBLES MENA', 'nacional', 0, '8534-3411', '2292-6949', '2014-12-16 23:05:11', 'CR', 'IPIS', '', 'Default.png', '', 'activo', 5, 2, 0, 0),
(104320342, 'ELIZABETH', 'SANABRIA MADRIGAL', 'nacional', 0, '8420-0002', '2225-0946', '2014-12-16 22:57:53', 'CR', 'SAN PEDRO', '', 'Default.png', '', 'activo', 5, 2, 0, 0),
(106160183, 'MARIA YORLENY', 'ORTEGA BARBOZA', 'nacional', 0, '6044-7941', '6044-7941', '2014-12-16 23:07:07', 'CR', 'CONCEPCION ABAJO', '', 'Default.png', '', 'activo', 5, 2, 0, 0),
(106460698, 'OLGA ', 'AVILA ROJAS', 'nacional', 0, '8668-1249', '2270-5200', '2014-12-17 22:47:57', 'CR', 'HIGUITO', '', 'Default.png', '', 'activo', 5, 2, 0, 0),
(108930163, 'BEATRIZ', 'QUIROS ALFARO', 'nacional', 0, '8849-2405', '2274-1452', '2014-12-16 23:00:30', 'CR', 'PATARRA', '', 'Default.png', '', 'activo', 5, 2, 0, 0),
(109930888, 'IVANNIA', 'BOLAÑOS MAROTO', 'nacional', 3342, '8855-5206', '8855-5206', '2014-12-16 22:51:18', 'CR', 'SAN JOSE', '', 'Default.png', '', 'activo', 5, 2, 0, 0),
(113810630, 'ANDREA STEPHANIE', 'PORRAS ELIZONDO', 'nacional', 0, '8617-7149', '2417-0148', '2014-12-16 23:30:57', 'CR', 'PURISCAL', '', 'Default.png', '', 'activo', 5, 2, 0, 0),
(114450945, 'KENNIA SUSANA', 'PADILLA ARAYA', 'nacional', 0, '8771-6174', '8771-6174', '2014-12-16 23:16:40', 'CR', 'GUADALUPE', '', 'Default.png', '', 'activo', 5, 2, 0, 0),
(301900516, 'DIGNA', 'QUIROS CARVAJAL', 'nacional', 0, '8336-8704', '2254-7793', '2015-01-03 19:01:52', '', '50 METROS SUR SALON COMUNAL HATILLO 4.', 'EMPLEADO', 'Default.png', 'DIGNA.QUIROS@GMAIL.COM', 'semiactivo', 5, 2, 0, 0),
(321124553, 'EMMA', 'Watson ', 'nacional', 1002, '2225-5436', '1124-4112', '2014-04-16 06:00:00', 'Britania', 'nulo', '', '321124553.jpg', 'emawatson@hotmail.com', 'activo', 5, 1, 0, 0),
(501820700, 'MARIA MERCEDES', 'OBANDO OBANDO', 'nacional', 0, '8355-7633', '8355-7633', '2014-12-16 22:55:47', 'CR', 'LOMAS', '', 'Default.png', '', 'activo', 5, 2, 0, 0),
(155802012123, 'SONIA RAQUEL', 'MORALES ALMANZA', 'residencia', 0, '8302-0883', '8302-0288', '2014-12-16 23:45:39', 'NI', 'AURORA ALAJUELITA', '', 'Default.png', '', 'activo', 5, 2, 0, 0),
(155815848731, 'CLAUDIA ELIZABETH', 'OROZCO SOLIS', 'residencia', 0, '8402-0806', '8891-7369', '2014-12-16 23:35:23', 'NI', 'ALAJUELITA', '', 'Default.png', '', 'activo', 5, 2, 0, 0),
(155821928233, 'MARIA DEL CARMEN', 'GUTIERREZ LLANES', 'residencia', 0, '8431-6124', '8431-6124', '2014-12-16 23:43:40', 'NI', 'BARRIO LUJAN', '', 'Default.png', '', 'activo', 5, 2, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_04_articulos_proforma`
--

CREATE TABLE IF NOT EXISTS `tb_04_articulos_proforma` (
  `Articulo_Proforma_Id` int(11) NOT NULL AUTO_INCREMENT,
  `Articulo_Proforma_Codigo` varchar(30) DEFAULT NULL,
  `Articulo_Proforma_Descripcion` varchar(45) DEFAULT NULL,
  `Articulo_Proforma_Cantidad` int(11) DEFAULT NULL,
  `Articulo_Proforma_Descuento` varchar(45) DEFAULT NULL,
  `Articulo_Proforma_Exento` varchar(45) DEFAULT NULL,
  `Articulo_Proforma_Precio_Unitario` varchar(45) DEFAULT NULL,
  `Articulo_Proforma_Imagen` varchar(45) DEFAULT NULL,
  `TB_10_Proforma_Proforma_Consecutivo` int(11) NOT NULL,
  `TB_10_Proforma_TB_02_Sucursal_Codigo` int(11) NOT NULL,
  `TB_10_Proforma_Proforma_Vendedor_Codigo` int(11) NOT NULL,
  `TB_10_Proforma_Proforma_Vendedor_Sucursal` int(11) NOT NULL,
  `TB_10_Proforma_TB_03_Cliente_Cliente_Cedula` bigint(20) NOT NULL,
  PRIMARY KEY (`Articulo_Proforma_Id`,`TB_10_Proforma_Proforma_Consecutivo`,`TB_10_Proforma_TB_02_Sucursal_Codigo`,`TB_10_Proforma_Proforma_Vendedor_Codigo`,`TB_10_Proforma_Proforma_Vendedor_Sucursal`,`TB_10_Proforma_TB_03_Cliente_Cliente_Cedula`),
  KEY `fk_TB_10_Proforma_has_TB_06_Articulo_TB_10_Proforma1_idx` (`TB_10_Proforma_Proforma_Consecutivo`,`TB_10_Proforma_TB_02_Sucursal_Codigo`,`TB_10_Proforma_Proforma_Vendedor_Codigo`,`TB_10_Proforma_Proforma_Vendedor_Sucursal`,`TB_10_Proforma_TB_03_Cliente_Cliente_Cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `Familia_Estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`Familia_Codigo`,`TB_02_Sucursal_Codigo`),
  KEY `fk_TB_05_Familia_TB_02_Sucursal1_idx` (`TB_02_Sucursal_Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `TB_05_Familia_Familia_Codigo` int(11) NOT NULL,
  `TB_02_Sucursal_Codigo` int(11) NOT NULL,
  PRIMARY KEY (`Articulo_Codigo`,`TB_05_Familia_Familia_Codigo`,`TB_02_Sucursal_Codigo`),
  KEY `fk_TB_06_Articulo_TB_05_Familia1_idx` (`TB_05_Familia_Familia_Codigo`),
  KEY `fk_TB_06_Articulo_TB_02_Sucursal1_idx` (`TB_02_Sucursal_Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `Factura_Moneda` varchar(15) DEFAULT NULL,
  `Factura_porcentaje_iva` double DEFAULT NULL,
  `Factura_tipo_cambio` double DEFAULT NULL,
  `Factura_Nombre_Cliente` varchar(150) DEFAULT NULL,
  `TB_02_Sucursal_Codigo` int(11) NOT NULL,
  `Factura_Vendedor_Codigo` int(11) NOT NULL,
  `Factura_Vendedor_Sucursal` int(11) NOT NULL,
  `TB_03_Cliente_Cliente_Cedula` bigint(20) NOT NULL,
  PRIMARY KEY (`Factura_Consecutivo`,`TB_02_Sucursal_Codigo`,`Factura_Vendedor_Codigo`,`Factura_Vendedor_Sucursal`,`TB_03_Cliente_Cliente_Cedula`),
  KEY `fk_TB_07_Factura_TB_03_Cliente1_idx` (`TB_03_Cliente_Cliente_Cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_08_articulos_factura`
--

CREATE TABLE IF NOT EXISTS `tb_08_articulos_factura` (
  `Articulo_Factura_id` int(11) NOT NULL AUTO_INCREMENT,
  `Articulo_Factura_Codigo` varchar(30) DEFAULT NULL,
  `Articulo_Factura_Descripcion` varchar(45) DEFAULT NULL,
  `Articulo_Factura_Cantidad` int(11) DEFAULT NULL,
  `Articulo_Factura_Descuento` varchar(45) DEFAULT NULL,
  `Articulo_Factura_Exento` varchar(45) DEFAULT NULL,
  `Articulo_Factura_Precio_Unitario` varchar(45) DEFAULT NULL,
  `Articulo_Factura_Imagen` varchar(45) DEFAULT NULL,
  `TB_07_Factura_Factura_Consecutivo` int(11) NOT NULL,
  `TB_07_Factura_TB_02_Sucursal_Codigo` int(11) NOT NULL,
  `TB_07_Factura_Factura_Vendedor_Codigo` int(11) NOT NULL,
  `TB_07_Factura_Factura_Vendedor_Sucursal` int(11) NOT NULL,
  `TB_07_Factura_TB_03_Cliente_Cliente_Cedula` bigint(20) NOT NULL,
  PRIMARY KEY (`Articulo_Factura_id`,`TB_07_Factura_Factura_Consecutivo`,`TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_07_Factura_Factura_Vendedor_Codigo`,`TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_07_Factura_TB_03_Cliente_Cliente_Cedula`),
  KEY `fk_TB_08_Articulos_Factura_TB_07_Factura1_idx` (`TB_07_Factura_Factura_Consecutivo`,`TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_07_Factura_Factura_Vendedor_Codigo`,`TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_07_Factura_TB_03_Cliente_Cliente_Cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_09_compras`
--

CREATE TABLE IF NOT EXISTS `tb_09_compras` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Codigo` varchar(30) DEFAULT NULL,
  `Descripcion` varchar(150) DEFAULT NULL,
  `Costo` double DEFAULT NULL,
  `Cantidad` int(11) DEFAULT NULL,
  `Fecha_Ingreso` timestamp NULL DEFAULT NULL,
  `Usuario` int(11) NOT NULL,
  `Sucursal` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_TB_09_Bodega_TB_02_Sucursal1_idx` (`Sucursal`),
  KEY `fk_TB_09_Bodega_TB_01_Usuario1_idx` (`Usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `Proforma_Moneda` varchar(15) DEFAULT NULL,
  `Proforma_Porcentaje_IVA` double DEFAULT NULL,
  `Proforma_Tipo_Cambio` double DEFAULT NULL,
  `Proforma_Nombre_Cliente` varchar(150) DEFAULT NULL,
  `TB_02_Sucursal_Codigo` int(11) NOT NULL,
  `Proforma_Vendedor_Codigo` int(11) NOT NULL,
  `Proforma_Vendedor_Sucursal` int(11) NOT NULL,
  `TB_03_Cliente_Cliente_Cedula` bigint(20) NOT NULL,
  PRIMARY KEY (`Proforma_Consecutivo`,`TB_02_Sucursal_Codigo`,`Proforma_Vendedor_Codigo`,`Proforma_Vendedor_Sucursal`,`TB_03_Cliente_Cliente_Cedula`),
  KEY `fk_TB_10_Proforma_TB_02_Sucursal1_idx` (`TB_02_Sucursal_Codigo`),
  KEY `fk_TB_10_Proforma_TB_01_Usuario1_idx` (`Proforma_Vendedor_Codigo`,`Proforma_Vendedor_Sucursal`),
  KEY `fk_TB_10_Proforma_TB_03_Cliente1_idx` (`TB_03_Cliente_Cliente_Cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_11_precios`
--

CREATE TABLE IF NOT EXISTS `tb_11_precios` (
  `Precio_Id` int(11) NOT NULL AUTO_INCREMENT,
  `Precio_Numero` int(11) NOT NULL DEFAULT '0',
  `Precio_Monto` double NOT NULL DEFAULT '0',
  `TB_06_Articulo_Articulo_Codigo` varchar(30) NOT NULL,
  `TB_06_Articulo_TB_05_Familia_Familia_Codigo` int(11) NOT NULL,
  `TB_06_Articulo_TB_02_Sucursal_Codigo` int(11) NOT NULL,
  PRIMARY KEY (`Precio_Id`,`TB_06_Articulo_Articulo_Codigo`,`TB_06_Articulo_TB_05_Familia_Familia_Codigo`,`TB_06_Articulo_TB_02_Sucursal_Codigo`),
  KEY `fk_TB_11_Precios_TB_06_Articulo1_idx` (`TB_06_Articulo_Articulo_Codigo`,`TB_06_Articulo_TB_05_Familia_Familia_Codigo`,`TB_06_Articulo_TB_02_Sucursal_Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_12_transacciones`
--

CREATE TABLE IF NOT EXISTS `tb_12_transacciones` (
  `Trans_Codigo` int(11) NOT NULL AUTO_INCREMENT,
  `Trans_Descripcion` varchar(150) DEFAULT NULL,
  `Trans_Fecha_Hora` timestamp NULL DEFAULT NULL,
  `Trans_Tipo` varchar(15) DEFAULT NULL,
  `Trans_IP` varchar(40) DEFAULT NULL,
  `TB_01_Usuario_Usuario_Codigo` int(11) NOT NULL,
  `TB_01_Usuario_TB_02_Sucursal_Codigo` int(11) NOT NULL,
  PRIMARY KEY (`Trans_Codigo`,`TB_01_Usuario_Usuario_Codigo`,`TB_01_Usuario_TB_02_Sucursal_Codigo`),
  KEY `fk_TB_12_Transacciones_TB_01_Usuario1_idx` (`TB_01_Usuario_Usuario_Codigo`,`TB_01_Usuario_TB_02_Sucursal_Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_13_cheque`
--

CREATE TABLE IF NOT EXISTS `tb_13_cheque` (
  `Cheque_Id` int(11) NOT NULL AUTO_INCREMENT,
  `Cheque_Numero` varchar(45) DEFAULT NULL,
  `TB_07_Factura_Factura_Consecutivo` int(11) NOT NULL,
  `TB_07_Factura_TB_02_Sucursal_Codigo` int(11) NOT NULL,
  `TB_07_Factura_Factura_Vendedor_Codigo` int(11) NOT NULL,
  `TB_07_Factura_Factura_Vendedor_Sucursal` int(11) NOT NULL,
  `TB_07_Factura_TB_03_Cliente_Cliente_Cedula` bigint(20) NOT NULL,
  PRIMARY KEY (`Cheque_Id`,`TB_07_Factura_Factura_Consecutivo`,`TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_07_Factura_Factura_Vendedor_Codigo`,`TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_07_Factura_TB_03_Cliente_Cliente_Cedula`),
  KEY `fk_TB_13_Cheque_TB_07_Factura1_idx` (`TB_07_Factura_Factura_Consecutivo`,`TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_07_Factura_Factura_Vendedor_Codigo`,`TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_07_Factura_TB_03_Cliente_Cliente_Cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_14_sesiones`
--

CREATE TABLE IF NOT EXISTS `tb_14_sesiones` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text,
  PRIMARY KEY (`session_id`),
  KEY `'last_activity_idx'` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_15_permisos`
--

CREATE TABLE IF NOT EXISTS `tb_15_permisos` (
  `Permisos_Id` int(11) NOT NULL AUTO_INCREMENT,
  `Permisos_Area` varchar(30) DEFAULT NULL,
  `Permisos_Value` tinyint(1) DEFAULT NULL,
  `TB_01_Usuario_Usuario_Codigo` int(11) NOT NULL,
  `TB_01_Usuario_TB_02_Sucursal_Codigo` int(11) NOT NULL,
  PRIMARY KEY (`Permisos_Id`,`TB_01_Usuario_Usuario_Codigo`,`TB_01_Usuario_TB_02_Sucursal_Codigo`),
  KEY `fk_TB_15_Permisos_TB_01_Usuario1_idx` (`TB_01_Usuario_Usuario_Codigo`,`TB_01_Usuario_TB_02_Sucursal_Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `tb_15_permisos` (`Permisos_Id`, `Permisos_Area`, `Permisos_Value`, `TB_01_Usuario_Usuario_Codigo`, `TB_01_Usuario_TB_02_Sucursal_Codigo`) VALUES
(2, 'activar_empresa', 1, 1, 0),
(3, 'activar_familias', 1, 1, 0),
(4, 'anular_recibos', 1, 1, 0),
(5, 'cambio_codigo_articulo', 1, 1, 0),
(6, 'consultar_ventas', 1, 1, 0),
(7, 'crear_factura', 1, 1, 0),
(8, 'crear_proforma', 1, 1, 0),
(9, 'crear_retiros', 1, 1, 0),
(10, 'entrar_notas', 1, 1, 0),
(11, 'entrar_notas_d', 1, 1, 0),
(12, 'deposito_recibos', 1, 1, 0),
(13, 'desactivar_banco', 1, 1, 0),
(14, 'desactivar_empresa', 1, 1, 0),
(15, 'desactivar_familias', 1, 1, 0),
(16, 'editar_autorizacion', 1, 1, 0),
(17, 'editar_banco', 1, 1, 0),
(18, 'editar_cliente', 1, 1, 0),
(19, 'editar_codigo', 1, 1, 0),
(20, 'editar_empresa', 1, 1, 0),
(21, 'editar_familias', 1, 1, 0),
(22, 'editar_usuarios', 1, 1, 0),
(23, 'entrada_familias', 1, 1, 0),
(24, 'entrar_banco', 1, 1, 0),
(25, 'entrar_caja', 1, 1, 0),
(26, 'entrar_configuracion', 1, 1, 0),
(27, 'entrar_empresa', 1, 1, 0),
(28, 'entrar_recibos', 1, 1, 0),
(29, 'ingreso_bodega', 1, 1, 0),
(30, 'otros_cliente', 1, 1, 0),
(31, 'registrar_articulo_individual', 1, 1, 0),
(32, 'registrar_articulos_masivo', 1, 1, 0),
(33, 'registrar_banco', 1, 1, 0),
(34, 'registrar_cliente', 1, 1, 0),
(35, 'registrar_empresa', 1, 1, 0),
(36, 'registrar_familia', 1, 1, 0),
(37, 'registrar_usuarios', 1, 1, 0),
(38, 'traspaso_individual_articulo', 1, 1, 0),
(39, 'traspaso_articulos_masivo', 1, 1, 0),
(40, 'ver_autorizacion', 1, 1, 0),
(41, 'ver_bitacora', 1, 1, 0),
(42, 'activar_empresa', 1, 4, 0),
(43, 'activar_familias', 1, 4, 0),
(44, 'anular_recibos', 1, 4, 0),
(45, 'cambio_codigo_articulo', 1, 4, 0),
(46, 'consultar_ventas', 1, 4, 0),
(47, 'crear_factura', 1, 4, 0),
(48, 'crear_proforma', 1, 4, 0),
(49, 'crear_retiros', 1, 4, 0),
(50, 'entrar_notas', 1, 4, 0),
(51, 'entrar_notas_d', 1, 4, 0),
(52, 'deposito_recibos', 1, 4, 0),
(53, 'desactivar_banco', 1, 4, 0),
(54, 'desactivar_empresa', 1, 4, 0),
(55, 'desactivar_familias', 1, 4, 0),
(56, 'editar_autorizacion', 1, 4, 0),
(57, 'editar_banco', 1, 4, 0),
(58, 'editar_cliente', 1, 4, 0),
(59, 'editar_codigo', 1, 4, 0),
(60, 'editar_empresa', 1, 4, 0),
(61, 'editar_familias', 1, 4, 0),
(62, 'editar_usuarios', 1, 4, 0),
(63, 'entrada_familias', 1, 4, 0),
(64, 'entrar_banco', 1, 4, 0),
(65, 'entrar_caja', 1, 4, 0),
(66, 'entrar_configuracion', 1, 4, 0),
(67, 'entrar_empresa', 1, 4, 0),
(68, 'entrar_recibos', 1, 4, 0),
(69, 'ingreso_bodega', 1, 4, 0),
(70, 'otros_cliente', 1, 4, 0),
(71, 'registrar_articulo_individual', 1, 4, 0),
(72, 'registrar_articulos_masivo', 1, 4, 0),
(73, 'registrar_banco', 1, 4, 0),
(74, 'registrar_cliente', 1, 4, 0),
(75, 'registrar_empresa', 1, 4, 0),
(76, 'registrar_familia', 1, 4, 0),
(77, 'registrar_usuarios', 1, 4, 0),
(78, 'traspaso_individual_articulo', 1, 4, 0),
(79, 'traspaso_articulos_masivo', 1, 4, 0),
(80, 'ver_autorizacion', 1, 4, 0),
(81, 'ver_bitacora', 1, 4, 0);
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
  `TB_03_Cliente_Cliente_Cedula` bigint(20) NOT NULL,
  PRIMARY KEY (`TB_03_Cliente_Cliente_Cedula`,`AuthClientes_Id`),
  KEY `fk_TB_16_AuthClientes_TB_03_Cliente1_idx` (`TB_03_Cliente_Cliente_Cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_17_descuento_producto`
--

CREATE TABLE IF NOT EXISTS `tb_17_descuento_producto` (
  `Descuento_producto_id` int(11) NOT NULL AUTO_INCREMENT,
  `Descuento_producto_monto` double DEFAULT NULL,
  `Descuento_producto_porcentaje` double DEFAULT NULL,
  `TB_06_Articulo_Articulo_Codigo` varchar(30) NOT NULL,
  `TB_06_Articulo_TB_05_Familia_Familia_Codigo` int(11) NOT NULL,
  `TB_03_Cliente_Cliente_Cedula` bigint(20) NOT NULL,
  `TB_02_Sucursal_Codigo` int(11) NOT NULL,
  PRIMARY KEY (`Descuento_producto_id`,`TB_06_Articulo_Articulo_Codigo`,`TB_06_Articulo_TB_05_Familia_Familia_Codigo`,`TB_03_Cliente_Cliente_Cedula`,`TB_02_Sucursal_Codigo`),
  KEY `fk_TB_21_Descuento_Producto_TB_06_Articulo1_idx` (`TB_06_Articulo_Articulo_Codigo`,`TB_06_Articulo_TB_05_Familia_Familia_Codigo`),
  KEY `fk_TB_21_Descuento_Producto_TB_03_Cliente1_idx` (`TB_03_Cliente_Cliente_Cedula`),
  KEY `fk_TB_21_Descuento_Producto_TB_02_Sucursal1_idx` (`TB_02_Sucursal_Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_18_tarjeta`
--

CREATE TABLE IF NOT EXISTS `tb_18_tarjeta` (
  `Tarjeta_Id` int(11) NOT NULL AUTO_INCREMENT,
  `Tarjeta_Numero_Transaccion` varchar(45) DEFAULT NULL,
  `Tarjeta_Comision_Banco` float DEFAULT NULL,
  `TB_07_Factura_Factura_Consecutivo` int(11) NOT NULL,
  `TB_07_Factura_TB_02_Sucursal_Codigo` int(11) NOT NULL,
  `TB_07_Factura_Factura_Vendedor_Codigo` int(11) NOT NULL,
  `TB_07_Factura_Factura_Vendedor_Sucursal` int(11) NOT NULL,
  `TB_07_Factura_TB_03_Cliente_Cliente_Cedula` bigint(20) NOT NULL,
  `TB_22_Banco_Banco_Codigo` int(11) NOT NULL,
  PRIMARY KEY (`Tarjeta_Id`,`TB_07_Factura_Factura_Consecutivo`,`TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_07_Factura_Factura_Vendedor_Codigo`,`TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_07_Factura_TB_03_Cliente_Cliente_Cedula`,`TB_22_Banco_Banco_Codigo`),
  KEY `fk_TB_18_Tarjeta_TB_07_Factura1_idx` (`TB_07_Factura_Factura_Consecutivo`,`TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_07_Factura_Factura_Vendedor_Codigo`,`TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_07_Factura_TB_03_Cliente_Cliente_Cedula`),
  KEY `fk_TB_18_Tarjeta_TB_22_Banco1_idx` (`TB_22_Banco_Banco_Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `TB_07_Factura_TB_03_Cliente_Cliente_Cedula` bigint(20) NOT NULL,
  `TB_22_Banco_Banco_Codigo` int(11) NOT NULL,
  PRIMARY KEY (`Deposito_Id`,`TB_07_Factura_Factura_Consecutivo`,`TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_07_Factura_Factura_Vendedor_Codigo`,`TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_07_Factura_TB_03_Cliente_Cliente_Cedula`,`TB_22_Banco_Banco_Codigo`),
  KEY `fk_TB_19_Deposito_TB_07_Factura1_idx` (`TB_07_Factura_Factura_Consecutivo`,`TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_07_Factura_Factura_Vendedor_Codigo`,`TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_07_Factura_TB_03_Cliente_Cliente_Cedula`),
  KEY `fk_TB_19_Deposito_TB_22_Banco1_idx` (`TB_22_Banco_Banco_Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_20_descuento_familia`
--

CREATE TABLE IF NOT EXISTS `tb_20_descuento_familia` (
  `Descuento_familia_id` int(11) NOT NULL AUTO_INCREMENT,
  `Descuento_familia_monto` double DEFAULT NULL,
  `Descuento_familia_porcentaje` double DEFAULT NULL,
  `TB_05_Familia_Familia_Codigo` int(11) NOT NULL,
  `TB_05_Familia_TB_02_Sucursal_Codigo` int(11) NOT NULL,
  `TB_03_Cliente_Cliente_Cedula` bigint(20) NOT NULL,
  `TB_02_Sucursal_Codigo` int(11) NOT NULL,
  PRIMARY KEY (`Descuento_familia_id`,`TB_05_Familia_Familia_Codigo`,`TB_05_Familia_TB_02_Sucursal_Codigo`,`TB_03_Cliente_Cliente_Cedula`,`TB_02_Sucursal_Codigo`),
  KEY `fk_TB_20_Descuento_familia_TB_05_Familia1_idx` (`TB_05_Familia_Familia_Codigo`,`TB_05_Familia_TB_02_Sucursal_Codigo`),
  KEY `fk_TB_20_Descuento_Familia_TB_03_Cliente1_idx` (`TB_03_Cliente_Cliente_Cedula`),
  KEY `fk_TB_20_Descuento_Familia_TB_02_Sucursal1_idx` (`TB_02_Sucursal_Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_21_descuento_cliente`
--

CREATE TABLE IF NOT EXISTS `tb_21_descuento_cliente` (
  `Descuento_cliente_id` int(11) NOT NULL AUTO_INCREMENT,
  `Descuento_cliente_porcentaje` double DEFAULT NULL,
  `TB_03_Cliente_Cliente_Cedula` bigint(20) NOT NULL,
  `TB_02_Sucursal_Codigo` int(11) NOT NULL,
  PRIMARY KEY (`Descuento_cliente_id`,`TB_03_Cliente_Cliente_Cedula`,`TB_02_Sucursal_Codigo`),
  KEY `fk_TB_24_Descuento_Cliente_TB_03_Cliente1_idx` (`TB_03_Cliente_Cliente_Cedula`),
  KEY `fk_TB_24_Descuento_Cliente_TB_02_Sucursal1_idx` (`TB_02_Sucursal_Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_22_banco`
--

CREATE TABLE IF NOT EXISTS `tb_22_banco` (
  `Banco_Codigo` int(11) NOT NULL AUTO_INCREMENT,
  `Banco_Nombre` varchar(45) DEFAULT NULL,
  `Banco_Comision_Porcentaje` float DEFAULT NULL,
  `Banco_Creado_Por` int(11) DEFAULT NULL,
  PRIMARY KEY (`Banco_Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_23_mixto`
--

CREATE TABLE IF NOT EXISTS `tb_23_mixto` (
  `Mixto_Id` int(11) NOT NULL AUTO_INCREMENT,
  `Mixto_Cantidad_Paga` float DEFAULT NULL,
  `TB_18_Tarjeta_Tarjeta_Id` int(11) NOT NULL,
  `TB_18_Tarjeta_TB_07_Factura_Factura_Consecutivo` int(11) NOT NULL,
  `TB_18_Tarjeta_TB_07_Factura_TB_02_Sucursal_Codigo` int(11) NOT NULL,
  `TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Codigo` int(11) NOT NULL,
  `TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Sucursal` int(11) NOT NULL,
  `TB_18_Tarjeta_TB_07_Factura_TB_03_Cliente_Cliente_Cedula` bigint(20) NOT NULL,
  `TB_18_Tarjeta_TB_22_Banco_Banco_Codigo` int(11) NOT NULL,
  PRIMARY KEY (`Mixto_Id`,`TB_18_Tarjeta_Tarjeta_Id`,`TB_18_Tarjeta_TB_07_Factura_Factura_Consecutivo`,`TB_18_Tarjeta_TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Codigo`,`TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_18_Tarjeta_TB_07_Factura_TB_03_Cliente_Cliente_Cedula`,`TB_18_Tarjeta_TB_22_Banco_Banco_Codigo`),
  KEY `fk_TB_23_Mixto_TB_18_Tarjeta1_idx` (`TB_18_Tarjeta_Tarjeta_Id`,`TB_18_Tarjeta_TB_07_Factura_Factura_Consecutivo`,`TB_18_Tarjeta_TB_07_Factura_TB_02_Sucursal_Codigo`,`TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Codigo`,`TB_18_Tarjeta_TB_07_Factura_Factura_Vendedor_Sucursal`,`TB_18_Tarjeta_TB_07_Factura_TB_03_Cliente_Cliente_Cedula`,`TB_18_Tarjeta_TB_22_Banco_Banco_Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_24_credito`
--

CREATE TABLE IF NOT EXISTS `tb_24_credito` (
  `Credito_Id` int(11) NOT NULL AUTO_INCREMENT,
  `Credito_Numero_Dias` int(11) DEFAULT NULL,
  `Credito_Saldo_Actual` double DEFAULT NULL,
  `Credito_Saldo_Inicial` double DEFAULT NULL,
  `Credito_Generico` varchar(150) DEFAULT NULL,
  `Credito_Fecha_Expedicion` timestamp NULL DEFAULT NULL,
  `Credito_Factura_Consecutivo` int(11) NOT NULL,
  `Credito_Sucursal_Codigo` int(11) NOT NULL,
  `Credito_Vendedor_Codigo` int(11) NOT NULL,
  `Credito_Vendedor_Sucursal` int(11) NOT NULL,
  `Credito_Cliente_Cedula` bigint(20) NOT NULL,
  PRIMARY KEY (`Credito_Id`,`Credito_Factura_Consecutivo`,`Credito_Sucursal_Codigo`,`Credito_Vendedor_Codigo`,`Credito_Vendedor_Sucursal`,`Credito_Cliente_Cedula`),
  KEY `fk_TB_24_Credito_TB_07_Factura1_idx` (`Credito_Factura_Consecutivo`,`Credito_Sucursal_Codigo`,`Credito_Vendedor_Codigo`,`Credito_Vendedor_Sucursal`,`Credito_Cliente_Cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_25_maximo_credito_cliente`
--

CREATE TABLE IF NOT EXISTS `tb_25_maximo_credito_cliente` (
  `Credito_Cliente_Id` int(11) NOT NULL AUTO_INCREMENT,
  `Credito_Cliente_Cantidad_Maxima` double DEFAULT NULL,
  `TB_03_Cliente_Cliente_Cedula` bigint(20) NOT NULL,
  `TB_02_Sucursal_Codigo` int(11) NOT NULL,
  PRIMARY KEY (`Credito_Cliente_Id`,`TB_03_Cliente_Cliente_Cedula`,`TB_02_Sucursal_Codigo`),
  KEY `fk_TB_25_Maximo_Credito_Cliente_TB_03_Cliente1_idx` (`TB_03_Cliente_Cliente_Cedula`),
  KEY `fk_TB_25_Maximo_Credito_Cliente_TB_02_Sucursal1_idx` (`TB_02_Sucursal_Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_26_recibos_dinero`
--

CREATE TABLE IF NOT EXISTS `tb_26_recibos_dinero` (
  `Consecutivo` int(11) NOT NULL AUTO_INCREMENT,
  `Recibo_Cantidad` float DEFAULT NULL,
  `Recibo_Fecha` timestamp NULL DEFAULT NULL,
  `Recibo_Saldo` float DEFAULT NULL,
  `Anulado` tinyint(1) NOT NULL DEFAULT '0',
  `Tipo_Pago` varchar(20) NOT NULL,
  `Credito` int(11) NOT NULL,
  PRIMARY KEY (`Consecutivo`,`Credito`),
  KEY `fk_TB_26_Recibos_Dinero_TB_24_Credito1_idx` (`Credito`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `Sucursal` int(11) NOT NULL,
  `Cliente` bigint(20) NOT NULL,
  PRIMARY KEY (`Consecutivo`,`Factura_Acreditar`,`Factura_Aplicar`,`Sucursal`,`Cliente`),
  UNIQUE KEY `Consecutivo_UNIQUE` (`Consecutivo`),
  KEY `fk_TB_27_Notas_Credito_TB_07_Factura1_idx` (`Factura_Acreditar`),
  KEY `fk_TB_27_Notas_Credito_TB_07_Factura2_idx` (`Factura_Aplicar`),
  KEY `fk_TB_27_Notas_Credito_TB_02_Sucursal1_idx` (`Sucursal`),
  KEY `fk_TB_27_Notas_Credito_TB_03_Cliente1_idx` (`Cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_28_productos_notas_credito`
--

CREATE TABLE IF NOT EXISTS `tb_28_productos_notas_credito` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Codigo` varchar(30) DEFAULT NULL,
  `Descripcion` varchar(150) DEFAULT NULL,
  `Cantidad_Bueno` int(11) DEFAULT NULL,
  `Cantidad_Defectuoso` int(11) DEFAULT NULL,
  `Precio_Unitario` double DEFAULT NULL,
  `Nota_Credito_Consecutivo` int(11) NOT NULL,
  `Sucursal` int(11) NOT NULL,
  PRIMARY KEY (`Id`,`Nota_Credito_Consecutivo`,`Sucursal`),
  KEY `fk_TB_28_Productos_Notas_Credito_TB_27_Notas_Credito1_idx` (`Nota_Credito_Consecutivo`),
  KEY `fk_TB_28_Productos_Notas_Credito_TB_02_Sucursal1_idx` (`Sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_29_deposito_recibo`
--

CREATE TABLE IF NOT EXISTS `tb_29_deposito_recibo` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Banco_Id` int(11) DEFAULT NULL,
  `Banco_Nombre` varchar(45) DEFAULT NULL,
  `Numero_Deposito` varchar(45) DEFAULT NULL,
  `Fecha` timestamp NULL DEFAULT NULL,
  `Recibo` int(11) NOT NULL,
  `Credito` int(11) NOT NULL,
  PRIMARY KEY (`Id`,`Recibo`,`Credito`),
  KEY `fk_TB_29_Deposito_Recibo_TB_26_Recibos_Dinero1_idx` (`Recibo`,`Credito`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `Sucursal` int(11) NOT NULL,
  PRIMARY KEY (`Consecutivo`,`Sucursal`,`Usuario`),
  KEY `fk_TB_30_Notas_Debito_TB_02_Sucursal1_idx` (`Sucursal`),
  KEY `fk_TB_30_Notas_Debito_TB_01_Usuario1_idx` (`Usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_31_productos_notas_debito`
--

CREATE TABLE IF NOT EXISTS `tb_31_productos_notas_debito` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Codigo` varchar(30) DEFAULT NULL,
  `Descripcion` varchar(150) DEFAULT NULL,
  `Cantidad_Debitar` int(11) DEFAULT NULL,
  `Precio_Unitario` double DEFAULT NULL,
  `Nota_Debito_Consecutivo` int(11) NOT NULL,
  `Sucursal` int(11) NOT NULL,
  `Usuario` int(11) NOT NULL,
  PRIMARY KEY (`Id`,`Nota_Debito_Consecutivo`,`Sucursal`,`Usuario`),
  KEY `fk_TB_31_Productos_Notas_Debito_TB_30_Notas_Debito1_idx` (`Nota_Debito_Consecutivo`,`Sucursal`,`Usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_32_tarjeta_recibos`
--

CREATE TABLE IF NOT EXISTS `tb_32_tarjeta_recibos` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Numero_Autorizacion` varchar(45) DEFAULT NULL,
  `Comision_Por` float DEFAULT NULL,
  `Banco` int(11) NOT NULL,
  `Recibo` int(11) NOT NULL,
  `Credito` int(11) NOT NULL,
  PRIMARY KEY (`Id`,`Recibo`,`Credito`,`Banco`),
  KEY `fk_TB_32_Tarjeta_Recibos_TB_26_Recibos_Dinero1_idx` (`Recibo`,`Credito`),
  KEY `fk_TB_32_Tarjeta_Recibos_TB_22_Banco1_idx` (`Banco`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_33_retiros_parciales`
--

CREATE TABLE IF NOT EXISTS `tb_33_retiros_parciales` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Monto` double DEFAULT NULL,
  `Fecha_Hora` timestamp NULL DEFAULT NULL,
  `Usuario` int(11) NOT NULL,
  `Sucursal` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_TB_33_Retiros_Parciales_TB_01_Usuario1_idx` (`Usuario`),
  KEY `fk_TB_33_Retiros_Parciales_TB_02_Sucursal1_idx` (`Sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `Sucursal` int(11) NOT NULL,
  PRIMARY KEY (`Codigo`),
  KEY `fk_TB_34_Bodega_TB_01_Usuario1_idx` (`Usuario`),
  KEY `fk_TB_34_Bodega_TB_02_Sucursal1_idx` (`Sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_35_cambio_codigo`
--

CREATE TABLE IF NOT EXISTS `tb_35_cambio_codigo` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Fecha` timestamp NULL DEFAULT NULL,
  `Usuario` int(11) NOT NULL,
  `Sucursal` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_TB_35_Cambio_Codigo_TB_01_Usuario1_idx` (`Usuario`),
  KEY `fk_TB_35_Cambio_Codigo_TB_02_Sucursal1_idx` (`Sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_36_articulos_cambio_codigo`
--

CREATE TABLE IF NOT EXISTS `tb_36_articulos_cambio_codigo` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Articulo_Cambio` varchar(30) DEFAULT NULL,
  `Descripcion_Cambio` varchar(150) DEFAULT NULL,
  `Articulo_Abonado` varchar(30) DEFAULT NULL,
  `Descripcion_Abonado` varchar(150) DEFAULT NULL,
  `Cantidad` int(11) DEFAULT NULL,
  `Cambio_Codigo` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_TB_36_Articulos_Cambio_Codigo_TB_35_Cambio_Codigo1_idx` (`Cambio_Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_37_cierre_caja`
--

CREATE TABLE IF NOT EXISTS `tb_37_cierre_caja` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Fecha` timestamp NULL DEFAULT NULL,
  `Sucursal` int(11) NOT NULL,
  `Usuario` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_TB_37_Cierre_Caja_TB_02_Sucursal1_idx` (`Sucursal`),
  KEY `fk_TB_37_Cierre_Caja_TB_01_Usuario1_idx` (`Usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `Cierre_Caja` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_TB_38_Moneda_Cierre_Caja_TB_37_Cierre_Caja1_idx` (`Cierre_Caja`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  ADD CONSTRAINT `fk_TB_13_Cheque_TB_07_Factura1` FOREIGN KEY (`TB_07_Factura_Factura_Consecutivo`, `TB_07_Factura_TB_02_Sucursal_Codigo`, `TB_07_Factura_Factura_Vendedor_Codigo`, `TB_07_Factura_Factura_Vendedor_Sucursal`, `TB_07_Factura_TB_03_Cliente_Cliente_Cedula`) REFERENCES `tb_07_factura` (`Factura_Consecutivo`, `TB_02_Sucursal_Codigo`, `Factura_Vendedor_Codigo`, `Factura_Vendedor_Sucursal`, `TB_03_Cliente_Cliente_Cedula`) ON DELETE NO ACTION ON UPDATE NO ACTION;

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

 DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`127.0.0.1` PROCEDURE `productoConPrecios`(IN codigoProducto varchar(20), IN codigoSucursal int(11), IN codigoFamilia int(11))
BEGIN
	SELECT  `tb_06_articulo`.`Articulo_Codigo`, 
			`tb_06_articulo`.`Articulo_Descripcion`,
			`tb_06_articulo`.`Articulo_Codigo_Barras`,
			`tb_06_articulo`.`Articulo_Cantidad_Inventario`,
			`tb_06_articulo`.`Articulo_Cantidad_Defectuoso`,
			`tb_06_articulo`.`Articulo_Descuento`,
			`tb_06_articulo`.`Articulo_Imagen_URL`,
			`tb_06_articulo`.`Articulo_Exento`,
			`tb_06_articulo`.`TB_05_Familia_Familia_Codigo`, 
			`tb_06_articulo`.`TB_02_Sucursal_Codigo`,
			MAX(CASE WHEN TB_11_Precios.Precio_Numero = '0' THEN TB_11_Precios.Precio_Monto END) Precio_0,
			MAX(CASE WHEN TB_11_Precios.Precio_Numero = '1' THEN TB_11_Precios.Precio_Monto END) Precio_1,
			MAX(CASE WHEN TB_11_Precios.Precio_Numero = '2' THEN TB_11_Precios.Precio_Monto END) Precio_2,
			MAX(CASE WHEN TB_11_Precios.Precio_Numero = '3' THEN TB_11_Precios.Precio_Monto END) Precio_3,
			MAX(CASE WHEN TB_11_Precios.Precio_Numero = '4' THEN TB_11_Precios.Precio_Monto END) Precio_4,
			MAX(CASE WHEN TB_11_Precios.Precio_Numero = '5' THEN TB_11_Precios.Precio_Monto END) Precio_5
	FROM    TB_11_Precios, tb_06_articulo
	WHERE   TB_11_Precios.TB_06_Articulo_Articulo_Codigo  = codigoProducto 
	AND 	TB_11_Precios.TB_06_Articulo_TB_05_Familia_Familia_Codigo = codigoFamilia 
	AND 	TB_11_Precios.TB_06_Articulo_TB_02_Sucursal_Codigo = codigoSucursal
	AND  	tb_06_articulo.Articulo_Codigo = codigoProducto 
	AND 	tb_06_articulo.TB_05_Familia_Familia_Codigo  = codigoFamilia	
	AND 	tb_06_articulo.TB_02_Sucursal_Codigo = codigoSucursal;
END$$

DELIMITER ;

ALTER TABLE `tb_02_sucursal` CHANGE `Sucursal_Nombre` `Sucursal_Nombre` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

--
-- Base de datos: `garotas_bonitas_main_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tb_39_configuracion`
--

CREATE TABLE IF NOT EXISTS `tb_39_configuracion` (
  `Parametro` varchar(50) NOT NULL,
  `Valor` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`Parametro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tb_39_configuracion`
--

INSERT INTO `tb_39_configuracion` (`Parametro`, `Valor`) VALUES
('cantidad_decimales', '2'),
('codigo_empresa_traspaso_compras', '0'),
('correo_administracion', 'esteban@garotasbonitascr.com'),
('dolar_compra', '530.00'),
('dolar_venta', '542.00'),
('iva', '13'),
('monto_intermedio_compra_cliente', '5000.00'),
('monto_minimo_compra_cliente', '2500.00'),
('porcentaje_retencion_tarjetas_hacienda', '2'),
('tiempo_sesion', '600'),
('ultima_actualizacion_estado_clientes', NULL);

ALTER TABLE `tb_27_notas_credito` ADD `Tipo_Pago` VARCHAR(10) NOT NULL AFTER `Factura_Aplicar`, ADD `Moneda` VARCHAR(15) NOT NULL AFTER `Tipo_Pago`, ADD `Por_IVA` DOUBLE NOT NULL AFTER `Moneda`, ADD `Tipo_Cambio` DOUBLE NOT NULL AFTER `Por_IVA`;

CREATE TABLE IF NOT EXISTS `GAROTAS_BONITAS_MAIN_DB`.`TB_40_Apartado` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Abono` DOUBLE NULL,
  `Credito` INT NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `fk_TB_40_Apartado_TB_24_Credito1_idx` (`Credito` ASC),
  CONSTRAINT `fk_TB_40_Apartado_TB_24_Credito1`
    FOREIGN KEY (`Credito`)
    REFERENCES `GAROTAS_BONITAS_MAIN_DB`.`TB_24_Credito` (`Credito_Id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB

CREATE TABLE IF NOT EXISTS `GAROTAS_BONITAS_MAIN_DB`.`TB_41_Productos_Factura_Temporal` (
  `Id` INT NOT NULL,
  `Codigo_Articulo` VARCHAR(20) NOT NULL,
  `Cantidad` INT NULL DEFAULT 0,
  `Factura_Temporal` VARCHAR(32) NULL,
  `Sucursal` INT NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `fk_TB_41_Productos_Factura_Temporal_TB_02_Sucursal1_idx` (`Sucursal` ASC),
  INDEX `fk_TB_41_Productos_Factura_Temporal_TB_06_Articulo1_idx` (`Codigo_Articulo` ASC),
  CONSTRAINT `fk_TB_41_Productos_Factura_Temporal_TB_02_Sucursal1`
    FOREIGN KEY (`Sucursal`)
    REFERENCES `GAROTAS_BONITAS_MAIN_DB`.`TB_02_Sucursal` (`Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_TB_41_Productos_Factura_Temporal_TB_06_Articulo1`
    FOREIGN KEY (`Codigo_Articulo`)
    REFERENCES `GAROTAS_BONITAS_MAIN_DB`.`TB_06_Articulo` (`Articulo_Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB

ALTER TABLE `tb_41_productos_factura_temporal` CHANGE `Id` `Id` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tb_33_retiros_parciales` ADD `Tipo_Cambio` DOUBLE NOT NULL AFTER `Fecha_Hora`;

CREATE TABLE IF NOT EXISTS `GAROTAS_BONITAS_MAIN_DB`.`TB_42_Moneda_Retiro_Parcial` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Denominacion` INT NULL,
  `Cantidad` INT NULL,
  `Tipo` VARCHAR(15) NULL,
  `Moneda` VARCHAR(15) NULL,
  `Retiro` INT NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `fk_TB_42_Moneda_Retiro_Parcial_TB_33_Retiros_Parciales1_idx` (`Retiro` ASC),
  CONSTRAINT `fk_TB_42_Moneda_Retiro_Parcial_TB_33_Retiros_Parciales1`
    FOREIGN KEY (`Retiro`)
    REFERENCES `GAROTAS_BONITAS_MAIN_DB`.`TB_33_Retiros_Parciales` (`Id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB

CREATE TABLE IF NOT EXISTS `GAROTAS_BONITAS_MAIN_DB`.`TB_43_Deposito_Recibo` (
  `Id` INT NOT NULL,
  `Numero_Documento` VARCHAR(45) NULL,
  `Recibo` INT NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `fk_TB_43_Deposito_Recibo_TB_26_Recibos_Dinero1_idx` (`Recibo` ASC),
  CONSTRAINT `fk_TB_43_Deposito_Recibo_TB_26_Recibos_Dinero1`
    FOREIGN KEY (`Recibo`)
    REFERENCES `GAROTAS_BONITAS_MAIN_DB`.`TB_26_Recibos_Dinero` (`Consecutivo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB

ALTER TABLE `tb_43_deposito_recibo` CHANGE `Id` `Id` INT(11) NOT NULL AUTO_INCREMENT;
  
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
