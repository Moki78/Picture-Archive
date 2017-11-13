<?php

namespace PictureArchiveFrontendBundle\Controller;

use PictureArchiveBundle\Entity\MediaFile;
use PictureArchiveFrontendBundle\Service\PaginationBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use PictureArchiveFrontendBundle\Form\MediaFileType;

/**
 *
 * @package PictureArchiveFrontendBundle\Controller
 * @author Moki <picture-archive@mokis-welt.de>
 */
class AdminController extends Controller
{
    /**
     * Lists all mediaFile entities.
     *
     * @Route("/", name="mediafile_index")
     * @Method("GET")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        /** @var PaginationBuilder $paginationBuilder */
        $paginationBuilder = $this->get('picture_archive_frontend.service.pagination_builder');

        return $this->render('@PictureArchiveFrontend/admin/index.html.twig', [
            'paginator' => $paginationBuilder->build($request)
        ]);
    }

    /**
     * Creates a new mediaFile entity.
     *
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function newAction(Request $request)
    {
        $mediaFile = new Mediafile();
        $form = $this->createForm(MediaFileType::class, $mediaFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($mediaFile);
            $em->flush();

            return $this->redirectToRoute('picture_archive_frontend_admin_show', ['id' => $mediaFile->getId()]);
        }

        return $this->render('@PictureArchiveFrontend/admin/new.html.twig', [
            'mediaFile' => $mediaFile,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a mediaFile entity.
     * @param MediaFile $mediaFile
     * @return Response
     */
    public function showAction(MediaFile $mediaFile): Response
    {
        $deleteForm = $this->createDeleteForm($mediaFile);

        return $this->render('@PictureArchiveFrontend/admin/show.html.twig', [
            'mediaFile' => $mediaFile,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Finds and downloads a mediaFile entity.
     *
     * @param MediaFile $mediaFile
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \InvalidArgumentException
     */
    public function downloadAction(MediaFile $mediaFile): Response
    {
        $configuration = $this->get('picture_archive.component.configuration');

        return new BinaryFileResponse(
            new \SplFileInfo($configuration->getArchiveBaseDirectory() . '/' . $mediaFile->getPath())
        );
    }

    /**
     * Displays a form to edit an existing mediaFile entity.
     * @param Request $request
     * @param MediaFile $mediaFile
     * @return RedirectResponse|Response
     * @throws \LogicException
     */
    public function editAction(Request $request, MediaFile $mediaFile)
    {
        $deleteForm = $this->createDeleteForm($mediaFile);
        $editForm = $this->createForm(MediaFileType::class, $mediaFile);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('picture_archive_frontend_admin_edit', ['id' => $mediaFile->getId()]);
        }

        return $this->render('@PictureArchiveFrontend/admin/edit.html.twig', [
            'mediaFile' => $mediaFile,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a mediaFile entity.
     * @param Request $request
     * @param MediaFile $mediaFile
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \LogicException
     */
    public function deleteAction(Request $request, MediaFile $mediaFile): RedirectResponse
    {
        $form = $this->createDeleteForm($mediaFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($mediaFile);
            $em->flush();
        }

        return $this->redirectToRoute('mediafile_index');
    }

    /**
     * Creates a form to delete a mediaFile entity.
     *
     * @param MediaFile $mediaFile The mediaFile entity
     *
     * @return Form The form
     */
    private function createDeleteForm(MediaFile $mediaFile): Form
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('picture_archive_frontend_admin_delete', ['id' => $mediaFile->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
