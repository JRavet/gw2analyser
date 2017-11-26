<?php

class gw2_api extends TinyMVC_Controller {

	private $match_id;
	private $helper;

	function __construct($match_id=null, $helper=null)
	{ // static collector does not use params, active collector does
		$this->match_id = $match_id;
		$this->helper = $helper;
	}

	/**
	 * Requests data from the API until it has valid data; half second delay between attempts
	 *
	 * @param url - full url (including arguments) to pull data from
	 * @return requested data
	 */
	private function call_api($url) {
		$data = json_decode(file_get_contents($url));
		$delay = 0;
		while ( is_null($data) || !isset($data) ) {
			usleep(500000 + $delay*1000000); // half-second + up to 4 seconds delay
			$data = json_decode(file_get_contents($url));
			if ($delay < 4) { // until the delay is 4 seconds, add more to it
				$delay += 0.5;
			} else { // else delay is maxed: show error of invalid data
				$this->helper->log_message(501);
			}
		}
		return $data;
	}

	/**
	 * Retrieves full match-data for this instances match_id
	 *
	 * @return object of match-data
	**/
	public function get_match_data()
	{
		$match = $this->call_api('https://api.guildwars2.com/v2/wvw/matches?id=' . $this->match_id);

		return $match;
	}

	public function get_server_population($server_id)
	{
		return $this->call_api("https://api.guildwars2.com/v2/worlds?ids=" . $server_id)[0]->population;
	}

	public function get_guild($guild_id)
	{
		return $this->call_api("https://api.guildwars2.com/v1/guild_details.json?guild_id=" . $guild_id);
	}

	/**
	 * Retrieves list of scores (NOT kills/deaths!)
	 *
	 * @return array of score-data
	**/
	public function get_scores()
	{
		return $this->call_api("https://api.guildwars2.com/v2/wvw/matches/scores?id=" . $this->match_id);
	}

	/**
	 * Retrieves a list of world names and ids from the api
	 *
	 * @return array of world names and ids
	**/
	public function get_server_info()
	{
		return $this->call_api("https://api.guildwars2.com/v2/worlds?ids=all");
	}

	/**
	 * Retrieves a list of guild upgrades that can be applied to WvW objectives (tactics)
	 *
	 * @return array of guild upgrades (id, name, description, icon)
	**/
	public function get_guild_upgrades()
	{
		return $this->call_api("https://api.guildwars2.com/v2/guild/upgrades?ids=all");
	}

	/**
	 * Retrieves a list of objectives non-state-related data (e.g. position, name)
	 *
	 * @return array of objective data (id, name, type, sector_id, map_id, map_type, coord[x,y,z], label_coord[x,y], marker, chat_link)
	**/
	public function get_objectives()
	{
		return $this->call_api("https://api.guildwars2.com/v2/wvw/objectives?ids=all");
	}
}

?>