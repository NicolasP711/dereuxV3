<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactFormType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use DateTime;

/**
 * @Route("/contact")
 */
class ContactController extends AbstractController
{

    /**
     * @Route("/", name="contact")
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
        return $this->render('contact/contact.html.twig',[
            'contactForm' => $form->createView()
        ]);
    }


    /**
     * @Route("/admin/liste", name="contact_list", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function contactList(Request $request, PaginatorInterface $paginator)
    {

        $requestedPage = $request->query->getInt('page', 1);

        if($requestedPage < 1){
            throw new NotFoundHttpException();
        }

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('SELECT a FROM App\Entity\Contact a');

        $pageContacts = $paginator->paginate(
            $query,     // Requête de selection des articles en BDD
            $requestedPage,     // Numéro de la page dont on veux les articles
            10      // Nombre d'articles par page
        );
        return $this->render('contact/contactList.html.twig', [
            'contacts' => $pageContacts,
        ]);
    }

    /**
     * @Route("/{slug}", name="contact_show")
     */
    public function show(Contact $contact): Response
    {

        return $this->render('contact/contactShow.html.twig', [
            'contact' => $contact,


        ]);
    }

    /**
     *  Page affichant les résultats de recherches faites par le formulaire de recherche dans la navbar
     *
     * @Route("/admin/recherche/", name="admin_contact_search")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function contactSearch(Request $request, PaginatorInterface $paginator): Response
    {
        // Récupération de la variable $_GET['page]
        $requestedPage = $request->query->getInt('page', 1);

        if($requestedPage < 1){
            throw new NotFoundHttpException();
        }

        $search = $request->query->get('q');

        $em = $this->getDoctrine()->getManager();

        $query = $em
            ->createQuery('SELECT a FROM App\Entity\Contact a WHERE a.id LIKE :search
            OR a.subject LIKE :search OR
            a.message LIKE :search OR
            a.name LIKE :search OR
            a.email LIKE :search OR
            a.dateSent LIKE :search
            ORDER BY a.dateSent DESC ')
            ->setParameters(['search' => '%' . $search . '%'])
        ;

        $contacts = $paginator->paginate(
            $query,
            $requestedPage,
            10,
        );

        return $this->render('contact/contactSearch.html.twig', [
            'contacts' => $contacts
        ]);
    }

    /**
     * @Route("/admin/supprimer/{slug}", name="admin_contact_delete", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function adminDelete(Request $request, Contact $contact): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contact->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($contact);
            $entityManager->flush();

            $this->addFlash('success', 'Contact supprimé avec succès.');
        }

        return $this->redirectToRoute('contact_list', [], Response::HTTP_SEE_OTHER);
    }
}
