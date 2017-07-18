<?php
/*
id int(11) unsigned AUTO_INCREMENT,
match_detail_id int(11) unsigned,
server_id int(4),
server_color enum("Red","Blue","Green"),
server_lead boolean,
server_population varchar(15),
PRIMARY KEY(id),
FOREIGN KEY(match_detail_id) REFERENCES match_detail(id)
ON DELETE CASCADE,
FOREIGN KEY(server_id) REFERENCES server_info(server_id)
*/
class server_linking extends TinyMVC_Model
{
	protected $_table = "server_linking";
	protected $pk = "id";

	public function get_server_owner($match_detail_id, $color)
	{
		$this->db->select("server_id");
		$this->db->from($this->_table);
		$this->db->where("match_detail_id", $match_detail_id);
		$this->db->where("server_color", $color);
		$this->db->where("server_lead", 1);
		return $this->db->query_one()['server_id'];
	}
}

?>