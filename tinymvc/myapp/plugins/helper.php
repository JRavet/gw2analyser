<?php

class helper extends TinyMVC_Controller
{

	private $log_code;
	private $match_id;

	public function __construct($match_id)
	{
		parent::__construct();
		$this->log_code = new log_code();
		$this->match_id = $match_id;
	}

	public function get_time_interval()
	{

	}
	public function get_server_owner()
	{

	}
	public function estimate_yaks_delivered()
	{

	}
	public function get_week_num($start_time)
	{
		$week = new DateTime($start_time);
		$week = $week->diff(new DateTime("2017-01-01 00:00:00")); //hardcoded time for arbitrary reasons
		$week = (int)(($week->days)/7);
		return $week;
	}
	public function calculate_ppt()
	{

	}

	public function write_to_file($dir, $log_name, $message)
	{
		try
		{
			$file = fopen($dir . "/" . $log_name, "a"); // append to file
			if ($file === FALSE) throw new Exception("File not found"); // if file did not exist, throw error
		}
		catch (Exception $e)
		{
			mkdir($dir, 0770, TRUE); // group & user get all perms; world gets none. recursively create all directories
			$file = fopen($dir . "/" . $log_name, "a");
		}

		fwrite($file, $message);
		fclose($file);
	}

	public function log_message($code, $msg="")
	{
		$details = $this->log_code->find(array("id"=>$code));

		$message = date("Y-m-d H:i:s") . "," . $details['id'] . "," . $details['type'] . "," . $details['message'] . "," . $msg . "\n";

		$dir = PATH_LOG . date('Y-m-d');
		$log_name = MATCH_ID . ".csv";
		$this->write_to_file($dir, $log_name, $message);

		if ($code >= 500 || $code == -1)
		{ // if the code is a warning or worse, write to a special log
			$log_name = "error-log-" . MATCH_ID . ".csv";
			$this->write_to_file($dir, $log_name, $message);
		}

		echo $message; // put message in console as well
	}

}

?>