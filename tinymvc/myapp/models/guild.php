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

}

?>