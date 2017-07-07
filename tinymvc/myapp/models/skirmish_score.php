<?php
/*
match_detail_id int(11) unsigned,
timeStamp datetime,
skirmish_number int(2),
red_skirmish_score int(3),
blue_skirmish_score int(3),
green_skirmish_score int(3),
PRIMARY KEY(match_detail_id,timeStamp),
FOREIGN KEY(match_detail_id) REFERENCES match_detail(id)
ON DELETE CASCADE
*/
class skirmish_score extends TinyMVC_Model
{

}

?>