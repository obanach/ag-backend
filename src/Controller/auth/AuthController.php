<?php

namespace App\Controller\auth;

use App\Controller\BaseController;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/auth', name: 'auth_')]
class AuthController extends BaseController {

    private ValidatorInterface $validator;
    private UserPasswordHasherInterface $userPasswordHasher;
    private EntityManagerInterface $entityManager;

    public function __construct (
        ValidatorInterface $validator,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
    ) {
        $this->validator = $validator;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
    }

    #[Route(path: '/register', name: 'register', methods: ['POST'])]
    public function register(Request $request): Response {
        $data = $request->toArray();

        if (!isset($data['username']) || !isset($data['firstname']) || !isset($data['lastname']) || !isset($data['email']) || !isset($data['password'])) {
            return $this->errorView("Missing parameters");
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
            return $this->errorView($errors[0]->getMessage());
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        //TODO: Send email confirmation

        return $this->successView([]);

    }

    #[Route(path: '/user', name: 'user', methods: ['GET'])]
    public function user(Request $request): Response {

        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->errorView("You are not logged in");
        }

        $user = $this->getUser();
        return $this->successView([
            'username' => $user->getUsername(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'created_at' => $user->getCreatedAt(),
            'updated_at' => $user->getUpdatedAt(),
        ]);
    }

    #[Route(path: '/confirm_email', name: 'confirm_email')]
    public function confirm_email(): JsonResponse {
        return new JsonResponse([
            'status' => false,
            'message' => 'Missing parameters',
        ]);
    }
}
