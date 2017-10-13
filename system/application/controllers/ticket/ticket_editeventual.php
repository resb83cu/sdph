<?php

/**
 * @orm ticket_editetecsa
 */
class Ticket_editeventual extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'ticket/ticket_editeventual_model', 'conn', true );
	}
	
	function index() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'ticket/ticket_editeventual' );
		if ($flag) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'ticket/ticket_editeventual_view' );
			$this->load->view ( 'sys/footer_view' );
		} else {
			$this->redirectError ();
		}
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
	public function setDataGrid() {
		$to = (! isset ( $_POST ['limit'] )) ? 15 : $_POST ['limit'];
		$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		$dateStart = $this->input->post ( 'dateStart' );
		$dateEnd = $this->input->post ( 'dateEnd' );
		//$province = $this->input->post('province');
		$motive = $this->input->post ( 'motive' );
		$state = $this->input->post ( 'state' );
		$this->load->model ( 'request/request_tickets_model', 'conn2', true );
		return $this->conn2->getDataEventual ( $to, $from, $dateStart, $dateEnd, $motive, $state );
	
	}
	
	public function eventualPdf($pdf = 'si') {
			$dateStart = $this->input->post ( 'startdt' );
			$dateEnd = $this->input->post ( 'enddt' );
			$state = $this->input->post ( 'edit_state_idHidden' );
			$motive = $this->input->post ( 'motive' );
			$this->load->model ( 'request/request_tickets_model', 'conn2', true );
			
			$data = $this->conn2->getDataEventualPDF ( $dateStart, $dateEnd, $motive, $state);
			$count = count ( $data ); //tama;o del arreglo
			

			//ini pdf
			$this->load->library ( 'FPDF/pdf_ticket_eventual' );
			$pdf = new Pdf_ticket_eventual ( 'P', 'mm', 'Letter', '' ); //le hubiera podido pasar la fecha pero lo hice en la linea de abajo  
			

			$mydateformat = ' Fecha de viaje:    ' . $data [0] ['ticket_date'];
			
			$pdf->myheader = $mydateformat . '     ' . $data [0] ['state']; //si levanta el pdf es porque al menos hay una fila, y las fechas son iguales
			

			$pdf->AddPage ();
			
			$pdf->Ln ( 1 );
			
			$pdf->SetFont ( 'Arial', '', 8 );
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

				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_worker'] );
				$pdf->Cell ( 60, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_identity'] );
				$pdf->Cell ( 25, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['province_namefrom'] );
				$pdf->Cell ( 30, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['province_nameto'] );
				$pdf->Cell ( 30, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['state'] );
				$pdf->Cell ( 20, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['hotel'] );
				$pdf->Cell ( 20, 7, $str, '', '', '', true );
				
				$pdf->Ln ();
			
			}
			
			$pdf->Output ( 'Reporte de Eventuales - '.$data [0] ['ticket_date'].'.pdf', 'D' );
	}
	
	/**
	 * Funcion para Insertar Provincia
	 *
	 */
	function insert() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'ticket/ticket_editeventual' );
		if ($flag) {
			$result = $this->conn->insert ();
			die ( "{success : $result}" );
		} else {
			$this->redirectError ();
		}
	}
	
	function insertMulti($request_id, $ticket_date, $state_id) {
		if ($this->session->userdata ( 'roll_id' ) >= 4) {
			$result = $this->conn->insertMulti ( $request_id, $ticket_date, $state_id );
			die ( "{success : $result}" );
		} else {
			$this->redirectError ();
		}
	}
	
	/**
	 * Elimina Transporte
	 * recibe como parametro el nombre de la provincia
	 */
	function delete($id) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'ticket/ticket_editeventual' );
		if ($flag) {
			$this->conn->delete ( $id );
		} else {
			$this->redirectError ();
		}
	}
	
	/**
	 * Devuelve una provincia dado el nombre de la misma
	 *
	 */
	function get() {
		$data = $this->conn->get ();
		die ( "{data : " . json_encode ( $data ) . "}" );
	
	}
	
	function getById($request_id, $ticket_date) {
		$data = $this->conn->getById ( $request_id, $ticket_date );
		die ( "{data : " . json_encode ( $data ) . "}" );
		//echo "<pre>"; print_r($data); echo "</pre>";
	}
	
	function etecsaPdf($request_id, $ticket_date) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'ticket/ticket_editeventual' );
		if ($flag) {
			$data = $this->conn->getById ( $request_id, $ticket_date );
			$count = count ( $data );
			if ($count == 0) {
				return "{success: false, errors: { reason: 'Error al generar la solicitud. Por favor intente de nuevo.' }}";
			}
			$this->load->library ( 'FPDF/pdf_request' );
			$pdf = new Pdf_request ( 'L', 'mm', 'Letter' );
			
			$pdf->AddPage ();
			$pdf->SetFont ( 'Arial', 'B', 12 );
			
			$pdf->Ln ( 5 );
			$pdf->SetFillColor ( 255, 255, 255 );

			$str = iconv ( 'UTF-8', 'windows-1252', 'SOLICITUD DE PASAJES Y HOSPEDAJES' );
			$pdf->Cell ( 80, 7, $str, '', '', '', true );
			$pdf->SetFont ( 'Arial', '', 12 );
			
			$pdf->Ln ();
			$pdf->Ln ( 20 );
			$pdf->SetFillColor ( 255, 255, 255 );
			
			$request_date = $data [0] ['request_date'];
			$ticket_date = $data [0] ['ticket_date'];
			$person_namerequestedby = $data [0] ['person_namerequestedby'];
			$person_namelicensedby = $data [0] ['person_namelicensedby'];
			$center_name = $data [0] ['center_name'];
			$transport_name = $data [0] ['transport_name'];
			$person_nameworker = $data [0] ['person_nameworker'];
			$person_identity = $data [0] ['person_identity'];
			$person_province = $data [0] ['person_province'];
			$province_namefrom = $data [0] ['province_namefrom'];
			$province_nameto = $data [0] ['province_nameto'];
			$motive_name = $data [0] ['motive_name'];
			$request_details = $data [0] ['request_details'];

			$str = iconv ( 'UTF-8', 'windows-1252', 'Persona que Autoriza: ' . $person_namelicensedby );
			$pdf->Cell ( 120, 7, $str, '', '', '', true );
			$pdf->Ln ( 12 );
			$str = iconv ( 'UTF-8', 'windows-1252', 'Persona que introdujo la solicitud: ' . $person_namerequestedby );
			$pdf->Cell ( 120, 7, $str, '', '', '', true );
			$pdf->Ln ( 12 );
			$str = iconv ( 'UTF-8', 'windows-1252', 'Fecha de solicitud: ' . $request_date );
			$pdf->Cell ( 120, 7, $str, '', '', '', true );
			$pdf->Ln ( 12 );
			$str = iconv ( 'UTF-8', 'windows-1252', 'Fecha de viaje: ' . $ticket_date );
			$pdf->Cell ( 120, 7, $str, '', '', '', true );
			$pdf->Ln ( 20 );
			
			$pdf->SetFont ( 'Arial', '', 10 );
			
			$pdf->Cell ( 60, 7, 'Nombre y Apellidos', 1, '', 'C', true );
			$pdf->Cell ( 35, 7, 'C.Identidad', 1, '', 'C', true );
			$pdf->Cell ( 48, 7, 'Provincia', 1, '', 'C', true );
			$pdf->Cell ( 40, 7, 'Origen', 1, '', 'C', true );
			$pdf->Cell ( 40, 7, 'Destino.', 1, '', 'C', true );
			$pdf->Cell ( 38, 7, 'Transportacion.', 1, '', 'C', true );
			//aqui se hace el for
			$pdf->Ln ( 7 );
			$str = iconv ( 'UTF-8', 'windows-1252', $person_nameworker );
			$pdf->Cell ( 60, 7, $str, 1, '', 'C', true );
			$str = iconv ( 'UTF-8', 'windows-1252', $person_identity );
			$pdf->Cell ( 35, 7, $str, 1, '', 'C', true );
			$str = iconv ( 'UTF-8', 'windows-1252', $person_province );
			$pdf->Cell ( 48, 7, $str, 1, '', 'C', true );
			$str = iconv ( 'UTF-8', 'windows-1252', $province_namefrom );
			$pdf->Cell ( 40, 7, $str, 1, '', 'C', true );
			$str = iconv ( 'UTF-8', 'windows-1252', $province_nameto );
			$pdf->Cell ( 40, 7, $str, 1, '', 'C', true );
			$str = iconv ( 'UTF-8', 'windows-1252', $transport_name );
			$pdf->Cell ( 38, 7, $str, 1, '', 'C', true );
			
			$pdf->Ln ( 15 );
			$pdf->Cell ( 37, 7, 'Observaciones:', 0, '', 'L', true );
			$pdf->Ln ( 10 );
			$str = iconv ( 'UTF-8', 'windows-1252', $request_details );
			$pdf->Cell ( 250, 7, $str, 0, '', 'c', true );
			$pdf->Ln ( 15 );
			$pdf->SetFont ( 'Arial', '', 12 );
			$pdf->Cell ( 60, 7, 'Con cargo al presupuesto: ', 0, '', 'L', true );
			$str = iconv ( 'UTF-8', 'windows-1252', $center_name );
			$pdf->Cell ( 212, 7, $str, 0, '', 'c', true );		
			
			$pdf->Output ( 'Solicitud'.$person_nameworker.'-'.$ticket_date.'.pdf', 'D' );
		} else {
			$this->redirectError ();
		}
	}	

}

?>
