<?php

namespace App\Controller\Platform\Module\Shopify;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class Ecard
{
    #[Route('/shopify/ecard/order', name: 'shopify_ecard_webhook')]
    public function webhook(Request $request): JsonResponse
    {
        return new JsonResponse('Order Webhook');
    }

}
