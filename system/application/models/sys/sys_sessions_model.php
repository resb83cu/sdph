<?php

class Sys_sessions_model extends Model {
	const TABLE_NAME = 'user_sessions';
	
	function __construct() {
		parent::__construct ();
	}
	
	/**
	 * Funcion que devuelve los valores de la tabla Logs 
	 *
	 * @param int $to
	 * @param int $from
	 * @return array
	 */
	public function getData($to, $from, $dateStart, $dateEnd) {
		$this->load->model ( 'person/person_persons_model' );
		$user_users_table = 'user_users';
		$query = 'user_sessions.session_id,
					user_users.user_name,
					user_sessions.session_ip,
					user_sessions.session_user_agent,
					user_sessions.session_begindate,
					user_sessions.session_enddate';
		$this->db->select ( $query );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( $user_users_table, $user_users_table. '.person_id = ' . self::TABLE_NAME . '.person_id', 'inner' );
		if (! empty ( $dateStart ) && ! empty ( $dateEnd )) {
			$this->db->where ( self::TABLE_NAME . '.session_begindate >=', $dateStart );
			$this->db->where ( self::TABLE_NAME . '.session_begindate <=', $dateEnd );
		}
		$this->db->limit ( $to, $from );
		$result = $this->db->order_by ('user_sessions.session_begindate', 'desc');
		$result = $this->db->get ();
		return $result->result_array ();
	}
	

	/**
	 * funcion que devuelve la cantidad de registros en la tabla
	 *
	 */
	public function getCant($dateStart, $dateEnd) {
		$this->load->model ( 'person/person_persons_model' );
		$user_users_table = 'user_users';
		$query = 'user_sessions.session_id,
					user_users.user_name,
					user_sessions.session_ip,
					user_sessions.session_user_agent,
					user_sessions.session_begindate,
					user_sessions.session_enddate';
		$this->db->select ( $query );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( $user_users_table, $user_users_table. '.person_id = ' . self::TABLE_NAME . '.person_id', 'inner' );
		if (! empty ( $dateStart ) && ! empty ( $dateEnd )) {
			$this->db->where ( self::TABLE_NAME . '.session_begindate >=', $dateStart );
			$this->db->where ( self::TABLE_NAME . '.session_begindate <=', $dateEnd );
		}
		return $this->db->count_all_results();
	}


}
?>
