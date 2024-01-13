<?php

namespace App\Service;

use App\Entity\Hub\Hub;
use App\Entity\Hub\Log;
use App\Entity\Hub\Mqtt;
use App\Entity\User;
use App\Exception\Service\HubException;
use App\Repository\Hub\HubRepository;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HubService {

    private ValidatorInterface $validator;
    private EntityManagerInterface $entityManager;
    private HubRepository $hubRepository;

    public function __construct(ValidatorInterface $validator,EntityManagerInterface $entityManager, HubRepository $hubRepository) {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->hubRepository = $hubRepository;
    }

    /**
     * @throws RandomException
     * @throws HubException
     */
    public function create(array $data, User $user): Hub {

        $hub = new Hub();
        $hub->setName($data['name']);
        $hub->setPairCode($this->generatePairCode());
        $hub->setUser($user);
        $hub->setAccessToken($this->generateRandomKey());

        $mqtt = new Mqtt();
        $mqtt->setUsername($this->generateRandomKey());
        $mqtt->setPassword($this->generateRandomKey());
        $mqtt->setAction('all');
        $mqtt->setPermission('allow');

        $log = new Log();
        $log->setType('info');
        $log->setMessage('Hub created');

        $hub->setMqtt($mqtt);
        $hub->AddLog($log);

        $errors = $this->validator->validate($hub);
        if (count($errors) > 0) {
            throw new HubException($errors[0]->getMessage());
        }

        $this->entityManager->persist($hub);
        $this->entityManager->flush();

        $mqtt->setTopic('hub/' . $hub->getId() . '/#');
        $this->entityManager->persist($mqtt);
        $this->entityManager->flush();

        return $hub;
    }

    public function getUserHubs(User $user): array {
        $hubs = $this->hubRepository->findActiveByUser($user);

        if (!$hubs) {
            return [];
        }

        $data = [];
        foreach ($hubs as $hub) {
            $data[] = [
                'id' => $hub->getId(),
                'name' => $hub->getName(),
                'pairCode' => $hub->getPairCode(),
                'modulesCount' => count($hub->getModules()),
                'pingAt' => $hub->getPingAt(),
            ];
        }

        return $data;
    }

    public function getUserHub(int $id, User $user): array {
        $hub = $this->hubRepository->findOneBy(['id' => $id, 'user' => $user]);

        if (!$hub) {
            throw new HubException("Hub not found");
        }

        return [
            'id' => $hub->getId(),
            'name' => $hub->getName(),
            'modulesCount' => count($hub->getModules()),
            'pingAt' => $hub->getPingAt(),
        ];
    }

    /**
     * @throws RandomException
     */
    private function generatePairCode(): int {
        return random_int(100000, 999999);
    }

    /**
     * @throws RandomException
     */
    private function generateRandomKey(): string {
        return hash('sha256', random_bytes(64));
    }

}