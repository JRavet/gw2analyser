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
		$objective = $objective_model->find_one(array(
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
		{ // there is only 1 result, but it is inside an iterable object - extract it
			return ( isset($yaks['yaks']) ? round($yaks['yaks']) : 0 );
		}
	}

	public function getList($params=array())
	{
		$this->db->select("ch.id, timeStamp, last_flipped, owner_color, si.name, o.name,
			concat(o.compass_direction, ' ', o.type) as 'place', o.map_type, duration_owned, si.name as server_owner");
		$this->db->from($this->_table . " ch");
		$this->db->join("objective o", "o.obj_id = ch.obj_id");
		$this->db->join("server_info si", "si.server_id = ch.owner_server");
		$this->db->join("match_detail md", "md.id = ch.match_detail_id");
		$this->db->join("claim_history chi", "chi.capture_history_id = ch.id");
		$this->db->join("guild g", "g.guild_id = chi.claimed_by");
		$this->db->orderby("ch.last_flipped asc");
		$this->append_query($params);

		$results = array();

		$claim_history = new claim_history();
		$upgrade_history = new upgrade_history();
		$yak_history = new yak_history();

		foreach($this->db->query_all() as $row)
		{
			switch ($row['map_type'])
			{
				case 'Center': $row['map_type'] = "EBG"; break;
				case 'RedHome': $row['map_type'] = "RBL"; break;
				case 'BlueHome': $row['map_type'] = "BBL"; break;
				case 'GreenHome': $row['map_type'] = "GBL"; break;
				default: $row['map_type'] = "UNKN"; break;
			}
			$claims = $claim_history->getByCaptureId(array(
				"capture_history_id" => $row['id']
			));
			$upgrades = $upgrade_history->getByCaptureId(array(
				"capture_history_id" => $row['id']
			));
			$yaks = $yak_history->getByCaptureId(array(
				"capture_history_id" => $row['id']
			));
			$details = array_merge($claims, $upgrades, $yaks);

			usort($details, function($a, $b){
				return $a['timeStamp'] > $b['timeStamp'];
			});

			$row['details'] = $details;


			$results[] = $row;
		}
		return $results;
	}

	public function getActivitySummary($params)
	{
		$this->db->select("o.map_type, count(ch.id) as number,
		SUM(CASE WHEN owner_color = 'Red' THEN
			CASE WHEN o.type = 'Camp' THEN 1 ELSE
				CASE WHEN o.type = 'Tower' THEN 2 ELSE
					CASE WHEN o.type = 'Keep' THEN 3 ELSE
						CASE WHEN o.type = 'Castle' THEN 4 ELSE 0 END
					END
				END
			END
		ELSE 0 END) as redcaps,
		SUM(CASE WHEN owner_color = 'Blue' THEN
			CASE WHEN o.type = 'Camp' THEN 1 ELSE
				CASE WHEN o.type = 'Tower' THEN 2 ELSE
					CASE WHEN o.type = 'Keep' THEN 3 ELSE
						CASE WHEN o.type = 'Castle' THEN 4 ELSE 0 END
					END
				END
			END
		ELSE 0 END) as bluecaps,
		SUM(CASE WHEN owner_color = 'Green' THEN
			CASE WHEN o.type = 'Camp' THEN 1 ELSE
				CASE WHEN o.type = 'Tower' THEN 2 ELSE
					CASE WHEN o.type = 'Keep' THEN 3 ELSE
						CASE WHEN o.type = 'Castle' THEN 4 ELSE 0 END
					END
				END
			END
		ELSE 0 END) as greencaps,
		SUM(CASE WHEN o.type = 'Camp' THEN 1 ELSE
			CASE WHEN o.type = 'Tower' THEN 2 ELSE
				CASE WHEN o.type = 'Keep' THEN 3 ELSE
					CASE WHEN o.type = 'Castle' THEN 4 ELSE 0 END
				END
			END
		END) as totalcaps");
		$this->db->from($this->_table . " ch");
		$this->db->groupby('o.map_type');
		$this->db->join('objective o', 'o.obj_id = ch.obj_id');
		$this->db->join("match_detail md", "md.id = ch.match_detail_id");
		$this->db->join("server_linking sl", "sl.match_detail_id = md.id");
		$this->db->join("server_info si", "si.server_id = sl.server_id");
		$this->append_query($params);
		return $this->db->query_all();
	}
}

?>