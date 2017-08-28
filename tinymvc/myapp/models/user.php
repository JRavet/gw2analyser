<?php
/*
id int(11) unsigned AUTO_INCREMENT,
name varchar(64),
password varchar(41),
email varchar(64),
PRIMARY KEY (id)
*/
class User extends TinyMVC_Model
{
	protected $_table = "user";
	protected $pk = "id";
	public $id = NULL;
	public $name = NULL;

	public function __construct($user_id=null) {
		parent::__construct();
		if ( isset($user_id) ) {
			$this->db->select("id, name");
			$this->db->from($this->_table);
			$this->db->where("id", $user_id);
			$user = $this->db->query_one();

			if (isset($user)) {
				$this->name = $user['name'];
				$this->id = $user['id'];
			}
		}
	}

}

?>