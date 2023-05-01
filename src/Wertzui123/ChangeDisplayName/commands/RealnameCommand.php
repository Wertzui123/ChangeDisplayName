<?php

namespace Wertzui123\ChangeDisplayName\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use Wertzui123\ChangeDisplayName\Main;

class RealnameCommand extends Command implements PluginOwned
{

    private $plugin;

    public function __construct(Main $plugin)
    {
        parent::__construct($plugin->getConfig()->getNested('command.realname.command'), $plugin->getConfig()->getNested('command.realname.description'), $plugin->getConfig()->getNested('command.realname.usage'), $plugin->getConfig()->getNested('command.realname.aliases'));
        $this->setPermissions(['changedisplayname.command.realname']);
        $this->setPermissionMessage($plugin->getMessage('command.realname.noPermission'));
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!isset($args[0])) {
            $sender->sendMessage($this->plugin->getMessage('command.realname.passPlayer'));
            return;
        }
        $displayName = implode(' ', $args);
        $player = $this->plugin->getPlayerByNickname($displayName);
        if ($player instanceof Player) {
            $sender->sendMessage($this->plugin->getMessage('command.realname.success', ['{displayname}' => $displayName, '{realname}' => $player->getName()]));
        } else {
            $sender->sendMessage($this->plugin->getMessage('command.realname.noPlayer'));
        }
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }

}