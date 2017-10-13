<?php

class Sys_logs_model extends Model {
	const TABLE_NAME = 'logs';
	
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
		$query = 'logs.id,
					user_users.user_name,
					logs.date,
					logs.tablename,
					logs.operation,
					logs.query';
		$this->db->select ( $query );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( $user_users_table, $user_users_table. '.person_id = ' . self::TABLE_NAME . '.user_id', 'inner' );
		if (! empty ( $dateStart ) && ! empty ( $dateEnd )) {
			$this->db->where ( self::TABLE_NAME . '.date >=', $dateStart );
			$this->db->where ( self::TABLE_NAME . '.date <=', $dateEnd );
		}
		$this->db->order_by("date", "desc"); 
		$this->db->limit ( $to, $from );
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
		$query = 'logs.id,
					user_users.user_name,
					logs.date,
					logs.tablename,
					logs.operation,
					logs.query';
		$this->db->select ( $query );
		$this->db->from ( self::TABLE_NAME );
		$this->db->join ( $user_users_table, $user_users_table. '.person_id = ' . self::TABLE_NAME . '.user_id', 'inner' );
		if (! empty ( $dateStart ) && ! empty ( $dateEnd )) {
			$this->db->where ( self::TABLE_NAME . '.date >=', $dateStart );
			$this->db->where ( self::TABLE_NAME . '.date <=', $dateEnd );
		}
		return $this->db->count_all_results();
	}


}
?>
