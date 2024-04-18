<?php

namespace App\Controller\Platform\Module\Shopify;

use App\Entity\Platform\User;
use Shopify\Auth\FileSessionStorage;
use Shopify\Clients\Rest;
use Shopify\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_USER)]
class ShopifyController extends AbstractController
{
    private ?array $config = null;

    public function __construct()
    {
        // do nothing
    }

    #[Route('/{_locale}/admin/module/shopify/index/', name: 'admin_module_shopify_index')]
    public function shopifyIndex(): Response
    {
        return $this->render('platform/backend/v1/content.html.twig', [
            'title' => 'Shopify module',
            'content' => 'coming soon'
        ]);
    }

    public function getConfig(): array
    {
        if (!is_array($this->config) || is_null($this->config)) {
            $this->config['hu'] = [
                'SHOPIFY_API_KEY' => $_ENV['MODULE_SHOPIFY_API_KEY'],
                'SHOPIFY_API_SECRET_KEY' => $_ENV['MODULE_SHOPIFY_API_SECRET_KEY'],
                'SHOPIFY_API_ACCESS_TOKEN' => $_ENV['MODULE_SHOPIFY_API_ACCESS_TOKEN'],
                'SHOPIFY_API_SCOPES' => $_ENV['MODULE_SHOPIFY_API_SCOPES'],
                'SHOPIFY_API_VERSION' => $_ENV['MODULE_SHOPIFY_API_VERSION'],
                'SHOPIFY_DOMAIN' => $_ENV['MODULE_SHOPIFY_DOMAIN'],
            ];
        }

        return $this->config;
    }

    public function getClient()
    {
        $this->initializeContext();

        return new Rest($this->getConfig()['SHOPIFY_DOMAIN'], $this->getConfig()['SHOPIFY_API_ACCESS_TOKEN']);
    }

    private function initializeContext(): void
    {
        Context::initialize(
            apiKey: $this->getConfig()['SHOPIFY_API_KEY'],
            apiSecretKey: $this->getConfig()['SHOPIFY_API_ACCESS_TOKEN'],
            scopes: $this->getConfig()['SHOPIFY_API_SCOPES'],
            hostName: $this->getConfig()['SHOPIFY_DOMAIN'],
            sessionStorage: new FileSessionStorage('/tmp/php_sessions'),
            apiVersion: $this->getConfig()['SHOPIFY_API_VERSION'],
            isEmbeddedApp: true,
            isPrivateApp: false,
        );
    }
}
