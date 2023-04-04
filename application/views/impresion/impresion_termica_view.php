<!DOCTYPE HTML>
<html>
	<head>
		<title>Modulo Impresión - Garotas Bonitas</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="shortcut icon" href="<?php echo base_url('application/images/impresion/header_icon.png'); ?>">
		<script src="<?php echo base_url('application/scripts/jquery-1.11.0.js'); ?>"></script>
		<script type="text/javascript" src="<?php echo base_url('application/scripts/impresion/deployJava.js'); ?>"></script>
		<script type="text/javascript" src="<?php echo base_url('application/scripts/impresion/qz-websocket.js'); ?>"></script>
		
		<script src="<?php echo base_url("application/scripts/impresion/tools.js?v=$javascript_cache_version"); ?>"></script>
			<script type="text/javascript">



/**
 * Deploy tray version of QZ, or
 * Optionally used to deploy multiple versions of the applet for mixed
 * environments.  Oracle uses document.write(), which puts the applet at the
 * top of the page, bumping all HTML content down.
 */
deployQZ();

function getCertificate(callback) {
    /*
    $.ajax({
        method: 'GET',
        url: 'assets/auth/public-key.txt',
        async: false,
        success: callback // Data returned from ajax call should be the site certificate
    });
    */

    //Non-ajax method, only include public key and intermediate key
    callback("-----BEGIN CERTIFICATE-----\n" +
        "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\n" +
        "-----END CERTIFICATE-----\n" +
        "--START INTERMEDIATE CERT--\n" +
        "-----BEGIN CERTIFICATE-----\n" +
        "MIIFEjCCA/qgAwIBAgICEAAwDQYJKoZIhvcNAQELBQAwgawxCzAJBgNVBAYTAlVT\n" +
        "MQswCQYDVQQIDAJOWTESMBAGA1UEBwwJQ2FuYXN0b3RhMRswGQYDVQQKDBJRWiBJ\n" +
        "bmR1c3RyaWVzLCBMTEMxGzAZBgNVBAsMElFaIEluZHVzdHJpZXMsIExMQzEZMBcG\n" +
        "A1UEAwwQcXppbmR1c3RyaWVzLmNvbTEnMCUGCSqGSIb3DQEJARYYc3VwcG9ydEBx\n" +
        "emluZHVzdHJpZXMuY29tMB4XDTE1MDMwMjAwNTAxOFoXDTM1MDMwMjAwNTAxOFow\n" +
        "gZgxCzAJBgNVBAYTAlVTMQswCQYDVQQIDAJOWTEbMBkGA1UECgwSUVogSW5kdXN0\n" +
        "cmllcywgTExDMRswGQYDVQQLDBJRWiBJbmR1c3RyaWVzLCBMTEMxGTAXBgNVBAMM\n" +
        "EHF6aW5kdXN0cmllcy5jb20xJzAlBgkqhkiG9w0BCQEWGHN1cHBvcnRAcXppbmR1\n" +
        "c3RyaWVzLmNvbTCCAiIwDQYJKoZIhvcNAQEBBQADggIPADCCAgoCggIBANTDgNLU\n" +
        "iohl/rQoZ2bTMHVEk1mA020LYhgfWjO0+GsLlbg5SvWVFWkv4ZgffuVRXLHrwz1H\n" +
        "YpMyo+Zh8ksJF9ssJWCwQGO5ciM6dmoryyB0VZHGY1blewdMuxieXP7Kr6XD3GRM\n" +
        "GAhEwTxjUzI3ksuRunX4IcnRXKYkg5pjs4nLEhXtIZWDLiXPUsyUAEq1U1qdL1AH\n" +
        "EtdK/L3zLATnhPB6ZiM+HzNG4aAPynSA38fpeeZ4R0tINMpFThwNgGUsxYKsP9kh\n" +
        "0gxGl8YHL6ZzC7BC8FXIB/0Wteng0+XLAVto56Pyxt7BdxtNVuVNNXgkCi9tMqVX\n" +
        "xOk3oIvODDt0UoQUZ/umUuoMuOLekYUpZVk4utCqXXlB4mVfS5/zWB6nVxFX8Io1\n" +
        "9FOiDLTwZVtBmzmeikzb6o1QLp9F2TAvlf8+DIGDOo0DpPQUtOUyLPCh5hBaDGFE\n" +
        "ZhE56qPCBiQIc4T2klWX/80C5NZnd/tJNxjyUyk7bjdDzhzT10CGRAsqxAnsjvMD\n" +
        "2KcMf3oXN4PNgyfpbfq2ipxJ1u777Gpbzyf0xoKwH9FYigmqfRH2N2pEdiYawKrX\n" +
        "6pyXzGM4cvQ5X1Yxf2x/+xdTLdVaLnZgwrdqwFYmDejGAldXlYDl3jbBHVM1v+uY\n" +
        "5ItGTjk+3vLrxmvGy5XFVG+8fF/xaVfo5TW5AgMBAAGjUDBOMB0GA1UdDgQWBBSQ\n" +
        "plC3hNS56l/yBYQTeEXoqXVUXDAfBgNVHSMEGDAWgBQDRcZNwPqOqQvagw9BpW0S\n" +
        "BkOpXjAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBCwUAA4IBAQAJIO8SiNr9jpLQ\n" +
        "eUsFUmbueoxyI5L+P5eV92ceVOJ2tAlBA13vzF1NWlpSlrMmQcVUE/K4D01qtr0k\n" +
        "gDs6LUHvj2XXLpyEogitbBgipkQpwCTJVfC9bWYBwEotC7Y8mVjjEV7uXAT71GKT\n" +
        "x8XlB9maf+BTZGgyoulA5pTYJ++7s/xX9gzSWCa+eXGcjguBtYYXaAjjAqFGRAvu\n" +
        "pz1yrDWcA6H94HeErJKUXBakS0Jm/V33JDuVXY+aZ8EQi2kV82aZbNdXll/R6iGw\n" +
        "2ur4rDErnHsiphBgZB71C5FD4cdfSONTsYxmPmyUb5T+KLUouxZ9B0Wh28ucc1Lp\n" +
        "rbO7BnjW\n" +
        "-----END CERTIFICATE-----\n");
}

function signRequest(toSign, callback) {
    /*
    $.ajax({
        method: 'GET',
        url: '/secure/url/for/sign-message.php?request=' + toSign,
        async: false,
        success: callback // Data returned from ajax call should be the signature
    });
    */

    //Send unsigned messages to socket - users will then have to Allow/Deny each print request
    callback();
}


/**
 * Automatically gets called when applet has loaded.
 */
function qzReady() {
	// Setup our global qz object
	var title = document.getElementById("title");
	if (qz) {
		try {
		findPrinter();
			if(notReady()){
				//alert("LISTO");		
				//$("#estado").html("Módulo de Impresión -<span class='status-ready'> LISTO  <img class='check' src='img/check.png' width='40'></span>");
				//setEventos();
				//imprimir();
			}
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

function qzSocketError(event) {
    document.getElementById("qz-status").style.background = "#F5A9A9";
    console.log('Error:');
    console.log(event);

    alert("Connection had an error:\n"+ event.reason);
}

function qzSocketClose(event) {
    document.getElementById("qz-status").style.background = "#A0A0A0";
    console.log('Close:');
    console.log(event);

    alert("Connection was closed:\n"+ event.reason);
}



/**
 * Returns whether or not the applet is not ready to print.
 * Displays an alert if not ready.
 */
function notReady() {
    // If applet is not loaded, display an error
    if (!isLoaded()) {
        return true;
    }
    // If a printer hasn't been selected, display a message.
    else if (!qz.getPrinter()) {
       
        return true;
    }
    return false;
}

/**
 * Returns is the applet is not loaded properly
 */
function isLoaded() {
    if (!qz) {
        alert('Error:\n\n\tPrint plugin is NOT loaded!');
        return false;
    } else {
        try {
            if (!qz.isActive()) {
                alert('Error:\n\n\tPrint plugin is loaded but NOT active!');
                return false;
            }
        } catch (err) {
            alert('Error:\n\n\tPrint plugin is NOT loaded properly!');
            return false;
        }
    }
    return true;
}

/**
 * Automatically gets called when "qz.print()" is finished.
 */
function qzDonePrinting() {
    // Alert error, if any
    if (qz.getException()) {
        alert('Error printing:\n\n\t' + qz.getException().getLocalizedMessage());
        qz.clearException();
        return;
    }

    
}

/***************************************************************************
 * Prototype function for finding the "default printer" on the system
 * Usage:
 *    qz.findPrinter();
 *    window['qzDoneFinding'] = function() { alert(qz.getPrinter()); };
 ***************************************************************************/
function useDefaultPrinter() {
    if (isLoaded()) {
        // Searches for default printer
        qz.findPrinter();

        // Automatically gets called when "qz.findPrinter()" is finished.
        window['qzDoneFinding'] = function() {
            // Alert the printer name to user
            var printer = qz.getPrinter();
            alert(printer !== null ? 'Default printer found: "' + printer + '"':
            'Default printer ' + 'not found');

            // Remove reference to this function
            window['qzDoneFinding'] = null;
        };
    }
}

/***************************************************************************
 * Prototype function for printing raw commands directly to the filesystem
 * Usage:
 *    qz.append("\n\nHello world!\n\n");
 *    qz.printToFile("C:\\Users\\Jdoe\\Desktop\\test.txt");
 ***************************************************************************/
function printToFile() {
    if (isLoaded()) {
        // Any printer is ok since we are writing to the filesystem instead
        qz.findPrinter();

        // Automatically gets called when "qz.findPrinter()" is finished.
        window['qzDoneFinding'] = function() {
            // Send characters/raw commands to qz using "append"
            // Hint:  Carriage Return = \r, New Line = \n, Escape Double Quotes= \"
            qz.append("A590,1570,2,3,1,1,N,\"Testing qz.printToFile() function\"\n");
            qz.append("P1\n");

            // Send characters/raw commands to file
            // i.e.  qz.printToFile("\\\\server\\printer");
            //       qz.printToFile("/home/user/test.txt");
            qz.printToFile("C:\\tmp\\qz-print_test-print.txt");

            // Remove reference to this function
            window['qzDoneFinding'] = null;
        };
    }
}

/***************************************************************************
 * Prototype function for printing raw commands directly to a hostname or IP
 * Usage:
 *    qz.append("\n\nHello world!\n\n");
 *    qz.printToHost("192.168.1.254", 9100);
 ***************************************************************************/
function printToHost() {
    if (isLoaded()) {
        // Any printer is ok since we are writing to a host address instead
        qz.findPrinter();

        // Automatically gets called when "qz.findPrinter()" is finished.
        window['qzDoneFinding'] = function() {
            // Send characters/raw commands to qz using "append"
            // Hint:  Carriage Return = \r, New Line = \n, Escape Double Quotes= \"
            qz.append("A590,1570,2,3,1,1,N,\"Testing qz.printToHost() function\"\n");
            qz.append("P1\n");

            // qz.printToHost(String hostName, int portNumber);
            // qz.printToHost("192.168.254.254");   // Defaults to 9100
            qz.printToHost("192.168.1.254", 9100);

            // Remove reference to this function
            window['qzDoneFinding'] = null;
        };
    }
}


/***************************************************************************
 * Prototype function for finding the closest match to a printer name.
 * Usage:
 *    qz.findPrinter('zebra');
 *    window['qzDoneFinding'] = function() { alert(qz.getPrinter()); };
 ***************************************************************************/
function findPrinter(name) {
    // Get printer name from input box
    var p = document.getElementById('printer');
    if (name) {
        p.value = name;
    }

    if (isLoaded()) {
        // Searches for locally installed printer with specified name
        qz.findPrinter(p.value);

        // Automatically gets called when "qz.findPrinter()" is finished.
        window['qzDoneFinding'] = function() {
            var p = document.getElementById('printer');
            var printer = qz.getPrinter();
			$("#estado").html("Módulo de Impresión -<span class='status-ready'> LISTO  <img class='check' src='"+(location.protocol+'//'+document.domain+(location.port ? ':'+location.port: ''))+"/application/images/impresion/check.png' width='40'></span>");
            imprimir();

            // Remove reference to this function
            window['qzDoneFinding'] = null;
        };
    }
}

/***************************************************************************
 * Prototype function for listing all printers attached to the system
 * Usage:
 *    qz.findPrinter('\\{dummy_text\\}');
 *    window['qzDoneFinding'] = function() { alert(qz.getPrinters()); };
 ***************************************************************************/
function findPrinters() {
    if (isLoaded()) {
        // Searches for a locally installed printer with a bogus name
        qz.findPrinter('\\{bogus_printer\\}');

        // Automatically gets called when "qz.findPrinter()" is finished.
        window['qzDoneFinding'] = function() {
            // Get the CSV listing of attached printers
            var printers = qz.getPrinters().replace(/,/g, '\n');
            alert(printers);

            // Remove reference to this function
            window['qzDoneFinding'] = null;
        };
    }
}

/***************************************************************************
 * Prototype function for printing raw EPL commands
 * Usage:
 *    qz.append('\nN\nA50,50,0,5,1,1,N,"Hello World!"\n');
 *    qz.print();
 ***************************************************************************/
function printEPL() {
    if (notReady()) { return; }

    // Send characters/raw commands to qz using "append"
    // This example is for EPL.  Please adapt to your printer language
    // Hint:  Carriage Return = \r, New Line = \n, Escape Double Quotes= \"
    qz.append('\nN\n');
    qz.append('q609\n');
    qz.append('Q203,26\n');
    qz.append('B5,26,0,1A,3,7,152,B,"1234"\n');
    qz.append('A310,26,0,3,1,1,N,"SKU 00000 MFG 0000"\n');
    qz.append('A310,86,0,3,1,1,N,"TEST PRINT SUCCESSFUL"\n');
    qz.append('A310,116,0,3,1,1,N,"FROM SAMPLE.HTML"\n');
    qz.append('A310,146,0,3,1,1,N,"QZINDUSTRIES.COM"\n');
    qz.appendImage(getPath() + 'assets/img/image_sample_bw.png', 'EPL', 150, 300);

    // Automatically gets called when "qz.appendImage()" is finished.
    window['qzDoneAppending'] = function() {
        // Append the rest of our commands
        qz.append('\nP1,1\n');

        // Tell the applet to print.
        qz.print();

        // Remove reference to this function
        window['qzDoneAppending'] = null;
    };
}

/***************************************************************************
 * Prototype function for printing raw ESC/POS commands
 * Usage:
 *    qz.append('\n\n\nHello world!\n');
 *    qz.print();
 ***************************************************************************/
function printESCP() {
    if (notReady()) { return; }

    // Append a png in ESCP format with single pixel density
    qz.appendImage(getPath() + "assets/img/image_sample_bw.png", "ESCP", "single");

    // Automatically gets called when "qz.appendImage()" is finished.
    window["qzDoneAppending"] = function() {
        // Append the rest of our commands
        qz.append('\nPrinted using qz-print plugin.\n\n\n\n\n\n');

        // Tell the apple to print.
        qz.print();

        // Remove any reference to this function
        window['qzDoneAppending'] = null;
    };
}


/***************************************************************************
 * Prototype function for printing raw ZPL commands
 * Usage:
 *    qz.append('^XA\n^FO50,50^ADN,36,20^FDHello World!\n^FS\n^XZ\n');
 *    qz.print();
 ***************************************************************************/
function printZPL() {
    if (notReady()) { return; }

    // Send characters/raw commands to qz using "append"
    // This example is for ZPL.  Please adapt to your printer language
    // Hint:  Carriage Return = \r, New Line = \n, Escape Double Quotes= \"
    qz.append('^XA\n');
    qz.appendImage(getPath() + 'assets/img/image_sample_bw.png', 'ZPLII');

    // Automatically gets called when "qz.appendImage()" is finished.
    window['qzDoneAppending'] = function() {
        // Append the rest of our commands
        qz.append('^FS\n');
        qz.append('^XZ\n');

        // Tell the apple to print.
        qz.print();

        // Remove any reference to this function
        window['qzDoneAppending'] = null;
    };
}


/***************************************************************************
 * Prototype function for printing syntactically proper raw commands directly
 * to a EPCL capable card printer, such as the Zebra P330i.  Uses helper
 * appendEPCL() to add the proper NUL, data length, escape character and
 * newline per spec:  https://km.zebra.com/kb/index?page=content&id=SO8390
 * Usage:
 *    appendEPCL('A1');
 *    qz.print();
 ***************************************************************************/
function printEPCL()  {
    if (notReady()) { return; }

    appendEPCL('+RIB 4');      // Monochrome ribbon
    appendEPCL('F');           // Clear monochrome print buffer
    appendEPCL('+C 8');        // Adjust monochrome intensity
    appendEPCL('&R');          // Reset magnetic encoder
    appendEPCL('&CDEW 0 0');   // Set R/W encoder to ISO default
    appendEPCL('&CDER 0 0');   // Set R/W encoder to ISO default
    appendEPCL('&SVM 0');      // Disable magnetic encoding verifications
    appendEPCL('T 80 600 0 1 0 45 1 QZ INDUSTRIES');	// Write text buffer
    appendEPCL('&B 1 123456^INDUSTRIES/QZ^789012');	// Write mag strip buffer
    appendEPCL('&E*');         // Encode magnetic data
    appendEPCL('I 10');        // Print card (10 returns to print ready pos.)
    appendEPCL('MO');          // Move card to output hopper

    qz.printToFile("C:\\tmp\\EPCL_Proper.txt");
    //qz.print();
}

/**
 * EPCL helper function that appends a single line of EPCL data, taking into
 * account special EPCL NUL characters, data length, escape character and
 * carriage return
 */
function appendEPCL(data) {
    if (data == null || data.length == 0) {
        return alert('Empty EPCL data, skipping!');
    }

    // Data length for this command, in 2 character Hex (base 16) format
    var len = (data.length + 2).toString(16);
    len = len.length < 2 ? '0' + len : len;

    // Append three NULs
    qz.appendHex('x00x00x00');
    // Append our command length, in base16 (hex)
    qz.appendHex('x' + len);
    // Append our command
    qz.append(data);
    // Append carriage return
    qz.append('\r');
}

/***************************************************************************
 * Prototype function for printing raw base64 encoded commands
 * Usage:
 *    qz.append64('SGVsbG8gV29ybGQh');
 *    qz.print();
 ***************************************************************************/
function print64() {
    if (notReady()) { return; }

    // Send base64 encoded characters/raw commands to qz using "append64"
    // This will automatically convert provided base64 encoded text into
    // text/ascii/bytes, etc.  This example is for EPL and contains an
    // embedded image.  Please adapt to your printer language
    qz.append64('Ck4KcTYwOQpRMjAzLDI2CkI1LDI2LDAsMUEsMyw3LDE1MixCLCIxMjM0IgpBMzEwLDI2LDAsMywx' +
            'LDEsTiwiU0tVIDAwMDAwIE1GRyAwMDAwIgpBMzEwLDU2LDAsMywxLDEsTiwiUVogUFJJTlQgQVBQ' +
            'TEVUIgpBMzEwLDg2LDAsMywxLDEsTiwiVEVTVCBQUklOVCBTVUNDRVNTRlVMIgpBMzEwLDExNiww' +
            'LDMsMSwxLE4sIkZST00gU0FNUExFLkhUTUwiCkEzMTAsMTQ2LDAsMywxLDEsTiwiUVpJTkRVU1RS' +
            'SUVTLkNPTSIKR1cxNTAsMzAwLDMyLDEyOCz/////////6SSSX///////////////////////////' +
            '//////////6UlUqX////////////////////////////////////8kqkpKP/////////////////' +
            '//////////////////6JUpJSVf//////////////////////////////////9KpKVVU+////////' +
            '//////////////////////////8KSSlJJf5/////////////////////////////////9KUqpVU/' +
            '/7////////////////////////////////9KqUkokf//P///////////////////////////////' +
            '+VKUqpZP//+P///////////////////////////////ElKUlSf///9f/////////////////////' +
            '////////+ipSkqin////y/////////////////////////////+lVUpUlX/////r////////////' +
            '/////////////////qlJKUql/////+n////////////////////////////BFKVKUl//////8v//' +
            '/////////////////////////zVSlKUp///////0f//////////////////////////wiSlSUpf/' +
            '//////q///////////////////////////KqlJUpV///////+R//////////////////////////' +
            '4UlKSpSX///////9T/////////6L///////////////BKlKpSqP///////1X////////0qg/23/V' +
            'VVVVVVf//8CSlJKklf///////kv///////+pS0/JP8AAAAAAB///wFSlSSpV///////+pf//////' +
            '/pUoq+qfwAAAAAAH//+AClSqpUT///////9S///////8pJUlkr+AAAAAAA///4AFJSSSUv//////' +
            '/yl///////KVUpTUv8AAAAAAH///gBKSqlVU////////lX//////6UkqoiU/wAAAAAA///+ABKpJ' +
            'Uko////////JH//////UpIiqlJ/AAAAAAD///wACkSUpJX///////6q//////6pVVSqiv4AAAAAA' +
            'f///AAJVVIqpP///////pI//////pSVtSSq/wAAAAAD///8AAJSlVJVf///////Sp/////8Sq//U' +
            'qL/ttttoAP///wAAUpVSpJ///////+pT/////qkn//UlH/////AB////AABKUSpSX///////5Sn/' +
            '///+lJ//+pS/////4AP///8AABKUkpVP///////ylP////1Kv//+qr/////AA////4AAKVVJUl//' +
            '/////+lKf////KS///8kv////8AH////gAAKSSpJR///////9Kq////9Kv///5Kf////gAf///+A' +
            'AAUlUqov///////1JT////lS////qn////8AD////4AABKpKSqf///////Skj///+kr////JH///' +
            '/wAf////wAACkqUlK///////8pKv///ypf///9V////+AD/////AAAFKUVSj///////wqlP///JT' +
            '////yR////wAP////8AAAFKqkpv///////JSlf//9Sv////U/////AB/////4AAAVIpKRf//////' +
            '+ElV///pS////8of///4AP/////gAAASZVKr///////4qkj///Sn////0v////AA//////AAABUS' +
            'VJH///////glJn//8pP////KH///8AH/////+AAACtUlVf//////+ClRP//qV////9K////gA///' +
            '///4AAACEpJK///////8BSqf/+lX////yr///8AD//////wAAAVUqVH///////gUlU//5Rf////R' +
            'P///gAf//////gAAApKqTP//////8AVSV//pU////6qf//+AD//////+AAAAqkki//////8AEpVL' +
            '/+qP////1L///wAP//////4AAACSVVB/////+AFUpKX/9KP////Sv//+AB///////AAAAEqSgH//' +
            '//+ACkpSUv/lV////6k///4AP//////+AAAAUlSgf////gAJKRUpf/ST////1J///AA///////4A' +
            'AAAVJVB////gAtVFUpV/8lX///+Vf//4AH///////gAAABKSSD///wASSVVJSR/1Vf///8kf//gA' +
            '///////+AAAABVUof//4AElUpKqqv/SL////1L//8AD///////4AAAABJJQ//8AFVJKVKSSP+qj/' +
            '///Kv//gAf///////gAAAAKSpT/+ACkqSlKUkqf5Rf///6S//+AD///////+AAAAAKqpP/ABJKVS' +
            'klKqU/xUf///qp//wAP///////4AAAAAkko+gASVKUlVKlKX/VK///9Sf/+AB////////gAAAACp' +
            'UrgAKqVKVJKSlKf+Sl///0kf/4AP///////+AAAAABSVIAFJUlKqSUpKV/0pX//8qr//AA//////' +
            '//8AAAAACklACSopKSVUqVKX/qpH//okv/4AH////////gAAAAAVVKBUpUqUkkpKSk//SSv/xVK/' +
            '/AAAAAAD////AAAAAAJKWSUpVKVVUqVSp/+qqH9SlR/8AAAAAAH///4AAAAABSUklJSSlJJKUkpf' +
            '/8klQFSo//gAAAAAA////wAAAAABVKqlUkqlSqkqqU//6pUqkkof8AAAAAAB/r//AAAAAAElEpSK' +
            'qSlSSpJKL//pUqpVKr/wAAAAAAP8v/8AAAAAAJLKUqkkpSqkqSVf//yUkpKSv+AAAAAAAfqf/wAA' +
            'AAAAVClKVVUoklUqqp///UpKVVS/wAAAAAAD+S//AAAAAAAlpSkkkpVKkpKSX///JVKTpR+AAAAA' +
            'AAH9X/8AAAAAABRUpVJUqqSpSUlf///SSk/Sv4AAAAAAA/y//wAAAAAAFSVUlSUkUkpUqr////VS' +
            'v9S/AAAAAAAB/3//AAAAAAAFUkpSlJMqqUpJP////13/pT////////////8AAAAAAAEpJSlSqUkk' +
            'pVS////////Un////////////wAAAAAABJVSlSpUqpUpJX///////8q/////////////gAAAAAAC' +
            'pSqkkpKSUpSSP///////5L////////////+AAAAAAACSkVVKSklKpVV///////+SX///////////' +
            '/4AAAAAAAFSqJKlSqqiVSX///////9U/////////////gAAAAAAASpVSlSkklVJU////////yr//' +
            '//////////+AAAAAAAAkpJSklKpKSUp////////kn////////////4AAAAAAABJSqlKqkqUqVf//' +
            '/////5K/////////////gAAAAAAACpUlKpJKUqlI////////1L////////////+AAAAAAAAFSVKS' +
            'SqkpFKX////////SX////////////4AAAAAAAAiklKlSSpTKKv///////9U/////////////wAAA' +
            'AAAABSpSlSqlSiVJ////////pV/////////////AAAAAAAAVUpSkklSlUqX////////Uv///////' +
            '/////8AAAAAAAAkqUpVJJSqpVf///////8pf////////////4AAAAAAAFJKUpKqUpJUT////////' +
            '4r/////////////wAAAAAAAKqVKVKUqSSVX///////+Uv/////////////gAAAAAAASUlKSkpKql' +
            'S////////+qf/////////////AAAAAAAEkpKUlUpJJCn////////iH///////////wAAAAAAAAAA' +
            'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA' +
            'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA' +
            'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA' +
            'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAH/4B+A8AH/AAAAA' +
            'AAAAAAAAAAAAAA//AAfwD4H4HwAAf/4H4DwB//gAAAAAAAAAAAAAAAAAD/+AB/APgfgfAAB//wfw' +
            'PAf/+AAAAAAAAAAAAAgAAAAP/8AH8AfB+D4AAH//B/g8D//4AAAAAAAAAAAADwAAAA//4A/4B8H4' +
            'PgAAfB+H+DwP4HgAAAAAAAAAAAAPwAAAD4fgD/gHw/w+AAB8D4f8PB+AGAAAAAAAAAAAAA/wAAAP' +
            'g+Af/AfD/D4AAHwPh/48HwAAAAAAAAAAAAAAB/4AAA+D4B98A+P8PAAAfA+Hvjw+AAAAAAAAAAAA' +
            'AAAB/4AAD4PgH3wD4/x8AAB8H4e/PD4AAAAAAAAAAAAAAAB/8AAPh8A+PgPn/nwAAH//B5+8Pg/4' +
            'AH/j/x/4/8f+AA/8AA//wD4+A+eefAAAf/4Hj7w+D/gAf+P/H/j/x/4AA/wAD/+APj4B5554AAB/' +
            '/AeP/D4P+AB/4/8f+P/H/gAD/AAP/wB8HwH3nvgAAH/wB4f8Pw/4AH/j/x/4/8f+AA/8AA//AH//' +
            'Af+f+AAAfAAHg/wfAPgAAAAAAAAAAAAAf/AAD5+A//+B/w/4AAB8AAeD/B+A+AAAAAAAAAAAAAH/' +
            'gAAPj8D//4D/D/AAAHwAB4H8H+D4AAAAAAAAAAAAB/4AAA+H4P//gP8P8AAAfAAHgPwP//gAAAAA' +
            'AAAAAAAP8AAAD4fh+A/A/w/wAAB8AAeA/Af/+AAAAAAAAAAAAA/AAAAPg/HwB8B+B+AAAHwAB4B8' +
            'Af/4AAAAAAAAAAAADwAAAA+B+fAHwH4H4AAAfAAHgHwAf4AAAAAAAAAAAAAIAAAAD4H/8Afgfgfg' +
            'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA' +
            'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA' +
            'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA' +
            'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA' +
            'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA' +
            'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA' +
            'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA' +
            'AAAAAAAAAAAAAAAAAAAAAAAAClAxLDEK');

    // Tell the apple to print.
    qz.print();
}

/***************************************************************************
 * Prototype function for controlling print spooling between pages
 * Usage:
 *    qz.setEndOfDocument('P1,1\r\n');
 *    qz.setDocumentsPerSpool('5');
 *    qz.appendFile('/path/to/file.txt');
 *    window['qzDoneAppending'] = function() { qz.print(); };
 ***************************************************************************/
function printPages() {
    if (notReady()) { return; }

    // Mark the end of a label, in this case  P1 plus a newline character
    // qz-print knows to look for this and treat this as the end of a "page"
    // for better control of larger spooled jobs (i.e. 50+ labels)
    qz.setEndOfDocument('P1,1\r\n');

    // The amount of labels to spool to the printer at a time. When
    // qz-print counts this many `EndOfDocument`'s, a new print job will
    // automatically be spooled to the printer and counting will start
    // over.
    qz.setDocumentsPerSpool("2");

    qz.appendFile(getPath() + "assets/epl_multiples.txt");

    // Automatically gets called when "qz.appendFile()" is finished.
    window['qzDoneAppending'] = function() {
        // Tell the applet to print.
        qz.print();

        // Remove reference to this function
        window['qzDoneAppending'] = null;
    };
}

/***************************************************************************
 * Prototype function for printing a single XML node containing base64
 * encoded data.
 * Usage:
 *    qz.appendXML('/path/to/file.xml');
 *    window['qzDoneAppending'] = function() { qz.print(); };
 ***************************************************************************/
function printXML() {
    if (notReady()) { return; }

    // Appends the contents of an XML file from a SOAP response, etc.
    // First parameter:  A valid complete URL is required for the XML file.
    // Second parameter:  A valid XML tag/node name containing
    //    base64 encoded data, i.e. <node_1>aGVsbG8gd29ybGQ=</node_1>
    // Example:
    //    qz.appendXML("http://yoursite.com/zpl.xml", "node_1");
    qz.appendXML(getPath() + "assets/zpl_sample.xml", "v7:Image");

    // Automatically gets called when "qz.appendXML()" is finished.
    window['qzDoneAppending'] = function() {
        // Tell the applet to print.
        qz.print();

        // Remove reference to this function
        window['qzDoneAppending'] = null;
    };
}

/***************************************************************************
 * Prototype function for printing hexadecimal formatted raw data
 *
 * Usage:
 *    qz.appendHex('00AABBCCDDEEFF');
 *    qz.appendHex('x00xAAxBBxCCxDDxEExFF');
 *    qz.print();
 ***************************************************************************/
function printHex() {
    if (notReady()) { return; }
    // Since 1.5.4, No backslashes needed (fixes \x00 NUL JavaScript bug)
    // Can be in format "1B00" or "x1Bx00"
    // EPL Sample Provided
    qz.appendHex("4e0d0a713630390d0a513230332c32360d0a42352c32362c");
    qz.appendHex("302c31412c332c372c3135322c422c2231323334220d0a41");
    qz.appendHex("3331302c32362c302c332c312c312c4e2c22534b55203030");
    qz.appendHex("303030204d46472030303030220d0a413331302c35362c30");
    qz.appendHex("2c332c312c312c4e2c22515a205072696e7420506c756769");
    qz.appendHex("6e220d0a413331302c38362c302c332c312c312c4e2c2254");
    qz.appendHex("657374207072696e74207375636365737366756c220d0a41");
    qz.appendHex("3331302c3131362c302c332c312c312c4e2c2266726f6d20");
    qz.appendHex("73616d706c652e68746d6c220d0a413331302c3134362c30");
    qz.appendHex("2c332c312c312c4e2c227072696e7448657828292066756e");
    qz.appendHex("6374696f6e2e220d0a50312c310d0a");

    // Send characters/raw commands to printer
    qz.print();
}

/***************************************************************************
 * Prototype function for printing a text or binary file containing raw
 * print commands.
 * Usage:
 *    qz.appendFile('/path/to/file.txt');
 *    window['qzDoneAppending'] = function() { qz.print(); };
 ***************************************************************************/
function printFile(file) {
    if (notReady()) { return; }

    // Append raw or binary text file containing raw print commands
    qz.appendFile(getPath() + "assets/" + file);

    // Automatically gets called when "qz.appendFile()" is finished.
    window['qzDoneAppending'] = function() {
        // Tell the applet to print.
        qz.print();

        // Remove reference to this function
        window['qzDoneAppending'] = null;
    };
}

/***************************************************************************
 * Prototype function for printing a graphic to a PostScript capable printer.
 * Not to be used in combination with raw printers.
 * Usage:
 *    qz.appendImage('/path/to/image.png');
 *    window['qzDoneAppending'] = function() { qz.printPS(); };
 ***************************************************************************/
function printImage(scaleImage) {
    if (notReady()) { return; }

    // Optional, set up custom page size.  These only work for PostScript printing.
    // setPaperSize() must be called before setAutoSize(), setOrientation(), etc.
    if (scaleImage) {
        qz.setPaperSize("8.5in", "11.0in");  // US Letter
        //qz.setPaperSize("210mm", "297mm");  // A4
        qz.setAutoSize(true);
        //qz.setOrientation("landscape");
        //qz.setOrientation("reverse-landscape");
    }

    //qz.setCopies(3);
    qz.setCopies(parseInt(document.getElementById("copies").value));

    // Append our image (only one image can be appended per print)
    qz.appendImage(getPath() + "assets/img/image_sample.png");

    // Automatically gets called when "qz.appendImage()" is finished.
    window['qzDoneAppending'] = function() {
        // Tell the applet to print PostScript.
        qz.printPS();

        // Remove reference to this function
        window['qzDoneAppending'] = null;
    };
}

/***************************************************************************
 * Prototype function for printing a PDF to a PostScript capable printer.
 * Not to be used in combination with raw printers.
 * Usage:
 *    qz.appendPDF('/path/to/sample.pdf');
 *    window['qzDoneAppending'] = function() { qz.printPS(); };
 ***************************************************************************/
function printPDF() {
    if (notReady()) { return; }
    // Append our pdf (only one pdf can be appended per print)
    qz.appendPDF(getPath() + "assets/pdf_sample.pdf");

    //qz.setCopies(3);
    qz.setCopies(parseInt(document.getElementById("copies").value));

    // Automatically gets called when "qz.appendPDF()" is finished.
    window['qzDoneAppending'] = function() {
        // Tell the applet to print PostScript.
        qz.printPS();

        // Remove reference to this function
        window['qzDoneAppending'] = null;
    };
}

/***************************************************************************
 * Prototype function for printing plain HTML 1.0 to a PostScript capable
 * printer.  Not to be used in combination with raw printers.
 * Usage:
 *    qz.appendHTML('<h1>Hello world!</h1>');
 *    qz.printPS();
 ***************************************************************************/
function printHTML() {
    if (notReady()) { return; }

    // Preserve formatting for white spaces, etc.
    colA = colA + '<color=red>Version:</color> ' + qz.getVersion() + '<br />';
    colA = colA + '<color=red>Visit:</color> http://code.google.com/p/jzebra';

    // HTML image
    var colB = '<img src="' + getPath() + 'assets/img/image_sample.png">';

    //qz.setCopies(3);
    qz.setCopies(parseInt(document.getElementById("copies").value));

    // Append our image (only one image can be appended per print)
    qz.appendHTML(
            '<html>' +
                '<table face="monospace" border="1px">' +
                '<tr height="6cm">' +
                    '<td valign="top">' + colA + '</td>' +
                    '<td valign="top">' + colB + '</td>' +
                '</tr>' +
                '</table>' +
            '</html>'
    );

    qz.printHTML();
}

/***************************************************************************
 * Prototype function for getting the primary IP or Mac address of a computer
 * Usage:
 *    qz.findNetworkInfo();
 *    window['qzDoneFindingNetwork'] = function() {alert(qz.getMac() + ',' +
	*       qz.getIP()); };
 ***************************************************************************/
function listNetworkInfo() {
    if (isLoaded()) {
        // Makes a quick connection to www.google.com to determine the active interface
        // Note, if you don't wish to use google.com, you can customize the host and port
        // qz.getNetworkUtilities().setHostname("qzindustries.com");
        // qz.getNetworkUtilities().setPort(80);
        qz.findNetworkInfo();

        // Automatically gets called when "qz.findPrinter()" is finished.
        window['qzDoneFindingNetwork'] = function() {
            alert("Primary adapter found: " + qz.getMac() + ", IP: " + qz.getIP());

            // Remove reference to this function
            window['qzDoneFindingNetwork'] = null;
        };
    }
}

/***************************************************************************
 * Prototype function for printing an HTML screenshot of the existing page
 * Usage: (identical to appendImage(), but uses html2canvas for png rendering)
 *    qz.setPaperSize("8.5in", "11.0in");  // US Letter
 *    qz.setAutoSize(true);
 *    qz.appendImage($("canvas")[0].toDataURL('image/png'));
 ***************************************************************************/
function printHTML5Page() {
    $("#qz-status").html2canvas({
        canvas: hidden_screenshot,
        onrendered: function() {
            if (notReady()) { return; }
            // Optional, set up custom page size.  These only work for PostScript printing.
            // setPaperSize() must be called before setAutoSize(), setOrientation(), etc.
            qz.setPaperSize("8.5in", "11.0in");  // US Letter
            qz.setAutoSize(true);
            qz.appendImage($("canvas")[0].toDataURL('image/png'));

            //qz.setCopies(3);
            qz.setCopies(parseInt(document.getElementById("copies").value));

            // Automatically gets called when "qz.appendFile()" is finished.
            window['qzDoneAppending'] = function() {
                // Tell the applet to print.
                qz.printPS();

                // Remove reference to this function
                window['qzDoneAppending'] = null;
            };
        }
    });
}

/***************************************************************************
 * Prototype function for logging a PostScript printer's capabilities to the
 * java console to expose potentially  new applet features/enhancements.
 * Warning, this has been known to trigger some PC firewalls
 * when it scans ports for certain printer capabilities.
 * Usage: (identical to appendImage(), but uses html2canvas for png rendering)
 *    qz.setLogPostScriptFeatures(true);
 *    qz.appendHTML("<h1>Hello world!</h1>");
 *    qz.printPS();
 ***************************************************************************/
function logFeatures() {
    if (isLoaded()) {
        var logging = qz.getLogPostScriptFeatures();
        console.log(logging);
        qz.setLogPostScriptFeatures(!logging);
        alert('Logging of PostScript printer capabilities to console set to "' + !logging + '"');
    }
}

/***************************************************************************
 * Prototype function to force Unix to use the terminal/command line for
 * printing rather than the Java-to-CUPS interface.  This will write the
 * raw bytes to a temporary file, then execute a shell command.
 * (i.e. lpr -o raw temp_file).  This was created specifically for OSX but
 * may work on several Linux versions as well.
 *    qz.useAlternatePrinting(true);
 *    qz.append('\n\nHello World!\n\n');
 *    qz.print();
 ***************************************************************************/
function useAlternatePrinting() {
    if (isLoaded()) {
        var alternate = qz.isAlternatePrinting();
        qz.useAlternatePrinting(!alternate);
        alert('Alternate CUPS printing set to "' + !alternate + '"');
    }
}


/***************************************************************************
 * Prototype function to list all available com ports available to this PC
 * used for RS232 communication.  Relies on jssc_qz.jar signed and in the
 * /dist/ folder.
 *    qz.findPorts();
 *    window['qzDoneFindingPorts'] = function() { alert(qz.getPorts()); };
 ***************************************************************************/
function listSerialPorts() {
    if (isLoaded()) {
        // Search the PC for communication (RS232, COM, tty) ports
        qz.findPorts();

        // Automatically called when "qz.findPorts()" is finished
        window['qzDoneFindingPorts'] = function() {
            var ports = qz.getPorts().split(",");
            for (var p in ports) {
                if (p == 0) {
                    document.getElementById("port_name").value = ports[p];
                }
                alert(ports[p]);
            }
            // Remove reference to this function
            window['qzDoneFindingPorts'] = null;
        };
    }
}


/***************************************************************************
 * Prototype function to open the specified communication port for 2-way
 * communication.
 *    qz.openPort('COM1');
 *    qz.openPort('/dev/ttyUSB0');
 *    window['qzDoneOpeningPort'] = function(port) { alert(port); };
 ***************************************************************************/
function openSerialPort() {
    if (isLoaded()) {
        qz.openPort(document.getElementById("port_name").value);

        // Automatically called when "qz.openPort()" is finished (even if it fails to open)
        window['qzDoneOpeningPort'] = function(portName) {
            if (qz.getException()) {
                alert("Could not open port [" + portName + "] \n\t" +
                        qz.getException().getLocalizedMessage());
                qz.clearException();
            } else {
                alert("Port [" + portName +  "] is open!");
            }
        };
    }
}

/***************************************************************************
 * Prototype function to close the specified communication port.
 *    qz.closePort('COM1');
 *    qz.closePort('/dev/ttyUSB0');
 *    window['qzDoneClosingPort'] = function(port) { alert(port); };
 ***************************************************************************/
function closeSerialPort() {
    if (isLoaded()) {
        qz.closePort(document.getElementById("port_name").value);

        // Automatically called when "qz.closePort() is finished (even if it fails to close)
        window['qzDoneClosingPort'] = function(portName) {
            if (qz.getException()) {
                alert("Could not close port [" + portName + "] \n\t" +
                        qz.getException().getLocalizedMessage());
                qz.clearException();
            } else {
                alert("Port [" + portName +  "] closed!");
            }
        };
    }
}


/***************************************************************************
 * Prototype function to send data to the open port
 *    qz.setSerialBegin(chr(2));
 *    qz.setSerialEnd(chr(13));
 *    qz.setSerialProperties("9600", "7", "1", "even", "none");
 *    qz.send("COM1", "\nW\n");
 ***************************************************************************/
function sendSerialData() {
    if (isLoaded()) {
        // Beginning and ending patterns that signify port has responded
        // chr(2) and chr(13) surround data on a Mettler Toledo Scale
        qz.setSerialBegin(chr(2));
        qz.setSerialEnd(chr(13));
        // Baud rate, data bits, stop bits, parity, flow control
        // "9600", "7", "1", "even", "none" = Default for Mettler Toledo Scale
        qz.setSerialProperties("9600", "7", "1", "even", "none");
        // Send raw commands to the specified port.
        // W = weight on Mettler Toledo Scale
        qz.send(document.getElementById("port_name").value, "\nW\n");

        // Automatically called when "qz.send()" is finished waiting for
        // a valid message starting with the value supplied for setSerialBegin()
        // and ending with with the value supplied for setSerialEnd()
        window['qzSerialReturned'] = function(portName, data) {
            if (qz.getException()) {
                alert("Could not send data:\n\t" + qz.getException().getLocalizedMessage());
                qz.clearException();
            } else {
                if (data == null || data == "") {       // Test for blank data
                    alert("No data was returned.")
                } else if (data.indexOf("?") !=-1) {    // Test for bad data
                    alert("Device not ready.  Please wait.")
                } else {                                // Display good data
                    alert("Port [" + portName + "] returned data:\n\t" + data);
                }
            }
        };
    }
}

/***************************************************************************
 ****************************************************************************
 * *                          HELPER FUNCTIONS                             **
 ****************************************************************************
 ***************************************************************************/


/***************************************************************************
 * Gets the current url's path, such as http://site.com/example/dist/
 ***************************************************************************/
function getPath() {
    var path = window.location.href;
    return path.substring(0, path.lastIndexOf("/")) + "/";
}

/**
 * Fixes some html formatting for printing. Only use on text, not on tags!
 * Very important!
 *   1.  HTML ignores white spaces, this fixes that
 *   2.  The right quotation mark breaks PostScript print formatting
 *   3.  The hyphen/dash autoflows and breaks formatting
 */
function fixHTML(html) {
    return html.replace(/\s/g, "&nbsp;").replace(/’/g, "'").replace(/-/g,"&#8209;");
}

/**
 * Equivalent of VisualBasic CHR() function
 */
function chr(i) {
    return String.fromCharCode(i);
}
</script>
		<link rel="stylesheet" type="text/css" href="<?php echo base_url('application/styles/impresion/estilo.css'); ?>">
	</head>
	<body>
		
		<input id="printer" value="TM" type="hidden"/>		
		<div class="contenedor">
			<img class="logo" src="<?php echo base_url('application/images/impresion/header_pic.png'); ?>"/>
			<hr>
			<h2 id="estado">Módulo de Impresión -<span class="status-fail"> NO LISTO  <img class="check" src="<?php echo base_url('application/images/impresion/error.png'); ?>" width="40"></span></h2>
			<hr>
			<input type="button" value="Reimprimir" id="boton_reimprimir" onclick="imprimir()"/>
		</div>
	</body>
</html>