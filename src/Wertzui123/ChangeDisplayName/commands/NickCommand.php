<?php

namespace Wertzui123\ChangeDisplayName\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use Wertzui123\ChangeDisplayName\Main;

class NickCommand extends Command implements PluginOwned
{

    private $plugin;

    public function __construct(Main $plugin)
    {
        parent::__construct($plugin->getConfig()->getNested('command.nick.command'), $plugin->getConfig()->getNested('command.nick.description'), $plugin->getConfig()->getNested('command.nick.usage'), $plugin->getConfig()->getNested('command.nick.aliases'));
        $this->setPermissions(['changedisplayname.command.nick']);
        $this->setPermissionMessage($plugin->getMessage('command.nick.noPermission'));
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage($this->plugin->getMessage('command.nick.runIngame'));
            return;
        }
        if (!isset($args[0])) {
            $sender->sendMessage($this->plugin->getMessage('command.nick.passName'));
            return;
        }
        $displayName = implode(' ', $args);
        if (in_array($displayName, $this->plugin->getConfig()->get('banned_nicknames'))) {
            $sender->sendMessage($this->plugin->getMessage('command.nick.banned'));
            return;
        }
        if ($this->plugin->getPlayerByNickname($displayName) !== null && $this->plugin->getServer()->getPlayerExact($displayName) !== null) {
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
        foreach ($sender->getServer()->getOnlinePlayers() as $player) {
            $player->getNetworkSession()->syncPlayerList($this->plugin->getServer()->getOnlinePlayers());
        }
        $sender->sendMessage($this->plugin->getMessage('command.nick.success', ['{displayname}' => $displayName]));
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }

}