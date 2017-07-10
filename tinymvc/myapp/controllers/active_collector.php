<?php
require_once("../../../htdocs/scripts.php"); // load the framework
//
DEFINE('SECONDS', 1000000); // number of microseconds in a second
date_default_timezone_set("UTC"); // set all timestamps' formats to universal time
error_reporting(E_ERROR); // explicit error reporting enabled
//
$collector_started = false; // a hack to make this file load the framework AND execute itself
if (class_exists('Active_Collector_Controller', false) === false)
{ // only create the class once; necessary because calling this file will ...
	// define the class when loading the framework, and then it is defined again due to calling this file
	class Active_Collector_Controller extends TinyMVC_Controller
	{
		public function start_collector()
		{
			$this->synchronize(); // initial synchro
			// begin looping
				$this->store_match_details();
				$this->store_activity_data();
				$this->store_scores();
				$this->synchronize(); // looping synchro
			// end looping
			return null;
		}
		private function synchronize()
		{
			echo "synchronized\n";
		}
		private function store_scores()
		{
			echo "stored scores\n";
			$this->calculate_ppt();
			$this->store_skirmish_scores();
		}
		private function calculate_ppt()
		{
			echo "calculated ppt\n";
		}
		private function store_skirmish_scores()
		{
			echo "stored skirmish scores\n";
		}
		private function check_guild_claim()
		{
			echo "guild claimed\n";
		}
		private function store_activity_data()
		{
			$this->check_objective_upgrades();
			$this->check_guild_claim();
			$this->estimate_yaks_delivered();
			$this->get_server_owner();
			echo "stored activity data\n";
		}
		private function check_objective_upgrades()
		{
			echo "checked objective upgrades\n";
		}
		private function get_server_owner()
		{
			echo "got server owner\n";
		}
		private function estimate_yaks_delivered()
		{
			echo "estimated yaks delivered\n";
		}
		private function store_match_details()
		{
			echo "stored match details\n";
			$this->store_server_linkings();
		}
		private function store_server_linkings()
		{
			echo "stored server linkings\n";
		}
		private function log_message()
		{
			echo "logged a message\n";
		}
	} // END CLASS active collector
	$collector_started = true; // a hack to make this file load the framework AND execute itself
} // END if not-class-exists
if ($collector_started === true) { // a hack to make this file load the framework AND execute itself
	if ( !in_array($argv[1],array(1,2)) )
	{
		echo "Invalid region specified: " . $argv[1] . "\nExiting.\n";
		exit;
	}
	DEFINE('REGION', $argv[1]); // define region as a constant for the rest of the script
	$tmvc->main('active_collector', 'start_collector'); // start the active collector
}
?>