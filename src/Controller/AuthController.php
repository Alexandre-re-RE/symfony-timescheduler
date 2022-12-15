<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ) {

        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        $form = $this->createForm(RegisterType::class);

        if ($request->isMethod('POST')) {

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                /** @var \App\Repository\RoleRepository $roleRepo */
                $roleRepo = $em->getRepository(Role::class);

                $user = (new User())
                    ->setFirstname($data['firstname'])
                    ->setLastname($data['lastname'])
                    ->setUsername($data['username'])
                    ->setEmail($data['email'])
                    ->setRole($roleRepo->findOneBy(['code' => 'USER']));

                $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);

                $user->setPassword($hashedPassword);

                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Ton compte à bien été créé.');

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/logout', 'app_logout', methods: ['GET'])]
    public function logout()
    {
    }
}
