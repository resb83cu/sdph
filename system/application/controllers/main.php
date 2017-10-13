<?php
//include_once base_url()."/system/application/sdph/models/sys/system.php";
class Main extends Controller {
	
	function Main() {
		parent::Controller ();
		$this->load->scaffolding ( 'sys_menus' );
	}
	
	function index() {
		$this->begin ();
	}
	
	function begin() {
		$this->load->view ( "sys/header_view" );
		$this->load->view ( "sys/login_view" );
		$this->load->view ( "sys/footer_view" );
	}

}
/* End of file systemp.php */
/* Location: ./system/application/controllers/systemp.php */
?>