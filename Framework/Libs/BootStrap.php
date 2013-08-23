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

		$this->config['config'] = new Config($this->path . "Commands.yml", CONFIG_YAML, array(
			"Commands" => array(
				"framework" => ""
			),
		));

		$this->mapCommands();
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
					$Container = new Container(
						array(
							"path" => $this->path . "Commands/" . ucfirst(strtolower($key)) . ".php",
							"api" => $this->api,
							"server" => $this->server,
						),
						$data
					);
				} else {
					console("Warning: Failed to perform command $key. Failed to open file.");
				}
			} else {
				console("Command doesn't exist! Use /help\n");
			}
		}
	}
}

class Container
{
	private $API, $Server, $Player, $Level;

	public function _construct($env, $data)
	{
		$this->API = $env['api'];
		$this->Server = $env['server'];
		if (isset($data['issuer'])) {
			$this->Player = $data['issuer'];
		} else if (isset($data['player'])) {
			$this->Player = $data['player'];
		}

		$this->Level = $this->Player->level;

		include($env['path']);
	}

	public function _destruct()
	{

	}
}