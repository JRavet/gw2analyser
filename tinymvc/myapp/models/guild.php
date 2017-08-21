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

	/**
	 * Gets a set of summary stats for all guilds under the $params search filters
	 *
	 * @param $params - array
	 *			- where=>array("column"=>"value")
	 *			- orderby=>"values"
	 *			- join=>array("table"=>"t1.col = t2.col")
	 * @return array() of guild stats for any number of selected guilds
	 */
	public function getSummaryList($params=array())
	{
		$this->db->select("g.guild_id as 'id',
		concat(g.name, ' [', g.tag, ']') as guild_name,
		COUNT(CASE WHEN o.map_type='Center' THEN 1 END) AS claims_EBG,
		COUNT(CASE WHEN o.map_type='RedHome' THEN 1 END) AS claims_RBL,
		COUNT(CASE WHEN o.map_type='BlueHome' THEN 1 END) AS claims_BBL,
		COUNT(CASE WHEN o.map_type='GreenHome' THEN 1 END) AS claims_GBL,
		COUNT(CASE WHEN o.map_type=concat(cah.owner_color,'Home') THEN 1 END) as claims_home,
		COUNT(CASE WHEN o.map_type!=concat(cah.owner_color,'Home') AND o.map_type!='Center' THEN 1 END) as claims_enemy,
		SEC_TO_TIME( SUM( TIME_TO_SEC(ch.duration_claimed) ) ) as total_claim_duration,
		SEC_TO_TIME( ROUND(SUM( TIME_TO_SEC(ch.duration_claimed) ) / SUM(1)) ) as avg_claim_duration,
		MAX(ch.duration_claimed) as max_claim_duration,
		COUNT(CASE WHEN o.type='Camp' THEN 1 END) AS camps_claimed,
		COUNT(CASE WHEN o.type='Tower' THEN 1 END) AS towers_claimed,
		COUNT(CASE WHEN o.type='Keep' THEN 1 END) AS keeps_claimed,
		COUNT(CASE WHEN o.type='Castle' THEN 1 END) AS castles_claimed,
		COUNT(CASE WHEN TIME_TO_SEC(ch.duration_claimed) < 1800 THEN 1 END) AS claims_under_30min,
		COUNT(CASE WHEN TIME_TO_SEC(ch.duration_claimed) > 10800 THEN 1 END) AS claims_over_3hours,
		COUNT(*) AS claims_total");
		$this->db->from($this->_table . " g");
		$this->db->join("claim_history ch", "ch.claimed_by = g.guild_id");
		$this->db->join("capture_history cah", "cah.id = ch.capture_history_id");
		$this->db->join("objective o", "o.obj_id = cah.obj_id");
		$this->db->groupby("ch.claimed_by");
		$this->db->orderby("COUNT(*) DESC");
		$this->db->limit(50); // TODO arbitrary testing limit
		// TODO: # of upgrades slotted (dont include tiers)
		// TODO: most claimed objective
		// TODO: table of servers guild has claims on
		// TODO-TODO: attempt to determine actual server across different linkings / transfers

		$this->append_query($params);

		return $this->db->query_all();
	}

}

?>