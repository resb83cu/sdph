<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

	 

	/**

	* Session class using native PHP session features and hardened against session fixation.

	*

	* @package     CodeIgniter

	* @subpackage  Libraries

	* @category    Sessions

	* @author      Dariusz Debowczyk

	* @link        http://www.codeigniter.com/user_guide/libraries/sessions.html

	*/

	class CI_Session {

	 
        var $CI; 
	    var $flash_key = 'flash'; // prefix for "flash" variables (eg. flash:new:message)
        var $time_to_update = 300;
        var $sess_length = 1800;
		var $session_id_ttl = 1800;
	 

	 

	 

	    function CI_Session()

	    {    
            $this->CI =& get_instance(); 
	       //ahora garantizamos que coja el valor de la configuracion del config.php
            $this->time_to_update = $this->CI->config->item('sess_time_to_update');
			$this->sess_length = $this->CI->config->item('sess_expiration');
            log_message('debug', "Native_session Class Initialized");
            $this->session_id_ttl = $this->CI->config->item('sess_expiration');
	        $this->_sess_run();

	    }

	 

	    /**

	    * Regenerates session id

	    */

	    function regenerate_id()

	    {

	        // copy old session data, including its id
         if (isset($_SESSION)){

	        $old_session_id = session_id();

	        $old_session_data = $_SESSION;

	 

	        // regenerate session id and store it

	        session_regenerate_id();

	        $new_session_id = session_id();

		

			// switch to the old session and destroy its storage

	        session_id($old_session_id);

           
        	session_destroy();
           
	 

	        // switch back to the new session id and send the cookie

	        session_id($new_session_id);

	        session_start();

	 

	        // restore the old session data into the new session

	        $_SESSION = $old_session_data;

	 

	        // update the session creation time

	        $_SESSION['regenerated'] = time();

			

				

			// session_write_close() patch based on this thread

	        // http://www.codeigniter.com/forums/viewthread/1624/

	        // there is a question mark ?? as to side affects

	

		

		$fecha=getdate();

		$mes=$fecha ['mon'] ;

		strlen($mes) ==1?$mes='0'.$mes:$mes=$mes;

		$dia=$fecha ['mday'];

		strlen($dia) ==1?$dia='0'.$dia:$dia=$dia;

		$hora=$fecha ['hours'];

		strlen($hora) ==1?$hora='0'.$hora:$hora=$hora;

		$minuto=$fecha ['minutes'];

		strlen($minuto) ==1?$minuto='0'.$minuto:$minuto=$minuto;

		$segundo=$fecha ['seconds'];

		strlen($segundo) ==1?$segundo='0'.$segundo:$segundo=$segundo;

		

        $enddate = $fecha['year'] . '-' . $mes. '-' .$dia . ' ' . $hora . ':' . $minuto . ':' . $segundo;

		

		if ($this->userdata('id')!=FALSE)//verificando que no ha expirado aun la sesion,si expiro no se hace nada

          $this->CI->db->query ( 'update user_sessions  set session_id=\'' .$new_session_id. '\'      , session_enddate=\''.$enddate.'\'  where  id = '. $this->userdata('id').'   ');//

		

		

		

			// end the current session and store session data.

			

        session_write_close();

	    }

        }
	 

	    /**

	    * Destroys the session and erases session storage

	    */

	    function destroy()

	    {

	        unset($_SESSION);

	        if ( isset( $_COOKIE[session_name()] ) )

	        {

	            setcookie(session_name(), '', time()-42000, '/');

	        }

			

		

			//de pin!!!! obligado para que cuando el usuario se desloguee pueda sin cerrar navegador volver a conectarse, sino no s epuede hasta cerrar el navegador	

			$_SESSION['regenerated']=0;	

	        
	
           if (isset ($_SESSION))  unset($_SESSION);
			//session_destroy();

	    }

	 

	    /**

	    * Reads given session attribute value

	    */

	    function userdata($item)

	    {

	        if($item == 'session_id'){ //added for backward-compatibility

	            return session_id();

	        }else{

	            return ( ! isset($_SESSION[$item])) ? false : $_SESSION[$item];

	        }

	    }

	 

	    /**

	    * Sets session attributes to the given values

	    */

	    function set_userdata($newdata = array(), $newval = '')

	    {

	        if (is_string($newdata))

	        {

	            $newdata = array($newdata => $newval);

	        }

	 

	        if (count($newdata) > 0)

	        {

	            foreach ($newdata as $key => $val)

	            {

	                $_SESSION[$key] = $val;

	            }

	        }

	    }

	 

	    /**

	    * Erases given session attributes

	    */

	    function unset_userdata($newdata = array())

	    {

	        if (is_string($newdata))

	        {

	            $newdata = array($newdata => '');

	        }

	 

	        if (count($newdata) > 0)

	        {

	            foreach ($newdata as $key => $val)

	            {

	                unset($_SESSION[$key]);

	            }

	        }

	    }

	 

	    /**

	    * Starts up the session system for current request

	    */

	    function _sess_run()

	    {

	        session_start();

	        

	        $session_id_ttl = $this->sess_length;

	 

	                 

	 

	        if (is_numeric($session_id_ttl))

	        {

	            if ($session_id_ttl > 0)

	            {

	                $this->session_id_ttl = $this->sess_length;

	            }

	            else

	            {

	                $this->session_id_ttl = (60*60*24*365*2);

	            }

	        }

	 

	        // check if session id needs regeneration

	        if ( $this->_session_id_expired() )

	        {

	            // regenerate session id (session data stays the

	            // same, but old session storage is destroyed)

	            $this->regenerate_id();
                $this->destroy();
	        } 

			
	

			

			

			

		//de pin!!!!! hacer esto
     if (isset($_SESSION)){
			if (($_SESSION['regenerated'] + $this->time_to_update) < time())

			{

				$this->regenerate_id();

			}

	 }

			

			

			

			

			

			

			

			

			

			

			

			

			

			

			

			

			

			

			     // delete old flashdata (from last request)

	        $this->_flashdata_sweep();

	 

	        // mark all new flashdata as old (data will be deleted before next request)

	        $this->_flashdata_mark();

	    }

	 

	    /**

	    * Checks if session has expired

	    */

	    function _session_id_expired()

	    {

	        if ( !isset( $_SESSION['regenerated'] ) )

	        {

	            $_SESSION['regenerated'] = time();

	            return false;

	        }

	 

	        $expiry_time = time() - $this->session_id_ttl;

	 

	        if ( $_SESSION['regenerated'] <=  $expiry_time )

	        {

			   
			  
			    if (isset ($_SESSION))  $this->destroy();
	            return true;

	        }

	         

	        return false;

	    }

	 

	    /**

	    * Sets "flash" data which will be available only in next request (then it will

	    * be deleted from session). You can use it to implement "Save succeeded" messages

	    * after redirect.

	    */

	    function set_flashdata($key, $value)

	    {

	        $flash_key = $this->flash_key.':new:'.$key;

	        $this->set_userdata($flash_key, $value);

	    }

	 

	    /**

	    * Keeps existing "flash" data available to next request.

	    */

	    function keep_flashdata($key)

	    {

	        $old_flash_key = $this->flash_key.':old:'.$key;

	        $value = $this->userdata($old_flash_key);

	 

	        $new_flash_key = $this->flash_key.':new:'.$key;

	        $this->set_userdata($new_flash_key, $value);

	    }

	 

	    /**

	    * Returns "flash" data for the given key.

	    */

	    function flashdata($key)

	    {

	        $flash_key = $this->flash_key.':old:'.$key;

	        return $this->userdata($flash_key);

	    }

	 

	    /**

	    * PRIVATE: Internal method - marks "flash" session attributes as 'old'

	    */

	    function _flashdata_mark()

	    {
            if (isset($_SESSION)){
	        foreach ($_SESSION as $name => $value)

	        {

	            $parts = explode(':new:', $name);

	            if (is_array($parts) && count($parts) == 2)

	            {

	                $new_name = $this->flash_key.':old:'.$parts[1];

	                $this->set_userdata($new_name, $value);

	                $this->unset_userdata($name);

	            }

	        }

	    }

	 }

	    /**

	    * PRIVATE: Internal method - removes "flash" session marked as 'old'

	    */

	    function _flashdata_sweep()

	    {
            if (isset($_SESSION)){
	        foreach ($_SESSION as $name => $value)

	        {

	            $parts = explode(':old:', $name);

	            if (is_array($parts) && count($parts) == 2 && $parts[0] == $this->flash_key)

	            {

	                $this->unset_userdata($name);

	            }

	        }
           }
	    }

	}

	?>