<?php
/*
id int(11) unsigned AUTO_INCREMENT,
match_detail_id int(11) unsigned,
timeStamp datetime,
last_flipped datetime,
obj_id  varchar(10),
owner_server int(4),
tick_timer float(3,1),
owner_color enum("Blue","Red","Green","Neutral"),
num_yaks_est int(3),
num_yaks int(3),
duration_owned time,
PRIMARY KEY(id),
FOREIGN KEY(obj_id) REFERENCES objective(obj_id)
ON DELETE CASCADE,
FOREIGN KEY(match_detail_id) REFERENCES match_detail(id)
ON DELETE CASCADE
*/
class capture_history extends TinyMVC_Model
{

}

?>