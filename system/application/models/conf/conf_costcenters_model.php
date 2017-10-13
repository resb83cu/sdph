<?php

class Conf_costcenters_model extends Model {
	const TABLE_NAME = 'conf_costcenters';
	
	function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Funcion que devuelve los valores de la tabla Conf_motives 
	 *
	 * @param int $hasta
	 * @param int $desde
	 * @return array
	 */
	public function getData() {
		$this->load->model ( 'person/person_persons_model' );
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->db->select ( 'conf_costcenters.center_id, 
							conf_costcenters.center_name,
							conf_costcenters.center_deleted, 
							conf_costcenters.person_id,
							person_persons.person_name, 
							conf_costcenters.province_id,
							conf_provinces.province_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Person_persons_model::TABLE_NAME, Person_persons_model::TABLE_NAME . '.person_id = ' . self::TABLE_NAME . '.person_id', 'left' );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . self::TABLE_NAME . '.province_id', 'left' );
		$this->db->where ( 'center_deleted', 'no' );
		//$this->db->limit ( $to, $from);
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != "") {
			foreach ( $result->result () as $row ) {
				$value [] = array ('center_id' => $row->center_id, 
									'center_name' => $row->center_name);
			}
		
		}
		return $value;
	}
	
    public function getDataSap()
    {
        $this->db->select('center_id, center_name, center_sap');
        $this->db->from(self::TABLE_NAME);
        $this->db->where('center_deleted', 'no');
        $this->db->where('center_sap !=', "");
        $result = $this->db->get();
        $value = array();
        if ($result->result() != "") {
            foreach ($result->result() as $row) {
                $value [] = array('center_id' => $row->center_id,
                    'center_name' => $row->center_sap . " / " . $row->center_name);
            }
        }
        return $value;
    }

    public function getDataSapService()
    {
        $this->db->select('center_id, center_name');
        $this->db->from(self::TABLE_NAME);
        $this->db->where('center_deleted', 'no');
        $this->db->where('center_sap !=', "");
        $result = $this->db->get();
        $value = array();
        if ($result->result() != "") {
            foreach ($result->result() as $row) {
                $value [$row->center_id] = $row->center_name;
            }
        }
        return $value;
    }

    public function getDataService()
    {
        $this->db->select('center_id,
							center_name');
        $this->db->from(self::TABLE_NAME);
        $this->db->where('center_deleted', 'no');
        $result = $this->db->get();
        $value = array();
        if ($result->result() != "") {
            foreach ($result->result() as $row) {
                $value [$row->center_id] = $row->center_name;
            }
        }
        return $value;
    }
	
	public function getDataByProvince($province_id) {
		$this->load->model ( 'person/person_persons_model' );
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->db->select ( 'conf_costcenters.center_id, 
							conf_costcenters.center_name' );
		$this->db->from ( self::TABLE_NAME );
		if (! empty( $province_id )) {
			$this->db->where ( self::TABLE_NAME. '.province_id', $province_id );
		}		
		$result = $this->db->get ();
		return $result->result_array ();
	}
	
	public function getDataGrid($to, $from, $name, $province) {
		$this->load->model ( 'person/person_persons_model' );
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->db->select ( 'conf_costcenters.center_id, 
							conf_costcenters.center_name,
							conf_costcenters.center_deleted, 
							conf_costcenters.person_id,
							person_persons.person_name, 
							conf_costcenters.province_id,
							conf_provinces.province_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Person_persons_model::TABLE_NAME, Person_persons_model::TABLE_NAME . '.person_id = ' . self::TABLE_NAME . '.person_id', 'left' );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . self::TABLE_NAME . '.province_id', 'left' );
		if (! empty( $province )) {
			$this->db->where ( self::TABLE_NAME. '.province_id', $province );
		}		
		if (! empty( $name )) {
			$this->db->like ( self::TABLE_NAME. '.center_name', strtolower($name) );
			$this->db->or_like ( self::TABLE_NAME. '.center_name', strtoupper($name) );
			$this->db->or_like ( self::TABLE_NAME. '.center_name', ucfirst($name) );
		}
		$this->db->order_by('conf_costcenters.center_deleted asc, conf_costcenters.province_id asc');
		$this->db->limit ( $to, $from);
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != "") {
			foreach ( $result->result () as $row ) {
				if (!empty($row->person_id)) {
					$fullname = Person_persons_model::getNameById($row->person_id);
				} else {
					$fullname = '---';
				}
				
				$deleted = ucfirst($row->center_deleted);
				$value [] = array ('center_id' => $row->center_id, 
									'center_name' => $row->center_name,
									'center_deleted' => $deleted,
									'person_id' => $row->person_id, 
									'person_name' => $fullname,
									'province_id' => $row->province_id, 
									'province_name' => $row->province_name);
			}
		
		}
		return $value;
	}
	
	/**
	 * funcion que devuelve la cantidad de registros en la tabla ejemplo
	 *
	 */
	public function getCant($to, $from, $name, $province) {
		$this->load->model ( 'person/person_persons_model' );
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->db->select ( 'conf_costcenters.center_id, 
							conf_costcenters.center_name,
							conf_costcenters.center_deleted, 
							conf_costcenters.person_id,
							person_persons.person_name, 
							conf_costcenters.province_id,
							conf_provinces.province_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Person_persons_model::TABLE_NAME, Person_persons_model::TABLE_NAME . '.person_id = ' . self::TABLE_NAME . '.person_id', 'left' );
		$this->db->join ( Conf_provinces_model::TABLE_NAME, Conf_provinces_model::TABLE_NAME . '.province_id = ' . self::TABLE_NAME . '.province_id', 'left' );
		if (! empty( $province )) {
			$this->db->where ( self::TABLE_NAME. '.province_id', $province );
		}		
		if (! empty( $name )) {
			$this->db->like ( self::TABLE_NAME. '.center_name', strtolower($name) );
			$this->db->or_like ( self::TABLE_NAME. '.center_name', ucfirst($name) );
		}	
		$this->db->limit ( $to, $from);
		return $this->db->count_all_results();
	}
	
	/**
	 * Esta es la funcion encargada de insertar las provincias
	 *
	 * @return boolean
	 */
	public function insert() {

		$center_id = $this->input->post ( 'center_id' );
		$centers ['center_name'] = $this->input->post ( 'center_name' );
		$centers ['province_id'] = $this->input->post ( 'province_id' );
		$centers ['person_id'] = $this->input->post ( 'person_id' );
		if (empty ( $center_id )) {
			$this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $centers );
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $centers );
			$logs->write ( self::TABLE_NAME, 'INSERT', $myquery );
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
			if ($re == true)
				return "true"; else
				return "false";
		
		} else {
			$centers ['center_deleted'] = strtolower($this->input->post ( 'center_deleted' ));
			$this->db->where ( 'center_id', $center_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $centers );
			$logs = new Logs ( );
			
			$mywhere = 'where center_id = '.$center_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $centers, $mywhere);
			
			$logs->write( self::TABLE_NAME, 'UPDATE', $myquery);
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
			if ($re == true)
                return "true";
            else
                return "false";
        }
    }

    public function incrementConsecutive($center_id)
    {
        $this->db->set('center_consecutive', 'center_consecutive + 1', FALSE);
        $this->db->where('center_id', $center_id);
        $re = $this->db->update(self::TABLE_NAME);
        if ($re == true) {
            return "true";
        } else {
            return "false";
        }
    }
	
	/**
	 * Funcion para eliminar una provincia por su nombre
	 *
	 * @param string $motive_name
	 */
	public function delete($center_id) {
		$centers ['center_deleted'] = 'si';
		$this->db->where ( 'center_id', $center_id );
		$this->db->trans_begin ();
		$re = $this->db->update ( self::TABLE_NAME, $centers );
		$logs = new Logs ( );
		$mywhere = 'where center_id = ' . $center_id;
		$myquery = $logs->sqldelete ( self::TABLE_NAME, $mywhere );
		$logs->write ( self::TABLE_NAME, 'DELETE', $myquery );
		
		if ($this->db->trans_status () === FALSE) {
			$this->db->trans_rollback ();
		} else {
			$this->db->trans_commit ();
		}
		
	}
	
	/**
	 * Esta funcion devuelve dado el nombre
	 *
	 * @param string $motive_name
	 * @return motives
	 */
	public function getByName($center_name) {
		$result = $this->db->get_where ( self::TABLE_NAME, array ('center_name' => $center_name ) );
		return $result->result_array ();
	
	}
	
	public function get() {
		$result = $this->db->get ( self::TABLE_NAME );
		return $result->result_array;
	} // de la funcion
	

	public function getById($center_id) {
		$this->db->select ( 'conf_costcenters.center_id,
							conf_costcenters.center_name,
							conf_costcenters.center_deleted, 
							conf_costcenters.person_id, 
                        conf_costcenters.province_id,
                        conf_costcenters.center_consecutive');
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'center_id', $center_id );
		$result = $this->db->get ();
		$value = array();
		if ($result->result () != "") {
			foreach ( $result->result () as $row ) {
				$deleted = ucfirst($row->center_deleted);
				$value [] = array ('center_id' => $row->center_id, 
									'center_name' => $row->center_name,
									'center_deleted' => $deleted,
									'person_id' => $row->person_id,
                    'province_id' => $row->province_id,
                    'center_consecutive' => $row->center_consecutive);
            }
        }
        return $value;
    }

    public function getCenterById($center_id)
    {
        $result = $this->db->get_where(self::TABLE_NAME, array('center_id' => $center_id))->row();
        return $result;
    }

// de la funcion
	
	public function getNameById($center_id) {
		$this->db->select ( 'center_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'center_id', $center_id );
		$result = $this->db->get ();
		$center_name = '---';
		foreach ( $result->result () as $row ) {
			$center_name = $row->center_name;
		}
		return $center_name;
	
	}
	
	public function getDirectorById($center_id) {
		$this->db->select ( 'person_id' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'center_id', $center_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$person_id = $row->person_id;
		}
		return $person_id;
	
	}


}
?>
