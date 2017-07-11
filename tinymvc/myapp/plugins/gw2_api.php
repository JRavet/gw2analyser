<?php

class gw2_api extends TinyMVC_Controller {

	private $region;
	private $regional_match_ids;

	function __construct($region=1) {
		$this->region = $region;
		$this->regional_match_ids = $this->get_match_ids();
	}

	public function refresh_regional_match_ids()
	{ // currently unnecessary and unused
		$this->regional_match_ids = $this->get_match_ids();
	}

	public function get_match_ids()
	{
		$all_ids = json_decode(file_get_contents("https://api.guildwars2.com/v2/wvw/matches/overview"));
		$regional_ids = "";

		foreach ($all_ids as $id)
		{
			if ( preg_match("/" . $this->region . "\-[0-9]*/", $id) ) // ensure the match id is relevant to the region
			{ // 1 = NA; 2 = EU
				$regional_ids .= $id . ",";
			}
		}

		return $regional_ids;
	}

	/**
	 * Retrieves a list of matches with full state-data from the api
	 * Filters list down to matches within the specified region
	 *
	 * @return array of world names and ids
	**/
	public function get_matches()
	{
		$matches = json_decode(file_get_contents('https://api.guildwars2.com/v2/wvw/matches?ids=' . $this->regional_match_ids));
	
		foreach ($matches as $match)
		{
			echo $match->id . "\n";
		}

		while ( is_null($matches) || !isset($matches[0]->start_time) )
		{ // if the api failed in returning data, try again
			usleep(500000); // half-second
			$matches = json_decode(file_get_contents('https://api.guildwars2.com/v2/wvw/matches?ids=' . $this->regional_match_ids));
		}

		return $matches;
	}

	/**
	 * Retrieves a list of world names and ids from the api
	 *
	 * @return array of world names and ids
	**/
	public function get_server_info()
	{
		return json_decode(file_get_contents("https://api.guildwars2.com/v2/worlds?ids=all"));
	}

	/**
	 * Retrieves a list of guild upgrades that can be applied to WvW objectives (tactics)
	 *
	 * @return array of guild upgrades (id, name, description, icon)
	**/
	public function get_guild_upgrades()
	{
		return json_decode(file_get_contents("https://api.guildwars2.com/v2/guild/upgrades?ids=all"));
	}

	/**
	 * Retrieves a list of objectives non-state-related data (e.g. position, name)
	 *
	 * @return array of objective data (id, name, type, sector_id, map_id, map_type, coord[x,y,z], label_coord[x,y], marker, chat_link)
	**/
	public function get_objectives()
	{
		return json_decode(file_get_contents("https://api.guildwars2.com/v2/wvw/objectives?ids=all"));
	}
}

?>