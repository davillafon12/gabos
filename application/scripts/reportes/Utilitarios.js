// Utilitarios 
// Patrón Module    

var Utilitarios = (function (window, undefined) {
    /*Función Privada*/
    // <descripcion>
    // Inicialización en español para la extensión 'UI date picker' para jQuery.
    // </descripcion>
    jQuery(function ($) {
        var anioActual = (new Date()).getFullYear();
        var anioMenor = anioActual - 100;
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: '&#x3C;Ant',
            nextText: 'Sig&#x3E;',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
            'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd-mm-yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: '',
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);

        $.datepicker.setDefaults({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy'
        });
    });


    // <descripcion>
    // Función que permite convertir u cuadro de texto en un calendario. Esta función es muy básica, si se ocupara que el calendario
    // de jquery sea más complejo, se debe programar por aparte o en su defecto definir otra función estándar como esta.
    // </descripcion>
    // <parametro name="selector" type="String">String que representa u selector de jquery.</parametro>
    // <parametro name="conBoton" type="String">Boolean que indica si el cuadro de texto muestra u icono de calendario al lado derecho.</parametro>
    // <parametro name="fechaParaMostrar" type="Date">Parámetro opcional. Objeto de tipo Date.</parametro>
    // <parametro name="fechaMaxima" type="Date">[Opcional]. Objeto Date para definir hasta que fecha máxima se puede establecer en el calendario.</parametro>
    // <parametro name="beforeShowFunction" type="Date">[Opcional]. Función anónima que se ejecuta antes de que se muestre el calendario.</parametro>
    // <parametro name="onSelectFunction" type="Date">[Opcional]. Función anónima que se ejecuta cuando se selecciona algo en el calendario.</parametro>
    // <parametro name="onCloseFunction" type="Date">[Opcional]. Función anónima que se ejecuta cuando se cierra el calendario.</parametro>
    function _InicializarCalendario(selector, conBoton, fechaParaMostrar, fechaMaxima) {
        var mostrarBoton = '';

        if (typeof conBoton !== 'undefined' && conBoton != null && $.type(conBoton) == 'boolean') {
            mostrarBoton = conBoton ? 'both' : '';
        }
        $(selector).datepicker({
            showOn: "both",
            buttonImageOnly: true,
            buttonImage: "/application/images/calendar.gif",
            buttonText: "Calendar",
            onClose: function () {
                $(this).valid();
            },
            //altFormat: "mm-dd-yyyy" 
        });

        if (typeof fechaParaMostrar !== 'undefined' && fechaParaMostrar != null && fechaParaMostrar != '' && $.type(fechaParaMostrar) == 'date') {
            $(selector).datepicker('setDate', fechaParaMostrar);
        }
    }
	
	// <descripcion>
    // Función que devuelve una fecha anterior a la actual por mes
    // </descripcion>
    // <parametro name="mes" type="String">se asigna cuantos meses va a tener de diferencia</parametro>
	function _obtenerFechaAnterior(mes){
		var now = new Date();
		var current; 
		if (now.getMonth() == 11) {
			current = new Date(now.getFullYear() - 1, mes, 1);
		} else {
			current = new Date(now.getFullYear(), now.getMonth() - mes, 1);
		}
		return current; 
	}

    // <descripcion>
    // Funcion encargada de desactivarle la funcionalidad de teclas a un formulario de la pagina 
    // </descripcion>
    // <parametro name="formulario" type="form">Obtiene el formulario que se desea modificar</parametro>
    function _pfDesactivarTeclas(formulario) {
        formulario.keypress(function (e) {
            var code = e.keyCode || e.which;
            if (code == 13) {
                e.preventDefault();
            }
        });
    }

    // <descripcion>
    // Funcion encargada de devolver true en caso de que la fecha inicial sea menor a la fecha final, en caso contrario devuelve false 
    // </descripcion>
    // <parametro name="finicial" type="date">fecha inicial que se utilizara de referencia para hacer la comparación </parametro>
    // <parametro name="ffinal" type="date">fecha final a la cual se le realizara la comparación con la fecha inicial</parametro>
    // <ejemplo>fnFechaMenor($("#txtFechaInicial"), $("#txtFechaFinal").val())</ejemplo>
    // <formato>formato de la fecha entrante: 01-12-2014 / día-mes-año</formato>
    // <formatoCorrecto>formato de la fecha entrante: 12-01-2014 / mes-día-año</formatoCorrecto>
    function _pfVerificarFechaMayor(finicial, ffinal) {
        fecha1 = $(finicial).val().split("-");
        inicial = new Date(fecha1[2], fecha1[1] - 1, fecha1[0]);
        fecha2 = ffinal.split("-");
        ffinal = new Date(fecha2[2], fecha2[1] - 1, fecha2[0]);
        if (inicial <= ffinal)
            return true;
        else
            return false;
    }

    //<description>
    // Metodo encargado de verificar el formato correcto de una fecha
    //</description>
    function _pfValidarFecha(fecha) {
        var date = Date.parse(fecha);
        if (isNaN(date)) {
            return false;
        }
        var comp = fecha.split('-');
        if (comp.length != 3) {
            return false;
        }
        var d = parseInt(comp[0], 10);
        var m = parseInt(comp[1], 10);
        var y = parseInt(comp[2], 10);
        var date = new Date(y, m - 1, d);
        return (date.getFullYear() == y && date.getMonth() + 1 == m && date.getDate() == d);
    };

    //<description>
    // pfUwords se encarga de obtener una cadena de información en mayusculas o minusculas, convertir la información en minusculas 
    // agregando la primer letra de cada palabra en mayuscula, se utiliza cuando el nombre viene en mayusculas para convertirlo.
    //</description>
    function _pfUcwords(cadena) {
        var str = cadena.toLowerCase();
        return str.replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g,
            function ($1) {
                return $1.toUpperCase();
            });
    }
	
	
	
    return {       
        fnInicializarCalendario: function (selector, conBoton, fechaParaMostrar, fechaMaxima) {
            return _InicializarCalendario(selector, conBoton, fechaParaMostrar, fechaMaxima); 
        },       
		fnObtenerFechaAnterior: function (mes) {
            return _obtenerFechaAnterior(mes);
        },       		
        fnDesactivarTeclas: function (formulario) {
            return _pfDesactivarTeclas(formulario); 
        },
        fnVerificarFechaMayor: function (finicial, ffinal) {
            return _pfVerificarFechaMayor(finicial, ffinal); 
        },
        lfValidarFecha: function (fecha) {
            return _pfValidarFecha(fecha);
        },       
        lfUcwords: function (cadena) {
            return _pfUcwords(cadena); 
        }
    };//fin del return
})();//fin de variable Utilitarios

