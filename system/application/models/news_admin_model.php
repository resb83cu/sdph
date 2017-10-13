<?php

class News_admin_model extends Model {
	const TABLE_NAME = 'news';
	
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
	public function getData($to, $from) {
		$this->db->from ( self::TABLE_NAME );
		$this->db->limit ( $to, $from );
		$this->db->order_by( self::TABLE_NAME . '.dateput','desc' );
		$result = $this->db->get (); // 
		return $result->result_array ();
	} // de la funcion
	

	/**
	 * funcion que devuelve la cantidad de registros en la tabla ejemplo
	 *
	 */
	public function getCant() {
		return $this->db->count_all ( self::TABLE_NAME );
	}
	
	/**
	 * Esta es la funcion encargada de insertar las provincias
	 *
	 * @return boolean
	 */
	public function insert() {

		$new_id = $this->input->post ( 'new_id' );
		$date = new Dates();
		
		$news ['dateput'] = $date->now();// $this->input->post ( 'dateput' );
		$news ['name'] =  $this->input->post ( 'name' );

		
		$news ['content'] = $this->input->post ( 'content' );
		
         $news ['priority'] = $this->input->post ( 'priority' );
		
		
		if (empty ( $new_id )) {//insertar
		   
		 
		   
		    $this->db->trans_begin ();
			
			$re = $this->db->insert ( self::TABLE_NAME, $news );
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $news );
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
			$this->db->where ( 'new_id', $new_id );
			$this->db->trans_begin ();
			$re = $this->db->update ( self::TABLE_NAME, $news );
			$logs = new Logs ( );
			
			$mywhere = 'where new_id = '.$new_id;
			$myquery = $logs->sqlupdate(self::TABLE_NAME, $news, $mywhere);
			
			$logs->write( self::TABLE_NAME, 'UPDATE', $myquery);
			
			if ($this->db->trans_status () === FALSE) {
				$this->db->trans_rollback ();
			} else {
				$this->db->trans_commit ();
			}
			if ($re == true)
				return "true"; else
				return "false";
		
		return true;
		}
		
	}
	
	/**
	 * Funcion para eliminar un documento por su nombre
	 *
	 * @param string $
	 */
	public function delete($new_id) {
		
			$this->db->where ( 'new_id', $new_id );
		$this->db->trans_begin ();
		$this->db->delete ( self::TABLE_NAME );
		$logs = new Logs ( );
		$mywhere = 'where new_id = ' . $new_id;
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
	public function getByName($name) {
		$result = $this->db->get_where ( self::TABLE_NAME, array ('name' => $name ) );
		return $result->result_array ();
	
	}
	
	public function get() {
		$result = $this->db->get ( self::TABLE_NAME );
		return $result->result_array;
	} // de la funcion
	

	public function getById($new_id) {
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'new_id', $new_id );
		$result = $this->db->get ();
		return $result->result_array ();
	
	} // de la funcion
	
	public function getNameById($new_id) {
		$this->db->select ( 'name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'new_id', $new_id );
		$result = $this->db->get ();
		$name='';
		foreach ( $result->result () as $row ) {
			$name = $row->name;
		}
		return $name;
	
	}
	
	public function getContentById($new_id) {
		$this->db->select ( 'name' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'new_id', $new_id );
		$result = $this->db->get ();
		$content='';
		foreach ( $result->result () as $row ) {
			$content = $row->content;
		}
		return $content;
	
	}


}
?>