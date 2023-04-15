<?php

namespace Wertzui123\ChangeDisplayName\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use Wertzui123\ChangeDisplayName\Main;

class UnnickCommand extends Command implements PluginOwned
{

    private $plugin;

    public function __construct(Main $plugin)
    {
        parent::__construct($plugin->getConfig()->getNested('command.unnick.command'), $plugin->getConfig()->getNested('command.unnick.description'), $plugin->getConfig()->getNested('command.unnick.usage'), $plugin->getConfig()->getNested('command.unnick.aliases'));
        $this->setPermissions(['changedisplayname.command.unnick']);
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender->hasPermission($this->getPermissions()[0])) {
            $sender->sendMessage($this->plugin->getMessage('command.unnick.noPermission'));
            return;
        }
        if (isset($args[0])) {
            $player = $this->plugin->getPlayerByNickname(implode(' ', $args)) ?? $this->plugin->getServer()->getPlayerByPrefix(implode(' ', $args)) ?? $sender;
        } else {
            $player = $sender;
            if (!$sender instanceof Player) {
                $sender->sendMessage($this->plugin->getMessage('command.unnick.runIngame'));
                return;
            }
        }
        $player->setDisplayName($player->getName());
        if ($this->plugin->getServer()->getPluginManager()->getPlugin('PurePerms')) {
            $purePerms = $this->plugin->getServer()->getPluginManager()->getPlugin('PurePerms');
            $group = $purePerms->getGroup($purePerms->getUserDataMgr()->getData($player)['group']);
        } else {
            $purePerms = null;
            $group = '/';
        }
        if ($purePerms !== null) {
            $purePerms->setGroup($player, $group);
        } else {
            $player->setNameTag($player->getName());
        }
        foreach ($sender->getServer()->getOnlinePlayers() as $player) {
            $player->getNetworkSession()->syncPlayerList($this->plugin->getServer()->getOnlinePlayers());
        }
        if ($player === $sender) {
            $sender->sendMessage($this->plugin->getMessage('command.unnick.success.self'));
        } else {
            $sender->sendMessage($this->plugin->getMessage('command.unnick.success.other', ['{player}' => $player->getName()]));
        }
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }

}