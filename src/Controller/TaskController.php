<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\StatusRepository;
use App\Repository\TaskRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/task')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class TaskController extends AbstractController
{
    #[Route('/', name: 'app_task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): Response
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findAll(),
        ]);
    }

    #[Route('/calendar', name: 'app_task_calendar', methods: ['GET'])]
    public function calendar(TaskRepository $taskRepository): Response
    {
        return $this->json([
            'tasks' => $taskRepository->findAll(),
        ], Response::HTTP_OK, [], [
            'groups' => ['task']
        ]);
    }

    #[Route('/new', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TaskRepository $taskRepository): Response
    {
        $task = new Task();
        $task->setCreatedAt(new DateTimeImmutable());
        $task->setUpdatedAt(new DateTimeImmutable());
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->save($task, true);

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/new.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/change-status', name: 'app_task_change_status', methods: ['POST'])]
    public function start(
        TaskRepository $taskRepository,
        StatusRepository $statusRepository,
        EntityManagerInterface $em,
        Request $request
    ): Response {

        $params = json_decode($request->getContent(), true);

        $task = $taskRepository->find($params['id']);

        $status = $statusRepository->find($params['status_id']);

        if (!$status) {
            return $this->json([
                'message' => 'Status n\'a pas été trouvé'
            ], Response::HTTP_NOT_FOUND);
        }

        /** @var User $user */
        $user = $this->getUser();

        $checkStatus = $this->isAbleToChangeStatus($task, $user);

        if ($checkStatus['status'] === false) {
            return $this->json($checkStatus, Response::HTTP_UNAUTHORIZED);
        }

        $task->setStatus($status);
        $em->flush();

        return $this->json([
            'message' => sprintf('La tâche: %s a désormais le status: %s', $task->getTitle(), $status->getName())
        ]);
    }

    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $task->setUpdatedAt(new DateTimeImmutable());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->save($task, true);

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->request->get('_token'))) {
            $taskRepository->remove($task, true);
        }

        return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @param \App\Entity\Task $task
     * @param \App\Entity\User $user
     * @return array
     */
    private function isAbleToChangeStatus(Task $task, User $user)
    {
        if ($task->getUser()->getId() !== $user->getId()) {
            return [
                'status' => false,
                'message' => 'Cette tâche ne vous appartient pas'
            ];
        }

        if ($task->getStatus()->getCode() === 'FINISH') {
            return [
                'status' => false,
                'message' => 'Cette tâche est déjà fini'
            ];
        }

        return [
            'status' => true,
            'message' => null
        ];
    }
}
