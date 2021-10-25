<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\Category;
use App\Helper\EntityFactoryException;
use App\Helper\EntityFactoryInterface;
use App\Repository\ClientRepository;
use App\Repository\CategoryRepository;

class CategoryService implements EntityFactoryInterface
{
    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(
        ClientRepository $clientRepository,
        CategoryRepository $categoryRepository)
    {
        $this->clientRepository = $clientRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function createEntity(string $json, int $userId, bool $insert): Category
    {
        $objetoJson = json_decode($json);
        $this->checkAllProperties($objetoJson);

        $client = $this->getClient($userId);

        $this->validateIfNameAlreadyExist($insert, $client->getId(), $objetoJson->name);

        $entity = new Category();
        $entity->setClient($client)
               ->setName($objetoJson->name)
               ->setShortName($objetoJson->shortName)
               ->setActive(true);

        return $entity;
    }

    private function getClient(int $userId): Client
    {
        $client = $this->clientRepository->findBy(array('User' => $userId));
        if (count($client) <= 0) {
            throw new EntityFactoryException("User doesn't have a client registered");
        }

        return $client[0];
    }

    private function checkAllProperties(object $objetoJson): void
    {
        if (!property_exists($objetoJson, 'name')) {
            throw new EntityFactoryException('Category needs a name');
        }

        if (!property_exists($objetoJson, 'shortName')) {
            throw new EntityFactoryException('Category needs a short name');
        }
    }

    private function validateIfNameAlreadyExist(bool $insert, int $clientId, string $name): void
    {
        if(!$insert)
            return;

        $category = $this->categoryRepository->findBy(array('Client' => $clientId, 'Name' => $name));
        if (count($category) > 0) {
            throw new EntityFactoryException("Category already exist with these name");
        }
    }
}
