<?php

/**
 * 
 */
class Request_requests extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'request/request_requests_model', 'conn', true );
	}
	
	function index() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'request/request_requests' );
		if ($flag) {
			
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'request/request_requests_view' );
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
		$to = (! isset ( $_POST ['limit'] )) ? 15 : $_POST ['limit'];
		$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		$dateStart = $this->input->post ( 'dateStart' );
		$dateEnd = $this->input->post ( 'dateEnd' );
		return $this->conn->getData ( $to, $from, $dateStart, $dateEnd );
	}
	
	/**
	 *
	 *
	 */
	function insert() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'request/request_requests' );
		if ($flag) {
			$result = $this->conn->insert ();
			die ( $result );
		} else {
			$this->redirectError ();
		}
	}
	
	function requestProrogation() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'request/request_requests' );
		if ($flag) {
			$result = $this->conn->requestProrogation ();
			die ( $result );
		} else {
			$this->redirectError ();
		}
	}
	
	/**
	 * Elimina request request
	 *
	 */
	function delete($id) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'request/request_requests' );
		if ($flag) {
			$result = $this->conn->delete ( $id );
			die ( "{success : $result}" );
		} else {
			$this->redirectError ();
		}
	}
	
	function deleteTicket($id, $date) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'request/request_requests' );
		if ($flag) {
			$this->load->model ( 'request/request_tickets_model', 'ticket', true );
			$this->ticket->delete ( $id, $date );
		} else {
			$this->redirectError ();
		}
	
	}
	
	function deleteLodging($id) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'request/request_requests' );
		if ($flag) {
			$this->load->model ( 'request/request_lodgings_model', 'lodging', true );
			$this->lodging->delete ( $id );
		} else {
			$this->redirectError ();
		}
	
	}
	
	/**
	 * Devuelve un request request
	 *
	 */
	function get() {
		$data = $this->conn->get ();
		die ( "{data : " . json_encode ( $data ) . "}" );
	
	}
	
	function getById($request_id) {
		$data = $this->conn->getById ( $request_id );
		die ( "{data : " . json_encode ( $data ) . "}" );
		//echo "<pre>"; print_r($data); echo "</pre>";
	}
	
	function getAll() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'lodging/lodging_conciliations' );
		if ($flag) {
			$begin = $this->input->post ( 'dateStart' );
			$end = $this->input->post ( 'dateEnd' );
			$motive = $this->input->post ( 'motive_id' );
			$data = $this->conn->getCountLodgingProvincePerCenter ($begin, $end, $motive);
			die ( "{data : " . json_encode ( $data ) . "}" );
		} else {
			$this->redirectError ();
		}
	}
	
	function showReport() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'lodging/lodging_conciliations' );
		if ($flag) {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'lodging/lodging_province_per_center_view' );
			$this->load->view ( 'sys/footer_view' );
		} else {
			$this->redirectError ();
		}
		//echo "<pre>"; print_r($data); echo "</pre>";
	}
	
	//hasta aqui para la manipulacion de los datos normal, ahora definirmos metodos para el reporte y aprovechar el mismo controlador
	function report() {
		$this->load->view ( 'sys/header_view' );
		$this->load->view ( 'lodging/lodging_request_parameters_view' );
		$this->load->view ( 'sys/footer_view' );
	}
	//  
	function reporte() {
		$this->load->view ( 'sys/header_view' );
		//$datos=array();
		$datos ['motive_id'] = $this->input->post ( 'motive_id' ); /*este si es el nombre del componente, estos vienen de la forma de recoger los parametros*/
		$datos ['request_entrancedate'] = $this->input->post ( 'request_entrancedate' );
		$datos ['request_exitdate'] = $this->input->post ( 'request_exitdate' );
		
		$datos ['province_idlodging'] = $this->input->post ( 'province_idlodging' );
		
		$datos ['transport_id'] = $this->input->post ( 'transport_id' );
		$this->load->view ( 'lodging/lodging_request_report_view', $datos ); //le paso el arreglo a la vista
		$this->load->view ( 'sys/footer_view' );
	}
	
	//
	public function setDataGridConditional() {
		
		$to = (! isset ( $_POST ['limit'] )) ? 15 : $_POST ['limit'];
		
		$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
		
		$datosForWheres = array ();
		
		$datosForWheres ['motive_id'] = $this->input->post ( 'motive_idP' /*estos son los parametros pasados desde el stored con baseparam*/);
		
		$datosForWheres ['request_exitdate'] = $this->input->post ( 'request_exitdateP' );
		$datosForWheres ['request_entrancedate'] = $this->input->post ( 'request_entrancedateP' );
		
		$datosForWheres ['province_idlodging'] = $this->input->post ( 'province_idlodgingP' );
		$datosForWheres ['transport_id'] = $this->input->post ( 'transport_idP' );
		//         $datosForWheres['transport_itinerary']=$this->input->post('transport_itineraryP' ) ;
		

		/*$data = $this->conn->getDataConditional( $to, $from,$datosForWheres,'no');
          $cant = $this->conn->getCant ();    //aqui no sirve pero si paso solo el count me da menos que el cant dela consulta, o sea solo los 30 primeros cuando relamente puede haber mas
     	   die ( "{count : " . $cant . ", data : " . json_encode ( $data ) . "}" );
    */
		return $this->conn->getDataConditional ( $to, $from, $datosForWheres, 'no' ); //y ya devuelve el die con el count real y el data...
	//devuelve en formato Json los datos a cargar en el js por el grid(ver store del grid)
	

	}
	
	function exportToPdf() {
		
		$begin = $this->input->post ( 'startdt' );
		$end = $this->input->post ( 'enddt' );
		$motive = $this->input->post ( 'motive_id' );
		
		$data = $this->conn->getCountLodgingProvincePerCenter ( $begin, $end, $motive );
		$cant = count ( $data );
		

		$this->load->library ( 'FPDF/Pdf_lodging_province_per_center' );
		$pdf = new Pdf_lodging_province_per_center ( 'L', 'mm', 'Letter');
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

			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['center'] );
			$pdf->Cell ( 50, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['pri'] );
			$pdf->Cell ( 12, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['art'] );
			$pdf->Cell ( 12, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['myb'] );
			$pdf->Cell ( 12, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['hab'] );
			$pdf->Cell ( 12, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['mtz'] );
			$pdf->Cell ( 12, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['vcl'] );
			$pdf->Cell ( 12, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['cfg'] );
			$pdf->Cell ( 12, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['ssp'] );
			$pdf->Cell ( 12, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['cav'] );
			$pdf->Cell ( 12, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['cmg'] );
			$pdf->Cell ( 12, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['ltu'] );
			$pdf->Cell ( 12, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['hol'] );
			$pdf->Cell ( 12, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['grm'] );
			$pdf->Cell ( 12, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['scu'] );
			$pdf->Cell ( 12, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['gtm'] );
			$pdf->Cell ( 12, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['isj'] );
			$pdf->Cell ( 11, 7, $str, '', '', '', true );
			
			$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['total'] );
			$pdf->Cell ( 17, 7, $str, '', '', '', true );
						
			$pdf->Ln ();
		
		}
		
		$pdf->Output ( 'Reporte hospedados en provincias por Centro Contable.pdf', 'D' );
	
	}

}

?>