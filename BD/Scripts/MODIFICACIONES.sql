
ALTER TABLE  `tb_49_consignacion` ADD  `Estado` VARCHAR( 10 ) NOT NULL AFTER  `Total`;

update tb_49_consignacion set Estado = 'creada';