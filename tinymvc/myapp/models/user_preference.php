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

	private $user_id = -1;

	public function __construct($user_id=null) {
		parent::__construct();
		$this->user_id = $user_id;
	}

	public function getColorScheme()
	{
		$defaults = array(
			"bgColor1"    => "white",
			"bgColor2"    => "gray",
			"redServer"   => "#ffc6c6",
			"blueServer"  => "#c6ceff",
			"greenServer" => "#c6ffc6"
		);
		if ( isset($this->user_id) ) { // user logged in, get prefs

			$this->db->select("preference, value");
			$this->db->from($this->_table);
			$this->db->where("user_id", $this->user_id);
			$this->db->in("preference", array("bgColor1","bgColor2", "redServer", "blueServer", "greenServer"));

			$result = $this->db->query_all();

			$colorScheme = array();
			foreach ($result as $color) {
				$colorScheme[$color['preference']] = $color['value'];
			}

			$colorScheme = array_unique(array_merge($defaults,$colorScheme)); // colorScheme overwrites defaults

			return $colorScheme;
		}
		// user not logged in
		return $defaults;
	}
}

?>