<?php
require_once("../../../htdocs/scripts.php");
$collector_started = false; // a hack to make this file load the framework AND execute itself
if (class_exists('Static_Collector_Controller', false) === false)
{ // only create the class once; necessary because calling this file will ...
	// define the class when loading the framework, and then it is defined again due to calling this file
	class Static_Collector_Controller extends TinyMVC_Controller
	{
		public function start_collector( $params )
		{
			// remove foreign key restraints temporarily
			$this->load->model("log_code", "conn"); // need a dummy model to set foreign key checking
			$this->conn->db->pdo->query('SET FOREIGN_KEY_CHECKS=0;');

			if ($params == "-a" OR $params == "-o")
			{
				$this->load->model("objective");
				$this->objective->delete_all();
				$this->objective->store_objectives();
			}
			if ($params == "-a" OR $params == "-u")
			{
				$this->load->model('objective_upgrade');
				$this->objective_upgrade->delete_all();
				$this->objective_upgrade->store_upgrades();
			}
			if ($params == "-a" OR $params == "-c")
			{
				$this->load->model("log_code");
				$this->log_code->delete_all();
				$this->log_code->store_codes();
			}
			if ($params == "-a" OR $params == "-r")
			{
				$this->load->model("supply_route");
				$this->supply_route->delete_all();
				$this->supply_route->store_supply_routes();
			}
			if ($params == "-a" OR $params == "-s")
			{
				$this->load->model("server_info");
				$this->server_info->delete_all();
				$this->server_info->store_server_info();
			}
			if ($params == "-a" OR $params == "-g")
			{
				$this->load->model("guild");
				$this->guild->store_guild((object)array(
					'guild_id' => '',
					'emblem_last_updated' => 0,
					'name' => '',
					'tag' => ''
				));
			}

			// re-enable foreign key checking
			$this->conn->db->pdo->query('SET FOREIGN_KEY_CHECKS=1;');
		}
	}
	$collector_started = true; // a hack to make this file load the framework AND execute itself
}
if ($collector_started === true) { // a hack to make this file load the framework AND execute itself
	if ( isset($argv[1]) )
	{
		$params = $argv[1];
	}
	else
	{
		echo "Please specificy a paramater.\n";
		echo "\t-a for all\n";
		echo "\t-o for objectives\n";
		echo "\t-u for objective upgrades\n";
		echo "\t-c for log codes\n";
		echo "\t-r for supply routes\n";
		echo "\t-s for server info\n";
		echo "\t-g for a blank guild\n";
		exit;
	}
	$tmvc->main('static_collector', 'start_collector', $params); // start the static collector
}
?>