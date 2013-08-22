<?php
/**
 * Created by JetBrains PhpStorm.
 * User: sekjun9878
 * Date: 22/08/13
 * Time: 5:16 PM
 * To change this template use File | Settings | File Templates.
 */

class BootStrap
{
	private $Registered;
	private $api, $server;
	private $path;

	public function __construct($data)
	{
		$this->Registered = new stdClass();
		$this->Registered->Commands = new stdClass();

		$this->path = $data['path'];
		$this->api = $data['api'];
		$this->server = $data['server'];

		foreach ($data['config']->get("Commands") as $key => $value) {
			$this->Registered->Commands->$key = new stdClass();
			$this->Registered->Commands->$key = $value;
			if (include ($value) != 'OK')
				console("Warning: Failed to register command $key: Could not open file $value");
		}
	}

	public function commandHandler($data, $event)
	{
		foreach ($this->Registered->Commands as $key => $value) {
			if (strtolower($key) == $data['cmd']) {
				include($this->path . "Commands/" . ucfirst(strtolower($key)) . ".php");
			}
		}
	}
}