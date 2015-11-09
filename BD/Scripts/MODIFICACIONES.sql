CREATE TABLE IF NOT EXISTS `TB_47_Descuento_Proforma` (
  `Id` INT NOT NULL,
  `Proforma` INT NOT NULL,
  `Sucursal` INT NOT NULL,
  PRIMARY KEY (`Id`),
  INDEX `fk_TB_47_Descuento_Proforma_TB_10_Proforma1_idx` (`Proforma` ASC),
  INDEX `fk_TB_47_Descuento_Proforma_TB_02_Sucursal1_idx` (`Sucursal` ASC),
  CONSTRAINT `fk_TB_47_Descuento_Proforma_TB_10_Proforma1`
    FOREIGN KEY (`Proforma`)
    REFERENCES `TB_10_Proforma` (`Proforma_Consecutivo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_TB_47_Descuento_Proforma_TB_02_Sucursal1`
    FOREIGN KEY (`Sucursal`)
    REFERENCES `TB_02_Sucursal` (`Codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

ALTER TABLE `tb_26_recibos_dinero` ADD `Comentarios` VARCHAR(1000) NOT NULL AFTER `Tipo_Pago`;