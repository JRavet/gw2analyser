<?php
/*
id int(4),
name varchar(64),
description text,
icon text,
tactic_slot tinyint(1),
PRIMARY KEY(id)
*/
class objective_upgrade extends TinyMVC_Model
{

	protected $_table = "objective_upgrade";
	protected $pk = 'id';

	private function get_tactic_slot($id)
	{
		switch($id)
		{
			/*
				[1] [2]
				[3] [4]
				[5] [6]
			*/
			case 147: return 4; // Iron Guards
			case 168: return 6; // Cloaking Waters
			case 178: return 5; // Emergency Waypoint
			case 183: return 6; // Auto Turrets
			case 222: return 1; // Dune Roller
			case 298: return 5; // Airship Defense
			case 306: return 2; // Sabatoge Depot
			case 307: return 2; // Armored Dolyaks
			case 329: return 4; // Hardened Siege
			case 345: return 3; // Centaur Banner
			case 365: return 2; // Packed Dolyaks
			case 383: return 1; // Invulnerable Dolyaks
			case 389: return 2; // Hardened Gates
			case 399: return 3; // Turtle Banner
			case 418: return 6; // Presence of the Keep
			case 483: return 2; // Minor Supply Drop
			case 513: return 5; // Invulnerable Fortifications
			case 559: return 1; // Chilling Fog
			case 562: return 2; // Speedy Dolyaks
			case 583: return 6; // Watchtower
			case 590: return 3; // Dragon Banner
			default: return -1; // null
		}
	}

	public function store_upgrades()
	{
		$guild_upgrades = (new gw2_api())->get_guild_upgrades();

		foreach ($guild_upgrades as $upgrade)
		{
			if ( $upgrade->type === "Claimable" )
			{

				$tactic_slot = $this->get_tactic_slot($upgrade->id);

				$this->save(array(
					'id' => $upgrade->id,
					'name' => $upgrade->name,
					'description' => $upgrade->description,
					'icon' => $upgrade->icon,
					'tactic_slot' => $tactic_slot
				));
			}
		}
		for ($i = 1; $i < 4; $i++)
		{ // the tier-status of objectives
			$this->save(array(
				'id' => $i,
				'name' => "Tier " . $i,
				'description' => 'Objective reached tier ' . $i,
				'icon' => null,
				'tactic_slot' => 0
			));
		}
	}
}

?>