<?php

/***
 * Name:       TinyMVC
 * About:      An MVC application framework for PHP
 * Copyright:  (C) 2007-2008 Monte Ohrt, All rights reserved.
 * Author:     Monte Ohrt, monte [at] ohrt [dot] com
 * License:    LGPL, see included license file  
 ***/

// ------------------------------------------------------------------------

/**
 * TinyMVC_Model
 *
 * @package		TinyMVC
 * @author		Monte Ohrt
 */

class TinyMVC_Model
{
 	/**
	 * $db
	 *
	 * the database object instance
	 *
	 * @access	public
	 */
  var $db = null;  
    
 	/**
	 * class constructor
	 *
	 * @access	public
	 */
  function __construct($poolname=null) {
    $this->db = tmvc::instance()->controller->load->database($poolname);
  }

  function save($data) {
  	try
  	{
		$this->db->insert($this->_table, $data);
	}
	catch (Exception $e)
	{
		echo $e->getMessage();
	}
  }

  function is_stored($data) {
  	try
  	{
  		$this->db->select('*');
  		$this->db->from($this->_table);

  		foreach($data as $key=>$value)
  		{
  		 	$this->db->where($key, $value);
  		}

  		$this->db->query();
  		return $this->db->next();
  	}
  	catch (Exception $e)
  	{
  		echo $e->getMessage();
  	}
  }

  function delete_all()
  {
  	$this->db->delete($this->_table);
  }
  
}

?>
