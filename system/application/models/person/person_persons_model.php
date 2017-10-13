<?php

class Person_persons_model extends Model {
	const TABLE_NAME = 'person_persons';
	
	function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Funcion que devuelve los valores de la tabla
	 *
	 * @param int $hasta
	 * @param int $desde
	 * @return array
	 */
	public function getData($ci, $name, $lastname, $secondlastname, $province) {
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->db->select ( 'person_persons.person_id,
							person_persons.person_identity, 
							person_persons.person_name, 
							person_persons.person_lastname, 
							person_persons.person_secondlastname, 
							person_persons.person_phone, 
							conf_provinces.province_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . self::TABLE_NAME . '.province_id', 'inner' );
		$this->db->where ( 'person_deleted', 'no' );
		if (! empty( $province )) {
			$this->db->where ( self::TABLE_NAME. '.province_id', $province );
		}		
		if (! empty( $ci )) {
			$this->db->like ( self::TABLE_NAME. '.person_identity', $ci );
		}		
		if (! empty( $name )) {
			$this->db->like ( 'LOWER('.self::TABLE_NAME.'.person_name)' , strtolower($name) );
		}		
		if (! empty( $lastname )) {
			$this->db->like ( 'LOWER('.self::TABLE_NAME.'.person_lastname)' , strtolower($lastname) );
		}		
		if (! empty( $secondlastname )) {
			$this->db->like ( 'LOWER('.self::TABLE_NAME.'.person_secondlastname)' , strtolower($secondlastname) );
		}
		$this->db->order_by("person_name", "asc");
		$value = array();
		$result = $this->db->get ();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$person_fullname = Person_persons_model::getNameById($row->person_id);
				$value [] = array ('person_id' => $row->person_id, 
									'person_identity' => $row->person_identity, 
									'person_fullname' => $person_fullname, 
									'person_phone' => $row->person_phone, 
									'province_name' => $row->province_name );
			}
		}
		return $value;
	}
	
	public function getDataGrid($to, $from, $ci, $name, $lastname, $secondlastname, $province) {
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->db->select ( 'person_persons.person_id,
							person_persons.person_identity, 
							person_persons.person_name, 
							person_persons.person_lastname, 
							person_persons.person_secondlastname, 
							person_persons.person_phone, 
							conf_provinces.province_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . self::TABLE_NAME . '.province_id', 'inner' );
		//$this->db->where ( 'person_deleted', 'no' );
		if (! empty( $province )) {
			$this->db->where ( self::TABLE_NAME. '.province_id', $province );
		}		
		if (! empty( $ci )) {
			$this->db->like ( self::TABLE_NAME. '.person_identity', $ci );
		}		
		if (! empty( $name )) {
			$this->db->like ( 'LOWER('.self::TABLE_NAME.'.person_name)' , strtolower($name) );
		}		
		if (! empty( $lastname )) {
			$this->db->like ( 'LOWER('.self::TABLE_NAME.'.person_lastname)' , strtolower($lastname) );
		}		
		if (! empty( $secondlastname )) {
			$this->db->like ( 'LOWER('.self::TABLE_NAME.'.person_secondlastname)' , strtolower($secondlastname) );
		}
		$this->db->order_by("province_name", "asc");
		$this->db->order_by("person_name", "asc");
		$this->db->limit ( $to, $from );
		$this->db->distinct ();
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$person_fullname = Person_persons_model::getNameById($row->person_id);
				$value [] = array ('person_id' => $row->person_id, 
									'person_identity' => $row->person_identity, 
									'person_fullname' => $person_fullname, 
									'person_phone' => $row->person_phone, 
									'province_name' => $row->province_name );
			}
		}
		return $value;
	}
	
	public function getDataByProvinceId($province_id) {
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->db->select ( 'person_persons.person_id,
							person_persons.person_identity, 
							person_persons.person_name, 
							person_persons.person_lastname, 
							person_persons.person_secondlastname, 
							person_persons.person_phone, 
							conf_provinces.province_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . self::TABLE_NAME . '.province_id', 'inner' );
		$this->db->where ( 'person_deleted', 'no' );
		if (!empty($province_id)) {
			$this->db->where ( self::TABLE_NAME. '.province_id', $province_id );
		}
		$this->db->order_by("person_name", "asc");
		$value = array();
		$result = $this->db->get ();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$person_name = $row->person_name;
				$person_lastname = $row->person_lastname;
				$person_secondlastname = $row->person_secondlastname;
				$person_fullname = $person_name . ' ' . $person_lastname . ' ' . $person_secondlastname;
				$value [] = array ('person_id' => $row->person_id, 
									'person_identity' => $row->person_identity, 
									'person_fullname' => $person_fullname, 
									'person_phone' => $row->person_phone, 
									'province_name' => $row->province_name );
			}
		}
		return $value;
	}

	public function getDataByProvinceIdFilter($province_id, $name)
    	{
        $this->load->model('conf/conf_provinces_model');
        $this->db->select('person_persons.person_id,
							person_persons.person_identity,
							person_persons.person_name,
							person_persons.person_lastname,
							person_persons.person_secondlastname,
							person_persons.person_phone,
							conf_provinces.province_name');
        $this->db->from(self::TABLE_NAME);
        $this->db->join(Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . self::TABLE_NAME . '.province_id', 'inner');
        $this->db->where('person_deleted', 'no');
        if (!empty($province_id)) {
            $this->db->where(self::TABLE_NAME . '.province_id', $province_id);
        }
        if (!empty($name)) {
            $this->db->like('LOWER(' . self::TABLE_NAME . '.person_name)', strtolower(utf8_encode($name)));
        }
//        $this->db->or_like('LOWER(' . self::TABLE_NAME . '.person_lastname)', strtolower($name));
//        $this->db->or_like('LOWER(' . self::TABLE_NAME . '.person_secondlastname)', strtolower($name));
        $this->db->order_by("person_name", "asc");
        $value = array();
        $result = $this->db->get();
        if ($result->result() != null) {
            foreach ($result->result() as $row) {
                $person_name = $row->person_name;
                $person_lastname = $row->person_lastname;
                $person_secondlastname = $row->person_secondlastname;
                $person_fullname = $person_name . ' ' . $person_lastname . ' ' . $person_secondlastname;
                $value [] = array('person_id' => $row->person_id,
                    'person_identity' => $row->person_identity,
                    'person_fullname' => $person_fullname,
                    'person_phone' => $row->person_phone,
                    'province_name' => $row->province_name);
            }
        }
        return $value;
    }
	
	/**
	 * funcion que devuelve la cantidad de registros en la tabla
	 *
	 */
	public function getCant($ci, $name, $lastname, $secondlastname, $province) {
		$this->db->select ( 'person_persons.person_id,
							person_persons.person_identity, 
							person_persons.person_name, 
							person_persons.person_lastname, 
							person_persons.person_secondlastname, 
							person_persons.person_phone, 
							conf_provinces.province_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . self::TABLE_NAME . '.province_id', 'inner' );
		//$this->db->where ( 'person_deleted', 'no' );
		if (! empty( $province )) {
			$this->db->where ( self::TABLE_NAME. '.province_id', $province );
		}		
		if (! empty( $ci )) {
			$this->db->like ( self::TABLE_NAME. '.person_identity', $ci );
		}		
		if (! empty( $name )) {
			$this->db->like ( 'LOWER('.self::TABLE_NAME.'.person_name)' , strtolower($name) );
		}		
		if (! empty( $lastname )) {
			$this->db->like ( 'LOWER('.self::TABLE_NAME.'.person_lastname)' , strtolower($lastname) );
		}		
		if (! empty( $secondlastname )) {
			$this->db->like ( 'LOWER('.self::TABLE_NAME.'.person_secondlastname)' , strtolower($secondlastname) );
		}
		return $this->db->count_all_results ();
	}
	
	/**
	 * Esta es la funcion encargada de insertar los transportes
	 *
	 * @return boolean
	 */
	public function insert() {
		$this->load->model ( 'person/person_workers_model' );
		$person_id = $this->input->post ( 'person_id' );
		$persons ['person_identity'] = $this->input->post ( 'person_identity' );
		$persons ['person_name'] = strtoupper($this->input->post ( 'person_name' ));
		$persons ['person_lastname'] = strtoupper($this->input->post ( 'person_lastname' ));
		$persons ['person_secondlastname'] = strtoupper($this->input->post ( 'person_secondlastname' ));
		$persons ['person_address'] = $this->input->post ( 'person_address' );
		$persons ['person_phone'] = $this->input->post ( 'person_phone' );
		$persons ['province_id'] = $this->input->post ( 'province_id' );
		$persons ['person_deleted'] = 'no';
		$isworker = $this->input->post ( 'person_isworker' );
		$persons ['person_isworker'] = $isworker;
		$worker_email = $this->input->post ( 'worker_email' );
		$worker_phone = $this->input->post ( 'worker_phone' );
		$position_id = is_numeric($this->input->post ( 'position_id' )) ? $this->input->post ( 'position_id' ) : null;//$this->input->post ( 'position_id' );//empty($this->input->post ( 'position_id' )) ? null : $this->input->post ( 'position_id' );
		if (empty ( $person_id )) {
			if (self::getCountByIdentity($this->input->post ( 'person_identity' )) == 0) {
			    $this->db->trans_begin ();
				$re = $this->db->insert ( self::TABLE_NAME, $persons );
				$person_id = $this->db->insert_id ();
				$logs = new Logs ( );
				$myquery = $logs->sqlinsert ( self::TABLE_NAME, $persons );
				$logs->write ( self::TABLE_NAME, 'INSERT', $myquery );
				
				if ($this->db->trans_status () === FALSE) {
					$this->db->trans_rollback ();
				} else {
					$this->db->trans_commit ();
				}			
				if ($isworker) {
					$re = Person_workers_model::insert ( $person_id, $worker_email, $worker_phone, /*$person_idparent, $center_id, */$position_id );
				}
				if ($re == true) {
					return "true";
				} else
					return "false";
			} else {
				return " false" . ", errors: { reason: 'Ya existe una persona con ese carné de identidad.' }";
			}
		} else {
			$this->db->where ( 'person_id', $person_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $persons );
			$logs = new Logs ( );
			
			$mywhere = 'where person_id = '.$person_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $persons, $mywhere);
			
			$logs->write( self::TABLE_NAME, 'UPDATE', $myquery);
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
			if ($isworker) {
				$re = Person_workers_model::insert ( $person_id, $worker_email, $worker_phone, /*$person_idparent, $center_id, */$position_id );
			}
			if ($re == true){
				return "true"; 
			}
			else
				return "false";
		}
	}
	
	/**
	 * Funcion para eliminar una provincia por su nombre
	 *
	 * @param string $requestperson_name
	 */
	public function delete($person_id) {
		$this->db->where ( 'person_id', $person_id );
		$persons ['person_deleted'] = 'si';
		$this->db->trans_begin ();
		$this->db->update ( self::TABLE_NAME, $persons );
		$logs = new Logs ( );
			
			$mywhere = 'where person_id = '.$person_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $persons, $mywhere);
			
			$logs->write( self::TABLE_NAME, 'UPDATE', $myquery);
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
		//$this->db->delete ( self::TABLE_NAME );
	}
	
	/**
	 * Esta funcion devuelve datos dado el nombre
	 *
	 * @param string $service_name
	 * @return services
	 */
	public function getByName($person_name) {
		$result = $this->db->get_where ( self::TABLE_NAME, array ('person_name' => $person_name ) );
		return $result->result_array ();
	
	}
	
	public function get() {
		$result = $this->db->get ( self::TABLE_NAME );
		return $result->result_array;
	} // de la funcion
	

	public function getById($person_id) {
		$this->load->model ( 'person/person_workers_model' );
		$person = 'person_persons.person_id,
					person_persons.person_identity, 
					person_persons.person_name, 
					person_persons.person_lastname, 
					person_persons.person_secondlastname,
					person_persons.person_address, 
					person_persons.person_phone, 
					person_persons.province_id,
					person_persons.person_isworker';
		$this->db->select ( $person );
		if (Person_workers_model::getCountById ( $person_id ) > 0) {
			$worker = $person . ',person_workers.worker_email,
								person_workers.worker_phone,
								person_workers.position_id';
			$this->db->select ( $worker );
			$this->db->from ( self::TABLE_NAME );
			$this->db->join ( Person_workers_model::TABLE_NAME, Person_workers_model::TABLE_NAME . '.person_id = ' . self::TABLE_NAME . '.person_id', 'inner' );
			$this->db->where ( 'person_persons.person_id', $person_id );
			$result = $this->db->get ();
			return $result->result_array ();
		} else {
			$this->db->from ( self::TABLE_NAME );
			$this->db->where ( 'person_persons.person_id', $person_id );
			$result = $this->db->get ();
			return $result->result_array ();
		}
	
	}
	
	public function getIdentityById($person_id) {

		$this->db->select ( 'person_identity' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'person_persons.person_id', $person_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$person_identity = $row->person_identity;
		}
		return $person_identity;
	}
	
	public function getProvinceById($person_id) {
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->db->select ( 'province_id' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'person_id', $person_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$province_id = $row->province_id;
		}
		return Conf_provinces_model::getNameById($province_id);
	}
	
	
	public function getNameById($person_id) {
		$person_fullname = '';
		$this->db->select ( 'person_name,
							person_lastname,
							person_secondlastname' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'person_id', $person_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$person_name = $row->person_name;
			$person_lastname = $row->person_lastname;
			$person_secondlastname = $row->person_secondlastname;
			$person_fullname = $person_name . ' ' . $person_lastname . ' ' . $person_secondlastname;
		}
		return $person_fullname;
	
	}
	
	public function getCountByIdentity($person_identity) {
		$this->db->select ( 'person_id' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'person_identity', $person_identity );
		return $this->db->count_all_results ();
	}
	

}
?>
