<?php
include_once ('fpdf1.php');
class Pdf_conciliation_lodging_report extends FPDF1 {
	
	function Header() {

		//Select Arial bold 15
		$this->SetFont ( 'Arial', 'B', 13 );
		//Framed title
		$this->Cell ( 30, 10, 'Conciliacion - Hospedaje', '', 0, 'I' );
		
		$this->Image ( 'system/application/libraries/FPDF/logo.png', 220, 4, 45 );
		$this->Ln ();
		$this->Cell ( 15, 10, 'Fac.', '', 0, 'I' );
		$this->Cell ( 20, 10, 'Carnet', '', 0, 'I' );
		$this->Cell ( 60, 10, 'Nombre y Apellidos', '', 0, 'I' );
		$this->Cell ( 40, 10, 'Hotel', '', 0, 'I' );
		$this->Cell ( 23, 10, 'Entrada', '', 0, 'I' );
		$this->Cell ( 23, 10, 'Salida', '', 0, 'I' );
		$this->Cell ( 20, 10, 'Dieta', '', 0, 'I' );
		$this->Cell ( 30, 10, 'Hospedaje', '', 0, 'I' );
                $this->Cell ( 42, 10, 'Total', '', 0, 'I' );
		$this->Ln ();
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
