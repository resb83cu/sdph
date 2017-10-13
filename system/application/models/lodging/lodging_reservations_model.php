<?php
class Lodging_reservations_model extends Model {
	const TABLE_NAME = 'lodging_reservations';
	
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
	
	/*	 
La funcion getdata coge los datos de  lodging_requests para mostrar por ejemplo en el grid, para ello se cargan los model de las tablas de campos foraneos, luego el select coge todo lo 
 de ticket_request mas los campos descriptivos de las de las tablas; fijarse bien en los join; luego en el foreach es para devolver en el arreglo los campos que se muestran, ej curioso nombre+lastname+secondlastname que se concatena en una variable	 para devolver el nombre completo de person_xxx en vez de person_id.
	 
		 */
	public function getData($to, $from) {
		$this->load->model ( 'person/person_persons_model' );
		$centinela = new Centinela();
		$roll_id = $centinela->get_roll_id();
		$this->load->model ( 'conf/conf_hotels_model' );
		$this->db->select ( 'reservation_id,
							reservation_number,
							reservation_rooms,
							reservation_persons,
							reservation_requestdate,
							reservation_begindate,
							reservation_enddate,
							lodging_reservations.person_id,
							conf_hotels.hotel_name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( Conf_hotels_model::TABLE_NAME, Conf_hotels_model::TABLE_NAME . '.hotel_id = ' . self::TABLE_NAME . '.hotel_id', 'inner' );
		if ( $roll_id < 5 ) {
			$this->db->where ( self::TABLE_NAME . '.person_id =', $centinela->get_person_id() );
		}
		$this->db->limit ( $to, $from );
		$result = $this->db->get ();
		$value = array ( );
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$temp = $row->person_id;
				$person_name = Person_persons_model::getNameById ( $temp ); 
				$value [] = array ('reservation_id' => $row->reservation_id, 
									'reservation_number' => $row->reservation_number, 
									'reservation_rooms' => $row->reservation_rooms, 
									'reservation_persons' => $row->reservation_persons, 
									'reservation_requestdate' => $row->reservation_requestdate, 
									'reservation_begindate' => $row->reservation_begindate, 
									'reservation_enddate' => $row->reservation_enddate, 
									'person_fullname' => $person_name, 
									'hotel_name' => $row->hotel_name );
			}
		}
		return $value;
	}
	/**
	 * funcion que devuelve la cantidad de registros en la tabla ejemplo
	 *
	 */
	public function getCant() {
		return $this->db->count_all ( self::TABLE_NAME );
	}
	
	/**
	 * Esta es la funcion encargada de insertar los tickets
	 *
	 * @return boolean
	 */
	
	public function insert() {
		//los datos del post son los del nombres de los componentes del formulario ojo he.. se coge del name o hidenname (no del id)
		$reservation_id = $this->input->post ( 'reservation_id' );
		
		$fecha = new Dates();
		$request ['reservation_requestdate'] = $fecha->now();
		
		$request ['reservation_number'] = $this->input->post ( 'reservation_number' );
		$request ['reservation_rooms'] = $this->input->post ( 'reservation_rooms' );
		$request ['reservation_persons'] = $this->input->post ( 'reservation_persons' );
		
		$request ['reservation_begindate'] = $this->input->post ( 'reservation_begindate' );
		
		if ($this->input->post ( 'reservation_enddate' ) != null)
			$request ['reservation_enddate'] = $this->input->post ( 'reservation_enddate' ); else
			$request ['reservation_enddate'] = null;
		
		$centinela = new Centinela ( );
		$request ['person_id'] = $centinela->get_person_id ();
		
		$request ['hotel_id'] = $this->input->post ( 'hotel_id' );
		
		if (empty ( $reservation_id )) {
			$this->db->trans_begin ();
			$re = $this->db->insert ( self::TABLE_NAME, $request );
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $request );
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
			$this->db->where ( 'reservation_id', $reservation_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $request );
			$logs = new Logs ( );
			
			$mywhere = 'where reservation_id = '.$reservation_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $request, $mywhere);
			
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
	}
	/**
	 * Funcion para eliminar un lodging_request por su id
	 *
	 * @param string $request_id
	 */
	public function delete($reservation_id) {
		$this->db->where ( 'reservation_id', $reservation_id );
		$this->db->trans_begin ();
        $this->db->delete ( self::TABLE_NAME );
		$logs = new Logs ( );
		$mywhere = 'where reservation_id = ' . $reservation_id;
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
		return $result->result_array;
	} // de la funcion
	

	public function getById($reservation_id) {
	$this->load->model ( 'conf/conf_hotels_model' );
		$this->db->select ( 'reservation_id,
							reservation_number,
							reservation_rooms,
							reservation_persons,
							reservation_requestdate,
							reservation_begindate,
							reservation_enddate,
							lodging_reservations.person_id,
							lodging_reservations.hotel_id' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'reservation_id', $reservation_id );
		$result = $this->db->get ();
		
		$value = array ( );
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {			
			    $province_id =  Conf_hotels_model::getProvinceIdById($row->hotel_id);
				$value [] = array ('reservation_id' => $row->reservation_id, 
									'reservation_number' => $row->reservation_number, 
									'reservation_rooms' => $row->reservation_rooms, 
									'reservation_persons' => $row->reservation_persons, 
									'reservation_requestdate' => $row->reservation_requestdate, 
									'reservation_begindate' => $row->reservation_begindate, 
									'reservation_enddate' => $row->reservation_enddate, 
									'person_id' => $row->person_id, 
									'hotel_id' => $row->hotel_id ,
									'province_idlodging'=>$province_id);
			
			}
		
		}
		
		return $value;
	}
	//ahora para reporte, pude haberlo hecho en el mismo getData pero para separar  y mejor entendimiento lo hago aparte
	

	public function getDataConditional($to, $from, $datosForWheres/*que es un arreglo*/ ,$isPdf) {
		$this->load->model ( 'person/person_persons_model' );
		//$this->load->model ( 'person/person_workers_model' ); //no me hace falta pues esta tabla es como herencia de person_persons, solo si devuelvo algun campo propio de person_worker
		$this->load->model ( 'conf/conf_hotels_model' );
		
		$this->db->select ( 'reservation_id,reservation_number,reservation_rooms,reservation_persons,reservation_requestdate,reservation_begindate,reservation_enddate,lodging_reservations.person_id,conf_hotels.hotel_name' );
		
		$this->db->from ( self::TABLE_NAME );
		
		$this->db->join ( Conf_hotels_model::TABLE_NAME, Conf_hotels_model::TABLE_NAME . '.hotel_id = ' . self::TABLE_NAME . '.hotel_id', 'inner' );
		
		//los join solo para campos que no se repiten la llave extarnjera con otra tabla, sino da error
		

		//ahora los where 
		

		if (! empty ( $datosForWheres ['person_id'] ))
			$this->db->where ( self::TABLE_NAME . '.person_id ', $datosForWheres ['person_id'] );
		
		if (! empty ( $datosForWheres ['hotel_id'] ))
			$this->db->where ( self::TABLE_NAME . '.hotel_id ', $datosForWheres ['hotel_id'] );
		
		if (! empty ( $datosForWheres ['reservation_begindate'] ))
			$this->db->where ( self::TABLE_NAME . '.reservation_begindate ', $datosForWheres ['reservation_begindate'] );
		
		if (! empty ( $datosForWheres ['reservation_enddate'] ))
			$this->db->where ( self::TABLE_NAME . '.reservation_enddate ', $datosForWheres ['reservation_enddate'] );
		
		if (! empty ( $datosForWheres ['reservation_number'] ))
			$this->db->where ( self::TABLE_NAME . '.reservation_number ', $datosForWheres ['reservation_number'] );
		
		$cant = $this->db->count_all_results (); //para coger la cantidad real de filas de la consulta filtrada sin from-limit
		

		//de nuevo la consulta, pues 2 veces no se puede llamar al get, se funde
		

		$this->db->select ( 'reservation_id,reservation_number,reservation_rooms,reservation_persons,reservation_requestdate,reservation_begindate,reservation_enddate,lodging_reservations.person_id,conf_hotels.hotel_name' );
		
		$this->db->from ( self::TABLE_NAME );
		
		$this->db->join ( Conf_hotels_model::TABLE_NAME, Conf_hotels_model::TABLE_NAME . '.hotel_id = ' . self::TABLE_NAME . '.hotel_id', 'inner' );
		
		//ahora los where 
		

		if (! empty ( $datosForWheres ['person_id'] ))
			$this->db->where ( self::TABLE_NAME . '.person_id ', $datosForWheres ['person_id'] );
		
		if (! empty ( $datosForWheres ['hotel_id'] ))
			$this->db->where ( self::TABLE_NAME . '.hotel_id ', $datosForWheres ['hotel_id'] );
		
		if (! empty ( $datosForWheres ['reservation_begindate'] ))
			$this->db->where ( self::TABLE_NAME . '.reservation_begindate ', $datosForWheres ['reservation_begindate'] );
		
		if (! empty ( $datosForWheres ['reservation_enddate'] ))
			$this->db->where ( self::TABLE_NAME . '.reservation_enddate ', $datosForWheres ['reservation_enddate'] );
		
		if (! empty ( $datosForWheres ['reservation_number'] ))
			$this->db->where ( self::TABLE_NAME . '.reservation_number ', $datosForWheres ['reservation_number'] );
		
		$this->db->limit ( $to, $from );
		
		$result = $this->db->get ();
		$value = array ( ); //unset($value); //para refresacar, creo no es obligado 
		if ($result->result () != null) {
			foreach ( $result->result () as $row ) {
				$temp = $row->person_id;
				$person_name = Person_persons_model::getNameById ( $temp );
				$value [] = array ('reservation_id' => $row->reservation_id, 'reservation_number' => $row->reservation_number, 'reservation_rooms' => $row->reservation_rooms, 'reservation_persons' => $row->reservation_persons, 'reservation_requestdate' => $row->reservation_requestdate, 'reservation_begindate' => $row->reservation_begindate, 'reservation_enddate' => $row->reservation_enddate, 'person_fullname' => $person_name, 'hotel_name' => $row->hotel_name );
			}
		}
		
		if ($isPdf == 'si') { //devuelve todos por exceso
			return $value;
		} else { //el cant es filtrado
			echo ("{count : " . $cant . ", data : " . json_encode ( $value ) . "}"); // este valor se le asigna al return del setDatagridconditional para pasarle
		}
	
	}

}
?>
