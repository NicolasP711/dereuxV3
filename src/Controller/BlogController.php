<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\Article1Type;
use App\Repository\ArticleRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Entity\ArticleComment;
use App\Form\ArticleCommentFormType;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{

    /**
     * @Route("/", name="blog_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {

        $requestedPage = $request->query->getInt('page', 1);

        if($requestedPage < 1){
            throw new NotFoundHttpException();
        }

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('SELECT a FROM App\Entity\Article a');

        $pageArticles = $paginator->paginate(
            $query,     // Requête de selection des articles en BDD
            $requestedPage,     // Numéro de la page dont on veux les articles
            10      // Nombre d'articles par page
        );
        return $this->render('blog/index.html.twig', [
            'articles' => $pageArticles,
        ]);
    }


    /**
     * @Route("/admin", name="blog_admin", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function admin(Request $request, PaginatorInterface $paginator): Response
    {
        $requestedPage = $request->query->getInt('page', 1);

        if($requestedPage < 1){
            throw new NotFoundHttpException();
        }

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('SELECT a FROM App\Entity\Article a');

        $pageArticles = $paginator->paginate(
            $query,     // Requête de selection des articles en BDD
            $requestedPage,     // Numéro de la page dont on veux les articles
            10      // Nombre d'articles par page
        );
        return $this->render('blog/admin.html.twig', [
            'articles' => $pageArticles,
        ]);
    }

    /**
     *  Page affichant les résultats de recherches faites par le formulaire de recherche dans la navbar
     *
     * @Route("/article/recherche", name="article_search")
     */
    public function articleSearch(Request $request, PaginatorInterface $paginator): Response
    {
        // Récupération de la variable $_GET['page]
        $requestedPage = $request->query->getInt('page', 1);

        if($requestedPage < 1){
            throw new NotFoundHttpException();
        }

        $search = $request->query->get('q');

        $em = $this->getDoctrine()->getManager();

        $query = $em
            ->createQuery('SELECT a FROM App\Entity\Article a WHERE a.id LIKE :search OR a.title LIKE :search OR a.content LIKE :search OR a.publicationDate LIKE :search ORDER BY a.publicationDate DESC ')
            ->setParameters(['search' => '%' . $search . '%'])
        ;

        $articles = $paginator->paginate(
            $query,
            $requestedPage,
            10,
        );

        return $this->render('blog/articleSearch.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     *  Page affichant les résultats de recherches faites par le formulaire de recherche dans la navbar
     *
     * @Route("/admin/article/recherche", name="admin_article_search")
     */
    public function adminArticleSearch(Request $request, PaginatorInterface $paginator): Response
    {
        // Récupération de la variable $_GET['page]
        $requestedPage = $request->query->getInt('page', 1);

        if($requestedPage < 1){
            throw new NotFoundHttpException();
        }

        $search = $request->query->get('q');

        $em = $this->getDoctrine()->getManager();

        $query = $em
            ->createQuery('SELECT a FROM App\Entity\Article a WHERE a.id LIKE :search OR a.title LIKE :search OR a.content LIKE :search OR a.publicationDate LIKE :search ORDER BY a.publicationDate DESC ')
            ->setParameters(['search' => '%' . $search . '%'])
        ;

        $articles = $paginator->paginate(
            $query,
            $requestedPage,
            10,
        );

        return $this->render('blog/adminArticleSearch.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/admin/nouvel-article", name="blog_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(Article1Type::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setPublicationDate( new DateTime() );
            $article->setAuthor($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/article/{slug}", name="blog_show")
     */
    public function show(Article $article, Request $request): Response
    {
        $newComment = new ArticleComment();

        $form = $this->createForm(ArticleCommentFormType::class, $newComment);

        $form->handleRequest($request);

        // Pour savoir si le formulaire a été envoyé, on a accès à cette condition :
            if($form->isSubmitted() && $form->isValid()){

                $newComment
                    ->setPublicationDate(new DateTime())
                    ->setAuthor($this->getUser())
                    ->setArticle($article)
                ;

                // récupération du manager des entités et sauvegarde en BDD de $newArticle
                $em = $this->getDoctrine()->getManager();

                $em->persist($newComment);

                $em->flush();

                $this->addFlash('success', 'Commentaire publié avec succès.');

                // On re-créé le formulaire pour pas que le texte saisi dans le dernier commentaire se remettent dans le nouveau
                $newComment = new ArticleComment();
                $form = $this->createForm(ArticleCommentFormType::class, $newComment);
            }
        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/{slug}/edition", name="blog_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(Article1Type::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/{slug}/edition", name="admin_blog_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function adminEdit(Request $request, Article $article): Response
    {
        $form = $this->createForm(Article1Type::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'L\'article a été supprimé avec succès.');
            return $this->redirectToRoute('blog_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/suppression/{slug}", name="admin_blog_delete", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function adminDelete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
            $this->addFlash('success', 'L\'article a été supprimé avec succès.');
        }

        return $this->redirectToRoute('blog_admin', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/suppression/{slug}", name="blog_delete", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
            $this->addFlash('success', 'L\'article a été supprimé avec succès.');
        }

        return $this->redirectToRoute('blog_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     *  Page admin servant à supprimer un commentaire via son id passé dans l'URL
     *
     * @Route("/commentaire/suppression/{id}/", name="delete_article_comment")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     */
    public function commentDelete(ArticleComment $comment, Request $request): Response
    {

        if(!$this->isCsrfTokenValid('delete_article_comment_' . $comment->getId(), $request->query->get('csrf_token')  )){

            $this->AddFlash('error', 'Token sécurité invalide, veuillez ré-essayer.');

        } else {

            $em = $this->getDoctrine()->getManager();

            $em->remove($comment);

            $em->flush();

            $this->addFlash('success', 'Le commentaire a été supprimé avec succès.');

        }

        return $this->redirectToRoute('blog_show', [
            'slug' => $comment->getArticle()->getSlug()
        ]);

    }
}
