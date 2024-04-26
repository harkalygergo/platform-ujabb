<?php

namespace App\Controller\Platform;

use App\Entity\Platform\User;
use App\Repository\Platform\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/', name: 'admin_default_empty_login_screen')]
    public function adminDefaultEmptyLoginScreen(TranslatorInterface $translator): Response
    {
        $data = [
            'title' => '403 Forbidden',
            'content' => '<hr>RESTRICTED AREA<hr><p><b>IP:</b> '.$_SERVER['REMOTE_ADDR'].'<br><b>Agent:</b> '.$_SERVER['HTTP_USER_AGENT'].'</p>',
        ];

        return $this->render('platform/backend/v1/index_empty.html.twig', $data);
    }

    /*
    #[Route('/', name: 'admin_login_language_redirect')]
    public function loginLanguageRedirect(): RedirectResponse
    {
        $defaultLocale = $this->getParameter('kernel.default_locale');
        $supportedLanguages = explode('|', $this->getParameter('app.supported_locales'));
        $browserLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

        if (in_array($browserLanguage, $supportedLanguages)) {
            return $this->redirect('/' . $browserLanguage);
        }

        return $this->redirect('/' . $defaultLocale);
    }
    */

    private function getHashedPassword(UserPasswordHasherInterface $passwordHasher, User $user, string $plainTextPassword): string
    {
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plainTextPassword
        );

        /*
        $user->setPassword($hashedPassword);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        */

        return $hashedPassword;
    }

    #[Route('/{_locale}', name: 'admin_login')]
    public function login(UserPasswordHasherInterface $passwordHasher, Request $request, Security $security, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $form = $this->createFormBuilder()
            ->add('username', TextType::class, [
                'label' => $translator->trans('global.identifier'),
                'attr' => [
                    'class' => 'form-control form-control-lg bg-dark text-white'
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => $translator->trans('global.password'),
                'attr' => [
                    'class' => 'form-control form-control-lg bg-dark text-white'
                ]
            ])
            ->add('language', ChoiceType::class, [
                'label' => $translator->trans('global.language'),
                'choices'  => [
                    'english' => 'en',
                    'magyar' => 'hu',
                ],
                'attr' => [
                    'class' => 'form-control form-control-lg bg-dark text-white'
                ]
            ])
            ->add('keep_logged_in', CheckboxType::class, [
                'label' => $translator->trans('global.keepLoggedIn'),
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input bg-dark text-white'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => $translator->trans('global.login'),
                'attr' => [
                    'class' => 'btn btn-outline-light btn-lg px-5 my-3'
                ]
            ])
            ->getForm();

        $form->get('language')->setData($request->getLocale());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user = $this->isLoginCredentialsValid($passwordHasher, $userRepository, $security, $data['username'], $data['password']);

            if ($user) {
                if ($user->isStatus()) {
                    return $this->redirectToRoute('admin_index');
                }
            }
        }

        $data = [
            'title' => '<i class="bi bi-login"></i> '.$translator->trans('global.login').'<hr>',
            'content' => '',
            'form' => $form->createView(),
        ];

        return $this->render('platform/backend/login.html.twig', $data);
    }

    public function isLoginCredentialsValid(UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository, Security $security, string $username, string $password): bool|User
    {
        $findUser = $userRepository->findBy([
            'username'  => $username,
        ]);

        if ($findUser) {
            $user = $findUser[0];

            if ($user->getPassword() === hash('sha256', $password) ){
                $security->login($user, 'form_login', 'main');
                $this->setUserLastLogin($user);

                return $user;
            }
        }

        return false;
    }

    private function setUserLastLogin(User $user)
    {
        $user->setLastLogin(new \DateTime());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
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
