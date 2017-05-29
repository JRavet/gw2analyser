<?php

/**
 * default.php
 *
 * default application controller
 *
 * @package		TinyMVC
 * @author		Monte Ohrt
 */

class Default_Controller extends TinyMVC_Controller
{
	public function index()
	{
		$this->load->model("guild","guild_model");
		$guilds = $this->guild_model->get_guilds();

		$this->view->assign("guilds", $guilds);
		$this->view->display('index_view');
	}
	private function _test()
	{
		$counter = 6;
		while ($counter > 0) 
		{
			usleep(100000);
			echo "$counter\n";
			$counter--;
		}
		return "success _test\n";
	}
	public function test()
	{
		$counter = 6;
		while ($counter > 0) 
		{
			usleep(100000);
			echo "$counter\n";
			$counter--;
		}
		echo $this->_test();
		return "success test";
	}
}
?>
