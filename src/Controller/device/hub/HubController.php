<?php

namespace App\Controller\device\hub;

use App\Controller\device\DeviceController;
use App\Exception\Service\HubException;
use App\Repository\Hub\HubRepository;
use App\Service\HubService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/device/hub', name: 'device_hub_')]
class HubController extends DeviceController {

    private HubRepository $hubRepository;
    private EntityManagerInterface $entityManager;
    private HubService $hubService;

    public function __construct(HubRepository $hubRepository, EntityManagerInterface $entityManager, HubService $hubService) {
        $this->hubRepository = $hubRepository;
        $this->entityManager = $entityManager;
        $this->hubService = $hubService;

    }

    #[Route('/{hubId}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $hubId, Request $request): Response {

        $accessToken = $request->headers->get('X-Device-Token');
        try {
            $this->hubService->checkHubAccess($hubId, $accessToken);
            $this->hubService->delete($hubId);
        } catch (HubException $e) {
            return $this->errorView($e->getMessage());
        }

        return $this->blankView();
    }

    #[Route('/pair', name: 'pair', methods: ['POST'])]
    public function pair(Request $request): Response {

        $data = $request->toArray();

        if (!isset($data['pairCode']) ) {
            return $this->errorView("Missing pair code parameter");
        }

        $hub = $this->hubRepository->findByPairCode($data['pairCode']);

        if (!$hub) {
            return $this->errorView("Hub not found");
        }

        $hub->setPairCode(null);
        $hub->setPingAt(new \DateTimeImmutable());
        $this->entityManager->persist($hub);
        $this->entityManager->flush();

        return $this->successView([
            'id' => $hub->getId(),
            'name' => $hub->getName(),
            'accessToken' => $hub->getAccessToken(),
            'mqtt' => [
                'username' => $hub->getMqtt()->getUsername(),
                'password' => $hub->getMqtt()->getPassword(),
            ]
        ]);
    }

    #[Route('/{hubId}/ping', name: 'ping', methods: ['POST'])]
    public function ping(int $hubId, Request $request): Response {

        $accessToken = $request->headers->get('X-Device-Token');

        try {
            $this->hubService->checkHubAccess($hubId, $accessToken);
            $this->hubService->pingHub($hubId);
        } catch (HubException $e) {
            return $this->errorView($e->getMessage());
        }

        return $this->blankView();
    }
}
