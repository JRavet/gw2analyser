<?php
/*
id int(11) unsigned AUTO_INCREMENT,
capture_history_id int(11) unsigned,
claimed_by varchar(60),
claimed_at datetime,
duration_claimed time,
PRIMARY KEY(id),
FOREIGN KEY(claimed_by) REFERENCES guild(guild_id)
ON DELETE CASCADE,
FOREIGN KEY(capture_history_id) REFERENCES capture_history(id)
ON DELETE CASCADE
*/
class claim_history extends TinyMVC_Model
{
	protected $_table = "claim_history";
	protected $pk = "id";

	public function getByCaptureId($params)
	{
		$this->db->select("'Claim' as type, claimed_at as timeStamp,
			concat(g.name, ' ', '[', g.tag, ']') as 'guild', duration_claimed");
		$this->db->from($this->_table . " ch");
		$this->db->join("guild g", "g.guild_id = ch.claimed_by");
		foreach($params as $key=>$value)
		{
			$this->db->where($key, $value);
		}
		return $this->db->query_all();
	}

	public function getActivitySummary($params)
	{
		$this->db->select("o.map_type, count(ch.id) as number,
		SUM(CASE WHEN cah.owner_color = 'Red' THEN 1 ELSE 0 END) as redclaims,
		SUM(CASE WHEN cah.owner_color = 'Blue' THEN 1 ELSE 0 END) as blueclaims,
		SUM(CASE WHEN cah.owner_color = 'Green' THEN 1 ELSE 0 END) as greenclaims,
		COUNT(cah.id) as totalclaims");
		$this->db->from($this->_table . " ch");
		$this->db->groupby('o.map_type');
		$this->db->join("capture_history cah", "cah.id = ch.capture_history_id");
		$this->db->join('objective o', 'o.obj_id = cah.obj_id');
		$this->db->join("match_detail md", "md.id = cah.match_detail_id");
		$this->db->join("server_linking sl", "sl.match_detail_id = md.id");
		$this->db->join("server_info si", "si.server_id = sl.server_id");
		$this->append_query($params);
		return $this->db->query_all();
	}
}

?>