<?php

class Lodging_conciliations extends Controller
{

    const TABLE_NAME = 'lodging_conciliations';

    function __construct()
    {
        parent::Controller();
        $this->load->model('lodging/lodging_conciliations_model', 'conn', true);
    }

    function index()
    {
        $centinela = new Centinela ();
        $flag = $centinela->accessTo('lodging/lodging_conciliations');
        if ($flag) {
            $this->load->view('sys/header_view');
            $this->load->view('lodging/lodging_conciliations_view');
            $this->load->view('sys/footer_view');
        } else {
            $this->redirectError();
        }
    }

    function redirectError()
    {
        $this->load->view('sys/header_view');
        $this->load->view('error_message');
        $this->load->view('sys/footer_view');
    }

    /**
     * Busca los datos para llenar el grid y los devuelve en formato JSON
     *
     */
    public function setDataGrid()
    {
        $dateStart = $this->input->post('dateStart');
        $dateEnd = $this->input->post('dateEnd');
        $hotel = $this->input->post('hotel');
        $province = $this->input->post('province');

        $data = $this->conn->getData($dateStart, $dateEnd, $hotel, $province);
        $cant = count($data);
        die("{count : " . $cant . ", data : " . json_encode($data) . "}");
    }

    public function accounting($show = false, $isPDF = 'no')
    {
        if ($this->session->userdata('roll_id') >= 4) {
            if ($show == false && $isPDF == 'no') {
                $this->load->view('sys/header_view');
                $this->load->view('lodging/lodging_accounting_view');
                $this->load->view('sys/footer_view');
            }

            if ($show == true && $isPDF == 'no') {

                $datosForWheres = array();
                $datosForWheres ['dateStart'] = $this->input->post('dateStart'); //no es el nomnre ed los componentes sino el valor ed las variables javascript del baseparam del js
                $datosForWheres ['dateEnd'] = $this->input->post('dateEnd');
                $datosForWheres ['center'] = $this->input->post('center');
                $datosForWheres ['province'] = $this->input->post('province');
                $datosForWheres ['hotel'] = $this->input->post('hotel');
                $datosForWheres ['motive'] = $this->input->post('motive');
                $datosForWheres ['conciliation'] = $this->input->post('conciliation');
                $datosForWheres ['inversion'] = $this->input->post('inversion');
                $datosForWheres ['chain_id'] = $this->input->post('chain_id');

                die($this->conn->getDataAccounting($datosForWheres ['dateStart'], $datosForWheres ['dateEnd'],
                    $datosForWheres ['hotel'], $datosForWheres ['province'], $datosForWheres ['center'],
                    $datosForWheres ['motive'], $show, $isPDF, $datosForWheres ['conciliation'],
                    $datosForWheres ['inversion'], $datosForWheres ['chain_id']));
            }

            if ($show == true && $isPDF == 'si') {

                $datosForWheres = array();

                $datosForWheres ['dateStart'] = $this->input->post('startdt'); //aqui obligado  se pasael nombre ed los componentes, verificar que eeste en standar submit true la forma de los componentes
                $datosForWheres ['dateEnd'] = $this->input->post('enddt');
                $datosForWheres ['center'] = $this->input->post('center_id');
                $datosForWheres ['province'] = $this->input->post('province_id');
                $datosForWheres ['hotel'] = $this->input->post('hotel_id');
                $datosForWheres ['motive'] = $this->input->post('motive_id');
                $datosForWheres ['conciliation'] = $this->input->post('conciliation');
                $datosForWheres ['inversion'] = $this->input->post('inversion');
                $datosForWheres ['chain_id'] = $this->input->post('chain_id');

                $data = $this->conn->getDataAccounting($datosForWheres ['dateStart'], $datosForWheres ['dateEnd'],
                    $datosForWheres ['hotel'], $datosForWheres ['province'], $datosForWheres ['center'],
                    $datosForWheres ['motive'], $show, $isPDF, $datosForWheres ['conciliation'],
                    $datosForWheres ['inversion'], $datosForWheres ['chain_id']);
                //son todos pero se filtra a veces
                // $cant = $this->conn->getCant();//no porque cogeria toda la tabla y en el for para formar el pdf explotaria
                $cant = count($data); //y como no se pagina pues el count es del total del arreglo recibido


                $this->load->library('FPDF/pdf_contab_lodging');
                $pdf = new Pdf_contab_lodging('L', 'mm', 'Letter');
                /* //a partir de aqui comienzo con lo q estaba en el header
                  $this->SetFont ( 'Arial', 'B', 13 );
                  $this->Ln ();
                  $this->Cell ( 15, 10, 'Fac.', '', 0, 'I' );
                  $this->Cell ( 20, 10, 'Carnet', '', 0, 'I' );
                  $this->Cell ( 60, 10, 'Nombre y Apellidos', '', 0, 'I' );
                  $this->Cell ( 42, 10, 'Prov. Hosp.', '', 0, 'I' );
                  $this->Cell ( 40, 10, 'Hotel', '', 0, 'I' );
                  $this->Cell ( 23, 10, 'Entrada', '', 0, 'I' );
                  $this->Cell ( 23, 10, 'Salida', '', 0, 'I' );
                  $this->Cell ( 20, 10, 'Dieta', '', 0, 'I' );
                  $this->Cell ( 20, 10, 'Hospedaje', '', 0, 'I' );
                  $this->Ln (); */
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

                    $str = iconv('UTF-8', 'windows-1252', $data [$i] ['person_identity']);
                    $pdf->Cell(20, 5, $str, '', '', '', true);

                    $str = iconv('UTF-8', 'windows-1252', $data [$i] ['person_worker']);
                    $pdf->Cell(60, 5, $str, '', '', '', true);

                    $str = iconv('UTF-8', 'windows-1252', $data [$i] ['province_lodging']);
                    $pdf->Cell(42, 5, $str, '', '', '', true);

                    $str = iconv('UTF-8', 'windows-1252', $data [$i] ['hotel_name']);
                    $pdf->Cell(40, 5, $str, '', '', '', true);

                    $str = iconv('UTF-8', 'windows-1252', $data [$i] ['lodging_entrancedate']);
                    $pdf->Cell(23, 5, $str, '', '', '', true);

                    $str = iconv('UTF-8', 'windows-1252', $data [$i] ['lodging_exitdate']);
                    $pdf->Cell(23, 5, $str, '', '', '', true);

                    $str = iconv('UTF-8', 'windows-1252', $data [$i] ['diet']);
                    $pdf->Cell(20, 5, $str, '', '', 'L', true);

                    $str = iconv('UTF-8', 'windows-1252', $data [$i] ['lodging']);
                    $pdf->Cell(20, 5, $str . ',00', '', '', 'L', true);
                    $pdf->Ln();
                    if ($i < $cant - 1) {
                        $pdf->Cell(20, 5, 'Centro Costo:', 0, '', 'L', true);
                        $str = iconv('UTF-8', 'windows-1252', $data [$i] ['center_name']);
                        $pdf->Cell(40, 5, $str, '', '', 'L', true);
                        $pdf->Cell(15, 5, 'Autoriza:', 0, '', 'L', true);
                        $str = iconv('UTF-8', 'windows-1252', $data [$i] ['person_licensedby']);
                        $pdf->Cell(80, 5, $str, '', '', 'L', true);
                        $pdf->Cell(25, 5, 'Tarea Inversion:', 0, '', 'L', true);
                        $str = iconv('UTF-8', 'windows-1252', $data [$i] ['request_inversiontask']);
                        $pdf->Cell(68, 5, $str, '', '', 'L', true);
                        $pdf->Ln(5);
                        $pdf->Cell(15, 5, 'Detalles:', 0, '', 'L', true);
                        $str = iconv('UTF-8', 'windows-1252', ucfirst($data [$i] ['request_details']));
                        $pdf->Cell(233, 5, $str, '', '', 'L', true);
                        $pdf->Ln(5);
                    }
                }
                $pdf->Output('Reporte Contabilidad Hospedajes-' . $datosForWheres ['dateStart'] . '-' . $datosForWheres ['dateEnd'] . '.pdf', 'D');
            }
        } else {
            $this->redirectError();
        }
    }

    public function setDataCociliation($show = false, $isPDF = 'no')
    {
		//set_time_limit(60);
		//max_execution_time(60);
        if ($this->session->userdata('roll_id') >= 4) {
            if ($show == false && $isPDF == 'no') {
                $this->load->view('sys/header_view');
                $this->load->view('lodging/lodging_reportconciliations_view');
                $this->load->view('sys/footer_view');
            }

            if ($show == true && $isPDF == 'no') {
                $to = (!isset($_POST ['limit'])) ? 100 : $_POST ['limit'];
                $from = (!isset($_POST ["start"])) ? 0 : $_POST ["start"];

                $datosForWheres = array();
                $datosForWheres ['dateStart'] = $this->input->post('dateStart'); //no es el nomnre ed los componentes sino el valor ed las variables javascript del baseparam del js
                $datosForWheres ['dateEnd'] = $this->input->post('dateEnd');
                $datosForWheres ['center'] = $this->input->post('center');
                $datosForWheres ['province'] = $this->input->post('province');
                $datosForWheres ['hotel'] = $this->input->post('hotel');
                $datosForWheres ['motive'] = $this->input->post('motive');

                die($this->conn->getDataCociliation($datosForWheres ['dateStart'], $datosForWheres ['dateEnd'], $datosForWheres ['hotel'], $datosForWheres ['province'], $datosForWheres ['center'], $datosForWheres ['motive'], $show, $isPDF));
            }

            if ($show == true && $isPDF == 'si') {

                $to = (!isset($_POST ['limit'])) ? 1000000 : $_POST ['limit']; //todos
                $from = (!isset($_POST ["start"])) ? 0 : $_POST ["start"];

                $datosForWheres = array();

                $datosForWheres ['dateStart'] = $this->input->post('startdt'); //aqui obligado  se pasael nombre ed los componentes, verificar que eeste en standar submit true la forma de los componentes
                $datosForWheres ['dateEnd'] = $this->input->post('enddt');
                $datosForWheres ['center'] = $this->input->post('center_id');
                $datosForWheres ['province'] = $this->input->post('province_id');
                $datosForWheres ['hotel'] = $this->input->post('hotel_id');
                $datosForWheres ['motive'] = $this->input->post('motive_id');

                $data = $this->conn->getDataCociliation($datosForWheres ['dateStart'], $datosForWheres ['dateEnd'], $datosForWheres ['hotel'], $datosForWheres ['province'], $datosForWheres ['center'], $datosForWheres ['motive'], $show, $isPDF);
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

    function to_excelAll()
    {
        $datosForWheres = array();
        $datosForWheres ['dateStart'] = $this->input->post('startdt'); //aqui obligado  se pasael nombre ed los componentes, verificar que eeste en standar submit true la forma de los componentes
        $datosForWheres ['dateEnd'] = $this->input->post('enddt');
        $datosForWheres ['center'] = $this->input->post('center_id');
        $datosForWheres ['province'] = $this->input->post('province_id');
        $datosForWheres ['hotel'] = $this->input->post('hotel_id');
        $datosForWheres ['motive'] = $this->input->post('motive_id');
        $this->load->plugin('to_excel');
        $request_requests_table = 'request_requests';
        $request_lodgings_table = 'request_lodgings';
        $conf_costcenters_table = 'conf_costcenters';
        $conf_hotels_table = 'conf_hotels';
        $lodging_edit_table = 'lodging_edit';
        $this->db->select(self::TABLE_NAME . '.bill_number as factura, ' .
            self::TABLE_NAME . '.diet_amount as dieta, ' .
            self::TABLE_NAME . '.lodging_amount as hospedaje, ' .
            self::TABLE_NAME . '.conciliation_entrancedate as entrada_hospedaje, ' .
            self::TABLE_NAME . '.conciliation_exitdate as salida_hospedaje, ' .
            'person_persons.person_name as Nombre,
							person_persons.person_lastname as PrimerApellido,
							person_persons.person_secondlastname as SegundoApellido,
							person_persons.person_identity as CI,
							conf_provinces.province_name as provincia_hospedaje,
							conf_hotels.hotel_name as Hotel,
							conf_costcenters.center_name as Centro_costo,' .
            $request_requests_table . '.request_inversiontask as tarea_inversion, ' .
            $request_requests_table . '.request_details as detalle');
        $this->db->from('request_requests');
        $this->db->join('person_persons', 'person_persons.person_id = request_requests.person_idworker', 'inner');
        $this->db->join('conf_motives', 'conf_motives.motive_id = request_requests.motive_id', 'inner');
        $this->db->join('conf_costcenters', 'conf_costcenters.center_id = request_requests.center_id', 'inner');
        $this->db->join('request_lodgings', 'request_lodgings.request_id = request_requests.request_id', 'inner');
        $this->db->join('conf_provinces', 'conf_provinces.province_id = request_lodgings.province_idlodging', 'inner');
        $this->db->join('lodging_edit', 'lodging_edit.request_id = request_lodgings.request_id', 'inner');
        $this->db->join('conf_hotels', 'conf_hotels.hotel_id = lodging_edit.hotel_id', 'inner');
        $this->db->join(self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $lodging_edit_table . '.request_id', 'inner');
        $this->db->where('lodging_edit.lodging_noshow <> ', 'on');
        $this->db->where('request_lodgings.lodging_state', 1);
        $this->db->where(self::TABLE_NAME . '.conciliation_entrancedate >=', $datosForWheres ['dateStart']);
        $this->db->where(self::TABLE_NAME . '.conciliation_entrancedate <=', $datosForWheres ['dateEnd']);

        if (!empty($datosForWheres ['province'])) {
            $this->db->where($request_lodgings_table . '.province_idlodging =', $datosForWheres ['province']);
        }
        if (!empty($datosForWheres ['hotel'])) {
            $this->db->where($lodging_edit_table . '.hotel_id', $datosForWheres ['hotel']);
        }
        if (!empty($datosForWheres ['center'])) {
            $this->db->where($request_requests_table . '.center_id', $datosForWheres ['center']);
        }
        $this->db->distinct();
        $this->db->order_by('entrada_hospedaje', 'asc');
        $result = $this->db->get();
        to_excel($result, 'reporte_de_conciliacion_hospedaje');
    }

    function to_excel($array)
    {

        $datosForWheres = array();
        $dateStart = $this->input->post('startdt'); //aqui obligado  se pasael nombre ed los componentes, verificar que eeste en standar submit true la forma de los componentes
        $dateEnd = $this->input->post('enddt');
        $center = $this->input->post('center_id');
        $province = $this->input->post('province_id');
        $hotel = $this->input->post('hotel_id');
        $motive = $this->input->post('motive_id');
        $this->load->plugin('to_excel');
        $request_requests_table = 'request_requests';
        $request_lodgings_table = 'request_lodgings';
        $conf_costcenters_table = 'conf_costcenters';
        $conf_hotels_table = 'conf_hotels';
        $lodging_edit_table = 'lodging_edit';
        $this->db->select(self::TABLE_NAME . '.bill_number as factura, ' .
            self::TABLE_NAME . '.diet_amount as dieta, ' .
            self::TABLE_NAME . '.lodging_amount as hospedaje, ' .
            self::TABLE_NAME . '.conciliation_entrancedate as entrada_hospedaje, ' .
            self::TABLE_NAME . '.conciliation_exitdate as salida_hospedaje, ' .
            'person_persons.person_name as Nombre,
							person_persons.person_lastname as PrimerApellido,
							person_persons.person_secondlastname as SegundoApellido,
							person_persons.person_identity as CI,
							conf_provinces.province_name as provincia_hospedaje,
							conf_hotels.hotel_name as Hotel,
							conf_costcenters.center_name as Centro_costo,' .
            $request_requests_table . '.request_inversiontask as tarea_inversion, ' .
            $request_requests_table . '.request_details as detalle');
        $this->db->from('request_requests');
        $this->db->join('person_persons', 'person_persons.person_id = request_requests.person_idworker', 'inner');
        $this->db->join('conf_motives', 'conf_motives.motive_id = request_requests.motive_id', 'inner');
        $this->db->join('conf_costcenters', 'conf_costcenters.center_id = request_requests.center_id', 'inner');
        $this->db->join('request_lodgings', 'request_lodgings.request_id = request_requests.request_id', 'inner');
        $this->db->join('conf_provinces', 'conf_provinces.province_id = request_lodgings.province_idlodging', 'inner');
        $this->db->join('lodging_edit', 'lodging_edit.request_id = request_lodgings.request_id', 'inner');
        $this->db->join('conf_hotels', 'conf_hotels.hotel_id = lodging_edit.hotel_id', 'inner');
        $this->db->join(self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $lodging_edit_table . '.request_id', 'inner');
        $this->db->where('lodging_edit.lodging_noshow <> ', 'on');
        $this->db->where('request_lodgings.lodging_state', 1);
        $this->db->where(self::TABLE_NAME . '.conciliation_entrancedate >=', $dateStart);
        $this->db->where(self::TABLE_NAME . '.conciliation_entrancedate <=', $dateEnd);
        $request_ids = explode("-", $array);
        $this->db->where_in($lodging_edit_table . '.request_id', $request_ids);
        if (!empty($province)) {
            $this->db->where($request_lodgings_table . '.province_idlodging =', $province);
        }
        if (!empty($hotel)) {
            $this->db->where($lodging_edit_table . '.hotel_id', $hotel);
        }
        if (!empty($center)) {
            $this->db->where($request_requests_table . '.center_id', $center);
        }
        $this->db->distinct();
        $this->db->order_by('entrada_hospedaje', 'asc');
        $result = $this->db->get();
        if ($result->result() != null) {
            foreach ($request_ids as $value) {
                $this->conn->setPay($value);
            }
        }
        to_excel($result, 'reporte_de_conciliacion_hospedaje');
    }

    public function setPdfCociliation($array)
    {
        if ($this->session->userdata('roll_id') >= 4) {
            $to = (!isset($_POST ['limit'])) ? 1000000 : $_POST ['limit'];
            $from = (!isset($_POST ["start"])) ? 0 : $_POST ["start"];

            $datosForWheres = array();

            $datosForWheres ['dateStart'] = $this->input->post('startdt'); //aqui obligado  se pasael nombre ed los componentes, verificar que eeste en standar submit true la forma de los componentes
            $datosForWheres ['dateEnd'] = $this->input->post('enddt');
            $datosForWheres ['center'] = $this->input->post('center_id');
            $datosForWheres ['province'] = $this->input->post('province_id');
            $datosForWheres ['hotel'] = $this->input->post('hotel_id');
            $datosForWheres ['motive'] = $this->input->post('motive_id');

            $data = $this->conn->getPdfCociliation($datosForWheres ['dateStart'], $datosForWheres ['dateEnd'], $datosForWheres ['hotel'], $datosForWheres ['province'], $datosForWheres ['center'], $datosForWheres ['motive'], $array);
            $cant = count($data);

            if ($cant > 0) {
                $request_ids = explode("-", $array);
                foreach ($request_ids as $value) {
                    $this->conn->setPay($value);
                }
            }
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
        } else {
            $this->redirectError();
        }
    }

    public function billPdf($bill, $hotel)
    {
        $data = $this->conn->billPdf($bill, $hotel);
        $cant = count($data); //y como no se pagina pues el count es del total del arreglo recibido

        $this->load->library('FPDF/pdf_conciliation_lodging');
        $pdf = new Pdf_conciliation_lodging('L', 'mm', 'A4');
        $pdf->AddPage();

        $pdf->Ln(1);
        $flag = true;
        $pdf->SetFont('Arial', '', 8);

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

            $str = iconv('UTF-8', 'windows-1252', $data [$i] ['province_lodging']);
            $pdf->Cell(42, 5, $str, '', '', '', true);

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
                $str = iconv('UTF-8', 'windows-1252', strtolower($data [$i] ['request_details']));
                $pdf->Cell(248, 5, $str, '', '', 'L', true);
                $pdf->Ln(5);
            }
        }
        $pdf->Output('Reporte Conciliación de Hospedajes - Factura' . $bill . '.pdf', 'D');
    }

    /**
     * Funcion para Insertar
     *
     */
    function insert()
    {
        $centinela = new Centinela ();
        $flag = $centinela->accessTo('lodging/lodging_conciliations');
        if ($flag) {
            $result = $this->conn->insert();
            die("{success : $result}");
        } else {
            $this->redirectError();
        }
    }

    function insertBill($request_id, $bill_number)
    {
        $centinela = new Centinela ();
        $flag = $centinela->accessTo('lodging/lodging_conciliations');
        if ($flag) {

            $result = $this->conn->insertBill($request_id, $bill_number);
            die("{success : $result}");
        } else {
            $this->redirectError();
        }
    }

    /**
     * Elimina
     *
     */
    function delete($id)
    {
        $centinela = new Centinela ();
        $flag = $centinela->accessTo('lodging/lodging_lodgingconciliations');
        if ($flag) {

            $this->conn->delete($id);
        } else {
            $this->redirectError();
        }
    }

    /**
     *
     *
     */
    function getIds()
    {
        $data = $this->conn->getIds();
        die("{data : " . json_encode($data) . "}");
    }

    function getById($request_id)
    {
        $data = $this->conn->getById($request_id);
        die("{data : " . json_encode($data) . "}");
        //echo "<pre>"; print_r($data); echo "</pre>";
    }

}

?>
