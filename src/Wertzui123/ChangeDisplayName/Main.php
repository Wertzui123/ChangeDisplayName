<?php

declare(strict_types=1);

namespace Wertzui123\ChangeDisplayName;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use Wertzui123\ChangeDisplayName\commands\NickCommand;
use Wertzui123\ChangeDisplayName\commands\RealnameCommand;
use Wertzui123\ChangeDisplayName\commands\UnnickCommand;

class Main extends PluginBase
{

    /** @var float */
    const CONFIG_VERSION = 4.0;

    /** @var Config */
    private $stringsFile;

    /**
     * Called when the plugin enables
     */
    public function onEnable(): void
    {
        $this->configUpdater();
        $this->stringsFile = new Config($this->getDataFolder() . 'strings.yml', Config::YAML);
        $this->getServer()->getCommandMap()->register('ChangeDisplayName', new NickCommand($this));
        $this->getServer()->getCommandMap()->register('ChangeDisplayName', new RealnameCommand($this));
        $this->getServer()->getCommandMap()->register('ChangeDisplayName', new UnnickCommand($this));
    }

    /**
     * Checks whether the config version is the latest and updates it if it isn't
     */
    private function configUpdater()
    {
        if (!file_exists($this->getDataFolder() . 'config.yml')) {
            $this->saveResource('config.yml');
            $this->saveResource('strings.yml');
            return;
        }
        if (!$this->getConfig()->exists('config-version')) {
            $this->getLogger()->info("§eYour config isn't the latest. ChangeDisplayName renamed your old config to §bconfig-old.yml §6and created a new config. Have fun!");
            rename($this->getDataFolder() . 'config.yml', $this->getDataFolder() . 'config-old.yml');
            rename($this->getDataFolder() . 'strings.yml', $this->getDataFolder() . 'strings.yml');
            $this->saveResource('config.yml', true);
            $this->saveResource('strings.yml', true);
        } elseif ($this->getConfig()->get('config-version') !== self::CONFIG_VERSION) {
            $config_version = $this->getConfig()->get('config-version');
            $this->getLogger()->info("§eYour Config isn't the latest. ChangeDisplayName renamed your old config to §bconfig-" . $config_version . ".yml §6and created a new config. Have fun!");
            rename($this->getDataFolder() . 'config.yml', $this->getDataFolder() . 'config-' . $config_version . '.yml');
            rename($this->getDataFolder() . 'strings.yml', $this->getDataFolder() . 'strings-' . $config_version . '.yml');
            $this->saveResource('config.yml');
            $this->saveResource('strings.yml');
        }
    }

    /**
     * Returns the file containing all messages and strings
     * @return Config
     */
    public function getStringsFile(): Config
    {
        return $this->stringsFile;
    }

    /**
     * @internal
     * Returns a string from the strings file
     * @param string $key
     * @param array $replace [optional]
     * @param mixed $default [optional]
     * @return string|mixed
     */
    public function getString(string $key, $replace = [], $default = '')
    {
        return str_replace(array_keys($replace), $replace, $this->getStringsFile()->getNested($key, $default));
    }

    /**
     * @internal
     * Returns a message from the strings file
     * @param string $key
     * @param array $replace [optional]
     * @param mixed $default [optional]
     * @return string|mixed
     */
    public function getMessage(string $key, $replace = [], $default = '')
    {
        return $this->getString($key, $replace, $default);
    }

    /**
     * Returns a player with the nickname $nickname, or null if not found
     * @param string $nickname
     * @return Player|null
     */
    public function getPlayerByNickname(string $nickname): ?Player
    {
        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            if (TextFormat::clean($player->getDisplayName()) === TextFormat::clean($nickname)) {
                return $player;
            }
        }
        return null;
    }

}