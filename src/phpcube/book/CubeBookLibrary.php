<?php

declare(strict_types=1);

namespace phpcube\book;

use Closure;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\item\WritableBookBase;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\network\mcpe\protocol\types\inventory\PredictedResult;
use pocketmine\network\mcpe\protocol\types\inventory\TriggerType;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use RuntimeException;

final class CubeBookLibrary {
    protected static bool $registered = false;

    public static function register(PluginBase $plugin): void
    {
        if (CubeBookLibrary::$registered) {
            throw new RuntimeException("Tried to register CubeBookLibrary twice");
        }

        CubeBookLibrary::$registered = true;
    }

    protected static function useItem(Player $player, Item $item): void {
        $item->setCustomName("");

        $oldItem = $player->getInventory()->getItemInHand();
        $networkSession = $player->getNetworkSession();

        $player->getInventory()->setItemInHand($item);
        $networkSession->getInvManager()->syncSlot(
            $player->getInventory(),
            $player->getInventory()->getHeldItemIndex(),
            $networkSession->getTypeConverter()->coreItemStackToNet($item)
        );

        $networkSession->sendDataPacket(InventoryTransactionPacket::create(
            0,
            [],
            UseItemTransactionData::new(
                [],
                UseItemTransactionData::ACTION_CLICK_AIR,
                TriggerType::PLAYER_INPUT,
                new BlockPosition(0, 0, 0),
                0,
                0,
                ItemStackWrapper::legacy(ItemStack::null()),
                Vector3::zero(),
                Vector3::zero(),
                0,
                PredictedResult::SUCCESS
            )
        ));

        $player->getInventory()->setItemInHand($oldItem);
    }

    public static function sendPreview(Player $player, WritableBookBase $book): void {
        CubeBookLibrary::useItem($player, $book);
    }


    public static function sendInput(Player $player, Closure $handler): void {
        $item = VanillaItems::WRITABLE_BOOK();
        $item->setPageText(0, "");
        $item->setPageText(1, "");

        CubeBookLibrary::useItem($player, $item);
    }

    public static function isRegistered(): bool
    {
        return CubeBookLibrary::$registered;
    }
}
