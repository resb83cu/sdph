<?php
class DocumentsAdmin extends Controller {
	function DocumentsAdmin() {
		parent::Controller ();
		$this->load->model ( 'documents_admin_model', 'conn', true );
	}
	
	function index() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'documentsAdmin' );
		if ($flag) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'documents_admin_view' );
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
		$hasta = (! isset ( $_POST ['limit'] )) ? 15 : $_POST ['limit'];
		$desde = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		
		$data = $this->conn->getData ( $hasta, $desde );
		$cant = $this->conn->getCant ();
		die ( "{count : " . $cant . ", data : " . json_encode ( $data ) . "}" );
	}
	
	/**
	 * Funcion para Insertar
	 *
	 */
	function insert() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'documentsAdmin' );
		if ($flag) {
			
			$result = $this->conn->insert ();
			die ( "{success : $result}" );
		} else {
			$this->redirectError ();
		}
	}
	
	/**
	 * 
	 * 
	 */
	function delete($document_id) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'documentsAdmin' );
		if ($flag) {
			$this->conn->delete ( $document_id );
		} else {
			$this->redirectError ();
		}
	
	}
	
	/**
	 * 
	 *
	 */
	function get() {
		$data = $this->conn->get ();
		die ( "{data : " . json_encode ( $data ) . "}" );
	
	}
	
	function getById($document_id) {
		$data = $this->conn->getById ( $document_id );
		die ( "{data : " . json_encode ( $data ) . "}" );
	}

}

?>
