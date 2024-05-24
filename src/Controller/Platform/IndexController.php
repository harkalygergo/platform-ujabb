<?php

namespace App\Controller\Platform;

use App\Entity\Platform\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_USER)]
class IndexController extends _PlatformAbstractController
{
    #[Route('/{_locale}/admin/', name: 'admin_index')]
    public function adminIndex(Request $request): Response
    {
        return $this->render('platform/backend/v1/index.html.twig', [
            'sidebar' => $this->getSidebarMain($request),
        ]);
    }
}
