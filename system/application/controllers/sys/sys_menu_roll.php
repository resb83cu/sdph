<?php
class Sys_menu_roll extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'sys/sys_menu_roll_model', 'conn', true );
	}
	
	function index() {
		$centinela = new Centinela();
		$flag = $centinela->accessTo('sys/sys_menu_roll');
		if ( $flag ) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'sys/sys_menu_roll_view' );
			$this->load->view ( 'sys/footer_view' );
		} else {
			$this->redirectError();
		}
				
	}
	
	function redirectError()
	{
		$this->load->view ( 'sys/header_view' );
		$this->load->view ( 'error_message' );
		$this->load->view ( 'sys/footer_view' );
	}
	
	function insert() {
		$centinela = new Centinela();
		$flag = $centinela->accessTo('sys/sys_menu_roll');
		if ( $flag ) {
			$result = $this->conn->insert ();
			die ( $result );
		} else {
			$this->redirectError ();
		}
	}
	
	public function setDataGrid() {
		$data = $this->conn->getData ();
		$cant = $this->conn->getCant ();
		die ( "{count : " . $cant . ", data : " . json_encode ( $data ) . "}" );
	}
	
}
?>