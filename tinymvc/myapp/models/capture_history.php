<?php
/*
id int(11) unsigned AUTO_INCREMENT,
match_detail_id int(11) unsigned,
timeStamp datetime,
last_flipped datetime,
obj_id  varchar(10),
owner_server int(4),
owner_color enum("Blue","Red","Green","Neutral"),
tick_timer float(3,1),
num_yaks int(4),
duration_owned time,
PRIMARY KEY(id),
FOREIGN KEY(obj_id) REFERENCES objective(obj_id),
FOREIGN KEY(match_detail_id) REFERENCES match_detail(id)
ON DELETE CASCADE
*/
class capture_history extends TinyMVC_Model
{
	protected $_table = "capture_history";
	protected $pk = "id";

	public function estimate_yaks_delivered($prev_capture_history, $match_detail_id, $objective_model)
	{
		// set some shorthand variable names
		$obj_flipped = $prev_capture_history['last_flipped'];
		$dur_owned = $prev_capture_history['duration_owned'];
		$obj_id = $prev_capture_history['obj_id'];
		$color = $prev_capture_history['owner_color'];
		// get the corresponding objective from the database
		$objective = $objective_model->find(array(
			"obj_id" => $obj_id
		));

		if ($objective['type'] == "Camp")
		{ // set the "direction" of the yaks
			$select = "to_obj";
			$where = "from_obj";
		}
		elseif ($objective['type'] == "Tower" || $objective['type'] == "Keep" || $objective['type'] == "Castle")
		{ // set the "direction" of the yaks
			$select = "from_obj";
			$where = "to_obj";
		}

		$data = $this->db->pdo->query("
			SELECT SUM(yaks.yaks) as yaks
			FROM
			(
				SELECT estimated_travel_time, last_flipped, duration_owned,
					CASE
						WHEN #objective in middle of timespan; other-objective capped after objective->last_flipped and held part time
							last_flipped > '$obj_flipped' AND ADDTIME('$obj_flipped','$dur_owned') > ADDTIME(last_flipped,duration_owned)
						THEN (duration_owned)/(estimated_travel_time*60)
						WHEN #objective on left-edge of timespan; other-objective capped before the objective and held long enough to contribute
							last_flipped < '$obj_flipped' AND ADDTIME(last_flipped,duration_owned) > '$obj_flipped'
						THEN TIME_TO_SEC(TIMEDIFF(ADDTIME(last_flipped,duration_owned),'$obj_flipped'))/(estimated_travel_time*60)
						WHEN #objective on right-edge of timespan; other-objective capped before objective changed, held longer than objective
							last_flipped > '$obj_flipped' AND ADDTIME(last_flipped,duration_owned) > ADDTIME('$obj_flipped','$dur_owned')
						THEN TIME_TO_SEC(TIMEDIFF(ADDTIME('$obj_flipped','$dur_owned'),last_flipped))/(estimated_travel_time*60)
						WHEN #objective capped before other-objective AND held longer than other-objective
							last_flipped < '$obj_flipped' AND ADDTIME(last_flipped,duration_owned) < ADDTIME('$obj_flipped','$obj_owned')
						THEN (duration_owned)/(estimated_travel_time*60)
						#ELSE -100 #something went wrong with the above logic
					END AS 'yaks'
				FROM capture_history, supply_route
				WHERE
					match_detail_id = $match_detail_id
					AND ADDTIME(last_flipped, duration_owned) > '$obj_flipped'
					AND ADDTIME('$obj_flipped','$dur_owned') > last_flipped
					AND owner_color = '$color'
					AND obj_id = $select AND $where = '$obj_id'
			) yaks;"
		);
		foreach($data as $yaks)
		{
			return ( isset($yaks['yaks']) ? round($yaks['yaks']) : 0 );
		}
	}
}

?>