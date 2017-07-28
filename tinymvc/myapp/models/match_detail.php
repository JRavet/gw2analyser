<?php
/*
id int(11) unsigned AUTO_INCREMENT,
match_id varchar(4),
week_num int(3),
start_time datetime,
end_time datetime,
PRIMARY KEY(id)
*/
class match_detail extends TinyMVC_Model
{
	protected $_table = "match_detail";
	protected $pk = "id";

	public function find_readable()
	{
		$data = $this->db->pdo->query("
			SELECT md.id, md.start_time, md.end_time,
				(SELECT GROUP_CONCAT(name SEPARATOR ', ') as red_servers
					FROM server_linking sl
					LEFT JOIN server_info si on si.server_id = sl.server_id
					WHERE server_color = 'Red' AND md.id = sl.match_detail_id
				) red_servers,
				(SELECT GROUP_CONCAT(name SEPARATOR ', ') as blue_servers
					FROM server_linking sl
					LEFT JOIN server_info si on si.server_id = sl.server_id
					WHERE server_color = 'Blue' AND md.id = sl.match_detail_id
				) blue_servers,
				(SELECT GROUP_CONCAT(name SEPARATOR ', ') as green_servers
					FROM server_linking sl
					LEFT JOIN server_info si on si.server_id = sl.server_id
					WHERE server_color = 'Green' AND md.id = sl.match_detail_id
				) green_servers
			FROM match_detail md
			ORDER BY md.start_time DESC;"
		);

		return $data;

	}
}

?>