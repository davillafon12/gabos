ALTER TABLE `tb_07_factura` CHANGE `Factura_Retencion` `Factura_Retencion` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `tb_07_factura` CHANGE `Factura_Entregado_Vuelto` `Factura_Entregado_Vuelto` VARCHAR(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '0';
ALTER TABLE `tb_07_factura` CHANGE `Factura_Recibido_Vuelto` `Factura_Recibido_Vuelto` VARCHAR(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '0';
ALTER TABLE `tb_10_proforma` CHANGE `Proforma_Estado` `Proforma_Estado` VARCHAR(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL;
ALTER TABLE `tb_10_proforma` CHANGE `Proforma_Retencion` `Proforma_Retencion` DOUBLE NOT NULL DEFAULT '0';
ALTER TABLE `tb_16_authclientes` CHANGE `AuthClientes_Id` `AuthClientes_Id` INT NOT NULL DEFAULT '1';
ALTER TABLE `tb_12_transacciones` CHANGE `Trans_Tipo` `Trans_Tipo` VARCHAR(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL;
ALTER TABLE `tb_19_deposito` CHANGE `Deposito_Id` `Deposito_Id` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `tb_30_notas_debito` CHANGE `Observaciones` `Observaciones` VARCHAR(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL;