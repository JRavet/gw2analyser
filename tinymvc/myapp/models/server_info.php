<?php
/*
server_id int(4),
name varchar(60),
PRIMARY KEY(server_id)
--abbreviation varchar(4) // currently removed
*/
class server_info extends TinyMVC_Model
{

	protected $_table = 'server_info';
	protected $pk = 'server_id';

	public function store_server_info()
	{
		$servers = (new gw2_api())->get_server_info();
		$servers[] = (object)array('id' => 0, 'name' => 'Neutral'); // append a "neutral" server to the list

		foreach ($servers as $server) {
			$abbv = $this->getServerAbbreviation($server->name);
			$this->save(array(
				"server_id" => $server->id,
				"name" => $server->name,
				"abbreviation" => $abbv
			));
		}

	}

	public function getServerAbbreviation($name)
	{
		switch($name) {
			case "Anvil Rock": return "AR";
			case "Borlis Pass": return "BP";
			case "Yak's Bend": return "YB";
			case "Henge of Denravi": return "HoD";
			case "Maguuma": return "Mag";
			case "Sorrow's Furnace": return "SF";
			case "Gate of Madness": return "GoM";
			case "Jade Quarry": return "JQ";
			case "Fort Aspenwood": return "FA";
			case "Ehmry Bay": return "Ebay";
			case "Stormbluff Isle": return "SBI";
			case "Darkhaven": return "DH";
			case "Sanctum of Rall": return "SoR";
			case "Crystal Desert": return "CD";
			case "Isle of Janthir": return "IoJ";
			case "Sea of Sorrows": return "SoS";
			case "Tarnished Coast": return "TC";
			case "Northern Shiverpeaks": return "NSP";
			case "Blackgate": return "BG";
			case "Ferguson's Crossing": return "FC";
			case "Dragonbrand": return "DB";
			case "Devona's Rest": return "DR";
			case "Eredon Terrace": return "ET";
			case "Kaineng": return "Kg";
			default: return "";
		}
	}

	public function getFormList()
	{
		$this->db->select('server_id as "id", concat(name, " (", abbreviation, ")") as "name"');
		$this->db->from($this->_table);
		$this->db->orderby('server_id ASC');
		$this->db->notin('name', array('Neutral'));
		return $this->db->query_all();
	}
}

?>