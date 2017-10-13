<?php

class Ticket_editviazul_model extends Model {
    const TABLE_NAME = 'ticket_editviazul';

    function __construct() {
        parent::__construct();
    }

    /**
     * Esta es la funcion encargada de insertar los transportes
     *
     * @return boolean
     */
    public function insert() {
        $request_id = $this->input->post('request_id');
        $voucher = $this->input->post('viazul_voucher');
        $exithour = $this->input->post('viazul_exithour');
        $arrivalhour = $this->input->post('viazul_arrivalhour');
        $price = $this->input->post('viazul_price');
        $ticket_date = $this->input->post('ticket_date');
        $viazul ['request_id'] = $request_id;
        $viazul ['person_ideditedby'] = $this->session->userdata('person_id');
        $viazul ['province_idfrom'] = $this->input->post('province_idfrom');
        $viazul ['province_idto'] = $this->input->post('province_idto');
        $viazul ['ticket_date'] = $ticket_date;
        if (empty($voucher)) {
            $viazul ['viazul_voucher'] = null;
        } else {
            $viazul ['viazul_voucher'] = $voucher;
        }
        if (empty($exithour)) {
            $viazul ['viazul_exithour'] = null;
        } else {
            $viazul ['viazul_exithour'] = $exithour;
        }
        if (empty($arrivalhour)) {
            $viazul ['viazul_arrivalhour'] = null;
        } else {
            $viazul ['viazul_arrivalhour'] = $arrivalhour;
        }
        if (empty($price)) {
            $viazul ['viazul_price'] = null;
        } else {
            $viazul ['viazul_price'] = $price;
        }
        $viazul ['viazulstate_id'] = $this->input->post('viazulstate_id');
        $flag = self::getCountById($request_id, $ticket_date);
        if ($flag > 0) {
            $this->db->where('request_id', $request_id);
            $this->db->where('ticket_date', $ticket_date);
            $this->db->trans_begin();
            $re = $this->db->update(self::TABLE_NAME, $viazul);
            $logs = new Logs ( );

            $mywhere = 'where request_id = ' . $request_id . 'and ticket_date ' . $ticket_date;
            $myquery = $logs->sqlupdate(self::TABLE_NAME, $viazul, $mywhere);

            $logs->write(self::TABLE_NAME, 'UPDATE', $myquery);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
            if ($re == true) {
                if ($viazul ['viazulstate_id'] == 3) {
                    Request_tickets_model::updateTicketCancel($request_id, $ticket_date, 1);
                } else {
                    Request_tickets_model::updateTicketCancel($request_id, $ticket_date, 0);
                }
                return "true";
            }
            else
                return "false";
        } else {
            $this->db->trans_begin();
            $re = $this->db->insert(self::TABLE_NAME, $viazul);
            $logs = new Logs ( );
            $myquery = $logs->sqlinsert(self::TABLE_NAME, $viazul);
            $logs->write(self::TABLE_NAME, 'INSERT', $myquery);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
            $this->load->model('request/request_tickets_model');
            Request_tickets_model::updateState($request_id, $ticket_date, 1);
            if ($re == true) {
                if ($viazul ['viazulstate_id'] == 3) {
                    Request_tickets_model::updateTicketCancel($request_id, $ticket_date, 1);
                } else {
                    Request_tickets_model::updateTicketCancel($request_id, $ticket_date, 0);
                }
                return "true";
            }
            else
                return "false";
        }
    }

    public function changeDate() {
        $request_id = $this->input->post('request_id');
        $newdate = $this->input->post('newdate');
        $ticket_date = $this->input->post('ticket_date');
        $viazul ['ticket_date'] = $newdate;
        
        //$flag = self::getCountById($request_id, $ticket_date);
        //if ($flag > 0) {
            $this->db->where('request_id', $request_id);
            $this->db->where('ticket_date', $ticket_date);
            $this->db->trans_begin();
            $re = $this->db->update('request_tickets', $viazul);
            $logs = new Logs ( );

            $mywhere = 'where request_id = ' . $request_id . 'and ticket_date ' . $ticket_date;
            $myquery = $logs->sqlupdate('request_tickets', $viazul, $mywhere);

            $logs->write('request_tickets', 'UPDATE', $myquery);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
            if ($re == true) {
                return "true";
            }
            else
                return "false";
        //}
    }

    /**
     * Funcion para eliminar una provincia por su nombre
     *
     * @param string $requestservice_name
     */
    public function delete($request_id, $ticket_date) {
        $this->db->where('request_id', $request_id);
        $this->db->where('ticket_date', $ticket_date);
        $this->db->trans_begin();
        $this->db->delete(self::TABLE_NAME);
        $logs = new Logs ( );
        $mywhere = 'where request_id = ' . $request_id . 'and ticket_date ' . $ticket_date;
        $myquery = $logs->sqldelete(self::TABLE_NAME, $mywhere);
        $logs->write(self::TABLE_NAME, 'DELETE', $myquery);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
    }

    public function get() {
        $result = $this->db->get(self::TABLE_NAME);
        return $result->result_array();
    }

// de la funcion

    public function getById($request_id, $ticket_date) {
        $this->load->model('conf/conf_provinces_model');
        $this->load->model('person/person_persons_model');
        $conf_costcenters_table = 'conf_costcenters';
        $conf_tickettransports_table = 'conf_lodgingtransports';
        $conf_motives_table = 'conf_motives';
        $request_requests_table = 'request_requests';
        $request_tickets_table = 'request_tickets';
        $count = self::getCountById($request_id, $ticket_date);
        $query = 'request_tickets.request_id, 
					request_requests.request_date,
					request_requests.person_idworker, 
					request_requests.person_idrequestedby,
					request_requests.person_idlicensedby,
					request_requests.request_details,
					request_tickets.ticket_date,
					request_tickets.province_idfrom, 
					request_tickets.province_idto,
					conf_costcenters.center_name,
					conf_lodgingtransports.transport_name,
					conf_motives.motive_name';
        if ($count > 0) {
            $editviazul = ',ticket_editviazul.viazul_voucher,
	        				ticket_editviazul.viazul_exithour,
							ticket_editviazul.viazul_arrivalhour,
							ticket_editviazul.viazul_price,
							ticket_editviazul.viazulstate_id';
            $query = $query . $editviazul;
        }
        $this->db->select($query);
        $this->db->from($request_requests_table);
        $this->db->join($conf_costcenters_table, $conf_costcenters_table . '.center_id = ' . $request_requests_table . '.center_id', 'inner');
        $this->db->join($conf_motives_table, $conf_motives_table . '.motive_id = ' . $request_requests_table . '.motive_id', 'inner');
        $this->db->join($request_tickets_table, $request_tickets_table . '.request_id = ' . $request_requests_table . '.request_id', 'inner');
        $this->db->join($conf_tickettransports_table, $conf_tickettransports_table . '.transport_id = ' . $request_tickets_table . '.transport_id', 'inner');
        if ($count > 0) {
            $this->db->join(self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $request_tickets_table . '.request_id AND ' . self::TABLE_NAME . '.ticket_date = ' . $request_tickets_table . '.ticket_date');
        }
        $this->db->where($request_tickets_table . '.request_id', $request_id);
        $this->db->where($request_tickets_table . '.ticket_date', $ticket_date);

        $result = $this->db->get();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $person_namerequestedby = Person_persons_model::getNameById($row->person_idrequestedby);
                $person_namelicensedby = Person_persons_model::getNameById($row->person_idlicensedby);
                $person_nameworker = Person_persons_model::getNameById($row->person_idworker);
                $person_identity = Person_persons_model::getIdentityById($row->person_idworker);
                $person_province = Person_persons_model::getProvinceById($row->person_idworker);
                $province_namefrom = Conf_provinces_model::getNameById($row->province_idfrom);
                $province_nameto = Conf_provinces_model::getNameById($row->province_idto);
                if ($count > 0) {
                    if (is_null($row->viazul_exithour)) {
                        $viazul_exithour = $row->viazul_exithour;
                    } else {
                        $viazul_exithour = substr($row->viazul_exithour, 0, 5);
                    }
                    if (is_null($row->viazul_arrivalhour)) {
                        $viazul_arrivalhour = $row->viazul_arrivalhour;
                    } else {
                        $viazul_arrivalhour = substr($row->viazul_arrivalhour, 0, 5);
                    }

                    $value [] = array('request_id' => $row->request_id,
                        'request_date' => $row->request_date,
                        'ticket_date' => $row->ticket_date,
                        'person_namerequestedby' => $person_namerequestedby,
                        'person_namelicensedby' => $person_namelicensedby,
                        'center_name' => $row->center_name,
                        'transport_name' => $row->transport_name,
                        'person_nameworker' => $person_nameworker,
                        'person_identity' => $person_identity,
                        'person_province' => $person_province,
                        'province_idfrom' => $row->province_idfrom,
                        'province_idto' => $row->province_idto,
                        'province_namefrom' => $province_namefrom,
                        'province_nameto' => $province_nameto,
                        'motive_name' => $row->motive_name,
                        'request_details' => $row->request_details,
                        'viazul_voucher' => $row->viazul_voucher,
                        'viazul_exithour' => $viazul_exithour,
                        'viazul_arrivalhour' => $viazul_arrivalhour,
                        'viazul_price' => $row->viazul_price,
                        'viazulstate_id' => $row->viazulstate_id);
                } else {
                    $value [] = array('request_id' => $row->request_id,
                        'request_date' => $row->request_date,
                        'ticket_date' => $row->ticket_date,
                        'person_namerequestedby' => $person_namerequestedby,
                        'person_namelicensedby' => $person_namelicensedby,
                        'center_name' => $row->center_name,
                        'transport_name' => $row->transport_name,
                        'person_nameworker' => $person_nameworker,
                        'person_identity' => $person_identity,
                        'person_province' => $person_province,
                        'province_idfrom' => $row->province_idfrom,
                        'province_idto' => $row->province_idto,
                        'province_namefrom' => $province_namefrom,
                        'province_nameto' => $province_nameto,
                        'request_details' => $row->request_details,
                        'motive_name' => $row->motive_name);
                }
            }
        } else {
            $value = array();
        }
        return $value;
    }

    public function getCountById($request_id, $ticket_date) {
        $this->db->select('request_id');
        $this->db->from(self::TABLE_NAME);
        $this->db->where('request_id', $request_id);
        $this->db->where('ticket_date', $ticket_date);
        return $this->db->count_all_results();
    }

}

?>
