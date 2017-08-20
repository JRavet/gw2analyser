<?php
/*
guild_id varchar(60),
emblem_last_updated datetime,
name varchar(60),
tag varchar(10),
PRIMARY KEY(guild_id)
*/
class Guild extends TinyMVC_Model
{

	protected $_table = "guild";
	protected $pk = "guild_id";

	public function getStats($where=array())
	{
		$this->db->select("concat(g.name, ' [', g.tag, ']') as guild_name,
		COUNT(CASE WHEN o.map_type='Center' THEN 1 END) AS claims_EBG,
		COUNT(CASE WHEN o.map_type='RedHome' THEN 1 END) AS claims_RBL,
		COUNT(CASE WHEN o.map_type='BlueHome' THEN 1 END) AS claims_BBL,
		COUNT(CASE WHEN o.map_type='GreenHome' THEN 1 END) AS claims_GBL,
		COUNT(*) AS claims_total");
		$this->db->from($this->_table . " g");
		$this->db->join("claim_history ch", "ch.claimed_by = g.guild_id");
		$this->db->join("capture_history cah", "cah.id = ch.capture_history_id");
		$this->db->join("objective o", "o.obj_id = cah.obj_id");
		$this->db->groupby("g.guild_id");

		foreach ($where as $k=>$v) {
			$this->db->where("$k", "$v");
		}

		return $this->db->query_all();
	}

}

?>