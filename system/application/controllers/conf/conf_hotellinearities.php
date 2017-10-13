<?php

class Conf_hotellinearities extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'conf/conf_hotellinearities_model', "conn", true );
	
	}
	
	function index() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'conf/conf_hotellinearities' );
		if ($flag) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'conf/conf_hotellinearities_view' );
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
		$hotel_id = $this->input->post ( 'hotel_id' );
		$data = $this->conn->getData ( $hotel_id );
		die ( "{data:" . json_encode ( $data ) . "}" );
	}
	
	public function setData() {
		$to = (! isset ( $_POST ['limit'] )) ? 15 : $_POST ['limit'];
		$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		$data = $this->conn->getDataGrid ( $to, $from );
		$cant = $this->conn->getCant ();
		//echo "<pre>"; print_r($datos); echo "</pre>";
		die ( "{count:" . $cant . ",data:" . json_encode ( $data ) . "}" );
	}
	
	/**
	 * Funcion para Insertar
	 *
	 */
	function insert() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'conf/conf_hotellinearities' );
		if ($flag) {
			$result = $this->conn->insert ();
			die ( "{success : $result}" );
		} else {
			$this->redirectError ();
		}
	}
	
	/**
	 * Elimina Hotel
	 * recibe como parametro el id del Hotel
	 */
	function delete($id) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'conf/conf_hotellinearities' );
		if ($flag) {
			$this->conn->delete ( $id );
		} else {
			$this->redirectError ();
		}
	
	}
	
	function getById($linearity_id) {
		$data = $this->conn->getById ( $linearity_id );
		die ( "{data : " . json_encode ( $data ) . "}" );
	}

}

?>