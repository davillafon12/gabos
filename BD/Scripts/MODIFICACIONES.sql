UPDATE `garotas_bonitas_main_db`.`tb_03_cliente` SET `Cliente_Numero_Pago` = '1' WHERE `tb_03_cliente`.`Cliente_Cedula` = 1;
UPDATE `garotas_bonitas_main_db`.`tb_03_cliente` SET `Cliente_Numero_Pago` = '2' WHERE `tb_03_cliente`.`Cliente_Cedula` = 0;

-- Creación de Usuario de Consultas con permisos de lectura y ejecución de Procedimientos 

create user 'consulta'@'%' identified by 'consulta';
GRANT SELECT ON garotas_bonitas_main_db . * TO  'consulta'@'%'
IDENTIFIED BY  'consulta'
GRANT SELECT , EXECUTE ON `garotas\_bonitas\_main\_db` . * TO 'consulta'@'%';



