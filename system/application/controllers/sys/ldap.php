<?php

class Ldap extends Controller {
	function __construct() {
		parent::Controller ();
	}
	function index() {
		$this->begin ();
	}
	function begin() {
		$this->load->view ( "sys/header_view" );
		$this->showlogin ();
		$this->load->view ( "sys/footer_view" );
	}
	function showlogin() {
		$this->load->view ( "sys/ldaplogin" );
	}
	function login($isFirst=TRUE) {
		$host = 'ds.etecsa.cu';
		$port = '389';
		$basedn = 'ou=etecsa.cu,ou=People,dc=etecsa,dc=cu';
		$usuario = strtolower($this->input->post ( 'loginUsername' )); //le pasamos el nombre del componente que esta en l js que llamo a la pagina
		$password = $this->input->post ( 'loginPassword' );
		$user = 'uid='.$usuario.',ou=etecsa.cu,ou=People,dc=etecsa,dc=cu';
		$ds = ldap_connect ( $host, $port );
		$r = ldap_search ( $ds, $basedn, 'uid=' . $usuario );
		if ($r) {
			if (ldap_bind ( $ds, $user, $password )) {
				echo "{success: false, errors: { reason:'ok'}}";
			}else {
				echo "{success: false, errors: { reason:'mierda'}}";
			}
		}

	}
	function logout() {
		$centinela = new Centinela ( TRUE );
		$centinela->logout ();
		redirect ( '' );
	}

}
/* End of file systemp.php */
/* Location: ./system/application/controllers/systemp.php */
?>