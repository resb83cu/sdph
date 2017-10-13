<?php

/**
 * @orm ticket_editetecsa
 */
class Ticket_accounting extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'ticket/ticket_conciliations_model', 'conn', true );
	}
	
	function index() {
		$this->load->view ( 'sys/header_view' );
		$this->load->view ( 'ticket/ticket_accounting_view' );
		$this->load->view ( 'sys/footer_view' );
	
	}
	
	/**
	 * Busca los datos para llenar el grid y los devuelve en formato JSON
	 *
	 */
	public function setDataGrid($show = false, $isPDF = 'no') {
		
		if ($show == false & $isPDF == 'no') {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'ticket/ticket_conciliations_view' );
			$this->load->view ( 'sys/footer_view' );
		
		}
		
		if ($show == true && $isPDF == 'no') {
			
			$to = (! isset ( $_POST ['limit'] )) ? 15 : $_POST ['limit'];
			$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
			
			/*$datosForWheres = array ();
			$datosForWheres ['startdt'] = $this->input->post ( 'dateStart' ); //no es el nomnre ed los componentes sino el valor ed las variables javascript del baseparam del js
			$datosForWheres ['enddt'] = $this->input->post ( 'dateEnd' );
			$datosForWheres ['center_id'] = $this->input->post ( 'center' );
			$datosForWheres ['transport_id'] = $this->input->post ( 'transport' );
			$datosForWheres ['motive_id'] = $this->input->post ( 'motive' );*/
			
			$startdt = $this->input->post ( 'dateStart' ); //no es el nomnre ed los componentes sino el valor ed las variables javascript del baseparam del js
			$enddt = $this->input->post ( 'dateEnd' );
			$center_id = $this->input->post ( 'center' );
			$transport_id = $this->input->post ( 'transport' );
			$motive_id = $this->input->post ( 'motive' );
			
			die ( $this->conn->getDataAccounting ( $startdt, $enddt, $center_id, $transport_id, $motive_id, $show, $isPDF ) );
		}
		
		//
		if ($show == true && $isPDF == 'si') {
			
			$to = (! isset ( $_POST ['limit'] )) ? 1000000 : $_POST ['limit']; //todos
			$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
			
			$datosForWheres = array ();
			
			$datosForWheres ['startdt'] = $this->input->post ( 'startdt' ); //aqui obligado  se pasael nombre ed los componentes, verificar que eeste en standar submit true la forma de los componentes
			$datosForWheres ['enddt'] = $this->input->post ( 'enddt' );
			$datosForWheres ['center_id'] = $this->input->post ( 'center_id' );
			$datosForWheres ['transport_id'] = $this->input->post ( 'transport_id' );
			$datosForWheres ['motive_id'] = $this->input->post ( 'motive_id' );

			$data = $this->conn->getDataAccounting ( $datosForWheres ['startdt'], $datosForWheres ['enddt'], $datosForWheres ['center_id'], $datosForWheres ['transport_id'], $datosForWheres ['motive_id'], $show, $isPDF );
			//$data = $this->conn->getDataAccounting ( $startdt, $enddt, $center_id, $transport_id, $motive_id, $show, $isPDF );
			//son todos pero se filtra a veces
			// $cant = $this->conn->getCant();//no porque cogeria toda la tabla y en el for para formar el pdf explotaria
			$cant = count ( $data ); //y como no se pagina pues el count es del total del arreglo recibido
			

			$this->load->library ( 'FPDF/pdf_contab_ticket' );
			$pdf = new Pdf_contab_ticket ( 'L', 'mm', 'Letter' );
			$pdf->AddPage ();
			
			$pdf->Ln ( 1 );
			
			$pdf->SetFont ( 'Arial', '', 8 );
			$flag = true;
			
			for($i = 0; $i < $cant; $i ++) {
				if ($flag == false) {
					$flag = true;
					$pdf->SetFillColor ( 255, 255, 255 );
				} else {
					$flag = false;
					$pdf->SetFillColor ( 200, 215, 235 );
				}
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['viazul_voucher'] );
				$pdf->Cell ( 40, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_nameworker'] );
				$pdf->Cell ( 60, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['viazul_price'] );
				$pdf->Cell ( 25, 7, $str.',00', '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_namelicensedby'] );
				$pdf->Cell ( 60, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['center_name'] );
				$pdf->Cell ( 35, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['ticket_date'] );
				$pdf->Cell ( 25, 7, $str, '', '', '', true );
				
				$pdf->Ln ();
			}
			if ($datosForWheres ['transport_id'] == 2) {
				$transp = 'Viazul';
			} elseif ($datosForWheres ['transport_id'] == 3) {
				$transp = 'Avion';
			} else {
				$transp = 'Barco';
			}
			$pdf->Output ( 'Reporte Contabilidad Pasajes-'.$transp.'-'.$datosForWheres ['startdt'].'-'.$datosForWheres ['enddt'].'.pdf', 'D' );
		
		}
		//	return  $this->conn->getDataAccounting($dateStart, $dateEnd, $center, $transport, $motive,$show,$isPDF); 
	}

}

?>
