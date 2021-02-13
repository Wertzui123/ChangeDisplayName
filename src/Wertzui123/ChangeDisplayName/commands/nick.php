<?php

namespace Wertzui123\ChangeDisplayName\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use Wertzui123\ChangeDisplayName\Main;

class nick extends Command implements PluginIdentifiableCommand
{

    private $plugin;

    public function __construct(Main $plugin)
    {
        parent::__construct($plugin->getConfig()->getNested('command.nick.command'), $plugin->getConfig()->getNested('command.nick.description'), $plugin->getConfig()->getNested('command.nick.usage'), $plugin->getConfig()->getNested('command.nick.aliases'));
        $this->setPermission('changedisplayname.command.nick');
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage($this->plugin->getMessage('command.nick.runIngame'));
            return;
        }
        if (!$sender->hasPermission($this->getPermission())) {
            $sender->sendMessage($this->plugin->getMessage('command.nick.noPermission'));
            return;
        }
        if (!isset($args[0])) {
            $sender->sendMessage($this->plugin->getMessage('command.nick.passName'));
            return;
        }
        $displayName = implode(' ', $args);
        if(in_array($displayName, $this->plugin->getConfig()->get('banned_nicknames'))) {
            $sender->sendMessage($this->plugin->getMessage('command.nick.banned'));
            return;
        }
        if($this->plugin->getPlayerByNickname($displayName) !== null && $this->plugin->getServer()->getPlayerExact($displayName) !== null){
            $sender->sendMessage($this->plugin->getMessage('command.nick.alreadyTaken'));
            return;
        }

        if ($this->plugin->getServer()->getPluginManager()->getPlugin('PurePerms')) {
            $purePerms = $this->plugin->getServer()->getPluginManager()->getPlugin('PurePerms');
            $group = $purePerms->getGroup($purePerms->getUserDataMgr()->getData($sender)['group']);
        } else {
            $purePerms = null;
            $group = '/';
        }

        $format = str_replace(['{displayname}', '{group}'], [$displayName, $group], $this->plugin->getConfig()->get('nickname_format'));
        $sender->setDisplayName($format);
        if ($purePerms !== null) {
            $purePerms->setGroup($sender, $group);
        } else {
            $sender->setNameTag($format);
        }
        $sender->sendMessage($this->plugin->getMessage('command.nick.success', ['{displayname}' => $displayName]));
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }

}