CREATE TABLE IF NOT EXISTS `TB_48_Relacion_Sucursal_Cliente` (
  `Id` INT NOT NULL,
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
ENGINE = InnoDB;

ALTER TABLE `tb_48_relacion_sucursal_cliente` CHANGE `Id` `Id` INT(11) NOT NULL AUTO_INCREMENT;

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
ENGINE = InnoDB;

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
ENGINE = InnoDB;