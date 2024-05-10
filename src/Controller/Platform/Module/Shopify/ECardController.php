<?php

namespace App\Controller\Platform\Module\Shopify;

use App\Entity\Platform\Module\Shopify\ECard;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Service\Attribute\Required;

class ECardController
{
    #[Required]
    private ManagerRegistry $doctrine;

    #[Route('/shopify/ecard/order', name: 'shopify_ecard_webhook')]
    public function webhook(): JsonResponse
    {
        // Get the raw POST data sent by Shopify
        $webhookContent = file_get_contents('php://input');

        // Save the order JSON to the database
        $eCard = new ECard();
        $eCard->setOrderJSON($webhookContent);
        $em = $this->doctrine->getManager();
        $em->persist($eCard);
        $em->flush();

        return new JsonResponse('Order Webhook');
    }

}
