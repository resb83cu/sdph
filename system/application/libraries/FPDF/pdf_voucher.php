<?php 
include_once('fpdf1.php');
class Pdf_voucher extends FPDF1{



function Header()
{

	 $this->SetFont('Arial','B',12);
	 
	 $this->Ln (5);
	 $this->SetFillColor(255,255,255);
	 $str = iconv ( 'UTF-8', 'windows-1252', $this->myheader);
			$this->Cell (30, 7, $str, '', '', '', true );

	
	$this->Image('system/application/libraries/FPDF/logo.png',150,12,45);
	$this->Ln();
    //Line break
 
	    
}


function Footer()
{
    //Go to 1.5 cm from bottom
    $this->SetY(-15);
    //Select Arial italic 8
   
   
   
   
   
    $this->SetFont('Arial','I',7);
    //Print centered page number
	$str = iconv ( 'UTF-8', 'windows-1252',  ' En caso de dificultades con el voucher, por favor, nos complaceria atenderles por el telefono 642-5690 ');
	$this->Cell(0,10,'  '.$str,'','','',true);
	
	$this->Cell(0,10,'  '.$this->PageNo(),0,0,'R');
}





}
?>
