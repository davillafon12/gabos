ALTER TABLE  `tb_04_articulos_proforma` ADD  `TipoCodigo` VARCHAR( 2 ) NOT NULL ,
ADD  `UnidadMedida` VARCHAR( 10 ) NOT NULL;

update tb_04_articulos_proforma set `TipoCodigo` = '01', `UnidadMedida` = 'Unid';

ALTER TABLE  `tb_51_lista_consignacion` ADD  `TipoCodigo` VARCHAR( 2 ) NOT NULL ,
ADD  `UnidadMedida` VARCHAR( 10 ) NOT NULL;

update tb_51_lista_consignacion set `TipoCodigo` = '01', `UnidadMedida` = 'Unid';