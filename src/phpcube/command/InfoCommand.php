<?php
namespace phpcube\command;

use pocketmine\command\{Command, CommandSender};
use phpcube\CubeInfo;
use pocketmine\player\Player;

class InfoCommand extends Command
{
    /**
     * @var CubeInfo
     */
    public CubeInfo $loader;

    /**
     * @param CubeInfo $loader
     */
    public function __construct(CubeInfo $loader)
    {
        $this->loader = $loader;
        parent::__construct("cinfo", "Информация", null, []);
        $this->setPermission("cmd.cinfo");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if ($sender instanceof Player) {
            if(!isset($args[0])) {
                $sender->sendMessage("Использование: /cinfo <команда>");
                return false;
            }
            $this->loader->sendPreview($sender, strval($args[0]));
        } else {
            $sender->sendMessage('Эта команда доступна только в игре.');
        }
        return true;
    }
}