<?php

namespace PictureArchiveFrontendBundle\Controller;

use GuzzleHttp\Client;
use PictureArchiveBundle\Entity\MediaFile;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 *
 * @package PictureArchiveFrontendBundle\Controller
 * @author Moki <picture-archive@mokis-welt.de>
 */
class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {


        return $this->render('@PictureArchiveFrontend/default/index.html.twig');
    }

    public function ajaxRandomImageAction()
    {
        $cacheManager = $this->get('liip_imagine.cache.manager');
        $entityManager = $this->get('doctrine.orm.entity_manager');

        $repository = $entityManager->getRepository(MediaFile::class);

        $queryBuilder = $repository->createQueryBuilder('m')
            ->select('count(m)')
            ->where('m.type = \'image\'')
            ->andWhere('m.mediaDate > \'2009-00-00\'');

        $count = $queryBuilder->getQuery()->getSingleScalarResult();

        $index = random_int(0, $count -1);
        $queryBuilder
            ->select('m')
            ->setFirstResult($index)
            ->setMaxResults(1);

        /** @var MediaFile $mediaFile */
        $mediaFile = $queryBuilder->getQuery()->getSingleResult();

        return new JsonResponse([
            'link' => $cacheManager->getBrowserPath($mediaFile->getPath(), 'full'),
            'name' => $mediaFile->getName(),
            'date' => $mediaFile->getMediaDate(),
            'mime' => $mediaFile->getMimeType()
        ]);
    }

    public function randomImageAction(Request $request)
    {
        $cacheManager = $this->get('liip_imagine.cache.manager');
        $entityManager = $this->get('doctrine.orm.entity_manager');

        $repository = $entityManager->getRepository(MediaFile::class);

        $queryBuilder = $repository->createQueryBuilder('m')
            ->select('count(m)')
            ->where('m.type = \'image\'')
            ->andWhere('m.mediaDate > \'2009-00-00\'');

        $count = $queryBuilder->getQuery()->getSingleScalarResult();

        $index = random_int(0, $count -1);
        $queryBuilder
            ->select('m')
            ->setFirstResult($index)
            ->setMaxResults(1);

        /** @var MediaFile $mediaFile */
        $mediaFile = $queryBuilder->getQuery()->getSingleResult();

        $httpClient = new Client();
        $httpResponse = $httpClient->request('GET', $cacheManager->getBrowserPath($mediaFile->getPath(), 'full'));

        $response = new Response();
        $response->headers->set(
            'Content-Disposition',
            $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE,
                $mediaFile->getName()
            )
        );
        $response->headers->set('Content-Type', $httpResponse->getHeader('content-type'));
        $response->setContent($httpResponse->getBody());

        return $response;
    }
}
