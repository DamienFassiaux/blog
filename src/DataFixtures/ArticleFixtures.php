<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
       $faker = \Faker\Factory::create('fr_FR'); //importation de la librairie FAKER via COMPOSER ds le terminal

       for($i = 1; $i<= 3; $i++)
       {
           $category = new Category;

           $category->setTitle($faker->sentence())
                    ->setDescription($faker->paragraph());

            $manager->persist($category);

             // créer entre 4 et 6 articles
                for($j = 1; $j <= mt_rand(4,6); $j++)
                {
                $article = new Article(); // on instancie la class Article() qui se trouve dans le dossier App\Entity
                $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';
                // Nous pouvons maintenant faire appel au setteur pour créer des articles
                $article->setTitle($faker->sentence())
                ->setContent($content)
                ->setImage("https://picsum.photos/seed/picsum/600/400")
                ->setCreatedAt($faker->dateTimeBetween('-
                6 months')) // on instancie la classe DateTime() pour formater l'heure
                ->setCategory($category);
                $manager->persist($article); // permet de faire persister l'article dans le temps
                //On donne des commentaires à l'article
                for($k = 1; $k <= mt_rand(4,10); $k++)

                {
                    $comment = new Comment();
                    $content = '<p>' . join($faker->paragraphs(2), '</p><p>') . '</p>';
                    // $now = new \DateTime();
                    // $interval = $now->diff($article->getCreatedAt());
                    // $days = $interval->days;
                    // $minimum = '-' . $days . ' days'; // moins -100 days
                    $days = (new \DateTime())->diff($article->getCreatedAt())->days;
                   
                    $comment->setAuthor($faker->name)
                    ->setContent($content)
                    ->setCreatedAt($faker->dateTimeBetween('-' . $days . ' days'))
                    ->setArticle($article);

                    $manager->persist($comment);
       
        
                }
            }
        }

        $manager->flush();
  }
}
