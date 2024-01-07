<?php

namespace App\Controller\Platform;

use App\Repository\Platform\WebsiteRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class WebsiteController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine)
    {
    }

    #[Route('/{_locale}/admin/website/', name: 'admin_website')]
    public function index(WebsiteRepository $repository): Response
    {
        $dataList = $repository->findAll();

        $data = [
            'title' => '<i class="bi bi-list-task"></i> Honlap',
            'dataList' => $dataList
        ];

        return $this->render('platform/backend/v1/list.html.twig', $data);
    }
}
