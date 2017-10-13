<?php

class Documents extends Controller {
	
	function Documents() {
		parent::Controller ();
	}
	
	function index() {
		$this->load->view ( 'sys/header_view' );
		$this->load->view ( 'documents_view' );
		$this->load->view ( 'sys/footer_view' );
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */
?>