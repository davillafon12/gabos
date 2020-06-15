-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla 'tb_63_control_inventario'
--

CREATE TABLE IF NOT EXISTS tb_63_control_inventario (
  id int(11) NOT NULL AUTO_INCREMENT,
  Fecha_Creacion timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  Creado_Por int(11) NOT NULL,
  Empate_Autorizado_Por int(11) NOT NULL,
  Sucursal int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla 'tb_64_articulos_control_inventario'
--

CREATE TABLE IF NOT EXISTS tb_64_articulos_control_inventario (
  id int(11) NOT NULL AUTO_INCREMENT,
  Codigo varchar(30) NOT NULL,
  Descripcion varchar(150) NOT NULL,
  Fisico_Defectuoso int(11) NOT NULL,
  Fisico_Bueno int(11) NOT NULL,
  Sistema_Defectuoso int(11) NOT NULL,
  Sistema_Bueno int(11) NOT NULL,
  Empatar tinyint(1) NOT NULL,
  Control_Inventario int(11) NOT NULL,
  PRIMARY KEY (id),
  KEY Control_Inventario (Control_Inventario)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tb_64_articulos_control_inventario`
--
ALTER TABLE `tb_64_articulos_control_inventario`
  ADD CONSTRAINT tb_64_articulos_control_inventario_ibfk_1 FOREIGN KEY (Control_Inventario) REFERENCES tb_64_articulos_control_inventario (id) ON DELETE CASCADE ON UPDATE CASCADE;