<?php

/**
 * @orm ticket_editetecsa
 */
class Ticket_conciliations extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'ticket/ticket_conciliations_model', 'conn', true );
	}
	
	function index() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'ticket/ticket_conciliations' );
		if ($flag) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'ticket/ticket_conciliations_view' );
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
	
	public function accounting() {
		if ($this->session->userdata ( 'roll_id' ) >= 4) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'ticket/ticket_accounting_view' );
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
		$dateStart = $this->input->post ( 'dateStart' );
		$dateEnd = $this->input->post ( 'dateEnd' );
		$transport = $this->input->post ( 'transport' );
		return $this->conn->getData ( $dateStart, $dateEnd, $transport );
	}
	
	public function setDataAccounting() {
		$to = (! isset ( $_POST ['limit'] )) ? 15 : $_POST ['limit'];
		$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		$dateStart = $this->input->post ( 'dateStart' );
		$dateEnd = $this->input->post ( 'dateEnd' );
		$transport = $this->input->post ( 'transport' );
		$center = $this->input->post ( 'center' );
		$motive = $this->input->post ( 'motive' );
		
		return $this->conn->getDataAccounting ( $to, $from, $dateStart, $dateEnd, $center, $transport, $motive );
	
	}
	
	/**
	 * Funcion para Insertar Provincia
	 *
	 */
	function insert($request_id, $bill_number, $ticket_date) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'ticket/ticket_conciliations' );
		if ($flag) {
			$result = $this->conn->insert ( $request_id, $bill_number, $ticket_date );
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
		$flag = $centinela->accessTo ( 'ticket/ticket_conciliations' );
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
	function getIds() {
		$data = $this->conn->getIds ();
		die ( "{data : " . json_encode ( $data ) . "}" );
	
	}
	
	function getById($request_id) {
		$data = $this->conn->getById ( $request_id );
		die ( "{data : " . json_encode ( $data ) . "}" );
		//echo "<pre>"; print_r($data); echo "</pre>";
	}

}

?>
