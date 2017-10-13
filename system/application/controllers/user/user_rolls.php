<?php

/**
 * @orm user_rolls
 */
class User_rolls extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'user/user_rolls_model', 'conn', true );
	}
	
	function index() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'user/user_rolls' );
		if ($flag) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'user/user_rolls_view' );
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
		
		$data = $this->conn->getData ( $to, $from );
		$cant = $this->conn->getCant ();
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
	 * Elimina
	 * 
	 */
	function delete($id) {
		$this->conn->delete ( $id );
	
	}
	
	function getRolls() {
		$data = $this->conn->getRolls ();
		die ( "{data : " . json_encode ( $data ) . "}" );
	
	}
	
	function getById($roll_id) {
		//$roll_id = $this->input->post ( 'roll_id' );
		$data = $this->conn->getById ( $roll_id );
		die ( "{data : " . json_encode ( $data ) . "}" );
		//echo "<pre>"; print_r($data); echo "</pre>";
	}

}
?>