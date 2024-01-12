<?php

namespace App\Controller\device;

use App\Controller\DeviceController;
use App\Repository\Hub\HubRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/device/hub', name: 'device_hub_')]
class HubController extends DeviceController {

    private HubRepository $hubRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(HubRepository $hubRepository, EntityManagerInterface $entityManager) {
        $this->hubRepository = $hubRepository;
        $this->entityManager = $entityManager;

    }

    #[Route('/pair', name: 'pair', methods: ['POST'])]
    public function pair(Request $request): Response {

        $data = $request->toArray();

        if (!isset($data['pairCode']) ) {
            return $this->errorView("Missing parameters");
        }


        $hub = $this->hubRepository->findByPairCode($data['pairCode']);

        if (!$hub) {
            return $this->errorView("Hub not found");
        }

        $hub->setPairCode(null);
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
}
