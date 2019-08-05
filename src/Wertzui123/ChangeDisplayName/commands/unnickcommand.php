<?php

namespace Wertzui123\ChangeDisplayName\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use Wertzui123\ChangeDisplayName\Main;

class unnickcommand extends Command
{

    private $plugin;

    public function __construct(Main $plugin, array $data)
    {
        parent::__construct($data["command"], $data["description"], null, $data["aliases"]);
        $this->setPermission("cdn.cmd.unnick");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $cfg = $this->plugin->getConfig()->getAll();
        $msgs = $this->plugin->getMSGS()->getAll();

        if(!$sender->hasPermission($this->getPermission())){
            $sender->sendMessage($msgs[""]);
            return true;
        }

        if(isset($args[0])){
            $player = $this->plugin->getServer()->getPlayer($player = implode(" ", $args)) ?? $sender;
        }else{
            $player = $sender;
        }

        if($player === $sender) {
            if(!$sender instanceof Player){
                $sender->sendMessage($msgs["unnick_run_ingame"]);
                return true;
            }
            $player->setDisplayName($player->getName());
            $sender->sendMessage($msgs["unnick_succes_self"]);
            }else{
            $player->setDisplayName($player->getName());
            $sender->sendMessage(str_replace("{player}", $player->getName(), $msgs["unnick_succes_other"]));
        }
        return true;
    }

}