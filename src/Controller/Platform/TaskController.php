<?php

namespace App\Controller\Platform;

use App\Entity\Platform\Task;
use App\Repository\Platform\TaskRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine)
    {
    }

    #[Route('/{_locale}/admin/task', name: 'app_task')]
    public function index(TaskRepository $repository): Response
    {
        $tasks = $repository->findAll();
        $newUrl = $this->generateUrl('admin_task_new');

        $i = 0;
        $datalist = '<div class="row"><div class="col-sm-6"><h2>Feladatok listázása</h2></div><div class="col-sm-6 text-end"><a href="'.$newUrl.'" class="btn btn-success default-btn"> + Add New </a></div></div>';
        $datalist .= '<table class="table table-striped"><thead><tr><th>#</th><th>Cím</th><th>Tartalom</th><th class="text-end">Eszközök</th></tr></thead><tbody>';
        foreach ($tasks as $task) {
            $editUrl = $this->generateUrl('admin_task_edit', ['id'=>$task->getId()]);
            $datalist .= '<tr><td>' . ++$i . '.</td><td>' . $task->getTitle() . '</td><td>' . $task->getDescription() . '</td><td class="text-end"><a href="'.$editUrl.'">szerkesztés</a> duplikálás</td></tr>';
        }
        $datalist .= '</tbody></table>';

        $data = [
            'title' => '<i class="bi bi-list-task"></i> Feladatkezelő<hr>',
            'content' => $datalist
        ];

        return $this->render('platform/backend/v1/content.html.twig', $data);
    }

    #[Route('/{_locale}/admin/task/new', name: 'admin_task_new')]
    public function new(Request $request)
    {
        $entity = new Task();

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
            'title' => '<i class="bi bi-list-task"></i> Feladatkezelő<hr>',
            'form' => $form
        ];

        return $this->render('platform/backend/v1/form.html.twig', $data);
    }

    #[Route('/{_locale}/admin/task/{id<\d+>}/edit', name: 'admin_task_edit')]
    public function edit(Request $request, Task $task)
    {
        $form = $this->createFormBuilder($task)
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
            'title' => '<i class="bi bi-list-task"></i> Feladatkezelő<hr>',
            'form' => $form
        ];

        return $this->render('platform/backend/v1/form.html.twig', $data);    }
}
