<?php

/**
 * @orm conf_transports
 */
class Conf_tickettransports extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'conf/conf_tickettransports_model', 'conn', true );
	}
	
	function index() {
		$this->redirectError ();
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
		$hasta = (! isset ( $_POST ['limit'] )) ? 15 : $_POST ['limit'];
		$desde = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		
		$data = $this->conn->getData ( $hasta, $desde );
		$cant = $this->conn->getCant ();
		die ( "{count : " . $cant . ", data : " . json_encode ( $data ) . "}" );
	}
	
}

?>
