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
ENGINE = InnoDB