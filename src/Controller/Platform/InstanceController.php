<?php

namespace App\Controller\Platform;

use App\Entity\Platform\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_USER)]
class InstanceController extends _PlatformAbstractController
{
    #[Route('/{_locale}/admin/intranet/', name: 'admin_show_intranet')]
    public function showIntranet(UserInterface $user, Request $request): Response
    {
        $currentInstance = $user->getDefaultInstance();

        return $this->render('platform/backend/v1/content.html.twig', [
            'title' => 'Intranet',
            'content' => $currentInstance->getIntranet() ?? '',
            'sidebar' => $this->getSidebarMain($request),
        ]);
    }

    // list instances for user
    #[Route('/{_locale}/admin/account/instances/', name: 'admin_list_user_instances')]
    public function listUserInstances(UserInterface $user, Request $request): Response
    {
        $dataList = $user->getInstances();

        $data = [
            'title' => 'Instances',
            'dataList' => $dataList,
            'sidebar' => $this->getSidebarMain($request),
        ];

        return $this->render('platform/backend/v1/list.html.twig', $data);
    }


}
