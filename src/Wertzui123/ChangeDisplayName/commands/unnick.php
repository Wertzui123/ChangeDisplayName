<?php

namespace Wertzui123\ChangeDisplayName\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use Wertzui123\ChangeDisplayName\Main;

class unnick extends Command implements PluginIdentifiableCommand
{

    private $plugin;

    public function __construct(Main $plugin)
    {
        parent::__construct($plugin->getConfig()->getNested('command.unnick.command'), $plugin->getConfig()->getNested('command.unnick.description'), $plugin->getConfig()->getNested('command.unnick.usage'), $plugin->getConfig()->getNested('command.unnick.aliases'));
        $this->setPermission('changedisplayname.command.unnick');
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender->hasPermission($this->getPermission())) {
            $sender->sendMessage($this->plugin->getMessage('command.unnick.noPermission'));
            return;
        }
        if (isset($args[0])) {
            $player = $this->plugin->getPlayerByNickname(implode(' ', $args)) ?? $this->plugin->getServer()->getPlayer(implode(' ', $args)) ?? $sender;
        } else {
            $player = $sender;
        }
        if ($player === $sender) {
            if (!$sender instanceof Player) {
                $sender->sendMessage($this->plugin->getMessage('command.unnick.runIngame'));
                return;
            }
            $player->setDisplayName($player->getName());
            $sender->sendMessage($this->plugin->getMessage('command.unnick.success.self'));
        } else {
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
            $sender->sendMessage($this->plugin->getMessage('command.unnick.success.other', ['{player}' => $player->getName()]));
        }
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }

}