CakeFramework
=============

A Simple WIP Framework for PocketMine

#How to use
Right now this framework only supports commands.
To add a new command, make a new file at Plugins/HelloWorld(PluginName)/Commands/Hello(CommandName).php and put
```
$this->API->broadcast("Hello World");
```
in the plugin.

Supported Variables are($this->variable):
```
$this->API
$this->Server
$this->Player(If the command gets a Player object, may be a username so be sure to check it.)
$this->Level(If the command gets a Player object which has a level object)
```
