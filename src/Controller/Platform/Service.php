<?php

namespace App\Controller\Platform;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class Service extends AbstractController
{
    #[Route('/{_locale}/admin/services/', name: 'admin_service_index')]
    public function viewServices(UserInterface $user)
    {
        $dataList = $user->getServices();

        $data = [
            'title' => '<i class="bi bi-plugin"></i> Szolgáltatások',
            'dataList' => $dataList
        ];

        return $this->render('platform/backend/v1/list.html.twig', $data);
    }
}
