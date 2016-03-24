CREATE TABLE IF NOT EXISTS `TB_52_Traspaso_Inventario` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Fecha` TIMESTAMP NULL,
  `Sucursal_Entrega` INT NOT NULL,
  `Sucursal_Recibe` INT NOT NULL,
  `Usuario` INT NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `fk_TB_52_Traspaso_Inventario_TB_01_Usuario1_idx` (`Usuario` ASC),
  INDEX `fk_TB_52_Traspaso_Inventario_TB_02_Sucursal1_idx` (`Sucursal_Recibe` ASC),
  INDEX `fk_TB_52_Traspaso_Inventario_TB_02_Sucursal2_idx` (`Sucursal_Entrega` ASC),
  CONSTRAINT `fk_TB_52_Traspaso_Inventario_TB_01_Usuario1`
    FOREIGN KEY (`Usuario`)
    REFERENCES `TB_01_Usuario` (`Usuario_Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_TB_52_Traspaso_Inventario_TB_02_Sucursal1`
    FOREIGN KEY (`Sucursal_Recibe`)
    REFERENCES `TB_02_Sucursal` (`Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_TB_52_Traspaso_Inventario_TB_02_Sucursal2`
    FOREIGN KEY (`Sucursal_Entrega`)
    REFERENCES `TB_02_Sucursal` (`Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `TB_53_Articulos_Traspaso_Inventario` (
  `Id` INT NOT NULL,
  `Traspaso` INT NOT NULL,
  `Codigo` VARCHAR(45) NULL,
  `Cantidad` INT NULL,
  `Descripcion` VARCHAR(200) NULL,
  PRIMARY KEY (`Id`),
  INDEX `fk_TB_53_Articulos_Traspaso_Inventario_TB_52_Traspaso_Inven_idx` (`Traspaso` ASC),
  CONSTRAINT `fk_TB_53_Articulos_Traspaso_Inventario_TB_52_Traspaso_Inventa1`
    FOREIGN KEY (`Traspaso`)
    REFERENCES `TB_52_Traspaso_Inventario` (`Id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

ALTER TABLE `tb_53_articulos_traspaso_inventario` CHANGE `Id` `Id` INT(11) NOT NULL AUTO_INCREMENT;