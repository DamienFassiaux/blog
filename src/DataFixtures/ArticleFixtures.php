<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 1;$i<= 10; $i++)
        {

             //Pour inserer ds la table sql article nous devons instancier un objet issu de cette classe 
            //L'entité article reflete la table sql article
            //Nous avons besoin de renseigner tous les setteurs et tous les objets $article afin de pouvoir générer les insertions en BDD
            $article = new Article;


           //on rempli les objets artciles grace aux setteurs
            $article->setTitle("Titre de l'article n° $i")
                    ->setContent("<p>Contenu de l'article $i</p>")
                    ->setImage("https://picsum.photos/id/237/600/400")
                    ->setCreatedAt(new \DateTime); //on instancie la classe Datetime afin d'avoir la date et l'heure ds la BDD
         
            //en Symfony nous avons besoin d un manager qui permet de manipuler les lignes de la BDD (insertion, modif, suppression)
            //persist() est une methode issue de la classe ObjectManager qui permet de garder en mémoire les objets article créés et préparer les requetes d'insertion (INSERT INTO)
            $manager->persist($article);

        }
            //flush() est une methode issue de la classe ObjectManager qui execute les insertions en BDD ( comme execute() en PHP)
            $manager->flush();

            // une fois les fixtures réaliseés, il faut les charger en BDD grace à doctrine (ORM) par la commande : 
            // php bin/console doctrine:fixtures:load

        
    }
}
