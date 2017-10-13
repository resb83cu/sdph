<?php

/**
 * Licensee: George Bolanos
 * License Type: Purchased
 */

/**
 * @orm conf_provinces
 */
class Conf_provinces extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'conf/conf_provinces_model', 'conn', true );
	
	}
	
	function index() {
		$centinela = new Centinela();
		$flag = $centinela->accessTo('conf/conf_provinces');
		if ( $flag ) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'conf/conf_provinces_view' );
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
		$data = $this->conn->getData ();
		die ( "{data : " . json_encode ( $data ) . "}" );
	}
	
	public function setData() {
		$to = (! isset ( $_POST ['limit'] )) ? 30 : $_POST ['limit'];
		$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		
		$data = $this->conn->getDataGrid ( $to, $from );
		$cant = $this->conn->getCant ();
		die ( "{count : " . $cant . ", data : " . json_encode ( $data ) . "}" );
	}	
	
	/**
	 * Funcion para Insertar Provincia
	 *
	 */
	function insert() {
		$centinela = new Centinela();
		$flag = $centinela->accessTo('conf/conf_provinces');
		if ( $flag ) {
			$result = $this->conn->insertProvince ();
			die ( "{success : $result}" );
		} else {
			$this->redirectError ();
		}
	}
	
	/**
	 * Elimina Provincia
	 * recibe como parametro el nombre de la provincia
	 */
	function delete($id) {
		$centinela = new Centinela();
		$flag = $centinela->accessTo('conf/conf_provinces');
		if ( $flag ) {
			$this->conn->deleteProvince ( $id );
		} else {
			$this->redirectError ();
		}
	
	}
	
	/**
	 * Devuelve una provincia dado el nombre de la misma
	 *
	 */
	function getProvinces() {
		$data = $this->conn->getProvinces ();
		die ( "{data : " . json_encode ( $data ) . "}" );
	
	}
	
	function getById($province_id) {
		$data = $this->conn->getById ( $province_id );
		die ( "{data : " . json_encode ( $data ) . "}" );
		//echo "<pre>"; print_r($data); echo "</pre>";
	}

}

?>
