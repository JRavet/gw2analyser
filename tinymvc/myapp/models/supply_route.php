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

	protected $_table = "supply_route";
	protected $pk = 'id';

	public function store_supply_routes()
	{
		$routes = array(
		#BEGIN GREEN BL
			#BEGIN S CAMP
				array('from_obj' => '95-34', 'to_obj' => '95-33', 'estimated_travel_time' => 5), #TO BAY
				array('from_obj' => '95-34', 'to_obj' => '95-32', 'estimated_travel_time' => 5), #TO HILLS
				array('from_obj' => '95-34', 'to_obj' => '95-35', 'estimated_travel_time' => 5), #TO SWT
				array('from_obj' => '95-34', 'to_obj' => '95-36', 'estimated_travel_time' => 5), #TO SET
			#END S CAMP
			#BEGIN SE CAMP
				array('from_obj' => '95-50', 'to_obj' => '95-32', 'estimated_travel_time' => 5), #TO HILLS
				array('from_obj' => '95-50', 'to_obj' => '95-36', 'estimated_travel_time' => 5), #TO SET
			#END SE CAMP
			#BEGIN SW CAMP
				array('from_obj' => '95-53', 'to_obj' => '95-33', 'estimated_travel_time' => 5), #TO BAY
				array('from_obj' => '95-53', 'to_obj' => '95-35', 'estimated_travel_time' => 5), #TO SWT
			#END SW CAMP
			#BEGIN NW CAMP
				array('from_obj' => '95-52', 'to_obj' => '95-37', 'estimated_travel_time' => 7), #TO GARRI
				array('from_obj' => '95-52', 'to_obj' => '95-33', 'estimated_travel_time' => 7), #TO BAY
			#END NW CAMP
			#BEGIN NE CAMP
				array('from_obj' => '95-51', 'to_obj' => '95-37', 'estimated_travel_time' => 7), #TO GARRI
				array('from_obj' => '95-51', 'to_obj' => '95-32', 'estimated_travel_time' => 7), #TO HILLS
			#END NE CAMP
			#BEGIN N CAMP
				array('from_obj' => '95-39', 'to_obj' => '95-38', 'estimated_travel_time' => 5), #TO NWT
				array('from_obj' => '95-39', 'to_obj' => '95-40', 'estimated_travel_time' => 5), #TO NET
				array('from_obj' => '95-39', 'to_obj' => '95-37', 'estimated_travel_time' => 7), #TO GARRI
			#END N CAMP
		#END GREEN BL
		#BEGIN BLUE BL
			#BEGIN S CAMP
				array('from_obj' => '96-34', 'to_obj' => '96-33', 'estimated_travel_time' => 5), #TO BAY
				array('from_obj' => '96-34', 'to_obj' => '96-32', 'estimated_travel_time' => 5), #TO HILLS
				array('from_obj' => '96-34', 'to_obj' => '96-35', 'estimated_travel_time' => 5), #TO SWT
				array('from_obj' => '96-34', 'to_obj' => '96-36', 'estimated_travel_time' => 5), #TO SET
			#END S CAMP
			#BEGIN SE CAMP
				array('from_obj' => '96-50', 'to_obj' => '96-32', 'estimated_travel_time' => 3), #TO HILLS
				array('from_obj' => '96-50', 'to_obj' => '96-36', 'estimated_travel_time' => 3), #TO SET
			#END SE CAMP
			#BEGIN SW CAMP
				array('from_obj' => '96-53', 'to_obj' => '96-33', 'estimated_travel_time' => 3), #TO BAY
				array('from_obj' => '96-53', 'to_obj' => '96-35', 'estimated_travel_time' => 3), #TO SWT
			#END SW CAMP
			#BEGIN NW CAMP
				array('from_obj' => '96-52', 'to_obj' => '96-37', 'estimated_travel_time' => 4), #TO GARRI
				array('from_obj' => '96-52', 'to_obj' => '96-33', 'estimated_travel_time' => 2), #TO BAY
			#END NW CAMP
			#BEGIN NE CAMP
				array('from_obj' => '96-51', 'to_obj' => '96-37', 'estimated_travel_time' => 5), #TO GARRI
				array('from_obj' => '96-51', 'to_obj' => '96-32', 'estimated_travel_time' => 2), #TO HILLS
			#END NE CAMP
			#BEGIN N CAMP
				array('from_obj' => '96-39', 'to_obj' => '96-38', 'estimated_travel_time' => 2.5), #TO NWT
				array('from_obj' => '96-39', 'to_obj' => '96-40', 'estimated_travel_time' => 3.5), #TO NET
				array('from_obj' => '96-39', 'to_obj' => '96-37', 'estimated_travel_time' => 6), #TO GARRI
			#END N CAMP
		#END BLUE BL
		#BEGIN RED BL
			#BEGIN N CAMP
				array('from_obj' => '1099-99', 'to_obj' => '1099-104', 'estimated_travel_time' => 4.25), #TO NET
				array('from_obj' => '1099-99', 'to_obj' => '1099-102', 'estimated_travel_time' => 3), #TO NWT
				array('from_obj' => '1099-99', 'to_obj' => '1099-113', 'estimated_travel_time' => 1), #TO EARTH
			#END N CAMP
			#BEGIN NE CAMP
				array('from_obj' => '1099-109', 'to_obj' => '1099-114', 'estimated_travel_time' => 2), #TO AIR
				array('from_obj' => '1099-109', 'to_obj' => '1099-104', 'estimated_travel_time' => 3), #TO NET
				array('from_obj' => '1099-109', 'to_obj' => '1099-113', 'estimated_travel_time' => 4), #TO EARTH
			#END NE CAMP
			#BEGIN NW CAMP
				array('from_obj' => '1099-115', 'to_obj' => '1099-106', 'estimated_travel_time' => 2), #TO FIRE
				array('from_obj' => '1099-115', 'to_obj' => '1099-102', 'estimated_travel_time' => 3), #TO NWT
				array('from_obj' => '1099-115', 'to_obj' => '1099-113', 'estimated_travel_time' => 4.5), #TO EARTH
			#END NW CAMP
			#BEGIN SW CAMP
				array('from_obj' => '1099-101', 'to_obj' => '1099-110', 'estimated_travel_time' => 3), #TO SWT
				array('from_obj' => '1099-101', 'to_obj' => '1099-106', 'estimated_travel_time' => 2.25), #TO FIRE
			#END SW CAMP
			#BEGIN SE CAMP
				array('from_obj' => '1099-100', 'to_obj' => '1099-105', 'estimated_travel_time' => 2.25), #TO SET
				array('from_obj' => '1099-100', 'to_obj' => '1099-114', 'estimated_travel_time' => 3), #TO AIR
			#END SE CAMP
			#BEGIN S CAMP
				array('from_obj' => '1099-116', 'to_obj' => '1099-110', 'estimated_travel_time' => 2.5), #TO SWT
				array('from_obj' => '1099-116', 'to_obj' => '1099-105', 'estimated_travel_time' => 3), #TO SET
			#END S CAMP
		#END RED BL
		#BEGIN EB
			#BEGIN ROGUES QUARRY
				array('from_obj' => '38-10', 'to_obj' => '38-3', 'estimated_travel_time' => 5), #TO GREEN KEEP
				array('from_obj' => '38-10', 'to_obj' => '38-11', 'estimated_travel_time' => 5), #TO ALDONS
				array('from_obj' => '38-10', 'to_obj' => '38-12', 'estimated_travel_time' => 3.5), #TO WILDCREEK
				array('from_obj' => '38-10', 'to_obj' => '38-9', 'estimated_travel_time' => 5), #TO SMC
			#END ROGUES QUARRY
			#BEGIN GOLANTA
				array('from_obj' => '38-4', 'to_obj' => '38-3', 'estimated_travel_time' => 5), #TO GREEN KEEP
				array('from_obj' => '38-4', 'to_obj' => '38-14', 'estimated_travel_time' => 5), #TO KLOVAN
				array('from_obj' => '38-4', 'to_obj' => '38-13', 'estimated_travel_time' => 5), #TO JERRIS
				array('from_obj' => '38-4', 'to_obj' => '38-9', 'estimated_travel_time' => 5), #TO SMC
			#END GOLANTA
			#BEGIN SPELDANS CLEARCUT
				array('from_obj' => '38-6', 'to_obj' => '38-1', 'estimated_travel_time' => 4), #TO RED KEEP
				array('from_obj' => '38-6', 'to_obj' => '38-18', 'estimated_travel_time' => 5), #TO ANZ
				array('from_obj' => '38-6', 'to_obj' => '38-17', 'estimated_travel_time' => 3.5), #TO MENDONS
				array('from_obj' => '38-6', 'to_obj' => '38-9', 'estimated_travel_time' => 5), #TO SMC
			#END SPELDANS CLEARCUT
			#BEGIN PANGLOSS RISE
				array('from_obj' => '38-5', 'to_obj' => '38-1', 'estimated_travel_time' => 3.5), #TO RED KEEP
				array('from_obj' => '38-5', 'to_obj' => '38-20', 'estimated_travel_time' => 3), #TO VELOKA
				array('from_obj' => '38-5', 'to_obj' => '38-19', 'estimated_travel_time' => 3), #TO OGREWATCH
				array('from_obj' => '38-5', 'to_obj' => '38-9', 'estimated_travel_time' => 6), #TO SMC
			#END PANGLOSS RISE
			#BEGIN UMBERGLADE WOODS
				array('from_obj' => '38-8', 'to_obj' => '38-2', 'estimated_travel_time' => 5), #TO BLUE KEEP
				array('from_obj' => '38-8', 'to_obj' => '38-21', 'estimated_travel_time' => 5), #TO DURIOUS
				array('from_obj' => '38-8', 'to_obj' => '38-22', 'estimated_travel_time' => 4), #TO BRAVOST
				array('from_obj' => '38-8', 'to_obj' => '38-9', 'estimated_travel_time' => 5), #TO SMC
			#END UMBERGLADE WOODS
			#BEGIN DANELON PASSAGE
				array('from_obj' => '38-7', 'to_obj' => '38-2', 'estimated_travel_time' => 4), #TO BLUE KEEP
				array('from_obj' => '38-7', 'to_obj' => '38-15', 'estimated_travel_time' => 3.25), #TO LANGOR
				array('from_obj' => '38-7', 'to_obj' => '38-16', 'estimated_travel_time' => 2.75), #TO QUENTIN
				array('from_obj' => '38-7', 'to_obj' => '38-9', 'estimated_travel_time' => 5) #TO SMC
			#END DANELON PASSAGE
		#END EB
		);

		foreach ($routes as $route)
		{
			$this->save($route);
		}
	}
}

?>