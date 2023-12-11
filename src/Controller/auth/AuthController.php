<?php

namespace App\Controller\auth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/auth', name: 'auth_')]
class AuthController extends AbstractController {

    private ValidatorInterface $validator;
    private UserPasswordHasherInterface $userPasswordHasher;
    private EntityManagerInterface $entityManager;

    public function __construct (
        ValidatorInterface $validator,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ) {
        $this->validator = $validator;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request): Response {
        $data = $request->toArray();

        if (!isset($data['username']) || !isset($data['firstname']) || !isset($data['lastname']) || !isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse([
                'status' => false,
                'message' => 'Missing parameters',
            ]);
        }

        $user = new User();
        $user->setUsername($data['username']);
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setEmail($data['email']);
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $data['password']
            )
        );

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            return new JsonResponse([
                'status' => false,
                'message' => $errors[0]->getMessage(),
            ]);
        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'status' => true
        ]);

    }

    #[Route(path: '/confirm_email', name: 'app_confirm_email')]
    public function confirm_email(): JsonResponse {
        return new JsonResponse([
            'status' => false,
            'message' => 'Missing parameters',
        ]);
    }
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
