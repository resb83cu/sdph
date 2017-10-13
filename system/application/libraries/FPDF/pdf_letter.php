<?php 
include_once('fpdf1.php');
class Pdf_letter extends FPDF1{



function Header()
{

	$this->SetFont('Arial','',12);
	$this->Ln (20 );
	$this->Cell(30,10,$this->myheader,0,0,'L');
	$this->Image('system/application/libraries/FPDF/logo.png',150,12,45);

	$this->Ln();
    //Line break
   
	    
}



function Footer()
{
    //Go to 1.5 cm from bottom
    $this->SetY(-15);
    //Select Arial italic 8
    $this->SetFont('Arial','I',8);
    //Print centered page number
    $this->Cell(0,10,'Página '.$this->PageNo(),0,0,'C');
}
}
?>
