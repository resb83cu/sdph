<?php 
include_once('fpdf1.php');
class Pdf_contab_ticket_eventual  extends FPDF1{


function Header()
{
    //Select Arial bold 15
    $this->SetFont('Arial','B',15);
    //Framed title
    $this->Cell(30,10,'Contabilidad - Reporte de Eventuales');
	
	$this->Image('system/application/libraries/FPDF/logo.png',220,4,45);
	$this->SetFont('Arial','B',12);
	$this->Ln();
		$this->Cell(25,10,'Servicio','',0,'I');
		$this->Cell(35,10,'Capacidad','',0,'I');
		$this->Cell(25,10,'Fecha','',0,'I');
		$this->Cell(25,10,'Importe','',0,'I');
		  
		$this->Cell(50,10,'Suministrador','',0,'I');
		$this->Cell(50,10,'Origen','',0,'I');
		$this->Cell(50,10,'Destino','',0,'I');
		$this->Ln();
   }


function Footer(){
    //Go to 1.5 cm from bottom
    $this->SetY(-15);
    //Select Arial italic 8
    $this->SetFont('Arial','I',8);
    //Print centered page number
    $this->Cell(0,10,'Pagina '.$this->PageNo(),0,0,'C');
}
}
?>
