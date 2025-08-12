var enterWasPressed = false;  //Se utiliza para saltarse la validacion de numero en formatearSaldoInput

function setSaldo(){
	saldo = calcularSaldoAPagar();
	saldo = saldo.format(2, 3, '.', ',');
	$("#saldo_a_pagar_input").val(saldo);
}

function calcularSaldoAPagar(){
	saldo = 0;
	for(i = 0; i<facturasSaldar.length; i++){
		//facturasSaldar es un array de herramientas.js
		//Contiene los ids de las facturas a saldar
		//Estos ids son ids de la BD, no los consecutivos de la factura
		saldo += getSaldoFromCredito(facturasSaldar[i]);
	}
	return saldo;
}

function getSaldoFromCredito(id){ //Le llega un id y lo procesa
	saldo = $("#saldo_"+id).html();
	saldo = toFloat(saldo);
	return saldo;
}

function toFloat(valor){ //Para convertir el innerhtml del saldo en float
	valor = valor = valor.replace(/\./g,'').replace(',','.');
	valor = parseFloat(valor);
	return valor;
}

function seleccionarSaldoInput(){
	saldo = $("#saldo_a_pagar_input").val();
	saldo = saldo.replace(/\./g,'');
	//saldo = saldo.replace(',','.');
	if(isNumber(saldo)){saldo = parseFloat(saldo);}
	$("#saldo_a_pagar_input").val(saldo);
	$("#saldo_a_pagar_input").select();		
}

function formatearSaldoInput(){
	saldoEscrito = $("#saldo_a_pagar_input").val();
	saldoEscrito = saldoEscrito.replace('.','');
	saldoEscrito = saldoEscrito.replace(',','.');
	if(isNumber(saldoEscrito)||enterWasPressed){
		saldoEscrito = parseFloat(saldoEscrito);		
		if(calcularSaldoAPagar()>=saldoEscrito){ //Si el saldo digitado es igual o menor al saldo a pagar
			$("#saldo_a_pagar_input").val(saldoEscrito.format(2, 3, '.', ','));
		}else{
			notyMsg('¡El saldo digitado es mayor al saldo a pagar, se ingresó saldo a pagar total!', 'error');
			setSaldo();
		}
	}else{
		notyMsg('¡Debe digitar un saldo válido!', 'error');
		$("#saldo_a_pagar_input").select();
	}
}

function filtrarEventosInputSaldo(e){
	if(e!=null){ 
		if (e.keyCode == 13) { //Si es enter
			enterWasPressed = true; //para que pase la validacion
			formatearSaldoInput();
			$("#boton_envio_cobro").focus();
			enterWasPressed = false; //lo ponemos en su estado original
		}
	}
}
