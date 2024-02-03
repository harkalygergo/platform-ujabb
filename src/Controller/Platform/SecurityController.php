<?php

namespace App\Controller\Platform;

use App\Repository\Platform\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    #[Route('/{_locale?}', name: 'admin_login')]
    public function login(Request $request, Security $security, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $form = $this->createFormBuilder()
            ->add('username', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg bg-dark text-white'
                ]
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control form-control-lg bg-dark text-white'
                ]
            ])
            ->add('language', ChoiceType::class, [
                'choices'  => [
                    'english' => 'en',
                    'magyar' => 'hu',
                ],
                'attr' => [
                    'class' => 'form-control form-control-lg bg-dark text-white'
                ]
            ])
            ->add('keep_logged_in', CheckboxType::class, [
                'label'    => 'Keep me logged in',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input bg-dark text-white'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => $translator->trans('global.login'),
                'attr' => [
                    'class' => 'btn btn-outline-light btn-lg px-5 m-5'
                ]
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($this->isLoginCredentialsValid($userRepository, $security, $data['username'], $data['password']) ) {
                return $this->redirectToRoute('admin_index');
            }
        }

        $data = [
            'title' => '<i class="bi bi-login"></i> '.$translator->trans('global.login').'<hr>',
            'content' => '',
            'form' => $form->createView(),
        ];

        return $this->render('platform/backend/login.html.twig', $data);
    }

    public function isLoginCredentialsValid(UserRepository $userRepository, Security $security, string $username, string $password): bool
    {
        $findUser = $userRepository->findBy([
            'username'  => $username,
            'password'  => $password,
        ]);

        if ($findUser) {
            $security->login($findUser[0], 'form_login', 'main');
            return true;
        }

        return false;
    }

    #[Route('/{_locale?}/logout/', name: 'admin_logout')]
    public function logout(Security $security)
    {
        if ($security->getUser()) {
            $security->logout(false);
        }

        return $this->redirectToRoute('admin_login');
    }
}
