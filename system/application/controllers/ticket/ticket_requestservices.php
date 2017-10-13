<?php

/**
 * @orm ticket_requestservices
 */
class Ticket_requestservices extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'ticket/ticket_requestservices_model', 'conn', true );
	}
	
	function index() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'ticket/ticket_requestservices' );
		if ($flag) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'ticket/ticket_requestservices_view' );
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
	
	function accountingEventuals() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'ticket/ticket_requestservices' );
		if ($flag) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'ticket/ticket_accountingeventuals_view' );
			$this->load->view ( 'sys/footer_view' );
		} else {
			$this->redirectError ();
		}
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
		$supplier = $this->input->post ( 'supplier' );
		$data = $this->conn->getData ( $to, $from, $dateStart, $dateEnd, $supplier );
		$cant = $this->conn->getCant ( $dateStart, $dateEnd, $supplier );
		die ( "{count : " . $cant . ", data : " . json_encode ( $data ) . "}" );
	}
	
	public function getDataAccounting() {
		$dateStart = $this->input->post ( 'dateStart' );
		$dateEnd = $this->input->post ( 'dateEnd' );
		$supplier = $this->input->post ( 'supplier' );
		$data = $this->conn->getDataAccounting ( $dateStart, $dateEnd, $supplier );
		$cant = $this->conn->getCant ( $dateStart, $dateEnd, $supplier );
		die ( "{count : " . $cant . ", data : " . json_encode ( $data ) . "}" );
	}
	
	public function accountingPdf() {
		$to = 1000000;
		$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		$dateStart = $this->input->post ( 'startdt' );
		$dateEnd = $this->input->post ( 'enddt' );
		$supplier = $this->input->post ( 'supplier_id' );
		$data = $this->conn->getDataAccounting ( $dateStart, $dateEnd, $supplier );
		$cant = count ( $data );
		
		$this->load->library ( 'FPDF/pdf_ticketrequestservice' );
		$pdf = new Pdf_ticketrequestservice ( 'L', 'mm', 'Letter' );
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

			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['service_id'] );
			$pdf->Cell ( 25, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['service_capacity'] );
			$pdf->Cell ( 35, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['service_date'] );
			$pdf->Cell ( 25, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['supplier_name'] );
			$pdf->Cell ( 50, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['province_nameexit'] );
			$pdf->Cell ( 50, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['province_namearrival'] );
			$pdf->Cell ( 50, 7, $str, '', '', '', true );
						
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['service_amount'] );
			$pdf->Cell ( 25, 7, $str, '', '', 'R', true );
			
			$pdf->Ln ();
		}
		$pdf->Output ( 'Reporte de Eventuales-'.$supplier.'-'.$dateStart.'-'.$dateEnd.'.pdf', 'D' );
	}
	
	/**
	 * Funcion para Insertar Provincia
	 *
	 */
	function insert() {
		$centinela = new Centinela ( );
		$this->load->model ( 'person/person_workers_model' );
		$flag = $centinela->accessTo ( 'ticket/ticket_requestservices' );
		$person_id = $centinela->get_person_id ();
		$position = is_null(Person_workers_model::getPositionById ( $person_id )) ? 'Gestor de Servicio' : Person_workers_model::getPositionById ( $person_id );
		$phone = Person_workers_model::getPhoneById ( $person_id );
		if ($flag) {
			$data = $this->conn->insert ();
			$this->load->library ( 'FPDF/pdf_ticketrequestservice' );
			$pdf = new Pdf_ticketrequestservice ( 'P', 'mm', 'Letter' );
			
			$this->load->model ( 'conf/conf_provinces_model' );
			$this->load->model ( 'conf/conf_costcenters_model' );
			$this->load->model ( 'conf/conf_transportsuppliers_model' );
			$service_id = $data ['service_id']; //lo cojo del primero, este arreglo debe venir con count >0 siempre, sino la carta no tiene sentido
			$service_capacity = $data ['service_capacity'];
			$service_date = $data ['service_date'];
			$service_hour = $data ['service_hour'];
			$service_itinerary = $data ['service_itinerary'];
			$place_exit = $data ['place_exit'];
			$place_lunch = empty($data ['place_lunch']) ? '' : '( '.$data ['place_lunch'].' )';
			$place_arrival = $data ['place_arrival'];
			$supplier = Conf_transportsuppliers_model::getNameById ( $data ['supplier_id'] );
			
			$province_exit = Conf_provinces_model::getNameById ( $data ['province_idexit'] );
			if (empty ( $data ['province_idlunch'] )) {
				$province_lunch = '---';
			} else {
				$province_lunch = Conf_provinces_model::getNameById ( $data ['province_idlunch'] );
			}
			
			$province_arrival = Conf_provinces_model::getNameById ( $data ['province_idarrival'] );
			$service_costcenter = $data ['service_costcenter'];
			
			$pdf->SetFont ( 'Arial', 'B', 12 );
			$pdf->myheader = 'A comercial de:  ' . $supplier;
			$pdf->AddPage ();
			$pdf->SetFont ( 'Arial', '', 12 );
			
			$dates = new Dates ( );
			$now = $dates->now ();
			$pdf->Cell ( 140, 10, 'Servicio - ' . $service_id );
			$pdf->Ln ( 10 );
			
			$pdf->Ln ( 20 );
			$pdf->SetFont ( 'Arial', '', 11 );
			$texto1 = 'Por este medio le estamos solicitando un transporte para el movimiento de funcionarios de nuestra empresa';
			$str = iconv ( 'UTF-8', 'windows-1252', $texto1 );
			$pdf->Cell ( 40, 10, $str );
			$pdf->Ln ( 6 );
			$texto1 = 'por razones de trabajo el dia ' . $service_date . ' .   Los datos de interés relacionados son: ';
			$str = iconv ( 'UTF-8', 'windows-1252', $texto1 );
			$pdf->Cell ( 40, 10, $str );
			$pdf->SetFont ( 'Arial', 'U', 8 );
			//
			$pdf->SetFillColor ( 255, 255, 255 );
			$pdf->Ln ( 12 );
			
			$pdf->SetTextColor ( 0, 0, 0 );
			$pdf->SetFont ( 'Arial', '', 9 );
			$str = iconv ( 'UTF-8', 'windows-1252', '  Cant. Plazas' );
			$pdf->Cell ( 25, 5, $str, '1', '', '', true );
			$pdf->SetFont ( 'Arial', '', 9 );
			$str = iconv ( 'UTF-8', 'windows-1252', 'Hora de salida' );
			$pdf->Cell ( 25, 5, $str, '1', '', '', true );
			$str = iconv ( 'UTF-8', 'windows-1252', 'Lugar de salida' );
			$pdf->Cell ( 45, 5, $str, '1', '', '', true );
			$str = iconv ( 'UTF-8', 'windows-1252', 'Lugar de almuerzo' );
			$pdf->Cell ( 45, 5, $str, '1', '', '', true );
			$str = iconv ( 'UTF-8', 'windows-1252', 'Lugar de llegada ' );
			$pdf->Cell ( 45, 5, $str, '1', '', '', true );
			
			$pdf->SetFont ( 'Arial', '', 10 );
			
			$pdf->Ln ( 5 );
			
			$str = iconv ( 'UTF-8', 'windows-1252', '  ' . $service_capacity );
			$pdf->Cell ( 25, 7, $str, '1', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $service_hour );
			$pdf->Cell ( 25, 7, $str, '1', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $province_exit );
			$pdf->Cell ( 45, 7, $str, '1', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $province_lunch );
			$pdf->Cell ( 45, 7, $str, '1', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $province_arrival );
			$pdf->Cell ( 45, 7, $str, '1', '', '', true );
			
			$pdf->Ln ( 7 );
			$pdf->Cell ( 25, 7, '', '1', '', '', true );
			
			$pdf->Cell ( 25, 7, '', '1', '', '', true );
			
			$str2 = iconv ( 'UTF-8', 'windows-1252', $place_exit );
			$pdf->Cell ( 45, 7, '( '.$str2.' )', '1', '', '', true );
			
			$str2 = iconv ( 'UTF-8', 'windows-1252', $place_lunch );
			$pdf->Cell ( 45, 7, $str2, '1', '', '', true );
			
			$str2 = iconv ( 'UTF-8', 'windows-1252', $place_arrival );
			$pdf->Cell ( 45, 7, '( '.$str2.' )', '1', '', '', true );
			
			$pdf->SetFont ( 'Arial', '', 8 );
			$pdf->Ln ( 15 );
			
			$count_space = substr_count ( $service_itinerary, "\n" );
			$length = strlen ( $service_itinerary );
			$first = strpos ( $service_itinerary, "\n" );
			$itenerary1 = $first > 0 ? substr ( $service_itinerary, 0, $first ) : $service_itinerary;
			
			$str = iconv ( 'UTF-8', 'windows-1252', 'Recorrido: ' . $itenerary1 );
			$pdf->Cell ( 200, 7, $str, '0', '', '', true );
			$pdf->Ln ( 7 );
			
			for($i = 0; $i < $count_space; $i ++) {
				$tmp = strpos ( $service_itinerary, "\n", $first );
				$itenerary1 = substr ( $service_itinerary, $first, $tmp );
				$str = iconv ( 'UTF-8', 'windows-1252', $itenerary1 );
				$pdf->Cell ( 200, 7, $str, '0', '', '', true );
				$pdf->Ln ( 7 );
				$first = $first + $tmp;
			}
			
			/*for ($i = 0; $i < $count_space; $i++){
				$flag = substr ( $service_itinerary, $first, $length );
				$tmp = strpos($flag, "\n");
				$itenerary1 = substr ( $flag, 0, $tmp );
				$str = iconv ( 'UTF-8', 'windows-1252', $flag." ". $tmp ." ". $first ." ". $length);
				$pdf->Cell ( 200, 7, $str, '0', '', '', true );
				$pdf->Ln ( 7 );
				$first = $tmp + $first;
			}*/
			
			/*$itenerary1 = substr ( $service_itinerary, 0, 130 );
			$last = strrpos ($itenerary1, ' ');
			$first = substr ( $service_itinerary, 0, $last );
			$temp = substr ( $service_itinerary, $last, 130 );
			$itenerary2 = substr ( $service_itinerary, 130, strlen($service_itinerary) );
			$second = $temp.$itenerary2;
			$str2 = iconv ( 'UTF-8', 'windows-1252', $second );
			$pdf->Cell ( 200, 7, $str, '0', '', '', true );
			$pdf->Ln ( 7 );
			$pdf->Cell ( 200, 7, $str2, '0', '', '', true );*/
			
			//
			$centrocosto = $service_costcenter;//Conf_costcenters_model::getNameById ( $this->session->userdata ( 'center_id' ) );
			
			$pdf->Ln ( 20 );
			$str = iconv ( 'UTF-8', 'windows-1252', '              Cargar presupuesto a:    ' . $centrocosto );
			$pdf->Cell ( 140, 7, $str, '', '', '', true );
			
			$pdf->SetTextColor ( 0, 0, 0 );
			//
			$pdf->Ln ( 10 );
			$pdf->SetFont ( 'Arial', '', 12 );
			$texto2 = 'Sin más le solicitamos confirmación de la recepción de la solicitud del servicio al teléfono ' . $phone . '.';
			$str = iconv ( 'UTF-8', 'windows-1252', $texto2 );
			$pdf->Cell ( 40, 10, $str );
			
			$pdf->Ln ( 7 );
			$texto2 = 'Agradecimientos anticipados por su atención.';
			$str = iconv ( 'UTF-8', 'windows-1252', $texto2 );
			$pdf->Cell ( 40, 10, $str );
			
			$pdf->Ln ( 30 );
			$firma = '___________________';
			$pdf->Cell ( 40, 10, $firma );
			$pdf->Ln ( 5 );
			$firma = $this->session->userdata ( 'person_fullname' );
			$str = iconv ( 'UTF-8', 'windows-1252', $firma );
			$pdf->Cell ( 40, 10, $str );
			
			$pdf->Ln ( 7 );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $position );
			$pdf->Cell ( 40, 10, $str );
			
			$pdf->Output ( 'Solicitud-' . $supplier . '-' . $service_id . '-' . $service_date . '.pdf', 'D' );
		} else {
			$this->redirectError ();
		}
		
	//endpdf
	}
	
	/**
	 * Elimina Transporte
	 * recibe como parametro el nombre de la provincia
	 */
	function delete($id) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'ticket/ticket_requestservices' );
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
	
	function getById($service_id) {
		$data = $this->conn->getById ( $service_id );
		die ( "{data : " . json_encode ( $data ) . "}" );
	}

}

?>
