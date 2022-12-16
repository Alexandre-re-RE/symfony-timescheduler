<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route(path: '/', name: 'dashboard')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function dashboard(ProjectRepository $projectRepository)
    {
        $projects = $projectRepository->findAllWithTasks();

        return $this->render('pages/dashboard.html.twig', compact('projects'));
    }
}
