<?php

declare(strict_types=1);

namespace Game\Router\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Game\Router\Response;

final class Marketplace extends \Game\Router\Controller {

    public function buy(Request $request, Response $response, array $arguments): string {
        $player = $this->context->player;
        $foreignOffers = \Game\DB\Marketplace::getInstance()->getAllForeignOffers($player->getUid());

        $view = new \View();
        $view->assign('offers', $foreignOffers);

        return $view->render('marketplace/buy.phtml');
    }

    public function offer(Request $request, Response $response, array $arguments): string {
        $player = $this->context->player;
        $ownOffers = \Game\DB\Marketplace::getInstance()->getAllOffersInVillage($player->getVillage()->getId());

        $view = new \View();
        $view->assign('ownOffers', $ownOffers);

        return $view->render('marketplace/offer.phtml');
    }
}
