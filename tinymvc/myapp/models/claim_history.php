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
}

?>