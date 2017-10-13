<?php

class Conf_municipalities extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'conf/conf_municipalities_model', "conn", true );
	
	}
	
	function index() {
		
		$this->load->view ( 'sys/header_view' );
		$this->load->view ( 'conf/conf_municipalities_view' );
		$this->load->view ( 'sys/footer_view' );
	
	}
	
	/**
	 * Busca los datos para llenar el grid y los devuelve en formato JSON
	 *
	 */
	public function setDatosGrid() {
		$hasta = (! isset ( $_POST ['limit'] )) ? 30 : $_POST ['limit'];
		$desde = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		$idProvince = $this->input->post ( 'province_id' );
		$datos = $this->conn->getDatos ( $hasta, $desde, $idProvince );
		$cant = $this->conn->getCant ();
		die ( "{count:" . $cant . ",data:" . json_encode ( $datos ) . "}" );
	}
	
	/**
	 * Funcion para Insertar Municipio
	 *
	 */
	function insertMunicipality() {
		$result = $this->conn->insertMunicipality ();
		die ( "{success : $result}" );
	}
	
	/**
	 * Elimina Provincia
	 * recibe como parametro el nombre del municipio
	 */
	function deleteMunicipality($id) {
		$this->conn->deleteMunicipality ( $id );
	
	}

}

?>