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
	 * Finds all servers that a guild has claims on, regardless of any filters
	 * 		to give a true representation of the servers a guild has been on
	 *
	 * @param guild_id - the 30-character hex string of a guild
	 * @return array(server, server_claims, last_claim, claims_total)
	 */
	public function getServerClaims($guild_id) {
		$this->db->select("s.name as 'server', count(*) as 'server_claims', max(ch.claimed_at) as 'last_claim',
			(SELECT COUNT(*) FROM guild g LEFT JOIN claim_history ch ON ch.claimed_by = g.guild_id WHERE g.guild_id = \"$guild_id\") as 'claims_total'"
		);
		$this->db->from("server_info s");
		$this->db->join("capture_history cah", "cah.owner_server = s.server_id");
		$this->db->join("claim_history ch", "ch.capture_history_id = cah.id");
		$this->db->join("guild g", "g.guild_id = ch.claimed_by");
		$this->db->where("g.guild_id", $guild_id);
		$this->db->orderby("COUNT(*) DESC");
		$this->db->groupby('cah.owner_server');
		return $this->db->query_all();
	}

	/**
	 * Gets the most-claimed objective for a guild within the specified filter
	 *
	 * @param guild_id - the 30-character hex string of a guild
	 * @param params - filter parameters
	 * @return a single array of (objective, type, dir, claims, map)
	 */
	public function getMostClaimedObjective($guild_id, $params) {
		$this->db->select("o.name as 'objective', o.type as 'type', o.compass_direction as 'dir',
		count(*) as 'claims', o.map_type as 'map'");
		$this->db->from("objective o");
		$this->db->join("capture_history cah", "cah.obj_id = o.obj_id");
		$this->db->join("claim_history ch", "ch.capture_history_id = cah.id");
		$this->db->join("guild g", "g.guild_id = ch.claimed_by");
		$this->db->join("match_detail md", "md.id = cah.match_detail_id");
		$this->db->where("g.guild_id", $guild_id);
		$this->db->orderby("COUNT(*) DESC");
		$this->db->limit(1);
		$this->db->groupby('cah.obj_id');
		$this->append_query($params);
		return $this->db->query_one();
	}

	/**
	 * Gets the number of tactics slotted into all of the guilds' claimed objectives within the filter results
	 *
	 * @param guild_id - the 30-character hex string of a guild
	 * @param params - filter parameters
	 * @return a single array of (tactics_slotted)
	 */
	public function getNumberTacticsSlotted($guild_id, $params) {
		$this->db->select("COUNT(*) as 'tactics_slotted'");
		$this->db->from("claim_history ch");
		$this->db->join("capture_history cah", "cah.id = ch.capture_history_id");
		$this->db->join("upgrade_history uh", "uh.capture_history_id = cah.id");
		$this->db->join("guild g", "g.guild_id = ch.claimed_by");
		$this->db->join("match_detail md", "md.id = cah.match_detail_id");
		$this->db->notIn("uh.id", array(1,2,3));
		$this->db->where("g.guild_id", $guild_id);
		$this->db->groupby('ch.claimed_by');
		$this->append_query($params);
		return $this->db->query_one();
	}

	/**
	 * Gets a set of summary stats for all guilds under the $params search filters
	 *
	 * @param $params - array of filter-inputs
	 *			- where=>array("column"=>"value")
	 *			- wherein=>array("column"=>"value")
	 *			- orderby=>"values"
	 *			- join=>array("table"=>"t1.col = t2.col")
	 * @return array() of guild stats for any number of selected guilds
	 */
	public function getSummaryList($params=array(), $offset=NULL)
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
		$this->db->join("match_detail md", "md.id = cah.match_detail_id");
		$this->db->groupby("ch.claimed_by");
		$this->db->orderby("COUNT(*) DESC, g.name ASC");
		if (isset($offset)) {
			$this->db->limit("18446744073709551615", $offset);
		}
		$this->append_query($params);

		$results = array();
		foreach($this->db->query_all() as $row) {
			$row['servers'] = $this->getServerClaims($row['id']);
			$row['most_claimed'] = $this->getMostClaimedObjective($row['id'], $params);
			$row['tactics_slotted'] = $this->getNumberTacticsSlotted($row['id'], $params)['tactics_slotted'];
			$results[] = $row;
		}

		return $results;
	}

	/**
	 * Gets a list of all guild names in the database, potentially within the filter results
	 *
	 * @param params - array of filter-inputs
	 * @return a list of guild_names for a typeahad input
	 */
	public function getFormList($params=array())
	{
		$this->db->select("concat(g.name, ' [', g.tag, ']') as 'guild_name'");
		$this->db->from($this->_table . " g");
		$this->db->join("claim_history ch", "ch.claimed_by = g.guild_id");
		$this->db->join("capture_history cah", "cah.id = ch.capture_history_id");
		$this->db->join("objective o", "o.obj_id = cah.obj_id");
		$this->db->join("match_detail md", "md.id = cah.match_detail_id");
		$this->db->notin("g.name", array("")); // exclude the blank guild
		$this->append_query($params);
		$this->db->groupby("ch.claimed_by");
		$this->db->orderby("COUNT(*) DESC, g.name ASC");
		return $this->db->query_all();
	}

}

?>