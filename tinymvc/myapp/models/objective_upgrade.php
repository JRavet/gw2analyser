<?php
/*
id int(4),
name varchar(64),
description text,
icon text,
PRIMARY KEY(id)
*/
class objective_upgrade extends TinyMVC_Model
{

	protected $_table = "objective_upgrade";
	protected $pk = 'id';

	function store_upgrades()
	{
	    $guild_upgrades = (new gw2_api())->get_guild_upgrades();

        foreach ($guild_upgrades as $upgrade)
        {
        	if ( $upgrade->type === "Claimable" )
        	{
	        	$this->save(array(
	        		'id' => $upgrade->id,
	    			'name' => $upgrade->name,
	    			'description' => $upgrade->description,
	    			'icon' => $upgrade->icon        			
	        	));
        	}
        }
        for ($i = 1; $i < 4; $i++)
        { // the tier-status of objectives
	    	$this->save(array(
	    		'id' => $i,
	    		'name' => "Tier " . $i,
	    		'description' => 'Objective reached tier ' . $i,
	    		'icon' => null
	    	));
    	}
	}
}

?>