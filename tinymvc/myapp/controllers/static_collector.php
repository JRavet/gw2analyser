<?php

class Static_Collector_Controller extends TinyMVC_Controller
{
	public function start_collector()
	{
		// load all the required models
		$this->load->model('objective_upgrade', 'upgrades');
		$this->load->model("log_code","codes");
		$this->load->model("objective","objectives");
		$this->load->model("supply_route","routes");
		$this->load->model("server_info","server_info");
		$this->load->model("guild","guild");
		// remove foreign key restraints temporarily
		$this->upgrades->db->pdo->query('SET FOREIGN_KEY_CHECKS=0;');
		// delete all data within the targeted tables
		$this->upgrades->delete_all();
		$this->codes->delete_all();
		$this->objectives->delete_all();
		$this->routes->delete_all();
		$this->server_info->delete_all();
		// store the new, fresh data
		$this->upgrades->store_upgrades();
		$this->codes->store_codes();
		$this->objectives->store_objectives();
		$this->routes->store_supply_routes();
		$this->server_info->store_server_info();
		// store a blank guild to prevent errors when storing claim_history
		$this->guild->store_guild((object)array(
			'guild_id' => '',
			'emblem_last_updated' => '',
			'name' => '',
			'tag' => ''
		));
		// re-enable foreign key checking
		$this->upgrades->db->pdo->query('SET FOREIGN_KEY_CHECKS=1;');
	}
}
?>