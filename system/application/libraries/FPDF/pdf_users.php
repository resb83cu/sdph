<?php 
include_once('fpdf1.php');
class Pdf_users  extends FPDF1{


function Header()
{
    //Select Arial bold 15
    $this->SetFont('Arial','B',15);
    //Framed title
    $this->Cell(30,10,'Usuarios del Sistema');
	
	$this->Image('system/application/libraries/FPDF/logo.png',220,4,45);
	$this->SetFont('Arial','B',12);
	$this->Ln();
		$this->Cell(25,10,'Usuario','',0,'I');
		$this->Cell(60,10,'Nombre y Apellidos','',0,'I');
		$this->Cell(30,10,'Rol','',0,'I');	  
		$this->Cell(60,10,'Centro de Costo','',0,'I');
		$this->Cell(35,10,'Creado','',0,'I');
		$this->Cell(45,10,'Ultima modificacion','',0,'I');		
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
