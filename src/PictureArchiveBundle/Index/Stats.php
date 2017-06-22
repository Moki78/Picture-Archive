<?php

namespace PictureArchiveBundle\Index;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use PictureArchiveBundle\Entity\File as FileEntity;
use PictureArchiveBundle\Entity\File;
use PictureArchiveBundle\Util\FileHashInterface;
use PictureArchiveBundle\Util\FileScanner;
use PictureArchiveBundle\Util\ImageExif;
use Symfony\Bridge\Monolog\Logger;

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
