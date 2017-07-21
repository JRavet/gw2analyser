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
		$this->load->model('claim_history');
		$this->load->model('upgrade_history');
		$this->load->model('yak_history');
		$helper = new helper();

		$data = $this->capture_history->find_readable(); // get all

		$this->view->assign("data", $data);
		$this->view->assign("js", $helper->get_js());
		$this->view->assign("css", $helper->get_css());
		$this->view->display('index_view');
	}
}
?>
