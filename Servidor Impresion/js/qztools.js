function qzReady() {
	// Setup our global qz object
	window["qz"] = document.getElementById('qz');
	var title = document.getElementById("title");
	if (qz) {
		try {
			//alert("LISTO");		
			$("#estado").html("Módulo de Impresión -<span class='status-ready'> LISTO  <img class='check' src='img/check.png' width='40'></span>");
			setEventos();
			imprimir();
		} catch(err) { 
			//alert("NO LISTO");						
	  }
	}
	
}

function setEventos(){
	if (window.addEventListener) {
	  // Normal browsers
	  window.addEventListener("storage", handler, false);
	} else {
	  // for IE (why make your life more difficult)
	  window.attachEvent("onstorage", handler);
	};  
}

function handler(e) {
	console.log('Successfully communicate with other tab');
	console.log('Received data: ' + localStorage.getItem('data'));
}