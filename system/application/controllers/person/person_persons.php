<?php

/**
 * @orm conf_transports
 */
class Person_persons extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'person/person_persons_model', 'conn', true );
	}
	
	function index() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'person/person_persons' );
		if ($flag) {
			
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'person/person_persons_view' );
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
	public function setDataByProvinceId() {
		$province_id = $this->input->post ( 'province_id' );
		$data = $this->conn->getDataByProvinceId ( $province_id );
		die ( "{data : " . json_encode ( $data ) . "}" );
	}

	public function getDataByProvinceIdFilter()
    	{
        	$province_id = $this->input->post('province_id');
        	$name = $this->input->post('name');
        	$data = $this->conn->getDataByProvinceIdFilter($province_id, $name);
        	die ("{data : " . json_encode($data) . "}");
    	}
	
	public function setDataGrid() {
		$ci = $this->input->post ( 'ci' );
		$name = $this->input->post ( 'name' );
		$lastname = $this->input->post ( 'lastname' );
		$province = $this->input->post ( 'province' );
		$secondlastname = $this->input->post ( 'secondlastname' );
		$data = $this->conn->getData ($ci, $name, $lastname, $secondlastname, $province);
		die ( "{data : " . json_encode ( $data ) . "}" );
	}
	
	public function setData() {
		$to = (! isset ( $_POST ['limit'] )) ? 100 : $_POST ['limit'];
		$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		$ci = $this->input->post ( 'ci' );
		$name = $this->input->post ( 'name' );
		$lastname = $this->input->post ( 'lastname' );
		$province = $this->input->post ( 'province' );
		$secondlastname = $this->input->post ( 'secondlastname' );
		$data = $this->conn->getDataGrid ($to, $from, $ci, $name, $lastname, $secondlastname, $province);
		$cant = $this->conn->getCant ($ci, $name, $lastname, $secondlastname, $province);
		die ( "{count:" . $cant . ",data:" . json_encode ( $data ) . "}" );
	}
	
	/**
	 * Funcion para Insertar Provincia
	 *
	 */
	function insert() {
		if ($this->session->userdata ( 'roll_id' ) >= 3) {
			$result = $this->conn->insert ();
			die ( "{success : $result}" );
		} else {
			$this->redirectError ();
		}
	}
	
	/**
	 * Elimina Transporte
	 * recibe como parametro el id
	 */
	function delete($id) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'person/person_persons' );
		if ($flag) {
			
			$this->conn->delete ( $id );
		} else {
			$this->redirectError ();
		}
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
		die ( "{data : " . json_encode ( $data ) . "}" );
		//echo "<pre>"; print_r($data); echo "</pre>";
	}

}

?>
