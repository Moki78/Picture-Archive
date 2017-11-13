<?php

namespace PictureArchiveFrontendBundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use PictureArchiveBundle\Entity\MediaFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 *
 * @package PictureArchiveFrontendBundle\Service
 * @author Moki <picture-archive@mokis-welt.de>
 */
class PaginationBuilder
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var int
     */
    private $limit = 10;

    /**
     * @var int
     */
    private $offset = 0;

    /**
     * PaginationBuilder constructor.
     * @param EntityManager $entityManager
     * @internal param RequestStack $requestStack
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $limit
     * @return PaginationBuilder
     */
    public function setLimit(int $limit): PaginationBuilder
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param int $offset
     * @return PaginationBuilder
     */
    public function setOffset(int $offset): PaginationBuilder
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @param Request $request
     * @return Paginator
     */
    public function build(Request $request): Paginator
    {
        $this->setOffset($request->get('offset', $this->offset));
        $this->setLimit($request->get('limit', $this->limit));

        $page = (int)$request->get('page');
        if (0 < $page) {
            $this->setOffset(($page - 1) * $this->limit);
        }

        $queryBuilder = $this->entityManager->getRepository(MediaFile::class)->createQueryBuilder('f');

        $queryBuilder->setMaxResults($this->limit);
        $queryBuilder->setFirstResult($this->offset);

        return new Paginator($queryBuilder->getQuery(), false);
    }
}
