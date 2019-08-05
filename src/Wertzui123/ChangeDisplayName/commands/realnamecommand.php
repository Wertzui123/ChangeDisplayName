<?php

namespace Wertzui123\ChangeDisplayName\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use Wertzui123\ChangeDisplayName\Main;

class realnamecommand extends Command
{

    private $plugin;

    public function __construct(Main $plugin, array $data)
    {
        parent::__construct($data["command"], $data["description"], null, $data["aliases"]);
        $this->setPermission("cdn.cmd.realname");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $cfg = $this->plugin->getConfig()->getAll();
        $msgs = $this->plugin->getMSGS()->getAll();

        if(!$sender->hasPermission($this->getPermission())){
            $sender->sendMessage($msgs["realname_no_permissions"]);
            return true;
        }

        if(!isset($args[0])){
            $sender->sendMessage($cfg["realname_usage"]);
            return true;
        }

        $dn = implode(" ", $args);
        $player = $this->plugin->getPlayerByDisplayName($dn);
        if($player instanceof Player) {
            $sender->sendMessage(str_replace(["{displayname}", "{realname}"], [$dn, $player->getName()], $msgs["realname_succes"]));
        }else {
            $sender->sendMessage($msgs["realname_no_player"]);
        }
        return true;
    }

}