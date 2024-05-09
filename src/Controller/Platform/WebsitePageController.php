<?php

namespace App\Controller\Platform;

use App\Repository\Platform\WebsitePageRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class WebsitePageController extends _PlatformAbstractController
{
    private string $title = '';

    public function __construct(private ManagerRegistry $doctrine, TranslatorInterface $translator)
    {
        $this->title = '<i class="bi bi-page"></i> '. $translator->trans('global.website');
    }

    #[Route('/{_locale}/admin/website/x/pages/', name: 'admin_website_page_list')]
    public function list(WebsitePageRepository $repository): Response
    {
        $dataList = $repository->findAll();

        $data = [
            'title' => $this->title,
            'dataList' => $dataList
        ];

        return $this->render('platform/backend/v1/list.html.twig', $data);
    }
}

