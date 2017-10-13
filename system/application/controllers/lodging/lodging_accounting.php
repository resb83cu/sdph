<?php

/**
 * @orm ticket_editetecsa
 */
class Lodging_accounting extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'ticket/ticket_conciliations_model', 'conn', true );
	}
	
	function index() {
		$this->load->view ( 'sys/header_view' );
		$this->load->view ( 'lodging/lodging_accounting_view' );
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
		$transport = $this->input->post('transport');
		$center = $this->input->post('center');
		$motive = $this->input->post('motive');
		
		return  $this->conn->getDataAccounting($to, $from, $dateStart, $dateEnd, $center, $transport, $motive);
	}
	
	public function setDataAccounting() {
		$to = (! isset ( $_POST ['limit'] )) ? 15 : $_POST ['limit'];
		$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		$dateStart = $this->input->post ( 'dateStart' );
		$dateEnd = $this->input->post ( 'dateEnd' );
		$transport = $this->input->post('transport');
		$center = $this->input->post('center');
		$motive = $this->input->post('motive');
		
		return  $this->conn->getDataAccounting($to, $from, $dateStart, $dateEnd, $center, $transport, $motive);
		//$cant = $this->conn->getCant();
		//echo "<pre>"; print_r($data); echo "</pre>";
		//die ( "{count : " . $cant . ", data : " . json_encode ( $data ) . "}" );
	}


}

?>
