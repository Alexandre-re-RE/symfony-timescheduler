<?php

namespace App\Controller;

use App\Entity\Status;
use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
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

    #[Route('/start/{id}', name: 'app_task_start', methods: ['POST'])]
    public function start(Request $request, Task $task, ManagerRegistry $doctrine): Response
    {
        $statusRepository = $doctrine->getRepository(Status::class);
        /** @var Status $statusStart */
        $statusStart = $statusRepository->findOneBy([
            'code' => 'START'
        ]);

        /** @var User $user */
        $user = $this->getUser();

        $canMoveTaskStatus = $this->isTaskOfCurrentUserAndTaskNotFinish($task, $user);

        if ($canMoveTaskStatus) {
            $task->setStatus($statusStart);
            $doctrine->getManager()->flush();
        } else {
        }
    }

    #[Route('/pause/{id}', name: 'app_task_pause', methods: ['POST'])]
    public function pause(Request $request, Task $task, ManagerRegistry $doctrine): Response
    {
        $statusRepository = $doctrine->getRepository(Status::class);
        /** @var Status $statusStart */
        $statusStart = $statusRepository->findOneBy([
            'code' => 'PAUSE'
        ]);
        /** @var User $user */
        $user = $this->getUser();

        $canMoveTaskStatus = $this->isTaskOfCurrentUserAndTaskNotFinish($task, $user);

        if ($canMoveTaskStatus) {
            $task->setStatus($statusStart);
            $doctrine->getManager()->flush();
        } else {
        }
    }

    #[Route('/finish/{id}', name: 'app_task_finish', methods: ['POST'])]
    public function finish(Request $request, Task $task, ManagerRegistry $doctrine): Response
    {
        $statusRepository = $doctrine->getRepository(Status::class);
        /** @var Status $statusStart */
        $statusStart = $statusRepository->findOneBy([
            'code' => 'FINISH'
        ]);
        /** @var User $user */
        $user = $this->getUser();

        $canMoveTaskStatus = $this->isTaskOfCurrentUserAndTaskNotFinish($task, $user);

        if ($canMoveTaskStatus) {
            $task->setStatus($statusStart);
            $doctrine->getManager()->flush();
        } else {
        }
    }

    /**
     * @param Task $task
     * @param User $user
     * @return bool
     */
    private function isTaskOfCurrentUserAndTaskNotFinish(Task $task, User $user)
    {
        $taskMouvable = true;

        //check que la tache appartient bien a l'utilisateur actuel
        if ($task->getUser()->getId() !== $user->getId()) {
            $taskMouvable = false;
            dd('ce nest pas vôtre tâche');
        }

        //verification que la tache n'est pas terminer
        if ($task->getStatus()->getCode() === 'FINISH') {
            $taskMouvable = false;
            dd('on ne peut plus faire action sur la tache');
        }

        return $taskMouvable;
    }
}
