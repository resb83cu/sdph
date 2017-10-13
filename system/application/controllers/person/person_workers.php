<?php

/**
 * @orm conf_transports
 */
class Person_workers extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'person/person_workers_model', 'conn', true );
	}
	
	function index() {
		$this->load->view ( 'sys/header_view' );
		$this->load->view ( 'person/person_workers_view' );
		$this->load->view ( 'sys/footer_view' );
	
	}
	
	/**
	 * Busca los datos para llenar el grid y los devuelve en formato JSON
	 *
	 */
	public function setDataGrid() {
		$province_id = $this->input->post('province_id');
		$data = $this->conn->getData ( $province_id );
		//echo "<pre>"; print_r($data); echo "</pre>";
		die ( "{data : " . json_encode ( $data ) . "}" );
	}
	
	public function setDataByProvince() {
		$province_id = $this->input->post('province_id');
		$data = $this->conn->getDataByProvince ( $province_id );
		//echo "<pre>"; print_r($data); echo "</pre>";
		die ( "{data : " . json_encode ( $data ) . "}" );
	}
	
	public function setDirectorByProvince() {
		$province_id = $this->input->post('province_id');
		$data = $this->conn->getDirector ( $province_id );
		$cant = $this->conn->getCant ();
		//echo "<pre>"; print_r($data); echo "</pre>";
		die ( "{count : " . $cant . ", data : " . json_encode ( $data ) . "}" );
	}
	
	
	/**
	 * Funcion para Insertar
	 *
	 */
	function insert() {
		$result = $this->conn->insert ();
		die ( "{success : $result}" );
	}
	
	/**
	 * Elimina Transporte
	 * recibe como parametro el id
	 */
	function delete($id) {
		$this->conn->delete ( $id );
	
	}
	
	/**
	 * Devuelve todos los datos
	 *
	 */
	function get() {
		$data = $this->conn->get ();
		die ( "{data : " . json_encode ( $data ) . "}" );
	
	}
	
	function getById($person_id) {
		$data = $this->conn->getById ( $person_id );
		die ( "{data : " . json_encode ( $data ) . "}" );
		//echo "<pre>"; print_r($data); echo "</pre>";
	}
	
	function getCountById($person_id) {
		$data = $this->conn->getCountById ( $person_id );
		//die ( "{data : " . json_encode ( $data ) . "}" );
		echo "<pre>";
		print_r ( $data );
		echo "</pre>";
	}

}

?>
