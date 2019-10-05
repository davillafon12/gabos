USE gabo_fe; 
DELIMITER ;;
CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_EstadoCuentaCliente
(
	IN paFechaI VARCHAR(30), 
	IN paFechaF VARCHAR(30),
	IN paSucursal VARCHAR(10),
	IN paCedula VARCHAR(30),
	IN paEstadoFactura VARCHAR(150)
 )
 BEGIN
SELECT  * FROM (
		SELECT  fac.Factura_Consecutivo as 'Consecutivo' ,  
				'Credito' as 'Tipo_Transaccion',
				cli.cliente_cedula, 
        CONCAT('(', cli.Codigo_Pais_Telefono, ') ', cli.Cliente_Celular ) as 'Celular', 
        cli.Cliente_Direccion, 
				CONCAT(cli.Cliente_Nombre, ' ', cli.Cliente_Apellidos) as Nombre, 
				DATE_FORMAT(cre.Credito_Fecha_Expedicion,'%d/%m/%Y') as 'Fecha_Emi',  
				cre.Credito_Numero_Dias as Dias,
				DATE_FORMAT(ADDDATE(cre.Credito_Fecha_Expedicion, INTERVAL cre.Credito_Numero_Dias DAY),'%d/%m/%Y') as 'Fecha_Cad',
				0 as 'Debito', 
			   cre.Credito_Saldo_Inicial as 'Credito',
         suc.Sucursal_Nombre, 
         suc.Sucursal_Cedula, 
         suc.Sucursal_Telefono 
		FROM   tb_03_cliente cli 
		INNER JOIN tb_07_factura fac on cli.Cliente_Cedula = fac.TB_03_Cliente_Cliente_Cedula 
    INNER JOIN tb_02_sucursal suc on fac.TB_02_Sucursal_Codigo = suc.Codigo
		INNER JOIN tb_24_credito cre on fac.Factura_Consecutivo = cre.Credito_Factura_Consecutivo and 
		fac.TB_02_Sucursal_Codigo = cre.Credito_Sucursal_Codigo
		WHERE  cli.Cliente_Cedula = paCedula and 
		fac.TB_02_Sucursal_Codigo = paSucursal and 
		fac.Factura_Estado IN (paEstadoFactura) and 
		cre.Credito_Saldo_Actual != '0'
		and UNIX_TIMESTAMP(cre.Credito_Fecha_Expedicion) BETWEEN UNIX_TIMESTAMP(paFechaI) AND UNIX_TIMESTAMP(paFechaF)
		union
		SELECT 
				red.Consecutivo,
				'Debito' as 'Tipo_Transaccion',
				cli.cliente_cedula, 
        CONCAT('(', cli.Codigo_Pais_Telefono, ') ', cli.Cliente_Celular ) as 'Celular', 
        cli.Cliente_Direccion, 
				CONCAT(cli.Cliente_Nombre, ' ', cli.Cliente_Apellidos) as Nombre, 
				DATE_FORMAT(red.Recibo_Fecha,'%d/%m/%Y') as 'Fecha_Emi',  
				0 as Dias,
				DATE_FORMAT(red.Recibo_Fecha,'%d/%m/%Y') as 'Fecha_Cad',
				red.Recibo_Cantidad as 'Debito', 
				0 as 'Credito',
         suc.Sucursal_Nombre, 
         suc.Sucursal_Cedula, 
         suc.Sucursal_Telefono 
		FROM   tb_03_cliente cli 
		INNER JOIN tb_07_factura fac on cli.Cliente_Cedula = fac.TB_03_Cliente_Cliente_Cedula 
    INNER JOIN tb_02_sucursal suc on fac.TB_02_Sucursal_Codigo = suc.Codigo
		INNER JOIN tb_24_credito cre on fac.Factura_Consecutivo = cre.Credito_Factura_Consecutivo and 
		fac.TB_02_Sucursal_Codigo = cre.Credito_Sucursal_Codigo
		INNER JOIN tb_26_recibos_dinero red on cre.Credito_Id = red.Credito
		WHERE  cli.Cliente_Cedula = paCedula and 
		fac.TB_02_Sucursal_Codigo = paSucursal and 
		fac.Factura_Estado IN (paEstadoFactura) and 
		cre.Credito_Saldo_Actual != '0'
		and UNIX_TIMESTAMP(red.Recibo_Fecha) BETWEEN UNIX_TIMESTAMP(paFechaI) AND UNIX_TIMESTAMP(paFechaF)
		union
		SELECT 
				ncre.Consecutivo,  
				'Nota Credito' as 'Tipo_Transaccion',
				cli.cliente_cedula, 
        CONCAT('(', cli.Codigo_Pais_Telefono, ') ', cli.Cliente_Celular ) as 'Celular', 
        cli.Cliente_Direccion, 
				CONCAT(cli.Cliente_Nombre, ' ', cli.Cliente_Apellidos) as Nombre, 
				DATE_FORMAT(ncre.Fecha_Creacion,'%d/%m/%Y') as 'Fecha_Emi',  
				0 as Dias,
				DATE_FORMAT(ncre.Fecha_Creacion,'%d/%m/%Y') as 'Fecha_Cad',
				SUM((pncre.Cantidad_Bueno * pncre.Precio_Unitario) + (pncre.Cantidad_Defectuoso * pncre.Precio_Unitario)) as 'Debito', 
				0 as 'Credito',
         suc.Sucursal_Nombre, 
         suc.Sucursal_Cedula, 
         suc.Sucursal_Telefono 
		FROM   tb_03_cliente cli 
		INNER JOIN tb_07_factura fac on cli.Cliente_Cedula = fac.TB_03_Cliente_Cliente_Cedula 
    INNER JOIN tb_02_sucursal suc on fac.TB_02_Sucursal_Codigo = suc.Codigo
		INNER JOIN tb_24_credito cre on fac.Factura_Consecutivo = cre.Credito_Factura_Consecutivo and 
		fac.TB_02_Sucursal_Codigo = cre.Credito_Sucursal_Codigo
		INNER JOIN tb_27_notas_credito ncre on cli.Cliente_Cedula = ncre.Cliente and
		cre.Credito_Sucursal_Codigo = ncre.Sucursal and 
		fac.Factura_Consecutivo = ncre.Consecutivo and 
		ncre.Es_Anulacion = 0
		INNER JOIN tb_28_productos_notas_credito pncre on ncre.Consecutivo = pncre.Nota_Credito_Consecutivo and 
		pncre.sucursal = ncre.Sucursal
		WHERE  cli.Cliente_Cedula = paCedula and 
		fac.TB_02_Sucursal_Codigo = paSucursal and 
		fac.Factura_Estado IN (paEstadoFactura) and 
		cre.Credito_Saldo_Actual != '0'
		and UNIX_TIMESTAMP(ncre.Fecha_Creacion) BETWEEN UNIX_TIMESTAMP(paFechaI) AND UNIX_TIMESTAMP(paFechaF)
		group by cli.cliente_cedula, 
			   fac.Factura_Consecutivo, 
			   fac.Factura_Fecha_Hora
		) AS T ORDER BY T.Fecha_Emi, T.Consecutivo;
 END ;;