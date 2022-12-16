<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Status;
use App\Entity\Task;
use App\Entity\User;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ProjectController extends AbstractController
{
    #[Route('/', name: 'app_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findAllWithStatusAndClient(),
        ]);
    }

    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProjectRepository $projectRepository, EntityManagerInterface $em): Response
    {
        /** @var \App\Repository\StatusRepository $statusRepository */
        $statusRepository = $em->getRepository(Status::class);
        $project = (new Project())
            ->setStatus($statusRepository->findOneBy(['code' => 'WAITING']));

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectRepository->save($project, true);

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_project_show', methods: ['GET'])]
    public function show($id, ProjectRepository $projectRepository, ManagerRegistry $doctrine): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $project = $projectRepository->findOneWithStatusAndClient($id);

        $taskRepository = $doctrine->getRepository(Task::class);

        $taskAssignedToCurrentUser = $taskRepository->findBy([
            'user' => $currentUser->getId(),
            'project' => $project->getId()
        ]);

        //        dd($taskAssignedToCurrentUser);

        return $this->render('project/show.html.twig', [
            'project' => $project,
            'tasks' => $taskAssignedToCurrentUser
        ]);
    }

    #[Route('/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectRepository->save($project, true);

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
            $projectRepository->remove($project, true);
        }

        return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/kanban/{id}', name: 'app_project_tasks_kanban')]
    public function kanban(Project $project, EntityManagerInterface $em)
    {
        /** @var \App\Repository\TaskRepository $taskRepo */
        $taskRepo = $em->getRepository(Task::class);

        $tasks = new ArrayCollection($taskRepo->findAllByProject($project));

        return $this->json(compact('tasks'), 200, [], [
            'groups' => ['task']
        ]);
    }
}
