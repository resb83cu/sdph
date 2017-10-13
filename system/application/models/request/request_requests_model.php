<?php

class Request_requests_model extends Model {

    const TABLE_NAME = 'request_requests';

    function __construct() {
        parent::__construct();
    }

    /**
     * Funcion que devuelve los valores de la tabla lodging_request
     *
     * @param int $hasta
     * @param int $desde
     * @return array
     */
    public function getData($to, $from, $dateStart, $dateEnd) {
        $centinela = new Centinela ( );
        $roll_id = $centinela->get_roll_id();
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_motives_model');
        $this->load->model('conf/conf_costcenters_model');
        $this->load->model('request/request_tickets_model');
        $request_tickets_table = 'request_tickets';
        $request_lodgings_table = 'request_lodgings';
        $this->db->select('request_requests.request_id, 
                            request_requests.request_date,
				request_requests.request_consecutive,
                            request_requests.request_details,
                            request_requests.person_idrequestedby,
                            request_requests.person_idlicensedby,
                            request_requests.center_id,
                            request_requests.motive_id,
                            request_requests.person_idworker,
                            conf_motives.motive_name,
                            conf_costcenters.center_name,
                            request_lodgings.request_id as lodging,
                            request_lodgings.lodging_entrancedate,
                            request_lodgings.lodging_state,
                            request_tickets.request_id as ticket');
        $this->db->from(self::TABLE_NAME);
        $this->db->join($request_tickets_table, $request_tickets_table . '.request_id = ' . self::TABLE_NAME . '.request_id', 'left');
        $this->db->join($request_lodgings_table, $request_lodgings_table . '.request_id = ' . self::TABLE_NAME . '.request_id', 'left');
        $this->db->join ( Conf_motives_model::TABLE_NAME, Conf_motives_model::TABLE_NAME . '.motive_id = ' . self::TABLE_NAME . '.motive_id', 'inner' );
        $this->db->join ( Conf_costcenters_model::TABLE_NAME, Conf_costcenters_model::TABLE_NAME . '.center_id = ' . self::TABLE_NAME . '.center_id', 'inner' );
        if ($roll_id < 5) {
            $this->db->where(self::TABLE_NAME . '.person_idrequestedby =', $centinela->get_person_id());
            /* $this->db->where ( $request_tickets_table . '.province_idfrom =', $centinela->get_province_id () );
              $this->db->or_where ( $request_tickets_table . '.province_idto =', $centinela->get_province_id () );
              $this->db->or_where ( $request_lodgings_table . '.province_idlodging =', $centinela->get_province_id () ); */
        }
        if (!empty($dateStart) && !empty($dateEnd)) {
            $this->db->where(self::TABLE_NAME . '.request_date >=', $dateStart . ' 00:00:000');
            $this->db->where(self::TABLE_NAME . '.request_date <=', $dateEnd . ' 23:59:000');
        }
        //$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . self::TABLE_NAME . '.province_idlodging', 'inner' );
        $this->db->limit($to, $from);
        $this->db->order_by(self::TABLE_NAME . '.request_date', 'desc');
        $this->db->distinct();
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $temp = $row->person_idworker;
                $person_worker = Person_persons_model::getNameById($temp);
                $temp = $row->person_idlicensedby;
                $person_licensedby = Person_persons_model::getNameById($temp);
                $temp = $row->person_idrequestedby;
                $person_requestedby = Person_persons_model::getNameById($temp);
                //$temp = $row->center_id;
                $center_name = $row->center_name;//Conf_costcenters_model::getNameById($temp);
                //$temp = $row->motive_id;
                $motive_name = $row->motive_name;//Conf_motives_model::getNameById($temp);
                $tickets = Request_tickets_model::getById($row->request_id);
                $lodging = 'NO';
                $ticket = 'NO';
                if (!empty($row->lodging)) {
                    $lodging = 'SI';
                }
                if (!empty($row->ticket)) {
                    $ticket = 'SI';
                }
                $ticket_date = count($tickets) == 0 ? null : $tickets[0]['ticket_date'];
                $ticket_state = count($tickets) == 0 ? null : $tickets[0]['ticket_state'];
                $value [] = array('request_id' => $row->request_id,
                    'request_date' => $row->request_date,
                    'request_details' => $row->request_details,
                    'request_consecutive' => $row->request_consecutive,
                    'person_requestedby' => $person_requestedby,
                    'center_name' => $center_name,
                    'person_worker' => $person_worker,
                    'person_licensedby' => $person_licensedby,
                    'motive_name' => $motive_name,
                    'lodging' => $lodging,
                    'lodging_entrancedate' => $row->lodging_entrancedate,
                    'lodging_state' => $row->lodging_state,
                    'ticket' => $ticket,
                    'ticket_date' => $ticket_date,
                    'ticket_state' => $ticket_state);
            }
        }
        //if (!empty($dateStart) && !empty($dateEnd)) {
            $cant = $this->getDataCount($dateStart, $dateEnd);
        /*}else {
            $cant = count($value);
        }*/
        echo ( "{count : " . $cant . ", data : " . json_encode($value) . "}" );
    }

    public function getDataChangeMotive($to, $from, $dateStart, $dateEnd, $motive) {
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_motives_model');
        $this->db->select('request_requests.request_id,
                                    request_requests.request_details,
                                    request_requests.request_date,
                                    request_requests.person_idrequestedby,
                                    request_requests.motive_id,
                                    request_requests.center_id,
                                    request_requests.person_idworker');
        $this->db->from(self::TABLE_NAME);
        if (!empty($dateStart) && !empty($dateEnd)) {
            $this->db->where(self::TABLE_NAME . '.request_date >=', $dateStart);
            $this->db->where(self::TABLE_NAME . '.request_date <=', $dateEnd);
        }
        if (!empty($motive)) {
            $this->db->where(self::TABLE_NAME . '.motive_id', $motive);
        }
        $this->db->limit($to, $from);
        $this->db->order_by(self::TABLE_NAME . '.request_date', 'desc');
        $this->db->distinct();
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $temp = $row->person_idworker;
                $person_worker = Person_persons_model::getNameById($temp);
                $temp = $row->person_idrequestedby;
                $person_requestedby = Person_persons_model::getNameById($temp);
                $temp = $row->motive_id;
                $motive_name = Conf_motives_model::getNameById($temp);
                $temp = $row->center_id;
                $center_name = Conf_costcenters_model::getNameById($temp);
                $value [] = array('request_id' => $row->request_id,
                    'request_date' => $row->request_date,
                    'request_details' => $row->request_details,
                    'person_requestedby' => $person_requestedby,
                    'center_name' => $center_name,
                    'person_worker' => $person_worker,
                    'motive_name' => $motive_name);
            }
        }
        $cant = $this->getDataCountChangeMotive($dateStart, $dateEnd, $motive);
        echo ( "{count : " . $cant . ", data : " . json_encode($value) . "}" );
    }

    /**
     * funcion que devuelve la cantidad de registros en la tabla ejemplo
     *
     */
    public function getDataCount($dateStart, $dateEnd) {
        $centinela = new Centinela ( );
        $roll_id = $centinela->get_roll_id();
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_motives_model');
        $this->load->model('conf/conf_costcenters_model');
        $request_tickets_table = 'request_tickets';
        $request_lodgings_table = 'request_lodgings';
        $this->db->select('request_requests.request_id,
							request_lodgings.request_id as lodging,
							request_tickets.request_id as ticket');
        $this->db->from(self::TABLE_NAME);
        $this->db->join($request_tickets_table, $request_tickets_table . '.request_id = ' . self::TABLE_NAME . '.request_id', 'left');
        $this->db->join($request_lodgings_table, $request_lodgings_table . '.request_id = ' . self::TABLE_NAME . '.request_id', 'left');
        if ($roll_id < 5) {
            $this->db->where(self::TABLE_NAME . '.person_idrequestedby =', $centinela->get_person_id());
            /* $this->db->where ( $request_tickets_table . '.province_idfrom =', $centinela->get_province_id () );
              $this->db->or_where ( $request_tickets_table . '.province_idto =', $centinela->get_province_id () );
              $this->db->or_where ( $request_lodgings_table . '.province_idlodging =', $centinela->get_province_id () ); */
        }
        if (!empty($dateStart) && !empty($dateEnd)) {
            $this->db->where(self::TABLE_NAME . '.request_date >=', $dateStart . ' 00:00:000');
            $this->db->where(self::TABLE_NAME . '.request_date <=', $dateEnd . ' 23:59:000');
        }
        $this->db->distinct();
        return $this->db->count_all_results();
    }

    public function getDataCountChangeMotive($dateStart, $dateEnd, $motive) {
        $this->db->select('request_id');
        $this->db->from(self::TABLE_NAME);
        if (!empty($dateStart) && !empty($dateEnd)) {
            $this->db->where(self::TABLE_NAME . '.request_date >=', $dateStart);
            $this->db->where(self::TABLE_NAME . '.request_date <=', $dateEnd);
        }
        if (!empty($motive)) {
            $this->db->where(self::TABLE_NAME . '.motive_id', $motive);
        }
        $this->db->distinct();
        return $this->db->count_all_results();
    }

    function changeMotive($chain, $motive) {
        $data = array(
            'motive_id' => $motive
        );
        $this->db->where_in('request_id', explode('-', $chain));
        return $this->db->update(self::TABLE_NAME, $data);
    }

    public function verifyProrogation($lodging_entrancedate, $person_idworker, $province_idlodging) {
        $request_lodgings_table = 'request_lodgings';
        $this->db->select('request_requests.request_id,
							request_requests.request_date,
							request_requests.person_idrequestedby,
							request_requests.person_idworker,
							request_lodgings.lodging_entrancedate,
							request_lodgings.lodging_exitdate,
							request_lodgings.province_idlodging');
        $this->db->from(self::TABLE_NAME);
        $this->db->join($request_lodgings_table, $request_lodgings_table . '.request_id = ' . self::TABLE_NAME . '.request_id', 'left');
        $this->db->where(self::TABLE_NAME . '.person_idworker', $person_idworker);
        $this->db->where($request_lodgings_table . '.lodging_exitdate', $lodging_entrancedate);
        $this->db->where($request_lodgings_table . '.lodging_canceled', 0);
        return $this->db->count_all_results();
    }

    public function canInsertLodging($lodging_entrancedate, $lodging_exitdate, $person_idworker) {
        $pgsql = "SELECT 
						request_requests.request_id,
						request_requests.request_date,
						request_requests.person_idrequestedby,
						request_requests.person_idworker,
						request_lodgings.lodging_entrancedate,
						request_lodgings.lodging_exitdate,
						request_lodgings.province_idlodging,
						request_lodgings.lodging_canceled,
						lodging_edit.lodging_noshow,
						accounting_advanceliquidation.liquidation_liquidated
					FROM request_requests
					LEFT JOIN request_lodgings ON request_requests.request_id = request_lodgings.request_id
					LEFT JOIN lodging_edit ON request_lodgings.request_id = lodging_edit.request_id
					LEFT JOIN accounting_advanceliquidation ON lodging_edit.request_id = accounting_advanceliquidation.request_id
					WHERE ( request_requests.person_idworker = ? )
					AND (request_lodgings.lodging_canceled = 0)
					AND (request_requests.diet_entrancedate >= '2014-08-01') 
					AND ((request_lodgings.lodging_exitdate < ? AND request_lodgings.lodging_exitdate > ?) 
					OR (request_lodgings.lodging_entrancedate <= ? AND request_lodgings.lodging_exitdate >= ?) 
					OR (request_lodgings.lodging_entrancedate < ? AND request_lodgings.lodging_entrancedate > ?)
					OR (request_lodgings.lodging_entrancedate > ? AND request_lodgings.lodging_exitdate < ?))";
        $result = $this->db->query($pgsql, array($person_idworker, $lodging_exitdate, $lodging_entrancedate,
            $lodging_entrancedate, $lodging_exitdate,
            $lodging_exitdate, $lodging_entrancedate,
            $lodging_entrancedate, $lodging_exitdate));
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                if ($row->lodging_noshow == 'on' || $row->lodging_canceled == 1) {
                    return 0;
                } else {
                    return 1;
                }
            }
        }
    }

    public function canInsertDietReal($entrancedate, $exitdate, $person_idworker)
    {
        $pgsql = "SELECT
						accounting_advanceliquidation.request_id,
						conf_costcenters.center_name,
						conf_costcenters.center_sap,
						accounting_advanceliquidation.center_consecutive,
						accounting_advanceliquidation.diet_entrancedate_real,
						accounting_advanceliquidation.diet_exitdate_real
                    FROM accounting_advanceliquidation
                    inner JOIN conf_costcenters ON accounting_advanceliquidation.center_idadvance = conf_costcenters.center_id
                    WHERE ( accounting_advanceliquidation.person_idworker = ? )
                    AND (accounting_advanceliquidation.liquidation_used != 'CANCELADO')
                    AND (accounting_advanceliquidation.liquidation_liquidated = TRUE)
                    AND (accounting_advanceliquidation.diet_entrancedate_real >= '2014-08-01')
					AND ((accounting_advanceliquidation.diet_exitdate_real < ? AND accounting_advanceliquidation.diet_exitdate_real >= ?)
					OR (accounting_advanceliquidation.diet_entrancedate_real <= ? AND accounting_advanceliquidation.diet_exitdate_real >= ?)
					OR (accounting_advanceliquidation.diet_entrancedate_real < ? AND accounting_advanceliquidation.diet_entrancedate_real > ?)
					OR (accounting_advanceliquidation.diet_entrancedate_real > ? AND accounting_advanceliquidation.diet_exitdate_real < ?))";
        $result = $this->db->query($pgsql, array($person_idworker, $exitdate, $entrancedate,
            $entrancedate, $exitdate,
            $exitdate, $entrancedate,
            $entrancedate, $exitdate));
        return $result->result();
    }

    public function canInsertDietEstimado($entrancedate, $exitdate, $person_idworker)
    {
        $pgsql = "SELECT
						accounting_advanceliquidation.request_id,
						conf_costcenters.center_name,
						conf_costcenters.center_sap,
						accounting_advanceliquidation.center_consecutive,
						accounting_advanceliquidation.lodging_entrancedate,
						accounting_advanceliquidation.lodging_exitdate
                    FROM accounting_advanceliquidation
                    inner JOIN conf_costcenters ON accounting_advanceliquidation.center_idadvance = conf_costcenters.center_id
                    WHERE ( accounting_advanceliquidation.person_idworker = ? )
                    AND (accounting_advanceliquidation.liquidation_liquidated = FALSE )
                    AND (accounting_advanceliquidation.lodging_entrancedate >= '2014-08-01')
					AND ((accounting_advanceliquidation.lodging_exitdate < ? AND accounting_advanceliquidation.lodging_exitdate >= ?)
					OR (accounting_advanceliquidation.lodging_entrancedate <= ? AND accounting_advanceliquidation.lodging_exitdate >= ?)
					OR (accounting_advanceliquidation.lodging_entrancedate < ? AND accounting_advanceliquidation.lodging_entrancedate > ?)
					OR (accounting_advanceliquidation.lodging_entrancedate > ? AND accounting_advanceliquidation.lodging_exitdate < ?))";
        $result = $this->db->query($pgsql, array($person_idworker, $exitdate, $entrancedate,
            $entrancedate, $exitdate,
            $exitdate, $entrancedate,
            $entrancedate, $exitdate));
        return $result->result();
    }

    public function canInsertDiet($entrancedate, $exitdate, $person_idworker)
    {
        $pgsql = "SELECT
						request_requests.request_id,
						conf_costcenters.center_name,
						conf_costcenters.center_sap,
						request_requests.request_consecutive,
						request_requests.diet_entrancedate,
						request_requests.diet_exitdate
                    FROM request_requests
                    inner JOIN conf_costcenters ON request_requests.center_idadvance = conf_costcenters.center_id
                    LEFT JOIN request_lodgings ON request_requests.request_id = request_lodgings.request_id
                    WHERE ( request_requests.person_idworker = ? )
                    AND ( request_requests.advance_requested = FALSE )
                    AND (request_lodgings.lodging_canceled = 0)
                    AND (request_requests.diet_entrancedate >= '2014-08-01')
					AND ((request_requests.diet_exitdate < ? AND request_requests.diet_exitdate >= ?)
					OR (request_requests.diet_entrancedate <= ? AND request_requests.diet_exitdate >= ?)
					OR (request_requests.diet_entrancedate < ? AND request_requests.diet_entrancedate > ?)
					OR (request_requests.diet_entrancedate > ? AND request_requests.diet_exitdate < ?))";
        $result = $this->db->query($pgsql, array($person_idworker, $exitdate, $entrancedate,
            $entrancedate, $exitdate,
            $exitdate, $entrancedate,
            $entrancedate, $exitdate));
        return $result->result();
    }

    public function lodgingEdit($request_id) {
        $this->db->select(
                'request_lodgings.request_id,
                        request_lodgings.lodging_state'
        );
        $this->db->from('request_lodgings');
        $this->db->where('request_lodgings.lodging_state = 1');
        $this->db->where('request_lodgings.request_id', $request_id);
        return $this->db->count_all_results();
    }

    public function ticketEdit($request_id) {
        $this->db->select(
                'request_tickets.request_id,
                        request_tickets.lodging_state'
        );
        $this->db->from('request_tickets');
        $this->db->where('request_tickets.ticket_state = 1');
        $this->db->where('request_tickets.request_id', $request_id);
        return $this->db->count_all_results();
    }

    public function canInsertTicket($ticket_date, $person_idworker, $province_idfrom) {
        $request_tickets_table = 'request_tickets';
        $this->db->select('request_requests.request_id,
							request_requests.request_date,
							request_requests.person_idworker,
							request_tickets.ticket_date,
							request_tickets.province_idfrom,
							request_tickets.ticket_cancel');
        $this->db->from(self::TABLE_NAME);
        $this->db->join($request_tickets_table, $request_tickets_table . '.request_id = ' . self::TABLE_NAME . '.request_id', 'left');
        $this->db->where(self::TABLE_NAME . '.person_idworker', $person_idworker);
        $this->db->where($request_tickets_table . '.ticket_date', $ticket_date);
        $this->db->where($request_tickets_table . '.province_idfrom', $province_idfrom);
        $this->db->where($request_tickets_table . '.ticket_cancel', 0);
        return $this->db->count_all_results();
    }

    /**
     * Esta es la funcion encargada de insertar los tickets
     *
     * @return boolean
     */
    public function insert() {
        $this->load->model('request/request_lodgings_model');
        $this->load->model('request/request_tickets_model');
        $this->load->model('conf/conf_costcenters_model');
        $request_id = $this->input->post('request_id');
        $person_idworker = $this->input->post('person_idworker');

        if ($this->session->userdata('person_id') == 0) {
            return "{success: false, errors: { reason: 'Su sesi&oacute;n ha expirado. Ingrese de nuevo al sistema.' }, prorogation: 'no'}";
        }

        $dates = new Dates ( );
        $request ['request_date'] = $dates->now();
        $request ['request_details'] = $this->input->post('request_details');
        $request ['person_idrequestedby'] = $this->session->userdata('person_id');
        $center_id = $this->input->post('center_id');
        //MODIFICAR EN EL SERVER
        $center = empty($center_id) ? $this->session->userdata('center_id') : $center_id;
        $request ['center_id'] = $center;
        $centerAdvanced = $this->input->post('center_idadvance');
        $diet_entrancedate = $this->input->post('diet_entrancedate');
        $diet_exitdate = $this->input->post('diet_exitdate');
        //FIN MODIFICAR EN EL SERVER
        $request ['person_idlicensedby'] = empty($center_id) ? $this->session->userdata('person_idparent') : Conf_costcenters_model::getDirectorById($center_id);
        $request ['person_idworker'] = $person_idworker;
        $request ['motive_id'] = $this->input->post('motive_id');
        $request ['request_inversiontask'] = $this->input->post('request_inversiontask');
        $request ['request_consecutive'] = $this->input->post('request_consecutive');
        $request ['request_area'] = $this->input->post('request_area');
        $request ['person_groupresponsable'] = strtoupper($this->input->post('person_groupresponsable'));
        $request ['center_idadvance'] = empty($centerAdvanced) ? null : $centerAdvanced;
        $request ['diet_entrancedate'] = empty($diet_entrancedate) ? null : $diet_entrancedate;
        $request ['diet_exitdate'] = empty($diet_exitdate) ? null : $diet_exitdate;
        //lodging data
        $lodging_entrancedate = $this->input->post('lodging_entrancedate');
        $lodging_exitdate = $this->input->post('lodging_exitdate');
        $transport_idlodging = $this->input->post('transport_idlodging');
        $transport_idreturnlodging = $this->input->post('transport_idreturnlodging');
        $province_idlodging = $this->input->post('province_idlodging');
        $lodging_requestreinforceddiet = $this->input->post('lodging_requestreinforceddiet');
        $lodging_requestelongationdiet = $this->input->post('lodging_requestelongationdiet');
        //ticket exit
        $ticket_idexit = $this->input->post('ticket_idexit');
        $ticket_exitdate = $this->input->post('ticket_exitdate');
        $transport_idexit = $this->input->post('transport_idexit');
        $transport_itinerary = $this->input->post('transport_itinerary');
        $province_idfrom = $this->input->post('province_idfrom');
        $province_idto = $this->input->post('province_idto');
        //ticket return
        $ticket_idreturn = $this->input->post('ticket_idreturn');
        $ticket_returndate = $this->input->post('ticket_returndate');
        $transport_idreturn = $this->input->post('transport_idreturn');
        $transport_return_itinerary = $this->input->post('transport_return_itinerary');
        $province_idfrom_return = $this->input->post('province_idfrom_return');
        $province_idto_return = $this->input->post('province_idto_return');

        if (empty($request_id)) {
            $prorogation = 0;
            $canInsertLodging = 0;
            $canInsertTicket = 0;
            $canInsertTicketReturn = 0;
            //$noConsecutive = $this->verifyConsecutive($this->input->post('request_consecutive'), $center, $person_idworker);
            if (!empty($ticket_exitdate)) {
                $canInsertTicket = self::canInsertTicket($ticket_exitdate, $person_idworker, $province_idfrom);
                if ($canInsertTicket > 0) {
                    return "{success: false, errors: { reason: 'Ya existe una solicitud de pasaje para este trabajador en la fecha señalada.' }, prorogation: 'no'}";
                }
            }
            if (!empty($ticket_returndate)) {
                $canInsertTicketReturn = self::canInsertTicket($ticket_returndate, $person_idworker, $province_idfrom_return);
                if ($canInsertTicketReturn > 0) {
                    return "{success: false, errors: { reason: 'Ya existe una solicitud de pasaje para este trabajador en la fecha señalada.' }, prorogation: 'no'}";
                }
            }
            if (!empty($diet_entrancedate)) {
                $canInsertDiet = $this->canInsertDiet($diet_entrancedate, $diet_exitdate, $person_idworker);
                if (count($canInsertDiet) > 0) {
                    $inicioReq = $canInsertDiet[0]->diet_entrancedate;
                    $finReq = $canInsertDiet[0]->diet_exitdate;
                    $ccReq = $canInsertDiet[0]->center_sap . ' / ' . $canInsertDiet[0]->center_name;
                    return "{success: false, errors: { reason: 'Este trabajador tiene una solicitud de dieta en la fecha comprendida entre: $inicioReq y $finReq en el Centro Contable $ccReq, aunque no se ha generado el Anticipo todavia.'}, prorogation: 'no'}";
                }
                $canInsertDietEstimado = $this->canInsertDietEstimado($diet_entrancedate, $diet_exitdate, $person_idworker);
                if (count($canInsertDietEstimado) > 0) {
                    $inicio = $canInsertDietEstimado[0]->lodging_entrancedate;
                    $fin = $canInsertDietEstimado[0]->lodging_exitdate;
                    $cc = $canInsertDietEstimado[0]->center_sap . ' / ' . $canInsertDietEstimado[0]->center_name;
                    return "{success: false, errors: { reason: 'Este trabajador tiene una solicitud de dieta en la fecha comprendida entre: $inicio y $fin en el Centro Contable $cc.'}, prorogation: 'no'}";
                }
                $canInsertDietReal = $this->canInsertDietReal($diet_entrancedate, $diet_exitdate, $person_idworker);
                if (count($canInsertDietReal) > 0) {
                    $inicioReal = $canInsertDietReal[0]->diet_entrancedate_real;
                    $finReal = $canInsertDietReal[0]->diet_exitdate_real;
                    $ccReal = $canInsertDietReal[0]->center_sap . ' / ' . $canInsertDietReal[0]->center_name;
                    return "{success: false, errors: { reason: 'Este trabajador tiene una solicitud de dieta en la fecha comprendida entre: $inicioReal y $finReal en el Centro Contable $ccReal.'}, prorogation: 'no'}";
                }
            }
            if (!empty($lodging_entrancedate)) {
                $canInsertLodging = self::canInsertLodging($lodging_entrancedate, $lodging_exitdate, $person_idworker);
                $prorogation = self::verifyProrogation($lodging_entrancedate, $person_idworker, $province_idlodging);
                if ($canInsertLodging > 0) {
                    return "{success: false, errors: { reason: 'Ya existe una solicitud de hospedaje para este trabajador en el intervalo de tiempo señalado.' }, prorogation: 'no'}";
                }
                if ($prorogation > 0) {
                    return "{success: false, errors: { reason: 'La fecha de entrada de la solicitud coincide con la salida del hospedaje del trabajador seleccionado. Desea hacer una pr&oacute;rroga del hospedaje para este trabajador?.' }, prorogation: 'si'}";
                }
            }
            /*if ($noConsecutive > 0) {
                return "{success: false, errors: { reason: 'Ya existe una solicitud con este numero consecutivo para este Centro de Costo.' }, prorogation: 'no'}";
            }*/
            $this->db->trans_begin();
            $re = $this->db->insert(self::TABLE_NAME, $request);
            $request_id = $this->db->insert_id();
            $logs = new Logs ( );
            $myquery = $logs->sqlinsert(self::TABLE_NAME, $request);
            $logs->write(self::TABLE_NAME, 'INSERT', $myquery);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }

            $lodgingInsert = "true";
            $ticketInsert = "true";

            if ($re == true) {
                if (!empty($lodging_entrancedate)) {
                    $lodgingInsert = Request_lodgings_model::insert($request_id, $lodging_entrancedate, $lodging_exitdate, $transport_idlodging, $transport_idreturnlodging, $province_idlodging, $lodging_requestreinforceddiet, $lodging_requestelongationdiet, false);
                }
                if (!empty($ticket_exitdate) && $transport_idexit != 6) {
                    $ticketInsert = Request_tickets_model::insert($ticket_idexit, $request_id, $ticket_exitdate, $transport_idexit, $transport_itinerary, $province_idfrom, $province_idto);
                    /* $ticketInsert = Request_tickets_model::insert($request_id, $ticket_exitdate, $transport_idexit, $transport_itinerary, 
                      $province_idfrom, $province_idto, $ticket_returndate, $transport_idreturn, $transport_return_itinerary,
                      $province_idfrom_return, $province_idto_return); */
                }
                if (!empty($ticket_returndate) && $transport_idreturn != 6) {
                    $ticketInsert = Request_tickets_model::insert($ticket_idreturn, $request_id, $ticket_returndate, $transport_idreturn, $transport_return_itinerary, $province_idfrom_return, $province_idto_return);
                    /* $ticketInsert = Request_tickets_model::insert($request_id, $ticket_exitdate, $transport_idexit, $transport_itinerary, 
                      $province_idfrom, $province_idto, $ticket_returndate, $transport_idreturn, $transport_return_itinerary,
                      $province_idfrom_return, $province_idto_return); */
                }
            }
            if ($lodgingInsert == "true" && $ticketInsert == "true")
                return "true";
            else
                return "false";
        } else {
            $this->db->where('request_id', $request_id);
            $this->db->trans_begin();
            $re = $this->db->update(self::TABLE_NAME, $request);
            $logs = new Logs ( );

            $mywhere = 'where request_id = ' . $request_id;
            $myquery = $logs->sqlupdate(self::TABLE_NAME, $request, $mywhere);

            $logs->write(self::TABLE_NAME, 'UPDATE', $myquery);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }

            $lodgingInsert = "true";
            $ticketInsert = "true";

            if ($this->lodgingEdit($request_id) > 0 || $this->ticketEdit($request_id) > 0) {
                return "{success: false, errors: { reason: 'No se puede modificar la solicitud porque ya ha sido editada.' }, prorogation: 'no'}";
            }

            if ($re == true) {
                if (!empty($lodging_entrancedate)) {
                    $lodgingInsert = Request_lodgings_model::insert($request_id, $lodging_entrancedate, $lodging_exitdate, $transport_idlodging, $transport_idreturnlodging, $province_idlodging, $lodging_requestreinforceddiet, $lodging_requestelongationdiet, false);
                }
                if (!empty($ticket_exitdate)) {
                    $ticketInsert = Request_tickets_model::insert($ticket_idexit, $request_id, $ticket_exitdate, $transport_idexit, $transport_itinerary, $province_idfrom, $province_idto);
                }
                if (!empty($ticket_returndate)) {
                    $ticketInsert = Request_tickets_model::insert($ticket_idreturn, $request_id, $ticket_returndate, $transport_idreturn, $transport_return_itinerary, $province_idfrom_return, $province_idto_return);
                }
                /* if (!empty($ticket_exitdate) || !empty($ticket_returndate)) {
                  $ticketInsert = Request_tickets_model::insert($request_id, $ticket_exitdate, $transport_idexit, $transport_itinerary, $province_idfrom, $province_idto, $ticket_returndate, $transport_idreturn, $transport_return_itinerary, $province_idfrom_return, $province_idto_return);
                  } */
            }
            if ($lodgingInsert == "true" && $ticketInsert == "true")
                return "true";
            else
                return "false";
        }
    }

    public function requestProrogation() {
        $this->load->model('request/request_lodgings_model');
        $this->load->model('request/request_tickets_model');
        //$request_id = $this->input->post ( 'request_id' );
        $person_idworker = $this->input->post('person_idworker');
	$center = $this->input->post('center_idadvance');
        $diet_entrancedate = $this->input->post('diet_entrancedate');
        $diet_exitdate = $this->input->post('diet_exitdate');

        $dates = new Dates ( );
        $request ['request_date'] = $dates->now();
        $request ['request_details'] = $this->input->post('request_details');
        $request ['person_idrequestedby'] = $this->session->userdata('person_id');
        $center_id = $this->input->post('center_id');
        $request ['center_id'] = empty($center_id) ? $this->session->userdata('center_id') : (int)$center_id;
        $request ['person_idlicensedby'] = empty($center_id) ? $this->session->userdata('person_idparent') : Conf_costcenters_model::getDirectorById($center_id);
        $request ['person_idworker'] = (int)$person_idworker;
        $request ['motive_id'] = (int)$this->input->post('motive_id');
        $request ['request_inversiontask'] = $this->input->post('request_inversiontask');
        $request ['request_consecutive'] = (int)$this->input->post('request_consecutive');
        $request ['request_area'] = $this->input->post('request_area');
        $request ['person_groupresponsable'] = strtoupper($this->input->post('person_groupresponsable'));
        $request ['center_idadvance'] = empty($center) ? null : (int)$center;
        $request ['diet_entrancedate'] = empty($diet_entrancedate) ? null : $diet_entrancedate;
        $request ['diet_exitdate'] = empty($diet_exitdate) ? null : $diet_exitdate;
        //lodging data
        $lodging_entrancedate = $this->input->post('lodging_entrancedate');
        $lodging_exitdate = $this->input->post('lodging_exitdate');
        $transport_idlodging = $this->input->post('transport_idlodging');
        $transport_idreturnlodging = $this->input->post('transport_idreturnlodging');
        $province_idlodging = $this->input->post('province_idlodging');
        $lodging_requestreinforceddiet = $this->input->post('lodging_requestreinforceddiet');
        $lodging_requestelongationdiet = $this->input->post('lodging_requestelongationdiet');
	//ticket exit
        $ticket_idexit = $this->input->post('ticket_idexit');
        $ticket_exitdate = $this->input->post('ticket_exitdate');
        $transport_idexit = $this->input->post('transport_idexit');
        $transport_itinerary = $this->input->post('transport_itinerary');
        $province_idfrom = $this->input->post('province_idfrom');
        $province_idto = $this->input->post('province_idto');
        //ticket return
        $ticket_idreturn = $this->input->post('ticket_idreturn');
        $ticket_returndate = $this->input->post('ticket_returndate');
        $transport_idreturn = $this->input->post('transport_idreturn');
        $transport_return_itinerary = $this->input->post('transport_return_itinerary');
        $province_idfrom_return = $this->input->post('province_idfrom_return');
        $province_idto_return = $this->input->post('province_idto_return');
        $this->db->trans_begin();
        $re = $this->db->insert(self::TABLE_NAME, $request);
        $request_id = $this->db->insert_id();

        $prorogate_id = Request_tickets_model::getIdOfProrogateTicket($person_idworker, $lodging_entrancedate);

        $logs = new Logs ( );
        $myquery = $logs->sqlinsert(self::TABLE_NAME, $request);
        $logs->write(self::TABLE_NAME, 'INSERT', $myquery);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        if ($re == true) {
            $res = Request_lodgings_model::insert($request_id, $lodging_entrancedate, $lodging_exitdate, $transport_idlodging, $transport_idreturnlodging, $province_idlodging, $lodging_requestreinforceddiet, $lodging_requestelongationdiet, 1);
            Request_tickets_model::cancelTicket($prorogate_id);
	    if (!empty($ticket_exitdate) && $transport_idexit != 6) {
                $ticketInsert = Request_tickets_model::insert($ticket_idexit, $request_id, $ticket_exitdate, $transport_idexit, $transport_itinerary, $province_idfrom, $province_idto);
            }
            if (!empty($ticket_returndate) && $transport_idreturn != 6) {
                $ticketInsert = Request_tickets_model::insert($ticket_idreturn, $request_id, $ticket_returndate, $transport_idreturn, $transport_return_itinerary, $province_idfrom_return, $province_idto_return);
            }
        }
        if ($res == true)
            return "true";
        else
            return "false";
    }

    public function makeProrogation($r_request_date, $r_request_details, $r_person_idrequestedby, $r_center_id, $r_person_idlicensedby, $r_person_idworker, $r_motive_id, $r_request_inversiontask, $l_lodging_entrancedate, $l_lodging_exitdate, $l_transport_idlodging, $l_transport_idreturnlodging, $l_province_idlodging, $l_lodging_requestreinforceddiet, $l_lodging_requestelongationdiet, $l_lodging_prorogate, $l_lodging_reinforceddiet, $l_lodging_elongationdiet, $l_lodging_noshow, $l_lodging_prorogation, $l_hotel_id, $l_linearity_id, $l_cafeteria_id, $l_person_editedby) {
        $this->load->model('lodging/lodging_edit_model');
        $this->load->model('request/request_lodgings_model');

        //request data
        $request ['request_date'] = $r_request_date;
        $request ['request_details'] = $r_request_details;
        $request ['person_idrequestedby'] = $r_person_idrequestedby;
        $request ['center_id'] = $r_center_id;
        $request ['person_idlicensedby'] = $r_person_idlicensedby;
        $request ['person_idworker'] = $r_person_idworker;
        $request ['motive_id'] = $r_motive_id;
        $request ['request_inversiontask'] = $r_request_inversiontask;
        //lodging data
        $lodging_entrancedate = $l_lodging_entrancedate;
        $lodging_exitdate = $l_lodging_exitdate;
        $transport_idlodging = $l_transport_idlodging;
        $transport_idreturnlodging = $l_transport_idreturnlodging;
        $province_idlodging = $l_province_idlodging;
        $lodging_requestreinforceddiet = $l_lodging_requestreinforceddiet;
        $lodging_requestelongationdiet = $l_lodging_requestelongationdiet;
        $lodging_prorogate = $l_lodging_prorogate;
        $lodging_reinforceddiet = $l_lodging_reinforceddiet;
        $lodging_elongationdiet = $l_lodging_elongationdiet;
        $lodging_noshow = $l_lodging_noshow;
        $lodging_prorogation = $l_lodging_prorogation;
        $hotel_id = $l_hotel_id;
        $linearity_id = $l_linearity_id;
        $cafeteria_id = $l_cafeteria_id;
        $person_editedby = $l_person_editedby;
        $this->db->trans_begin();
        $re = $this->db->insert(self::TABLE_NAME, $request);
        $request_id = $this->db->insert_id();
        $logs = new Logs ( );
        $myquery = $logs->sqlinsert(self::TABLE_NAME, $request);
        $logs->write(self::TABLE_NAME, 'INSERT', $myquery);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        if ($re == true) {
            Request_lodgings_model::insert($request_id, $lodging_entrancedate, $lodging_exitdate, $transport_idlodging, $transport_idreturnlodging, $province_idlodging, $lodging_requestreinforceddiet, $lodging_requestelongationdiet, $lodging_prorogate);
            Lodging_edit_model::insertProrogation($request_id, $lodging_reinforceddiet, $lodging_elongationdiet, $lodging_noshow, $lodging_prorogation, $hotel_id, $linearity_id, $cafeteria_id, $person_editedby);
        }
        return 'true';
    }

    public function updateState($state) {
        $data = array('lodging_state' => $state);
        $this->db->trans_begin();
        $re = $this->db->update(self::TABLE_NAME, $data);
        $logs = new Logs ( );

        $mywhere = 'where state = ' . $state;
        $myquery = $logs->sqlupdate(self::TABLE_NAME, $data, $mywhere);

        $logs->write(self::TABLE_NAME, 'UPDATE', $myquery);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        if ($re == true)
            return "true"; else
            return "false";
    }

    /**
     * Funcion para eliminar un lodging_request por su id
     *
     * @param string $request_id
     */
    public function delete($request_id) {
        $this->load->model('request/request_lodgings_model');
        $this->load->model('request/request_tickets_model');
        $re = Request_lodgings_model::canceledState($request_id, 1);
        $re1 = Request_tickets_model::cancelTicket($request_id);
        if ($re || $re1)
            return "true";
        else
            return "false";
        /* $centinela = new Centinela();
          $roll_id = $centinela->get_roll_id ();
          $person_id = $centinela->get_person_id();
          if ($roll_id < 5) {
          $temp = self::getPersonRequestedById($request_id);
          if ($temp == $person_id) {
          $this->db->where ( 'person_idrequestedby', $person_id );
          $this->db->where ( 'request_id', $request_id );
          $this->db->trans_begin ();
          $re = $this->db->delete ( self::TABLE_NAME );
          $logs = new Logs ( );
          $mywhere = 'where person_idrequestedby = ' . $person_id.' and request_id = ' . $request_id ;
          $myquery = $logs->sqldelete ( self::TABLE_NAME, $mywhere );
          $logs->write ( self::TABLE_NAME, 'DELETE', $myquery );

          if ($this->db->trans_status () === FALSE) {
          $this->db->trans_rollback ();
          } else {
          $this->db->trans_commit ();
          }
          if ($re == 1)
          return "true";
          else
          return "false";
          } else {
          return "false";
          }
          } else {
          $this->db->where ( 'request_id', $request_id );
          $this->db->trans_begin ();
          $re = $this->db->delete ( self::TABLE_NAME );
          $logs = new Logs ( );
          $mywhere = 'where request_id = ' . $request_id;
          $myquery = $logs->sqldelete ( self::TABLE_NAME, $mywhere );
          $logs->write ( self::TABLE_NAME, 'DELETE', $myquery );

          if ($this->db->trans_status () === FALSE) {
          $this->db->trans_rollback ();
          } else {
          $this->db->trans_commit ();
          }
          if ($re == 1)
          return "true";
          else
          return "false";
          } */
    }

    public function getPersonRequestedById($request_id) {
        $this->db->select('person_idrequestedby');
        $this->db->from(self::TABLE_NAME);
        $this->db->where('request_id', $request_id);
        $result = $this->db->get();
        foreach ($result->result() as $row) {
            $person_id = $row->person_idrequestedby;
        }
        return $person_id;
    }

    public function getById($request_id) {

        $centinela = new Centinela ( );
        $roll_id = $centinela->get_roll_id();
        $this->load->model('person/person_persons_model');
        $this->load->model('request/request_tickets_model');
        $request_tickets_table = 'request_tickets';
        $request_lodgings_table = 'request_lodgings';
        $this->db->select('request_requests.request_id,
							request_requests.request_details,
							request_requests.motive_id,
							request_requests.person_idworker,
							request_requests.request_inversiontask,
                            request_requests.request_consecutive,
                            request_requests.request_area,
                            request_requests.person_groupresponsable,
							request_lodgings.lodging_entrancedate,
							request_lodgings.lodging_exitdate,
							request_lodgings.lodging_requestreinforceddiet, 
							request_lodgings.lodging_requestelongationdiet, 
							request_lodgings.province_idlodging, 
							request_lodgings.transport_idlodging, 
							request_lodgings.transport_idreturnlodging, 
							request_lodgings.lodging_state');
        $this->db->from(self::TABLE_NAME);
        //$this->db->join ( $request_tickets_table, $request_tickets_table . '.request_id = ' . self::TABLE_NAME . '.request_id', 'left' );
        $this->db->join($request_lodgings_table, $request_lodgings_table . '.request_id = ' . self::TABLE_NAME . '.request_id', 'left');
        $this->db->where(self::TABLE_NAME . '.request_id =', $request_id);
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $province = Person_persons_model::getById($row->person_idworker);
                $tickets = Request_tickets_model::getById($row->request_id);
                $count = count($tickets);
                if ($count > 1) {
                    $value [] = array('request_id' => $row->request_id,
                        'request_details' => $row->request_details,
                        'motive_id' => $row->motive_id,
                        'person_idworker' => $row->person_idworker,
                        'request_inversiontask' => $row->request_inversiontask,
                        'request_consecutive' => $row->request_consecutive,
                        'request_area' => $row->request_area,
                        'person_groupresponsable' => $row->person_groupresponsable,
                        'province_idworker' => $province[0]['province_id'],
                        'lodging_entrancedate' => $row->lodging_entrancedate,
                        'lodging_exitdate' => $row->lodging_exitdate,
                        'lodging_requestreinforceddiet' => $row->lodging_requestreinforceddiet,
                        'lodging_requestelongationdiet' => $row->lodging_requestelongationdiet,
                        'province_idlodging' => $row->province_idlodging,
                        'transport_idlodging' => $row->transport_idlodging,
                        'transport_idreturnlodging' => $row->transport_idreturnlodging,
                        'lodging_state' => $row->lodging_state,
                        'ticket_idexit' => $tickets[0]['id'],
                        'transport_idexit' => $tickets[0]['transport_id'],
                        'transport_itinerary' => $tickets[0]['transport_itinerary'],
                        'ticket_exitdate' => $tickets[0]['ticket_date'],
                        'province_idfrom' => $tickets[0]['province_idfrom'],
                        'province_idto' => $tickets[0]['province_idto'],
                        'ticket_state' => $tickets[0]['ticket_state'],
                        'ticket_idreturn' => $tickets[1]['id'],
                        'transport_idreturn' => $tickets[1]['transport_id'],
                        'transport_return_itinerary' => $tickets[1]['transport_itinerary'],
                        'ticket_returndate' => $tickets[1]['ticket_date'],
                        'province_idfrom_return' => $tickets[1]['province_idfrom'],
                        'province_idto_return' => $tickets[1]['province_idto'],
                        'ticket_state_return' => $tickets[1]['ticket_state']);
                } elseif ($count == 1) {
                    $value [] = array('request_id' => $row->request_id,
                        'request_details' => $row->request_details,
                        'motive_id' => $row->motive_id,
                        'person_idworker' => $row->person_idworker,
                        'request_inversiontask' => $row->request_inversiontask,
                        'request_consecutive' => $row->request_consecutive,
                        'request_area' => $row->request_area,
                        'person_groupresponsable' => $row->person_groupresponsable,
                        'province_idworker' => $province[0]['province_id'],
                        'lodging_entrancedate' => $row->lodging_entrancedate,
                        'lodging_exitdate' => $row->lodging_exitdate,
                        'lodging_requestreinforceddiet' => $row->lodging_requestreinforceddiet,
                        'lodging_requestelongationdiet' => $row->lodging_requestelongationdiet,
                        'province_idlodging' => $row->province_idlodging,
                        'transport_idlodging' => $row->transport_idlodging,
                        'transport_idreturnlodging' => $row->transport_idreturnlodging,
                        'lodging_state' => $row->lodging_state,
                        'ticket_idexit' => $tickets[0]['id'],
                        'transport_idexit' => $tickets[0]['transport_id'],
                        'transport_itinerary' => $tickets[0]['transport_itinerary'],
                        'ticket_exitdate' => $tickets[0]['ticket_date'],
                        'province_idfrom' => $tickets[0]['province_idfrom'],
                        'province_idto' => $tickets[0]['province_idto'],
                        'ticket_state' => $tickets[0]['ticket_state']);
                } else {
                    $value [] = array('request_id' => $row->request_id,
                        'request_details' => $row->request_details,
                        'motive_id' => $row->motive_id,
                        'person_idworker' => $row->person_idworker,
                        'request_inversiontask' => $row->request_inversiontask,
                        'request_consecutive' => $row->request_consecutive,
                        'request_area' => $row->request_area,
                        'person_groupresponsable' => $row->person_groupresponsable,
                        'province_idworker' => $province[0]['province_id'],
                        'lodging_entrancedate' => $row->lodging_entrancedate,
                        'lodging_exitdate' => $row->lodging_exitdate,
                        'lodging_requestreinforceddiet' => $row->lodging_requestreinforceddiet,
                        'lodging_requestelongationdiet' => $row->lodging_requestelongationdiet,
                        'province_idlodging' => $row->province_idlodging,
                        'transport_idlodging' => $row->transport_idlodging,
                        'transport_idreturnlodging' => $row->transport_idreturnlodging,
                        'lodging_state' => $row->lodging_state);
                }
            }
        }
        return $value;
    }

    public function getRequestDetailById($request_id)
    {
        $centinela = new Centinela ();
        $this->db->select('request_requests.request_id,
                            request_requests.request_details,
                            request_requests.request_inversiontask,
                            request_requests.person_idrequestedby,
                            request_requests.person_idworker,
                            request_requests.request_area,
                            request_requests.request_consecutive,
                            request_requests.person_groupresponsable,
                            request_requests.diet_entrancedate,
                            request_requests.diet_exitdate,
                            request_requests.person_idrequestedby,
                            request_requests.center_id,
                            conf_motives.motive_name,
                            conf_costcenters.center_name,
                            conf_costcenters.center_sap,
                            person_persons.person_identity,
                            person_persons.person_name,
                            person_persons.person_lastname,
                            person_persons.person_secondlastname');
        $this->db->from(self::TABLE_NAME);
        $this->db->join('conf_costcenters', 'conf_costcenters.center_id = ' . self::TABLE_NAME . '.center_idadvance', 'inner');
        $this->db->join('conf_motives', 'conf_motives.motive_id = ' . self::TABLE_NAME . '.motive_id', 'inner');
        $this->db->join('person_persons', 'person_persons.person_id = ' . self::TABLE_NAME . '.person_idworker', 'inner');
        $this->db->where(self::TABLE_NAME . '.request_id =', $request_id);
        $row = $this->db->get()->row();
        $value = array();
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_costcenters_model');
        $person_requestedby = Person_persons_model::getNameById($row->person_idrequestedby);
        $center_name = Conf_costcenters_model::getNameById($row->center_id);
        $value [] = array('request_id' => $row->request_id,
            'request_details' => $row->request_details,
            'motive_name' => $row->motive_name,
            'person_worker' => $row->person_identity . ' - ' . $row->person_name . ' ' . $row->person_lastname . ' ' . $row->person_secondlastname,
            'request_inversiontask' => $row->request_inversiontask,
            'person_requestedby' => $person_requestedby,
            'center_requestedby' => $center_name,
            'request_consecutive' => $row->request_consecutive,
            'request_area' => $row->request_area,
            'person_groupresponsable' => $row->person_groupresponsable,
            'diet_entrancedate' => $row->diet_entrancedate,
            'diet_exitdate' => $row->diet_exitdate,
            'center_advance' => $row->center_sap . ' / ' . $row->center_name);
        return $value;
    }


    public function getAdvanceLiquidationById($request_id)
    {
        $request_lodgings_table = 'request_lodgings';
        $person_persons_table = 'person_persons';
        $conf_costcenters_table = 'conf_costcenters';
        $conf_motives_table = 'conf_motives';
        $this->db->select('request_requests.request_id,
                            request_requests.request_date, 
                            request_requests.person_idworker, 
                            request_requests.center_idadvance, 
                            request_requests.request_details, 
                            request_requests.request_area, 
                            request_requests.request_consecutive, 
                            request_requests.person_groupresponsable,
                            request_requests.diet_entrancedate,
                            request_requests.diet_exitdate,
                            request_requests.advance_requested,
                            conf_costcenters.center_name,
                            conf_costcenters.center_consecutive,
                            person_persons.person_name, 
                            person_persons.person_lastname, 
                            person_persons.person_secondlastname');
        $this->db->from(self::TABLE_NAME);
        $this->db->join($conf_costcenters_table, $conf_costcenters_table . '.center_id = ' . self::TABLE_NAME . '.center_idadvance', 'inner');
        $this->db->join($conf_motives_table, $conf_motives_table . '.motive_id = ' . self::TABLE_NAME . '.motive_id', 'inner');
        $this->db->join($person_persons_table, $person_persons_table . '.person_id = ' . self::TABLE_NAME . '.person_idworker', 'inner');
        $this->db->where(self::TABLE_NAME . '.request_id =', $request_id);
        $result = $this->db->get()->row();
        return $result;
    }

    /**
     * Devuelve los anticipos individuales
     * @param type $dateStart
     * @param type $dateEnd
     * @param type $province
     */
    public function getAdvanceLiquidationRequests($dateStart, $dateEnd)
    {
        $centinela = new Centinela();
        $roll_id = $centinela->get_roll_id();
        $person_persons_table = 'person_persons';
        $conf_costcenters_table = 'conf_costcenters';
        $conf_motives_table = 'conf_motives';
        $this->db->select('request_requests.request_id,
                            request_requests.request_date,
                            request_requests.person_idworker,
                            request_requests.center_idadvance,
                            request_requests.request_details,
                            request_requests.request_area,
                            request_requests.request_consecutive,
                            request_requests.person_groupresponsable,
                            request_requests.diet_entrancedate,
                            request_requests.diet_exitdate,
                            request_requests.advance_requested,
                            conf_costcenters.center_name,
                            person_persons.person_name,
                            person_persons.person_lastname,
                            person_persons.person_secondlastname,
                            request_lodgings.lodging_canceled');
        $this->db->from(self::TABLE_NAME);
        $this->db->join($conf_costcenters_table, $conf_costcenters_table . '.center_id = ' . self::TABLE_NAME . '.center_idadvance', 'inner');
        $this->db->join($conf_motives_table, $conf_motives_table . '.motive_id = ' . self::TABLE_NAME . '.motive_id', 'inner');
        $this->db->join($person_persons_table, $person_persons_table . '.person_id = ' . self::TABLE_NAME . '.person_idworker', 'inner');
        $this->db->join('request_lodgings', 'request_lodgings.request_id = ' . self::TABLE_NAME . '.request_id', 'left');
        $this->db->where(self::TABLE_NAME . '.diet_entrancedate >=', '2014-08-01');
        $this->db->where(self::TABLE_NAME . '.diet_entrancedate >=', $dateStart);
        $this->db->where(self::TABLE_NAME . '.diet_entrancedate <=', $dateEnd);
        if ($roll_id != 6) {
            if($centinela->get_user_name() == 'mirlena.martinez' || $centinela->get_user_name() == 'beatriz.brizuela'){
                $cc = array(40, 47);
                $this->db->where_in(self::TABLE_NAME . '.center_idadvance', $cc);
            }else{
                $this->db->where(self::TABLE_NAME . '.center_idadvance', $centinela->get_center_id());
            }
        }
        $this->db->where(self::TABLE_NAME . '.person_groupresponsable', "");
        $this->db->order_by("diet_entrancedate", "asc");
        $this->db->order_by("request_id", "asc");
        $result = $this->db->get();
        $value = array();
        $date = new Dates();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                if($row->advance_requested == 'f' && $row->lodging_canceled == '1'){
                    continue;
                }
                $person_worker = $row->person_name . ' ' . $row->person_lastname . ' ' . $row->person_secondlastname;
                $value [] = array('request_id' => $row->request_id,
                    'request_date' => $row->request_date,
                    'diet_entrancedate' => $row->diet_entrancedate,
                    'diet_exitdate' => $row->diet_exitdate,
                    'center_name' => $row->center_name,
                    'request_details' => $row->request_details,
                    'person_worker' => $person_worker,
                    'request_area' => $row->request_area,
                    'person_groupresponsable' => $row->person_groupresponsable,
                    'advance_requested' => $row->advance_requested,
                    'request_area' => $row->request_area,
                    'request_consecutive' => $row->request_consecutive);
            }
        }
        echo("{count : " . count($value) . ", data : " . json_encode($value) . "}");
    }

//    public function getAdvanceLiquidationRequests ($dateStart, $dateEnd, $province)
//    {
//        $centinela = new Centinela();
//        $roll_id = $centinela->get_roll_id ();
//        $request_lodgings_table = 'request_lodgings';
//        $conf_provinces_table = 'conf_provinces';
//        $person_persons_table = 'person_persons';
//        $conf_costcenters_table = 'conf_costcenters';
//        $conf_motives_table = 'conf_motives';
//        $this->db->select ('request_requests.request_id,
//                            request_requests.request_date,
//                            request_requests.person_idworker,
//                            request_requests.center_idadvance,
//                            request_requests.request_details,
//                            request_requests.request_area,
//                            request_requests.request_consecutive,
//                            request_requests.person_groupresponsable,
//                            request_requests.diet_entrancedate,
//                            request_requests.diet_exitdate,
//                            request_requests.advance_requested,
//                            conf_costcenters.center_name,
//                            person_persons.person_name,
//                            person_persons.person_lastname,
//                            person_persons.person_secondlastname,
//                            request_lodgings.lodging_entrancedate,
//                            request_lodgings.lodging_exitdate,
//                            request_lodgings.province_idlodging,
//                            conf_provinces.province_name');
//        $this->db->from (self::TABLE_NAME);
//        $this->db->join ($conf_costcenters_table, $conf_costcenters_table . '.center_id = ' . self::TABLE_NAME . '.center_idadvance', 'inner');
//        $this->db->join ($conf_motives_table, $conf_motives_table . '.motive_id = ' . self::TABLE_NAME . '.motive_id', 'inner');
//        $this->db->join ($person_persons_table, $person_persons_table . '.person_id = ' . self::TABLE_NAME . '.person_idworker', 'inner');
//        $this->db->join ($request_lodgings_table, $request_lodgings_table . '.request_id = ' . self::TABLE_NAME . '.request_id', 'inner');
//        $this->db->join ($conf_provinces_table, $conf_provinces_table . '.province_id = ' . $request_lodgings_table . '.province_idlodging', 'inner');
//        //$this->db->where(self::TABLE_NAME . '.diet_entrancedate >=', "");
//        //$this->db->where(self::TABLE_NAME . '.advance_requested', "FALSE"); verificar si es conveniente mostrarlo o no
//        $this->db->where ($request_lodgings_table . '.lodging_canceled', 0);
//        $this->db->where (self::TABLE_NAME . '.person_groupresponsable', "");
//        $this->db->where (self::TABLE_NAME . '.diet_entrancedate >=', $dateStart);
//        $this->db->where (self::TABLE_NAME . '.diet_entrancedate <=', $dateEnd);
//        if ($roll_id != 6) {
//            $this->db->where (self::TABLE_NAME . '.center_idadvance', $centinela->get_center_id ());
//        }
//        if (!empty($province)) {
//            $this->db->where ($request_lodgings_table . '.province_idlodging', $province);
//        }
//        $this->db->order_by ("diet_entrancedate", "asc");
//        $this->db->order_by ("request_id", "asc");
//        $result = $this->db->get ();
//        $value = array ();
//        $date = new Dates();
//        if ($result->result () != null) {
//            foreach ($result->result () as $row) {
//                $person_worker = $row->person_name . ' ' . $row->person_lastname . ' ' . $row->person_secondlastname;
//                $value [] = array ('request_id' => $row->request_id,
//                    'request_date' => $row->request_date,
//                    'diet_entrancedate' => $row->diet_entrancedate,
//                    'diet_exitdate' => $row->diet_exitdate,
//                    'center_name' => $row->center_name,
//                    'request_details' => $row->request_details,
//                    'person_worker' => $person_worker,
//                    'request_area' => $row->request_area,
//                    'person_groupresponsable' => $row->person_groupresponsable,
//                    'advance_requested' => $row->advance_requested,
//                    'request_area' => $row->request_area,
//                    'request_consecutive' => $row->request_consecutive,
//                    'province_name' => $row->province_name);
//            }
//        }
//        echo ("{count : " . count ($value) . ", data : " . json_encode ($value) . "}");
//    }
//
    public function getAdvanceLiquidationRequestsGroup($dateStart, $dateEnd, $province)
    {
        $centinela = new Centinela();
        $roll_id = $centinela->get_roll_id();
        $request_lodgings_table = 'request_lodgings';
        $conf_provinces_table = 'conf_provinces';
        $person_persons_table = 'person_persons';
        $conf_costcenters_table = 'conf_costcenters';
        $conf_motives_table = 'conf_motives';
        $this->db->select('request_requests.request_id,
                            request_requests.request_date, 
                            request_requests.person_idworker, 
                            request_requests.center_idadvance, 
                            request_requests.request_details, 
                            request_requests.request_area, 
                            request_requests.request_consecutive, 
                            request_requests.person_groupresponsable,
                            request_requests.diet_entrancedate,
                            request_requests.diet_exitdate,
                            request_requests.advance_requested,
                            conf_costcenters.center_name, 
                            person_persons.person_name, 
                            person_persons.person_lastname, 
                            person_persons.person_secondlastname, 
                            request_lodgings.lodging_entrancedate, 
                            request_lodgings.lodging_exitdate,
                            request_lodgings.province_idlodging,
                            conf_provinces.province_name');
        $this->db->from(self::TABLE_NAME);
        $this->db->join($conf_costcenters_table, $conf_costcenters_table . '.center_id = ' . self::TABLE_NAME . '.center_idadvance', 'inner');
        $this->db->join($conf_motives_table, $conf_motives_table . '.motive_id = ' . self::TABLE_NAME . '.motive_id', 'inner');
        $this->db->join($person_persons_table, $person_persons_table . '.person_id = ' . self::TABLE_NAME . '.person_idworker', 'inner');
        $this->db->join($request_lodgings_table, $request_lodgings_table . '.request_id = ' . self::TABLE_NAME . '.request_id', 'inner');
        $this->db->join($conf_provinces_table, $conf_provinces_table . '.province_id = ' . $request_lodgings_table . '.province_idlodging', 'inner');
        $this->db->where(self::TABLE_NAME . '.diet_entrancedate >=', '2014-08-01');
        $this->db->where($request_lodgings_table . '.lodging_canceled', 0);
        $this->db->where(self::TABLE_NAME . '.diet_entrancedate >=', $dateStart);
        $this->db->where(self::TABLE_NAME . '.diet_entrancedate <=', $dateEnd);
        $this->db->where(self::TABLE_NAME . '.person_groupresponsable !=', "");
        if ($roll_id != 6) {
            if($centinela->get_user_name() == 'mirlena.martinez' || $centinela->get_user_name() == 'beatriz.brizuela'){
                $cc = array(40, 47);
                $this->db->where_in(self::TABLE_NAME . '.center_idadvance', $cc);
            }else{
                $this->db->where(self::TABLE_NAME . '.center_idadvance', $centinela->get_center_id());
            }
        }
        if (!empty($province)) {
            $this->db->where($request_lodgings_table . '.province_idlodging', $province);
        }
        $this->db->order_by("diet_entrancedate", "asc");
        $this->db->order_by("request_id", "asc");
        $result = $this->db->get();
        $value = array();
        $date = new Dates();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $person_worker = $row->person_name . ' ' . $row->person_lastname . ' ' . $row->person_secondlastname;
                $value [] = array('request_id' => $row->request_id,
                    'request_date' => $row->request_date,
                    'diet_entrancedate' => $row->diet_entrancedate,
                    'diet_exitdate' => $row->diet_exitdate,
                    'center_name' => $row->center_name,
                    'request_details' => $row->request_details,
                    'person_worker' => $person_worker,
                    'request_area' => $row->request_area,
                    'person_groupresponsable' => $row->person_groupresponsable . "-" . $row->diet_entrancedate,
                    'advance_requested' => $row->advance_requested,
                    'request_area' => $row->request_area,
                    'request_consecutive' => $row->request_consecutive,
                    'province_name' => $row->province_name);
            }
        }
        echo("{count : " . count($value) . ", data : " . json_encode($value) . "}");
    }

    public function updateAdvanceRequested($request_id, $state)
    {
        $data = array('advance_requested' => $state);
        $this->db->where('request_id', $request_id);
        $this->db->trans_begin();
        $re = $this->db->update(self::TABLE_NAME, $data);
        $logs = new Logs ();

        $mywhere = 'where request_id = ' . $request_id;
        $myquery = $logs->sqlupdate(self::TABLE_NAME, $data, $mywhere);

        $logs->write(self::TABLE_NAME, 'UPDATE', $myquery);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        if ($re == true) {
            return "true";
        } else {
            return "false";
        }
    }
    public function getCountLodgingProvincePerCenter($begin, $end, $motive = '', $chain = '') {

        $result = self::getAllRequests($begin, $end, $motive, $chain);
        $value = array();
        $center = $result[0]['center_id'];
        $PRI = 0;
        $ART = 0;
        $MYB = 0;
        $HAB = 0;
        $MTZ = 0;
        $VCL = 0;
        $CFG = 0;
        $SSP = 0;
        $CAV = 0;
        $CMG = 0;
        $LTU = 0;
        $HOL = 0;
        $GRM = 0;
        $SCU = 0;
        $GTM = 0;
        $ISJ = 0;
        $TPRI = 0;
        $TART = 0;
        $TMYB = 0;
        $THAB = 0;
        $TMTZ = 0;
        $TVCL = 0;
        $TCFG = 0;
        $TSSP = 0;
        $TCAV = 0;
        $TCMG = 0;
        $TLTU = 0;
        $THOL = 0;
        $TGRM = 0;
        $TSCU = 0;
        $TGTM = 0;
        $TISJ = 0;
        $TTOTAL = 0;
        foreach ($result as $row) {
            if ($row['center_id'] == $center) {
                $center_name = $row['center_name'];
                switch ($row ['province_lodging']) {
                    case 1 :
                        $PRI++;
                        $TPRI++;
                        break;
                    case 3 :
                        $ART++;
                        $TART++;
                        break;
                    case 4 :
                        $MYB++;
                        $TMYB++;
                        break;
                    case 5 :
                        $HAB++;
                        $THAB++;
                        break;
                    case 6 :
                        $MTZ++;
                        $TMTZ++;
                        break;
                    case 7 :
                        $VCL++;
                        $TVCL++;
                        break;
                    case 8 :
                        $CFG++;
                        $TCFG++;
                        break;
                    case 9 :
                        $SSP++;
                        $TSSP++;
                        break;
                    case 10 :
                        $CAV++;
                        $TCAV++;
                        break;
                    case 11 :
                        $CMG++;
                        $TCMG++;
                        break;
                    case 12 :
                        $LTU++;
                        $TLTU++;
                        break;
                    case 13 :
                        $HOL++;
                        $THOL++;
                        break;
                    case 14 :
                        $GRM++;
                        $TGRM++;
                        break;
                    case 15 :
                        $SCU++;
                        $TSCU++;
                        break;
                    case 16 :
                        $GTM++;
                        $TGTM++;
                        break;
                    case 17 :
                        $ISJ++;
                        $TISJ++;
                        break;
                }
            } else {
                $TOTAL = $PRI + $ART + $MYB + $HAB + $MTZ + $VCL + $CFG + $SSP + $CAV + $CMG + $LTU + $HOL + $GRM + $SCU + $GTM + $ISJ;
                $value [] = array('center' => $center_name,
                    'pri' => $PRI,
                    'art' => $ART,
                    'myb' => $MYB,
                    'hab' => $HAB,
                    'mtz' => $MTZ,
                    'vcl' => $VCL,
                    'cfg' => $CFG,
                    'ssp' => $SSP,
                    'cav' => $CAV,
                    'cmg' => $CMG,
                    'ltu' => $LTU,
                    'hol' => $HOL,
                    'grm' => $GRM,
                    'scu' => $SCU,
                    'gtm' => $GTM,
                    'isj' => $ISJ,
                    'total' => $TOTAL);
                $center = $row['center_id'];
                $center_name = $row['center_name'];
                $PRI = 0;
                $ART = 0;
                $MYB = 0;
                $HAB = 0;
                $MTZ = 0;
                $VCL = 0;
                $CFG = 0;
                $SSP = 0;
                $CAV = 0;
                $CMG = 0;
                $LTU = 0;
                $HOL = 0;
                $GRM = 0;
                $SCU = 0;
                $GTM = 0;
                $ISJ = 0;
                switch ($row ['province_lodging']) {
                    case 1 :
                        $PRI++;
                        $TPRI++;
                        break;
                    case 3 :
                        $ART++;
                        $TART++;
                        break;
                    case 4 :
                        $MYB++;
                        $TMYB++;
                        break;
                    case 5 :
                        $HAB++;
                        $THAB++;
                        break;
                    case 6 :
                        $MTZ++;
                        $TMTZ++;
                        break;
                    case 7 :
                        $VCL++;
                        $TVCL++;
                        break;
                    case 8 :
                        $CFG++;
                        $TCFG++;
                        break;
                    case 9 :
                        $SSP++;
                        $TSSP++;
                        break;
                    case 10 :
                        $CAV++;
                        $TCAV++;
                        break;
                    case 11 :
                        $CMG++;
                        $TCMG++;
                        break;
                    case 12 :
                        $LTU++;
                        $TLTU++;
                        break;
                    case 13 :
                        $HOL++;
                        $THOL++;
                        break;
                    case 14 :
                        $GRM++;
                        $TGRM++;
                        break;
                    case 15 :
                        $SCU++;
                        $TSCU++;
                        break;
                    case 16 :
                        $GTM++;
                        $TGTM++;
                        break;
                    case 17 :
                        $ISJ++;
                        $TISJ++;
                        break;
                }
            }
        }
        $TOTAL = $PRI + $ART + $MYB + $HAB + $MTZ + $VCL + $CFG + $SSP + $CAV + $CMG + $LTU + $HOL + $GRM + $SCU + $GTM + $ISJ;
        $value [] = array('center' => $center_name,
            'pri' => $PRI,
            'art' => $ART,
            'myb' => $MYB,
            'hab' => $HAB,
            'mtz' => $MTZ,
            'vcl' => $VCL,
            'cfg' => $CFG,
            'ssp' => $SSP,
            'cav' => $CAV,
            'cmg' => $CMG,
            'ltu' => $LTU,
            'hol' => $HOL,
            'grm' => $GRM,
            'scu' => $SCU,
            'gtm' => $GTM,
            'isj' => $ISJ,
            'total' => $TOTAL);
        $last = array();
        $TTOTAL = $TPRI + $TART + $TMYB + $THAB + $TMTZ + $TVCL + $TCFG + $TSSP + $TCAV + $TCMG + $TLTU + $THOL + $TGRM + $TSCU + $TGTM + $TISJ;
        $last [] = array('center' => 'TOTAL',
            'pri' => $TPRI,
            'art' => $TART,
            'myb' => $TMYB,
            'hab' => $THAB,
            'mtz' => $TMTZ,
            'vcl' => $TVCL,
            'cfg' => $TCFG,
            'ssp' => $TSSP,
            'cav' => $TCAV,
            'cmg' => $TCMG,
            'ltu' => $TLTU,
            'hol' => $THOL,
            'grm' => $TGRM,
            'scu' => $TSCU,
            'gtm' => $TGTM,
            'isj' => $TISJ,
            'total' => $TTOTAL);
        $value = array_merge((array) $value, (array) $last);
        return $value;
    }

    public function getTotalTicketPerCenter($begin = '2011-01-01', $end = '2011-08-31', $motive = '') {

        $result = self::getAllTicketsRequests($begin, $end, $motive = '');
        $value = array();
        $center = $result[0]['center_id'];
        $etecsa = 0;
        $viazul = 0;
        $avion = 0;
        $eventual = 0;
        $barco = 0;
        $independiente = 0;
        $tetecsa = 0;
        $tviazul = 0;
        $tavion = 0;
        $teventual = 0;
        $tbarco = 0;
        $tindependiente = 0;
        $TTOTAL = 0;
        foreach ($result as $row) {
            if ($row['center_id'] == $center) {
                $center_name = $row['center_name'];
                switch ($row ['transport_id']) {
                    case 1 :
                        $etecsa++;
                        $tetecsa++;
                        break;
                    case 2 :
                        $viazul++;
                        $tviazul++;
                        break;
                    case 3 :
                        $avion++;
                        $tavion++;
                        break;
                    case 4 :
                        $eventual++;
                        $teventual++;
                        break;
                    case 5 :
                        $barco++;
                        $tbarco++;
                        break;
                    case 6 :
                        $independiente++;
                        $tindependiente++;
                        break;
                }
            } else {
                $TOTAL = $etecsa + $viazul + $avion + $eventual + $barco + $independiente;
                $value [] = array('center' => $center_name,
                    'etecsa' => $etecsa,
                    'viazul' => $viazul,
                    'avion' => $avion,
                    'eventual' => $eventual,
                    'barco' => $barco,
                    'independiente' => $independiente,
                    'total' => $TOTAL);
                $center = $row['center_id'];
                $center_name = $row['center_name'];
                $etecsa = 0;
                $viazul = 0;
                $avion = 0;
                $eventual = 0;
                $barco = 0;
                $independiente = 0;
                switch ($row ['transport_id']) {
                    case 1 :
                        $etecsa++;
                        $tetecsa++;
                        break;
                    case 2 :
                        $viazul++;
                        $tviazul++;
                        break;
                    case 3 :
                        $avion++;
                        $tavion++;
                        break;
                    case 4 :
                        $eventual++;
                        $teventual++;
                        break;
                    case 5 :
                        $barco++;
                        $tbarco++;
                        break;
                    case 6 :
                        $independiente++;
                        $tindependiente++;
                        break;
                }
            }
        }
        $TOTAL = $etecsa + $viazul + $avion + $eventual + $barco + $independiente;
        $value [] = array('center' => $center_name,
            'etecsa' => $etecsa,
            'viazul' => $viazul,
            'avion' => $avion,
            'eventual' => $eventual,
            'barco' => $barco,
            'independiente' => $independiente,
            'total' => $TOTAL);
        $last = array();
        $TTOTAL = $tetecsa + $tviazul + $tavion + $teventual + $tbarco + $tindependiente;
        $last [] = array('center' => 'TOTAL',
            'etecsa' => $tetecsa,
            'viazul' => $tviazul,
            'avion' => $tavion,
            'eventual' => $teventual,
            'barco' => $tbarco,
            'independiente' => $tindependiente,
            'total' => $TTOTAL);
        $value = array_merge((array) $value, (array) $last);
        return $value;
    }

    public function getAllRequests($begin, $end, $motive, $chain) {
        $this->load->model('conf/conf_costcenters_model');
        $request_requests_table = 'request_requests';
        $request_lodgings_table = 'request_lodgings';
        $conf_hotels_table = 'conf_hotels';
        $conf_hotelchains_table = 'conf_hotelchains';
        $conf_costcenters_table = 'conf_costcenters';
        $lodging_edit_table = 'lodging_edit';
        $this->db->select(self::TABLE_NAME . '.request_id, ' .
                $request_requests_table . '.center_id, ' .
                'conf_costcenters.center_name, ' .
                $request_lodgings_table . '.province_idlodging');
        $this->db->from($request_requests_table);
        $this->db->join('conf_costcenters', 'conf_costcenters.center_id = ' . $request_requests_table . '.center_id', 'inner');
        $this->db->join($request_lodgings_table, $request_lodgings_table . '.request_id = ' . $request_requests_table . '.request_id', 'inner');
        $this->db->join($lodging_edit_table, $lodging_edit_table . '.request_id = ' . $request_lodgings_table . '.request_id', 'inner');
        $this->db->join($conf_hotels_table, $conf_hotels_table . '.hotel_id = ' . $lodging_edit_table . '.hotel_id', 'inner');
        $this->db->join($conf_hotelchains_table, $conf_hotelchains_table . '.chain_id = ' . $conf_hotels_table . '.chain_id', 'inner');

        $this->db->where($lodging_edit_table . '.lodging_noshow !=', 'on');
        $this->db->where($request_lodgings_table . '.lodging_canceled =', 0);
        $this->db->where($request_lodgings_table . '.lodging_entrancedate >=', $begin);
        $this->db->where($request_lodgings_table . '.lodging_entrancedate <=', $end);
        if (!empty($motive))
            $this->db->where('request_requests.motive_id', $motive);
        if (!empty($chain))
            $this->db->where('conf_hotelchains.chain_id', $chain);
        $this->db->order_by('center_id asc, province_idlodging asc');
        $result = $this->db->get();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $value [] = array('request_id' => $row->request_id,
                    'center_id' => $row->center_id,
                    'center_name' => $row->center_name,
                    'province_lodging' => $row->province_idlodging);
            }
        } else {
            $value = array();
        }
        return $value;
    }

    public function getAllTicketsRequests($begin, $end, $motive) {
        $this->load->model('conf/conf_costcenters_model');
        $request_requests_table = 'request_requests';
        $request_tickets_table = 'request_tickets';
        $conf_costcenters_table = 'conf_costcenters';
        $this->db->select(self::TABLE_NAME . '.request_id, ' .
                $request_requests_table . '.center_id, ' .
                'conf_costcenters.center_name, ' .
                $request_tickets_table . '.transport_id');
        $this->db->from($request_requests_table);
        $this->db->join('conf_costcenters', 'conf_costcenters.center_id = ' . $request_requests_table . '.center_id', 'inner');
        $this->db->join($request_tickets_table, $request_tickets_table . '.request_id = ' . $request_requests_table . '.request_id', 'inner');
        $this->db->where($request_tickets_table . '.ticket_cancel =', 0);
        $this->db->where($request_tickets_table . '.ticket_date >=', $begin);
        $this->db->where($request_tickets_table . '.ticket_date <=', $end);
        if (!empty($motive))
            $this->db->where('request_requests.motive_id', $motive);
        $this->db->order_by('center_id asc, transport_id desc');
        $result = $this->db->get();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $value [] = array('request_id' => $row->request_id,
                    'center_id' => $row->center_id,
                    'center_name' => $row->center_name,
                    'transport_id' => $row->transport_id);
            }
        } else {
            $value = array();
        }
        return $value;
    }

    /*A PARTIR DE AQUI LO NUEVO DE LA PARTE CONTABLE*/
    public function insertService(
        $observaciones_solicitud_trabajador,
        $solicitante,
        $trabajador,
        $fecha_entrada_hosp_solicitud_trabajador,
        $fecha_salida_hosp_solicitud_trabajador,
        $fecha_ida_pasaje_solicitud_trabajador,
        $fecha_regreso_pasaje_solicitud_trabajador,
        $origen_ubicacion,
        $destino_ubicacion,
        $regreso_ubicacion,
        $transporte,
        $uorganizativa_cargo_presupuesto,
        $dieta_desde,
        $dieta_hasta,
        $centro_contable,
        $consecutivo_modelo,
        $persona_a_recibir_modelo,
        $transporte_regreso,
        $request_area)
    {
        $this->load->model('request/request_lodgings_model');
        $this->load->model('request/request_tickets_model');
        $this->load->model('conf/conf_costcenters_model');
        $this->load->model('user/user_users_model');
        $this->load->model('person/person_persons_model');
        $person_idrequestedby = User_users_model::getByUserName($solicitante);
        $person_worker = Person_persons_model::getByIdentity($trabajador['ci']);

        $dates = new Dates ();
        $request ['request_date'] = $dates->now();
        $request ['request_details'] = $observaciones_solicitud_trabajador;
        if ($person_idrequestedby != null) {
            $request ['person_idrequestedby'] = $person_idrequestedby->person_id;
        } else {
            return array('result' => false, 'msg' => 'E_SOLICITANTE_NO_EXISTE', 'no_solicitud' => '');
        }
//        $center_id = $this->input->post ('center_id');

        $request ['center_id'] = $uorganizativa_cargo_presupuesto;
        $request ['person_idlicensedby'] = Conf_costcenters_model::getDirectorById($uorganizativa_cargo_presupuesto);

        if ($person_worker != null) {
            $request ['person_idworker'] = $person_worker->person_id;
        } else {
            if (empty($trabajador['provincia'])) {
                return array('result' => false, 'msg' => 'E_SDPH_VAL_DATOS', 'no_solicitud' => '');
            }
            if (strlen($trabajador['ci']) != 11) {
                return array('result' => false, 'msg' => 'E_SDPH_VAL_DATOS', 'no_solicitud' => '');
            }
            $person_id = Person_persons_model::insertService($trabajador['ci'], utf8_encode($trabajador['nombre']), utf8_encode($trabajador['apellidos']), $trabajador['provincia']);
            if ($person_id != null) {
                $request ['person_idworker'] = $person_id;
            } else {
                return array('result' => false, 'msg' => 'E_SDPH_VAL_DATOS', 'no_solicitud' => '');
            }
        }
        $person_idworker = $person_worker != null ? $person_worker->person_id : $person_id;
        $group_responsable = empty($persona_a_recibir_modelo) ? "" : $persona_a_recibir_modelo['nombre'] . ' ' . $persona_a_recibir_modelo['apellidos'];
        $request ['motive_id'] = 4;
        $request ['request_inversiontask'] = '';
        $request ['request_consecutive'] = $consecutivo_modelo;
        $request ['request_area'] = $request_area;
        $request ['person_groupresponsable'] = $group_responsable;
        $request ['center_idadvance'] = $centro_contable;
        $request ['diet_entrancedate'] = $dieta_desde;
        $request ['diet_exitdate'] = $dieta_hasta;

        //lodging data
        $lodging_entrancedate = $fecha_entrada_hosp_solicitud_trabajador;
        $lodging_exitdate = $fecha_salida_hosp_solicitud_trabajador;
        $transport_idlodging = $transporte;
        $transport_idreturnlodging = $transporte_regreso;
        $province_idlodging = $destino_ubicacion;
        $lodging_requestreinforceddiet = '';
        $lodging_requestelongationdiet = '';
        //ticket exit
        $ticket_idexit = ''; //$this->input->post ('ticket_idexit');
        $ticket_exitdate = $fecha_ida_pasaje_solicitud_trabajador;
        $transport_idexit = $transporte;
        $itinerary = '';
        if ($transporte == 1) {
            $itinerary = $origen_ubicacion > $destino_ubicacion ? 'Santiago-Habana' : 'Habana-Santiago';
        }
        $transport_itinerary = $itinerary; //falta recibir esta infrmacion desde el service
        $province_idfrom = $origen_ubicacion;
        $province_idto = $destino_ubicacion;
        //ticket return
        $ticket_idreturn = ''; //$this->input->post ('ticket_idreturn');
        $ticket_returndate = $fecha_regreso_pasaje_solicitud_trabajador;
        $transport_idreturn = $transporte_regreso;
        $return_itinerary = '';
        if ($transport_idreturn == 1) {
            $return_itinerary = $origen_ubicacion > $destino_ubicacion ? 'Santiago-Habana' : 'Habana-Santiago';
        }
        $transport_return_itinerary = $return_itinerary; //falta recibir esta infrmacion desde el service
        $province_idfrom_return = $destino_ubicacion;
        $province_idto_return = $regreso_ubicacion;

        $prorogation = 0;
        $canInsertLodging = 0;
        $canInsertTicket = 0;
        $canInsertTicketReturn = 0;
        if (!empty($ticket_exitdate)) {
            $canInsertTicket = self::canInsertTicket($ticket_exitdate, $person_idworker, $province_idfrom);
            if ($canInsertTicket > 0) {
                return array('result' => false, 'msg' => 'E_PASAJE_FECHA_YA_EXIXTE', 'no_solicitud' => '');
//                    return "{success: false, errors: { reason: 'Ya existe una solicitud de pasaje para este trabajador en la fecha señalada.' }, prorogation: 'no'}";
            }
        }
        if (!empty($ticket_returndate)) {
            $canInsertTicketReturn = self::canInsertTicket($ticket_returndate, $person_idworker, $province_idfrom_return);
            if ($canInsertTicketReturn > 0) {
                return array('result' => false, 'msg' => 'E_PASAJE_FECHA_YA_EXIXTE', 'no_solicitud' => '');
//                    return "{success: false, errors: { reason: 'Ya existe una solicitud de pasaje para este trabajador en la fecha señalada.' }, prorogation: 'no'}";
            }
        }
        if (!empty($lodging_entrancedate)) {
            $canInsertLodging = self::canInsertLodging($lodging_entrancedate, $lodging_exitdate, $person_idworker);
            $prorogation = self::verifyProrogation($lodging_entrancedate, $person_idworker, $province_idlodging);
            if ($canInsertLodging > 0) {
                return array('result' => false, 'msg' => 'E_HOSPEDAJE_FECHA_COINCIDE', 'no_solicitud' => '');
//                    return "{success: false, errors: { reason: 'Ya existe una solicitud de hospedaje para este trabajador en el intervalo de tiempo señalado.' }, prorogation: 'no'}";
            }
            if ($prorogation > 0) {
                //PONER AQUI LA SOLICITUD AUROMATICA DE PRORROGA
//                    return "{success: false, errors: { reason: 'La fecha de entrada de la solicitud coincide con la salida del hospedaje del trabajador seleccionado. Desea hacer una pr&oacute;rroga del hospedaje para este trabajador?.' }, prorogation: 'si'}";
            }
        }
        $this->db->trans_begin();
        $re = $this->db->insert(self::TABLE_NAME, $request);
        $request_id = $this->db->insert_id();
        $logs = new Logs ();
        $myquery = $logs->sqlinsert(self::TABLE_NAME, $request);
        $logs->writeService(self::TABLE_NAME, 'INSERT', $myquery, $person_idrequestedby->person_id);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

        $lodgingInsert = "true";
        $ticketInsert = "true";

        if ($re == true) {
            if (!empty($lodging_entrancedate)) {
                $lodgingInsert = Request_lodgings_model::insertService($request_id, $lodging_entrancedate, $lodging_exitdate, $transport_idlodging, $transport_idreturnlodging, $province_idlodging, $lodging_requestreinforceddiet, $lodging_requestelongationdiet, false, $person_idrequestedby->person_id);
            }
            if (!empty($ticket_exitdate)) {
                $ticketInsert = Request_tickets_model::insertService($ticket_idexit, $request_id, $ticket_exitdate, $transport_idexit, $transport_itinerary, $province_idfrom, $province_idto, $person_idrequestedby->person_id);
            }
            if (!empty($ticket_returndate)) {
                $ticketInsert = Request_tickets_model::insertService($ticket_idreturn, $request_id, $ticket_returndate, $transport_idreturn, $transport_return_itinerary, $province_idfrom_return, $province_idto_return, $person_idrequestedby->person_id);
            }
        }
        if ($lodgingInsert == "true" && $ticketInsert == "true") {
            return array('result' => true, 'msg' => '', 'no_solicitud' => $request_id);
        } else {
            return array('result' => false, 'msg' => 'E_SDPH_VAL_DATOS', 'no_solicitud' => '');
        }
    }

    public function verifyConsecutive($consecutivo, $center_id, $worker)
    {
        $this->db->select('request_id');
        $this->db->from(self::TABLE_NAME);
        if (!empty($consecutivo)) {
            $this->db->where(self::TABLE_NAME . '.request_consecutive', $consecutivo);
        }
        $this->db->where(self::TABLE_NAME . '.person_idworker', $worker);
        $this->db->where(self::TABLE_NAME . '.center_id', $center_id);
        return $this->db->count_all_results();
    }

    public function existId($request_id)
    {
        $this->db->from(self::TABLE_NAME);
        $this->db->select('request_requests.request_id');
        $this->db->where(self::TABLE_NAME . '.request_id', $request_id);
        return $this->db->count_all_results();
    }

    public function cancelSolicitudService($request_id, $user)
    {
        $this->load->model('user/user_users_model');
        $person_idrequestedby = User_users_model::getByUserName($user);
        if ($person_idrequestedby != null) {
            $person_id = $person_idrequestedby->person_id;
        } else {
            return array('result' => false, 'msg' => 'E_SOLICITANTE_NO_EXISTE', 'no_solicitud' => '');
        }
        if ($this->existId($request_id) > 0) {
            $this->load->model('request/request_lodgings_model');
            $this->load->model('request/request_tickets_model');
            $re = Request_lodgings_model::canceledStateService($request_id, 1, $person_id);
            $re1 = Request_tickets_model::cancelTicketService($request_id, $person_id);
            if ($re || $re1) {
                return array('result' => true, 'msg' => '', 'no_solicitud' => $request_id);
            } else {
                return array('result' => false, 'msg' => 'E_SDPH_CANCEL_SOLICITUD', 'no_solicitud' => $request_id);
            }
        } else {
            return array('result' => false, 'msg' => 'E_SDPH_CANCEL_SOLICITUD', 'no_solicitud' => $request_id);
        }
    }


}
