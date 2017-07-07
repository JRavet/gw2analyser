<?php
/*
match_detail_id int(11) unsigned,
server_id int(4),
server_color enum("Red","Blue","Green"),
server_lead boolean,
server_population varchar(15),
PRIMARY KEY(match_detail_id, server_id),
FOREIGN KEY(match_detail_id) REFERENCES match_detail(id)
ON DELETE CASCADE,
FOREIGN KEY(server_id) REFERENCES server_info(server_id)
*/
class server_linking extends TinyMVC_Model
{

}

?>