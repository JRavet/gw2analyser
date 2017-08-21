<?php
/*
id int(11) unsigned AUTO_INCREMENT,
timeStamp datetime,
capture_history_id int(11) unsigned,
num_yaks int(4),
PRIMARY KEY (id),
FOREIGN KEY(capture_history_id) REFERENCES capture_history(id)
ON DELETE CASCADE
*/
class yak_history extends TinyMVC_Model
{
	protected $_table = "yak_history";
	protected $pk = "id";

	public function getByCaptureId($params)
	{
		$this->db->select("'Yak #' as type, $this->_table.*");
		$this->db->from($this->_table);
		foreach($params as $k=>$v) $this->db->where($k, $v);

		return $this->db->query_all();
	}
}

?>