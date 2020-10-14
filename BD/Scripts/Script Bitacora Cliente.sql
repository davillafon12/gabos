CREATE TABLE gabo_fe.tb_60_Bitacora_cliente (
   IdBitacoraCliente INT(11) AUTO_INCREMENT NOT NULL,
   Cliente_Cedula VARCHAR(50) NOT NULL,
   Sucursal INT(11) NOT NULL,
   Usuario INT(11) NOT NULL,
   Trans_Fecha_Hora TIMESTAMP NOT NULL,
   Trans_Tipo VARCHAR(20) NOT NULL,
   Trans_IP VARCHAR(40) NOT NULL,
   Trans_Descripcion varchar(400) DEFAULT NULL,
  CONSTRAINT fk_tb_03_cliente_Cliente_Cedula FOREIGN KEY (Cliente_Cedula) REFERENCES gabo_fe.tb_03_cliente (Cliente_Cedula) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_tb_01_usuario_Usuario_Codigo FOREIGN KEY (Usuario) REFERENCES gabo_fe.tb_01_usuario (Usuario_Codigo) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT fk_tb_02_sucursal_Codigo FOREIGN KEY (Sucursal) REFERENCES gabo_fe.tb_02_sucursal (Codigo) ON DELETE NO ACTION ON UPDATE NO ACTION,
  PRIMARY KEY (IdBitacoraCliente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


