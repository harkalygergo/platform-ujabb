<?php

namespace App\Controller\Platform;

use App\Entity\Platform\Website;
use App\Entity\Platform\WebsitePage;
use App\Repository\Platform\WebsitePageRepository;
use App\Repository\Platform\WebsiteRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class WebsitePageController extends _PlatformAbstractController
{
    private string $title = '';

    public function __construct(private ManagerRegistry $doctrine, TranslatorInterface $translator)
    {
        $this->title = '<i class="bi bi-page"></i> '. $translator->trans('global.page');
    }

    #[Route('/{_locale}/admin/website/{website}/pages/', name: 'admin_website_page_list')]
    public function list(WebsitePageRepository $repository, WebsiteRepository $websiteRepository, Website $website, Request $request): Response
    {
        $dataList = $repository->findByWebsiteId($website->getId());

        $data = [
            'title' => $this->title,
            'dataList' => $dataList,
            'sidebar' => $this->getSidebarMain($request),
        ];

        return $this->render('platform/backend/v1/list.html.twig', $data);
    }

    // create edit form for Website Page
    #[Route('/{_locale}/admin/website/{website}/pages/edit/{id}', name: 'admin_website_page_edit')]
    public function edit(Request $request, WebsitePageRepository $repository, Website $website, int $id): Response
    {
        $entity = $repository->find($id);

        // create form for Website Page
        $form = $this->createFormBuilder($entity)
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('content', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('metaTitle', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('metaDescription', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('metaKeywords', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('metaRobots', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('metaCanonical', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'global.save',
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $em = $this->doctrine->getManager();
            $em->persist($user);
            $em->flush();

            $data['notification'] = $user->getTitle() . ' sikeresen létrehozva.';
        }

        $data = [
            'title' => $this->title,
            'data' => $entity,
            'form'  => $form->createView(),
            'sidebar' => $this->getSidebarMain($request),
        ];

        return $this->render('platform/backend/v1/form.html.twig', $data);
    }

    public function addNex(Request $request, object $entity, FormInterface $form)
    {

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Website $new */
            $new = $form->getData();
            $new->setCreatedAt(new \DateTimeImmutable('now')); // setting current date and time

            $em = $this->doctrine->getManager();
            $em->persist($new);
            $em->flush();

            $data['notification'] = $new->getTitle() . ' sikeresen létrehozva.';
        }

        $data = [
            'title' => $this->title.'<hr>',
            'form' => $form,
            'sidebar' => $this->getSidebarMain($request),
        ];

        return $this->render('platform/backend/v1/form.html.twig', $data);
    }

    #[Route('/{_locale}/admin/website/{website}/pages/new/', name: 'admin_website_page_new')]
    public function new(Request $request, TranslatorInterface $translator, Website $website)
    {
        $entity = new WebsitePage();

        $form = $this->createFormBuilder($entity)
            // add website_id as hidden input
            ->add('website', TextType::class, [
                'data' => $website->getId(),
                'attr' => [
                    'class' => 'form-control',
                    'hidden' => true
                ]
            ])
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
            ->add('save', SubmitType::class, [
                'label' => $translator->trans('global.save'),
                'attr' => [
                    'class' => 'my-1 btn btn-lg btn-success'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Website $new */
            $new = $form->getData();
            $new->setCreatedAt(new \DateTimeImmutable('now')); // setting current date and time

            $em = $this->doctrine->getManager();
            $em->persist($new);
            $em->flush();

            $data['notification'] = $new->getTitle() . ' sikeresen létrehozva.';
        }

        $data = [
            'title' => $this->title.'<hr>',
            'form' => $form,
            'sidebar' => $this->getSidebarMain($request),
        ];

        return $this->render('platform/backend/v1/form.html.twig', $data);
    }
}

