<?php
/*
id int(11) unsigned AUTO_INCREMENT,
from_obj varchar(10),
to_obj varchar(10),
estimated_travel_time float(3,1),
PRIMARY KEY (id),
FOREIGN KEY (from_obj) REFERENCES objective(obj_id),
FOREIGN KEY (to_obj) REFERENCES objective(obj_id)
*/
class supply_route extends TinyMVC_Model
{

}

?>