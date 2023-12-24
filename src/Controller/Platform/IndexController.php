<?php

namespace App\Controller\Platform;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('platform/backend/v1/index.html.twig', [
        ]);
    }

    #[Route('/account/edit', name: 'account_edit')]
    public function accountEdit(): Response
    {
        return $this->render('platform/backend/v1/content.html.twig', [
            'title' => '<i class="bi bi-person"></i> Profil szerkesztése',
            'content' => '',
        ]);
    }
}
