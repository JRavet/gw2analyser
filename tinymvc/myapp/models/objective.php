<?php
/*
obj_id varchar(10),
name varchar(40),
ppt_base int(2),
type varchar(10),
sector_id varchar(8),
map_id varchar(8),
map_type varchar(50),
coordX float(8,2),
coordY float(8,2),
coordZ float(8,2),
label_coordX float(8,2),
label_coordY float(8,2),
marker varchar(255),
compass_direction varchar(5),
chat_link varchar(20),
PRIMARY KEY (obj_id)
*/
class objective extends TinyMVC_Model
{
	protected $_table = "objective";
	protected $pk = 'obj_id';

	function store_objectives()
	{
		$objectives = (new gw2_api())->get_objectives();

		foreach ($objectives as $objective)
		{
			$this->save(array(
				'obj_id' => $objective->id,
				'name' => $objective->name,
				'ppt_base' => $this->get_base_ppt($objective->type),
				'type' => $objective->type,
				'sector_id' => $objective->sector_id,
				'map_id' => $objective->map_id,
				'map_type' => $objective->map_type,
				'coordX' => isset($objective->coord) ? $objective->coord[0] : 0,
				'coordY' => isset($objective->coord) ? $objective->coord[1] : 0,
				'coordZ' => isset($objective->coord) ? $objective->coord[2] : 0,
				'label_coordX' => isset($objective->label_coord) ? $objective->label_coord[0] : 0,
				'label_coordY' => isset($objective->label_coord) ? $objective->label_coord[1] : 0,
				'marker' => isset($objective->marker) ? $objective->marker : '',
				'compass_direction' => $this->get_compass_direction($objective),
				'chat_link' => $objective->chat_link
			));
		}
	} // END FUNCTION store_objectives

	private function get_compass_direction($objective)
	{
		if (preg_match("/The /",$objective->name))
		{
			return "N";
		}
		elseif (preg_match("/[a-z]*lake/",$objective->name))
		{
			return "SE";
		}
		elseif (preg_match("/water/",$objective->name))
		{
			return "SE";
		}
		elseif (preg_match("/-34/",$objective->id))
		{
			return "S";
		}
		elseif (preg_match("/-40/",$objective->id))
		{
			return "NE";
		}
		elseif (preg_match("/-51/",$objective->id))
		{
			return "NE";
		}
		elseif (preg_match("/-38/",$objective->id))
		{
			return "NW";
		}
		elseif (preg_match("/52/",$objective->id))
		{
			return "NW";
		}
		elseif (preg_match("/vale/",$objective->name))
		{
			return "SW";
		}
		elseif (preg_match("/briar/",$objective->name))
		{
			return "SW";
		}
		elseif (preg_match("/Necropolis/",$objective->name))
		{
			return "NE";
		}
		else if (preg_match("/'s Refuge/",$objective->name))
		{
			return "NE";
		}
		else if (preg_match("/Palace/",$objective->name))
		{
			return "E";
		}
		else if (preg_match("/Farmstead/",$objective->name))
		{
			return "SE";
		}
		else if (preg_match("/Depot/",$objective->name))
		{
			return "SE";
		}
		else if (preg_match("/Well/",$objective->name))
		{
			return "S";
		}
		else if (preg_match("/Outpost/",$objective->name))
		{
			return "SW";
		}
		else if (preg_match("/Encampment/",$objective->name))
		{
			return "SW";
		}
		else if (preg_match("/Undercroft/",$objective->name))
		{
			return "W";
		}
		else if (preg_match("/Hideaway/",$objective->name))
		{
			return "NW";
		}
		else if (preg_match("/Academy/",$objective->name))
		{
			return "NW";
		}
		else if (preg_match("/Lab/",$objective->name))
		{
			return "N";
		}
		else if (preg_match("/Rampart/",$objective->name))
		{
			return "N";
		}
		else
		{
			return "";
		}
	} // END FUNCTION get_cardinal_direction

	private function get_base_ppt($type)
	{
		if ($type == "Camp")
		{
			return 2;
		}
		elseif($type == "Tower")
		{
			return 4;
		}
		elseif($type == "Keep")
		{
			return 8;
		}
		elseif($type == "Castle")
		{
			return 12;
		}
	}
}

?>