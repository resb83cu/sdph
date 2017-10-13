<?php

/**
 * @orm sys_logs
 */
class Sys_logs extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'sys/sys_logs_model', 'conn', true );
	}
	
	function index() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'sys/sys_logs' );
		if ($flag) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'sys/sys_logs_view' );
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
		$data = $this->conn->getData ( $to, $from, $dateStart, $dateEnd );
		$cant = $this->conn->getCant ($dateStart, $dateEnd);
		die ( "{count : " . $cant . ", data : " . json_encode ( $data ) . "}" );
	}

}

?>
