<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('username')
            ->add('roles', CollectionType::class, [   //collectionType car stocké dans la BDD au format array/Json
                'label_format' => 'Role utilisateur',
                'entry_type' => ChoiceType::class,  //choiceType pour avoir une liste déroulante dans le formulaire
                'entry_options' => [
                    'choices' => [
                        'Utilisateur'=> 'ROLE_USER',
                        'Administrateur'=> 'ROLE_ADMIN'
                    ]
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
