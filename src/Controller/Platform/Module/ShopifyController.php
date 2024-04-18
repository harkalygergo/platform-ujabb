<?php

namespace App\Controller\Platform\Module;

use App\Entity\Platform\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_USER)]
class ShopifyController extends AbstractController
{
    #[Route('/{_locale}/admin/module/shopify/index/', name: 'admin_module_shopify_index')]
    public function shopifyIndex(): Response
    {
        return $this->render('platform/backend/v1/content.html.twig', [
            'title' => 'Shopify module',
            'content' => 'coming soon'
        ]);
    }
}
