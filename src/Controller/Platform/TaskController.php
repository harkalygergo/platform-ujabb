<?php

namespace App\Controller\Platform;

use App\Entity\Task;
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
    public function __construct(private ManagerRegistry $doctrine) {}

    #[Route('/admin/task', name: 'app_task')]
    public function index(Request $request, TaskRepository $repository): Response
    {
        $tasks = $repository->findAll();

        // creates a task object and initializes some data for this example
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

            $data['notification'] = $user->getTitle(). ' sikeresen létrehozva.';
        }

        $i = 0;
        $a = '<div class="row"><div class="col-sm-6"><h2>Feladatok listázása</h2></div><div class="col-sm-6 text-end"><button class="btn btn-primary default-btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"> + Add New </button></div></div>';
        $a .= '<table class="table table-striped"><thead><tr><th>#</th><th>Cím</th><th>Tartalom</th><th class="text-end">Eszközök</th></tr></thead><tbody>';
        foreach ($tasks as $task) {
            $a .= '<tr><td>'.++$i.'.</td><td>'.$task->getTitle().'</td><td>'.$task->getDescription().'</td><td class="text-end">szerkesztés duplikálás</td></tr>';
        }
        $a .= '</tbody></table>';

        $data = [
            'title' => '<i class="bi bi-list-task"></i> Feladatkezelő<hr>',
            'content' => $a,
            'form' => $form
        ];

        return $this->render('platform/backend/v1/content.html.twig', $data);
    }
}
