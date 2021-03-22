<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

   
    /**
 * @Route("/inscription", name="security_registration")
 */
      public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User(); // on précise à quelle entité va être relié notre formulaire

        $formRegistration = $this->createForm(registrationType::class, $user, [
          'validation_groups' => ['registration']  // Nous definissons un groupe de validation afin qu'elles ne soient pris en compte seulement lors de l'inscription mais pas dans le Backoffice cf RegistrationType.php
        ]); // on appel la classe qui permet de construire le formulaire

        dump($request);

        $formRegistration->handleRequest($request);

        dump($user);

        if($formRegistration->isSubmitted() && $formRegistration->isValid())
                {
                   $hash = $encoder->encodePassword($user, $user->getPassword()); 
    
                    $user->setPassword($hash);
                    $user->setRoles(["ROLE_USER"]); //role User par défaut pour chaque User

                    dump($hash);
       
                  $manager->persist($user); 
                  $manager->flush(); // on lance la requete d'insertion

                  $this->addFlash('success', "Félicitations !! Votre compte a bien été validé ! Vous pouvez dés maintenant vous connecter !");

                  return $this->redirectToRoute('security_login');

                  }

    return $this->render('security/registration.html.twig', [
       'formRegistration' => $formRegistration->createView()
            ]);
       
    }

    /**
     * AuthenticationUtils permet de récupérer le dernier email saisi au moment de la connexion
     * AuthenticationUtils pemet de récuperer le message d erreur en cas de mauvaise connexion
     * 
        * @Route("/connexion" , name="security_login")
        */
        
  
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
      $error = $authenticationUtils->getLastAuthenticationError();

      $lastUsername = $authenticationUtils->getLastUsername();

      return $this->render('security/login.html.twig', [
        'error'=> $error,
        'lastUsername'=> $lastUsername     
         ]);
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */

     public function logout()
     {
       //cette méthode ne retourne rien, il suffit d'avoir une route pour se deconnecter
     }

}
