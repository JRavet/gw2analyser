<?php
/*
id int(11) unsigned AUTO_INCREMENT,
match_id varchar(4),
week_num int(3),
start_time datetime,
end_time datetime,
PRIMARY KEY(id)
*/
class match_detail extends TinyMVC_Model
{
	protected $_table = "match_detail";
	protected $pk = "id";

	public function getList($params=array())
	{
		$this->db->select("md.id, md.start_time, md.end_time, md.match_id,
			max(red_skirmish_score) as red_skirmish_score,
			max(blue_skirmish_score) as blue_skirmish_score,
			max(green_skirmish_score) as green_skirmish_score,
				(SELECT GROUP_CONCAT(name SEPARATOR ', ') as red_servers
					FROM server_linking sl
					LEFT JOIN server_info si on si.server_id = sl.server_id
					WHERE server_color = 'Red' AND md.id = sl.match_detail_id
				) red_servers,
				(SELECT GROUP_CONCAT(name SEPARATOR ', ') as blue_servers
					FROM server_linking sl
					LEFT JOIN server_info si on si.server_id = sl.server_id
					WHERE server_color = 'Blue' AND md.id = sl.match_detail_id
				) blue_servers,
				(SELECT GROUP_CONCAT(name SEPARATOR ', ') as green_servers
					FROM server_linking sl
					LEFT JOIN server_info si on si.server_id = sl.server_id
					WHERE server_color = 'Green' AND md.id = sl.match_detail_id
				) green_servers");
		$this->db->from($this->_table . " md");
		$this->db->join("skirmish_score sc", "sc.match_detail_id = md.id");
		$this->db->join("server_linking sl", "sl.match_detail_id = md.id");
		$this->db->groupby("md.id");
		$this->db->orderby("md.start_time DESC, md.match_id");
		$this->append_query($params);

		return $this->db->query_all();
	}

	public function getMatchDates() {
		$this->db->select("start_time, end_time");
		$this->db->from($this->_table);
		$this->db->orderby("start_time DESC");
		$this->db->groupby("start_time");

		return $this->db->query_all();
	}

	public function getFormList($include_regionals=false)
	{
		if ($include_regionals == true) {
			return array(
				"All NA"    => "1-%",
				"NA Tier 1" => "1-1",
				"NA Tier 2" => "1-2",
				"NA Tier 3" => "1-3",
				"NA Tier 4" => "1-4",
				"All EU"    => "2-%",
				"EU Tier 1" => "2-1",
				"EU Tier 2" => "2-2",
				"EU Tier 3" => "2-3",
				"EU Tier 4" => "2-4",
				"EU Tier 5" => "2-5",
			);
		} else { // single-matches only
			return array(
				"NA Tier 1" => "1-1",
				"NA Tier 2" => "1-2",
				"NA Tier 3" => "1-3",
				"NA Tier 4" => "1-4",
				"EU Tier 1" => "2-1",
				"EU Tier 2" => "2-2",
				"EU Tier 3" => "2-3",
				"EU Tier 4" => "2-4",
				"EU Tier 5" => "2-5",
			);
		}
	}
}

?>