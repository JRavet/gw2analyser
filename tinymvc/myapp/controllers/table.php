<?php

/**
 * default.php
 *
 * default application controller
 *
 * @package		TinyMVC
 * @author		Monte Ohrt
 */

class Table_Controller extends TinyMVC_Controller
{
	public function match_details()
	{
		$this->load->model('match_detail');
		$data = $this->match_detail->find_readable(); // get all

		$this->view->assign("data", $data);
		$this->view->display('match_detail_view');
	}
	public function capture_history()
	{
		$this->load->model('capture_history');
		$data = $this->capture_history->find_readable(); // get all

		$this->view->assign("data", $data);
		$this->view->display('capture_history_view');
	}

	public function guild_history()
	{
		$this->load->model("guild");

		$data = $this->guild->getStats();

		$this->view->assign("data", $data);
		$this->view->display("guild_history_view");
	}
}
?>
