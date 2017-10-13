<?php

class Request_tickets extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'request/request_tickets_model', "conn", true );
	
	}
	
	function index() {
	
	}
	
	function redirectError() {
		$this->load->view ( 'sys/header_view' );
		$this->load->view ( 'error_message' );
		$this->load->view ( 'sys/footer_view' );
	}
	
	/**
	 * Busca los datos para llenar el grid y los devuelve en formato JSON
	 *
	 */
	public function setDatosGrid() {
		$hasta = (! isset ( $_POST ['limit'] )) ? 15 : $_POST ['limit'];
		$desde = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		
		$data = $this->conn->getDatos ( $hasta, $desde );
		$cant = $this->conn->getCant ();
		die ( "{count : " . $cant . ", data : " . json_encode ( $data ) . "}" );
	}
	
	public function collects() {
		if ($this->session->userdata ( 'roll_id' ) == 5 || $this->session->userdata ( 'roll_id' ) == 6 || $this->session->userdata ( 'person_id' ) == 24 || $this->session->userdata ( 'person_id' ) == 27 || $this->session->userdata ( 'person_id' ) == 19 || $this->session->userdata ( 'person_id' ) == 16533) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'request/request_collects_view' );
			$this->load->view ( 'sys/footer_view' );
		} else {
			$this->redirectError ();
		}
	
	}
	
	public function getDataCollect() {
		$dateStart = $this->input->post ( 'dateStart' );
		$dateEnd = $this->input->post ( 'dateEnd' );
		die ( $this->conn->getDataCollect ( $dateStart, $dateEnd ) );
	}
	
	public function collectsPdf($show = false, $pdf = 'no') {
		$date = $this->input->post ( 'begindate' );
		if ($show == false && $pdf == 'no') {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'report/reportInternalTicketCollect_view' );
			$this->load->view ( 'sys/footer_view' );
		} elseif ($show == true && $pdf == 'no') {
			die ( $this->conn->getDataCollectPdf ( $date, 'no' ) );
		} else {
			$data = $this->conn->getDataCollectPdf ( $date, 'si' );
			$count = count ( $data ); //tama;o del arreglo
			

			//ini pdf
			$this->load->library ( 'FPDF/pdf_ticket_collect' );
			$pdf = new Pdf_ticket_collect ( 'L', 'mm', 'Letter', '' ); //le hubiera podido pasar la fecha pero lo hice en la linea de abajo  
			

			$mydateformat = ' Fecha de viaje:    ' . $data [0] ['ticketdate'];
			
			$pdf->myheader = $mydateformat; //. '     ' . $data [0] ['state']; //si levanta el pdf es porque al menos hay una fila, y las fechas son iguales
			

			$pdf->AddPage ();
			
			$pdf->Ln ( 1 );
			
			$pdf->SetFont ( 'Arial', '', 9 );
			$flag = true;
			
			for($i = 0; $i < $count; $i ++) {
				//$pdf->Cell(40,10,$data[$i]['request_id']);
				

				if ($flag == false) {
					$flag = true;
					$pdf->SetFillColor ( 255, 255, 255 );
				} else {
					$flag = false;
					$pdf->SetFillColor ( 200, 215, 235 );
				}
				
				$str = iconv ( 'UTF-8', 'windows-1252', $i + 1 );
				$pdf->Cell ( 7, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person'] );
				$pdf->Cell ( 50, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['province_from'] );
				$pdf->Cell ( 35, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['province_to'] );
				$pdf->Cell ( 35, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['exithour'] );
				$pdf->Cell ( 20, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['arrivalhour'] );
				$pdf->Cell ( 20, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['transport'] );
				$pdf->Cell ( 40, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['hotel'] );
				$pdf->Cell ( 30, 7, $str, '', '', '', true );
				
				$pdf->Ln ();
			
			}
			$pdf->Output ( 'Reporte de Recogida CFN - ' . $data [0] ['ticketdate'] . '.pdf', 'D' );
		}
	}
	
	/**
	 * Funcion para Insertar Solicitud de Pasaje
	 *
	 */
	function insertRequestTicke() {
		$data ['tickes_date'] = $this->input->post ( 'tickes_date' );
		$data ['place_idfrom'] = $this->input->post ( 'place_idfrom' );
		$data ['place_idto'] = $this->input->post ( 'place_idto' );
		
		$result = $this->conn->insertRequestTicke ( $data );
		die ( "{success : $result}" );
	}
	
	function insertCollect($request_id, $ticketdate) {
		$result = $this->conn->insertCollect ( $request_id, $ticketdate );
		die ( "{success : $result}" );
	}
	/**
	 * Elimina Solicitud de Pasaje
	 * recibe como parametro el nombre de la provincia
	 */
	function cancelCollect($request_id, $ticketdate) {
		$result = $this->conn->cancelCollect ( $request_id, $ticketdate );
		die ( "{success : $result}" );
	}

}

?>
