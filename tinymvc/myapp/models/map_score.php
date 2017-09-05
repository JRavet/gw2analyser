<?php
/*
id int(11) unsigned AUTO_INCREMENT,
match_detail_id int(11) unsigned,
timeStamp datetime,
map_id enum("RedHome","BlueHome","GreenHome","Center"),
greenScore int(6),
blueScore int(6),
redScore int(6),
greenKills int(4),
blueKills int(4),
redKills int(4),
greenDeaths int(4),
blueDeaths int(4),
redDeaths int(4),
green_ppt int(3),
blue_ppt int(3),
red_ppt int(3),
PRIMARY KEY(id),
FOREIGN KEY(match_detail_id) references match_detail(id)
ON DELETE CASCADE
*/
class map_score extends TinyMVC_Model
{
	protected $_table = "map_score";
	protected $pk = "id";

	public function getScores($params=array()) {
		$this->db->select("timeStamp, sum(greenDeaths) as greendeaths,
			sum(blueDeaths) as bluedeaths, sum(redDeaths) as reddeaths,
			sum(greenKills) as greenkills, sum(blueKills) as bluekills,
			sum(redKills) as redkills, sum(greenScore) as greenscore,
			sum(blueScore) as bluescore, sum(redScore) as redscore,
			sum(green_ppt) as greenppt, sum(blue_ppt) as blueppt,
			sum(red_ppt) as redppt,
			#match_details.green_srv as green Server,
			#match_details.blue_srv as blue Server,
			#match_details.red_srv as red Server,
			sum(greenKills)/sum(greenDeaths)*100.0 as greenkdr,
			sum(blueKills)/sum(blueDeaths)*100.0 as bluekdr,
			sum(redKills)/sum(redDeaths)*100.0 as redkdr");
		$this->db->from($this->_table . " s");
		$this->db->join("match_detail md", "md.id = s.match_detail_id");
		$this->db->join("server_linking sl", "sl.match_detail_id = md.id");
		$this->append_query($params);
		return $this->db->query_all();
	}
}

?>