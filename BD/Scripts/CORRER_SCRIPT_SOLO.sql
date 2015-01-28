-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-01-2015 a las 04:44:28
-- Versión del servidor: 5.6.16
-- Versión de PHP: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
