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