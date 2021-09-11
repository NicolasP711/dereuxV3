<?php

namespace App\Controller;

use App\Entity\User;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Repository\UserRepository;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_user", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function adminIndex(Request $request, PaginatorInterface $paginator): Response
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
        $query = $em->createQuery('SELECT a FROM App\Entity\User a');

        $pageUser = $paginator->paginate(
            $query,     // Requête de selection des articles en BDD
            $requestedPage,     // Numéro de la page dont on veux les articles
            10      // Nombre d'articles par page
        );
        return $this->render('user/adminUser.html.twig', [
            'users' => $pageUser,
        ]);
    }

    /**
     *  Page affichant les résultats de recherches faites par le formulaire de recherche dans la navbar
     *
     * @Route("/admin/recherche-utilisateur", name="admin_user_search")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function adminUserSearch(Request $request, PaginatorInterface $paginator): Response
    {
        // Récupération de la variable $_GET['page]
        $requestedPage = $request->query->getInt('page', 1);

        if($requestedPage < 1){
            throw new NotFoundHttpException();
        }

        $search = $request->query->get('q');

        $em = $this->getDoctrine()->getManager();

        $query = $em
            ->createQuery('SELECT a FROM App\Entity\User a WHERE a.id LIKE :search OR a.pseudonym LIKE :search OR a.email LIKE :search OR a.registrationDate LIKE :search ORDER BY a.registrationDate DESC ')
            ->setParameters(['search' => '%' . $search . '%'])
        ;

        $users = $paginator->paginate(
            $query,
            $requestedPage,
            10,
        );

        return $this->render('user/adminUserSearch.html.twig', [
            'users' => $users
        ]);
    }

    /** Fonction permettant de supprimer son compte depuis la page de profil
     *
     * @Route("/admin/supprimer-profil/{id}", name="admin_delete_account")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     */
    public function adminDeleteAccount(Request $request, User $user): Response
    {
            $user->getId();
            $request->request->get('_token');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Le compte a bien été supprimé');
            return $this->redirectToRoute('admin_user');
    }

    /**
     * @Route("/admin/utilisateur/{id}", name="user_show")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function show(User $user): Response
    {

        return $this->render('user/adminUserShow.html.twig', [
            'user' => $user,
        ]);
    }
}
