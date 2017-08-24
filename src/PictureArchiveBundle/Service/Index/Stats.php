<?php

namespace PictureArchiveBundle\Service\Index;

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
     */
    public function getStatistics()
    {
        return array(
            'files' => $this->em->getRepository('PictureArchiveBundle\:File')->countAll(),
            'types' => $this->em->getRepository('PictureArchiveBundle\:File')->countByType()
        );
    }
}
