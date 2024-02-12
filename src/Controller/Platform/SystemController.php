<?php

namespace App\Controller\Platform;

use App\Entity\Platform\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_USER)]
class SystemController extends AbstractController
{
    #[Route('/{_locale}/admin/system/', name: 'admin_system_index')]
    public function viewSystem(UserInterface $user)
    {
        $data = [
            'title' => '<i class="bi bi-plugin"></i> Rendszerbeállítások',
            'content' => '',
            'sidebar' => 'platform/backend/v1/sidebar_system.html.twig',
        ];

        return $this->render('platform/backend/v1/content.html.twig', $data);
    }
}
