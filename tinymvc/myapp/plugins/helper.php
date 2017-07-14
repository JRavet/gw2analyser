<?php

class helper extends TinyMVC_Controller {

	public function get_time_interval()
	{
		echo "time itnerval\n";
	}
	public function get_server_owner()
	{
		echo "got server owner\n";
	}
	public function estimate_yaks_delivered()
	{
		echo "estimated yaks delivered\n";
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
		echo "calculated ppt\n";
	}
	public function log_message()
	{
		echo "logged a message\n";
	}
}

?>