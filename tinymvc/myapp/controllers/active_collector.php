<?php
require_once("../../../scripts.php"); // load the framework
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
		private $match_detail, $server_linking, $capture_history,
			$claim_history, $upgrade_history, $yak_history, $guild,
			$guild_emblem, $objective, $map_score, $skirmish_score; // models
		private $guild_cache, $capture_history_cache, $claim_history_cache,
			$upgrade_history_cache, $yak_history_cache, $objective_cache,
			$server_owners;

		/**
		 * Constructor
		 * Also starts the main loop
		**/
		public function __construct()
		{
			parent::__construct();
			tmvc::instance()->controller = $this; // must set framework's controller to this
			$this->helper = new helper(MATCH_ID);
			$this->api = new gw2_api(MATCH_ID, $this->helper);
			$this->match_detail = new match_detail();
			$this->server_linking = new server_linking();
			$this->capture_history = new capture_history();
			$this->claim_history = new claim_history();
			$this->upgrade_history = new upgrade_history();
			$this->yak_history = new yak_history();
			$this->guild = new guild();
			$this->guild_emblem = new guild_emblem();
			$this->objective = new objective();
			$this->map_score = new map_score();
			$this->skirmish_score = new skirmish_score();
			$this->main_loop(); // start the collector
			$this->guild_cache = $this->capture_history_cache = $this->claim_history_cache
				= $this->upgrade_history_cache = $this->yak_history_cache = array(); // initialize caches
		}

		/**
		 * The main driver logic of the active collector
		**/
		private function main_loop()
		{
			$match_detail_id = null; // since this is only set once a week, store it in memory so we don't query DB for it all the time
			$tick_timer = 5.0;
			$this->objective_cache = $this->objective->find(array()); // gets all
			$sync_data = $this->synchronize(); // initial synchronize
			$sync_data['new_week'] = TRUE; // assume a new week to store new match_details for
			while (true)
			{ // begin looping
				$this->helper->log_message(0, "Starting loop; tick_timer=" . $tick_timer);
				$begin_time = microtime(true); // get the current time in microseconds; used to calculate processing time
				$timeStamp = Date("Y-m-d H:i:s"); // make a unique timestamp to pass to functions that store data with timestamps

				$match = $this->api->get_match_data();

				if ( $sync_data['new_week'] == TRUE )
				{ // if the match->start_times differed during sync, new matchups! Store 'em
					$match_detail_id = $this->store_match_details($match);
					$sync_data['new_week'] = FALSE;
				}

				$this->store_capture_history($match, $tick_timer, $timeStamp, $match_detail_id);

				if ( $sync_data['save_scores'] === true || $tick_timer == 5 )
				{ // store scores after every point tick
					$this->store_scores($match, $timeStamp, $match_detail_id);
					$sync_data['save_scores'] = NULL; // not true, not false - prevents sync / scores
				}

				$processing_time = (microtime(true) - $begin_time)*SECONDS;
				$idle_time = ((30*SECONDS) - $processing_time);

				if ($processing_time > 30*SECONDS)
				{
					$segments_to_skip = floor( $processing_time / (30*SECONDS) );

					$this->helper->log_message(500, "Too much time elapsed; processing_time=" . $processing_time
						. "; fast-forwarding by " . $segments_to_skip . " 0.5-min intervals" );

					$time_to_sleep = fmod(30 - ($processing_time/SECONDS),30);

					$skipped_5min = false;
					for ($i = 0; $i < $segments_to_skip; $i++)
					{
						$tick_timer -= 0.5;
						if ( $tick_timer <= 0 )
						{ // wrap back to 5 minutes
							$skipped_5min = true;
							$tick_timer = 5;
						}
					}

					if ($skipped_5min === true)
					{ // if we would have skipped the score-taking, do it now
						$this->store_scores( $this->api->get_match_data(), Date("Y-m-d H:i:s"), $match_detail_id );
					}

					$this->helper->log_message(4, $time_to_sleep*SECONDS);
					usleep($time_to_sleep*SECONDS);
				}

				if ( $tick_timer == 0.5 || true ) // $sync_data['save_scores'] === false ) // TODO temp hack - always resync due to API issues
				{ // either the previous sync failed, or its time for a natural sync
					$sync_data = $this->synchronize($sync_data, $processing_time);
					$idle_time = 1; // idle for virtually 0 seconds after resyncing
				}

				$this->helper->log_message(4, $idle_time);

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
			if (TEST_MODE === TRUE)
			{
				return array(
					"new_week" => TRUE,
					"prev_start_time" => NULL,
					"sync_wait" => TRUE, // always do an extra sync-delay after the initial no-wait sync
					"save_scores" => true,
				);
			}
			$begin_time = microtime(true);

			$total_time = $processing_time; // get the number of seconds it took to process


			$this->helper->log_message(1, MATCH_ID);

			if ( $sync_data['sync_wait'] === TRUE && $processing_time < (18*SECONDS) )
			{ // if there should be an initial delay, and the processing-time wasnt too long, idle for some time
				$this->helper->log_message(4, 17*SECONDS - $processing_time);
				usleep(17*SECONDS - $processing_time); // sleep for a combined (processing+idle) time of 21 seconds
			}

			$prev_match = $this->api->get_match_data();

			usleep(1*SECONDS); // wait 2 seconds so the score data just collected will be processing_timeerent

			$total_time += (microtime(true) - $begin_time)*SECONDS;

			while (TRUE)
			{
				$loop_time = microtime(true);

				$current_match = $this->api->get_match_data();

				$current_score = $current_match->scores->red + $current_match->scores->blue + $current_match->scores->green;
				$prev_score = $prev_match->scores->red + $prev_match->scores->blue + $prev_match->scores->green;

				// $this->helper->log_message(0, "Synchronization in progress: prev_score=" . $prev_score . " | current_score=" . $current_score);

				if ( $current_score >= ($prev_score + 250) )
				{ // and a tick did occur
					$save_scores = true; // proper sync - scores ready
					$sync_wait = TRUE;
					$total_time = 30*SECONDS - 1; // don't wait after a proper sync
					break; // done syncing
				}

				$prev_match = $current_match; // get ready to compare the next set of data

				if ($total_time >= (25*SECONDS) && $sync_data['first_sync'] === FALSE) 
				{ // only if it isn't the very first sync, exit if more than ~30 seconds have passed
					$this->helper->log_message(500, "Could not sync within 30 seconds");
					$save_scores = false; // scores didn't sync properly - don't store
					$sync_wait = FALSE; // don't wait next attempted sync
					$total_time += (microtime(true) - $loop_time)*SECONDS;
					break;
				}

				usleep(1*SECONDS);

				$total_time += (microtime(true) - $loop_time)*SECONDS;
			}

			$this->helper->log_message(4, 30*SECONDS - $total_time);
			usleep(30*SECONDS - $total_time); // sleep the remainder of time

			$new_start_time = $current_match->start_time;

			if ($new_start_time != $sync_data["prev_start_time"])
			{ // start-times differ = reset occurred, new matchups!
				$this->helper->log_message(3, MATCH_ID);
				$new_week = TRUE;
			}

			$this->helper->log_message(2, MATCH_ID);

			return array(
				"new_week" => $new_week,
				"prev_start_time" => $new_start_time,
				"sync_wait" => $sync_wait, // always do an extra sync-delay after the initial no-wait sync
				"save_scores" => $save_scores,
				"first_sync" => FALSE
			);
		} // END FUNCTION sychronize
		private function store_scores($match, $timeStamp, $match_detail_id)
		{
			$this->helper->log_message(5, "scores");

			foreach($match->maps as $map)
			{
				$this->helper->calculate_ppt($map, "green");
				$this->map_score->save(array(
					"match_detail_id" => $match_detail_id,
					"timeStamp" => $timeStamp,
					"map_id" => $map->type,
					"greenScore" => $map->scores->green,
					"greenKills" => $map->kills->green,
					"greenDeaths" => $map->deaths->green,
					"green_ppt" => $this->helper->calculate_ppt($map->objectives, "Green"),
					"blueScore" => $map->scores->blue,
					"blueKills" => $map->kills->blue,
					"blueDeaths" => $map->deaths->blue,
					"blue_ppt" => $this->helper->calculate_ppt($map->objectives, "Blue"),
					"redScore" => $map->scores->red,
					"redKills" => $map->kills->red,
					"redDeaths" => $map->deaths->red,
					"red_ppt" => $this->helper->calculate_ppt($map->objectives, "Red")
				));
			} // end foreach->map as map

			$this->store_skirmish_scores($match, $match_detail_id, $timeStamp); // unconditionally call this function - it doesn't duplicate data

			$this->helper->log_message(6, "scores");
		} // END FUNCTION store_scores
		private function store_skirmish_scores($match, $match_detail_id, $timeStamp)
		{
			$this->helper->log_message(5, "skirmish points");

			$skirmish_score_exists = $this->skirmish_score->find_one(array(
				"match_detail_id" => $match_detail_id,
				"red_skirmish_score" => $match->victory_points->red,
				"blue_skirmish_score" => $match->victory_points->blue,
				"green_skirmish_score" => $match->victory_points->green
			));

			if ( !isset($skirmish_score_exists['id']) )
			{
				$current_time = time(); // get the current time
				$match_start = strtotime($match->start_time); // cast the match_start time to a time object
				$skirmish_number = floor(($current_time - $match_start)/7200); // get the number of hours between now and match_start, divided by 2

				$this->skirmish_score->save(array(
					"match_detail_id" => $match_detail_id,
					"skirmish_number" => $skirmish_number,
					"timeStamp" => $timeStamp,
					"red_skirmish_score" => $match->victory_points->red,
					"blue_skirmish_score" => $match->victory_points->blue,
					"green_skirmish_score" => $match->victory_points->green
				));
			}

			$this->helper->log_message(6, "skirmish points");
		} // END FUNCTION store_skirmish_scores
		private function store_capture_history($match, $tick_timer, $timeStamp, $match_detail_id)
		{
			$this->helper->log_message(5, "capture, claim, upgrade and yak history");

			foreach($match->maps as $map)
			{
				foreach($map->objectives as $objective)
				{
					$where = array( // where
						"match_detail_id" => $match_detail_id,
						"obj_id" => $objective->id,
						"last_flipped" => $objective->last_flipped
					);

					$prev_capture_history = $this->helper->check_cache($this->capture_history_cache, $where);

					if (!isset($prev_capture_history['id'])) { // no cached item - query DB for it
						$prev_capture_history = $this->capture_history->find_one(
							$where,
							array( // order by
								"timeStamp" => "DESC"
							)
						);
					}

					$yaks = $objective->yaks_delivered;

					if ( !isset($prev_capture_history['id']) )
					{ // no previous record for this set of data; store a new set
						$data = array(
							"match_detail_id" => $match_detail_id,
							"timeStamp" => $timeStamp,
							"last_flipped" => $objective->last_flipped,
							"obj_id" => $objective->id,
							"owner_server" => $this->server_owners[$objective->owner],
							"owner_color" => $objective->owner,
							"tick_timer" => $tick_timer,
							"num_yaks" => $yaks, // if $yaks == 140 from the api, the next run of code will update using our own calcs
							"duration_owned" => $this->helper->calc_time_interval($objective->last_flipped, $timeStamp)
						);
						// also make an array storing only the id of the last_insert
						$prev_capture_history['id'] = $this->capture_history->save($data);
						$data['id'] = $prev_capture_history['id']; // add the newly-added internal id to the data
						$this->helper->add_to_cache($this->capture_history_cache, $data); // cache it!
					}
					else
					{ // update duration_owned and the number of yaks -- all other data is already set
						if ($yaks == 140) // api only reports up to 140
						{ // when the api caps out the number of yaks, we use our own calculations
							$yaks_est = $this->capture_history->estimate_yaks_delivered($prev_capture_history, $match_detail_id, $this->objective);
							$yaks = ($yaks > $yaks_est) ? $yaks : $yaks_est; // choose whichever is higher
						}
						$this->capture_history->update(
							array( // set
								"num_yaks" => $yaks,
								"duration_owned" => $this->helper->calc_time_interval($objective->last_flipped, $timeStamp)
							),
							array( // where
								"id" => $prev_capture_history['id']
							)
						);
					}

					$obj = $this->helper->check_cache($this->objective_cache, array('obj_id'=>$objective->id));

					if ( in_array( $obj['type'], array("Camp", "Tower", "Keep", "Castle") ) )
					{ // only store following data for claimable objectives
						$this->store_claim_history($objective, $prev_capture_history, $timeStamp);
						$this->store_upgrade_history($objective, $prev_capture_history, $timeStamp);
						$this->store_yak_history($yaks, $prev_capture_history, $timeStamp);
					}
					usleep(SECONDS/15);
				} // end foreach map->objective as objective
			} // end foreach match->maps as map

			$this->helper->log_message(6, "capture, claim, upgrade and yak history");
		} // END FUNCTION store_capture_history
		private function store_claim_history($objective, $capture_history, $timeStamp)
		{
			$where = array("capture_history_id" => $capture_history['id']);

			$prev_claim_history = $this->helper->check_cache($this->capture_history_cache, $where);

			if (!isset($prev_claim_history['id'])) { // not cached
				$prev_claim_history = $this->claim_history->find_one(
					$where,
					array( // order-by
						"id" => "DESC"
					)
				); // get the last known claim-history for the capture-history
			}

			if ( $objective->owner != $capture_history['owner_color']
				|| $objective->claimed_by != $prev_claim_history['claimed_by'] )
			{ // owner-color changed or the guild claiming it changed - store new claim history
				// see if the claiming guild has been stored yet
				$where = array("guild_id" => $objective->claimed_by);
				$guild_exists = $this->helper->check_cache($this->guild_cache, $where);

				if (!isset($guild_exists['guild_id'])) {
					$guild_exists = $this->guild->find_one($where);
					// check if the guild exists in the database
				}

				if ( is_null($guild_exists['guild_id']) )
				{ // guild does not exist at all in db

					$guild = $this->api->get_guild($objective->claimed_by);
					// get the guild data from the api then save it
					$data = array(
						"guild_id" => $guild->guild_id,
						"name" => $guild->guild_name,
						"tag" => $guild->tag,
						"emblem_last_updated" => date("Y-m-d H:i:s")
					);

					$this->guild->save($data);
					$this->helper->add_to_cache($this->guild_cache, $data);

					// make shorthand name for the emblem
					$emblem = $guild->emblem; // returned with the other guild data already
					// save the emblem data

					$this->guild_emblem->save(array(
						"guild_id" => $guild->guild_id,
						"background_id" => $emblem->background_id,
						"foreground_id" => $emblem->foreground_id,
						"flags" => implode("|", $emblem->flags),
						"background_color_id" => $emblem->background_color_id,
						"foreground_primary_color_id" => $emblem->foreground_primary_color_id,
						"foreground_secondary_color_id" => $emblem->foreground_secondary_color_id
					));

				} // end if-guild-not-exists

				if ( (is_null($objective->claimed_by) && !is_null($prev_claim_history['id']))
					|| !is_null($objective->claimed_by) )
				{ // only store null-claims if the objective was claimed prior
					$data = array(
						"capture_history_id" => $capture_history['id'],
						"claimed_by" => $objective->claimed_by, // below: if claimed_at is null, use current timeStamp
						"claimed_at" => ( is_null($objective->claimed_at) ? NULL : $objective->claimed_at )
					);
					$prev_claim_history['id'] = $this->claim_history->save($data);
					$data['id'] = $prev_claim_history['id']; // add internal id to data
					$this->helper->add_to_cache($this->claim_history_cache, $data); // cache it!
				}

			} // end if-need-to-insert claim_history

			// always update claim history - it either existed or was just created
			if ( !is_null($prev_claim_history['id']) )
			{ // if the objective has any claim at all, update duration_claimed
				$this->claim_history->update(
					array( // set
						"duration_claimed" => $this->helper->calc_time_interval($objective->claimed_at, $timeStamp)
					),
					array( // where
						"id" => $prev_claim_history['id']
					)
				);
			}
		} // END FUNCTION store_claim_history
		private function store_upgrade_history($objective, $capture_history, $timeStamp)
		{ // TODO: currently does not account for on-off-on upgrades
			$upgrades = $objective->guild_upgrades;
			$tier = $this->helper->objective_tier($objective->yaks_delivered);
			$upgrades[] = $tier; // append any objective-tier upgrades to the list as well

			foreach ($upgrades as $upgrade)
			{ // see if an entry already exists for this capture_history and upgrade

				$where = array( // where
					"capture_history_id" => $capture_history['id'],
					"upgrade_id" => $upgrade
				);

				$previous_upgrade = $this->helper->check_cache($this->upgrade_history_cache, $where);

				if (!isset($previous_upgrade['id'])) {
					$previous_upgrade = $this->upgrade_history->find_one(
						$where,
						array( // order-by
							"id" => "DESC"
						)
					);
				}

				if ( !isset($previous_upgrade['id']) && $upgrade != 0 )
				{ // if the upgrade was not found in the tables, and it isn't tier 0, save it
					$data = array(
						"timeStamp" => $timeStamp,
						"capture_history_id" => $capture_history['id'],
						"upgrade_id" => $upgrade
					);
					$this->upgrade_history->save($data);
					$this->helper->add_to_cache($this->upgrade_history_cache, $data, 1000);
				}
			} // end foreach upgrades as upgrade
		} // END FUNCTION store_upgrade_history
		private function store_yak_history($yaks, $capture_history, $timeStamp)
		{
			$yaks = ( (int) substr($yaks, 0, -1) ) * 10; // 15 becomes 10, 143 becomes 140

			$where = array(
				"capture_history_id" => $capture_history['id'],
				"num_yaks" => $yaks
			);

			$yaks_stored = $this->helper->check_cache($this->yak_history_cache, $where);

			if (!isset($yaks_stored['id'])) {
				$yaks_stored = $this->yak_history->find_one($where);
				// see if this number of yaks has been stored for this capture-history yet
			}

			if ( !isset($yaks_stored['id']) && $yaks > 0 )
			{ // if it isn't saved yet, save it now, only if it has had any yaks at all
				$data = array(
					"capture_history_id" => $capture_history['id'],
					"num_yaks" => $yaks,
					"timeStamp" => $timeStamp
				);
				$this->yak_history->save($data);
				$this->helper->add_to_cache($this->yak_history_cache, $data, 1000);
			}
		} // END FUNCTION store_yak_history

		/**
		 * Checks the database if the current match has already been stored or not
		 * Stores the new match-detail data if it hasn't been already
		 *
		 * @param $match - the full match-data object
		 * @return void
		**/
		private function store_match_details($match)
		{
			$this->helper->log_message(5, "match details");

			$is_stored = $this->match_detail->find_one(array(
				"match_id" => $match->id,
				"start_time" => $match->start_time
			));

			$match_detail_id = $is_stored['id'];

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

			$this->server_owners = array(
				"Red" => $this->server_linking->get_server_owner($match_detail_id, "red"),
				"Blue" => $this->server_linking->get_server_owner($match_detail_id, "blue"),
				"Green" => $this->server_linking->get_server_owner($match_detail_id, "green"),
				"Neutral" => 0
			);

			$this->helper->log_message(6, "match details");
			return $match_detail_id;
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
			$this->helper->log_message(5, "server links");
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
				} // end foreach $servers
			} // end foreach $match->all_worlds
			$this->helper->log_message(6, "server links");
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
	if ( isset($argv[2]) )
	{
		DEFINE(TEST_MODE, TRUE);
	}
	DEFINE(MATCH_ID, $argv[1]);
	$tmvc->main('active_collector', null); // start the active collector
}
?>