<?php

namespace App\Controller;

use App\Entity\User;
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
use Symfony\Component\Form\FormError;
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


            $newPassword = $form->get('plainPassword')['password']->getData();
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
     * @Route("/cgu", name="cgu")
     */
    public function cgu(): Response
    {
        return $this->render('main/cgu.html.twig');
    }


}
