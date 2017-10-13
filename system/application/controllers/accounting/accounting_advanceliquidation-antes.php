<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of accounting_advanceliquidation
 *
 * @author ramiro
 */
class Accounting_advanceliquidation extends Controller
{

    //put your code here
    public function __construct()
    {
        parent::Controller();
	 $this->load->library('tcpdf');
        $this->load->model('request/request_requests_model', 'conn', true);
        $this->load->model('accounting/accounting_advanceliquidation_model', 'liquidation', true);
    }

    function index()
    {
        $centinela = new Centinela ();
        $flag = $centinela->accessTo('accounting/accounting_advanceliquidation');
        if ($flag) {
            $this->load->view('sys/header_view');
            $this->load->view('accounting/accounting_advanceliquidation_view');
            $this->load->view('sys/footer_view');
        } else {
            $this->redirectError();
        }
    }

    function advanceLiquidationGroup()
    {
        $centinela = new Centinela ();
        $flag = $centinela->accessTo('accounting/accounting_advanceliquidation');
        if ($flag) {
            $this->load->view('sys/header_view');
            $this->load->view('accounting/accounting_advanceliquidationgroup_view');
            $this->load->view('sys/footer_view');
        } else {
            $this->redirectError();
        }
    }

    function showLiquidationView()
    {
//        $centinela = new Centinela ( );
//        $flag = $centinela->accessTo('accounting/accounting_advanceliquidation');
//        if ($flag) {
        $this->load->view('sys/header_view');
        $this->load->view('accounting/accounting_liquidation_view');
        $this->load->view('sys/footer_view');
//        } else {
//            $this->redirectError();
//        }
    }

    function redirectError()
    {
        $this->load->view('sys/header_view');
        $this->load->view('error_message');
        $this->load->view('sys/footer_view');
    }

    function getAdvanceLiquidationRequests()
    {
        $centinela = new Centinela ();
        $flag = $centinela->accessTo('accounting/accounting_advanceliquidation');
        if ($flag) {
            $dateStart = $this->input->post('dateStart');
            $dateEnd = $this->input->post('dateEnd');
            $province = $this->input->post('province');

            return $this->conn->getAdvanceLiquidationRequests($dateStart, $dateEnd, $province);
            //echo "<pre>"; print_r($data); echo "</pre>";
        } else {
            $this->redirectError();
        }
    }

    function getAdvanceLiquidationRequestsGroup()
    {
        $centinela = new Centinela ();
        $flag = $centinela->accessTo('accounting/accounting_advanceliquidation');
        if ($flag) {
            $dateStart = $this->input->post('dateStart');
            $dateEnd = $this->input->post('dateEnd');
            $province = $this->input->post('province');

            return $this->conn->getAdvanceLiquidationRequestsGroup($dateStart, $dateEnd, $province);
            //echo "<pre>"; print_r($data); echo "</pre>";
        } else {
            $this->redirectError();
        }
    }

    function manageAdvanceLiquidation($request_id)
    {
	 if ($this->session->userdata('person_id') == 0) {
            return "{success: false, errors: { reason: 'Su sesi&oacute;n ha expirado. Ingrese de nuevo al sistema.' }, prorogation: 'no'}";
        }
        $fecha = new Dates ();
        $data = $this->conn->getAdvanceLiquidationById($request_id);
        $this->load->model('conf/conf_costcenters_model', 'costcenter', true);
        if ($data->advance_requested == "f") {
            if($data->center_idadvance == 47){
                $this->costcenter->incrementConsecutive(40);
                $consecutive = $this->costcenter->getCenterById(40)->center_consecutive;
            } else {
                $this->costcenter->incrementConsecutive($data->center_idadvance);
                $consecutive = $this->costcenter->getCenterById($data->center_idadvance)->center_consecutive;
            }            
	     $printDate = $fecha->now();
        } else {
            $liq = $this->liquidation->getLiquidationByRequestId($request_id);
//            echo "<pre>"; print_r($liq); echo "</pre>";
            $consecutive = $liq[0]['center_consecutive'];
            $printDate = $liq[0]['print_date'];
        }
        $id = $this->liquidation->insert($data->request_id, $data->request_details, $data->person_idworker, $data->request_area, $data->request_consecutive, $data->person_groupresponsable, $data->center_idadvance, $data->diet_entrancedate, $data->diet_exitdate, $printDate, $consecutive);
        if ($id > 0) {
            $this->conn->updateAdvanceRequested($request_id, "true");
        }
        return $this->conn->getAdvanceLiquidationById($request_id);
    }

    function manageAdvanceLiquidationGroup($request_ids)
    {
	 if ($this->session->userdata('person_id') == 0) {
            return "{success: false, errors: { reason: 'Su sesi&oacute;n ha expirado. Ingrese de nuevo al sistema.' }, prorogation: 'no'}";
        }

        $fecha = new Dates ();
        $data = $this->conn->getAdvanceLiquidationById($request_ids[0]);
        $this->load->model('conf/conf_costcenters_model', 'costcenter', true);
        if ($data->advance_requested === "f") {
            if($data->center_idadvance == 47){
                $this->costcenter->incrementConsecutive(40);
                $consecutive = $this->costcenter->getCenterById(40)->center_consecutive;
            } else {
                $this->costcenter->incrementConsecutive($data->center_idadvance);
                $consecutive = $this->costcenter->getCenterById($data->center_idadvance)->center_consecutive;
            }
            $printDate = $fecha->now();
        } else {
            $liq = $this->liquidation->getLiquidationByRequestId($request_ids[0]);
            $consecutive = $liq[0]['center_consecutive'];
            $printDate = $liq[0]['print_date'];
        }

        foreach ($request_ids as $value) {
            $this->conn->updateAdvanceRequested($value, "true");
        }
        $advance = $this->conn->getAdvanceLiquidationById($request_ids[0]);
        $this->liquidation->insert($advance->request_id, $advance->request_details, $advance->person_idworker, $advance->request_area, $advance->request_consecutive, $advance->person_groupresponsable, $advance->center_idadvance, $advance->diet_entrancedate, $advance->diet_exitdate, $printDate, $consecutive);
        return $this->conn->getAdvanceLiquidationById($advance->request_id);
    }

    function insertAdvanceLiquidation($request_id)
    {
        $data = $this->manageAdvanceLiquidation($request_id);
        if (is_null($data)) {
            return FALSE;
        }
        return TRUE;
    }

    function reportAdvanceLiquidationById($request_id)
    {
	 if ($this->session->userdata('person_id') == 0) {
            return "{success: false, errors: { reason: 'Su sesi&oacute;n ha expirado. Ingrese de nuevo al sistema.' }, prorogation: 'no'}";
        }

        $fecha = new Dates ();
        $data = $this->manageAdvanceLiquidation($request_id);
        $nombreTrabajador = $data->person_name . ' ' . $data->person_lastname . ' ' . $data->person_secondlastname;
        $fechaEntrada = explode('-', $data->diet_entrancedate);
        $fechaSalida = explode('-', $data->diet_exitdate);
        /*if ($data->lodging_prorogate == 1) {
            $diasHospedaje = $fecha->diasEntreFechas($data->diet_entrancedate, $data->diet_exitdate);
        } else {*/
            $diasHospedaje = $fecha->diasEntreFechas($data->diet_entrancedate, $data->diet_exitdate) + 1;
        //}
        $pagoDinero = 6.5;
        $amount = $diasHospedaje * $pagoDinero;
        $this->liquidation->setAmountEstimated($request_id, $amount);
        $liq = $this->liquidation->getLiquidationByRequestId($request_id);
//            echo "<pre>"; print_r($liq); echo "</pre>";
        $center_consecutive = $liq[0]['center_consecutive'];
        $arrayDay = explode('-', $liq[0]['print_date']);
        //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	 $pdf = new TCPDF('L', PDF_UNIT, 'LETTER', true, 'UTF-8', false);
        $pdf->AddPage();
        $pdf->SetFont('times', '', 8);
        $html = '<table width="700" border="1">
                    <tr>
                      <td width="50%" colspan="3" rowspan="4" align="left" valign="top" class="Pdf"><br /> Centro contable: ' . $data->center_name . ' <br /> Área de trabajo: ' . $data->request_area . ' <br /> Solicitud No. ' . $data->request_consecutive . '<br /></td>
                      <td width="35%" colspan="3" rowspan="4" align="center" valign="top" class="Pdf"><strong>SC-3-02 <br />ANTICIPO Y LIQUIDACION  DE <br />GASTOS DE VIAJES NACIONALES <br />EN CUC</strong></td>
                      <td width="15%" colspan="4"><img src="images/logo.png" width="100" height="36" /></td>
                    </tr>
                    <tr>
                      <td width="15%" colspan="4"><div align="center"><span class="Pdf">Fecha de emisión</span></div></td>
                    </tr>
                    <tr>
                      <td width="5%"><div align="center"><span class="Pdf">D</span></div></td>
                      <td width="5%" ><div align="center"><span class="Pdf">M</span></div></td>
                      <td width="5%" colspan="2" ><div align="center"><span class="Pdf">A</span></div></td>
                    </tr>
                    <tr>
                      <td><div align="center"><span class="Pdf">' . substr($arrayDay[2], 0, 2) . '</span></div></td>
                      <td><div align="center"><span class="Pdf">' . $arrayDay[1] . '</span></div></td>
                      <td colspan="2"><div align="center"><span class="Pdf">' . $arrayDay[0] . '</span></div></td>
                    </tr>
                    <tr>
                      <td width="65%" colspan="5" rowspan="2" valign="top" class="Pdf">Nombre completo y Apellidos: ' . $nombreTrabajador . '</td>
                      <td width="35%" colspan="5"><div class="Pdf" align="center">CLASIFICACION</div></td>
                    </tr>
                    <tr>
                      <td width="30%" colspan="3" class="Pdf"> Fuera de la localidad (Extranjero).</td>
                      <td width="5%" colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="65%" colspan="5" rowspan="7" valign="top" class="Pdf"> Labor a realizar o detalle del pago: ' . $data->request_details . '</td>
                      <td width="30%" colspan="3"><span class="Pdf"> En la localidad.(Dentro del país).</span></td>
                      <td width="5%" colspan="2"><div align="center"><span class="Pdf">X</span></div></td>
                    </tr>
                    <tr>
                      <td width="20%"><div align="center"><span class="Pdf">FECHA</span></div></td>
                      <td width="5%" ><div align="center"><span class="Pdf">D</span></div></td>
                      <td width="5%" ><div align="center"><span class="Pdf">M</span></div></td>
                      <td width="5%" colspan="2"><div align="center"><span class="Pdf">HORA</span></div></td>
                    </tr>
                    <tr>
                      <td width="20%" ><div align="left"><span class="Pdf"> Salida estimada</span></div></td>
                      <td width="5%" ><div align="center"><span class="Pdf">' . $fechaEntrada[2] . '</span></div></td>
                      <td width="5%" ><div align="center"><span class="Pdf">' . $fechaEntrada[1] . '</span></div></td>
                      <td width="5%" colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="20%" ><div align="left"><span class="Pdf"> Regreso estimado</span></div></td>
                      <td width="5%" ><div align="center"><span class="Pdf">' . $fechaSalida[2] . '</span></div></td>
                      <td width="5%" ><div align="center"><span class="Pdf">' . $fechaSalida[1] . '</span></div></td>
                      <td width="5%" colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="20%"><div align="left"><span class="Pdf"> Salida Real</span></div></td>
                      <td width="5%" >&nbsp;</td>
                      <td width="5%" >&nbsp;</td>
                      <td width="5%" colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="20%"><div align="left"><span class="Pdf"> Regreso Real</span></div></td>
                      <td width="5%" >&nbsp;</td>
                      <td width="5%" >&nbsp;</td>
                      <td width="5%" colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="35%" colspan="5"><div align="center"><span class="Pdf">Días de Viaje</span></div></td>
                    </tr>
                    <tr>
                      <td width="50%" colspan="3"><div align="center"><span class="Pdf">AUTORIZADO</span></div></td>
                      <td width="7%"><div align="center"><span class="Pdf">DIA</span></div></td>
                      <td width="8%"><div align="center"><span class="Pdf">MES</span></div></td>
                      <td width="30%" colspan="3"><span class="Pdf"> Dias estimados</span></td>
                      <td width="5%" colspan="2"><div align="center">' . $diasHospedaje . '</div></td>
                    </tr>
                    <tr>
                      <td width="25%" rowspan="2" valign="top"><span class="Pdf"> Entrega:</span></td>
                      <td width="25%" colspan="2" valign="top" rowspan="2"><span class="Pdf"> Liquidación:</span></td>
                      <td rowspan="2">&nbsp;</td>
                      <td rowspan="2">&nbsp;</td>
                      <td width="30%" colspan="3"><span class="Pdf"> Dias Reales</span></td>
                      <td width="5%" colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="30%" colspan="3"><span class="Pdf"> Dias hospedados reales</span></td>
                      <td width="5%" colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="20%" rowspan="2" valign="top"><span class="Pdf"> Recibido</span></td>
                      <td width="7%" valign="center"><div align="center"><span class="Pdf">Dia</span></div></td>
                      <td width="7%" valign="center" class="Pdf"><div align="center">Mes</div></td>
                      <td width="14%" class="Pdf"><div align="center">Concepto</div></td>
                      <td width="10%" class="Pdf"><div align="center">Total</div></td>
                      <td width="12%" colspan="2"><div align="center">
                      <span class="Pdf">Dieta</span></div></td>
                      <td width="10%" class="Pdf"><div align="center">Hosp</div></td>
                      <td width="10%" class="Pdf"><div align="center">Otros </div></td>
                      <td width="10%" class="Pdf"><div align="center">Transp.</div></td>
                    </tr>
                    <tr>
                      <td valign="top">&nbsp;</td>
                      <td valign="top" class="Pdf">&nbsp;</td>
                      <td class="Pdf"><div align="center">Entregado</div></td>
                      <td class="Pdf"><div align="center">' . $this->formatDecimalMoney($amount) . '</div></td>
                      <td colspan="2"><div align="center">' . $this->formatDecimalMoney($amount) . '</div></td>
                      <td class="Pdf">&nbsp;</td>
                      <td class="Pdf">&nbsp;</td>
                      <td class="Pdf">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="20%" rowspan="3" valign="top"><div align="left" class="Pdf"> Liquidado</div></td>
                      <td width="7%" valign="center"><div align="center"><span class="Pdf">Dia</span></div></td>
                      <td width="7%" valign="center" class="Pdf"><div align="center">Mes</div></td>
                      <td width="14%" class="Pdf"><div align="center">Utilizado</div></td>
                      <td width="10%" class="Pdf">&nbsp;</td>
                      <td width="12%" colspan="2">&nbsp;</td>
                      <td width="10%" class="Pdf">&nbsp;</td>
                      <td width="10%" class="Pdf">&nbsp;</td>
                      <td width="10%" class="Pdf">&nbsp;</td>
                    </tr>
                    <tr>
                      <td rowspan="2" valign="top">&nbsp;</td>
                      <td rowspan="2" valign="top">&nbsp;</td>
                      <td width="14%" class="Pdf"><div align="center">Devuelto</div></td>
                      <td width="10%" class="Pdf">&nbsp;</td>
                      <td width="12%" colspan="2">&nbsp;</td>
                      <td width="10%" class="Pdf">&nbsp;</td>
                      <td width="10%" class="Pdf">&nbsp;</td>
                      <td width="10%" class="Pdf">&nbsp;</td>
                    </tr>
                    <tr>
                      <td class="Pdf"><div align="center">A. Entreg.</div></td>
                      <td class="Pdf">&nbsp;</td>
                      <td colspan="2">&nbsp;</td>
                      <td class="Pdf">&nbsp;</td>
                      <td class="Pdf">&nbsp;</td>
                      <td class="Pdf">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="20%" rowspan="2" valign="top" class="Pdf"> Custodio</td>
                      <td valign="top"><div align="center">Dia</div></td>
                      <td valign="top"><div align="center">Mes</div></td>
                      <td colspan="6"> Anotado por:</td>
                      <td rowspan="2" valign="top"> No. ' . $center_consecutive . '</td>
                    </tr>
                    <tr>
                      <td valign="top">&nbsp;</td>
                      <td valign="top" class="Pdf">&nbsp;</td>
                      <td valign="top" colspan="6" class="Pdf"> Registrado por:</td>
                    </tr>
                  </table>';
        // output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('MODELO SC-3-02 - ANTICIPO Y LIQUIDACION DE GASTOS-' . $nombreTrabajador . '-' . $fecha->now() . '.pdf', 'D');
    }

    function reportControlAdvanceLiquidation()
    {
	 if ($this->session->userdata('person_id') == 0) {
            return "{success: false, errors: { reason: 'Su sesi&oacute;n ha expirado. Ingrese de nuevo al sistema.' }, prorogation: 'no'}";
        }

        $fecha = new Dates ();
        $arrayDay = explode('-', $fecha->now());
        $data = $this->liquidation->getAdvanceControl();

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->AddPage();
        $pdf->SetFont('times', '', 8);
        $header = '<table width="520" border="1">
                      <tr>
                        <td colspan="4" width="23%" class="Pdf"><p align="center" class="Pdf"><br/><strong>ETECSA</strong></p>
                          Centro Contable:' . $data[0]['center_name'] . '</td>
                        <td colspan="5" width="51%"><p align="center" class="Pdf"><br/><strong>SC-3-04</strong><br />
                        <strong>CONTROL DE ANTICIPOS A JUSTIFICAR</strong></p><p align="center" class="Pdf">CUC_X_  CUP__   USD__  EUR__</p><br /></td>
                        <td colspan="5" width="26%"><br/><p align="center" class="Pdf">A�O:' . $arrayDay[0] . '</p></td>
                      </tr>
                      <tr>
                        <td colspan="2" width="5%" align="center" class="Pdf">Fecha</td>
                        <td align="center" rowspan="2" width="6%" class="Pdf">No.<br />Anticipo</td>
                        <td align="center" rowspan="2" width="12%" class="Pdf">Tipo de<br /> Anticipo</td>
                        <td align="center" rowspan="2" width="24%" class="Pdf">A  Nombre de</td>
                        <td align="center" rowspan="2" width="16%" class="Pdf">�rea  de trabajo</td>
                        <td align="center" rowspan="2" width="6%" class="Pdf">Importe</td>
                        <td colspan="2" width="5%" align="center" class="Pdf">Vence</td>
                        <td colspan="5" width="26%" align="center" class="Pdf">Liquidaci�n</td>
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

    function reportAdvanceLiquidationByResponsable($ids)
    {
	 if ($this->session->userdata('person_id') == 0) {
            return "{success: false, errors: { reason: 'Su sesi&oacute;n ha expirado. Ingrese de nuevo al sistema.' }, prorogation: 'no'}";
        }

        $fecha = new Dates ();
        $requests = explode('-', $ids);
        $data = $this->manageAdvanceLiquidationGroup($requests);
	 $workers = $this->getRequestDataFromAdvance($requests);
        $nombreTrabajador = strtoupper($data->person_groupresponsable);
        $fechaEntrada = explode('-', $data->diet_entrancedate);
        $fechaSalida = explode('-', $data->diet_exitdate);
	 //nuevo para evitar que salga mal el desgloce
	 $liq = $this->liquidation->getLiquidationByRequestId($requests[0]);
        $center_consecutive = $liq[0]['center_consecutive'];
        $arrayDay = explode('-', $liq[0]['print_date']);

        $html2 = '<table width="700" border="1">
                      <tr>
                        <td colspan="7"><div align="center">
                          <p align="center"><strong>Desglose  de dietas en efectivo CUC por trabajador</strong></p>
                        </div></td>
                      </tr>
                      <tr>
                        <td width="6%"><div align="center">No. anticipo</div></td>
                        <td width="39%"><div align="center"><strong>Nombre del trabajador</strong></div></td>
                        <td width="11%"><div align="center"><strong>Días estimados de viaje</strong></div></td>
                        <td width="11%"><div align="center"><strong>Importe de dieta entregada</strong></div></td>
                        <td width="11%"><div align="center"><strong>Días reales de viaje</strong></div></td>
                        <td width="11%"><div align="center"><strong>Importe de dieta utilizada</strong></div></td>
                        <td width="11%"><div align="center"><strong>Importe de dieta devuelta</strong></div></td>
                      </tr>';
        $totalDias = 0;
        $totalDinero = 0;
        $innerHtml = '';
        foreach ($workers as $value) {
            $innerHtml .= '<tr>
                        <td><div align="center">' . $center_consecutive . '</div></td>
                        <td> ' . $value['nombre'] . '</td>
                        <td><div align="center">' . $value['dias'] . '</div></td>
                        <td><div align="center">' . $this->formatDecimalMoney($value['importe']) . '</div></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>';
            $totalDias += $value['dias'];
            $totalDinero += $value['importe'];
        }
        $lastRow = '<tr>
                        <td><div align="center"><strong>TOTAL</strong></div></td>
                        <td>&nbsp;</td>
                        <td><div align="center"><strong>' . $totalDias . '</strong></div></td>
                        <td><div align="center"><strong>' . $this->formatDecimalMoney($totalDinero) . '</strong></div></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>
                    </table>';
        $tableDesglose = $html2 . $innerHtml . $lastRow; // ;
	 $this->liquidation->setAmountEstimated($requests[0], $totalDinero);
        

        //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	 $pdf = new TCPDF('L', PDF_UNIT, 'LETTER', true, 'UTF-8', false);

        $pdf->AddPage();
        $pdf->SetFont('times', '', 8);
        $html = '<table width="700" border="1">
                    <tr>
                      <td width="50%" colspan="3" rowspan="4" align="left" valign="top" class="Pdf"><br /> Centro contable: ' . $data->center_name . ' <br /> Área de trabajo: ' . $data->request_area . ' <br /> Solicitud No. ' . $data->request_consecutive . '<br /></td>                    
                      <td width="35%" colspan="3" rowspan="4" align="center" valign="top" class="Pdf"><strong>SC-3-02 <br />ANTICIPO Y LIQUIDACION  DE <br />GASTOS DE VIAJES NACIONALES <br />EN CUC</strong></td>
                      <td width="15%" colspan="4"><img src="images/logo.png" width="100" height="36" /></td>
                    </tr>
                    <tr>
                      <td width="15%" colspan="4"><div align="center"><span class="Pdf">Fecha de emisión</span></div></td>
                    </tr>
                    <tr>
                      <td width="5%"><div align="center"><span class="Pdf">D</span></div></td>
                      <td width="5%" ><div align="center"><span class="Pdf">M</span></div></td>
                      <td width="5%" colspan="2" ><div align="center"><span class="Pdf">A</span></div></td>
                    </tr>
                    <tr>
                      <td><div align="center"><span class="Pdf">' . substr($arrayDay[2], 0, 2) . '</span></div></td>
                      <td><div align="center"><span class="Pdf">' . $arrayDay[1] . '</span></div></td>
                      <td colspan="2"><div align="center"><span class="Pdf">' . $arrayDay[0] . '</span></div></td>
                    </tr>
                    <tr>
                      <td width="65%" colspan="5" rowspan="2" valign="top" class="Pdf">Nombre completo y Apellidos: ' . $nombreTrabajador . '</td>
                      <td width="35%" colspan="5"><div class="Pdf" align="center">CLASIFICACION</div></td>
                    </tr>
                    <tr>
                      <td width="30%" colspan="3" class="Pdf"> Fuera de la localidad (Extranjero).</td>
                      <td width="5%" colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="65%" colspan="5" rowspan="7" valign="top" class="Pdf"> Labor a realizar o detalle del pago: ' . $data->request_details . '</td>
                      <td width="30%" colspan="3"><span class="Pdf"> En la localidad.(Dentro del país).</span></td>
                      <td width="5%" colspan="2"><div align="center"><span class="Pdf">X</span></div></td>
                    </tr>
                    <tr>
                      <td width="20%"><div align="center"><span class="Pdf">FECHA</span></div></td>
                      <td width="5%" ><div align="center"><span class="Pdf">D</span></div></td>
                      <td width="5%" ><div align="center"><span class="Pdf">M</span></div></td>
                      <td width="5%" colspan="2"><div align="center"><span class="Pdf">HORA</span></div></td>
                    </tr>
                    <tr>
                      <td width="20%" ><div align="left"><span class="Pdf"> Salida estimada</span></div></td>
                      <td width="5%" ><div align="center"><span class="Pdf">' . $fechaEntrada[2] . '</span></div></td>
                      <td width="5%" ><div align="center"><span class="Pdf">' . $fechaEntrada[1] . '</span></div></td>
                      <td width="5%" colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="20%" ><div align="left"><span class="Pdf"> Regreso estimado</span></div></td>
                      <td width="5%" ><div align="center"><span class="Pdf">' . $fechaSalida[2] . '</span></div></td>
                      <td width="5%" ><div align="center"><span class="Pdf">' . $fechaSalida[1] . '</span></div></td>
                      <td width="5%" colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="20%"><div align="left"><span class="Pdf"> Salida Real</span></div></td>
                      <td width="5%" >&nbsp;</td>
                      <td width="5%" >&nbsp;</td>
                      <td width="5%" colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="20%"><div align="left"><span class="Pdf"> Regreso Real</span></div></td>
                      <td width="5%" >&nbsp;</td>
                      <td width="5%" >&nbsp;</td>
                      <td width="5%" colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="35%" colspan="5"><div align="center"><span class="Pdf">Días de Viaje</span></div></td>
                    </tr>
                    <tr>
                      <td width="50%" colspan="3"><div align="center"><span class="Pdf">AUTORIZADO</span></div></td>
                      <td width="7%"><div align="center"><span class="Pdf">DIA</span></div></td>
                      <td width="8%"><div align="center"><span class="Pdf">MES</span></div></td>
                      <td width="30%" colspan="3"><span class="Pdf"> Dias estimados</span></td>
                      <td width="5%" colspan="2"><div align="center"></div></td>
                    </tr>
                    <tr>
                      <td width="25%" rowspan="2" valign="top"><span class="Pdf"> Entrega:</span></td>
                      <td width="25%" colspan="2" valign="top" rowspan="2"><span class="Pdf"> Liquidación:</span></td>
                      <td rowspan="2">&nbsp;</td>
                      <td rowspan="2">&nbsp;</td>
                      <td width="30%" colspan="3"><span class="Pdf"> Dias Reales</span></td>
                      <td width="5%" colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="30%" colspan="3"><span class="Pdf"> Dias hospedados reales</span></td>
                      <td width="5%" colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="20%" rowspan="2" valign="top"><span class="Pdf"> Recibido</span></td>
                      <td width="7%" valign="center"><div align="center"><span class="Pdf">Dia</span></div></td>
                      <td width="7%" valign="center" class="Pdf"><div align="center">Mes</div></td>
                      <td width="14%" class="Pdf"><div align="center">Concepto</div></td>
                      <td width="10%" class="Pdf"><div align="center">Total</div></td>
                      <td width="12%" colspan="2"><div align="center">
                      <span class="Pdf">Dieta</span></div></td>
                      <td width="10%" class="Pdf"><div align="center">Hosp</div></td>
                      <td width="10%" class="Pdf"><div align="center">Otros </div></td>
                      <td width="10%" class="Pdf"><div align="center">Transp.</div></td>
                    </tr>
                    <tr>
                      <td valign="top">&nbsp;</td>
                      <td valign="top" class="Pdf">&nbsp;</td>
                      <td class="Pdf"><div align="center">Entregado</div></td>
                      <td class="Pdf"><div align="center">' . $this->formatDecimalMoney($totalDinero) . '</div></td>
                      <td colspan="2"><div align="center">' . $this->formatDecimalMoney($totalDinero) . '</div></td>
                      <td class="Pdf">&nbsp;</td>
                      <td class="Pdf">&nbsp;</td>
                      <td class="Pdf">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="20%" rowspan="3" valign="top"><div align="left" class="Pdf"> Liquidado</div></td>
                      <td width="7%" valign="center"><div align="center"><span class="Pdf">Dia</span></div></td>
                      <td width="7%" valign="center" class="Pdf"><div align="center">Mes</div></td>
                      <td width="14%" class="Pdf"><div align="center">Utilizado</div></td>
                      <td width="10%" class="Pdf">&nbsp;</td>
                      <td width="12%" colspan="2">&nbsp;</td>
                      <td width="10%" class="Pdf">&nbsp;</td>
                      <td width="10%" class="Pdf">&nbsp;</td>
                      <td width="10%" class="Pdf">&nbsp;</td>
                    </tr>
                    <tr>
                      <td rowspan="2" valign="top">&nbsp;</td>
                      <td rowspan="2" valign="top">&nbsp;</td>
                      <td width="14%" class="Pdf"><div align="center">Devuelto</div></td>
                      <td width="10%" class="Pdf">&nbsp;</td>
                      <td width="12%" colspan="2">&nbsp;</td>
                      <td width="10%" class="Pdf">&nbsp;</td>
                      <td width="10%" class="Pdf">&nbsp;</td>
                      <td width="10%" class="Pdf">&nbsp;</td>
                    </tr>
                    <tr>
                      <td class="Pdf"><div align="center">A. Entreg.</div></td>
                      <td class="Pdf">&nbsp;</td>
                      <td colspan="2">&nbsp;</td>
                      <td class="Pdf">&nbsp;</td>
                      <td class="Pdf">&nbsp;</td>
                      <td class="Pdf">&nbsp;</td>
                    </tr>
                    <tr>
                      <td width="20%" rowspan="2" valign="top" class="Pdf"> Custodio</td>
                      <td valign="top"><div align="center">Dia</div></td>
                      <td valign="top"><div align="center">Mes</div></td>
                      <td colspan="6"> Anotado por:</td>
                      <td rowspan="2" valign="top"> No. ' . $center_consecutive . '</td>
                    </tr>
                    <tr>
                      <td valign="top">&nbsp;</td>
                      <td valign="top" class="Pdf">&nbsp;</td>
                      <td valign="top" colspan="6" class="Pdf"> Registrado por:</td>
                    </tr>
                  </table>';
        // output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
        //$pdf->AddPage();
        $pdf->writeHTML($tableDesglose, true, false, true, false, '');
        $pdf->Output('MODELO SC-3-02 - ANTICIPO Y LIQUIDACION DE GASTOS-' . $nombreTrabajador . '-' . $fecha->now() . '.pdf', 'D');
    }

    private function getRequestDataFromAdvance($array)
    {
	 if ($this->session->userdata('person_id') == 0) {
            return "{success: false, errors: { reason: 'Su sesi&oacute;n ha expirado. Ingrese de nuevo al sistema.' }, prorogation: 'no'}";
        }

        $data = array();
        $fecha = new Dates ();
        foreach ($array as $value) {
            $request = $this->conn->getAdvanceLiquidationById($value);
            /*if ($request->lodging_prorogate == 1) {
                $diasHospedaje = $fecha->diasEntreFechas($request->diet_entrancedate, $request->diet_exitdate);
            } else {*/
                $diasHospedaje = $fecha->diasEntreFechas($request->diet_entrancedate, $request->diet_exitdate) + 1;
            //}
            $value = array(
		  'consecutivo' => $request->center_consecutive,
                'nombre' => $request->person_name . ' ' . $request->person_lastname . ' ' . $request->person_secondlastname,
                'dias' => $diasHospedaje,
                'importe' => $diasHospedaje * 6.5);
            array_push($data, $value);
        }
        return $data;
    }

    function getLiquidationGrid()
    {
        $dateStart = $this->input->post('dateStart');
        $dateEnd = $this->input->post('dateEnd');
        return $this->liquidation->getLiquidationGrid($dateStart, $dateEnd);
    }

    function getLiquidationById($request_id)
    {
        $data = $this->liquidation->getLiquidationById($request_id);
        die("{data : " . json_encode($data) . "}");
        //echo "<pre>"; print_r($data); echo "</pre>";
    }

    public function insertLiquidation()
    {
        $centinela = new Centinela ();
        $flag = $centinela->accessTo('accounting/accounting_advanceliquidation');
        if ($flag) {
            $result = $this->liquidation->insertLiquidation();
            die("{success : $result}");
        } else {
            $this->redirectError();
        }
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
