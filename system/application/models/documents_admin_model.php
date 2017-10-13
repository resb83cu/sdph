<?php

class Documents_admin_model extends Model {
	const TABLE_NAME = 'documents';
	
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
		$result = $this->db->get (); // 
		return $result->result_array ();
	}
	

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

		$document_id = $this->input->post ( 'document_id' );
		$date = new Dates();
		
		$documents ['dateput'] = $date->now();
		
		
		$documents ['name'] =  $this->input->post ( 'name' );

		
		$documents ['pathname'/*nombre del componente*/] = $_FILES['pathname']['name'];//de ping!!!!!!!!!!!!!!!!!! descubrir esto merece $100000
		
		
      
		
		
		if (empty ( $document_id )) {//insertar
		   
		   $tipo_archivo = $_FILES['pathname']['type'];
           $tamano_archivo = $_FILES['pathname']['size']; 
		
		if ($tamano_archivo <=4116480/*2MB*/ && $tamano_archivo !=0 /*archivos mas garndes que lso 
aceptados*/&& (strpos($documents ['pathname'] , "doc") || strpos($documents ['pathname'] , "pdf") || 
strpos($documents ['pathname'] ,"ods") ||  strpos($documents 
['pathname'] ,"rar")||  strpos($documents ['pathname'] 
,"zip")||  strpos($documents ['pathname'] ,"ppt")||  strpos($documents ['pathname'] ,"odt")||  strpos($documents 
['pathname'] ,"odp")||  strpos($documents ['pathname'] ,"tar") ||  strpos($documents ['pathname'] ,"tar.gz") ||  
strpos($documents ['pathname'] ,"xls") ||  strpos($documents ['pathname'] ,"jpg") ||  strpos($documents 
['pathname'] ,"gif") ||  strpos($documents ['pathname'] ,"png")))
{
		move_uploaded_file($_FILES['pathname']['tmp_name'], './system/application/docs/'.$documents ['pathname']);
		   
		   
		   $this->db->trans_begin ();
			
			$re = $this->db->insert ( self::TABLE_NAME, $documents );
			$logs = new Logs ( );
			$myquery = $logs->sqlinsert ( self::TABLE_NAME, $documents );
			$logs->write ( self::TABLE_NAME, 'INSERT', $myquery );
			
		if ($this->db->trans_status () === FALSE) {
			$this->db->trans_rollback ();
		} else {
				$this->db->trans_commit ();
			}
			if ($re == true)
				return "true"; else
				return "false";
		 
		 return 'true';
		 
		 }else {return "false";}//condiciones del fichero a subir no aceptadas
		
		}
		
	}
	
	/**
	 * Funcion para eliminar un documento por su nombre
	 *
	 * @param string $
	 */
	public function delete($document_id) {
		
		//hago uan consulta para ver el nombre del documento
		$pathname ="";
		$res=$this->db->query('select * from documents where document_id='.$document_id);
		if ($res->result () != null) {
		   foreach ( $res->result () as $row ){  //esta en esta tabla
						$pathname = $row->pathname;
		 }
        }	
		
		
		
		//borro el documento en la carpeta que se especifica:
		unlink('./system/application/docs/'.$pathname);	
		
		
		
		
		
		
		$this->db->where ( 'document_id', $document_id );
		$this->db->trans_begin ();
		$this->db->delete ( self::TABLE_NAME );
		$logs = new Logs ( );
		$mywhere = 'where document_id = ' . $document_id;
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
	public function getByName($pathname) {
		$result = $this->db->get_where ( self::TABLE_NAME, array ('pathname' => $pathname ) );
		return $result->result_array ();
	
	}
	
	public function get() {
		$result = $this->db->get ( self::TABLE_NAME );
		return $result->result_array;
	} // de la funcion
	

	public function getById($document_id) {
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'document_id', $document_id );
		$result = $this->db->get ();
		return $result->result_array ();
	
	} // de la funcion
	
	public function getNameById($document_id) {
		$this->db->select ( 'pathname' );
		$this->db->from ( self::TABLE_NAME );
		$this->db->where ( 'document_id', $document_id );
		$result = $this->db->get ();
		foreach ( $result->result () as $row ) {
			$pathname = $row->pathname;
		}
		return $pathname;
	
	}


}
?>
