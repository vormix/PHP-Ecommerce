<?php

use Fpdf\Fpdf;

class PdfUtilities extends Fpdf {

  public function printOrderInvoice($orderId, $orderItems, $orderTotal, $first_name, $email, $address ) {

    $data = $orderItems;

    // Column headings
    $header = array('ID', 'Prodotto', iconv('UTF-8', 'windows-1252', 'Quantità' ), 'Prezzo Unitario', 'Prezzo');
    $w = array(0, 70, 30, 50, 30);
    // Data loading

    $this->SetFont('Arial','B',20);

    $this->AddPage();
    $this->Cell(160,20, SITE_NAME . ' - Fattura ordine #' . $orderId, '', 0, 'C');
    $this->Ln();

    $this->setFont('Arial', 'B', 12);
    for($i=1; $i<count($header); $i++)  {
      $this->Cell($w[$i], 10, $header[$i], 1, 0, 'C');
    }
    $this->Ln();
    // Data
    $this->setFont('Arial', '', 12);
    foreach($data as $row)
    {
      $eur = iconv('UTF-8', 'windows-1252', '€ ' );
      $row['product_name'] = iconv('UTF-8', 'windows-1252', $row['product_name']);
      $row['quantity'] = iconv('UTF-8', 'windows-1252', $row['quantity']);

      $this->Cell($w[1],10 ,utf8_encode($row['product_name']),'LR', 0, 'C');
      $this->Cell($w[2],10 , number_format($row['quantity']),'LR',0,'C');
      $this->Cell($w[3],10 , $eur . number_format($row['single_price'], 2, ',', '.'),'LR',0,'C');
      $this->Cell($w[4],10 , $eur . number_format($row['total_price'], 2, ',', '.'),'LR',0,'C');

      $this->Ln();
    }
    // Closing line
    $this->Cell(array_sum($w),0,'','T');
    $this->Ln();

    $this->Cell($w[1],3, '' ,'');
    $this->Cell($w[1],3, '' ,'');
    $this->Cell($w[1],3, '' ,'');
    $this->Cell($w[1],3, '' ,'R');
    $this->Ln();
    $this->Cell(array_sum($w),0,'','T');
    $this->Ln();

    $this->setFont('Arial', 'B', 12);
    $this->Cell($w[1],10 , 'Spedizione: ' ,'LR', 0, 'R');
    $this->setFont('Arial', '', 12);
    $this->Cell($w[2],10 ,iconv('UTF-8', 'windows-1252', $orderTotal['shipment_name']),'',0,'L');
    $this->Cell($w[3],10 , '' ,'',0,'C');
    $this->Cell($w[4],10 , $eur . number_format($orderTotal['shipment_price'] , 2, ',', '.'),'LR',0,'C');
    $this->Ln();
    $this->Cell(array_sum($w),0,'','T');
    $this->Ln();

    //$this->Cell($w[1],10 ,'','L');
    $this->setFont('Arial', 'B', 12);
    $this->Cell($w[1],10 ,'Totale: ','LR', 0, 'R');
    $this->Cell($w[2],10 , '','',0,'C');
    $this->Cell($w[3],10 , '','',0,'C');
    $this->Cell($w[4],10 , $eur . number_format(($orderTotal['total'] + $orderTotal['shipment_price']), 2, ',', '.'),'LR',0,'C');
    $this->Ln();
    $this->Cell(array_sum($w),0,'','T');
    $this->Ln();

    $this->SetFont('Arial','B',18);

    // Cliente
    $this->Ln();
    $this->Cell(160,20,'Dettagli Cliente:', '', 0, 'C');
    $this->Ln();


    $this->SetFont('Arial','B',12);
    $this->Cell(70, 10 , 'Nominativo:',1,0,'R');
    $this->SetFont('Arial','',12);
    $this->Cell(110, 10 , iconv('UTF-8', 'windows-1252', $first_name) ,1,0,'C');
    $this->Ln();

    $this->SetFont('Arial','B',12);
    $this->Cell(70, 10 , 'Email:',1,0,'R');
    $this->SetFont('Arial','',12);
    $this->Cell(110, 10 , iconv('UTF-8', 'windows-1252', $email) ,1,0,'C');
    $this->Ln();

    $this->SetFont('Arial','B',12);
    $this->Cell(70, 10 , 'Indirizzo:',1,0,'R');
    $this->SetFont('Arial','',12);
    $indirizzo = esc_html($address['street']) . ' - ' . esc_html($address['city']) . ' ('. esc_html($address['cap']) . ')';
    $this->Cell(110, 10 , iconv('UTF-8', 'windows-1252', $indirizzo) ,1,0,'C');
    $this->Ln();

    $this->Cell(180 ,0,'','T');
    $this->Ln();

    $this->Output();
  }

}

