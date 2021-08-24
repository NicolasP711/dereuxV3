<?php

namespace App\Controller;

use App\Entity\Artwork;
use App\Form\ArtworkType;
use App\Repository\ArtworkRepository;
use DateTime;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/artwork")
 */
class ArtworkController extends AbstractController
{
    /**
     * @Route("/", name="artwork_index", methods={"GET"})
     */
    public function index(ArtworkRepository $artworkRepository): Response
    {
        return $this->render('artwork/index.html.twig', [
            'artworks' => $artworkRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="artwork_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $artwork = new Artwork();
        $form = $this->createForm(ArtworkType::class, $artwork);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $picture = $form->get('picture')->getData();

            $newFileName = md5( random_bytes(100) . time() ) . '.' . $picture->guessExtension();

            $artwork->setPicture($newFileName);

            $artwork->setPublicationDate( new DateTime() );

            $artwork->setAuthor($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($artwork);
            $entityManager->flush();

            $picture->move(
                $this->getParameter('artwork.photo.directory'),
                $newFileName
            );

            return $this->redirectToRoute('artwork_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artwork/new.html.twig', [
            'artwork' => $artwork,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="artwork_show", methods={"GET"})
     */
    public function show(Artwork $artwork): Response
    {
        return $this->render('artwork/show.html.twig', [
            'artwork' => $artwork,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="artwork_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Artwork $artwork): Response
    {
        $oldFileName = $artwork->getPicture();
        
        $form = $this->createForm(ArtworkType::class, $artwork);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $picture = $form->get('picture')->getData();

            $newFileName = md5( random_bytes(100) . time() ) . '.' . $picture->guessExtension();

            $artwork->setPicture($newFileName);

            if($artwork->getPicture() != null){
                unlink($this->getParameter('artwork.photo.directory') . $oldFileName );
            }

            $this->getDoctrine()->getManager()->flush();

            $picture->move(
                $this->getParameter('artwork.photo.directory'),
                $newFileName
            );

            return $this->redirectToRoute('artwork_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artwork/edit.html.twig', [
            'artwork' => $artwork,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="artwork_delete", methods={"POST"})
     */
    public function delete(Request $request, Artwork $artwork): Response
    {
        if ($this->isCsrfTokenValid('delete'.$artwork->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($artwork);
            $entityManager->flush();
        }

        return $this->redirectToRoute('artwork_index', [], Response::HTTP_SEE_OTHER);
    }
}
