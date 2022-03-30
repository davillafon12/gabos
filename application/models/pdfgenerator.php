<?php

Class pdfgenerator extends CI_Model{

    public function generatePDF($orientation = 'P', $unit = 'mm', $size = 'A4'){
        require_once(PATH_FPDF_LIBRARY);
        $pdf = new FPDF($orientation, $unit, $size);

        return $pdf;
    }

    public function getAmountPagesToDraw($amountItems, $articulosPorPagina = 30){
        $pivot = $amountItems / $articulosPorPagina;
        //var_dump($amountItems."|".$articulosPorPagina."|".$pivot);
        return $pivot <= 0 ? 1 : (($pivot - intval($pivot)) == 0 ? $pivot : intval($pivot) + 1);
    }

    public function drawHeader(&$pdf, $documentType, $data){
        switch($documentType){
            case CONTROL_DE_INVENTARIO:
                $pdf->SetFont('Arial','B',14);
                $pdf->Cell(40,10, $data->empresa->Sucursal_Nombre);
                $pdf->Line(10, 17, 100, 17);
                $pdf->ln(5);
                $pdf->SetFont('Arial','',10);
                $pdf->Cell(40,10,'Cédula Jurídica: '.$data->empresa->Sucursal_Cedula);
                $pdf->ln(4);
                $pdf->Cell(40,10,'Teléfono: '.$data->empresa->Sucursal_Telefono);
                $pdf->ln(4);
                $pdf->Cell(40,10,'Email: '.$data->empresa->Sucursal_Email);
				//Cuadro de numero de factura y hora/fecha
				$pdf->Rect(120, 10, 80, 20, 'D');
				$pdf->SetFont('Arial','B',16);
				$pdf->Text(122, 17, 'Control de Inventario #'.$data->control->id);
				$pdf->SetFont('Arial','',10);
				$pdf->Text(122, 22, 'Fecha y Hora: ');
				$pdf->Text(122, 27, date("d-m-Y h:i:s a", strtotime($data->control->Fecha_Creacion)));

				$pdf->Text(169, 27, "Pag. # {$data->paginaActual} de {$data->cantidadPaginas}");

                $pdf->Line(10, 35, 200, 35);
                $pdf->Text(10, 40, 'Creado Por: '.$data->usuarioQueRealiza->Usuario_Nombre." ".$data->usuarioQueRealiza->Usuario_Apellidos);
                $pdf->Text(10, 45, 'Empate Autorizado Por: '.$data->usuarioQueEmpata->Usuario_Nombre." ".$data->usuarioQueEmpata->Usuario_Apellidos);
            break;
            default:
                die("Document Type: ".$documentType." not supported");
            break;
        }
        return $this;
    }

    public function drawProductsContainer(&$pdf, $titles = [], $top = 65, $bottom = 173, $bottomLines = 240){
        //Agregamos el apartado de productos
		$pdf->SetFont('Arial','B',12);
		$pdf->Text(90, $top, 'Productos');
		//Caja redondeada 1
		$pdf->RoundedRect(10, $top+2, 190, $bottom, 5, '12', 'D');
        $pdf->Line(10, $top + 9, 200, $top + 9);

        $pdf->SetFont('Arial','',10);
        $sumWidth = 10;
        $counter = 1;
        foreach($titles as $title){

            $pdf->SetXY($sumWidth, $top + 3);

            if($counter != sizeof($titles)){
                $pdf->Line($sumWidth + $title["width"], $top+2, $sumWidth + $title["width"], $bottomLines);
            }

            $pdf->cell($title["width"],5, $title["title"],0,0,'C');

            $sumWidth += $title["width"];
            $counter++;
        }

        return $this;
    }

    public function drawFooter(&$pdf, $documentType, $values = [], $y = 240, $data = null){

        $pdf->SetFont('Arial','',12);

        $offset = 0;
        foreach($values as $value){
            $pdf->SetXY(130, $y + $offset);
            $pdf->cell(35,5, $value["title"], 1, 0,'R');
            $pdf->cell(35,5, $value["content"], 1, 0,'R');
            $offset += 5;
        }

        switch($documentType){
            case CONTROL_DE_INVENTARIO:
                $values = array(
                    array("head"=>"E:","content"=>"Producto Empatado"),
                    array("head"=>"F:","content"=>"Físico"),
                    array("head"=>"S:","content"=>"Sistema"),
                    array("head"=>"B:","content"=>"Balance")
                );

                $offSet = 2;
                foreach($values as $v){
                    $pdf->SetXY(10, $y + $offSet);
                    $pdf->SetFont('Arial','B',6);
                    $pdf->cell(5, 3, $v['head'], 0, 0,'R');
                    $pdf->SetFont('Arial','',6);
                    $pdf->cell(5, 3, $v['content'], 0, 0,'L');
                    $offSet += 3;
                }
            break;
            default: die("Document Type not supported"); break;
        }
    }

    public function addItemToBody(&$pdf, $item = [], $y = 65){
        $pdf->SetFont('Arial','',8);
        $pdf->SetXY(10, $y);
        foreach($item as $value){
            $pdf->cell($value["width"], 4, $value["content"], 0, 0, $value["align"]);
        }
    }
}