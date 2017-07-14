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

	public function calc_time_interval()
	{
		return 1;
	}
	public function estimate_yaks_delivered()
	{
		return 142;
	}
	/**
	 * Calculates the number of weeks between a static date and the given time
	 *
	 * @param $start_time
	 * @return void
	**/
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

	/**
	 * Writes a message to a log file. If the file or folder does not it exist, it is created
	 *
	 * @param $dir - directory to write to
	 * @param $log_name - name of file to write to
	 * @param $message - the fully formatted message to write to a file
	 * @return void
	**/
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

	/**
	 * Saves a log-entry to a date-stamped and match-id-stamped location. Also outputs to console
	 * Also generates an extra error-log for warnings and errors
	 *
	 * @param $code - int, the log code to retrieve the message for
	 * @param $msg - optional, string. Additional message to add to end of log-code. Commas are replaced with semicolons
	 * @return void
	**/
	public function log_message($code, $msg="")
	{

		preg_replace(",", ";", $msg); // changes commas to semicolons so as to not break CSVs

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