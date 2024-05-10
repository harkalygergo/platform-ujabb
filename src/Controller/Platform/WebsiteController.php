<?php

namespace App\Controller\Platform;

use App\Entity\Platform\User;
use App\Entity\Platform\Website;
use App\Repository\Platform\WebsiteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(User::ROLE_USER)]
class WebsiteController extends AbstractController
{
    private string $title = '';

    public function __construct(private ManagerRegistry $doctrine, TranslatorInterface $translator)
    {
        $this->title = '<i class="bi bi-globe"></i> '. $translator->trans('global.website');
    }

    #[Route('/{_locale}/admin/website/', name: 'admin_website')]
    public function index(WebsiteRepository $repository): Response
    {
        $dataList = $repository->findAll();

        $buttons = [
            '/pages/' => 'oldalak',
        ];

        $data = [
            'title' => $this->title,
            'dataList' => $dataList,
            'buttons' => $buttons
        ];

        return $this->render('platform/backend/v1/list.html.twig', $data);
    }

    #[Route('/{_locale}/admin/website/new/', name: 'admin_website_new')]
    public function new(Request $request, TranslatorInterface $translator)
    {
        $entity = new Website();

        $form = $this->createFormBuilder($entity)
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
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
            'form' => $form
        ];

        return $this->render('platform/backend/v1/form.html.twig', $data);
    }

    #[Route('/{_locale}/admin/website/edit/{id<\d+>}/', name: 'admin_website_edit')]
    public function edit(Request $request, Website $entity)
    {
        $form = $this->createFormBuilder($entity)
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('slogan', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('slogan', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('keywords', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('robots', ChoiceType::class, [
                'choices'  => [
                    'index, follow' => 'index, follow',
                    'index, nofollow' => 'index, nofollow',
                    'noindex, follow' => 'noindex, follow',
                    'noindex, nofollow' => 'noindex, nofollow',
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('language', ChoiceType::class, [
                'choices'  => [
                    'english' => 'en',
                    'magyar' => 'hu',
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('template', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('logo', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('favicon', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('primaryColor', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('secondaryColor', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('htmlHead', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('htmlBody', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('htmlFooter', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Frissítés',
                'attr' => [
                    'class' => 'my-1 btn btn-lg btn-success'
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
            'title' => $this->title.'<hr>',
            'form' => $form
        ];

        return $this->render('platform/backend/v1/form.html.twig', $data);    }
}
