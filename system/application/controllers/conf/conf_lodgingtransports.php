<?php

/**
 * @orm conf_transports
 */
class Conf_lodgingtransports extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'conf/conf_lodgingtransports_model', 'conn', true );
	}
	
	function index() {
		$centinela = new Centinela();
		$flag = $centinela->accessTo('conf/conf_lodgingtransports');
		if ( $flag ) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'conf/conf_lodgingtransports_view' );
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
		$to = (! isset ( $_POST ['limit'] )) ? 30 : $_POST ['limit'];
		$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		
		$data = $this->conn->getData ( $to, $from );
		$cant = $this->conn->getCant ();
		die ( "{count : " . $cant . ", data : " . json_encode ( $data ) . "}" );
	}
	
	/**
	 * Funcion para Insertar Provincia
	 *
	 */
	function insert() {
		$centinela = new Centinela();
		$flag = $centinela->accessTo('conf/conf_lodgingtransports');
		if ( $flag ) {
			$result = $this->conn->insert ();
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
		$centinela = new Centinela();
		$flag = $centinela->accessTo('conf/conf_lodgingtransports');
		if ( $flag ) {
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
	
	function getById($transport_id) {
		$data = $this->conn->getById ( $transport_id );
		die ( "{data : " . json_encode ( $data ) . "}" );
	}

}

?>
