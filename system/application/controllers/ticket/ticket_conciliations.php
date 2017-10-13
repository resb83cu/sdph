<?php

/**
 * @orm ticket_editetecsa
 */
class Ticket_conciliations extends Controller {

    function __construct() {
        parent::Controller();
        $this->load->model('ticket/ticket_conciliations_model', 'conn', true);
    }

    function index() {
        $centinela = new Centinela ( );
        $flag = $centinela->accessTo('ticket/ticket_conciliations');
        if ($flag) {
            $this->load->view('sys/header_view');
            $this->load->view('ticket/ticket_conciliations_view');
            $this->load->view('sys/footer_view');
        } else {
            $this->redirectError();
        }
    }

    function redirectError() {
        $this->load->view('sys/header_view');
        $this->load->view('error_message');
        $this->load->view('sys/footer_view');
    }

    public function accounting() {
        if ($this->session->userdata('roll_id') >= 4) {
            $this->load->view('sys/header_view');
            $this->load->view('ticket/ticket_accounting_view');
            $this->load->view('sys/footer_view');
        } else {
            $this->redirectError();
        }
    }

    /**
     * Busca los datos para llenar el grid y los devuelve en formato JSON
     *
     */
    public function setDataGrid() {
        $dateStart = $this->input->post('dateStart');
        $dateEnd = $this->input->post('dateEnd');
        $transport = $this->input->post('transport');
        $voucher = $this->input->post('voucher');
        return $this->conn->getData($dateStart, $dateEnd, $transport, $voucher);
    }

    public function setDataAccounting() {
        $to = (!isset($_POST ['limit'])) ? 15 : $_POST ['limit'];
        $from = (!isset($_POST ["start"])) ? 0 : $_POST ["start"];
        $dateStart = $this->input->post('dateStart');
        $dateEnd = $this->input->post('dateEnd');
        $transport = $this->input->post('transport');
        $center = $this->input->post('center');
        $motive = $this->input->post('motive');

        return $this->conn->getDataAccounting($to, $from, $dateStart, $dateEnd, $center, $transport, $motive);
    }

    public function setDataCociliation($show = false, $isPDF = 'no') {
        if ($this->session->userdata('roll_id') >= 4) {
            if ($show == false && $isPDF == 'no') {
                $this->load->view('sys/header_view');
                $this->load->view('ticket/ticket_reportconciliations_view');
                $this->load->view('sys/footer_view');
            }

            if ($show == true && $isPDF == 'no') {
                $datosForWheres = array();
                /* $datosForWheres ['center'] = $this->input->post('center');
                  $datosForWheres ['province'] = $this->input->post('province');
                  $datosForWheres ['motive'] = $this->input->post('motive'); */
                $datosForWheres ['bill'] = $this->input->post('bill');
                die($this->conn->getDataCociliation($datosForWheres ['bill']/* , $datosForWheres ['province'], $datosForWheres ['center'], $datosForWheres ['motive'] */, $show, $isPDF));
            }

            if ($show == true && $isPDF == 'si') {
                $datosForWheres = array();
                /* $datosForWheres ['center'] = $this->input->post('center');
                  $datosForWheres ['province'] = $this->input->post('province');
                  $datosForWheres ['motive'] = $this->input->post('motive'); */
                $datosForWheres ['bill'] = $this->input->post('bill');
                $data = $this->conn->getDataCociliation($datosForWheres ['bill']/* , $datosForWheres ['province'], $datosForWheres ['center'], $datosForWheres ['motive'] */, $show, $isPDF);
                $cant = count($data);

                $this->load->library('FPDF/pdf_conciliation_lodging_report');
                $pdf = new Pdf_conciliation_lodging_report('L', 'mm', 'A4');
                $pdf->AddPage();

                $pdf->Ln(1);

                $pdf->SetFont('Arial', '', 8);
                $flag = true;

                for ($i = 0; $i < $cant; $i++) {
                    if ($flag == false) {
                        $flag = true;
                        $pdf->SetFillColor(255, 255, 255);
                    } else {
                        $flag = false;
                        $pdf->SetFillColor(200, 215, 235);
                    }
                    $r = fmod($i + 1, 10);

                    if ($r == 0) {
                        $pdf->AddPage();
                    }

                    $str = iconv('UTF-8', 'windows-1252', $data [$i] ['bill_number']);
                    $pdf->Cell(15, 5, $str, '', '', '', true);

                    $str = iconv('UTF-8', 'windows-1252', $data [$i] ['person_identity']);
                    $pdf->Cell(20, 5, $str, '', '', '', true);

                    $str = iconv('UTF-8', 'windows-1252', $data [$i] ['person_worker']);
                    $pdf->Cell(60, 5, $str, '', '', '', true);

                    $str = iconv('UTF-8', 'windows-1252', $data [$i] ['hotel_name']);
                    $pdf->Cell(40, 5, $str, '', '', '', true);

                    $str = iconv('UTF-8', 'windows-1252', $data [$i] ['lodging_entrancedate']);
                    $pdf->Cell(23, 5, $str, '', '', '', true);

                    $str = iconv('UTF-8', 'windows-1252', $data [$i] ['lodging_exitdate']);
                    $pdf->Cell(23, 5, $str, '', '', '', true);

                    $str = iconv('UTF-8', 'windows-1252', $data [$i] ['diet']);
                    $pdf->Cell(20, 5, $str, '', '', 'L', true);

                    $str = iconv('UTF-8', 'windows-1252', $data [$i] ['lodging']);
                    $pdf->Cell(20, 5, $str, '', '', 'R', true);

                    $str = iconv('UTF-8', 'windows-1252', $data [$i] ['total']);
                    $pdf->Cell(42, 5, $str, '', '', 'R', true);
                    $pdf->Ln();
                    if ($i < $cant - 1) {
                        $pdf->Cell(20, 5, 'Centro Costo:', 0, '', 'L', true);
                        $str = iconv('UTF-8', 'windows-1252', $data [$i] ['center_name']);
                        $pdf->Cell(40, 5, $str, '', '', 'L', true);
                        $pdf->Cell(15, 5, 'Autoriza:', 0, '', 'L', true);
                        $str = iconv('UTF-8', 'windows-1252', $data [$i] ['person_licensedby']);
                        $pdf->Cell(95, 5, $str, '', '', 'L', true);
                        $pdf->Cell(25, 5, 'Tarea Inversion:', 0, '', 'L', true);
                        $str = iconv('UTF-8', 'windows-1252', $data [$i] ['request_inversiontask']);
                        $pdf->Cell(68, 5, $str, '', '', 'L', true);
                        $pdf->Ln(5);
                        $pdf->Cell(15, 5, 'Detalles:', 0, '', 'L', true);
                        $str = iconv('UTF-8', 'windows-1252', ucfirst($data [$i] ['request_details']));
                        $pdf->Cell(180, 5, $str, '', '', 'L', true);
                        $pdf->Cell(20, 5, 'Prov. Hosp:', 0, '', 'L', true);
                        $str = iconv('UTF-8', 'windows-1252', $data [$i] ['province_lodging']);
                        $pdf->Cell(48, 5, $str, '', '', '', true);
                        $pdf->Ln(5);
                    }
                }
                $pdf->Output('Reporte Conciliacion Hospedajes-' . $datosForWheres ['dateStart'] . '-' . $datosForWheres ['dateEnd'] . '.pdf', 'D');
            }
        } else {
            $this->redirectError();
        }
    }

    function to_excelAll() {
        $this->load->plugin('to_excel');
        $datosForWheres = array();
        /* $datosForWheres ['center'] = $this->input->post('center');
          $datosForWheres ['province'] = $this->input->post('province');
          $datosForWheres ['motive'] = $this->input->post('motive'); */
        $bill = $this->input->post('filter_bill');
        $result = $this->conn->getDataCociliationToExcel($bill/* , $datosForWheres ['province'], $datosForWheres ['center'], $datosForWheres ['motive'] */, true, 'si');
        to_excel($result, 'Reporte_de_conciliacion_pasaje_viazul');
    }

    /**
     * Funcion para Insertar Provincia
     *
     */
    function insert($request_id, $bill_number, $ticket_date) {
        $centinela = new Centinela ( );
        $flag = $centinela->accessTo('ticket/ticket_conciliations');
        if ($flag) {
            $result = $this->conn->insert($request_id, $bill_number, $ticket_date);
            die("{success : $result}");
        } else {
            $this->redirectError();
        }
    }

    /**
     * Elimina Transporte
     * recibe como parametro el nombre de la provincia
     */
    function delete($id) {
        $centinela = new Centinela ( );
        $flag = $centinela->accessTo('ticket/ticket_conciliations');
        if ($flag) {
            $this->conn->delete($id);
        } else {
            $this->redirectError();
        }
    }

    /**
     * Devuelve una provincia dado el nombre de la misma
     *
     */
    function getIds() {
        $data = $this->conn->getIds();
        die("{data : " . json_encode($data) . "}");
    }

    function getById($request_id) {
        $data = $this->conn->getById($request_id);
        die("{data : " . json_encode($data) . "}");
        //echo "<pre>"; print_r($data); echo "</pre>";
    }

}

?>
