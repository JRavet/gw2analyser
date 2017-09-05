<?php
/*
id int(11) unsigned AUTO_INCREMENT,
match_detail_id int(11) unsigned,
timeStamp datetime,
skirmish_number int(2),
red_skirmish_score int(3),
blue_skirmish_score int(3),
green_skirmish_score int(3),
PRIMARY KEY(id),
FOREIGN KEY(match_detail_id) REFERENCES match_detail(id)
ON DELETE CASCADE
*/
class skirmish_score extends TinyMVC_Model
{
	protected $_table = "skirmish_score";
	protected $pk = "id";

	public function getScores($params) {
		$this->db->select("*");
		$this->db->from($this->_table . " s");
		$this->db->join("match_detail md", "md.id = s.match_detail_id");
		$this->db->join("server_linking sl", "sl.match_detail_id = md.id");
		$this->append_query($params);
		return $this->db->query_all();
	}
}

?>