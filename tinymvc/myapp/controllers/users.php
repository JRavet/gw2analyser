<?php

class Users_Controller extends TinyMVC_Controller
{

	public function login()
	{
		session_start();
		$_SESSION['user_id'] = 1; // TODO temp hack
	}

	public function logout()
	{
		session_start();
		session_destroy();
	}

	public function preferences()
	{
	}
}
?>
