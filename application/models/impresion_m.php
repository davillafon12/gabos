<?php 
Class impresion_m extends CI_Model{
    
    private $numPagina = 0;
    private $cantidadPaginas = 1;

    public function facturaPDF($empresa, $fhead, $fbody, $makeFile){
        require(PATH_FPDF_LIBRARY);
        $pdf = new FPDF('P','mm','A4');

        $cantidadProductos = sizeOf($fbody);
        $paginasADibujar = $this->paginasADibujar($cantidadProductos);
        $this->cantidadPaginas = $paginasADibujar + 1;
        $cantidadTotalArticulos = 0;
        while($paginasADibujar>=$this->numPagina){
                //Agregamos pag		
                $pdf->AddPage();			
                //Agregamos el encabezado
                $this->encabezadoDocumentoPDF('f', $empresa[0], $fhead[0], $pdf);
                //Agregamos Productos
                $inicio = $this->numPagina*30;
                if((($this->numPagina+1)*30)<$cantidadProductos){
                        $final = ($this->numPagina+1)*30;
                }else{
                        $final = $cantidadProductos;
                }

                $cantidadTotalArticulos += $this->printProducts($fbody, $inicio, $final-1, $pdf, $fhead[0]);
                //Definimos el pie de pagina
                $this->pieDocumentoPDF('f', $fhead[0], $empresa[0], $pdf, $cantidadTotalArticulos);
                $this->numPagina++;
        }

        if($makeFile){
            $pdf->Output(PATH_DOCUMENTOS_ELECTRONICOS.$fhead[0]->clave.".pdf",'F');
        }else{
           //Imprimimos documento
            $pdf->Output(); 
        }
    }
    
    private function fni($numero){		
            return number_format($numero,$this->configuracion->getDecimales());
    }

    private function fe($valor){
            if($valor){
                    return 'E';
            }else{
                    return ' ';
            }
    }

    private function paginasADibujar($productos){
            $aux = $productos / 30; // 33 es el maximo de productos por pagina
            $auxInteger = intval($aux);
            if($auxInteger<$aux){
                    return $auxInteger++;
            }elseif($auxInteger==$aux){
                    return $auxInteger;
            }
            $this->cantidadPaginas = $auxInteger+1;
            return $auxInteger;
    }

    private function observaciones($obs, &$pdf){
            //Agregamos el cuadro de observaciones
            $pdf->SetFont('Arial','B',12);
            $pdf->Text(11, 230, 'Observaciones:');
            $pdf->SetFont('Arial','',8);
            $pdf->SetXY(10, 231);	
            $pdf->MultiCell(100,5,$obs);
    }

    private function printProducts($productos, $inicio, $fin, &$pdf, $fhead){
            //Agregamos el apartado de productos
            $pdf->SetFont('Arial','B',12);
            $pdf->Text(90, 65, 'Productos');
            //Caja redondeada 1
            $pdf->RoundedRect(10, 67, 190, 158, 5, '12', 'D');
            //Divisores verticales de productos
            $pdf->Line(10, 74, 200, 74);		
            //$pdf->Line(10, 67, 200, 67); //Borde abajo productos
            //$pdf->Line(10, 60, 10, 240); //Borde lado izquierdo tabla
            $pdf->Line(40, 67, 40, 225); //Divisor de codigo y descripcion
            $pdf->Line(110, 67, 110, 225); //Divisor de descripcion y cantidad
            $pdf->Line(125, 67, 125, 225); //Divisor de cantidad y exento
            $pdf->Line(131, 67, 131, 225); //Divisor de exento y descuento
            $pdf->Line(145, 67, 145, 225); //Divisor de descuento y precio unitario
            $pdf->Line(172, 67, 172, 225); //Divisor de precio unitario y precio total		
            //$pdf->Line(200, 60, 200, 240); //Borde lado derecho tabla
            //$pdf->Line(10, 240, 200, 240); //Borde abajo productos
            //Encabezado de productos
            $pdf->SetFont('Arial','',10);
            $pdf->Text(13, 72, 'Código');
            $pdf->Text(58, 72, 'Descripción');
            $pdf->Text(112, 72, 'Cant.');
            $pdf->Text(126.5, 72, 'E');
            $pdf->Text(133, 72, 'Desc.');
            $pdf->Text(149, 72, 'P/Unitario');
            $pdf->Text(179, 72, 'P/Total');
            //Agregamos Productos
            $pdf->SetFont('Arial','',9);

            $pdf->SetXY(110, 75.3);		
            $sl = 5; //Salto de linea
            $pl = 79; //Primera linea

            $cantidadTotalArticulos = 0;
            for($cc = $inicio; $cc<=$fin; $cc++){
                    //Calculamos precio total con descuento
                    $total = $productos[$cc]->cantidad * ($productos[$cc]->precio - ($productos[$cc]->precio * ($productos[$cc]->descuento/100))); 
                    $precio = $productos[$cc]->precio;
                    //Valoramos si es en dolares
                    if($fhead->moneda=='dolares'){
                            $total = $total/$fhead->cambio;
                            $precio = $precio/$fhead->cambio;
                    }

                    $pdf->Text(11, $pl, $productos[$cc]->codigo);
                    $pdf->Text(41, $pl, substr($productos[$cc]->descripcion,0,33));
                    $pdf->cell(15,5,$productos[$cc]->cantidad,0,0,'C');
                    $pdf->cell(6,5,$this->fe($productos[$cc]->exento),0,0,'C');
                    $pdf->cell(14,5,$productos[$cc]->descuento);
                    $pdf->cell(27.5,5,$this->fni($precio),0,0,'R');
                    $pdf->cell(28,5,$this->fni($total),0,0,'R');			
                    $pdf->ln($sl);
                    $pdf->SetX(110);
                    $pl += $sl;
                    $cantidadTotalArticulos += $productos[$cc]->cantidad;
            }
            return $cantidadTotalArticulos;
    }
    
    private function encabezadoDocumentoPDF($tipo, $empresa, $encabezado, &$pdf){
		//var_dump($empresa);
		
		switch($tipo){
			case 'f':
                            $pdf->Line(10, 17, 200, 17);
                            $pdf->Line(100, 17, 100, 35);
                           // $pdf->Line(100, 35, 200, 35);
                            $pdf->Line(10, 45, 200, 45);
                            
                                //var_dump($empresa);
                                $pdf->SetFont('Arial','B',11);
                                $pdf->Cell(40,20,$empresa->nombre);
                                
                                $pdf->ln(9);
                                $pdf->SetFont('Arial','',10);
                                $pdf->Cell(40,10,$empresa->administrador);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Cédula: '.$empresa->cedula);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Teléfono: '.$empresa->telefono);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Email: '.$empresa->email);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Dirección: '.$empresa->direccion);
		
                                $pdf->SetFont('Arial','B',12);
                                $pdf->Text(11, 15, 'Factura Electrónica');
                                $pdf->SetFont('Arial','',11);
                                $pdf->Text(172, 15, 'Pag. # '.($this->numPagina+1)." de ".$this->cantidadPaginas);
                                
                                $pdf->SetFont('Arial','B',11);
                                $pdf->Text(101, 21, 'Cliente');
                                $pdf->SetFont('Arial','',10);
				$pdf->Text(101, 25, 'Identificación: '.$encabezado->cliente_ced);
				$pdf->SetXY(100, 25);
				$pdf->MultiCell(89, 5, 'Nombre: '.$encabezado->cliente_nom);
                                
                                // Descripcion general de la factura
                             
				$pdf->Text(11, 51, 'Consecutivo: '.$encabezado->consecutivoH);
                              
                                $pdf->Text(11, 56, 'Fecha: '.$encabezado->fecha);
                                $pdf->Text(11, 61, 'Moneda: '.$encabezado->moneda);
                                $encabezado->estado = trim($encabezado->estado) == "cobrada" ? "Facturada" : $encabezado->estado;
                                $pdf->Text(90, 51, "Estado: ".$encabezado->estado);
                                $pdf->Text(90, 56, 'Vendedor: '.$encabezado->vendedor);   
                                $pdf->Text(150, 51, 'Tipo: '.$encabezado->tipo);
                                $factor = $encabezado->moneda=='dolares' ? $encabezado->cambio : 1;
				
				switch($encabezado->tipo){
					case 'credito':
						$pdf->Text(150, 56, 'Días: '.$encabezado->diasCredito);
						$pdf->Text(150, 61, 'Vence: '.$encabezado->fechaVencimiento);
					break;
					case 'mixto':
						$pdf->Text(150, 56, 'Pago Tarjeta: '.$this->fni($encabezado->cantidadTarjeta/$factor));
						$pdf->Text(150, 61, 'Pago Contado: '.$this->fni($encabezado->cantidadContado/$factor));
					break;
					case 'apartado':
						$pdf->Text(150, 56, 'Abono: '.$this->fni($encabezado->abono/$factor));
						$pdf->Text(150, 61, 'Saldo: '.$this->fni(($encabezado->total - $encabezado->abono)/$factor));
					break;
				}
                                
				//Cuadro de numero de factura y hora/fecha
				//$pdf->Rect(108, 10, 92, 20, 'D');
                                
                                  //$pdf->Text(109, 21, 'Clave');
                                //$pdf->SetFont('Arial','',8);
				//$pdf->Text(120, 20.8, $encabezado->clave);
                            
                                
                                
				//Info del cliente
				//$pdf->SetFont('Arial','B',12);
				//$pdf->Text(12, 42, 'Cliente');
				
				//Caja redondeada 1
				//$pdf->RoundedRect(10, 37, 190, 23, 5, '1234', 'D');
				//Divisores				
				//$pdf->Line(10, 37, 10, 60); //Lado izquierdo borde
				//$pdf->Line(100, 37, 100, 60); //Centro caja
				//$pdf->Line(10, 60, 200, 60); //Borde de abajo
				//$pdf->Line(200, 37, 200, 60); //Lado derecho caja
				//$pdf->Line(10, 37, 200, 37); //Borde de arriba
				//$pdf->Line(10, 44, 200, 44); //Borde debajo cliente y descripcion
				//$pdf->Line(100, 55, 200, 55); //Borde arriba vendedor
				//$pdf->Line(145, 37, 145, 44); //Divisor descripcion y estado
				//Info de la factura
				//$pdf->SetFont('Arial','B',12);
				//$pdf->Text(102, 42, 'Descripción');
				//$pdf->Text(150, 42, 'Estado:');
				//$pdf->SetFont('Arial','',11);
				//$encabezado->estado = trim($encabezado->estado) == "cobrada" ? "Facturada" : $encabezado->estado;
				//$pdf->Text(170, 42, $encabezado->estado);
				//$pdf->SetFont('Arial','',11);
				//$pdf->Text(102, 49, 'Tipo: '.$encabezado->tipo);
				//$pdf->Text(102, 54, 'Moneda: '.$encabezado->moneda);
				//$pdf->Text(102, 59, 'Vendedor: '.$encabezado->vendedor);
				
				
			break;
			case 'nc':
				//Cuadro de numero de factura y hora/fecha
//				$pdf->Rect(108, 10, 92, 20, 'D');
//                                $pdf->SetFont('Arial','B',11);
//				$pdf->Text(109, 15, 'Consecutivo');
//                                $pdf->Text(109, 21, 'Clave');
//                                $pdf->Text(109, 27, 'Fecha');
//                                $pdf->SetFont('Arial','',11);
//				$pdf->Text(134, 15, $encabezado->consecutivoH);
//                                $pdf->Text(122, 27, $encabezado->fecha);
//                                $pdf->Text(172, 27, 'Pag. # '.($this->numPagina+1)." de ".$this->cantidadPaginas);
//                                $pdf->SetFont('Arial','',8);
//				$pdf->Text(120, 20.8, $encabezado->clave);
//				
//				//Info del cliente
//				$pdf->SetFont('Arial','B',12);
//				$pdf->Text(12, 42, 'Cliente');
//				$pdf->SetFont('Arial','',11);
//				$pdf->Text(12, 49, 'Identificación: '.$encabezado->cliente_cedula);
//				$pdf->SetXY(11, 50);
//				$pdf->MultiCell(89, 5, 'Nombre: '.$encabezado->cliente_nombre);
//				//Caja redondeada 1
//				$pdf->RoundedRect(10, 37, 190, 23, 5, '1234', 'D');
//				//Divisores					
//				$pdf->Line(100, 37, 100, 60); //Centro caja				
//				$pdf->Line(10, 44, 200, 44); //Borde debajo cliente y descripcion
//				$pdf->Line(100, 51, 200, 51); //Borde arriba vendedor
//				//Info de la factura
//				$pdf->SetFont('Arial','B',12);
//				$pdf->Text(102, 42, 'Descripción');
                            
                                $pdf->Line(10, 17, 200, 17);
                            $pdf->Line(100, 17, 100, 35);
                           // $pdf->Line(100, 35, 200, 35);
                            $pdf->Line(10, 45, 200, 45);
                            
                                //var_dump($empresa);
                                $pdf->SetFont('Arial','B',11);
                                $pdf->Cell(40,20,$empresa->nombre);
                                
                                $pdf->ln(9);
                                $pdf->SetFont('Arial','',10);
                                $pdf->Cell(40,10,$empresa->administrador);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Cédula: '.$empresa->cedula);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Teléfono: '.$empresa->telefono);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Email: '.$empresa->email);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Dirección: '.$empresa->direccion);
		
                                $pdf->SetFont('Arial','B',12);
                                $pdf->Text(11, 15, 'Nota Crédito Electrónica');
                                $pdf->SetFont('Arial','',11);
                                $pdf->Text(172, 15, 'Pag. # '.($this->numPagina+1)." de ".$this->cantidadPaginas);
                                
                                $pdf->SetFont('Arial','B',11);
                                $pdf->Text(101, 21, 'Cliente');
                                $pdf->SetFont('Arial','',10);
				$pdf->Text(101, 25, 'Identificación: '.$encabezado->cliente_cedula);
				$pdf->SetXY(100, 25);
				$pdf->MultiCell(89, 5, 'Nombre: '.$encabezado->cliente_nombre);
                                
                                // Descripcion general de la factura
                             
				$pdf->Text(11, 51, 'Consecutivo: '.$encabezado->consecutivoH);
                              
                                $pdf->Text(11, 56, 'Fecha: '.$encabezado->fecha);
                                $pdf->Text(11, 61, 'Moneda: '.$encabezado->moneda);
                            
                            $pdf->SetFont('Arial','B',11);
				$pdf->Text(100, 51, 'Esta nota crédito se aplica a la factura #'.$encabezado->factura_aplicar);
//				$pdf->SetFont('Arial','',11);
				
				//$pdf->Text(102, 59, 'Vendedor: '.$encabezado->vendedor);
			break;
			case 'nd':
                                $pdf->SetFont('Arial','B',14);
                                $pdf->Cell(40,10,$empresa->nombre);
                                $pdf->Line(10, 17, 100, 17);
                                $pdf->ln(5);
                                $pdf->SetFont('Arial','',10);
                                $pdf->Cell(40,10,'Cédula Jurídica: '.$empresa->cedula);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Teléfono: '.$empresa->telefono);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Email: '.$empresa->email);
				//Cuadro de numero de factura y hora/fecha
				$pdf->Rect(120, 10, 80, 20, 'D');
				$pdf->SetFont('Arial','B',16);
				$pdf->Text(122, 17, 'Nota Débito #'.$encabezado->nota);			
				$pdf->SetFont('Arial','',12);
				$pdf->Text(122, 22, 'Fecha y Hora: ');				
				$pdf->Text(122, 27, $encabezado->fecha);
				
				$pdf->Text(180, 27, 'Pag. # '.($this->numPagina+1));
				
				
				$encabezado -> entrega = $encabezado -> entrega ." - ".$this->empresa->getNombreEmpresa($encabezado -> entrega);
				$encabezado -> recibe = $encabezado -> recibe ." - ".$this->empresa->getNombreEmpresa($encabezado -> recibe);
				
				$pdf->Text(12, 42, 'Sucursal Entrega: '.$encabezado -> entrega);
				$pdf->Text(12, 49, 'Sucursal Recibe: '.$encabezado -> recibe);
								
			break;
			case 'p':
                                $pdf->SetFont('Arial','B',14);
                                $pdf->Cell(40,10,$empresa->nombre);
                                $pdf->Line(10, 17, 100, 17);
                                $pdf->ln(5);
                                $pdf->SetFont('Arial','',10);
                                $pdf->Cell(40,10,'Cédula Jurídica: '.$empresa->cedula);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Teléfono: '.$empresa->telefono);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Email: '.$empresa->email);
				//Cuadro de numero de factura y hora/fecha
				$pdf->Rect(120, 10, 80, 20, 'D');
				$pdf->SetFont('Arial','B',16);
				$pdf->Text(122, 17, 'Proforma #'.$encabezado->consecutivo);			
				$pdf->SetFont('Arial','',12);
				$pdf->Text(122, 22, 'Fecha y Hora: ');				
				$pdf->Text(122, 27, $encabezado->fecha);
				
				$pdf->Text(180, 27, 'Pag. # '.($this->numPagina+1));
				
				//Info del cliente
				$pdf->SetFont('Arial','B',12);
				$pdf->Text(12, 42, 'Cliente');
				$pdf->SetFont('Arial','',11);
				$pdf->Text(12, 49, 'Identificación: '.$encabezado->cliente_ced);
				$pdf->SetXY(11, 50);
				$pdf->MultiCell(89, 5, 'Nombre: '.$encabezado->cliente_nom);
				//Caja redondeada 1
				$pdf->RoundedRect(10, 37, 190, 23, 5, '1234', 'D');
				//Divisores				
				$pdf->Line(100, 37, 100, 60); //Centro caja
				$pdf->Line(10, 44, 200, 44); //Borde debajo cliente y descripcion
				$pdf->Line(100, 55, 200, 55); //Borde arriba vendedor
				//Info de la factura
				$pdf->SetFont('Arial','B',12);
				$pdf->Text(102, 42, 'Descripción');
				$pdf->SetFont('Arial','',11);
				$pdf->Text(102, 49, 'Moneda: '.$encabezado->moneda);
				
				//Preguntamos si los productos de esta factura ya fueron descontados de inventario
				if($encabezado->articulosDescontados){
						$pdf->Text(102, 54, '- - - ARTÍCULOS DESCONTADOS - - -');
				}
				
				$pdf->Text(102, 59, 'Vendedor: '.$encabezado->vendedor);				
			break;
			case 'r':
                                $pdf->SetFont('Arial','B',14);
                                $pdf->Cell(40,10,$empresa->nombre);
                                $pdf->Line(10, 17, 100, 17);
                                $pdf->ln(5);
                                $pdf->SetFont('Arial','',10);
                                $pdf->Cell(40,10,'Cédula Jurídica: '.$empresa->cedula);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Teléfono: '.$empresa->telefono);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Email: '.$empresa->email);
				//Cuadro de numero de factura y hora/fecha
				$pdf->Rect(120, 10, 80, 20, 'D');
				$pdf->SetFont('Arial','B',16);
				$pdf->Text(122, 17, 'Recibo de Dinero #'.$encabezado->recibo);			
				$pdf->SetFont('Arial','',12);
				$pdf->Text(122, 22, 'Fecha y Hora: ');				
				$pdf->Text(122, 27, $encabezado->fecha_recibo);
				
				$pdf->Text(180, 27, 'Pag. # '.($this->numPagina+1));
				
				//Info del cliente
				$pdf->SetFont('Arial','B',12);
				$pdf->Text(12, 42, 'Cliente');
				$pdf->SetFont('Arial','',11);
				$pdf->Text(12, 49, 'Identificación: '.$encabezado->cliente_cedula);
				$pdf->SetXY(11, 50);
				$pdf->MultiCell(89, 5, 'Nombre: '.$encabezado->cliente_nombre);
				//Caja redondeada 1
				$pdf->RoundedRect(10, 37, 190, 23, 5, '1234', 'D');
				//Divisores				
				$pdf->Line(100, 37, 100, 60); //Centro caja
				$pdf->Line(10, 44, 200, 44); //Borde debajo cliente y descripcion
				$pdf->Line(100, 55, 200, 55); //Borde arriba vendedor
				//Info de la factura
				$pdf->SetFont('Arial','B',12);
				$pdf->Text(102, 42, 'Descripción');
				$pdf->SetFont('Arial','',11);
				$pdf->Text(102, 49, 'Moneda: '.$encabezado->moneda);
				$pdf->Text(102, 54, 'Tipo de Pago: '.$encabezado->tipo_pago);
			break;
			case 't':
                                $pdf->SetFont('Arial','B',14);
                                $pdf->Cell(40,10,$empresa->nombre);
                                $pdf->Line(10, 17, 100, 17);
                                $pdf->ln(5);
                                $pdf->SetFont('Arial','',10);
                                $pdf->Cell(40,10,'Cédula Jurídica: '.$empresa->cedula);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Teléfono: '.$empresa->telefono);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Email: '.$empresa->email);
				//Cuadro de numero de factura y hora/fecha
				$pdf->Rect(120, 10, 80, 20, 'D');
				$pdf->SetFont('Arial','B',16);
				$pdf->Text(122, 17, 'Traspaso de Artículos #'.$encabezado->consecutivo);			
				$pdf->SetFont('Arial','',12);
				$pdf->Text(122, 22, 'Fecha y Hora: ');				
				$pdf->Text(122, 27, $encabezado->fecha);
				
				$pdf->Text(180, 27, 'Pag. # '.($this->numPagina+1));
				
				//Info del traspaso
				$pdf->SetFont('Arial','',11);
				$pdf->Text(12, 42, 'Suc. Salida:');		
				$pdf->Text(42, 42, $encabezado->salida." - ".substr($encabezado->nombre_salida,0,34));	
				$pdf->Text(12, 49, 'Suc. Entrada:');	
				$pdf->Text(42, 49, $encabezado->entrada." - ".substr($encabezado->nombre_entrada,0,34));
				//Caja redondeada 1
				$pdf->RoundedRect(10, 37, 190, 14, 2, '1234', 'D');
				//Divisores				
				$pdf->Line(110, 37, 110, 51); //Centro caja
				$pdf->Line(150, 37, 150, 44); //Despues de factura aplicada
				$pdf->Line(135, 44, 135, 51); //Despues de realizador
				$pdf->Line(40, 37, 40, 51); //Izquierda caja
				$pdf->Line(10, 44, 200, 44); //Borde debajo cliente y descripcion
			
				$pdf->Text(112, 42, 'Factura Traspasada:');
				$pdf->Text(152, 42, $encabezado->factura);
				$pdf->Text(112, 49, 'Realizador:');
				$pdf->Text(137, 49, $encabezado->usuario." - ".$encabezado->usuario_nombre);				
			break;
			case 'cc':
                                $pdf->SetFont('Arial','B',14);
                                $pdf->Cell(40,10,$empresa->nombre);
                                $pdf->Line(10, 17, 100, 17);
                                $pdf->ln(5);
                                $pdf->SetFont('Arial','',10);
                                $pdf->Cell(40,10,'Cédula Jurídica: '.$empresa->cedula);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Teléfono: '.$empresa->telefono);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Email: '.$empresa->email);
				//Cuadro de numero de factura y hora/fecha
				$pdf->Rect(120, 10, 80, 20, 'D');
				$pdf->SetFont('Arial','B',16);
				$pdf->Text(122, 17, 'Cierre de Caja #'.$encabezado->consecutivo);			
				$pdf->SetFont('Arial','',12);
				$pdf->Text(122, 22, 'Fecha y Hora: ');				
				$pdf->Text(122, 27, $encabezado->fecha);
				
				$pdf->Text(180, 27, 'Pag. # '.($this->numPagina+1));
								
			break;
			case 'con':
                                $pdf->SetFont('Arial','B',14);
                                $pdf->Cell(40,10,$empresa->nombre);
                                $pdf->Line(10, 17, 100, 17);
                                $pdf->ln(5);
                                $pdf->SetFont('Arial','',10);
                                $pdf->Cell(40,10,'Cédula Jurídica: '.$empresa->cedula);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Teléfono: '.$empresa->telefono);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Email: '.$empresa->email);
				//Cuadro de numero de factura y hora/fecha
				$pdf->Rect(120, 10, 80, 20, 'D');
				$pdf->SetFont('Arial','B',16);
				$pdf->Text(122, 17, 'Consignación #'.$encabezado->consecutivo);			
				$pdf->SetFont('Arial','',12);
				$pdf->Text(122, 22, 'Fecha y Hora: ');				
				$pdf->Text(122, 27, $encabezado->fecha);
				$pdf->SetFont('Arial','',11);
				$pdf->Text(172, 16, 'Pag. # '.($this->numPagina+1)." de ".$this->cantidadPaginas);
				
				//Info del cliente
				$pdf->SetFont('Arial','B',11);
				$pdf->Text(12, 41.5, 'Sucursal que entrega');
				$pdf->Text(12, 52.5, 'Sucursal que recibe');
				$pdf->SetFont('Arial','',10);
				$pdf->Text(12, 47, substr($encabezado->sucursal_entrega,0,39));
				$pdf->Text(12, 58, substr($encabezado->sucursal_recibe,0,39));
				$pdf->SetXY(11, 50);
				//$pdf->MultiCell(89, 5, 'Nombre: '.$encabezado->cliente_nom);
				//Caja redondeada 1
				$pdf->RoundedRect(10, 37, 190, 23, 5, '1234', 'D');
				//Divisores				
				//$pdf->Line(10, 37, 10, 60); //Lado izquierdo borde
				$pdf->Line(100, 37, 100, 60); //Centro caja
				$pdf->Line(10, 43, 200, 43); //Borde debajo sucursal que entrega y descripcion
				$pdf->Line(10, 49, 200, 49); //Borde debajo sucursal que entrega
				$pdf->Line(10, 54, 200, 54); //Borde debajo sucursal que entrega
				//Info de la factura
				$pdf->SetFont('Arial','B',11);
				$pdf->Text(102, 41.5, 'Cliente Utilizado Por Sucursal que Recibe');
				$pdf->Text(102, 52.5, 'Usuario que realizó la consignación');
				$pdf->SetFont('Arial','',10);
				$pdf->Text(102, 47, substr($encabezado->cliente, 0,49));
				$pdf->Text(102, 58, $encabezado->usuario);
				
			break;
			case 'ti':
                                $pdf->SetFont('Arial','B',14);
                                $pdf->Cell(40,10,$empresa->nombre);
                                $pdf->Line(10, 17, 100, 17);
                                $pdf->ln(5);
                                $pdf->SetFont('Arial','',10);
                                $pdf->Cell(40,10,'Cédula Jurídica: '.$empresa->cedula);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Teléfono: '.$empresa->telefono);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Email: '.$empresa->email);
				//Cuadro de numero de factura y hora/fecha
				$pdf->Rect(120, 10, 80, 20, 'D');
				$pdf->SetFont('Arial','B',16);
				$pdf->Text(122, 17, 'Traspaso #'.$encabezado->consecutivo);			
				$pdf->SetFont('Arial','',12);
				$pdf->Text(122, 22, 'Fecha y Hora: ');				
				$pdf->Text(122, 27, $encabezado->fecha);
				$pdf->SetFont('Arial','',11);
				$pdf->Text(172, 16, 'Pag. # '.($this->numPagina+1)." de ".$this->cantidadPaginas);
				
				//Info del cliente
				$pdf->SetFont('Arial','B',11);
				$pdf->Text(12, 41.5, 'Sucursal que entrega');
				$pdf->Text(12, 52.5, 'Sucursal que recibe');
				$pdf->SetFont('Arial','',10);
				$pdf->Text(12, 47, substr($encabezado->sucursal_entrega,0,39));
				$pdf->Text(12, 58, substr($encabezado->sucursal_recibe,0,39));
				$pdf->SetXY(11, 50);
				//$pdf->MultiCell(89, 5, 'Nombre: '.$encabezado->cliente_nom);
				//Caja redondeada 1
				$pdf->RoundedRect(10, 37, 190, 23, 5, '1234', 'D');
				//Divisores				
				//$pdf->Line(10, 37, 10, 60); //Lado izquierdo borde
				$pdf->Line(100, 37, 100, 60); //Centro caja
				$pdf->Line(10, 43, 200, 43); //Borde debajo sucursal que entrega y descripcion
				$pdf->Line(10, 49, 200, 49); //Borde debajo sucursal que entrega
				$pdf->Line(10, 54, 200, 54); //Borde debajo sucursal que entrega
				//Info de la factura
				$pdf->SetFont('Arial','B',11);
				$pdf->Text(102, 52.5, 'Usuario que realizó el traspaso');
				$pdf->SetFont('Arial','',10);
				$pdf->Text(102, 58, $encabezado->usuario);
				
			break;
			case 'cdc':
                                $pdf->SetFont('Arial','B',14);
                                $pdf->Cell(40,10,$empresa->nombre);
                                $pdf->Line(10, 17, 100, 17);
                                $pdf->ln(5);
                                $pdf->SetFont('Arial','',10);
                                $pdf->Cell(40,10,'Cédula Jurídica: '.$empresa->cedula);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Teléfono: '.$empresa->telefono);
                                $pdf->ln(4);
                                $pdf->Cell(40,10,'Email: '.$empresa->email);
				//Cuadro de numero de factura y hora/fecha
				$pdf->Rect(120, 10, 80, 20, 'D');
				$pdf->SetFont('Arial','B',16);
				$pdf->Text(122, 17, 'Cambio de Código #'.$encabezado->consecutivo);			
				$pdf->SetFont('Arial','',12);
				$pdf->Text(122, 22, 'Fecha y Hora: ');				
				$pdf->Text(122, 27, $encabezado->fecha);
				
				$pdf->Text(180, 27, 'Pag. # '.($this->numPagina+1));
				//Info de la factura
				$pdf->SetFont('Arial','B',12);
				$pdf->Text(12, 48, 'Usuario que realizó el cambio:');
				$pdf->SetFont('Arial','',11);
				$pdf->Text(12, 52.5, $encabezado->nombre." ".$encabezado->apellidos);
				
								
			break;
		}
	}
	
	private function pieDocumentoPDF($tipo, $encabezado, $empresa, &$pdf, $cantidadTotalArticulos){
		if($cantidadTotalArticulos != 0){
				//Cantidad total de articulos
				$pdf->SetXY(74, 225);
				if($tipo == 'ti'){
					$pdf->SetXY(150, 240);
				}
				if($tipo == 'nc'){
					$pdf->SetXY(74, 240);
				}
				$pdf->Cell(20,5,"Cantidad Total de Artículos:      ".$cantidadTotalArticulos);
		}
		switch($tipo){
			case 'f':
				//Parte de observaciones
				$this->observaciones($encabezado->observaciones, $pdf);
				//Leyenda de tributacion
				$pdf->SetFont('Arial','',8);
				$pdf->SetXY(10, 257);
                                $pdf->MultiCell(190,3,"Versión: 4.2",0,'C');
                                $pdf->MultiCell(190,3,"Clave: ".$encabezado->clave,0,'C');
				$pdf->MultiCell(190,3,$empresa->leyenda,0,'C');
				//Costos totales
				$subtotal = $encabezado->subtotal;
				$totalIVA = $encabezado->total_iva;
				$total = $encabezado->total;
				$retencion = $encabezado->retencion;
				//Valoramos si es en dolares
				if($encabezado->moneda=='dolares'){
					$subtotal = $subtotal/$encabezado->cambio;
					$totalIVA = $totalIVA/$encabezado->cambio;
					$total = $total/$encabezado->cambio;
					$retencion = $retencion/$encabezado->cambio;
				}
				$pdf->SetFont('Arial','',11);
				$pdf->SetXY(131, 225);	
				$pdf->Cell(41,7,'Subtotal:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($subtotal),1,0,'R');
				$pdf->SetXY(131, 232);	
				$pdf->Cell(41,7,'IVA:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($totalIVA+$retencion),1,0,'R');
/*
				$pdf->SetXY(131, 239);	
				$pdf->Cell(41,7,'Retención:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($retencion),1,0,'R');
*/
				$pdf->SetXY(131, 239);	
				$pdf->Cell(41,7,'Total:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($total),1,0,'R');
			break;			
			case 'nc':
				
				//Costos totales
				$pdf->SetFont('Arial','',11);
				$pdf->SetXY(131, 240);	
				$pdf->Cell(41,7,'Subtotal:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($encabezado->subtotal),1,0,'R');
				$pdf->SetXY(131, 247);	
				$pdf->Cell(41,7,'IVA:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($encabezado->total_iva+$encabezado->retencion),1,0,'R');
				//$pdf->SetXY(131, 254);	
				//$pdf->Cell(41,7,'Retención:',1,0,'R');
				//$pdf->Cell(28,7,$this->fni($encabezado->retencion),1,0,'R');
				$pdf->SetXY(131, 254);	
				$pdf->Cell(41,7,'Total:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($encabezado->total),1,0,'R');
                                
                                $pdf->SetFont('Arial','',8);
				$pdf->SetXY(10, 267);
                                $pdf->MultiCell(190,3,"Versión: 4.2",0,'C');
                                $pdf->MultiCell(190,3,"Clave: ".$encabezado->clave,0,'C');
				$pdf->MultiCell(190,3,$empresa->leyenda,0,'C');
			break;
			case 'nd':
				//Parte de observaciones
				$this->observaciones('', $pdf);
				//Costos totales
				$pdf->SetFont('Arial','',11);
				$pdf->SetXY(131, 225);	
				$pdf->Cell(41,7,'Subtotal:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($encabezado->subtotal),1,0,'R');
				$pdf->SetXY(131, 232);	
				$pdf->Cell(41,7,'IVA:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($encabezado->total_iva),1,0,'R');
				$pdf->SetXY(131, 239);	
				$pdf->Cell(41,7,'Total:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($encabezado->total),1,0,'R');
			break;
			case 'p':
				//Parte de observaciones
				$this->observaciones($encabezado->observaciones, $pdf);
				//Leyenda de tributacion
				$pdf->SetFont('Arial','',8);
				$pdf->SetXY(10, 255);	
				$pdf->MultiCell(190,3,$empresa->leyenda,0,'C');
				//Costos totales
				$subtotal = $encabezado->subtotal;
				$totalIVA = $encabezado->total_iva;
				$total = $encabezado->total;
				$retencion = $encabezado->retencion;
				//Valoramos si es en dolares
				if($encabezado->moneda=='dolares'){
					$subtotal = $subtotal/$encabezado->cambio;
					$totalIVA = $totalIVA/$encabezado->cambio;
					$total = $total/$encabezado->cambio;
					$retencion = $retencion/$encabezado->cambio;
				}
				$pdf->SetFont('Arial','',11);
				$pdf->SetXY(131, 225);	
				$pdf->Cell(41,7,'Subtotal:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($subtotal),1,0,'R');
				$pdf->SetXY(131, 232);	
				$pdf->Cell(41,7,'IVA:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($totalIVA),1,0,'R');
				$pdf->SetXY(131, 239);	
				$pdf->Cell(41,7,'Retención:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($retencion),1,0,'R');
				$pdf->SetXY(131, 246);	
				$pdf->Cell(41,7,'Total:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($total),1,0,'R');
			break;
			case 'r':
				//Costos totales
				$pdf->SetFont('Arial','',11);
				$pdf->SetXY(130, 95);	
				$pdf->Cell(42,7,'Saldo Anterior:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($encabezado->saldo_anterior),1,0,'R');
				$pdf->SetXY(130, 102);	
				$pdf->Cell(42,7,'Este Abono:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($encabezado->monto),1,0,'R');
				$pdf->SetXY(130, 109);	
				$pdf->Cell(42,7,'Saldo Actual:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($encabezado->saldo),1,0,'R');
			break;
			case 'con':
				//Leyenda de tributacion
				$pdf->SetFont('Arial','',8);
				$pdf->SetXY(10, 270);	
				$pdf->MultiCell(190,3, "ESTE DOCUMENTO NO TIENE LA VALIDEZ DE UNA FACTURA",0,'C');
				//Costos totales
				$subtotal = $encabezado->costo;
				$totalIVA = $encabezado->iva;
				$total = $encabezado->total;
				$retencion = $encabezado->retencion;
				$pdf->SetFont('Arial','',11);
				$pdf->SetXY(131, 240);	
				$pdf->Cell(41,7,'Subtotal:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($subtotal),1,0,'R');
				$pdf->SetXY(131, 247);	
				$pdf->Cell(41,7,'IVA:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($totalIVA),1,0,'R');
				$pdf->SetXY(131, 254);	
				$pdf->Cell(41,7,'Retención:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($retencion),1,0,'R');
				$pdf->SetXY(131, 261);	
				$pdf->Cell(41,7,'Total:',1,0,'R');
				$pdf->Cell(28,7,$this->fni($total),1,0,'R');
			break;	
		}		
	}
        
        public function notaCreditoPDF($empresa, $head, $productos, $makeFile){
		require(PATH_FPDF_LIBRARY);
		$pdf = new FPDF('P','mm','A4');
		
		$cantidadProductos = sizeOf($productos);
		$paginasADibujar = $this->paginasADibujar($cantidadProductos);
		$cantidadTotalArticulos = 0;
		while($paginasADibujar>=$this->numPagina){
			//Agregamos pag		
			$pdf->AddPage();			
			//Agregamos el encabezado
			$this->encabezadoDocumentoPDF('nc', $empresa, $head, $pdf);
			//Agregamos Productos
			$inicio = $this->numPagina*30;
			if((($this->numPagina+1)*30)<$cantidadProductos){
				$final = ($this->numPagina+1)*30;
			}else{
				$final = $cantidadProductos;
			}
			
			$cantidadTotalArticulos += $this->printProductsNotaCredito($productos, $inicio, $final-1, $pdf);
			//Definimos el pie de pagina
			$this->pieDocumentoPDF('nc', $head, $empresa, $pdf, $cantidadTotalArticulos);
			$this->numPagina++;
		}
		if($makeFile){
                    $pdf->Output(PATH_DOCUMENTOS_ELECTRONICOS.$head->clave.".pdf",'F');
                }else{
                   //Imprimimos documento
                    $pdf->Output(); 
                }
	}
        
        private function printProductsNotaCredito($productos, $inicio, $fin, &$pdf){
		//Agregamos el apartado de productos
		$pdf->SetFont('Arial','B',12);
		$pdf->Text(90, 65, 'Productos');
		//Caja redondeada 1
		$pdf->RoundedRect(10, 67, 190, 173, 5, '12', 'D');
		//Divisores verticales de productos
		$pdf->Line(10, 74, 200, 74);		
		$pdf->Line(30, 67, 30, 240); //Divisor de codigo y descripcion
		$pdf->Line(110, 67, 110, 240); //Divisor de descripcion y cantidad
		$pdf->Line(125, 67, 125, 240); //Divisor de cantidad y exento
		$pdf->Line(145, 67, 145, 240); //Divisor de descuento y precio unitario
		$pdf->Line(172, 67, 172, 240); //Divisor de precio unitario y precio total	
		//Encabezado de productos
		$pdf->SetFont('Arial','',10);
		$pdf->Text(13, 72, 'Código');
		$pdf->Text(58, 72, 'Descripción');
		$pdf->Text(112, 72, 'Bueno');
		$pdf->Text(126.5, 72, 'Defectuoso');
		$pdf->Text(151, 72, 'P/Unitario');
		$pdf->Text(179, 72, 'P/Total');
		//Agregamos Productos
		$pdf->SetFont('Arial','',9);
		
		$pdf->SetXY(110, 75.3);		
		$sl = 5; //Salto de linea
		$pl = 79; //Primera linea
		
		$cantidadTotalArticulos = 0;
		for($cc = $inicio; $cc<=$fin; $cc++){
			//Calculamos la cantidad
			$cantidad = $productos[$cc]->bueno + $productos[$cc]->defectuoso;
			
			//Calculamos precio total con descuento
			$total = $cantidad * $productos[$cc]->precio; 
			
			$pdf->Text(11, $pl, $productos[$cc]->codigo);
			$pdf->Text(31, $pl, substr($productos[$cc]->descripcion,0,33));
			$pdf->cell(15,5,$productos[$cc]->bueno,0,0,'C');
			$pdf->cell(20,5,$productos[$cc]->defectuoso,0,0,'C');
			$pdf->cell(27.5,5,$this->fni($productos[$cc]->precio),0,0,'R');
			$pdf->cell(28,5,$this->fni($total),0,0,'R');			
			$pdf->ln($sl);
			$pdf->SetX(110);
			$pl += $sl;
			$cantidadTotalArticulos += $cantidad;
		}
		return $cantidadTotalArticulos;
	}
    
}