<?php

class Guild extends TinyMVC_Model
{
	public function get_guilds()
	{
		$this->db->select("*");
		$this->db->from("guild");
		$this->db->limit("5");
		return $this->db->query_all();
	}

	public function save_guild($guild_obj)
	{
		
	}
}

?>