<?php

namespace App\Controller\Platform;

use App\Entity\Platform\Instance;
use App\Entity\Platform\User;
use App\Repository\Platform\InstanceRepository;
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
    #[Route('/{_locale}/admin/instance/', name: 'admin_list_user_instances')]
    public function listUserInstances(UserInterface $user, Request $request): Response
    {
        $dataList = $user->getInstances();

        $buttons = [
            '/users/' => 'felhasználók',
        ];

        $data = [
            'title' => 'Instances',
            'buttons' => $buttons,
            'dataList' => $dataList,
            'sidebar' => $this->getSidebarMain($request),
        ];

        return $this->render('platform/backend/v1/list.html.twig', $data);
    }

    // list users for an instance
    #[Route('/{_locale}/admin/instance/{instance}/users/', name: 'admin_list_instance_users')]
    public function listInstanceUsers(UserInterface $user, Request $request, InstanceRepository $repository, Instance $instance): Response
    {
        // users and instances are many-to-many, find all users by instance
        $dataList = $instance->getUsers();

        $data = [
            'title' => 'Users',
            'dataList' => $dataList,
            'sidebar' => $this->getSidebarMain($request),
        ];

        return $this->render('platform/backend/v1/list.html.twig', $data);
    }
}
