<?php
/*
from_obj varchar(10),
to_obj varchar(10),
estimated_travel_time float(3,1),
PRIMARY KEY (from_obj, to_obj),
FOREIGN KEY (from_obj) REFERENCES objective(obj_id)
ON DELETE CASCADE,
FOREIGN KEY (to_obj) REFERENCES objective(obj_id)
ON DELETE CASCADE
*/
class supply_route extends TinyMVC_Model
{

}

?>