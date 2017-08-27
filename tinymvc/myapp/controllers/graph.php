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
			// later on: select via checkbox of matches?
			$params = array();
			// TODO: select (region+tier AND weeknum) OR server AND weeknum

			$params = array(
				"where" => array(
				)
			);

			$score_history = $this->map_score->getScores($params);
			$skirmish_history = $this->skirmish_history->getScores($params);

			$this->view->assign("scores", $score_history);
			$this->view->assign("skirmish_points", $skirmish_history);
			$this->view->assign("formData", $data);
		} else { // fresh page-load

		}

		$this->view->assign("matches", $this->match_detail->getFormList()); // TODO remove All NA, all EU
		$this->view->assign("srv", $this->server_info->getFormList());
		$this->view->display("score_history_view");
	}
}
?>
