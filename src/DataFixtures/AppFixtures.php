<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
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
            ->setEmail('admin@a.a')
            ->setRegistrationDate(new DateTime('-1 year'))
            ->setPseudonym('Batman')
            ->setRoles(["ROLE_ADMIN"])
            ->setIsVerified('true')
            ->setPassword( $this->encoder->encodePassword($admin, 'aaAA11$$') )
        ;

        // Persistance du nouveau compte admin
        $manager->persist($admin);

        // Création de 200 articles
        for($i = 0; $i < 200; $i++){

            $article = new Article();

            $article
                ->setPublicationDate( $faker->dateTimeBetween($admin->getRegistrationDate(), 'now') )
                ->setAuthor($admin)
                ->setTitle( $faker->sentence(1) )
                ->setContent( $faker->paragraph(15) )
            ;

            $manager->persist($article);
        }

        $manager->flush();
    }
}
