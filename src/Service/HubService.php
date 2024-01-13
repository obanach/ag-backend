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

    public function __construct(ValidatorInterface $validator, EntityManagerInterface $entityManager, HubRepository $hubRepository) {
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->hubRepository = $hubRepository;
    }

    /**
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

        $this->entityValidator($hub);
        $this->entityManager->persist($hub);
        $this->entityManager->flush();

        $mqtt->setTopic('hub/' . $hub->getId() . '/#');
        $this->entityManager->persist($mqtt);
        $this->entityManager->flush();

        return $hub;
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

    /**
     * @throws HubException
     */
    private function entityValidator($entity): void {
        $errors = $this->validator->validate($entity);
        if (count($errors) > 0) {
            throw new HubException($errors[0]->getMessage());
        }
    }

    public function getUserHubs(User $user): array {
        return $this->hubRepository->findActiveByUser($user);
    }

    /**
     * @throws HubException
     */
    public function getDetails(int $id, ?User $user = null): Hub {
        $hub = $this->hubRepository->findOneBy(['id' => $id]);

        if (!$hub) {
            throw new HubException("Hub not found");
        }

        if ($user && $hub->getUser() !== $user) {
            throw new HubException("Hub assigned to another user");
        }

        return $hub;
    }

    /**
     * @throws HubException
     */
    public function delete(int $id, ?User $user = null): void {
        $hub = $this->hubRepository->findOneBy(['id' => $id]);

        if (!$hub) {
            throw new HubException("Hub not found");
        }

        if ($user && $hub->getUser() !== $user) {
            throw new HubException("Hub assigned to another user");
        }

        $this->entityManager->remove($hub);
        $this->entityManager->flush();
    }

    /**
     * @throws HubException
     */
    public function update(int $id, array $data, ?User $user = null): Hub {
        $hub = $this->hubRepository->findOneBy(['id' => $id]);

        if (!$hub) {
            throw new HubException("Hub not found");
        }

        if ($user && $hub->getUser() !== $user) {
            throw new HubException("Hub assigned to another user");
        }

        if (isset($data['name'])) {
            $hub->setName($data['name']);
        }

        $this->entityValidator($hub);
        $this->entityManager->persist($hub);
        $this->entityManager->flush();

        return $hub;
    }

    /**
     * @throws HubException
     */
    public function getModules(int $hubId, ?User $user = null): array {

        $hub = $this->hubRepository->findOneBy(['id' => $hubId]);

        if (!$hub) {
            throw new HubException("Hub not found");
        }

        if ($user && $hub->getUser() !== $user) {
            throw new HubException("Hub assigned to another user");
        }

        $data = [];
        foreach ($hub->getModules() as $module) {
            $data[] = [
                'id' => $module->getId(),
                'name' => $module->getName(),
                'type' => $module->getType(),
                'status' => $module->getStatus(),
                'pingAt' => $module->getPingAt(),
            ];
        }

        return $hub->getModules();

    }
}