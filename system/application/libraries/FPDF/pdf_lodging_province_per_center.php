<?php 
include_once('fpdf1.php');
class Pdf_lodging_province_per_center  extends FPDF1{


function Header()
{
    //Select Arial bold 15
    $this->SetFont('Arial','B',13);
    //Framed title
    $this->Cell(30,10,'Hospedaje - Reporte de Hospedados por Provincia de cada Centro Contable');
	
	$this->Image('system/application/libraries/FPDF/logo.png',220,4,45);
	
	$this->SetFont('Arial','B',10);
	
	$this->Ln();
		$this->Cell(50,10,'Centro Contable','',0,'I');
	   	$this->Cell(12,10,'PRI','',0,'I');
	   	$this->Cell(12,10,'ART','',0,'I');
	   	$this->Cell(12,10,'MYB','',0,'I');
	   	$this->Cell(12,10,'HAB','',0,'I');
	   	$this->Cell(12,10,'MTZ','',0,'I');
	   	$this->Cell(12,10,'VCL','',0,'I');
	   	$this->Cell(12,10,'CFG','',0,'I');
	   	$this->Cell(12,10,'SSP','',0,'I');
	   	$this->Cell(12,10,'CAV','',0,'I');
	   	$this->Cell(12,10,'CMG','',0,'I');
	   	$this->Cell(12,10,'LTU','',0,'I');
	   	$this->Cell(12,10,'HOL','',0,'I');
	   	$this->Cell(12,10,'GRM','',0,'I');
	   	$this->Cell(12,10,'SCU','',0,'I');
	   	$this->Cell(12,10,'GTM','',0,'I');
	   	$this->Cell(11,10,'ISJ','',0,'I');
	   	$this->Cell(17,10,'TOTAL','',0,'I');
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
