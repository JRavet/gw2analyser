<?php

// ini_set("display_errors", 1);

class Table_Controller extends TinyMVC_Controller
{
	public function score_history()
	{
		$this->load->model("map_score");
		$this->load->model("skirmish_score");
		$this->load->model('match_detail');
		$this->load->model("server_info");

		if ($this->view->form_submitted()) {

			$data = $this->view->getData();

			$params = array(
				"where" => array(
					"TIME(s.timeStamp) >=" => $data['startTime'],
					"TIME(s.timeStamp) <=" => $data['endTime'],
					"md.start_time" => $data['matchDate'],
				)
			);

			foreach($specialKeys as $k=>$v) { // special empty-checks for these keys and values
				if ( isset($data[$k]) && $data[$k] != "") {
					$params["where"][$v['key']] = $v['val'];
				}
			}

			if ( isset($data['serverid']) ) { // filter by server's id first
				$params['where']['sl.server_id'] = $data['serverid'];
			} elseif( isset($data['matchid']) ) { // filter by match_id if no server id provided
				$params['where']['match_id'] = $data['matchid'];
			} else {
				$error = "Must specify either a server or match tier!";
			}

			if ( !isset($error) ) {
				$score_history = $this->map_score->getScores($params, $data['map']);
					// the second parameter in above is required because skirmish_score doesn't have maps
				$skirmish_history = $this->skirmish_score->getScores($params);

				$this->view->assign("scores", $score_history);
				$this->view->assign("skirmish_points", $skirmish_history);
			} else {
				$this->view->assign("error", $error);
			}
		}

		$formBuilder = new Form();
		$form['serverList'] = $formBuilder->serverList($data['serverid']);
		$form['matchList'] = $formBuilder->matchList($data['matchid']);
		$form['timeList'] = $formBuilder->timeList($data['startTime'], $data['endTime']);
		$form['dateList'] = $formBuilder->matchDatesList($data['matchDate']);
		$form['mapList'] = $formBuilder->mapList($data['map']);
		$form['submitBtn'] = $formBuilder->submitBtn();
		$form['resetBtn'] = $formBuilder->resetBtn("/tables/score_history");

		$this->view->assign("form", $form);
		$this->view->display("tables/score_history_view");
	}

	public function match_history()
	{
		$this->load->model('match_detail');

		if ( $this->view->form_submitted() ) {

			$data = $this->view->getData();

			if ( !empty($data['matchids']) ) { // selecting match_detail_ids for use in other pages
				session_start();
				if ( isset($data['unset']) ) { // user wants to unset search params
					unset($_SESSION['matchids']); // remove matchids from $_SESSION
					unset($data['matchids']); // remove auto-checking too
				} else { // user wants to set matchids for future use
					$_SESSION['matchids'] = $data['matchids'];
					$params = array(
						"wherein" => array(
							"md.id" => $data['matchids']
						)
					);
				}
			} else {
				$specialKeys = array(
					"startDate" => array("key" => "DATE(md.start_time) =", "val" => date("Y-m-d", strtotime($data['startDate']))),
				); // keys which need to ensure there is data for

				$params = array(
					"where" => array (
						"sl.server_id" => $data['serverid'],
						"md.match_id LIKE" => $data['matchid'],
					)
				);

				foreach($specialKeys as $k=>$v) { // special empty-checks for these keys and values
					if ( isset($data[$k]) && $data[$k] != "") {
						$params["where"][$v['key']] = $v['val'];
					}
				}
			}

			$matches = $this->match_detail->getList($params);

		} else {
			$matches = $this->match_detail->getList(); // get all
		}

		$formBuilder = new Form();
		$form['serverList'] = $formBuilder->serverList($data['serverid']);
		$form['matchList'] = $formBuilder->matchList($data['matchid']);
		$form['dateList'] = $formBuilder->dateList($data['startDate'], NULL, true, "Start Date"); // only get half of the datelist
		$form['submitBtn'] = $formBuilder->submitBtn();
		$form['resetBtn'] = $formBuilder->resetBtn("/table/match_history");
		$form['matchids'] = $data['matchids'];

		$this->view->assign("form", $form);
		$this->view->assign("matches", $matches);
		$this->view->display('tables/match_history_view');
	}

	public function capture_history()
	{
		$this->load->model('capture_history');
		$this->load->model('guild');

		if ($this->view->form_submitted()) {

			$data = $this->view->getData();
			$form['dataLimit'] = 5000; // otherwise may be too much

			$specialKeys = array(
				"startDate" => array("key" => "DATE(ch.claimed_at) >=", "val" => date("Y-m-d", strtotime($data['startDate']))),
				"endDate" => array("key" => "DATE(ch.claimed_at) <=", "val" => date("Y-m-d", strtotime($data['endDate']))),
			); // keys which need to ensure there is data for

			$params = array(
				"where" => array(
					"md.match_id LIKE" => $data['matchid'],
					"concat(g.name, ' [', g.tag, ']') LIKE" => "%" . $data['guildname'] . "%",
					"ch.owner_server" => $data['serverid'],
					"TIME(ch.last_flipped) >=" => $data['startTime'],
					"TIME(ch.last_flipped) <=" => $data['endTime'],
					"o.type" => $data['objectiveType']
				),
				"wherein" => array(
					"DAYOFWEEK(ch.last_flipped)" => $data['weekday']
				),
				"limit" => $form['dataLimit']
			);

			foreach($specialKeys as $k=>$v) { // special empty-checks for these keys and values
				if ( isset($data[$k]) && $data[$k] != "") {
					$params["where"][$v['key']] = $v['val'];
				}
			}

			$captureList = $this->capture_history->getList($params);
			$this->view->assign("captureList", $captureList);
		}

		$guildList = $this->guild->getFormList(); // to fill the guildname select

		$formBuilder = new Form();
		$form['pageAmount'] = 300;
		$form['serverList'] = $formBuilder->serverList($data['serverid']);
		$form['matchList'] = $formBuilder->matchList($data['matchid']);
		$form['timeList'] = $formBuilder->timeList($data['startTime'], $data['endTime']);
		$form['weekdayList'] = $formBuilder->weekdayList($data['weekday']);
		$form['objectiveTypeList'] = $formBuilder->objectiveTypeList($data['objectiveType']);
		$form['guildList'] = $formBuilder->guildList($data['guildname'], $guildList);
		$form['pageList'] = $formBuilder->pageList($data['page'], $captureList, $form['pageAmount']);
		$form['dateList'] = $formBuilder->dateList($data['startDate'], $data['endDate']);
		// for paginating the displayed data
		$form['pageNum'] = $data['page'];
		$form['listCount'] = count($captureList);
		// submit/reset buttons
		$form['submitBtn'] = $formBuilder->submitBtn();
		$form['resetBtn'] = $formBuilder->resetBtn("/table/capture_history");

		$this->view->assign("form", $form);
		$this->view->display('tables/capture_history_view');
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
		$form['pageAmount'] = 100;
		$form['serverList'] = $formBuilder->serverList($data['serverid']);
		$form['matchList'] = $formBuilder->matchList($data['matchid']);
		$form['timeList'] = $formBuilder->timeList($data['startTime'], $data['endTime']);
		$form['weekdayList'] = $formBuilder->weekdayList($data['weekday']);
		$form['guildList'] = $formBuilder->guildList($data['guildname'], $guildList);
		$form['pageList'] = $formBuilder->pageList($data['page'], $guildList, $form['pageAmount']);
		$form['dateList'] = $formBuilder->dateList($data['startDate'], $data['endDate']);
		// for paginating the displayed data
		$form['pageNum'] = $data['page'];
		$form['listCount'] = count($guildList);
		// submit/reset buttons
		$form['submitBtn'] = $formBuilder->submitBtn();
		$form['resetBtn'] = $formBuilder->resetBtn("/table/guild_history");

		$this->view->assign("form", $form);
		$this->view->display('tables/guild_history_view');
	}
}
?>
