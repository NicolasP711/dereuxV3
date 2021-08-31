<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\User;
use App\Form\ContactFormType;
use App\Form\EditPasswordFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Form\EditUserFormType;
use App\Form\EditPhotoFormType;
use App\Repository\UserRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;


class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('main/index.html.twig');
    }

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
        return $this->render('main/adminUser.html.twig', [
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

        return $this->render('main/adminUserSearch.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/contact/", name="contact")
     */
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactFormType::class, $contact);
        $form->handleRequest($request);
        $contact->setDateSent( new DateTime() );

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $mail = $data->getEmail();
            $email = (new TemplatedEmail())
            ->from(new Address('expediteur@exemple.fr', 'noreply'))
            ->to($mail)
            ->subject('Sujet du mail')
            ->htmlTemplate('test/test.html.twig')    // Fichier twig du mail en version html
            ->textTemplate('test/test.txt.twig')     // Fichier twig du mail en version text
            /* Il est possible de faire passer aux deux templates twig des variables en ajoutant le code suivant :
            ->context([
                'fruits' => ['Pomme', 'Cerise', 'Poire']
            ])
            */
        ;

            // Envoi du mail
            $mailer->send($email);

            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            $this->addFlash('success', 'Message envoyé avec succès.');
            return $this->redirectToRoute('home');
        }
        return $this->render('main/contact.html.twig',[
            'contactForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/mon-profil/", name="profil")
     * @Security("is_granted('ROLE_USER')")
     *
     */
    public function myProfile(): Response
    {
        return $this->render('main/profil.html.twig');
    }

    /** Page de modification de profil
     *
     * @Route("/modifier-mon-profil/", name="edit_profil")
     * @Security("is_granted('ROLE_USER')")
     *
     */
    public function editProfil(Request $request, UserPasswordEncoderInterface $encoder): Response
    {

        $user = $this->getUser();

        $form = $this->createForm(EditUserFormType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Profil modifié avec succès.');
            return $this->redirectToRoute('profil');
        }
        $this->getDoctrine()->getManager()->refresh($user);


        // Pour que la vue puisse afficher le formulaire, on doit lui envoyer le formulaire généré, avec $form->createView()
        return $this->render('main/editProfil.html.twig', [
            'editProfilForm' => $form->createView()
        ]);
    }

    /** Page de modification de profil
     *
     * @Route("/modifier-mon-mot-de-passe/", name="edit_password")
     * @Security("is_granted('ROLE_USER')")
     *
     */
    public function editPassword(Request $request, UserPasswordEncoderInterface $encoder): Response
    {

        $user = $this->getUser();

        $form = $this->createForm(EditPasswordFormType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();

            $newPassword = $form->get('plainPassword')['first']->getData();

            // Grâce au service, on génère un nouveau hash de notre nouveau mot de passe
            $hashOfNewPassword = $encoder->encodePassword($user, $newPassword);

            // On change l'ancien mot de passe hashé par le nouveau que l'on a généré juste au dessus
            $user->setPassword( $hashOfNewPassword );
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Mot de passe modifié avec succès.');
            return $this->redirectToRoute('profil');
        }

        // Pour que la vue puisse afficher le formulaire, on doit lui envoyer le formulaire généré, avec $form->createView()
        return $this->render('main/editPassword.html.twig', [
            'editPasswordForm' => $form->createView()
        ]);
    }

    /** Fonction permettant de supprimer son compte depuis la page de profil
     *
     * @Route("/supprimer-mon-profil", name="delete_account")
     * @Security("is_granted('ROLE_USER')")
     *
     */
    public function deleteAccount(): Response
    {
        $user = $this->getUser();
        $this->container->get('security.token_storage')->setToken(null);

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', 'Compte supprimé avec succès.');

        return $this->redirectToRoute('home');
    }

    /** Fonction permettant de supprimer son compte depuis la page de profil
     *
     * @Route("/admin/supprimer-profil/{id}", name="admin_delete_account")
     * @Security("is_granted('ROLE_USER')")
     *
     */
    public function adminDeleteAccount(User $user): Response
    {
            $user->getId();
            $this->container->get('security.token_storage')->setToken(null);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        
            $this->addFlash('success', 'Compte supprimé avec succès.');


        return $this->redirectToRoute('admin_user', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Page de modification de la photo de profil
     *
     * @Route("/modifier-ma-photo-de-profil/", name="edit_photo")
     * @Security("is_granted('ROLE_USER')")
     *
     */
    public function editPhoto(Request $request): Response
    {
        $form = $this->createForm(EditPhotoFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $photo = $form->get('photo')->getData();

            // Suppression de la photo actuelle s'il y en a une
            if($this->getUser()->getPhoto() != null){
                unlink($this->getParameter('app.user.photo.directory') . $this->getUser()->getPhoto() );
            }

            $newFileName = md5( random_bytes(100) . time() ) . '.' . $photo->guessExtension();

            // Sauvegarde du nom du fichier dans l'utilisateur connecté en BDD
            $this->getUser()->setPhoto($newFileName);

            $em = $this->getDoctrine()->getManager();

            $em->flush();

            // Déplacer le fichier de l'image dans un dossier
            $photo->move(
                $this->getParameter('app.user.photo.directory'),
                $newFileName
            );

            $this->addFlash('success', 'Image de profil modifiée avec succès.');

            return $this->redirectToRoute('profil');
        }



        return $this->render('main/editPhoto.html.twig', [
            'photoForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/utilisateur/{id}", name="user_show")
     */
    public function show(User $user): Response
    {

        return $this->render('main/userShow.html.twig', [
            'user' => $user,


        ]);
    }


}
