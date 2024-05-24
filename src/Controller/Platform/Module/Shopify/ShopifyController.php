<?php

namespace App\Controller\Platform\Module\Shopify;

use App\Controller\Platform\_PlatformAbstractController;
use App\Entity\Platform\User;
use Shopify\Auth\FileSessionStorage;
use Shopify\Clients\HttpResponse;
use Shopify\Clients\Rest;
use Shopify\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_USER)]
class ShopifyController extends _PlatformAbstractController
{
    private ?array $config = null;

    public function __construct()
    {
        // do nothing
    }

    #[Route('/{_locale}/admin/module/shopify/order/list', name: 'admin_module_shopify_order_list')]
    public function shopifyOrderList(Request $request): Response
    {
        $orders = $this->getOrders();

        if ($orders) {
            $attributes = [
                'id'    => 'ID',
                'name'  => 'Megnevezés',
                'email' => 'Email',
                'total_price' => 'Total Price',
                'created_at' => 'Created At',
            ];

            $data = [
                'title'     => '<i class="bi bi-basket"></i> Shopify rendelések',
                'attributes'=> $attributes,
                'dataList'  => $orders,
                'new'       => false,
                'sidebar' => $this->getSidebarMain($request),
            ];

            return $this->render('platform/backend/v1/list.html.twig', $data);
        }

        return new Response('No orders found');
    }

    public function getConfig(): array
    {
        if (!is_array($this->config) || is_null($this->config)) {
            $this->config = [
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

    public function getOrders(int $limit = 50): array
    {
        $response = $this->getResponse('orders', $limit);

        return $response->getDecodedBody()['orders'];
    }

    public function getResponse(string $path, int $limit = 50, ?string $page_info = null): HttpResponse
    {
        $query = [];
        $query['limit'] = $limit;

        if (!is_null($page_info)) {
            $query['page_info'] = $page_info;
        }

        $response = $this->getClient()->get(path: $path, query: $query);

        return $response;
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
