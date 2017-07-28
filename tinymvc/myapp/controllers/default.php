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
	public function match_detail_view()
	{
		$this->load->model('match_detail');
		$data = $this->match_detail->find_readable(); // get all

		$this->view->assign("data", $data);
		$this->view->display('match_detail_view');
	}
	public function history_view()
	{
		$this->load->model('capture_history');
		$data = $this->capture_history->find_readable(); // get all

		$this->view->assign("data", $data);
		$this->view->display('history_view');
	}

}
?>
