ALTER TABLE  `tb_03_cliente` ADD  `Provincia` INT NOT NULL AFTER  `Cliente_Pais`;
ALTER TABLE  `tb_03_cliente` ADD  `Canton` INT NOT NULL AFTER  `Provincia` ,
ADD  `Distrito` INT NOT NULL AFTER  `Canton` ,
ADD  `Barrio` INT NOT NULL AFTER  `Distrito`;
ALTER TABLE  `tb_03_cliente` ADD  `Codigo_Pais_Telefono` VARCHAR( 5 ) NOT NULL AFTER  `Cliente_Carnet_Numero`;
ALTER TABLE  `tb_03_cliente` ADD  `Codigo_Pais_Fax` VARCHAR( 5 ) NOT NULL AFTER  `Cliente_Telefono` ,
ADD  `Numero_Fax` VARCHAR( 20 ) NOT NULL AFTER  `Codigo_Pais_Fax`;