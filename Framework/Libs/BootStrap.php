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
	private $config;

	public function __construct($data)
	{
		$this->Registered = new stdClass();
		$this->Registered->Commands = new stdClass();

		$this->path = $data['path'];
		$this->api = $data['api'];
		$this->server = $data['server'];

		$this->config['config'] = new Config($this->path . "config.yml", CONFIG_YAML, array(
			"Commands" => array(
				"framework" => ""
			),
		));
	}

	public function mapCommands()
	{
		$this->config['config']->reload();
		foreach ($this->config['config']->get("Commands") as $key => $value) {
			$this->Registered->Commands->$key = new stdClass();
			$this->Registered->Commands->$key = $value;
		}
	}

	public function commandHandler($data, $event)
	{
		if ($data['cmd'] == 'reload') {
			console("Reloading Framework...");
			$this->mapCommands();
			return true;
		}
		foreach ($this->Registered->Commands as $key => $value) {
			if (strtolower($key) == $data['cmd']) {
				if (file_exists($this->path . "Commands/" . ucfirst(strtolower($key)) . ".php") == true) {
					include($this->path . "Commands/" . ucfirst(strtolower($key)) . ".php");
				} else {
					console("Warning: Failed to perform command $key: Could not open file $value");
				}
			} else {
				console("Command doesn't exist! Use /help\n");
			}
		}
	}
}