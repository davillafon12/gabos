CREATE TABLE IF NOT EXISTS `GAROTAS_BONITAS_MAIN_DB`.`TB_44_Traspaso_Inventario` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Sucursal_Salida` INT NOT NULL,
  `Sucursal_Entrada` INT NOT NULL,
  `Fecha` TIMESTAMP NULL,
  `Usuario` INT NOT NULL,
  `Factura_Traspasada` INT NOT NULL,
  PRIMARY KEY (`Id`, `Sucursal_Salida`, `Sucursal_Entrada`, `Usuario`, `Factura_Traspasada`),
  INDEX `fk_TB_44_Traspaso_Inventario_TB_02_Sucursal1_idx` (`Sucursal_Salida` ASC),
  INDEX `fk_TB_44_Traspaso_Inventario_TB_02_Sucursal2_idx` (`Sucursal_Entrada` ASC),
  INDEX `fk_TB_44_Traspaso_Inventario_TB_01_Usuario1_idx` (`Usuario` ASC),
  INDEX `fk_TB_44_Traspaso_Inventario_TB_07_Factura1_idx` (`Factura_Traspasada` ASC),
  CONSTRAINT `fk_TB_44_Traspaso_Inventario_TB_02_Sucursal1`
    FOREIGN KEY (`Sucursal_Salida`)
    REFERENCES `GAROTAS_BONITAS_MAIN_DB`.`TB_02_Sucursal` (`Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_TB_44_Traspaso_Inventario_TB_02_Sucursal2`
    FOREIGN KEY (`Sucursal_Entrada`)
    REFERENCES `GAROTAS_BONITAS_MAIN_DB`.`TB_02_Sucursal` (`Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_TB_44_Traspaso_Inventario_TB_01_Usuario1`
    FOREIGN KEY (`Usuario`)
    REFERENCES `GAROTAS_BONITAS_MAIN_DB`.`TB_01_Usuario` (`Usuario_Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_TB_44_Traspaso_Inventario_TB_07_Factura1`
    FOREIGN KEY (`Factura_Traspasada`)
    REFERENCES `GAROTAS_BONITAS_MAIN_DB`.`TB_07_Factura` (`Factura_Consecutivo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

CREATE TABLE IF NOT EXISTS `GAROTAS_BONITAS_MAIN_DB`.`TB_45_Articulos_Traspaso_Inventario` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Codigo` VARCHAR(20) NULL,
  `Descripcion` VARCHAR(150) NULL,
  `Cantidad` INT NULL,
  `Traspaso` INT NOT NULL,
  PRIMARY KEY (`Id`, `Traspaso`),
  INDEX `fk_TB_45_Articulos_Traspaso_Inventario_TB_44_Traspaso_Inven_idx` (`Traspaso` ASC),
  CONSTRAINT `fk_TB_45_Articulos_Traspaso_Inventario_TB_44_Traspaso_Inventa1`
    FOREIGN KEY (`Traspaso`)
    REFERENCES `GAROTAS_BONITAS_MAIN_DB`.`TB_44_Traspaso_Inventario` (`Id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);