<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\ArticleComment;
use App\Entity\Artwork;
use App\Entity\Contact;
use App\Entity\User;
use \DateTime;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Faker;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {

        $faker = Faker\Factory::create('fr_FR');
        // $genres = ['male', 'female'];
        // Création d'un compte admin
        $admin = New User();
        // $genre= $faker->randomElement($genres);
        // $image = "https://randomuser.me/api/portraits/";

        // $imageId = $faker->numberBetween(1,99) . '.jpg';

        // $image .= ($genre == 'male' ? 'men/' : 'women/').$imageId;

        $admin
            // ->setPhoto($image)
            ->setEmail('admin@dereux.com')
            ->setRegistrationDate(new DateTime('-1 year'))
            ->setPseudonym('Administrateur')
            ->setRoles(["ROLE_ADMIN"])
            ->setIsVerified('true')
            ->setPassword( $this->encoder->encodePassword($admin, 'aaAA11$$') )
        ;

        // Persistance du nouveau compte admin
        $manager->persist($admin);

        // Création de 200 articles
        for($i = 0; $i < 200; $i++){

            $user = new User();

            $user
                ->setRegistrationDate(new DateTime('-1 year'))
                ->setPseudonym( $faker->unique()->userName() )
                ->setEmail( $faker->unique()->email() )
                ->setIsVerified('true')
                ->setPassword( $this->encoder->encodePassword($admin, 'aaAA11$$') )
            ;

            $manager->persist($user);
        }

        // Création de 200 articles
        for($i = 0; $i < 200; $i++){

            $article = new Article();

            $article
                ->setPublicationDate( $faker->dateTimeBetween($admin->getRegistrationDate(), 'now') )
                ->setAuthor($admin)
                ->setTitle( $faker->sentence(1) )
                ->setContent( $faker->paragraph(50) )
            ;

            $manager->persist($article);
        }

        for($i = 0; $i < 200; $i++){

            $artwork = new Artwork();

            $artwork
                ->setPicture('e3689961e4320f8dd775b5471bcf79fd.png')
                ->setYearOfCreation('1901')
                ->setPublicationDate( $faker->dateTimeBetween($admin->getRegistrationDate(), 'now') )
                ->setAuthor($admin)
                ->setTitle( $faker->sentence(1) )
                ->setDescription( $faker->paragraph(25) )
                ->setArtist( $faker->name() )
            ;

            $manager->persist($artwork);
        }

        for($e = 0; $e < 200; $e++){

            $commentArticle = new ArticleComment();

            $commentArticle
                ->setPublicationDate( $faker->dateTimeBetween($admin->getRegistrationDate(), 'now') )
                ->setAuthor($admin)
                ->setArticle($article)
                ->setContent( $faker->paragraph(15) )
            ;

            $manager->persist($commentArticle);
        }

        for($i = 0; $i < 200; $i++){

            $contact = new Contact();

            $contact
                ->setName($faker->name())
                ->setSubject($faker->sentence(1))
                ->setMessage($faker->paragraph(15))
                ->setEmail( $faker->email() )
                ->setDateSent($faker->dateTimeBetween($admin->getRegistrationDate(), 'now'))
            ;

            $manager->persist($contact);
        }

        $manager->flush();
    }
}
