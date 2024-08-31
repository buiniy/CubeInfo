<?php

declare(strict_types=1);

namespace phpcube;

use phpcube\book\CubeBookLibrary;
use phpcube\command\InfoCommand;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class CubeInfo extends PluginBase
{

    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        CubeBookLibrary::register($this);

        $this->getServer()->getCommandMap()->register("cinfo", new InfoCommand($this));
    }

    public function sendPreview(Player $player, string $command): void
    {

        if(!$this->getConfig()->exists($command)) {
            $player->sendMessage(TextFormat::colorize("§c§lТакой команды существует!"));
            return;
        }

        $item = VanillaItems::WRITTEN_BOOK();

        foreach ($this->getConfig()->get($command) as $pageId => $pageText) {
            $page = $pageId - 1;
            $item->setPageText($page, TextFormat::colorize($pageText));
        }
        CubeBookLibrary::sendPreview($player, $item);
    }

}
