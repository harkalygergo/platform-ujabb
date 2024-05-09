<?php

namespace App\Controller\Platform;

use App\Repository\Platform\WebsitePageRepository;
use App\Repository\Platform\WebsiteRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class WebsitePageController extends _PlatformAbstractController
{
    private string $title = '';

    public function __construct(private ManagerRegistry $doctrine, TranslatorInterface $translator)
    {
        $this->title = '<i class="bi bi-page"></i> '. $translator->trans('global.website');
    }

    #[Route('/{_locale}/admin/website/x/pages/', name: 'admin_website_page_list')]
    public function list(WebsitePageRepository $repository, WebsiteRepository $websiteRepository): Response
    {
        $website = $websiteRepository->findByInstance($this->getUser()->getDefaultInstance()->getId());
        $dataList = $repository->findByWebsiteId($website['0']->getId());

        $data = [
            'title' => $this->title,
            'dataList' => $dataList
        ];

        return $this->render('platform/backend/v1/list.html.twig', $data);
    }

    // create edit form for Website Page
    #[Route('/{_locale}/admin/website/x/pages/edit/{id}', name: 'admin_website_page_edit')]
    public function edit(WebsitePageRepository $repository, int $id): Response
    {
        $data = $repository->find($id);

        // create form for Website Page
        $form = $this->createFormBuilder($data)
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('content', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('metaTitle', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('metaDescription', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('metaKeywords', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('metaRobots', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('metaCanonical', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'global.save',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->getForm();

        $data = [
            'title' => $this->title,
            'data' => $data,
            'form'  => $form->createView()
        ];

        return $this->render('platform/backend/v1/form.html.twig', $data);
    }
}

