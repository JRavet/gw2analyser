<?php

class Users_Controller extends TinyMVC_Controller
{

	public function login()
	{
		session_start();
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
