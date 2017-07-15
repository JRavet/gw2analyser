<?php
/*
id int(11) unsigned AUTO_INCREMENT,
timeStamp datetime,
capture_history_id int(11) unsigned,
num_yaks int(4),
PRIMARY KEY (id),
FOREIGN KEY(capture_history_id) REFERENCES capture_history(id)
ON DELETE CASCADE
*/
class yak_history extends TinyMVC_Model
{
	protected $_table = "yak_history";
	protected $pk = "id";
}

?>