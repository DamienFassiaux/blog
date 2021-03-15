<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleFormType;
use App\Form\CommentFormType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('blog/home.html.twig', [
            'title' => "Bienvenue sur le blog Symfony",
            'age' => 25
        ]);
    }

    /**
     * méthode permettant d'afficher toute la liste des articles stockés en BDD
     */
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo): Response
    {
        //Pour selectionner des données ds une table SQL, nous devons avoir accès a la classe Repository de l'entité correspondante
        //un Repository est une classe permettant uniquement d'executer des requetes de selection en BDD (SELECT)
        //Nous devons donc accéder au repository de l'entité Article grace à la méthode getRepository()
        //$repo est un objet issu de la classe ArticleRepository, cet objet contient des methodes permettant d'executer des requetes de selections
        //findAll() est une de ces méthodes permettant un SELECT * FROM article
       // $repo = $this->getDoctrine()->getRepository(Article::class);

        dump($repo); // outil de debugage Symfony equivalent d'un var_dump en PHP

        $articles = $repo->findAll(); //equivalent de SELECT * FROM article + fetchAll

        dump($articles);

        return $this->render('blog/index.html.twig', [
            'title' => 'Liste des articles',
            'articles' => $articles // on envoie sur le template les articles selectionnés en BDD afin de pouvoir les afficher dynamiquement sur le template à l'aide du langage Twig
        ]);
    }


    /**
     * @Route("/blog/new" , name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function create(Article $articleCreate = null, Request $request, EntityManagerInterface $manager): Response
    {
        dump($articleCreate);

          
        if(!$articleCreate)
        {
         $articleCreate = new Article;
        }

         //nous renseignons le setter de l'objet et Symfony est capable automatiquement d'envoyer les valeurs de l'entité directement dans les attributs 'value' du formulaire, étant donné que l'entité $articleCreate est relié au formulaire
         //$articleCreate->setTitle("Titre à la con")
           //            ->setContent("contenu à la con");

         //nous avons creer une classe qui permet de generer le formulaire d'ajout d'article, il faut ds le controller importer cette classe ArticleFormType et relier le formulaire à notre entité Article $articleCreate
         $form = $this->createForm(ArticleFormType::class, $articleCreate);

        //On pioche dans l'objet du formulaire la méthode handleRequest() qui permet de récupérer chaque données saisies ds le formulaire et de les binder(transmettre) dans les bons setteurs de mon entité 
        $form->handleRequest($request);

        dump($articleCreate);

         //si formulaire soumis et champs valides dans les bon setteurs de l'entité alors on netre ds le if on génère l'insertion et on appel le setter de la date car pas de champ date ds le formulaire
        if($form->isSubmitted() && $form->isValid())
        {

            if(!$articleCreate->getId()) //on rentre ds le if en cas d'insertion, l'article n'a pas d'Id  
            {
            $articleCreate->setCreatedAt(new \DateTime);
            }

            $manager->persist($articleCreate);//on appel le manager pour preparer la requete d'insertion et la garder en memoire
            $manager->flush(); //on execute la requete d'insertion en BDD

            return $this->redirectToRoute('blog_show',[
                'id' => $articleCreate->getId()
            ]);
        }

        return $this->render('blog/create.html.twig',[
          'formArticle' => $form->createView(),
          'editMode' => $articleCreate->getId() //Cela permet de savoir ds le template si l'article possède un article ou non, si insertion ou modification
      ]);
    }


    /**
     * methode permettant d'afficher le detail d'un article
     * 
     * @Route("/blog/{id}", name="blog_show")
     */

     public function show(Article $article, request $request, EntityManagerInterface $manager): Response
     {
        
        $comment = new Comment();

        $formComment = $this->createForm(CommentFormType::class, $comment);
        //$repoArticle = $this->getDoctrine()->getRepository(Article::class);

        // dump($repoArticle);

        // dump($id);

         //$article = $repoArticle->find($id);

         $formComment->handleRequest($request);

         dump($comment);

         if($formComment->isSubmitted() && $formComment->isValid())
         {
             $comment->setCreatedAt(new \DateTime())
                      ->setArticle($article);

                      $manager->persist($comment);
                      $manager->flush();

                      $this->addFlash('success', "Le commentaire a bien été posté!");

                      return $this->redirectToRoute('blog_show', [
                                'id' => $article->getId() 
                      ]);
         }

         return $this->render('blog/show.html.twig',[
                    'article'=> $article,
                    'formComment' => $formComment->createView()      // on envoi sur le template les données selectionnées en BDD, c'est à dire les informations d'1 article en fonction l'id transmit dans l'URL

         ]);
     }

     /*
        En fonction de la route paramétrée {id} et de l'injection de dépendance $article, Symfony voit que l'on besoin d'un article de la BDD par rapport à l'ID transmit dans l'URL, il est donc capable de recupérer l'ID et de selectionner en BDD l'article correspondant et de l'envoyer directement en argument de la méthode show(Article $article)
        Tout ça grace à des ParamConverter qui appel des convertisseurs pour convertir les paramètres de l'objet. Ces objets sont stockés en tant qu'attribut de requete et peuvent donc être injectés an tant qu'argument de méthode de controller
    */


    
}
