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
	public function match_history()
	{
		$this->load->model('match_detail');

		if ( $this->view->form_submitted() ) {
			$data = $this->match_detail->getList(); // get all
			$this->view->assign("data", $data);
		}

		$this->view->display('match_history_view');
	}

	public function capture_history()
	{
		$this->load->model('capture_history');
		$data = $this->capture_history->getList(); // get all

		$this->view->assign("data", $data);
		$this->view->display('capture_history_view');
	}

	public function getTimeFormList() {
		$times = array();
		for ($i = 0; $i < 96; $i++) {
			$times[] = date("H:i:s", $i*15*60);
		}
		asort($times);
		$times[] = "24:00:00"; // date() disallows this time
		return $times;
	}

	public function guild_history()
	{
		$this->load->model("guild");
		$this->load->model("server_info");
		$this->load->model("match_detail");

		if ($this->view->form_submitted()) {

			$data = $this->view->getData();

			foreach($data as $k=>$v) {
				if ($v == "NULL" || !isset($v) || $v == "") {
					unset($data[$k]);
				}
			}

			$specialKeys = array(
				"startDate" => array("key" => "DATE(ch.claimed_at) >=", "val" => date("Y-m-d", strtotime($data['startDate']))),
				"endDate" => array("key" => "DATE(ch.claimed_at) <=", "val" => date("Y-m-d", strtotime($data['endDate']))),

			); // keys which need to ensure there is data for

			$params = array(
				"where" => array(
					"md.match_id LIKE" => $data['matchid'],
					"concat(g.name, ' [', g.tag, ']') LIKE" => "%" . $data['guildname'] . "%",
					"cah.owner_server" => $data['serverid'],
					"TIME(ch.claimed_at) >=" => $data['startTime'],
					"TIME(ch.claimed_at) <=" => $data['endTime']
				),
				"wherein" => array(
					"DAYOFWEEK(ch.claimed_at)" => $data['weekday']
				)
			);

			foreach($specialKeys as $k=>$v) { // special empty-checks for these keys and values
				if ( isset($data[$k]) && $data[$k] != "") {
					$params["where"][$v['key']] = $v['val'];
				}
			}

			$guildStats = $this->guild->getSummaryList($params);
			$this->view->assign("data", $guildStats);
			$this->view->assign("guildNames", $guildStats); // also use query data for guild-name-select list
			$this->view->assign("formData", $data);
		} else { // fresh page-load
			$data = array( // setting some default values for fields
				// "startDate" => date('m/d/Y', strtotime('last Friday')), 
				// "endDate" => date('m/d/Y', strtotime('next Friday')),
				"startTime" => "00:00:00",
				"endTime" => "24:00:00",
			);
			$this->view->assign('formData', $data); // setting default values
			$this->view->assign("guildNames", $this->guild->getFormList());
		}

		$this->view->assign("timeList", $this->getTimeFormList());
		$this->view->assign("srv", $this->server_info->getFormList());
		$this->view->assign("matches", $this->match_detail->getFormList());
		$this->view->display("guild_history_view");
	}
}
?>
