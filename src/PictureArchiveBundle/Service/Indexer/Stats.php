<?php

namespace PictureArchiveBundle\Service\Indexer;

use Doctrine\ORM\EntityManager;

class Stats
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * Processor constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function getStatistics(): array
    {
        return [
            'files' => $this->em->getRepository('PictureArchiveBundle:MediaFile')->countAll(),
            'types' => $this->em->getRepository('PictureArchiveBundle:MediaFile')->countByType()
        ];
    }
}
