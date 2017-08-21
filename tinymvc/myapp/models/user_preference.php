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
		if ( isset($user_id) ) { // user logged in, get prefers
			$this->db->select("value");
			$this->db->from($this->_table);
			$this->db->where("user_id", $user_id);
			$this->db->in("preference", array("bgColor1","bgColor2"));

			$prefs = $this->db->query_all();
			if ( !empty($prefs) ) {
				return $prefs;
			}
		}
		// user not logged in, or prefs were empty - use defaults
		return array(array("value"=>"white"), array("value"=>"gray"));
	}
}

?>