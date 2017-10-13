<?php

class Lodging_dietconciliations_model extends Model {
    const TABLE_NAME = 'lodging_dietconciliations';

    function __construct() {
        parent::__construct();
    }

    public function getData($dateStart, $dateEnd, $hotel, $province) {
        $this->load->model('conf/conf_costcenters_model');
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_cafeterias_model');
        $request_requests_table = 'request_requests';
        $request_lodgings_table = 'request_lodgings';
        $conf_costcenters_table = 'conf_costcenters';
        $lodging_edit_table = 'lodging_edit';
        $date = new Dates();
        $today = $date->now();
        $dateStart = empty($dateStart) ? '1900-01-01' : $dateStart;
        $dateEnd = empty($dateEnd) ? '1900-01-01' : $dateEnd;
        $this->db->select($lodging_edit_table . '.request_id, ' .
                self::TABLE_NAME . '.conciliation_id, ' .
                self::TABLE_NAME . '.bill_number, ' .
                self::TABLE_NAME . '.diet_amount, ' .
                self::TABLE_NAME . '.conciliation_entrancedate, ' .
                self::TABLE_NAME . '.conciliation_exitdate, ' .
                $request_requests_table . '.request_inversiontask, ' .
                $request_requests_table . '.request_details,' .
                $request_requests_table . '.person_idlicensedby, ' .
                $request_requests_table . '.center_id, ' .
                $request_requests_table . '.person_idworker, ' .
                $request_lodgings_table . '.lodging_entrancedate, ' .
                $request_lodgings_table . '.lodging_exitdate, ' .
                $request_lodgings_table . '.province_idlodging,' .
                $lodging_edit_table . '.cafeteria_id');
        $this->db->from($request_requests_table);
        $this->db->join($request_lodgings_table, $request_lodgings_table . '.request_id = ' . $request_requests_table . '.request_id', 'inner');
        $this->db->join($lodging_edit_table, $lodging_edit_table . '.request_id = ' . $request_lodgings_table . '.request_id', 'inner');
        $this->db->join(self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $lodging_edit_table . '.request_id', 'left');
        $this->db->where($lodging_edit_table . '.cafeteria_id =', $hotel);
        $this->db->where($lodging_edit_table . '.lodging_noshow !=', 'on');
        $this->db->where($request_lodgings_table . '.lodging_canceled =', 0);
        $this->db->where($request_lodgings_table . '.province_idlodging =', $province);
        $this->db->where($request_lodgings_table . '.lodging_entrancedate >=', $dateStart);
        $this->db->where($request_lodgings_table . '.lodging_entrancedate <=', $dateEnd);
        $this->db->where($request_lodgings_table . '.lodging_exitdate <', $today);
        $this->db->distinct();
        $this->db->order_by('lodging_entrancedate', 'asc');
        $result = $this->db->get();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $center_name = Conf_costcenters_model::getNameById($row->center_id);
                $cafeteria = Conf_cafeterias_model::getNameById($row->cafeteria_id);
                $person_worker = Person_persons_model::getNameById($row->person_idworker);
                $person_identity = Person_persons_model::getIdentityById($row->person_idworker);
                $person_licensedby = Person_persons_model::getNameById($row->person_idlicensedby);
                $province_lodging = Conf_provinces_model::getNameById($row->province_idlodging);
                $lodging_entrancedate = empty($row->conciliation_entrancedate) ? $row->lodging_entrancedate : $row->conciliation_entrancedate;
                $lodging_exitdate = empty($row->conciliation_exitdate) ? $row->lodging_exitdate : $row->conciliation_exitdate;
                $value [] = array('request_id' => $row->request_id,
                    'bill_number' => $row->bill_number,
                    'conciliation_id' => $row->conciliation_id,
                    'person_licensedby' => $person_licensedby,
                    'request_inversiontask' => $row->request_inversiontask,
                    'lodging_entrancedate' => $lodging_entrancedate,
                    'lodging_exitdate' => $lodging_exitdate,
                    'center_name' => $center_name,
                    'person_worker' => $person_worker,
                    'person_identity' => $person_identity,
                    'request_details' => $row->request_details,
                    'province_lodging' => $province_lodging,
                    'cafeteria_name' => $cafeteria,
                    'diet_amount' => $row->diet_amount);
            }
        } else {
            $value = array();
        }
        return $value;
    }

    public function getDataCociliation($dateStart='', $dateEnd='', $hotel='', $province='', $center='', $motive='', $show=false, $isPDF='no') {
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_cafeterias_model');
        $this->load->model('conf/conf_costcenters_model');
        $this->load->model('conf/conf_provinces_model');
        $request_requests_table = 'request_requests';
        $request_lodgings_table = 'request_lodgings';
        $conf_costcenters_table = 'conf_costcenters';
        $conf_hotels_table = 'conf_hotels';
        $lodging_edit_table = 'lodging_edit';
        $request_ids = self::getIds();
        $request_ids = empty($request_ids) ? 0 : $request_ids;
        $dateStart = empty($dateStart) ? '1900-01-01' : $dateStart;
        $dateEnd = empty($dateEnd) ? '1900-01-01' : $dateEnd;
        $this->db->select($lodging_edit_table . '.request_id, ' .
                self::TABLE_NAME . '.conciliation_id, ' .
                self::TABLE_NAME . '.bill_number, ' .
                self::TABLE_NAME . '.diet_amount, ' .
                self::TABLE_NAME . '.conciliation_entrancedate, ' .
                self::TABLE_NAME . '.conciliation_exitdate, ' .
                $request_requests_table . '.request_inversiontask, ' .
                $request_requests_table . '.request_details,' .
                $request_requests_table . '.person_idlicensedby, ' .
                $request_requests_table . '.center_id, ' .
                $request_requests_table . '.person_idworker, ' .
                $request_lodgings_table . '.province_idlodging,' .
                $lodging_edit_table . '.cafeteria_id');
        $this->db->from($request_requests_table);
        $this->db->join($request_lodgings_table, $request_lodgings_table . '.request_id = ' . $request_requests_table . '.request_id', 'inner');
        $this->db->join($lodging_edit_table, $lodging_edit_table . '.request_id = ' . $request_lodgings_table . '.request_id', 'inner');
        $this->db->join(self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $lodging_edit_table . '.request_id', 'inner');
        $this->db->where(self::TABLE_NAME . '.conciliation_entrancedate >=', $dateStart);
        $this->db->where(self::TABLE_NAME . '.conciliation_entrancedate <=', $dateEnd);
        $this->db->where($lodging_edit_table . '.cafeteria_id >', 0);

        if (!empty($province)) {
            $this->db->where($request_lodgings_table . '.province_idlodging =', $province);
        }
        if (!empty($hotel)) {
            $this->db->where($lodging_edit_table . '.cafeteria_id', $hotel);
        }
        if (!empty($center)) {
            $this->db->where($request_requests_table . '.center_id', $center);
        }
        $this->db->distinct();
        $this->db->order_by('conciliation_entrancedate', 'asc');
        $result = $this->db->get();
        $cant = 0;
        $total_diet = 0;
        if ($result->result() != null) {
            $date = new Dates();
            $value = array();
            foreach ($result->result() as $row) {
                $hotel = Conf_cafeterias_model::getNameById($row->cafeteria_id);
                $person_worker = Person_persons_model::getNameById($row->person_idworker);
                $person_identity = Person_persons_model::getIdentityById($row->person_idworker);
                $person_licensedby = Person_persons_model::getNameById($row->person_idlicensedby);
                $province_lodging = Conf_provinces_model::getNameById($row->province_idlodging);
                $center_name = Conf_costcenters_model::getNameById($row->center_id);
                $value [] = array('request_id' => $row->request_id,
                    'bill_number' => $row->bill_number,
                    'conciliation_id' => $row->conciliation_id,
                    'person_licensedby' => $person_licensedby,
                    'request_inversiontask' => $row->request_inversiontask,
                    'lodging_entrancedate' => $row->conciliation_entrancedate,
                    'lodging_exitdate' => $row->conciliation_exitdate,
                    'center_name' => $center_name,
                    'person_worker' => $person_worker,
                    'person_identity' => $person_identity,
                    'request_details' => $row->request_details,
                    'province_lodging' => $province_lodging,
                    'cafeteria_name' => $hotel,
                    'diet' => $row->diet_amount);
                $total_diet = $total_diet + $row->diet_amount;
            }
            $last = array();
            $last [] = array('request_id' => '',
                'bill_number' => 'TOTAL',
                'conciliation_id' => '',
                'person_licensedby' => '',
                'request_inversiontask' => '',
                'lodging_entrancedate' => '',
                'lodging_exitdate' => '',
                'center_name' => '',
                'person_worker' => '',
                'person_identity' => '',
                'request_details' => '',
                'province_lodging' => '',
                'cafeteria_name' => '',
                'diet' => $total_diet);
            $value = array_merge((array) $value, (array) $last);
            if ($isPDF == 'si') {
                return $value;
            } else {
                echo ("{count : " . $cant . ", data : " . json_encode($value) . "}");
            }
        }
    }

    /**
     * funcion que devuelve la cantidad de registros en la tabla
     *
     */
    public function getCant() {
        return $this->db->count_all(self::TABLE_NAME);
    }

    /**
     * Esta es la funcion encargada de insertar
     *
     * @return boolean
     */
    public function insert() {
        $conciliation_id = $this->input->post('conciliation_id');
        $conciliation ['request_id'] = $this->input->post('request_id');
        $conciliation ['diet_amount'] = $this->input->post('diet_amount');
        $conciliation ['conciliation_entrancedate'] = $this->input->post('lodging_entrancedate');
        $conciliation ['conciliation_exitdate'] = $this->input->post('lodging_exitdate');
        if (!empty($conciliation_id)) {

            $this->db->where('conciliation_id', $conciliation_id);
            $this->db->trans_begin();
            $re = $this->db->update(self::TABLE_NAME, $conciliation);
            $logs = new Logs ( );

            $mywhere = 'where conciliation_id = ' . $conciliation_id;
            $myquery = $logs->sqlupdate(self::TABLE_NAME, $conciliation, $mywhere);

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
        } else {
            $this->db->trans_begin();
            $re = $this->db->insert(self::TABLE_NAME, $conciliation);
            $logs = new Logs ( );
            $myquery = $logs->sqlinsert(self::TABLE_NAME, $conciliation);
            $logs->write(self::TABLE_NAME, 'INSERT', $myquery);

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
    }

    /**
     * Esta es la funcion encargada de insertar
     *
     * @return boolean
     */
    public function insertBill($request_id, $bill_number) {
        //$request_id = $this->input->post('request_id');
        $conciliation ['request_id'] = $request_id;
        $conciliation ['bill_number'] = $bill_number; //$this->input->post('bill_number');

        /*
         * ver despues la validacion que no haya request_id repetidos en la tabla
         */
        $flag = self::getCountById($request_id);
        if ($flag > 0) {
            $this->db->where('request_id', $request_id);
            $this->db->trans_begin();
            $re = $this->db->update(self::TABLE_NAME, $conciliation);
            $logs = new Logs ( );

            $mywhere = 'where request_id = ' . $request_id;
            $myquery = $logs->sqlupdate(self::TABLE_NAME, $conciliation, $mywhere);

            $logs->write(self::TABLE_NAME, 'UPDATE', $myquery);

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
        } else {
            $this->db->trans_begin();
            $re = $this->db->insert(self::TABLE_NAME, $conciliation);
            $logs = new Logs ( );
            $myquery = $logs->sqlinsert(self::TABLE_NAME, $conciliation);
            $logs->write(self::TABLE_NAME, 'INSERT', $myquery);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
            if ($re == true)
                return "true"; else
                return "false";
        }
    }

    /**
     * Funcion para eliminar una provincia por su nombre
     *
     * @param string $requestservice_name
     */
    public function delete($request_id) {
        $this->db->where('request_id', $request_id);
        $this->db->trans_begin();
        $this->db->delete(self::TABLE_NAME);
        $logs = new Logs ( );
        $mywhere = 'where request_id = ' . $request_id;
        $myquery = $logs->sqldelete(self::TABLE_NAME, $mywhere);
        $logs->write(self::TABLE_NAME, 'DELETE', $myquery);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
    }

    public function getIds() {
        $this->db->select('request_id');
        $this->db->from(self::TABLE_NAME);
        $result = $this->db->get();
        $value = array();
        $cant = 0;
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $value [$cant] = $row->request_id;
                $cant++;
            }
        }
        return $value;
    }

    public function getById($request_id) {
        $this->load->model('conf/conf_costcenters_model');
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_hotels_model');
        $request_requests_table = 'request_requests';
        $request_lodgings_table = 'request_lodgings';
        $conf_costcenters_table = 'conf_costcenters';
        $lodging_edit_table = 'lodging_edit';
        $date = new Dates();
        $today = $date->now();
        $dateStart = empty($dateStart) ? '1900-01-01' : $dateStart;
        $dateEnd = empty($dateEnd) ? '1900-01-01' : $dateEnd;
        $this->db->select($lodging_edit_table . '.request_id, ' .
                self::TABLE_NAME . '.conciliation_id, ' .
                self::TABLE_NAME . '.diet_amount, ' .
                self::TABLE_NAME . '.conciliation_entrancedate, ' .
                self::TABLE_NAME . '.conciliation_exitdate, ' .
                $request_requests_table . '.person_idlicensedby, ' .
                $request_requests_table . '.center_id, ' .
                $request_requests_table . '.person_idworker, ' .
                $request_lodgings_table . '.lodging_entrancedate, ' .
                $request_lodgings_table . '.lodging_exitdate ');
        $this->db->from($request_requests_table);
        $this->db->join($request_lodgings_table, $request_lodgings_table . '.request_id = ' . $request_requests_table . '.request_id', 'inner');
        $this->db->join($lodging_edit_table, $lodging_edit_table . '.request_id = ' . $request_lodgings_table . '.request_id', 'inner');
        $this->db->join(self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $lodging_edit_table . '.request_id', 'left');
        $this->db->where($lodging_edit_table . '.request_id', $request_id);
        $result = $this->db->get();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $center_name = Conf_costcenters_model::getNameById($row->center_id);
                $person_worker = Person_persons_model::getNameById($row->person_idworker);
                $person_identity = Person_persons_model::getIdentityById($row->person_idworker);
                $person_licensedby = Person_persons_model::getNameById($row->person_idlicensedby);
                $lodging_entrancedate = empty($row->conciliation_entrancedate) ? $row->lodging_entrancedate : $row->conciliation_entrancedate;
                $lodging_exitdate = empty($row->conciliation_exitdate) ? $row->lodging_exitdate : $row->conciliation_exitdate;
                $value [] = array('request_id' => $row->request_id,
                    'conciliation_id' => $row->conciliation_id,
                    'person_licensedby' => $person_licensedby,
                    'lodging_entrancedate' => $lodging_entrancedate,
                    'lodging_exitdate' => $lodging_exitdate,
                    'center_name' => $center_name,
                    'person_worker' => $person_worker,
                    'person_identity' => $person_identity,
                    'diet_amount' => $row->diet_amount);
            }
        } else {
            $value = array();
        }
        return $value;
    }

    public function getCountById($request_id) {
        $this->db->select('request_id');
        $this->db->from(self::TABLE_NAME);
        $this->db->where('request_id', $request_id);
        return $this->db->count_all_results();
    }

}

?>
