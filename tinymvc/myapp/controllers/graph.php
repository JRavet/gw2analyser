<?php

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
					"week_num" => $data['weekNum'],
				)
			);

			if ( isset($data['serverid']) ) { // filter by server's id
				$params['where']['server_id'] = $data['serverid'];
			} else { // filter by match_id if no server id provided
				$params['where']['match_id'] = $data['matchid'];
			}

			$score_history = $this->map_score->getScores($params);
			$skirmish_history = $this->skirmish_history->getScores($params);

			$this->view->assign("scores", $score_history);
			$this->view->assign("skirmish_points", $skirmish_history);
			$this->view->assign("formData", $data);
		} else { // fresh page-load

		}

		$this->view->assign("matches", $this->match_detail->getFormList());
		$this->view->assign("week_numbers", $this->match_detail->getWeekNumbers());
		$this->view->assign("srv", $this->server_info->getFormList());
		$this->view->display("score_history_view");
	}
}
?>
