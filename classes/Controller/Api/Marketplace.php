<?php

declare(strict_types=1);

namespace Game\Router\Controller\Api;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Game\Helpers\Marketplace as MarketplaceHelper;
use Game\Router\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Game\ExceptionNotice;

final class Marketplace extends Api {

    public function createOffer(Request $request, Response $response, array $args): Response {
        $payload = $request->getParsedBody();

        if (MarketplaceHelper::createOffer($payload, $this->context->player)) {
            return $this->renderError(StatusCode::STATUS_BAD_REQUEST, new ExceptionNotice('unexpected error'));
        }

        return $response->withStatus(StatusCode::STATUS_NO_CONTENT);
    }

    public function getOffer(Request $request, Response $response, array $args): Response {
        $offerId = (int)$args['id'];
        $result = \Game\DB\Marketplace::getInstance()->getOfferById($offerId);

        return $response->withJson($result);
    }


    public function getAllOffer(Request $request, Response $response, array $args): Response {
        $currentVillage = $this->context->player->getVillage()->getId();
        $result = \Game\DB\Marketplace::getInstance()->getAllOffersInVillage($currentVillage);

        return $response->withJson($result);
    }

    public function removeOffer(Request $request, Response $response, array $args): Response {
        $offerId = (int)$args['id'];

        if (!MarketplaceHelper::removeOffer($offerId)) {
            return $this->renderError(StatusCode::STATUS_BAD_REQUEST, new ExceptionNotice('unexpected error'));
        }

        return $response->withStatus(StatusCode::STATUS_NO_CONTENT);
    }

    public function acceptOffer(Request $request, Response $response, array $args): Response {
        $offerId = (int)$args['id'];

        MarketplaceHelper::acceptOffer($offerId, $this->context->player->getVillage());

        return $response->withStatus(StatusCode::STATUS_NO_CONTENT);
    }
}
