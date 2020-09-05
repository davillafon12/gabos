CREATE TABLE IF NOT EXISTS `GAROTAS_BONITAS_MAIN_DB`.`TB_37_Cierre_Caja` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Fecha` TIMESTAMP NULL,
  `Sucursal` INT NOT NULL,
  `Usuario` INT NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `fk_TB_37_Cierre_Caja_TB_02_Sucursal1_idx` (`Sucursal` ASC),
  INDEX `fk_TB_37_Cierre_Caja_TB_01_Usuario1_idx` (`Usuario` ASC),
  CONSTRAINT `fk_TB_37_Cierre_Caja_TB_02_Sucursal1`
    FOREIGN KEY (`Sucursal`)
    REFERENCES `GAROTAS_BONITAS_MAIN_DB`.`TB_02_Sucursal` (`Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_TB_37_Cierre_Caja_TB_01_Usuario1`
    FOREIGN KEY (`Usuario`)
    REFERENCES `GAROTAS_BONITAS_MAIN_DB`.`TB_01_Usuario` (`Usuario_Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);
	
CREATE TABLE IF NOT EXISTS `GAROTAS_BONITAS_MAIN_DB`.`TB_38_Moneda_Cierre_Caja` (
  `Id` INT NOT NULL,
  `Denominacion` INT NULL,
  `Cantidad` INT NULL,
  `Tipo` VARCHAR(15) NULL,
  `Moneda` VARCHAR(15) NULL,
  `Cierre_Caja` INT NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `fk_TB_38_Moneda_Cierre_Caja_TB_37_Cierre_Caja1_idx` (`Cierre_Caja` ASC),
  CONSTRAINT `fk_TB_38_Moneda_Cierre_Caja_TB_37_Cierre_Caja1`
    FOREIGN KEY (`Cierre_Caja`)
    REFERENCES `GAROTAS_BONITAS_MAIN_DB`.`TB_37_Cierre_Caja` (`Id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);