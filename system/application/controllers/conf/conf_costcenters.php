<?php

/**
 * @orm conf_motives
 */
class Conf_costcenters extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'conf/conf_costcenters_model', 'conn', true );
	}
	
	function index() {
		$centinela = new Centinela();
		$flag = $centinela->accessTo('conf/conf_costcenters');
		if ( $flag ) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'conf/conf_costcenters_view' );
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

    public function getDataSap() {
        $data = $this->conn->getDataSap ();


        die ( "{data : " . json_encode ( $data ) . "}" );
    }
	
	public function setData() {
		$to = (! isset ( $_POST ['limit'] )) ? 15 : $_POST ['limit'];
		$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		$name = $this->input->post ( 'name' );
		$province = $this->input->post ( 'province' );
		$data = $this->conn->getDataGrid ($to, $from, $name, $province);
		$cant = $this->conn->getCant ($to, $from, $name, $province);
		die ( "{count : " . $cant . ", data : " . json_encode ( $data ) . "}" );
	}
	
	/**
	 * Funcion para Insertar Provincia
	 *
	 */
	function insert() {
		$centinela = new Centinela();
		$flag = $centinela->accessTo('conf/conf_costcenters');
		if ( $flag ) {
			$result = $this->conn->insert ();
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
		$flag = $centinela->accessTo('conf/conf_costcenters');
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
	
	function getById($center_id) {
		$data = $this->conn->getById ( $center_id );
		die ( "{data : " . json_encode ( $data ) . "}" );
	}
	
	function getDataByProvince(){
		$province_id = $province_id = $this->input->post('province_id');
		$data = $this->conn->getDataByProvince ($province_id);
		die ( "{data : " . json_encode ( $data ) . "}" );
	}

}

?>
