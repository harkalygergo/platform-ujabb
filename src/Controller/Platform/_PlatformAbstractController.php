<?php

namespace App\Controller\Platform;

use App\Entity\Platform\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(User::ROLE_USER)]
class _PlatformAbstractController extends AbstractController
{
    private ?array $modules = null;

    private array $sidebar = [];

    #[Required]
    public TranslatorInterface $translator;

    public function getSidebarElements($request)
    {
        $this->sidebar = [
            'cms' => [
                'title' => 'CMS | Tartalomkezelés',
                'elements' => [
                    $this->generateUrl('admin_website', ['_locale' => $request->getLocale()]) => '<i class="bi bi-globe2"></i>'. $this->translator->trans('global.website'),
                    '#webshops' => '<i class="bi bi-cart"></i> Webáruház',
                    '#webapplications' => '<i class="bi bi-window"></i> Webalkalmazás',
                    '#mobilapplications' => '<i class="bi bi-phone"></i> Mobilalkalmazás',
                ]
            ],
            'erp' => [
                'title' => 'ERP | Vállalkozásirányítás',
                'elements' => [
                    $this->generateUrl('app_task', ['_locale' => $request->getLocale()]) => '<i class="bi bi-list-task"></i> Feladatkezelő',
                    '#idopontfoglalo' => '<i class="bi bi-calendar-plus"></i> Időpontfoglaló <small>(szolgáltatói)</small>',
                    '#realestate' => '<i class="bi bi-house"></i> Ingatlanbázis <small>(ingatlanügynökségi)</small>',
                    '#accommodation' => '<i class="bi bi-building-add"></i> Szállásfoglaló <small>(szállásadói)</small>',
                ]
            ],
            'crm' => [
                'title' => 'CRM | Ügyfélkapcsolat',
                'elements' => [
                    '#clients' => '<i class="bi bi-people"></i> Ügyféllista',
                    '#newsletter' => '<i class="bi bi-mailbox-flag"></i> Hírlevél',
                    '#forms' => '<i class="bi bi-ui-checks"></i> Űrlapok',
                    '#automatizmusok' => '<i class="bi bi-person"></i> Automatizmusok',
                ]
            ],
            'ecom' => [
                'title' => 'ECOM | Értékesítés',
                'elements' => [
                    '#products' => '<i class="bi bi-shop"></i> Termékek',
                    '#abandontcarts' => '<i class="bi bi-bag-x"></i> Elhagyott kosarak',
                    '#orders' => '<i class="bi bi-basket"></i> Vásárlások',
                    '#paymentmethods' => '<i class="bi bi-credit-card"></i> Fizetési módok',
                    '#shippingmethods' => '<i class="bi bi-truck"></i> Szállítási módok',
                    '#analytics' => '<i class="bi bi-bar-chart"></i> Analitika',
                ]
            ],
            'assets' => [
                'title' => 'Eszközök',
                'elements' => [
                    '#drive' => '<i class="bi bi-hdd"></i> Tárhely',
                    '#clients' => '<i class="bi bi-link-45deg"></i> Link rövidítés',
                    '#exportimport' => '<i class="bi bi-arrow-down-up"></i> Export/import',
                ]
            ],
            'shopifyXprintbox' => [
                'title' => 'Shopify X Printbox',
                'elements' => [
                    $this->generateUrl('admin_module_shopify_index', ['_locale' => $request->getLocale()]) => '<i class="bi bi-basket"></i> Rendelések',
                    $this->generateUrl('shopify_ecard_list', ['_locale' => $request->getLocale()]) => '<i class="bi bi-card-list"></i> eCard',
                    '#shopify/plans' => '<i class="bi bi-easel"></i> Tervek',
                    '#shopify/customers' => '<i class="bi bi-person-circle"></i> Vevők',
                    '#shopify/google-merchant' => '<i class="bi bi-filetype-xml"></i> Google Merchant XML',
                ]
            ],
            'printbox' => [
                'title' => 'Printbox',
                'elements' => [
                    $this->generateUrl('module_printbox_saved_projects_list', ['_locale' => $request->getLocale()]) => '<i class="bi bi-basket"></i> Mentett tervek',
                ]
            ]
        ];

        return $this->sidebar;
    }

    public function getModules(): ?array
    {
        if ($this->modules === null) {
            $this->setModules($_ENV['PLATFORM_MODULES']);
        }

        return $this->modules;
    }

    public function setModules(string $modules): void
    {
        $this->modules = explode(',', $modules);
    }

    public function getSidebarMain(Request $request): string
    {
        $modules = $this->getModules();
        $sidebarModules = '';
        $sidebarModules .= $this->getSidebarDefaults($request);
        $sidebarModules .= $this->getSidebarFavourites($request);

        foreach ($modules as $module) {
            $sidebarModules .= $this->getSidebarModuleHTML($request, $module);
        }

        return $sidebarModules;
    }

    private function getSidebarDefaults(Request $request)
    {
        return $this->renderView(
            'platform/backend/v1/_sidebar_template.html.twig',
            [
                'title' => '<br><a class="text-decoration-none text-light" href="/'.$request->getLocale().'/admin"><i class="bi bi-speedometer2"></i> Vezérlőpult</a>',
                'elements' => []
            ]
        );
    }

    private function getSidebarFavourites(Request $request)
    {
        return $this->renderView(
            'platform/backend/v1/_sidebar_template.html.twig',
            [
                'title' => '<i class="bi bi-star"></i> Kedvencek',
                'elements' => [
                    $this->generateUrl('admin_show_intranet', ['_locale' => $request->getLocale()]) => '<i class="bi bi-info-square"></i> Intranet',
                ]
            ]
        );
    }

    private function getSidebarModuleHTML(Request $request, string $module): string
    {
        return $this->renderView(
            'platform/backend/v1/_sidebar_template.html.twig',
            $this->getSidebarElements($request)[$module]
        );
    }

    private function get(string $class)
    {
    }
}
