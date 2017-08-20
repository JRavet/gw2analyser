<?php
/*
id int(11) unsigned AUTO_INCREMENT,
timeStamp datetime,
capture_history_id int(11) unsigned,
upgrade_id int(4),
PRIMARY KEY (id),
FOREIGN KEY(upgrade_id) REFERENCES objective_upgrade(id)
ON DELETE CASCADE,
FOREIGN KEY(capture_history_id) REFERENCES capture_history(id)
ON DELETE CASCADE
*/
class upgrade_history extends TinyMVC_Model
{
	protected $_table = "upgrade_history";
	protected $pk = "id";

	public function find($params)
	{
		$this->db->select("'upgrade' as type, timeStamp, name");
		$this->db->from($this->_table . " uh");
		$this->db->join("objective_upgrade ou", "ou.id = uh.upgrade_id");
		foreach($params as $k=>$v)
		{
			$this->db->where($k, $v);
		}
		return $this->db->query_all();
	}
}

?>