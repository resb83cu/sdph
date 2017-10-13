<?php
class Sys_menu extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'sys/sys_menu_model', 'conn', true );
	}
	
	function index() {
		
	}
	
	function getMenuAjaxJs() {
		$centinela = new Centinela();
		$roll_id = $centinela->get_roll_id();
		$datos = $this->conn->getMenu ($roll_id);
		$this->load->library ( 'tree' );
		$treeObj = new Tree ( $datos, 'root', 'menus_id', 'menus_parentid' );
		$menu = $treeObj->getTreeArr ();		

		$menu = json_encode ( $menu );
		die ( "{root: " . $menu . "}" );
	}
	
	public function setDataGrid() {
		$data = $this->conn->getData ();
		die ( "{data : " . json_encode ( $data ) . "}" );
	}

}
?>