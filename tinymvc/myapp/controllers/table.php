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

		if ( $this->view->form_submitted() ) {
			$data = $this->match_detail->getList(); // get all
			$this->view->assign("data", $data);
		}

		$this->view->display('match_detail_view');
	}

	public function capture_history()
	{
		$this->load->model('capture_history');
		$data = $this->capture_history->getList(); // get all

		$this->view->assign("data", $data);
		$this->view->display('capture_history_view');
	}

	public function guild_history($test='')
	{
		$this->load->model("guild");
		$this->load->model("server_info");

		if ($this->view->form_submitted()) {

			$data = $this->view->getData();

			foreach($data as $k=>$v) {
				if ($data[$k] == "NULL") {
					unset($data[$k]);
				}
			}
			$params = array(
				"where" => array(
					"md.match_id" => $data['matchid'],
					"cah.owner_server" => $data['serverid']
				)
			);

			$guildStats = $this->guild->getSummaryList($params);
			$this->view->assign("data", $guildStats);
			$this->view->assign("formData", $data);
		}

		$this->view->assign("srv", $this->server_info->getFormList());
		$this->view->display("guild_history_view");
	}
}
?>
