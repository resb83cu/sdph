<?php 
include_once('fpdf1.php');
class Pdf_reportinternalticket extends FPDF1{



function Header()
{
    //Select Arial bold 15
    $this->SetFont('Arial','B',15);
    //Framed title
    $this->Cell(30,10,'Reporte de Pasajes',0,0,'L');
	

	$this->Image('system/application/libraries/FPDF/logo.png',220,4,45);


	$this->Ln();
    //Line break
   
	      $this->SetFont('Arial','BU',8);
		
		  $this->Cell(60,10,'Trabajador','',0,'I');
		  $this->Cell(25,10,'NoIdentidad','',0,'I');
		   $this->Cell(20,10,'Fecha pasaje','',0,'I');
		  $this->Cell(30,10,'Provincia origen','',0,'I');
		  $this->Cell(30,10,'Provincia destino','',0,'I');
		   $this->Cell(20,10,'Transporte','',0,'I');
		  $this->Cell(50,10,'Centro de costo','',0,'I');
		  
		  
		  $this->Cell(25,10,'Motivo','',0,'I');

		  $this->Ln(7);
}



function Footer()
{
    //Go to 1.5 cm from bottom
    $this->SetY(-15);
    //Select Arial italic 8
    $this->SetFont('Arial','I',8);
    //Print centered page number
    $this->Cell(0,10,'P�gina '.$this->PageNo(),0,0,'C');
}
}
?>
