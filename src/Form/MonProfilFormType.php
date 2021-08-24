<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MonProfilFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $campus = new Campus();
        $builder
            ->add('pseudo',TextType::class,['empty_data'=>''])
            ->add('prenom', TextType::class)
            ->add('nom')
            ->add('telephone')
            ->add('email')
            ->add('password')
            ->add('campus', EntityType::class,['class'=>Campus::class, 'choice_label'=>'nom'])
            ->add('image',FileType::class,[
                'label' => 'Profile image',
                'mapped' => false,
                'required' => false
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
