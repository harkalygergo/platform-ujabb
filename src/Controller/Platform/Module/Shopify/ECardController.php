<?php

namespace App\Controller\Platform\Module\Shopify;

use App\Controller\Platform\_PlatformAbstractController;
use App\Entity\Platform\Module\Shopify\ECard;
use App\Repository\Platform\Module\Shopify\ECardRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ECardController extends _PlatformAbstractController
{
    public function __construct(public ManagerRegistry $doctrine)
    {
    }

    #[Route('/shopify/ecard/order', name: 'shopify_ecard_webhook')]
    public function webhook(): JsonResponse
    {
        // Get the raw POST data sent by Shopify
        $webhookContent = file_get_contents('php://input');
        $orderDetails = json_decode($webhookContent, true);
        $isECardProductOrdered = false;

        foreach ($orderDetails['line_items'] as $lineItem) {
            $skuFirstPart = explode('-', $lineItem['sku'])['0'];

            if (in_array($skuFirstPart, ['FSMP01', 'FSMS01'])) {
                $isECardProductOrdered = true;
            }
        }

        if ($isECardProductOrdered) {
            // Save the order JSON to the database
            $eCard = new ECard();
            $eCard->setOrderJSON($webhookContent);
            $em = $this->doctrine->getManager();
            $em->persist($eCard);
            $em->flush();
        }

        return new JsonResponse('Order Webhook');
    }

    #[Route('/{_locale}/shopify/ecard/list/', name: 'shopify_ecard_list')]
    public function list(ECardRepository $repository): Response
    {
        $dataList = $repository->findAll();

        $data = [
            'title' => '<i class="bi bi-card-list"></i> eCard',
            'dataList' => $dataList
        ];

        return $this->render('platform/backend/v1/list.html.twig', $data);
    }


}
