<?php 
Class impresion_m extends CI_Model{
    
    private $numPagina = 0;
    private $cantidadPaginas = 1;
    
    public function facturaPDF($empresa, $fhead, $fbody, $makeFile){
        $this->numPagina = 0;
        $this->cantidadPaginas = 1;
        require_once(PATH_FPDF_LIBRARY);
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
            $filePath = "/tmp/".$fhead[0]->clave.".pdf";
			$pdf->Output($filePath,'F');
			$tipoDocumento = "fe";
			if(property_exists($fhead[0], "isFEC")){
				if($fhead[0]->isFEC){
					$tipoDocumento = "fec";
				}
			}
            $this->storeFile($fhead[0]->clave.".pdf", $tipoDocumento, $filePath, null, $fhead[0]->fecha);
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
            $pdf->Line(122, 67, 122, 225); //Divisor de cantidad y exento
            $pdf->Line(128, 67, 128, 225); //Divisor de exento y descuento
            $pdf->Line(148, 67, 148, 225); //Divisor de descuento y precio unitario
            $pdf->Line(159, 67, 159, 225); //Divisor de precio unitario y IVA	
			$pdf->Line(179, 67, 179, 225); //IVA y precio total		
            //$pdf->Line(200, 60, 200, 240); //Borde lado derecho tabla
            //$pdf->Line(10, 240, 200, 240); //Borde abajo productos
            //Encabezado de productos
            $pdf->SetFont('Arial','',10);
            $pdf->Text(13, 72, 'Código');
            $pdf->Text(58, 72, 'Descripción');
            $pdf->Text(111, 72, 'Cant.');
            $pdf->Text(124, 72, 'E');            
            $pdf->Text(130.5, 72, 'P/Unitario');
			$pdf->Text(149, 72, 'Desc.');
			$pdf->Text(166, 72, 'IVA');
            $pdf->Text(183, 72, 'P/Total');
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

					//$precioFinal = $precio + $precio * 100 / $piva;
					//$precioFinal = $precio (1 + 100/$piva);
					//$precioFinal / (1 + 100/$piva) = $precio

					$ivaArticulo = property_exists($productos[$cc], 'porcentaje_iva') ? $productos[$cc]->porcentaje_iva : $fhead->porcentaje_iva;

					//echo "ivaArticulo: ".$ivaArticulo."\n";

					$ivaArticulo = $ivaArticulo == 0 ? 0 : ($precio / (1 + 100/$ivaArticulo));

					//echo "ivaArticulo2: ".$ivaArticulo."\n";
					//echo "precio: ".$precio."\n";

					$precioUnitarioSinIva = $precio - $ivaArticulo;

					//echo "precioUnitarioSinIva: ".$precioUnitarioSinIva."\n";

					$ivaArticuloTotal = $productos[$cc]->cantidad * $ivaArticulo;
                    //Valoramos si es en dolares
                    if($fhead->moneda=='dolares'){
                            $total = $total/$fhead->cambio;
                            $precio = $precio/$fhead->cambio;
                    }

                    $pdf->Text(11, $pl, substr($productos[$cc]->codigo,0,15));
                    $pdf->Text(41, $pl, substr($productos[$cc]->descripcion,0,35));
                    $pdf->cell(12,5,$productos[$cc]->cantidad,0,0,'C');
                    $pdf->cell(6,5,$this->fe($productos[$cc]->exento),0,0,'C');
					$pdf->cell(20,5,$this->fni($precioUnitarioSinIva),0,0,'R');
                    $pdf->cell(11,5,$this->fni($productos[$cc]->descuento));                    
					$pdf->cell(20,5,$this->fni($ivaArticuloTotal),0,0,'R');
                    $pdf->cell(21.5,5,$this->fni($total),0,0,'R');			
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
								if($encabezado->clave !== false){
									if(property_exists($encabezado, "isFEC")){
										$pdf->Text(11, 15, 'Factura Electrónica de Compra');
									}else{
										$pdf->Text(11, 15, $encabezado->isTE ? 'Tiquete Electrónico' : 'Factura Electrónica');
									}
								}else{
									$pdf->Text(11, 15, 'Factura');
								}
								
                                
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
								if($encabezado->clave !== false){
									$pdf->Text(11, 15, 'Nota Crédito Electrónica');
								}else{
									$pdf->Text(11, 15, 'Nota Crédito');
								}
                                
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
				if($encabezado->clave !== false){
					$pdf->MultiCell(190,3,"Versión: 4.4",0,'C');
                    $pdf->MultiCell(190,3,"Clave: ".$encabezado->clave,0,'C');
				}
                                
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
				
				if($encabezado->clave !== false){
					$pdf->SetXY(131, 232);	
					$pdf->Cell(41,7,'IVA:',1,0,'R');
					$pdf->Cell(28,7,$this->fni($totalIVA+$retencion),1,0,'R');
				}
				
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
				$pdf->SetXY(10, 260);
				if($encabezado->clave !== false){
					$pdf->MultiCell(190,3,"Versión: 4.4",0,'C');
                    $pdf->MultiCell(190,3,"Clave: ".$encabezado->clave,0,'C');
				}
                                
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
                $this->numPagina = 0;
                $this->cantidadPaginas = 1;
		require_once(PATH_FPDF_LIBRARY);
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
			
			$cantidadTotalArticulos += $this->printProductsNotaCredito($productos, $inicio, $final-1, $pdf, $head);
			//Definimos el pie de pagina
			$this->pieDocumentoPDF('nc', $head, $empresa, $pdf, $cantidadTotalArticulos);
			$this->numPagina++;
		}
		if($makeFile){
                    $filePath = "/tmp/".$head->clave.".pdf";
                    $pdf->Output($filePath,'F');
                    $this->storeFile($head->clave.".pdf", "nc", $filePath);
                }else{
                   //Imprimimos documento
                    $pdf->Output(); 
                }
	}
        
        private function printProductsNotaCredito($productos, $inicio, $fin, &$pdf, $head){
		//Agregamos el apartado de productos
		$pdf->SetFont('Arial','B',12);
		$pdf->Text(90, 65, 'Productos');
		//Caja redondeada 1
		$pdf->RoundedRect(10, 67, 190, 173, 5, '12', 'D');
		//Divisores verticales de productos
		$pdf->Line(10, 74, 200, 74);		
		$pdf->Line(30, 67, 30, 240); //Divisor de codigo y descripcion
		$pdf->Line(110, 67, 110, 240); //Divisor de descripcion y bueno
		$pdf->Line(125, 67, 125, 240); //Divisor de bueno y defectuoso
		$pdf->Line(140, 67, 140, 240); //Divisor de defectuoso y precio
		$pdf->Line(160, 67, 160, 240); //Divisor de precio y iva
		$pdf->Line(180, 67, 180, 240); //Divisor de iva y precio total	
		//Encabezado de productos
		$pdf->SetFont('Arial','',10);
		$pdf->Text(13, 72, 'Código');
		$pdf->Text(58, 72, 'Descripción');
		$pdf->Text(112, 72, 'Bueno');
		$pdf->Text(126.5, 72, 'Defect.');
		$pdf->Text(142, 72, 'P/Unitario');
		$pdf->Text(168, 72, 'IVA');
		$pdf->Text(184, 72, 'P/Total');
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

			//$precioFinal = $precio + $precio * 100 / $piva;
			//$precioFinal = $precio (1 + 100/$piva);
			//$precioFinal / (1 + 100/$piva) = $precio

			$ivaArticulo = $head->iva == 0 ? 0 : ($productos[$cc]->precio / (1 + 100/$head->iva));
			$precioSinIva = $productos[$cc]->precio - $ivaArticulo;
			$totalIvaLinea = $cantidad * $ivaArticulo;
			

			
			$pdf->Text(11, $pl, $productos[$cc]->codigo);
			$pdf->Text(31, $pl, substr($productos[$cc]->descripcion,0,33));
			$pdf->cell(15,5,$productos[$cc]->bueno,0,0,'C');
			$pdf->cell(15,5,$productos[$cc]->defectuoso,0,0,'C');
			$pdf->cell(20,5,$this->fni($precioSinIva),0,0,'R');
			$pdf->cell(20,5,$this->fni($totalIvaLinea),0,0,'R');
			$pdf->cell(20.5,5,$this->fni($total),0,0,'R');			
			$pdf->ln($sl);
			$pdf->SetX(110);
			$pl += $sl;
			$cantidadTotalArticulos += $cantidad;
		}
		return $cantidadTotalArticulos;
	}
    
	function recibosPDF($recibos, $empresa, $makeFile = false)
	{
		require(PATH_FPDF_LIBRARY);
		$pdf = new FPDF('P', 'mm', 'A4');
		foreach ($recibos as $recibo) {
			//Agregamos una pagina
			$pdf->AddPage();
			//Obtenemos el recibo
			$recibo = $recibo[0];
			//Agregamos el encabezado
			$this->encabezadoDocumentoPDF('r', $empresa, $recibo, $pdf);
			//Agregamos el cuerpo del recibo
			//Caja redondeada 1
			$pdf->RoundedRect(10, 67, 190, 28, 5, '12', 'D');
			$pdf->SetFont('Arial', '', 11);
			$pdf->Text(12, 72, 'Recibimos de: ');
			$pdf->Text(42, 72, $recibo->cliente_cedula . " - " . $recibo->cliente_nombre);

			$V = new EnLetras();
			$con_letra = $V->ValorEnLetras($recibo->monto, $recibo->moneda);

			$pdf->Text(12, 79, 'La suma de: ');
			$pdf->Text(42, 79, $this->fni($recibo->monto) . " - $con_letra");
			$pdf->Text(12, 86, 'Por concepto de abono a la factura: ');
			$pdf->Text(12, 93, 'Consecutivo # ');
			$pdf->Text(42, 93, $recibo->factura);
			$pdf->Text(62, 93, 'Emitida el ');
			$pdf->Text(84, 93, $recibo->fecha_expedicion);
			$pdf->Text(132, 93, 'Por el monto de ');
			$pdf->Text(164, 93, $this->fni($recibo->Saldo_inicial));

			//Comentarios
			$pdf->Text(12, 100, 'Comentarios: ');
			$pdf->SetXY(12, 102);
			$pdf->SetFont('Arial', '', 8);
			$pdf->MultiCell(118, 3, $recibo->comentarios, 0, 'L');

			//Divisores
			$pdf->Line(10, 74, 200, 74); //Primer divisor
			$pdf->Line(10, 81, 200, 81); //Segundo divisor
			$pdf->Line(10, 88, 200, 88); //Tercer divisor
			//$pdf->Line(10, 95, 200, 95); //Cuarto divisor
			$pdf->Line(40, 67, 40, 81); //Primer divisor vertical
			//$pdf->Line(40, 88, 40, 95); //Segundo divisor vertical
			$pdf->Line(60, 88, 60, 95); //Tercer divisor vertical
			//$pdf->Line(82, 88, 82, 95); //Cuarto divisor vertical
			$pdf->Line(130, 88, 130, 95); //Quinto divisor vertical
			//$pdf->Line(162, 88, 162, 95); //Sexto divisor vertical

			//Definimos el pie de pagina
			$this->pieDocumentoPDF('r', $recibo, $empresa, $pdf, 0);

			//Aumentamos la cantidad de paginas
			$this->numPagina++;
		}

		if($makeFile){
            $filePath = "/tmp/".$recibo->clave.".pdf";
			$pdf->Output($filePath,'F');
			$tipoDocumento = "rep";
            $this->storeFile($recibo->clave.".pdf", $tipoDocumento, $filePath, null, $recibo->fecha);
        }else{
           //Imprimimos documento
            $pdf->Output(); 
        }
	}

	
	
}


class EnLetras
{
	var $Void = "";
	var $SP = " ";
	var $Dot = ".";
	var $Zero = "0";
	var $Neg = "Menos";

	function ValorEnLetras($x, $Moneda)
	{
		$s = "";
		$Ent = "";
		$Frc = "";
		$Signo = "";

		if (floatVal($x) < 0)
			$Signo = $this->Neg . " ";
		else
			$Signo = "";

		if (intval(number_format($x, 2, '.', '')) != $x) //<- averiguar si tiene decimales
			$s = number_format($x, 2, '.', '');
		else
			$s = number_format($x, 2, '.', '');

		$Pto = strpos($s, $this->Dot);

		if ($Pto === false) {
			$Ent = $s;
			$Frc = $this->Void;
		} else {
			$Ent = substr($s, 0, $Pto);
			$Frc =  substr($s, $Pto + 1);
		}

		if ($Ent == $this->Zero || $Ent == $this->Void)
			$s = "Cero ";
		elseif (strlen($Ent) > 7) {
			$s = $this->SubValLetra(intval(substr($Ent, 0,  strlen($Ent) - 6))) .
				"Millones " . $this->SubValLetra(intval(substr($Ent, -6, 6)));
		} else {
			$s = $this->SubValLetra(intval($Ent));
		}

		if (substr($s, -9, 9) == "Millones " || substr($s, -7, 7) == "Millón ")
			$s = $s . "de ";

		$s = $s . $Moneda;

		/*if($Frc != $this->Void)
    {
       $s = $s . " " . $Frc. "/100";
       //$s = $s . " " . $Frc . "/100";
    }
    $letrass=$Signo . $s . " M.N."; */
		return ($Signo . $s);
	}


	function SubValLetra($numero)
	{
		$Ptr = "";
		$n = 0;
		$i = 0;
		$x = "";
		$Rtn = "";
		$Tem = "";

		$x = trim("$numero");
		$n = strlen($x);

		$Tem = $this->Void;
		$i = $n;

		while ($i > 0) {
			$Tem = $this->Parte(intval(substr($x, $n - $i, 1) .
				str_repeat($this->Zero, $i - 1)));
			if ($Tem != "Cero")
				$Rtn .= $Tem . $this->SP;
			$i = $i - 1;
		}


		//--------------------- GoSub FiltroMil ------------------------------
		$Rtn = str_replace(" Mil Mil", " Un Mil", $Rtn);
		while (1) {
			$Ptr = strpos($Rtn, "Mil ");
			if (!($Ptr === false)) {
				if (!(strpos($Rtn, "Mil ", $Ptr + 1) === false))
					$this->ReplaceStringFrom($Rtn, "Mil ", "", $Ptr);
				else
					break;
			} else break;
		}

		//--------------------- GoSub FiltroCiento ------------------------------
		$Ptr = -1;
		do {
			$Ptr = strpos($Rtn, "Cien ", $Ptr + 1);
			if (!($Ptr === false)) {
				$Tem = substr($Rtn, $Ptr + 5, 1);
				if ($Tem == "M" || $Tem == $this->Void);
				else
					$this->ReplaceStringFrom($Rtn, "Cien", "Ciento", $Ptr);
			}
		} while (!($Ptr === false));

		//--------------------- FiltroEspeciales ------------------------------
		$Rtn = str_replace("Diez Un", "Once", $Rtn);
		$Rtn = str_replace("Diez Dos", "Doce", $Rtn);
		$Rtn = str_replace("Diez Tres", "Trece", $Rtn);
		$Rtn = str_replace("Diez Cuatro", "Catorce", $Rtn);
		$Rtn = str_replace("Diez Cinco", "Quince", $Rtn);
		$Rtn = str_replace("Diez Seis", "Dieciseis", $Rtn);
		$Rtn = str_replace("Diez Siete", "Diecisiete", $Rtn);
		$Rtn = str_replace("Diez Ocho", "Dieciocho", $Rtn);
		$Rtn = str_replace("Diez Nueve", "Diecinueve", $Rtn);
		$Rtn = str_replace("Veinte Un", "Veintiun", $Rtn);
		$Rtn = str_replace("Veinte Dos", "Veintidos", $Rtn);
		$Rtn = str_replace("Veinte Tres", "Veintitres", $Rtn);
		$Rtn = str_replace("Veinte Cuatro", "Veinticuatro", $Rtn);
		$Rtn = str_replace("Veinte Cinco", "Veinticinco", $Rtn);
		$Rtn = str_replace("Veinte Seis", "Veintiseís", $Rtn);
		$Rtn = str_replace("Veinte Siete", "Veintisiete", $Rtn);
		$Rtn = str_replace("Veinte Ocho", "Veintiocho", $Rtn);
		$Rtn = str_replace("Veinte Nueve", "Veintinueve", $Rtn);

		//--------------------- FiltroUn ------------------------------
		if (substr($Rtn, 0, 1) == "M") $Rtn = "Un " . $Rtn;
		//--------------------- Adicionar Y ------------------------------
		for ($i = 65; $i <= 88; $i++) {
			if ($i != 77)
				$Rtn = str_replace("a " . Chr($i), "* y " . Chr($i), $Rtn);
		}
		$Rtn = str_replace("*", "a", $Rtn);
		return ($Rtn);
	}


	function ReplaceStringFrom(&$x, $OldWrd, $NewWrd, $Ptr)
	{
		$x = substr($x, 0, $Ptr)  . $NewWrd . substr($x, strlen($OldWrd) + $Ptr);
	}


	function Parte($x)
	{
		$Rtn = '';
		$t = '';
		$i = '';
		do {
			switch ($x) {
				case 0:
					$t = "Cero";
					break;
				case 1:
					$t = "Un";
					break;
				case 2:
					$t = "Dos";
					break;
				case 3:
					$t = "Tres";
					break;
				case 4:
					$t = "Cuatro";
					break;
				case 5:
					$t = "Cinco";
					break;
				case 6:
					$t = "Seis";
					break;
				case 7:
					$t = "Siete";
					break;
				case 8:
					$t = "Ocho";
					break;
				case 9:
					$t = "Nueve";
					break;
				case 10:
					$t = "Diez";
					break;
				case 20:
					$t = "Veinte";
					break;
				case 30:
					$t = "Treinta";
					break;
				case 40:
					$t = "Cuarenta";
					break;
				case 50:
					$t = "Cincuenta";
					break;
				case 60:
					$t = "Sesenta";
					break;
				case 70:
					$t = "Setenta";
					break;
				case 80:
					$t = "Ochenta";
					break;
				case 90:
					$t = "Noventa";
					break;
				case 100:
					$t = "Cien";
					break;
				case 200:
					$t = "Doscientos";
					break;
				case 300:
					$t = "Trescientos";
					break;
				case 400:
					$t = "Cuatrocientos";
					break;
				case 500:
					$t = "Quinientos";
					break;
				case 600:
					$t = "Seiscientos";
					break;
				case 700:
					$t = "Setecientos";
					break;
				case 800:
					$t = "Ochocientos";
					break;
				case 900:
					$t = "Novecientos";
					break;
				case 1000:
					$t = "Mil";
					break;
				case 1000000:
					$t = "Millón";
					break;
			}

			if ($t == $this->Void) {
				$i = $i + 1;
				$x = $x / 1000;
				if ($x == 0) $i = 0;
			} else
				break;
		} while ($i != 0);

		$Rtn = $t;
		switch ($i) {
			case 0:
				$t = $this->Void;
				break;
			case 1:
				$t = " Mil";
				break;
			case 2:
				$t = " Millones";
				break;
			case 3:
				$t = " Billones";
				break;
		}
		return ($Rtn . $t);
	}
}