<?
/*
__PocketMine Plugin__
name=Framework
description=A Dynamic framework.
version=1.0.0
author=sekjun9878
class=framework
apiversion=7,8,9,10
*/

class Framework implements Plugin
{
	private $api, $server;

	private $BootStrap;

	public function __construct(ServerAPI $api, $server = false)
	{
		$this->api = $api;
		$server = ServerAPI::request();
	}

	public function init()
	{
		//Include Bootstrap - Bootstrap variable can be later unset to reload framework.
		require_once($this->api->plugin->configPath($this) . "Libs/BootStrap.php");
		$BootStrap = new BootStrap(array(
			"api" => $this->api,
			"server" => $this->server,
			"path" => $this->api->plugin->configPath($this),
		));

		$this->api->addHandler("console.command", array($BootStrap, "commandHandler"), 50);
		$this->api->addHandler("console.command.unknown", function () {
			return false;
		}, 50);
	}

	public function __destruct()
	{

	}
}