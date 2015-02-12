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