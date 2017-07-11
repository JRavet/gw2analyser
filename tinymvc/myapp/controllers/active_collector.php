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
		private $api;

		public function __construct($region)
		{
			$this->api = new gw2_api($region);
			$this->start_collector();
		}

		public function start_collector()
		{
			$tick_timer = 5.0;
			$sync_data = $this->synchronize(); // initial synchronize
			$sync_data['new_week'] = TRUE; // assume a new week to store new match_details for
			while (true)
			{ // begin looping
				$begin_time = microtime(true); //get the current time in microseconds; used to calculate processing time
				$timeStamp = Date("Y-m-d H:i:s"); //make a unique timestamp to pass to functions that store data with timestamps

				foreach( $this->api->get_matches() as $match )
				{
					if ( $sync_data['new_week'] == TRUE )
					{
						$this->store_match_details($match);
					}
					$this->store_activity_data($match, $timeStamp, $tick_timer);
					$this->store_scores($match, $tick_timer, $timeStamp);
				} // END foreach

				if ( $sync_data['store_data'] === TRUE )
				{
					$sync_data['store_data'] = FALSE;
				}

				$diff = (microtime(true) - $begin_time)*SECONDS;
				$idle_time = ((30*SECONDS) - $diff);

				if ($tick_timer == 0.5 || $diff > (30*SECONDS))
				{
					$sync_data = $this->synchronize($sync_data, $diff);
					$idle_time = 1;
					$tick_timer = 5.5;
				}

				usleep($idle_time);
				$tick_timer -= 0.5;

				if ($tick_timer <= 0)
				{
					$tick_timer = 5;
				}
			} // end looping
		}
		private function synchronize()
		{
			// to fix off-by-a-second issues:
			// for loop over the data
			// if !isset($data[$tier]) then compare scores; if tick happened, $data[$tier]=...
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
	if ( !in_array($argv[1], array(1,2)) )
	{
		echo "Invalid region specified: " . $argv[1] . "\nExiting.\n";
		exit;
	}
	$tmvc->main('active_collector', null, $argv[1]); // start the active collector
}
?>