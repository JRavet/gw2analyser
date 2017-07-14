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

	public function get_server_owner($match_id, $start_time, $color)
	{
		$this->db->select("server_id");
		$this->db->from($this->_table);
		$this->db->join("match_detail", "match_detail.id=server_linking.match_detail_id");
		$this->db->where("match_id", $match_id);
		$this->db->where("start_time", $start_time);
		$this->db->where("server_color", $color);
		$this->db->where("server_lead", 1);
		return $this->db->query_one()['server_id'];
	}
}

?>