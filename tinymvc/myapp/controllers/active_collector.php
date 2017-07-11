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
			$this->main_loop(); // start the collector
		}

		/**
		 * The main driver logic of the active collector
		 *
		**/
		private function main_loop()
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
					$this->store_capture_history($match, $tick_timer, $timeStamp);
					$this->store_scores($match, $tick_timer, $timeStamp);
				} // END foreach

				if ( $sync_data['new_week'] === TRUE )
				{ // if new match-details were stored, don't do it again
					$sync_data['new_week'] = FALSE;
				}

				$diff = (microtime(true) - $begin_time)*SECONDS;
				$idle_time = ((30*SECONDS) - $diff);

				if ($tick_timer == 0.5 || $diff > (30*SECONDS))
				{
					// TODO if diff > TIME, apply corrections but do not force resync
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
		private function synchronize($sync_data, $diff) //TODO rename $diff
		{
			if ($diff >= (30*SECONDS))
			{ //if the processing time was over 30 seconds, no need to idle before syncing
				$sync_data['sync_wait'] = FALSE; //just to ensure it doesn't wait extra time
			}

			if ( $sync_data['sync_wait'] === TRUE && $diff < (25*SECONDS) )
			{ //if there should be an initial delay, and the processing-time wasnt too long, idle for some time
				usleep(23*SECONDS - $diff); //sleep for a combined (processing+idle) time of 25 seconds
			}

			$score_data = $this->api->get_matches();
			//TODO set prev_score[$tiers] here
			usleep(2*SECONDS); // wait 2 seconds so the score data just collected will be different

			while (TRUE)
			{
				$matches = $this->api->get_matches();

				// TODO set current_score[$tiers] here

				//$current_score = $matches[0]->scores->green + $matches[0]->scores->blue + $matches[0]->scores->red;

				// TODO foreach compare by [$tiers] here
				// $current_score >= ($prev_score+230)
				// array of boolean values by [$tier] if $tier_ready
				if ($all_tiers_ready == TRUE) // TODO <-- is shorthand
				{
					$new_start_time = $matches[0]->start_time;

					if ($new_start_time != $sync_data["prev_start_time"])
					{
						$new_week = TRUE;
					}

					return array(
						"new_week" => $new_week,
						"prev_start_time" => $new_start_time,
						"sync_wait" => TRUE
					);
				} // END if all_tiers_ready == TRUE

				$prev_score = $current_score;

				usleep(2*SECONDS);
			} // END while
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
		private function store_claim_history()
		{
			echo "guild claimed\n";
		}
		private function store_capture_history()
		{
			$this->get_server_owner();
			$this->estimate_yaks_delivered();
			$this->store_claim_history();
			$this->store_upgrade_history();
			echo "stored activity data\n";
		}
		private function store_upgrade_history()
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