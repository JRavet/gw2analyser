<?php
/*
id int(11) unsigned AUTO_INCREMENT,
match_detail_id int(11) unsigned,
timeStamp datetime,
last_flipped datetime,
obj_id  varchar(10),
owner_server int(4),
owner_color enum("Blue","Red","Green","Neutral"),
tick_timer float(3,1),
num_yaks int(4),
duration_owned time,
PRIMARY KEY(id),
FOREIGN KEY(obj_id) REFERENCES objective(obj_id),
FOREIGN KEY(match_detail_id) REFERENCES match_detail(id)
ON DELETE CASCADE
*/
class capture_history extends TinyMVC_Model
{
	protected $_table = "capture_history";
	protected $pk = "id";
}

?>