<?php

namespace App\Controller\Platform;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginController extends AbstractController
{
    #[Route('/{_locale?}', name: 'admin_login')]
    public function login(TranslatorInterface $translator): Response
    {
        $data = [
            'title' => '<i class="bi bi-login"></i> '.$translator->trans('global.login').'<hr>',
            'content' => ''];

        return $this->render('platform/backend/login.html.twig', $data);
    }
}