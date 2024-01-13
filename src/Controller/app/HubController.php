<?php

namespace App\Controller\app;

use App\Controller\BaseController;
use App\Exception\Service\HubException;
use App\Service\HubService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/app/hub', name: 'app_hub_')]
class HubController extends BaseController {

    private HubService $hubService;

    public function __construct (HubService $hubService) {
        $this->hubService = $hubService;
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $request): Response {

        $data = $request->toArray();

        if (!isset($data['name']) ) {
            return $this->errorView("Missing parameters");
        }

        try {
            $hub = $this->hubService->create($data, $this->getUser());
        } catch (HubException $e) {
            return $this->errorView($e->getMessage());
        }


        return $this->successView([
            'id' => $hub->getId(),
            'name' => $hub->getName(),
            'pairCode' => $hub->getPairCode()
        ]);
    }

    #[Route('/', name: 'user_get_all', methods: ['GET'])]
    public function userGetAll(): Response {
            $hubs = $this->hubService->getUserHubs($this->getUser());
            return $this->successView($hubs);
    }

    #[Route('/{id}', name: 'user_get_one', methods: ['GET'])]
    public function userGetOne(int $id): Response {
        try {
            $hub = $this->hubService->getUserHub($id, $this->getUser());
        } catch (HubException $e) {
            return $this->errorView($e->getMessage());
        }

        if (!$hub) {
            return $this->errorView("Hub not found");
        }
        return $this->successView($hub);
    }

}