<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/task')]
class TaskController extends AbstractController
{
    #[Route('/', name: 'app_task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): Response
    {
        // "Un utilisateur peut seulement voir les tâches dont il est l'auteur."
        // "Les administrateurs peuvent effectuer toutes les actions"
        if ($this->isGranted('ROLE_ADMIN')) {
            $tasks = $taskRepository->findAll();
        } else {
            $tasks = $taskRepository->findBy(['author' => $this->getUser()]);
        }

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/create', name: 'app_task_create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setAuthor($this->getUser());
            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'Tâche créée avec succès !');

            return $this->redirectToRoute('app_task_index');
        }

        return $this->render('task/create.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_view', methods: ['GET'])]
    public function view(Task $task): Response
    {
        if (!$this->isGranted('VIEW', $task)) {
            $this->addFlash('error', 'Vous n\'avez pas accès à cette tâche.');
            return $this->redirectToRoute('app_task_index');
        }

        return $this->render('task/view.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('EDIT', $task)) {
            $this->addFlash('error', 'Vous n\'avez pas le droit de modifier cette tâche.');
            return $this->redirectToRoute('app_task_index');
        }

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Tâche modifiée avec succès !');

            return $this->redirectToRoute('app_task_index');
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('DELETE', $task)) {
            $this->addFlash('error', 'Vous n\'avez pas le droit de supprimer cette tâche.');
            return $this->redirectToRoute('app_task_index');
        }

        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
            $this->addFlash('success', 'Tâche supprimée avec succès !');
        }

        return $this->redirectToRoute('app_task_index');
    }
}
