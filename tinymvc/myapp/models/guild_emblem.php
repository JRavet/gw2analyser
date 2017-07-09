<?php
/*
id int(11) unsigned AUTO_INCREMENT,
guild_id varchar(60),
background_id int(5),
foreground_id int(5),
flags varchar(255),
background_color_id int(5),
foreground_primary_color_id int(5),
foreground_secondary_color_id int(5),
PRIMARY KEY(id),
FOREIGN KEY (guild_id) REFERENCES guild (guild_id)
ON DELETE CASCADE
*/
class guild_emblem extends TinyMVC_Model
{

}

?>