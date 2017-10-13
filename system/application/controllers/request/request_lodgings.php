<?php

/**
 * Licensee: George Bolanos
 * License Type: Purchased
 */

/**
 * @orm conf_provinces
 */
class Request_lodgings extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'request/request_lodgings_model', "conn", true );
		$this->load->helpers ( 'url' );
		$this->load->helpers ( 'form' );
	
	}
	
	function index() {
		$this->load->view ( 'sys/header_view' );
		$this->load->view ( 'request/request_lodgings_view' );
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
	
	/**
	 * Funcion para Insertar Solicitud de Hospedaje
	 *
	 */
	function insertRequestLodging() {
		$data ['lodging_entrydate'] = $this->input->post ( 'lodging_entrydate' );
		$data ['lodging_exitdate'] = $this->input->post ( 'lodging_exitdate' );
		$data ['place_id'] = $this->input->post ( 'place_id' );
		
		$result = $this->conn->insertRequestLodging ( $data );
		die ( "{success : $result}" );
	}
	
	/**
	 * Elimina Solicitud de Hospedaje
	 * recibe como parametro el nombre de la provincia
	 */
	function deleteRequestLodging() {
		$data = $this->uri->segment ( 4 );
		$this->conn->deleteRequestLodging ( $data );
		$this->load->view ( 'request/request_lodgings_view' );
	
	}

}

?>
