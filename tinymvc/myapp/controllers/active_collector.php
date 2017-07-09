<?php
require_once("../../../htdocs/scripts.php");
$collector_started = false; // a hack to make this file load the framework AND execute itself
if (class_exists('Active_Collector_Controller', false) === false)
{ // only create the class once; necessary because calling this file will ...
	// define the class when loading the framework, and then it is defined again due to calling this file
	class Active_Collector_Controller extends TinyMVC_Controller
	{
		public function start_collector($region)
		{
			echo "$region\n";
			return "";
		}
	}
	$collector_started = true; // a hack to make this file load the framework AND execute itself
}
if ($collector_started === true) { // a hack to make this file load the framework AND execute itself
	$tmvc->main('active_collector', 'start_collector', 1); // start the active collector
}
?>