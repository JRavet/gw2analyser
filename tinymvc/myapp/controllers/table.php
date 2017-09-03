<?php

//ini_set("display_errors", 1);

class Table_Controller extends TinyMVC_Controller
{
	public function match_history()
	{
		$this->load->model('match_detail');

		if ( $this->view->form_submitted() ) {

			$data = $this->view->getData();

		} else {
			$matches = $this->match_detail->getList(); // get all
		}

		$formBuilder = new Form();
		$form['serverList'] = $formBuilder->serverList($data['serverid']);
		$form['matchList'] = $formBuilder->matchList($data['matchid']);

		$this->view->assign("form", $form);
		$this->view->assign("matches", $matches);
		$this->view->display('match_history_view');
	}

	public function capture_history()
	{
		$this->load->model('capture_history');
		$data = $this->capture_history->getList(); // get all

		$this->view->assign("data", $data);
		$this->view->display('capture_history_view');
	}

	public function guild_history()
	{
		$this->load->model("guild");
		$this->load->model("server_info");
		$this->load->model("match_detail");

		if ($this->view->form_submitted()) {

			$data = $this->view->getData();

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

			$guildList = $this->guild->getSummaryList($params);
			$this->view->assign("data", $guildList);
		} else { // fresh page-load
			$guildList = $this->guild->getFormList(); // to fill the guildname select
		}

		$formBuilder = new Form();
		$form['serverList'] = $formBuilder->serverList($data['serverid']);
		$form['matchList'] = $formBuilder->matchList($data['matchid']);
		$form['timeList'] = $formBuilder->timeList($data['startTime'], $data['endTime']);
		$form['weekdayList'] = $formBuilder->weekdayList($data['weekday']);
		$form['guildList'] = $formBuilder->guildList($data['guildname'], $guildList);
		$form['pageList'] = $formBuilder->pageList($data['page'], $guildList);
		$form['dateList'] = $formBuilder->dateList($data['startDate'], $data['endDate']);
		// for paginating the displayed data
		$form['pageNum'] = $data['page'];
		$form['listCount'] = count($guildList);
		// submit/reset buttons
		$form['submitBtn'] = $formBuilder->submitBtn();
		$form['resetBtn'] = $formBuilder->resetBtn("/table/guild_history");

		$this->view->assign("form", $form);
		$this->view->display("guild_history_view");
	}
}
?>
