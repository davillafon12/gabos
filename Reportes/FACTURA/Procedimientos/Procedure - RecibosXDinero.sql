USE garotas_bonitas_main_db; 
DELIMITER ;;
CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_RecibosXDinero
(
	IN paFechaI VARCHAR(30), 
	IN paFechaF VARCHAR(30),
	IN paSucursal VARCHAR(10),
	IN paCedula VARCHAR(30),
	IN paNombre VARCHAR(100), 
	IN paPendientes VARCHAR(10)
 )
 BEGIN
	SET @wherePrincipal 		= CONCAT(' where   cre.Credito_Sucursal_Codigo = ', '\'', paSucursal, '\'');
	
	SET @QUERY 			= CONCAT( 'SELECT  recd.Consecutivo,  
											recd.Recibo_Fecha, 
											recd.Recibo_Cantidad, 
											recd.Tipo_Pago, 
											recd.Anulado,
											cre.Credito_Saldo_Inicial, 
											cre.Credito_Saldo_Actual, 
											cre.Credito_Factura_Consecutivo as consecutivoFac, 
											concat(cli.Cliente_Nombre, \' \', cli.Cliente_Apellidos) as Nombre,
											cli.Cliente_cedula as identificacion
									FROM    tb_26_recibos_dinero recd 
											inner join tb_24_credito cre on cre.Credito_Id = recd.Credito
											inner join tb_03_cliente cli on cre.Credito_Cliente_Cedula = cli.Cliente_Cedula ');	
	IF paCedula <> 'null' THEN
		SET @wherePrincipal = CONCAT(@wherePrincipal, ' AND cli.Cliente_Cedula = ', '\'',paCedula, '\'' );
	END IF;
	IF paNombre <> 'null' THEN 
		SET @wherePrincipal = CONCAT(@wherePrincipal, ' AND concat(cli.Cliente_Nombre, \' \', cli.Cliente_Apellidos) LIKE  ', '\'%',paNombre, '%\'' );
	END IF; 
	IF paFechaI <> 'null' AND paFechaF <> 'null' then 
		SET @wherePrincipal = CONCAT (@wherePrincipal, ' AND UNIX_TIMESTAMP(recd.Recibo_Fecha) BETWEEN UNIX_TIMESTAMP(', '\'',paFechaI, '\'', ') AND UNIX_TIMESTAMP(', '\'',paFechaF, '\')');      
	END IF;
	IF paPendientes <> 'null' AND paFechaF <> 'null' then 
		SET @wherePrincipal = CONCAT (@wherePrincipal, ' AND cre.Credito_Saldo_Actual > 0');      
	END IF;
	SET @QUERY = CONCAT(@QUERY, @wherePrincipal, ' AND recd.Anulado = 0');
  -- select @QUERY as 'Resultado';  
  -- preparamos el objete Statement a partir de nuestra variable
   PREPARE smpt FROM @Query;
  -- ejecutamos el Statement
   EXECUTE smpt;
  -- liberamos la memoria 
   DEALLOCATE PREPARE smpt;
 END
;;