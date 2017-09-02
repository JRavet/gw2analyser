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

	public function dateList($startDate, $endDate) {
		$el = '<div class="row-fluid">
				<div class="control-group">
					<div class="controls span12">
						<label class="control-label"> Claim date-range </label>
						<div data-date="" class="input-append date datepicker">
							<input value="' . $startDate . '" data-date-format="mm/dd/yyyy" name="startDate" type="text">
							<span class="add-on"><i class="icon-th"></i></span>
						</div>
						-
						<div data-date="" class="input-append date datepicker">
							<input value="' . $endDate . '" data-date-format="mm/dd/yyyy" name="endDate" type="text">
							<span class="add-on"><i class="icon-th"></i></span>
						</div>
					</div>
				</div>
			</div>';

		return $el;
	}

	public function weekdayList($input) {
		$el = '
		<div class="row-fluid">
			<div class="control-group">
				<div class="controls span12">
					<label class="control-label"> Days </label>
					<input type="checkbox" name="weekday[]" ' . (in_array(1, $input) ? 'checked' : '') . ' value="1">Sunday
					<input type="checkbox" name="weekday[]" ' . (in_array(2, $input) ? 'checked' : '') . ' value="2">Monday
					<input type="checkbox" name="weekday[]" ' . (in_array(3, $input) ? 'checked' : '') . ' value="3">Tuesday
					<input type="checkbox" name="weekday[]" ' . (in_array(4, $input) ? 'checked' : '') . ' value="4">Wednesday
					<input type="checkbox" name="weekday[]" ' . (in_array(5, $input) ? 'checked' : '') . ' value="5">Thursday
					<input type="checkbox" name="weekday[]" ' . (in_array(6, $input) ? 'checked' : '') . ' value="6">Friday
					<input type="checkbox" name="weekday[]" ' . (in_array(7, $input) ? 'checked' : '') . ' value="7">Saturday
				</div>
			</div>
		</div>';
		return $el;
	}

	public function guildList($input, $guildNames) {
		$el = '<div class="row-fluid">
			<div class="control-group">
				<div class="controls span3">
				<label class="control-label"> Guild </label>
						<input autocomplete="off" name="guildname" type="text" data-provide="typeahead" data-items"' . count($guildNames) . '" value="' . $input .'"
						data-source=\'["' . implode(array_map(function($el){return $el['guild_name']; }, $guildNames),'","') . '"]\'>
				</div>
			</div>
		</div>';
		return $el;
	}

	public function pageList($pageNum, $list) {
		if ( !isset($pageNum) ) {
			$pageNum = 0;
		}

		$el = '<div class="row-fluid">
			<div class="control-group">
				<div class="controls span3">
					<label class="control-label"> Viewing Results </label>
					<select name="page">';

		for ($i = 0; $i < count($list) / 100; $i++) {
			$el .= "<option " . ($pageNum == $i ? 'selected' : '') . " value=\"" . $i . "\">" . (($i*100)+1) . " - " . (($i+1)*100) . "</option>\n";
		}

		$el .= '		</select>
				</div>
			</div>
		</div>';
		return $el;
	}

	public function submitBtn() {
		return '<input type="submit" value="Filter">';
	}

	public function resetBtn($url) {
		return '<a class="btn" style="margin-top:5px" href="' . $url . '">Reset Filter</a>';
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