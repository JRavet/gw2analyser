<?php
/*
id int(11) unsigned AUTO_INCREMENT,
user_id int(11) unsigned,
preference varchar(64),
value varchar(64),
PRIMARY KEY (id),
FOREIGN KEY (user_id) REFERENCES user(id)
ON DELETE CASCADE
*/
class user_preference extends TinyMVC_Model
{
	protected $_table = "user_preference";
	protected $pk = "id";

	public function getBgColors($user_id)
	{
		$this->db->select("value");
		$this->db->from($this->_table);
		$this->db->where("user_id", $user_id);
		$this->db->orwhere("preference","bgColor1");
		$this->db->where("preference","bgColor2");
	}
}

?>