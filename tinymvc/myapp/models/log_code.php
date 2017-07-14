<?php
/*
id int(11),
type varchar(100),
message varchar(255),
PRIMARY KEY (id)
*/
class log_code extends TinyMVC_Model
{
	protected $_table = "log_code";
	protected $pk = "id";


	public function store_codes()
	{
		$codes = array(
			array("id" => -1, "type" => 'database error', "message" => 'A database error has occurred'),
            array("id" => 0, "type" => 'note', "message" => ''),
            array("id" => 1, "type" => 'info', "message" => 'Synchronizing to in-game clock for match'),
            array("id" => 2, "type" => 'info', "message" => 'Synchronizing complete for match'),
            array("id" => 3, "type" => 'info', "message" => 'Match-reset detected'),
            array("id" => 4, "type" => 'idle', "message" => 'Idling for time (microseconds)'),
            array("id" => 5, "type" => 'process-start', "message" => 'Starting process'),
            array("id" => 6, "type" => 'process-end', "message" => 'Finished process'),
            array("id" => 17, "type" => 'file-deletion', "message" => 'Removing log file'),
            array("id" => 18, "type" => 'folder-deletion', "message" => 'Removing log folder'),
            array("id" => 19, "type" => 'file-removal prep', "message" => 'Preparing to remove files'),
            array("id" => 20, "type" => 'storing data', "message" => 'Storing data'),
            array("id" => 21, "type" => 'stored data', "message" => 'Stored data'),
            array("id" => 500, "type" => 'warning', "message" =>'A non-fatal error has occurred'),
            array("id" => 501, "type" => 'warning', "message" =>'Invalid match-data detected'),
            array("id" => 1000, "type" => 'error', "message" =>'A fatal error has occurred'),
            array("id" => 1001, "type" =>'file error', "message" =>'An error involving files has occurred')
        );
        foreach($codes as $code)
        {
			$this->save($code);
        }
	}
}

?>