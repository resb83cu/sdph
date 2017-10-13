<?php

class Conf_places extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'conf/conf_places_model', "conn", true );
	}
	
	function index() {
		$this->load->view ( 'sys/header_view' );
		$this->load->view ( 'conf/conf_places_view' );
		$this->load->view ( 'sys/footer_view' );
	
	}
	
	/**
	 * Busca los datos para llenar el grid y los devuelve en formato JSON
	 * 
	 * @return JSON
	 *
	 */
	public function setDataGrid() {
		
		$to = (! isset ( $_POST ['limit'] )) ? 30 : $this->input->post ( 'limit' );
		$from = (! isset ( $_POST ['start'] )) ? 0 : $this->input->post ( 'start' );
		
		$data = $this->conn->getData ( $to, $from );
		$cant = $this->conn->getCount ();
		die ( "{count : " . $cant . ", data : " . json_encode ( $data ) . "}" );
	}
	
	public function get($field, $value) {
		if (isset ( $field ) && $field != '' && isset ( $value ) && $value != '') {
			$fields [$field] = $value;
		} else {
			$fields = null;
		}
		
		$data = json_encode ( $this->conn->get ( $fields ) );
		$count = $this->conn->getCount ();
		
		die ( "{count: $count, data: $data}" );
	
	}
	
	/**
	 * Funcion para Insertar registro
	 *
	 */
	function insert() {
		$result = $this->conn->insert ( $this->input->post ( 'place_name' ), $this->input->post ( 'municipality_id' ) );
		die ( "{success : $result}" );
	}
	
	/**
	 * Elimina un registro
	 * @param $id :id del registro a eliminar
	 */
	function delete($id) {
		$this->conn->delete ( $id );
	}

}

?>