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
		private $helper;
		private $match_detail, $server_linking; // models

		public function __construct()
		{
			parent::__construct();
			tmvc::instance()->controller = $this; // must set framework's controller to this
			$this->api = new gw2_api(MATCH_ID);
			$this->helper = new helper();
			$this->match_detail = new match_detail();
			$this->server_linking = new server_linking();
			$this->main_loop(); // start the collector
		}

		/**
		 * The main driver logic of the active collector
		**/
		private function main_loop()
		{
			$tick_timer = 5.0;
			$sync_data = $this->synchronize(); // initial synchronize
			$sync_data['new_week'] = TRUE; // assume a new week to store new match_details for
			while (true)
			{ // begin looping
				$begin_time = microtime(true); // get the current time in microseconds; used to calculate processing time
				$timeStamp = Date("Y-m-d H:i:s"); // make a unique timestamp to pass to functions that store data with timestamps

				$match = $this->api->get_match_data();

				if ( $sync_data['new_week'] == TRUE )
				{ // if the match->start_times differed during sync, new matchups! Store 'em
					$this->store_match_details($match);
				}

				$this->store_capture_history($match, $tick_timer, $timeStamp);
				$this->store_scores($match, $tick_timer, $timeStamp);

				if ( $sync_data['new_week'] === TRUE )
				{ // if new match-details were stored, don't do it again
					$sync_data['new_week'] = FALSE;
				}

				$processing_time = (microtime(true) - $begin_time)*SECONDS;
				$idle_time = ((30*SECONDS) - $processing_time);

				if ( $tick_timer == 0.5 || $processing_time > (30*SECONDS) )
				{
					// TODO if processing_time > TIME, apply corrections but do not force resync
					$sync_data = $this->synchronize($sync_data, $processing_time);
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
		} // END FUNCTION main_loop
		private function synchronize($sync_data, $processing_time)
		{
			if ($processing_time >= (30*SECONDS))
			{ // if the processing time was over 30 seconds, no need to idle before syncing
				$sync_data['sync_wait'] = FALSE; //just to ensure it doesn't wait extra time
			}

			if ( $sync_data['sync_wait'] === TRUE && $processing_time < (25*SECONDS) )
			{ // if there should be an initial delay, and the processing-time wasnt too long, idle for some time
				usleep(24*SECONDS - $processing_time); // sleep for a combined (processing+idle) time of 25 seconds
			}

			$prev_match = $this->api->get_scores();

			usleep(1*SECONDS); // wait 2 seconds so the score data just collected will be processing_timeerent

			while (TRUE)
			{
				$current_match = $this->api->get_scores();

				$current_score = $current_match->scores->red + $current_match->scores->blue + $current_match->scores->green;
				$prev_score = $prev_match->scores->red + $prev_match->scores->blue + $prev_match->scores->green;

				if ( $current_score >= ($prev_score + 200) )
				{ // and a tick did occur
					break; // done syncing
				}

				$prev_match = $current_match; // get ready to compare the next set of data

				usleep(1*SECONDS);
			}

			$new_start_time = $current_match->start_time;

			if ($new_start_time != $sync_data["prev_start_time"])
			{ // start-times differ = reset occurred, new matchups!
				$new_week = TRUE;
			}

			return array(
				"new_week" => $new_week,
				"prev_start_time" => $new_start_time,
				"sync_wait" => TRUE // always do an extra sync-delay after the initial no-wait sync
			);
		} // END FUNCTION sychronize
		private function store_scores()
		{
			echo "stored scores\n";
			$this->helper->calculate_ppt();
			$this->store_skirmish_scores();
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
			$this->helper->get_server_owner();
			$this->helper->estimate_yaks_delivered();
			$this->store_claim_history();
			$this->store_upgrade_history();
			echo "stored activity data\n";
		}
		private function store_upgrade_history()
		{
			echo "checked objective upgrades\n";
		}

		/**
		 * Checks the database if the current match has already been stored or not
		 * Stores the new match-detail data if it hasn't been already
		 *
		 * @param $match - the full match-data object
		 * @return void
		**/
		private function store_match_details($match)
		{
			$is_stored = $this->match_detail->is_stored(array(
				"match_id" => $match->id,
				"start_time" => $match->start_time
			));

			if ( !is_array($is_stored) )
			{ // if the data was not present in the DB, save it now
				$match_detail_id = $this->match_detail->save(array(
					"match_id" => $match->id,
					"week_num" => $this->helper->get_week_num($match->start_time),
					"start_time" => $match->start_time,
					"end_time" => $match->end_time
				));
				// then store the server-linkings for this match-detail
				$this->store_server_linkings($match, $match_detail_id);
			}
		} // END FUNCTION store_match_details
		/**
		 * Stores all servers involved in the given match
		 * Indicates what color they are and which server is the leader of that color, for a given match
		 *
		 * @param $match - full match-data object
		 * @param $match_detail_id - internal id of the match-detail that was just stored
		 * @return void
		**/
		private function store_server_linkings($match, $match_detail_id)
		{
			$lead_worlds = json_decode(json_encode($match->worlds), true); // turns the object into an array
			foreach ($match->all_worlds as $color=>$servers)
			{ // loop through all worlds in the match by their color; each value is another array
				foreach($servers as $server_id)
				{ // loop through the array of worlds, singling out each server
					$lead = 0; // assume server is not a leader
					if ( in_array($server_id, $lead_worlds) )
					{ // if this servers's id is in the list of leading worlds, set a bit to identify it as such
						$lead = 1;
					}

					$population = $this->api->get_server_population($server_id);

					$this->server_linking->save(array(
						"match_detail_id" => $match_detail_id,
						"server_id" => $server_id,
						"server_color" => $color,
						"server_lead" => $lead,
						"server_population" => $population
					));
				}
			}
		} // END FUNCTION store_server_linkings
	} // END CLASS active collector
	$collector_started = true; // a hack to make this file load the framework AND execute itself
} // END if not-class-exists
if ($collector_started === true) { // a hack to make this file load the framework AND execute itself
	if ( !isset($argv[1]) )
	{
		echo "Invalid match specified: " . $argv[1] . "\nExiting.\n";
		exit;
	}
	DEFINE(MATCH_ID, $argv[1]);
	$tmvc->main('active_collector', null); // start the active collector
}
?>