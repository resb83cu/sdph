<?php

class Accounting_controlexport extends Controller
{

    //put your code here
    public function __construct()
    {
        parent::Controller();
        //$this->load->library('tcpdf');
        $this->load->model('request/request_requests_model', 'conn', true);
        $this->load->model('accounting/accounting_advanceliquidation_model', 'liquidation', true);
    }

    function index()
    {
        $centinela = new Centinela ();
        $flag = $centinela->accessTo('accounting/accounting_advanceliquidation');
        if ($flag) {
            $this->load->view('sys/header_view');
            $this->load->view('accounting/accounting_controlexcel_view');
            $this->load->view('sys/footer_view');
        } else {
            $this->redirectError();
        }
    }

    function controlExportPdf()
    {
        $centinela = new Centinela ();
        $flag = $centinela->accessTo('accounting/accounting_advanceliquidation');
        if ($flag) {
            $this->load->view('sys/header_view');
            $this->load->view('accounting/accounting_controlpdf_view');
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

    function reportControlExcel()
    {

        $desde = $this->input->post('startdt');
        $hasta = $this->input->post('enddt');
        $data = $this->liquidation->getAdvanceControlExcel($desde, $hasta);
        $this->load->plugin('to_excel');
        $doc = 'Control_Anticipo_' . $desde . '_' . $hasta;
        to_excel($data, $doc, array('FECHA_IMPRESION', 'CONSECUTIVO', 'RESPONSABLE', 'NOMBRE_TRABAJADOR', '1ER_APELLIDO', '2DO_APELLIDO', 'CC', 'AREA', 'DESDE', 'HASTA', 'IMPORTE', 'FECHA_LIQUIDACION', 'UTILIZADO', 'DEVUELTO', 'ENTREGADO'));
        //echo "<pre>"; print_r($data); echo "</pre>";
    }

    function reportControlPdf(/*$center*/)
    {
        if ($this->session->userdata('person_id') == 0) {
            return "{success: false, errors: { reason: 'Su sesi&oacute;n ha expirado. Ingrese de nuevo al sistema.' }, prorogation: 'no'}";
        }
        $fecha = new Dates ();
        $arrayDay = explode('-', $fecha->now());
        $data = $this->liquidation->getAdvanceControl();//$center
	 //echo "<pre>"; print_r($data); echo "</pre>";
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->AddPage();
        $pdf->SetFont('times', '', 8);
        $header = '<table width="520" border="1">
                      <tr>
                        <td colspan="4" width="23%" class="Pdf"><p align="center" class="Pdf"><br/><strong>ETECSA</strong></p>
                          Centro Contable:' . $data[0]['center_name'] . '</td>
                        <td colspan="5" width="51%"><p align="center" class="Pdf"><br/><strong>SC-3-04</strong><br />
                        <strong>CONTROL DE ANTICIPOS A JUSTIFICAR</strong></p><p align="center" class="Pdf">CUC_X_  CUP__   USD__  EUR__</p><br /></td>
                        <td colspan="5" width="26%"><br/><p align="center" class="Pdf">AÑO:' . $arrayDay[0] . '</p></td>
                      </tr>
                      <tr>
                        <td colspan="2" width="5%" align="center" class="Pdf">Fecha</td>
                        <td align="center" rowspan="2" width="6%" class="Pdf">No.<br />Anticipo</td>
                        <td align="center" rowspan="2" width="12%" class="Pdf">Tipo de<br /> Anticipo</td>
                        <td align="center" rowspan="2" width="24%" class="Pdf">A  Nombre de</td>
                        <td align="center" rowspan="2" width="16%" class="Pdf">Área  de trabajo</td>
                        <td align="center" rowspan="2" width="6%" class="Pdf">Importe</td>
                        <td colspan="2" width="5%" align="center" class="Pdf">Vence</td>
                        <td colspan="5" width="26%" align="center" class="Pdf">Liquidación</td>
                      </tr>
                      <tr>
                        <td align="center" width="2.5%" class="Pdf">D</td>
                        <td align="center" width="2.5%" class="Pdf">M</td>

                        <td align="center" width="2.5%" class="Pdf">D</td>
                        <td align="center" width="2.5%" class="Pdf">M</td>
                        <td align="center" width="2.5%" class="Pdf">D</td>
                        <td align="center" width="2.5%" class="Pdf">M</td>
                        <td align="center" width="7%" class="Pdf">Utilizado</td>
                        <td align="center" width="7%" class="Pdf">Devuelto</td>
                        <td align="center" width="7%" class="Pdf">Entregado</td>
                      </tr>';
        $body = '';
        foreach ($data as $value) {
            $nombreTrabajador = $value['person_worker'];
            $fechaPrint = explode('-', $value['print_date']);
            $fechaIncrementada = $fecha->DateAddManyDays($value['lodging_exitdate'], 4);
            $fechaSalida = explode('-', $fechaIncrementada);
            if (empty($value['liquidation_date'])) {
                $dayLiquidation = '';
                $monthLiquidation = '';
            } else {
                $fechaLiquidacion = explode('-', $value['liquidation_date']);
                $dayLiquidation = $fechaLiquidacion[2];
                $monthLiquidation = $fechaLiquidacion[1];
            }
            $liquidation_used = empty($value['liquidation_used']) ? '' : $this->formatDecimalMoney($value['liquidation_used']);
            if (strtolower($value['liquidation_used']) == 'cancelado') {
                $liquidation_used = $value['liquidation_used'];
            }
            $liquidation_repay = empty($value['liquidation_repay']) ? '' : $this->formatDecimalMoney($value['liquidation_repay']);
            $liquidation_given = empty($value['liquidation_given']) ? '' : $this->formatDecimalMoney($value['liquidation_given']);
            $body .= '<tr>
                        <td align="center">' . substr($fechaPrint[2], 0, 2) . '</td>
                        <td align="center">' . $fechaPrint[1] . '</td>
                        <td align="center" class="Pdf">' . $value['center_consecutive'] . '</td>
                        <td align="center" class="Pdf">Gastos de Viaje</td>
                        <td align="center" class="Pdf">' . $nombreTrabajador . '</td>
                        <td align="center" class="Pdf">' . $value['request_area'] . '</td>
                        <td align="center" class="Pdf">' . $value['amount_estimated'] . '</td>
                        <td align="center" class="Pdf">' . $fechaSalida[2] . '</td>
                        <td align="center" class="Pdf">' . $fechaSalida[1] . '</td>
                        <td align="center" class="Pdf">' . $dayLiquidation . '</td>
                        <td align="center" class="Pdf">' . $monthLiquidation . '</td>
                        <td align="center" class="Pdf">' . $liquidation_used . '</td>
                        <td align="center" class="Pdf">' . $liquidation_repay . '</td>
                        <td align="center" class="Pdf">' . $liquidation_given . '</td>
                      </tr>';
        }
        $lastRow = '</table>';
        $html = $header . $body . $lastRow;
        // output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('MODELO SC-3-04 - CONTROL DE ANTICIPOS A JUSTIFICAR -' . $data[0]['center_name'] . '-' . $fecha->now() . '.pdf', 'D');
    }

    public function formatDecimalMoney($money)
    {
        $is_decimal = stripos($money, ".");
        if (is_numeric($is_decimal)) {
            return $money . "0";
        } else {
            return $money . ".00";
        }
    }

}
