<?php

namespace App\Controller;

use App\Entity\HypermidiaResponse;
use App\Helper\EntityFactoryInterface;
use App\Helper\RequestDataExtractor;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\Persistence\ObjectRepository as PersistenceObjectRepository;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class BaseController extends AbstractController
{
    /**
     * @var ObjectRepository
     */
    protected $repository;
    /**
     * @var EntityFactoryInterface
     */
    protected $entityFactory;
    /**
     * @var RequestDataExtractor
     */
    protected $requestDataExtractor;
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        RequestDataExtractor $requestDataExtractor,
        PersistenceObjectRepository $repository,
        CacheItemPoolInterface $cache,
        LoggerInterface $logger
    ) {
        $this->entityFactory = $entityFactory;
        $this->requestDataExtractor = $requestDataExtractor;
        $this->repository = $repository;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function getTotalRows(Request $request): Response
    {
        try {
            $filterData = $this->requestDataExtractor->getFilterData($request);
            $rows = $this->repository->count($filterData);
        } catch (\Throwable $erro) {
            return HypermidiaResponse::fromError($erro)->getResponse();
        }
        return new JsonResponse($rows, Response::HTTP_OK);
    }

    public function getAll(Request $request): Response
    {
        try {
            $filterData = $this->requestDataExtractor->getFilterData($request);
            $orderData = $this->requestDataExtractor->getOrderData($request);
            $paginationData = $this->requestDataExtractor->getPaginationData($request);
            $itemsPerPage = $_ENV['ITEMS_PER_PAGE'] ?? 10;

            $entityList = $this->repository->findBy(
                $filterData,
                $orderData,
                $itemsPerPage,
                $paginationData * $itemsPerPage
            );

            $hypermidiaResponse = new HypermidiaResponse($entityList, true, Response::HTTP_OK, $paginationData, $itemsPerPage);
        } catch (\Throwable $erro) {
            $hypermidiaResponse = HypermidiaResponse::fromError($erro);
        }

        return $hypermidiaResponse->getResponse();
    }

    public function GetOne(int $id)
    {
        $entity = $this->cache->hasItem($this->cachePrefix() . $id)
            ? $this->cache->getItem($this->cachePrefix() . $id)->get()
            : $this->repository->find($id);
        $hypermidiaResponse = new HypermidiaResponse($entity, true, Response::HTTP_OK, null);

        return $hypermidiaResponse->getResponse();
    }

    public function new(Request $request): Response
    {
        $entity = $this->entityFactory->createEntity($request->getContent(), $this->getUser()->getId(), true);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($entity);
        $entityManager->flush();

        $cacheItem = $this->cache->getItem(
            $this->cachePrefix() . $entity->getId()
        );
        $cacheItem->set($entity);
        $this->cache->save($cacheItem);

        $this->logger
            ->notice(
                'New entity from {entity} added with id: {id}.',
                [
                    'entity' => get_class($entity),
                    'id' => $entity->getId(),
                ]
            );

        return $this->json($entity, Response::HTTP_CREATED);
    }

    public function update(int $id, Request $request): Response
    {
        $entity = $this->entityFactory->createEntity($request->getContent(), $this->getUser()->getId(), false);
        $existingEntity = $this->updateExistingEntity($id, $entity);

        $this->getDoctrine()->getManager()->flush();

        $cacheItem = $this->cache->getItem($this->cachePrefix() . $id);
        $cacheItem->set($existingEntity);
        $this->cache->save($cacheItem);

        return $this->json($existingEntity);
    }

    public function delete(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $entity = $this->repository->find($id);
        $entityManager->remove($entity);
        $entityManager->flush();

        $this->cache->deleteItem($this->cachePrefix() . $id);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    abstract public function updateExistingEntity(int $id, $entity);
    abstract public function cachePrefix(): string;
}
