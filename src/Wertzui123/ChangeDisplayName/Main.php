<?php

declare(strict_types=1);

namespace Wertzui123\ChangeDisplayName;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use Wertzui123\ChangeDisplayName\commands\cdncommand;
use Wertzui123\ChangeDisplayName\commands\realnamecommand;
use Wertzui123\ChangeDisplayName\commands\unnickcommand;

class Main extends PluginBase implements Listener
{

    private $msgs;
    private $cfgversion = 3.0;

    public function onEnable(): void
    {
        $this->ConfigUpdater($this->cfgversion);
        $this->msgs = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
        $cfg = $this->getConfig()->getAll();
        $this->getServer()->getCommandMap()->register("ChangeDisplayName", new cdncommand($this, ["command" => $cfg["changedisplayname_command"], "description" => $cfg["changedisplayname_description"], "aliases" => $cfg["changedisplayname_aliases"]]));
        $this->getServer()->getCommandMap()->register("ChangeDisplayName", new realnamecommand($this, ["command" => $cfg["realname_command"], "description" => $cfg["realname_description"], "aliases" => $cfg["realname_aliases"]]));
        $this->getServer()->getCommandMap()->register("ChangeDisplayName", new unnickcommand($this, ["command" => $cfg["unnick_command"], "description" => $cfg["unnick_description"], "aliases" => $cfg["unnick_aliases"]]));
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

    }

    public function getMSGS(): Config
    {
        return $this->msgs;
    }

    public function ConfigUpdater($version)
    {
        $cfgpath = $this->getDataFolder() . "config.yml";
        $msgpath = $this->getDataFolder() . "messages.yml";
        if (file_exists($cfgpath)) {
            $cfgversion = $this->getConfig()->get("version");
            if ($cfgversion !== $version) {
                $this->getLogger()->info("Your config has been renamed to config-" . $cfgversion . ".yml and your messages file has been renamed to messages-" . $cfgversion . ".yml. That's because your config version wasn't the latest avable. So we created a new config and a new messages file for you!");
                rename($cfgpath, $this->getDataFolder() . "config-" . $cfgversion . ".yml");
                rename($msgpath, $this->getDataFolder() . "messages-" . $cfgversion . ".yml");
                $this->saveResource("config.yml");
                $this->saveResource("messages.yml");
            }
        } else {
            $this->saveResource("config.yml");
            $this->saveResource("messages.yml");
        }
    }

    public function getPlayerByDisplayName($dn): ?Player
    {
        foreach ($this->getServer()->getOnlinePlayers() as $player){
            if($player->getDisplayName() == $dn){
                return $player;
            }
        }
        return null;
    }
}
