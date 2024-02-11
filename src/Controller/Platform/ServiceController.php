<?php

namespace App\Controller\Platform;

use App\Entity\Platform\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_USER)]
class ServiceController extends AbstractController
{
    #[Route('/{_locale}/admin/services/', name: 'admin_service_index')]
    public function viewServices(UserInterface $user)
    {
        $dataList = $user->getServices();

        $data = [
            'title' => '<i class="bi bi-plugin"></i> Szolgáltatások',
            'dataList' => $dataList,
            'sidebar' => 'platform/backend/v1/sidebar_profile.html.twig',
        ];

        return $this->render('platform/backend/v1/list.html.twig', $data);
    }
}
