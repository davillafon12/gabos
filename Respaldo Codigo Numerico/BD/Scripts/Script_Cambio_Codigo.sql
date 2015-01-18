CREATE TABLE IF NOT EXISTS `GAROTAS_BONITAS_MAIN_DB`.`TB_35_Cambio_Codigo` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Fecha` TIMESTAMP NULL,
  `Usuario` INT NOT NULL,
  `Sucursal` INT NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `fk_TB_35_Cambio_Codigo_TB_01_Usuario1_idx` (`Usuario` ASC),
  INDEX `fk_TB_35_Cambio_Codigo_TB_02_Sucursal1_idx` (`Sucursal` ASC),
  CONSTRAINT `fk_TB_35_Cambio_Codigo_TB_01_Usuario1`
    FOREIGN KEY (`Usuario`)
    REFERENCES `GAROTAS_BONITAS_MAIN_DB`.`TB_01_Usuario` (`Usuario_Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_TB_35_Cambio_Codigo_TB_02_Sucursal1`
    FOREIGN KEY (`Sucursal`)
    REFERENCES `GAROTAS_BONITAS_MAIN_DB`.`TB_02_Sucursal` (`Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


CREATE TABLE IF NOT EXISTS `GAROTAS_BONITAS_MAIN_DB`.`TB_36_Articulos_Cambio_Codigo` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Articulo_Cambio` INT NULL,
  `Descripcion_Cambio` VARCHAR(150) NULL,
  `Articulo_Abonado` INT NULL,
  `Descripcion_Abonado` VARCHAR(150) NULL,
  `Cantidad` INT NULL,
  `Cambio_Codigo` INT NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `fk_TB_36_Articulos_Cambio_Codigo_TB_35_Cambio_Codigo1_idx` (`Cambio_Codigo` ASC),
  CONSTRAINT `fk_TB_36_Articulos_Cambio_Codigo_TB_35_Cambio_Codigo1`
    FOREIGN KEY (`Cambio_Codigo`)
    REFERENCES `GAROTAS_BONITAS_MAIN_DB`.`TB_35_Cambio_Codigo` (`Id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);