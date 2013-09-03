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
		$this->Registered->Plugins = new stdClass();
		$this->Registered->Commands = new stdClass();

		$this->path = $data['path'];
		$this->api = $data['api'];
		$this->server = $data['server'];

		$this->reloadMap();
	}

	public function reloadMap()
	{
		$results = scandir($this->path . "Plugins");
		foreach ($results as $result) {
			if ($result === '.' or $result === '..') continue;

			if (is_dir($this->path . "Plugins" . '/' . $result)) {
				foreach ($result as $key) {
					$this->Registered->Plugins->$key = true;
					foreach (glob($this->path . "Plugins/$key/Commands/*.php", GLOB_BRACE) as $command) {
						$temp = explode("/", $command);
						$temp = end($temp);
						$temp = substr($temp, 0, -4);
						$temp = ucfirst(strtolower($temp));
						$this->Registered->Commands->$temp = $key;
					}
				}
			}
		}
	}

	public function commandHandler($data, $event)
	{
		if ($data['cmd'] == 'reload') {
			console("Reloading Framework...");
			$this->reloadMap();
			return true;
		}
		foreach ($this->Registered->Commands as $key => $value) {
			if (strtolower($key) == $data['cmd']) {
				if (file_exists($this->path . "Plugins/$value/Commands/" . ucfirst(strtolower($key)) . ".php") == true) {
					$FrameworkContainer = new FrameworkContainer(
						array(
							"Path" => $this->path . "Plugins/$value/Commands/" . ucfirst(strtolower($key)) . ".php",
							"API" => $this->api,
							"Server" => $this->server,
						),
						$data
					);
					unset($FrameworkContainer);
				} else {
					console("Warning: Failed to perform command $key. Failed to open file.");
				}
			} else {
				console("Command doesn't exist! Use /help");
			}
		}
	}
}

class FrameworkContainer
{
	private $API, $Server, $Player, $Level, $Env;

	public function __construct($env, $data)
	{
		$this->API = $env['API'];
		$this->Server = $env['Server'];
		if (isset($data['issuer'])) {
			$this->Player = $data['issuer'];
		} else if (isset($data['player'])) {
			$this->Player = $data['player'];
		}

		if (isset($this->Player->level)) {
			$this->Level = $this->Player->level;
		}

		$this->Env = new stdClass();
		foreach ($env as $key => $value) {
			$this->Env->$key = $value;
		}

		include($this->Env->Path);
	}

	public function __destruct()
	{

	}
}