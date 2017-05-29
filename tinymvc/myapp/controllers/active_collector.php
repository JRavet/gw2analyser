<?php
require_once("../../../htdocs/scripts.php");
$tmvc->main("data_collector", "active_collector", $argv[1]);

exit;
DEFINE('SECONDS', 1000000);
DEFINE('REGION', $argv[1]);
date_default_timezone_set("UTC");
error_reporting(E_ERROR);
/**
 * This function establishes and returns a connection to the database
 * Loops until it achieves a valid connection
 * @return $conn - connection object for the database
**/
function connect_to_database()
{
	$conn = NULL;
	while (true)
	{
		try
		{
			$conn = new PDO("mysql:host=127.0.0.1;dbname=Gw2Analyserv2", 'gw2datacollector', 'egamirrorimeht');
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$conn->query("SELECT COUNT(*) FROM `log_codes`;"); //sample query which could potentially cause an error -- means no connection
			return $conn;
		}
		catch(PDOException $e)
		{
			echo "CONNECTION ERR: "  . $e->getMessage();
			$conn = NULL;
			usleep(1*SECONDS); //idle for 1 second, then try again
		}
	}
}
/**
 * This function writes a specific message, with optional extra details, to  ...
 * ... a series of files by year-month-and-day folders, and hour-stamped csv files
 * @param $conn - database connection object
 * @param $code - integer; the standardized message to log
 * @param $msg - optional; additional information to go with the standardized log-message
 * @return nothing; writes information to a log file
**/
function log_message($conn,$code,$msg="")
{
	try
	{
		$sql = "SELECT * FROM `log_codes` where id=$code;";
		$details = $conn->query($sql)->fetch();
		$message = date("Y-m-d H:i:s") . "," . $details['id'] . "," . $details['type'] . "," . $details['message'] . "," . $msg . "\n";
	}
	catch(PDOException $e)
	{
		$message = date("Y-m-d H:i:s") . ",-1,database error,A database error has occurred," . "code=$code | details=" . $msg . " | sql=" . $sql . " | stack_trace=" . $e->getMessage();
	}
	finally
	{
 		$dir = "./region-". REGION . "-logs/" . date('Y-m-d');
		$name = "log-" . date("H") . ".csv";
		try
		{
			$file = fopen($dir . "/" . $name,"a"); //append to file
			if ($file === FALSE) throw new Exception("File not found"); //if file did not exist, throw error
		}
		catch (Exception $e)
		{
			mkdir($dir,0770,TRUE); //group & user get all perms; world gets none. recursively create all directories
			$file = fopen($dir . "/" . $name,"a");
		}
		fwrite($file,$message);
		fclose($file);
		if ($code >= 500 || $code == -1)
		{ //if the code is a warning or worse, write to a special log
			$name = "error-log-" . date("H") . ".csv";
			try
			{
				$file = fopen($dir . "/" . $name,"a"); //append to file
				if ($file === FALSE) throw new Exception("File not found"); //if file did not exist, throw error
			}
			catch (Exception $e)
			{
				mkdir($dir,0770,TRUE); //group & user get all perms; world gets none. recursively create all directories
				$file = fopen($dir . "/" . $name,"a");
			}
			fwrite($file,$message);
			fclose($file);
		}
		echo $message;
	}
} //END FUNCTION log_message
/**
 * This function will store all servers (and their links) into the database for the given match/week
 * @param $match - a single match object
 * @param $conn - the database connection to use
 * @return nothing; stores information into database
**/
function store_server_linkings($match, $conn)
{
	log_message($conn,10,"match->id=" . $match->id);
	$lead_worlds = json_decode(json_encode($match->worlds), true); //turns the object into an array
	foreach ($match->all_worlds as $color=>$worlds)
	{ //loop through all worlds in the match by their color; each value is another array
		foreach($worlds as $world)
		{ //loop through the array of worlds, singling out each world
			if (in_array($world,$lead_worlds))
			{ //if this world's id is in the list of leading worlds, set a bit to identify it as such
				$lead = 1;
			}
			else
			{
				$lead = 0;
			}
			$population = json_decode(file_get_contents("https://api.guildwars2.com/v2/worlds?ids=" . $world))[0]->population;
			//get the world's population by querying the world's information and selecting the first element in the one-element array
			try
			{
				log_message($conn,21,"Storing server-linkings for " . $match->id . " | world=" . $world);
				$sql = "INSERT INTO `server_linkings` (match_start, match_id, server_id, server_color, server_lead, server_population)
					VALUES ('$match->start_time', '$match->id', $world, '$color', $lead, '$population');";
				$conn->exec($sql);
				log_message($conn,22,"Stored server-linkings for " . $match->id . " | world=" . $world);
			}
			catch(PDOException $e)
			{
				log_message($conn,-1,"In store_server_linkings: " . $e->getMessage() . " | $sql");
			}
		}
	}
	log_message($conn,11,"match->id=" . $match->id);
} //END FUNCTION store_server_linkings
/**
 * This function will store the over-all details and server linkings for the given match/week
 * @param $match - a single match to store over-all details and server linkings for
 * @param $conn - database connection object
 * @return nothing - stores information into database
**/
function store_match_details($match, $conn)
{
	log_message($conn,8,"match->id=" . $match->id);
	if ($match->start_time == 0)
	{ //if the start-time is 0, something went wrong
		log_message($conn,502,"match->id=" . $match->id);
		$match = json_decode(file_get_contents("https://api.guildwars2.com/v2/wvw/matches?ids=".$match->id))[0];
		//attempt to retrieve only this match, one more time
		if ($match->start_time == 0)
		{ //if its still invalid, just exit this function
			log_message($conn,503,"match->id=" . $match->id);
			return;
		}
	}
	$week = new DateTime($match->start);
	$week = $week->diff(new DateTime("2017-01-01 00:00:00")); //hardcoded time for arbitrary reasons
	$week = (int)(($week->days)/7);
	$sql = "INSERT INTO `match_details` (match_id,week_num,start_time,end_time)
		VALUES ('$match->id',$week,'$match->start_time','$match->end_time');";
	try
	{
		log_message($conn,21,"Storing match details for " . $match->id);
		$conn->exec($sql); //if this entry is a duplicate, store_server_linkings is also skipped due to the exception
		log_message($conn,22,"Stored match details for " . $match->id);
		store_server_linkings($match, $conn);
	}
	catch(PDOException $e)
	{
		log_message($conn,0,"Warning in store-match-details: " . $e->getMessage());
	}
	log_message($conn,9,"match->id=" . $match->id); 
} //END FUNCTION store_match_details
/**
 * This function estimates the given objective's tier based on the following criteria:
 * 		1) Number of eligible supply camps (or objectives, for camps) nearby
 *		2) Duration of ownership for those supply camps (or objectives, for camps)
 *		3) The estimated delivery time for yaks for each route
 * @param objective - the objective (as pulled from the database) to estimate the tier of
 * @param match - the current match data; used to extract the match id and start time
 * @param conn - the database connection object
 * @return "tier" => int - 0, 1, 2 or 3, representing the objective's tier, and "yaks" => int - representing number of yaks
**/
function estimate_yaks_delivered($objective, $match, $conn)
{
	if ($objective['duration_owned'] == null)
	{
		$objective['duration_owned'] = 0;
	}
	if ($objective['type'] == "Camp")
	{
		$select = "to_obj";
		$where = "from_obj";
	}
	elseif ($objective['type'] == "Tower" || $objective['type'] == "Keep" || $objective['type'] == "Castle")
	{
		$select = "from_obj";
		$where = "to_obj";
	}
	else
	{ //ideally this will never happen, as the entry-point to this function also has type-checks
		return "NULL"; //not an objective that can have a tier
	}
	try
	{
		$obj_flipped = $objective['last_flipped'];
		$dur_owned = $objective['duration_owned'];
		$color = $objective['owner_color'];
		$obj_id = $objective['obj_id'];
		$match_id = $match->id;
		$match_start = $match->start_time;
		$sql = "
		SELECT SUM(yaks.yaks) as yaks
		FROM
		(
			SELECT estimated_travel_time, last_flipped, duration_owned,
			CASE
			WHEN #objective in middle of timespan; other-objective capped after objective->last_flipped and held part time
				last_flipped > '$obj_flipped' AND ADDTIME('$obj_flipped','$dur_owned') > ADDTIME(last_flipped,duration_owned)
			THEN (duration_owned)/(estimated_travel_time*60)
			WHEN #objective on left-edge of timespan; other-objective capped before the objective and held long enough to contribute
				last_flipped < '$obj_flipped' AND ADDTIME(last_flipped,duration_owned) > '$obj_flipped'
			THEN TIME_TO_SEC(TIMEDIFF(ADDTIME(last_flipped,duration_owned),'$obj_flipped'))/(estimated_travel_time*60)
			WHEN #objective on right-edge of timespan; other-objective capped before objective changed, held longer than objective
				last_flipped > '$obj_flipped' AND ADDTIME(last_flipped,duration_owned) > ADDTIME('$obj_flipped','$dur_owned')
			THEN TIME_TO_SEC(TIMEDIFF(ADDTIME('$obj_flipped','$dur_owned'),last_flipped))/(estimated_travel_time*60)
			WHEN #objective capped before other-objective AND held longer than other-objective
				last_flipped < '$obj_flipped' AND ADDTIME(last_flipped,duration_owned) < ADDTIME('$obj_flipped','$obj_owned')
			THEN (duration_owned)/(estimated_travel_time*60)
			#ELSE -100 #something went wrong with the above logic
			END AS 'yaks'
			FROM activity_data, supply_routes
			WHERE
			match_id = '$match_id'
			AND start_time = '$match_start'
			AND ADDTIME(last_flipped,duration_owned) > '$obj_flipped'
			AND ADDTIME('$obj_flipped','$dur_owned') > last_flipped
			AND owner_color = '$color'
			AND obj_id = $select AND $where = '$obj_id'
		) yaks;";
		$yak_count = $conn->query($sql)->fetch();
	}
	catch (PDOException $e)
	{
		log_message($conn, -1,"In estimate_yaks_delivered. " . $e->getMessage() . " | sql=" . $sql);
	}
	//cumulative yak counts
	$yaks = $yak_count['yaks'];
	if ($yaks == NULL)
	{
		$yaks = 0; //initialize to 0 in the event the query fails
	}
	if ($yaks < 20)
	{
		$objective_tier = 0;
	}
	elseif ($yaks >= 20 && $yaks < 60)
	{
		$objective_tier = 1;
	}
	elseif ($yaks >= 60 && $yaks < 140)
	{
		$objective_tier = 2;
	}
	else //$yaks >= 140
	{
		$objective_tier = 3;
	}
	return array('tier'=>$objective_tier,'yaks'=>$yaks);
} //END FUNCTION estimate_yaks_delivered
/**
 * Determines which server owns a given objective based on the current match information
 *	and the color of the objective-owner
 * @param $match - the current match information
 * @param $objective - the current objective to determine the server-owner for
 * @param $conn - the database connection object
 * @return $server_id - a 4-digit id number of the server that owns the objective
**/
function get_server_owner($match, $objective, $conn)
{
	try
	{
		$sql = "SELECT * FROM `server_linkings`
			WHERE match_id='$match->id'
				and match_start='$match->start_time'
				and server_color='$objective->owner'
				and server_lead=1;";
		$server_owner = $conn->query($sql)->fetch();
		if ($server_owner['server_id'] == "")
		{
			return 0; //"Neutral" server
		}
		return $server_owner['server_id'];
	}
	catch (PDOException $e)
	{
		log_message($conn,-1,"In get_server_owner " . $e->getMessage());
		return 0; //"Neutral" server
	}
} //END FUNCTION get_server_owner
/**
 *
**/
function check_objective_guild_upgrades($prev_obj_data, $current_obj_data, $timeStamp, $conn)
{
	foreach ($current_obj_data->guild_upgrades as $upgrade)
	{
		try
		{
			$sql = "SELECT *
					FROM objective_state_upgrade
					WHERE guild_upgrade_id=$upgrade
						AND activity_data_id = ".$prev_obj_data['id']."
						AND timeStamp > '".$current_obj_data->last_flipped."';";
			$upgrade_stored = $conn->query($sql)->fetch();
			if ($upgrade_stored['id'] == NULL)
			{
				$sql = "INSERT INTO objective_state_upgrade (id, timeStamp, activity_data_id, guild_upgrade_id)
					VALUES(NULL, '$timeStamp', " . $prev_obj_data['id'] . ", $upgrade);";
				$conn->exec($sql);
			}
		}
		catch(PDOException $e)
		{
			log_message($conn,-1,"In check_objective_guild_upgrades: " . $e->getMessage() . " | sql=" . $sql);
			return; //exit the function; this error only happens when the DB is down, or a match reset is almost up
		}
	}
} //END FUNCTION check_objective_guild_upgrades
/**
 * This function performs the following:
 * 		1) Iterates over every map in match, and every objective of each map
 *		2) Retrieves the most recent database record for the objective-state, from activity_data, in the given match
 *		3) Retrieves the objective information from the database to obtain ppt_base and type of the objective
 *		4) Determines if a new record needs to be inserted for the objective-state
 *		5) Calculates duration_owned and duration_claimed for the record in the database, and updates it accordingly
 *		6) Calls check_guild_claim which stores newly encountered guild information
 *		7) Calls estimate_yaks_delivered
 * @param match - a single set of match data to store activity data for
 * @param timeStamp - the current timestamp, used when storing data
 * @param ingame_clock_time - float representing the game's clock
 * @param $conn - the database connection object
 * @return nothing; inserts and updates records in the activity_data table
**/
function store_activity_data($match, $timeStamp, $ingame_clock_time, $conn)
{
	log_message($conn,21,"Storing activity data for " . $match->id);
	foreach($match->maps as $map)
	{ //loop over every map in the match
		foreach($map->objectives as $objective)
		{ //loop over every objective in each map for the match
			$obj_id = $objective->id;
			$owner = $objective->owner;
			$match_id = $match->id;
			$match_start = $match->start_time;
			$sql = "SELECT * FROM `activity_data` 
			WHERE obj_id='$obj_id'
				AND match_id='$match_id'
				AND start_time='$match_start'
			ORDER BY timeStamp DESC
			LIMIT 1;"; //to retrieve the most recent activity-record from the database
			try
			{
				$prev_obj_data = $conn->query($sql)->fetch(); //get the most recent activity-record
				$sql = "SELECT ppt_base, type FROM `objectives` WHERE obj_id = '$obj_id'";
				$objective_data = $conn->query($sql)->fetch();
				$objective->ppt_base = $objective_data['ppt_base'];
				$objective->type = $objective_data['type'];
				$prev_obj_data['type'] = $objective_data['type']; //used as an object-property in estimate_yaks_delivered
				$update_clause = ""; //always initialize to blank at the beginning of each loop
				$num_yaks_est = 0;
				// ^ query the database to get and store additional information about the objective into the object
				//the additional information in used in several places, including estimate_yaks_delivered and calcing ppt
				$insert = FALSE; //assume we don't need to insert a new record for this objective

				$num_yaks = $objective->yaks_delivered;
				if (!isset($num_yaks))
				{ //in the event of non-claimable objectives
					$num_yaks = 0;
				}
				if ($num_yaks < 20)
				{
					$tier = 0;
				}
				elseif ($num_yaks >= 20 && $num_yaks < 60)
				{
					$tier = 1;
				}
				elseif ($num_yaks >= 60 && $num_yaks < 140)
				{
					$tier = 2;
				}
				else //$num_yaks >= 140
				{
					$tier = 3;
				}

				if ($prev_obj_data['owner_color'] != $objective->owner)
				{ //objective has changed sever-owners. calculate the time-gap; insert new entry
					$update_duration_owned = get_time_interval($objective->last_flipped,$prev_obj_data["last_flipped"]);
					$update_duration_claimed = check_guild_claim($objective, $prev_obj_data, $timeStamp, $conn);
					$num_yaks_est = 0;
					$objective->tier = $tier;
					$insert = TRUE; //there is a new entry to store; the objective changed owners
			   	}
				else
				{ //objective still owned by same team; either update or insert new entry
					if ($prev_obj_data['claimed_by'] != $objective->claimed_by)
			   		{ //the owning guild / time has changed, insert a new entry to show the difference
			   			$update_duration_owned = 0; //set to 0 to prevent duplicate duration-owned data points; proper update on new data
				   		$update_duration_claimed = check_guild_claim($objective, $prev_obj_data, $timeStamp, $conn);
			   			$objective->tier = $tier;
			   			$num_yaks_est = $prev_obj_data['num_yaks_est'];
			   			$insert = TRUE; //there is a new entry to store; the objective changed guild-claims
			   		}
			   		else
			   		{ //the owning guild / time is the same; update old entries with new times
			   			$update_duration_owned = get_time_interval($timeStamp,$prev_obj_data['last_flipped']);
			   			$update_duration_claimed = get_time_interval($timeStamp,$prev_obj_data['claimed_at']);
			   			//using prev-obj-data because those values havent changed, except for API-data-resets
						$tier_data = estimate_yaks_delivered($prev_obj_data, $match, $conn);
						$num_yaks_est = $tier_data['yaks'];
							//using prev_obj_data due to fields that only exist in the database, derived from other API data
						$objective->tier = $tier;
					   	if (($prev_obj_data['type'] == "Castle" || $prev_obj_data['type'] == "Keep" 
					   	|| $prev_obj_data['type'] == "Tower"  || $prev_obj_data['type'] == "Camp"))
						{ //update old objective data with the current number of yaks / tier
							$update_clause = ", num_yaks_est=$num_yaks_est, tier=$tier, num_yaks=$num_yaks";
			   			}
			   		}
				}
				if ($prev_obj_data['claimed_by'] != "")
				{
					$update_clause .= ", duration_claimed=$update_duration_claimed";
				} //modifies SQL query to update duration claimed, if the previous objective was claimed at all
				$sql = "UPDATE `activity_data` SET duration_owned=$update_duration_owned $update_clause
				WHERE id='".$prev_obj_data['id']."';";
				$conn->exec($sql); //update the most recent entry of this match/objective combo with the new owned/claimed durations
			   	//UPDATE MUST HAPPEN BEFORE INSERT
				if ($insert === TRUE)
				{ //if a new row needs to be inserted (new server-owner, guild-claim, or objective-tier)
					$last_flipped = $objective->last_flipped;
					$obj_id = $objective->id;
					$owner_server = get_server_owner($match,$objective,$conn);
					$owner_color = $objective->owner;
					$claimed_by = $objective->claimed_by;
					$claimed_at = $objective->claimed_at;
					$match_id = $match->id;
					$start_time = $match->start_time;
					//objective_tier is set above
					//duration_owned/claimed is initialized to null; updated later as appropriate

					$sql = "INSERT INTO `activity_data` (timeStamp, last_flipped, obj_id, owner_server, tick_timer,
						owner_color, claimed_by, claimed_at, match_id, start_time, num_yaks_est,
						tier, num_yaks, duration_owned, duration_claimed)
					VALUES('$timeStamp','$last_flipped','$obj_id',$owner_server,$ingame_clock_time,
						'$owner_color','$claimed_by','$claimed_at','$match_id','$start_time',$num_yaks_est,
						$tier, $num_yaks, 0,0);";
					$conn->exec($sql); //store the new activity_data point

					$prev_obj_data['id'] = $conn->lastInsertId();
					check_objective_guild_upgrades($prev_obj_data, $objective, $timeStamp, $conn);
				}
			}
			catch(PDOException $e)
			{
				log_message($conn,-1,"In store_activity_data: " . $e->getMessage() . " | sql=" . $sql);
				return; //exit the function; this error only happens when the DB is down, or a match reset is almost up
			}
		} //END foreach $map->$objectives as $objective
	} //END foreach $match->maps as $map
	log_message($conn,22,"Stored activity data for " . $match->id);
} //END FUNCTION store_activity_data
/**
 * This function takes 2 timestamps, subtracts the first from the second and returns a formatted string in "H:i:s"
 * @param $time1 - the first, greater timestamp
 * @param $time2 - the second, lesser timestamp
 * @return timeStamp in a string, formatted as "H:i:s"
**/
function get_time_interval($time1, $time2)
{
	try
	{
		$date1 = new DateTime($time1);
		$date2 = new DateTime($time2);
		$diff = date_diff($date1, $date2, TRUE);
		$diff = "'" . ($diff->d*24 + $diff->h) . ":" . $diff->i . ":" . $diff->s . "'"; //manual formatting to time interval
		return $diff;
	}
	catch (Exception $e)
	{
		return "'00:00:00'";
	}
} //END FUNCTION get_time_interval
/**
 * This function performs several things:
 * 1) It checks if the current objective's guild-id is already in the database
 *		- if it isn't, it obtains the details of the guild from the API, and stores the guild id, name, tag and emblem into the database
 * 2) It returns a time-value representing how long the objective has been claimed
 * @param $objective - the current objective to check the guild claim on
 * @param $prev_obj_data - the most recent record for the given objective from the database
 * @param $timeStamp - the current timestamp; substitutes for $prev_obj_data['claimed_at'] if it is null
 * @param $conn - database connection object
 * @return duration of time of the guild claim for the objective
**/
function check_guild_claim($objective, $prev_obj_data, $timeStamp, $conn)
{
	if ($prev_obj_data['owner_server'] != $objective->owner || $objective->claimed_by != $prev_obj_data['claimed_by'])
	{ //if the objective changed server-owners or guild-owners, there might be a new guild to see -- also calculate claim_duration
		try
		{
			if ($objective->claimed_by != "")
			{ //if the objective is claimed
				$sql = "SELECT * FROM `guild` where guild_id='".$objective->claimed_by."';";
				$guild_exists = $conn->query($sql)->fetch(); //query the DB to see if the new-claiming guild exists
				$guild_data = json_decode(file_get_contents("https://api.guildwars2.com/v1/guild_details.json?guild_id=".$objective->claimed_by));
				if ($guild_exists['guild_id'] != NULL)
				{ //if the guild does exist, make sure we dont need to update the emblem
					$emblem_age = date_diff(new DateTime(date("Y-m-d H:i:s")),new DateTime($guild_exists['emblem_last_updated']));
					$emblem_age = (($emblem_age->d*24+$emblem_age->h)*60+$emblem_age->i)*60+$emblem_age->s;
					//add up all the time, converted to seconds
					if ($emblem_age > 3*4*7*24*60*60) //emblem older than 3 months, in seconds
					{ //update emblem
						$emblem = $guild_data->emblem;
						$sql = "UPDATE `guild_emblem` SET background_id=".$emblem->background_id.", foreground_id=".$emblem->foreground_id.",
						flags='".implode("|",$emblem->flags)."', background_color_id=".$emblem->background_color_id.",
						foreground_primary_color_id=".$emblem->foreground_primary_color_id.", foreground_secondary_color_id=".$emblem->foreground_secondary_color_id."
						WHERE guild_id='".$guild_data->guild_id."'; UPDATE `guild` SET emblem_last_updated=NOW() WHERE guild_id='".$guild_data->guild_id."';";
						try
						{
							log_message($conn,21,"Updating emblem; guild_id=" . $guild_data->guild_id);
							$conn->exec($sql); //store or update the guild emblem
							log_message($conn,22,"Updating emblem; guild_id=" . $guild_data->guild_id);
						}
						catch (PDOException $e1)
						{ //in the event the guild did not have an emblem, do not stop the code that follows - just note it and continue
							log_message($conn,-1,"In check_guild_claim->emblem->update: " . $e1->getMessage() . " | sql=" . $sql);
						}
					}
				}
				elseif ($guild_exists['guild_id'] == NULL)
				{ //insert a guild and it's emblem
					log_message($conn,21,"Storing guild; guild_id=" . $guild_data->guild_id);
					$sql = "INSERT INTO `guild` (guild_id, emblem_last_updated, name, tag)
						VALUES('".$guild_data->guild_id."',NOW(),'".$guild_data->guild_name."','".$guild_data->tag."');";
					$conn->exec($sql); //then store the information
					log_message($conn,22,"Storing guild; guild_id=" . $guild_data->guild_id);
					//
					$emblem = $guild_data->emblem;
					$sql = "INSERT INTO `guild_emblem` (guild_id, background_id, foreground_id, flags, background_color_id,
						foreground_primary_color_id, foreground_secondary_color_id)
						VALUES('".$guild_data->guild_id."',".$emblem->background_id.",".$emblem->foreground_id.",
						'".implode("|",$emblem->flags)."',".$emblem->background_color_id.",".$emblem->foreground_primary_color_id.",
						".$emblem->foreground_secondary_color_id.");";
						try
						{
							$conn->exec($sql); //store or update the guild emblem
						}
						catch (PDOException $e1)
						{ //in the event the guild did not have an emblem, do not stop the code that follows - just note it and continue
							log_message($conn,-1,"In check_guild_claim->emblem->insert: " . $e1->getMessage() . " | sql=" . $sql);
						}
				}
			}
			if ($prev_obj_data['claimed_at'] == "")
			{
				$prev_obj_data['claimed_at'] = $timeStamp;
			}
			if ($prev_obj_data['owner_server'] != $objective->owner)
			{ //if the objective changed owner-servers, calculate duration_claimed based on when the objective changed owners
				return get_time_interval($objective->last_flipped,$prev_obj_data['claimed_at']);
			}
			if ($objective->claimed_by != $prev_obj_data['claimed_by'])
			{ //if the objective changed owner-guilds, calculate the duration_claimed based on the difference in claimed_at values
				return get_time_interval($objective->claimed_at,$prev_obj_data['claimed_at']);
			}
		}
		catch(PDOException $e)
		{
			log_message($conn,-1,"In check_guild_claim: " . $e->getMessage() . " | sql=" . $sql);
			return -1; //error - unknown claim duration
		}
	}
} //END FUNCTION check_guild_claim
/**
 * Stores the victory_points from the API into the database
 * @param $match - the match to store information for
 * @param $skirmish_num - the number of the skirmish (hours between now and match_start, divided by 2)
 * @param $timeStamp - the time stamp as determined by the main() loop
 * @param $conn - the database connection object
 * @return nothing; stores information into database
**/
function store_skirmish_scores($match, $skirmish_num, $timeStamp, $conn)
{
	log_message($conn,14,"match->id=" . $match->id);
	try
	{
		$sql = "INSERT INTO `skirmish_scores` (match_id, match_start, timeStamp, skirmish_number, red_skirmish_score,
			blue_skirmish_score, green_skirmish_score)
		VALUES('$match->id', '$match->start_time', '$timeStamp', $skirmish_num, ".$match->victory_points->red.", 
			".$match->victory_points->blue.", ".$match->victory_points->green.");";
		$conn->exec($sql);
	}
	catch(PDOException $e)
	{
		log_message($conn,-1,"In store_skirmish_scores for match_id " . $match->id . " and skirmish number " .
			$skirmish_num . ": " . $e->getMessage());
	}
	log_message($conn,15,"match->id=" . $match->id);
} //END FUNCTION store_skirmish_scores
/**
 * This function will remove old log files and folders
 * Removes all files inside folders which are older than 2 weeks, then removes the containing folder
 * @param $conn - the database connection object
 * @return nothing; removes old log files
**/
function clean_logs($conn)
{
	try
	{
		$dateStamp = date('Y-m-d', strtotime('-2 week')); //get the day-stamp from 2 weeks ago
		$directory = './region-' . REGION . '-logs/' . $dateStamp;
		if (!file_exists($directory))
		{ //if the log-folder doesn't exist, no logs to clean
			return;
		}
		log_message($conn,16);
		log_message($conn,20," from $directory");
		$files = glob($directory . "/*"); //get all files from the folder
		foreach($files as $file)
		{ //loop through all the log files
			if(is_file($file))
			{ //remove all the log files
				log_message($conn,18,"file=$file");
				unlink($file); //delete file
			}
		}
		log_message($conn,19,"folder=$directory");
		rmdir($directory); //finally, remove the containing folder
	}
	catch (Exception $e)
	{
		log_message($conn,1001,$e->getMessage());
	}
	log_message($conn,17);
} //END FUNCTION clean_logs
/**
 * This function calculates the ppt for the given set of objectives (determined by map and match)
 * $objective->ppt_base and $objective->tier are set by estimate_yaks_delivered when calling store_activity_data
 * @param $objectives - the array of objective; modified by store_activity_data so each contains additional properties
 * @param $conn - the database connection object
 * @return array of ints ("red", "blue", "green" keys) representing the ppt of each
 *			- "red" => int
 *			- "blue" => int
 *			- "green" => int
**/
function calculate_ppt($objectives, $conn)
{
	$red_ppt = 0;
	$blue_ppt = 0;
	$green_ppt = 0;
	foreach($objectives as $objective)
	{
		if ($objective->owner == "Red")
		{
			$red_ppt += $objective->ppt_base*(0.5*$objective->tier+1);
		}
		elseif ($objective->owner == "Blue")
		{
			$blue_ppt += $objective->ppt_base*(0.5*$objective->tier+1);
		}
		elseif ($objective->owner == "Green")
		{
			$green_ppt += $objective->ppt_base*(0.5*$objective->tier+1);
		}
	}
	return array("red"=>$red_ppt,"blue"=>$blue_ppt,"green"=>$green_ppt);
} //END FUNCTION calculate_ppt
/**
 * This function performs 2 distinct things:
 * 1) At $ingame_clock_time == 5, stores skirmish scores for the match. Also cleans logs older than 2 weeks
 * 2) At $ingame_clock_time == 5, stores the score data for the match; calculates the proper PPT using the $ppt_data match
 * @param $match - the current match to store scores for
 * @param $ppt_data - a set of matches from minute-5 on the in-game clock; used to calc a matches' ppt at the time
 * @param $ingame_clock_time - between 0 and 5; represents the in-game clock
 * @param $timeStamp - the current timeStamp as determined by the main() loop
 * @param $conn - the database connection object
 * @return nothing - stores score-data and skirmish-scores into the database
**/
function store_scores($match, $ingame_clock_time, $timeStamp, $conn)
{
	if ($ingame_clock_time == 5)
	{
		$current_time = time(); //get the current time
		$match_start = strtotime($match->start_time); //cast the match_start time to a time object
		$hours = round(($current_time - $match_start)/3600,1); //get the number of hours between now and match_start
		if ((fmod($hours,2) >= 0 && fmod($hours,2) < 0.07) && ($hours/2) > 1) //fmod is the modulus operator but allows for fractional numbers
		{ //if it has been a 2-hour mark, and it isn't immediately reset, store skirmish
			store_skirmish_scores($match, ($hours/2), $timeStamp, $conn);
			clean_logs($conn); //clean old log files
		}
		log_message($conn,12,"match->id=" . $match->id);

		for ($i = 0; $i < 4; $i++)
		{ //using a for-loop rather than a for-each loop because of the differing data sets (ppt_data vs match)
			$ppt_totals = calculate_ppt($match->maps[$i]->objectives, $conn); //use the data from 1 minute ago and calculate ppt totals
			$id = $match->id;
			$start_time = $match->start_time;
			$map_id = $match->maps[$i]->type;
			//
			$greenScore = $match->maps[$i]->scores->green;
			$blueScore = $match->maps[$i]->scores->blue;
			$redScore = $match->maps[$i]->scores->red;
			//
			$greenKills = $match->maps[$i]->kills->green;
			$blueKills = $match->maps[$i]->kills->blue;
			$redKills = $match->maps[$i]->kills->red;
			//
			$greenDeaths = $match->maps[$i]->deaths->green;
			$blueDeaths = $match->maps[$i]->deaths->blue;
			$redDeaths = $match->maps[$i]->deaths->red;
			//
			$greenPPT = $ppt_totals["green"];
			$bluePPT = $ppt_totals["blue"];
			$redPPT = $ppt_totals["red"];
			//
			$sql = "INSERT INTO `map_scores` (timeStamp, match_id, start_time, map_id, greenScore, blueScore, redScore,
					greenKills, blueKills, redKills, greenDeaths, blueDeaths, redDeaths, green_ppt, blue_ppt, red_ppt)
				VALUES ('$timeStamp','$id','$start_time','$map_id', $greenScore, $blueScore, $redScore,
					$greenKills, $blueKills, $redKills, $greenDeaths, $blueDeaths, $redDeaths, $greenPPT, $bluePPT, $redPPT);";
			try
			{
				log_message($conn,21,"Storing scores for " . $match->id . " map=" . $match->maps[$i]->type);
				$conn->exec($sql); //if this entry is a duplicate, store_server_linkings is also skipped due to the exception
				log_message($conn,22,"Stored scores for " . $match->id . " map=" . $match->maps[$i]->type);
			}
			catch(PDOException $e)
			{
				log_message($conn,-1,"In store_scores: " . $e->getMessage() . " | $sql");
			}
		}
		log_message($conn,13,"match->id=" . $match->id);
	}
} //END FUNCTION store_scores
/**
 * This function synchronizes to the region's in-game clock by checking for score changes, every few seconds
 * This function also determines if a reset has occurred, and passes a variable along to store the new match-details
 * @param $new_week - see return value below
 * @param $diff - the difference, in microseconds, 
 * @return array(store_data, prev_start_time, sync_wait)
 *				store_data => boolean; determines whether or not to store new match_details for the week.
 								- true if prev_start_time != the sync'd matches' start_time
 				prev_start_time => the current weeks' match-start-time; used to detect match-resets
 				sync_wait => TRUE after the very first synchronize; tells this function to wait up to 25 seconds before checking scores
**/
function synchronize($new_week, $conn, $diff=0)
{
	log_message($conn,1,"Region=" . REGION);
	if ($diff >= (30*SECONDS))
	{ //if the processing time was over 30 seconds, no need to idle before syncing
		log_message($conn,501,"Region=" . REGION);
		$new_week['sync_wait'] = FALSE; //just to ensure it doesn't wait extra time
	}
	$prev_score = PHP_INT_MAX; //initialize to a very high value
	if ($new_week['sync_wait'] === TRUE && $diff < (20*SECONDS))
	{ //if there should be an initial delay, and the processing-time wasnt too long, idle for some time
		log_message($conn,4,(20*SECONDS-$diff));
		usleep((20*SECONDS - $diff)); //sleep for a combined (processing+idle) time of 20 seconds
	}
	while (TRUE)
	{
		$all_matches = json_decode(file_get_contents('https://api.guildwars2.com/v2/wvw/matches?ids=all'));
		$matches = array();
		foreach ($all_matches as $match)
		{
			if (preg_match("/" . REGION . "\-[0-9]*/", $match->id))
			{
				$matches[] = $match;
			}
		} //TODO if looping more than X times and $new_week[sync_wait] === TRUE then throw error once
		$current_score = $matches[0]->scores->green + $matches[0]->scores->blue + $matches[0]->scores->red;

		log_message($conn,0,"Region=" . REGION . " current_score=" . $current_score . "|prev_score=" . $prev_score);
	
		if ($current_score >= ($prev_score+230))
		{
			$prev_start_time = $matches[0]->start_time;
		
			if ($prev_start_time != $new_week["prev_start_time"])
			{
				log_message($conn,3,"Region=" . REGION);
				$store_data = TRUE;
			}
			log_message($conn,2,"Region=" . REGION);
			return array("store_data"=>$store_data,"prev_start_time"=>$prev_start_time,"sync_wait"=>TRUE);
		}
		$prev_score = $current_score;
		usleep(2*SECONDS);
	}
} //END FUNCTION synchronize
/**
 * This function is the main driver-logic behind the data collector
 * Process is as follows:
 * 		- intializes some variables, does an initial synchronize; starts at ingame_clock_time=4
 *		- begin indefinite loop
 *			- loop over every match-set retrieved for the REGION
 *				- if the program has determined that there is a new weeks' worth of matches, store the match details
 *					- also stores server_linkings
 *				- calls store_score_data function
 *					- at time=5, stores score & ppt data
 *					- at time=5, checks to see if its time to store skirmish-score data (happens every 2nd hour)
 *				- stores activity data
 *					- stores any newly encountered guilds into database when calculating duration_claimed
 *			- end of for-each-match loop
 *			- idles for a time based on the time it took to process the data; combined time = 30 seconds
 *			- subtracts 0.5 off the ingame_clock_time; wraps around to 5 when it hits 0
 *		- end of while-loop; go back to beginning of indefinite loop
**/
function main()
{
	if (REGION != 1 && REGION != 2)
	{ //invalid region; 1 = NA, 2 = EU
		echo "Invalid region specified: " . REGION . "\n";
		exit;
	}
	$ingame_clock_time = 5.0; //once the initial sync is finished, the in-game clock will be 4:00
	$conn = connect_to_database(); //have to connect to get log messages for synchronize
	$new_week = synchronize($new_week, $conn); //do an initial synchronize
	$new_week['store_data'] = TRUE; //always assume a new week has started
	$conn = NULL; //clear the connection before entering the loop - it is re-established every iteration
	while (true)
	{ //loop indefinitely
		$begin_time = microtime(true); //get the current time in microseconds; used to calculate processing time
		$conn = connect_to_database(); //connect to the database to perform this iterations' storage/retrieval
		$timeStamp = Date("Y-m-d H:i:s"); //make a unique timestamp to pass to functions that store data with timestamps
				//having a unified timestamp allows group-by statements to work properly; also prevents recalculations
		log_message($conn,5,"Region=" . REGION . "; clock-time=" . $ingame_clock_time);
		$matches = json_decode(file_get_contents('https://api.guildwars2.com/v2/wvw/matches?ids=all'));
		if ($matches == NULL)
		{
			log_message($conn,502, "Region=" . REGION);
			$matches = json_decode(file_get_contents('https://api.guildwars2.com/v2/wvw/matches?ids=all'));
			if ($matches == NULL)
			{
				log_message($conn,503,"Region=" . REGION);
			}
		}
		foreach($matches as $match)
		{ //loop through each match retrieved from all matches
			if (preg_match("/" . REGION . "\-[0-9]*/", $match->id))
			{ //only process matches within the specified region
				if ($match->start_time == 0)
				{ //if the start-time is 0, something went wrong
					usleep(2*SECONDS); //sleep for 2 seconds, give the API time to refresh
					log_message($conn,502,"match->id=" . $match->id);
					$match = json_decode(file_get_contents("https://api.guildwars2.com/v2/wvw/matches?ids=".$match->id))[0];
					//attempt to retrieve only this match, one more time
				}
				if ($new_week['store_data'] === TRUE)
				{ //if the latest synchronization determined that a new matchup started, store the details
					store_match_details($match, $conn);
				 }
				store_activity_data($match, $timeStamp, $ingame_clock_time, $conn); //store any objective state-changes since the last sweep; calculates dur_owned/claimed
				//always store activity data before score data; sets the objective's tier for later
				store_scores($match, $ingame_clock_time, $timeStamp, $conn); //always call this method despite it only working on 2 times
					//on time=5, stores skirmish data if needed
					//on time=5, stores score data & ppt data
			}
		} //END foreach $matches as $match
		if ($new_week['store_data'] === TRUE)
		{ //once the above for-each loop has completed, then new match data has been stored already
			$new_week['store_data'] = FALSE;
		}
		log_message($conn,6,"Region=" . REGION . "; clock-time=" . $ingame_clock_time);
		$diff = (microtime(true) - $begin_time)*SECONDS; //determine how long it took to process the batch of data
		$idle_time = ((30*SECONDS) - $diff); //determine how long the idle time needs to be to maintain 30 seconds between each loop
		if ($ingame_clock_time == 0.5 || $diff > (30*SECONDS))
		{ //if its time to resync to the in-game clock, or the process took too long, resycn
			// TODO roll over instead of resyncing
			$new_week = synchronize($new_week, $conn, $diff);
			$idle_time = 1; //set the time to sleep to 1 microsecond -- virtually 0
			$ingame_clock_time = 5.5; //in the event of a forced resync, set the clock-timer
		}
		log_message($conn,4,$idle_time);
		$conn = null; //clear the database connection before idling, since we're not using a persistent connection
		usleep($idle_time); //sleep for however long needed to maintain exactly 30 seconds per loop
		$ingame_clock_time -= 0.5; //subtract half a minute off the in-game clock counter
		if ($ingame_clock_time <= 0)
		{ //if the in-game clock is 0, roll back up to 5
			$ingame_clock_time = 5;
		}
	} //END while(true)
} //END FUNCTION main
//
main();
?>