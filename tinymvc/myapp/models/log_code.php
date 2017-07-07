<?php
/*
id int(11),
type varchar(100),
message varchar(255),
PRIMARY KEY (id)
*/
class log_code extends TinyMVC_Model
{
	private $_table = "log_code";
	private $pk = "id";


	public function store_codes()
	{
		$codes = array(
			array("id" => -1, "type" => 'database error', "message" => 'A database error has occurred'),
            array("id" => 0, "type" => 'note', "message" => ''),
            array("id" => 1, "type" => 'info', "message" => 'Synchronizing to in-game clock for region'),
            array("id" => 2, "type" => 'info', "message" => 'Synchronizing complete for region'),
            array("id" => 3, "type" => 'info', "message" => 'New match-set detected'),
            array("id" => 4, "type" => 'idle', "message" => 'Idling for time (microseconds)'),
            array("id" => 5, "type" => 'process-start', "message" => 'Storing data for region'),
            array("id" => 6, "type" => 'process-end', "message" => 'Finished storing data for region'),
            array("id" => 7, "type" => 'info', "message" => 'Connection to database complete'),
            array("id" => 8, "type" => 'process-start', "message" => 'Storing match_detail data'),
            array("id" => 9, "type" => 'process-end', "message" => 'Finished storing match_detail data'),
            array("id" => 10, "type" => 'process-start', "message" => 'Storing server_linkings data'),
            array("id" => 11, "type" => 'process-end', "message" => 'Finished storing server_linkings data'),
            array("id" => 12, "type" => 'process-start', "message" => 'Storing score data'),
            array("id" => 13, "type" => 'process-end', "message" => 'Finished storing score data'),
            array("id" => 14, "type" => 'process-start', "message" => 'Storing skirmish scores'),
            array("id" => 15, "type" => 'process-end', "message" => 'Finished storing skirmish scores'),
            array("id" => 16, "type" => 'process-start', "message" => 'Removing old logs'),
            array("id" => 17, "type" => 'process-end', "message" => 'Finished removing old logs'),
            array("id" => 18, "type" => 'file-deletion', "message" => 'Removing log file'),
            array("id" => 19, "type" => 'folder-deletion', "message" => 'Removing log folder'),
            array("id" => 20, "type" => 'file-removal prep', "message" => 'Preparing to remove files'),
            array("id" => 21, "type" => 'storing data', "message" => ''),
            array("id" => 22, "type" => 'stored data', "message" => ''),
            array("id" => 500, "type" => 'warning', "message" =>'A non-fatal error has occurred'),
            array("id" => 501, "type" => 'warning', "message" =>'Too much time elapsed storing data - resyncing'),
            array("id" => 502, "type" => 'warning', "message" =>'Invalid match-data detected. Attempting to restore data'),
            array("id" => 503, "type" => 'warning', "message" =>'Invalid match-data. Restoration failed'),
            array("id" => 1000, "type" => 'error', "message" =>'A fatal error has occurred'),
            array("id" => 1001, "type" =>'file error', "message" =>'An error involving files has occurred')
        );
        foreach($codes as $code)
        {
			$this->db->insert($this->_table, $code);
        }
	}
}

?>