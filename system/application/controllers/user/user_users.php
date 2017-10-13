<?php

/**
 * @orm conf_transports
 */
class User_users extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'user/user_users_model', 'conn', true );
	}
	
	function index() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'user/user_users' );
		if ($flag) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'user/user_users_view' );
			$this->load->view ( 'sys/footer_view' );
		} else {
			$this->redirectError ();
		}
	
	}
	
	function changePasswordForm() {
		if ($this->session->userdata ( 'roll_id' ) == 6) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'user/user_users_changepassword_view' );
			$this->load->view ( 'sys/footer_view' );
		} else {
			$this->redirectError ();
		}
	
	}

	function userContableView() {
		if ($this->session->userdata ( 'roll_id' ) == 6 || $this->session->userdata ( 'roll_id' ) == 8) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'user/user_users_contable_view' );
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
		$to = (! isset ( $_POST ['limit'] )) ? 100 : $_POST ['limit'];
		$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		$province_id = $this->input->post ( 'province_id' );
		$data = $this->conn->getData ( $to, $from, $province_id );
		$cant = $this->conn->getCant ();
		die ( "{count : " . $cant . ", data : " . json_encode ( $data ) . "}" );
	}
	
	public function setData() {
		$to = (! isset ( $_POST ['limit'] )) ? 100 : $_POST ['limit'];
		$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		$name = $this->input->post ( 'name' );
		$province = $this->input->post ( 'province' );
		$data = $this->conn->getDataGrid ( $to, $from, $name, $province );
		$cant = $this->conn->getCant ( $to, $from, $name, $province );
		die ( "{count : " . $cant . ", data : " . json_encode ( $data ) . "}" );
	}

    public function setDataContable() {
		$to = (! isset ( $_POST ['limit'] )) ? 100 : $_POST ['limit'];
		$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		$name = $this->input->post ( 'name' );
		$province = $this->input->post ( 'province' );
		$data = $this->conn->getDataGridContable ( $to, $from, $name, $province );
		$cant = count($data);
		die ( "{count : " . $cant . ", data : " . json_encode ( $data ) . "}" );
	}
	
	public function usersPdf() {
		$to = 1000000;
		$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		$dateStart = $this->input->post ( 'dateStart' );
		$dateEnd = $this->input->post ( 'dateEnd' );
		$supplier = $this->input->post ( 'supplier' );
		$data = $this->conn->getDataPdf ( $to, $from );
		$cant = count ( $data );
		
		$this->load->library ( 'FPDF/pdf_users' );
		$pdf = new Pdf_users ( 'L', 'mm', 'Letter' );
		$pdf->AddPage ();
		
		$pdf->Ln ( 1 );
		
		$pdf->SetFont ( 'Arial', '', 8 );
		$flag = true;

		for($i = 0; $i < $cant; $i ++) {
			if ($flag == false) {
				$flag = true;
				$pdf->SetFillColor ( 255, 255, 255 );
			} else {
				$flag = false;
				$pdf->SetFillColor ( 200, 215, 235 );
			}

			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['user_name'] );
			$pdf->Cell ( 25, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_fullname'] );
			$pdf->Cell ( 60, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['roll_description'] );
			$pdf->Cell ( 30, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['center_name'] );
			$pdf->Cell ( 60, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['user_createdate'] );
			$pdf->Cell ( 35, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['last_password'] );
			$pdf->Cell ( 45, 7, $str, '', '', '', true );
			
			$pdf->Ln ();
		}
		$pdf->Output ( 'Usuarios del Sistema.pdf', 'D' );
	}
	
	/**
	 * Funcion para Insertar
	 *
	 */
	function insert($operation) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'user/user_users' );
		if ($flag) {
			$result = $this->conn->insert ( $operation );
			die ( "{success : $result}" );
		} else {
			$this->redirectError ();
		}
	}
	
	/**
	 * Elimina
	 * recibe como parametro el id
	 */
	function delete($id) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'user/user_users' );
		if ($flag) {
			$this->conn->delete ( $id );
		} else {
			$this->redirectError ();
		}
	
	}
	
	function deleteSession($id) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'user/user_users' );
		if ($flag) {
			$this->conn->deleteSession ( $id );
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
	
	function changePassword() {
		$user_name = $this->input->post('user_name');
		$newpassword = $this->input->post('newpassword');
		$flag = $this->conn->changePassword($user_name, $newpassword);
		if ($flag == TRUE) {
		    echo "{success: true}";
		} else {
		    echo "{success: false, errors: { reason: 'Fallo al cambiar el password. Vuelva a intentarlo.' }}";
		}
	
	}

}

?>
