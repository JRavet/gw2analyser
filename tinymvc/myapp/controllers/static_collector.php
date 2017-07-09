<?php

class Static_Collector_Controller extends TinyMVC_Controller
{
	public function start_collector()
	{
		$this->store_guild_upgrades();
		$this->store_log_codes();
		$this->store_supply_routes();
		$this->store_objectives();
		$this->store_servers();
	}

	private function store_guild_upgrades()
	{
		$this->load->model('objective_upgrade', 'upgrades');
		$this->upgrades->store_upgrades();
	}
	private function store_log_codes()
	{
		$this->load->model("log_code","codes");
		$this->codes->store_codes();
	}
	private function store_supply_routes()
	{

	}
	private function store_objectives()
	{
		$this->load->model("objective","objectives");
		$this->objectives->store_objectives();
	}
	private function store_servers()
	{

	}
}
?>