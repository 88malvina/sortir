<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class MonProfilFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $campus = new Campus();
        $builder
            //to-do pseudo doit être unique
            ->add('pseudo',TextType::class,['empty_data'=>''])
            ->add('prenom', TextType::class)
            ->add('nom')
            ->add('telephone')
            ->add('email')
            ->add('password', RepeatedType::class,[
                'type'=>PasswordType::class,
                'invalid_message'=>'le password doit être identique dans 2 champs',
                'options'=>['attr'=>['class'=>'password-field']],
                'required'=>true,
                'first_options'=>['label'=>'Password'],
                'second_options'=>['label'=>'Confirmation']
            ])
            //to-do ajouter un champ confirmation password
            ->add('campus', EntityType::class,['class'=>Campus::class, 'choice_label'=>'nom'])
            ->add('image',FileType::class,[
                'label' => 'Profile image',

                'mapped' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Télécharger vers le serveur'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
