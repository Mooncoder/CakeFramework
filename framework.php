<?
/*
__PocketMine Plugin__
name=Framework
description=A Dynamic Framework.
version=1.0.0
author=sekjun9878
class=Framework
apiversion=7,8,9,10
*/

class Framework implements Plugin
{
	private $api, $server;

	private $config;

	private $BootStrap;

	public function __construct(ServerAPI $api, $server = false)
	{
		$this->api = $api;
		$server = ServerAPI::request();
	}

	public function init()
	{
		$this->config['config'] = new Config($this->api->plugin->configPath($this) . "config.yml", CONFIG_YAML, array(
			"enable" => false,
			"BFSseverity" => 3, //1:Info 2:notice 3:warning 4:error 5:danger
		));

		//Include Bootstrap - Bootstrap variable can be later unset to reload framework.
		require_once($this->api->plugin->configPath($this) . "libs/BootStrap.php");
		$BootStrap = new BootStrap(array(
			"config" => $this->config['config'],
			"api" => $this->api,
			"server" => $this->server,
		));

		$this->api->addHandler("console.command", array($BootStrap, "commandHandler"), 50);
	}
}