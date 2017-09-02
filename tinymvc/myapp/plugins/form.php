<?php

class Form extends TinyMVC_Controller
{

	public function serverList($input) {
		$this->load->model("server_info");

		$srv = $this->server_info->getFormList();

		$el = '<div class="row-fluid">
			<div class="control-group">
				<div class="controls span3">
				<label class="control-label"> Owner Server </label>
					<select id="serverid" name="serverid">
						<option value="NULL">All</option>';
		foreach($srv as $s) {
			$el .= "<option " . ($input == $s['id'] ? 'selected' : '')
			. " value=\"" . $s['id'] . "\">" . $s['name'] . "</option>\n";
		}
		$el .= "	</select>
				</div>
			</div>
		</div>";
		return $el;
	}

	public function matchList($input) {
		$this->load->model("match_detail");

		$matches = $this->match_detail->getFormList(true);

		$el = '<div class="row-fluid">
				<div class="control-group">
					<div class="controls span3">
					<label class="control-label"> Match Tier </label>
						<select id="matchid" name="matchid">
							<option value="NULL">All</option>';
		foreach ($matches as $k=>$v) {
			$el .= '<option ' . ($input == $v ? 'selected' : '') . ' value="' . $v . '">' . $k . "</option>\n";
		}
		$el .= 	"	</select>
					</div>
				</div>
			</div>";
		return $el;
	}

	public function timeList($startTime, $endTime) {
		$timeList = $this->getTimeFormList();

		$el = '<div class="row-fluid">
				<div class="control-group">
					<div class="controls span12">
						<label class="control-label"> Claim time-range </label>
						<input autocomplete="off" name="startTime" class="span3" data-provide="typeahead" data-items="' . count($timeList) . '" type="text"
						data-source=\'["' . implode($timeList,'","') . '"]\' value="' . $startTime . '">
						-
						<input autocomplete="off" name="endTime" class="span3" data-provide="typeahead" data-items="' . count($timeList) . '" type="text"
						data-source=\'["' . implode($timeList,'","') . '"]\' value="' . $endTime . '">
					</div>
				</div>
			</div>';
		return $el;
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


}

?>