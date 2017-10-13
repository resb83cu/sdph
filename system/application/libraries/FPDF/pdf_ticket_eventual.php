<?php 
include_once('fpdf1.php');
class Pdf_ticket_eventual extends FPDF1{



function Header()
{
    //Select Arial bold 15
    $this->SetFont('Arial','B',15);
    //Framed title
    $this->Cell(30,10,'Listado de Pasajes - Arrendado ',0,0,'L');
	
	

	$this->Ln();
	$this->SetFont('Arial','',10);
	$this->Cell(30,10,$this->myheader,0,0,'L');
	
	$this->Image('system/application/libraries/FPDF/logo.png',154,4,45);

	$this->Ln();
    //Line break
   
	      $this->SetFont('Arial','BU',8);
		  $this->Cell(7,10,'No','',0,'I');
		  $this->Cell(60,10,'Trabajador','',0,'I');
		  $this->Cell(25,10,'No. CI','',0,'I');                  
		  
		 
		  $this->Cell(30,10,'Prov. Origen','',0,'I');
		  $this->Cell(30,10,'Prov. Destino','',0,'I');
		  
		  $this->Cell(20,10,'Omnibus','',0,'I');
		  $this->Cell(20,10,'Hotel','',0,'I');
		  
		  
		  $this->Ln(7);
}



function Footer()
{
    //Go to 1.5 cm from bottom
    $this->SetY(-15);
    //Select Arial italic 8
    $this->SetFont('Arial','I',8);
    //Print centered page number
    $this->Cell(0,10,'Pagina '.$this->PageNo(),0,0,'C');
}
}
?>
