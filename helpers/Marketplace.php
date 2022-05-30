<?php

namespace Game\Helpers;

use Game\Build\BuildingIdType;
use Game\DB;
use Game\Village;

final class Marketplace {

    private const RES_LUMBER = 1;
    private const RES_CLAY = 2;
    private const RES_IRON = 3;
    private const RES_CROP = 4;

    /**
     * Remove marketplace offer by id and return back success of offer deletion
     */
    public static function removeOffer(int $id): bool {
        $db = DB\Marketplace::getInstance();
        $row = $db->getOfferById($id);
        if ($row && $db->deleteOfferById($id) > 0) {
            $result = [
                'r1' => 0,
                'r2' => 0,
                'r3' => 0,
                'r4' => 0,
            ];
            $result['r' . $row['rid1']] += (int)$row['m1'];
            Village::getInstance($row['did'])->changeResources($result['r1'], $result['r2'], $result['r3'], $result['r4']);

            return true;
        }

        return false;
    }

    /**
     * Marketplace Offer Accepted: Sends raw materials to buyers
     * @throws \Game\ExceptionNotice
     * @throws \Game\ExceptionWarning
     */
    public static function acceptOffer(int $offerId, Village $village): void {
        // Seller's offer
        $offer = DB\Marketplace::getInstance()->getOfferById($offerId);
        if ($offer === null) {
            throw new \Game\ExceptionNotice('Offer doesn\'t exist');
        }

        $marketPlaceData = DB\Building::getInstance()->getBuildingByVillageIdAndType($village->getId(), BuildingIdType::MARKET);
        if ($marketPlaceData === null) {
            throw new \Game\ExceptionNotice('No marketplace in village');
        }

        switch ($offer['rid2']) {
            case self::RES_LUMBER:
                $requestedResource = $village->getLumberAmount();
                break;
            case self::RES_CLAY:
                $requestedResource = $village->getClayAmount();
                break;
            case self::RES_IRON:
                $requestedResource = $village->getIronAmount();
                break;
            case self::RES_CROP:
                $requestedResource = $village->getCropAmount();
                break;
            default:
                throw new \Game\ExceptionWarning('Unknown resource is requested');
        }

        // Does the buyer have enough raw materials?
        if ($requestedResource < $offer['m2']) {
            throw new \Game\ExceptionNotice('Not enough resources');
        }

        // Amount of resources that the buyer has to ship
        $buyerResources = ['r1' => 0, 'r2' => 0, 'r3' => 0, 'r4' => 0];
        $buyerResources['r' . $offer['rid2']] = $offer['m2'];

        $village->changeResources(-$buyerResources['r1'], -$buyerResources['r2'], -$buyerResources['r3'], -$buyerResources['r4']);

        // remove offer
        DB\Marketplace::getInstance()->deleteOfferById($offerId);
    }

    public function createOffer(array $data, Player $player): void {
        $reserved = ['r1' => 0, 'r2' => 0, 'r3' => 0, 'r4' => 0];
        $reserved['r' . $data['rid1']] += (int)$data['m1'];

        $village = $player->getVillage();
        $village->changeResources(-$reserved['r1'], -$reserved['r2'], -$reserved['r3'], -$reserved['r4']);

        DB\Marketplace::getInstance()->createOffer($data, $player->getUid(), $village->getId());
    }
}
