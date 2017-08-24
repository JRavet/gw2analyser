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

		foreach ($servers as $server)
		{
			$this->save(array(
				"server_id" => $server->id,
				"name" => $server->name
			));
		}

	}

	public function getFormList()
	{
		$this->db->select('server_id as "id", name');
		$this->db->from($this->_table);
		$this->db->orderby('server_id ASC');
		$this->db->notin('name', array('Neutral'));
		return $this->db->query_all();
	}
}

?>