<?php

/**
 * @orm ticket_editetecsa
 */
class Lodging_edit extends Controller {
	
	function __construct() {
		parent::Controller ();
		$this->load->model ( 'lodging/lodging_edit_model', 'conn', true );
		$this->load->model ( 'request/request_lodgings_model', 'conn2', true );
		$this->load->model ( 'request/request_tickets_model', 'conn3', true );
	}
	
	function index() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'lodging/lodging_edit' );
		if ($flag) {
			
			$this->load->view ( 'sys/header_view' );
			$this->load->view ( 'lodging/lodging_edit_view' );
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
	
	function redirectErrorLetter() {
		$this->load->view ( 'sys/header_view' );
		$this->load->view ( 'letter_error_view' );
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
		$center = $this->input->post ( 'center' );
		$province = $this->input->post ( 'province' );
		$motive = $this->input->post ( 'motive' );
		$hotel = $this->input->post ( 'hotel' );
		return $this->conn2->getData ( $to, $from, $dateStart, $dateEnd, $center, $province, $motive, $hotel );
	
	}
	
	/**
	 * Funcion para Insertar
	 *
	 */
	function insert() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'lodging/lodging_edit' );
		if ($flag) {
			$result = $this->conn->insert ();
			die ( "{success : $result}" );
		} else {
			$this->redirectError ();
		}
	}
	
	function insertMulti($request_id, $hotel_id) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'lodging/lodging_edit' );
		if ($flag) {
			$result = $this->conn->insertMulti ( $request_id, $hotel_id );
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
		$flag = $centinela->accessTo ( 'lodging/lodging_edit' );
		if ($flag) {
			$this->conn->delete ( $id );
		} else {
			$this->redirectError ();
		}
	}
	
	function canceled($id) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'lodging/lodging_edit' );
		if ($flag) {
			$this->conn2->canceledState($id, 1);
			$this->conn3->cancelTicket($id);
		} else {
			$this->redirectError ();
		}
	}
	
	function updateDate() {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'lodging/lodging_edit' );
		if ($flag) {
			$request_id = $this->input->post ( 'request_id' );
			$lodging_entrancedate = $this->input->post ( 'lodging_entrancedate');
			$lodging_exitdate = $this->input->post ( 'lodging_exitdate');
			$this->conn2->updateDate($request_id, $lodging_entrancedate, $lodging_exitdate);
		} else {
			$this->redirectError ();
		}
	}
		
	function voucher($request_id) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'lodging/lodging_edit' );
		if ($flag) {
			$data = $this->conn->voucher ( $request_id );
			$count = count ( $data );
			if ($count == 0) {
				return "{success: false, errors: { reason: 'Error al generar el voucher. Por favor intente de nuevo.' }}";
			}
			$is_softball = stripos($data [0] ['details'], "ujc");
            		if (is_numeric($is_softball) ) {
                		$data = $this->conn->voucherSoftball($request_id);
            		}
			$this->conn->updateVoucher ( $request_id );
			$this->conn2->canceledState($request_id, 0);
			$this->load->library ( 'FPDF/pdf_voucher' );
			$pdf = new Pdf_voucher ( 'P', 'mm', 'Letter' );
			//$pdf->myheader = 'Voucher de Hospedados por ETECSA';
			$pdf->AddPage ();
			$pdf->SetFont ( 'Arial', 'B', 12 );
			
			$pdf->Ln ( 5 );
			$pdf->SetFillColor ( 255, 255, 255 );
			
			$date = new Dates ( );
			
			$today = substr ( $date->now (), 0, 10 );
			substr ( $today, 8, 2 ) . '/' . substr ( $today, 5, 2 ) . '/' . substr ( $today, 0, 4 ); //para devolver dia/mes/ano en vez de anno-mes-dia
			

			$str = iconv ( 'UTF-8', 'windows-1252', 'Voucher de Hospedados por ETECSA' );
			$pdf->Cell ( 80, 7, $str, '', '', '', true );
			$pdf->SetFont ( 'Arial', '', 11 );
			
			$pdf->Ln ();
			$pdf->Ln ( 1 );
			$pdf->SetFillColor ( 255, 255, 255 );
			
			$cadena = $data [0] ['chain'];
			$cafeteria = $data [0] ['cafeteria'];
			$hotel = $data [0] ['hotel'];
			$gerencia = $data [0] ['province'];
			$nombre = $data [0] ['person_name'];
			$habitacion = '______';
			$ci = $data [0] ['person_identity'];
			$credito = '_________';
			$detalles = $data [0] ['details'];
			$task = $data [0] ['task'];
			$total = $data [$count - 1] ['total'];
			
			$pdf->SetFont ( 'Arial', 'B', 8 );
			$str = iconv ( 'UTF-8', 'windows-1252', '     Cadena Hotelera:  ' . $cadena );
			$pdf->Cell ( 60, 7, $str, '', '', '', true );
			$pdf->Ln ( 5 );
			$str = iconv ( 'UTF-8', 'windows-1252', '     Hotel:                     ' . $hotel );
			$pdf->Cell ( 60, 7, $str, '', '', '', true );
			$pdf->Ln ( 5 );
			$str = iconv ( 'UTF-8', 'windows-1252', '     Gerencia:               ' . $gerencia );
			$pdf->Cell ( 95, 7, $str, '', '', '', true );
			$str = iconv ( 'UTF-8', 'windows-1252', ' Dieta en: ' . $cafeteria );
			$pdf->Cell ( 60, 7, $str, '', '', '', true );
			
			$pdf->Ln ( 5 );
			
			$str = iconv ( 'UTF-8', 'windows-1252', '     Nombre:                 ' . $nombre );
			$pdf->Cell ( 95, 7, $str, '', '', '', true );
			$str = iconv ( 'UTF-8', 'windows-1252', ' Habitacion: ' . $habitacion );
			$pdf->Cell ( 30, 7, $str, '', '', '', true );
			
			$pdf->Ln ( 5 );
			
			$str = iconv ( 'UTF-8', 'windows-1252', '     CI:                           ' . $ci );
			$pdf->Cell ( 95, 7, $str, '', '', '', true );
			$str = iconv ( 'UTF-8', 'windows-1252', ' Credito: ' . $credito );
			$pdf->Cell ( 30, 7, $str, '', '', '', true );
			//
			

			$pdf->Ln ( 12 );
			
			$pdf->Cell ( 37, 5, 'Cant.', 1, '', 'C', true );
			$pdf->Cell ( 30, 5, 'Dpto.', 1, '', 'C', true );
			$pdf->Cell ( 34, 5, 'No. Cheque', 1, '', 'C', true );
			$pdf->Cell ( 30, 5, 'Dpte.', 1, '', 'C', true );
			$pdf->Cell ( 34, 5, 'Consumo.', 1, '', 'C', true );
			$pdf->Cell ( 34, 5, 'Saldo.', 1, '', 'C', true );
			//aqui se hace el for
			$pdf->Ln ( 8 );
			//
			

			$incremento = 1;
			for($i = 0; $i < $count - 1; $i ++) {
				$date = $data [$i] ['date'];
				$importe = $data [$i] ['money'];
				if ($incremento == 8) { //maximo de dias en una hoja
					$incremento = 2; //para empezar una nueva hoja
					$pdf->AddPage ();
					//$pdf->Ln (12 );//por si el encabezado es para todos las paginas, ver si es 12 o mas
					$pdf->Ln ( 12 );
					
					$pdf->Cell ( 37, 5, 'Cant.', 1, '', 'C', true );
					$pdf->Cell ( 30, 5, 'Dpto.', 1, '', 'C', true );
					$pdf->Cell ( 34, 5, 'No. Cheque', 1, '', 'C', true );
					$pdf->Cell ( 30, 5, 'Dpte.', 1, '', 'C', true );
					$pdf->Cell ( 34, 5, 'Consumo.', 1, '', 'C', true );
					$pdf->Cell ( 34, 5, 'Saldo.', 1, '', 'C', true );
					
					$pdf->Ln ( 5 );
				
				} else { // misma hoja
					$incremento = $incremento + 1;
				} //fin else
				

				$pdf->Cell ( 37, 5, ' Importe diario ', 1, '', 'C', true );
				$pdf->Cell ( 30, 5, '  ', 1, '', 'C', true );
				$pdf->Cell ( 34, 5, '  ', 1, '', 'C', true );
				$pdf->Cell ( 30, 5, '  ', 1, '', 'C', true );
				$pdf->Cell ( 34, 5, '  ', 1, '', 'C', true );
				$pdf->Cell ( 34, 5, '  ', 1, '', 'C', true );
				$pdf->Ln ();
				$pdf->Cell ( 37, 5, '  ', 1, '', 'C', true );
				$pdf->Cell ( 30, 5, '  ', 1, '', 'C', true );
				$pdf->Cell ( 34, 5, '  ', 1, '', 'C', true );
				$pdf->Cell ( 30, 5, '  ', 1, '', 'C', true );
				$pdf->Cell ( 34, 5, '  ', 1, '', 'C', true );
				$pdf->Cell ( 34, 5, '  ', 1, '', 'C', true );
				$pdf->Ln ( 5 );
				$pdf->Cell ( 37, 5, '$ ' . self::formatDecimalMoney ($importe), 1, '', 'C', true );
				$pdf->Cell ( 30, 5, '  ', 1, '', 'C', true );
				$pdf->Cell ( 34, 5, '  ', 1, '', 'C', true );
				$pdf->Cell ( 30, 5, '  ', 1, '', 'C', true );
				$pdf->Cell ( 34, 5, '  ', 1, '', 'C', true );
				$pdf->Cell ( 34, 5, '  ', 1, '', 'C', true );
				$pdf->Ln ( 5 );
				$pdf->Cell ( 37, 5, substr ( $date, 8, 2 ) . '/' . substr ( $date, 5, 2 ) . '/' . substr ( $date, 0, 4 ), 1, '', 'C', true );
				$pdf->Cell ( 30, 5, '  ', 1, '', 'C', true );
				$pdf->Cell ( 34, 5, '  ', 1, '', 'C', true );
				$pdf->Cell ( 30, 5, '  ', 1, '', 'C', true );
				$pdf->Cell ( 34, 5, '  ', 1, '', 'C', true );
				$pdf->Cell ( 34, 5, '  ', 1, '', 'C', true );
				
				$pdf->Ln ( 1 ); //nuev para poner una celda vacia pero con borde 1 ver luego
				$pdf->Ln ( 5 );
			
			}
			//despues del for
			$pdf->Ln ( 1 );
			$str = iconv ('UTF-8', 'windows-1252', 'Total  $ ' . self::formatDecimalMoney ($total));
			$pdf->Cell ( 199, 7, $str, 0, '', 'L', true );
			
			$pdf->Ln ( 5 );
			$pdf->Cell ( 37, 7, 'Observaciones.', 1, '', 'L', true );
			$str = iconv ( 'UTF-8', 'windows-1252', $detalles );
			$pdf->Cell ( 162, 7, $str, 1, '', 'c', true );
			
			$pdf->Ln ( 5 );
			$pdf->Cell ( 37, 7, 'Tarea de Inversion.', 1, '', 'L', true );
			$str = iconv ( 'UTF-8', 'windows-1252', $task );
			$pdf->Cell ( 162, 7, $str, 1, '', 'c', true );
			$pdf->Ln ( 5 );
			
			$str = iconv ( 'UTF-8', 'windows-1252', 'Firma del Huesped' );
			$pdf->Cell ( 37, 4, $str, 1, '', 'c', true );
			$pdf->SetFont ( 'Arial', 'B', 12 );
			$str = iconv ( 'UTF-8', 'windows-1252', 'ENTREGAR VOUCHER  EN CARPETA ' );
			$pdf->Cell ( 162, 9, $str, 1, '', 'C', true );
			
			$pdf->Ln ( 4 ); //area de firma
			$pdf->Cell ( 37, 5, '', 1, '', 'c', true );
			
			
			/*$pdf->Ln ( 4 ); //area de firma
		    $this->SetFont('Arial','I',7);
		    //Print centered page number
			$str = iconv ( 'UTF-8', 'windows-1252',  ' En caso de dificultades con el voucher, por favor, nos complaceria atenderles por el telefono 642-5690 ');
			$this->Cell(0,10,'  '.$str,'','','',true);*/			
			
			$pdf->Output ( 'Voucher'.$nombre.'-'.$today.'.pdf', 'D' );
		} else {
			$this->redirectError ();
		}
	
	}

	public function formatDecimalMoney ($money)
    	{
        	$is_decimal = stripos ($money, ".");
        	if (is_numeric ($is_decimal)) {
            		return $money . "0";
        	} else {
            		return $money . ".00";
        	}
    	}
	
	function hotel_letter($id) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'lodging/lodging_edit' );
		if ($flag) {
			$data = $this->conn->hotel_letter ( $id );
			$count = count ( $data );
			if ($count == 0) {
				echo "{success: false, errors: { reason: 'Error al introducir la carta. Por favor intente de nuevo' }}";
			}
			$this->load->library ( 'FPDF/pdf_letter' );
			$pdf = new Pdf_letter ( 'P', 'mm', 'Letter' );
			
			$hotel = $data [0] ['hotel'];
			$cafeteria = $data [0] ['cafeteria'];
			$letter = $data [0] ['letter_id'];
			$startdate = $data [0] ['lodging_entrancedate'];
			$enddate = $data [0] ['lodging_exitdate'];
			
			$pdf->myheader = 'A gerente del hotel: ' . $hotel;
			$pdf->AddPage ();
			$pdf->SetFont ( 'Arial', '', 12 );
			$dates = new Dates ( );
			$now = '';//$dates->now ();
			$pdf->Cell ( 40, 10, 'Carta-' . $letter . '   ' . substr ( $now, 0, 10 ) );
			
			$pdf->Ln ( 6 );
			$pdf->SetFont ( 'Arial', '', 10 );
			$texto1 = 'Por este medio le estamos solicitando hospedaje para funcionarios de nuestra empresa que por razones de trabajo ';
			$pdf->Cell ( 40, 10, $texto1 );
			$pdf->Ln ( 6 );
			
			$texto1 = 'visitarán esta ciudad desde el día ' . $startdate . ' hasta el día ' . $enddate . '. Los datos de interés relacionados son: ';
			$str = iconv ( 'UTF-8', 'windows-1252', $texto1 );
			$pdf->Cell ( 40, 10, $str );
			$pdf->SetFont ( 'Arial', 'U', 8 );
			//
			$pdf->SetFillColor ( 255, 255, 255 );
			$pdf->Ln ( 12 );
			
			$pdf->SetTextColor ( 0, 0, 0 );
			$pdf->SetFont ( 'Arial', '', 8 );
			$str = iconv ( 'UTF-8', 'windows-1252', '        No' );
			$pdf->Cell ( 15, 7, $str, '', '', '', true );
			$pdf->SetFont ( 'Arial', 'U', 8 );
			$str = iconv ( 'UTF-8', 'windows-1252', 'Carnet Id' );
			$pdf->Cell ( 20, 7, $str, '', '', '', true );
			$str = iconv ( 'UTF-8', 'windows-1252', 'Nombre y Apellidos' );
			
			$pdf->Cell ( 60, 7, $str, '', '', '', true );
			$str = iconv ( 'UTF-8', 'windows-1252', 'Provincia' );
			$pdf->Cell ( 50, 7, $str, '', '', '', true );
			
			$flag = true;
			$pdf->SetFont ( 'Arial', '', 8 );
			for($i = 0; $i < $count; $i ++) {
				
				$pdf->Ln ( 5 );
				
				if ($flag == false) {
					$flag = true;
					$pdf->SetTextColor ( 0, 0, 0 );
				} else {
					$flag = false;
					$pdf->SetTextColor ( 21, 16, 59 );
				}
				
				$str = iconv ( 'UTF-8', 'windows-1252', '        ' . ($i + 1) );
				$pdf->Cell ( 15, 7, $str, '', '', '', true );
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_identity'] );
				$pdf->Cell ( 20, 7, $str, '', '', '', true );
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_name'] );
				$pdf->Cell ( 60, 7, $str, '', '', '', true );
				$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_province'] );
				$pdf->Cell ( 50, 7, $str, '', '', '', true );
			
			} //fin del for
			

			$pdf->SetTextColor ( 0, 0, 0 );
			//
			$pdf->Ln ( 10 );
			$pdf->SetFont ( 'Arial', '', 10 );
			$texto2 = 'El pago por concepto de hospedajes será de acuerdo con las cifras del hotel';
			$str = iconv ( 'UTF-8', 'windows-1252', $texto2 );
			$pdf->Cell ( 40, 10, $str );
			
			$pdf->Ln ( 10 );
			$texto2 = 'Agradecimientos anticipados por su atención.';
			$str = iconv ( 'UTF-8', 'windows-1252', $texto2 );
			$pdf->Cell ( 40, 10, $str );
			
			$pdf->Ln ( 15 );
			$firma = '_________________';
			$pdf->Cell ( 40, 10, $firma );
			
			$pdf->Ln ( 7 );
			$firma = 'Firma Autorizada';
			$pdf->Cell ( 40, 10, $firma );
			
			if (!empty($cafeteria)) {
				$pdf->myheader = 'A gerente de la cafeteria: ' . $cafeteria;
				$pdf->AddPage ();
				$pdf->SetFont ( 'Arial', '', 12 );
				$pdf->Cell ( 40, 10, 'Carta-' . $letter );
				
				$pdf->Ln ( 6 );
				$pdf->SetFont ( 'Arial', '', 10 );
				$texto1 = 'Por este medio le estamos solicitando consumo de dieta para funcionarios de nuestra empresa que por razones de trabajo ';
				$pdf->Cell ( 40, 10, $texto1 );
				$pdf->Ln ( 6 );
				
				$texto1 = 'visitarán esta ciudad desde el día ' . $startdate . ' hasta el día ' . $enddate . '. Los datos de interés relacionados son: ';
				$str = iconv ( 'UTF-8', 'windows-1252', $texto1 );
				$pdf->Cell ( 40, 10, $str );
				$pdf->SetFont ( 'Arial', 'U', 8 );
				//
				$pdf->SetFillColor ( 255, 255, 255 );
				$pdf->Ln ( 12 );
				
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->SetFont ( 'Arial', '', 8 );
				$str = iconv ( 'UTF-8', 'windows-1252', '        No' );
				$pdf->Cell ( 15, 7, $str, '', '', '', true );
				$pdf->SetFont ( 'Arial', 'U', 8 );
				$str = iconv ( 'UTF-8', 'windows-1252', 'Carnet Id' );
				$pdf->Cell ( 20, 7, $str, '', '', '', true );
				$str = iconv ( 'UTF-8', 'windows-1252', 'Nombre y Apellidos' );
				
				$pdf->Cell ( 60, 7, $str, '', '', '', true );
				$str = iconv ( 'UTF-8', 'windows-1252', 'Provincia' );
				$pdf->Cell ( 50, 7, $str, '', '', '', true );
				
				$flag = true;
				$pdf->SetFont ( 'Arial', '', 8 );
				for($i = 0; $i < $count; $i ++) {
					
					$pdf->Ln ( 5 );
					
					if ($flag == false) {
						$flag = true;
						$pdf->SetTextColor ( 0, 0, 0 );
					} else {
						$flag = false;
						$pdf->SetTextColor ( 21, 16, 59 );
					}
					
					$str = iconv ( 'UTF-8', 'windows-1252', '        ' . ($i + 1) );
					$pdf->Cell ( 15, 7, $str, '', '', '', true );
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_identity'] );
					$pdf->Cell ( 20, 7, $str, '', '', '', true );
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_name'] );
					$pdf->Cell ( 60, 7, $str, '', '', '', true );
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_province'] );
					$pdf->Cell ( 50, 7, $str, '', '', '', true );
				
				} //fin del for
				
	
				$pdf->SetTextColor ( 0, 0, 0 );
				//
				$pdf->Ln ( 10 );
				$pdf->SetFont ( 'Arial', '', 10 );
				
				$pdf->Ln ( 10 );
				$texto2 = 'Agradecimientos anticipados por su atención.';
				$str = iconv ( 'UTF-8', 'windows-1252', $texto2 );
				$pdf->Cell ( 40, 10, $str );
				
				$pdf->Ln ( 15 );
				$firma = '_________________';
				$pdf->Cell ( 40, 10, $firma );
				
				$pdf->Ln ( 7 );
				$firma = 'Firma Autorizada';
				$pdf->Cell ( 40, 10, $firma );
			}
			
			$pdf->Output ( 'Carta-'.$letter.'.pdf', 'D' );
		
		} else {
			$this->redirectError ();
		}
		
	//fin del PDF
	

	}
	
	function getByLetter($letter_id) {
		$centinela = new Centinela ( );
		$flag = $centinela->accessTo ( 'lodging/lodging_edit' );
		if ($flag) {
			$count = $this->conn->getCountByLetterId ( $letter_id );
			if ($count == 0) {
				$this->redirectErrorLetter ();
			} else {
				//ini pdf
				$data = $this->conn->getByLetter ( $letter_id );
				$this->load->library ( 'FPDF/pdf_letter' );
				$pdf = new Pdf_letter ( 'P', 'mm', 'Letter' );
				
				$hotel = $data [0] ['hotel'];
				$cafeteria = $data [0] ['cafeteria'];
				$letter = $data [0] ['letter_id'];
				$startdate = $data [0] ['lodging_entrancedate'];
				$enddate = $data [0] ['lodging_exitdate'];
				
				$pdf->myheader = 'A gerente del hotel: ' . $hotel;
				$pdf->AddPage ();
				$pdf->SetFont ( 'Arial', '', 12 );
				$dates = new Dates ( );
				$now = $dates->now ();
				$pdf->Cell ( 40, 10, 'Carta-' . $letter . '   ' . substr ( $now, 0, 10 ) );
				
				$pdf->Ln ( 5 );
				$pdf->SetFont ( 'Arial', '', 10 );
				$texto1 = 'Por este medio le estamos solicitando hospedaje para funcionarios de nuestra empresa que por razones de trabajo ';
				$pdf->Cell ( 40, 10, $texto1 );
				$pdf->Ln ( 6 );
				$texto1 = 'visitarán esta ciudad desde el dia ' . $startdate . ' hasta el dia ' . $enddate . ' . Los datos de interés relacionados son: ';
				$str = iconv ( 'UTF-8', 'windows-1252', $texto1 );
				$pdf->Cell ( 40, 10, $str );
				
				//
				$pdf->SetFillColor ( 255, 255, 255 );
				$pdf->Ln ( 12 );
				
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->SetFont ( 'Arial', '', 8 );
				$str = iconv ( 'UTF-8', 'windows-1252', '        No' );
				$pdf->Cell ( 15, 7, $str, '', '', '', true );
				$pdf->SetFont ( 'Arial', 'U', 8 );
				$str = iconv ( 'UTF-8', 'windows-1252', 'Carnet Id' );
				$pdf->Cell ( 20, 7, $str, '', '', '', true );
				$str = iconv ( 'UTF-8', 'windows-1252', 'Nombre y Apellidos' );
				
				$pdf->Cell ( 60, 7, $str, '', '', '', true );
				$str = iconv ( 'UTF-8', 'windows-1252', 'Provincia' );
				$pdf->Cell ( 50, 7, $str, '', '', '', true );
				
				$flag = true;
				$pdf->SetFont ( 'Arial', '', 8 );
				for($i = 0; $i < $count; $i ++) {
					
					$pdf->Ln ( 5 );
					
					if ($flag == false) {
						$flag = true;
						$pdf->SetTextColor ( 0, 0, 0 );
					} else {
						$flag = false;
						$pdf->SetTextColor ( 21, 16, 59 );
					}
					
					$str = iconv ( 'UTF-8', 'windows-1252', '        ' . ($i + 1) );
					$pdf->Cell ( 15, 7, $str, '', '', '', true );
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_identity'] );
					$pdf->Cell ( 20, 7, $str, '', '', '', true );
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_name'] );
					$pdf->Cell ( 60, 7, $str, '', '', '', true );
					
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_province'] );
					$pdf->Cell ( 50, 7, $str, '', '', '', true );
				
				} //fin del for
				

				$pdf->Ln ( 10 );
				$pdf->SetFont ( 'Arial', '', 10 );
				
				$pdf->Ln ( 10 );
				$texto2 = 'Agradecimientos anticipados por su atención.';
				$str = iconv ( 'UTF-8', 'windows-1252', $texto2 );
				$pdf->Cell ( 40, 10, $str );
				
				$pdf->Ln ( 15 );
				$firma = '_________________';
				$pdf->Cell ( 40, 10, $firma );
				
				$pdf->Ln ( 7 );
				$firma = 'Firma Autorizada';
				$pdf->Cell ( 40, 10, $firma );
				
			if (!empty($cafeteria)) {
				$pdf->myheader = 'A gerente de la cafeteria: ' . $cafeteria;
				$pdf->AddPage ();
				$pdf->SetFont ( 'Arial', '', 12 );
				$pdf->Cell ( 40, 10, 'Carta-' . $letter );
				
				$pdf->Ln ( 6 );
				$pdf->SetFont ( 'Arial', '', 10 );
				$texto1 = 'Por este medio le estamos solicitando consumo de dieta para funcionarios de nuestra empresa que por razones de trabajo ';
				$pdf->Cell ( 40, 10, $texto1 );
				$pdf->Ln ( 6 );
				
				$texto1 = 'visitarán esta ciudad desde el día ' . $startdate . ' hasta el día ' . $enddate . '. Los datos de interés relacionados son: ';
				$str = iconv ( 'UTF-8', 'windows-1252', $texto1 );
				$pdf->Cell ( 40, 10, $str );
				$pdf->SetFont ( 'Arial', 'U', 8 );
				//
				$pdf->SetFillColor ( 255, 255, 255 );
				$pdf->Ln ( 12 );
				
				$pdf->SetTextColor ( 0, 0, 0 );
				$pdf->SetFont ( 'Arial', '', 8 );
				$str = iconv ( 'UTF-8', 'windows-1252', '        No' );
				$pdf->Cell ( 15, 7, $str, '', '', '', true );
				$pdf->SetFont ( 'Arial', 'U', 8 );
				$str = iconv ( 'UTF-8', 'windows-1252', 'Carnet Id' );
				$pdf->Cell ( 20, 7, $str, '', '', '', true );
				$str = iconv ( 'UTF-8', 'windows-1252', 'Nombre y Apellidos' );
				
				$pdf->Cell ( 60, 7, $str, '', '', '', true );
				$str = iconv ( 'UTF-8', 'windows-1252', 'Provincia' );
				$pdf->Cell ( 50, 7, $str, '', '', '', true );
				
				$flag = true;
				$pdf->SetFont ( 'Arial', '', 8 );
				for($i = 0; $i < $count; $i ++) {
					
					$pdf->Ln ( 5 );
					
					if ($flag == false) {
						$flag = true;
						$pdf->SetTextColor ( 0, 0, 0 );
					} else {
						$flag = false;
						$pdf->SetTextColor ( 21, 16, 59 );
					}
					
					$str = iconv ( 'UTF-8', 'windows-1252', '        ' . ($i + 1) );
					$pdf->Cell ( 15, 7, $str, '', '', '', true );
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_identity'] );
					$pdf->Cell ( 20, 7, $str, '', '', '', true );
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_name'] );
					$pdf->Cell ( 60, 7, $str, '', '', '', true );
					$str = iconv ( 'UTF-8', 'windows-1252', $data [$i] ['person_province'] );
					$pdf->Cell ( 50, 7, $str, '', '', '', true );
				
				} //fin del for
				
	
				$pdf->SetTextColor ( 0, 0, 0 );
				//
				$pdf->Ln ( 10 );
				$pdf->SetFont ( 'Arial', '', 10 );
				
				$pdf->Ln ( 10 );
				$texto2 = 'Agradecimientos anticipados por su atención.';
				$str = iconv ( 'UTF-8', 'windows-1252', $texto2 );
				$pdf->Cell ( 40, 10, $str );
				
				$pdf->Ln ( 15 );
				$firma = '_________________';
				$pdf->Cell ( 40, 10, $firma );
				
				$pdf->Ln ( 7 );
				$firma = 'Firma Autorizada';
				$pdf->Cell ( 40, 10, $firma );
			}				
				
				$pdf->Output ( 'Carta-'.$letter.'.pdf', 'D' );
			}
			//fin del PDF
		

		} else {
			$this->redirectError ();
		}
	}
	
	/**
	 * Devuelve un objeto dado el nombre
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

}

?>
