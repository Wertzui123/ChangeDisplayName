<?php

declare(strict_types=1);

namespace Wertzui123\ChangeDisplayName;

use pocketmine\Player;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{

	public function onEnable() : void{
	    $this->saveResource("config.yml");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}



	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		switch($command->getName()){
	 case "changedisplayname":
	 
	 
	 $settings = new Config($this->getDataFolder() . "config.yml", Config::YAML);
$runingame = $settings->get("run_in_game");
$usage = $settings->get("usage");
$cdnsucces = $settings->get("cdn_succes");
$missingpermission = $settings->get("missing_permission");
$nicker = $sender->getName();
$nickname = $settings->get("nickname_format");

		if(!$sender instanceof Player) {
			$sender->sendMessage($runingame);
			return true;
		}
		
		if($this->getServer()->getPluginManager()->getPlugin("PurePerms")) {
	$purePerms = $this->getServer()->getPluginManager()->getPlugin("PurePerms");
    $group1 = $purePerms->getUserDataMgr()->getData($sender)['group'];
	$group = $purePerms->getGroup("$group1");
} else {
    $group = "The plugin isn't installed";
}
		
		if($sender->hasPermission("cdn.cmd")) {
				if(empty($args[0])){
					$sender->sendMessage($usage);
					return true;
				} else {
$nickname = str_replace("{nickname}", $args[0], $nickname);
$text = str_replace("{nickname}", $args[0], $cdnsucces);
$nickname = str_replace("{realname}", $sender->getName(), $nickname);
$nickname = str_replace("{rank}", $group, $nickname);
$nickname = str_replace("{group}", $group, $nickname);
$sender->setDisplayName("§r" . $nickname . "§r");
if($this->getServer()->getPluginManager()->getPlugin("PurePerms")){
	$purePerms->setGroup($sender, $group); 
}else{
	$sender->setNameTag("§r" . $nickname . "§r");
}
$sender->sendMessage($text);
      }
      } else{
			$sender->sendMessage($missingpermission);
     }
     return true;
		}
  }
  
	public function onDisable() : void{
	}
}
//This Plugin was written by Wertzui123 and you're not allowed to modify, rewrite it or copy the code into you're plugin!
//You also musn't change the author or the license.
//To adjust it, just use the config.yml in the plugin_data/ChangeDisplayName folder.
//© 2019 Wertzui123
