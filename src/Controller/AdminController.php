<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Category;
use App\Entity\Comment;
use App\Form\CategoryFormType;
use App\Repository\CommentRepository;
use App\Form\CommentFormType;

class AdminController extends AbstractController
{
    /**
     * Méthode permettant d'afficher l'accueil du backOffice
     * 
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }


    /**
     * @Route("/admin/articles", name="admin_articles")
     * @Route("/admin/{id}/remove", name="admin_remove_article")
     */
    public function adminArticles(EntityManagerInterface $manager, ArticleRepository $repoArticle, Article $article = null): Response
    {
        // Le manager permet de manipuler la BDD, on execute la methode getClassMetadata() afin de selectionner les metadonnées des colonnes (primary key, not null, type, taille etc...)
        // getFieldNames() permet de selectionner les noms des champs/colonnes de la table Article de la bdd
        //$colonne = $data->getColumnData() ->$colonne['name'] en procédural
        $colonnes = $manager->getClassMetadata(Article::class)->getFieldNames();

        dump($colonnes);

        //Selection de tout les articles en BDD
        $articles = $repoArticle->findAll();

        dump($articles);

        if($article)
        {

            $id = $article->getId();

            $manager->remove($article);
            $manager->flush();

            $this->addFlash('success', "L'article n°$id a bien été supprimé !");

            return $this->redirectToRoute('admin_articles');
        }

        return $this->render('admin/admin_articles.html.twig', [
            'colonnes'=> $colonnes, // on transmet à la méthode render le nom des champs/colonnes selectionnés en BDD afin de pouvoir les receptionner sur le template et de pouvoir les afficher
            'articlesBdd'=>$articles //on transmet a la methode render les articles selectionnés en BDD au template afin de pouvoir les afficher

        ]);
    }


    /**
     * Méthode permettant de modifier un article existant dans le BackOffice
     * 
     * @Route("/admin/{id}/edit-article", name="admin_edit_article")
     */
    public function adminEditArticle(Article $article, Request $request, EntityManagerInterface $manager)
    {
        dump($article);

        $formArticle = $this->createForm(ArticleFormType::class, $article);

        dump($request);

        $formArticle->handleRequest($request); 

        if($formArticle->isSubmitted() && $formArticle->isValid())
        {
            $manager->persist($article);
            $manager->flush();

            $this->addFlash('success', "L'article n° ". $article->getId() . " a bien été modifié");

            return $this->redirectToRoute('admin_articles');

        }

        return $this->render('admin/admin_edit_article.html.twig', [
            'idArticle'=> $article->getId(),
            'formArticle'=> $formArticle->createView()
        ]);
    }

    /**
     * @Route("/admin/categories", name="admin_category")
     * @Route("/admin/category/{id}/remove", name="admin_remove_category")
     */
    public function adminCategories(EntityManagerInterface $manager, CategoryRepository $repoCategory, Category $category = null): Response
    {
       
        $colonnes = $manager->getClassMetadata(Category::class)->getFieldNames();

        dump($colonnes);

      

        if($category)
        {

            if($category->getArticles()->isEmpty()) //nous avons une relation entre tables article et category et une contrainte d'integrité Restrict nous ne pouvons pas supprimer la catégorie si des articles y sont associés
            //getArticles de l'entité category retourne tout les articles associés a la catégorie(relation bidirectionnelle)
            {
                $manager->remove($category);
                $manager->flush();

                $this->addFlash('success', "La catégorie a bien été supprimé !");

            }
            else
            {
                $this->addFlash('danger', "Il n'est pas possible de supprimer la catégorie car des articles y sont associés !");
            }
                  
                 return $this->redirectToRoute('admin_category');
        }

        $categories = $repoCategory->findAll();

        dump($categories);

        return $this->render('admin/admin_categories.html.twig', [
             'colonnes'=>$colonnes,
             'categoriesBdd'=>$categories
        ]);
    }




    /**
     * @Route("/admin/category/new", name="admin_new_category")
     * @Route("/admin/{id}/edit-category", name="admin_edit_category")
     */
    public function adminEditCategory( Request $request, EntityManagerInterface $manager, Category $category = null): Response
    {

        if(!$category)
         {
            $category = new Category;
         }
    

        $formCategory = $this->createForm(CategoryFormType::class, $category, [
            'validation_groups' => ['category']
        ]);

        dump($request);

        $formCategory->handleRequest($request); 

        dump($category);

        if($formCategory->isSubmitted() && $formCategory->isValid() )
        {
            if(!$category->getId())
            {
                $message = "La catégorie " . $category->getTitle() . " a été enregistrée avec succès !";
            }
            else
            {
                $message = "La catégorie " . $category->getTitle() . " a été modifiée avec succès !";
            }
            $manager->persist($category);
            $manager->flush();

            $this->addFlash('success', $message);

            return $this->redirectToRoute('admin_category');
        }

        return $this->render('admin/admin_edit_category.html.twig', [
            'formCategory'=> $formCategory->createView()
        ]);
    } 
    
    /**
     * @Route("/admin/comments", name="admin_comments")
     * @Route("/admin/comment/{id}/remove", name="admin_remove_comment")
     */
    public function adminComment(EntityManagerInterface $manager, CommentRepository $repoComment, Comment $comment = null): Response
    {
       $colonnes = $manager->getClassMetaData(Comment::class)->getFieldNames();

       dump($colonnes);

       $comments = $repoComment->findAll();

        dump($comments);

        if($comment)
        {
            $id = $comment->getId();

            $manager->remove($comment);
            $manager->flush();

            $this->addFlash('success', "Le commentaire n°$id a bien été supprimé !");

            return $this->redirectToRoute('admin_comments');
        }

        return $this->render('admin/admin_comments.html.twig', [
            'colonnes'=> $colonnes, 
            'commentsBdd'=>$comments 
        ]);

    }


    /**
     * @Route("/admin/comment/{id}/edit", name="admin_edit_comment")
     */
    public function editComment(Comment $comment, Request $request, EntityManagerInterface $manager)
    {
        dump($comment);

        $formComment = $this->createForm(CommentFormType::class, $comment);

        dump($request);

        $formComment->handleRequest($request); 

        if($formComment->isSubmitted() && $formComment->isValid())
        {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success', "Le commentaire n° ". $comment->getId() . " a bien été modifié");

            return $this->redirectToRoute('admin_comments');

        }

        return $this->render('admin/admin_edit_comment.html.twig', [
            'idComment'=> $comment->getId(),
            'formComment'=> $formComment->createView()
        ]);
    }
}
