<?php 
include_once('fpdf1.php');
class Pdf_request extends FPDF1{



function Header()
{

	 $this->SetFont('Arial','B',12);
	 
	 $this->Ln (5);
	 $this->SetFillColor(255,255,255);
	 $str = iconv ( 'UTF-8', 'windows-1252', $this->myheader);
			$this->Cell (30, 7, $str, '', '', '', true );

	
	$this->Image('system/application/libraries/FPDF/logo.png',200,12,45);
	$this->Ln();
    //Line break
 
	    
}


function Footer()
{
    //Go to 1.5 cm from bottom
    $this->SetY(-15);
    //Select Arial italic 8
	$this->Cell(0,10,'  '.$this->PageNo(),0,0,'R');
}





}
?>
