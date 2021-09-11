<?php

namespace App\Controller;

use App\Entity\Artwork;
use App\Entity\ArtworkComment;
use App\Form\ArtworkCommentFormType;
use App\Form\ArtworkType;
use App\Recaptcha\RecaptchaValidator;
use App\Repository\ArtworkRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormError;

/**
 * @Route("/oeuvres")
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
        return $this->render('artwork/admin.html.twig', [
            'artworks' => $pageArtworks,
        ]);
    }
    

    /**
     * @Route("/admin/nouvelle-oeuvre", name="artwork_new")
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

            $this->addFlash('success', 'Oeuvre publiée avec succès.');


            return $this->redirectToRoute('artwork_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artwork/new.html.twig', [
            'artwork' => $artwork,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/nouvelle-oeuvre", name="admin_artwork_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function adminNew(Request $request): Response
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

            $this->addFlash('success', 'Oeuvre publiée avec succès.');


            return $this->redirectToRoute('admin_artwork_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artwork/new.html.twig', [
            'artwork' => $artwork,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="artwork_show")
     */
    public function show(Artwork $artwork, Request $request, RecaptchaValidator $recaptcha, PaginatorInterface $paginator): Response
    {

        $newComment = new ArtworkComment();

        $form = $this->createForm(ArtworkCommentFormType::class, $newComment);

        $form->handleRequest($request);

        // Pour savoir si le formulaire a été envoyé, on a accès à cette condition :
            if($form->isSubmitted()){

                 // Verif captcha
                $captchaResponse = $request->get('g-recaptcha-response', null);

                if($captchaResponse == null || !$recaptcha->verify($captchaResponse, $request->server->get('REMOTE_ADDR'))){

                $form->addError(new FormError('Veuillez remplir le captcha de sécurité'));

                }

                if($form->isValid()){

                    $newComment
                        ->setPublicationDate(new DateTime())
                        ->setAuthor($this->getUser())
                        ->setArtwork($artwork)
                    ;
    
                    // récupération du manager des entités et sauvegarde en BDD de $newArticle
                    $em = $this->getDoctrine()->getManager();
    
                    $em->persist($newComment);
    
                    $em->flush();
    
                    $this->addFlash('success', 'Commentaire publié avec succès.');
    
                    // On re-créé le formulaire pour pas que le texte saisi dans le dernier commentaire se remettent dans le nouveau
                    $newComment = new ArtworkComment();
                    $form = $this->createForm(ArtworkCommentFormType::class, $newComment);
                }

            }

            $requestedPage = $request->query->getInt('page', 1);

            if($requestedPage < 1){
                throw new NotFoundHttpException();
            }

            $em = $this->getDoctrine()->getManager();

            $query = $em->getRepository(ArtworkComment::class)->findByArtwork(array('artwork_id'=>$artwork));

            $pageComments = $paginator->paginate(
                $query,     // Requête de selection des articles en BDD
                $requestedPage,     // Numéro de la page dont on veux les articles
                10      // Nombre d'articles par page
            );


        return $this->render('artwork/show.html.twig', [
            'artwork' => $artwork,
            'form' => $form->createView(),
            'comments' => $pageComments,
        ]);
    }

    /**
     * @Route("/editer-commentaire/{slug}", name="artwork_comment_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_USER')")
     */
    public function editArtworkComment(Request $request, ArtworkComment $artworkComment): Response
    {
        $form = $this->createForm(ArtworkCommentFormType::class, $artworkComment);
        $form->handleRequest($request);
        $artwork = $artworkComment->getArtwork();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Commentaire édité avec succès.');


            return $this->redirectToRoute('artwork_show', ['slug' => $artwork->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artwork/editComment.html.twig', [
            'artworkComment' => $artworkComment,
            'form' => $form->createView(),
        ]);
    }

    /**
     *  Page admin servant à supprimer un commentaire via son id passé dans l'URL
     *
     * @Route("/supprimer-commentaire/{slug}/", name="delete_artwork_comment")
     * @Security("is_granted('ROLE_USER')")
     *
     */
    public function commentDelete(ArtworkComment $comment, Request $request): Response
    {

        if(!$this->isCsrfTokenValid('delete_artwork_comment_' . $comment->getId(), $request->query->get('csrf_token')  )){

            $this->AddFlash('error', 'Token sécurité invalide, veuillez ré-essayer.');

        } else {

            $em = $this->getDoctrine()->getManager();

            $em->remove($comment);

            $em->flush();

            $this->addFlash('success', 'Commentaire supprimé avec succès.');

        }

        return $this->redirectToRoute('artwork_show', [
            'slug' => $comment->getArtwork()->getSlug()
        ]);

    }

    /**
     * @Route("/editer/{slug}", name="artwork_edit", methods={"GET","POST"})
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
            
            if($artwork->getPicture() != null){
                unlink($this->getParameter('artwork.photo.directory') . $oldFileName );
            }

            $artwork->setPicture($newFileName);


            $this->getDoctrine()->getManager()->flush();

            $picture->move(
                $this->getParameter('artwork.photo.directory'),
                $newFileName
            );

            $this->addFlash('success', 'Oeuvre éditée avec succès.');


            return $this->redirectToRoute('artwork_show', ['slug' => $artwork->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artwork/edit.html.twig', [
            'artwork' => $artwork,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/editer/{slug}", name="admin_artwork_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function adminEdit(Request $request, Artwork $artwork): Response
    {
        $oldFileName = $artwork->getPicture();

        $form = $this->createForm(ArtworkType::class, $artwork);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $picture = $form->get('picture')->getData();

            $newFileName = md5( random_bytes(100) . time() ) . '.' . $picture->guessExtension();
            
            if($artwork->getPicture() != null){
                unlink($this->getParameter('artwork.photo.directory') . $oldFileName );
            }

            $artwork->setPicture($newFileName);


            $this->getDoctrine()->getManager()->flush();

            $picture->move(
                $this->getParameter('artwork.photo.directory'),
                $newFileName
            );

            $this->addFlash('success', 'Oeuvre éditée avec succès.');

            return $this->redirectToRoute('admin_artwork_index', ['slug' => $artwork->getSlug()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('artwork/edit.html.twig', [
            'artwork' => $artwork,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/supprimer/{slug}", name="admin_artwork_delete", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function adminDelete(Request $request, Artwork $artwork): Response
    {
        if ($this->isCsrfTokenValid('delete'.$artwork->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($artwork);
            $entityManager->flush();
            $this->addFlash('success', 'Oeuvre supprimée avec succès.');
        }



        return $this->redirectToRoute('admin_artwork_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/supprimer/{slug}", name="artwork_delete", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Request $request, Artwork $artwork): Response
    {
        if ($this->isCsrfTokenValid('delete'.$artwork->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($artwork);
            $entityManager->flush();
            $this->addFlash('success', 'Oeuvre supprimée avec succès.');
        }

        return $this->redirectToRoute('artwork_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     *  Page affichant les résultats de recherches faites par le formulaire de recherche dans la navbar
     *
     * @Route("/utilisateur/recherche", name="artwork_search")
     *
     */
    public function artworkSearch(Request $request, PaginatorInterface $paginator): Response
    {
        // Récupération de la variable $_GET['page]
        $requestedPage = $request->query->getInt('page', 1);

        if($requestedPage < 1){
            throw new NotFoundHttpException();
        }

        $search = $request->query->get('q');

        $em = $this->getDoctrine()->getManager();

        $query = $em
            ->createQuery('SELECT a FROM App\Entity\Artwork a WHERE a.id LIKE :search OR a.title LIKE :search OR a.description LIKE :search OR a.artist LIKE :search OR a.yearOfCreation LIKE :search OR a.publicationDate LIKE :search ORDER BY a.publicationDate DESC ')
            ->setParameters(['search' => '%' . $search . '%'])
        ;

        $artworks = $paginator->paginate(
            $query,
            $requestedPage,
            12,
        );

        return $this->render('artwork/artworkSearch.html.twig', [
            'artworks' => $artworks
        ]);
    }

    /**
     *  Page affichant les résultats de recherches faites par le formulaire de recherche dans la navbar
     *
     * @Route("/admin/recherche", name="admin_artwork_search")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function adminArtworkSearch(Request $request, PaginatorInterface $paginator): Response
    {
        // Récupération de la variable $_GET['page]
        $requestedPage = $request->query->getInt('page', 1);

        if($requestedPage < 1){
            throw new NotFoundHttpException();
        }

        $search = $request->query->get('q');

        $em = $this->getDoctrine()->getManager();

        $query = $em
            ->createQuery('SELECT a FROM App\Entity\Artwork a WHERE a.id LIKE :search OR a.title LIKE :search OR a.description LIKE :search OR a.artist LIKE :search OR a.yearOfCreation LIKE :search OR a.publicationDate LIKE :search ORDER BY a.publicationDate DESC ')
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
