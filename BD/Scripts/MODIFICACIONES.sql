ALTER TABLE  `tb_02_sucursal` ADD  `RequiereIVA` BOOLEAN NOT NULL AFTER  `RequiereFE`;
UPDATE  `tb_02_sucursal` SET  `RequiereIVA` = 1;