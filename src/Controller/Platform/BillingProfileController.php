<?php

namespace App\Controller\Platform;

use App\Entity\Platform\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(User::ROLE_USER)]
class BillingProfileController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine)
    {
    }

    #[Route('/{_locale}/admin/billing/', name: 'admin_billing_profile')]
    public function index(UserInterface $user): Response
    {
        $dataList = $user->getBillingProfiles();

        $data = [
            'title' => '<i class="bi bi-receipt"></i> Számlázási profilok',
            'dataList' => $dataList,
            'sidebar' => 'platform/backend/v1/sidebar_profile.html.twig',
        ];

        return $this->render('platform/backend/v1/list.html.twig', $data);
    }
}
