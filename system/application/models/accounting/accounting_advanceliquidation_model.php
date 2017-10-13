<?php

class Accounting_advanceliquidation_model extends Model
{

    const TABLE_NAME = 'accounting_advanceliquidation';

    function __construct()
    {
        parent::__construct();
    }

    public function insert($request_id, $request_details, $person_idworker, $request_area, $request_consecutive, $person_groupresponsable, $center_idadvance, $lodging_entrancedate, $lodging_exitdate, $print_date, $center_consecutive /* , $liquidation_date, $liquidation_used, $liquidation_repay, $liquidation_given, $liquidation_liquidated */)
    {
        $centinela = new Centinela();
        $person_id = $centinela->get_person_id();
        $liquidation['request_id'] = $request_id;
        $liquidation['request_details'] = $request_details;
        $liquidation['person_idworker'] = $person_idworker;
        $liquidation['request_area'] = $request_area;
        $liquidation['request_consecutive'] = $request_consecutive;
        $liquidation['person_groupresponsable'] = $person_groupresponsable;
        $liquidation['center_idadvance'] = $center_idadvance;
        $liquidation['lodging_entrancedate'] = $lodging_entrancedate;
        $liquidation['lodging_exitdate'] = $lodging_exitdate;
        $liquidation['print_date'] = $print_date;
        $liquidation['person_idprint'] = $person_id;
        $liquidation['center_consecutive'] = $center_consecutive;

//        $liquidation['liquidation_used'] = $liquidation_used;
//        $liquidation['liquidation_repay'] = $liquidation_repay;
//        $liquidation['liquidation_given'] = $liquidation_given;
//        $liquidation['liquidation_liquidated'] = $liquidation_liquidated;
        if ($this->existRequest($request_id) > 0) {
            $this->db->where('request_id', $request_id);
            $this->db->trans_begin();
            $upd = $this->db->update(self::TABLE_NAME, $liquidation);
            $logs = new Logs ();

            $mywhere = 'where request_id = ' . $request_id;
            $myquery = $logs->sqlupdate(self::TABLE_NAME, $liquidation, $mywhere);

            $logs->write(self::TABLE_NAME, 'UPDATE', $myquery);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
            return $this->db->insert_id();
        }

        $this->db->trans_begin();
        $re = $this->db->insert(self::TABLE_NAME, $liquidation);
        //$request_id = $this->db->insert_id();
        $logs = new Logs ();
        $myquery = $logs->sqlinsert(self::TABLE_NAME, $liquidation);
        $logs->write(self::TABLE_NAME, 'INSERT', $myquery);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        return $this->db->insert_id();
    }

    public function setAmountEstimated($id, $amount)
    {
        $this->db->set('amount_estimated', $amount, FALSE);
        $this->db->where('request_id', $id);
        $re = $this->db->update(self::TABLE_NAME);
        if ($re == true) {
            return "true";
        } else {
            return "false";
        }
    }

    public function updateLiquidationState($request_id, $state)
    {
        $data = array('liquidation_liquidated' => $state);
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

    public function existRequest($request_id)
    {
        $this->db->select('request_id')
            ->from(self::TABLE_NAME)
            ->where(self::TABLE_NAME . '.request_id', $request_id);
        return $this->db->count_all_results();
    }

    //PENDIENTEEEEE
    public function getData($dateStart, $dateEnd, $province)
    {
        $centinela = new Centinela();
        $roll_id = $centinela->get_roll_id();
        $this->load->model('person/person_persons_model');
        $this->load->model('conf/conf_hotels_model');
        $request_requests_table = 'request_requests';
        $request_lodgings_table = 'request_lodgings';
        $person_persons_table = 'person_persons';
        $conf_costcenters_table = 'conf_costcenters';
        $conf_motives_table = 'conf_motives';
        $this->db->select($request_lodgings_table . '.request_id,
            request_requests.request_date, 
            request_requests.person_idworker, 
            request_requests.center_idadvance, 
            request_requests.request_details, 
            request_requests.request_area, 
            request_requests.request_consecutive, 
            request_requests.person_groupresponsable,
            conf_costcenters.center_name, 
            conf_costcenters.center_consecutive,
            person_persons.person_name, 
            person_persons.person_lastname, 
            person_persons.person_secondlastname, 
            request_lodgings.lodging_entrancedate, 
            request_lodgings.lodging_exitdate');
        $this->db->from($request_lodgings_table);
        $this->db->join($request_requests_table, $request_requests_table . '.request_id = ' . $request_lodgings_table . '.request_id', 'inner');
        $this->db->join($conf_costcenters_table, $conf_costcenters_table . '.center_id = ' . $request_requests_table . '.center_idadvance', 'inner');
        $this->db->join($conf_motives_table, $conf_motives_table . '.motive_id = ' . $request_requests_table . '.motive_id', 'inner');
        $this->db->join($person_persons_table, $person_persons_table . '.person_id = ' . $request_requests_table . '.person_idworker', 'inner');
        $this->db->where($request_lodgings_table . '.lodging_canceled', 0);
        if (!empty($dateStart) && !empty($dateEnd)) {
            $this->db->where($request_lodgings_table . '.lodging_entrancedate >=', $dateStart);
            $this->db->where($request_lodgings_table . '.lodging_entrancedate <=', $dateEnd);
        }
        if ($roll_id != 6) {
            $this->db->where($request_requests_table . '.center_idadvance', $centinela->get_center_id());
        }
        $this->db->order_by("lodging_entrancedate", "asc");
        //$this->db->order_by("request_id", "asc");
        $result = $this->db->get();
        $value = array();
        $date = new Dates();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $person_worker = Person_persons_model::getNameById($row->person_idworker);
                $value [] = array('request_id' => $row->request_id,
                    'request_date' => $row->request_date,
                    'lodging_entrancedate' => $row->lodging_entrancedate,
                    'lodging_exitdate' => $row->lodging_exitdate,
                    'center_name' => $row->center_name,
                    'request_details' => $row->request_details,
                    'person_worker' => $person_worker,
                    'state' => $state,
                    'hotel_name' => $hotel_name,
                    'letter' => $letter,
                    'voucher' => $voucher);
            }
        }
        $cant = $this->getDataCount($dateStart, $dateEnd, $center, $province, $motive, $hotel);
        echo("{count : " . $cant . ", data : " . json_encode($value) . "}");
    }

    function getLiquidationGrid($dateStart, $dateEnd)
    {
        $centinela = new Centinela();
        $roll_id = $centinela->get_roll_id();
        $person_persons_table = 'person_persons';
        $conf_costcenters_table = 'conf_costcenters';
        $fecha = new Dates();
        $this->db->select('accounting_advanceliquidation.id,
                            accounting_advanceliquidation.request_id, 
                            accounting_advanceliquidation.person_idworker, 
                            accounting_advanceliquidation.center_idadvance, 
                            accounting_advanceliquidation.request_details, 
                            accounting_advanceliquidation.request_area, 
                            accounting_advanceliquidation.request_consecutive, 
                            accounting_advanceliquidation.person_groupresponsable,
                            accounting_advanceliquidation.lodging_entrancedate,
                            accounting_advanceliquidation.lodging_exitdate,
                            accounting_advanceliquidation.liquidation_used,
                            conf_costcenters.center_name, 
                            person_persons.person_name, 
                            person_persons.person_lastname, 
                            person_persons.person_secondlastname, 
                            accounting_advanceliquidation.liquidation_repay, 
                            accounting_advanceliquidation.liquidation_given,
                            accounting_advanceliquidation.center_consecutive,
                            accounting_advanceliquidation.amount_estimated,
                            accounting_advanceliquidation.print_date,
                            accounting_advanceliquidation.liquidation_date');
        $this->db->from(self::TABLE_NAME);
        $this->db->join($conf_costcenters_table, $conf_costcenters_table . '.center_id = ' . self::TABLE_NAME . '.center_idadvance', 'inner');
        $this->db->join($person_persons_table, $person_persons_table . '.person_id = ' . self::TABLE_NAME . '.person_idworker', 'inner');
        if (!empty($dateStart)) {
            $this->db->where(self::TABLE_NAME . '.lodging_entrancedate >=', $dateStart);
        }
        if (!empty($dateEnd)) {
            $this->db->where(self::TABLE_NAME . '.lodging_entrancedate <=', $dateEnd);
        }
        if ($roll_id != 6) {
            $this->db->where(self::TABLE_NAME . '.center_idadvance', $centinela->get_center_id());
        }
        $this->db->order_by("accounting_advanceliquidation.center_consecutive", "asc");
        $result = $this->db->get();
        //echo $this->db->last_query();
        //die();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                if (empty($row->person_groupresponsable)) {
                    $person_worker = $row->person_name . ' ' . $row->person_lastname . ' ' . $row->person_secondlastname;
                } else {
                    $person_worker = $row->person_groupresponsable;
                }
                $value [] = array(
                    'id' => $row->id,
                    'request_id' => $row->request_id,
                    'person_idworker' => $row->person_idworker,
                    'center_idadvance' => $row->center_idadvance,
                    'request_details' => $row->request_details,
                    'center_name' => $row->center_name,
                    'person_worker' => strtoupper($person_worker),
                    'request_area' => $row->request_area,
                    'request_consecutive' => $row->request_consecutive,
                    'lodging_entrancedate' => $row->lodging_entrancedate,
                    'lodging_exitdate' => $row->lodging_exitdate,
                    'liquidation_used' => $row->liquidation_used,
                    'liquidation_repay' => $row->liquidation_repay,
                    'liquidation_given' => $row->liquidation_given,
                    'center_consecutive' => $row->center_consecutive,
                    'amount_estimated' => $row->amount_estimated,
                    'print_date' => $row->print_date,
                    'liquidation_date' => $row->liquidation_date);
            }
            usort($value, function ($a, $b) {
                return $a['center_consecutive'] - $b['center_consecutive'];
            });
        }
        echo("{count : " . count($value) . ", data : " . json_encode($value) . "}");
    }

    function getLiquidationLateGrid($dateEnd)
    {
        $centinela = new Centinela();
        $roll_id = $centinela->get_roll_id();
        $person_persons_table = 'person_persons';
        $conf_costcenters_table = 'conf_costcenters';
        $this->db->select('accounting_advanceliquidation.id,
                            accounting_advanceliquidation.request_id,
                            accounting_advanceliquidation.person_idworker,
                            accounting_advanceliquidation.center_idadvance,
                            accounting_advanceliquidation.request_details,
                            accounting_advanceliquidation.request_area,
                            accounting_advanceliquidation.request_consecutive,
                            accounting_advanceliquidation.person_groupresponsable,
                            accounting_advanceliquidation.lodging_entrancedate,
                            accounting_advanceliquidation.lodging_exitdate,
                            accounting_advanceliquidation.liquidation_used,
                            conf_costcenters.center_name,
                            person_persons.person_name,
                            person_persons.person_lastname,
                            person_persons.person_secondlastname,
                            accounting_advanceliquidation.liquidation_repay,
                            accounting_advanceliquidation.liquidation_given,
                            accounting_advanceliquidation.center_consecutive,
                            accounting_advanceliquidation.amount_estimated,
                            accounting_advanceliquidation.print_date,
                            accounting_advanceliquidation.liquidation_date');
        $this->db->from(self::TABLE_NAME);
        $this->db->join($conf_costcenters_table, $conf_costcenters_table . '.center_id = ' . self::TABLE_NAME . '.center_idadvance', 'inner');
        $this->db->join($person_persons_table, $person_persons_table . '.person_id = ' . self::TABLE_NAME . '.person_idworker', 'inner');
        $this->db->where(self::TABLE_NAME . '.liquidation_liquidated', 'f');
        $this->db->where(self::TABLE_NAME . '.lodging_exitdate <', $dateEnd .' 23:59:59');
        if ($roll_id != 6) {
            $this->db->where(self::TABLE_NAME . '.center_idadvance', $centinela->get_center_id());
        }
        $this->db->order_by("accounting_advanceliquidation.lodging_exitdate", "asc");
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                if (empty($row->person_groupresponsable)) {
                    $person_worker = $row->person_name . ' ' . $row->person_lastname . ' ' . $row->person_secondlastname;
                } else {
                    $person_worker = $row->person_groupresponsable;
                }
                $value [] = array(
                    'id' => $row->id,
                    'request_id' => $row->request_id,
                    'person_idworker' => $row->person_idworker,
                    'center_idadvance' => $row->center_idadvance,
                    'request_details' => $row->request_details,
                    'center_name' => $row->center_name,
                    'person_worker' => strtoupper($person_worker),
                    'request_area' => $row->request_area,
                    'request_consecutive' => $row->request_consecutive,
                    'lodging_entrancedate' => $row->lodging_entrancedate,
                    'lodging_exitdate' => $row->lodging_exitdate,
                    'liquidation_used' => $row->liquidation_used,
                    'liquidation_repay' => $row->liquidation_repay,
                    'liquidation_given' => $row->liquidation_given,
                    'center_consecutive' => $row->center_consecutive,
                    'amount_estimated' => $row->amount_estimated,
                    'print_date' => $row->print_date,
                    'liquidation_date' => $row->liquidation_date);
            }
//            usort($value, function ($a, $b) {
//                return $a['center_consecutive'] - $b['center_consecutive'];
//            });
        }
        echo("{count : " . count($value) . ", data : " . json_encode($value) . "}");
    }

    function getAdvanceControl( /*$center $dateStart, $dateEnd*/)
    {
        $centinela = new Centinela();
        $roll_id = $centinela->get_roll_id();
        $person_persons_table = 'person_persons';
        $conf_costcenters_table = 'conf_costcenters';
        $fecha = new Dates();
        $this->db->select('accounting_advanceliquidation.id,
                            accounting_advanceliquidation.person_idworker,
                            accounting_advanceliquidation.center_idadvance,
                            accounting_advanceliquidation.request_area,
                            accounting_advanceliquidation.request_consecutive,
                            accounting_advanceliquidation.person_groupresponsable,
                            accounting_advanceliquidation.lodging_entrancedate,
                            accounting_advanceliquidation.lodging_exitdate,
                            accounting_advanceliquidation.liquidation_used,
                            conf_costcenters.center_name,
                            person_persons.person_name,
                            person_persons.person_lastname,
                            person_persons.person_secondlastname,
                            accounting_advanceliquidation.liquidation_repay,
                            accounting_advanceliquidation.liquidation_given,
                            accounting_advanceliquidation.center_consecutive,
                            accounting_advanceliquidation.amount_estimated,
                            accounting_advanceliquidation.print_date,
                            accounting_advanceliquidation.liquidation_date');
        $this->db->from(self::TABLE_NAME);
        $this->db->join($conf_costcenters_table, $conf_costcenters_table . '.center_id = ' . self::TABLE_NAME . '.center_idadvance', 'inner');
        $this->db->join($person_persons_table, $person_persons_table . '.person_id = ' . self::TABLE_NAME . '.person_idworker', 'inner');
        //$this->db->where(self::TABLE_NAME . '.lodging_exitdate <', $fecha->now());
//        if (!empty($dateStart)) {
//            $this->db->where (self::TABLE_NAME . '.lodging_entrancedate >=', $dateStart);
//        }
//        if (!empty($dateEnd)) {
//            $this->db->where (self::TABLE_NAME . '.lodging_entrancedate <=', $dateEnd);
//        }
//        if ($roll_id == 6) {
//            if ($center != 0) {
//                $this->db->where(self::TABLE_NAME . '.center_idadvance', $center);
//            } else {
//                $this->db->where(self::TABLE_NAME . '.center_idadvance', $centinela->get_center_id());
//            }
//        } else {
        $this->db->where(self::TABLE_NAME . '.center_idadvance', $centinela->get_center_id());
//        }
        $this->db->order_by("accounting_advanceliquidation.center_consecutive", "asc");
        $result = $this->db->get();
//        return $result;
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                if (empty($row->person_groupresponsable)) {
                    $person_worker = $row->person_name . ' ' . $row->person_lastname . ' ' . $row->person_secondlastname;
                } else {
                    $person_worker = $row->person_groupresponsable;
                }
                $value [] = array(
                    'center_name' => $row->center_name,
                    'person_worker' => strtoupper($person_worker),
                    'request_area' => $row->request_area,
                    'request_consecutive' => $row->request_consecutive,
                    'lodging_exitdate' => $row->lodging_exitdate,
                    'liquidation_used' => $row->liquidation_used,
                    'liquidation_repay' => $row->liquidation_repay,
                    'liquidation_given' => $row->liquidation_given,
                    'center_consecutive' => $row->center_consecutive,
                    'amount_estimated' => $row->amount_estimated,
                    'print_date' => $row->print_date,
                    'liquidation_date' => $row->liquidation_date);
            }
        }
        usort($value, function ($a, $b) {
            return $a['center_consecutive'] - $b['center_consecutive'];
        });
        return $value;
    }

    function getAdvanceControlExcel($dateStart, $dateEnd)
    {
        $centinela = new Centinela();
        $roll_id = $centinela->get_roll_id();
        $person_persons_table = 'person_persons';
        $conf_costcenters_table = 'conf_costcenters';
        $fecha = new Dates();
        $this->db->select('accounting_advanceliquidation.print_date,
                            accounting_advanceliquidation.center_consecutive,
                            accounting_advanceliquidation.person_groupresponsable,
                            person_persons.person_name,
                            person_persons.person_lastname,
                            person_persons.person_secondlastname,
                            conf_costcenters.center_name,
                            accounting_advanceliquidation.request_area,
                            accounting_advanceliquidation.lodging_entrancedate,
                            accounting_advanceliquidation.lodging_exitdate,
                            accounting_advanceliquidation.amount_estimated,
                            accounting_advanceliquidation.liquidation_date,
                            accounting_advanceliquidation.liquidation_used,
                            accounting_advanceliquidation.liquidation_repay,
                            accounting_advanceliquidation.liquidation_given', FALSE);
        $this->db->from(self::TABLE_NAME);
        $this->db->join($conf_costcenters_table, $conf_costcenters_table . '.center_id = ' . self::TABLE_NAME . '.center_idadvance', 'inner');
        $this->db->join($person_persons_table, $person_persons_table . '.person_id = ' . self::TABLE_NAME . '.person_idworker', 'inner');
        //$this->db->where(self::TABLE_NAME . '.lodging_exitdate <', $fecha->now());
        if (!empty($dateStart)) {
            $this->db->where(self::TABLE_NAME . '.print_date >=', $dateStart .' 00:00:00');
        }
        if (!empty($dateEnd)) {
            $this->db->where(self::TABLE_NAME . '.print_date <=', $dateEnd .' 23:59:59');
        }
//        if ($roll_id == 6) {
//            if ($center != 0) {
//                $this->db->where(self::TABLE_NAME . '.center_idadvance', $center);
//            } else {
//                $this->db->where(self::TABLE_NAME . '.center_idadvance', $centinela->get_center_id());
//            }
//        } else {
        $this->db->where(self::TABLE_NAME . '.center_idadvance', $centinela->get_center_id());
//        }
        $this->db->order_by("accounting_advanceliquidation.center_consecutive", "asc");
        $result = $this->db->get();
        return $result;
//        usort($value, function ($a, $b) {
//            return $a['center_consecutive'] - $b['center_consecutive'];
//        });
    }
//    function array_empty($mixed) {
//        if (is_array($mixed)) {
//            foreach ($mixed as $value) {
//                if (!array_empty($value)) {
//                    return false;
//                }
//            }
//        }
//        elseif (!empty($mixed)) {
//            return false;
//        }
//        return true;
//    }

    function getLiquidationById($id)
    {
        $person_persons_table = 'person_persons';
        $conf_costcenters_table = 'conf_costcenters';
        $this->db->select('accounting_advanceliquidation.id,
                            accounting_advanceliquidation.person_groupresponsable,
                            accounting_advanceliquidation.lodging_entrancedate,
                            accounting_advanceliquidation.lodging_exitdate,
                            conf_costcenters.center_name,
                            person_persons.person_name,
                            person_persons.person_lastname,
                            person_persons.person_secondlastname,
                            accounting_advanceliquidation.liquidation_used,
                            accounting_advanceliquidation.liquidation_repay,
                            accounting_advanceliquidation.liquidation_given,
                            accounting_advanceliquidation.center_consecutive,
                            accounting_advanceliquidation.amount_estimated,
                            accounting_advanceliquidation.diet_entrancedate_real,
                            accounting_advanceliquidation.diet_exitdate_real,
                            accounting_advanceliquidation.liquidation_date')
            ->from(self::TABLE_NAME)
            ->join($conf_costcenters_table, $conf_costcenters_table . '.center_id = ' . self::TABLE_NAME . '.center_idadvance', 'inner')
            ->join($person_persons_table, $person_persons_table . '.person_id = ' . self::TABLE_NAME . '.person_idworker', 'inner')
            ->where(self::TABLE_NAME . '.id', $id);
        $row = $this->db->get()->row();
        $value = array();
        if (empty($row->person_groupresponsable)) {
            $person_worker = $row->person_name . ' ' . $row->person_lastname . ' ' . $row->person_secondlastname;
        } else {
            $person_worker = $row->person_groupresponsable;
            //return $this->getLiquidationByResponsable($person_worker, $row->lodging_entrancedate, $row->lodging_exitdate);
        }

        $value [] = array(
            'id' => $row->id,
            'center_name' => $row->center_name,
            'person_worker' => strtoupper($person_worker),
            'lodging_entrancedate' => $row->lodging_entrancedate,
            'lodging_exitdate' => $row->lodging_exitdate,
            'liquidation_used' => $row->liquidation_used,
            'liquidation_repay' => $row->liquidation_repay,
            'liquidation_given' => $row->liquidation_given,
            'center_consecutive' => $row->center_consecutive,
            'amount_estimated' => $row->amount_estimated,
            'lodging_entrancedate_real' => $row->diet_entrancedate_real,
            'lodging_exitdate_real' => $row->diet_exitdate_real,
            'liquidation_date' => $row->liquidation_date);

        return $value;

    }

    function getLiquidationByResponsable($responsable, $starDate, $endDate)
    {
        $person_persons_table = 'person_persons';
        $conf_costcenters_table = 'conf_costcenters';
        $this->db->select('accounting_advanceliquidation.id,
                            accounting_advanceliquidation.person_groupresponsable,
                            accounting_advanceliquidation.lodging_entrancedate,
                            accounting_advanceliquidation.lodging_exitdate,
                            conf_costcenters.center_name,
                            person_persons.person_name,
                            person_persons.person_lastname,
                            person_persons.person_secondlastname,
                            accounting_advanceliquidation.liquidation_used,
                            accounting_advanceliquidation.liquidation_repay,
                            accounting_advanceliquidation.liquidation_given,
                            accounting_advanceliquidation.center_consecutive,
                            accounting_advanceliquidation.amount_estimated')
            ->from(self::TABLE_NAME)
            ->join($conf_costcenters_table, $conf_costcenters_table . '.center_id = ' . self::TABLE_NAME . '.center_idadvance', 'inner')
            ->join($person_persons_table, $person_persons_table . '.person_id = ' . self::TABLE_NAME . '.person_idworker', 'inner')
            ->where(self::TABLE_NAME . '.person_groupresponsable', $responsable)
            ->where(self::TABLE_NAME . '.lodging_entrancedate', $starDate)
            ->where(self::TABLE_NAME . '.lodging_exitdate', $endDate);

        $result = $this->db->get();
        $value = array();
        $amount = 0;
        $repay = 0;
        $used = 0;
        $given = 0;
        $rows = $result->result();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $amount += $row->amount_estimated;
                $repay += $row->liquidation_repay;
                $used += $row->liquidation_used;
                $given += $row->liquidation_given;
            }
            $value [] = array(
                'id' => $rows[0]->id,
                'center_name' => $rows[0]->center_name,
                'person_worker' => strtoupper($rows[0]->person_groupresponsable),
                'lodging_entrancedate' => $rows[0]->lodging_entrancedate,
                'lodging_exitdate' => $rows[0]->lodging_exitdate,
                'liquidation_used' => $used,
                'liquidation_repay' => $repay,
                'liquidation_given' => $given,
                'center_consecutive' => $rows[0]->center_consecutive,
                'amount_estimated' => $amount);
        }

        return $value;
    }

    function getLiquidationByRequestId($id)
    {
        $person_persons_table = 'person_persons';
        $conf_costcenters_table = 'conf_costcenters';
        $this->db->select('accounting_advanceliquidation.id,
                            accounting_advanceliquidation.request_id,
                            accounting_advanceliquidation.person_groupresponsable,
                            accounting_advanceliquidation.lodging_entrancedate,
                            accounting_advanceliquidation.lodging_exitdate,
                            accounting_advanceliquidation.print_date,
                            conf_costcenters.center_name,
                            person_persons.person_name,
                            person_persons.person_lastname,
                            person_persons.person_secondlastname,
                            accounting_advanceliquidation.liquidation_used,
                            accounting_advanceliquidation.liquidation_repay,
                            accounting_advanceliquidation.liquidation_given,
                            accounting_advanceliquidation.center_consecutive,
                            accounting_advanceliquidation.amount_estimated')
            ->from(self::TABLE_NAME)
            ->join($conf_costcenters_table, $conf_costcenters_table . '.center_id = ' . self::TABLE_NAME . '.center_idadvance', 'inner')
            ->join($person_persons_table, $person_persons_table . '.person_id = ' . self::TABLE_NAME . '.person_idworker', 'inner')
            ->where(self::TABLE_NAME . '.request_id', $id);
        $row = $this->db->get()->row();
        $value = array();
        if (empty($row->person_groupresponsable)) {
            $person_worker = $row->person_name . ' ' . $row->person_lastname . ' ' . $row->person_secondlastname;
        } else {
            $person_worker = $row->person_groupresponsable;
        }

        $value [] = array(
            'center_name' => $row->center_name,
            'person_worker' => strtoupper($person_worker),
            'lodging_entrancedate' => $row->lodging_entrancedate,
            'lodging_exitdate' => $row->lodging_exitdate,
            'print_date' => $row->print_date,
            'liquidation_used' => $row->liquidation_used,
            'liquidation_repay' => $row->liquidation_repay,
            'liquidation_given' => $row->liquidation_given,
            'center_consecutive' => $row->center_consecutive,
            'amount_estimated' => $row->amount_estimated);

        return $value;
    }

    public function insertLiquidation()
    {
        $id = $this->input->post('id');
        $centinela = new Centinela();
        $date = new Dates();
        $data = array(
            'liquidation_used' => $this->input->post('liquidation_used'),
            'liquidation_repay' => $this->input->post('liquidation_repay'),
            'liquidation_given' => $this->input->post('liquidation_given'),
            'liquidation_liquidated' => 't',
            'person_idliquidate' => $centinela->get_person_id(),
            'liquidation_date' => $this->input->post('liquidation_date'),
            'diet_entrancedate_real' => $this->input->post('lodging_entrancedate_real'),
            'diet_exitdate_real' => $this->input->post('lodging_exitdate_real')
        );
        $this->db->where('id', $id);
        $this->db->trans_begin();
        $re = $this->db->update(self::TABLE_NAME, $data);
        $logs = new Logs ();

        $mywhere = 'where id = ' . $id;
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
            return "{success: false, errors: { reason: 'Vuelva a intentarlo.' }}";
        }
    }

}
