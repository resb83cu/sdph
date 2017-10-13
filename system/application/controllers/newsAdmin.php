<?php
class NewsAdmin extends Controller {
	function NewsAdmin() {
		parent::Controller ();
		$this->load->model ( 'news_admin_model', 'conn', true );
	}
	
	function index() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'newsAdmin' );
		if ($flag) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'news_admin_view' );
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
	

	public function setDataGrid() {
		$hasta = (! isset ( $_POST ['limit'] )) ? 15 : $_POST ['limit'];
		$desde = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		
		$data = $this->conn->getData ( $hasta, $desde );
		$cant = $this->conn->getCant ();
		die ( "{count : " . $cant . ", data : " . json_encode ( $data ) . "}" );
	}
	

	function insert() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'newsAdmin' );
		if ($flag) {
			
			$result = $this->conn->insert ();
			die ( "{success : $result}" );
		} else {
			$this->redirectError ();
		}
	}
	

	function delete($new_id) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'newsAdmin' );
		if ($flag) {
			$this->conn->delete ( $new_id );
		} else {
			$this->redirectError ();
		}
	
	}
	

	function get() {
		$data = $this->conn->get ();
		die ( "{data : " . json_encode ( $data ) . "}" );
	
	}
	
	function getById($new_id) {
		$data = $this->conn->getById ( $new_id );
		die ( "{data : " . json_encode ( $data ) . "}" );
	}

}

?>
