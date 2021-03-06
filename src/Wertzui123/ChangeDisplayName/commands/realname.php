<?php

namespace Wertzui123\ChangeDisplayName\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use Wertzui123\ChangeDisplayName\Main;

class realname extends Command implements PluginIdentifiableCommand
{

    private $plugin;

    public function __construct(Main $plugin)
    {
        parent::__construct($plugin->getConfig()->getNested('command.realname.command'), $plugin->getConfig()->getNested('command.realname.description'), $plugin->getConfig()->getNested('command.realname.usage'), $plugin->getConfig()->getNested('command.realname.aliases'));
        $this->setPermission('changedisplayname.command.realname');
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender->hasPermission($this->getPermission())) {
            $sender->sendMessage($this->plugin->getMessage('command.realname.noPermission'));
            return;
        }
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
        return;
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }

}