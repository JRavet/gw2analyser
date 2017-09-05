<?php

// ini_set("display_errors", 1);

class Graph_Controller extends TinyMVC_Controller
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
					"md.start_time" => $data['matchDate']
				)
			);

			foreach($specialKeys as $k=>$v) { // special empty-checks for these keys and values
				if ( isset($data[$k]) && $data[$k] != "") {
					$params["where"][$v['key']] = $v['val'];
				}
			}

			if ( isset($data['serverid']) ) { // filter by server's id first
				$params['where']['server_id'] = $data['serverid'];
			} elseif( isset($data['matchid']) ) { // filter by match_id if no server id provided
				$params['where']['match_id'] = $data['matchid'];
			} else {
				$error = "Must specify either a server or match tier!";
			}

			if ( !isset($error) ) {
				$score_history = $this->map_score->getScores($params);
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
		$form['dateList'] = $formBuilder->matchDatesList($data['matchDates']);
		$form['submitBtn'] = $formBuilder->submitBtn();
		$form['resetBtn'] = $formBuilder->resetBtn("/graph/score_history");

		$this->view->assign("form", $form);
		$this->view->display("graphs/score_history_view");
	}
}
?>
