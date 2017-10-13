<?php

class Ticket_editship_model extends Model {
	const TABLE_NAME = 'ticket_editship';
	
	function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Funcion que devuelve los valores de la tabla ticket_editship
	 *
	 * @param int $hasta
	 * @param int $desde
	 * @return array
	 */
	public function getData($to, $from, $dateStart, $dateEnd, $motive) {
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->load->model ( 'person/person_persons_model' );
		$this->load->model ( 'request/request_requests_model' );
		$this->load->model ( 'conf/conf_costcenters_model' );
		$this->load->model ( 'conf/conf_motives_model' );
		$this->load->model ( 'conf/conf_tickettransports_model' );
		$this->load->model ( 'conf/conf_ticketviazulstates_model' );
		$this->load->model ( 'conf/conf_ticketrequeststates_model' );
		$this->db->select ( 'ticket_requests.request_id, 
							ticket_requests.request_date, 
							ticket_requests.request_exitdate, 
							ticket_requests.person_idrequestedby, 
							ticket_requests.person_idworker,
							ticket_requests.province_idfrom,
							ticket_requests.province_idto,
							ticket_editship.state_id' );
		$this->db->from ( Ticket_requests_model::TABLE_NAME );
		$this->db->join ( self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . Request_tickets_model::TABLE_NAME . '.request_id', 'left' );
		$this->db->where( Ticket_requests_model::TABLE_NAME.'.request_exitdate >=', $dateStart);
		$this->db->where( Ticket_requests_model::TABLE_NAME.'.request_exitdate <=', $dateEnd);
		$this->db->where( Ticket_requests_model::TABLE_NAME.'.transport_id', 3);
		if (!empty($motive)) {
			$this->db->where( Ticket_requests_model::TABLE_NAME.'.motive_id', $motive);
		}
		$this->db->limit ( $to, $from );
		$this->db->distinct ();
		$result = $this->db->get ();
		$cant = 0;
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$person_namerequestedby = Person_persons_model::getNameById ( $row->person_idrequestedby );
				$person_nameworker = Person_persons_model::getNameById ( $row->person_idworker );
				$province_namefrom = Conf_provinces_model::getNameById($row->province_idfrom);
				$province_nameto = Conf_provinces_model::getNameById($row->province_idto);
				$state_id = $row->state_id;
				$state = Conf_ticketviazulstates_model::getNameById($state_id);
				
				$value [] = array ('request_id' => $row->request_id, 
								'request_date' => $row->request_date, 
								'request_exitdate' => $row->request_exitdate, 
								'person_namerequestedby' => $person_namerequestedby, 
								'person_nameworker' => $person_nameworker, 
								'province_namefrom' => $province_namefrom, 
								'province_nameto' => $province_nameto,
								'state' => $state);
				$cant ++;
				
			}
		
		} else {
			$value = array();
		}
		echo ( "{count : " . $cant . ", data : " . json_encode ( $value ) . "}" );
		//return $value;
	}
	
	/**
	 * funcion que devuelve la cantidad de registros en la tabla ejemplo
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
	public function insert() {
		$request_id = $this->input->post('request_id');
		$mco = $this->input->post('ship_mco');
		$cheque = $this->input->post('ship_cheque');
		$exithour = $this->input->post('ship_exithour');
		$arrivalhour = $this->input->post('ship_arrivalhour');
		$price = $this->input->post('ship_price');
		$ticket_number = $this->input->post('ship_ticketnumber');
		$ticket_date = $this->input->post('ticket_date');
		$ship ['request_id'] = $request_id;
		$ship ['person_ideditedby'] = $this->session->userdata('person_id');
		$ship ['province_idfrom'] = $this->input->post('province_idfrom');
		$ship ['province_idto'] = $this->input->post('province_idto');
		$ship ['ticket_date'] = $ticket_date;
		if (empty($mco)) {
			$ship ['ship_mco'] = null;
		}else {
			$ship ['ship_mco'] = $mco;
		}
		if (empty($cheque)) {
			$ship ['ship_cheque'] = null;
		}else {
			$ship ['ship_cheque'] = $cheque;
		}
		if (empty($exithour)) {
			$ship ['ship_exithour'] = null;
		}else {
			$ship ['ship_exithour'] = $exithour;
		}
		if (empty($arrivalhour)) {
			$ship ['ship_arrivalhour'] = null;
		}else {
			$ship ['ship_arrivalhour'] = $arrivalhour;
		}
		if (empty($price)) {
			$ship ['ship_price'] = null;
		}else {
			$ship ['ship_price'] = $price;
		}
		if (empty($ticket_number)) {
			$ship ['ship_ticketnumber'] = null;
		}else {
			$ship ['ship_ticketnumber'] = $ticket_number;
		}
		$ship ['state_id'] = $this->input->post('state_id');
		$flag = self::getCountById($request_id, $ticket_date);
		if ($flag > 0) {
			$this->db->where ( 'request_id', $request_id );
			$this->db->where ( 'ticket_date', $ticket_date );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $ship );
			$logs = new Logs ( );
			
			$mywhere = 'where request_id = '.$request_id .'and ticket_date '.$ticket_date;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $ship, $mywhere);
			
			$logs->write( self::TABLE_NAME, 'UPDATE', $myquery);
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
            if ($re == true) {
                if ($ship ['state_id'] == 3) {
                    Request_tickets_model::updateTicketCancel($request_id, $ticket_date, 1);
                } else {
                    Request_tickets_model::updateTicketCancel($request_id, $ticket_date, 0);
                }
                return "true";
            }
            else
                return "false";
		
		} else {
		     $this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $ship );
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $ship );
			$logs->write ( self::TABLE_NAME, 'INSERT', $myquery );
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
			$this->load->model ( 'request/request_tickets_model' );
			Request_tickets_model::updateState($request_id, $ticket_date, 1);
            if ($re == true) {
                if ($ship ['state_id'] == 3) {
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
	
	/**
	 * Funcion para eliminar una provincia por su nombre
	 *
	 * @param string $requestservice_name
	 */
	public function delete($request_id, $ticket_date) {
		$this->db->where ( 'request_id', $request_id );
		$this->db->where ( 'ticket_date', $ticket_date );
		$this->db->trans_begin ();
		$this->db->delete ( self::TABLE_NAME );
		$logs = new Logs ( );
		$mywhere = 'where request_id = '.$request_id .'and ticket_date '.$ticket_date;
		$myquery = $logs->sqldelete ( self::TABLE_NAME, $mywhere );
		$logs->write ( self::TABLE_NAME, 'DELETE', $myquery );
		
		if ($this->db->trans_status () === FALSE) {
			$this->db->trans_rollback ();
		} else {
			$this->db->trans_commit ();
		}
	}
	
	public function get() {
		$result = $this->db->get ( self::TABLE_NAME );
		return $result->result_array ();
	} // de la funcion
	

	public function getById($request_id, $ticket_date) {
		$this->load->model ( 'conf/conf_provinces_model' );
		$this->load->model ( 'person/person_persons_model' );
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
			$editship = ',ticket_editship.ship_mco,
							ticket_editship.ship_cheque,
	        				ticket_editship.ship_exithour,
							ticket_editship.ship_arrivalhour,
							ticket_editship.ship_price,
							ticket_editship.ship_ticketnumber,
							ticket_editship.state_id';
			$query = $query . $editship;
		}
		$this->db->select ($query);
		$this->db->from ( $request_requests_table );
		$this->db->join ( $conf_costcenters_table, $conf_costcenters_table . '.center_id = ' . $request_requests_table . '.center_id', 'inner' );
		$this->db->join ( $conf_motives_table, $conf_motives_table . '.motive_id = ' . $request_requests_table . '.motive_id', 'inner' );
		$this->db->join ( $request_tickets_table, $request_tickets_table . '.request_id = ' . $request_requests_table . '.request_id', 'inner' );
		$this->db->join ( $conf_tickettransports_table, $conf_tickettransports_table . '.transport_id = ' . $request_tickets_table . '.transport_id', 'inner' );
		if ($count > 0) {
			$this->db->join ( self::TABLE_NAME, self::TABLE_NAME . '.request_id = ' . $request_tickets_table . '.request_id AND '.self::TABLE_NAME . '.ticket_date = ' .$request_tickets_table . '.ticket_date' );
		}
		$this->db->where ( $request_tickets_table . '.request_id', $request_id );
		$this->db->where ( $request_tickets_table . '.ticket_date', $ticket_date );

		$result = $this->db->get ();
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$person_namerequestedby = Person_persons_model::getNameById ( $row->person_idrequestedby );
				$person_namelicensedby = Person_persons_model::getNameById ( $row->person_idlicensedby );
				$person_nameworker = Person_persons_model::getNameById ( $row->person_idworker );
				$person_identity = Person_persons_model::getIdentityById ( $row->person_idworker );
				$person_province = Person_persons_model::getProvinceById ( $row->person_idworker );
				$province_namefrom = Conf_provinces_model::getNameById ( $row->province_idfrom);
				$province_nameto = Conf_provinces_model::getNameById ( $row->province_idto);
				if ($count > 0) {
					if (is_null($row->ship_exithour)) {
						$ship_exithour = $row->ship_exithour;
					} else {
						$ship_exithour = substr ( $row->ship_exithour, 0, 5 );
					}
					if (is_null($row->ship_arrivalhour)) {
						$ship_arrivalhour = $row->ship_arrivalhour;
					} else {
						$ship_arrivalhour = substr ( $row->ship_arrivalhour, 0, 5 );
					}
					
					$value [] = array ('request_id' => $row->request_id, 
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
									'ship_mco' => $row->ship_mco,
									'ship_cheque' => $row->ship_cheque,
									'ship_exithour' => $ship_exithour,
									'ship_arrivalhour' => $ship_arrivalhour,
									'ship_price' => $row->ship_price,
									'ship_ticketnumber' => $row->ship_ticketnumber,
									'state_id' => $row->state_id);
				}else {
					$value [] = array ('request_id' => $row->request_id, 
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
		
		}
		else {
			$value = array();
		} 		
		return $value;
		
	}
	
	public function getCountById($request_id, $ticket_date) {
		$this->db->select ( 'request_id' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'request_id', $request_id );
		$this->db->where ( 'ticket_date', $ticket_date );
		return $this->db->count_all_results ();
	}

}
?>
