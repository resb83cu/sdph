<?php

class Request_tickets_model extends Model {

    const TABLE_NAME = 'request_tickets';

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
    public function getDataViazul($to, $from, $dateStart, $dateEnd, $motive) {
        $centinela = new Centinela ( );
        $roll_id = $centinela->get_roll_id();
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_ticketviazulstates_model');
        $request_requests_table = 'request_requests';
        $ticket_editviazul_table = 'ticket_editviazul';
        $this->load->model('conf/conf_lodgingtransports_model');
        $this->load->model('conf/conf_provinces_model');
        $this->db->select('request_tickets.request_id, 
							request_requests.request_date,
							request_requests.person_idworker, 
							request_tickets.ticket_date, 
							request_tickets.transport_id,
							request_tickets.province_idfrom, 
							request_tickets.province_idto,
							request_tickets.ticket_state,
							ticket_editviazul.viazulstate_id,
							ticket_editviazul.ticket_viazul_id,
							ticket_editviazul.viazul_voucher,
							request_tickets.ticket_cancel');
        $this->db->from($request_requests_table);
        $this->db->join(self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $request_requests_table . '.request_id', 'inner');
        $this->db->join($ticket_editviazul_table, $ticket_editviazul_table . '.request_id = ' . self::TABLE_NAME . '.request_id AND ' . $ticket_editviazul_table . '.ticket_date = ' . self::TABLE_NAME . '.ticket_date', 'left');
        $this->db->where(self::TABLE_NAME . '.ticket_date >=', $dateStart);
        $this->db->where(self::TABLE_NAME . '.ticket_date <=', $dateEnd);
        $this->db->where(self::TABLE_NAME . '.transport_id', 2);
        if (!empty($motive)) {
            $this->db->where($request_requests_table . '.motive_id', $motive);
        }
        if ($roll_id < 5) {
            $where = "(request_tickets.province_idfrom = " . $centinela->get_province_id() . " OR request_tickets.province_idto = " . $centinela->get_province_id() . ")";
            $this->db->where($where);
        }
        $this->db->order_by('request_requests.person_idworker', 'asc');
        $this->db->order_by('request_tickets.ticket_date', 'asc');
        $this->db->order_by('request_tickets.province_idfrom', 'asc');
        $this->db->order_by('request_tickets.province_idto', 'asc');
        $this->db->limit($to, $from);
        $this->db->distinct();
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $temp = $row->person_idworker;
                $person_worker = Person_persons_model::getNameById($temp);
                $temp = $row->transport_id;
                $transport_name = Conf_lodgingtransports_model::getNameById($temp);
                $temp = $row->province_idfrom;
                $province_namefrom = Conf_provinces_model::getNameById($temp);
                $temp = $row->province_idto;
                $province_nameto = Conf_provinces_model::getNameById($temp);
                $state = '';
                if ($row->ticket_cancel == 1) {
                    $state = 'Cancelada';
                } else if (!empty($row->viazulstate_id)) {
                    $state = Conf_ticketviazulstates_model::getNameById($row->viazulstate_id);
                }
                $value [] = array(
                    'request_id' => $row->request_id,
                    'request_date' => $row->request_date,
                    'ticket_date' => $row->ticket_date,
                    'person_worker' => $person_worker,
                    'transport_name' => $transport_name,
                    'province_namefrom' => $province_namefrom,
                    'province_nameto' => $province_nameto,
                    'viazul_voucher' => $row->viazul_voucher,
                    'state' => $state);
            }
        }
        $cant = $this->getCountDataViazul($dateStart, $dateEnd, $motive);
        echo ("{count : " . $cant . ", data : " . json_encode($value) . "}");
    }

    public function getCountDataViazul($dateStart, $dateEnd, $motive) {
        $centinela = new Centinela ( );
        $roll_id = $centinela->get_roll_id();
        $request_requests_table = 'request_requests';
        $ticket_editviazul_table = 'ticket_editviazul';
        $this->load->model('conf/conf_lodgingtransports_model');
        $this->load->model('conf/conf_provinces_model');
        $this->db->select('request_tickets.request_id, 
							request_requests.request_date,
							request_requests.person_idworker, 
							request_tickets.ticket_date, 
							request_tickets.transport_id,
							request_tickets.province_idfrom, 
							request_tickets.province_idto,
							request_tickets.ticket_state,
							ticket_editviazul.viazulstate_id');

        $this->db->from($request_requests_table);
        $this->db->join(self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $request_requests_table . '.request_id', 'inner');
        $this->db->join($ticket_editviazul_table, $ticket_editviazul_table . '.request_id = ' . self::TABLE_NAME . '.request_id AND ' . $ticket_editviazul_table . '.ticket_date = ' . self::TABLE_NAME . '.ticket_date', 'left');
        $this->db->where(self::TABLE_NAME . '.ticket_date >=', $dateStart);
        $this->db->where(self::TABLE_NAME . '.ticket_date <=', $dateEnd);
        $this->db->where(self::TABLE_NAME . '.transport_id', 2);
        if (!empty($motive)) {
            $this->db->where($request_requests_table . '.motive_id', $motive);
        }
        if ($roll_id < 5) {
            $where = "(request_tickets.province_idfrom = " . $centinela->get_province_id() . " OR request_tickets.province_idto = " . $centinela->get_province_id() . ")";
            $this->db->where($where);
        }
        return $this->db->count_all_results();
    }

    public function getDataAirplane($to, $from, $dateStart, $dateEnd, $motive) {
        $centinela = new Centinela ( );
        $roll_id = $centinela->get_roll_id();
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_ticketviazulstates_model');
        $request_requests_table = 'request_requests';
        $ticket_editairplane_table = 'ticket_editairplane';
        $this->load->model('conf/conf_lodgingtransports_model');
        $this->load->model('conf/conf_provinces_model');
        $this->db->select('request_tickets.request_id, 
                            request_requests.request_date,
                            request_requests.person_idworker, 
                            request_tickets.ticket_date, 
                            request_tickets.transport_id,
                            request_tickets.province_idfrom, 
                            request_tickets.province_idto,
                            request_tickets.ticket_state,
                            ticket_editairplane.state_id,
                            request_tickets.ticket_cancel');
        $this->db->from($request_requests_table);
        $this->db->join(self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $request_requests_table . '.request_id', 'inner');
        $this->db->join($ticket_editairplane_table, $ticket_editairplane_table . '.request_id = ' . self::TABLE_NAME . '.request_id AND ' . $ticket_editairplane_table . '.ticket_date = ' . self::TABLE_NAME . '.ticket_date', 'left');
        $this->db->where(self::TABLE_NAME . '.ticket_date >=', $dateStart);
        $this->db->where(self::TABLE_NAME . '.ticket_date <=', $dateEnd);
        $this->db->where(self::TABLE_NAME . '.transport_id', 3);
        if (!empty($motive)) {
            $this->db->where($request_requests_table . '.motive_id', $motive);
        }
        if ($roll_id < 5) {
            $where = "(request_tickets.province_idfrom = " . $centinela->get_province_id() . " OR request_tickets.province_idto = " . $centinela->get_province_id() . ")";
            $this->db->where($where);
        }
        $this->db->limit($to, $from);
        $this->db->distinct();
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $temp = $row->person_idworker;
                $person_worker = Person_persons_model::getNameById($temp);
                $temp = $row->transport_id;
                $transport_name = Conf_lodgingtransports_model::getNameById($temp);
                $temp = $row->province_idfrom;
                $province_namefrom = Conf_provinces_model::getNameById($temp);
                $temp = $row->province_idto;
                $province_nameto = Conf_provinces_model::getNameById($temp);
                $state = '';
                if ($row->ticket_cancel == 1) {
                    $state = 'Cancelada';
                } else if (!empty($row->state_id)) {
                    $state = Conf_ticketviazulstates_model::getNameById($row->state_id);
                }
                $value [] = array('request_id' => $row->request_id, 'request_date' => $row->request_date, 'ticket_date' => $row->ticket_date, 'person_worker' => $person_worker, 'transport_name' => $transport_name, 'province_namefrom' => $province_namefrom, 'province_nameto' => $province_nameto, 'state' => $state);
            }
        }
        $cant = $this->getCountDataAirplane($dateStart, $dateEnd, $motive);
        echo ("{count : " . $cant . ", data : " . json_encode($value) . "}");
    }

    public function getCountDataAirplane($dateStart, $dateEnd, $motive) {
        $centinela = new Centinela ( );
        $roll_id = $centinela->get_roll_id();
        $request_requests_table = 'request_requests';
        $ticket_editairplane_table = 'ticket_editairplane';
        $this->load->model('conf/conf_lodgingtransports_model');
        $this->load->model('conf/conf_provinces_model');
        $this->db->select('request_tickets.request_id, 
							request_requests.request_date,
							request_requests.person_idworker, 
							request_tickets.ticket_date, 
							request_tickets.transport_id,
							request_tickets.province_idfrom, 
							request_tickets.province_idto,
							request_tickets.ticket_state,
							ticket_editairplane.state_id');

        $this->db->from($request_requests_table);
        $this->db->join(self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $request_requests_table . '.request_id', 'inner');
        $this->db->join($ticket_editairplane_table, $ticket_editairplane_table . '.request_id = ' . self::TABLE_NAME . '.request_id AND ' . $ticket_editairplane_table . '.ticket_date = ' . self::TABLE_NAME . '.ticket_date', 'left');
        $this->db->where(self::TABLE_NAME . '.ticket_date >=', $dateStart);
        $this->db->where(self::TABLE_NAME . '.ticket_date <=', $dateEnd);
        $this->db->where(self::TABLE_NAME . '.transport_id', 3);
        if (!empty($motive)) {
            $this->db->where($request_requests_table . '.motive_id', $motive);
        }
        if ($roll_id < 5) {
            $where = "(request_tickets.province_idfrom = " . $centinela->get_province_id() . " OR request_tickets.province_idto = " . $centinela->get_province_id() . ")";
            $this->db->where($where);
        }
        return $this->db->count_all_results();
    }

    public function getDataShip($to, $from, $dateStart, $dateEnd, $motive) {
        $centinela = new Centinela ( );
        $roll_id = $centinela->get_roll_id();
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_ticketviazulstates_model');
        $request_requests_table = 'request_requests';
        $ticket_editship_table = 'ticket_editship';
        $this->load->model('conf/conf_lodgingtransports_model');
        $this->load->model('conf/conf_provinces_model');
        $this->db->select('request_tickets.request_id, 
							request_requests.request_date,
							request_requests.person_idworker, 
							request_tickets.ticket_date, 
							request_tickets.transport_id,
							request_tickets.province_idfrom, 
							request_tickets.province_idto,
							request_tickets.ticket_state,
							ticket_editship.state_id,
							request_tickets.ticket_cancel');
        $this->db->from($request_requests_table);
        $this->db->join(self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $request_requests_table . '.request_id', 'inner');
        $this->db->join($ticket_editship_table, $ticket_editship_table . '.request_id = ' . self::TABLE_NAME . '.request_id AND ' . $ticket_editship_table . '.ticket_date = ' . self::TABLE_NAME . '.ticket_date', 'left');
        $this->db->where(self::TABLE_NAME . '.ticket_date >=', $dateStart);
        $this->db->where(self::TABLE_NAME . '.ticket_date <=', $dateEnd);
        $this->db->where(self::TABLE_NAME . '.transport_id', 5);
        if (!empty($motive)) {
            $this->db->where($request_requests_table . '.motive_id', $motive);
        }
        if ($roll_id < 5) {
            $where = "(request_tickets.province_idfrom = " . $centinela->get_province_id() . " OR request_tickets.province_idto = " . $centinela->get_province_id() . ")";
            $this->db->where($where);
        }
        $this->db->limit($to, $from);
        $this->db->distinct();
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $temp = $row->person_idworker;
                $person_worker = Person_persons_model::getNameById($temp);
                $temp = $row->transport_id;
                $transport_name = Conf_lodgingtransports_model::getNameById($temp);
                $temp = $row->province_idfrom;
                $province_namefrom = Conf_provinces_model::getNameById($temp);
                $temp = $row->province_idto;
                $province_nameto = Conf_provinces_model::getNameById($temp);
                $state = '';
                if ($row->ticket_cancel == 1) {
                    $state = 'Cancelada';
                } else if (!empty($row->state_id)) {
                    $state = Conf_ticketviazulstates_model::getNameById($row->state_id);
                }
                $value [] = array('request_id' => $row->request_id, 'request_date' => $row->request_date, 'ticket_date' => $row->ticket_date, 'person_worker' => $person_worker, 'transport_name' => $transport_name, 'province_namefrom' => $province_namefrom, 'province_nameto' => $province_nameto, 'state' => $state);
            }
        }
        $cant = $this->getCountDataShip($dateStart, $dateEnd, $motive);
        echo ("{count : " . $cant . ", data : " . json_encode($value) . "}");
    }

    public function getCountDataShip($dateStart, $dateEnd, $motive) {
        $centinela = new Centinela ( );
        $roll_id = $centinela->get_roll_id();
        $request_requests_table = 'request_requests';
        $ticket_editship_table = 'ticket_editship';
        $this->load->model('conf/conf_lodgingtransports_model');
        $this->load->model('conf/conf_provinces_model');
        $this->db->select('request_tickets.request_id, 
							request_requests.request_date,
							request_requests.person_idworker, 
							request_tickets.ticket_date, 
							request_tickets.transport_id,
							request_tickets.province_idfrom, 
							request_tickets.province_idto,
							request_tickets.ticket_state,
							ticket_editship.state_id');

        $this->db->from($request_requests_table);
        $this->db->join(self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $request_requests_table . '.request_id', 'inner');
        $this->db->join($ticket_editship_table, $ticket_editship_table . '.request_id = ' . self::TABLE_NAME . '.request_id AND ' . $ticket_editship_table . '.ticket_date = ' . self::TABLE_NAME . '.ticket_date', 'left');
        $this->db->where(self::TABLE_NAME . '.ticket_date >=', $dateStart);
        $this->db->where(self::TABLE_NAME . '.ticket_date <=', $dateEnd);
        $this->db->where(self::TABLE_NAME . '.transport_id', 5);
        if (!empty($motive)) {
            $this->db->where($request_requests_table . '.motive_id', $motive);
        }
        if ($roll_id < 5) {
            $where = "(request_tickets.province_idfrom = " . $centinela->get_province_id() . " OR request_tickets.province_idto = " . $centinela->get_province_id() . ")";
            $this->db->where($where);
        }
        return $this->db->count_all_results();
    }

    public function getDataEtecsa($to, $from, $ticketDate, $transportItinerary, $motive, $state, $isPdf = 'no') {
        $this->load->model('person/person_persons_model');
        $request_requests_table = 'request_requests';
        $ticket_editetecsa_table = 'ticket_editetecsa';
        $this->load->model('conf/conf_lodgingtransports_model');
        $this->load->model('conf/conf_ticketrequeststates_model');
        $this->load->model('lodging/lodging_edit_model');
        $this->load->model('conf/conf_provinces_model');
        $this->load->model('conf/conf_hotels_model');
        $this->db->select('request_tickets.request_id, 
							request_requests.request_date,
							request_requests.person_idworker, 
							request_tickets.ticket_date, 
							request_tickets.transport_id, 
							request_tickets.transport_itinerary,
							request_tickets.province_idfrom, 
							request_tickets.province_idto,
							request_tickets.ticket_state,
							ticket_editetecsa.state_id,
							request_tickets.ticket_cancel');
        $this->db->from($request_requests_table);
        $this->db->join(self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $request_requests_table . '.request_id');
        $this->db->join($ticket_editetecsa_table, $ticket_editetecsa_table . '.request_id = ' . self::TABLE_NAME . '.request_id AND ' . $ticket_editetecsa_table . '.ticket_date = ' . self::TABLE_NAME . '.ticket_date', 'left');
        $this->db->where(self::TABLE_NAME . '.ticket_date', $ticketDate);
        $this->db->where(self::TABLE_NAME . '.transport_itinerary', $transportItinerary);
        $this->db->where(self::TABLE_NAME . '.transport_id', 1);
        if (!empty($motive)) {
            $this->db->where($request_requests_table . '.motive_id', $motive);
        }
        if (!empty($state)) {
            $this->db->where($ticket_editetecsa_table . '.state_id', $state);
        }
        $this->db->order_by("ticket_editetecsa.state_id", "asc");
        if ($transportItinerary == 'Habana-Santiago') {
            $this->db->order_by("request_tickets.province_idfrom", "asc");
            $this->db->order_by("request_tickets.province_idto", "asc");
        } else {
            $this->db->order_by("request_tickets.province_idfrom", "desc");
            $this->db->order_by("request_tickets.province_idto", "desc");
        }

        //$this->db->limit($to, $from);
        $this->db->distinct();
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $temp = $row->person_idworker;
                $person_worker = Person_persons_model::getNameById($temp);
                $person_identity = Person_persons_model::getIdentityById($temp);
                $temp = $row->transport_id;
                $transport_name = Conf_lodgingtransports_model::getNameById($temp);
                $temp = $row->province_idfrom;
                $province_namefrom = Conf_provinces_model::getNameById($temp);
                $temp = $row->province_idto;
                $province_nameto = Conf_provinces_model::getNameById($temp);
                $state = '';
                if ($row->ticket_cancel == 1) {
                    $state = 'Cancelada';
                } else if (!empty($row->state_id)) {
                    $state = Conf_ticketrequeststates_model::getNameById($row->state_id);
                }
                $hotel = self::getHotelByTicket($row->person_idworker, $ticketDate);
                $value [] = array('request_id' => $row->request_id,
                    'request_date' => $row->request_date,
                    'ticket_date' => $row->ticket_date,
                    'person_worker' => $person_worker,
                    'person_identity' => $person_identity,
                    'transport_name' => $transport_name,
                    'transport_itinerary' => $row->transport_itinerary,
                    'province_namefrom' => $province_namefrom,
                    'province_nameto' => $province_nameto,
                    'hotel' => $hotel,
                    'state' => $state,
                    'itinerarsy' => $transportItinerary);
            }
        }
        $cant = $this->getCountDataEtecsa($ticketDate, $transportItinerary, $motive);
        if ($isPdf == 'no')
            echo ("{count : " . $cant . ", data : " . json_encode($value) . "}");
        else
            return $value;
    }

    public function getCountDataEtecsa($ticketDate, $transportItinerary, $motive) {
        $this->load->model('person/person_persons_model');
        $request_requests_table = 'request_requests';
        $ticket_editetecsa_table = 'ticket_editetecsa';
        $this->load->model('conf/conf_lodgingtransports_model');
        $this->load->model('conf/conf_ticketrequeststates_model');
        $this->load->model('conf/conf_provinces_model');
        $this->db->select('request_tickets.request_id, 
							request_requests.request_date,
							request_requests.person_idworker, 
							request_tickets.ticket_date, 
							request_tickets.transport_id, 
							request_tickets.transport_itinerary,
							request_tickets.ticket_state
							ticket_editetecsa.state_id');
        $this->db->from($request_requests_table);
        $this->db->join(self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $request_requests_table . '.request_id', 'inner');
        $this->db->join($ticket_editetecsa_table, $ticket_editetecsa_table . '.request_id = ' . self::TABLE_NAME . '.request_id AND ' . $ticket_editetecsa_table . '.ticket_date = ' . self::TABLE_NAME . '.ticket_date', 'left');
        $this->db->where(self::TABLE_NAME . '.ticket_date', $ticketDate);
        $this->db->where(self::TABLE_NAME . '.transport_itinerary', $transportItinerary);
        $this->db->where(self::TABLE_NAME . '.transport_id', 1);
        if (!empty($motive)) {
            $this->db->where($request_requests_table . '.motive_id', $motive);
        }
        return $this->db->count_all_results();
    }

    public function getDataEventual($to, $from, $dateStart, $dateEnd, $motive, $state) {
        $centinela = new Centinela ( );
        $roll_id = $centinela->get_roll_id();
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_hotels_model');
        $request_requests_table = 'request_requests';
        $ticket_editeventual_table = 'ticket_editeventual';
        $this->load->model('conf/conf_lodgingtransports_model');
        $this->load->model('conf/conf_ticketrequeststates_model');
        $this->load->model('conf/conf_provinces_model');
        $this->db->select('request_tickets.request_id, 
							request_requests.request_date,
							request_requests.person_idworker, 
							request_tickets.ticket_date, 
							request_tickets.transport_id,
							request_tickets.province_idfrom, 
							request_tickets.province_idto,
							request_tickets.ticket_state,
							ticket_editeventual.state_id');

        $this->db->from($request_requests_table);
        $this->db->join(self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $request_requests_table . '.request_id', 'inner');
        $this->db->join($ticket_editeventual_table, $ticket_editeventual_table . '.request_id = ' . self::TABLE_NAME . '.request_id AND ' . $ticket_editeventual_table . '.ticket_date = ' . self::TABLE_NAME . '.ticket_date', 'left');
        $this->db->where(self::TABLE_NAME . '.ticket_date >=', $dateStart);
        $this->db->where(self::TABLE_NAME . '.ticket_date <=', $dateEnd);
        $this->db->where(self::TABLE_NAME . '.transport_id', 4);
        if (!empty($motive)) {
            $this->db->where($request_requests_table . '.motive_id', $motive);
        }
        if (!empty($state)) {
            $this->db->where('ticket_editeventual.state_id', $state);
        }
        if ($roll_id < 5) {
            $where = "(request_tickets.province_idfrom = " . $centinela->get_province_id() . " OR request_tickets.province_idto = " . $centinela->get_province_id() . ")";
            $this->db->where($where);
        }
        $this->db->order_by("ticket_editeventual.state_id", "asc");
        $this->db->order_by("request_tickets.province_idfrom", "asc");
        $this->db->order_by("request_tickets.province_idto", "asc");
        //$this->db->limit($to, $from);
        $this->db->distinct();
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $temp = $row->person_idworker;
                $person_worker = Person_persons_model::getNameById($temp);
                $person_identity = Person_persons_model::getIdentityById($temp);
                $temp = $row->transport_id;
                $transport_name = Conf_lodgingtransports_model::getNameById($temp);
                $temp = $row->province_idfrom;
                $province_namefrom = Conf_provinces_model::getNameById($temp);
                $temp = $row->province_idto;
                $province_nameto = Conf_provinces_model::getNameById($temp);
                $states = '';
                if ($row->ticket_state > 0) {
                    $temp = $row->state_id;
                    $states = is_null($temp) ? '' : Conf_ticketrequeststates_model::getNameById($temp);
                }

                $hotel = '---';

                $res = $this->db->query('select hotel_id from lodging_edit where lodging_edit.lodging_noshow != \'on\' and lodging_edit.request_id = ' . $row->request_id);
                if ($res->result() != null) {
                    foreach ($res->result() as $row2) { //esta en esta tabla
                        $hotel_id = $row2->hotel_id;
                        $hotel = Conf_hotels_model::getNameById($hotel_id);
                    }
                }

                $value [] = array('request_id' => $row->request_id,
                    'request_date' => $row->request_date,
                    'ticket_date' => $row->ticket_date,
                    'person_worker' => $person_worker,
                    'person_identity' => $person_identity,
                    'transport_name' => $transport_name,
                    'province_namefrom' => $province_namefrom,
                    'province_nameto' => $province_nameto,
                    'hotel' => $hotel,
                    'state' => $states);
            }
        }
        $cant = $this->getCountDataEventual($dateStart, $dateEnd, $motive, $state);
        echo ("{count : " . $cant . ", data : " . json_encode($value) . "}");
    }

    public function getDataEventualPDF($dateStart, $dateEnd, $motive, $state) {
        $centinela = new Centinela ( );
        $roll_id = $centinela->get_roll_id();
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_hotels_model');
        $request_requests_table = 'request_requests';
        $ticket_editeventual_table = 'ticket_editeventual';
        $this->load->model('conf/conf_lodgingtransports_model');
        $this->load->model('conf/conf_ticketrequeststates_model');
        $this->load->model('conf/conf_provinces_model');
        $this->db->select('request_tickets.request_id, 
							request_requests.request_date,
							request_requests.person_idworker, 
							request_tickets.ticket_date, 
							request_tickets.transport_id,
							request_tickets.province_idfrom, 
							request_tickets.province_idto,
							request_tickets.ticket_state,
							ticket_editeventual.state_id');
        $this->db->from($request_requests_table);
        $this->db->join(self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $request_requests_table . '.request_id', 'inner');
        $this->db->join($ticket_editeventual_table, $ticket_editeventual_table . '.request_id = ' . self::TABLE_NAME . '.request_id AND ' . $ticket_editeventual_table . '.ticket_date = ' . self::TABLE_NAME . '.ticket_date', 'left');
        $this->db->where(self::TABLE_NAME . '.ticket_date >=', $dateStart);
        $this->db->where(self::TABLE_NAME . '.ticket_date <=', $dateEnd);
        $this->db->where(self::TABLE_NAME . '.transport_id', 4);
        if (!empty($motive)) {
            $this->db->where($request_requests_table . '.motive_id', $motive);
        }
        if (!empty($state)) {
            $this->db->where('ticket_editeventual.state_id', $state);
        }
        if ($roll_id < 5) {
            $where = "(request_tickets.province_idfrom = " . $centinela->get_province_id() . " OR request_tickets.province_idto = " . $centinela->get_province_id() . ")";
            $this->db->where($where);
        }
        $this->db->order_by("ticket_editeventual.state_id", "asc");
        $this->db->order_by("request_tickets.province_idfrom", "asc");
        $this->db->order_by("request_tickets.province_idto", "asc");
        $this->db->distinct();
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $temp = $row->person_idworker;
                $person_worker = Person_persons_model::getNameById($temp);
                $person_identity = Person_persons_model::getIdentityById($temp);
                $temp = $row->transport_id;
                $transport_name = Conf_lodgingtransports_model::getNameById($temp);
                $temp = $row->province_idfrom;
                $province_namefrom = Conf_provinces_model::getNameById($temp);
                $temp = $row->province_idto;
                $province_nameto = Conf_provinces_model::getNameById($temp);
                $states = '';
                if ($row->ticket_state > 0) {
                    $temp = $row->state_id;
                    $states = is_null($temp) ? '' : Conf_ticketrequeststates_model::getNameById($temp);
                }

                $hotel = '---';

                $res = $this->db->query('select hotel_id from lodging_edit where lodging_edit.lodging_noshow != \'on\' and lodging_edit.request_id = ' . $row->request_id);
                if ($res->result() != null) {
                    foreach ($res->result() as $row2) {
                        $hotel_id = $row2->hotel_id;
                        $hotel = Conf_hotels_model::getNameById($hotel_id);
                    }
                }

                $value [] = array('request_id' => $row->request_id,
                    'request_date' => $row->request_date,
                    'ticket_date' => $row->ticket_date,
                    'person_worker' => $person_worker,
                    'person_identity' => $person_identity,
                    'transport_name' => $transport_name,
                    'province_namefrom' => $province_namefrom,
                    'province_nameto' => $province_nameto,
                    'hotel' => $hotel,
                    'state' => $states);
            }
        }
        return $value;
    }

    public function getCountDataEventual($dateStart, $dateEnd, $motive, $state) {
        $centinela = new Centinela ( );
        $roll_id = $centinela->get_roll_id();
        $this->load->model('person/person_persons_model');
        $request_requests_table = 'request_requests';
        $ticket_editeventual_table = 'ticket_editeventual';
        $this->load->model('conf/conf_lodgingtransports_model');
        $this->load->model('conf/conf_ticketrequeststates_model');
        $this->load->model('conf/conf_provinces_model');
        $this->db->select('request_tickets.request_id, 
							request_requests.request_date,
							request_requests.person_idworker, 
							request_tickets.ticket_date, 
							request_tickets.transport_id,
							request_tickets.province_idfrom, 
							request_tickets.province_idto,
							request_tickets.ticket_state,
							ticket_editeventual.state_id');
        $this->db->from($request_requests_table);
        $this->db->join(self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $request_requests_table . '.request_id', 'inner');
        $this->db->join($ticket_editeventual_table, $ticket_editeventual_table . '.request_id = ' . self::TABLE_NAME . '.request_id AND ' . $ticket_editeventual_table . '.ticket_date = ' . self::TABLE_NAME . '.ticket_date', 'left');
        $this->db->where(self::TABLE_NAME . '.ticket_date >=', $dateStart);
        $this->db->where(self::TABLE_NAME . '.ticket_date <=', $dateEnd);
        $this->db->where(self::TABLE_NAME . '.transport_id', 4);
        if (!empty($motive)) {
            $this->db->where($request_requests_table . '.motive_id', $motive);
        }
        if (!empty($state)) {
            $this->db->where('ticket_editeventual.state_id', $state);
        }
        if ($roll_id < 5) {
            $this->db->where(self::TABLE_NAME . '.province_idfrom =', $centinela->get_province_id());
            $this->db->or_where(self::TABLE_NAME . '.province_idto =', $centinela->get_province_id());
        }
        return $this->db->count_all_results();
    }

    public function getDataCollect($dateStart, $dateEnd) {
        $centinela = new Centinela ( );
        $roll_id = $centinela->get_roll_id();
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_ticketviazulstates_model');
        $request_requests_table = 'request_requests';
        $ticket_editviazul_table = 'ticket_editviazul';
        $this->load->model('conf/conf_lodgingtransports_model');
        $this->load->model('conf/conf_provinces_model');
        $begin = substr($dateStart, 0, 4) . "-" . substr($dateStart, 5, 2) . "-" . substr($dateStart, 8, 2);
        $end = substr($dateEnd, 0, 4) . "-" . substr($dateEnd, 5, 2) . "-" . substr($dateEnd, 8, 2);
        $sql = 'select distinct
						request_tickets.request_id,
						request_requests.person_idworker,
						request_requests.person_idrequestedby,
						request_tickets.ticket_date, 
						request_tickets.transport_id,
						request_tickets.province_idfrom, 
						request_tickets.province_idto,
						request_tickets.request_collect
					from request_requests
					inner join request_tickets on request_requests.request_id = request_tickets.request_id
					inner join person_persons on request_requests.person_idworker = person_persons.person_id
					where (request_tickets.ticket_date >= ? and request_tickets.ticket_date <= ?)
					and (request_tickets.province_idfrom = 5 or request_tickets.province_idto = 5)
					and (request_tickets.transport_id = 2 or request_tickets.transport_id = 3 or request_tickets.transport_id = 5)
					and (request_requests.person_idrequestedby = 24 or request_requests.person_idrequestedby = 27 or request_requests.person_idrequestedby = 19)
					and (person_persons.province_id != 5)
					order by request_tickets.ticket_date desc';
        $result = $this->db->query($sql, array($begin, $end));
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $person = Person_persons_model::getNameById($row->person_idworker);
                $transporte = Conf_lodgingtransports_model::getNameById($row->transport_id);
                $provinceFrom = Conf_provinces_model::getNameById($row->province_idfrom);
                $provinceTo = Conf_provinces_model::getNameById($row->province_idto);
                $request_id = $row->request_id;
                $state = $row->request_collect == 1 ? 'SI' : 'NO';
                //inicio de trasnporte=1 etecsa
                if ($row->transport_id == 2) { //ticket_viazul
                    //$hotel = '---';
                    //$tipo = '---';
                    $exithour = '---';
                    $arrivalhour = '---';
                    $res = $this->db->query('select viazulstate_id, 
													viazul_exithour, 
													viazul_arrivalhour 
												from 
													ticket_editviazul 
												where 
													ticket_editviazul.request_id=' . $row->request_id . ' and ticket_editviazul.ticket_date=\'' . $row->ticket_date . ' \'');
                    if ($res->result() != null) { //existe y esta editada por supuesto en esta tabla
                        foreach ($res->result() as $row2) {
                            $tipoEdit = $row2->viazulstate_id;
                            if ($row2->viazulstate_id != 3 && $row2->viazulstate_id != 4) {
                                $exithour = substr($row2->viazul_exithour, 0, 5);
                                $arrivalhour = substr($row2->viazul_arrivalhour, 0, 5);
                            }
                        }
                        /* if ($tipoEdit != 0) 
                          $tipo = Conf_ticketviazulstates_model::getNameById ( $tipoEdit ); */
                    } //fin del if !=null
                    $value [] = array('request_id' => $row->request_id,
                        'person' => $person,
                        //'identity' => $identity, 
                        'ticketdate' => $row->ticket_date,
                        'province_from' => $provinceFrom,
                        'province_to' => $provinceTo,
                        'transport' => $transporte,
                        'state' => $state,
                        //'hotel' => $hotel,
                        'exithour' => $exithour,
                        'arrivalhour' => $arrivalhour);
                } //fin $row->transport_id==1 	
                //inicio de trasnporte=2 viazul
                if ($row->transport_id == 3) { //ticket_editairplane
                    //$hotel = '---';
                    //$tipo = '---';
                    $exithour = '---';
                    $arrivalhour = '---';
                    $res = $this->db->query('select 
													state_id, 
													airplane_exithour, 
													airplane_arrivalhour 
												from 
													ticket_editairplane 
												where 
													ticket_editairplane.request_id=' . $row->request_id . ' and ticket_editairplane.ticket_date=\'' . $row->ticket_date . ' \'');
                    $tipoEdit = 0; //estado por ejemplo propia,extra1,extra2...etc
                    if ($res->result() != null) { //existe y esta editada por supuesto en esta tabla
                        foreach ($res->result() as $row2) {
                            $tipoEdit = $row2->state_id;
                            if ($tipoEdit != 3 && $tipoEdit != 4) {
                                $exithour = substr($row2->airplane_exithour, 0, 5);
                                $arrivalhour = substr($row2->airplane_arrivalhour, 0, 5);
                            }
                        }
                        /* if ($tipoEdit != 0) 
                          $tipo = Conf_ticketviazulstates_model::getNameById ( $tipoEdit ); */
                    } //fin del if !=null
                    $value [] = array('request_id' => $row->request_id,
                        'person' => $person,
                        //'identity' => $identity, 
                        'ticketdate' => $row->ticket_date,
                        'province_from' => $provinceFrom,
                        'province_to' => $provinceTo,
                        'transport' => $transporte,
                        'state' => $state,
                        //'hotel' => $hotel,
                        'exithour' => $exithour,
                        'arrivalhour' => $arrivalhour);
                }
                if ($row->transport_id == 5) { //ticket_editship
                    //$hotel = '---';
                    //$tipo = '---';
                    $exithour = '---';
                    $arrivalhour = '---';
                    $res = $this->db->query('select 
													state_id, 
													ship_exithour, 
													ship_arrivalhour 
												from 
													ticket_editship 
												where 
													ticket_editship.request_id=' . $row->request_id . ' and ticket_editship.ticket_date=\'' . $row->ticket_date . ' \'');
                    $tipoEdit = 0; //estado por ejemplo propia,extra1,extra2...etc
                    if ($res->result() != null) { //existe y esta editada por supuesto en esta tabla
                        foreach ($res->result() as $row2) {
                            $tipoEdit = $row2->state_id;
                            if ($tipoEdit != 3 && $tipoEdit != 4) {
                                $exithour = substr($row2->ship_exithour, 0, 5);
                                $arrivalhour = substr($row2->ship_arrivalhour, 0, 5);
                            }
                        }
                        /* if ($tipoEdit != 0) 
                          $tipo = Conf_ticketviazulstates_model::getNameById ( $tipoEdit ); */
                    } //fin del if !=null
                    $value [] = array('request_id' => $row->request_id,
                        'person' => $person,
                        //'identity' => $identity, 
                        'ticketdate' => $row->ticket_date,
                        'province_from' => $provinceFrom,
                        'province_to' => $provinceTo,
                        'transport' => $transporte,
                        'state' => $state,
                        //'hotel' => $hotel,
                        'exithour' => $exithour,
                        'arrivalhour' => $arrivalhour);
                }
            }
        }
        echo ("{data : " . json_encode($value) . "}");
    }

    public function getDataCollectPdf($date, $isPdf = 'no') {
        $centinela = new Centinela ( );
        $roll_id = $centinela->get_roll_id();
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_ticketviazulstates_model');
        $request_requests_table = 'request_requests';
        $ticket_editviazul_table = 'ticket_editviazul';
        $this->load->model('conf/conf_lodgingtransports_model');
        $this->load->model('conf/conf_provinces_model');
        $begin = substr($date, 0, 4) . "-" . substr($date, 5, 2) . "-" . substr($date, 8, 2);
        $sql = 'select distinct
						request_tickets.request_id,
						request_requests.person_idworker,
						request_requests.person_idrequestedby,
						request_tickets.ticket_date, 
						request_tickets.transport_id,
						request_tickets.province_idfrom, 
						request_tickets.province_idto,
						request_tickets.request_collect
					from request_requests
					inner join request_tickets on request_requests.request_id = request_tickets.request_id
					where (request_tickets.ticket_date = ?)
					and (request_tickets.request_collect = 1)';
        $result = $this->db->query($sql, array($begin));
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $person = Person_persons_model::getNameById($row->person_idworker);
                $transporte = Conf_lodgingtransports_model::getNameById($row->transport_id);
                $provinceFrom = Conf_provinces_model::getNameById($row->province_idfrom);
                $provinceTo = Conf_provinces_model::getNameById($row->province_idto);
                $request_id = $row->request_id;
                $hotel = self::getHotelByTicket($row->person_idworker, $date);
                if ($row->transport_id == 2) { //ticket_viazul
                    //$hotel = '---';
                    //$tipo = '---';
                    $exithour = '---';
                    $arrivalhour = '---';
                    $res = $this->db->query('select viazulstate_id, 
													viazul_exithour, 
													viazul_arrivalhour 
												from 
													ticket_editviazul 
												where 
													ticket_editviazul.request_id=' . $row->request_id . ' and ticket_editviazul.ticket_date=\'' . $row->ticket_date . ' \'');
                    if ($res->result() != null) { //existe y esta editada por supuesto en esta tabla
                        foreach ($res->result() as $row2) {
                            $tipoEdit = $row2->viazulstate_id;
                            if ($row2->viazulstate_id != 3 && $row2->viazulstate_id != 4) {
                                $exithour = substr($row2->viazul_exithour, 0, 5);
                                $arrivalhour = substr($row2->viazul_arrivalhour, 0, 5);
                            }
                        }
                        /* if ($tipoEdit != 0) 
                          $tipo = Conf_ticketviazulstates_model::getNameById ( $tipoEdit ); */
                    } //fin del if !=null
                    $value [] = array('request_id' => $row->request_id,
                        'person' => $person,
                        //'identity' => $identity, 
                        'ticketdate' => $row->ticket_date,
                        'province_from' => $provinceFrom,
                        'province_to' => $provinceTo,
                        'transport' => $transporte,
                        'hotel' => $hotel,
                        'exithour' => $exithour,
                        'arrivalhour' => $arrivalhour);
                } //fin $row->transport_id==1 	
                //inicio de trasnporte=2 viazul
                if ($row->transport_id == 3) { //ticket_editairplane
                    //$hotel = '---';
                    //$tipo = '---';
                    $exithour = '---';
                    $arrivalhour = '---';
                    $res = $this->db->query('select 
													state_id, 
													airplane_exithour, 
													airplane_arrivalhour 
												from 
													ticket_editairplane 
												where 
													ticket_editairplane.request_id=' . $row->request_id . ' and ticket_editairplane.ticket_date=\'' . $row->ticket_date . ' \'');
                    $tipoEdit = 0; //estado por ejemplo propia,extra1,extra2...etc
                    if ($res->result() != null) { //existe y esta editada por supuesto en esta tabla
                        foreach ($res->result() as $row2) {
                            $tipoEdit = $row2->state_id;
                            if ($tipoEdit != 3 && $tipoEdit != 4) {
                                $exithour = substr($row2->airplane_exithour, 0, 5);
                                $arrivalhour = substr($row2->airplane_arrivalhour, 0, 5);
                            }
                        }
                        /* if ($tipoEdit != 0) 
                          $tipo = Conf_ticketviazulstates_model::getNameById ( $tipoEdit ); */
                    } //fin del if !=null
                    $value [] = array('request_id' => $row->request_id,
                        'person' => $person,
                        //'identity' => $identity, 
                        'ticketdate' => $row->ticket_date,
                        'province_from' => $provinceFrom,
                        'province_to' => $provinceTo,
                        'transport' => $transporte,
                        'hotel' => $hotel,
                        'exithour' => $exithour,
                        'arrivalhour' => $arrivalhour);
                }
                if ($row->transport_id == 5) { //ticket_editship
                    //$hotel = '---';
                    //$tipo = '---';
                    $exithour = '---';
                    $arrivalhour = '---';
                    $res = $this->db->query('select 
													state_id, 
													ship_exithour, 
													ship_arrivalhour 
												from 
													ticket_editship 
												where 
													ticket_editship.request_id=' . $row->request_id . ' and ticket_editship.ticket_date=\'' . $row->ticket_date . ' \'');
                    $tipoEdit = 0; //estado por ejemplo propia,extra1,extra2...etc
                    if ($res->result() != null) { //existe y esta editada por supuesto en esta tabla
                        foreach ($res->result() as $row2) {
                            $tipoEdit = $row2->state_id;
                            if ($tipoEdit != 3 && $tipoEdit != 4) {
                                $exithour = substr($row2->ship_exithour, 0, 5);
                                $arrivalhour = substr($row2->ship_arrivalhour, 0, 5);
                            }
                        }
                        /* if ($tipoEdit != 0) 
                          $tipo = Conf_ticketviazulstates_model::getNameById ( $tipoEdit ); */
                    } //fin del if !=null
                    $value [] = array('request_id' => $row->request_id,
                        'person' => $person,
                        //'identity' => $identity, 
                        'ticketdate' => $row->ticket_date,
                        'province_from' => $provinceFrom,
                        'province_to' => $provinceTo,
                        'transport' => $transporte,
                        'hotel' => $hotel,
                        'exithour' => $exithour,
                        'arrivalhour' => $arrivalhour);
                }
            }
        }
        if ($isPdf == 'si') { //devuelve todos por exceso
            return $value;
        } else { //el cant es filtrado
            echo ("{data : " . json_encode($value) . "}"); // este valor se le asigna al return del setDatagridconditional para pasarle
        }
    }

    public function getHotelByTicket($person_id, $date) {
        $this->load->model('conf/conf_hotels_model');
        /* $this->db->select ( 'lodging_edit.hotel_id' );
          $this->db->from ( 'request_requests' );
          $this->db->join ( 'request_lodgings', 'request_lodgings.request_id = request_requests.request_id', 'inner' );
          $this->db->join ( 'lodging_edit', 'lodging_edit.request_id = request_lodgings.request_id', 'left' );

          $this->db->where ( 'request_requests.person_idworker', $person_id );
          $this->db->where ( 'request_lodgings.lodging_entrancedate', $date );
          //$this->db->or_where ( 'request_lodgings.lodging_exitdate', $date );
          $this->db->distinct ();
          $result = $this->db->get (); */
        $begin = substr($date, 0, 4) . "-" . substr($date, 5, 2) . "-" . substr($date, 8, 2);
        $sql = 'select  
					lodging_edit.hotel_id
				from request_requests
				inner join request_lodgings on request_lodgings.request_id = request_requests.request_id
				left join lodging_edit on lodging_edit.request_id = request_lodgings.request_id
				where (request_requests.person_idworker = ' . $person_id . ' )
				and ( request_lodgings.lodging_entrancedate = ? or request_lodgings.lodging_exitdate = ? )
				and ( request_lodgings.lodging_canceled != 1 )';
        $result = $this->db->query($sql, array($begin, $begin));
        $hotel = '---';
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                if (!empty($row->hotel_id)) {
                    $temp = $row->hotel_id;
                    $hotel = Conf_hotels_model::getNameById($temp);
                }
            }
        }
        return $hotel;
    }

    public function insertCollect($request_id, $ticket_date) {
        $data = array('request_collect' => 1);
        $this->db->where('request_id', $request_id);
        $this->db->where('ticket_date', $ticket_date);
        $this->db->trans_begin();
        $re = $this->db->update(self::TABLE_NAME, $data);
        $logs = new Logs ( );

        $mywhere = 'where request_id = ' . $request_id . ' and request_id = ' . $request_id;
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

    public function cancelCollect($request_id, $ticket_date) {
        $data = array('request_collect' => 0);
        $this->db->where('request_id', $request_id);
        $this->db->where('ticket_date', $ticket_date);
        $this->db->trans_begin();
        $re = $this->db->update(self::TABLE_NAME, $data);
        $logs = new Logs ( );

        $mywhere = 'where request_id = ' . $request_id . ' and request_id = ' . $request_id;
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
     * funcion que devuelve la cantidad de registros en la tabla ejemplo
     *
     */
    public function getCant() {
        return $this->db->count_all(self::TABLE_NAME);
    }

    public function canUpdateTicket($request_id) {
        $request_requests_table = 'request_requests';
        $this->db->select('request_ticket.request_id,
							request_tickets.ticket_date,
							request_tickets.province_idfrom');
        $this->db->from(self::TABLE_NAME);
        $this->db->join($request_requests_table, $request_requests_table . '.request_id = ' . self::TABLE_NAME . '.request_id', 'inner');
        $this->db->where(self::TABLE_NAME . '.request_id', $request_id);
        return $this->db->count_all_results();
    }

    /**
     * Esta es la funcion encargada de insertar los tickets
     *
     * @return boolean
     */
    public function insert($id, $request_id, $ticket_date, $transport_id, $transport_itinerary, $province_idfrom, $province_idto) {

        $re = "true";
        $res = "true";

        $ticket ['request_id'] = $request_id;
        $ticket ['ticket_date'] = $ticket_date;
        $ticket ['transport_id'] = $transport_id;
        $ticket ['transport_itinerary'] = $transport_itinerary;
        $ticket ['province_idfrom'] = $province_idfrom;
        $ticket ['province_idto'] = $province_idto;

        if (!empty($id)) {
            $this->db->where('id', $id);
            $this->db->trans_begin();
            $re = $this->db->update(self::TABLE_NAME, $ticket);
            $logs = new Logs ( );

            $mywhere = 'where id = ' . $id;
            $myquery = $logs->sqlupdate(self::TABLE_NAME, $ticket, $mywhere);

            $logs->write(self::TABLE_NAME, 'UPDATE', $myquery);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
            if ($re == true) {
                return "true";
            } else
                return "false";
        } else {
            $this->db->trans_begin();
            $re = $this->db->insert(self::TABLE_NAME, $ticket);
            $logs = new Logs ( );
            $myquery = $logs->sqlinsert(self::TABLE_NAME, $ticket);
            $logs->write(self::TABLE_NAME, 'INSERT', $myquery);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
        }
        if ($re == "true" && $res == "true")
            return "true"; else
            return "false";
    }

    public function updateState($request_id, $ticket_date, $state) {
        $data = array('ticket_state' => $state);
        $this->db->where('request_id', $request_id);
        $this->db->where('ticket_date', $ticket_date);
        $this->db->trans_begin();
        $re = $this->db->update(self::TABLE_NAME, $data);
        $logs = new Logs ( );

        $mywhere = 'where request_id = ' . $request_id . ' and ticket_date = ' . $ticket_date;
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

    public function updateTicketCancel($request_id, $ticket_date, $state) {
        $data = array('ticket_cancel' => $state);
        $this->db->where('request_id', $request_id);
        $this->db->where('ticket_date', $ticket_date);
        $this->db->trans_begin();
        $re = $this->db->update(self::TABLE_NAME, $data);
        $logs = new Logs ( );

        $mywhere = 'where request_id = ' . $request_id . ' and ticket_date = ' . $ticket_date;
        $myquery = $logs->sqlupdate(self::TABLE_NAME, $data, $mywhere);

        $logs->write(self::TABLE_NAME, 'UPDATE', $myquery);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        if ($re == true)
            return "true";
        else
            return "false";
    }

    /**
     * Funcion para eliminar un request_tickets por su id y fecha
     *
     * @param int $request_id
     * @param date $ticket_date
     */
    public function delete($request_id, $ticket_date) {
        $this->db->where('request_id', $request_id);
        $this->db->where('ticket_date', $ticket_date);
        $this->db->trans_begin();
        $this->db->delete(self::TABLE_NAME);
        $logs = new Logs ( );
        $mywhere = 'where request_id = ' . $request_id . ' and ticket_date = ' . $ticket_date;
        $myquery = $logs->sqldelete(self::TABLE_NAME, $mywhere);
        $logs->write(self::TABLE_NAME, 'DELETE', $myquery);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
    }

    public function cancelTicket($request_id) {
        $data = array('ticket_cancel' => 1);
        $this->db->where('request_id', $request_id);
        $this->db->trans_begin();
        $re = $this->db->update(self::TABLE_NAME, $data);
        $logs = new Logs ( );

        $mywhere = 'where request_id = ' . $request_id;
        $myquery = $logs->sqlupdate(self::TABLE_NAME, $data, $mywhere);

        $logs->write(self::TABLE_NAME, 'UPDATE', $myquery);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        if ($re == true)
            return "true";
        else
            return "false";
    }

    public function getIdOfProrogateTicket($person_idworker, $lodging_entrancedate) {
        $request_requests_table = 'request_requests';
        $this->db->select(self::TABLE_NAME . '.request_id');
        $this->db->from(self::TABLE_NAME);
        $this->db->join($request_requests_table, $request_requests_table . '.request_id = ' . self::TABLE_NAME . '.request_id', 'inner');
        $this->db->where($request_requests_table . '.person_idworker', $person_idworker);
        $this->db->where('ticket_date', $lodging_entrancedate);
        $result = $this->db->get();
        $request_id = 0;
        foreach ($result->result() as $row) {
            $request_id = $row->request_id;
        }
        return $request_id;
    }

    public function getCountById($request_id, $ticket_date) {
        $this->db->select('request_id');
        $this->db->from(self::TABLE_NAME);
        $this->db->where('request_id', $request_id);
        $this->db->where('ticket_date', $ticket_date);
        return $this->db->count_all_results();
    }

    public function getById($request_id) {
        $centinela = new Centinela ( );
        $request_tickets_table = 'request_tickets';
        $request_lodgings_table = 'request_lodgings';
        $this->db->select('request_tickets.id,
                                    request_tickets.request_id,
                                    request_tickets.transport_id,
                                    request_tickets.transport_itinerary,
                                    request_tickets.ticket_date,
                                    request_tickets.province_idfrom,
                                    request_tickets.province_idto,
                                    request_tickets.ticket_state');
        $this->db->from(self::TABLE_NAME);
        $this->db->where(self::TABLE_NAME . '.request_id =', $request_id);
        $this->db->order_by('ticket_date', 'asc');
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $value [] = array(
                    'id' => $row->id,
                    'request_id' => $row->request_id,
                    'transport_id' => $row->transport_id,
                    'transport_itinerary' => $row->transport_itinerary,
                    'ticket_date' => $row->ticket_date,
                    'province_idfrom' => $row->province_idfrom,
                    'province_idto' => $row->province_idto,
                    'ticket_state' => $row->ticket_state
                );
            }
        }
        return $value;
    }

}

?>