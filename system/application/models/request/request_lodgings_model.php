<?php
class Request_lodgings_model extends Model {
	const TABLE_NAME = 'request_lodgings';
	
	function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Funcion que devuelve los valores de la tabla lodging_request
	 *
	 * @param int $hasta
	 * @param int $desde
	 * @return array
	 */

	public function getData($to, $from, $dateStart, $dateEnd, $center, $province, $motive, $hotel) {
		$centinela = new Centinela();
		$roll_id = $centinela->get_roll_id();
		$this->load->model ( 'person/person_persons_model' );
		$this->load->model ( 'conf/conf_hotels_model' );
		$conf_costcenters_table = 'conf_costcenters';
		$request_lodgings_table = 'request_lodgings';
		$request_requests_table = 'request_requests';
		$lodging_edit_table = 'lodging_edit';
		$this->db->select ( $request_lodgings_table . '.request_id, ' . 
							$request_requests_table . '.request_date, ' . 
							$request_lodgings_table . '.lodging_entrancedate, ' . 
							$request_lodgings_table . '.lodging_exitdate, ' . 
							$request_lodgings_table . '.lodging_canceled, ' . 
							$conf_costcenters_table . '.center_name, ' . 
							$request_requests_table . '.person_idworker, ' .
							$request_requests_table . '.request_details, ' .
							$lodging_edit_table . '.hotel_id, ' . 
							$lodging_edit_table . '.letter_id, ' .
							$lodging_edit_table . '.lodging_noshow, ' .
							$lodging_edit_table . '.lodging_voucher' );
		$this->db->from ( $request_requests_table );
		$this->db->join ( $conf_costcenters_table, $conf_costcenters_table . '.center_id = ' . $request_requests_table . '.center_id', 'inner' );
		$this->db->join ( $request_lodgings_table, $request_lodgings_table . '.request_id = ' . $request_requests_table . '.request_id', 'inner' );
		$this->db->join ( $lodging_edit_table, $lodging_edit_table . '.request_id = ' . $request_lodgings_table . '.request_id', 'left' );
		if (! empty ( $dateStart ) && ! empty ( $dateEnd )) {
			$this->db->where ( $request_lodgings_table . '.lodging_entrancedate >=', $dateStart );
			$this->db->where ( $request_lodgings_table . '.lodging_entrancedate <=', $dateEnd );
		}
		if (! empty ( $center )) {
			$this->db->where ( $request_requests_table . '.center_id =', $center );
		}
		if (! empty ( $motive )) {
			$this->db->where ( $request_requests_table . '.motive_id =', $motive );
		}
		if (! empty ( $hotel )) {
			$this->db->where ( $lodging_edit_table . '.hotel_id =', $hotel );
		}
		if ( $roll_id < 5 ) {
			$this->db->where ( $request_lodgings_table . '.province_idlodging =', $centinela->get_province_id() );
		} else {
			$this->db->where ( $request_lodgings_table . '.province_idlodging =', $province );
		}
		$this->db->limit ( $to, $from );
		$this->db->order_by("lodging_entrancedate", "asc");
		$this->db->order_by("request_id", "asc");
		$result = $this->db->get ();
		$value = array ( );
		$date = new Dates();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$person_worker = Person_persons_model::getNameById ( $row->person_idworker );
				if (! empty ( $row->letter_id)) {
					$letter = $row->letter_id;
				} else {
					$letter = '---';
				}
				if (! empty ( $row->hotel_id )) {
					$hotel_name = Conf_hotels_model::getNameById ( $row->hotel_id );
					if ($row->lodging_noshow != 'on') {
						$state = 'OK';
					} else {
						$state = 'NO SHOW';
					}
				} else {
					$hotel_name = '---';
					$fecha = getdate ();					
					$endDate = date_parse($row->lodging_entrancedate);
					$fullDays = $date->dateDiffNow($fecha, $endDate);
					if ($fullDays >= 3) {
						$state = 'Faltan '.$fullDays.' dias';
					} else if ($fullDays < 0) {
						$state = 'SIN EFECTO';
					} else {
						$state = 'ATRASADA';
					}
				}
				if ($row->lodging_voucher == 'si') {
					$voucher = 'SI';
				} else {
					$voucher = 'NO';
				}
				if ($row->lodging_canceled != 0) {
					$state = 'CANCELADA';
				}
				$value [] = array ('request_id' => $row->request_id, 
									'request_date' => $row->request_date, 
									'lodging_entrancedate' => $row->lodging_entrancedate, 
									'lodging_exitdate' => $row->lodging_exitdate, 
									'center_name' => $row->center_name, 
									'request_details' => $row->request_details,
									'person_worker' => $person_worker, 
									'state' => $state, 
									'hotel_name' => $hotel_name,
									'letter' => $letter,
									'voucher' => $voucher );
			}
		}
		$cant = $this->getDataCount($dateStart, $dateEnd, $center, $province, $motive, $hotel);
		echo ( "{count : " . $cant . ", data : " . json_encode( $value ) . "}" );
	}
		/**
	 * funcion que devuelve la cantidad de registros en la tabla ejemplo
	 *
	 */
	public function getDataCount($dateStart, $dateEnd, $center, $province, $motive, $hotel) {
		$centinela = new Centinela();
		$roll_id = $centinela->get_roll_id();
		$this->load->model ( 'person/person_persons_model' );
		$this->load->model ( 'conf/conf_hotels_model' );
		$conf_costcenters_table = 'conf_costcenters';
		$request_lodgings_table = 'request_lodgings';
		$request_requests_table = 'request_requests';
		$lodging_edit_table = 'lodging_edit';
		$this->db->select ( $request_lodgings_table . '.request_id, ' . 
							$request_requests_table . '.request_date, ' . 
							$request_lodgings_table . '.lodging_entrancedate, ' . 
							$request_lodgings_table . '.lodging_exitdate, ' . 
							$conf_costcenters_table . '.center_name, ' . 
							$request_requests_table . '.person_idworker, ' . 
							$lodging_edit_table . '.hotel_id, ' . 
							$lodging_edit_table . '.letter_id' );
		$this->db->from ( $request_requests_table );
		$this->db->join ( $conf_costcenters_table, $conf_costcenters_table . '.center_id = ' . $request_requests_table . '.center_id', 'inner' );
		$this->db->join ( $request_lodgings_table, $request_lodgings_table . '.request_id = ' . $request_requests_table . '.request_id', 'inner' );
		$this->db->join ( $lodging_edit_table, $lodging_edit_table . '.request_id = ' . $request_lodgings_table . '.request_id', 'left' );
		if (! empty ( $dateStart ) && ! empty ( $dateEnd )) {
			$this->db->where ( $request_lodgings_table . '.lodging_entrancedate >=', $dateStart );
			$this->db->where ( $request_lodgings_table . '.lodging_entrancedate <=', $dateEnd );
		}
		if (! empty ( $center )) {
			$this->db->where ( $request_requests_table . '.center_id =', $center );
		}
		if (! empty ( $motive )) {
			$this->db->where ( $request_requests_table . '.motive_id =', $motive );
		}
		if (! empty ( $hotel )) {
			$this->db->where ( $lodging_edit_table . '.hotel_id =', $hotel );
		}
		if ( $roll_id < 5 ) {
			$this->db->where ( $request_lodgings_table . '.province_idlodging =', $centinela->get_province_id() );
		} else {
			$this->db->where ( $request_lodgings_table . '.province_idlodging =', $province );
		}
		return $this->db->count_all_results ();
	}
	
	/**
	 * Esta es la funcion encargada de insertar los tickets
	 *
	 * @return boolean
	 */

	public function insert($request_id, $lodging_entrancedate, $lodging_exitdate, $transport_idlodging, $transport_idreturnlodging, $province_idlodging, $lodging_requestreinforceddiet, $lodging_requestelongationdiet, $lodging_prorogate) {
		$lodging ['request_id'] = $request_id;
		$lodging ['lodging_entrancedate'] = $lodging_entrancedate;
		$lodging ['lodging_exitdate'] = $lodging_exitdate;
		$lodging ['transport_idlodging'] = $transport_idlodging;
		$lodging ['transport_idreturnlodging'] = $transport_idreturnlodging;
		$lodging ['province_idlodging'] = $province_idlodging;
		$lodging ['lodging_requestreinforceddiet'] = $lodging_requestreinforceddiet;
		$lodging ['lodging_requestelongationdiet'] = $lodging_requestelongationdiet;
		if (!empty($lodging_prorogate)) {
			$lodging ['lodging_prorogate'] = $lodging_prorogate;
		} else {
			$lodging ['lodging_prorogate'] = false;
		}
		$flag = self::getCountById($request_id);
		if ($flag > 0) {
			$this->db->where ( 'request_id', $request_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $lodging );
			$logs = new Logs ( );
			
			$mywhere = 'where request_id = '.$request_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $lodging, $mywhere);
			
			$logs->write( self::TABLE_NAME, 'UPDATE', $myquery);
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
			if ($re == true) {
				return "true";
			} else
				return "false";
		} else {
		    $this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $lodging );
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $lodging );
			$logs->write ( self::TABLE_NAME, 'INSERT', $myquery );
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}			
			if ($re == true) {
				return "true";
			} else
				return "false";
		}

	}

	public function updateState($request_id, $state) {
		$data = array ('lodging_state' => $state );
		$this->db->where ( 'request_id', $request_id );
		$this->db->trans_begin ();
		$re = $this->db->update ( self::TABLE_NAME, $data );
		$logs = new Logs ( );
			
			$mywhere = 'where request_id = '.$request_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $data, $mywhere);
			
			$logs->write( self::TABLE_NAME, 'UPDATE', $myquery);
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
		if ($re == true)
			return "true"; else
			return "false";
	}
	
	public function updateDate($request_id, $lodging_entrancedate, $lodging_exitdate) {
		$lodging ['lodging_entrancedate'] = $lodging_entrancedate;
		$lodging ['lodging_exitdate'] = $lodging_exitdate;
		$this->db->where ( 'request_id', $request_id );
		$this->db->trans_begin ();
		$re = $this->db->update ( self::TABLE_NAME, $lodging );
		$logs = new Logs ( );
			
			$mywhere = 'where request_id = '.$request_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $lodging, $mywhere);
			
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
	
	public function canceledState($request_id, $state) {
		$data = array ('lodging_canceled' => $state );
		$this->db->where ( 'request_id', $request_id );
		$this->db->trans_begin ();
		$re = $this->db->update ( self::TABLE_NAME, $data );
		$logs = new Logs ( );
			
			$mywhere = 'where request_id = '.$request_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $data, $mywhere);
			
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
	
	/**
	 * Funcion para eliminar un request_lodging por su id
	 *
	 * @param string $request_id
	 */
	public function delete($request_id) {
		$this->db->where ( 'request_id', $request_id );
		$this->db->trans_begin ();
		$this->db->delete ( self::TABLE_NAME );
		$logs = new Logs ( );
		$mywhere = 'where request_id = ' . $request_id;
		$myquery = $logs->sqldelete ( self::TABLE_NAME, $mywhere );
		$logs->write ( self::TABLE_NAME, 'DELETE', $myquery );
		
		if ($this->db->trans_status () === FALSE) {
			$this->db->trans_rollback ();
		} else {
			$this->db->trans_commit ();
		}
	}
	
	public function getCountById($request_id) {
		$this->db->select ( 'request_id' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'request_id', $request_id );
		return $this->db->count_all_results ();
	}

}
?>