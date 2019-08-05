<?php

namespace Wertzui123\ChangeDisplayName\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use Wertzui123\ChangeDisplayName\Main;

class cdncommand extends Command
{

    private $plugin;

    public function __construct(Main $plugin, array $data)
    {
        parent::__construct($data["command"], $data["description"], null, $data["aliases"]);
        $this->setPermission("cdn.cmd.cdn");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $cfg = $this->plugin->getConfig()->getAll();
        $msgs = $this->plugin->getMSGS()->getAll();

        if (!$sender instanceof Player) {
            $sender->sendMessage($msgs["cdn_run_ingame"]);
            return true;
        }

        if ($this->plugin->getServer()->getPluginManager()->getPlugin("PurePerms")) {
            $purePerms = $this->plugin->getServer()->getPluginManager()->getPlugin("PurePerms");
            $group1 = $purePerms->getUserDataMgr()->getData($sender)['group'];
            $group = $purePerms->getGroup($group1);
        } else {
            $purePerms = null;
            $group = "PurePerms isn't installed";
        }

        if ($sender->hasPermission("cdn.cmd")) {
            if (!isset($args[0])) {
                $sender->sendMessage($cfg["changedisplayname_usage"]);
            } else {
                $dn = implode(" ", $args);
                $nickname = str_replace("{nickname}", $dn, $cfg["nickname_format"]);
                $text = str_replace("{nickname}", $dn, $msgs["cdn_succes"]);
                $nickname = str_replace("{realname}", $sender->getName(), $nickname);
                $nickname = str_replace(["group", "{rank}"], $group, $nickname);
                $sender->setDisplayName($nickname);
                if ($purePerms !== null) {
                    $purePerms->setGroup($sender, $group);
                } else {
                    $sender->setNameTag($nickname);
                }
                $sender->sendMessage($text);
            }
        } else {
            $sender->sendMessage($msgs["cdn_missing_permissions"]);
        }
        return true;
    }

}