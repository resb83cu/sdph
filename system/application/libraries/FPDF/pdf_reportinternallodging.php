<?php
include_once ('fpdf1.php');
class Pdf_reportinternallodging extends FPDF1 {
	
	function Header() {
		//Select Arial bold 15
		$this->SetFont ( 'Arial', 'B', 15 );
		//Framed title
		$this->Cell ( 30, 10, 'Reporte de Hospedajes' );
		
		$this->Image ( 'system/application/libraries/FPDF/logo.png', 220, 4, 45 );
		
		$this->Ln ();
		//Line break
		

		$this->SetFont ( 'Arial', 'BU', 8 );
		
		$this->SetFont ( 'Arial', 'BU', 8 );
		
		$this->Cell ( 7, 10, '', '', 0, 'I' );
		$this->Cell ( 60, 10, 'Trabajador', '', 0, 'I' );
		$this->Cell ( 25, 10, 'CI', '', 0, 'I' );
		$this->Cell ( 20, 10, 'Entrada', '', 0, 'I' );
		$this->Cell ( 20, 10, 'Salida', '', 0, 'I' );
		
		$this->Cell ( 30, 10, 'Provincia hospedaje', '', 0, 'I' );
		$this->Cell ( 45, 10, 'Hotel', '', 0, 'I' );
		$this->Cell ( 35, 10, 'Centro de costo', '', 0, 'I' );
		
		$this->Cell ( 25, 10, 'Motivo', '', 0, 'I' );
		
		$this->Ln ( 7 );
	}
	
	function Footer() {
		//Go to 1.5 cm from bottom
		$this->SetY ( - 15 );
		//Select Arial italic 8
		$this->SetFont ( 'Arial', 'I', 8 );
		//Print centered page number
		$this->Cell ( 0, 10, 'Pagina ' . $this->PageNo (), 0, 0, 'C' );
	}
}
?>
