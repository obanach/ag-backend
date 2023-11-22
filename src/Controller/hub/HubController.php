<?php

namespace App\Controller\hub;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/hub', name: 'hub_')]
class HubController extends AbstractController {

    #[Route('/test', name: 'test_get', methods: ['GET'])]
    public function test_get(): JsonResponse {
        return new JsonResponse([
            'method' => 'GET',
            'status' => 'ok',
            'message' => 'Welcome to the Hub API',
        ]);
    }

    #[Route('/test', name: 'test_post', methods: ['POST'])]
    public function test_post(Request $request): JsonResponse {
        $data = $request->toArray();

        return new JsonResponse([
            'method' => 'POST',
            'status' => 'ok',
            'params' => $data,
        ]);
    }

    #[Route('/test', name: 'test_update', methods: ['UPDATE'])]
    public function test_update(Request $request): JsonResponse {
        $data = $request->toArray();

        return new JsonResponse([
            'method' => 'UPDATE',
            'status' => 'ok',
            'params' => $data,
        ]);
    }

    #[Route('/test', name: 'test_delete', methods: ['DELETE'])]
    public function test_delete(Request $request): JsonResponse {
        $data = $request->toArray();

        return new JsonResponse([
            'method' => 'DELETE',
            'status' => 'ok',
            'params' => $data,
        ]);
    }
}
