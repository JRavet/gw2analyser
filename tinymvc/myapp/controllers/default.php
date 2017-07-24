<?php

/**
 * default.php
 *
 * default application controller
 *
 * @package		TinyMVC
 * @author		Monte Ohrt
 */

class Default_Controller extends TinyMVC_Controller
{
	public function index()
	{
		$this->load->model('capture_history');

		$data = $this->capture_history->find_readable(); // get all

		$this->view->assign("data", $data);
		$this->view->display('history_view');
	}
}
?>
