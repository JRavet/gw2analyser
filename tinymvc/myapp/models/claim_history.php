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

	public function find_readable($params)
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
}

?>