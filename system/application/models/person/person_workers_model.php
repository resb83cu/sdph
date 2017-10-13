<?php

class Person_workers_model extends Model {
	const TABLE_NAME = 'person_workers';
	
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
	public function getDataByProvince($province_id) {
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->load->model ( 'person/person_persons_model' );
		$this->db->select ( 'person_workers.person_id,
							person_persons.person_identity, 
							person_persons.person_phone, 
							conf_provinces.province_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Person_persons_model::TABLE_NAME, Person_persons_model::TABLE_NAME . '.person_id = ' . self::TABLE_NAME . '.person_id', 'inner' );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . Person_persons_model::TABLE_NAME . '.province_id', 'inner' );
		$this->db->where ( 'person_deleted', 'no' );
		if (! empty( $province_id )) {
			$this->db->where ( Person_persons_model::TABLE_NAME. '.province_id', $province_id );
		}
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
	
	public function getDirector($province_id) {
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->load->model ( 'person/person_persons_model' );
		$this->load->model ( 'conf/conf_positions_model' );
		$this->db->select ( 'person_workers.person_id,
							person_persons.person_identity, 
							person_persons.person_phone, 
							conf_provinces.province_name,
							conf_positions.position_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Person_persons_model::TABLE_NAME, Person_persons_model::TABLE_NAME . '.person_id = ' . self::TABLE_NAME . '.person_id', 'inner' );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . Person_persons_model::TABLE_NAME . '.province_id', 'inner' );
		$this->db->join ( Conf_positions_model::TABLE_NAME, Conf_positions_model::TABLE_NAME . '.position_id = ' . self::TABLE_NAME . '.position_id', 'inner' );
		$this->db->where ( 'person_deleted', 'no' );
		$this->db->where ( Person_persons_model::TABLE_NAME. '.province_id', $province_id );
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$person_fullname = Person_persons_model::getNameById($row->person_id);
				$position = strtolower($row->position_name);
				$flag = strripos($position, 'director');
				$flag2 = strripos($position, 'presidente');
				if (($flag === false) && ($flag2 === false) ) {
					
				} else {
					$value [] = array ('person_id' => $row->person_id, 
								'person_identity' => $row->person_identity, 
								'person_fullname' => $person_fullname, 
								'person_phone' => $row->person_phone, 
								'province_name' => $row->province_name );
				}
				
			}
		}
		return $value;
	}

	public function getData($to, $from, $province_id) {
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->load->model ( 'person/person_persons_model' );
		$this->db->select ( 'person_workers.person_id,
							person_persons.person_identity, 
							person_persons.person_name, 
							person_persons.person_lastname, 
							person_persons.person_secondlastname, 
							person_persons.person_phone, 
							conf_provinces.province_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Person_persons_model::TABLE_NAME, Person_persons_model::TABLE_NAME . '.person_id = ' . self::TABLE_NAME . '.person_id', 'inner' );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . Person_persons_model::TABLE_NAME . '.province_id', 'inner' );
		$this->db->limit ( $to, $from );
		$this->db->where ( 'person_deleted', 'no' );
		if (!is_null($province_id) && is_numeric($province_id)) {
			$this->db->where ( Person_persons_model::TABLE_NAME. '.province_id', $province_id );
		}
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
	
	/**
	 * funcion que devuelve la cantidad de registros en la tabla
	 *
	 */
	public function getCant() {
		return $this->db->count_all ( self::TABLE_NAME );
	}
	
	/**
	 * Esta es la funcion encargada de insertar los transportes
	 *
	 * @return boolean
	 */
	public function insert($person_id, $worker_email, $worker_phone, /*$person_idparent, $center_id, */$position_id) {
		$workers ['person_id'] = $person_id;
		$workers ['worker_email'] = $worker_email;
		$workers ['worker_phone'] = $worker_phone;
		$workers ['position_id'] = $position_id;
		if (self::getCountById ( $person_id ) > 0) {
			$this->db->where ( 'person_id', $person_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $workers );
			$logs = new Logs ( );
			
			$mywhere = 'where person_id = '.$person_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $workers, $mywhere);
			
			$logs->write( self::TABLE_NAME, 'UPDATE', $myquery);
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
			if ($re == true)
				return "true"; else
				return "false";
		
		} else {
		    $this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $workers );
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $workers );
			$logs->write ( self::TABLE_NAME, 'INSERT', $myquery );
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}			
			
			if ($re == true)
				return "true"; else
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
	

	public function getCountById($person_id) {
		$this->db->select ( 'person_id' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'person_id', $person_id );
		return $this->db->count_all_results ();
	}
	
	public function getPositionById($person_id) {
		$this->db->select ( 'conf_positions.position_name' );
		$this->db->from ( 'conf_positions' );
		$this->db->join ( self::TABLE_NAME, self::TABLE_NAME . '.position_id = conf_positions.position_id', 'inner' );
		$this->db->where ( 'person_id', $person_id );
		$result = $this->db->get ();
		$position_name = null;
		foreach ( $result->result () as $row ) {
			$position_name = $row->position_name;
		}
		return $position_name;
	}
	
	public function getPhoneById($person_id) {
		$this->db->select ( 'worker_phone' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'person_id', $person_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$worker_phone = $row->worker_phone;
		}
		return $worker_phone;
	}
	
}
?>
