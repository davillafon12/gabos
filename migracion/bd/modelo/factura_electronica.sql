CREATE TABLE IF NOT EXISTS `gabo_dev_db`.`tb_65_cuerpo_factura_electronica` (
  `Consecutivo` INT NOT NULL,
  `Sucursal` INT NOT NULL,
  `XMLSinFirmar` LONGTEXT NULL,
  `XMLFirmado` LONGTEXT NULL,
  `RespuestaHaciendaXML` LONGTEXT NULL,
  PRIMARY KEY (`Consecutivo`, `Sucursal`),
  CONSTRAINT `fk_tb_65_cuerpo_factura_electronica_tb_55_factura_electronica1`
    FOREIGN KEY (`Consecutivo` , `Sucursal`)
    REFERENCES `gabo_dev_db`.`tb_55_factura_electronica` (`Consecutivo` , `Sucursal`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

insert into tb_65_cuerpo_factura_electronica (Consecutivo, Sucursal, XMLSinFirmar, XMLFirmado, RespuestaHaciendaXML) select Consecutivo, Sucursal, XMLSinFirmar, XMLFirmado, RespuestaHaciendaXML from tb_55_factura_electronica;

ALTER TABLE `tb_55_factura_electronica` DROP `XMLSinFirmar`;
ALTER TABLE `tb_55_factura_electronica` DROP `XMLFirmado`;
ALTER TABLE `tb_55_factura_electronica` DROP `RespuestaHaciendaXML`;