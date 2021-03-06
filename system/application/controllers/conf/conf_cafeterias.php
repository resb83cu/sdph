<?php

class Conf_cafeterias extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'conf/conf_cafeterias_model', "conn", true );
	
	}
	
	function index() {
		$centinela = new Centinela();
		$flag = $centinela->accessTo('conf/conf_cafeterias');
		if ( $flag ) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'conf/conf_cafeterias_view' );
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
		$province_id = $this->input->post ( 'province_id' );
		$datos = $this->conn->getData ( $province_id );
		die ( "{data:" . json_encode ( $datos ) . "}" );
	}
	
	public function setData() {
		$to = (! isset ( $_POST ['limit'] )) ? 15 : $_POST ['limit'];
		$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		$name = $this->input->post ( 'name' );
		$province = $this->input->post ( 'province' );
		$data = $this->conn->getDataGrid ($to, $from, $name, $province);
		$cant = $this->conn->getCant ($name, $province);
		die ( "{count:" . $cant . ",data:" . json_encode ( $data ) . "}" );
	}
	
	public function setDataByProvince() {
		$province_id = $this->input->post ( 'province_id' );
		$datos = $this->conn->getDataByProvince ( $province_id );
		die ( "{data:" . json_encode ( $datos ) . "}" );
	}
	
	/**
	 * Funcion para Insertar cafeterias
	 *
	 */
	function insert() {
		$centinela = new Centinela();
		$flag = $centinela->accessTo('conf/conf_cafeterias');
		if ( $flag ) {
			$result = $this->conn->insert ();
			die ( "{success : $result}" );
		} else {
			$this->redirectError ();
		}
	}
	
	/**
	 * Elimina cafeteria
	 * recibe como parametro el id del cafeteria
	 */
	function delete($id) {
		$centinela = new Centinela();
		$flag = $centinela->accessTo('conf/conf_cafeterias');
		if ( $flag ) {
			$this->conn->delete ( $id );
		} else {
			$this->redirectError ();
		}
	}
	
	function getById($cafeteria_id) {	
		$data = $this->conn->getById ( $cafeteria_id );
		die ( "{data : " . json_encode ( $data ) . "}" );
	}

}

?>