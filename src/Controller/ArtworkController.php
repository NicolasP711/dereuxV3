<?php

namespace App\Controller;

use App\Entity\Artwork;
use App\Form\ArtworkType;
use App\Repository\ArtworkRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/artwork")
 */
class ArtworkController extends AbstractController
{

    /**
     * @Route("/", name="artwork_index", methods={"GET"})
     *
     */
    public function index(ArtworkRepository $artworkRepository, Request $request, PaginatorInterface $paginator): Response
    {

        // On récupère dans l'url la données GET page (si elle n'existe pas, la valeur retournée par défaut sera la page 1)
        $requestedPage = $request->query->getInt('page', 1);

        // Si le numéro de page demandé dans l'url est inférieur à 1, erreur 404
        if($requestedPage < 1){
            throw new NotFoundHttpException();
        }

        // Récupération du manager des entités
        $em = $this->getDoctrine()->getManager();

        // Création d'une requête qui servira au paginator pour récupérer les articles de la page courante
        $query = $em->createQuery('SELECT a FROM App\Entity\Artwork a');

        $pageArtworks = $paginator->paginate(
            $query,     // Requête de selection des articles en BDD
            $requestedPage,     // Numéro de la page dont on veux les articles
            12      // Nombre d'articles par page
        );
        return $this->render('artwork/index.html.twig', [
            'artworks' => $pageArtworks,
        ]);
    }
    /**
     * @Route("/admin", name="admin_artwork_index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function adminIndex(ArtworkRepository $artworkRepository, Request $request, PaginatorInterface $paginator): Response
    {

        // On récupère dans l'url la données GET page (si elle n'existe pas, la valeur retournée par défaut sera la page 1)
        $requestedPage = $request->query->getInt('page', 1);

        // Si le numéro de page demandé dans l'url est inférieur à 1, erreur 404
        if($requestedPage < 1){
            throw new NotFoundHttpException();
        }

        // Récupération du manager des entités
        $em = $this->getDoctrine()->getManager();

        // Création d'une requête qui servira au paginator pour récupérer les articles de la page courante
        $query = $em->createQuery('SELECT a FROM App\Entity\Artwork a');

        $pageArtworks = $paginator->paginate(
            $query,     // Requête de selection des articles en BDD
            $requestedPage,     // Numéro de la page dont on veux les articles
            10      // Nombre d'articles par page
        );
        return $this->render('artwork/adminIndex.html.twig', [
            'artworks' => $pageArtworks,
        ]);
    }
    

    /**
     * @Route("/new", name="artwork_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
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

            return $this->redirectToRoute('admin_artwork_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artwork/new.html.twig', [
            'artwork' => $artwork,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="artwork_show", methods={"GET"})
     */
    public function show(Artwork $artwork): Response
    {
        return $this->render('artwork/show.html.twig', [
            'artwork' => $artwork,
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="artwork_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
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

            return $this->redirectToRoute('admin_artwork_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artwork/edit.html.twig', [
            'artwork' => $artwork,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="artwork_delete", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Request $request, Artwork $artwork): Response
    {
        if ($this->isCsrfTokenValid('delete'.$artwork->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($artwork);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_artwork_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     *  Page affichant les résultats de recherches faites par le formulaire de recherche dans la navbar
     *
     * @Route("/recherche/admin", name="admin_artwork_search")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function artworkArticleSearch(Request $request, PaginatorInterface $paginator): Response
    {
        // Récupération de la variable $_GET['page]
        $requestedPage = $request->query->getInt('page', 1);

        if($requestedPage < 1){
            throw new NotFoundHttpException();
        }

        $search = $request->query->get('q');

        $em = $this->getDoctrine()->getManager();

        $query = $em
            ->createQuery('SELECT a FROM App\Entity\Artwork a WHERE a.id LIKE :search OR a.title LIKE :search OR a.description LIKE :search OR a.artist LIKE :search OR a.creationDate LIKE :search OR a.publicationDate LIKE :search ORDER BY a.publicationDate DESC ')
            ->setParameters(['search' => '%' . $search . '%'])
        ;

        $artworks = $paginator->paginate(
            $query,
            $requestedPage,
            10,
        );

        return $this->render('artwork/adminArtworkSearch.html.twig', [
            'artworks' => $artworks
        ]);
    }
}
