<?php

/**
 * @orm ticket_editetecsa
 */
class Ticket_editviazul extends Controller {

    function __construct() {
        parent::Controller();
        $this->load->model('ticket/ticket_editviazul_model', 'conn', true);
    }

    function index() {
        $centinela = new Centinela ( );
        $flag = $centinela->accessTo('ticket/ticket_editviazul');
        if ($flag) {
            $this->load->view('sys/header_view');
            $this->load->view('ticket/ticket_editviazul_view');
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

    /**
     * Busca los datos para llenar el grid y los devuelve en formato JSON
     *
     */
    public function setDataGrid() {
        $to = (!isset($_POST ['limit'])) ? 15 : $_POST ['limit'];
        $from = (!isset($_POST ["start"])) ? 0 : $_POST ["start"];
        $dateStart = $this->input->post('dateStart');
        $dateEnd = $this->input->post('dateEnd');
        $motive = $this->input->post('motive');
        $this->load->model('request/request_tickets_model', 'conn2', true);
        return $this->conn2->getDataViazul($to, $from, $dateStart, $dateEnd, $motive);
    }

    /**
     * Funcion para Insertar Provincia
     *
     */
    function insert() {
        $centinela = new Centinela ( );
        $flag = $centinela->accessTo('ticket/ticket_editviazul');
        if ($flag) {
            $result = $this->conn->insert();
            die("{success : $result}");
        } else {
            $this->redirectError();
        }
    }

    /**
     * Funcion para Modificar fecha solicitud viazul
     *
     */
    function changeDate() {
        $centinela = new Centinela ( );
        $flag = $centinela->accessTo('ticket/ticket_editviazul');
        if ($flag) {
            $result = $this->conn->changeDate();
            die("{success : $result}");
        } else {
            $this->redirectError();
        }
    }

    /**
     * Elimina Transporte
     * recibe como parametro el nombre de la provincia
     */
    function delete($id, $date) {
        $centinela = new Centinela ( );
        $flag = $centinela->accessTo('ticket/ticket_editviazul');
        if ($flag) {
            $this->conn->delete($id, $date);
        } else {
            $this->redirectError();
        }
    }

    /**
     * Devuelve una provincia dado el nombre de la misma
     *
     */
    function get() {
        $data = $this->conn->get();
        die("{data : " . json_encode($data) . "}");
    }

    function getById($request_id, $ticket_date) {
        $data = $this->conn->getById($request_id, $ticket_date);
        die("{data : " . json_encode($data) . "}");
        //echo "<pre>"; print_r($data); echo "</pre>";
    }

    function viazulPdfMultiple($requests) {
        $centinela = new Centinela ( );
        $results = explode(",", $requests);
        $this->load->library('FPDF/pdf_request');
        $pdf = new Pdf_request('L', 'mm', 'Letter');

        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);

        $pdf->Ln(5);
        $pdf->SetFillColor(255, 255, 255);

        $str = iconv('UTF-8', 'windows-1252', 'DATOS DEL PERSONAL AUTORIZADO A VIAJAR');
        $pdf->Cell(80, 7, $str, '', '', '', true);
        $pdf->SetFont('Arial', '', 12);

        $pdf->Ln();
        $pdf->Ln(20);
        $pdf->SetFillColor(255, 255, 255);

        $pdf->SetFont('Arial', '', 10);

        $pdf->Cell(60, 7, 'Nombre y Apellidos', 1, '', 'C', true);
        $pdf->Cell(25, 7, 'C.I', 1, '', 'C', true);
        $pdf->Cell(38, 7, 'Provincia', 1, '', 'C', true);
        $pdf->Cell(20, 7, 'Fecha Viaje', 1, '', 'C', true);
        $pdf->Cell(30, 7, 'Origen', 1, '', 'C', true);
        $pdf->Cell(30, 7, 'Destino.', 1, '', 'C', true);
        $pdf->Cell(40, 7, 'Presupuesto.', 1, '', 'C', true);
        $pdf->Cell(20, 7, 'Importe', 1, '', 'C', true);

        $pdf->Ln(7);
        $total = 0;
        foreach ($results as $row) {
            $item = explode("|", $row);
            $this->viazulPdfRow($item[0], $item[1], $pdf, $total);
        }

        $pdf->Cell(203, 7, '', 0, '', 'C', true);
        $pdf->Cell(40, 7, 'Importe Total', 1, '', 'L', true);
        $pdf->Cell(20, 7, number_format($total,2,",","."), 1, '', 'R', true);

        $pdf->Output('Viazul - Datos del personal autorizado a viajar -'.$centinela->get_person_fullname().' - '.$centinela->today().'.pdf', 'D');

    }

    function viazulPdfRow($request_id, $ticket_date, $pdf, &$total) {
        $data = $this->conn->getById($request_id, $ticket_date);
        $count = count($data);
        if ($count == 0) {
            return "{success: false, errors: { reason: 'Error al generar la solicitud. Por favor intente de nuevo.' }}";
        }


        $request_date = $data [0] ['request_date'];
        $ticket_date = $data [0] ['ticket_date'];
        $person_namerequestedby = $data [0] ['person_namerequestedby'];
        $person_namelicensedby = $data [0] ['person_namelicensedby'];
        $center_name = $data [0] ['center_name'];
        $transport_name = $data [0] ['transport_name'];
        $person_nameworker = $data [0] ['person_nameworker'];
        $person_identity = $data [0] ['person_identity'];
        $person_province = $data [0] ['person_province'];
        $province_namefrom = $data [0] ['province_namefrom'];
        $province_nameto = $data [0] ['province_nameto'];
        $motive_name = $data [0] ['motive_name'];
        $request_details = $data [0] ['request_details'];
        $viazul_price = $data [0] ['viazul_price'];

//        $str = iconv('UTF-8', 'windows-1252', 'Persona que Autoriza: ' . $person_namelicensedby);
//        $pdf->Cell(120, 7, $str, '', '', '', true);
//        $pdf->Ln(12);
//        $str = iconv('UTF-8', 'windows-1252', 'Persona que introdujo la solicitud: ' . $person_namerequestedby);
//        $pdf->Cell(120, 7, $str, '', '', '', true);
//        $pdf->Ln(12);
//        $str = iconv('UTF-8', 'windows-1252', 'Fecha de solicitud: ' . $request_date);
//        $pdf->Cell(120, 7, $str, '', '', '', true);
//        $pdf->Ln(12);
//        $str = iconv('UTF-8', 'windows-1252', 'Fecha de viaje: ' . $ticket_date);
//        $pdf->Cell(120, 7, $str, '', '', '', true);
//        $pdf->Ln(20);

        //aqui se hace el for

        $str = iconv('UTF-8', 'windows-1252', $person_nameworker);
        $pdf->Cell(60, 7, $str, 1, '', 'L', true);
        $str = iconv('UTF-8', 'windows-1252', $person_identity);
        $pdf->Cell(25, 7, $str, 1, '', 'L', true);
        $str = iconv('UTF-8', 'windows-1252', $person_province);
        $pdf->Cell(38, 7, $str, 1, '', 'L', true);
        $str = iconv('UTF-8', 'windows-1252', $ticket_date);
        $pdf->Cell(20, 7, $str, 1, '', 'L', true);
        $str = iconv('UTF-8', 'windows-1252', $province_namefrom);
        $pdf->Cell(30, 7, $str, 1, '', 'L', true);
        $str = iconv('UTF-8', 'windows-1252', $province_nameto);
        $pdf->Cell(30, 7, $str, 1, '', 'L', true);
        $str = iconv('UTF-8', 'windows-1252', $center_name);
        $pdf->Cell(40, 7, $str, 1, '', 'L', true);
        $str = iconv('UTF-8', 'windows-1252', number_format($viazul_price,2,",","."));
        $pdf->Cell(20, 7, $str, 1, '', 'R', true);

        $pdf->Ln(7);

        $total += $viazul_price;

    }

    function viazulPdf($request_id, $ticket_date) {
        $centinela = new Centinela ( );
        $flag = $centinela->accessTo('ticket/ticket_editviazul');
        if ($flag) {
            $data = $this->conn->getById($request_id, $ticket_date);
            $count = count($data);
            if ($count == 0) {
                return "{success: false, errors: { reason: 'Error al generar la solicitud. Por favor intente de nuevo.' }}";
            }
            $this->load->library('FPDF/pdf_request');
            $pdf = new Pdf_request('L', 'mm', 'Letter');

            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 12);

            $pdf->Ln(5);
            $pdf->SetFillColor(255, 255, 255);

            $str = iconv('UTF-8', 'windows-1252', 'SOLICITUD DE PASAJES Y HOSPEDAJES');
            $pdf->Cell(80, 7, $str, '', '', '', true);
            $pdf->SetFont('Arial', '', 12);

            $pdf->Ln();
            $pdf->Ln(20);
            $pdf->SetFillColor(255, 255, 255);

            $request_date = $data [0] ['request_date'];
            $ticket_date = $data [0] ['ticket_date'];
            $person_namerequestedby = $data [0] ['person_namerequestedby'];
            $person_namelicensedby = $data [0] ['person_namelicensedby'];
            $center_name = $data [0] ['center_name'];
            $transport_name = $data [0] ['transport_name'];
            $person_nameworker = $data [0] ['person_nameworker'];
            $person_identity = $data [0] ['person_identity'];
            $person_province = $data [0] ['person_province'];
            $province_namefrom = $data [0] ['province_namefrom'];
            $province_nameto = $data [0] ['province_nameto'];
            $motive_name = $data [0] ['motive_name'];
            $request_details = $data [0] ['request_details'];

            $str = iconv('UTF-8', 'windows-1252', 'Persona que Autoriza: ' . $person_namelicensedby);
            $pdf->Cell(120, 7, $str, '', '', '', true);
            $pdf->Ln(12);
            $str = iconv('UTF-8', 'windows-1252', 'Persona que introdujo la solicitud: ' . $person_namerequestedby);
            $pdf->Cell(120, 7, $str, '', '', '', true);
            $pdf->Ln(12);
            $str = iconv('UTF-8', 'windows-1252', 'Fecha de solicitud: ' . $request_date);
            $pdf->Cell(120, 7, $str, '', '', '', true);
            $pdf->Ln(12);
            $str = iconv('UTF-8', 'windows-1252', 'Fecha de viaje: ' . $ticket_date);
            $pdf->Cell(120, 7, $str, '', '', '', true);
            $pdf->Ln(20);

            $pdf->SetFont('Arial', '', 10);

            $pdf->Cell(60, 7, 'Nombre y Apellidos', 1, '', 'C', true);
            $pdf->Cell(35, 7, 'C.Identidad', 1, '', 'C', true);
            $pdf->Cell(48, 7, 'Provincia', 1, '', 'C', true);
            $pdf->Cell(40, 7, 'Origen', 1, '', 'C', true);
            $pdf->Cell(40, 7, 'Destino.', 1, '', 'C', true);
            $pdf->Cell(38, 7, 'Transportacion.', 1, '', 'C', true);
            //aqui se hace el for
            $pdf->Ln(7);
            $str = iconv('UTF-8', 'windows-1252', $person_nameworker);
            $pdf->Cell(60, 7, $str, 1, '', 'C', true);
            $str = iconv('UTF-8', 'windows-1252', $person_identity);
            $pdf->Cell(35, 7, $str, 1, '', 'C', true);
            $str = iconv('UTF-8', 'windows-1252', $person_province);
            $pdf->Cell(48, 7, $str, 1, '', 'C', true);
            $str = iconv('UTF-8', 'windows-1252', $province_namefrom);
            $pdf->Cell(40, 7, $str, 1, '', 'C', true);
            $str = iconv('UTF-8', 'windows-1252', $province_nameto);
            $pdf->Cell(40, 7, $str, 1, '', 'C', true);
            $str = iconv('UTF-8', 'windows-1252', $transport_name);
            $pdf->Cell(38, 7, $str, 1, '', 'C', true);

            $pdf->Ln(15);
            $pdf->Cell(37, 7, 'Observaciones:', 0, '', 'L', true);
            $pdf->Ln(10);
            $str = iconv('UTF-8', 'windows-1252', $request_details);
            $pdf->Cell(250, 7, $str, 0, '', 'c', true);
            $pdf->Ln(15);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(60, 7, 'Con cargo al presupuesto: ', 0, '', 'L', true);
            $str = iconv('UTF-8', 'windows-1252', $center_name);
            $pdf->Cell(212, 7, $str, 0, '', 'c', true);

            $pdf->Output('Solicitud' . $person_nameworker . '-' . $ticket_date . '.pdf', 'D');
        } else {
            $this->redirectError();
        }
    }

}

?>
