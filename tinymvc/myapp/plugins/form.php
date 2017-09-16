<?php

class Form extends TinyMVC_Controller
{

	public function serverList($input) {
		$this->load->model("server_info");

		$srv = $this->server_info->getFormList();

		$el = '<div class="row-fluid">
			<div class="control-group">
				<div class="controls span3">
				<label class="control-label"> Server </label>
					<select id="serverid" name="serverid">
						<option value="NULL">All</option>';
		foreach($srv as $s) {
			$el .= "<option " . ($input == $s['id'] ? 'selected' : '')
			. " value=\"" . $s['id'] . "\">" . preg_replace("/\(\)/", "", $s['name']) . "</option>\n";
		}
		$el .= "	</select>
				</div>
			</div>
		</div>";
		return $el;
	}

	public function matchDatesList($input) {
		$this->load->model("match_detail");

		$el = '<div class="row-fluid">
				<div class="control-group">
					<div class="controls span3">
					<label class="control-label"> Match Dates </label>
						<select id="matchDate" name="matchDate">';
		foreach ($this->match_detail->getMatchDates() as $date) {
			$el .= "<option " . ($input == $date['start_time'] ? 'selected' : '') . ' value="'
				. $date['start_time'] . '">' . date("m/d/Y", strtotime($date['start_time'])) . " - "
				. date("m/d/Y", strtotime($date['end_time'])) . "</option>\n";
		}
		$el .= 	"	</select>
					</div>
				</div>
			</div>";
		return $el;
	}

	public function mapList($input) {
		$el = '<div class="row-fluid">
				<div class="control-group">
					<div class="controls span3">
					<label class="control-label"> Map </label>
						<select id="map" name="map">
							<option value="NULL"> All </option>
							<option ' . ($input == "Center" ? "selected" : '') . ' value="Center"> Eternal Battlegrounds </option>
							<option ' . ($input == "GreenHome" ? "selected" : '') . ' value="GreenHome"> Green Borderlands </option>
							<option ' . ($input == "BlueHome" ? "selected" : '') . ' value="BlueHome"> Blue Borderlands </option>
							<option ' . ($input == "RedHome" ? "selected" : '') . ' value="RedHome"> Red Borderlands </option>
						</select>
					</div>
				</div>
			</div>';
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

		$startTime = isset($startTime) ? $startTime : "00:00:00";
		$endTime = isset($endTime) ? $endTime : "24:00:00";

		$el = '<div class="row-fluid">
				<div class="control-group">
					<div class="controls span12">
						<label class="control-label"> Time of day </label>
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

	public function dateList($startDate, $endDate, $halfList=false, $text="Date-range") {
		$el = '<div class="row-fluid">
				<div class="control-group">
					<div class="controls span12">
						<label class="control-label">' . $text . ' </label>
						<div data-date="" class="input-append date datepicker">
							<input value="' . $startDate . '" data-date-format="mm/dd/yyyy" name="startDate" type="text">
							<span class="add-on"><i class="icon-th"></i></span>
						</div>';
		if ($halfList === true) {
			$el .= "</div></div></div>";
			return $el;
		}
		$el .= '				-
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

	public function guildList($input) {
		$el = '<div class="row-fluid">
			<div class="control-group">
				<div class="controls span3">
				<label class="control-label"> Guild </label>
						<input autocomplete="off" id="guildname" name="guildname" type="text" data-provide="typeahead" value="' . (isset($input) ? $input : "Loading guilds ...") .'"
						data-source=""]\'>
				</div>
			</div>
		</div>';
		$js = '<script type="text/javascript">
			$(document).on("ready", function() {
				$.ajax({
					url: "/table/loadGuildList",
					method: "POST",
					success: function(data) {
						$("#guildname").val("' . $input . '");
						$("#guildname").attr("data-source", data);
					}
				});
			});
		</script>';
		return array("element"=>$el, "javascript"=>$js);
	}

	public function pageList($pageNum, $listCount, $pageAmount) {
		if ( !isset($pageNum) ) {
			$pageNum = 0;
		}

		$el = '<div class="row-fluid">
			<div class="control-group">
				<div class="controls span3">
					<label class="control-label"> Viewing Results </label>
					<select name="page">';

		for ($i = 0; $i < ($listCount / $pageAmount); $i++) {
			$el .= "<option " . ($pageNum == $i ? 'selected' : '') . " value=\"" . $i . "\">" . (($i*$pageAmount)+1) . " - " . (($i+1)*$pageAmount) . "</option>\n";
		}

		$el .= '		</select>
				</div>
			</div>
		</div>';
		return $el;
	}

	public function objectiveTypeList($input) {

		$el = '<div class="row-fluid">
			<div class="control-group">
				<div class="controls span3">
					<label class="control-label"> Objective Type </label>
					<select name="objectiveType">
						<option ' . (!isset($input) ? 'selected' : '') . ' value="NULL"> All </option>
						<option ' . ($input == 'Castle' ? 'selected' : '') . ' value="Castle"> Castle </option>
						<option ' . ($input == 'Keep' ? 'selected' : '') . ' value="Keep"> Keep </option>
						<option ' . ($input == 'Tower' ? 'selected' : '') . ' value="Tower"> Tower </option>
						<option ' . ($input == 'Camp' ? 'selected' : '') . ' value="Camp"> Camp </option>
						<option ' . ($input == 'Ruin' ? 'selected' : '') . ' value="Ruin"> Ruin </option>
					</select>
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