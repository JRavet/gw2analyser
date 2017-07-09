<?php
/*
id int(11) unsigned AUTO_INCREMENT,
match_detail_id int(11) unsigned,
timeStamp datetime,
map_id enum("RedHome","BlueHome","GreenHome","Center"),
greenScore int(6),
blueScore int(6),
redScore int(6),
greenKills int(4),
blueKills int(4),
redKills int(4),
greenDeaths int(4),
blueDeaths int(4),
redDeaths int(4),
green_ppt int(3),
blue_ppt int(3),
red_ppt int(3),
PRIMARY KEY(id),
FOREIGN KEY(match_detail_id) references match_detail(id)
ON DELETE CASCADE
*/
class map_score extends TinyMVC_Model
{

}

?>