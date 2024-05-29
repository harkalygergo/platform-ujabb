<?php

namespace App\Controller\Platform;

use App\Entity\Platform\Instance;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends _PlatformAbstractController
{
    public function __construct(private \Doctrine\Persistence\ManagerRegistry $doctrine) {}

    #[Route('/{_locale}/admin/account/edit', name: 'account_edit')]
    public function accountEdit(Request $request, TranslatorInterface $translator, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('fullName', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('position', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('profileImageUrl', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'choices'  => array_flip($user->getRoles()),
                'multiple' => true,
                'expanded' => true,
                'disabled' => true,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('defaultInstance', EntityType::class, [
                'class' => Instance::class,
                'choice_label' => 'title',
                'attr' => [
                    'class' => 'form-control'
                ],
                'disabled' => true
            ])
            ->add('language', ChoiceType::class, [
                'choices'  => [
                    'english' => 'en',
                    'magyar' => 'hu',
                ],
                'attr' => [
                    'class' => 'form-control form-control-lg'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => $translator->trans('global.save'),
                'attr' => [
                    'class' => 'my-2 btn btn-lg btn-success'
                ]
            ])
            ->getForm();

        $data = [
            'title' => '<i class="bi bi-person"></i> Profil szerkesztése',
            'content' => '',
            'sidebar' => 'platform/backend/v1/sidebar_profile.html.twig',
            'form' => $form
        ];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();


            // configure different hashers via the factory
            $factory = new PasswordHasherFactory([
                'common' => ['algorithm' => 'bcrypt'],
                'sodium' => ['algorithm' => 'sodium'],
            ]);

            // retrieve the hasher using bcrypt
            $hasher = $factory->getPasswordHasher('common');
            $hash = $hasher->hash('plain');

            /*
            // verify that a given string matches the hash calculated above
            $hasher->verify($hash, 'invalid'); // false
            $hasher->verify($hash, 'plain'); // true

            $passwordHasherFactory = new PasswordHasherFactory([
                // auto hasher with default options for the User class (and children)
                User::class => ['algorithm' => 'auto'],

                // auto hasher with custom options for all PasswordAuthenticatedUserInterface instances
                PasswordAuthenticatedUserInterface::class => [
                    'algorithm' => 'auto',
                    'cost' => 15,
                ],
            ]);


            $hashedPassword = $passwordHasherFactory->getPasswordHasher($user)->hash('alma');
            */
            $user->setPassword($hash);
            /*
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);
            */

            $em = $this->doctrine->getManager();
            $em->persist($user);
            $em->flush();

            $data['notification'] = $user->getUsername(). ' felhasználó sikeresen létrehozva.';
        }

        return $this->render('platform/backend/v1/form.html.twig', $data);
    }


    // create a route to change password, this is a POST request, ask current password once and new password twice, than update the password if current password is correct and two new passwords are the same
    #[Route('/{_locale}/admin/account/change-password', name: 'account_change_password')]
    public function changePassword(Request $request, TranslatorInterface $translator, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        // this form causes problem, regenerate it, problem is: Can't get a way to read the property "newPassword" in class "App\Entity\Platform\User".
        $form = $this->createFormBuilder($user)
            ->add('password', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => $translator->trans('global.save'),
                'attr' => [
                    'class' => 'btn btn-success w-100 py-2 mt-4'
                ]
            ])
        ->getForm();

        $data = [
            'title' => '<i class="bi bi-person"></i> Jelszó módosítása',
            'content' => '',
            'sidebar' => 'platform/backend/v1/sidebar_profile.html.twig',
            'form' => $form
        ];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ( $user->getPassword() === hash('sha256', $_POST['form']['password1']) ) {
                if ($_POST['form']['password1'] === $_POST['form']['password2']) {
                    $hashedPassword = hash('sha256', $_POST['form']['password1']);
                    $user->setPassword($hashedPassword);

                    $em = $this->doctrine->getManager();
                    $em->persist($user);
                    $em->flush();
                }

                $data['notification'] = $user->getUsername(). ' felhasználó sikeresen létrehozva.';
            }
        }

        return $this->render('platform/backend/v1/change-password.html.twig', $data);
    }

    // create function to add favourites, route is /favourites/add/{{ app.request.get('_route') }}
    #[Route('/favourites/add/{route}', name: 'favourites_add')]
    public function addFavourite(Request $request, TranslatorInterface $translator, string $route): Response
    {
        $route = $request->get('route');
        $user = $this->getUser();
        $favourites = $user->getFavourites();
        $favourites[] = $route;
        $user->setFavourites($favourites);

        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute($route);
    }

    // create a function to remove favourites, route is /favourites/remove/{{ app.request.get('_route') }}
    #[Route('/favourites/remove/{route}', name: 'favourites_remove')]
    public function removeFavourite(Request $request, TranslatorInterface $translator, string $route): Response
    {
        $route = $request->get('route');
        $user = $this->getUser();
        $favourites = $user->getFavourites();
        $favourites = array_diff($favourites, [$route]);
        $user->setFavourites($favourites);

        $em = $this->doctrine->getManager();
        $em->persist($user);
        $em->flush();

        // redirect user the page where the favourite was removed from
        return $this->redirectToRoute($route);
    }

}
