<?php

class User_users_model extends Model {

    const TABLE_NAME = 'user_users';

    function __construct() {
        parent::__construct();
    }

    /**
     * Funcion que devuelve los valores de la tabla
     *
     * @param int $hasta
     * @param int $desde
     * @return array
     */
    public function getData($to, $from, $province_id) {
        $this->load->model('conf/conf_provinces_model');
        $this->load->model('user/user_rolls_model');
        $this->load->model('person/person_persons_model');
        $this->load->model('user/user_users_model');
        $person_persons_table = 'person_persons';
        $person_workers_table = 'person_workers';
        $user_users_table = 'user_users';
        $this->db->select('user_users.person_id,
							user_users.user_name,
							user_users.user_createdate,
							user_users.roll_id,
							person_persons.province_id,
							user_users.locked');
        $this->db->from(self::TABLE_NAME);
        $this->db->join($person_workers_table, $person_workers_table . '.person_id = ' . self::TABLE_NAME . '.person_id', 'inner');
        $this->db->join($person_persons_table, $person_persons_table . '.person_id = ' . $person_workers_table . '.person_id', 'inner');

        $this->db->limit($to, $from);
        $this->db->where($person_persons_table . '.person_deleted', 'no');
        //$this->db->where ( $user_users_table.'.user_deleted', 'no' );
        if (!is_null($province_id) && is_numeric($province_id)) {
            $this->db->where($person_persons_table . '.province_id', $province_id);
        }
        $this->db->order_by('user_users.locked asc');
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $person_fullname = Person_persons_model::getNameById($row->person_id);
                $roll_description = User_rolls_model::getDescriptionById($row->roll_id);
                $province_name = Conf_provinces_model::getNameById($row->province_id);
                $value [] = array('person_id' => $row->person_id, 'user_name' => $row->user_name, 'person_fullname' => $person_fullname, 'user_createdate' => $row->user_createdate, 'roll_description' => $roll_description, 'province_id' => $row->province_id, 'province_name' => $province_name, 'locked' => $row->locked);
            }
        }
        return $value;
    }

    public function getDataGrid($to, $from, $name, $province) {
        $this->load->model('conf/conf_provinces_model');
        $this->load->model('user/user_rolls_model');
        $this->load->model('person/person_persons_model');
        $this->load->model('user/user_users_model');
        $person_persons_table = 'person_persons';
        $person_workers_table = 'person_workers';
        $user_users_table = 'user_users';
        $this->db->select('user_users.person_id,
							user_users.user_name,
							user_users.user_createdate,
							user_users.roll_id,
							person_persons.province_id,
							user_users.locked');
        $this->db->from(self::TABLE_NAME);
        $this->db->join($person_workers_table, $person_workers_table . '.person_id = ' . self::TABLE_NAME . '.person_id', 'inner');
        $this->db->join($person_persons_table, $person_persons_table . '.person_id = ' . $person_workers_table . '.person_id', 'inner');

        $this->db->limit($to, $from);
        $this->db->where($person_persons_table . '.person_deleted', 'no');
        if (!empty($province)) {
            $this->db->where($person_persons_table . '.province_id', $province);
        }
        if (!empty($name)) {
            $this->db->like(self::TABLE_NAME . '.user_name', strtolower($name));
            $this->db->or_like(self::TABLE_NAME . '.user_name', ucfirst($name));
        }
        $this->db->order_by('user_users.locked asc, person_persons.province_id asc');
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $person_fullname = Person_persons_model::getNameById($row->person_id);
                $roll_description = User_rolls_model::getDescriptionById($row->roll_id);
                $province_name = Conf_provinces_model::getNameById($row->province_id);
                $value [] = array('person_id' => $row->person_id, 'user_name' => $row->user_name, 'person_fullname' => $person_fullname, 'user_createdate' => $row->user_createdate, 'roll_description' => $roll_description, 'province_id' => $row->province_id, 'province_name' => $province_name, 'locked' => $row->locked);
            }
        }
        return $value;
    }

    public function getDataPdf($to, $from) {
        $this->load->model('conf/conf_costcenters_model');
        $this->load->model('user/user_rolls_model');
        $this->load->model('person/person_persons_model');
        $this->load->model('user/user_users_model');
        $user_users_table = 'user_users';
        $this->db->select('user_users.person_id,
							user_users.user_name,
							user_users.user_createdate,
							user_users.roll_id,
							user_users.center_id,
							user_users.locked');
        $this->db->from(self::TABLE_NAME);
        $this->db->limit($to, $from);
        $this->db->order_by('user_users.center_id', 'asc');
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $person_fullname = Person_persons_model::getNameById($row->person_id);
                $roll_description = User_rolls_model::getDescriptionById($row->roll_id);
                $center_name = Conf_costcenters_model::getNameById($row->center_id);
                $last_password = self::getLastPassModify($row->person_id);
                $value [] = array('person_id' => $row->person_id, 'user_name' => $row->user_name, 'person_fullname' => $person_fullname, 'user_createdate' => $row->user_createdate, 'roll_description' => $roll_description, 'center_name' => $center_name, 'last_password' => $last_password);
            }
        }
        return $value;
    }

    /**
     * funcion que devuelve la cantidad de registros en la tabla
     *
     */
    public function getCant() {
        return $this->db->count_all(self::TABLE_NAME);
    }

    public function getLastPassModify($person_id) {
        $this->db->select('person_id,
							  user_password,
							  put_date');
        $this->db->from('history_password');
        $this->db->limit(1, 0);
        $this->db->where('person_id', $person_id);
        $this->db->order_by('put_date', 'desc');
        $result = $this->db->get();
        $value = '1900-01-01';
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $value = $row->put_date;
            }
        }
        return $value;
    }

    /**
     * Esta es la funcion encargada de insertar
     *
     * @return boolean
     */
    public function insert($operation) {
        $this->load->model('conf/conf_costcenters_model');
        $person_id = $this->input->post('person_id');
        $center = $this->input->post('center_id');

        $users ['user_name'] = $this->input->post('user_name');
        $users ['user_password'] = $this->encrypt->sha1($this->input->post('user_password'));
        $users ['roll_id'] = $this->input->post('roll_id');
        $users ['center_id'] = $center;
        $users ['locked'] = $this->input->post('locked');
        if (self::getCountById($person_id) > 0 && $operation == 'UPDATE') {
            $message = 'Error al salvar los datos';
            $flag = TRUE;

            $re1 = $this->db->query('select  user_name  from user_users where person_id <> \'' . $person_id . '\''); //ojo conno que si ponia en 'user_name' estaba pasando el nuevo user-name en vez del mismo
            if ($re1->result() != null) {
                foreach ($re1->result() as $row) {
                    if ($row->user_name == $users ['user_name']) {
                        $flag = FALSE;
                        $message = 'Ya esta registrado ese nombre de usuario. Intente con otro.';
                    }
                }
            }

            $re2 = false; //si dejaba $re me daba palo por conflicto por el anterior $re
            if ($flag == TRUE) {
                $this->db->where('person_id', $this->input->post('person_id'));
                $this->db->trans_begin();
                $re2 = $this->db->update(self::TABLE_NAME, $users);
                self::unlock($person_id);
                $logs = new Logs ( );

                $mywhere = 'where person_id = ' . $person_id;
                $myquery = $logs->sqlupdate(self::TABLE_NAME, $users, $mywhere);
                $logs->write(self::TABLE_NAME, 'UPDATE', $myquery);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->query("delete from locked_count where person_id=" . $person_id);
                    $this->db->trans_commit();
                }
            }

            if ($re2 == true) {
                return "true";
            } else {

                return " false" . ", errors: { reason: '$message' }";
            }
        } else {
            if ($operation == 'INSERT') { //agregar
                $date = getdate();
                $users ['person_id'] = $person_id;
                $users ['user_createdate'] = $date ['year'] . '-' . $date ['mon'] . '-' . $date ['mday'];
                $message = 'Error al salvar los datos';
                $flag = TRUE;
                $re = $this->db->query('select  *  from user_users');
                if ($re->result() != null) {
                    foreach ($re->result() as $row) {
                        if ($row->user_name == $users ['user_name']) {
                            $flag = FALSE;
                            $message = 'Ya esta registrado ese nombre de usuario. Intente con otro.';
                        }
                        if ($row->person_id == $users ['person_id']) {
                            $flag = FALSE;
                            $message = 'Ya esta registrado ese mismo trabajador,solo puede estarlo una sola vez con un rol.';
                        }
                    }
                }

                $re2 = false;
                if ($flag == TRUE) {
                    $this->db->trans_begin();
                    $re2 = $this->db->insert(self::TABLE_NAME, $users);
                    $date = Dates::now();
                    $logs = new Logs ( );
                    $myquery = $logs->sqlinsert(self::TABLE_NAME, $users);
                    $logs->write(self::TABLE_NAME, 'INSERT', $myquery);

                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                    } else {
                        $this->db->trans_commit();
                    }
                }
                if ($re2 == true) {
                    return "true";
                } else
                    return " false" . ", errors: { reason: '$message' }";
            } else {
                if (self::getCountById($person_id) == 0 && $operation == 'UPDATE') {
                    return " false" . ", errors: { reason: 'No puede a un usuario ponerle otro trabajador' }";
                }
            }
        }
    }

    /**
     * Funcion para eliminar una provincia por su nombre
     *
     * @param string $requestperson_name
     */
    public function delete($person_id) {

        $user['user_deleted'] = 'si';
        $this->db->where('person_id', $person_id);
        $this->db->trans_begin();
        $this->db->update(self::TABLE_NAME, $user);
        $logs = new Logs ( );
        $mywhere = 'where person_id = ' . $person_id;
        $myquery = $logs->sqldelete(self::TABLE_NAME, $mywhere);
        $logs->write(self::TABLE_NAME, 'DELETE', $myquery);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
    }

    public function deleteSession($person_id) {
        $this->db->where('person_id', $person_id);
        $this->db->delete('current_sessions');
    }

    public function unlock($person_id) {
        $this->db->where('person_id', $person_id);
        $this->db->trans_begin();
        //$this->db->update ( self::TABLE_NAME, $persons );
        $this->db->delete('locked_count');
        $logs = new Logs ( );
        $mywhere = 'where person_id = ' . $person_id;
        $myquery = $logs->sqldelete(self::TABLE_NAME, $mywhere);
        $logs->write('locked_count', 'DELETE', $myquery);
	 $session_tmp = $this->db->from('user_sessions')->where('person_id', $person_id)->get();
        if (count($session_tmp->result()) == 0) {
            $session_id = "primera entrada";
            $session_ip = "192.168.0.0";
            $session_user_agent = "Mozilla";
        } else {
            $session_id = $this->db->from('user_sessions')->where('person_id', $person_id)->get()->row()->session_id;
            $session_ip = $this->db->from('user_sessions')->where('person_id', $person_id)->get()->row()->session_ip;
            $session_user_agent = $this->db->from('user_sessions')->where('person_id', $person_id)->get()->row()->session_user_agent;
        }
        $date = new Dates();
        $session = array(
            'session_id' => $session_id,
            'person_id' => $person_id,
            'session_begindate' => $date->now(),
            'session_enddate' => $date->now(),
            'session_ip' => $session_ip,
            'session_user_agent' => $session_user_agent
        );

        $this->db->insert('user_sessions', $session);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
    }

    /**
     * Esta funcion devuelve datos dado el nombre
     *
     * @param string $service_name
     * @return services
     */
    public function getByName($person_name) {
        $result = $this->db->get_where(self::TABLE_NAME, array('person_name' => $person_name));
        return $result->result_array();
    }

    public function get() {
        $result = $this->db->get(self::TABLE_NAME);
        return $result->result_array;
    }

// de la funcion

    public function getById($person_id) {
        $this->load->model('conf/conf_provinces_model');
        $this->load->model('user/user_rolls_model');
        $this->load->model('person/person_persons_model');
        $person_persons_table = 'person_persons';
        $person_workers_table = 'person_workers';
        $this->db->select('user_users.person_id,
							user_users.user_name,
							user_users.user_password,
							user_users.roll_id,
							person_persons.province_id,
							user_users.center_id,
							conf_costcenters.person_id as person_idparent,
                                                        user_users.locked');
        $this->db->from(self::TABLE_NAME);
        $this->db->join($person_workers_table, $person_workers_table . '.person_id = ' . self::TABLE_NAME . '.person_id', 'inner');
        $this->db->join($person_persons_table, $person_persons_table . '.person_id = ' . $person_workers_table . '.person_id', 'inner');
        $this->db->join('conf_costcenters', 'conf_costcenters.center_id = user_users.center_id', 'inner');
        $this->db->where($person_persons_table . '.person_deleted', 'no');
        $this->db->where(self::TABLE_NAME . '.person_id', $person_id);
        $result = $this->db->get();
        $value = array();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {

                $value [] = array('person_id' => $row->person_id, 'user_name' => $row->user_name, 'roll_id' => $row->roll_id, 'province_id' => $row->province_id, 'center_id' => $row->center_id, 'person_idparent' => $row->person_idparent, 'locked' => $row->locked);
            }
        }
        return $value;
    }

    public function getId($person_id) {

        $this->db->select('person_id');
        $this->db->from(self::TABLE_NAME);
        $this->db->where('person_id', $person_id);
        $result = $this->db->get();
        foreach ($result->result() as $row) {
            $person_id = $row->person_id;
        }
        return $person_id;
    }

    public function getNameById($person_id) {
        $this->db->select('person_name,
							person_lastname,
							person_secondlastname');
        $this->db->from(self::TABLE_NAME);
        $this->db->where('person_id', $person_id);
        $result = $this->db->get();
        foreach ($result->result() as $row) {
            $person_name = $row->person_name;
            $person_lastname = $row->person_lastname;
            $person_secondlastname = $row->person_secondlastname;
            $person_fullname = $person_name . ' ' . $person_lastname . ' ' . $person_secondlastname;
        }
        return $person_fullname;
    }

    public function getCountById($person_id) {
        $this->db->select('person_id');
        $this->db->from(self::TABLE_NAME);
        $this->db->where('person_id', $person_id);
        return $this->db->count_all_results();
    }

    public function changePassword($user_name, $newpassword) {
        $users ['user_password'] = $this->encrypt->sha1($newpassword);
        $re = false;
        $query = $this->db->query('select * from user_users where user_name=?', array($user_name));

        if ($query->num_rows() == 1) {
            $this->db->where('user_name', $user_name); //el usuario logueado
            $this->db->trans_begin();
            $re = $this->db->update(self::TABLE_NAME, $users);
            $logs = new Logs ( );

            $mywhere = 'where user_name = ' . $user_name;
            $myquery = $logs->sqlupdate(self::TABLE_NAME, $users, $mywhere);

            $logs->write(self::TABLE_NAME, 'UPDATE', $myquery);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }

            if ($re == true) {
                //$this->session->set_userdata('user_password', $users ['user_password']);
                return TRUE;
            }
        } else {
            return FALSE;
        }
    }

}

?>
