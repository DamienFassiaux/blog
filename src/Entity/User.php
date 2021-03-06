<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 * fields = {"email"},
 * message = "Un compte est déjà existant à cette adresse email!"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Veuillez renseigner une adresse Email!")
     * @Assert\Email(message = "Veuillez saisir une adresse Email valide!")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Veuillez renseigner un nom d'utilisateur!")
     * @Assert\Length(
     * min= 2,max= 50, minMessage="Nom d'utilisateur trop court",
     * maxMessage="Nom d'utilisateur trop long")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Veuillez renseigner un mot de passe!", groups={"registration"})
     * @Assert\EqualTo(propertyPath="confirm_password",
     * message="Les mots de passe ne correspondent pas", groups={"registration"})
     * @Assert\Length(min="8", minMessage="Votre mot de passe doit faire minimum 8 caractères")
     */
    private $password;

    /**
     * @Assert\NotBlank(message = "Veuillez confirmer votre mot de passe!", groups={"registration"})
     * @Assert\EqualTo(propertyPath="password",
     * message="Les mots de passe ne correspondent pas", groups={"registration"})
    */
    public $confirm_password;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    // Pour encoder le mot de passe, l'entité User doit implémenter l'interface UserInterface
    //Cette interface contient des méthodes abstraites que nous sommes obligé de déclarer
    //5 méthodes obligatoires: getUsername, getPassword, get eraseCredentials, getSalt et getRoles

    public function eraseCredentials() {}

    public function getSalt() {}

    public function getRoles() 
    {
        //return ['ROLE_USER']; // utilisateur classique
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
       


}
