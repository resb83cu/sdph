<?php

/**
 * 
 */
class Lodging_reservations extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'lodging/lodging_reservations_model', 'conn', true );
	}
	
	function index() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'lodging/lodging_reservations' );
		if ($flag) {
			
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'lodging/lodging_reservations_view' );
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
		
		$data = $this->conn->getData ( $to, $from );
		$cant = $this->conn->getCant ();
		//echo "<pre>"; print_r($data); echo "</pre>";
		die ( "{count : " . $cant . ", data : " . json_encode ( $data ) . "}" );
		//devuelve en formato Json los datos a cargar en el js por el grid(ver store del grid)
	}
	
	/**
	 *
	 *
	 */
	function insert() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'lodging/lodging_reservations' );
		if ($flag) {
			$result = $this->conn->insert ();
			die ( "{success : $result}" ); //devuelve true si tuvo exito la consulta
		} else {
			$this->redirectError ();
		}
	}
	
	/**
	 * Elimina lodging request
	 *
	 */
	function delete($id) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'lodging/lodging_reservations' );
		if ($flag) {
			
			$this->conn->delete ( $id );
		} else {
			$this->redirectError ();
		}
	}
	
	/**
	 * Devuelve un lodging request
	 *
	 */
	function get() {
		$data = $this->conn->get ();
		die ( "{data : " . json_encode ( $data ) . "}" );
	
	}
	
	function getById($reservation_id) {
		$data = $this->conn->getById ( $reservation_id );
		die ( "{data : " . json_encode ( $data ) . "}" );
		//echo "<pre>"; print_r($data); echo "</pre>";
	}
	
	//hasta aqui para la manipulacion de los datos normal, ahora definirmos metodos para el reporte y aprovechar el mismo controlador
	function reporteParametros() {
		$this->load->view ( 'sys/header_view' );
		$this->load->view ( 'lodging/lodging_reservations_parameters_view' );
		$this->load->view ( 'sys/footer_view' );
	}
	//  
	function reporte() {
		$this->load->view ( 'sys/header_view' );
		//$datos=array();
		/*este si es el nombre del componente, estos vienen de la forma de recoger los parametros*/
		$datos ['reservation_begindate'] = $this->input->post ( 'reservation_begindate' );
		$datos ['reservation_enddate'] = $this->input->post ( 'reservation_enddate' );
		
		$datos ['reservation_number'] = $this->input->post ( 'reservation_number' );
		
		$datos ['hotel_id'] = $this->input->post ( 'hotel_id' );
		$datos ['person_id'] = $this->input->post ( 'person_id' );
		
		$this->load->view ( 'lodging/lodging_reservations_report_view', $datos ); //le paso el arreglo a la vista
		$this->load->view ( 'sys/footer_view' );
	}
	
	//
	public function setDataGridConditional() {
		
		$to = (! isset ( $_POST ['limit'] )) ? 15 : $_POST ['limit'];
		
		$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		
		$datosForWheres = array ();
		/*este si es el nombre del componente, estos            vienen de la forma de recoger los parametros*/
		$datosForWheres ['reservation_begindate'] = $this->input->post ( 'reservation_begindateP' );
		$datosForWheres ['reservation_enddate'] = $this->input->post ( 'reservation_enddateP' );
		
		$datosForWheres ['reservation_number'] = $this->input->post ( 'reservation_numberP' );
		
		$datosForWheres ['hotel_id'] = $this->input->post ( 'hotel_idP' );
		$datosForWheres ['person_id'] = $this->input->post ( 'person_idP' );
		
		return $this->conn->getDataConditional ( $to, $from, $datosForWheres, 'no' ); //y ya devuelve el die con el count real y el data...
	//devuelve en formato Json los datos a cargar en el js por el grid(ver store del grid)
	

	}
	
	function exportToPdf() {
		$to = (! isset ( $_POST ['limit'] )) ? 1000000 : $_POST ['limit'];
		$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		
		$datosForWheres = array ();
		
		$datosForWheres ['reservation_id'] = $this->input->post ( 'reservation_id' ); /*este si es el nombre del componente, estos            vienen de la forma de recoger los parametros*/
		$datosForWheres ['reservation_begindate'] = $this->input->post ( 'reservation_begindate' );
		$datosForWheres ['reservation_enddate'] = $this->input->post ( 'reservation_enddate' );
		
		$datosForWheres ['reservation_number'] = $this->input->post ( 'reservation_number' );
		
		$datosForWheres ['hotel_id'] = $this->input->post ( 'hotel_id' );
		$datosForWheres ['person_id'] = $this->input->post ( 'person_id' );
		
		$data = $this->conn->getDataConditional ( $to, $from, $datosForWheres, 'si' ); //son todos pero hay que filtrar
		// $cant = $this->conn->getCant();//no porque cogeria toda la tabla y en el for para formar el pdf explotaria
		$cant = count ( $data ); //y como no se pagina pues el coutn es del total del arreglo recibido
		

		$this->load->library ( 'FPDF/pdf_lodging_reservations' );
		$pdf = new PDF_lodging_reservations ( );
		$pdf->AddPage ();
		
		$pdf->Ln ( 1 );
		
		$pdf->SetFont ( 'Arial', '', 8 );
		$flag = true;
		
		for($i = 0; $i < $cant; $i ++) {
			//$pdf->Cell(40,10,$data[$i]['request_id']);
			//$pdf->Cell(40,10,$data[$i]['request_date']);  
			if ($flag == false) {
				$flag = true;
				$pdf->SetFillColor ( 255, 255, 255 );
			} else {
				$flag = false;
				$pdf->SetFillColor ( 200, 215, 235 );
			}
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['reservation_begindate'] );
			$pdf->Cell ( 22, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['reservation_enddate'] );
			$pdf->Cell ( 22, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['reservation_number'] );
			$pdf->Cell ( 30, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['hotel_name'] );
			$pdf->Cell ( 30, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_fullname'] );
			$pdf->Cell ( 60, 7, $str, '', '', '', true );
			
			$pdf->Ln ();
		
		}
		
		$pdf->Output ( 'Lodging_Request', 'I' );
	
	}

}

?>