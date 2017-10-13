<?php

/**
 * @orm ticket_editetecsa
 */
class Lodging_lodgingconciliations extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'lodging/lodging_lodgingconciliations_model', 'conn', true );
	}
	
	function index() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'lodging/lodging_lodgingconciliations' );
		if ($flag) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'lodging/lodging_lodgingconciliations_view' );
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
		$dateStart = $this->input->post ( 'dateStart' );
		$dateEnd = $this->input->post ( 'dateEnd' );
		$hotel = $this->input->post ( 'hotel' );
		$province = $this->input->post ( 'province' );
		
		return $this->conn->getData ( /*$to, $from, */$dateStart, $dateEnd, $hotel, $province );
	}
	
	public function setDataAccounting() {
		$dateStart = $this->input->post ( 'dateStart' );
		$dateEnd = $this->input->post ( 'dateEnd' );
		$hotel = $this->input->post ( 'hotel' );
		$province = $this->input->post ( 'province' );
		$center = $this->input->post ( 'center' );
		$motive = $this->input->post ( 'motive' );
		
		return $this->conn->getDataAccounting ( /*$to, $from, */$dateStart, $dateEnd, $hotel, $province, $center, $motive );
	
	}
	
	public function accounting($show = false, $isPDF = 'no') {
		if ($this->session->userdata ( 'roll_id' ) >= 4) {
			if ($show == false && $isPDF == 'no') {
				$this->load->view ( 'sys/header_view' );
				$this->load->view ( 'lodging/lodging_accounting_view' );
				$this->load->view ( 'sys/footer_view' );
			}
			
			if ($show == true && $isPDF == 'no') {
				
				$to = (! isset ( $_POST ['limit'] )) ? 15 : $_POST ['limit'];
				$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
				
				$datosForWheres = array ();
				$datosForWheres ['dateStart'] = $this->input->post ( 'dateStart' ); //no es el nomnre ed los componentes sino el valor ed las variables javascript del baseparam del js
				$datosForWheres ['dateEnd'] = $this->input->post ( 'dateEnd' );
				$datosForWheres ['center'] = $this->input->post ( 'center' );
				$datosForWheres ['province'] = $this->input->post ( 'province' );
				$datosForWheres ['hotel'] = $this->input->post ( 'hotel' );
				$datosForWheres ['motive'] = $this->input->post ( 'motive' );
				
				die ( $this->conn->getDataAccounting ( $datosForWheres ['dateStart'], $datosForWheres ['dateEnd'], $datosForWheres ['hotel'], $datosForWheres ['province'], $datosForWheres ['center'], $datosForWheres ['motive'], $show, $isPDF ) );
			}
			
			if ($show == true && $isPDF == 'si') {
				
				$to = (! isset ( $_POST ['limit'] )) ? 1000000 : $_POST ['limit']; //todos
				$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
				
				$datosForWheres = array ();
				
				$datosForWheres ['dateStart'] = $this->input->post ( 'startdt' ); //aqui obligado  se pasael nombre ed los componentes, verificar que eeste en standar submit true la forma de los componentes
				$datosForWheres ['dateEnd'] = $this->input->post ( 'enddt' );
				$datosForWheres ['center'] = $this->input->post ( 'center_id' );
				$datosForWheres ['province'] = $this->input->post ( 'province_id' );
				$datosForWheres ['hotel'] = $this->input->post ( 'hotel_id' );
				$datosForWheres ['motive'] = $this->input->post ( 'motive_id' );
				
				$data = $this->conn->getDataAccounting ( $datosForWheres ['dateStart'], $datosForWheres ['dateEnd'], $datosForWheres ['hotel'], $datosForWheres ['province'], $datosForWheres ['center'], $datosForWheres ['motive'], $show, $isPDF );
				//son todos pero se filtra a veces
				// $cant = $this->conn->getCant();//no porque cogeria toda la tabla y en el for para formar el pdf explotaria
				$cant = count ( $data ); //y como no se pagina pues el count es del total del arreglo recibido
				

				$this->load->library ( 'FPDF/pdf_contab_lodging' );
				$pdf = new Pdf_contab_lodging ( 'L', 'mm', 'Letter' );
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
					$r = fmod($i + 1, 10);
					
					if ($r == 0) {
						$pdf->AddPage ();
					}
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_identity'] );
					$pdf->Cell ( 20, 5, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_worker'] );
					$pdf->Cell ( 60, 5, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['province_lodging'] );
					$pdf->Cell ( 42, 5, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['hotel_name'] );
					$pdf->Cell ( 40, 5, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['lodging_entrancedate'] );
					$pdf->Cell ( 23, 5, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['lodging_exitdate'] );
					$pdf->Cell ( 23, 5, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['diet'] );
					$pdf->Cell ( 20, 5, $str.',00', '', '', 'L', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['lodging'] );
					$pdf->Cell ( 20, 5, $str.',00', '', '', 'R', true );
					$pdf->Ln ();
					if ($i < $cant - 1) {
						$pdf->Cell ( 20, 5, 'Centro Costo:', 0, '', 'L', true );
						$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['center_name'] );
						$pdf->Cell ( 40, 5, $str, '', '', 'L', true );
						$pdf->Cell ( 15, 5, 'Autoriza:', 0, '', 'L', true );
						$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_licensedby'] );
						$pdf->Cell ( 80, 5, $str, '', '', 'L', true );
						$pdf->Cell ( 25, 5, 'Tarea Inversion:', 0, '', 'L', true );
						$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['request_inversiontask'] );
						$pdf->Cell ( 68, 5, $str, '', '', 'L', true );
						$pdf->Ln (5);
						$pdf->Cell ( 15, 5, 'Detalles:', 0, '', 'L', true );
						$str = iconv ( 'UTF-8', 'windows-1252', ucfirst($data [$i] ['request_details']) );
						$pdf->Cell ( 233, 5, $str, '', '', 'L', true );
						$pdf->Ln (5);

					}
				}
				$pdf->Output ( 'Reporte Contabilidad Hospedajes-'.$data [0] ['hotel_name'].'-'.$datosForWheres ['dateStart'].'-'.$datosForWheres ['dateEnd'].'.pdf', 'D' );
			
			}
		

		} else {
			$this->redirectError ();
		}
	
	}
	/**
	 * Funcion para Insertar Provincia
	 *
	 */
	function insert($request_id, $bill_number) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'lodging/lodging_lodgingconciliations' );
		if ($flag) {
			
			$result = $this->conn->insert ( $request_id, $bill_number );
			die ( "{success : $result}" );
		} else {
			$this->redirectError ();
		}
	}
	
	/**
	 * Elimina Transporte
	 * 
	 */
	function delete($id) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'lodging/lodging_lodgingconciliations' );
		if ($flag) {
			
			$this->conn->delete ( $id );
		} else {
			$this->redirectError ();
		}
	}
	
	/**
	 * Devuelve una provincia dado el nombre de la misma
	 *
	 */
	function getIds() {
		$data = $this->conn->getIds ();
		die ( "{data : " . json_encode ( $data ) . "}" );
	
	}
	
	function getById($request_id) {
		$data = $this->conn->getById ( $request_id );
		die ( "{data : " . json_encode ( $data ) . "}" );
		//echo "<pre>"; print_r($data); echo "</pre>";
	}

}

?>
