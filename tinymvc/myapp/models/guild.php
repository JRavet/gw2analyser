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

	public function store_guild($guild_obj)
	{
		$this->save(array(
			'guild_id' => $guild_obj->guild_id,
			'emblem_last_updated' => date('Y-m-d H:i:s'),
			'name' => $guild_obj->name,
			'tag' => $guild_obj->tag
		));
	}

}

?>