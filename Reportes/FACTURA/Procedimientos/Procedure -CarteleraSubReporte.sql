USE gabo_trueque; 
DELIMITER ;;
CREATE DEFINER = 'consulta'@'%' PROCEDURE PA_CarteleraSubReporte
(
	IN paFechaI VARCHAR(30), 
	IN paFechaF VARCHAR(30),
	IN paSucursal VARCHAR(10),
	IN paCedula VARCHAR(30),
	IN paNombre VARCHAR(100)
 )
 BEGIN
	SET @wherePrincipal 		= CONCAT(' where   cre.Credito_Sucursal_Codigo = ', '\'', paSucursal, '\'', ' and f.factura_estado <> \'anulada\' and cre.Credito_Saldo_Actual > 0 ');
	
	SET @QUERY 			= CONCAT( 'SELECT  distinct f.Factura_Consecutivo,  
										f.Factura_Fecha_Hora,
										DATE_ADD(f.factura_fecha_hora, INTERVAL cre.Credito_Numero_Dias DAY) as Fecha_Final,
										c.Cliente_Cedula,
										CONCAT(c.Cliente_Nombre, \' \', c.Cliente_Apellidos) as Nombre, 
										cre.Credito_Saldo_Inicial,
										cre.Credito_Saldo_Actual
										FROM tb_07_factura f 
								  inner join tb_03_cliente c on f.TB_03_Cliente_Cliente_Cedula = c.Cliente_Cedula
								  left join tb_24_credito cre on cre.Credito_Factura_Consecutivo = f.Factura_Consecutivo and 
											cre.Credito_Sucursal_Codigo = f.TB_02_Sucursal_Codigo and 
											cre.credito_vendedor_codigo = f.Factura_Vendedor_Codigo and 
											cre.Credito_Vendedor_Sucursal = f.Factura_Vendedor_Sucursal and 
											cre.credito_cliente_cedula = f.TB_03_Cliente_Cliente_Cedula 
								  left join tb_26_recibos_dinero recd on cre.Credito_Id = recd.Credito ');	
	IF paCedula <> 'null' THEN
		SET @wherePrincipal = CONCAT(@wherePrincipal, ' AND c.Cliente_Cedula = ', '\'',paCedula, '\'' );
	END IF;
	IF paNombre <> 'null' THEN 						   
		SET @wherePrincipal = CONCAT(@wherePrincipal, ' AND concat(c.Cliente_Nombre, \' \', c.Cliente_Apellidos) LIKE  ','\'',paNombre, '\'');
	END IF; 
	IF paFechaI <> 'null' AND paFechaF <> 'null' then 
		SET @wherePrincipal = CONCAT (@wherePrincipal, ' AND UNIX_TIMESTAMP(f.Factura_Fecha_Hora) BETWEEN UNIX_TIMESTAMP(', '\'',paFechaI, '\'', ') AND UNIX_TIMESTAMP(', '\'',paFechaF, '\')');      
	END IF;
	SET @QUERY = CONCAT(@QUERY, @wherePrincipal, ' order by c.Cliente_Cedula, f.Factura_Fecha_Hora');
	
	SET @QUERY = CONCAT('select a.Cliente_Cedula,
						   a.Nombre, 
						   sum(a.Credito_Saldo_Inicial) as Credito_Saldo_Inicial, 
						   sum(a.Credito_Saldo_Actual) as Credito_Saldo_Actual
						  from ( ', @QUERY, ' ) as a group by a.Cliente_Cedula' );
  -- select @QUERY as 'Resultado';  
  -- preparamos el objete Statement a partir de nuestra variable
   PREPARE smpt FROM @Query;
  -- ejecutamos el Statement
   EXECUTE smpt;
  -- liberamos la memoria 
   DEALLOCATE PREPARE smpt;
 END
;;