<?php

/**
 * @orm 
 */
class Reports extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'report/reports_model', 'conn', true ); //un modelo para dentro poner las funciones para cada reporte de aqui en el controlador
	}
	
	function index() {
		$this->load->view ( 'sys/header_view' );
		$this->load->view ( 'sys/footer_view' );
	
	}
	
	function redirectError() {
		$this->load->view ( 'sys/header_view' );
		$this->load->view ( 'error_message' );
		$this->load->view ( 'sys/footer_view' );
	}
	
	function reportInternalTicket($show = false, $isPDF = 'no') {
		if ($this->session->userdata ( 'roll_id' ) >= 2) {
			if ($show == false && $isPDF == 'no') {
				$this->load->view ( 'sys/header_view' );
				$this->load->view ( 'report/reportInternalTicket_view' );
				$this->load->view ( 'sys/footer_view' );
			}
			
			if ($show == true && $isPDF == 'no') {
				$to = (! isset ( $_POST ['limit'] )) ? 15 : $_POST ['limit'];
				$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
				
				$datosForWheres ['begindate'] = $this->input->post ( 'begindate' );
				$datosForWheres ['enddate'] = $this->input->post ( 'enddate' );
				$datosForWheres ['center_id'] = $this->input->post ( 'center_id' );
				$datosForWheres ['person_id'] = $this->input->post ( 'person_id' );
				$datosForWheres ['person_identity'] = $this->input->post ( 'person_identity' );
				$datosForWheres ['province_idworkers'] = $this->input->post ( 'province_idworkers' );
				$datosForWheres ['province_idto'] = $this->input->post ( 'province_idto' );
				$datosForWheres ['province_idfrom'] = $this->input->post ( 'province_idfrom' );
				$datosForWheres ['motive_id'] = $this->input->post ( 'motive_id' );
				$datosForWheres ['transport_id'] = $this->input->post ( 'transport_id' );
				$datosForWheres ['transport_itinerary'] = $this->input->post ( 'transport_itinerary' );
				die ( $this->conn->getDataReportInternalTicket ( $to, $from, $datosForWheres, 'no' ) );
			}
			
			if ($show == true && $isPDF == 'si') {
				$to = (! isset ( $_POST ['limit'] )) ? 1000000 : $_POST ['limit']; //todos
				$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
				$datosForWheres = array ();
				$datosForWheres ['begindate'] = $this->input->post ( 'begindate' );
				$datosForWheres ['enddate'] = $this->input->post ( 'enddate' );
				$datosForWheres ['center_id'] = $this->input->post ( 'center_id' );
				$datosForWheres ['person_id'] = $this->input->post ( 'person_id' );
				$datosForWheres ['person_identity'] = $this->input->post ( 'person_identity' );
				$datosForWheres ['province_idworkers'] = $this->input->post ( 'province_idworkers' );
				$datosForWheres ['province_idto'] = $this->input->post ( 'province_idto' );
				$datosForWheres ['province_idfrom'] = $this->input->post ( 'province_idfrom' );
				$datosForWheres ['motive_id'] = $this->input->post ( 'motive_id' );
				$datosForWheres ['transport_id'] = $this->input->post ( 'transport_id' );
				$datosForWheres ['transport_itinerary'] = $this->input->post ( 'transport_itinerary' );
				$data = $this->conn->getDataReportInternalTicket ( $to, $from, $datosForWheres, 'si' ); //son todos pero se filtra a veces
				// $cant = $this->conn->getCant();//no porque cogeria toda la tabla y en el for para formar el pdf explotaria
				$cant = count ( $data ); //y como no se pagina pues el coutn es del total del arreglo recibido
				$this->load->library ( 'FPDF/pdf_reportinternalticket' );
				$pdf = new PDF_reportinternalticket ( 'L', 'mm', 'Letter' );
				$pdf->AddPage ();
				$pdf->Ln ( 1 );
				
				$pdf->SetFont ( 'Arial', '', 8 );
				$flag = true;
				
				for($i = 0; $i < $cant; $i ++) {
					//$pdf->Cell(40,10,$data[$i]['request_id']);
					//$pdf->Cell(40,10,$data[$i]['request_date']);  
					if ($flag == false) {
						$flag = true;
						$pdf->SetFillColor ( 255, 255, 255 );
					} else {
						$flag = false;
						$pdf->SetFillColor ( 200, 215, 235 );
					}
					$str = iconv ( 'UTF-8', 'windows-1252', $i + 1 );
					$pdf->Cell ( 7, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person'] );
					$pdf->Cell ( 60, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['identity'] );
					$pdf->Cell ( 25, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['ticket_date'] );
					$pdf->Cell ( 20, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['provinceFrom'] );
					$pdf->Cell ( 30, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['provinceTo'] );
					$pdf->Cell ( 30, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['estado'] );
					$pdf->Cell ( 20, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['center'] );
					$pdf->Cell ( 50, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['motive'] );
					$pdf->Cell ( 30, 7, $str, '', '', '', true );
					/*$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['details'] );
			$pdf->Cell ( 30, 7, $str, '', '', '', true );
			*/
					$pdf->Ln ();
				
				}
				$fecha = new Dates ( );
				$pdf->Output ( 'Reporte de pasaje -' . $fecha->now () . '.pdf', 'D' );
			}
		} else {
			$this->redirectError ();
		}
		//fin pdf=si
	}
	//
	function reportInternalLodging($show = false, $isPDF = 'no') {
		//if ($this->session->userdata ( 'roll_id' ) >= 1) {
		if ($show == false && $isPDF == 'no') {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'report/reportInternalLodging_view' );
			$this->load->view ( 'sys/footer_view' );
		}
		
		if ($show == true && $isPDF == 'no') {
			$to = (! isset ( $_POST ['limit'] )) ? 15 : $_POST ['limit'];
			$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
			$datosForWheres ['begindate'] = $this->input->post ( 'begindate' );
			$datosForWheres ['enddate'] = $this->input->post ( 'enddate' );
			$datosForWheres ['center_id'] = $this->input->post ( 'center_id' );
			$datosForWheres ['person_id'] = $this->input->post ( 'person_id' );
			$datosForWheres ['person_identity'] = $this->input->post ( 'person_identity' );
			$datosForWheres ['province_idworkers'] = $this->input->post ( 'province_idworkers' );
			$datosForWheres ['province_idlodging'] = $this->input->post ( 'province_idlodging' );
			$datosForWheres ['motive_id'] = $this->input->post ( 'motive_id' );
			$datosForWheres ['hotel_id'] = $this->input->post ( 'hotel_id' );
			$datosForWheres ['reinforce'] = $this->input->post('reinforce');
			$datosForWheres ['chain_id'] = $this->input->post('chain_id');
			die ( $this->conn->getDataReportInternalLodging ( $to, $from, $datosForWheres, 'no' ) );
		}

		if ($show == true && $isPDF == 'si') {
			$to = (! isset ( $_POST ['limit'] )) ? 1000000 : $_POST ['limit']; //todos
			$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
			$datosForWheres = array ();
			$datosForWheres ['begindate'] = $this->input->post ( 'begindate' );
			$datosForWheres ['enddate'] = $this->input->post ( 'enddate' );
			$datosForWheres ['center_id'] = $this->input->post ( 'center_id' );
			$datosForWheres ['person_id'] = $this->input->post ( 'person_id' );
			$datosForWheres ['person_identity'] = $this->input->post ( 'person_identity' );
			$datosForWheres ['province_idworkers'] = $this->input->post ( 'province_idworkers' );
			$datosForWheres ['province_idlodging'] = $this->input->post ( 'province_idlodging' );
			$datosForWheres ['motive_id'] = $this->input->post ( 'motive_id' );
			$datosForWheres ['hotel_id'] = $this->input->post ( 'hotel_id' );
			$datosForWheres ['reinforce'] = $this->input->post('reinforce');
			$datosForWheres ['chain_id'] = $this->input->post('chain_id');
			$data = $this->conn->getDataReportInternalLodging ( $to, $from, $datosForWheres, 'si' ); //son todos pero se filtra a veces
			// $cant = $this->conn->getCant();//no porque cogeria toda la tabla y en el for para formar el pdf explotaria
			$cant = count ( $data ); //y como no se pagina pues el coutn es del total del arreglo recibido
			$this->load->library ( 'FPDF/pdf_reportinternallodging' );
			$pdf = new Pdf_reportinternallodging ( 'L', 'mm', 'Letter' );
			$pdf->AddPage ();
			
			$pdf->Ln ( 1 );
			
			$pdf->SetFont ( 'Arial', '', 8 );
			$flag = true;
			
			for($i = 0; $i < $cant; $i ++) {
				//$pdf->Cell(40,10,$data[$i]['request_id']);
				//$pdf->Cell(40,10,$data[$i]['request_date']);  
				if ($flag == false) {
					$flag = true;
					$pdf->SetFillColor ( 255, 255, 255 );
				} else {
					$flag = false;
					$pdf->SetFillColor ( 200, 215, 235 );
				}
				$str = iconv ( 'UTF-8', 'windows-1252', $i + 1 );
				$pdf->Cell ( 7, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person'] );
				$pdf->Cell ( 60, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['identity'] );
				$pdf->Cell ( 25, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['lodging_entrancedate'] );
				$pdf->Cell ( 20, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['lodging_exitdate'] );
				$pdf->Cell ( 20, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['provinceLodging'] );
				$pdf->Cell ( 30, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['hotel'] );
				$pdf->Cell ( 45, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['center'] );
				$pdf->Cell ( 35, 7, $str, '', '', '', true );
				
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['motive'] );
				$pdf->Cell ( 25, 7, $str, '', '', '', true );
				//$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['details'] );
				//$pdf->Cell ( 30, 7, $str, '', '', '', true );
				$pdf->Ln ();
			}
			$fecha = new Dates ( );
			$pdf->Output ( 'Reporte de hospedaje -' . $fecha->now () . '.pdf', 'D' );
		} //fin pdf=si
	/*} else {
			$this->redirectError ();
		}*/
	}
	//
	function reportTicket($show = false, $isPDF = 'no') {
		if ($show == false && $isPDF == 'no') {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'report/reportTicket_view' );
			$this->load->view ( 'sys/footer_view' );
		}
		
		if ($show == true && $isPDF == 'no') {
			$to = (! isset ( $_POST ['limit'] )) ? 25 : $_POST ['limit'];
			$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
			$datosForWheres ['begindate'] = $this->input->post ( 'begindate' );
			$datosForWheres ['enddate'] = $this->input->post ( 'enddate' );
			$datosForWheres ['motive_id'] = $this->input->post ( 'motive_id' );
			$datosForWheres ['province_idfrom'] = $this->input->post ( 'province_idfrom' );
			$datosForWheres ['province_idto'] = $this->input->post ( 'province_idto' );
			$datosForWheres ['transport_id'] = $this->input->post ( 'transport_id' );
			$datosForWheres ['transport_itinerary'] = $this->input->post ( 'transport_itinerary' );
			$datosForWheres ['province_idworkers'] = $this->input->post ( 'province_idworkers' );
			$datosForWheres ['person_id'] = $this->input->post ( 'person_id' );
			$datosForWheres ['person_identity'] = $this->input->post ( 'person_identity' );
			die ( $this->conn->getDataReportTicket ( $to, $from, $datosForWheres, 'no' ) );
		}
	
	}
	
	function reportLodging($show = false, $isPDF = 'no') {
		if ($show == false && $isPDF == 'no') {
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'report/reportLodging_view' );
			$this->load->view ( 'sys/footer_view' );
		}
		
		if ($show == true && $isPDF == 'no') {
			$to = (! isset ( $_POST ['limit'] )) ? 25 : $_POST ['limit'];
			$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
			$datosForWheres ['begindate'] = $this->input->post ( 'begindate' );
			$datosForWheres ['enddate'] = $this->input->post ( 'enddate' );
			$datosForWheres ['motive_id'] = $this->input->post ( 'motive_id' );
			$datosForWheres ['province_idlodging'] = $this->input->post ( 'province_idlodging' );
			//$datosForWheres['province_idlodging']= $this->input->post('province_idlodging');
			$datosForWheres ['hotel_id'] = $this->input->post ( 'hotel_id' );
			$datosForWheres ['province_idworkers'] = $this->input->post ( 'province_idworkers' );
			$datosForWheres ['person_id'] = $this->input->post ( 'person_id' );
			$datosForWheres ['person_identity'] = $this->input->post ( 'person_identity' );
			die ( $this->conn->getDataReportLodging ( $to, $from, $datosForWheres, 'no' ) );
		}
	}
	
	function reportSnack($show = false, $isPDF = 'no') {
		if ($this->session->userdata ( 'roll_id' ) == 5 || $this->session->userdata ( 'roll_id' ) == 6 || $this->session->userdata ( 'province_id' ) == 15) {
			if ($show == false && $isPDF == 'no') {
				$this->load->view ( 'sys/header_view' );
				$this->load->view ( 'report/reportInternalTicketSnack_view' );
				$this->load->view ( 'sys/footer_view' );
			}
			
			if ($show == true && $isPDF == 'no') {
				$date = $this->input->post ( 'begindate' );
				die ( $this->conn->getDataReportSnack ( $date, 'no' ) );
			}
			
			if ($show == true && $isPDF == 'si') {
				$date = $this->input->post ( 'begindate' );
				$data = $this->conn->getDataReportSnack ( $date, 'si' );
				$cant = count ( $data );
				$this->load->library ( 'FPDF/pdf_reportinternalstack' );
				$pdf = new Pdf_reportinternalstack ( 'P', 'mm', 'Letter' );
				$pdf->AddPage ();
				$pdf->Ln ( 1 );
				
				$pdf->SetFont ( 'Arial', '', 8 );
				$flag = true;
				
				for($i = 0; $i < $cant; $i ++) {
					//$pdf->Cell(40,10,$data[$i]['request_id']);
					//$pdf->Cell(40,10,$data[$i]['request_date']);  
					if ($flag == false) {
						$flag = true;
						$pdf->SetFillColor ( 255, 255, 255 );
					} else {
						$flag = false;
						$pdf->SetFillColor ( 200, 215, 235 );
					}
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person'] );
					$pdf->Cell ( 60, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['identity'] );
					$pdf->Cell ( 25, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['ticket_date'] );
					$pdf->Cell ( 20, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['provinceFrom'] );
					$pdf->Cell ( 30, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['provinceTo'] );
					$pdf->Cell ( 30, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['estado'] );
					$pdf->Cell ( 20, 7, $str, '', '', '', true );
					$pdf->Ln ();
				
				}
				$fecha = new Dates ( );
				$pdf->Output ( 'Reporte de merienda -' . $date . '-' . $fecha->now () . '.pdf', 'D' );
			}
		} else {
			$this->redirectError ();
		}
		//fin pdf=si
	

	}
	
	function reportLodgingMenByDay($show = false, $isPDF = 'no') {
		if ($this->session->userdata ( 'roll_id' ) >= 2) {
			if ($show == false && $isPDF == 'no') {
				$this->load->view ( 'sys/header_view' );
				$this->load->view ( 'report/reportLodgingMenByDay_view' );
				$this->load->view ( 'sys/footer_view' );
			}
			
			if ($show == true && $isPDF == 'no') {
				$to = (! isset ( $_POST ['limit'] )) ? 50 : $_POST ['limit'];
				$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
				$datosForWheres ['begindate'] = $this->input->post ( 'begindate' );
				$datosForWheres ['center_id'] = $this->input->post ( 'center_id' );
				$datosForWheres ['province_idlodging'] = $this->input->post ( 'province_idlodging' );
				$datosForWheres ['motive_id'] = $this->input->post ( 'motive_id' );
				$datosForWheres ['hotel_id'] = $this->input->post ( 'hotel_id' );
				die ( $this->conn->getDataReportLodgingMenByDay ( $to, $from, $datosForWheres, 'no' ) );
			}
			
			if ($show == true && $isPDF == 'si') {
				$to = (! isset ( $_POST ['limit'] )) ? 1000000 : $_POST ['limit'];
				$from = (! isset ( $_POST ["start"] )) ? 0 : $_POST ["start"];
				$datosForWheres = array ();
				$datosForWheres ['begindate'] = $this->input->post ( 'begindate' );
				$datosForWheres ['center_id'] = $this->input->post ( 'center_id' );
				$datosForWheres ['province_idlodging'] = $this->input->post ( 'province_idlodging' );
				$datosForWheres ['motive_id'] = $this->input->post ( 'motive_id' );
				$datosForWheres ['hotel_id'] = $this->input->post ( 'hotel_id' );
				$data = $this->conn->getDataReportLodgingMenByDay ( $to, $from, $datosForWheres, 'si' ); //son todos pero se filtra a veces
				// $cant = $this->conn->getCant();//no porque cogeria toda la tabla y en el for para formar el pdf explotaria
				$cant = count ( $data ); //y como no se pagina pues el coutn es del total del arreglo recibido
				$this->load->library ( 'FPDF/pdf_reportinternallodging' );
				$pdf = new Pdf_reportinternallodging ( 'L', 'mm', 'Letter' );
				$pdf->AddPage ();
				
				$pdf->Ln ( 1 );
				
				$pdf->SetFont ( 'Arial', '', 8 );
				$flag = true;
				
				for($i = 0; $i < $cant; $i ++) {
					//$pdf->Cell(40,10,$data[$i]['request_id']);
					//$pdf->Cell(40,10,$data[$i]['request_date']);  
					if ($flag == false) {
						$flag = true;
						$pdf->SetFillColor ( 255, 255, 255 );
					} else {
						$flag = false;
						$pdf->SetFillColor ( 200, 215, 235 );
					}
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person'] );
					$pdf->Cell ( 60, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['identity'] );
					$pdf->Cell ( 25, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['lodging_entrancedate'] );
					$pdf->Cell ( 20, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['lodging_exitdate'] );
					$pdf->Cell ( 20, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['provinceLodging'] );
					$pdf->Cell ( 30, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['hotel'] );
					$pdf->Cell ( 30, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['center'] );
					$pdf->Cell ( 50, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['motive'] );
					$pdf->Cell ( 30, 7, $str, '', '', '', true );
					//$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['details'] );
					//$pdf->Cell ( 30, 7, $str, '', '', '', true );
					$pdf->Ln ();
				}
				$fecha = new Dates ( );
				$pdf->Output ( 'Reporte de hospedaje -' . $fecha->now () . '.pdf', 'D' );
			} //fin pdf=si
		} else {
			$this->redirectError ();
		}
	}
	
	function testexcel() {
		Include 'PHPExcel.php';
		/** PHPExcel_Writer_Excel2007 */
		Include 'PHPExcel/Writer/Excel5.php';
		
		// Create new PHPExcel object
		echo date ( 'H:i:s' ) . " Create new PHPExcel object\n";
		$objPHPExcel = new PHPExcel ( );
		
		// Set properties
		echo date ( 'H:i:s' ) . " Set properties\n";
		$objPHPExcel->getProperties ()->setCreator ( "BazZ" );
		$objPHPExcel->getProperties ()->setLastModifiedBy ( "BazZ" );
		$objPHPExcel->getProperties ()->setTitle ( "TestExcel" );
		$objPHPExcel->getProperties ()->setSubject ( "" );
		
		// Set row height
		$objPHPExcel->getActiveSheet ()->getRowDimension ( 1 )->setRowHeight ( 50 );
		$objPHPExcel->getActiveSheet ()->getRowDimension ( 2 )->setRowHeight ( 25 );
		
		// Set column width
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'A' )->setWidth ( 5 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'B' )->setWidth ( 30 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'C' )->setWidth ( 30 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'D' )->setWidth ( 40 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'E' )->setWidth ( 15 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'F' )->setWidth ( 15 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'G' )->setWidth ( 30 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'H' )->setWidth ( 10 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'I' )->setWidth ( 20 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'J' )->setWidth ( 20 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'K' )->setWidth ( 20 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'L' )->setWidth ( 20 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'M' )->setWidth ( 20 );
		$objPHPExcel->getActiveSheet ()->getColumnDimension ( 'N' )->setWidth ( 20 );
		
		//Merge cells (warning: the row index is 0-based)
		$objPHPExcel->getActiveSheet ()->mergeCellsByColumnAndRow ( 0, 1, 13, 1 );
		$objPHPExcel->getActiveSheet ()->mergeCellsByColumnAndRow ( 0, 2, 13, 2 );
		$objPHPExcel->getActiveSheet ()->mergeCellsByColumnAndRow ( 0, 3, 0, 5 );
		$objPHPExcel->getActiveSheet ()->mergeCellsByColumnAndRow ( 1, 3, 1, 5 );
		$objPHPExcel->getActiveSheet ()->mergeCellsByColumnAndRow ( 2, 3, 3, 3 );
		$objPHPExcel->getActiveSheet ()->mergeCellsByColumnAndRow ( 2, 4, 2, 5 );
		$objPHPExcel->getActiveSheet ()->mergeCellsByColumnAndRow ( 3, 4, 3, 5 );
		$objPHPExcel->getActiveSheet ()->mergeCellsByColumnAndRow ( 4, 3, 4, 5 );
		$objPHPExcel->getActiveSheet ()->mergeCellsByColumnAndRow ( 5, 3, 5, 5 );
		$objPHPExcel->getActiveSheet ()->mergeCellsByColumnAndRow ( 6, 3, 6, 5 );
		$objPHPExcel->getActiveSheet ()->mergeCellsByColumnAndRow ( 7, 3, 9, 3 );
		$objPHPExcel->getActiveSheet ()->mergeCellsByColumnAndRow ( 7, 4, 7, 5 );
		$objPHPExcel->getActiveSheet ()->mergeCellsByColumnAndRow ( 8, 4, 9, 4 );
		$objPHPExcel->getActiveSheet ()->mergeCellsByColumnAndRow ( 10, 3, 10, 5 );
		$objPHPExcel->getActiveSheet ()->mergeCellsByColumnAndRow ( 11, 3, 11, 5 );
		$objPHPExcel->getActiveSheet ()->mergeCellsByColumnAndRow ( 12, 3, 12, 5 );
		$objPHPExcel->getActiveSheet ()->mergeCellsByColumnAndRow ( 13, 3, 13, 5 );
		
		//Modify cell's style
		$objPHPExcel->getActiveSheet ()->getStyle ( 'A1' )->applyFromArray ( array ('font' => array ('name' => 'Times New Roman', 'bold' => true, 'italic' => false, 'size' => 20 ), 'alignment' => array ('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true ) ) );
		
		$objPHPExcel->getActiveSheet ()->getStyle ( 'A2' )->applyFromArray ( array ('font' => array ('name' => 'Times New Roman', 'bold' => true, 'italic' => false, 'size' => 14 ), 'alignment' => array ('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true ) ) );
		
		$objPHPExcel->getActiveSheet ()->duplicateStyleArray ( array ('font' => array ('name' => 'Times New Roman', 'bold' => true, 'italic' => false, 'size' => 12 ), 'borders' => array ('top' => array ('style' => PHPExcel_Style_Border::BORDER_DOUBLE ), 'bottom' => array ('style' => PHPExcel_Style_Border::BORDER_DOUBLE ), 'left' => array ('style' => PHPExcel_Style_Border::BORDER_DOUBLE ), 'right' => array ('style' => PHPExcel_Style_Border::BORDER_DOUBLE ) ), 'alignment' => array ('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true ) ), 'A3:N5' );
		
		// Add some data
		echo date ( 'H:i:s' ) . " Add some data\n";
		$objPHPExcel->setActiveSheetIndex ( 0 );
		
		$objPHPExcel->getActiveSheet ()->SetCellValue ( 'A1', 'Try PHPExcel with CodeIgniter' );
		$objPHPExcel->getActiveSheet ()->SetCellValue ( 'A2', "Subtitle here" );
		
		$objPHPExcel->getActiveSheet ()->SetCellValue ( 'A3', "No." );
		$objPHPExcel->getActiveSheet ()->SetCellValue ( 'B3', "Name" );
		$objPHPExcel->getActiveSheet ()->SetCellValue ( 'C3', "Number" );
		$objPHPExcel->getActiveSheet ()->SetCellValue ( 'C4', "Code" );
		$objPHPExcel->getActiveSheet ()->SetCellValue ( 'D4', "Register" );
		$objPHPExcel->getActiveSheet ()->SetCellValue ( 'E3', "Space (M2)" );
		$objPHPExcel->getActiveSheet ()->SetCellValue ( 'F3', "Year" );
		$objPHPExcel->getActiveSheet ()->SetCellValue ( 'G3', "Location" );
		
		// Rename sheet
		echo date ( 'H:i:s' ) . " Rename sheet\n";
		$objPHPExcel->getActiveSheet ()->setTitle ( 'Try PHPExcel with CodeIgniter' );
		
		// Save Excel 2003 file
		echo date ( 'H:i:s' ) . " Write to Excel2003 format\n";
		$objWriter = new PHPExcel_Writer_Excel5 ( $objPHPExcel );
		$objWriter->save ( str_replace ( '.php', '.xls', __FILE__ ) );
	}
	
	function to_excel() {
		$datosForWheres = array ();
		$datosForWheres ['begindate'] = $this->input->post ( 'begindate' );
		$datosForWheres ['enddate'] = $this->input->post ( 'enddate' );
		$datosForWheres ['center_id'] = $this->input->post ( 'center_id' );
		$datosForWheres ['person_id'] = $this->input->post ( 'person_id' );
		$datosForWheres ['person_identity'] = $this->input->post ( 'person_identity' );
		$datosForWheres ['province_idworkers'] = $this->input->post ( 'province_idworkers' );
		$datosForWheres ['province_idlodging'] = $this->input->post ( 'province_idlodging' );
		$datosForWheres ['motive_id'] = $this->input->post ( 'motive_id' );
		$datosForWheres ['hotel_id'] = $this->input->post ( 'hotel_id' );
		$this->load->plugin ( 'to_excel' );
		$this->db->select ( 'person_persons.person_name as Nombre,
							person_persons.person_lastname as PrimerApellido,
							person_persons.person_secondlastname as SegundoApellido,
							person_persons.person_identity as CI,
							request_lodgings.lodging_entrancedate as Entrada,
							request_lodgings.lodging_exitdate as Salida,
							conf_provinces.province_name as Hospedaje,
							conf_hotels.hotel_name as Hotel,
							conf_costcenters.center_name as Centro,
							conf_motives.motive_name as Motivo,
							request_details as Detalle' );
		$this->db->from ( 'request_requests' );
		$this->db->join ( 'person_persons', 'person_persons.person_id = request_requests.person_idworker', 'inner' );
		$this->db->join ( 'conf_motives', 'conf_motives.motive_id = request_requests.motive_id', 'inner' );
		$this->db->join ( 'conf_costcenters', 'conf_costcenters.center_id = request_requests.center_id', 'inner' );
		$this->db->join ( 'request_lodgings', 'request_lodgings.request_id = request_requests.request_id', 'inner' );
		$this->db->join ( 'conf_provinces', 'conf_provinces.province_id = request_lodgings.province_idlodging', 'inner' );
		$this->db->join ( 'lodging_edit', 'lodging_edit.request_id = request_lodgings.request_id', 'inner' );
		$this->db->join ( 'conf_hotels', 'conf_hotels.hotel_id = lodging_edit.hotel_id', 'inner' );
		$this->db->where ( 'lodging_edit.lodging_noshow <> ', 'on' ); //se ve en tabla lodgin_edit, o sea el hospedaje se llevo a cabo/*se edito pero no se uso el hospedaje, que es != a no editado, podria ser que estuviera request-lodging.lodging_state en 1 pero el on en true que dice que se edito pero no se uso, y esto si le hace falta al reporte*/
		$this->db->where ( 'request_lodgings.lodging_state', 1/*editado*/ ); //realmente no hace falta porque al buscar quizas por error en lodging_edit si esta el noshow!=.on lo muestra igual, pero por si acaso
		$this->db->where('request_lodgings.lodging_canceled !=', 1/*cancelado*/);
		if (! empty ( $datosForWheres ['province_idlodging'] ))
			$this->db->where ( 'request_lodgings.province_idlodging', $datosForWheres ['province_idlodging'] );
		if (! empty ( $datosForWheres ['hotel_id'] ))
			$this->db->where ( 'lodging_edit.hotel_id', $datosForWheres ['hotel_id'] );
		if (! empty ( $datosForWheres ['motive_id'] ))
			$this->db->where ( 'request_requests.motive_id', $datosForWheres ['motive_id'] );
		if (! empty ( $datosForWheres ['center_id'] ))
			$this->db->where ( 'request_requests.center_id', $datosForWheres ['center_id'] );
		if (! empty ( $datosForWheres ['begindate'] )){
			/*if ($isPdf =='no') //viene el dateFormat de los componentes de fecha  como Y-m-d,pero sino viene standarsubmit y el componente tira d-m-Y
				$this->db->where ( 'request_lodgings.lodging_entrancedate >=', $datosForWheres ['begindate'] );
			else*/
		    	$this->db->where ( 'request_lodgings.lodging_entrancedate >=', substr($datosForWheres ['begindate'] ,0,4).'-'.substr($datosForWheres ['begindate'] ,5,2).'-'.substr($datosForWheres ['begindate'] ,8,2));}
		if (! empty ( $datosForWheres ['enddate'] )){
			/*if ($isPdf =='no') 
				$this->db->where ( 'request_lodgings.lodging_entrancedate <=', $datosForWheres ['enddate'] );
			else*/
		    	$this->db->where ( 'request_lodgings.lodging_entrancedate <=', substr($datosForWheres ['enddate'] ,0,4).'-'.substr($datosForWheres ['enddate'] ,5,2).'-'.substr($datosForWheres ['enddate'] ,8,2));
		}
		if (! empty ( $datosForWheres ['person_id'] )) {
			$this->db->where ( 'person_persons.person_id', $datosForWheres ['person_id'] );
		} else if (! empty ( $datosForWheres ['province_idworkers'] )) {
			$this->db->where ( 'person_persons.province_id', $datosForWheres ['province_idworkers'] );
		}
		if (! empty ( $datosForWheres ['person_identity'] ))
			$this->db->where ( 'person_persons.person_identity', $datosForWheres ['person_identity'] );
		$this->db->orderby ( 'lodging_edit.hotel_id', 'desc' );
		$result = $this->db->get ();		
		to_excel($result, 'hospedaje'); 
	}

}
?>